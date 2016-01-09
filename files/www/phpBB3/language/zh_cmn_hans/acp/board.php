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

// Board Settings
$lang = array_merge($lang, array(
	'ACP_BOARD_SETTINGS_EXPLAIN'	=> '这里您可以进行论坛的基本操作.',
	'BOARD_INDEX_TEXT'				=> '论坛首页名称',
	'BOARD_INDEX_TEXT_EXPLAIN'		=> '此文本是显示在论坛首页导航。如果不指定，将默认到“论坛首页”。',
	'BOARD_STYLE'					=> '论坛风格',
	'CUSTOM_DATEFORMAT'				=> '自定义…',
	'DEFAULT_DATE_FORMAT'			=> '日期格式',
	'DEFAULT_DATE_FORMAT_EXPLAIN'	=> '日期格式和PHP定义相同 <code>date</code> 函数。',
	'DEFAULT_LANGUAGE'				=> '默认语言',
	'DEFAULT_STYLE'					=> '默认风格',
	'DEFAULT_STYLE_EXPLAIN'			=> '默认设为新用户。',
	'DISABLE_BOARD'					=> '关闭论坛',
	'DISABLE_BOARD_EXPLAIN'			=> '这将让用户无法使用论坛。 您可以输入一段简单的文字描述原因(255个字符以内)。',
	'DISPLAY_LAST_SUBJECT'			=> '显示上次添加到版面列表里的帖子标题',
	'DISPLAY_LAST_SUBJECT_EXPLAIN'	=> '上次发帖标题将显示在版面列表里带一个超链接到帖子。来自密码保护和用户没有访问权限的版面未的标题不显示。',
	'GUEST_STYLE'					=> '访客风格',
	'GUEST_STYLE_EXPLAIN'			=> '访客专用风格。',
	'OVERRIDE_STYLE'				=> '覆盖用户界面',
	'OVERRIDE_STYLE_EXPLAIN'		=> '使用默认的风格覆盖用户所选风格。',
	'SITE_DESC'						=> '站点描述',
	'SITE_HOME_TEXT'				=> '主站首页名称',
	'SITE_HOME_TEXT_EXPLAIN'		=> '此文本将显示为一个链接到你的网站主页在论坛导航。如果不指定，将默认到“首页”。',
	'SITE_HOME_URL'					=> '主站URL',
	'SITE_HOME_URL_EXPLAIN'			=> '如果指定，一个链接到此地址将显示在论坛导航，论坛logo将链接到此地址取代论坛首页。一个绝对地址是必须的，例如<samp>http://www.phpbb.com</samp>。',
	'SITE_NAME'						=> '站点名称',
	'SYSTEM_TIMEZONE'            => '访客时区',
	'SYSTEM_TIMEZONE_EXPLAIN'         => '时区用来为未登录的用户（游客，爬虫）显示时区。已登陆用户在注册过程中选择时区并且可以在用户控制面板中修改。',
	'WARNINGS_EXPIRE'				=> '警告失效时间',
	'WARNINGS_EXPIRE_EXPLAIN'		=> '用户记录警告自动消失的天数。设为0使警告永不过期。',
));

// Board Features
$lang = array_merge($lang, array(
	'ACP_BOARD_FEATURES_EXPLAIN'	=> '这里您可以启用/禁用几个论坛功能',

	'ALLOW_ATTACHMENTS'			=> '允许附件',
	'ALLOW_BIRTHDAYS'			=> '启用“生日”',
	'ALLOW_BIRTHDAYS_EXPLAIN'	=> '允许输入个人生日并在个人信息中显示年龄。请注意论坛首页中显示的生日列表是由另一项负载设置控制的。',
	'ALLOW_BOOKMARKS'			=> '允许主题收藏',
	'ALLOW_BOOKMARKS_EXPLAIN'	=> '用户可以存储个人收藏夹',
	'ALLOW_BBCODE'				=> '允许BBCode',
	'ALLOW_FORUM_NOTIFY'		=> '允许订阅版面',
	'ALLOW_NAME_CHANGE'			=> '允许更改用户名',
	'ALLOW_NO_CENSORS'			=> '允许禁用敏感词过滤',
	'ALLOW_NO_CENSORS_EXPLAIN'	=> '用户可以选择是否禁用帖子和短信中的敏感词自动过滤。',
	'ALLOW_PM_ATTACHMENTS'		=> '在短信中允许附件',
	'ALLOW_PM_REPORT'			=> '允许用户举报不良站内短信',
	'ALLOW_PM_REPORT_EXPLAIN'	=> '启用后, 用户会增加举报站内短信的选项，随后被举报的短信会出现在版主控制面板上。',
	'ALLOW_QUICK_REPLY'			=> '允许快速回复',
	'ALLOW_QUICK_REPLY_EXPLAIN'	=> '关闭此选项将关闭整个论坛的快速回复，当启用后由各个版面的设定决定其快速回复功能是否打开。',
	'ALLOW_QUICK_REPLY_BUTTON'	=> '提交后将开启所有版面的快速回复功能',
	'ALLOW_SIG'					=> '允许签名档',
	'ALLOW_SIG_BBCODE'			=> '在签名档中允许使用BBCode',
	'ALLOW_SIG_FLASH'			=> '在用户签名档中允许使用<code>[FLASH]</code> 标签',
	'ALLOW_SIG_IMG'				=> '在用户签名档中允许使用<code>[IMG]</code> 标签',
	'ALLOW_SIG_LINKS'			=> '在用户签名档中允许使用链接',
	'ALLOW_SIG_LINKS_EXPLAIN'	=> '如果禁用 <code>[URL]</code> 标签和自动链接解析都会失效.',
	'ALLOW_SIG_SMILIES'			=> '在用户签名档中使用表情图标',
	'ALLOW_SMILIES'				=> '允许表情图标',
	'ALLOW_TOPIC_NOTIFY'		=> '允许订阅主题',
	'BOARD_PM'					=> '私人短信',
	'BOARD_PM_EXPLAIN'			=> '启用所有用户的私人短信.',
));

