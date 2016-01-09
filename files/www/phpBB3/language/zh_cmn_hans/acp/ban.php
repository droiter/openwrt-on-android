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

// Banning
$lang = array_merge($lang, array(
	'1_HOUR'		=> '1小时',
	'30_MINS'		=> '30分钟',
	'6_HOURS'		=> '6小时',

	'ACP_BAN_EXPLAIN'	=> '在这里您可以进行对用户名、IP地址或信箱地址的封禁操作。这些操作可以阻止特定用户访问整个论坛。如果愿意，您可以给出一个简短（至多255字节）的封禁理由，它将显示在管理记录中。您也可以控制封禁期限，如果您希望设定一个封禁解除日期，请将封禁期限设定为 <u>直到</u> ，并在下面以 yyyy-mm-dd（年-月-日）的格式填入日期。',

	'BAN_EXCLUDE'			=> '排除于封禁列表',
	'BAN_LENGTH'			=> '封禁期限',
	'BAN_REASON'			=> '封禁原因',
	'BAN_GIVE_REASON'		=> '显示给被封者的封禁原因',
	'BAN_UPDATE_SUCCESSFUL'	=> '封禁列表更新成功',
	'BANNED_UNTIL_DATE'		=> '直至 %s', // Example: "until Mon 13.Jul.2009, 14:44"
	'BANNED_UNTIL_DURATION'	=> '%1$s (直至 %2$s)', // Example: "7 days (until Tue 14.Jul.2009, 14:44)"

	'EMAIL_BAN'					=> '封禁信箱地址',
	'EMAIL_BAN_EXCLUDE_EXPLAIN'	=> '被排除的Email地址将不受任何封禁影响。',
	'EMAIL_BAN_EXPLAIN'			=> '要指定多个Email地址，请在每行输入一个。可以使用 * 作为通配符，例如：<samp>*@hotmail.com</samp>，<samp>*@*.domain.tld</samp>，等。',
	'EMAIL_NO_BANNED'			=> '没有被封禁的信箱地址',
	'EMAIL_UNBAN'				=> '解除封禁或解除排除Email地址',
	'EMAIL_UNBAN_EXPLAIN'		=> '您可以选择多个Email地址并对其执行解除封禁或解除排除的操作。被指定为排除的Email地址有特别背景标记。',

	'IP_BAN'					=> '封禁IP地址',
	'IP_BAN_EXCLUDE_EXPLAIN'	=> '被排除的IP地址将不受任何封禁影响。',
	'IP_BAN_EXPLAIN'			=> '要指定多个IP地址或域名，请在每行输入一个。要指定IP段，在两个IP地址间使用连字符（-）连接。通配符是 *。',
	'IP_HOSTNAME'				=> 'IP地址或域名',
	'IP_NO_BANNED'				=> '没有被封禁的IP地址',
	'IP_UNBAN'					=> '解除封禁或解除排除IP地址',
	'IP_UNBAN_EXPLAIN'			=> '您可以选择多个IP地址并对其执行解除封禁或解除排除的操作。被指定为排除的IP地址有特别背景标记。',

	'LENGTH_BAN_INVALID'		=> '日期格式必须为 <kbd>YYYY-MM-DD</kbd>.',

	'OPTIONS_BANNED'			=> '被禁的',
	'OPTIONS_EXCLUDED'			=> '排除的',

	'PERMANENT'		=> '永久',
	
	'UNTIL'						=> '直到',
	'USER_BAN'					=> '封禁用户名',
	'USER_BAN_EXCLUDE_EXPLAIN'	=> '被排除的用户名将不受任何封禁影响。',
	'USER_BAN_EXPLAIN'			=> '要指定多个用户名，请在每行输入一个。您可以使用【查找用户】来查找和添加用户名。',
	'USER_NO_BANNED'			=> '没有被封禁的用户名',
	'USER_UNBAN'				=> '解除封禁或解除排除用户名',
	'USER_UNBAN_EXPLAIN'		=> '您可以选择多个用户名并对其执行解除封禁或解除排除的操作。被指定为排除的用户名有特别背景标记。',
));
