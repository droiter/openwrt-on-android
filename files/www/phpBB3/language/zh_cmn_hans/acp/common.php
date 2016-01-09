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

// Common
$lang = array_merge($lang, array(
	'ACP_ADMINISTRATORS'		=> '管理员',
	'ACP_ADMIN_LOGS'			=> '管理日志',
	'ACP_ADMIN_ROLES'			=> '管理员角色',
	'ACP_ATTACHMENTS'			=> '附件',
	'ACP_ATTACHMENT_SETTINGS'	=> '附件设置',
	'ACP_AUTH_SETTINGS'			=> '认证设置',
	'ACP_AUTOMATION'			=> '自动操作',
	'ACP_AVATAR_SETTINGS'		=> '头像设定',

	'ACP_BACKUP'				=> '备份',
	'ACP_BAN'					=> '封禁',
	'ACP_BAN_EMAILS'			=> '封禁email地址',
	'ACP_BAN_IPS'				=> '封禁IP地址',
	'ACP_BAN_USERNAMES'			=> '封禁用户名',
	'ACP_BBCODES'				=> 'BBCode',
	'ACP_BOARD_CONFIGURATION'	=> '论坛配置',
	'ACP_BOARD_FEATURES'		=> '论坛功能',
	'ACP_BOARD_MANAGEMENT'		=> '论坛管理',
	'ACP_BOARD_SETTINGS'		=> '论坛设定',
	'ACP_BOTS'					=> '爬虫/机器人',

	'ACP_CAPTCHA'				=> '验证图片',

	'ACP_CAT_CUSTOMISE'			=> '自定',
	'ACP_CAT_DATABASE'			=> '数据库',
	'ACP_CAT_DOT_MODS'			=> '扩展',
	'ACP_CAT_FORUMS'			=> '版面',
	'ACP_CAT_GENERAL'			=> '综合',
	'ACP_CAT_MAINTENANCE'		=> '维护',
	'ACP_CAT_PERMISSIONS'		=> '权限',
	'ACP_CAT_POSTING'			=> '帖子',
	'ACP_CAT_STYLES'			=> '风格',
	'ACP_CAT_SYSTEM'			=> '系统',
	'ACP_CAT_USERGROUP'			=> '用户和组',
	'ACP_CAT_USERS'				=> '用户',
	'ACP_CLIENT_COMMUNICATION'	=> '客户端通信',
	'ACP_COOKIE_SETTINGS'		=> 'Cookie设定',
	'ACP_CONTACT'				=> '联系页',
	'ACP_CONTACT_SETTINGS'		=> '联系页设置',
	'ACP_CRITICAL_LOGS'			=> '错误日志',
	'ACP_CUSTOM_PROFILE_FIELDS'	=> '自定义资料栏目',

	'ACP_DATABASE'				=> '数据库管理',
	'ACP_DISALLOW'				=> '禁止',
	'ACP_DISALLOW_USERNAMES'	=> '禁止注册的用户名',

	'ACP_EMAIL_SETTINGS'		=> 'Email设定',
	'ACP_EXTENSION_GROUPS'		=> '管理扩展名组',
	'ACP_EXTENSION_MANAGEMENT'	=> '扩展管理',
	'ACP_EXTENSIONS'			=> '管理扩展',

	'ACP_FORUM_BASED_PERMISSIONS'	=> '基于版面的权限',
	'ACP_FORUM_LOGS'				=> '版面日志',
	'ACP_FORUM_MANAGEMENT'			=> '版面管理',
	'ACP_FORUM_MODERATORS'			=> '版面版主',
	'ACP_FORUM_PERMISSIONS'			=> '版面权限',
	'ACP_FORUM_PERMISSIONS_COPY'	=> '复制版面权限',
	'ACP_FORUM_ROLES'				=> '版面角色',

	'ACP_GENERAL_CONFIGURATION'		=> '综合设定',
	'ACP_GENERAL_TASKS'				=> '一般任务',
	'ACP_GLOBAL_MODERATORS'			=> '超级版主',
	'ACP_GLOBAL_PERMISSIONS'		=> '全局权限',
	'ACP_GROUPS'					=> '组',
	'ACP_GROUPS_FORUM_PERMISSIONS'	=> '组版面权限',
	'ACP_GROUPS_MANAGE'				=> '管理组',
	'ACP_GROUPS_MANAGEMENT'			=> '组管理',
	'ACP_GROUPS_PERMISSIONS'		=> '组权限',
	'ACP_GROUPS_POSITION'			=> '管理组位置',

	'ACP_ICONS'					=> '主题图标',
	'ACP_ICONS_SMILIES'			=> '主题图标/表情',
	'ACP_INACTIVE_USERS'		=> '冻结的用户',
	'ACP_INDEX'					=> '管理员控制面板首页',

	'ACP_JABBER_SETTINGS'		=> 'Jabber设置',

	'ACP_LANGUAGE'				=> '语言管理',
	'ACP_LANGUAGE_PACKS'		=> '语言包',
	'ACP_LOAD_SETTINGS'			=> '负载设置',
	'ACP_LOGGING'				=> '记录',

	'ACP_MAIN'					=> '管理员控制面板首页',

	'ACP_MANAGE_ATTACHMENTS'			=> '管理附件',
	'ACP_MANAGE_ATTACHMENTS_EXPLAIN'	=> '这里你可以列出和删除贴子和私信中的附件。',

	'ACP_MANAGE_EXTENSIONS'		=> '管理扩展名',
	'ACP_MANAGE_FORUMS'			=> '管理版面',
	'ACP_MANAGE_RANKS'			=> '管理等级',
	'ACP_MANAGE_REASONS'		=> '管理 举报/封禁 原因',
	'ACP_MANAGE_USERS'			=> '管理用户',
	'ACP_MASS_EMAIL'			=> '群发email',
	'ACP_MESSAGES'				=> '私人短信',
	'ACP_MESSAGE_SETTINGS'		=> '私人短信设定',
	'ACP_MODULE_MANAGEMENT'		=> '模块管理',
	'ACP_MOD_LOGS'				=> '版主日志',
	'ACP_MOD_ROLES'				=> '版主角色',

	'ACP_NO_ITEMS'				=> '列表为空。',

	'ACP_ORPHAN_ATTACHMENTS'	=> '幽灵文件',

	'ACP_PERMISSIONS'			=> '权限',
	'ACP_PERMISSION_MASKS'		=> '权限掩码',
	'ACP_PERMISSION_ROLES'		=> '权限角色',
	'ACP_PERMISSION_TRACE'		=> '权限跟踪',
	'ACP_PHP_INFO'				=> 'PHP 信息',
	'ACP_POST_SETTINGS'			=> '帖子设定',
	'ACP_PRUNE_FORUMS'			=> '裁减版面',
	'ACP_PRUNE_USERS'			=> '裁减用户',
	'ACP_PRUNING'				=> '裁减',

	'ACP_QUICK_ACCESS'			=> '快速访问',

	'ACP_RANKS'					=> '等级',
	'ACP_REASONS'				=> '举报/封禁 原因',
	'ACP_REGISTER_SETTINGS'		=> '用户注册设定',

	'ACP_RESTORE'				=> '恢复',
	
	'ACP_FEED'					=> 'Feed管理',
	'ACP_FEED_SETTINGS'			=> 'Feed设置',

	'ACP_SEARCH'				=> '搜索配置',
	'ACP_SEARCH_INDEX'			=> '搜索索引',
	'ACP_SEARCH_SETTINGS'		=> '搜索设置',

	'ACP_SECURITY_SETTINGS'		=> '安全设置',
	'ACP_SEND_STATISTICS'		=> '发送统计信息',
	'ACP_SERVER_CONFIGURATION'	=> '服务器配置',
	'ACP_SERVER_SETTINGS'		=> '服务器设置',
	'ACP_SIGNATURE_SETTINGS'	=> '签名设置',
	'ACP_SMILIES'				=> '表情',
	'ACP_STYLE_MANAGEMENT'		=> '风格管理',
	'ACP_STYLES'				=> '风格',
	'ACP_STYLES_CACHE'			=> '清除缓存',
	'ACP_STYLES_INSTALL'		=> '安装风格',
	
	'ACP_SUBMIT_CHANGES'		=> '提交更改',

	'ACP_TEMPLATES'				=> '模板',
	'ACP_THEMES'				=> '主题',

	'ACP_UPDATE'					=> '更新中',
	'ACP_USERS_FORUM_PERMISSIONS'	=> '用户版面权限',
	'ACP_USERS_LOGS'				=> '用户日志',
	'ACP_USERS_PERMISSIONS'			=> '用户权限',
	'ACP_USER_ATTACH'				=> '附件',
	'ACP_USER_AVATAR'				=> '头像',
	'ACP_USER_FEEDBACK'				=> '反馈',
	'ACP_USER_GROUPS'				=> '用户组',
	'ACP_USER_MANAGEMENT'			=> '用户管理',
	'ACP_USER_OVERVIEW'				=> '纵览',
	'ACP_USER_PERM'					=> '权限',
	'ACP_USER_PREFS'				=> '偏好设置',
	'ACP_USER_PROFILE'				=> '资料',
	'ACP_USER_RANK'					=> '等级',
	'ACP_USER_ROLES'				=> '用户角色',
	'ACP_USER_SECURITY'				=> '用户安全',
	'ACP_USER_SIG'					=> '签名档',
	'ACP_USER_WARNINGS'				=> '警告',

	'ACP_VC_SETTINGS'					=> '验证图片模块设置',
	'ACP_VC_CAPTCHA_DISPLAY'			=> 'CAPTCHA 图片预览',
	'ACP_VERSION_CHECK'					=> '检查更新',
	'ACP_VIEW_ADMIN_PERMISSIONS'		=> '查看管理员权限',
	'ACP_VIEW_FORUM_MOD_PERMISSIONS'	=> '查看版主权限',
	'ACP_VIEW_FORUM_PERMISSIONS'		=> '查看基于版面的权限',
	'ACP_VIEW_GLOBAL_MOD_PERMISSIONS'	=> '查看超级版主权限',
	'ACP_VIEW_USER_PERMISSIONS'			=> '查看基于用户的权限',

	'ACP_WORDS'					=> '敏感词过滤',

	'ACTION'				=> '操作',
	'ACTIONS'				=> '操作',
	'ACTIVATE'				=> '激活',
	'ADD'					=> '添加',
	'ADMIN'					=> '管理',
	'ADMIN_INDEX'			=> '管理首页',
	'ADMIN_PANEL'			=> '管理员控制面板',

	'ADM_LOGOUT'			=> '退出控制面板',
	'ADM_LOGGED_OUT'		=> '成功退出管理员控制面板',

	'BACK'					=> '后退',

	'COLOUR_SWATCH'			=> '网页安全色取色板',
	'CONFIG_UPDATED'		=> '配置更新完成。',
	'CRON_LOCK_ERROR'		=> '无法获得任务锁。',
	'CRON_NO_SUCH_TASK'		=> '无法找到计划任务 “%s”。',
	'CRON_NO_TASK'			=> '现在没有计划任务需要运行。',
	'CRON_NO_TASKS'			=> '没有计划任务能被找到。',

	'DEACTIVATE'				=> '冻结',
	'DIRECTORY_DOES_NOT_EXIST'	=> '输入的路径 "%s" 不存在。',
	'DIRECTORY_NOT_DIR'			=> '输入的路径 "%s" 不是目录。',
	'DIRECTORY_NOT_WRITABLE'	=> '输入的路径 “%s” 不可写。',
	'DISABLE'					=> '禁止',
	'DOWNLOAD'					=> '下载',
	'DOWNLOAD_AS'				=> '下载为',
	'DOWNLOAD_STORE'			=> '下载或存储文件',
	'DOWNLOAD_STORE_EXPLAIN'	=> '您可以直接下载文件或保存到您的 <samp>store/</samp> 目录。',
	'DOWNLOADS'					=> '下载',

	'EDIT'					=> '编辑',
	'ENABLE'				=> '启用',
	'EXPORT_DOWNLOAD'		=> '下载',
	'EXPORT_STORE'			=> '存储',

	'GENERAL_OPTIONS'		=> '综合选项',
	'GENERAL_SETTINGS'		=> '综合设定',
	'GLOBAL_MASK'			=> '全局权限掩码',

	'INSTALL'				=> '安装',
	'IP'					=> '用户 IP',
	'IP_HOSTNAME'			=> 'IP 地址或主机名',

	'LOAD_NOTIFICATIONS'			=> '显示通知',
	'LOAD_NOTIFICATIONS_EXPLAIN'	=> '显示通知列表在每个页面(通常在头部)。',
	'LOGGED_IN_AS'			=> '您登录的身份为:',
	'LOGIN_ADMIN'			=> '您必须是已授权的用户才能管理论坛。',
	'LOGIN_ADMIN_CONFIRM'	=> '您需要再次登录才能进入管理面板。',
	'LOGIN_ADMIN_SUCCESS'	=> '您已经成功登录，稍后将进入管理员控制面板',
	'LOOK_UP_FORUM'			=> '选择一个版面',
	'LOOK_UP_FORUMS_EXPLAIN'=> '您可以选择一个或多个版面。',

	'MANAGE'				=> '管理',
	'MENU_TOGGLE'			=> '隐藏或显示侧栏菜单',
	'MORE'					=> '更多',			// Not used at the moment
	'MORE_INFORMATION'		=> '更多信息 »',
	'MOVE_DOWN'				=> '下移',
	'MOVE_UP'				=> '上移',

	'NOTIFY'				=> '通知',
	'NO_ADMIN'				=> '您未被授权管理此论坛。',
	'NO_EMAILS_DEFINED'		=> '没有发现可用的email地址',
	'NO_FILES_TO_DELETE'	=> '你选的要删除的附件不存在。',
	'NO_PASSWORD_SUPPLIED'	=> '您需要输入您的密码后才能访问管理员控制面板。',	

	'OFF'					=> '关',
	'ON'					=> '开',

	'PARSE_BBCODE'						=> '解析 BBCode',
	'PARSE_SMILIES'						=> '解析表情图标',
	'PARSE_URLS'						=> '解析链接格式',
	'PERMISSIONS_TRANSFERRED'			=> '权限已经传递',
	'PERMISSIONS_TRANSFERRED_EXPLAIN'	=> '您现在使用来自于 %1$s 的权限。您可以以这个用户的权限浏览版面但是不能访问管理员控制面板因为管理员权限没有被传递。您可以随时 <a href="%2$s"><strong>恢复您的权限设置</strong></a>。',
	'PROCEED_TO_ACP'					=> '%s 进入管理员控制面板%s',

	'REMIND'							=> '提醒',
	'RESYNC'							=> '重新同步',

	'RUNNING_TASK'			=> '运行任务: %s。',
	'SELECT_ANONYMOUS'		=> '选择游客用户',
	'SELECT_OPTION'			=> '选择选项',

	'SETTING_TOO_LOW'		=> '对设置项 “%1$s” 输入的值太小。允许的最小值为 %2$d。',
	'SETTING_TOO_BIG'		=> '对设置项 “%1$s” 输入的值太大。允许的最大值为 %2$d。',	
	'SETTING_TOO_LONG'		=> '对设置项 “%1$s” 输入的长度太长。允许的最大长度为 %2$d。',
	'SETTING_TOO_SHORT'		=> '对设置项 “%1$s” 输入的长度太短。允许的最小长度为 %2$d。',
	
	'SHOW_ALL_OPERATIONS'	=> '显示所有操作',

	'TASKS_NOT_READY'			=> '未准备任务:',
	'TASKS_READY'			=> '准备任务:',
	'TOTAL_SIZE'			=> '总大小',

	'UCP'					=> '用户控制面板',
	'USERNAMES_EXPLAIN'		=> '在同一行上分开排列用户名',
	'USER_CONTROL_PANEL'	=> '用户控制面板',

	'WARNING'				=> '警告',
));

