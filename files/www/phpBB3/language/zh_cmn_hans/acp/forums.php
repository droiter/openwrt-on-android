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

// Forum Admin
$lang = array_merge($lang, array(
	'AUTO_PRUNE_DAYS'			=> '帖子自动裁减回复时间',
	'AUTO_PRUNE_DAYS_EXPLAIN'	=> '在最后一个帖子发表后多少天这个主题会被删除。',
	'AUTO_PRUNE_FREQ'			=> '自动裁减频率',
	'AUTO_PRUNE_FREQ_EXPLAIN'	=> '裁减操作之间的天数间隔。',
	'AUTO_PRUNE_VIEWED'			=> '帖子自动裁减点击时间',
	'AUTO_PRUNE_VIEWED_EXPLAIN'	=> '在帖子最后被查看后多少天这个主题会被删除。',
	'AUTO_PRUNE_SHADOW_FREQ'	=> '自动裁剪影子主题频率',
	'AUTO_PRUNE_SHADOW_DAYS'	=> '自动裁剪影子主题时期',
	'AUTO_PRUNE_SHADOW_DAYS_EXPLAIN'	=> '影子主题移除后天数。',
	'AUTO_PRUNE_SHADOW_FREQ_EXPLAIN'	=> '几天时间裁剪事件中。',

	'CONTINUE'						=> '继续',
	'COPY_PERMISSIONS'				=> '复制权限自',
	'COPY_PERMISSIONS_EXPLAIN'		=> '为了方便新版面的权限设置，您可以复制一个现有的版面权限至新版面。',
	'COPY_PERMISSIONS_ADD_EXPLAIN'	=> '一旦创建，这个版面将行使您选择的版面同样的权限。如果没有选中，新的版面在权限设置前将是不可见的。',
	'COPY_PERMISSIONS_EDIT_EXPLAIN'	=> '如果您选择了复制权限，版面将行使您选择的版面同样的权限。这将覆盖您原先设置的权限。如果没有选中，将保留原有权限。',
	'COPY_TO_ACL'					=> '另外，您也可以为这个版面 %s 设定新权限 %s 。',
	'CREATE_FORUM'					=> '创建新版面',

	'DECIDE_MOVE_DELETE_CONTENT'		=> '删除或移动到版面',
	'DECIDE_MOVE_DELETE_SUBFORUMS'		=> '删除子版面或移动到版面',
	'DEFAULT_STYLE'						=> '默认界面',
	'DELETE_ALL_POSTS'					=> '删除帖子',
	'DELETE_SUBFORUMS'					=> '删除子版面和帖子',
	'DISPLAY_ACTIVE_TOPICS'				=> '允许活跃帖子',
	'DISPLAY_ACTIVE_TOPICS_EXPLAIN'		=> '如果设置为是，在选中的子版面中活跃的帖子将会显示在这个分区下。',

	'EDIT_FORUM'					=> '编辑版面',
	'ENABLE_INDEXING'				=> '允许搜索索引',
	'ENABLE_INDEXING_EXPLAIN'		=> '如果设置为是, 这个版面的帖子将被记入搜索索引.',
	'ENABLE_POST_REVIEW'			=> '允许预览帖子',
	'ENABLE_POST_REVIEW_EXPLAIN'	=> '如果设置为是，当用户在编写帖子时有新的帖子发布，用户可以预览他们的帖子。这在交谈版面中应该禁用。',
	'ENABLE_QUICK_REPLY'			=> '启用快速回复',
	'ENABLE_QUICK_REPLY_EXPLAIN'	=> '启用后用户在这个版面可以通过快速回复来回复文章。如果全局设定中停用了快速回复，这里的选项将不起作用。',
	'ENABLE_RECENT'					=> '显示活跃帖子',
	'ENABLE_RECENT_EXPLAIN'			=> '如果设置为是，这个版面的话题将显示在活跃帖子列表中。',
	'ENABLE_TOPIC_ICONS'			=> '允许主题图标',

	'FORUM_ADMIN'						=> '版面管理',
	'FORUM_ADMIN_EXPLAIN'				=> '在phpBB3中一切都是基于版块。类只是特殊的版块。每个版块可以拥有无限的子版块并且您可以决定哪些能发帖哪些不能（例如是否像一个旧类）。这里您能添加、编辑、删除、锁定、解锁单独板块并适当设置一些额外控制。如果您的帖子和主题失去同步，您可以从新同步一下版面。<strong>要显示新创建的版块，您需要为它复制或设置适当的权限。</strong>',
	'FORUM_AUTO_PRUNE'					=> '开启自动裁减',
	'FORUM_AUTO_PRUNE_EXPLAIN'			=> '裁减版面的主题，在下面设置频率/时间参数。',
	'FORUM_CREATED'						=> '版面创建完成。',
	'FORUM_DATA_NEGATIVE'				=> '裁减参数不能为负。',
	'FORUM_DESC_TOO_LONG'				=> '版面描述太长，不能超过4000字符。',
	'FORUM_DELETE'						=> '删除版面',
	'FORUM_DELETE_EXPLAIN'				=> '下面的表单允许您删除一个版面。如果这个版面可以发表文章，您可以决定如何处置这个版面中的子版面和文章。',
	'FORUM_DELETED'						=> '版面删除完成。',
	'FORUM_DESC'						=> '描述',
	'FORUM_DESC_EXPLAIN'				=> '任何这里的文字都会原样显示。',
	'FORUM_EDIT_EXPLAIN'				=> '下面的表单允许您自定因这个版面。请注意版面管理和帖子数控制要通过每个用户或用户组的权限来控制。',
	'FORUM_IMAGE'						=> '版面图标',
	'FORUM_IMAGE_EXPLAIN'				=> '和版面关联的图标地址，使用论坛根目录的相对路径。',
	'FORUM_IMAGE_NO_EXIST'            => '指定的版面图标不存在',
	'FORUM_LINK_EXPLAIN'				=> '用户点击这个版面后前往的完整URL (包含协议头，例如 <samp>http://</samp>)。',
	'FORUM_LINK_TRACK'					=> '跟踪链接指向',
	'FORUM_LINK_TRACK_EXPLAIN'			=> '记录版面链接的点击次数。',
	'FORUM_NAME'						=> '版面名称',
	'FORUM_NAME_EMPTY'					=> '您必须为这个版面取个名字。',
	'FORUM_PARENT'						=> '父版面',
	'FORUM_PASSWORD'					=> '版面密码',
	'FORUM_PASSWORD_CONFIRM'			=> '确认版面密码',
	'FORUM_PASSWORD_CONFIRM_EXPLAIN'	=> '您输入了版面密码才需要在这里再次输入。',
	'FORUM_PASSWORD_EXPLAIN'			=> '设置这个版面的访问密码, 或者使用权限系统.',
	'FORUM_PASSWORD_UNSET'				=> '删除版面密码',
	'FORUM_PASSWORD_UNSET_EXPLAIN'		=> '如果您希望删除版面密码请勾选此处。',
	'FORUM_PASSWORD_OLD'				=> '当前版面密码使用的是旧的加密方式，需要立即更改。',
	'FORUM_PASSWORD_MISMATCH'			=> '您输入的密码不匹配。',
	'FORUM_PRUNE_SETTINGS'				=> '版面裁减设定',
	'FORUM_PRUNE_SHADOW'				=> '启用自动裁剪影子主题',
	'FORUM_PRUNE_SHADOW_EXPLAIN'			=> '裁剪影子主题，设置下面的频率/时期参数。',
	'FORUM_RESYNCED'					=> '版面 “%s” 同步完成',
	'FORUM_RULES_EXPLAIN'				=> '版面规则将在版面的任何页面显示。',
	'FORUM_RULES_LINK'					=> '版面规则链接',
	'FORUM_RULES_LINK_EXPLAIN'			=> '您可以在这里输入包含版面规则的链接。这个设定将覆盖版面文字规则。',
	'FORUM_RULES_PREVIEW'				=> '版面规则预览',
	'FORUM_RULES_TOO_LONG'				=> '版面规则不能超过4000个字符。',
	'FORUM_SETTINGS'					=> '版面设定',
	'FORUM_STATUS'						=> '版面状态',
	'FORUM_STYLE'						=> '版面风格',
	'FORUM_TOPICS_PAGE'					=> '每页主题数',
	'FORUM_TOPICS_PAGE_EXPLAIN'			=> '如果非0，这个数值将覆盖默认的每页主题数。',
	'FORUM_TYPE'						=> '版面类型',
	'FORUM_UPDATED'						=> '版面信息更新完成。',

	'FORUM_WITH_SUBFORUMS_NOT_TO_LINK'		=> '您正试图讲一个拥有子版面的普通版面改变为链接。请在继续之前将全部子版面移出，因为在您将它变为链接之后将无法看到它的子版面。',

	'GENERAL_FORUM_SETTINGS'	=> '版面综合设定',

	'LINK'						=> '链接',
	'LIST_INDEX'				=> '在子版面列表中显示',
	'LIST_INDEX_EXPLAIN'		=> '在父版面的子版面列表中显示一个指向本版面的链接。',
	'LIST_SUBFORUMS'			=> '在图例中显示子版面',
	'LIST_SUBFORUMS_EXPLAIN'	=> '如果启用此选项，就会在首页或其他页面的图例中显示子版面的链接。',
	'LOCKED'				=> '锁定',

	'MOVE_POSTS_NO_POSTABLE_FORUM'	=> '您选中移动的目标版面不可发表文章。请选择其他的版面。',
	'MOVE_POSTS_TO'		=> '移动帖子到',
	'MOVE_SUBFORUMS_TO'	=> '移动子版面到',

	'NO_DESTINATION_FORUM'			=> '您没有指定移动操作的目标版面',
	'NO_FORUM_ACTION'				=> '没有在版面处理中指定操作',
	'NO_PARENT'						=> '没有父版面',
	'NO_PERMISSIONS'				=> '不复制权限',
	'NO_PERMISSION_FORUM_ADD'		=> '您没有添加版面的权限。',
	'NO_PERMISSION_FORUM_DELETE'	=> '您没有删除版面的权限。',

	'PARENT_IS_LINK_FORUM'		=> '您指定的版面是一个版面链接。版面链接不能包含其他的版面，请指定其他分区或版面作为父版面。',
	'PARENT_NOT_EXIST'			=> '父版面不存在。',
	'PRUNE_ANNOUNCEMENTS'		=> '裁减公告',
	'PRUNE_STICKY'				=> '裁减置顶',
	'PRUNE_OLD_POLLS'			=> '裁减旧投票',
	'PRUNE_OLD_POLLS_EXPLAIN'	=> '移除在帖子的活跃天数内没有新投票的投票。',

	'REDIRECT_ACL'	=> '下一步您可以对这个论坛 %s设置权限%s。',

	'SYNC_IN_PROGRESS'			=> '正在同步版面',
	'SYNC_IN_PROGRESS_EXPLAIN'	=> '正在同步主题范围 %1$d/%2$d。',

	'TYPE_CAT'			=> '分区',
	'TYPE_FORUM'		=> '版面',
	'TYPE_LINK'			=> '链接',

	'UNLOCKED'			=> '未锁定',
));
