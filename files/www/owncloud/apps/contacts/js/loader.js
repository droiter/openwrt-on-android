/**
 * Copyright (c) 2013 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
OC.ContactsImporter = OC.ContactsImporter || {
	init:function(fileName) {
		var self = OC.ContactsImporter;
		self.path = $('#dir').val();
		self.fileName = fileName;
		console.log('fileName', self.path, self.fileName);
		OC.addScript('contacts', 'addressbooks', function() {
			OC.addScript('contacts', 'storage', function() {
				$.when(self._getTemplate()).then(function($tmpl) {
					self.$template = $tmpl;
					if(self.$dialog) {
						self.$dialog.ocdialog('close');
					}
					self.$dialog = self.$template.octemplate({
						selectText: t('contacts', 'Please choose the addressbook'),
						defaultText: t('contacts', 'Import into...')
					});
					self.$dialog.appendTo($('body'));
					self.addressBooks = new OC.Contacts.AddressBookList(
						new OC.Contacts.Storage(), self.$dialog, null, true
					);
					self.showDialog();
				})
				.fail(function() {
					alert(t('contacts', 'Error loading import template'));
				});
			})
			.fail(function(jqxhr, settings, exception) {
				console.warn('Error loading storage backend', jqxhr, settings, exception);
			});
		})
		.fail(function(jqxhr, settings, exception) {
			console.warn('Error loading address book backend', jqxhr, settings, exception);
		});
	},
	showDialog:function() {
		var self = this;
		$.when(self.addressBooks.loadAddressBooks()).then(function(response) {
			if(!response.error) {
				self.$dialog.ocdialog({
					modal: true,
					title: t('contacts', 'Import contacts'),
					close: function() {
						$(this).ocdialog('destroy').remove();
						self.$dialog = null;
					}
				});
				self.$importIntoSelect = self.$dialog.find('#import_into');
				self.$importIntoSelect.on('change', function() {
					var $selected = $(this).find('option:selected');
					if($(this).val() === '-1') {
						self.$dialog.ocdialog('option', 'buttons', []);
					} else {
						self.$dialog.ocdialog('option', 'buttons', [{
							text: t('contacts', 'Import'),
							defaultButton: true,
							click:function() {
								console.log('Selected', $selected);
								self.$dialog.ocdialog('option', {
									buttons: [],
									closeButton: false,
									title: t('contacts', 'Importing...')
								});
								self.startImport($selected.data('backend'), $selected.val());
							}
						}]);
					}
				});
			} else {
				console.warn('response.message');
			}
		})
		.fail(function(response) {
			console.warn(response);
		});
	},
	startImport: function(backend, addressBookId, importType) {
		var self = this;
		$('.import-select').hide();
		$('.import-status').show();
		$.when(self.addressBooks.prepareImport(backend, addressBookId, importType, this.path, this.fileName))
		.then(function(response) {
			if(!response.error) {
				$.when(self.addressBooks.doImport(response)).then(function(response) {
					self.$dialog.ocdialog('option', {
						title: t('contacts', 'Import done'),
						closeButton: true,
						buttons: [{
							text: t('contacts', 'Close'),
							defaultButton: true,
							click:function() {
								self.$dialog.ocdialog('close');
							}
						}]
					});
				})
				.fail(function(response) {
					console.warn(response);
				});
			} else {
				console.warn('response.message');
			}
		})
		.fail(function(response) {
			console.warn(response);
		});
	},
	_getTemplate: function() {
		var defer = $.Deferred();
		if(!this.$template) {
			$.get(OC.filePath('contacts', 'templates', 'importdialog.html'), function(tmpl) {
				defer.resolve($(tmpl));
			})
			.fail(function() {
				defer.reject();
			});
		} else {
			defer.resolve(this.$template);
		}
		return defer.promise();
	}
};

$(document).ready(function(){

	// translate search result type
	OC.search.resultTypes.contact = t('contacts', 'Contact');

	OC.search.customResults.contact = function (row, item){
		var text = '';
		if (item.email) {
			text += '✉ ' + item.email;
			if (item.phone) {
				text += ', '
			}
		}
		if (item.phone) {
			text += '☎ ' + item.phone
		}
		row.find('td.result .text').text(text);
	};

	// If the app is already active there's no need for the FileActions
	if(OC.Contacts) {
		return;
	}

	$(document).bind('status.contacts.error', function(e, data) {
		console.warn(data.message);
		//console.trace();
		//OC.notify({message:data.message});
	});

	if(typeof FileActions !== 'undefined'){
		FileActions.register('text/vcard','importaddressbook', OC.PERMISSION_READ, '', OC.ContactsImporter.init);
		FileActions.setDefault('text/vcard','importaddressbook');
		FileActions.register('text/x-vcard','importaddressbook', OC.PERMISSION_READ, '', OC.ContactsImporter.init);
		FileActions.setDefault('text/x-vcard','importaddressbook');
	}
	
});