// PHP info
$lang = array_merge($lang, array(
	'ACP_PHP_INFO_EXPLAIN'	=> '这个页面列出了这个服务器上所安装的PHP的信息。这包括装载的模块，可用的变量和默认设定，在诊断问题时这些信息可能会有用。请注意一些主机服务商可能会因为安全因素禁止显示某些信息。您尽可能不要泄露这些信息，除非技术支持中的 <a href="https://www.phpbb.com/about/">官方开发成员</a> 需要这些信息。',

	'NO_PHPINFO_AVAILABLE'	=> '无法获取PHP信息，Phpinfo() 因为安全原因被禁用。',
));

// Logs
$lang = array_merge($lang, array(
	'ACP_ADMIN_LOGS_EXPLAIN'	=> '这里列出了论坛管理员执行的操作。您可以按用户名、日期、IP地址或操作排序。如果您有合适的权限，您也可以清除部分或全部日志记录。',
	'ACP_CRITICAL_LOGS_EXPLAIN'	=> '这里列出了论坛自身的操作。这些日志为您解决特定问题提供有用信息，例如无法投递的email。您可以按用户名、日期、IP地址或操作排序。如果您有足够的权限，您也可以清除部分或全部日志记录。',
	'ACP_MOD_LOGS_EXPLAIN'		=> '这里列出了论坛版主执行的操作，在下拉框中选择一个版面。您可以按用户名、日期、IP地址或操作排序。如果您有足够的权限，您也可以清除部分或全部日志记录。',
	'ACP_USERS_LOGS_EXPLAIN'	=> '这里列出了用户执行的操作以及作用于用户的操作。',
	'ALL_ENTRIES'				=> '所有条目',

	'DISPLAY_LOG'	=> '从头显示条目',

	'NO_ENTRIES'	=> '没有记录',

	'SORT_IP'		=> 'IP地址',
	'SORT_DATE'		=> '日期',
	'SORT_ACTION'	=> '操作',
));