// Avatar Settings
$lang = array_merge($lang, array(
	'ACP_AVATAR_SETTINGS_EXPLAIN'	=> '头像是用户用于展示自己的一幅小图片。根据风格有所不同，不过它们通常显示在用户发布的帖子旁边。这里您可以决定用户是否能和如何定义他们的头像。请注意如果允许用户上载头像，您必须建立下面的目录并保证这个目录对于web服务器是可写的。同时也要注意这个文件大小只对头像上载有效，对于链接的头像是没有限制的。',

	'ALLOW_AVATARS'					=> '启用头像',
	'ALLOW_AVATARS_EXPLAIN'			=> '允许使用头像；<br />如果您禁用了头像功能，论坛将不再显示用户头像，但是会员在会员控制面板中仍可以查看和下载自己的头像。',
	'ALLOW_GRAVATAR'				=> '启用gravatar头像',
	'ALLOW_LOCAL'					=> '运行使用头像册',
	'ALLOW_REMOTE'					=> '允许使用外部头像',
	'ALLOW_REMOTE_EXPLAIN'			=> '从其他网站链接的头像',
	'ALLOW_REMOTE_UPLOAD'			=> '允许远程上载头像',
	'ALLOW_REMOTE_UPLOAD_EXPLAIN'	=> '允许会员上载来自另一个网站的头像。',
	'ALLOW_UPLOAD'					=> '允许头像上载',
	'AVATAR_GALLERY_PATH'			=> '头像册路径',
	'AVATAR_GALLERY_PATH_EXPLAIN'	=> '在您的phpBB论坛根目录下的预先设定的头像画册，例如 <samp>images/avatars/gallery</samp>',
	'AVATAR_STORAGE_PATH'			=> '头像存储路径',
	'AVATAR_STORAGE_PATH_EXPLAIN'	=> '在您的phpBB论坛根目录下的路径，例如 <samp>images/avatars/upload</samp>',
	'MAX_AVATAR_SIZE'				=> '最大头像尺寸',
	'MAX_AVATAR_SIZE_EXPLAIN'		=> '(用像素表示的宽 x 高)',
	'MAX_FILESIZE'					=> '最大的头像文件大小',
	'MAX_FILESIZE_EXPLAIN'			=> '针对上载的头像文件，如果设为0的话上传文件大小只受限于您的 PHP 配置。',
	'MIN_AVATAR_SIZE'				=> '最小的头像文件大小',
	'MIN_AVATAR_SIZE_EXPLAIN'		=> '(用像素表示的宽 x 高)',
));

// Message Settings
$lang = array_merge($lang, array(
	'ACP_MESSAGE_SETTINGS_EXPLAIN'		=> '这里您可以设置私人短信的所有默认设置',

	'ALLOW_BBCODE_PM'			=> '在私人短信中允许BBCode',
	'ALLOW_FLASH_PM'			=> '允许使用<code>[FLASH]</code>',
	'ALLOW_FLASH_PM_EXPLAIN'	=> '这个选项设置是否允许在私人短信中使用flash, 在这里启用后用户依然要视论坛的权限而决定是否可用.',
	'ALLOW_FORWARD_PM'			=> '允许转发私人短信',
	'ALLOW_IMG_PM'				=> '允许使用<code>[IMG]</code>',
	'ALLOW_MASS_PM'				=> '允许对多个用户和用户组发送私人短信',
	'ALLOW_MASS_PM_EXPLAIN'      => '对用户组的发送可以在用户组设置页面对每个用户组进行单独设置.',
	'ALLOW_PRINT_PM'			=> '允许打印格式查看私人短信',
	'ALLOW_QUOTE_PM'			=> '允许在私人短信中引用',
	'ALLOW_SIG_PM'				=> '允许在私人短信中使用签名档',
	'ALLOW_SMILIES_PM'			=> '允许在私人短信中使用表情图标',
	'BOXES_LIMIT'				=> '每个文件夹中的最大信件数量',
	'BOXES_LIMIT_EXPLAIN'		=> '用户每个文件夹中的短信将不能超过这个数量. 设置为0将不作限制.',
	'BOXES_MAX'					=> '每个用户的最大文件夹数量',
	'BOXES_MAX_EXPLAIN'			=> '默认情况下用户可以创建文件夹来存储自己的信件.',
	'ENABLE_PM_ICONS'			=> '在私人短信中允许使用主题图标',
	'FULL_FOLDER_ACTION'		=> '文件夹满时的默认动作',
	'FULL_FOLDER_ACTION_EXPLAIN'=> '如果用户的文件夹满了，默认进行的操作。已发送文件夹的默认操作固定为删除旧信件。',
	'HOLD_NEW_MESSAGES'			=> '挂起新信件',
	'PM_EDIT_TIME'				=> '编辑时限',
	'PM_EDIT_TIME_EXPLAIN'		=> '对于没有发送完成的信件的编辑时限。设置为0将不作限制。', 
	'PM_MAX_RECIPIENTS'         => '收信人的数量上限',
	'PM_MAX_RECIPIENTS_EXPLAIN'   => '对单个私人短信的收信人数量进行限制。如果设置为 0 将不作限制。此设置可以在用户组设置页面中对每个用户组单独进行设置。',
));

// Post Settings
$lang = array_merge($lang, array(
	'ACP_POST_SETTINGS_EXPLAIN'			=> '这里您可以设置发帖的所有默认设置',
	'ALLOW_POST_LINKS'					=> '在帖子/短信中允许链接',
	'ALLOW_POST_LINKS_EXPLAIN'			=> '如果禁用了<code>[URL]</code> 标签将停止链接解析.',
	'ALLOW_POST_FLASH'					=> '允许在帖子中使用 <code>[FLASH]</code> BBCode 标签',
	'ALLOW_POST_FLASH_EXPLAIN'			=> '如果禁用 <code>[FLASH]</code> BBCode 标签, 将禁止在帖子中使用flash. 否则由权限系统控制哪些用户可以使用 <code>[FLASH]</code> BBCode 标签.',

	'BUMP_INTERVAL'					=> '顶帖间隔',
	'BUMP_INTERVAL_EXPLAIN'			=> '主题中最后一个帖子发表到关闭回复的分钟数、小时数或者天数, 设定为0则关闭回复。',
	'CHAR_LIMIT'					=> '每个帖子的最大字数',
	'CHAR_LIMIT_EXPLAIN'			=> '设置为0则不作限制.',
	'DELETE_TIME'					=> '帖子删除时限',
	'DELETE_TIME_EXPLAIN'			=> '设定新帖子的可删除时间。设置为0则不作限制。',
	'DISPLAY_LAST_EDITED'			=> '显示最后编辑信息',
	'DISPLAY_LAST_EDITED_EXPLAIN'	=> '选择是否在帖子上显示最后被谁修改的信息',
	'EDIT_TIME'						=> '编辑时限',
	'EDIT_TIME_EXPLAIN'				=> '新帖子在多长时间内可编辑。设置为0则不作限制。',
	'FLOOD_INTERVAL'				=> '灌水间隔',
	'FLOOD_INTERVAL_EXPLAIN'		=> '新帖子发表之间的秒数。要让用户不受此限制，请修改其权限。',
	'HOT_THRESHOLD'					=> '热门帖子的帖数标准',
	'HOT_THRESHOLD_EXPLAIN'			=> '成为热门帖子需要的最小帖子数，设置为0则取消热帖功能。',
	'MAX_POLL_OPTIONS'				=> '投票的最大选项数',
	'MAX_POST_FONT_SIZE'			=> '帖子中可以使用的最大字体',
	'MAX_POST_FONT_SIZE_EXPLAIN'	=> '设置为0则不作限制.',
	'MAX_POST_IMG_HEIGHT'			=> '帖子中允许的最大图片高度',
	'MAX_POST_IMG_HEIGHT_EXPLAIN'	=> '作用于图片和flash，设置为0则不作限制。',
	'MAX_POST_IMG_WIDTH'			=> '帖子中允许的最大图片宽度',
	'MAX_POST_IMG_WIDTH_EXPLAIN'	=> '作用于图片和flash，设置为0则不作限制。',
	'MAX_POST_URLS'					=> '帖子中允许的最大链接数量',
	'MAX_POST_URLS_EXPLAIN'			=> '设置为0则不作限制.',
	'MIN_CHAR_LIMIT'				=> '文章和私人短信的最小字符数限制',
	'MIN_CHAR_LIMIT_EXPLAIN'		=> '用户在发帖或发私信时内容的最小字符数。最小值是1。',
	'POSTING'						=> '发帖',
	'POSTS_PER_PAGE'				=> '每页帖子数',
	'QUOTE_DEPTH_LIMIT'				=> '引用的最大深度',
	'QUOTE_DEPTH_LIMIT_EXPLAIN'		=> '设置为0则不作限制.',
	'SMILIES_LIMIT'					=> '每个帖子中的最大表情数量',
	'SMILIES_LIMIT_EXPLAIN'			=> '设置为 0 则不作限制.',
	'SMILIES_PER_PAGE'				=> '每页表情数量',
	'TOPICS_PER_PAGE'				=> '每页主题数',
));

