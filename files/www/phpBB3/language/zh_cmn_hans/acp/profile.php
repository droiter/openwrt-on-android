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

// Custom profile fields
$lang = array_merge($lang, array(
	'ADDED_PROFILE_FIELD'	=> '成功添加用户自定义资料栏目。',
	'ALPHA_DOTS'			=> '字母数字和点(句号)',
	'ALPHA_ONLY'			=> '只允许数字与字母',
	'ALPHA_SPACERS'			=> '数字，字母和空格',
	'ALPHA_UNDERSCORE'		=> '字母数字和下划线',
	'ALPHA_PUNCTUATION'		=> '字母数字带逗号、点、下划线和短横以字母开始',
	'ALWAYS_TODAY'			=> '总是当前日期',

	'BOOL_ENTRIES_EXPLAIN'	=> '输入您的选项',
	'BOOL_TYPE_EXPLAIN'		=> '定义类型，勾选框或是单选按钮。只有在选中给定用户后才会显示勾选框. 这时候<strong>第二</strong> 语言选项将被使用. 而单选框的显示则和它们的值无关.',

	'CHANGED_PROFILE_FIELD'		=> '用户个人资料栏目更新完成.',
	'CHARS_ANY'					=> '任何字符',
	'CHECKBOX'					=> '勾选框',
	'COLUMNS'					=> '列',
	'CP_LANG_DEFAULT_VALUE'		=> '默认值',
	'CP_LANG_EXPLAIN'			=> '栏目描述',
	'CP_LANG_EXPLAIN_EXPLAIN'	=> '显示给用户的栏目描述',
	'CP_LANG_NAME'				=> '显示给用户的名称/标题',
	'CP_LANG_OPTIONS'			=> '选项',
	'CREATE_NEW_FIELD'			=> '创建新栏目',
	'CUSTOM_FIELDS_NOT_TRANSLATED'	=> '至少有一列资料字段还没被翻译，请输入所需信息点 “翻译” 链接。',

	'DEFAULT_ISO_LANGUAGE'			=> '默认语言 [%s]',
	'DEFAULT_LANGUAGE_NOT_FILLED'	=> '尚未输入此栏目对应的默认语言条目。',
	'DEFAULT_VALUE'					=> '默认值',
	'DELETE_PROFILE_FIELD'			=> '删除资料栏目',
	'DELETE_PROFILE_FIELD_CONFIRM'	=> '您确认删除这个资料栏目吗？',
	'DISPLAY_AT_PROFILE'			=> '在用户的控制面板中显示',
	'DISPLAY_AT_PROFILE_EXPLAIN'	=> '用户可以在用户控制面板中更改这个资料栏。',
	'DISPLAY_AT_REGISTER'			=> '注册时显示',
	'DISPLAY_AT_REGISTER_EXPLAIN'	=> '启用这个选项后，用户注册时会显示此栏目。',
	'DISPLAY_ON_MEMBERLIST'			=> '显示在会员列表屏幕',
	'DISPLAY_ON_MEMBERLIST_EXPLAIN'	=> '如果启用此选项，字段将显示在用户行在会员屏幕。',
	'DISPLAY_ON_PM'					=> '显示在查看私信屏幕',
	'DISPLAY_ON_PM_EXPLAIN'			=> '如果启用此选项，自动将显示在迷你资料在私信屏幕。',
	'DISPLAY_ON_VT'					=> '于主题阅读页面显示',
	'DISPLAY_ON_VT_EXPLAIN'			=> '启用后, 此栏目会显示于主题阅读页面中的用户资料中.',
	'DISPLAY_PROFILE_FIELD'			=> '用户公开资料中显示',
	'DISPLAY_PROFILE_FIELD_EXPLAIN'	=> '用户资料栏将显示在加载的设置中允许的页面。设置此值为 “否” 将在浏览主题，个人资料和用户列表页面隐藏此资料栏。',
	'DROPDOWN_ENTRIES_EXPLAIN'		=> '输入您的选项, 一行一个',

	'EDIT_DROPDOWN_LANG_EXPLAIN'	=> '请注意您可以更改选项内容，也可以在最后添加新选项，但是不推荐在现有的选项中间添加新选项或删除选项 - 这样可能会给用户显示错误的选项。删除选项时需要从最后一个选项开始，选过这个选项的用户将自动重置到默认选项上。',
	'EMPTY_FIELD_IDENT'				=> '栏目定义空白',
	'EMPTY_USER_FIELD_NAME'			=> '请输入栏目名称/标题',
	'ENTRIES'						=> '条目',
	'EVERYTHING_OK'					=> '一切正常',

	'FIELD_BOOL'				=> '正反判断 (是/否)',
	'FIELD_CONTACT_DESC'		=> '联系描述',
	'FIELD_CONTACT_URL'			=> '联系链接',
	'FIELD_DATE'				=> '日期',
	'FIELD_DESCRIPTION'			=> '栏目描述',
	'FIELD_DESCRIPTION_EXPLAIN'	=> '显示给用户的栏目描述',
	'FIELD_DROPDOWN'			=> '下拉框',
	'FIELD_GOOGLEPLUS'			=> 'Google+',
	'FIELD_IDENT'				=> '栏目定义',
	'FIELD_IDENT_ALREADY_EXIST'	=> '选择的栏目定义已经存在，请选择另外一个名称。',
	'FIELD_IDENT_EXPLAIN'		=> '栏目定义是在数据库和模板中区分栏目的标记。',
	'FIELD_INT'					=> '数字',
	'FIELD_IS_CONTACT'			=> '显示字段为一个联系字段',
	'FIELD_IS_CONTACT_EXPLAIN'	=> '联系字段显示在用户资料的联系部分，和显示在迷你资料里的不同。你可以使用 <samp>%s</samp> 作为一个占位符变量可被用户提供的值取代。',
	'FIELD_LENGTH'				=> '输入框长度',
	'FIELD_NOT_FOUND'			=> '资料栏目未找到.',
	'FIELD_STRING'				=> '单行文字输入框',
	'FIELD_TEXT'				=> '文本编辑框',
	'FIELD_TYPE'				=> '栏目类型',
	'FIELD_TYPE_EXPLAIN'		=> '设定栏目类型后将不可修改。',
	'FIELD_URL'					=> 'URL (链接)',
	'FIELD_VALIDATION'			=> '栏目验证',
	'FIRST_OPTION'				=> '第一选项',

	'HIDE_PROFILE_FIELD'			=> '隐藏资料栏目',
	'HIDE_PROFILE_FIELD_EXPLAIN'	=> '只有管理员和版主和用户本人可以查看/填写这个栏目。如果显示在用户控制面板的选项未启用，用户将无法看到这个栏目，并且只能由管理员来修改。',

	'INVALID_CHARS_FIELD_IDENT'	=> '栏目定义只能包含小写 a-z 和 _',
	'INVALID_FIELD_IDENT_LEN'	=> '栏目定义字符长度不能超过17',
	'ISO_LANGUAGE'				=> '语言 [%s]',

	'LANG_SPECIFIC_OPTIONS'		=> '语言特定选项 [<strong>%s</strong>]',

	'LETTER_NUM_DOTS'			=> '任何字母、数字和点(句号)',
	'LETTER_NUM_ONLY'			=> '任何字母和数字',
	'LETTER_NUM_PUNCTUATION'	=> '任何字母、数字、逗号、点、下划线和短横并以字母开始',
	'LETTER_NUM_SPACERS'		=> '任何字母、数字和空格',
	'LETTER_NUM_UNDERSCORE'		=> '任何字母、数字和下划线',

	'MAX_FIELD_CHARS'		=> '最大字符串长度',
	'MAX_FIELD_NUMBER'		=> '最大允许数字',
	'MIN_FIELD_CHARS'		=> '最小字符串长度',
	'MIN_FIELD_NUMBER'		=> '最小允许数字',

	'NO_FIELD_ENTRIES'			=> '没有定义的条目',
	'NO_FIELD_ID'				=> '没有指定栏目ID.',
	'NO_FIELD_TYPE'				=> '没有指定栏目类型.',
	'NO_VALUE_OPTION'			=> '空白输入的等价选项',
	'NO_VALUE_OPTION_EXPLAIN'	=> '空条目的值。如果栏目是必填的而且用户选择了这个选项, 用户将得到一个错误提示',
	'NUMBERS_ONLY'				=> '只允许数字 (0-9)',

	'PROFILE_BASIC_OPTIONS'		=> '基本选项',
	'PROFILE_FIELD_ACTIVATED'	=> '资料栏目激活完成。',
	'PROFILE_FIELD_DEACTIVATED'	=> '资料栏目已经冻结。',
	'PROFILE_LANG_OPTIONS'		=> '特定语言选项',
	'PROFILE_TYPE_OPTIONS'		=> '特定资料类型选项',

	'RADIO_BUTTONS'				=> '单选框',
	'REMOVED_PROFILE_FIELD'		=> '资料栏目删除完成。',
	'REQUIRED_FIELD'			=> '必填栏目',
	'REQUIRED_FIELD_EXPLAIN'	=> '强制用户填写的栏目。这将在注册过程和用户控制面板中显示这个栏目，如果设置为注册过程中不显示，那么在用户编辑资料时会被要求填写。',
	'ROWS'						=> '行',

	'SAVE'							=> '保存',
	'SECOND_OPTION'					=> '第二选项',
	'SHOW_NOVALUE_FIELD'			=> '没有值时显示字段',
	'SHOW_NOVALUE_FIELD_EXPLAIN'	=> '决定字段是否显示，如果在可选字段或必选字段没有值的情况下。',
	'STEP_1_EXPLAIN_CREATE'			=> '这里您可以输入新资料栏的第一个基本参数，以便于下一步填写和设定更详细的栏目资料。',
	'STEP_1_EXPLAIN_EDIT'			=> '这里您可以更改资料栏的基本参数。相关的选项将在第二步重新计算。',
	'STEP_1_TITLE_CREATE'			=> '添加资料栏',
	'STEP_1_TITLE_EDIT'				=> '编辑资料栏',
	'STEP_2_EXPLAIN_CREATE'			=> '这里您可以设定一些综合选项。还可以预览新栏目，您需要多次调试以使栏目符合自己的要求。',
	'STEP_2_EXPLAIN_EDIT'			=> '这里您可以更改一些综合选项。还可以预览更改的栏目, 多次调试直至满意为止。<br /><strong>请注意更改资料栏将不会影响用户已经输入的数据。</strong>',
	'STEP_2_TITLE_CREATE'			=> '特定资料类型选项',
	'STEP_2_TITLE_EDIT'				=> '特定资料类型选项',
	'STEP_3_EXPLAIN_CREATE'			=> '因为论坛安装了多种语言，您必须填写下面的语言设置。资料栏将工作于默认的语言下，您可以以后再完成剩下的语言设置。',
	'STEP_3_EXPLAIN_EDIT'			=> '因为论坛安装了多种语言，您必须填写下面的语言设置。资料栏将工作于默认的语言下，您可以以后再完成剩下的语言设置。',
	'STEP_3_TITLE_CREATE'			=> '保留语言定义',
	'STEP_3_TITLE_EDIT'				=> '语言定义',
	'STRING_DEFAULT_VALUE_EXPLAIN'	=> '输入默认显示字符。如果您希望开始显示空白则留空。',

	'TEXT_DEFAULT_VALUE_EXPLAIN'	=> '输入显示的默认文字和默认值。如果留空则显示空白。',
	'TRANSLATE'						=> '翻译',

	'USER_FIELD_NAME'	=> '显示给用户的栏目名称/标题',

	'VISIBILITY_OPTION'				=> '可视化选项',
));
