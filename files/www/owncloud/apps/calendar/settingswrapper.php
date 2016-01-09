<?php
$l10n = OC_L10N::get('calendar');
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('calendar');

$tmpl = new OCP\Template('calendar', 'settings');
$timezone=OCP\Config::getUserValue(OCP\USER::getUser(),'calendar','timezone','');
$tmpl->assign('timezone',$timezone);
$tmpl->assign('timezones',DateTimeZone::listIdentifiers());
$tmpl->assign('calendars', OC_Calendar_Calendar::allCalendars(OCP\USER::getUser()), false);

OCP\Util::addscript('calendar','settings');
$tmpl->printPage();