// Signature Settings
$lang = array_merge($lang, array(
	'ACP_SIGNATURE_SETTINGS_EXPLAIN'	=> '这里您可以设置用户签名档的默认参数',

	'MAX_SIG_FONT_SIZE'				=> '签名档最大可用字体',
	'MAX_SIG_FONT_SIZE_EXPLAIN'		=> '用户签名档中允许的最大字体，设置为0则无限制。',
	'MAX_SIG_IMG_HEIGHT'			=> '签名档最大图片高度',
	'MAX_SIG_IMG_HEIGHT_EXPLAIN'	=> '作用于图片和flash，设置为0则无限制。',
	'MAX_SIG_IMG_WIDTH'				=> '签名档最大图片宽度',
	'MAX_SIG_IMG_WIDTH_EXPLAIN'		=> '作用于图片和flash，设置为0则无限制。',
	'MAX_SIG_LENGTH'				=> '最大签名档长度',
	'MAX_SIG_LENGTH_EXPLAIN'		=> '用户签名档的最大字符长度。',
	'MAX_SIG_SMILIES'				=> '签名档中的最大表情图标数量',
	'MAX_SIG_SMILIES_EXPLAIN'		=> '设置为0则无限制。',
	'MAX_SIG_URLS'					=> '签名档中的最大链接数量',
	'MAX_SIG_URLS_EXPLAIN'			=> '设置为0则无限制。',
));

// Registration Settings
$lang = array_merge($lang, array(
	'ACP_REGISTER_SETTINGS_EXPLAIN'		=> '这里您可以进行用户注册和资料修改相关的设置',

	'ACC_ACTIVATION'			=> '帐号激活',
	'ACC_ACTIVATION_EXPLAIN'	=> '这决定了用户在访问论坛之前是否需要确认。您也可以完全关闭新用户注册。为了使用用户或管理员激活功能，论坛邮件必须开启。',
	'ACC_ACTIVATION_WARNING'		=> '注意，目前选择的用户帐号激活方法需要启用“允许论坛发送Email”，否则注册将被停用。建议您选择其它的帐号己获方法或者重新启用“允许论坛发送Email”。',
	'NEW_MEMBER_POST_LIMIT'			=> '新会员发文限制',
	'NEW_MEMBER_POST_LIMIT_EXPLAIN'	=> '新会员会被归入 <em>新注册用户</em> 组直到其发表足够多的文章。您可以使用此用户组的组权限来限制其使用站内短信以及方便发文审核。<strong>设置为0则不启用此项功能。</strong>',
	'NEW_MEMBER_GROUP_DEFAULT'		=> '设置新注册用户组为默认组',
	'NEW_MEMBER_GROUP_DEFAULT_EXPLAIN'	=> '如果设置为是, 并且设定了新会员发帖数目限制，新会员不仅会加入到 <em>新注册用户</em> 组，这个用户组也会成为他的默认组。这将方便管理员为该组指定一个默认等级和组头像。',

	'ACC_ADMIN'					=> '由管理员',
	'ACC_DISABLE'				=> '不能注册',
	'ACC_NONE'					=> '无需激活（立即访问）',
	'ACC_USER'					=> '由用户（邮箱验证）',
//	'ACC_USER_ADMIN'			=> '用户 + 管理员',
	'ALLOW_EMAIL_REUSE'			=> '允许重复使用email地址',
	'ALLOW_EMAIL_REUSE_EXPLAIN'	=> '不同的用户可以使用相同的email注册。',
	'COPPA'						=> 'COPPA',
	'COPPA_FAX'					=> 'COPPA传真号',
	'COPPA_MAIL'				=> 'COPPA邮件地址',
	'COPPA_MAIL_EXPLAIN'		=> '这是父母用于发送COPPA注册表单的邮件地址',
	'ENABLE_COPPA'				=> '启用COPPA',
	'ENABLE_COPPA_EXPLAIN'		=> '这需要用户说明自己是否大于13周岁以遵守美国COPPA法规。如果禁止，COPPA用户组将不再显示。',
	'MAX_CHARS'					=> '最大',
	'MIN_CHARS'					=> '最小',
	'NO_AUTH_PLUGIN'			=> '没有发现合适的认证插件。',
	'PASSWORD_LENGTH'			=> '密码长度',
	'PASSWORD_LENGTH_EXPLAIN'	=> '密码的最大和最小长度。',
	'REG_LIMIT'					=> '注册尝试次数',
	'REG_LIMIT_EXPLAIN'			=> '用户在锁定会话前的尝试确认次数。',
	'USERNAME_ALPHA_ONLY'		=> '只允许英文字母',
	'USERNAME_ALPHA_SPACERS'	=> '英文字母和空格',
	'USERNAME_ASCII'			=> 'ASCII (没有国际化unicode)',
	'USERNAME_LETTER_NUM'		=> '任何字母和数字',
	'USERNAME_LETTER_NUM_SPACERS'	=> '任何字母，数字和空格',
	'USERNAME_CHARS'			=> '限制用户名字符',
	'USERNAME_CHARS_ANY'		=> '任何字符',
	'USERNAME_CHARS_EXPLAIN'	=> '限制用于用户名的字符类型，包括：空格, -, +, _, [ 和 ]',
	'USERNAME_LENGTH'			=> '用户名长度',
	'USERNAME_LENGTH_EXPLAIN'	=> '用户名字符的最大和最小长度。',
));