// Index page
$lang = array_merge($lang, array(
	'ADMIN_INTRO'				=> '感谢您选择phpBB作为论坛解决方案。这个界面将显示您的论坛的总体统计信息。左边的链接允许您从各个方面管理论坛，每个分页有如何使用管理工具的提示。',
	'ADMIN_LOG'					=> '记录的管理员操作',
	'ADMIN_LOG_INDEX_EXPLAIN'	=> '这里显示了管理员进行的最后五个操作。完整的操作日志可以通过下面的链接查看。',
	'AVATAR_DIR_SIZE'			=> '头像目录大小',

	'BOARD_STARTED'		=> '论坛开张日期',
	'BOARD_VERSION'		=> '论坛版本',

	'DATABASE_SERVER_INFO'	=> '数据库服务器',
	'DATABASE_SIZE'			=> '数据库大小',

	// Enviroment configuration checks, mbstring related
	'ERROR_MBSTRING_FUNC_OVERLOAD'					=> '功能超载配置错误',
	'ERROR_MBSTRING_FUNC_OVERLOAD_EXPLAIN'			=> '<var>mbstring.func_overload</var>必须设置为0或4。您可以在<samp>PHP信息</samp>页检查当前值。',
	'ERROR_MBSTRING_ENCODING_TRANSLATION'			=> '透明字符编码配置错误',
	'ERROR_MBSTRING_ENCODING_TRANSLATION_EXPLAIN'	=> '<var>mbstring.encoding_translation</var>必须设置为0。您可以在<samp>PHP信息</samp>页检查当前值。',
	'ERROR_MBSTRING_HTTP_INPUT'						=> 'HTTP输入字符转换配置错误',
	'ERROR_MBSTRING_HTTP_INPUT_EXPLAIN'				=> '<var>mbstring.http_input</var>必须设置为<samp>通过</samp>。您可以在<samp>PHP信息</samp>页检查当前值。',
	'ERROR_MBSTRING_HTTP_OUTPUT'					=> 'HTTP输出字符转换配置错误',
	'ERROR_MBSTRING_HTTP_OUTPUT_EXPLAIN'			=> '<var>mbstring.http_output</var>必须设置为<samp>通过</samp>。您可以在<samp>PHP信息</samp>页检查当前值。',

	'FILES_PER_DAY'		=> '每天的附件量',
	'FORUM_STATS'		=> '论坛统计',

	'GZIP_COMPRESSION'	=> 'GZip压缩',

	'NO_SEARCH_INDEX'	=> '所选搜索后端没有一个索引。<br />请建立索引为 “%1$s” 在 %2$s搜索索引%3$s 部分。',
	'NOT_AVAILABLE'		=> '不可用',
	'NUMBER_FILES'		=> '附件数量',
	'NUMBER_POSTS'		=> '帖子数量',
	'NUMBER_TOPICS'		=> '主题数量',
	'NUMBER_USERS'		=> '用户数量',
	'NUMBER_ORPHAN'		=> '幽灵文件',

	'PHP_VERSION_OLD'	=> '当前服务器上的PHP版本太旧，新的phpBB将不再支持这个版本的PHP。%s详情%s',

	'POSTS_PER_DAY'		=> '每日帖数',

	'PURGE_CACHE'			=> '清除缓存',
	'PURGE_CACHE_CONFIRM'	=> '您确认要清除缓存吗?',
	'PURGE_CACHE_EXPLAIN'	=> '清除所有缓存相关的条目，这包含被缓存的模板和数据库查询结果。',
	'PURGE_CACHE_SUCCESS'	=> '缓存成功清除。',
	
	'PURGE_SESSIONS'			=> '清除所有会话',
	'PURGE_SESSIONS_CONFIRM'	=> '您确认要清除所有会话吗? 这会让所有已登录的用户登出论坛。',
	'PURGE_SESSIONS_EXPLAIN'	=> '清除所有会话。这会让所有已登录的用户登出论坛。',
	'PURGE_SESSIONS_SUCCESS'	=> '会话成功清除。',

	'RESET_DATE'			=> '重置日期',
	'RESET_DATE_CONFIRM'			=> '您确认要重置论坛的起始时间吗?',
	'RESET_DATE_SUCCESS'				=> '论坛的起始时间重置',
	'RESET_ONLINE'			=> '重置在线数',
	'RESET_ONLINE_CONFIRM'			=> '您确认要重置这个论坛的最大在线人数吗?',
	'RESET_ONLINE_SUCCESS'				=> '最大在线人数重置',
	'RESYNC_POSTCOUNTS'		=> '同步帖子统计',
	'RESYNC_POSTCOUNTS_EXPLAIN'		=> '只有存在的帖子才会被计入。已经裁减掉的帖子将不予统计。',
	'RESYNC_POSTCOUNTS_CONFIRM'		=> '您确认要重新同步帖子统计吗?',
	'RESYNC_POSTCOUNTS_SUCCESS'			=> '重新同步帖数',
	'RESYNC_POST_MARKING'	=> '同步主题数',
	'RESYNC_POST_MARKING_CONFIRM'	=> '您确认要重新统计带标记的主题吗?',
	'RESYNC_POST_MARKING_EXPLAIN'	=> '首先取消标记所有的主题，然后重新标记在六个月内有操作的主题。',
	'RESYNC_POST_MARKING_SUCCESS'	=> '重新同步标记主题',
	'RESYNC_STATS'			=> '同步统计值',
	'RESYNC_STATS_CONFIRM'			=> '您确认要重新同步统计吗?',
	'RESYNC_STATS_EXPLAIN'			=> '重新计算帖子/主题/用户和文件的总数。',
	'RESYNC_STATS_SUCCESS'			=> '重新同步统计',
	'RUN'							=> '现在执行',

	'STATISTIC'			=> '统计',
	'STATISTIC_RESYNC_OPTIONS'	=> '重新同步/重置统计',

	'TIMEZONE_INVALID'	=> '你选的时区无效。',
	'TIMEZONE_SELECTED'	=> '(当前所选)',
	'TOPICS_PER_DAY'	=> '每日主题',

	'UPLOAD_DIR_SIZE'	=> '发表的附件大小',
	'USERS_PER_DAY'		=> '每日新用户',

	'VALUE'					=> '值',
	'VERSIONCHECK_FAIL'			=> '无法获取最新版本信息。',
	'VERSIONCHECK_FORCE_UPDATE'	=> '再次检查版本',
	'VIEW_ADMIN_LOG'		=> '查看管理员日志',
	'VIEW_INACTIVE_USERS'	=> '查看冻结帐号',

	'WELCOME_PHPBB'			=> '欢迎来到phpBB',
	'WRITABLE_CONFIG'      => '您的论坛配置文件(config.php)处于可写状态。我们强烈建议您将此文件的权限设置为640或者至少为644(例如: <a href="http://en.wikipedia.org/wiki/Chmod" rel="external">chmod</a> 640 config.php)。',
));

