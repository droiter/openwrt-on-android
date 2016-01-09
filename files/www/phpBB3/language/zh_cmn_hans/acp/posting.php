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

// BBCodes 
// Note to translators: you can translate everything but what's between { and }
$lang = array_merge($lang, array(
	'ACP_BBCODES_EXPLAIN'		=> 'BBCode是一种HTML的特殊实现，可以产生各种页面效果而不会有安全隐患。这里您可以添加/删除/编辑自定义BBCode',
	'ADD_BBCODE'				=> '添加BBCode',

	'BBCODE_DANGER'				=> '您添加的BBCode似乎在HTML属性中使用了 {TEXT} 标记。这可能会带来 XSS 安全隐患。请使用更安全的 {SIMPLETEXT} 或 {INTTEXT} 类型。除非您明白此设置的含义并确信使用 {TEXT} 并不会带来问题。',
	'BBCODE_DANGER_PROCEED'		=> '我明白，继续', //'I understand the risk',

	'BBCODE_ADDED'				=> 'BBCode添加完成。',
	'BBCODE_EDITED'				=> 'BBCode编辑完成。',
	'BBCODE_DELETED'			=> 'BBCode已成功移除。',
	'BBCODE_NOT_EXIST'			=> '您选择的BBCode不存在。',
	'BBCODE_HELPLINE'			=> '提示',
	'BBCODE_HELPLINE_EXPLAIN'	=> '这个区域会在鼠标移过时显示BBCode提示信息',
	'BBCODE_HELPLINE_TEXT'		=> '提示文本',
	'BBCODE_HELPLINE_TOO_LONG'   => '您设定的提示文本过长。',

	'BBCODE_INVALID_TAG_NAME'	=> '您选择的BBCode标签名称已经存在。',
	'BBCODE_INVALID'			=> '您的 BBCode 形式无效。',
	'BBCODE_OPEN_ENDED_TAG'		=> '您的自定义BBCode必须包含开关标签。',
	'BBCODE_TAG'				=> '标签',
	'BBCODE_TAG_TOO_LONG'		=> '您选择的标签名称太长了。',
	'BBCODE_TAG_DEF_TOO_LONG'	=> '您输入的标签定义太长了，请尽量简短。',
	'BBCODE_USAGE'				=> 'BBCode使用方法',
	'BBCODE_USAGE_EXAMPLE'		=> '[highlight={COLOR}]{TEXT}[/highlight]<br /><br />[font={SIMPLETEXT1}]{SIMPLETEXT2}[/font]',
	'BBCODE_USAGE_EXPLAIN'		=> '这里您可以定义bbcode的用法。使用相关的标记替换相关的变量输入(%s如下所示%s)',

	'EXAMPLE'						=> '例子:',
	'EXAMPLES'						=> '例子:',

	'HTML_REPLACEMENT'				=> 'HTML替换代码',
	'HTML_REPLACEMENT_EXAMPLE'		=> '&lt;span style="background-color: {COLOR};"&gt;{TEXT}&lt;/span&gt;<br /><br />&lt;span style="font-family: {SIMPLETEXT1};"&gt;{SIMPLETEXT2}&lt;/span&gt;',
	'HTML_REPLACEMENT_EXPLAIN'		=> '这里您可以定义默认的HTML替换代码 (每个模板可以使用它自己的替换代码)。不要忘记放回你上面使用的标记!',

	'TOKEN'					=> '标记',
	'TOKENS'				=> '标记',
	'TOKENS_EXPLAIN'		=> '标记是用户输入的占位符。只有满足设定的输入才能通过验证。如果需要的话，您可以使用数字对它们进行编号，例如 {TEXT1}，{TEXT2}.<br /><br />在HTML替换语句中您可以使用language目录下的任意语言条目，例如: {L_<em>&lt;STRINGNAME&gt;</em>} 这里 <em>&lt;STRINGNAME&gt;</em> 就是语言包下的一个翻译条目。例如，{L_WROTE} 在英文中将被显示为 &quot;wrote&quot; 或者用户所使用的语言的翻译.<br /><br /><strong>请注意只有如下列出的标记可以在自定义BBCode中使用.</strong>',
	'TOKEN_DEFINITION'		=> '这将是？',
	'TOO_MANY_BBCODES'		=> '您不能创建更多BBCodes。请删除掉一些现有的BBCode后再重试。',

	'tokens'	=>	array(
		'TEXT'			=> '任何文本，包括外文字符，数字等等..。您不能在HTML标签中使用这种类型，而必需使用 IDENTIFIER，INTTEXT 或 SIMPLETEXT。',
		'SIMPLETEXT'	=> '拉丁字符 (A-Z)，数字，空格，逗号，点，减号，加号，连字号和下划线',
		'INTTEXT'		=> 'Unicode字符，数字，空格，逗号，点，减号，加号，连字符和下划线。',
		'IDENTIFIER'	=> '拉丁字符 (A-Z)，数字，连字符和下划线',
		'NUMBER'		=> '任何数字序列',
		'EMAIL'			=> '有效的email地址',
		'URL'			=> '使用任何协议的有效的URL(http，ftp，等等… 不能用作javascript掠夺)。如果没有提供，将添加“http://”',
		'LOCAL_URL'		=> '本地URL。URL必须和主题页相关并且不能包含服务器名称和协议，就像带前缀"%s"的链接',
		'RELATIVE_URL'	=> '相对URL。您可以使用这个来匹配部分URL，但是注意: 一个完整的URL是一个有效的相对URL。当你想使用相对URL时,使用 LOCAL_URL 标记。',
		'COLOR'			=> 'HTML颜色，可以是六位十六进制数<samp>#FF1234</samp> 或者<a href="http://www.w3.org/TR/CSS21/syndata.html#value-def-color">CSS色彩关键字</a> 例如<samp>fuchsia</samp> 或 <samp>InactiveBorder</samp>'
	),
));