// Feeds
$lang = array_merge($lang, array(
	'ACP_FEED_MANAGEMENT'				=> 'ATOM管理',
	'ACP_FEED_MANAGEMENT_EXPLAIN'		=> '此模块提供多种 ATOM 输出, 并转换 BBCode 为可阅读的内容。',

	'ACP_FEED_GENERAL'					=> '综合设定',
	'ACP_FEED_POST_BASED'				=> '基于文章的输出设定',
	'ACP_FEED_TOPIC_BASED'				=> '基于话题的输出设定',
	'ACP_FEED_SETTINGS_OTHER'			=> '其它设定',

	'ACP_FEED_ENABLE'					=> '启用ATOM',
	'ACP_FEED_ENABLE_EXPLAIN'			=> '对整个论坛启用或停止ATOM输出。<br />关闭此选项会关闭论坛的所有ATOM输出，以下的选项将不再起作用。',
	'ACP_FEED_LIMIT'					=> '数量',
	'ACP_FEED_LIMIT_EXPLAIN'			=> 'ATOM输出条目的最大数量。',

	'ACP_FEED_OVERALL'					=> '启用整个论坛的ATOM输出',
	'ACP_FEED_OVERALL_EXPLAIN'			=> '整个论坛的新帖。',
	'ACP_FEED_FORUM'					=> '启用单个版面ATOM输出',
	'ACP_FEED_FORUM_EXPLAIN'			=> '单个版面和子版面的最新文章。',
	'ACP_FEED_TOPIC'					=> '启用单个话题ATOM输出',
	'ACP_FEED_TOPIC_EXPLAIN'			=> '单个话题的最新文章.',

	'ACP_FEED_TOPICS_NEW'				=> '启用新话题ATOM输出',
	'ACP_FEED_TOPICS_NEW_EXPLAIN'		=> '最新话题ATOM输出最近发表的话题及其第一篇文章的内容。',
	'ACP_FEED_TOPICS_ACTIVE'			=> '启用活跃话题ATOM输出',
	'ACP_FEED_TOPICS_ACTIVE_EXPLAIN'	=> '活跃话题ATOM输出最近发表的活跃话题及其最后一篇回复的内容。',
	'ACP_FEED_NEWS'						=> '各版面最新文章ATOM输出',
	'ACP_FEED_NEWS_EXPLAIN'				=> '从以下版面中各摘取最新的一篇文章。如果无指定版面则关闭此项输出。<br />要选定或反选多个版面，请按住<samp>CTRL</samp>键用鼠标左键点选。',

	'ACP_FEED_OVERALL_FORUMS'			=> '启用版面ATOM输出',
	'ACP_FEED_OVERALL_FORUMS_EXPLAIN'	=> '所有版面ATOM输出会显示所有版面列表.',

	'ACP_FEED_HTTP_AUTH'				=> '允许HTTP验证',
	'ACP_FEED_HTTP_AUTH_EXPLAIN'		=> '启用此验证后，用户可以访问到其他无权限用户所无法访问的内容，需要添加<samp>auth=http</samp>参数到URL。请注意一些PHP版本需要在.htaccess文件上做额外的设置。更多信息请搜索相关文章。',
	'ACP_FEED_ITEM_STATISTICS'			=> '条目统计',
	'ACP_FEED_ITEM_STATISTICS_EXPLAIN'	=> '显示各<br />(作者, 日期和时间, 回复数, 查看数)',
	'ACP_FEED_EXCLUDE_ID'				=> '排除下列版面',
	'ACP_FEED_EXCLUDE_ID_EXPLAIN'		=> '来自这些版面的内容将<strong>不会被输出</strong>。无指定版面则输出所有版面内容。<br />要选定或反选多个版面，请按住<samp>CTRL</samp>键用鼠标左键点选。',
));

// Visual Confirmation Settings
$lang = array_merge($lang, array(
	'ACP_VC_SETTINGS_EXPLAIN'				=> '这里您可以对论坛的验证机制进行设置，有多种方式应对Spam机器人的注册尝试。',
	'ACP_VC_EXT_GET_MORE'					=> '对于附加的(可能更好)反垃圾邮件插件，访问<a href="https://www.phpbb.com/go/anti-spam-ext"><strong>phpBB.com扩展数据库</strong></a>。更多防止论坛垃圾邮件的信息，访问<a href="https://www.phpbb.com/go/anti-spam"><strong>phpBB.com知识库</strong></a>。',
	'AVAILABLE_CAPTCHAS'					=> '可用插件',
	'CAPTCHA_UNAVAILABLE'					=> '无法使用验证图片因为服务器环境不支持。',
	'CAPTCHA_GD'							=> 'GD 验证图片',
	'CAPTCHA_GD_3D'							=> 'GD 3D 验证图片',
	'CAPTCHA_GD_FOREGROUND_NOISE'			=> 'GD 验证图片背景噪点',
	'CAPTCHA_GD_EXPLAIN'					=> '使用 GD 生成更高级的验证图片。',
	'CAPTCHA_GD_FOREGROUND_NOISE_EXPLAIN'	=> '使用基于GD的前景噪点。',
	'CAPTCHA_GD_X_GRID'						=> 'GD 验证图片X轴背景噪点',
	'CAPTCHA_GD_X_GRID_EXPLAIN'				=> '使用这种方式的较低设定生成基于GD的验证图片。 0 将禁用X轴的背景噪点。',
	'CAPTCHA_GD_Y_GRID'						=> 'GD 验证图片Y轴背景噪点',
	'CAPTCHA_GD_Y_GRID_EXPLAIN'				=> '使用这种方式的较低设定生成基于GD的验证图片。 0 将禁用Y轴的背景噪点。',
	'CAPTCHA_GD_WAVE'                  => 'GD 验证图片畸变波',
	'CAPTCHA_GD_WAVE_EXPLAIN'            => '在验证图片上使用畸变波。',
	'CAPTCHA_GD_3D_NOISE'               => '增加3D噪音',
	'CAPTCHA_GD_3D_NOISE_EXPLAIN'         => '这会在验证图片的字母上增加额外的噪音对象。',
	'CAPTCHA_GD_FONTS'                  => '使用不同字体',
	'CAPTCHA_GD_FONTS_EXPLAIN'            => '这个设置控制字体使用的数量。您可以使用默认的字体或导入其他的字体。也可以添加小写字符。',
	'CAPTCHA_FONT_DEFAULT'               => '默认',
	'CAPTCHA_FONT_NEW'                  => '新字体',
	'CAPTCHA_FONT_LOWER'               => '同时使用小写',
	'CAPTCHA_NO_GD'							=> '验证图片(无GD)',
	'CAPTCHA_PREVIEW_MSG'					=> '您的可视化验证设定尚未保存，这只是预览。',
	'CAPTCHA_PREVIEW_EXPLAIN'				=> '您当前设定下的验证图片预览。',

	'CAPTCHA_SELECT'						=> '安装验证图片插件',
	'CAPTCHA_SELECT_EXPLAIN'				=> '下拉框显示被识别的可用验证图片插件。灰色项为需要预先设定方可使用的插件。',
	'CAPTCHA_CONFIGURE'						=> '验证图片设定',
	'CAPTCHA_CONFIGURE_EXPLAIN'				=> '设定选中的验证图片。',
	'CONFIGURE'								=> '设定',
	'CAPTCHA_NO_OPTIONS'					=> '此验证图片没有可设置的选项。',

	'VISUAL_CONFIRM_POST'					=> '启用游客发文可视化验证',
	'VISUAL_CONFIRM_POST_EXPLAIN'			=> '当匿名用户发表文章时会被要求输入一组随机字符以防止机器人发布垃圾信息。',
	'VISUAL_CONFIRM_REG'					=> '在会员注册时启用可视化验证',
	'VISUAL_CONFIRM_REG_EXPLAIN'			=> '在新会员注册时会被要求输入一组随机字符以防止机器人注册。',
	'VISUAL_CONFIRM_REFRESH'				=> '允许用户刷新验证图片',
	'VISUAL_CONFIRM_REFRESH_EXPLAIN'		=> '当用户无法辨认当前验证图片时允许其刷新。一些插件可能不支持这个选项。',
));

