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
	'EXTENSION'					=> '扩展',
	'EXTENSIONS'				=> '扩展',
	'EXTENSIONS_ADMIN'			=> '扩展管理器',
	'EXTENSIONS_EXPLAIN'		=> '扩展管理器是phpBB论坛的一个工具，允许你管理所有扩展状态和查看有关信息。',
	'EXTENSION_INVALID_LIST'	=> '扩展 “%s” 无效。<br />%s<br /><br />',
	'EXTENSION_NOT_AVAILABLE'	=> '所选扩展对当前论坛不可用，请验证phpBB和PHP版本是否允许(看详情页)。',
	'EXTENSION_DIR_INVALID'		=> '所选扩展有一个无效目录结构，所以无法启用。',
	'EXTENSION_NOT_ENABLEABLE'	=> '所选扩展不能启用，请验证扩展的要求。',

	'DETAILS'				=> '详情',

	'EXTENSIONS_DISABLED'	=> '禁用的扩展',
	'EXTENSIONS_ENABLED'	=> '启用的扩展',

	'EXTENSION_DELETE_DATA'	=> '删除数据',
	'EXTENSION_DISABLE'		=> '禁用',
	'EXTENSION_ENABLE'		=> '启用',

	'EXTENSION_DELETE_DATA_EXPLAIN'	=> '删除扩展数据将移除所有数据和设置，扩展文件仍然保留以后可以重启。',
	'EXTENSION_DISABLE_EXPLAIN'		=> '禁用扩展将保留文件、数据和设置但其功能失效。',
	'EXTENSION_ENABLE_EXPLAIN'		=> '启用扩展允许你在论坛上使用它。',

	'EXTENSION_DELETE_DATA_IN_PROGRESS'	=> '扩展数据正在删除，完成前请不要关闭或刷新此页。',
	'EXTENSION_DISABLE_IN_PROGRESS'	=> '扩展正在禁用，完成前请不要关闭或刷新此页。',
	'EXTENSION_ENABLE_IN_PROGRESS'	=> '扩展正在启用，完成前请不要关闭或刷新此页。',

	'EXTENSION_DELETE_DATA_SUCCESS'	=> '扩展数据删除成功',
	'EXTENSION_DISABLE_SUCCESS'		=> '扩展禁用成功',
	'EXTENSION_ENABLE_SUCCESS'		=> '扩展启用成功',

	'EXTENSION_NAME'			=> '扩展名称',
	'EXTENSION_ACTIONS'			=> '活动',
	'EXTENSION_OPTIONS'			=> '选项',
	'EXTENSION_INSTALL_HEADLINE'=> '安装扩展功能',
	'EXTENSION_INSTALL_EXPLAIN'	=> '<ol>
			<li>从phpBB 的扩展数据库中下载</li>
			<li>解压缩，并上传到你的phpBB论坛的 <samp>ext/</samp> 文件夹</li>
			<li>用扩展管理器，启用它</li>
		</ol>',
		'EXTENSION_UPDATE_HEADLINE'	=> '更新扩展',
	'EXTENSION_UPDATE_EXPLAIN'	=> '<ol>
			<li>禁用扩展</li>
			<li>删除扩展文件</li>
			<li>上传新文件</li>
			<li>启用扩展</li>
		</ol>',
	'EXTENSION_REMOVE_HEADLINE'	=> '完全删除一个扩展',
	'EXTENSION_REMOVE_EXPLAIN'	=> '<ol>
			<li>禁用扩展</li>
			<li>删除扩展数据</li>
			<li>删除扩展文件</li>
		</ol>',

	'EXTENSION_DELETE_DATA_CONFIRM'	=> '你确定要删除有关 “%s” 的数据吗？<br /><br />这将删除它的所有数据和设置而且不能恢复！',
	'EXTENSION_DISABLE_CONFIRM'		=> '你确定要禁用 “%s” 扩展吗？',
	'EXTENSION_ENABLE_CONFIRM'		=> '你确定要启用 “%s” 扩展吗？',
	'EXTENSION_FORCE_UNSTABLE_CONFIRM'	=> '你确定要强制使用不稳定版本吗？',

	'RETURN_TO_EXTENSION_LIST'	=> '返回扩展列表',

	'EXT_DETAILS'			=> '扩展详情',
	'DISPLAY_NAME'			=> '显示名称',
	'CLEAN_NAME'			=> '名称',
	'TYPE'					=> '类型',
	'DESCRIPTION'			=> '描述',
	'VERSION'				=> '版本',
	'HOMEPAGE'				=> '主页',
	'PATH'					=> '文件路径',
	'TIME'					=> '发布时间',
	'LICENSE'				=> '许可',

	'REQUIREMENTS'			=> '要求',
	'PHPBB_VERSION'			=> 'phpBB版本',
	'PHP_VERSION'			=> 'PHP版本',
	'AUTHOR_INFORMATION'	=> '作者信息',
	'AUTHOR_NAME'			=> '名称',
	'AUTHOR_EMAIL'			=> 'Email',
	'AUTHOR_HOMEPAGE'		=> '主页',
	'AUTHOR_ROLE'			=> '角色',

	'NOT_UP_TO_DATE'		=> '%s 不是最新的',
	'UP_TO_DATE'			=> '%s 是最新的',
	'ANNOUNCEMENT_TOPIC'	=> '发布公告',
	'DOWNLOAD_LATEST'		=> '下载版本',
	'NO_VERSIONCHECK'		=> '没有给版本检查信息。',

	'VERSIONCHECK_FORCE_UPDATE_ALL'		=> '重新检查所有版本',
	'FORCE_UNSTABLE'					=> '总是检查不稳定版本',
	'EXTENSIONS_VERSION_CHECK_SETTINGS'	=> '版本检查设置',

	'BROWSE_EXTENSIONS_DATABASE'		=> '浏览扩展数据库',

	'META_FIELD_NOT_SET'	=> '所需的元字段 %s 还没有设置。',
	'META_FIELD_INVALID'	=> '元字段 %s 无效。',
));
