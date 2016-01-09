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

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'CLI_CONFIG_CANNOT_CACHED'			=> '如果配置选项会修改频繁，请加上这个选项。',
	'CLI_CONFIG_CURRENT'				=> '当前配置值，使用0和1表示布尔值',
	'CLI_CONFIG_DELETE_SUCCESS'			=> '成功删除配置 %s。',
	'CLI_CONFIG_NEW'					=> '新的配置值，使用0和1表示布尔值',
	'CLI_CONFIG_NOT_EXISTS'				=> '配置 %s 不存在',
	'CLI_CONFIG_OPTION_NAME'			=> '配置选项的名称',
	'CLI_CONFIG_PRINT_WITHOUT_NEWLINE'	=> '设置这个选项如果打印值时末尾不带新行。',
	'CLI_CONFIG_INCREMENT_BY'			=> '增加的数额',
	'CLI_CONFIG_INCREMENT_SUCCESS'		=> '成功增加配置 %s',
	'CLI_CONFIG_SET_FAILURE'			=> '无法设置配置 %s',
	'CLI_CONFIG_SET_SUCCESS'			=> '成功设置配置 %s',

	'CLI_DESCRIPTION_CRON_LIST'					=> '打印包括已准备和未准备的计划任务列表。',
	'CLI_DESCRIPTION_CRON_RUN'					=> '运行所有已准备之计划任务。',
	'CLI_DESCRIPTION_CRON_RUN_ARGUMENT_1'		=> '要运行的任务名称',
	'CLI_DESCRIPTION_DB_MIGRATE'				=> '应用改变更新数据库。',
	'CLI_DESCRIPTION_DELETE_CONFIG'				=> '删除一个配置选项',
	'CLI_DESCRIPTION_DISABLE_EXTENSION'			=> '禁用指定扩展。',
	'CLI_DESCRIPTION_ENABLE_EXTENSION'			=> '启用指定扩展。',
	'CLI_DESCRIPTION_FIND_MIGRATIONS'			=> '找到不依赖的改变。',
	'CLI_DESCRIPTION_GET_CONFIG'				=> '获取一个配置选项的值',
	'CLI_DESCRIPTION_INCREMENT_CONFIG'			=> '增加一个配置选项的值',
	'CLI_DESCRIPTION_LIST_EXTENSIONS'			=> '列出数据库和文件系统中的所有扩展。',
	'CLI_DESCRIPTION_OPTION_SAFE_MODE'			=> '安全模式运行(不启用扩展)',
	'CLI_DESCRIPTION_OPTION_SHELL'				=> '开始命令行模式。',
	'CLI_DESCRIPTION_PURGE_EXTENSION'			=> '清除指定的扩展。',
	'CLI_DESCRIPTION_RECALCULATE_EMAIL_HASH'	=> '重新计算用户表中的用户邮件地址哈希值。',
	'CLI_DESCRIPTION_SET_ATOMIC_CONFIG'			=> '只在配置选项的旧值匹配当前值时，才设置它',
	'CLI_DESCRIPTION_SET_CONFIG'				=> '设置一个配置选项的值',

	'CLI_EXTENSION_DISABLE_FAILURE'		=> '无法禁用扩展 %s',
	'CLI_EXTENSION_DISABLE_SUCCESS'		=> '成功禁用扩展 %s',
	'CLI_EXTENSION_ENABLE_FAILURE'		=> '无法启用扩展 %s',
	'CLI_EXTENSION_ENABLE_SUCCESS'		=> '成功启用扩展 %s',
	'CLI_EXTENSION_NAME'				=> '扩展名称',
	'CLI_EXTENSION_PURGE_FAILURE'		=> '无法清除扩展 %s',
	'CLI_EXTENSION_PURGE_SUCCESS'		=> '成功清除扩展 %s',
	'CLI_EXTENSION_NOT_FOUND'			=> '没有找到扩展。',
	'CLI_EXTENSIONS_AVAILABLE'			=> '可用的',
	'CLI_EXTENSIONS_DISABLED'			=> '已禁用',
	'CLI_EXTENSIONS_ENABLED'			=> '已启用',

	'CLI_FIXUP_RECALCULATE_EMAIL_HASH_SUCCESS'	=> '成功重新计算所有邮件哈希。',
));
