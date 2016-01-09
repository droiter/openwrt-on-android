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
	'ADMIN_SIG_PREVIEW'		=> '签名档预览',
	'AT_LEAST_ONE_FOUNDER'	=> '您不能将创始人设置为普通用户。这个论坛至少需要一名创始人。如果您需要改变这个用户的等级，请先提升另一位用户为创始人。',

	'BAN_ALREADY_ENTERED'	=> '封禁在更早的时候已经输入完成，封禁列表没有更新。',
	'BAN_SUCCESSFUL'		=> '封禁输入完成。',

	'CANNOT_BAN_ANONYMOUS'         => '能不能封禁游客帐号。游客权限可以在权限控制界面修改。',
	'CANNOT_BAN_FOUNDER'			=> '您不能封禁创始人帐号。',
	'CANNOT_BAN_YOURSELF'			=> '您不能封禁自己的帐号。',
	'CANNOT_DEACTIVATE_BOT'			=> '您不能冻结机器人帐号，如果需要停用, 请到搜索爬虫管理页面。',
	'CANNOT_DEACTIVATE_FOUNDER'		=> '您不能冻结创始人帐号。',
	'CANNOT_DEACTIVATE_YOURSELF'	=> '您不能冻结自己的帐号。',
	'CANNOT_FORCE_REACT_BOT'		=> '您不能强制激活机器人帐号，如果需要启用，请到搜索爬虫管理页面。',
	'CANNOT_FORCE_REACT_FOUNDER'	=> '您不能强制激活创始人帐号。',
	'CANNOT_FORCE_REACT_YOURSELF'	=> '您不能强制激活自己的帐号。',
	'CANNOT_REMOVE_ANONYMOUS'		=> '您不能删除游客帐号。',
	'CANNOT_REMOVE_FOUNDER'			=> '你不能删除创始人账户。',
	'CANNOT_REMOVE_YOURSELF'		=> '您不能删除自己的帐号。',
	'CANNOT_SET_FOUNDER_IGNORED'	=> '您不能提升被忽略的用户为创始人。',
	'CANNOT_SET_FOUNDER_INACTIVE'	=> '您需要先激活用户才能提升他们为创始人，只有活动的用户才能被提升。',
	'CONFIRM_EMAIL_EXPLAIN'			=> '您只有在改变用户email地址的时候才需要指定这个。',

	'DELETE_POSTS'			=> '删除帖子',
	'DELETE_USER'			=> '删除用户',
	'DELETE_USER_EXPLAIN'	=> '请注意删除的用户不可恢复,此用户发送的私信即使未被读取也将被删除，接受者永远也看不到了。',

	'FORCE_REACTIVATION_SUCCESS'	=> '强制激活完成。',
	'FOUNDER'						=> '创始人',
	'FOUNDER_EXPLAIN'				=> '创始人拥有管理员的所有权限，并且不能被封禁、删除和更改',

	'GROUP_APPROVE'					=> '成员批准',
	'GROUP_DEFAULT'					=> '设置为成员默认组',
	'GROUP_DELETE'					=> '删除组员',
	'GROUP_DEMOTE'					=> '组管理员降职',
	'GROUP_PROMOTE'					=> '提升为组管理员',

	'IP_WHOIS_FOR'			=> 'IP whois for %s',

	'LAST_ACTIVE'			=> '最后活动',

	'MOVE_POSTS_EXPLAIN'	=> '请问您要移动这个用户的所有帖子到哪个版面',

	'NO_SPECIAL_RANK'		=> '没有指派特殊等级',
	'NO_WARNINGS'			=> '没有警告.',
	'NOT_MANAGE_FOUNDER'	=> '您在尝试使用创始人特权管理用户。只有创始人才可以管理其他创始人用户。',

	'QUICK_TOOLS'			=> '快速工具',

	'REGISTERED'			=> '已注册',
	'REGISTERED_IP'			=> '注册自 IP',
	'RETAIN_POSTS'			=> '保留帖子',

	'SELECT_FORM'			=> '选择表单',
	'SELECT_USER'			=> '选择用户',

	'USER_ADMIN'					=> '用户管理',
	'USER_ADMIN_ACTIVATE'			=> '激活帐号',
	'USER_ADMIN_ACTIVATED'			=> '用户激活完成.',
	'USER_ADMIN_AVATAR_REMOVED'		=> '删除用户头像完成.',
	'USER_ADMIN_BAN_EMAIL'			=> '因email被封禁',
	'USER_ADMIN_BAN_EMAIL_REASON'	=> '在用户管理中封禁了的email地址',
	'USER_ADMIN_BAN_IP'				=> '因IP被封禁',
	'USER_ADMIN_BAN_IP_REASON'		=> '在用户管理中封禁了的IP地址',
	'USER_ADMIN_BAN_NAME_REASON'	=> '在用户管理中封禁了的用户名',
	'USER_ADMIN_BAN_USER'			=> '因用户名被封禁',
	'USER_ADMIN_DEACTIVATE'			=> '冻结的帐号',
	'USER_ADMIN_DEACTIVED'			=> '帐号冻结完成.',
	'USER_ADMIN_DEL_ATTACH'			=> '删除所有附件',
	'USER_ADMIN_DEL_AVATAR'			=> '删除头像',
	'USER_ADMIN_DEL_OUTBOX'			=> '清空发件箱',
	'USER_ADMIN_DEL_POSTS'			=> '删除所有帖子',
	'USER_ADMIN_DEL_SIG'			=> '删除签名档',
	'USER_ADMIN_EXPLAIN'			=> '这里您可以改变您的用户信息和一些特定选项。',
	'USER_ADMIN_FORCE'				=> '强制激活',
	'USER_ADMIN_LEAVE_NR'			=> '从新注册用户中移除',
	'USER_ADMIN_MOVE_POSTS'			=> '移动所有帖子',
	'USER_ADMIN_SIG_REMOVED'		=> '删除用户签名档完成。',
	'USER_ATTACHMENTS_REMOVED'		=> '用户名下的附件都已经删除。',
	'USER_AVATAR_NOT_ALLOWED'		=> '无法显示头像, 因为头像功能已经停用。',
	'USER_AVATAR_UPDATED'			=> '用户头像信息更新完成。',
	'USER_AVATAR_TYPE_NOT_ALLOWED'	=> '当前头像无法显示, 因为此类型头像已被停用。',
	'USER_CUSTOM_PROFILE_FIELDS'	=> '自定义个人资料',
	'USER_DELETED'					=> '用户已经删除。',
	'USER_GROUP_ADD'				=> '添加用户到组',
	'USER_GROUP_NORMAL'				=> '普通组员为成员自',
	'USER_GROUP_PENDING'			=> '组用户为等待状态',
	'USER_GROUP_SPECIAL'			=> '特殊组员为成员自', 
	'USER_LIFTED_NR'				=> '成功关闭此用户的新用户状态。',
	'USER_NO_ATTACHMENTS'			=> '没有可显示的附件。',
	'USER_NO_POSTS_TO_DELETE'			=> '用户没有要保留或删除的帖子。',
	'USER_OUTBOX_EMPTIED'			=> '用户的私人短信发件箱已清空。',
	'USER_OUTBOX_EMPTY'				=> '用户的私人短信发件箱是空的。',
	'USER_OVERVIEW_UPDATED'			=> '用户资料更新完成。',
	'USER_POSTS_DELETED'			=> '用户名下的所有帖子都已经删除。',
	'USER_POSTS_MOVED'				=> '已经移动用户的帖子到目标论坛。',
	'USER_PREFS_UPDATED'			=> '用户设定已更新。',
	'USER_PROFILE'					=> '用户资料',
	'USER_PROFILE_UPDATED'			=> '用户资料已更新。',
	'USER_RANK'						=> '用户等级',
	'USER_RANK_UPDATED'				=> '用户等级已更新。',
	'USER_SIG_UPDATED'				=> '用户签名档已更新。',
	'USER_WARNING_LOG_DELETED'		=> '无可用信息. 可能日志记录已经被删除。',
	'USER_TOOLS'					=> '基本工具',
));