// Cookie Settings
$lang = array_merge($lang, array(
	'ACP_COOKIE_SETTINGS_EXPLAIN'		=> '以下设定了送往用户浏览器的数据。在大多数情况下使用默认设置就已足够. 如果您自行调整，不正确的设置将会使用户无法登录论坛。',

	'COOKIE_DOMAIN'				=> 'Cookie作用域',
	'COOKIE_NAME'				=> 'Cookie名称',
	'COOKIE_PATH'				=> 'Cookie路径',
	'COOKIE_SECURE'				=> 'Cookie安全',
	'COOKIE_SECURE_EXPLAIN'		=> '如果您的服务器使用SSL协议，则启用这个选项，否则请禁用. 如果没有使用SSL而启用这个选项，将会使论坛转向错误。',
	'ONLINE_LENGTH'				=> '查看在线时间跨度',
	'ONLINE_LENGTH_EXPLAIN'		=> '非活动的用户在多少分钟后不再显示于在线用户列表。值越高将需要越多的资源生成列表。',
	'SESSION_LENGTH'			=> '会话长度',
	'SESSION_LENGTH_EXPLAIN'	=> '会话多少秒后超时。',
));

// Contact Settings
$lang = array_merge($lang, array(
	'ACP_CONTACT_SETTINGS_EXPLAIN'		=> '这里你可以启用或禁用联系页也可以添加文本显示在页面。',

	'CONTACT_US_ENABLE'				=> '启用联系页',
	'CONTACT_US_ENABLE_EXPLAIN'		=> '此页允许用户发送邮件给论坛管理员',

	'CONTACT_US_INFO'				=> '联系信息',
	'CONTACT_US_INFO_EXPLAIN'		=> '信息已显示在联系页',
	'CONTACT_US_INFO_PREVIEW'		=> '联系页信息 - 预览',
	'CONTACT_US_INFO_UPDATED'		=> '联系页信息已更新。',
));

// Load Settings
$lang = array_merge($lang, array(
	'ACP_LOAD_SETTINGS_EXPLAIN'	=> '这里您可以启用或禁用部分论坛功能以减轻服务器负载。 对于大多数服务器并不需要禁用任何功能。不过有些系统和合租环境下禁用某些不需要的功能能提高性能。您也可以指定系统的负载限制，超出限制的浏览将被拒绝。',

	'ALLOW_CDN'						=> '允许使用第三方内容分发网络',
	'ALLOW_CDN_EXPLAIN'				=> '如果此设置启用，一些文件将被第三方服务器提供，而不是你的服务器。这降低了网络带宽需求，但可能出现隐私问题对于一些论坛管理员。默认phpBB安装时这个包括加载 “jQuery” 和字体 “Open Sans” 从Google的内容分发网络。',
	'ALLOW_LIVE_SEARCHES'			=> '允许实时搜索',
	'ALLOW_LIVE_SEARCHES_EXPLAIN'	=> '如果此设置启用，用户输入时可看到关键词提示。',
	'CUSTOM_PROFILE_FIELDS'			=> '自定义用户资料',
	'LIMIT_LOAD'					=> '系统负载限制',
	'LIMIT_LOAD_EXPLAIN'			=> '如果系统一分钟内的平均负载超过这个值论坛将自动关闭。值1.0 等于单颗处理器的100%使用率，这只工作于基于UNIX/Linux的系统。',
	'LIMIT_SESSIONS'				=> '会话数限制',
	'LIMIT_SESSIONS_EXPLAIN'		=> '如果一分钟内的会话数超过这个值论坛将自动关闭。设置为0将不作限制。',
	'LOAD_CPF_MEMBERLIST'			=> '允许界面在会员列表中显示自定义资料',
	'LOAD_CPF_PM'					=> '显示自定义资料字段在私信里',
	'LOAD_CPF_VIEWPROFILE'			=> '在用户资料中显示自定义资料',
	'LOAD_CPF_VIEWTOPIC'			=> '在帖子查看中显示自定义用户资料',
	'LOAD_USER_ACTIVITY'			=> '显示用户活跃统计',
	'LOAD_USER_ACTIVITY_EXPLAIN'	=> '在用户资料和用户控制面板中显示活跃版面和主题。在帖子数超过百万的论坛上建议关闭此功能。',
	'READ_NOTIFICATION_EXPIRE_DAYS'	=> '已读通知到期',
	'READ_NOTIFICATION_EXPIRE_DAYS_EXPLAIN' => '已读通知自动删除的天数，设置为0通知将永久保存。',
	'RECOMPILE_STYLES'				=> '重新编译旧的风格组件',
	'RECOMPILE_STYLES_EXPLAIN'		=> '检查文件系统中更新风格组件并重新编译。',
	'YES_ANON_READ_MARKING'			=> '允许游客标记主题',
	'YES_ANON_READ_MARKING_EXPLAIN'	=> '为游客存储已读/未读状态。如果禁用，对于游客所有帖子将显示为已读。',
	'YES_BIRTHDAYS'					=> '启用生日列表',
	'YES_BIRTHDAYS_EXPLAIN'			=> '如果禁用，论坛将不会显示生日列表。要使这个选项生效，“生日”功能也必须被启用。',
	'YES_JUMPBOX'					=> '显示跳转列表',
	'YES_MODERATORS'				=> '显示论坛版主',
	'YES_ONLINE'					=> '显示在线用户',
	'YES_ONLINE_EXPLAIN'			=> '在首页，版面和帖子中显示在线用户信息。',
	'YES_ONLINE_GUESTS'				=> '查看在线用户时显示游客在线信息',
	'YES_ONLINE_GUESTS_EXPLAIN'		=> '在查看在线用户时，显示游客在线信息。',
	'YES_ONLINE_TRACK'				=> '显示用户在线/离线信息',
	'YES_ONLINE_TRACK_EXPLAIN'		=> '在用户资料和查看帖子页面中显示用户在线信息。',
	'YES_POST_MARKING'				=> '显示带点主题',
	'YES_POST_MARKING_EXPLAIN'		=> '带点的主题表示用户参与过这个主题。',
	'YES_READ_MARKING'				=> '允许服务器端标记',
	'YES_READ_MARKING_EXPLAIN'		=> '在数据库中保存已读/未读信息而不是存在cookie上。',
	'YES_UNREAD_SEARCH'            => '允许搜索未读文章',
));

