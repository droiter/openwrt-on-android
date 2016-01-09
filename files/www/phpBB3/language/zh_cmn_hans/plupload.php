<?php
/**
*
* This file is part of the phpBB Forum Software package.
* @简体中文语言　David Yin <http://www.g2soft.net/>
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @copyright (c) 2010-2013 Moxiecode Systems AB
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
	'PLUPLOAD_ADD_FILES'		=> '添加文件',
	'PLUPLOAD_ADD_FILES_TO_QUEUE'	=> '添加文件到上传队列然后点开始按钮。',
	'PLUPLOAD_ALREADY_QUEUED'	=> '%s 已在队列中。',
	'PLUPLOAD_CLOSE'			=> '关闭',
	'PLUPLOAD_DRAG'				=> '拖拽文件到这里。',
	'PLUPLOAD_DUPLICATE_ERROR'	=> '重复文件错误。',
	'PLUPLOAD_DRAG_TEXTAREA'	=> '你也可以通过拖拽附加文件。',
	'PLUPLOAD_ERR_INPUT'		=> '无法读取上传文件。',
	'PLUPLOAD_ERR_MOVE_UPLOADED'	=> '移动上传文件失败。',
	'PLUPLOAD_ERR_OUTPUT'		=> '无法开始写入文件。',
	'PLUPLOAD_ERR_FILE_TOO_LARGE'	=> '文件太大:',
	'PLUPLOAD_ERR_FILE_COUNT'	=> '文件数量错误。',
	'PLUPLOAD_ERR_FILE_INVALID_EXT'	=> '无效文件扩展:',
	'PLUPLOAD_ERR_RUNTIME_MEMORY'	=> '运行时可用内存不足。',
	'PLUPLOAD_ERR_UPLOAD_URL'	=> '上传地址可能错误或不存在。',
	'PLUPLOAD_EXTENSION_ERROR'	=> '文件扩展错误。',
	'PLUPLOAD_FILE'				=> '文件: %s',
	'PLUPLOAD_FILE_DETAILS'		=> '文件: %s, 大小: %d, 最大文件大小: %d',
	'PLUPLOAD_FILENAME'			=> '文件名',
	'PLUPLOAD_FILES_QUEUED'		=> '%d 文件排队中',
	'PLUPLOAD_GENERIC_ERROR'	=> '一般错误。',
	'PLUPLOAD_HTTP_ERROR'		=> 'HTTP 错误。',
	'PLUPLOAD_IMAGE_FORMAT'		=> '图片格式错误或不支持。',
	'PLUPLOAD_INIT_ERROR'		=> '初始化错误。',
	'PLUPLOAD_IO_ERROR'			=> 'IO 错误。',
	'PLUPLOAD_NOT_APPLICABLE'	=> 'N/A',
	'PLUPLOAD_SECURITY_ERROR'	=> '安全性错误',
	'PLUPLOAD_SELECT_FILES'		=> '选择文件',
	'PLUPLOAD_SIZE'				=> '大小',
	'PLUPLOAD_SIZE_ERROR'		=> '文件大小错误。',
	'PLUPLOAD_STATUS'			=> '状态',
	'PLUPLOAD_START_UPLOAD'		=> '开始上传',
	'PLUPLOAD_START_CURRENT_UPLOAD'	=> '开始上传队列',
	'PLUPLOAD_STOP_UPLOAD'		=> '停止上传',
	'PLUPLOAD_STOP_CURRENT_UPLOAD'	=> '停止当前上传',
	// Note: This string is formatted independently by plupload and so does not
	// use the same formatting rules as normal phpBB translation strings
	'PLUPLOAD_UPLOADED'			=> '上传的 %d/%d 文件',
));