// Smilies and topic icons
$lang = array_merge($lang, array(
	'ACP_ICONS_EXPLAIN'		=> '这里您可以添加/删除/编辑用户在主题和帖子中使用的图标。这些图标一般显示在标题旁，您也可以添加新的图标包。',
	'ACP_SMILIES_EXPLAIN'	=> '表情图标一般是较小的，有时可以是动画文件的小图片，用户用于表达情绪的好方式。这里您可以添加/删除/编辑表情图标。您也可以安装和创建新的表情图片包。',
	'ADD_SMILIES'			=> '添加多个表情图标',
	'ADD_SMILEY_CODE'		=> '添加更多的表情代码',
	'ADD_ICONS'				=> '添加多个图标',
	'AFTER_ICONS'			=> '在 %s 之后',
	'AFTER_SMILIES'			=> '在 %s 之后',

	'CODE'						=> '代码',
	'CURRENT_ICONS'				=> '当前图标',
	'CURRENT_ICONS_EXPLAIN'		=> '选择如何处理当前安装的图标',
	'CURRENT_SMILIES'			=> '当前表情图标',
	'CURRENT_SMILIES_EXPLAIN'	=> '选择如何处理当前安装的表情图标',

	'DISPLAY_ON_POSTING'		=> '在发帖时显示',
	'DISPLAY_POSTING'			=> '发帖时显示',
	'DISPLAY_POSTING_NO'		=> '发帖时不显示',

	'EDIT_ICONS'				=> '编辑图标',
	'EDIT_SMILIES'				=> '编辑表情图标',
	'EMOTION'					=> '表情',
	'EXPORT_ICONS'				=> '导出并下载icons.pak',
	'EXPORT_ICONS_EXPLAIN'		=> '%s点击这个链接，您安装的图标设置将被输出为 <samp>icons.pak</samp> 下载，用于创建 <samp>.zip</samp> 或 <samp>.tgz</samp> 文件，这个文件包含所有您的图标加上这个 <samp>icons.pak</samp> 配置文件%s。',
	'EXPORT_SMILIES'			=> '导出并下载smilies.pak',
	'EXPORT_SMILIES_EXPLAIN'	=> '%s点击这个链接，您安装的表情图标设置将被导出为 <samp>smilies.pak</samp> 下载，用于创建 <samp>.zip</samp> 或 <samp>.tgz</samp> 文件，这个文件包含所有您的表情图标加上配置文件 <samp>smilies.pak</samp>%s。',

	'FIRST'			=> '第一个',

	'ICONS_ADD'				=> '添加新图标',
	'ICONS_ADDED'			=> array(
		0	=> '没有添加图标。',
		1	=> '图标已添加成功。',
		2	=> '图标已添加成功。',
	),
	'ICONS_CONFIG'			=> '图标设置',
	'ICONS_DELETED'			=> '图标删除完成。',
	'ICONS_EDIT'			=> '编辑图标',
	'ICONS_EDITED'			=> array(
		0	=> '没有更新图标。',
		1	=> '图标已更新成功。',
		2	=> '图标已更新成功。',
	),
	'ICONS_HEIGHT'			=> '图标高度',
	'ICONS_IMAGE'			=> '图标图片',
	'ICONS_IMPORTED'		=> '图标包安装完成。',
	'ICONS_IMPORT_SUCCESS'	=> '图标系列导入完成。',
	'ICONS_LOCATION'		=> '图标位置',
	'ICONS_NOT_DISPLAYED'	=> '如下图标将不会在发帖中显示',
	'ICONS_ORDER'			=> '图标顺序',
	'ICONS_URL'				=> '图标文件',
	'ICONS_WIDTH'			=> '图标宽度',
	'IMPORT_ICONS'			=> '安装图标包',
	'IMPORT_SMILIES'		=> '安装表情图片包',

	'KEEP_ALL'			=> '保留全部',

	'MASS_ADD_SMILIES'	=> '添加多个表情图标',

	'NO_ICONS_ADD'		=> '没有可用于添加的图标。',
	'NO_ICONS_EDIT'		=> '没有可用于修改的图标。',
	'NO_ICONS_EXPORT'	=> '您没有用于创建包的图标。',
	'NO_ICONS_PAK'		=> '没有找到图片包。',
	'NO_SMILIES_ADD'	=> '没有可用于添加的表情图标。',
	'NO_SMILIES_EDIT'	=> '没有可用于修改的表情图标。',
	'NO_SMILIES_EXPORT'	=> '您没有用于创建表情图片包的表情图片。',
	'NO_SMILIES_PAK'	=> '没有找到表情图片包。',

	'PAK_FILE_NOT_READABLE'		=> '无法读取 <samp>.pak</samp> 文件。',

	'REPLACE_MATCHES'	=> '替换匹配',

	'SELECT_PACKAGE'			=> '选择一个包文件',
	'SMILIES_ADD'				=> '添加一个新表情',
	'SMILIES_ADDED'				=> array(
		0	=> '没有添加表情。',
		1	=> '表情已成功添加。',
		2	=> '表情已成功添加。',
	),
	'SMILIES_CODE'				=> '表情代码',
	'SMILIES_CONFIG'			=> '表情设置',
	'SMILIES_DELETED'			=> '表情删除完成。',
	'SMILIES_EDIT'				=> '编辑表情',
	'SMILIE_NO_CODE'			=> '忽略表情“%s”，因为没有输入代码。',
	'SMILIE_NO_EMOTION'			=> '忽略表情 “%s”，因为没有输入表情。',
	'SMILIE_NO_FILE'			=> '忽略表情“%s”，因为文件丢失。',
	'SMILIES_EDITED'			=> array(
		0	=> '没有更新表情。',
		1	=> '表情已成功更新。',
		2	=> '表情已成功更新。',
	),
	'SMILIES_EMOTION'			=> '表情',
	'SMILIES_HEIGHT'			=> '表情图片高度',
	'SMILIES_IMAGE'				=> '表情图片',
	'SMILIES_IMPORTED'			=> '表情图片包安装完成。',
	'SMILIES_IMPORT_SUCCESS'	=> '表情图片包导入完成。',
	'SMILIES_LOCATION'			=> '表情图片地址',
	'SMILIES_NOT_DISPLAYED'		=> '以下的表情图片在发帖页面上不显示',
	'SMILIES_ORDER'				=> '表情图标顺序',
	'SMILIES_URL'				=> '表情图片文件',
	'SMILIES_WIDTH'				=> '表情图片宽度',

	'TOO_MANY_SMILIES'			=> array(
		1	=> '上限 %d 个表情到达。',
		2	=> '上限 %d 个表情到达。',
	),

	'WRONG_PAK_TYPE'	=> '指定的文件包中没有所需的数据。',
));

