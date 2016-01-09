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
	'RECAPTCHA_LANG'				=> '英文',
	'RECAPTCHA_NOT_AVAILABLE'		=> '要使用reCaptcha， 您需要先在<a href="http://www.google.com/recaptcha">www.google.com/recaptcha</a>上注册一个账号。',
	'CAPTCHA_RECAPTCHA'				=> 'reCaptcha',
	'RECAPTCHA_INCORRECT'			=> '您输入的验证码不正确',

	'RECAPTCHA_PUBLIC'				=> 'reCaptcha公钥',
	'RECAPTCHA_PUBLIC_EXPLAIN'		=> '您的公开reCaptcha密钥。密钥可以从<a href="http://www.google.com/recaptcha">www.google.com/recaptcha</a>上获得。',
	'RECAPTCHA_PRIVATE'				=> 'reCaptcha私钥',
	'RECAPTCHA_PRIVATE_EXPLAIN'		=> '您的私人reCaptcha密钥。密钥可以从 <a href="http://www.google.com/recaptcha">www.google.com/recaptcha</a> 上获得。',

	'RECAPTCHA_EXPLAIN'				=> '为了证明您不是Spam机器人， 请输入下面显示的两个词。',
	'RECAPTCHA_SOCKET_ERROR'		=> '连接RECAPTCHA服务出问题：无法打开socket，请稍后重试。',
));
