OC.Contacts = OC.Contacts || {};


(function(window, $, OC) {
	'use strict';

	/**
	 * GroupList object
	 * Currently all data but the categories array is saved in the jquery DOM elements.
	 * This may change in the future.
	 * Each group element has a data-id and a data-type attribute. data-type can be
	 * 'fav' for favorites, 'all' for all elements, 'category' for group and 'shared' for
	 * a shared addressbook.
	 * data-id can be 'fav', 'all' or a numeric group or addressbook id.
	 * In addition each elements holds data entries for:
	 *   'contacts': An array of contact ids belonging to that group
	 *   'obj': A reference to the groupList object.
	 */
	var GroupList = function(storage, groupList, listItemTmpl) {
		this.storage = storage;
		this.$groupList = groupList;
		var self = this;

		this.$groupList.on('click', 'li.group', function(event) {
			$('.tipsy').remove();
			if(wrongKey(event)) {
				return;
			}
			console.log('group click', $(this));
			if($(event.target).is('.action.delete')) {
				$('.tipsy').remove();
				$(this).addClass('loading').removeClass('active');
				event.stopPropagation();
				event.preventDefault();
				var id = $(event.target).parents('li').first().data('id');
				self.deleteGroup(id, function(response) {
					if(response.error) {
						OC.notify({message:response.message});
					}
				});
			} else if($(event.target).is('.action.edit')) {
				event.stopPropagation();
				event.preventDefault();
				self.editGroup($(this));
			} else {
				if($(this).is(':not(.editing)')) {
					self.selectGroup({element:$(this)});
				}
			}
		});
		var $addInput = this.$groupList.find('.add-group');
		$addInput.addnew({
			addText: t('core', 'Add'),
			ok: function(event, name) {
				$addInput.addClass('loading');
				name = escapeHTML(name);
				self.addGroup({name:name}, function(response) {
					if(response.error) {
						$(document).trigger('status.contacts.error', response);
					} else {
						$addInput.addnew('close');
					}
					$addInput.removeClass('loading');
				});
			}
		});

		$(document).bind('status.contacts.count', function(e, data) {
			console.log('Num contacts:', data.count);
			self.findById('all').find('.numcontacts').text(data.count);
		});

		this.$groupListItemTemplate = listItemTmpl;
		this.categories = [];
	};

	/**
	 * Set a group as being currently selected
	 * 
	 * @param object params. A map containing either a group id
	 * or a jQuery group element.
	 * This triggers a 'status.group.selected' event unless if
	 * the group hasn't been saved/created yet.
	 */
	GroupList.prototype.selectGroup = function(params) {
		var self = this;
		/*if(!this.loaded) {
			console.log('Not loaded');
			setTimeout(function() {
				self.selectGroup(params);
			}, 100);
			return;
		}*/
		var id, $elem;
		if(typeof params.id !== 'undefined') {
			id = params.id;
			$elem = this.findById(id);
		} else if(typeof params.element !== 'undefined') {
			id = params.element.data('id');
			$elem = params.element;
		}
		if(!$elem.length) {
			self.selectGroup({id:'all'});
			return;
		}
		console.log('selectGroup', id, $elem);
		this.$groupList.find('li').removeClass('active');
		$elem.addClass('active');
		if(id === 'new') {
			return;
		}
		this.lastgroup = id;
		$(document).trigger('status.group.selected', {
			id: this.lastgroup,
			type: $elem.data('type'),
			contacts: $elem.data('contacts')
		});
	};

	GroupList.prototype.triggerLastGroup = function() {
		this.selectGroup({id:this.lastgroup});
	};

	/**
	 * Test if a group with this name exists (case-insensitive)
	 *
	 * @param string name
	 * @return bool
	 */
	GroupList.prototype.hasGroup = function(name) {
		return (this.findByName(name) !== null);
	};

	/**
	 * Get the group name by id.
	 * 
	 * Kind of a hack. Need to get the immidiate text without the enclosed spans with number etc.
	 * Not sure this works in IE8 and maybe others.
	 * 
	 * @param integer id. The numeric group or addressbook id.
	 * @returns string The name of the group.
	 */
	GroupList.prototype.nameById = function(id) {
		return $.trim(this.findById(id).data('rawname'));
		//return $.trim(this.findById(id).clone().find("*").remove().end().text()); //.contents().filter(function(){ return(this.nodeType == 3); }).text().trim();
	};

	/** Get the group element by name.
	 *
	 * @param string name. The name of the group to search for (case-insensitive).
	 * @returns object|null The jQuery object.
	 */
	GroupList.prototype.findByName = function(name) {
		var $elem = null;
		this.$groupList.find('li[data-type="category"]').each(function() {
			if ($(this).data('rawname').toLowerCase() === name.toLowerCase()) {
				$elem = $(this);
				return false; //break out of loop
			}
		});
		return $elem;
	};

	/** Get the group element by id.
	 * 
	 * @param integer id. The numeric group or addressbook id.
	 * @returns object The jQuery object.
	 */
	GroupList.prototype.findById = function(id) {
		return this.$groupList.find('li[data-id="' + id + '"]');
	};

	/**
	 * Check if a contact is favorited.
	 * @param integer contactid.
	 * @returns boolean.
	 */
	GroupList.prototype.isFavorite = function(contactid) {
		return this.inGroup(contactid, 'fav');
	};

	/**
	 * Check if a contact is in a specfic group.
	 * @param integer contactid.
	 * @param integer groupid.
	 * @returns boolean.
	 */
	GroupList.prototype.inGroup = function(contactid, groupid) {
		var $groupelem = this.findById(groupid);
		var contacts = $groupelem.data('contacts');
		return (contacts.indexOf(contactid) !== -1);
	};

	/**
	 * Mark/unmark a contact as favorite.
	 * 
	 * @param integer contactid.
	 * @param boolean state.
	 * @param function cb. Optional callback function.
	 */
	GroupList.prototype.setAsFavorite = function(contactid, state, cb) {
		var $groupelem = this.findById('fav');
		var contacts = $groupelem.data('contacts');
		if(state) {
			$.when(OC.Tags.addToFavorites(contactid, 'contact'))
			.then(function(response) {
				console.log(response);
				contacts.push(contactid);
				$groupelem.data('contacts', contacts);
				$groupelem.find('.numcontacts').text(contacts.length > 0 && contacts.length || '');
				if(contacts.length > 0 && $groupelem.is(':hidden')) {
					$groupelem.show();
				}
				if(typeof cb === 'function') {
					cb(response);
				}
			})
			.fail(function(response) {
				console.warn(response);
			});
		} else {
			$.when(OC.Tags.removeFromFavorites(contactid, 'contact'))
			.then(function(response) {
				contacts.splice(contacts.indexOf(contactid), 1);
				//console.log('contacts', contacts, contacts.indexOf(id), contacts.indexOf(String(id)));
				$groupelem.data('contacts', contacts);
				$groupelem.find('.numcontacts').text(contacts.length > 0 && contacts.length || '');
				if(contacts.length === 0 && $groupelem.is(':visible')) {
					$groupelem.hide();
				}
				if(typeof cb === 'function') {
					cb(response);
				}
			})
			.fail(function(response) {
				console.warn(response);
			});
		}
	};

	/**
	* Add one or more contact ids to a group
	* @param integer|array contactid. An integer id or an array of integer ids.
	* @param integer groupid. The integer id of the group
	* @param function cb. Optional call-back function
	*/
	GroupList.prototype.addTo = function(contactid, groupid, cb) {
		console.log('GroupList.addTo', contactid, groupid);
		var $groupelem = this.findById(groupid);
		var contacts = $groupelem.data('contacts');
		var ids = [];
		if(!contacts) {
			console.log('Contacts not found, adding list!!!');
			contacts = [];
		}
		var self = this;
		var doPost = false;
		if(typeof contactid === 'string') {
			if(contacts.indexOf(contactid) === -1) {
				ids.push(contactid);
				doPost = true;
			} else {
				if(typeof cb === 'function') {
					cb({error:true, message:t('contacts', 'Contact is already in this group.')});
				}
			}
		} else if(utils.isArray(contactid)) {
			$.each(contactid, function(i, id) {
				if(contacts.indexOf(id) === -1) {
					ids.push(id);
				}
			});
			if(ids.length > 0) {
				doPost = true;
			} else {
				if(typeof cb === 'function') {
					cb({error:true, message:t('contacts', 'Contacts are already in this group.')});
				}
			}
		} else {
			console.warn('Invalid data type: ' + typeof contactid);
		}
		if(doPost) {
			var groupname = self.nameById(groupid);
			$.when(this.storage.addToGroup(ids, groupid, groupname)).then(function(response) {
				if(!response.error) {
					contacts = contacts.concat(ids).sort();
					$groupelem.data('contacts', contacts);
					var $numelem = $groupelem.find('.numcontacts');
					$numelem.text(contacts.length > 0 && contacts.length || '').switchClass('', 'active', 200);
					setTimeout(function() {
						$numelem.switchClass('active', '', 1000);
					}, 2000);
					if(typeof cb === 'function') {
						cb({ids:ids});
					}
					$.each(ids, function(idx, contactid) {
						$(document).trigger('status.group.contactadded', {
							contactid: contactid,
							groupid: groupid,
							groupname: groupname
						});
					});
				} else {
					if(typeof cb === 'function') {
						cb({error:true, message:response.message});
					}
				}
			});
		}
	};

	/**
	* Removes one or more contact ids from a group
	* TODO: When deleting contacts this method should just remove the contact id
	* from its internal list without saving.
	* @param integer|array contactid. An integer id or an array of integer ids.
	* @param integer groupid. The integer id of the group
	* @param boolean onlyInternal If true don't save to backend
	* @param function cb. Optional call-back function
	*/
	GroupList.prototype.removeFrom = function(contactid, groupid, onlyInternal, cb) {
		console.log('GroupList.removeFrom', contactid, groupid);
		var $groupelem = this.findById(groupid);
		var groupname = this.nameById(groupid);
		var contacts = $groupelem.data('contacts');
		var ids = [];

		// If it's the 'all' group simply decrement the number
		if(groupid === 'all') {
			var $numelem = $groupelem.find('.numcontacts');
			$numelem.text(parseInt($numelem.text()-1)).switchClass('', 'active', 200);
			setTimeout(function() {
				$numelem.switchClass('active', '', 1000);
			}, 2000);
			if(typeof cb === 'function') {
				cb({ids:[id]});
			}
		}
		// If the contact is in the category remove it from internal list.
		if(!contacts) {
			if(typeof cb === 'function') {
				cb({error:true, message:t('contacts', 'Couldn\'t get contact list.')});
			}
			return;
		}
		var doPost = false;
		if(typeof contactid === 'string') {
			if(contacts.indexOf(contactid) !== -1) {
				ids.push(contactid);
				doPost = true;
			} else {
				if(typeof cb === 'function') {
					cb({error:true, message:t('contacts', 'Contact is not in this group.')});
				}
			}
		} else if(utils.isArray(contactid)) {
			$.each(contactid, function(i, id) {
				if(contacts.indexOf(id) !== -1) {
					ids.push(id);
				}
			});
			if(ids.length > 0) {
				doPost = true;
			} else {
				console.log(contactid, 'not in', contacts);
				if(typeof cb === 'function') {
					cb({error:true, message:t('contacts', 'Contacts are not in this group.')});
				}
			}
		}
		$.each(ids, function(idx, id) {
			contacts.splice(contacts.indexOf(id), 1);
		});
		$groupelem.find('.numcontacts').text(contacts.length > 0 && contacts.length || '');
		//console.log('contacts', contacts, contacts.indexOf(id), contacts.indexOf(String(id)));
		$groupelem.data('contacts', contacts);
		if(doPost) {
			// If a group is selected the contact has to be removed from the list
			$.each(ids, function(idx, contactid) {
				$(document).trigger('status.group.contactremoved', {
					contactid: contactid,
					groupid: parseInt(groupid),
					groupname: groupname
				});
			});
			if(!onlyInternal) {
				$.when(this.storage.removeFromGroup(ids, groupid, groupname)).then(function(response) {
					if(!response.error) {
						if(typeof cb === 'function') {
							cb({ids:ids});
						}
					} else {
						if(typeof cb === 'function') {
							cb({error:true, message:response.message});
						}
					}
				});
			}
		}
	};

	/**
	 * Remove a contact from all groups. Used on contact deletion.
	 * 
	 * @param integer contactid.
	 * @param boolean alsoSpecial. Whether the contact should also be
	 *    removed from non 'category' groups.
	 * @param boolean onlyInternal If true don't save to backend
	 */
	GroupList.prototype.removeFromAll = function(contactid, alsoSpecial, onlyInternal) {
		var self = this;
		var selector = alsoSpecial ? 'li' : 'li[data-type="category"]';
		$.each(this.$groupList.find(selector), function() {
			self.removeFrom(contactid, $(this).data('id'), onlyInternal);
		});
	};

	/**
	 * Handler that will be called by OCCategories if any groups have changed.
	 * This is called when categories are edited by the generic categories edit
	 * dialog, and will probably not be used in this app.
	 */
	GroupList.prototype.categoriesChanged = function(newcategories) {
		console.log('GroupList.categoriesChanged, I should do something with them:', newcategories);
	};

	/**
	 * Drop handler for for adding contact to group/favorites.
	 * FIXME: The drag helper object goes below the group elements
	 * during drag, and the drop target is hard to hit.
	 */
	GroupList.prototype.contactDropped = function(event, ui) {
		var dragitem = ui.draggable;
		console.log('dropped', dragitem);
		if(dragitem.is('.name')) {
			var id = String(dragitem.parent().data('id'));
			console.log('contact dropped', id, 'on', $(this).data('id'));
			if($(this).data('type') === 'fav') {
				$(this).data('obj').setAsFavorite(id, true);
			} else {
				$(this).data('obj').addTo(id, $(this).data('id'));
			}
		}
	};

	/**
	 * Remove a group from backend.
	 * 
	 * On success this triggers a 'status.group.groupremoved' event with an object
	 * containing the properties:
	 * 
	 *   groupid: The numeric id of the removed group
	 *   groupname: The string value of the group.
	 *   newgroupid: The id of the group that is selected after deletion.
	 *   contacts: An array of integer ids of contacts that must updated.
	 * 
	 * The handler for that event must take care of updating all contact objects
	 * internal CATEGORIES value and saving them to backend.
	 * 
	 * @param integer groupid.
	 * @param function cb. Optional callback function.
	 */
	GroupList.prototype.deleteGroup = function(groupid, cb) {
		var $elem = this.findById(groupid);
		var $newelem = $elem.prev('li');
		var name = this.nameById(groupid);
		var contacts = $elem.data('contacts');
		var self = this;
		console.log('delete group', $elem, groupid, contacts);
		$.when(this.storage.deleteGroup(name)).then(function(response) {
			if (!response.error) {
				$.each(self.categories, function(idx, category) {
					if(category.id === groupid) {
						self.categories.splice(self.categories.indexOf(category), 1);
						return false; // Break loop
					}
				});
				$(document).trigger('status.group.groupremoved', {
					groupid: groupid,
					newgroupid: parseInt($newelem.data('id')),
					groupname: name,
					contacts: contacts
				});
				$elem.remove();
				self.selectGroup({element:$newelem});
			} else {
				console.log('Error', response);
			}
			if(typeof cb === 'function') {
				cb(response);
			}
		})
		.fail(function(response) {
			console.log('Request Failed:', response);
			$(document).trigger('status.contacts.error', response);
		});
	};

	/**
	 * Edit a groups name.
	 * 
	 * @param object $element jQuery element
	 */
	GroupList.prototype.editGroup = function($element) {
		console.log('editGroup', $element);
		var self = this;
		var oldname = $element.data('rawname');
		var id = $element.data('id');

		var $editInput = $('<input type="text" />').val(oldname);
		$element.hide();
		$editInput.insertBefore($element).wrap('<li class="group editing" />');
		var $tmpelem = $editInput.parent('li');
		console.log('tmpelem', $tmpelem);

		$editInput.addnew({
			autoOpen: true,
			addText: t('contacts', 'Save'),
			ok: function(event, newname) {
				console.log('New name', newname);
				if(self.hasGroup(newname)) {
					$(document).trigger('status.contacts.error', {
						error: true,
						message: t('contacts', 'A group named "{group}" already exists', {group: escapeHTML(newname)})
					});
					return;
				}
				$editInput.addClass('loading');
				self.renameGroup(oldname, newname, function(response) {
					if(response.error) {
						$(document).trigger('status.contacts.error', response);
					} else {
						$editInput.addnew('close');
						$(document).trigger('status.group.grouprenamed', {
							groupid: id,
							from: oldname,
							to: newname,
							contacts: $element.data('contacts')
						});
						$element.data('rawname', newname);
						$element.find('.name').text(escapeHTML(newname));
					}
					$editInput.removeClass('loading');
				});
			},
			cancel: function(event) {
				console.log('cancel');
				$editInput.removeClass('loading');
			},
			close: function() {
				console.log('close');
				$tmpelem.remove();
				$element.show();
			}
		});

	};

	/**
	 * Rename a group.
	 *
	 * @param string from
	 * @param string to
	 * @param function cb
	 */
	GroupList.prototype.renameGroup = function(from, to, cb) {
		$.when(this.storage.renameGroup(from, to)).then(function(response) {
			cb({error:false});
		})
		.fail(function(response) {
			console.log('Request Failed:', response);
			cb({error:true});
			response.message = t('contacts', 'Failed renaming group: {error}', {error:response.message});
			$(document).trigger('status.contacts.error', response);
		});
	};

	/**
	 * Add a new group.
	 * 
	 * After the addition a group element will be inserted in the list of group
	 * elements with data-type="category".
	 * NOTE: The element is inserted (semi) alphabetically, but since group elements
	 * can now be rearranged by dragging them it should probably be dropped.
	 * 
	 * @param object params. Map that can have the following properties:
	 *   'name': Mandatory. If a group with the same name already exists
	 *       (not case sensitive) the callback will be called with its 'status'
	 *       set to 'error' and the function returns.
	 *   'element': A jQuery group element. If this property isn't present
	 *       a new element will be created.
	 * @param function cb. On success the only parameter is an object with
	 *    'status': 'success', id: new id from the backend and 'name' the group name.
	 *     On error 'status' will be 'error' and 'message' will hold any error message
	 *     from the backend.
	 */
	GroupList.prototype.addGroup = function(params, cb) {
		//console.log('GroupList.addGroup', params);
		var name = params.name;
		var contacts = []; // $.map(contacts, function(c) {return parseInt(c)});
		var self = this;
		if(this.hasGroup(name)) {
			if(typeof cb === 'function') {
				cb({error:true, message:t('contacts', 'A group named "{group}" already exists', {group: escapeHTML(name)})});
			}
			return;
		}
		$.when(this.storage.addGroup(name)).then(function(response) {
			if (!response.error) {
				name = response.data.name;
				var id = response.data.id;
				var tmpl = self.$groupListItemTemplate;
				var $elem = (tmpl).octemplate({
						id: id,
						type: 'category',
						num: 0+contacts.length,
						name: escapeHTML(name)
					});
				self.categories.push({id: id, name: name});
				$elem.data('obj', self);
				$elem.data('contacts', contacts);
				$elem.data('rawname', name);
				$elem.data('id', id);
				$elem.droppable({
					drop: self.contactDropped,
					activeClass: 'ui-state-active',
					hoverClass: 'ui-state-hover',
					scope: 'contacts'
				});
				var added = false;
				self.$groupList.find('li.group[data-type="category"]').each(function() {
					if ($(this).data('rawname').toLowerCase().localeCompare(name.toLowerCase()) > 0) {
						$(this).before($elem);
						added = true;
						return false;
					}
				});
				if(!added) {
					var $insertAfter = self.$groupList.find('li.group[data-type="category"]').last()
						|| self.$groupList.find('li.group[data-id="fav"]')
						|| self.$groupList.find('li.group[data-id="all"]');
					$elem.insertAfter($insertAfter);
				}
				self.selectGroup({element:$elem});
				$elem.tipsy({trigger:'manual', gravity:'w', fallback: t('contacts', 'You can drag groups to\narrange them as you like.')});
				$elem.tipsy('show');
				if(typeof cb === 'function') {
					cb({id:parseInt(id), name:name});
				}
			} else {
				if(typeof cb === 'function') {
					cb({error:true, message:response.data.message});
				}
			}
		})
		.fail(function(response) {
			console.log('Request Failed:', response);
			response.message = t('contacts', 'Failed adding group: {error}', {error:response.message});
			$(document).trigger('status.contacts.error', response);
		});
	};

	GroupList.prototype.loadGroups = function(cb) {
		var self = this;
		var acceptdrop = '.dragContact';
		var $groupList = this.$groupList;
		var tmpl = this.$groupListItemTemplate;
		var $elem;

		if(!this.findById('all').length) {
			tmpl.octemplate({id: 'all', type: 'all', num: '', name: t('contacts', 'All')}).appendTo($groupList);
		}
		return $.when(this.storage.getGroupsForUser()).then(function(response) {
			if (response && !response.error) {
				self.lastgroup = response.data.lastgroup;
				self.sortorder = contacts_groups_sortorder;
				console.log('sortorder', self.sortorder);
				// Favorites
				// Map to strings to easier lookup in contacts list.
				var contacts = $.map(response.data.favorites, function(c) {return String(c);});
				$elem = self.findById('fav');
				$elem = $elem.length ? $elem : tmpl.octemplate({
					id: 'fav',
					type: 'fav',
					num: contacts.length,
					name: t('contacts', 'Favorites')
				}).appendTo($groupList);
				$elem.data('obj', self);
				$elem.data('rawname', t('contacts', 'Favorites'));
				if(!$elem.find('.starred').length) {
					$elem.data('contacts', contacts).find('.numcontacts').before('<span class="icon-starred starred action" />');
				}
				$elem.droppable({
							drop: self.contactDropped,
							over: function( event, ui ) {
								console.log('over favorites', ui.draggable);
							},
							activeClass: 'ui-state-active',
							hoverClass: 'ui-state-hover',
							scope: 'contacts'
						});
				if(contacts.length === 0) {
					$elem.hide();
				}
				console.log('favorites', $elem.data('contacts'));
				// Normal groups
				$.each(response.data.categories, function(c, category) {
					var contacts = $.map(category.contacts, function(c) {return String(c);});
					$elem = self.findById(category.id);
					if($elem.length) {
						$elem.find('.numcontacts').text(contacts.length > 0 && contacts.length || '');
					} else {
						$elem = $elem.length ? $elem : (tmpl).octemplate({
							id: category.id,
							type: 'category',
							num: contacts.length,
							name: category.name
						});
						self.categories.push({id: category.id, name: category.name});
						$elem.data('obj', self);
						$elem.data('rawname', category.name);
						$elem.data('id', category.id);
						$elem.droppable({
										drop: self.contactDropped,
										over: function( event, ui ) {
											console.log('over group', ui.draggable);
										},
										activeClass: 'ui-state-active',
										hoverClass: 'ui-state-hover',
										scope: 'contacts'
									});
						$elem.appendTo($groupList);
					}
					$elem.data('contacts', contacts);
				});

				var elems = $groupList.find('li[data-type="category"]').get();

				elems.sort(function(a, b) {
					return self.sortorder.indexOf(parseInt($(a).data('id'))) > self.sortorder.indexOf(parseInt($(b).data('id')));
				});

				$.each(elems, function(index, elem) {
					$groupList.append(elem);
				});

				// Shared addressbook
				$.each(response.data.shared, function(c, shared) {
					var sharedindicator = '<img class="shared svg" src="' + OC.imagePath('core', 'actions/shared') + '"'
						+ 'title="' + t('contacts', 'Shared by {owner}', {owner:shared.owner}) + '" />';
					$elem = self.findById(shared.id);
					$elem = $elem.length ? $elem : (tmpl).octemplate({
						id: shared.id,
						type: 'shared',
						num: response.data.shared.length,
						name: shared.displayname
					});
					$elem.find('.numcontacts').after(sharedindicator);
					$elem.data('obj', self);
					$elem.data('rawname', shared.displayname);
					$elem.data('id', shared.id);
					$elem.appendTo($groupList);
				});
				if(!self.findById('uncategorized').length) {
					tmpl.octemplate({id: 'uncategorized', type: 'uncategorized', num: '', name: t('contacts', 'Not grouped')}).appendTo($groupList);
				}
				$groupList.sortable({
					items: 'li[data-type="category"]',
					stop: function() {
						console.log('stop sorting', $(this));
						var ids = [];
						$.each($(this).children('li[data-type="category"]'), function(i, elem) {
							var id = $(elem).data('id');
							if(typeof id === 'number' && id % 1 === 0) {
								ids.push(id);
							}
						});
						self.sortorder = ids;
						$(document).trigger('status.groups.sorted', {
							sortorder: self.sortorder.join(',')
						});
					}
				});
				$elem = self.findById(self.lastgroup);
				$elem.addClass('active');
				self.loaded = true;
			} // TODO: else
			if(typeof cb === 'function') {
				cb();
			}
		})
		.fail(function(response) {
			console.log('Request Failed:', response);
			response.message = t('contacts', 'Failed loading groups: {error}', {error:response.message});
			$(document).trigger('status.contacts.error', response);
		});
	};

	OC.Contacts.GroupList = GroupList;

})(window, jQuery, OC);