// Auth settings
$lang = array_merge($lang, array(
	'ACP_AUTH_SETTINGS_EXPLAIN'	=> 'phpBB 支持认证插件和模块。这允许您决定如何验证用户是否登录。默认的三个插件是DB，LDAP和Apache。并不是所有方式都需要额外信息，所以您只需要填写选中的方式需要的信息即可。',

	'AUTH_METHOD'				=> '选择认证方式',

	'AUTH_PROVIDER_OAUTH_ERROR_ELEMENT_MISSING'	=> '每个启用的OAuth服务的密钥和密码都要提供。只有提供一个被提供给OAuth服务商。',
	'AUTH_PROVIDER_OAUTH_EXPLAIN'				=> '每个OAuth提供商需要唯一的密码和密钥，为了验证外部服务器。这些应该被OAuth服务提供，当你注册你的网站时应该输入准确。<br />任何没有密钥和密码的服务在这儿将不可用，也要注意用户仍可以注册和登录使用DB验证插件。',
	'AUTH_PROVIDER_OAUTH_KEY'					=> '密钥',
	'AUTH_PROVIDER_OAUTH_TITLE'					=> 'OAuth',
	'AUTH_PROVIDER_OAUTH_SECRET'				=> '密码',

	'APACHE_SETUP_BEFORE_USE'	=> '您必须在转换到这种认证模式前建立apache认证功能。记住您用于apache认证的用户名必须和phpBB的用户名相同。',

	'LDAP'							=> 'LDAP',
	'LDAP_DN'						=> 'LDAP基础<var>dn</var>',
	'LDAP_DN_EXPLAIN'				=> '这是唯一的名字，用于定位用户信息，例如 <samp>o=My Company,c=US</samp>',
	'LDAP_EMAIL'					=> 'LDAP email属性',
	'LDAP_EMAIL_EXPLAIN'			=> '将这个设置为用户的email属性名称(如果存在的话)，以便于为新用户自动设置email地址，留空的话将使第一次登录的用户email地址为空。',
	'LDAP_INCORRECT_USER_PASSWORD'	=> '使用指定的用户名密码绑定LDAP服务器失败。',
	'LDAP_NO_EMAIL'					=> '指定的email属性不存在。',
	'LDAP_NO_IDENTITY'				=> '无法为 %s 找到登录身份',
	'LDAP_PASSWORD'					=> 'LDAP 密码',
	'LDAP_PASSWORD_EXPLAIN'			=> '匿名绑定此处请留空，否则请填入上面用户的密码。这对于动态目录服务器是必需的。<strong>警告:</strong>此密码会被明文存储在数据库中，对于任何可以访问数据库或者可以查看此配置页面的人都是可见的。',
	'LDAP_PORT'						=> 'LDAP 服务器端口',
	'LDAP_PORT_EXPLAIN'				=> '选填。您可以指定用于连接LDAP服务器的端口，默认端口为389。',
	'LDAP_SERVER'					=> 'LDAP 服务器名称',
	'LDAP_SERVER_EXPLAIN'			=> '如果使用LDAP，这是LDAP服务器的域名或IP地址。并且您也可以指定一个URL例如 ldap://hostname:port/',
	'LDAP_UID'						=> 'LDAP <var>uid</var>',
	'LDAP_UID_EXPLAIN'				=> '这是用于查找给定登录身份的关键字，例如 <var>uid</var>, <var>sn</var>。',
	'LDAP_USER'						=> 'LDAP 用户 <var>dn</var>',
	'LDAP_USER_EXPLAIN'				=> '如果绑定为匿名，此处请留空。如果填入，phpBB会使用指定的唯一用户名在登录中寻找正确的用户，例如： <samp>uid=Username,ou=MyUnit,o=MyCompany,c=US</samp>。这对于Active Directory Server是必需的。',
	'LDAP_USER_FILTER'				=> 'LDAP 用户过滤',
	'LDAP_USER_FILTER_EXPLAIN'		=> '选填，您可以使用附加条件过滤搜索的对象。例如<samp>objectClass=posixGroup</samp> 将变成 <samp>(&(uid=$username)(objectClass=posixGroup))</samp>',
));

// Server Settings
$lang = array_merge($lang, array(
	'ACP_SERVER_SETTINGS_EXPLAIN'	=> '这里配置服务器和域名相关的设定。请确保输入的数据是正确可靠的，错误将导致email包含错误信息，档输入域名时记住不包含http:// 和其他协议头。只有当您的服务器使用一个特别的端口时才需要更改端口号，一般使用的都是80。',

	'ENABLE_GZIP'				=> '启用GZip压缩', 
	'ENABLE_GZIP_EXPLAIN'		=> '生成的页面将在发送到浏览器前被压缩。这将减少网络流量但是会增加服务器和客户端的CPU负载。需要zlib支持',
	'FORCE_SERVER_VARS'			=> '强制设定服务器URL',
	'FORCE_SERVER_VARS_EXPLAIN'	=> '如果设置为是，以下的设定将启用',
	'ICONS_PATH'				=> '主题图标存储路径',
	'ICONS_PATH_EXPLAIN'		=> '相对于phpBB根目录的路径，例如<samp>images/icons</samp>',
	'MOD_REWRITE_ENABLE'		=> '启用路径重写',
	'MOD_REWRITE_ENABLE_EXPLAIN' => '启用后包含 ’app.php’ 的路径将重写为移除文件名(例如app.php/foo 变成 /foo)。<strong>必须有Apache服务器的mod_rewrite组件才能正常工作；如果此选项启用但没有mod_rewrite，路径将损坏。</strong>',
	'MOD_REWRITE_DISABLED'		=> '<strong>mod_rewrite</strong>组件在你的Apache服务器上禁用，启用该组件或联系主机提供商如果你想启用此功能。',
	'MOD_REWRITE_INFORMATION_UNAVAILABLE' => '我们不能决定是否此服务器支持路径重写，此设置可能启用但如果地址重写不可用，生成的路径(如链接)可能损坏。联系你的主机提供商如果你不确定是否你可以安全启用此功能。',
	'PATH_SETTINGS'				=> '路径设定',
	'RANKS_PATH'				=> '等级图标存储路径',
	'RANKS_PATH_EXPLAIN'		=> '相对于phpBB根目录的路径，例如<samp>images/ranks</samp>',
	'SCRIPT_PATH'				=> '脚本路径',
	'SCRIPT_PATH_EXPLAIN'		=> 'phpBB相对于域名的路径，例如<samp>/phpBB3</samp>',
	'SERVER_NAME'				=> '域名',
	'SERVER_NAME_EXPLAIN'		=> '论坛所在域名 (例如: <samp>www.foo.bar</samp>)',
	'SERVER_PORT'				=> '服务器端口',
	'SERVER_PORT_EXPLAIN'		=> '服务器运行的端口，通常是 80。如果不清楚请不要更改',
	'SERVER_PROTOCOL'			=> '服务协议',
	'SERVER_PROTOCOL_EXPLAIN'	=> '如果强制设定，这将用于服务器协议。如果留空或未强制设定，协议由cookie安全设定决定 (<samp>http://</samp> or <samp>https://</samp>)',
	'SERVER_URL_SETTINGS'		=> '服务器URL设定',
	'SMILIES_PATH'				=> '表情图标存储路径',
	'SMILIES_PATH_EXPLAIN'		=> '相对于phpBB根目录的路径，例如 <samp>images/smilies</samp>',
	'UPLOAD_ICONS_PATH'			=> '扩展名组图标存储路径',
	'UPLOAD_ICONS_PATH_EXPLAIN'	=> '相对于phpBB根目录的路径，例如 <samp>images/upload_icons</samp>',
	'USE_SYSTEM_CRON'		=> '运行定期任务从系统计划任务',
	'USE_SYSTEM_CRON_EXPLAIN'		=> '关闭后phpBB将安排定期任务自动运行。启用后phpBB将不安排任何定期任务；一个系统管理员必须安排<code>bin/phpbbcli.php cron:run</code>运行间隔(例如每隔5分钟)。',
));

