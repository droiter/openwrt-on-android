<?php

\OCP\User::checkLoggedIn();

$result = \OCP\Contacts::search($_REQUEST['term'], array('FN', 'ADR'));

$contacts = array();

foreach ($result as $r) {
	if (!isset($r['ADR'])) {
		continue;
	}

	$tmp = $r['ADR'][0];
	$address = trim(implode(" ", $tmp));
  
	$contacts[] = array('label' => $address);
}

\OCP\JSON::EncodedPrint($contacts);
