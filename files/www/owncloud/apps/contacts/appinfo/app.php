<?php
/**
 * @author Thomas Tanghus
 * @copyright 2011-2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts;

use \OC\AppFramework\Core\API;

//require_once __DIR__ . '/../lib/controller/pagecontroller.php';
\Sabre\VObject\Component::$classMap['VCARD']	= '\OCA\Contacts\VObject\VCard';
\Sabre\VObject\Property::$classMap['CATEGORIES'] = '\OCA\Contacts\VObject\GroupProperty';
\Sabre\VObject\Property::$classMap['FN']		= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['TITLE']		= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['ROLE']		= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['NOTE']		= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['NICKNAME']	= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['EMAIL']		= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['TEL']		= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['IMPP']		= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['URL']		= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['LABEL']		= '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['X-EVOLUTION-FILE-AS'] = '\OC\VObject\StringProperty';
\Sabre\VObject\Property::$classMap['N']			= '\OC\VObject\CompoundProperty';
\Sabre\VObject\Property::$classMap['ADR']		= '\OC\VObject\CompoundProperty';
\Sabre\VObject\Property::$classMap['GEO']		= '\OC\VObject\CompoundProperty';
\Sabre\VObject\Property::$classMap['ORG']		= '\OC\VObject\CompoundProperty';

\OC::$server->getNavigationManager()->add(array(
	'id' => 'contacts',
	'order' => 10,
	'href' => \OCP\Util::linkToRoute('contacts_index'),
	'icon' => \OCP\Util::imagePath( 'contacts', 'contacts.svg' ),
	'name' => \OCP\Util::getL10N('contacts')->t('Contacts')
	)
);

$api = new API('contacts');

$api->connectHook('OC_User', 'post_createUser', '\OCA\Contacts\Hooks', 'userCreated');
$api->connectHook('OC_User', 'post_deleteUser', '\OCA\Contacts\Hooks', 'userDeleted');
$api->connectHook('OCA\Contacts', 'pre_deleteAddressBook', '\OCA\Contacts\Hooks', 'addressBookDeletion');
$api->connectHook('OCA\Contacts', 'pre_deleteContact', '\OCA\Contacts\Hooks', 'contactDeletion');
$api->connectHook('OCA\Contacts', 'post_createContact', 'OCA\Contacts\Hooks', 'contactAdded');
$api->connectHook('OCA\Contacts', 'post_updateContact', '\OCA\Contacts\Hooks', 'contactUpdated');
$api->connectHook('OCA\Contacts', 'scanCategories', '\OCA\Contacts\Hooks', 'scanCategories');
$api->connectHook('OCA\Contacts', 'indexProperties', '\OCA\Contacts\Hooks', 'indexProperties');
$api->connectHook('OC_Calendar', 'getEvents', 'OCA\Contacts\Hooks', 'getBirthdayEvents');
$api->connectHook('OC_Calendar', 'getSources', 'OCA\Contacts\Hooks', 'getCalenderSources');

\OCP\Util::addscript('contacts', 'loader');
\OCP\Util::addscript('contacts', 'admin');

\OC_Search::registerProvider('OCA\Contacts\Search\Provider');
//\OCP\Share::registerBackend('contact', 'OCA\Contacts\Share_Backend_Contact');
\OCP\Share::registerBackend('addressbook', 'OCA\Contacts\Share\Addressbook', 'contact');
//\OCP\App::registerPersonal('contacts','personalsettings');
\OCP\App::registerAdmin('contacts', 'admin');

if (\OCP\User::isLoggedIn()) {
	$app = new App($api->getUserId());
	$addressBooks = $app->getAddressBooksForUser();
	foreach ($addressBooks as $addressBook)  {
		if ($addressBook->isActive()) {
            \OCP\Contacts::registerAddressBook($addressBook->getSearchProvider());
        }
	}
}

