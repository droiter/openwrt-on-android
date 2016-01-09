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
	'CONFIG_NOT_EXIST'					=> '配置设置 "%s" 不存在。',

	'GROUP_NOT_EXIST'					=> '组 "%s" 不存在。',

	'MIGRATION_APPLY_DEPENDENCIES'		=> '应用 %s 的依赖。',
	'MIGRATION_DATA_DONE'				=> '安装的数据: %1$s; 时间: %2$.2f 秒',
	'MIGRATION_DATA_IN_PROGRESS'		=> '安装数据: %1$s; 时间: %2$.2f 秒',
	'MIGRATION_DATA_RUNNING'			=> '安装数据: %s。',
	'MIGRATION_EFFECTIVELY_INSTALLED'	=> '改变已经安装(跳过): %s',
	'MIGRATION_EXCEPTION_ERROR'			=> '请求时出错，出现一个异常。出错前的修改会进行逆转，但你应该检查论坛是否有问题。',
	'MIGRATION_NOT_FULFILLABLE'			=> '改变 "%1$s" 无法实现，丢失改变 "%2$s"。',
	'MIGRATION_NOT_VALID'				=> '%s 不是一个有效的改变。',
	'MIGRATION_SCHEMA_DONE'				=> '安装的模式: %1$s; 时间: %2$.2f 秒',
	'MIGRATION_SCHEMA_RUNNING'			=> '安装模式: %s。',

	'MIGRATION_INVALID_DATA_MISSING_CONDITION'		=> '无效的 Migration。if 语句缺少条件。',
	'MIGRATION_INVALID_DATA_MISSING_STEP'			=> '无效的 Migration。if 语句缺少一个Migration 步骤的有效调用。',
	'MIGRATION_INVALID_DATA_CUSTOM_NOT_CALLABLE'	=> '无效的 Migration。 不能调用自定义的函数。',
	'MIGRATION_INVALID_DATA_UNKNOWN_TYPE'			=> '无效的 Migration。 一个未知的 Migration 工具类型。',
	'MIGRATION_INVALID_DATA_UNDEFINED_TOOL'			=> '无效的 Migration。 一个未定义的 Migration 工具。',
	'MIGRATION_INVALID_DATA_UNDEFINED_METHOD'		=> '无效的 Migration。 一个未定义的 Migration 工具方法。',

	'MODULE_ERROR'						=> '一个错误发生在创建组件时: %s',
	'MODULE_INFO_FILE_NOT_EXIST'		=> '一个必须组件信息文件丢失: %2$s',
	'MODULE_NOT_EXIST'					=> '一个必须组件不存在: %s',

	'PERMISSION_NOT_EXIST'				=> '权限设置 "%s" 不存在。',

	'ROLE_NOT_EXIST'					=> '权限角色 "%s" 不存在。',
));
