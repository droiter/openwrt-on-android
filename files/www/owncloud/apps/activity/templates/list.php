<?php

/**
 * ownCloud - Activity App
 *
 * @author Frank Karlitschek
 * @copyright 2013 Frank Karlitschek frank@owncloud.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/** @var $l OC_L10N */
/** @var $theme OC_Defaults */
/** @var $_ array */
?>
<?php $_['appNavigation']->printPage(); ?>

<div id="app-content">
	<div id="no_activities" class="hidden">
		<div class="body"><?php p($l->t('You will see a list of events here when you start to use your %s.', $theme->getTitle())) ?></div>
	</div>

	<div id="container" data-activity-filter="<?php p($_['filter']) ?>">
	</div>

	<div id="loading_activities" class="icon-loading"></div>

	<div id="no_more_activities" class="hidden">
		<?php p($l->t('No more events to load')) ?>
	</div>
</div>
