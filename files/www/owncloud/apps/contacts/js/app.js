Modernizr.load({
	test: Modernizr.input.placeholder,
	nope: [
			OC.filePath('contacts', 'css', 'placeholder_polyfill.min.css'),
			OC.filePath('contacts', 'js', 'placeholder_polyfill.jquery.min.combo.js')
		]
});

(function($) {
	$.QueryString = (function(a) {
		if (a === '') {return {};}
		var b = {};
		for (var i = 0; i < a.length; ++i)
		{
			var p=a[i].split('=');
			if (p.length !== 2) {
				continue;
			}
			b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, ' '));
		}
		return b;
	})(window.location.search.substr(1).split('&'));
})(jQuery);

var utils = {};

/**
 * utils.isArray
 *
 * Best guess if object is an array.
 */
utils.isArray = function(obj) {
     // do an instanceof check first
     if (obj instanceof Array) {
         return true;
     }
     // then check for obvious falses
     if (typeof obj !== 'object') {
         return false;
     }
     if (utils.type(obj) === 'array') {
         return true;
     }
     return false;
};

utils.isInt = function(s) {
  return typeof s === 'number' && (s.toString().search(/^-?[0-9]+$/) === 0);
};

utils.isUInt = function(s) {
  return typeof s === 'number' && (s.toString().search(/^[0-9]+$/) === 0);
};

/**
 * utils.type
 *
 * Attempt to ascertain actual object type.
 */
utils.type = function(obj) {
    if (obj === null || typeof obj === 'undefined') {
        return String (obj);
    }
    return Object.prototype.toString.call(obj)
        .replace(/\[object ([a-zA-Z]+)\]/, '$1').toLowerCase();
};

utils.moveCursorToEnd = function(el) {
	if (typeof el.selectionStart === 'number') {
		el.selectionStart = el.selectionEnd = el.value.length;
	} else if (typeof el.createTextRange !== 'undefined') {
		el.focus();
		var range = el.createTextRange();
		range.collapse(false);
		range.select();
	}
};

Array.prototype.clone = function() {
  return this.slice(0);
};

Array.prototype.clean = function(deleteValue) {
	var arr = this.clone();
	for (var i = 0; i < arr.length; i++) {
		if (arr[i] === deleteValue) {
			arr.splice(i, 1);
			i--;
		}
	}
	return arr;
};

// Keep it DRY ;)
var wrongKey = function(event) {
	return ((event.type === 'keydown' || event.type === 'keypress') 
		&& (event.keyCode !== 32 && event.keyCode !== 13));
};

/**
 * Simply notifier
 * Arguments:
 * @param string message - The text message to show.
 * @param int timeout - The timeout in seconds before the notification disappears. Default 10.
 * @param function timeouthandler - A function to run on timeout.
 * @param function clickhandler - A function to run on click. If a timeouthandler is given it will be cancelled on click.
 * @param object data - An object that will be passed as argument to the timeouthandler and clickhandler functions.
 * @param bool cancel - If set cancel all ongoing timer events and hide the notification.
 */
OC.notify = function(params) {
	var self = this;
	if(!self.notifier) {
		self.notifier = $('#notification');
		self.notifier.on('click', function() { $(this).fadeOut();});
	}
	if(params.cancel) {
		self.notifier.off('click');
		for(var id in self.notifier.data()) {
			if($.isNumeric(id)) {
				clearTimeout(parseInt(id));
			}
		}
		self.notifier.text('').fadeOut().removeData();
	}
	if(params.message) {
		self.notifier.text(params.message).fadeIn().css('display', 'inline');
	}

	var timer = setTimeout(function() {
		self.notifier.fadeOut();
		if(params.timeouthandler && $.isFunction(params.timeouthandler)) {
			params.timeouthandler(self.notifier.data(dataid));
			self.notifier.off('click');
			self.notifier.removeData(dataid);
		}
	}, params.timeout && $.isNumeric(params.timeout) ? parseInt(params.timeout)*1000 : 10000);
	var dataid = timer.toString();
	if(params.data) {
		self.notifier.data(dataid, params.data);
	}
	if(params.clickhandler && $.isFunction(params.clickhandler)) {
		self.notifier.on('click', function() {
			clearTimeout(timer);
			self.notifier.off('click');
			params.clickhandler(self.notifier.data(dataid));
			self.notifier.removeData(dataid);
		});
	}
};