// Inactive Users
$lang = array_merge($lang, array(
	'INACTIVE_DATE'					=> '冻结日期',
	'INACTIVE_REASON'				=> '原因',
	'INACTIVE_REASON_MANUAL'		=> '用户被管理员冻结',
	'INACTIVE_REASON_PROFILE'		=> '个人资料变更',
	'INACTIVE_REASON_REGISTER'		=> '新注册的帐号',
	'INACTIVE_REASON_REMIND'		=> '强制帐号激活',
	'INACTIVE_REASON_UNKNOWN'		=> '未知',
	'INACTIVE_USERS'				=> '冻结的用户',
	'INACTIVE_USERS_EXPLAIN'		=> '这是已经注册但是没有激活的用户列表。您可以删除或提醒 (发送email) 他们。',
	'INACTIVE_USERS_EXPLAIN_INDEX'	=> '这是最近10个已注册但未激活的用户列表，账户未激活可能是因为在用户注册设置里已启用账户激活但这些用户还没被激活，也可能因为这些账户已失效。点击下面的链接可得到一份完整的列表，如果您想的话可以激活、删除或提醒（通过 email）这些用户。',

	'NO_INACTIVE_USERS'	=> '没有冻结的用户',

	'SORT_INACTIVE'		=> '冻结日期',
	'SORT_LAST_VISIT'	=> '最后访问',
	'SORT_REASON'		=> '原因',
	'SORT_REG_DATE'		=> '注册日期',
	'SORT_LAST_REMINDER'=> '最后提醒',
	'SORT_REMINDER'		=> '提醒',

	'USER_IS_INACTIVE'		=> '用户被冻结',
));

// Send statistics page
$lang = array_merge($lang, array(
	'EXPLAIN_SEND_STATISTICS'	=> '请发送您的服务器和论坛设置信息至phpBB官方以便于进行统计分析。此信息不会包含任何与您或您的论坛的隐私数据。所有数据都是完全<strong>匿名</strong>的。我们会在今后的phpBB版本开发中参考收集到的信息。统计的结果会向公众公开，并提供给PHP语言项目的开发团队。',
	'EXPLAIN_SHOW_STATISTICS'	=> '使用下面的按钮您可以查看发送的内容。',
	'DONT_SEND_STATISTICS'		=> '返回管理员控制面板，如果您不希望发送统计信息给phpBB。',
	'GO_ACP_MAIN'				=> '前往管理员控制面板首页',
	'HIDE_STATISTICS'			=> '隐藏细节',
	'SEND_STATISTICS'			=> '发送统计信息',
	'SHOW_STATISTICS'			=> '显示详情',
	'THANKS_SEND_STATISTICS'	=> '感谢你提交信息。',
));

