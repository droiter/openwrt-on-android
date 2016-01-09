<?php
/**
*
* This file is part of the phpBB Forum Software package.
* @简体中文语言　David Yin <http://www.g2soft.net/>
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACTIVE_TOPICS'			=> '活跃主题',
	'ANNOUNCEMENTS'			=> '公告',

	'FORUM_PERMISSIONS'		=> '论坛权限',

	'ICON_ANNOUNCEMENT'		=> '公告',
	'ICON_STICKY'			=> '置顶',

	'LOGIN_NOTIFY_FORUM'	=> '您在本版面有通知，请登录查看。',

	'MARK_TOPICS_READ'		=> '标记主题为已读',

	'NEW_POSTS_HOT'			=> '有新帖[热门]',	// Not used anymore
	'NEW_POSTS_LOCKED'		=> '有新帖[锁定]',	// Not used anymore
	'NO_NEW_POSTS_HOT'		=> '无新帖[热门]',	// Not used anymore
	'NO_NEW_POSTS_LOCKED'	=> '无新帖[锁定]',	// Not used anymore
	'NO_READ_ACCESS'		=> '您没有阅读本版面文章的权限。',
	'NO_UNREAD_POSTS_HOT'      => '无新帖 [ 活跃 ]',
	'NO_UNREAD_POSTS_LOCKED'   => '无新帖 [ 锁定 ]',

	'POST_FORUM_LOCKED'		=> '版面被锁定',

	'TOPICS_MARKED'			=> '这个版面的文章现在被标记为已读',
	
	'UNREAD_POSTS_HOT'      => '有新帖 [ 热门 ]',
	'UNREAD_POSTS_LOCKED'   => '有新帖 [ 锁定 ]',

	'VIEW_FORUM'			=> '查看版面',
	'VIEW_FORUM_TOPICS'		=> array(
		1	=> '%d 主题',
		2	=> '%d 主题',
	),
));
