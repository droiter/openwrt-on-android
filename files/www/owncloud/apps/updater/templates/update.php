<?php

/**
 * ownCloud - Updater plugin
 *
 * @author Victor Dubiniuk
 * @copyright 2013 Victor Dubiniuk victor.dubiniuk@gmail.com
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 */

?>
<?php $isNewVersionAvailable = $_['isNewVersionAvailable']?>
<div id="updater-content" ng-app="updater" ng-init="navigation='backup'">
	<div class="section" ng-controller="updateCtrl">
		<h2><?php p($l->t('Updates')) ?></h2>
		<p id="update-info" ng-show="<?php p($isNewVersionAvailable) ?>">
			<?php p($l->t('A new version is available: %s', array($_['version']))) ?>
		</p>
		<p ng-show="<?php p(!$isNewVersionAvailable) ?>">
			<?php p($l->t('Up to date. Checked on %s', array($_['checkedAt']))) ?>
		</p>
		<div id="upd-step-title" style="display:none;">
			<ul class="track-progress" data-steps="3">
				<li class="icon-breadcrumb"><?php p($l->t('1. Check & Backup')) ?></li>
				<li class="icon-breadcrumb"><?php p($l->t('2. Download & Extract')) ?></li>
				<li><?php p($l->t('3. Replace')) ?></li>
			</ul>
		</div>
		<div id="upd-progress" style="display:none;"><div></div></div>
		<button ng-click="update()" ng-show="<?php p($isNewVersionAvailable) ?>" id="updater-start">
			<?php p($l->t('Update')) ?>
		</button>
	</div>
	<div class="section" ng-controller="backupCtrl">
		<h2><?php p($l->t('Backups')) ?></h2>
		<p>
			<?php p($l->t('Backup directory')) ?>:
			<?php p(\OCA\Updater\App::getBackupBase()); ?>
		</p>
		<p ng-show="!entries.length"><?php p($l->t('No backups found')) ?></p>
		<table ng-hide="!entries.length">
			<thead>
				<tr>
					<th><?php p($l->t('Backup')) ?></th>
					<th><?php p($l->t('Done on')) ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="entry in entries">
					<td title="<?php p($l->t('Download')) ?>" class="item" ng-click="doDownload(entry.title)">{{entry.title}}</td>
					<td title="<?php p($l->t('Download')) ?>" class="item" ng-click="doDownload(entry.title)">{{entry.date}}</td>
					<td title="<?php p($l->t('Delete')) ?>" class="item icon-delete" ng-click="doDelete(entry.title)"></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
