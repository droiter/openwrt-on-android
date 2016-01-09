<?php
/* Copyright (c) 2014, Joas Schilling nickvergessen@owncloud.com
 * This file is licensed under the Affero General Public License version 3
 * or later. See the COPYING-README file. */

/** @var $l OC_L10N */
/** @var $_ array */
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php p($l->t('Activity feed')); ?></title>
		<language><?php p($_['rssLang']); ?></language>
		<link><?php p($_['rssLink']); ?></link>
		<description><?php p($l->t('Personal activity feed for %s', $_['user'])); ?></description>
		<pubDate><?php p($_['rssPubDate']); ?></pubDate>
		<lastBuildDate><?php p($_['rssPubDate']); ?></lastBuildDate>
		<atom:link href="<?php p($_['rssLink']); ?>" rel="self" type="application/rss+xml" />
<?php foreach ($_['activities'] as $activity) { ?>
		<item>
			<guid isPermaLink="false"><?php p($activity['activity_id']); ?></guid>
<?php if (!empty($activity['subject'])): ?>
			<title><?php p($activity['subjectformatted']['full']); ?></title>
<?php endif; ?>
<?php if (!empty($activity['link'])): ?>
			<link><?php p($activity['link']); ?></link>
<?php endif; ?>
<?php if (!empty($activity['timestamp'])): ?>
			<pubDate><?php p(date('r', $activity['timestamp'])); ?></pubDate>
<?php endif; ?>
<?php if (!empty($activity['message'])): ?>
			<description><![CDATA[<?php p($activity['messageformatted']['full']); ?>]]></description>
<?php endif; ?>
		</item>
<?php } ?>
	</channel>
</rss>
