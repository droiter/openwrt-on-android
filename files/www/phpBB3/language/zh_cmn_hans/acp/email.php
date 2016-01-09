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

// Email settings
$lang = array_merge($lang, array(
	'ACP_MASS_EMAIL_EXPLAIN'		=> '这里您可以给所有的用户或者某个用户组的用户发送email。发送时，将会先往管理员的邮箱中发送一份拷贝，同时也发送到其他收信人的邮箱中。如果您要给很多人发送email，当提交后请耐心等待而不要动这个页面。一般群发需要很长一段时间，当结束的时候会提示您。',
	'ALL_USERS'						=> '所有用户',

	'COMPOSE'				=> '撰写',

	'EMAIL_SEND_ERROR'		=> '当发送email的时候出现一个或多个错误。请检查 %s错误日志%s 以得到更多信息。',
	'EMAIL_SENT'			=> '这个邮件已经被发送。',
	'EMAIL_SENT_QUEUE'		=> '这个邮件已经在队列中等待发送。',

	'LOG_SESSION'			=> '记录email对话到关键日志',

	'SEND_IMMEDIATELY'		=> '立即发送',
	'SEND_TO_GROUP'			=> '发送到组',
	'SEND_TO_USERS'			=> '发送到用户',
	'SEND_TO_USERS_EXPLAIN'	=> '这里输入用户名将覆盖上面选择的组。用多行输入多个用户名。',

	'MAIL_BANNED'			=> '邮件被禁用户',
	'MAIL_BANNED_EXPLAIN'	=> '向一个组群发邮件时您可以在这里选择是否让被禁用户也收到该邮件。',	
	'MAIL_HIGH_PRIORITY'	=> '高',
	'MAIL_LOW_PRIORITY'		=> '低',
	'MAIL_NORMAL_PRIORITY'	=> '中',
	'MAIL_PRIORITY'			=> '邮件优先级',
	'MASS_MESSAGE'			=> '信件内容',
	'MASS_MESSAGE_EXPLAIN'	=> '请注意您只能输入普通文本。所有超文本标记将在发送时被清除。',
	
	'NO_EMAIL_MESSAGE'		=> '您还没有输入内容?',
	'NO_EMAIL_SUBJECT'		=> '您还没有输入标题.',
));