// Log Entries
$lang = array_merge($lang, array(
	'LOG_ACL_ADD_USER_GLOBAL_U_'		=> '<strong>添加或编辑用户的用户权限</strong><br />» %s',
	'LOG_ACL_ADD_GROUP_GLOBAL_U_'		=> '<strong>添加或编辑组的用户权限</strong><br />» %s',
	'LOG_ACL_ADD_USER_GLOBAL_M_'		=> '<strong>添加或编辑用户的超级版主权限</strong><br />» %s',
	'LOG_ACL_ADD_GROUP_GLOBAL_M_'		=> '<strong>添加或编辑组的超级版主权限</strong><br />» %s',
	'LOG_ACL_ADD_USER_GLOBAL_A_'		=> '<strong>添加或编辑用户的管理员权限</strong><br />» %s',
	'LOG_ACL_ADD_GROUP_GLOBAL_A_'		=> '<strong>添加或编辑组的管理员权限</strong><br />» %s',

	'LOG_ACL_ADD_ADMIN_GLOBAL_A_'		=> '<strong>添加或编辑管理员</strong><br />» %s',
	'LOG_ACL_ADD_MOD_GLOBAL_M_'			=> '<strong>添加或编辑超级版主</strong><br />» %s',

	'LOG_ACL_ADD_USER_LOCAL_F_'			=> '<strong>添加或编辑用户的版面访问</strong> 自 %1$s<br />» %2$s',
	'LOG_ACL_ADD_USER_LOCAL_M_'			=> '<strong>添加或编辑用户的版主访问</strong> 自 %1$s<br />» %2$s',
	'LOG_ACL_ADD_GROUP_LOCAL_F_'		=> '<strong>添加或编辑组的版面访问</strong> 自 %1$s<br />» %2$s',
	'LOG_ACL_ADD_GROUP_LOCAL_M_'		=> '<strong>添加或编辑组的版面版主访问</strong> 自 %1$s<br />» %2$s',

	'LOG_ACL_ADD_MOD_LOCAL_M_'			=> '<strong>添加或编辑版主</strong> 自 %1$s<br />» %2$s',
	'LOG_ACL_ADD_FORUM_LOCAL_F_'		=> '<strong>添加或编辑版面权限</strong> 自 %1$s<br />» %2$s',

	'LOG_ACL_DEL_ADMIN_GLOBAL_A_'		=> '<strong>删除管理员</strong><br />» %s',
	'LOG_ACL_DEL_MOD_GLOBAL_M_'			=> '<strong>删除超级版主</strong><br />» %s',
	'LOG_ACL_DEL_MOD_LOCAL_M_'			=> '<strong>删除版主</strong> 自 %1$s<br />» %2$s',
	'LOG_ACL_DEL_FORUM_LOCAL_F_'		=> '<strong>删除用户/组版面权限</strong> 自 %1$s<br />» %2$s',

	'LOG_ACL_TRANSFER_PERMISSIONS'		=> '<strong>权限传递自</strong><br />» %s',
	'LOG_ACL_RESTORE_PERMISSIONS'		=> '<strong>恢复自己的权限，在使用完毕权限来自</strong><br />» %s',

	'LOG_ADMIN_AUTH_FAIL'		=> '<strong>失败的管理员登录尝试</strong>',
	'LOG_ADMIN_AUTH_SUCCESS'	=> '<strong>管理员登录</strong>',

	'LOG_ATTACHMENTS_DELETED'	=> '<strong>已删除附件</strong><br />» %s',

	'LOG_ATTACH_EXT_ADD'		=> '<strong>添加或编辑附件扩展名</strong><br />» %s',
	'LOG_ATTACH_EXT_DEL'		=> '<strong>删除附件扩展名</strong><br />» %s',
	'LOG_ATTACH_EXT_UPDATE'		=> '<strong>更新附件扩展名</strong><br />» %s',
	'LOG_ATTACH_EXTGROUP_ADD'	=> '<strong>添加扩展名组</strong><br />» %s',
	'LOG_ATTACH_EXTGROUP_EDIT'	=> '<strong>编辑扩展名组</strong><br />» %s',
	'LOG_ATTACH_EXTGROUP_DEL'	=> '<strong>删除扩展名组</strong><br />» %s',
	'LOG_ATTACH_FILEUPLOAD'		=> '<strong>幽灵文件上载至帖子</strong><br />» ID %1$d - %2$s',
	'LOG_ATTACH_ORPHAN_DEL'		=> '<strong>幽灵文件删除</strong><br />» %s',

	'LOG_BAN_EXCLUDE_USER'	=> '<strong>封禁中解除用户</strong> 原因: "<em>%1$s</em>"<br />» %2$s ',
	'LOG_BAN_EXCLUDE_IP'	=> '<strong>封禁中解除IP</strong> 原因: "<em>%1$s</em>"<br />» %2$s ',
	'LOG_BAN_EXCLUDE_EMAIL' => '<strong>封禁中解除email</strong> 原因: "<em>%1$s</em>"<br />» %2$s ',
	'LOG_BAN_USER'			=> '<strong>封禁用户</strong> 原因: "<em>%1$s</em>"<br />» %2$s ',
	'LOG_BAN_IP'			=> '<strong>封禁IP</strong> 原因: "<em>%1$s</em>"<br />» %2$s',
	'LOG_BAN_EMAIL'			=> '<strong>封禁email</strong> 原因: "<em>%1$s</em>"<br />» %2$s',
	'LOG_UNBAN_USER'		=> '<strong>用户解禁</strong><br />» %s',
	'LOG_UNBAN_IP'			=> '<strong>IP解禁</strong><br />» %s',
	'LOG_UNBAN_EMAIL'		=> '<strong>email解禁</strong><br />» %s',

	'LOG_BBCODE_ADD'		=> '<strong>添加新BBCode</strong><br />» %s',
	'LOG_BBCODE_EDIT'		=> '<strong>编辑BBCode</strong><br />» %s',
	'LOG_BBCODE_DELETE'		=> '<strong>删除BBCode</strong><br />» %s',

	'LOG_BOT_ADDED'		=> '<strong>添加新机器人</strong><br />» %s',
	'LOG_BOT_DELETE'	=> '<strong>删除机器人</strong><br />» %s',
	'LOG_BOT_UPDATED'	=> '<strong>更新机器人</strong><br />» %s',

	'LOG_CLEAR_ADMIN'		=> '<strong>清空管理员日志</strong>',
	'LOG_CLEAR_CRITICAL'	=> '<strong>清空错误日志</strong>',
	'LOG_CLEAR_MOD'			=> '<strong>清空版主日志</strong>',
	'LOG_CLEAR_USER'		=> '<strong>清空用户日志</strong><br />» %s',
	'LOG_CLEAR_USERS'		=> '<strong>清空多个用户日志</strong>',

	'LOG_CONFIG_ATTACH'			=> '<strong>更改附件设定</strong>',
	'LOG_CONFIG_AUTH'			=> '<strong>更改身份验证设定</strong>',
	'LOG_CONFIG_AVATAR'			=> '<strong>更改头像设定</strong>',
	'LOG_CONFIG_COOKIE'			=> '<strong>更改cookie设定</strong>',
	'LOG_CONFIG_EMAIL'			=> '<strong>更改email设定</strong>',
	'LOG_CONFIG_FEATURES'		=> '<strong>更改论坛功能</strong>',
	'LOG_CONFIG_LOAD'			=> '<strong>更改负载设定</strong>',
	'LOG_CONFIG_MESSAGE'		=> '<strong>更改站内短信设定</strong>',
	'LOG_CONFIG_POST'			=> '<strong>更改帖子设定</strong>',
	'LOG_CONFIG_REGISTRATION'	=> '<strong>更改用户注册设定</strong>',
	'LOG_CONFIG_FEED'			=> '<strong>更改ATOM设定</strong>',
	'LOG_CONFIG_SEARCH'			=> '<strong>更改搜索设定</strong>',
	'LOG_CONFIG_SECURITY'		=> '<strong>更改安全设定</strong>',
	'LOG_CONFIG_SERVER'			=> '<strong>更改服务器设定</strong>',
	'LOG_CONFIG_SETTINGS'		=> '<strong>更改论坛设定</strong>',
	'LOG_CONFIG_SIGNATURE'		=> '<strong>更改签名档设定</strong>',
	'LOG_CONFIG_VISUAL'			=> '<strong>更改可视化确认设定</strong>',

	'LOG_APPROVE_TOPIC'			=> '<strong>批准主题</strong><br />» %s',
	'LOG_BUMP_TOPIC'			=> '<strong>用户推举主题</strong><br />» %s',
	'LOG_DELETE_POST'			=> '<strong>删除帖子 "%1$s" 由 “%2$s” 原因是</strong><br />» %3$s',
	'LOG_DELETE_SHADOW_TOPIC'   => '<strong>删除影子主题</strong><br />» %s',
	'LOG_DELETE_TOPIC'			=> '<strong>删除主题 "%1$s" 由 “%2$s” 原因是</strong><br />» %3$s',
	'LOG_FORK'					=> '<strong>复制主题</strong><br />» from %s',
	'LOG_LOCK'					=> '<strong>锁定主题</strong><br />» %s',
	'LOG_LOCK_POST'				=> '<strong>锁定帖子</strong><br />» %s',
	'LOG_MERGE'					=> '<strong>合并帖子</strong> 至主题<br />» %s',
	'LOG_MOVE'					=> '<strong>移动主题</strong><br />» 自 %1$s 至 %2$s',
	'LOG_MOVED_TOPIC'			=> '<strong>已移动主题</strong><br />» %s',
	'LOG_PM_REPORT_CLOSED'		=> '<strong>关闭短信举报</strong><br />» %s',
	'LOG_PM_REPORT_DELETED'		=> '<strong>删除短信举报</strong><br />» %s',
	'LOG_POST_APPROVED'			=> '<strong>审批帖子</strong><br />» %s',
	'LOG_POST_DISAPPROVED'		=> '<strong>驳回帖子 “%1$s” 由 “%3$s” 原因是</strong><br />» %2$s',
	'LOG_POST_EDITED'			=> '<strong>编辑帖子 “%1$s” 由 “%2$s” 原因是</strong><br />» %3$s',
	'LOG_POST_RESTORED'			=> '<strong>恢复帖子</strong><br />» %s',
	'LOG_REPORT_CLOSED'			=> '<strong>关闭举报</strong><br />» %s',
	'LOG_REPORT_DELETED'		=> '<strong>删除举报</strong><br />» %s',
	'LOG_RESTORE_TOPIC'			=> '<strong>恢复主题 “%1$s” 写于</strong><br />» %2$s',
	'LOG_SOFTDELETE_POST'		=> '<strong>删除帖子 “%1$s” 写于 “%2$s” 以下原因</strong><br />» %3$s',
	'LOG_SOFTDELETE_TOPIC'		=> '<strong>删除主题 “%1$s” 写于 “%2$s” 以下原因</strong><br />» %3$s',
	'LOG_SPLIT_DESTINATION'		=> '<strong>分割并移动主题</strong><br />» 至 %s',
	'LOG_SPLIT_SOURCE'			=> '<strong>风格帖子</strong><br />» 自 %s',

	'LOG_TOPIC_APPROVED'		=> '<strong>审批主题</strong><br />» %s',
	'LOG_TOPIC_RESTORED'		=> '<strong>恢复主题</strong><br />» %s',
	'LOG_TOPIC_DISAPPROVED'		=> '<strong>驳回主题 “%1$s” 由 “%3$s” 原因是</strong><br />%2$s',
	'LOG_TOPIC_RESYNC'			=> '<strong>重新同步主题统计</strong><br />» %s',
	'LOG_TOPIC_TYPE_CHANGED'	=> '<strong>更改主题类型</strong><br />» %s',
	'LOG_UNLOCK'				=> '<strong>主题解锁</strong><br />» %s',
	'LOG_UNLOCK_POST'			=> '<strong>帖子解锁</strong><br />» %s',

	'LOG_DISALLOW_ADD'		=> '<strong>添加禁止注册的用户名</strong><br />» %s',
	'LOG_DISALLOW_DELETE'	=> '<strong>删除禁止注册的用户名</strong>',

	'LOG_DB_BACKUP'			=> '<strong>数据库备份</strong>',
	'LOG_DB_DELETE'			=> '<strong>删除数据库备份</strong>',
	'LOG_DB_RESTORE'		=> '<strong>数据库恢复</strong>',

	'LOG_DOWNLOAD_EXCLUDE_IP'	=> '<strong>从允许下载列表中去除IP/主机名</strong><br />» %s',
	'LOG_DOWNLOAD_IP'			=> '<strong>添加IP/主机名到允许下载列表</strong><br />» %s',
	'LOG_DOWNLOAD_REMOVE_IP'	=> '<strong>从允许下载列表中删除IP/主机名</strong><br />» %s',

	'LOG_ERROR_JABBER'		=> '<strong>Jabber错误</strong><br />» %s',
	'LOG_ERROR_EMAIL'		=> '<strong>Email错误</strong><br />» %s',
	
	'LOG_FORUM_ADD'							=> '<strong>创建新版面</strong><br />» %s',
	'LOG_FORUM_COPIED_PERMISSIONS'			=> '<strong>复制版面权限</strong> 自 %1$s<br />» %2$s',
	'LOG_FORUM_DEL_FORUM'					=> '<strong>删除版面</strong><br />» %s',
	'LOG_FORUM_DEL_FORUMS'					=> '<strong>删除版面及其子版面</strong><br />» %s',
	'LOG_FORUM_DEL_MOVE_FORUMS'				=> '<strong>删除版面，移动子版面</strong> 到 %1$s<br />» %2$s',
	'LOG_FORUM_DEL_MOVE_POSTS'				=> '<strong>删除版面，移动内容 </strong> 到 %1$s<br />» %2$s',
	'LOG_FORUM_DEL_MOVE_POSTS_FORUMS'		=> '<strong>删除版面和子版面，移动内容</strong> 到 %1$s<br />» %2$s',
	'LOG_FORUM_DEL_MOVE_POSTS_MOVE_FORUMS'	=> '<strong>删除版面，移动内容</strong> 到 %1$s <strong>and subforums</strong> to %2$s<br />» %3$s',
	'LOG_FORUM_DEL_POSTS'					=> '<strong>删除版面和版面的内容</strong><br />» %s',
	'LOG_FORUM_DEL_POSTS_FORUMS'			=> '<strong>删除版面和版面的内容及其子版面</strong><br />» %s',
	'LOG_FORUM_DEL_POSTS_MOVE_FORUMS'		=> '<strong>删除版面和版面的内容，移动子版面</strong> 到 %1$s<br />» %2$s',
	'LOG_FORUM_EDIT'						=> '<strong>编辑版面细节</strong><br />» %s',
	'LOG_FORUM_MOVE_DOWN'					=> '<strong>移动版面</strong> %1$s <strong>往下</strong> %2$s',
	'LOG_FORUM_MOVE_UP'						=> '<strong>移动版面</strong> %1$s <strong>往上</strong> %2$s',
	'LOG_FORUM_SYNC'						=> '<strong>重新同步版面</strong><br />» %s',
	
	'LOG_GENERAL_ERROR'	=> '<strong>发生错误</strong>: %1$s <br />» %2$s',

	'LOG_GROUP_CREATED'		=> '<strong>创建新用户组</strong><br />» %s',
	'LOG_GROUP_DEFAULTS'	=> '<strong>组 "%1$s" 默认为成员</strong><br />» %2$s',
	'LOG_GROUP_DELETE'		=> '<strong>删除用户组</strong><br />» %s',
	'LOG_GROUP_DEMOTED'		=> '<strong>组管理员降级为一般成员</strong> %1$s<br />» %2$s',
	'LOG_GROUP_PROMOTED'	=> '<strong>成员升级为组管理员</strong> %1$s<br />» %2$s',
	'LOG_GROUP_REMOVE'		=> '<strong>成员删除自用户组</strong> %1$s<br />» %2$s',
	'LOG_GROUP_UPDATED'		=> '<strong>用户组细节修改</strong><br />» %s',
	'LOG_MODS_ADDED'		=> '<strong>添加新的组管理员至用户组</strong> %1$s<br />» %2$s',
	'LOG_USERS_ADDED'		=> '<strong>添加新的成员至用户组</strong> %1$s<br />» %2$s',
	'LOG_USERS_APPROVED'	=> '<strong>用户被批准进入用户组</strong> %1$s<br />» %2$s',
	'LOG_USERS_PENDING'		=> '<strong>用户申请加入 “%1$s” 并等待批准</strong><br />» %2$s',

	'LOG_IMAGE_GENERATION_ERROR'	=> '<strong>创建图片时出错</strong><br />» 错误位于 %1$s 行 %2$s: %3$s',

	'LOG_INACTIVE_ACTIVATE'	=> '<strong>激活未激活的帐号</strong><br />» %s',
	'LOG_INACTIVE_DELETE'	=> '<strong>删除未激活的帐号</strong><br />» %s',
	'LOG_INACTIVE_REMIND'	=> '<strong>发送提醒email给未激活的用户</strong><br />» %s',
	'LOG_INSTALL_CONVERTED'	=> '<strong>转换自 %1$s 至 phpBB %2$s</strong>',
	'LOG_INSTALL_INSTALLED'	=> '<strong>安装phpBB %s</strong>',

	'LOG_IP_BROWSER_FORWARDED_CHECK'	=> '<strong>对话IP/browser/X_FORWARDED_FOR 检查失败</strong><br />»用户IP "<em>%1$s</em>" checked against 对话IP "<em>%2$s</em>"，用户浏览器字符串 "<em>%3$s</em>" checked against 对话浏览器字符串 "<em>%4$s</em>" and 用户 X_FORWARDED_FOR string "<em>%5$s</em>" checked against 对话 X_FORWARDED_FOR string "<em>%6$s</em>"。',

	'LOG_JAB_CHANGED'			=> '<strong>Jabber帐号修改</strong>',
	'LOG_JAB_PASSCHG'			=> '<strong>Jabber密码修改</strong>',
	'LOG_JAB_REGISTER'			=> '<strong>Jabber帐号注册</strong>',
	'LOG_JAB_SETTINGS_CHANGED'	=> '<strong>Jabber设置修改</strong>',

	'LOG_LANGUAGE_PACK_DELETED'		=> '<strong>删除语言包</strong><br />» %s',
	'LOG_LANGUAGE_PACK_INSTALLED'	=> '<strong>安装语言包</strong><br />» %s',
	'LOG_LANGUAGE_PACK_UPDATED'		=> '<strong>更新语言包细节</strong><br />» %s',
	'LOG_LANGUAGE_FILE_REPLACED'	=> '<strong>替换语言文件</strong><br />» %s',
	'LOG_LANGUAGE_FILE_SUBMITTED'	=> '<strong>提交语言文件并放置于store文件夹</strong><br />» %s',

	'LOG_MASS_EMAIL'		=> '<strong>群发email</strong><br />» %s',

	'LOG_MCP_CHANGE_POSTER'	=> '<strong>更改主题作者 "%1$s"</strong><br />» 自 %2$s to %3$s',

	'LOG_MODULE_DISABLE'	=> '<strong>模块关闭</strong><br />» %s',
	'LOG_MODULE_ENABLE'		=> '<strong>模块启用</strong><br />» %s',
	'LOG_MODULE_MOVE_DOWN'	=> '<strong>模块下移</strong><br />» %1$s 在 %2$s 下面',
	'LOG_MODULE_MOVE_UP'	=> '<strong>模块上移</strong><br />» %1$s 在 %2$s 上面',
	'LOG_MODULE_REMOVED'	=> '<strong>删除模块</strong><br />» %s',
	'LOG_MODULE_ADD'		=> '<strong>添加模块</strong><br />» %s',
	'LOG_MODULE_EDIT'		=> '<strong>编辑模块</strong><br />» %s',

	'LOG_A_ROLE_ADD'		=> '<strong>管理员角色添加</strong><br />» %s',
	'LOG_A_ROLE_EDIT'		=> '<strong>管理员角色编辑</strong><br />» %s',
	'LOG_A_ROLE_REMOVED'	=> '<strong>管理员角色删除</strong><br />» %s',
	'LOG_F_ROLE_ADD'		=> '<strong>版面角色添加</strong><br />» %s',
	'LOG_F_ROLE_EDIT'		=> '<strong>版面角色编辑</strong><br />» %s',
	'LOG_F_ROLE_REMOVED'	=> '<strong>版面角色删除</strong><br />» %s',
	'LOG_M_ROLE_ADD'		=> '<strong>版主角色添加</strong><br />» %s',
	'LOG_M_ROLE_EDIT'		=> '<strong>版主角色编辑</strong><br />» %s',
	'LOG_M_ROLE_REMOVED'	=> '<strong>版主角色删除</strong><br />» %s',
	'LOG_U_ROLE_ADD'		=> '<strong>用户角色添加</strong><br />» %s',
	'LOG_U_ROLE_EDIT'		=> '<strong>用户角色编辑</strong><br />» %s',
	'LOG_U_ROLE_REMOVED'	=> '<strong>用户角色删除</strong><br />» %s',

	'LOG_PLUPLOAD_TIDY_FAILED'		=> '<strong>无法打开 %1$s 整理，检查权限。</strong><br />异常: %2$s<br />跟踪: %3$s',

	'LOG_PROFILE_FIELD_ACTIVATE'	=> '<strong>资料栏目激活</strong><br />» %s',
	'LOG_PROFILE_FIELD_CREATE'		=> '<strong>资料栏目添加</strong><br />» %s',
	'LOG_PROFILE_FIELD_DEACTIVATE'	=> '<strong>资料栏目冻结</strong><br />» %s',
	'LOG_PROFILE_FIELD_EDIT'		=> '<strong>资料栏目修改</strong><br />» %s',
	'LOG_PROFILE_FIELD_REMOVED'		=> '<strong>资料栏目删除</strong><br />» %s',

	'LOG_PRUNE'					=> '<strong>裁减版面</strong><br />» %s',
	'LOG_AUTO_PRUNE'			=> '<strong>自动裁减版面</strong><br />» %s',
	'LOG_PRUNE_SHADOW'		=> '<strong>自动裁剪影子主题</strong><br />» %s',
	'LOG_PRUNE_USER_DEAC'		=> '<strong>用户冻结</strong><br />» %s',
	'LOG_PRUNE_USER_DEL_DEL'	=> '<strong>裁减用户和删除帖子</strong><br />» %s',
	'LOG_PRUNE_USER_DEL_ANON'	=> '<strong>裁减用户和保留帖子</strong><br />» %s',

	'LOG_PURGE_CACHE'			=> '<strong>清除缓存</strong>',
	'LOG_PURGE_SESSIONS'		=> '<strong>清除会话</strong>',

	'LOG_RANK_ADDED'		=> '<strong>添加新等级</strong><br />» %s',
	'LOG_RANK_REMOVED'		=> '<strong>删除等级</strong><br />» %s',
	'LOG_RANK_UPDATED'		=> '<strong>更新等级</strong><br />» %s',

	'LOG_REASON_ADDED'		=> '<strong>添加举报/否决理由</strong><br />» %s',
	'LOG_REASON_REMOVED'	=> '<strong>删除举报/否决理由</strong><br />» %s',
	'LOG_REASON_UPDATED'	=> '<strong>更新举报/否决理由</strong><br />» %s',

	'LOG_REFERER_INVALID'		=> '<strong>转向源验证错误</strong><br />»转向自 “<em>%1$s</em>”。请求被拒绝，此对话已终止。',
	'LOG_RESET_DATE'			=> '<strong>论坛开始日期重置</strong>',
	'LOG_RESET_ONLINE'			=> '<strong>最大在线用户数量重置</strong>',
	'LOG_RESYNC_FILES_STATS'	=> '<strong>File statistics resynchronised</strong>',
	'LOG_RESYNC_POSTCOUNTS'		=> '<strong>用户帖子数重新统计</strong>',
	'LOG_RESYNC_POST_MARKING'	=> '<strong>带点的主题重新同步</strong>',
	'LOG_RESYNC_STATS'			=> '<strong>帖子，主题和用户统计重新同步</strong>',

	'LOG_SEARCH_INDEX_CREATED'	=> '<strong>创建搜索索引于</strong><br />» %s',
	'LOG_SEARCH_INDEX_REMOVED'	=> '<strong>删除搜索索引于</strong><br />» %s',
	'LOG_SPHINX_ERROR'			=> '<strong>Sphinx Error</strong><br />» %s',
	'LOG_STYLE_ADD'				=> '<strong>添加新风格</strong><br />» %s',
	'LOG_STYLE_DELETE'			=> '<strong>删除风格</strong><br />» %s',
	'LOG_STYLE_EDIT_DETAILS'	=> '<strong>编辑风格</strong><br />» %s',
	'LOG_STYLE_EXPORT'			=> '<strong>导出风格</strong><br />» %s',

	// @deprecated 3.1
	'LOG_TEMPLATE_ADD_DB'			=> '<strong>添加新模板组到数据库</strong><br />» %s',
	// @deprecated 3.1
	'LOG_TEMPLATE_ADD_FS'			=> '<strong>添加新模板组到文件系统</strong><br />» %s',
	'LOG_TEMPLATE_CACHE_CLEARED'	=> '<strong>删除模板组中被缓存的模板文件 <em>%1$s</em></strong><br />» %2$s',
	'LOG_TEMPLATE_DELETE'			=> '<strong>删除模板组</strong><br />» %s',
	'LOG_TEMPLATE_EDIT'				=> '<strong>编辑模板组 <em>%1$s</em></strong><br />» %2$s',
	'LOG_TEMPLATE_EDIT_DETAILS'		=> '<strong>编辑模板细节</strong><br />» %s',
	'LOG_TEMPLATE_EXPORT'			=> '<strong>导出模板组</strong><br />» %s',
	// @deprecated 3.1
	'LOG_TEMPLATE_REFRESHED'		=> '<strong>刷新模板组</strong><br />» %s',

	// @deprecated 3.1
	'LOG_THEME_ADD_DB'			=> '<strong>添加新主题至数据库</strong><br />» %s',
	// @deprecated 3.1
	'LOG_THEME_ADD_FS'			=> '<strong>添加新主题至文件系统</strong><br />» %s',
	'LOG_THEME_DELETE'			=> '<strong>风格主题删除</strong><br />» %s',
	'LOG_THEME_EDIT_DETAILS'	=> '<strong>编辑风格主题细节</strong><br />» %s',
	'LOG_THEME_EDIT'			=> '<strong>编辑风格主题<em>%1$s</em></strong>',
	'LOG_THEME_EDIT_FILE'		=> '<strong>编辑风格主题 <em>%1$s</em></strong><br />» Modified file <em>%2$s</em>',
	'LOG_THEME_EXPORT'			=> '<strong>导出风格主题</strong><br />» %s',
	// @deprecated 3.1
	'LOG_THEME_REFRESHED'		=> '<strong>刷新风格主题</strong><br />» %s',

	'LOG_UPDATE_DATABASE'	=> '<strong>升级数据库自版本 %1$s 至 %2$s</strong>',
	'LOG_UPDATE_PHPBB'		=> '<strong>升级 phpBB 自版本 %1$s 至 %2$s</strong>',

	'LOG_USER_ACTIVE'		=> '<strong>用户激活</strong><br />» %s',
	'LOG_USER_BAN_USER'		=> '<strong>通过用户管理封禁用户</strong> 原因: "<em>%1$s</em>"<br />» %2$s',
	'LOG_USER_BAN_IP'		=> '<strong>通过用户管理封禁IP</strong> 原因: "<em>%1$s</em>"<br />» %2$s',
	'LOG_USER_BAN_EMAIL'	=> '<strong>通过用户管理封禁email</strong> 原因: "<em>%1$s</em>"<br />» %2$s',
	'LOG_USER_DELETED'		=> '<strong>删除用户</strong><br />» %s',
	'LOG_USER_DEL_ATTACH'	=> '<strong>删除这个用户发表的所有附件</strong><br />» %s',
	'LOG_USER_DEL_AVATAR'	=> '<strong>删除用户头像</strong><br />» %s',
	'LOG_USER_DEL_OUTBOX'	=> '<strong>清空用户发件箱</strong><br />» %s',
	'LOG_USER_DEL_POSTS'	=> '<strong>删除这个用户的所有帖子</strong><br />» %s',
	'LOG_USER_DEL_SIG'		=> '<strong>删除用户签名档</strong><br />» %s',
	'LOG_USER_INACTIVE'		=> '<strong>冻结用户</strong><br />» %s',
	'LOG_USER_MOVE_POSTS'	=> '<strong>移动用户帖子</strong><br />» posts by "%1$s" to forum "%2$s"',
	'LOG_USER_NEW_PASSWORD'	=> '<strong>更改用户密码</strong><br />» %s',
	'LOG_USER_REACTIVATE'	=> '<strong>强制用户帐号重新激活</strong><br />» %s',
	'LOG_USER_REMOVED_NR'	=> '<strong>关闭新注册标记于用户</strong><br />» %s',
	
	'LOG_USER_UPDATE_EMAIL'	=> '<strong>用户 "%1$s" 更改 email</strong><br />» from "%2$s" to "%3$s"',
	'LOG_USER_UPDATE_NAME'	=> '<strong>用户名称更改</strong><br />» from "%1$s" to "%2$s"',
	'LOG_USER_USER_UPDATE'	=> '<strong>用户细节更新</strong><br />» %s',

	'LOG_USER_ACTIVE_USER'		=> '<strong>用户帐号激活</strong>',
	'LOG_USER_DEL_AVATAR_USER'	=> '<strong>用户头像删除</strong>',
	'LOG_USER_DEL_SIG_USER'		=> '<strong>用户签名档删除</strong>',
	'LOG_USER_FEEDBACK'			=> '<strong>添加用户反馈</strong><br />» %s',
	'LOG_USER_GENERAL'			=> '<strong>添加条目:</strong><br />» %s',
	'LOG_USER_INACTIVE_USER'	=> '<strong>用户帐号被冻结</strong>',
	'LOG_USER_LOCK'				=> '<strong>用户锁定自己的主题</strong><br />» %s',
	'LOG_USER_MOVE_POSTS_USER'	=> '<strong>移动所有帖子到版面 "%s"</strong>',
	'LOG_USER_REACTIVATE_USER'	=> '<strong>强制用户帐号重新激活</strong>',
	'LOG_USER_UNLOCK'			=> '<strong>用户解锁自己的主题</strong><br />» %s',
	'LOG_USER_WARNING'			=> '<strong>添加用户警告</strong><br />» %s',
	'LOG_USER_WARNING_BODY'		=> '<strong>给这个用户发去如下的警告</strong><br />» %s',

	'LOG_USER_GROUP_CHANGE'			=> '<strong>用户更改默认组</strong><br />» %s',
	'LOG_USER_GROUP_DEMOTE'			=> '<strong>用户从组管理员职务降级</strong><br />» %s',
	'LOG_USER_GROUP_JOIN'			=> '<strong>用户加入组</strong><br />» %s',
	'LOG_USER_GROUP_JOIN_PENDING'	=> '<strong>用户加入组并等待批准</strong><br />» %s',
	'LOG_USER_GROUP_RESIGN'			=> '<strong>用户解除组成员关系</strong><br />» %s',
	
	'LOG_WARNING_DELETED'		=> '<strong>删除用户警告</strong><br />» %s',
	'LOG_WARNINGS_DELETED'		=> array(
		1 => '<strong>删除用户警告</strong><br />» %1$s',
		2 => '<strong>删除 %2$d 用户警告</strong><br />» %1$s', // Example: '<strong>Deleted 2 user warnings</strong><br />» username'
	),
	'LOG_WARNINGS_DELETED_ALL'	=> '<strong>删除所有用户警告</strong><br />» %s',

	'LOG_WORD_ADD'			=> '<strong>添加敏感词</strong><br />» %s',
	'LOG_WORD_DELETE'		=> '<strong>删除敏感词</strong><br />» %s',
	'LOG_WORD_EDIT'			=> '<strong>编辑敏感词</strong><br />» %s',

	'LOG_EXT_ENABLE'	=> '<strong>扩展启用</strong><br />» %s',
	'LOG_EXT_DISABLE'	=> '<strong>扩展禁用</strong><br />» %s',
	'LOG_EXT_PURGE'		=> '<strong>扩展的数据删除</strong><br />» %s',
));
