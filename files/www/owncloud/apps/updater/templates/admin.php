<?php

/**
 * ownCloud - Updater plugin
 *
 * @author Victor Dubiniuk
 * @copyright 2012-2013 Victor Dubiniuk victor.dubiniuk@gmail.com
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 */

?>

<div class="section">
	<h2><?php p($l->t('Updater')) ?></h2>
	<?php if (version_compare(PHP_VERSION, '5.4.0', '<')): ?>
		<?php p($l->t('The server environment is not compatible with ownCloud 8. You need to update PHP to the version 5.4.0 or above')) ?>
	<?php else: ?>
		<a class="button" target="_blank" href="<?php p(\OCP\Util::linkTo('updater', 'update.php')) ?>"><?php p($l->t('Open Update Center')) ?></a>
	<?php endif; ?>
</div>
