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
	'ACP_GROUPS_MANAGE_EXPLAIN'		=> '在这个面板您可以管理所有的用户组。您可以删除、创建和编辑现有的用户组。此外，您还可以选择组长，切换组状态为开放、隐藏或关闭，以及设置组名与描述。',
	'ADD_GROUP_CATEGORY'			=> '添加类别',
	'ADD_USERS'						=> '添加用户',
	'ADD_USERS_EXPLAIN'				=> '这里您可以添加新用户到用户组。您可以选择是否使用这个用户组作为用户的默认用户组。并且您可以设置用户组领导。如果有多个用户请在每一行输入用户名。',

	'COPY_PERMISSIONS'				=> '复制权限自',
	'COPY_PERMISSIONS_EXPLAIN'		=> '一旦创建，用户组将拥有与您选择的组同样的权限。',
	'CREATE_GROUP'					=> '创建新组',

	'GROUPS_NO_MEMBERS'				=> '这个组没有成员',
	'GROUPS_NO_MODS'				=> '没有设置组领导',

	'GROUP_APPROVE'					=> '批准成员',
	'GROUP_APPROVED'				=> '已批准的成员',
	'GROUP_AVATAR'					=> '组头像',
	'GROUP_AVATAR_EXPLAIN'			=> '这个图片将显示在组的控制面板。',
	'GROUP_CATEGORY_NAME'			=> '类别名',
	'GROUP_CLOSED'					=> '关闭',
	'GROUP_COLOR'					=> '组颜色',
	'GROUP_COLOR_EXPLAIN'			=> '设置组成员名字显示的颜色，如果使用用户默认请留空。',
	'GROUP_CONFIRM_ADD_USERS'		=> array(
		1	=> '你确定要添加用户 %2$s 到组吗？',
		2	=> '你确定要添加用户 %2$s 到组吗？',
	),
	'GROUP_CREATED'					=> '用户组创建完成。',
	'GROUP_DEFAULT'					=> '设置为成员的默认组',
	'GROUP_DEFS_UPDATED'			=> '设置为选中成员的默认组。',
	'GROUP_DELETE'					=> '从组中删除成员',
	'GROUP_DELETED'					=> '组已经删除并且用户默认组设置完成。',
	'GROUP_DEMOTE'					=> '组领导降级',
	'GROUP_DESC'					=> '用户组描述',
	'GROUP_DETAILS'					=> '用户组细节',
	'GROUP_EDIT_EXPLAIN'			=> '这里您可以编辑存在的用户组。更改组名称，描述和类型 (开放，关闭等)。您还可以设置一些用户组范围的选项例如颜色，等级。更改将覆盖用户的当前设置。请注意组成员可以更改他们的头像，除非您设置了相关的权限。',
	'GROUP_ERR_USERS_EXIST'			=> '指定的用户已经是这个用户组的成员',
	'GROUP_FOUNDER_MANAGE'			=> '仅由创始人管理',
	'GROUP_FOUNDER_MANAGE_EXPLAIN'	=> '只允许创始人管理此用户组。拥有组权限的用户与此用户组的成员一样可以看到这个组。',
	'GROUP_HIDDEN'					=> '隐藏',
	'GROUP_LANG'					=> '组语言',
	'GROUP_LEAD'					=> '组领导',
	'GROUP_LEADERS_ADDED'			=> '新领导添加成功。',
	'GROUP_LEGEND'					=> '在团队中显示',
	'GROUP_LIST'					=> '当前成员',
	'GROUP_LIST_EXPLAIN'			=> '用户组的成员完整列表。您可以删除成员 (除了一些特殊用户组) 或添加合适的新成员。',
	'GROUP_MEMBERS'					=> '用户组成员',
	'GROUP_MEMBERS_EXPLAIN'			=> '用户组的所有成员完整列表。包含了组领导，等待批准的成员和正式成员。您可以管理这个组中的成员。如果需要删除组领导身份但是保留在组中，请使用组领导降级而不是删除，同样的可以选择一个正式成员提升为组领导。',
	'GROUP_MESSAGE_LIMIT'			=> '用户组私人短信数量限制',
	'GROUP_MESSAGE_LIMIT_EXPLAIN'	=> '这个设置会覆盖用户的短信限制。如果设成0将使用用户的默认设置。',
	'GROUP_MODS_ADDED'				=> '新组管理员添加完成。',
	'GROUP_MODS_DEMOTED'			=> '组领导降级完成。',
	'GROUP_MODS_PROMOTED'			=> '组成员提升完成。',
	'GROUP_NAME'					=> '组名称',
	'GROUP_NAME_TAKEN'				=> '您输入的用户组名称已经被使用，请使用其他的组名称。',
	'GROUP_OPEN'					=> '打开',
	'GROUP_PENDING'					=> '待批准的成员',
	'GROUP_MAX_RECIPIENTS'         => '私人短信收件人数量上限',
	'GROUP_MAX_RECIPIENTS_EXPLAIN'   => '单个私人短信允许发送的收件人数量上限。设置为0则使用全局设置。',
	'GROUP_OPTIONS_SAVE'			=> '组范围选项',
	'GROUP_PROMOTE'					=> '升级为组领导',
	'GROUP_RANK'					=> '组级别',
	'GROUP_RECEIVE_PM'				=> '用户组可以接收私人短信',
	'GROUP_RECEIVE_PM_EXPLAIN'		=> '请注意隐藏的用户组不可以接收短信，这里的设置不起作用。',
	'GROUP_REQUEST'					=> '请求',
	'GROUP_SETTINGS_SAVE'			=> '用户组范围设置',
	'GROUP_SKIP_AUTH'				=> '权限中略过组领导',
	'GROUP_SKIP_AUTH_EXPLAIN'		=> '启用后组领导不再继承当前组的权限。',
	'GROUP_SPECIAL'					=> '预定义',
	'GROUP_TEAMPAGE'				=> '显示组在团队页',
	'GROUP_TYPE'					=> '组类型',
	'GROUP_TYPE_EXPLAIN'			=> '这决定了哪些用户可以加入或查看这个用户组。',
	'GROUP_UPDATED'					=> '用户组参数更新完成。',

	'GROUP_USERS_ADDED'				=> '新成员添加完成。',
	'GROUP_USERS_EXIST'				=> '选中的用户已经是成员了。',
	'GROUP_USERS_REMOVE'			=> '用户已经删除，新的默认用户组设置完成。',

	'LEGEND_EXPLAIN'				=> '显示在组图例里的组:',
	'LEGEND_SETTINGS'				=> '图例设置',
	'LEGEND_SORT_GROUPNAME'			=> '排列图例通过组名',
	'LEGEND_SORT_GROUPNAME_EXPLAIN'	=> '此选项启用时下面的排序忽略。',

	'MANAGE_LEGEND'			=> '管理组图例',
	'MANAGE_TEAMPAGE'		=> '管理团队页',
	'MAKE_DEFAULT_FOR_ALL'	=> '为所有成员设置默认用户组',
	'MEMBERS'				=> '成员',

	'NO_GROUP'					=> '没有指定用户组。',
	'NO_GROUPS_ADDED'			=> '还没有添加组。',
	'NO_GROUPS_CREATED'			=> '还没有创建用户组。',
	'NO_PERMISSIONS'			=> '不要复制权限',
	'NO_USERS'					=> '您没有输入任何用户。',
	'NO_USERS_ADDED'			=> '这个组还未添加成员。',
	'NO_VALID_USERS'			=> '您还没有输入符合此操作条件的用户。',

	'SELECT_GROUP'				=> '选择一个组',
	'SPECIAL_GROUPS'			=> '预设的用户组',
	'SPECIAL_GROUPS_EXPLAIN'	=> '预设的用户组是特殊用户组，它们不能删除和直接更改。但是可以增删用户和更改基本设置。点击 “默认” 您可以设置它为其成员的默认组。',

	'TEAMPAGE'					=> '团队页',
	'TEAMPAGE_DISP_ALL'			=> '所有成员',
	'TEAMPAGE_DISP_DEFAULT'		=> '只有用户的默认组',
	'TEAMPAGE_DISP_FIRST'		=> '只有第一个成员',
	'TEAMPAGE_EXPLAIN'			=> '显示在团队页的组:',
	'TEAMPAGE_FORUMS'			=> '显示适当的论坛',
	'TEAMPAGE_FORUMS_EXPLAIN'	=> '如果设为是，版主将有一个列表，含有版主权限的论坛。对于大型论坛这将密集读写数据库。',
	'TEAMPAGE_MEMBERSHIPS'		=> '显示用户成员',
	'TEAMPAGE_SETTINGS'			=> '团队页设置',
	'TOTAL_MEMBERS'				=> '成员',

	'USERS_APPROVED'				=> '用户已经批准。',
	'USER_DEFAULT'					=> '用户默认',
	'USER_DEF_GROUPS'				=> '用户设定的用户组',
	'USER_DEF_GROUPS_EXPLAIN'		=> '这些用户组由您或者其他管理员创建。您可以管理成员和编辑其属性或者删除它。点击 “默认” 可以设置这个组为其成员的默认用户组。',
	'USER_GROUP_DEFAULT'			=> '设置为默认用户组',
	'USER_GROUP_DEFAULT_EXPLAIN'	=> '选择是，将设置为添加的用户的默认组',
	'USER_GROUP_LEADER'				=> '设置为组领导',
));
