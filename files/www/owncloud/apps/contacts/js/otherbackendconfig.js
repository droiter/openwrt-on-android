OC.Contacts = OC.Contacts || {};

(function(window, $, OC) {
	'use strict';

	var OtherBackendConfig = function(storage, addressbooks, $template) {
		this.storage = storage;
		this.addressbooks = addressbooks;
		this.$template = $template;
		this.getConnectors();
	};
	
	OC.Contacts.OtherBackendConfig = OtherBackendConfig;

	OtherBackendConfig.prototype.openAddressbookUi = function() {
		this.addressbookUiInit();
	};

	OtherBackendConfig.prototype.editAddressbookUI = function(addressbook) {
		var self = this;
		$('#addressbooks-ui-addressbookid').val(addressbook.id);
		$('#addressbooks-ui-name').val(addressbook.displayname);
		$('#addressbooks-ui-uri').val(addressbook.uri);
		$('#addressbooks-ui-description').val(addressbook.description);
		$('#addressbooks-ui-ldapurl').val(addressbook.ldapurl);
		$('#addressbooks-ui-ldapanonymous').attr('checked', (addressbook.ldapanonymous===true));
		$('#addressbooks-ui-ldapreadonly').attr('checked', (addressbook.ldapreadonly===true));
		$('#addressbooks-ui-ldapuser').val(addressbook.ldapuser);
		$('#addressbooks-ui-ldappass').val('nochange');
		$('#addressbooks-ui-ldappass-modified').val('false');
		$('#addressbooks-ui-ldappagesize').val(addressbook.ldappagesize);
		$('#addressbooks-ui-ldapbasednsearch').val(addressbook.ldapbasednsearch);
		$('#addressbooks-ui-ldapfilter').val(addressbook.ldapfilter);
		$('#addressbooks-ui-ldapbasednmodify').val(addressbook.ldapbasednmodify);
		$('#addressbooks-ui-uri').prop('disabled', true);
		if ($('#addressbooks-ui-ldapanonymous').prop('checked')) {
			$('#addressbooks-ui-ldapuser').prop('disabled', true);
			$('#addressbooks-ui-ldappass').prop('disabled', true);
		} else {
			$('#addressbooks-ui-ldapuser').removeProp('disabled');
			$('#addressbooks-ui-ldappass').removeProp('disabled');
		}
		if ($('#addressbooks-ui-ldapreadonly').prop('checked')) {
			$('#addressbooks-ui-ldapbasednmodify').prop('disabled', true);
		} else {
			$('#addressbooks-ui-ldapbasednmodify').removeProp('disabled');
		}
		
		$('#addressbooks-ui-ldappass').change(function() {
			$('#addressbooks-ui-ldappass-modified').val('true');
		});
		
		this.addressbookUiInit();

		var connectors = self.getConnectors();
		$('#addressbooks-ui-ldapvcardconnector').empty();
		var custom = true;
		var $option = null;
		for (var id = 0; id < connectors.length; id++) {
			if (connectors[id].id === addressbook.ldapconnectorid) {
				$option = $('<option value="' + connectors[id].id + '">' + connectors[id].name + '</option>').attr('selected','selected');
				custom = false;
			} else {
				$option = $('<option value="' + connectors[id].id + '">' + connectors[id].name + '</option>');
			}
			$('#addressbooks-ui-ldapvcardconnector').append($option);
		}
		if (custom) {
			$option = $('<option value="">' + 'Custom connector' + '</option>').attr('selected','selected');
			$('#addressbooks-ui-ldapvcardconnector').append($option);
			$('#addressbooks-ui-ldapvcardconnector-value-p').show();
			$('#addressbooks-ui-ldapvcardconnector-copyfrom-p').show();
			$('#addressbooks-ui-ldapvcardconnector-copyfrom').empty();
			$option = $('<option value="">' + 'Select connector' + '</option>').attr('selected','selected');
			$('#addressbooks-ui-ldapvcardconnector-copyfrom').append($option);
			for (var id = 0; id < connectors.length; id++) {
				$option = $('<option value="' + connectors[id].id + '">' + connectors[id].name + '</option>');
				$('#addressbooks-ui-ldapvcardconnector-copyfrom').append($option);
			}

			$('#addressbooks-ui-ldapvcardconnector-value').text(addressbook.ldap_vcard_connector);
		} else {
			$option = $('<option value="">' + 'Custom connector' + '</option>');
			$('#addressbooks-ui-ldapvcardconnector').append($option);
		}
	};

	OtherBackendConfig.prototype.addressbookUiOk = function($divDlg) {
		var defer = $.Deferred();
		var addressbook = OC.Contacts.addressBooks;

		var error=false;
		var errorFields = [];
		$('[required]').each(function() {
			if ($(this).val() === '' && !$(this).attr('disabled')){
				error = true;
				errorFields.push($(this).attr('placeholder'));
			}
		});
		if (!error) {
			$('#addressbooks-ui-errortitle-p').empty();
			$('#addressbooks-ui-errormessage-p').empty();
			$.when(this.storage.addAddressBook($('#addressbooks-ui-backend').val(),
			{
				displayname: $('#addressbooks-ui-name').val(),
				description: $('#addressbooks-ui-description').val(),
				uri: ($('#addressbooks-ui-uri').val()==='')?$('#addressbooks-ui-name').val():$('#addressbooks-ui-uri').val(),
				ldapurl: $('#addressbooks-ui-ldapurl').val(),
				ldapanonymous: $('#addressbooks-ui-ldapanonymous').prop('checked')===true?'true':'false',
				ldapreadonly: $('#addressbooks-ui-ldapreadonly').prop('checked')===true?'true':'false',
				ldapuser: $('#addressbooks-ui-ldapuser').val(),
				ldappass: $('#addressbooks-ui-ldappass').val(),
				ldappagesize: $('#addressbooks-ui-ldappagesize').val(),
				ldapbasednsearch: $('#addressbooks-ui-ldapbasednsearch').val(),
				ldapfilter: $('#addressbooks-ui-ldapfilter').val(),
				ldapbasednmodify: $('#addressbooks-ui-ldapbasednmodify').val(),
				ldapvcardconnector: $('#addressbooks-ui-ldapvcardconnector').val(),
				ldapvcardconnectorvalue: $('#addressbooks-ui-ldapvcardconnector-value').val(),
			}
			)).then(function(response) {
				if(response.error) {
					var error = response.message;
					if(typeof cb === 'function') {
						cb({error:true, message:error});
					}
					defer.reject(response);
				} else {
					var book = addressbook.insertAddressBook(response.data);
					$(document).trigger('status.addressbook.added');
					if(typeof cb === 'function') {
						cb({error:false, addressbook: book});
					}
					defer.resolve({error:false, addressbook: book});
				}
				OC.Contacts.otherBackendConfig.addressbookUiClose($divDlg);
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
				OC.Contacts.otherBackendConfig.addressbookUiClose($divDlg);
			});
		} else {
			$('#addressbooks-ui-errortitle-p').css('color', 'red').text(t('contacts', 'Error, missing parameters: '));
			$('#addressbooks-ui-errormessage-p').css('color', 'red').text(errorFields.join(', '));
		}
	};

	OtherBackendConfig.prototype.addressbookUiEditOk = function($divDlg) {
		var defer = $.Deferred();

		var error=false;
		var errorFields = [];
		$('[required]').each(function() {
			if ($(this).val() === '' && !$(this).attr('disabled')){
				error = true;
				errorFields.push($(this).attr('placeholder'));
			}
		});
		if (!error) {
			$.when(this.storage.updateAddressBook($('#addressbooks-ui-backend').val(), $('#addressbooks-ui-addressbookid').val(),
			{properties:
				{
					displayname: $('#addressbooks-ui-name').val(),
					description: $('#addressbooks-ui-description').val(),
					uri: $('#addressbooks-ui-uri').val(),
					ldapurl: $('#addressbooks-ui-ldapurl').val(),
					ldapanonymous: $('#addressbooks-ui-ldapanonymous').prop('checked')===true?'true':'false',
					ldapreadonly: $('#addressbooks-ui-ldapreadonly').prop('checked')===true?'true':'false',
					ldapuser: $('#addressbooks-ui-ldapuser').val(),
					ldappassmodified: $('#addressbooks-ui-ldappass-modified').val(),
					ldappass: $('#addressbooks-ui-ldappass').val(),
					ldappagesize: $('#addressbooks-ui-ldappagesize').val(),
					ldapbasednsearch: $('#addressbooks-ui-ldapbasednsearch').val(),
					ldapfilter: $('#addressbooks-ui-ldapfilter').val(),
					ldapbasednmodify: $('#addressbooks-ui-ldapbasednmodify').val(),
					ldapvcardconnector: $('#addressbooks-ui-ldapvcardconnector').val(),
					ldapvcardconnectorvalue: $('#addressbooks-ui-ldapvcardconnector-value').val(),
				}
			}
			)).then(function(response) {
				if(response.error) {
					error = response.message;
					if(typeof cb === 'function') {
						cb({error:true, message:error});
					}
					defer.reject(response);
				}
				OC.Contacts.otherBackendConfig.addressbookUiClose($divDlg);
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
		} else {
			$('#addressbooks-ui-errortitle-p').css('color', 'red').text(t('contacts', 'Error, missing parameters: '));
			$('#addressbooks-ui-errormessage-p').css('color', 'red').text(errorFields.join(', '));
		}
	};

	OtherBackendConfig.prototype.addressbookUiClose = function($divDlg) {
		$divDlg.ocdialog().ocdialog('close');
		$divDlg.ocdialog().ocdialog('destroy').remove();
	};

	OtherBackendConfig.prototype.addressbookUiInit = function() {
		var self = this;
		
		$('#addressbooks-ui-ldapvcardconnector-value-p').hide();
		$('#addressbooks-ui-ldapvcardconnector-copyfrom-p').hide();
		$('#addressbooks-ui-name').change(function() {
			if ($('#addressbooks-ui-uri').val() === '') {
				$('#addressbooks-ui-uri').val($('#addressbooks-ui-name').val().toLowerCase().replace(' ', '-'));
			}
		});
		$('#addressbooks-ui-ldapanonymous').change(function() {
			if ($('#addressbooks-ui-ldapanonymous').prop('checked')) {
				$('#addressbooks-ui-ldapuser').prop('disabled', true);
				$('#addressbooks-ui-ldappass').prop('disabled', true);
			} else {
				$('#addressbooks-ui-ldapuser').removeProp('disabled');
				$('#addressbooks-ui-ldappass').removeProp('disabled');
			}
		});
		$('#addressbooks-ui-ldapreadonly').change(function() {
			if ($('#addressbooks-ui-ldapreadonly').prop('checked')) {
				$('#addressbooks-ui-ldapbasednmodify').prop('disabled', true);
			} else {
				$('#addressbooks-ui-ldapbasednmodify').removeProp('disabled');
			}
		});
		$('#addressbooks-ui-ldapbasednsearch').change(function() {
			if ($('#addressbooks-ui-ldapbasednmodify').val() === '') {
				$('#addressbooks-ui-ldapbasednmodify').val($('#addressbooks-ui-ldapbasednsearch').val());
			}
		});
		$('#addressbooks-ui-ldapbasednmodify').change(function() {
			if ($('#addressbooks-ui-ldapbasednsearch').val() === '') {
				$('#addressbooks-ui-ldapbasednsearch').val($('#addressbooks-ui-ldapbasednmodify').val());
			}
		});
		
		$('#addressbooks-ui-ldapvcardconnector').empty();
		var $option = null;
		var connectors = self.getConnectors();
		for (var id = 0; id < connectors.length; id++) {
			if (connectors[id] !== null) {
				$option = $('<option value="' + connectors[id].id + '">' + connectors[id].name + '</option>');
				$('#addressbooks-ui-ldapvcardconnector').append($option);
			}
		}
		$option = $('<option value="">' + 'Custom connector' + '</option>');
		$('#addressbooks-ui-ldapvcardconnector').append($option);

		$('#addressbooks-ui-ldapvcardconnector').change(function() {
			// Custom connector
			if ($('#addressbooks-ui-ldapvcardconnector').val() === '') {
				$('#addressbooks-ui-ldapvcardconnector-value-p').show();
				$('#addressbooks-ui-ldapvcardconnector-copyfrom-p').show();
				var connectors = self.getConnectors();
				$('#addressbooks-ui-ldapvcardconnector-copyfrom').empty();
				var $option = $('<option value="">' + 'Select connector' + '</option>').attr('selected','selected');
				$('#addressbooks-ui-ldapvcardconnector-copyfrom').append($option);
				for (var id = 0; id < connectors.length; id++) {
					$option = $('<option value="' + connectors[id].id + '">' + connectors[id].name + '</option>');
					$('#addressbooks-ui-ldapvcardconnector-copyfrom').append($option);
				}
			} else {
				$('#addressbooks-ui-ldapvcardconnector-value-p').hide();
				$('#addressbooks-ui-ldapvcardconnector-copyfrom-p').hide();
			}
		});
		$('#addressbooks-ui-ldapvcardconnector-copyfrom').change(function() {
			if ($('#addressbooks-ui-ldapvcardconnector-copyfrom').val() !== '') {
				var connectors = self.getConnectors();
				for (var id = 0; id < connectors.length; id++) {
					if ($('#addressbooks-ui-ldapvcardconnector-copyfrom').val() === connectors[id].id) {
						$('#addressbooks-ui-ldapvcardconnector-value').text(connectors[id].xml);
					}
				}
			}
		});
		
		$('#addressbooks-ui-ldappagesize').forceNumericOnly();
	};
	
	OtherBackendConfig.prototype.getConnectors = function() {
		var self = this;
		
		if (self.connectors === null || self.connectors === undefined) {
			$.when(self.storage.getConnectors($('#addressbooks-ui-backend').val()))
			.then(function(response) {
				self.connectors = response.data;
				return self.connectors;
			})
			.fail(function(jqxhr, textStatus, error) {
				var err = textStatus + ', ' + error;
				console.log('Request Failed', + err);
				defer.reject({error:true, message:error});
			});
		} else {
			return self.connectors;
		}
	};
	
	jQuery.fn.forceNumericOnly = function()
	{
		return this.each(function()
		{
			$(this).keydown(function(e)
			{
				var key = e.charCode || e.keyCode || 0;
				// allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
				// home, end, period, and numpad decimal
				return (
					key === 8 || 
					key === 9 ||
					key === 13 ||
					key === 46 ||
					key === 110 ||
					key === 190 ||
					(key >= 35 && key <= 40) ||
					(key >= 48 && key <= 57) ||
					(key >= 96 && key <= 105));
			});
		});
	};
	
})(window, jQuery, OC);