// Security Settings
$lang = array_merge($lang, array(
	'ACP_SECURITY_SETTINGS_EXPLAIN'		=> '这里您可以进行对话和登录相关的设定',

	'ALL'							=> '所有',
	'ALLOW_AUTOLOGIN'				=> '允许自动登录', 
	'ALLOW_AUTOLOGIN_EXPLAIN'		=> '决定用户是否可以在浏览论坛时自动登录。', 
	'ALLOW_PASSWORD_RESET'			=> '允许密码重置("忘记密码")',
	'ALLOW_PASSWORD_RESET_EXPLAIN'	=> '确定是否能使用"我忘了密码"链接在登录页恢复账户。如果使用外部验证机制你可能想禁用此功能。',
	'AUTOLOGIN_LENGTH'				=> '自动登录失效时间 (天数)', 
	'AUTOLOGIN_LENGTH_EXPLAIN'		=> '设置为0将取消限制。', 
	'BROWSER_VALID'					=> '浏览器验证',
	'BROWSER_VALID_EXPLAIN'			=> '启用浏览器验证以增加安全性。',
	'CHECK_DNSBL'					=> '检查 IP 以防御 DNS 黑洞',
	'CHECK_DNSBL_EXPLAIN'			=> '如果启用，用户的IP地址将被检查以防御如下在注册和发帖时的 DNSBL 服务: <a href="http://spamcop.net">spamcop.net</a> 和 <a href="http://www.spamhaus.org">www.spamhaus.org</a>。这个检查将耗费一些时间，取决于服务器的设置。如果让论坛变得很慢或产生很多错误报告，请禁用这个功能。',
	'CLASS_B'						=> 'A.B',
	'CLASS_C'						=> 'A.B.C',
	'EMAIL_CHECK_MX'				=> '检查email域名以得到有效 MX 记录',
	'EMAIL_CHECK_MX_EXPLAIN'		=> '如果启用，在注册时提供的email的域名将被检查是否有有效的MX记录。',
	'FORCE_PASS_CHANGE'				=> '强制密码变更',
	'FORCE_PASS_CHANGE_EXPLAIN'		=> '强制用户在一段时间(天数)后更改密码。设置为0则取消限制。',
	'FORM_TIME_MAX'					=> '提交表单的最长时间',
	'FORM_TIME_MAX_EXPLAIN'			=> '在这个时间前用户必须提交。使用 -1 取消这项功能。注意如果设置的时间过长，当对话失效时表单也会自动失效，这种情况下这里的设置是无效的。',
	'FORM_SID_GUESTS'				=> '游客表单提交限制',
	'FORM_SID_GUESTS_EXPLAIN'		=> '启用后, 游客所有的表单将会是对话唯一的，这个功能在某些ISP中可能无法正常使用。',
	'FORWARDED_FOR_VALID'			=> '验证<var>X_FORWARDED_FOR</var>头部',
	'FORWARDED_FOR_VALID_EXPLAIN'	=> '只有在发送的<var>X_FORWARDED_FOR</var> 字段头等于前一次请求中的字段头才继续会话。封禁也将检查 <var>X_FORWARDED_FOR</var> 中的IP。',
	'IP_VALID'						=> '对话IP验证',
	'IP_VALID_EXPLAIN'				=> '决定用户的IP如何用于会话验证; <samp>所有</samp> 表示完整地址。<samp>A.B.C</samp> 表示开头的 x.x.x, <samp>A.B</samp> 表示开头的 x.x, <samp>None</samp> 取消验证.',
	'IP_LOGIN_LIMIT_MAX'			=> '每个IP的最大登陆尝试次数',
	'IP_LOGIN_LIMIT_MAX_EXPLAIN'	=> '单个IP登陆尝试时触发验证码机制的阈值次数。输入0防止验证码机制被IP触发。',
	'IP_LOGIN_LIMIT_TIME'			=> 'IP登陆尝试过期时间',
	'IP_LOGIN_LIMIT_TIME_EXPLAIN'	=> '登陆尝试在这个周期后过期。',
	'IP_LOGIN_LIMIT_USE_FORWARDED'	=> '通过<var>X_FORWARDED_FOR</var>头部限制登陆尝试次数',
	'IP_LOGIN_LIMIT_USE_FORWARDED_EXPLAIN'	=> '用<var>X_FORWARDED_FOR</var>值代替限制IP登陆尝试次数。<br /><em><strong>警告：</strong>只有开启这个选项，您才能在代理服务器上设置<var>X_FORWARDED_FOR</var>成可信值。</em>',
	'MAX_LOGIN_ATTEMPTS'			=> '每个用户的最大登陆尝试次数',
	'MAX_LOGIN_ATTEMPTS_EXPLAIN'	=> '单独账号在触发验证码机制之前允许登陆尝试的次数。输入0为不同的账户防止验证码机制被触发。',
	'NO_IP_VALIDATION'				=> '无',
	'NO_REF_VALIDATION'				=> '无',
	'PASSWORD_TYPE'					=> '密码复杂度',
	'PASSWORD_TYPE_EXPLAIN'			=> '决定设定或更改时密码的复杂度，多个选项则往前叠加。',
	'PASS_TYPE_ALPHA'				=> '必须包含数字字母',
	'PASS_TYPE_ANY'					=> '没有要求',
	'PASS_TYPE_CASE'				=> '必须混用大写字符',
	'PASS_TYPE_SYMBOL'				=> '必须包含符号',
	'REF_HOST'						=> '只验证主机名',
	'REF_PATH'						=> '同时验证路径',
	'REFERRER_VALID'					=> '验证转向来源',
	'REFERRER_VALID_EXPLAIN'			=> '启用此功能后，POST请求将根据主机名/脚本路径等设置进行验证。此功能会对使用多域名和外部登录的论坛造成影响。',
	'TPL_ALLOW_PHP'					=> '在模板中允许PHP',
	'TPL_ALLOW_PHP_EXPLAIN'			=> '如果启用这个选项，<code>PHP</code> 和 <code>INCLUDEPHP</code> 声明将在模板中被解析。',
));

