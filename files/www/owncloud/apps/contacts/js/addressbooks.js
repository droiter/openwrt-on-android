OC.Contacts = OC.Contacts || {};


(function(window, $, OC) {
	'use strict';

	var AddressBook = function(storage, book, template, isFileAction) {
		this.isFileAction = isFileAction || false;
		this.storage = storage;
		this.book = book;
		this.$template = template;
	};

	AddressBook.prototype.render = function() {
		var self = this;
		//var dialog = OC.Contacts.Dialog(null, null);
		
		this.$li = this.$template.octemplate({
			id: this.book.id,
			displayname: this.book.displayname,
			backend: this.book.backend,
			permissions: this.book.permissions
		});
		if (this.isFileAction) {
			return this.$li;
		}
		this.$li.find('a.action').tipsy({gravity: 'w'});
		if (!this.hasPermission(OC.PERMISSION_DELETE)) {
			this.$li.find('a.action.delete').hide();
		}
		if (!this.hasPermission(OC.PERMISSION_UPDATE)) {
			this.$li.find('a.action.edit').hide();
		}
		if (!this.hasPermission(OC.PERMISSION_SHARE)) {
			this.$li.find('a.action.share').hide();
		}
		if (['local', 'ldap'].indexOf(this.getBackend()) === -1) {
			this.$li.find('a.action.carddav').hide();
		}
		this.$li.find('input:checkbox').prop('checked', this.book.active).on('change', function() {
			console.log('activate', self.getId());
			var checkbox = $(this).get(0);
			self.setActive(checkbox.checked, function(response) {
				if(!response.error) {
					self.book.active = checkbox.checked;
				} else {
					checkbox.checked = !checkbox.checked;
				}
			});
		});
		this.$li.find('a.action.download')
			.attr('href', OC.generateUrl(
				'apps/contacts/addressbook/{backend}/{addressBookId}/export',
				{
					backend: this.getBackend(),
					addressBookId: this.getId()
				}
			));
		this.$li.find('a.action.delete').on('click keypress', function() {
			$('.tipsy').remove();
			console.log('delete', self.getId());
			self.destroy();
		});
		this.$li.find('a.action.carddav').on('click keypress', function() {
			var uri = (self.book.owner === oc_current_user ) ? self.book.uri : self.book.uri + '_shared_by_' + self.book.owner;
			var link = OC.linkToRemote('carddav')+'/addressbooks/'+encodeURIComponent(oc_current_user)+'/'+encodeURIComponent(uri);
			var $dropdown = $('<li><div id="dropdown" class="drop"><input type="text" value="{link}" readonly /></div></li>')
				.octemplate({link:link}).insertAfter(self.$li);
			var $input = $dropdown.find('input');
			$input.focus().get(0).select();
			$input.on('blur', function() {
				$dropdown.hide('blind', function() {
					$dropdown.remove();
				});
			});
		});
		$('#add-ldap-address-book-element').on('click keypress', function() {
			var $rightContent = $('#app-content');
			$rightContent.append('<div id="addressbook-ui-dialog"></div>');
			var $dlg = $('#addressBookConfigTemplate').octemplate({backend: 'ldap'});
			var $divDlg = $('#addressbook-ui-dialog');
			$divDlg.html($dlg).ocdialog({
				modal: true,
				closeOnEscape: true,
				title: t('contacts', 'Add new LDAP Addressbook'),
				height: 'auto',
				width: 'auto',
				buttons: [
					{
						text: t('contacts', 'Ok'),
						click: function() {
							OC.Contacts.otherBackendConfig.addressbookUiOk($divDlg);
						},
						defaultButton: true
					},
					{
						text: t('contacts', 'Cancel'),
						click: function() {
							OC.Contacts.otherBackendConfig.addressbookUiClose($divDlg);
						}
					}
				],
				close: function(/*event, ui*/) {
					OC.Contacts.otherBackendConfig.addressbookUiClose($divDlg);
				},
				open: function(/*event, ui*/) {
					OC.Contacts.otherBackendConfig.openAddressbookUi();
				}
			});
		});
		this.$li.find('a.action.edit').on('click keypress', function(event) {
			$.when(self.storage.getAddressBook(self.getBackend(), self.getId()))
			.then(function(response) {
			if(!response.error) {
				if(response.data) {
					var addressbook = response.data;
					console.log('addressbook', addressbook);
					if (addressbook.backend === 'local') {
						if($(this).data('open')) {
							return;
						}
						var editor = this;
						event.stopPropagation();
						event.preventDefault();
						var $dropdown = $('<li><div><input type="text" value="{name}" /></div></li>')
							.octemplate({name:self.getDisplayName()}).insertAfter(self.$li);
						var $input = $dropdown.find('input');
						//$input.focus().get(0).select();
						$input.addnew({
							autoOpen: true,
							//autoClose: false,
							addText: t('contacts', 'Save'),
							ok: function(event, name) {
								console.log('edit-address-book ok', name);
								$input.addClass('loading');
								self.update({displayname:name}, function(response) {
									console.log('response', response);
									if(response.error) {
										$(document).trigger('status.contacts.error', response);
									} else {
										self.setDisplayName(response.data.displayname);
										$input.addnew('close');
									}
									$input.removeClass('loading');
								});
							},
							close: function() {
								$dropdown.remove();
								$(editor).data('open', false);
							}
						});
						$(this).data('open', true);
					} else {
						var $rightContent = $('#app-content');
						$rightContent.append('<div id="addressbook-ui-dialog"></div>');
						var $dlg = $('#addressBookConfigTemplate').octemplate({backend: 'ldap'});
						var $divDlg = $('#addressbook-ui-dialog');
						//var $divDlg = $('#addressbook-ui-dialog');
						$divDlg.html($dlg).ocdialog({
							modal: true,
							closeOnEscape: true,
							title: t('contacts', 'Edit Addressbook'),
							height: 'auto', width: 'auto',
							buttons: [
								{
									text: t('contacts', 'Ok'),
									click: function() {
										OC.Contacts.otherBackendConfig.addressbookUiEditOk($divDlg);
										self.setDisplayName($('#addressbooks-ui-name').val());
									},
									defaultButton: true
								},
								{
									text: t('contacts', 'Cancel'),
									click: function() {
										OC.Contacts.otherBackendConfig.addressbookUiClose($divDlg);
									}
								}
							],
							close: function() {
								OC.Contacts.otherBackendConfig.addressbookUiClose($divDlg);
							},
							open: function() {
								OC.Contacts.otherBackendConfig.editAddressbookUI(addressbook);
							}
						});
					}
					return this.$li;
				}
			} else {
				console.warn('Addressbook getAddressbook - no data !!');
			}
			})
			.fail(function(response) {
				console.warn('Request Failed:', response.message);
				$(document).trigger('status.contacts.error', response);
			});
		});
		return this.$li;
	};

	AddressBook.prototype.getId = function() {
		return String(this.book.id);
	};

	AddressBook.prototype.getBackend = function() {
		return this.book.backend;
	};

	AddressBook.prototype.getDisplayName = function() {
		return this.book.displayname;
	};

	AddressBook.prototype.setDisplayName = function(name) {
		this.book.displayname = name;
		this.$li.find('label').text(escapeHTML(name));
	};

	AddressBook.prototype.getPermissions = function() {
		return this.book.permissions;
	};

	AddressBook.prototype.hasPermission = function(permission) {
		return (this.getPermissions() & permission);
	};

	AddressBook.prototype.getOwner = function() {
		return this.book.owner;
	};

	AddressBook.prototype.getMetaData = function() {
		return {
			permissions:this.getPermissions,
			backend: this.getBackend(),
			id: this.getId(),
			displayname: this.getDisplayName()
		};
	};

	/**
	 * Update address book in data store
	 * @param object properties An object current only supporting the property 'displayname'
	 * @param cb Optional callback function which
	 * @return An object with a boolean variable 'error'.
	 */
	AddressBook.prototype.update = function(properties, cb) {
		return $.when(this.storage.updateAddressBook(this.getBackend(), this.getId(), {properties:properties}))
			.then(function(response) {
			if(response.error) {
				$(document).trigger('status.contacts.error', response);
			}
			cb(response);
		});
	};

	AddressBook.prototype.isActive = function() {
		return this.book.active;
	};

	/**
	 * Save an address books active state to data store.
	 * @param bool state
	 * @param cb Optional callback function which
	 * @return An object with a boolean variable 'error'.
	 */
	AddressBook.prototype.setActive = function(state, cb) {
		var self = this;
		return $.when(this.storage.activateAddressBook(this.getBackend(), this.getId(), state))
			.then(function(response) {
			if(response.error) {
				$(document).trigger('status.contacts.error', response);
			} else {
				$(document).trigger('status.addressbook.activated', {
					addressbook: self,
					state: state
				});
			}
			cb(response);
		});
	};

	/**
	 * Delete a list of contacts from the data store
	 * @param array contactsIds An array of contact ids to be deleted.
	 * @param cb Optional callback function which will be passed:
	 * @return An object with a boolean variable 'error'.
	 */
	AddressBook.prototype.deleteContacts = function(contactsIds, cb) {
		console.log('deleteContacts', contactsIds);
		return $.when(this.storage.deleteContacts(this.getBackend(), this.getId(), contactsIds))
			.then(function(response) {
			if(response.error) {
				$(document).trigger('status.contacts.error', response);
			}
			if(typeof cb === 'function') {
				cb(response);
			}
		});
	};

	/**
	 * Delete address book from data store and remove it from the DOM
	 * @return An object with a boolean variable 'error'.
	 */
	AddressBook.prototype.destroy = function() {
		var self = this;
		$.when(this.storage.deleteAddressBook(this.getBackend(), self.getId()))
			.then(function(response) {
			if(!response.error) {
				self.$li.remove();
				$(document).trigger('status.addressbook.removed', {
					addressbook: self
				});
			} else {
				$(document).trigger('status.contacts.error', response);
			}
		}).fail(function(response) {
			console.log(response.message);
			$(document).trigger('status.contacts.error', response);
		});
	};

	/**
	 * Controls access to address books
	 */
	var AddressBookList = function(
			storage,
			bookTemplate,
			bookItemTemplate,
			isFileAction
		) {
		var self = this;
		this.isFileAction = isFileAction || false;
		this.storage = storage;
		this.$bookTemplate = bookTemplate;
		this.$bookList = this.$bookTemplate.find('.addressbooklist');
		this.$bookItemTemplate = bookItemTemplate;
		this.$importIntoSelect = this.$bookTemplate.find('#import_into');
		this.$importFormatSelect = this.$bookTemplate.find('#import_format');
		this.$importProgress = this.$bookTemplate.find('#import-status-progress');
		this.$importStatusText = this.$bookTemplate.find('#import-status-text');
		this.addressBooks = [];

		if(this.isFileAction) {
			return;
		}
		this.$importFileInput = this.$bookTemplate.find('#import_upload_start');
		var $addInput = this.$bookTemplate.find('#add-address-book');
		$addInput.addnew({
			ok: function(event, name) {
				console.log('add-address-book ok', name);
				$addInput.addClass('loading');
				self.add(name, function(response) {
					console.log('response', response);
					if(response.error) {
						$(document).trigger('status.contacts.error', response);
					} else {
						$(this).addnew('close');
					}
					$addInput.removeClass('loading');
				});
			}
		});

		$(document).bind('status.addressbook.removed', function(e, data) {
			var addressBook = data.addressbook;
			self.addressBooks.splice(self.addressBooks.indexOf(addressBook), 1);
			self.buildImportSelect();
		});
		$(document).bind('status.addressbook.added', function() {
			self.buildImportSelect();
		})
		this.$importFormatSelect.on('change', function() {
			self.$importIntoSelect.trigger('change');
		});
		this.$importIntoSelect.on('change', function() {
			// Disable file input if no address book selected
			var value = $(this).val();
			self.$importFileInput.prop('disabled', value === '-1' );
			if(value !== '-1') {
				var url = OC.generateUrl(
					'apps/contacts/addressbook/{backend}/{addressBookId}/{importType}/import/upload',
					{
						addressBookId:value,
						importType:self.$importFormatSelect.find('option:selected').val(),
						backend: $(this).find('option:selected').data('backend')
					}
				);
				self.$importFileInput.fileupload('option', 'url', url);
			}
		});
		this.$importFileInput.fileupload({
			dataType: 'json',
			start: function(/*e, data*/) {
				self.$importProgress.progressbar({value:false});
				$('.tipsy').remove();
				$('.import-upload').hide();
				$('.import-status').show();
				self.$importProgress.fadeIn();
				self.$importStatusText.text(t('contacts', 'Starting file import'));
			},
			done: function (e, data) {
				if ($('#import_format').find('option:selected').val() != 'automatic') {
					$('#import-status-text').text(t('contacts', 'Format selected: {format}',
													{format: $('#import_format').find('option:selected').text() }));
				} else {
					$('#import-status-text').text(t('contacts', 'Automatic format detection'));
				}
				console.log('Upload done:', data);
				self.doImport(self.storage.formatResponse(data.jqXHR));
			},
			fail: function(e, data) {
				console.log('fail', data);
				OC.notify({message:data.errorThrown + ': ' + data.textStatus});
				$('.import-upload').show();
				$('.import-status').hide();
			}
		});
	};

	AddressBookList.prototype.count = function() {
		return this.addressBooks.length;
	};

	/**
	 * For importing from oC filesyatem
	 */
	AddressBookList.prototype.prepareImport = function(backend, addressBookId, importType, path, fileName) {
		console.log('prepareImport', backend, addressBookId, importType, path, fileName);
		this.$importProgress.progressbar({value:false});
		if (importType != 'automatic') {
			this.$importStatusText.text(t('contacts', 'Format selected: {format}',
											{format: self.$importFormatSelect.find('option:selected').val() }));
		} else {
			this.$importStatusText.text(t('contacts', 'Automatic format detection'));
		}
		return this.storage.prepareImport(
				backend, addressBookId, importType,
				{filename:fileName, path:path}
			);
	};

	AddressBookList.prototype.doImport = function(response) {
		console.log('doImport', response);
		var defer = $.Deferred();
		var done = false;
		var interval = null, isChecking = false;
		var self = this;
		var closeImport = function() {
			defer.resolve();
			self.$importProgress.fadeOut();
			setTimeout(function() {
				$('.import-upload').show();
				$('.import-status').hide();
				self.importCount = null;
				if(self.$importProgress.hasClass('ui-progressbar')) {
					self.$importProgress.progressbar('destroy');
				}
			}, 3000);
		};
		if(!response.error) {
			this.$importProgress.progressbar('value', 0);
			var data = response.data;
			var getStatus = function(backend, addressbookid, importType, progresskey, interval, done) {
				if(done) {
					clearInterval(interval);
					closeImport();
					return;
				}
				if(isChecking) {
					return;
				}
				isChecking = true;
				$.when(
					self.storage.importStatus(
						backend, addressbookid, importType,
						{progresskey:progresskey}
					))
				.then(function(response) {
					if(!response.error) {
						console.log('status, response: ', response);
						if (response.data.total != null && response.data.progress != null) {
							self.$importProgress.progressbar('option', 'max', Number(response.data.total));
							self.$importProgress.progressbar('value', Number(response.data.progress));
							self.$importStatusText.text(t('contacts', 'Processing {count}/{total} cards',
														{count: response.data.progress, total: response.data.total}));
						}
					} else {
						console.warn('Error', response.message);
						self.$importStatusText.text(response.message);
					}
					isChecking = false;
				}).fail(function(response) {
					console.log(response.message);
					$(document).trigger('status.contacts.error', response);
					isChecking = false;
				});
			};
			$.when(
				self.storage.startImport(
					data.backend, data.addressBookId, data.importType,
					{filename:data.filename, progresskey:data.progresskey}
				)
			)
			.then(function(response) {
				console.log('response', response);
				if(!response.error) {
					console.log('Import done');
					self.$importStatusText.text(t('contacts', 'Total:{total}, Success:{imported}, Errors:{failed}',
													  {total: response.data.total, imported:response.data.imported, failed: response.data.failed}));
					var addressBook = self.find({id:response.data.addressBookId, backend: response.data.backend});
					$(document).trigger('status.addressbook.imported', {
						addressbook: addressBook
					});
					defer.resolve();
				} else {
					defer.reject(response);
					self.$importStatusText.text(response.message);
					$(document).trigger('status.contacts.error', response);
				}
				done = true;
			}).fail(function(response) {
				defer.reject(response);
				console.log(response.message);
				$(document).trigger('status.contacts.error', response);
				done = true;
			});
			interval = setInterval(function() {
				getStatus(data.backend, data.addressBookId, data.importType, data.progresskey, interval, done);
			}, 1500);
		} else {
			defer.reject(response);
			done = true;
			self.$importStatusText.text(response.message);
			closeImport();
			$(document).trigger('status.contacts.error', response);
		}
		return defer;
	};

	/**
	 * Rebuild the select to choose which address book to import into.
	 */
	AddressBookList.prototype.buildImportSelect = function() {
		console.log('buildImportSelect');
		var self = this;
		this.$importIntoSelect.find('option:not([value="-1"])').remove();
		var addressBooks = this.selectByPermission(OC.PERMISSION_UPDATE);
		$.each(addressBooks, function(idx, book) {
			var $opt = $('<option />');
			$opt.val(book.getId()).text(book.getDisplayName()).data('backend', book.getBackend());
			self.$importIntoSelect.append($opt);
			console.log('appending', $opt, 'to', self.$importIntoSelect);
		});
		if(!this.isFileAction) {
			if(addressBooks.length === 1) {
				this.$importIntoSelect.val(this.$importIntoSelect.find('option:not([value="-1"])').first().val()).hide().trigger('change');
				self.$importFileInput.prop('disabled', false);
			} else {
				this.$importIntoSelect.show();
				self.$importFileInput.prop('disabled', true);
			}
		}
	};

	/**
	 * Create an AddressBook object, save it in internal list and append it's rendered result to the list
	 *
	 * @param object addressBook
	 * @param bool rebuild If true rebuild the address book select for import.
	 * @return AddressBook
	 */
	AddressBookList.prototype.insertAddressBook = function(addressBook) {
		var book = new AddressBook(this.storage, addressBook, this.$bookItemTemplate, this.isFileAction);
		if(!this.isFileAction) {
			var result = book.render();
			this.$bookList.append(result);
		}
		this.addressBooks.push(book);
		return book;
	};

	/**
	 * Get an AddressBook
	 *
	 * @param object info An object with the string  properties 'id' and 'backend'
	 * @return AddressBook|null
	 */
	AddressBookList.prototype.find = function(info) {
		console.log('AddressBookList.find', info);
		var addressBook = null;
		$.each(this.addressBooks, function(idx, book) {
			if(book.getId() === String(info.id) && book.getBackend() === info.backend) {
				addressBook = book;
				return false; // break loop
			}
		});
		return addressBook;
	};

	/**
	 * Move a contacts from one address book to another..
	 *
	 * @param Contact The contact to move
	 * @param object from An object with properties 'id' and 'backend'.
	 * @param object target An object with properties 'id' and 'backend'.
	 */
	AddressBookList.prototype.moveContact = function(contact, from, target) {
		console.log('AddressBookList.moveContact, contact', contact, from, target);
		$.when(this.storage.moveContact(from.backend, from.id, contact.getId(), {target:target}))
			.then(function(response) {
			if(!response.error) {
				console.log('Contact moved', response);
				$(document).trigger('status.contact.moved', {
					contact: contact,
					data: response.data
				});
			} else {
				$(document).trigger('status.contacts.error', response);
			}
		});
	};

	/**
	 * Get an array of address books with at least the required permission.
	 *
	 * @param int permission
	 * @param bool noClone If true the original objects will be returned and can be manipulated.
	 */
	AddressBookList.prototype.selectByPermission = function(permission, noClone) {
		var books = [];
		$.each(this.addressBooks, function(idx, book) {
			if(book.getPermissions() & permission) {
				// Clone the address book not to mess with with original
				books.push(noClone ? book : $.extend(true, {}, book));
			}
		});
		return books;
	};

	/**
	 * Add a new address book.
	 *
	 * @param string name
	 * @param function cb
	 * @return jQuery.Deferred
	 * @throws Error
	 */
	AddressBookList.prototype.add = function(name, cb) {
		console.log('AddressBookList.add', name, typeof cb);
		var defer = $.Deferred();
		// Check for wrong, duplicate or empty name
		if(typeof name !== 'string') {
			throw new TypeError('BadArgument: AddressBookList.add() only takes String arguments.');
		}
		if(name.trim() === '') {
			throw new Error('BadArgument: Cannot add an address book with an empty name.');
		}
		var error = '';
		$.each(this.addressBooks, function(idx, book) {
			if(book.getDisplayName() === name) {
				console.log('Dupe');
				error = t('contacts', 'An address book called {name} already exists', {name:name});
				if(typeof cb === 'function') {
					cb({error:true, message:error});
				}
				defer.reject({error:true, message:error});
				return false; // break loop
			}
		});
		if(error.length) {
			console.warn('Error:', error);
			return defer;
		}
		var self = this;
		$.when(this.storage.addAddressBook('local',
		{displayname: name, description: ''})).then(function(response) {
			if(response.error) {
				error = response.message;
				if(typeof cb === 'function') {
					cb({error:true, message:error});
				}
				defer.reject(response);
			} else {
				var book = self.insertAddressBook(response.data);
				$(document).trigger('status.addressbook.added');
				if(typeof cb === 'function') {
					cb({error:false, addressbook: book});
				}
				defer.resolve({error:false, addressbook: book});
			}
		})
		.fail(function(jqxhr, textStatus, error) {
			$(this).removeClass('loading');
			var err = textStatus + ', ' + error;
			console.log('Request Failed', + err);
			error = t('contacts', 'Failed adding address book: {error}', {error:err});
			if(typeof cb === 'function') {
				cb({error:true, message:error});
			}
			defer.reject({error:true, message:error});
		});
		return defer;
	};

	/**
	* Load address books
	*/
	AddressBookList.prototype.loadAddressBooks = function() {
		var self = this;
		var defer = $.Deferred();
		$.when(this.storage.getAddressBooksForUser()).then(function(response) {
			if(!response.error) {
				$.each(response.data.addressbooks, function(idx, addressBook) {
					self.insertAddressBook(addressBook);
				});
				self.buildImportSelect();
				console.log('After buildImportSelect');
				if(!self.isFileAction) {
					if(typeof OC.Share !== 'undefined') {
						OC.Share.loadIcons('addressbook');
					} else {
						self.$bookList.find('a.action.share').css('display', 'none');
					}
				}
				console.log('Before resolve');
				defer.resolve(self.addressBooks);
				console.log('After resolve');
			} else {
				defer.reject(response);
				$(document).trigger('status.contacts.error', response);
			}
		})
		.fail(function(response) {
			console.warn('Request Failed:', response);
			defer.reject({
				error: true,
				message: t('contacts', 'Failed loading address books: {error}', {error:response.message})
			});
		});
		return defer.promise();
	};

	OC.Contacts.AddressBookList = AddressBookList;

})(window, jQuery, OC);
