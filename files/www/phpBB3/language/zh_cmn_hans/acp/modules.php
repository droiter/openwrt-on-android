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
	'ACP_MODULE_MANAGEMENT_EXPLAIN'	=> '这里您可以管理各种模块。请注意管理员控制面板使用的是三层菜单结构 (分类 -> 分类 -> 模块) 而其他使用的是两层菜单结构 (分类 -> 模块)。请同样注意的是您有可能会把自己给锁在模块外面，如果您删除或禁用了涉及模块自身管理的模块。',
	'ADD_MODULE'					=> '添加模块',
	'ADD_MODULE_CONFIRM'			=> '您确认用选中的模式添加选中的模块吗？',
	'ADD_MODULE_TITLE'				=> '添加模块',

	'CANNOT_REMOVE_MODULE'	=> '无法删除模块，因为有下层的引用。请先删除或移除所有下层引用。',
	'CATEGORY'				=> '分类',
	'CHOOSE_MODE'			=> '选择模块模式',
	'CHOOSE_MODE_EXPLAIN'	=> '选择使用的模块模式.',
	'CHOOSE_MODULE'			=> '选择模块',
	'CHOOSE_MODULE_EXPLAIN'	=> '选择这个模块调用的文件。',
	'CREATE_MODULE'			=> '创建新模块',

	'DEACTIVATED_MODULE'	=> '冻结模块',
	'DELETE_MODULE'			=> '删除模块',
	'DELETE_MODULE_CONFIRM'	=> '您确认删除这个模块吗？',

	'EDIT_MODULE'			=> '编辑模块',
	'EDIT_MODULE_EXPLAIN'	=> '这里您可以输入模块特有设定',

	'HIDDEN_MODULE'			=> '隐藏模块',

	'MODULE'					=> '模块',
	'MODULE_ADDED'				=> '模块添加完成.',
	'MODULE_DELETED'			=> '模块移除完成.',
	'MODULE_DISPLAYED'			=> '显示模块',
	'MODULE_DISPLAYED_EXPLAIN'	=> '如果您不希望显示这个模块又想使用它，设置为否。',
	'MODULE_EDITED'				=> '模块编辑完成。',
	'MODULE_ENABLED'			=> '模块启用',
	'MODULE_LANGNAME'			=> '模块语言名称',
	'MODULE_LANGNAME_EXPLAIN'	=> '输入显示的模块名称。如果语言由语言文件提供请使用语言常数。',
	'MODULE_TYPE'				=> '模块类型',

	'NO_CATEGORY_TO_MODULE'	=> '无法转变分类为模块。请先删除或移除所有子模块。',
	'NO_MODULE'				=> '没有发现模块。',
	'NO_MODULE_ID'			=> '没有指定模块ID。',
	'NO_MODULE_LANGNAME'	=> '没有指定模块语言。',
	'NO_PARENT'				=> '没有父类',

	'PARENT'				=> '父类',
	'PARENT_NO_EXIST'		=> '父类不存在。',

	'SELECT_MODULE'			=> '选择一个模块',
));
