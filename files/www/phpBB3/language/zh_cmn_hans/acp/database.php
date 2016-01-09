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

// Database Backup/Restore
$lang = array_merge($lang, array(
	'ACP_BACKUP_EXPLAIN'	=> '这里您可以备份所有phpBB相关的数据。您可以将备份保存在 <samp>store/</samp> 文件夹或者直接下载。如果您的服务器支持，您可以将文件压缩成好几种格式。',
	'ACP_RESTORE_EXPLAIN'	=> '这将使用备份文件进行一次所有phpBB表单的完整恢复。如果您的服务器支持您可以使用gzip或bzip2格式压缩的文本文件。<strong>警告</strong> 这将覆盖所有现在的数据。恢复可能需要一个很长的时间，在其完成前请不要动这个页面。备份存储在 <samp>store/</samp> 文件夹，由phpBB的备份功能生成。使用由其他系统生成的备份进行恢复操作可能会失败。',

	'BACKUP_DELETE'		=> '备份文件已经被删除。',
	'BACKUP_INVALID'	=> '选择的备份文件无效。',
	'BACKUP_OPTIONS'	=> '备份选项',
	'BACKUP_SUCCESS'	=> '备份创建完成。',
	'BACKUP_TYPE'		=> '备份类型',

	'DATABASE'			=> '数据库工具',
	'DATA_ONLY'			=> '仅数据',
	'DELETE_BACKUP'		=> '删除备份',
	'DELETE_SELECTED_BACKUP'	=> '您确认要删除选中的备份吗？',
	'DESELECT_ALL'		=> '取消全部',
	'DOWNLOAD_BACKUP'	=> '下载备份',

	'FILE_TYPE'			=> '文件类型',
	'FILE_WRITE_FAIL'	=> '无法在存储文件夹下写入。',
	'FULL_BACKUP'		=> '完整',

	'RESTORE_FAILURE'		=> '备份文件已损坏。',
	'RESTORE_OPTIONS'		=> '恢复选项',
	'RESTORE_SELECTED_BACKUP'	=> '您确定您要恢复选定的备份吗？',
	'RESTORE_SUCCESS'		=> '数据库已经成功恢复。<br /><br />您的论坛回到了备份前的状态。',

	'SELECT_ALL'			=> '选择全部',
	'SELECT_FILE'			=> '选择一个文件',
	'START_BACKUP'			=> '开始备份',
	'START_RESTORE'			=> '开始恢复',
	'STORE_AND_DOWNLOAD'	=> '存储并下载',
	'STORE_LOCAL'			=> '本地存储文件',
	'STRUCTURE_ONLY'		=> '仅结构',

	'TABLE_SELECT'		=> '表单选择',
	'TABLE_SELECT_ERROR'=> '您必须选中至少一个表单。',
));
