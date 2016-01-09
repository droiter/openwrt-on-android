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

// Bot settings
$lang = array_merge($lang, array(
	'BOTS'				=> '管理机器人',
	'BOTS_EXPLAIN'		=> '“机器人”、“蜘蛛” 及 “爬虫” 一般是搜索引擎用于更新其数据库的自动工具。因为这些小东西会贪婪地使用对话数量，导致奇怪的访客数量，增加论坛负载，甚至会导致论坛短时间内访问受阻。这里您可以定义一系列特殊用户来预防这些问题。',
	'BOT_ACTIVATE'		=> '启用',
	'BOT_ACTIVE'		=> '启用的机器人',
	'BOT_ADD'			=> '添加机器人',
	'BOT_ADDED'			=> '新机器人添加完成.',
	'BOT_AGENT'			=> '符合的代理',
	'BOT_AGENT_EXPLAIN'	=> '匹配机器人代理的字符串, 允许部分匹配。',
	'BOT_DEACTIVATE'	=> '冻结',
	'BOT_DELETED'		=> '机器人删除完成。',
	'BOT_EDIT'			=> '编辑机器人',
	'BOT_EDIT_EXPLAIN'	=> '这里您可以增加或修改当前的机器人条目。您可以设置一个机构字符串和/或多个IP地址(或者地址范围)来匹配。当设置匹配字符串时要小心。您也可以指定机器人浏览时看到的界面和语言。设定一个简单的界面可以减少机器人使用的带宽。记住给机器人用户组设置合适的权限。',
	'BOT_LANG'			=> '机器人语言',
	'BOT_LANG_EXPLAIN'	=> '当机器人浏览时显示的语言。',
	'BOT_LAST_VISIT'	=> '最后访问',
	'BOT_IP'			=> '机器人IP地址',
	'BOT_IP_EXPLAIN'	=> '允许局部匹配, 用英文逗号区分多个地址。',
	'BOT_NAME'			=> '机器人名称',
	'BOT_NAME_EXPLAIN'	=> '仅用于您对机器人的称呼。',
	'BOT_NAME_TAKEN'	=> '这个名字已经在您的论坛中使用，不能作为机器人的名字。',
	'BOT_NEVER'			=> '从未',
	'BOT_STYLE'			=> '机器人界面',
	'BOT_STYLE_EXPLAIN'	=> '当机器人浏览时显示的界面。',
	'BOT_UPDATED'		=> '机器人更新完成。',

	'ERR_BOT_AGENT_MATCHES_UA'	=> '您提供的机器人代理和您现在使用的很相似。请调整这个机器人的代理。',
	'ERR_BOT_NO_IP'				=> '您提供的IP地址或主机名是无效的。',
	'ERR_BOT_NO_MATCHES'		=> '您必须为这个机器人提供至少一个匹配的代理或IP地址。',

	'NO_BOT'		=> '没有找到指定ID的机器人。',
	'NO_BOT_GROUP'	=> '无法找到机器人用户组。',
));
