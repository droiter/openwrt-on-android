<?php
/* Copyright (c) 2014, Joas Schilling nickvergessen@owncloud.com
 * This file is licensed under the Affero General Public License version 3
 * or later. See the COPYING-README file. */

/** @var $l OC_L10N */
/** @var $_ array */
?>
<div id="app-navigation">
	<?php foreach ($_['navigations'] as $navigationGroup => $navigationEntries) { ?>
		<?php if ($navigationGroup !== 'apps'): ?><ul><?php endif; ?>

		<?php foreach ($navigationEntries as $navigation) { ?>
		<li>
			<a
				<?php if ($_['activeNavigation'] == $navigation['id']): ?> class="active"<?php endif; ?>
				data-navigation="<?php p($navigation['id']) ?>"
				href="<?php p($navigation['url']) ?>"
			>
				<?php p($navigation['name']) ?>
			</a>
		</li>
		<?php } ?>

		<?php if ($navigationGroup !== 'top'): ?></ul><?php endif; ?>
	<?php } ?>

	<div id="app-settings">
		<div id="app-settings-header">
			<button class="settings-button" data-apps-slide-toggle="#app-settings-content"></button>
		</div>

		<div id="app-settings-content">
			<input type="checkbox"<?php if ($_['rssLink']): ?> checked="checked"<?php endif; ?> id="enable_rss" />
			<label for="enable_rss"><?php p($l->t('Enable RSS feed'));?></label>
			<input id="rssurl"<?php if (!$_['rssLink']): ?> class="hidden"<?php endif; ?> type="text" readonly="readonly" value="<?php p($_['rssLink']); ?>" />
		</div>
	</div>
</div>
