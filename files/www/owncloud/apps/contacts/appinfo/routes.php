<?php
/**
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
namespace OCA\Contacts;

use OCA\Contacts\Dispatcher;

//define the routes
$this->create('contacts_index', '/')
	->get()
	->action(
		function($params){
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('PageController', 'index');
		}
	);

$this->create('contacts_jsconfig', 'ajax/config.js')
	->actionInclude('contacts/js/config.php');

$this->create('contacts_address_books_for_user', 'addressbooks/')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'userAddressBooks');
		}
	);

$this->create('contacts_address_book_connectors', 'connectors/{backend}')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('BackendController', 'getConnectors');
		}
	)
	->requirements(array('backend'));

$this->create('contacts_backend_enable', 'backend/{backend}/{enable}')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('BackendController', 'enableBackend');
		}
	)
	->requirements(array('backend', 'enable'));

$this->create('contacts_backend_status', 'backend/{backend}')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('BackendController', 'backendStatus');
		}
	)
	->requirements(array('backend'));

$this->create('contacts_address_book_add', 'addressbook/{backend}/add')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'addAddressBook');
		}
	)
	->requirements(array('backend'));

$this->create('contacts_address_book', 'addressbook/{backend}/{addressBookId}')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'getAddressBook');
		}
	)
	->requirements(array('backend', 'addressBookId'));

$this->create('contacts_address_book_contacts', 'addressbook/{backend}/{addressBookId}/contacts')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'getContacts');
		}
	)
	->requirements(array('backend', 'addressBookId'));

$this->create('contacts_address_book_headers', 'addressbook/{backend}/{addressBookId}')
	->method('HEAD')
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'getAddressBook');
		}
	)
	->requirements(array('backend', 'addressBookId'));

$this->create('contacts_address_book_options', 'addressbook/{backend}/{addressBookId}')
	->method('OPTIONS')
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'getAddressBook');
		}
	)
	->requirements(array('backend', 'addressBookId'));

$this->create('contacts_address_book_update', 'addressbook/{backend}/{addressBookId}')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'updateAddressBook');
		}
	)
	->requirements(array('backend', 'addressBookId'));

$this->create('contacts_address_book_delete', 'addressbook/{backend}/{addressBookId}')
	->delete()
	->action(
		function($params) {
			$dispatcher = new Dispatcher($params);
			\OC::$session->close();
			$dispatcher->dispatch('AddressBookController', 'deleteAddressBook');
		}
	)
	->requirements(array('backend', 'addressBookId'));

$this->create('contacts_address_book_activate', 'addressbook/{backend}/{addressBookId}/activate')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'activateAddressBook');
		}
	)
	->requirements(array('backend', 'addressBookId'));

$this->create('contacts_address_book_add_contact', 'addressbook/{backend}/{addressBookId}/contact/add')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'addChild');
		}
	)
	->requirements(array('backend', 'addressBookId'));

$this->create('contacts_address_book_delete_contact', 'addressbook/{backend}/{addressBookId}/contact/{contactId}')
	->delete()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'deleteChild');
		}
	)
	->requirements(array('backend', 'addressBookId', 'contactId'));

$this->create('contacts_address_book_delete_contacts', 'addressbook/{backend}/{addressBookId}/deleteContacts')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'deleteChildren');
		}
	)
	->requirements(array('backend', 'addressBookId', 'contactId'));

$this->create('contacts_address_book_move_contact', 'addressbook/{backend}/{addressBookId}/contact/{contactId}')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('AddressBookController', 'moveChild');
		}
	)
	->requirements(array('backend', 'addressBookId', 'contactId'));

$this->create('contacts_import_upload', 'addressbook/{backend}/{addressBookId}/{importType}/import/upload')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ImportController', 'upload');
		}
	)
	->requirements(array('backend', 'addressBookId', 'importType'));

$this->create('contacts_import_prepare', 'addressbook/{backend}/{addressBookId}/{importType}/import/prepare')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ImportController', 'prepare');
		}
	)
	->requirements(array('backend', 'addressBookId', 'importType'));

$this->create('contacts_import_start', 'addressbook/{backend}/{addressBookId}/{importType}/import/start')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ImportController', 'start');
		}
	)
	->requirements(array('backend', 'addressBookId', 'importType'));

$this->create('contacts_import_status', 'addressbook/{backend}/{addressBookId}/{importType}/import/status')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ImportController', 'status');
		}
	)
	->requirements(array('backend', 'addressBookId', 'importType'));

$this->create('contacts_address_book_export', 'addressbook/{backend}/{addressBookId}/export')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ExportController', 'exportAddressBook');
		}
	)
	->requirements(array('backend', 'addressBookId'));

$this->create('contacts_contact_export', 'addressbook/{backend}/{addressBookId}/contact/{contactId}/export')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ExportController', 'exportContact');
		}
	)
	->requirements(array('backend', 'addressbook', 'contactId'));

$this->create('contacts_export_selected', 'exportSelected')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ExportController', 'exportSelected');
		}
	);

$this->create('contacts_contact_photo', 'addressbook/{backend}/{addressBookId}/contact/{contactId}/photo')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ContactPhotoController', 'getPhoto');
		}
	)
	->requirements(array('backend', 'addressbook', 'contactId'));

$this->create('contacts_upload_contact_photo', 'addressbook/{backend}/{addressBookId}/contact/{contactId}/photo')
	->put()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ContactPhotoController', 'uploadPhoto');
		}
	)
	->requirements(array('backend', 'addressbook', 'contactId'));

$this->create('contacts_cache_contact_photo', 'addressbook/{backend}/{addressBookId}/contact/{contactId}/photo/cacheCurrent')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ContactPhotoController', 'cacheCurrentPhoto');
		}
	)
	->requirements(array('backend', 'addressbook', 'contactId'));

$this->create('contacts_cache_fs_photo', 'addressbook/{backend}/{addressBookId}/contact/{contactId}/photo/cacheFS')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ContactPhotoController', 'cacheFileSystemPhoto');
		}
	)
	->requirements(array('backend', 'addressBookId', 'contactId'));

$this->create('contacts_tmp_contact_photo', 'addressbook/{backend}/{addressBookId}/contact/{contactId}/photo/{key}/tmp')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ContactPhotoController', 'getTempPhoto');
		}
	)
	->requirements(array('backend', 'addressbook', 'contactId', 'key'));

$this->create('contacts_crop_contact_photo', 'addressbook/{backend}/{addressBookId}/contact/{contactId}/photo/{key}/crop')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ContactPhotoController', 'cropPhoto');
		}
	)
	->requirements(array('backend', 'addressbook', 'contactId', 'key'));

// Save or delete a single property.
$this->create('contacts_contact_patch', 'addressbook/{backend}/{addressBookId}/contact/{contactId}')
	->patch()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ContactController', 'patch');
		}
	)
	->requirements(array('backend', 'addressbook', 'contactId'));

$this->create('contacts_contact_get', 'addressbook/{backend}/{addressBookId}/contact/{contactId}/')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ContactController', 'getContact');
		}
	)
	->requirements(array('backend', 'addressbook', 'contactId'));

// Save all properties. Used for merging contacts.
$this->create('contacts_contact_save_all', 'addressbook/{backend}/{addressBookId}/contact/{contactId}/save')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('ContactController', 'saveContact');
		}
	)
	->requirements(array('backend', 'addressbook', 'contactId'));

$this->create('contacts_categories_list', 'groups/')
	->get()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GroupController', 'getGroups');
		}
	);

$this->create('contacts_categories_add', 'groups/add')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GroupController', 'addGroup');
		}
	);

$this->create('contacts_categories_delete', 'groups/delete')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GroupController', 'deleteGroup');
		}
	);

$this->create('contacts_categories_rename', 'groups/rename')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GroupController', 'renameGroup');
		}
	);

$this->create('contacts_categories_addto', 'groups/addto/{categoryId}')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GroupController', 'addToGroup');
		}
	);

$this->create('contacts_categories_removefrom', 'groups/removefrom/{categoryId}')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('GroupController', 'removeFromGroup');
		}
	)
	->requirements(array('categoryId'));

$this->create('contacts_setpreference', 'preference/set')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			$dispatcher = new Dispatcher($params);
			$dispatcher->dispatch('SettingsController', 'set');
		}
	);

$this->create('contacts_index_properties', 'indexproperties/{user}/')
	->post()
	->action(
		function($params) {
			\OC::$session->close();
			// TODO: Add BackgroundJob for this.
			\OCP\Util::emitHook('OCA\Contacts', 'indexProperties', array());

			\OCP\Config::setUserValue($params['user'], 'contacts', 'contacts_properties_indexed', 'yes');
			\OCP\JSON::success(array('isIndexed' => true));
		}
	)
	->requirements(array('user'))
	->defaults(array('user' => \OCP\User::getUser()));