// Word censors
$lang = array_merge($lang, array(
	'ACP_WORDS_EXPLAIN'		=> '这个控制面板中可以添加，编辑，删除需要过滤的词汇。但是用户将仍可以使用包含这些敏感词的用户名注册帐号。可以使用通配符，例如 *test* 将匹配detestable，test*将匹配testing，*test将匹配detest',
	'ADD_WORD'				=> '添加敏感词',

	'EDIT_WORD'		=> '编辑敏感词',
	'ENTER_WORD'	=> '您必须输入敏感词和其替代词。',

	'NO_WORD'	=> '没有选择编辑对象。',

	'REPLACEMENT'	=> '替代词',

	'UPDATE_WORD'	=> '更新敏感词',

	'WORD'				=> '敏感词',
	'WORD_ADDED'		=> '敏感词添加完成。',
	'WORD_REMOVED'		=> '选中的敏感词删除完成。',
	'WORD_UPDATED'		=> '选中的敏感词更新完成。',
));

// Ranks
$lang = array_merge($lang, array(
	'ACP_RANKS_EXPLAIN'		=> '使用这个表单您可以添加，编辑，查看和删除等级。您也可以创建自定义等级，使用在用户管理中。',
	'ADD_RANK'				=> '添加新等级',

	'MUST_SELECT_RANK'		=> '您必须选择一个等级。',

	'NO_ASSIGNED_RANK'		=> '没有指定特殊等级。',
	'NO_RANK_TITLE'			=> '您没有给等级指定头衔。',
	'NO_UPDATE_RANKS'		=> '等级成功删除。但是使用这个等级的用户并没有更新。您需要手工重置这些用户的等级。',

	'RANK_ADDED'			=> '等级添加完成。',
	'RANK_IMAGE'			=> '等级图标',
	'RANK_IMAGE_EXPLAIN'	=> '这里定义和等级相关联的小图片。路径为phpBB根目录的相对路径。',
	'RANK_IMAGE_IN_USE'		=> '(使用中)',
	'RANK_MINIMUM'			=> '最小帖子数',
	'RANK_REMOVED'			=> '等级删除完成。',
	'RANK_SPECIAL'			=> '设置为特殊等级',
	'RANK_TITLE'			=> '等级头衔',
	'RANK_UPDATED'			=> '等级更新完成。',
));