(function(window, $, OC) {
	'use strict';

	OC.Contacts = OC.Contacts || {
		init:function() {
			if(oc_debug === true) {
				$.error = console.error;
			}
			var self = this;

			this.lastSelectedContacts = [];
			this.scrollTimeoutMiliSecs = 100;
			this.isScrolling = false;
			this.cacheElements();
			this.storage = new OC.Contacts.Storage();
			this.addressBooks = new OC.Contacts.AddressBookList(
				this.storage,
				$('#app-settings-content'),
				$('#addressBookTemplate')
			);
			this.otherBackendConfig = new OC.Contacts.OtherBackendConfig(
				this.storage,
				this.addressBooks,
				$('#addressBookConfigTemplate')
			);
			this.contacts = new OC.Contacts.ContactList(
				this.storage,
				this.addressBooks,
				this.$contactList,
				this.$contactListItemTemplate,
				this.$contactDragItemTemplate,
				this.$contactFullTemplate,
				this.detailTemplates
			);
			this.groups = new OC.Contacts.GroupList(
				this.storage,
				this.$groupList,
				this.$groupListItemTemplate
			);
			self.groups.loadGroups(function() {
				self.loading(self.$navigation, false);
			});
			// Hide the list while populating it.
			this.$contactList.hide();
			$.when(this.addressBooks.loadAddressBooks()).then(function(addressBooks) {
				var deferreds = $(addressBooks).map(function(/*i, elem*/) {
					return self.contacts.loadContacts(this.getBackend(), this.getId(), this.isActive());
				});
				// This little beauty is from http://stackoverflow.com/a/6162959/373007 ;)
				$.when.apply(null, deferreds.get()).then(function() {
					self.contacts.setSortOrder(contacts_sortby);
					self.$contactList.show();
					$(document).trigger('status.contacts.loaded', {
						numcontacts: self.contacts.length
					});
					self.loading(self.$rightContent, false);
					// TODO: Move this to event handler
					self.groups.selectGroup({id:contacts_lastgroup});
					var id = $.QueryString.id; // Keep for backwards compatible links.
					if(!id) {
						id = window.location.hash.substr(1);
					}
					console.log('Groups loaded, id from url:', id);
					if(id) {
						self.openContact(id);
					}
					if(!contacts_properties_indexed) {
						// Wait a couple of mins then check if contacts are indexed.
						setTimeout(function() {
								$.when($.post(OC.generateUrl('apps/contacts/indexproperties/{user}/', {user: OC.currentUser})))
									.then(function(response) {
										if(!response.isIndexed) {
											OC.notify({message:t('contacts', 'Indexing contacts'), timeout:20});
										}
									});
						}, 10000);
					} else {
						console.log('contacts are indexed.');
					}
				}).fail(function(response) {
					console.warn(response);
					self.$rightContent.removeClass('loading');
					var message = t('contacts', 'Unrecoverable error loading address books: {msg}', {msg:response.message});
					OC.dialogs.alert(message, t('contacts', 'Error.'));
				});
			}).fail(function(response) {
				console.log(response.message);
				$(document).trigger('status.contacts.error', response);
			});
			$(OC.Tags).on('change', this.groups.categoriesChanged);
			this.bindEvents();
			this.$toggleAll.show();
			this.hideActions();
			$('.hidden-on-load').removeClass('hidden-on-load');
		},
		loading:function(obj, state) {
			$(obj).toggleClass('loading', state);
		},
		/**
		* Show/hide elements in the header
		* @param act An array of actions to show based on class name e.g ['add', 'delete']
		*/
		hideActions:function() {
			this.showActions(false);
		},
		showActions:function(act) {
			console.log('showActions', act);
			//console.trace();
			this.$headeractions.children().hide();
			if(act && act.length > 0) {
				this.$contactList.addClass('multiselect');
				this.$contactListHeader.find('.actions').css('display', '');
				this.$contactListHeader.find('.action').css('display', 'none');
				this.$contactListHeader.find('.name').attr('colspan', '5');
				this.$contactListHeader.find('.info').css('display', 'none');
				this.$contactListHeader.find('.'+act.join(',.')).css('display', '');
			} else {
				this.$contactListHeader.find('.actions').css('display', 'none');
				this.$contactListHeader.find('.action').css('display', '');
				this.$contactListHeader.find('.name').attr('colspan', '1');
				this.$contactListHeader.find('.info').css('display', '');
				this.$contactList.removeClass('multiselect');
			}
		},
		showAction:function(act, show) {
			this.$contactListHeader.find('.' + act).toggle(show);
		},
		cacheElements: function() {
			var self = this;
			this.detailTemplates = {};
			// Load templates for contact details.
			// The weird double loading is because jquery apparently doesn't
			// create a searchable object from a script element.
			$.each($($('#contactDetailsTemplate').html()), function(idx, node) {
				var $node = $(node);
				if($node.is('div')) {
					var $tmpl = $(node.innerHTML);
					self.detailTemplates[$tmpl.data('element')] = $node;
				}
			});
			this.$groupListItemTemplate = $('#groupListItemTemplate');
			this.$contactListItemTemplate = $('#contactListItemTemplate');
			this.$contactDragItemTemplate = $('#contactDragItemTemplate');
			this.$contactFullTemplate = $('#contactFullTemplate');
			this.$contactDetailsTemplate = $('#contactDetailsTemplate');
			this.$rightContent = $('#app-content');
			this.$navigation = $('#app-navigation');
			//this.$header = $('#contactsheader');
			this.$groupList = $('#grouplist');
			this.$contactList = $('#contactlist');
			this.$contactListHeader = $('#contactsHeader');
			this.$sortOrder = this.$contactListHeader.find('.action.sort');
			this.$sortOrder.val(contacts_sortby||'fn');
			this.$headeractions = this.$groupList.find('.contact-actions');
			this.$toggleAll = this.$contactListHeader.find('.toggle');
			this.$groups = this.$contactListHeader.find('.groups');
			this.$ninjahelp = $('#ninjahelp');
			this.$firstRun = $('#firstrun');
			this.$settings = $('#app-settings');
		},
		// Build the select to add/remove from groups.
		buildGroupSelect: function() {
			// If a contact is open we know which categories it's in
			if(this.currentid) {
				var contact = this.contacts.findById(this.currentid);
				if(contact === null) {
					return false;
				}
				this.$groups.find('optgroup,option:not([value="-1"])').remove();
				var addopts = '', rmopts = '';
				$.each(this.groups.categories, function(i, category) {
					if(contact.inGroup(category.name)) {
						rmopts += '<option value="' + category.id + '">' + category.name + '</option>';
					} else {
						addopts += '<option value="' + category.id + '">' + category.name + '</option>';
					}
				});
				if(addopts.length) {
					$(addopts).appendTo(this.$groups)
					.wrapAll('<optgroup data-action="add" label="' + t('contacts', 'Add to...') + '"/>');
				}
				if(rmopts.length) {
					$(rmopts).appendTo(this.$groups)
					.wrapAll('<optgroup data-action="remove" label="' + t('contacts', 'Remove from...') + '"/>');
				}
			} else if(this.contacts.getSelectedContacts().length > 0) { // Otherwise add all categories to both add and remove
				this.$groups.find('optgroup,option:not([value="-1"])').remove();
				var addopts = '', rmopts = '';
				$.each(this.groups.categories, function(i, category) {
					rmopts += '<option value="' + category.id + '">' + category.name + '</option>';
					addopts += '<option value="' + category.id + '">' + category.name + '</option>';
				});
				$(addopts).appendTo(this.$groups)
					.wrapAll('<optgroup data-action="add" label="' + t('contacts', 'Add to...') + '"/>');
				$(rmopts).appendTo(this.$groups)
					.wrapAll('<optgroup data-action="remove" label="' + t('contacts', 'Remove from...') + '"/>');
			} else {
				// 3rd option: No contact open, none checked, just show "Add group..."
				this.$groups.find('optgroup,option:not([value="-1"])').remove();
			}
			$('<option value="add">' + t('contacts', 'Add group...') + '</option>').appendTo(this.$groups);
			this.$groups.val(-1);
		},
		bindEvents: function() {
			var self = this;

			// Should fix Opera check for delayed delete.
			$(window).unload(function (){
				$(window).trigger('beforeunload');
			});

			this.hashChange = function() {
				console.log('hashchange', window.location.hash);
				var id = String(window.location.hash.substr(1));
				if(id && id !== self.currentid && self.contacts.findById(id) !== null) {
					self.openContact(id);
				} else if(!id && self.currentid) {
					self.closeContact(self.currentid);
				}
			};

			// This apparently get's called on some weird occasions.
			//$(window).bind('popstate', this.hashChange);
			$(window).bind('hashchange', this.hashChange);

			// App specific events
			$(document).bind('status.contact.deleted', function(e, data) {
				var id = String(data.id);
				if(id === self.currentid) {
					delete self.currentid;
				}
				console.log('contact', data.id, 'deleted');
				// update counts on group lists
				self.groups.removeFromAll(data.id, true, true);
			});

			$(document).bind('status.contact.added', function(e, data) {
				self.currentid = String(data.id);
				self.buildGroupSelect();
				self.hideActions();
			});

			// Keep error messaging at one place to be able to replace it.
			$(document).bind('status.contacts.error', function(e, data) {
				var message = data.message;
				console.warn(message);
				//console.trace();
				OC.notify({message:message});
			});

			$(document).bind('status.contact.enabled', function(e, enabled) {
				console.log('status.contact.enabled', enabled);
				/*if(enabled) {
					self.showActions(['back', 'download', 'delete', 'groups']);
				} else {
					self.showActions(['back']);
				}*/
			});

			$(document).bind('status.contacts.count', function(e, response) {
				console.log('Num contacts:', response.count);
				if(response.count > 0) {
					self.$contactList.show();
					self.$firstRun.hide();
				}
			});

			$(document).bind('status.contacts.loaded status.contacts.deleted', function(e, response) {
				console.log('status.contacts.loaded', response);
				if(response.error) {
					$(document).trigger('status.contacts.error', response);
					console.log('Error loading contacts!');
				} else {
					if(response.numcontacts === 0) {
						self.$contactList.hide();
						self.$firstRun.show();
					} else {
						self.$contactList.show();
						self.$firstRun.hide();
					$.each(self.addressBooks.addressBooks, function(idx, addressBook) {
						console.log('addressBook', addressBook);
						if(!addressBook.isActive()) {
							self.contacts.showFromAddressbook(addressBook.getId(), false);
						}
					});
					}
				}
			});

			$(document).bind('status.contact.currentlistitem', function(e, result) {
				//console.log('status.contact.currentlistitem', result, self.$rightContent.height());
				if(self.dontScroll !== true) {
					if(result.pos > self.$rightContent.height()) {
						self.$rightContent.scrollTop(result.pos - self.$rightContent.height() + result.height);
					}
					else if(result.pos < self.$rightContent.offset().top) {
						self.$rightContent.scrollTop(result.pos);
					}
				} else {
					setTimeout(function() {
						self.dontScroll = false;
					}, 100);
				}
				self.currentlistid = result.id;
			});

			$(document).bind('status.nomorecontacts', function(e, result) {
				console.log('status.nomorecontacts', result);
				self.$contactList.hide();
				self.$firstRun.show();
			});

			$(document).bind('status.visiblecontacts', function(e, result) {
				console.log('status.visiblecontacts', result);
				// TODO: To be decided.
			});

			$(document).bind('request.openurl', function(e, data) {
				switch(data.type) {
					case 'url':
						var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!-\/]))?/;
						//if(new RegExp("[a-zA-Z0-9]+://([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?").test(data.url)) {
						if(regexp.test(data.url)) {
							var newWindow = window.open(data.url,'_blank');
							newWindow.focus();
						} else {
							$(document).trigger('status.contacts.error', {
								error: true,
								message: t('contacts', 'Invalid URL: "{url}"', {url:data.url})
							});
						}
						break;
					case 'email':
						var regexp = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
						if(regexp.test(data.url)) {
							console.log('success');
							var url = 'mailto:' + data.url;
							try {
								var mailer = window.open(url, 'Mailer');
							} catch(e) {
								console.log('There was an error opening a mail composer.', e);
							}
							setTimeout(function(){
								try {
									if(mailer.location.href === url || mailer.location.href.substr(0, 6) === 'about:') {
										mailer.close();
									}
								} catch(e) {
									console.log('There was an error opening a mail composer.', e);
								}
								}, 1000);
						} else {
							$(document).trigger('status.contacts.error', {
								error: true,
								message: t('contacts', 'Invalid email: "{url}"', {url:data.url})
							});
						}
						break;
					case 'adr':
						var address = data.url.filter(function(n) {
							return n;
						});
						var newWindow = window.open('http://open.mapquest.com/?q='+address, '_blank');
						newWindow.focus();
						break;
				}
			});

			// A contact id was in the request
			$(document).bind('request.loadcontact', function(e, result) {
				console.log('request.loadcontact', result);
				if(self.numcontacts) {
					self.openContact(result.id);
				} else {
					// Contacts are not loaded yet, try again.
					console.log('waiting for contacts to load');
					setTimeout(function() {
						$(document).trigger('request.loadcontact', {
							id: result.id
						});
					}, 1000);
				}
			});

			$(document).bind('request.contact.move', function(e, data) {
				console.log('contact', data, 'request.contact.move');
				self.addressBooks.moveContact(data.contact, data.from, data.target);
			});

			$(document).bind('request.contact.setasfavorite', function(e, data) {
				console.log('contact', data.id, 'request.contact.setasfavorite');
				self.groups.setAsFavorite(data.id, data.state);
			});

			$(document).bind('request.contact.addtogroup', function(e, data) {
				self.groups.addTo(data.id, data.groupid, function(response) {
					console.log('contact', data.id, 'request.contact.addtogroup', response);
				});
			});

			$(document).bind('request.contact.removefromgroup', function(e, data) {
				console.log('contact', data.id, 'request.contact.removefromgroup');
				self.groups.removeFrom(data.id, data.groupid);
			});

			$(document).bind('request.contact.export', function(e, data) {
				console.log('request.contact.export', data);
				document.location.href = OC.generateUrl('apps/contacts/addressbook/{backend}/{addressBookId}/contact/{contactId}/export', data);
			});

			$(document).bind('request.contact.close', function(e, data) {
				var id = String(data.id);
				console.log('contact', data.id, 'request.contact.close');
				self.closeContact(id);
			});

			$(document).bind('request.contact.open', function(e, data) {
				var id = String(data.id);
				console.log('contact', data.id, 'request.contact.open');
				self.openContact(id);
			});

			$(document).bind('request.contact.delete', function(e, data) {
				var id = String(data.contactId);
				console.log('contact', data, 'request.contact.delete');
				self.closeContact(id);
				self.contacts.delayedDelete(data);
				self.$contactList.removeClass('dim');
				self.hideActions();
			});

			$(document).bind('request.contact.merge', function(e, data) {
				console.log('contact','request.contact.merge', data);
				var merger = self.contacts.findById(data.merger);
				var mergees = [];
				if(!merger) {
					$(document).trigger('status.contacts.error', {
						message: t('contacts', 'Merge failed. Cannot find contact: {id}', {id:data.merger})
					});
					return;
				}
				$.each(data.mergees, function(idx, id) {
					var contact = self.contacts.findById(id);
					if(!contact) {
						console.warn('cannot find', id, 'by id');
					}
					mergees.push(contact);
				});
				if(!merger.merge(mergees)) {
					$(document).trigger('status.contacts.error', {
						message: t('contacts', 'Merge failed.')
					});
					return;
				}
				merger.saveAll(function(response) {
					if(response.error) {
						$(document).trigger('status.contacts.error', {
							message: t('contacts', 'Merge failed. Error saving contact.')
						});
						return;
					} else {
						if(data.deleteOther) {
							self.contacts.delayedDelete(mergees);
						}
						console.log('merger', merger);
						self.openContact(merger.getId());
					}
				});
			});

			$(document).bind('request.select.contactphoto.fromlocal', function(e, contact) {
				console.log('request.select.contactphoto.fromlocal', contact);
				$('#contactphoto_fileupload').trigger('click', contact);
			});

			$(document).bind('request.select.contactphoto.fromcloud', function(e, metadata) {
				console.log('request.select.contactphoto.fromcloud', metadata);
				OC.dialogs.filepicker(t('contacts', 'Select photo'), function(path) {
					self.cloudPhotoSelected(metadata, path);
				}, false, 'image', true);
			});

			$(document).bind('request.edit.contactphoto', function(e, metadata) {
				console.log('request.edit.contactphoto', metadata);
				self.editCurrentPhoto(metadata);
			});

			$(document).bind('request.groups.reload', function(e, result) {
				console.log('request.groups.reload', result);
				self.groups.loadGroups(function() {
					self.groups.triggerLastGroup();
				});
			});

			$(document).bind('status.group.groupremoved', function(e, result) {
				console.log('status.group.groupremoved', result);
				if(parseInt(result.groupid) === parseInt(self.currentgroup)) {
					self.contacts.showContacts([]);
					self.currentgroup = 'all';
				}
				$.each(result.contacts, function(idx, contactid) {
					var contact = self.contacts.findById(contactid);

					// Test if valid because there could be stale ids in the tag index.
					if(contact) {
						contact.removeFromGroup(result.groupname);
					}
				});
			});

			$(document).bind('status.group.grouprenamed', function(e, result) {
				console.log('status.group.grouprenamed', result);
				$.each(result.contacts, function(idx, contactid) {
					var contact = self.contacts.findById(contactid);
					if(!contact) {
						console.warn('Couldn\'t find contact', contactid);
						return true; // continue
					}
					contact.renameGroup(result.from, result.to);
				});
			});

			$(document).bind('status.group.contactremoved', function(e, result) {
				console.log('status.group.contactremoved', result, self.currentgroup, result.groupid);
				var contact = self.contacts.findById(result.contactid);
				if(contact) {
					if(contact.inGroup(result.groupname)) {
						contact.removeFromGroup(result.groupname);
					}
					if(parseInt(self.currentgroup) === parseInt(result.groupid)) {
						console.log('Hiding', contact.getId());
						contact.hide();
					}
				}
			});

			$(document).bind('status.group.contactadded', function(e, result) {
				console.log('status.group.contactadded', result);
				var contact = self.contacts.findById(result.contactid);
				if(contact) {
					if(!contact.inGroup(result.groupname)) {
						contact.addToGroup(result.groupname);
					}
					if(parseInt(self.currentgroup) === parseInt(result.groupid)) {
						console.log('Showing', contact.getId());
						contact.show();
					}
					if(self.currentgroup === 'uncategorized') {
						console.log('Hiding', contact.getId());
						contact.hide();
					}
				}
			});

			// Group sorted, save the sort order
			$(document).bind('status.groups.sorted', function(e, result) {
				console.log('status.groups.sorted', result);
				$.when(self.storage.setPreference('groupsort', result.sortorder)).then(function(response) {
					if(response.error) {
						$(document).trigger('status.contacts.error', {
							message: response ? response.message : t('contacts', 'Network or server error. Please inform administrator.')
						});
					}
				})
				.fail(function(response) {
					console.log(response.message);
					$(document).trigger('status.contacts.error', response);
					done = true;
				});
			});
			// Group selected, only show contacts from that group
			$(document).bind('status.group.selected', function(e, result) {
				console.log('status.group.selected', result);
				self.currentgroup = result.id;
				// Close any open contact.
				if(self.currentid) {
					var id = self.currentid;
					self.closeContact(id);
					self.jumpToContact(id);
				}
				self.$toggleAll.show();
				self.hideActions();
				if(result.type === 'category' ||  result.type === 'fav') {
					self.contacts.showContacts(result.contacts);
				} else if(result.type === 'shared') {
					self.contacts.showFromAddressbook(self.currentgroup, true, true);
				} else if(result.type === 'uncategorized') {
					self.contacts.showUncategorized();
				} else {
					self.contacts.showContacts(self.currentgroup);
				}
				$.when(self.storage.setPreference('lastgroup', self.currentgroup)).then(function(response) {
					if(response.error) {
						$(document).trigger('status.contacts.error', response);
					}
				})
				.fail(function(response) {
					console.log(response.message);
					$(document).trigger('status.contacts.error', response);
					done = true;
				});
				self.$rightContent.scrollTop(0);
			});
			// mark items whose title was hid under the top edge as read
			/*this.$rightContent.scroll(function() {
				// prevent too many scroll requests;
				if(!self.isScrolling) {
					self.isScrolling = true;
					var num = self.$contactList.find('tr').length;
					//console.log('num', num);
					var offset = self.$contactList.find('tr:eq(' + (num-20) + ')').offset().top;
					if(offset < self.$rightContent.height()) {
						console.log('load more');
						self.contacts.loadContacts(num, function() {
							self.isScrolling = false;
						});
					} else {
						setTimeout(function() {
							self.isScrolling = false;
						}, self.scrollTimeoutMiliSecs);
					}
					//console.log('scroll, unseen:', offset, self.$rightContent.height());
				}
			});*/
			$('#contactphoto_fileupload').on('click', function(event, contact) {
				console.log('contact', contact);
				var metaData = contact.metaData();
				var url = OC.generateUrl(
					'apps/contacts/addressbook/{backend}/{addressBookId}/contact/{contactId}/photo',
					{backend: metaData.backend, addressBookId: metaData.addressBookId, contactId: metaData.contactId}
				);
				$(this).fileupload('option', 'url', url);
			}).fileupload({
				singleFileUploads: true,
				multipart: false,
				dataType: 'json',
				type: 'PUT',
				dropZone: null, pasteZone: null,
				acceptFileTypes: /^image\//,
				add: function(e, data) {
					var file = data.files[0];
					if (file.type.substr(0, 6) !== 'image/') {
						$(document).trigger('status.contacts.error', {
							error: true,
							message: t('contacts', 'Only images can be used as contact photos')
						});
						return;
					}
					if (file.size > parseInt($(this).siblings('[name="MAX_FILE_SIZE"]').val())) {
						$(document).trigger('status.contacts.error', {
							error: true,
							message: t(
								'contacts',
								'The size of "{filename}" exceeds the maximum allowed {size}',
								{filename: file.name, size: $(this).siblings('[name="max_human_file_size"]').val()}
							)
						});
						return;
					}
					data.submit();
				},
				start: function(e, data) {
					console.log('fileupload.start',data);
				},
				done: function (e, data) {
					console.log('Upload done:', data);
					self.editPhoto(
						data.result.metadata,
						data.result.tmp
					);
				},
				fail: function(e, data) {
					console.log('fail', data);
					var response = self.storage.formatResponse(data.jqXHR);
					$(document).trigger('status.contacts.error', response);
				}
			});

			this.$rightContent.bind('drop dragover', function (e) {
				e.preventDefault();
			});

			this.$ninjahelp.find('.close').on('click keydown',function(event) {
				if(wrongKey(event)) {
					return;
				}
				self.$ninjahelp.hide();
			});

			this.$toggleAll.on('change', function(event) {
				event.stopPropagation();
				event.preventDefault();
				var isChecked = $(this).is(':checked');
				self.setAllChecked(isChecked);
				if(self.$groups.find('option').length === 1) {
					self.buildGroupSelect();
				}
				if(isChecked) {
					self.showActions(['toggle', 'add', 'download', 'groups', 'delete', 'favorite', 'merge']);
				} else {
					self.hideActions();
				}
			});

			this.$contactList.on('change', 'input:checkbox', function(/*event*/) {
				var selected = self.contacts.getSelectedContacts();
				var id = String($(this).val());
				// Save list of last selected contact to be able to select range
				$(this).is(':checked') && self.lastSelectedContacts.indexOf(id) === -1
					? self.lastSelectedContacts.push(id)
					: self.lastSelectedContacts.splice(self.lastSelectedContacts.indexOf(id), 1);

				if(selected.length > 0 && self.$groups.find('option').length === 1) {
					self.buildGroupSelect();
				}
				if(selected.length === 0) {
					self.hideActions();
				} else if(selected.length === 1) {
					self.showActions(['toggle', 'add', 'download', 'groups', 'delete', 'favorite']);
				} else {
					self.showActions(['toggle', 'add', 'download', 'groups', 'delete', 'favorite', 'merge']);
				}
			});

			this.$contactList.on('click', 'label:not([for=select_all])', function(/*event*/) {
				var $input = $(this).prev('input');
				$input.prop('checked', !$input.prop('checked'));
				$input.trigger('change');
				return false; // Prevent opening contact
			});

			// Add title to names that would elliptisized (is that a word?)
			this.$contactList.on('mouseenter', '.nametext', function() {
				var $this = $(this);

				if($this.width() > $this.parent().width() && !$this.attr('title')) {
					$this.attr('title', $this.text());
				}
			});

			this.$sortOrder.on('change', function() {
				$(this).blur().addClass('loading');
				contacts_sortby = $(this).val();
				self.contacts.setSortOrder();
				$(this).removeClass('loading');
				self.storage.setPreference('sortby', contacts_sortby);
			});

			// Add to/remove from group multiple contacts.
			this.$groups.on('change', function() {
				var $opt = $(this).find('option:selected');
				var action = $opt.parent().data('action');
				var groupName, groupId, buildnow = false;

				var contacts = self.contacts.getSelectedContacts();
				var ids = $.map(contacts, function(c) {return c.getId();});

				self.setAllChecked(false);
				self.$toggleAll.prop('checked', false);
				if(!self.currentid) {
					self.hideActions();
				}

				if($opt.val() === 'add') { // Add new group
					action = 'add';
					console.log('add group...');
					self.$groups.val(-1);
					self.addGroup(function(response) {
						if(!response.error) {
							groupId = response.id;
							groupName = response.name;
							self.groups.addTo(ids, groupId, function(result) {
								if(!result.error) {
									$.each(ids, function(idx, id) {
										// Delay each contact to not trigger too many ajax calls
										// at a time.
										setTimeout(function() {
											var contact = self.contacts.findById(id);
											if(contact === null) {
												return true;
											}
											contact.addToGroup(groupName);
											// I don't think this is used...
											if(buildnow) {
												self.buildGroupSelect();
											}
										}, 1000);
									});
								} else {
									$(document).trigger('status.contacts.error', result);
								}
							});
						} else {
							$(document).trigger('status.contacts.error', response);
						}
					});
					return;
				}

				groupName = $opt.text(), groupId = $opt.val();

				if(action === 'add') {
					self.groups.addTo(ids, $opt.val(), function(result) {
						console.log('after add', result);
						if(!result.error) {
							$.each(result.ids, function(idx, id) {
								// Delay each contact to not trigger too many ajax calls
								// at a time.
								setTimeout(function() {
									console.log('adding', id, 'to', groupName);
									var contact = self.contacts.findById(id);
									if(contact === null) {
										return true;
									}
									contact.addToGroup(groupName);
									// I don't think this is used...
									if(buildnow) {
										self.buildGroupSelect();
									}
								}, 1000);
							});
						} else {
							var msg = result.message ? result.message : t('contacts', 'Error adding to group.');
							$(document).trigger('status.contacts.error', {message:msg});
						}
					});
					if(!buildnow) {
						self.$groups.val(-1).hide().find('optgroup,option:not([value="-1"])').remove();
					}
				} else if(action === 'remove') {
					self.groups.removeFrom(ids, $opt.val(), false, function(result) {
						console.log('after remove', result);
						if(!result.error) {
							var groupname = $opt.text(), groupid = $opt.val();
							$.each(result.ids, function(idx, id) {
								var contact = self.contacts.findById(id);
								if(contact === null) {
									return true;
								}
								contact.removeFromGroup(groupname);
								if(buildnow) {
									self.buildGroupSelect();
								}
							});
						} else {
							var msg = result.message ? result.message : t('contacts', 'Error removing from group.');
							$(document).trigger('status.contacts.error', {message:msg});
						}
					});
					if(!buildnow) {
						self.$groups.val(-1).hide().find('optgroup,option:not([value="-1"])').remove();
					}
				} // else something's wrong ;)
				self.setAllChecked(false);
			});

			this.$contactList.on('mouseenter', 'tr.contact', function(event) {
				if ($(this).data('obj').hasPermission(OC.PERMISSION_DELETE)) {
					var $td = $(this).find('td').filter(':visible').last();
					$('<a />').addClass('icon-delete svg delete action').appendTo($td);
				}
			});

			this.$contactList.on('mouseleave', 'tr.contact', function(event) {
				$(this).find('a.delete').remove();
			});

			// Prevent Firefox from selecting the table-cell
			this.$contactList.mousedown(function (event) {
				if (event.ctrlKey || event.metaKey || event.shiftKey) {
					event.preventDefault();
				}
			});

			$(window).on('click', function(event) {
				if(!$(event.target).is('a[href^="mailto"]')) {
					return;
				}
				console.log('mailto clicked', $(event.target));

				$(document).trigger('request.openurl', {
					type: 'email',
					url: $(event.target).attr('href').substr(7)
				});

				event.stopPropagation();
				event.preventDefault();
			});

			// Contact list. Either open a contact or perform an action (mailto etc.)
			this.$contactList.on('click', 'tr.contact', function(event) {
				if($(event.target).is('input') || $(event.target).is('a[href^="mailto"]')) {
					return;
				}
				// Select a single contact or a range of contacts.
				if(event.ctrlKey || event.metaKey || event.shiftKey) {
					event.stopPropagation();
					event.preventDefault();
					self.dontScroll = true;
					var $input = $(this).find('input:checkbox');
					var index = self.$contactList.find('tr.contact:visible').index($(this));
					if(event.shiftKey && self.lastSelectedContacts.length > 0) {
						self.contacts.selectRange(
							$(this).data('id'),
							self.lastSelectedContacts[self.lastSelectedContacts.length-1]
						);
					} else {
						self.contacts.setSelected($(this).data('id'), !$input.prop('checked'));
					}
					return;
				}
				if($(event.target).is('a.mailto')) {
					$(document).trigger('request.openurl', {
						type: 'email',
						url: $.trim($(this).find('.email').text())
					});
					return;
				}
				if($(event.target).is('a.delete')) {
					$(document).trigger('request.contact.delete', {
						contactId: $(this).data('id')
					});
					return;
				}
				self.openContact(String($(this).data('id')));
			});

			this.$settings.find('#app-settings-header').on('click keydown',function(event) {
				if(wrongKey(event)) {
					return;
				}
				var bodyListener = function(e) {
					if(self.$settings.find($(e.target)).length === 0) {
						self.$settings.switchClass('open', '');
					}
				};
				if(self.$settings.hasClass('open')) {
					self.$settings.switchClass('open', '');
					$('body').unbind('click', bodyListener);
				} else {
					self.$settings.switchClass('', 'open');
					$('body').bind('click', bodyListener);
				}
			});

			var addContact = function() {
				console.log('add');
				if(self.currentid) {
					if(self.currentid === 'new') {
						return;
					} else {
						var contact = self.contacts.findById(self.currentid);
						if(contact) {
							contact.close(true);
						}
					}
				}
				self.currentid = 'new';
				// Properties that the contact doesn't know
				console.log('addContact, groupid', self.currentgroup);
				var groupprops = {
					favorite: false,
					groups: self.groups.categories,
					currentgroup: {id:self.currentgroup, name:self.groups.nameById(self.currentgroup)}
				};
				self.$firstRun.hide();
				self.$contactList.show();
				self.tmpcontact = self.contacts.addContact(groupprops);
				self.tmpcontact.prependTo(self.$contactList.find('tbody')).show().find('.fullname').focus();
				self.$rightContent.scrollTop(0);
				self.hideActions();
			};

			this.$firstRun.on('click keydown', '.import', function(event) {
				event.preventDefault();
				event.stopPropagation();
				self.$settings.find('.settings').click();
			});

			this.$firstRun.on('click keydown', '.add-contact', function(event) {
				if(wrongKey(event)) {
					return;
				}
				addContact();
			});

			this.$groupList.on('click keydown', '.add-contact', function(event) {
				if(wrongKey(event)) {
					return;
				}
				addContact();
			});

			this.$contactListHeader.on('click keydown', '.delete', function(event) {
				if(wrongKey(event)) {
					return;
				}
				console.log('delete');
				if(self.currentid) {
					console.assert(typeof self.currentid === 'string', 'self.currentid is not a string');
					contactInfo = self.contacts[self.currentid].metaData();
					self.contacts.delayedDelete(contactInfo);
				} else {
					self.contacts.delayedDelete(self.contacts.getSelectedContacts());
				}
				self.hideActions();
			});

			this.$contactListHeader.on('click keydown', '.download', function(event) {
				if(wrongKey(event)) {
					return;
				}

				var doDownload = function(contacts) {
					// Only get backend, addressbookid and contactid
					contacts = $.map(contacts, function(c) {return c.metaData();});
					var targets = {};
					// Try to shorten request URI
					$.each(contacts, function(idx, contact) {
						if(!targets[contact.backend]) {
							targets[contact.backend] = {};
						}
						if(!targets[contact.backend][contact.addressBookId]) {
							targets[contact.backend][contact.addressBookId] = [];
						}
						targets[contact.backend][contact.addressBookId].push(contact.contactId);
					});
					targets = JSON.stringify(targets);
					var url = OC.generateUrl('apps/contacts/exportSelected?t={t}', {t:targets});
					//console.log('export url', url);
					document.location.href = url;
				};
				var contacts = self.contacts.getSelectedContacts();
				console.log('download', contacts.length);

				// The 300 is just based on my little testing with Apache2
				// Other web servers may fail before.
				if(contacts.length > 300) {
					OC.notify({
						message:t('contacts', 'You have selected over 300 contacts.\nThis will most likely fail! Click here to try anyway.'),
						timeout:5,
						clickhandler:function() {
							doDownload(contacts);
						}
					});
				} else {
					doDownload(contacts);
				}
			});

			this.$contactListHeader.on('click keydown', '.merge', function(event) {
				if(wrongKey(event)) {
					return;
				}
				console.log('merge');
				self.mergeSelectedContacts();
			});

			this.$contactListHeader.on('click keydown', '.favorite', function(event) {
				if(wrongKey(event)) {
					return;
				}

				var contacts = self.contacts.getSelectedContacts();

				self.setAllChecked(false);
				self.$toggleAll.prop('checked', false);
				if(!self.currentid) {
					self.hideActions();
				}

				$.each(contacts, function(idx, contact) {
					if(!self.groups.isFavorite(contact.getId())) {
						self.groups.setAsFavorite(contact.getId(), true, function(result) {
							if(result.status !== 'success') {
								$(document).trigger('status.contacts.error', {message:
									t('contacts',
										'Error setting {name} as favorite.',
										{name:contact.getDisplayName()})
								});
							}
						});
					}
				});

				self.hideActions();
			});

			this.$contactList.on('mouseenter', 'td.email', function(event) {
				if($.trim($(this).text()).length > 3) {
					$(this).find('.mailto').css('display', 'inline-block'); //.fadeIn(100);
				}
			});
			this.$contactList.on('mouseleave', 'td.email', function(event) {
				$(this).find('.mailto').fadeOut(100);
			});

			$('body').on('touchmove', function(event) {
				event.preventDefault();
			});

			$(document).on('keyup', function(event) {
				if(!$(event.target).is('body') || event.isPropagationStopped()) {
					return;
				}
				var keyCode = Math.max(event.keyCode, event.which);
				// TODO: This should go in separate method
				console.log(event, keyCode + ' ' + event.target.nodeName);
				/**
				* To add:
				* Shift-a: add addressbook
				* u (85): hide/show leftcontent
				* f (70): add field
				*/
				switch(keyCode) {
					case 13: // Enter?
						console.log('Enter?');
						if(!self.currentid && self.currentlistid) {
							self.openContact(self.currentlistid);
						}
						break;
					case 27: // Esc
						if(self.$ninjahelp.is(':visible')) {
							self.$ninjahelp.hide();
						} else if(self.currentid) {
							self.closeContact(self.currentid);
						}
						break;
					case 46: // Delete
						if(event.shiftKey) {
							self.contacts.delayedDelete(self.currentid);
						}
						break;
					case 40: // down
					case 74: // j
						console.log('next');
						if(!self.currentid && self.currentlistid) {
							self.contacts.contacts[self.currentlistid].next();
						}
						break;
					case 65: // a
						if(event.shiftKey) {
							console.log('add group?');
							break;
						}
						addContact();
						break;
					case 38: // up
					case 75: // k
						console.log('previous');
						if(!self.currentid && self.currentlistid) {
							self.contacts.contacts[self.currentlistid].prev();
						}
						break;
					case 34: // PageDown
					case 78: // n
						console.log('page down');
						break;
					case 79: // o
						console.log('open contact?');
						break;
					case 33: // PageUp
					case 80: // p
						// prev addressbook
						//OC.contacts.contacts.previousAddressbook();
						break;
					case 82: // r
						console.log('refresh - what?');
						break;
					case 63: // ? German.
						if(event.shiftKey) {
							self.$ninjahelp.toggle('fast');
						}
						break;
					case 171: // ? Danish
					case 191: // ? Standard qwerty
						self.$ninjahelp.toggle('fast').position({my: 'center', at: 'center', of: '#content'});
						break;
				}

			});

			// find all with a title attribute and tipsy them
			$('.tooltipped.downwards:not(.onfocus)').tipsy({gravity: 'n'});
			$('.tooltipped.upwards:not(.onfocus)').tipsy({gravity: 's'});
			$('.tooltipped.rightwards:not(.onfocus)').tipsy({gravity: 'w'});
			$('.tooltipped.leftwards:not(.onfocus)').tipsy({gravity: 'e'});
			$('.tooltipped.downwards.onfocus').tipsy({trigger: 'focus', gravity: 'n'});
			$('.tooltipped.rightwards.onfocus').tipsy({trigger: 'focus', gravity: 'w'});
		},
		mergeSelectedContacts: function() {
			var contacts = this.contacts.getSelectedContacts();
			this.$rightContent.append('<div id="merge_contacts_dialog"></div>');
			if(!this.$mergeContactsTmpl) {
				this.$mergeContactsTmpl = $('#mergeContactsTemplate');
			}
			var $dlg = this.$mergeContactsTmpl.octemplate();
			var $liTmpl = $dlg.find('li').detach();
			var $mergeList = $dlg.find('.mergelist');
			$.each(contacts, function(idx, contact) {
				var $li = $liTmpl
					.octemplate({idx: idx, id: contact.getId(), displayname: contact.getDisplayName()});
				if(!contact.data.thumbnail) {
					$li.addClass('thumbnail');
				} else {
					$li.css('background-image', 'url(data:image/png;base64,' + contact.data.thumbnail + ')');
				}
				if(idx === 0) {
					$li.find('input:radio').prop('checked', true);
				}
				$mergeList.append($li);
			});
			$('#merge_contacts_dialog').html($dlg).ocdialog({
				modal: true,
				closeOnEscape: true,
				title:  t('contacts', 'Merge contacts'),
				height: 'auto', width: 'auto',
				buttons: [
					{
						text: t('contacts', 'Merge contacts'),
						click:function() {
							// Do the merging, use $(this) to get dialog
							var contactid = $(this).find('input:radio:checked').val();
							var others = [];
							var deleteOther = $(this).find('#delete_other').prop('checked');
							console.log('Selected contact', contactid, 'Delete others', deleteOther);
							$.each($(this).find('input:radio:not(:checked)'), function(idx, item) {
								others.push($(item).val());
							});
							console.log('others', others);
							$(document).trigger('request.contact.merge', {
								merger: contactid,
								mergees: others,
								deleteOther: deleteOther
							});

							$(this).ocdialog('close');
						},
						defaultButton: true
					},
					{
						text: t('contacts', 'Cancel'),
						click:function() {
							$(this).ocdialog('close');
							return false;
						}
					}
				],
				close: function(/*event, ui*/) {
					$(this).ocdialog('destroy').remove();
					$('#merge_contacts_dialog').remove();
				},
				open: function(/*event, ui*/) {
					$dlg.find('input').focus();
				}
			});
		},
		addGroup: function(cb) {
			var self = this;
			this.$rightContent.append('<div id="add_group_dialog"></div>');
			if(!this.$addGroupTmpl) {
				this.$addGroupTmpl = $('#addGroupTemplate');
			}
			this.$contactList.addClass('dim');
			var $dlg = this.$addGroupTmpl.octemplate();
			$('#add_group_dialog').html($dlg).ocdialog({
				modal: true,
				closeOnEscape: true,
				title:  t('contacts', 'Add group'),
				height: 'auto', width: 'auto',
				buttons: [
					{
						text: t('contacts', 'OK'),
						click:function() {
							var name = $(this).find('input').val();
							if(name.trim() === '') {
								return false;
							}
							self.groups.addGroup(
								{name:$dlg.find('input:text').val()},
								function(response) {
									if(typeof cb === 'function') {
										cb(response);
									} else {
										if(response.error) {
											$(document).trigger('status.contacts.error', response);
										}
									}
								});
							$(this).ocdialog('close');
						},
						defaultButton: true
					},
					{
						text: t('contacts', 'Cancel'),
						click:function() {
							$(this).ocdialog('close');
							return false;
						}
					}
				],
				close: function(/*event, ui*/) {
					$(this).ocdialog('destroy').remove();
					$('#add_group_dialog').remove();
					self.$contactList.removeClass('dim');
				},
				open: function(/*event, ui*/) {
					$dlg.find('input').focus();
				}
			});
		},
		setAllChecked: function(checked) {
			var selector = checked ? 'input:checkbox:visible:not(checked)' : 'input:checkbox:visible:checked';
			$.each(this.$contactList.find(selector), function() {
				$(this).prop('checked', checked);
			});
			this.lastSelectedContacts = [];
		},
		jumpToContact: function(id) {
			this.$rightContent.scrollTop(this.contacts.contactPos(id));
		},
		closeContact: function(id) {
			$(window).unbind('hashchange', this.hashChange);
			if(this.currentid === 'new') {
				this.tmpcontact.slideUp().remove();
				this.$contactList.show();
			} else {
				var contact = this.contacts.findById(id);
				if(contact) {
					// Only show the list element if contact is in current group
					var showListElement = contact.inGroup(this.groups.nameById(this.currentgroup))
						|| ['all', 'fav', 'uncategorized'].indexOf(this.currentgroup) !== -1
						|| (this.currentgroup === 'uncategorized' && contact.groups().length === 0);
					contact.close(showListElement);
				}
			}
			delete this.currentid;
			this.hideActions();
			this.$groups.find('optgroup,option:not([value="-1"])').remove();
			if(this.contacts.length === 0) {
				$(document).trigger('status.nomorecontacts');
			}
			window.location.hash = '';
			$(window).bind('hashchange', this.hashChange);
		},
		openContact: function(id) {
			var self = this, contact;
			if(typeof id === 'undefined' || id === 'undefined') {
				console.warn('id is undefined!');
				console.trace();
			}
			console.log('Contacts.openContact', id, typeof id);
			if(this.currentid && this.currentid !== id) {
				this.closeContact(this.currentid);
			}
			contact = this.contacts.findById(id);
			if (!contact) {
				console.warn('Contact', id, 'not found. Possibly deleted');
				return;
			}
			this.currentid = id;
			this.hideActions();
			// If opened from search we can't be sure the contact is in currentgroup
			if(!contact.inGroup(this.groups.nameById(this.currentgroup))
				&& ['all', 'fav', 'uncategorized'].indexOf(this.currentgroup) === -1
			) {
				this.groups.selectGroup({id:'all'});
			}
			$(window).unbind('hashchange', this.hashChange);
			console.assert(typeof this.currentid === 'string', 'Current ID not string:' + this.currentid);
			// Properties that the contact doesn't know
			var groupprops = {
				favorite: this.groups.isFavorite(this.currentid),
				groups: this.groups.categories,
				currentgroup: {id:this.currentgroup, name:this.groups.nameById(this.currentgroup)}
			};
			if(!contact) {
				console.warn('Error opening', this.currentid);
				$(document).trigger('status.contacts.error', {
					message: t('contacts', 'Could not find contact: {id}', {id:this.currentid})
				});
				this.currentid = null;
				return;
			}
			var $contactelem = contact.renderContact(groupprops);
			var $listElement = contact.getListItemElement();
			console.log('selected element', $listElement);
			window.location.hash = this.currentid;
			$contactelem.insertAfter($listElement).show().find('.fullname').focus();
			// Remove once IE8 is finally obsoleted in oC.
			if (!OC.Util.hasSVGSupport()) {
				OC.Util.replaceSVG($contactelem);
			}
			self.jumpToContact(self.currentid);
			$listElement.hide();
			setTimeout(function() {
				$(window).bind('hashchange', self.hashChange);
			}, 500);
		},
		update: function() {
			console.log('update');
		},
		cloudPhotoSelected:function(metadata, path) {
			var self = this;
			console.log('cloudPhotoSelected', metadata);
			var url = OC.generateUrl(
				'apps/contacts/addressbook/{backend}/{addressBookId}/contact/{contactId}/photo/cacheFS',
				{backend: metadata.backend, addressBookId: metadata.addressBookId, contactId: metadata.contactId}
			);
			var jqXHR = $.getJSON(url, {path: path}, function(response) {
				console.log('response', response);
				response = self.storage.formatResponse(jqXHR);
				if(!response.error) {
					self.editPhoto(metadata, response.data.tmp);
				} else {
					$(document).trigger('status.contacts.error', response);
				}
			}).fail(function(response) {
				response = self.storage.formatResponse(jqXHR);
				console.warn('response', response);
				$(document).trigger('status.contacts.error', response);
			});
		},
		editCurrentPhoto:function(metadata) {
			var self = this;
			var url = OC.generateUrl(
				'apps/contacts/addressbook/{backend}/{addressBookId}/contact/{contactId}/photo/cacheCurrent',
				{backend: metadata.backend, addressBookId: metadata.addressBookId, contactId: metadata.contactId}
			);
			console.log('url', url);
			var jqXHR = $.getJSON(url, function(response) {
				response = self.storage.formatResponse(jqXHR);
				if(!response.error) {
					self.editPhoto(metadata, response.data.tmp);
				} else {
					$(document).trigger('status.contacts.error', response);
				}
			}).fail(function(response) {
				response = self.storage.formatResponse(jqXHR);
				console.warn('response', response);
				$(document).trigger('status.contacts.error', response);
			});
		},
		editPhoto:function(metadata, tmpkey) {
			console.log('editPhoto', metadata, tmpkey);
			$('.tipsy').remove();
			// Simple event handler, called from onChange and onSelect
			// event handlers, as per the Jcrop invocation below
			var showCoords = function(c) {
				$('#x').val(c.x);
				$('#y').val(c.y);
				$('#w').val(c.w);
				$('#h').val(c.h);
			};

			var clearCoords = function() {
				$('#coords input').val('');
			};

			var self = this;
			if(!this.$cropBoxTmpl) {
				this.$cropBoxTmpl = $('#cropBoxTemplate');
			}
			var $container = $('<div />').appendTo($('#content'));
			var $dlg = this.$cropBoxTmpl.octemplate().prependTo($container);

			$.when(this.storage.getTempContactPhoto(
				metadata.backend,
				metadata.addressBookId,
				metadata.contactId,
				tmpkey
			))
			.then(function(image) {
				var x = 5, y = 5, w = Math.min(image.width, image.height), h = w;
				//$dlg.css({'min-width': w, 'min-height': h});
				console.log(x,y,w,h);
				$(image).attr('id', 'cropbox').prependTo($dlg).show()
				.Jcrop({
					onChange:	showCoords,
					onSelect:	showCoords,
					onRelease:	clearCoords,
					//maxSize:	[w, h],
					bgColor:	'black',
					bgOpacity:	.4,
					boxWidth:	400,
					boxHeight:	400,
					setSelect:	[ x, y, w-10, h-10 ],
					aspectRatio: 1
				});
				$container.ocdialog({
					modal: true,
					closeOnEscape: true,
					title:  t('contacts', 'Edit profile picture'),
					height: image.height+100, width: image.width+20,
					buttons: [
						{
							text: t('contacts', 'Crop photo'),
							click:function() {
								self.savePhoto($(this), metadata, tmpkey, function() {
									$container.ocdialog('close');
								});
							},
							defaultButton: true
						}
					],
					close: function(/*event, ui*/) {
						$(this).ocdialog('destroy').remove();
						$container.remove();
					},
					open: function(/*event, ui*/) {
						showCoords({x:x,y:y,w:w-10,h:h-10});
					}
				});
			})
			.fail(function() {
				console.warn('Error getting temporary photo');
			});
		},
		savePhoto:function($dlg, metaData, key, cb) {
			var coords = {};
			$.each($dlg.find('#coords').serializeArray(), function(idx, coord) {
				coords[coord.name] = coord.value;
			});

			$.when(this.storage.cropContactPhoto(
				metaData.backend, metaData.addressBookId, metaData.contactId, key, coords
			))
			.then(function(response) {
				$(document).trigger('status.contact.photoupdated', {
					id: response.data.id,
					thumbnail: response.data.thumbnail
				});
			})
			.fail(function(response) {
				console.log('response', response);
				if(!response || !response.message) {
					$(document).trigger('status.contacts.error', {
						message:t('contacts', 'Network or server error. Please inform administrator.')
					});
				} else {
					$(document).trigger('status.contacts.error', response);
				}
			})
			.always(function() {
				cb();
			});
		}
	};
})(window, jQuery, OC);

$(document).ready(function() {

	$.getScript(OC.generateUrl('apps/contacts/ajax/config.js'))
	.done(function() {
		OC.Contacts.init();
	})
	.fail(function(jqxhr, settings, exception) {
		console.log('Failed loading settings.', jqxhr, settings, exception);
	});

});
