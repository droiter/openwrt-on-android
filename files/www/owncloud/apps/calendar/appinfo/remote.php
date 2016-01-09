<?php
/**
 * Copyright (c) 2011 Jakob Sack <mail@jakobsack.de>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
OCP\App::checkAppEnabled('calendar');

if(substr(OCP\Util::getRequestUri(),0,strlen(OC_App::getAppWebPath('calendar').'/caldav.php')) == OC_App::getAppWebPath('calendar'). '/caldav.php') {
	$baseuri = OC_App::getAppWebPath('calendar').'/caldav.php';
}

// only need authentication apps
$RUNTIME_APPTYPES=array('authentication');
OC_App::loadApps($RUNTIME_APPTYPES);
if(\OCP\App::isEnabled('contacts')) {
	\OCP\Share::registerBackend('addressbook', 'OCA\Contacts\Share\Addressbook', 'contact');
}

// Backends
$authBackend = new OC_Connector_Sabre_Auth();
$principalBackend = new OC_Connector_Sabre_Principal();
$caldavBackend    = new OC_Connector_Sabre_CalDAV();
$requestBackend = new OC_Connector_Sabre_Request();

// Root nodes
$Sabre_CalDAV_Principal_Collection = new \Sabre\CalDAV\Principal\Collection($principalBackend);
$Sabre_CalDAV_Principal_Collection->disableListing = true; // Disable listening

$calendarRoot = new OC_Connector_Sabre_CalDAV_CalendarRoot($principalBackend, $caldavBackend);
$calendarRoot->disableListing = true; // Disable listening

$nodes = array(
	$Sabre_CalDAV_Principal_Collection,
	$calendarRoot,
	);

// Fire up server
$server = new \Sabre\DAV\Server($nodes);
$server->httpRequest = $requestBackend;
$server->setBaseUri($baseuri);
// Add plugins
$server->addPlugin(new \Sabre\DAV\Auth\Plugin($authBackend,'ownCloud'));
$server->addPlugin(new \Sabre\CalDAV\Plugin());
$server->addPlugin(new \Sabre\DAVACL\Plugin());
$server->addPlugin(new \Sabre\DAV\Browser\Plugin(false)); // Show something in the Browser, but no upload
$server->addPlugin(new \Sabre\CalDAV\ICSExportPlugin());

// And off we go!
$server->exec();
