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
	'ACP_ATTACHMENT_SETTINGS_EXPLAIN'	=> '这里您可以配置附件和相关的附件分类.',
	'ACP_EXTENSION_GROUPS_EXPLAIN'		=> '这里您可以添加，删除，修改或禁用扩展名组。选项包括设定附件类别，更改下载机制和定义附件组的简明图标。',
	'ACP_MANAGE_EXTENSIONS_EXPLAIN'		=> '这里您可以管理您允许的附件扩展名。要激活某个扩展名，请到扩展名类别管理面板。请注意不要允许脚本文件 (例如 <code>php</code>，<code>php3</code>，<code>php4</code>，<code>phtml</code>，<code>pl</code>，<code>cgi</code>，<code>py</code>，<code>rb</code>，<code>asp</code>，<code>aspx</code>等类型…)。',
	'ACP_ORPHAN_ATTACHMENTS_EXPLAIN'	=> '这里您可以看见附件上传目录中存在但是没有发表在帖子中的文件。这很可能因为用户添加了附件但是最后没提交帖子。您可以删除这些附件或者将其添加到存在的帖子中。添加附件到帖子中需要一个有效的帖子ID，您需要自己添加这个ID，这个功能主要是帮助那些希望通过其他方式上传文件的人们，可以把附件(通常会很大)添加到已经发表的帖子中。',
	'ADD_EXTENSION'						=> '添加扩展名',
	'ADD_EXTENSION_GROUP'				=> '添加扩展名组',
	'ADMIN_UPLOAD_ERROR'				=> '添加文件中出错: %s',
	'ALLOWED_FORUMS'					=> '允许的版面',
	'ALLOWED_FORUMS_EXPLAIN'			=> '允许在选中的版面中发表指定的扩展名附件',
	'ALLOWED_IN_PM_POST'				=> '允许',
	'ALLOW_ATTACHMENTS'					=> '允许附件',
	'ALLOW_ALL_FORUMS'					=> '所有版面中允许',
	'ALLOW_IN_PM'						=> '短信中允许',
	'ALLOW_PM_ATTACHMENTS'				=> '允许在私人短信中添加附件',
	'ALLOW_SELECTED_FORUMS'				=> '只允许在下列选中的版面',
	'ASSIGNED_EXTENSIONS'				=> '指定的扩展名',
	'ASSIGNED_GROUP'					=> '指定的扩展名组',
	'ATTACH_EXTENSIONS_URL'				=> '扩展名',
	'ATTACH_EXT_GROUPS_URL'				=> '扩展名组',
	'ATTACH_ID'							=> 'ID',
	'ATTACH_MAX_FILESIZE'				=> '最大文件尺寸',
	'ATTACH_MAX_FILESIZE_EXPLAIN'		=> '每个文件的最大尺寸，如果设为0的话上传文件大小只受限于您的 PHP 配置。',
	'ATTACH_MAX_PM_FILESIZE'			=> '最大短信文件尺寸',
	'ATTACH_MAX_PM_FILESIZE_EXPLAIN'	=> '每个用户短信附件的文件大小上限，0表示无限制。',
	'ATTACH_ORPHAN_URL'					=> '幽灵文件',
	'ATTACH_POST_ID'					=> '帖子ID',
	'ATTACH_POST_TYPE'					=> '帖子类型',
	'ATTACH_QUOTA'						=> '合计附件空间',
	'ATTACH_QUOTA_EXPLAIN'				=> '整个论坛可用的最大附件空间，0表示无限制。',
	'ATTACH_TO_POST'					=> '添加附件到帖子',

	'CAT_FLASH_FILES'			=> 'Flash文件',
	'CAT_IMAGES'				=> '图片',
	'CAT_QUICKTIME_FILES'		=> 'Quicktime多媒体文件',
	'CAT_RM_FILES'				=> 'RealMedia多媒体文件',
	'CAT_WM_FILES'				=> 'Windows Media多媒体文件',
	'CHECK_CONTENT'				=> '检查附件',
	'CHECK_CONTENT_EXPLAIN'		=> '一些浏览器可以被欺骗而得到错误的附件文件类型。这个选项可以确保这样的文件不会被上传。',
	'CREATE_GROUP'				=> '创建新类别',
	'CREATE_THUMBNAIL'			=> '创建缩略图',
	'CREATE_THUMBNAIL_EXPLAIN'	=> '在所有可能的情况下创建缩略图。',

	'DEFINE_ALLOWED_IPS'			=> '设置允许的IP地址/主机名',
	'DEFINE_DISALLOWED_IPS'			=> '设置封禁的IP地址/主机名',
	'DOWNLOAD_ADD_IPS_EXPLAIN'		=> '设置多个IP或主机名, 请使用多行输入。设置IP地址范围，请在起止间用破折号 (-)，通配符请用 * 号',
	'DOWNLOAD_REMOVE_IPS_EXPLAIN'	=> '您可以在浏览器中使用合适的键盘和鼠标操作组合删除 (或 取消排除) 多个IP地址，排除的IP地址背景为蓝色。',
	'DISPLAY_INLINED'				=> '帖子中插入图片',
	'DISPLAY_INLINED_EXPLAIN'		=> '如果设置为否, 图片将显示为一个链接。',
	'DISPLAY_ORDER'					=> '附件显示顺序',
	'DISPLAY_ORDER_EXPLAIN'			=> '按时间显示附件。',
	
	'EDIT_EXTENSION_GROUP'			=> '编辑扩展名类别',
	'EXCLUDE_ENTERED_IP'			=> '选中这个以排除输入的IP地址/主机名。',
	'EXCLUDE_FROM_ALLOWED_IP'		=> '从允许的IP地址/主机名中排除IP',
	'EXCLUDE_FROM_DISALLOWED_IP'	=> '从封禁的IP地址/主机名中排除IP',
	'EXTENSIONS_UPDATED'			=> '扩展名更新完成',
	'EXTENSION_EXIST'				=> '扩展名 %s 已经存在',
	'EXTENSION_GROUP'				=> '扩展名类别',
	'EXTENSION_GROUPS'				=> '扩展名类别',
	'EXTENSION_GROUP_DELETED'		=> '扩展名类别删除完成.',
	'EXTENSION_GROUP_EXIST'			=> '扩展名类别 %s 已经存在',

	'EXT_GROUP_ARCHIVES'         => '存档文件',
	'EXT_GROUP_DOCUMENTS'         => '文档',
	'EXT_GROUP_DOWNLOADABLE_FILES'   => '可下载文件',
	'EXT_GROUP_FLASH_FILES'         => 'Flash文件',
	'EXT_GROUP_IMAGES'            => '图片',
	'EXT_GROUP_PLAIN_TEXT'         => '纯文本文件',
	'EXT_GROUP_QUICKTIME_MEDIA'      => 'Quicktime多媒体',
	'EXT_GROUP_REAL_MEDIA'         => 'Real Media多媒体',
	'EXT_GROUP_WINDOWS_MEDIA'      => 'Windows Media多媒体',

	'FILES_GONE'			=> '一些你要删除的附件不存在，它们可能已被删除，存在的附件被删除。',
	'FILES_STATS_WRONG'		=> '你的文件数据好像不准确需要重新同步，实际值：附件数量 = %1$d，附件总大小 = %2$s。<br />点%3$s这里%4$s重新同步它们。',

	'GO_TO_EXTENSIONS'		=> '前往扩展名管理界面',
	'GROUP_NAME'			=> '组名',

	'IMAGE_LINK_SIZE'			=> '图片链接尺寸',
	'IMAGE_LINK_SIZE_EXPLAIN'	=> '当图片大于这个时在文本中插入的图片会显示为一个链接。要禁用这个功能请将值设为长 0px 宽 0px.',
	'IMAGICK_PATH'				=> 'Imagemagick路径',
	'IMAGICK_PATH_EXPLAIN'		=> 'Imagemagick程序的完整路径, 例如 <samp>/usr/bin/</samp>',

	'MAX_ATTACHMENTS'				=> '每个帖子的最大附件数量',
	'MAX_ATTACHMENTS_PM'			=> '每个短信的最大附件数量',
	'MAX_EXTGROUP_FILESIZE'			=> '最大文件大小',
	'MAX_IMAGE_SIZE'				=> '最大图片尺寸',
	'MAX_IMAGE_SIZE_EXPLAIN'		=> '图片附件的最大尺寸. 值都设为0px将禁用这个功能。',
	'MAX_THUMB_WIDTH'				=> '缩略图的最大宽度(象素值)',
	'MAX_THUMB_WIDTH_EXPLAIN'		=> '生成的缩略图将不会超过这里设置的宽度',
	'MIN_THUMB_FILESIZE'			=> '缩略图文件下限',
	'MIN_THUMB_FILESIZE_EXPLAIN'	=> '当图片文件大小低于此值时不创建缩略图.',
	'MODE_INLINE'					=> '图文混排',
	'MODE_PHYSICAL'					=> '物理',

	'NOT_ALLOWED_IN_PM'			=> '只在帖子中允许',
	'NOT_ALLOWED_IN_PM_POST'	=> '不允许',
	'NOT_ASSIGNED'				=> '没有指定',
	'NO_ATTACHMENTS'			=> '没有找到这段期间的附件。',
	'NO_EXT_GROUP'				=> '无',
	'NO_EXT_GROUP_NAME'			=> '没有输入类别名称',
	'NO_EXT_GROUP_SPECIFIED'	=> '没有指定扩展名类别。',
	'NO_FILE_CAT'				=> '无',
	'NO_IMAGE'					=> '没有图片',
	'NO_THUMBNAIL_SUPPORT'		=> '缩略图功能被禁用因为没有可支持的GD库函数和imagemagick可执行模块。',
	'NO_UPLOAD_DIR'				=> '您指定的上载目录不存在.',
	'NO_WRITE_UPLOAD'			=> '您指定的上载目录不可写入。请更改目录权限使web服务器可以作写操作。',

	'ONLY_ALLOWED_IN_PM'	=> '只在私人短信中允许',
	'ORDER_ALLOW_DENY'		=> '允许',
	'ORDER_DENY_ALLOW'		=> '禁止',

	'REMOVE_ALLOWED_IPS'		=> '删除或排除 <em>允许</em> 的IP地址/主机名',
	'REMOVE_DISALLOWED_IPS'		=> '删除或排除 <em>禁止</em> 的IP地址/主机名',
	'RESYNC_FILES_STATS_CONFIRM'	=> '你确定要同步文件状态吗？',

	'SEARCH_IMAGICK'				=> '搜索 Imagemagick',
	'SECURE_ALLOW_DENY'				=> '允许/封禁 列表',
	'SECURE_ALLOW_DENY_EXPLAIN'		=> '当防盗链启用后, <strong>允许列表</strong> 和 <strong>封禁列表</strong> 规定了特例的情况',
	'SECURE_DOWNLOADS'				=> '启用防盗链功能',
	'SECURE_DOWNLOADS_EXPLAIN'		=> '当开启选项后，下载将仅对您自己的域名内的链接和下面设定的IP地址/主机名有效。',
	'SECURE_DOWNLOAD_NOTICE'		=> '防盗链功能没有启用. 下面的设置将在启用后生效。',
	'SECURE_DOWNLOAD_UPDATE_SUCCESS'=> 'IP 列表更新完成。',
	'SECURE_EMPTY_REFERRER'			=> '允许空转向',
	'SECURE_EMPTY_REFERRER_EXPLAIN'	=> '防盗链基于判断转向来源. 您希望允许来源为空的下载吗？',
	'SETTINGS_CAT_IMAGES'			=> '图片类别设置',
	'SPECIAL_CATEGORY'				=> '特殊类别',
	'SPECIAL_CATEGORY_EXPLAIN'		=> '特殊类别区别于在帖子中显示的方式不同。',
	'SUCCESSFULLY_UPLOADED'			=> '上载成功',
	'SUCCESS_EXTENSION_GROUP_ADD'	=> '扩展名类别添加完成',
	'SUCCESS_EXTENSION_GROUP_EDIT'	=> '扩展名类别更新完成',

	'UPLOADING_FILES'				=> '上载文件中',
	'UPLOADING_FILE_TO'				=> '上载文件 “%1$s” 到帖子ID %2$d…',
	'UPLOAD_DENIED_FORUM'			=> '您没有上载文件到版面 “%s” 的权限',
	'UPLOAD_DIR'					=> '上载目录',
	'UPLOAD_DIR_EXPLAIN'			=> '附件的存储路径.',
	'UPLOAD_ICON'					=> '上载图标',
	'UPLOAD_NOT_DIR'				=> '您指定的上载地址不是一个目录。',
));