// Disallow Usernames
$lang = array_merge($lang, array(
	'ACP_DISALLOW_EXPLAIN'	=> '这里您可以控制不允许使用的用户名，可包含通配符*。',
	'ADD_DISALLOW_EXPLAIN'	=> '您可以禁用某个用户名，可使用通配符*匹配任意字符。',
	'ADD_DISALLOW_TITLE'	=> '添加禁用用户名',

	'DELETE_DISALLOW_EXPLAIN'	=> '您可以通过点击列表中的用户名再点击提交来删除一个禁用的用户名',
	'DELETE_DISALLOW_TITLE'		=> '删除一个禁用的用户名',
	'DISALLOWED_ALREADY'		=> '您输入的用户名已被禁用。',
	'DISALLOWED_DELETED'		=> '禁用的用户名删除完成。',
	'DISALLOW_SUCCESSFUL'		=> '禁用的用户名添加完成。',

	'NO_DISALLOWED'				=> '没有禁用的用户名',
	'NO_USERNAME_SPECIFIED'		=> '您没有输入操作的用户名。',
));

// Reasons
$lang = array_merge($lang, array(
	'ACP_REASONS_EXPLAIN'	=> '这里您可以管理用于举报和否决帖子的原因。默认的原因 (带 * 标记) 不能删除，这个原因一般用于没有合适的选项而要指定自定义原因的场合。',
	'ADD_NEW_REASON'		=> '添加新原因',
	'AVAILABLE_TITLES'		=> '可用的本地语言原因标题',

	'IS_NOT_TRANSLATED'			=> '原因 <strong>尚未</strong> 本地化。',
	'IS_NOT_TRANSLATED_EXPLAIN'	=> '原因 <strong>尚未</strong> 被翻译成本地语言。如果您希望提供本地化的表单，则需要为语言文件的举报原因片段设定合适的关键字。',
	'IS_TRANSLATED'				=> '原因已经本地化。',
	'IS_TRANSLATED_EXPLAIN'		=> '原因已经被翻译成本地语言。如果你您输入的标题在语言文件的举报原因章节已经设置过，本地化的标题和描述将自动启用。',

	'NO_REASON'					=> '无法找到"原因"。',
	'NO_REASON_INFO'			=> '您必须给这个原因指定标题和描述。',
	'NO_REMOVE_DEFAULT_REASON'	=> '您不能删除默认的原因 "其他"。',

	'REASON_ADD'				=> '添加举报/否决原因',
	'REASON_ADDED'				=> '举报/否决原因添加完成。',
	'REASON_ALREADY_EXIST'		=> '使用这个标题的原因已经存在，请输入另一个标题。',
	'REASON_DESCRIPTION'		=> '原因描述',
	'REASON_DESC_TRANSLATED'	=> '显示原因描述',
	'REASON_EDIT'				=> '编辑举报/否决原因',
	'REASON_EDIT_EXPLAIN'		=> '这里您可以添加或编辑原因。如果原因本有当地语言的版本则自动显示当地语言。',
	'REASON_REMOVED'			=> '举报/否决删除完成。',
	'REASON_TITLE'				=> '原因标题',
	'REASON_TITLE_TRANSLATED'	=> '显示的原因标题',
	'REASON_UPDATED'			=> '举报/否决原因修改完成。',

	'USED_IN_REPORTS'		=> '举报中使用',
));
