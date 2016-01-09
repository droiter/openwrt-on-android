<?php

/**
 * ownCloud - Contacts
 *
 * @author Nicolas Mora
 * @copyright 2014 Nicolas Mora mail@babelouest.org
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 */

?>

<div class="section">
	<h2><?php p($l->t('Contacts')) ?></h2>
	<input type="checkbox" name="contacts-ldap-enabled" id="contacts-ldap-enabled" value="checked" <?php (\OCP\Config::getAppValue('contacts', 'backend_ldap', "false") === "true")?print "checked":print ""?> />
	<label for="contacts-ldap-enabled"><?php p($l->t('Enable LDAP Backend')) ?></label><br>
	<em><?php p($l->t('Enable LDAP backend for the contacts application')) ?></em>
	<br/><em><?php p($l->t('Warning: LDAP Backend is in beta mode, use with precautions')) ?></em>
</div>