// Email Settings
$lang = array_merge($lang, array(
	'ACP_EMAIL_SETTINGS_EXPLAIN'	=> '在论坛向用户发送e-mail时将使用这个信息。请确保e-mail地址有效，任何被退回和无法投递的消息将很可能被发回至这个地址. 如果您的主机不提供本地(基于PHP的) email服务, 您可以使用SMTP发送消息. 这需要服务器的地址 (必要的话询问提供者). 如果服务器需要验证 (并且只有在需要时) 输入必要的用户名和密码.',

	'ADMIN_EMAIL'					=> '返回email地址',
	'ADMIN_EMAIL_EXPLAIN'			=> '这将是所有email的返回地址，技术联络email。将显示于<samp>Return-Path</samp> 和 <samp>Sender</samp>。',
	'BOARD_EMAIL_FORM'				=> '用户通过论坛发送email',
	'BOARD_EMAIL_FORM_EXPLAIN'		=> '可以使用论坛发送email而不显示用户的email地址.',
	'BOARD_HIDE_EMAILS'				=> '隐藏email地址',
	'BOARD_HIDE_EMAILS_EXPLAIN'		=> '这个功能使email地址完全隐蔽。',
	'CONTACT_EMAIL'					=> 'email联络地址',
	'CONTACT_EMAIL_EXPLAIN'			=> '这将使用在任何需要指定联络方式的场合，例如 垃圾信息，错误输出，等等。这将总是显示在 <samp>From</samp> 和 <samp>Reply-To</samp>。',
	'CONTACT_EMAIL_NAME'			=> '联系名字',
	'CONTACT_EMAIL_NAME_EXPLAIN'	=> '这是邮件收件人将看到的联系名字，如果你不想要联系名字就留空。',
	'EMAIL_FUNCTION_NAME'			=> 'Email函数名称',
	'EMAIL_FUNCTION_NAME_EXPLAIN'	=> '在PHP中用于发送email的函数。',
	'EMAIL_PACKAGE_SIZE'			=> 'Email数据包大小',
	'EMAIL_PACKAGE_SIZE_EXPLAIN'	=> '这是在一个数据包中包含的最大email数量。这项设置被用于内部信件队列；如果您遇到无法投递信件的错误，请将它设置为0。',
	'EMAIL_SIG'						=> 'Email签名',
	'EMAIL_SIG_EXPLAIN'				=> '将在论坛发送的email后附加这段文字.',
	'ENABLE_EMAIL'					=> '允许论坛发送email',
	'ENABLE_EMAIL_EXPLAIN'			=> '如果禁用，论坛将不会发送任何email。<em>注意会员激活需要启用此项功能。如果当前设定为会员自行激活和管理员激活，则停用此项功能将使会员无需激活。</em>',
	'SMTP_AUTH_METHOD'				=> 'SMTP验证方式',
	'SMTP_AUTH_METHOD_EXPLAIN'		=> '只有在设置过用户名/密码的场合，询问提供者如果您不能确定使用何种方式。',
	'SMTP_CRAM_MD5'					=> 'CRAM-MD5',
	'SMTP_DIGEST_MD5'				=> 'DIGEST-MD5',
	'SMTP_LOGIN'					=> 'LOGIN',
	'SMTP_PASSWORD'					=> 'SMTP 密码',
	'SMTP_PASSWORD_EXPLAIN'			=> '只有当您的SMTP服务器需要时才要输入。',
	'SMTP_PLAIN'					=> 'PLAIN',
	'SMTP_POP_BEFORE_SMTP'			=> 'POP-BEFORE-SMTP',
	'SMTP_PORT'						=> 'SMTP服务器端口',
	'SMTP_PORT_EXPLAIN'				=> '只有当您清楚您的SMTP服务器运行在一个不同的端口上时才需要设置。',
	'SMTP_SERVER'					=> 'SMTP服务器地址',
	'SMTP_SETTINGS'					=> 'SMTP设定',
	'SMTP_USERNAME'					=> 'SMTP用户名',
	'SMTP_USERNAME_EXPLAIN'			=> '只有当您的SMTP服务器需要时才要输入。',
	'USE_SMTP'						=> '使用SMTP服务器发送email',
	'USE_SMTP_EXPLAIN'				=> '选择“是”。如果您向通过其他服务器而不是本地mail函数发送email。',
));

// Jabber settings
$lang = array_merge($lang, array(
	'ACP_JABBER_SETTINGS_EXPLAIN'	=> '这里您可以启用并控制用户使用Jabber发送及时消息和论坛通知。Jabber是任何人都可以使用的开放协议。一些Jabber服务器提供允许您联系其他网络用户的通道。并非所有的服务器都提供这样的通道，协议上的变化将使得操作失败。请确认输入的是已经注册的帐号信息，phpBB将会使用你这里输入的数据。',

	'JAB_ENABLE'				=> '启用Jabber',
	'JAB_ENABLE_EXPLAIN'		=> '允许使用jabber消息和通知',
	'JAB_GTALK_NOTE'			=> '请注意GTalk无法工作因为无法找到 <samp>dns_get_record</samp> 函数。这个函数在PHP4中是无效的, 并且在windows平台上没有此模块。当前此功能还无法工作在BSD系统和Mac操作系统上。',
	'JAB_PACKAGE_SIZE'			=> 'Jabber数据包大小',
	'JAB_PACKAGE_SIZE_EXPLAIN'	=> '这是单个数据包中发送的消息数量。设置为0将不作延迟而直接发送。',
	'JAB_PASSWORD'				=> 'Jabber密码',
	'JAB_PASSWORD_EXPLAIN'		=> '<em><strong>注意:</strong> 此密码将以明文保存在数据库中，任何拥有数据库访问权限或可以访问此页的用户都可以看到此密码。</em>',
	'JAB_PORT'					=> 'Jabber端口',
	'JAB_PORT_EXPLAIN'			=> '留空, 除非您清楚这个端口不是5222',
	'JAB_SERVER'				=> 'Jabber服务器',
	'JAB_SERVER_EXPLAIN'		=> '查看%sjabber.org%s上的服务器列表',
	'JAB_SETTINGS_CHANGED'		=> 'Jabber设定修改完成.',
	'JAB_USE_SSL'				=> '使用SSL连接',
	'JAB_USE_SSL_EXPLAIN'		=> '如果启用安全连接，Jabber端口将更改为5223，如果5222没有被指定。',
	'JAB_USERNAME'				=> 'Jabber用户名或JID',
	'JAB_USERNAME_EXPLAIN'		=> '请指定一个已经注册的用户，它将不会被检测是否存在。 如果您仅仅指定一个用户名，那么您的JID将是您指定的用户名和服务器名称，否则您需要指定一个有效的JID，例如user@jabber.org。',
));
