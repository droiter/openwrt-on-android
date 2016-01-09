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
	'ADMIN_CONFIG'				=> '管理员设置',
	'ADMIN_PASSWORD'			=> '管理员密码',
	'ADMIN_PASSWORD_CONFIRM'	=> '确认管理员密码',
	'ADMIN_PASSWORD_EXPLAIN'	=> '请输入一个6到30位的密码。',
	'ADMIN_TEST'				=> '检测管理员设置',
	'ADMIN_USERNAME'			=> '管理员用户名',
	'ADMIN_USERNAME_EXPLAIN'	=> '请输入一个3到20位的用户名。',
	'APP_MAGICK'				=> 'Imagemagick 支持【附件】',
	'AUTHOR_NOTES'				=> '作者提示<br />» %s',
	'AVAILABLE'					=> '可用',
	'AVAILABLE_CONVERTORS'		=> '可用的转换程序',

	'BEGIN_CONVERT'					=> '开始转换',
	'BLANK_PREFIX_FOUND'			=> '对数据库的扫描显示存在可用的无前缀表单。',
	'BOARD_NOT_INSTALLED'			=> '没有发现已安装的 phpBB',
	'BOARD_NOT_INSTALLED_EXPLAIN'	=> '为了进行转换您必须预先安装一个全新的 phpBB3。请注意新的安装数据要和旧的数据存在同一个数据库里，您是否要进行【<a href="%s">全新安装</a>】？',
	'BACKUP_NOTICE'					=> '升级前请备份您的论坛，防止升级过程中出现问题。',

	'CATEGORY'					=> '分区',
	'CACHE_STORE'				=> '缓存类型',
	'CACHE_STORE_EXPLAIN'		=> '缓存的物理地址，请优先使用文件系统。',
	'CAT_CONVERT'				=> '转换操作',
	'CAT_INSTALL'				=> '全新安装',
	'CAT_OVERVIEW'				=> '综合信息',
	'CAT_UPDATE'				=> '升级',
	'CHANGE'					=> '改变',
	'CHECK_TABLE_PREFIX'		=> '请检查您的表单前缀后重试',
	'CLEAN_VERIFY'				=> '正在清理并校验最终结构',
	'CLEANING_USERNAMES'		=> '正在清理用户名',
	'COLLIDING_CLEAN_USERNAME'	=> '<strong>%s</strong> 清理自用户:',
	'COLLIDING_USERNAMES_FOUND'	=> '在旧的论坛中发现用户名冲突。为了完成论坛转换，请删除或重命名这些用户使得只有一个使用该用户名的用户存在。',
	'COLLIDING_USER'			=> '» 用户 id: <strong>%d</strong> 用户名: <strong>%s</strong> (%d posts)',
	'CONFIG_CONVERT'			=> '正在转换配置内容',
	'CONFIG_FILE_UNABLE_WRITE'	=> '写入配置文件失败，创建这个文件的其他方式如下：',
	'CONFIG_FILE_WRITTEN'		=> '写入配置文件成功，您现在可以继续进行下一步。',
	'CONFIG_PHPBB_EMPTY'		=> '缺少 phpBB3 的配置信息“%s”。',
	'CONFIG_RETRY'				=> '重试',
	'CONTINUE_CONVERT'			=> '继续转换',
	'CONTINUE_CONVERT_BODY'		=> '检测到曾经进行过转换，您可以选择进行重新转换还是继续上一次的转换。',
	'CONTINUE_LAST'				=> '继续进行操作',
	'CONTINUE_OLD_CONVERSION'	=> '继续进行以前的转换',
	'CONVERT'					=> '转换',
	'CONVERT_COMPLETE'			=> '转换完成',
	'CONVERT_COMPLETE_EXPLAIN'	=> '您已经将您的论坛成功转换为 phpBB 3.1，您现在可以登录并<a href="../">访问您的论坛</a>。请在开启新论坛前确认参数都已经正确设置。别忘了phpBB的在线使用帮助有<a href="https://www.phpbb.com/support/docs/en/3.1/ug/">文档</a>和<a href="https://www.phpbb.com/phpBB/viewforum.php?f=466">支持论坛</a>。',
	'CONVERT_INTRO'				=> '欢迎使用 phpBB 统一转换框架',
	'CONVERT_INTRO_BODY'		=> '这里您可以从其它（已安装）的论坛导入数据，下表列出的是所有可用的转换模块。如果其中没有您想要的转换模块，请访问我们的网站，那里可能会提供更多的转换模块下载。',
	'CONVERT_NEW_CONVERSION'	=> '新的转换',
	'CONVERT_NOT_EXIST'			=> '指定的转换程序不存在',
	'CONVERT_OPTIONS'			=> '选项',
	'CONVERT_SETTINGS_VERIFIED'	=> '您输入的信息已经被校验，要开始转换，请点击下面的按钮。',
	'CONV_ERR_FATAL'					=> '严重错误',

	'CONV_ERROR_ATTACH_FTP_DIR'			=> '原论坛允许附件使用FTP上载，请禁用FTP上载并确认设定了有效的上载目录，所有附件将被拷贝到这个新的web可访问的目录。这些完成后，请重新开始转换程序。',
	'CONV_ERROR_CONFIG_EMPTY'			=> '转换程序没有可用的配置信息。',
	'CONV_ERROR_FORUM_ACCESS'			=> '无法得到版面的访问信息。',
	'CONV_ERROR_GET_CATEGORIES'			=> '无法得到分区信息。',
	'CONV_ERROR_GET_CONFIG'				=> '无法找到您的版面设置信息。',
	'CONV_ERROR_COULD_NOT_READ'			=> '无法读写 “%s”。',
	'CONV_ERROR_GROUP_ACCESS'			=> '无法得到用户组权限信息。',
	'CONV_ERROR_INCONSISTENT_GROUPS'	=> '在add_bots()中检测到用户组表单中的矛盾数据 - 如果手工设置过您需要添加所有特殊用户组。',
	'CONV_ERROR_INSERT_BOT'				=> '无法在用户组中添加bot。',
	'CONV_ERROR_INSERT_BOTGROUP'		=> '无法在bots表单中添加bot。',
	'CONV_ERROR_INSERT_USER_GROUP'		=> '无法在用户组表单中添加用户。',
	'CONV_ERROR_MESSAGE_PARSER'			=> '内容解析错误',
	'CONV_ERROR_NO_AVATAR_PATH'			=> '开发者注意: 您必须指定 $convertor[\'avatar_path\'] 以使用 %s。',
	'CONV_ERROR_NO_FORUM_PATH'			=> '指向源论坛的相对路径未指定。',
	'CONV_ERROR_NO_GALLERY_PATH'		=> '开发者注意: 您必须指定 $convertor[\'avatar_gallery_path\'] 以使用 %s。',
	'CONV_ERROR_NO_GROUP'				=> '用户组 "%1$s" 在 %2$s 中无法找到。',
	'CONV_ERROR_NO_RANKS_PATH'			=> '开发者注意: 您必须指定 $convertor[\'ranks_path\'] 以使用 %s。',
	'CONV_ERROR_NO_SMILIES_PATH'		=> '开发者注意: 您必须指定 $convertor[\'smilies_path\'] 以使用 %s。',
	'CONV_ERROR_NO_UPLOAD_DIR'			=> '开发者注意: 您必须指定 $convertor[\'upload_path\'] 以使用 %s。',
	'CONV_ERROR_PERM_SETTING'			=> '无法添加/更改权限设置。',
	'CONV_ERROR_PM_COUNT'				=> '无法选择站内短信数量。',
	'CONV_ERROR_REPLACE_CATEGORY'		=> '无法在新论坛中替换旧分区。',
	'CONV_ERROR_REPLACE_FORUM'			=> '无法在新论坛中替换旧版面。',
	'CONV_ERROR_USER_ACCESS'			=> '无法得到用户权限信息。',
	'CONV_ERROR_WRONG_GROUP'			=> '错误的用户组 "%1$s" 定义在 %2$s。',
	'CONV_OPTIONS_BODY'					=> '这个页面用于收集访问原论坛所需要的数据。输入原论坛数据库参数，转换程序不会对原数据库作任何更改。原论坛必须暂时停用以确保转换能正确完成。',
	'CONV_SAVED_MESSAGES'				=> '保存信息',

	'COULD_NOT_COPY'			=> '无法复制文件 <strong>%1$s</strong> 到 <strong>%2$s</strong><br /><br />请检查目标文件夹是否存在并是否可以被 web 服务器写入',
	'COULD_NOT_FIND_PATH'		=> '无法找到您以前论坛的路径。 请检查您的设定并再试一次。<br />» 指定的路径是 %s',

	'DBMS'						=> '数据库类型',
	'DB_CONFIG'					=> '数据库设置',
	'DB_CONNECTION'				=> '数据库连接',
	'DB_ERR_INSERT'				=> '执行 <code>INSERT</code> 语句时发生错误。',
	'DB_ERR_LAST'				=> '执行 <var>query_last</var> 时发生错误。',
	'DB_ERR_QUERY_FIRST'		=> '执行 <var>query_first</var> 时发生错误。',
	'DB_ERR_QUERY_FIRST_TABLE'	=> '执行 <var>query_first</var> 时发生错误：%s (“%s”)。',
	'DB_ERR_SELECT'				=> '执行 <code>SELECT</code> 语句时发生错误。',
	'DB_HOST'					=> '数据库服务器地址，或 DSN',
	'DB_HOST_EXPLAIN'			=> 'DSN代表数据源名称，它只与ODBC安装有关。对于PostgreSQL，需要通过UNIX domain socket用localhost或通过TCP用127.0.0.1连接本地服务器。对于SQLite，需要输入数据文件的完整路径。',
	'DB_NAME'					=> '数据库名称',
	'DB_PASSWORD'				=> '数据库密码',
	'DB_PORT'					=> '数据库服务器端口',
	'DB_PORT_EXPLAIN'			=> '不用填写，除非您确定服务器监听一个非标准端口。',
	'DB_UPDATE_NOT_SUPPORTED'	=> '非常抱歉，升级程序无法升级版本低于 “%1$s” 的phpBB论坛。您当前使用的论坛版本为 “%2$s”。请升级至一个较新的版本后再执行此升级程序。如果您需要帮助，请到phpBB.com的用户支持版面提出。',
	'DB_USERNAME'				=> '数据库用户名',
	'DB_TEST'					=> '连接检测',
	'DEFAULT_LANG'				=> '默认论坛语言',
	'DEFAULT_PREFIX_IS'			=> '根据提供的前缀，转换器无法找到可用的表单。请确认您在同一数据库中存在旧的表单。%1$s 默认的表单前缀是 <strong>%2$s</strong>',
	'DEV_NO_TEST_FILE'			=> '在转换器中没有指定test_file变量的值。如果您是转换器的使用者，您不应该看到这个错误，请联络转换器的作者并报告这个错误。如果您是转换器的作者，您必须指定一个源论坛中存在的文件名称，使得路径校验能正常进行。',
	'DIRECTORIES_AND_FILES'		=> '配置目录与文件',
	'DISABLE_KEYS'				=> '禁用关键字..。',
	'DLL_FTP'					=> 'FTP 支持【 安装phpBB 】',
	'DLL_GD'					=> 'GD 图形支持【 图形确认码 】',
	'DLL_MBSTRING'				=> '多字节字符支持',
	'DLL_MSSQL'					=> 'MSSQL Server 2000+',
	'DLL_MSSQL_ODBC'			=> 'MSSQL Server 2000+ via ODBC',
	'DLL_MSSQLNATIVE'         => 'MSSQL Server 2005+ [ Native ]',
	'DLL_MYSQL'					=> 'MySQL',
	'DLL_MYSQLI'				=> 'MySQL (使用 MySQLi 扩展)',
	'DLL_ORACLE'				=> 'Oracle',
	'DLL_POSTGRES'				=> 'PostgreSQL',
	'DLL_SQLITE'				=> 'SQLite 2',
	'DLL_SQLITE3'				=> 'SQLite 3',
	'DLL_XML'					=> 'XML 支持【 Jabber 】',
	'DLL_ZLIB'					=> 'zlib 压缩支持【 压缩文件：.gz .tar.gz .zip 】',
	'DL_CONFIG'					=> '下载配置',
	'DL_CONFIG_EXPLAIN'			=> '您应该将完整的 config.php 下载到您的个人电脑中，然后手动上传它，覆盖 phpBB 3.1 根目录中已经存在的文件。请注意以 ASCII 格式上传（如果您不确定如何办到，请阅读您的FTP软件文档）。上传完成之后，请点击 “完成” 以进行下一步。',
	'DL_DOWNLOAD'				=> '下载',
	'DONE'						=> '完成',

	'ENABLE_KEYS'				=> '重新启用关键字.这需要等待一小会儿。',

	'FILES_OPTIONAL'			=> '可选的文件与目录',
	'FILES_OPTIONAL_EXPLAIN'	=> '<strong>可选的</strong> - 这些文件、目录及权限不是必需的。如果它们不存在或不可写入，安装程序将会尝试使用一些技术手段来创建它们。但如果存在，将会使安装加速。',
	'FILES_REQUIRED'			=> '文件与目录',
	'FILES_REQUIRED_EXPLAIN'	=> '<strong>必需的</strong> - 为了正常运行，phpBB需要针对特定文件或目录的写入权限。如果下面出现“不存在”，您就需要创建相应的文件或目录；如果出现“不可写入”，您就需要改变相应的文件或目录的权限来允许phpBB对其进行写入操作。',
	'FILLING_TABLE'				=> '正在填充表格：<strong>%s</strong>',
	'FILLING_TABLES'			=> '正在填充表格',
		
	'FINAL_STEP'				=> '正在执行最后一步',
	'FORUM_ADDRESS'				=> '论坛地址',
	'FORUM_ADDRESS_EXPLAIN'		=> '这是指向您的论坛根目录的超链接地址，例如：<samp>http://www.example.com/phpBB3/</samp>。如果您填入了一个地址，所有在信件、短信及签名档中与前面示例相同的地址都将被替换为新的论坛地址。',
	'FORUM_PATH'				=> '论坛路径',
	'FORUM_PATH_EXPLAIN'		=> '这是在磁盘上对应于您<strong>现在的phpBB3根目录</strong>的原论坛 <strong>相对</strong> 路径',
	'FOUND'						=> '存在',
	'FTP_CONFIG'				=> '通过 FTP 传输配置',
	'FTP_CONFIG_EXPLAIN'		=> 'phpBB已经在服务器上检测到 FTP 模块，如果您希望，您可以尝试通过它安装您的 config.php。您将需要提供以下信息，请注意用户名和密码是用来登入服务器的（如果您不确定是什么，联络您的服务供应商）！',
	'FTP_PATH'					=> 'FTP 路径',
	'FTP_PATH_EXPLAIN'			=> '这是从FTP根目录至phpBB目录的相对路径，例如：htdocs/phpBB3/',
	'FTP_UPLOAD'				=> '上传',

	'GPL'						=> 'General Public License(GPL协议)',
	
	'INITIAL_CONFIG'			=> '基本设置',
	'INITIAL_CONFIG_EXPLAIN'	=> '安装程序认为您的服务器可以运行phpBB，您需要提供一些具体信息。如果您不知道如何连接您的数据库，请首先考虑联络您的服务供应商，或是访问phpBB支持论坛。在继续下一步之前，请仔细检查您输入的信息。',
	'INSTALL_CONGRATS'			=> '恭喜！',
	'INSTALL_CONGRATS_EXPLAIN'	=> '
		您已经成功安装 phpBB %1$s。从这里，您可以通过以下选项设置您的 phpBB3:</p>
		<h2>转换一个已经存在的论坛到 phpBB3</h2>
		<p>phpBB 统一转换框架支持从 phpBB 2.0.x 和其他论坛软件到 phpBB3 的转换。 如果您有一个旧的论坛需要转换，请 <a href="%2$s">运行转换程序</a>。</p>
		<h2>使用您的 phpBB3!</h2>
		<p>点击下面的按钮将带您到管理员控制面板(ACP)下提交统计数据的表单，我们将十分感激您能发送这些信息来帮助我们。然后您应该花一些时间检查设置选项是否对您可用。别忘了可以使用在线帮助有<a href="https://www.phpbb.com/support/docs/en/3.1/ug/">文档</a>，<a href="%3$s">README</a>， <a href="https://www.phpbb.com/community/viewforum.php?f=466">支持论坛</a>。</p><p><strong>请在使用论坛前删除，移动或重命名install文件夹。如果这个文件夹存在，只有管理员控制面板(ACP)能访问。</strong>',
	'INSTALL_INTRO'				=> '欢迎安装！',

	'INSTALL_INTRO_BODY'		=> '使用这个选项，应该可以在您的服务器上安装 phpBB.</p><p>为了继续安装，您需要知道您的数据库设置。如果您不清楚这些，请联络您的web空间提供者。没有这些信息安装将不能继续。您需要:</p>

	<ul>
		<li>数据库类型 - 您将使用的数据库.</li>
		<li>数据库服务器主机名或 DSN - 数据库服务器地址.</li>
		<li>数据库服务器端口 - 数据库服务器端口 (一般情况下不需要输入).</li>
		<li>数据库名称 - 数据库服务器上的数据库名称.</li>
		<li>数据库用户名和密码 - 用于登录并访问上述数据库的用户资料.</li>
	</ul>

	<p><strong>注意:</strong> 如果您使用 SQLite，您应该在DSN框中输入数据库的完整路径并保持用户名和密码空白。为了安全的原因， 您应该确保数据库文件不会存放在一个可以被公众访问的文件夹下。</p>

	<p>phpBB3 支持如下的数据库:</p>
	<ul>
		<li>MySQL 3.23 或更高 (支持MySQLi)</li>
		<li>PostgreSQL 8.3+</li>
		<li>SQLite 2.8.2+</li>
		<li>SQLite 3.6.15+</li>
		<li>MS SQL Server 2000 或更高 (直接访问或通过 ODBC)</li>
		<li>MS SQL Server 2005 或更高 (native)</li>
		<li>Oracle</li>
	</ul>

	<p>只有您的服务器支持的数据库才会被显示。',
	'INSTALL_INTRO_NEXT'		=> '要开始安装，点击下面的按钮。',
	'INSTALL_LOGIN'				=> '登入论坛',
	'INSTALL_NEXT'				=> '下一步',
	'INSTALL_NEXT_FAIL'			=> '某些检测未能通过，您应该在进行下一步之前修正这些问题，不然可能会导致安装无法完成。',
	'INSTALL_NEXT_PASS'			=> '全部的基础检测都已经通过，您可以进行下一步了。如果您改变了一些配置比如权限、模块等等，您可以选择重新检测。',
	'INSTALL_PANEL'				=> '安装面板',
	'INSTALL_SEND_CONFIG'		=> '很抱歉，phpBB没能将配置信息直接写入您的 config.php 文件。这可能是由于此文件不存在或不可写入。下面将给出一些选项帮助您完成设置 config.php。',
	'INSTALL_START'				=> '开始安装',
	'INSTALL_TEST'				=> '重新检测',
	'INST_ERR'					=> '安装进程出错',
	'INST_ERR_DB_CONNECT'		=> '连接数据库失败，错误信息如下',
	'INST_ERR_DB_FORUM_PATH'	=> '指定的数据库文件位于论坛目录内，您应该把它放在一个无法通过网络访问的位置。',
	'INST_ERR_DB_INVALID_PREFIX'=> '您输入的前缀无效。必须以字母开头并且只包含字母、数字和下划线。',
	'INST_ERR_DB_NO_ERROR'		=> '没有得到相应的错误信息',
	'INST_ERR_DB_NO_MYSQLI'		=> '服务器内安装的 MySQL 版本与您选择的 “MySQL (使用 MySQLi 扩展)” 选项不兼容，请尝试 “MySQL” 选项。',
	'INST_ERR_DB_NO_SQLITE'		=> '您安装的 SQLite 版本太古老，请升级至最低 2.8.2 版。',
	'INST_ERR_DB_NO_SQLITE3'	=> '你安装的SQLite扩展版本太旧，必须升级到至少3.6.15。',
	'INST_ERR_DB_NO_ORACLE'		=> '服务器内安装的 Oracle 版本需要您将参数 <var>NLS_CHARACTERSET</var> 设置为 <var>UTF8</var>。请设置此参数，或将 Oracle 升级至最低 9.2 版。',
	'INST_ERR_DB_NO_POSTGRES'	=> '您选择的数据库不是 <var>UNICODE</var> 或 <var>UTF8</var> 编码，请使用 <var>UNICODE</var> 或 <var>UTF8</var> 编码的数据库。',
	'INST_ERR_DB_NO_NAME'		=> '没有指定数据库名称',
	'INST_ERR_EMAIL_INVALID'	=> '您输入的Email地址无效',
	'INST_ERR_EMAIL_MISMATCH'	=> '您输入的两个Email地址互相不匹配',
	'INST_ERR_FATAL'			=> '安装进程出现致命错误',
	'INST_ERR_FATAL_DB'			=> '数据库出现了一个致命且不可恢复的错误。这可能是由于您指定的用户没有 <code>CREATE TABLES</code> 或 <code>INSERT</code> 的权限等等，下面可能会给出进一步的信息。请首先考虑联络您的服务供应商，或是访问phpBB支持论坛，以得到进一步帮助。',
	'INST_ERR_FTP_PATH'			=> '无法转换到指定目录，请检查路径信息。',
	'INST_ERR_FTP_LOGIN'		=> '无法登入 FTP 服务器，请检查用户名和密码。',
	'INST_ERR_MISSING_DATA'		=> '您必须填完此表格的全部单元',
	'INST_ERR_NO_DB'			=> '无法找到指定数据库类型的 PHP 模块',
	'INST_ERR_PASSWORD_MISMATCH'	=> '您输入的两个密码互相不匹配。',
	'INST_ERR_PASSWORD_TOO_LONG'	=> '您输入的密码过长，请输入最多30个字符。',
	'INST_ERR_PASSWORD_TOO_SHORT'	=> '您输入的密码太短，请输入最少6个字符。',
	'INST_ERR_PREFIX'			=> '已经存在使用指定前缀的表格，请指定另一个。',
	'INST_ERR_PREFIX_INVALID'	=> '您指定的表格前缀无效，请尝试另一个，去掉诸如连字符之类的字符。',
	'INST_ERR_PREFIX_TOO_LONG'	=> '您指定的表格前缀过长，上限为 %d 个字符。',
	'INST_ERR_USER_TOO_LONG'	=> '您输入的用户名过长，请输入最多20个字符。',
	'INST_ERR_USER_TOO_SHORT'	=> '您输入的用户名太短，请输入最少3个字符。',
	'INVALID_PRIMARY_KEY'		=> '无效的主键 : %s',

	'LONG_SCRIPT_EXECUTION'		=> '请注意这需要一段时间……请不要中断脚本。',

	// mbstring
	'MBSTRING_CHECK'						=> '<samp>mbstring</samp> 插件检测',
	'MBSTRING_CHECK_EXPLAIN'				=> '<samp>mbstring</samp> 是一个 PHP 扩展插件，它提供多字节字符串处理功能。某些 mbstring 的功能与phpBB不兼容，因此必须被禁用。',
	'MBSTRING_FUNC_OVERLOAD'				=> '程序过载',
	'MBSTRING_FUNC_OVERLOAD_EXPLAIN'		=> '<var>mbstring.func_overload</var> 必须被设置为 0 或 4',
	'MBSTRING_ENCODING_TRANSLATION'			=> '字符编码',
	'MBSTRING_ENCODING_TRANSLATION_EXPLAIN'	=> '<var>mbstring.encoding_translation</var> 必须被设置为 0',
	'MBSTRING_HTTP_INPUT'					=> 'HTTP 输入字符转换',
	'MBSTRING_HTTP_INPUT_EXPLAIN'			=> '<var>mbstring.http_input</var> 必须被设置为 <samp>pass</samp>',
	'MBSTRING_HTTP_OUTPUT'					=> 'HTTP 输出字符转换',
	'MBSTRING_HTTP_OUTPUT_EXPLAIN'			=> '<var>mbstring.http_output</var> 必须被设置为 <samp>pass</samp>',

	'MAKE_FOLDER_WRITABLE'		=> '请首先确保此文件夹存在并且可以被网络服务器写入，然后重试：<br />»<strong>%s</strong>',
	'MAKE_FOLDERS_WRITABLE'		=> '请首先确保这些文件夹存在并且可以被网络服务器写入，然后重试：<br />»<strong>%s</strong>',
	
	'MYSQL_SCHEMA_UPDATE_REQUIRED'   => '您的MySQL数据库模式太旧了。phpBB检测到MySQL 3.x/4.x的模式，但是服务器所运行的是MySQL %2$s.<br /><strong>在您进行升级之前，您需要升级模式。</strong><br /><br />请参考<a href="https://www.phpbb.com/kb/article/doesnt-have-a-default-value-errors/">关于升级MySQL模式的知识库文章</a>。如果您遇到问题，请访问<a href="https://www.phpbb.com/community/viewforum.php?f=466">我们的支持论坛</a>。',

	'NAMING_CONFLICT'			=> '命名冲突：%s 与 %s 都是别名<br /><br />%s',
	'NEXT_STEP'					=> '继续进行下一步',
	'NOT_FOUND'					=> '不存在',
	'NOT_UNDERSTAND'			=> '无法理解 %s #%d，表格 %s (“%s”)',
	'NO_CONVERTORS'				=> '没有可用的转换程序',
	'NO_CONVERT_SPECIFIED'		=> '没有指定转换程序',
	'NO_LOCATION'				=> '无法确定位置。如果您确定 Imagemagick 已经安装，请于论坛安装完成之后，在管理员控制面板中指定它的位置。',
	'NO_TABLES_FOUND'			=> '没有找到任何表格',

	'OVERVIEW_BODY'					=> '欢迎来到phpBB3!<br /><br />phpBB®是世界上使用最为广泛的开源论坛软件。phpBB3是自2000年以来这一系列的最新产品。与之前的版本相比，phpBB3 具有更丰富的功能，更友好的操作界面，并拥有phpBB团队的完整技术支持。phpBB3大幅提升了phpBB2受人欢迎的功能，并且添加了众多用户迫切需要的新特性。我们希望phpBB3能满足您的期待.<br /><br />这个安装系统将全程引导您安装，从旧版升级或者转换不同的论坛(包括phpBB2)到phpBB3。要获取更多的信息，我们推荐您阅读<a href="../docs/INSTALL.html">安装指南</a>。<br /><br />要阅读phpBB3许可或了解如何获得支持及我们的立场，请在旁边的菜单中选择。继续下一步操作，请点击上方相应的标签。',

	'PCRE_UTF_SUPPORT'				=> 'PCRE UTF-8 支持',
	'PCRE_UTF_SUPPORT_EXPLAIN'		=> '如果PHP的PCRE插件不支持UTF-8，phpBB 将 <strong>无法</strong> 运行。',
	'PHP_GETIMAGESIZE_SUPPORT'			=> 'PHP 函数 getimagesize() 可用',
	'PHP_GETIMAGESIZE_SUPPORT_EXPLAIN'	=> '<strong>必须的</strong> - 为了让phpBB正常工作， 需要启用 getimagesize 函数。',
	'PHP_JSON_SUPPORT'				=> 'PHP JSON 支持',
	'PHP_JSON_SUPPORT_EXPLAIN'		=> '<strong>要求</strong> - 为了使phpBB正常运转，PHP JSON 扩展需要可用。',
	'PHP_OPTIONAL_MODULE'			=> '可选模块',
	'PHP_OPTIONAL_MODULE_EXPLAIN'	=> '<strong>可选的</strong> - 这些模块或程序不是必需的。但如果它们可用，您将可以使用附加功能。',
	'PHP_SUPPORTED_DB'				=> '支持的数据库',
	'PHP_SUPPORTED_DB_EXPLAIN'		=> '<strong>必需的</strong> - 您必须为PHP提供至少一个兼容的数据库。如果下面没有可用的数据库模块，您应该联络服务供应商，或者查阅相关的PHP安装文档。',
	'PHP_REGISTER_GLOBALS'			=> 'PHP 设置 <var>register_globals</var> 已禁用',
	'PHP_REGISTER_GLOBALS_EXPLAIN'	=> '如果此设置被允许，phpBB仍然会运行。但出于安全考虑，如果条件允许，建议您将 register_globals 禁用。',
	'PHP_SAFE_MODE'					=> '安全模式',
	'PHP_SETTINGS'					=> 'PHP 版本和设置',
	'PHP_SETTINGS_EXPLAIN'			=> '<strong>必需的</strong> - 要安装phpBB，您必需正在运行最低 4.3.3 版本的PHP。如果下面出现 <var>safe mode</var>，您的PHP正在运行于安全模式，这将给远程管理及类似功能带来限制。',
	'PHP_URL_FOPEN_SUPPORT'			=> 'PHP 设定 <var>allow_url_fopen</var> 为启用状态',
	'PHP_URL_FOPEN_SUPPORT_EXPLAIN'	=> '<strong>可选的</strong> - 这个设置是可选的，不过某些phpBB的功能例如外部头像可能会因为没有它而无法正常工作。',
	'PHP_VERSION_REQD'				=> 'PHP 版本 >= 5.3.3',
	'POST_ID'						=> '帖子 ID',
	'PREFIX_FOUND'					=> '对数据表的扫描显示一组有效的表格正在使用 <strong>%s</strong> 作为前缀。',
	'PREPROCESS_STEP'				=> '正在执行转换准备操作',
	'PRE_CONVERT_COMPLETE'			=> '全部的转换准备步骤都已被成功完成，您现在可以开始进行实际的转换操作。',
	'PROCESS_LAST'					=> '正在执行最后的指令',

	'REFRESH_PAGE'				=> '刷新页面以继续转换',
	'REFRESH_PAGE_EXPLAIN'		=> '如果设置为“是”，转换程序将会在完成每一步之后刷新页面，然后继续。如果这是您为了测试目的而进行的第一次转换，我们建议您将此设置为“No”。',
	'REQUIREMENTS_TITLE'		=> '服务器兼容性',
	'REQUIREMENTS_EXPLAIN'		=> '在完整安装之前，phpBB需要对您的服务器设置及所需文件进行检测，以确定您是否可以安装和运行phpBB。请仔细浏览以下结果，并在继续进行之前确保所有必需的检测都已通过。如果您希望使用任何基于非必需检测的功能，请同时确保相关检测已通过。',
	'RETRY_WRITE'				=> '重新尝试写入配置',
	'RETRY_WRITE_EXPLAIN'		=> '如果您想允许phpBB写入config.php，您可以改变它的权限，然后点击下面的【重试】按钮。请记得在安装完成之后恢复config.php的正确权限。',

	'SCRIPT_PATH'				=> '脚本路径',
	'SCRIPT_PATH_EXPLAIN'		=> 'phpBB 根目录与域名指向目录的相对路径，例如：<samp>/phpBB3</samp>',
	'SELECT_LANG'				=> '选择语言',
	'SERVER_CONFIG'				=> '服务器设置',
	'SEARCH_INDEX_UNCONVERTED'	=> '搜索索引没有转换',
	'SEARCH_INDEX_UNCONVERTED_EXPLAIN'	=> '您的旧搜索索引没有转换。搜索将总是得到空结果。如果需要创建一个新索引，请到管理员控制面板，选择维护，然后从子菜单中选择搜索索引。',
	'SELECT_FORUM_GA'			=> '在phpBB 3.1全局公告连接到论坛，为你当前的全局公告选择一个论坛(以后可以移动):',
	'SOFTWARE'					=> '论坛软件',
	'SPECIFY_OPTIONS'			=> '设定转换选项',
	'STAGE_ADMINISTRATOR'		=> '管理员信息',
	'STAGE_ADVANCED'			=> '高级设置',
	'STAGE_ADVANCED_EXPLAIN'	=> '只有您确定需要一些非默认设置时，您才有必要更改此页的内容。如果您不确定，请继续至下一页，因为这些设置可以随时在管理员控制面板中更改。',
	'STAGE_CONFIG_FILE'			=> '配置文件',
	'STAGE_CREATE_TABLE'		=> '创建数据表',
	'STAGE_CREATE_TABLE_EXPLAIN'	=> 'phpBB 3.1 所使用的数据库表格已经被创建并被填入一些初始数据，请继续至下一步以完成安装。',
	'STAGE_DATABASE'			=> '数据库设置',
	'STAGE_FINAL'				=> '完成',
	'STAGE_INTRO'				=> '简介',
	'STAGE_IN_PROGRESS'			=> '进行转换',
	'STAGE_REQUIREMENTS'		=> '检测需求',
	'STAGE_SETTINGS'			=> '设置',
	'STARTING_CONVERT'			=> '开始转换操作',
	'STEP_PERCENT_COMPLETED'	=> '第 <strong>%d</strong> 步 / 共 <strong>%d</strong> 步',
	'SUB_INTRO'					=> '简介',
	'SUB_LICENSE'				=> '授权',
	'SUB_SUPPORT'				=> '支持',
	'SUCCESSFUL_CONNECT'		=> '连接成功',
	'SUPPORT_BODY'				=> '我们将免费为此次发布的phpBB3稳定版本提供完全的技术支持。这包括:</p><ul><li>安装</li><li>设置</li><li>技术问题</li><li>与软件中潜在的Bug相关的问题</li><li>从先前发布的候选(RC)版本升级至最新版本</li><li>从phpBB 2.0.x 转换至 phpBB3</li><li>从其他的论坛转换至 phpBB3 (请访问 <a href="https://www.phpbb.com/community/viewforum.php?f=65">转换器论坛</a>)</li></ul><p>我们建议还在使用Beta版本的用户立即用最新的版本替换他们的系统.</p><h2>扩展/风格</h2><p>与扩展相关的问题，请发表在相应的 <a href="https://www.phpbb.com/community/viewforum.php?f=451">扩展论坛</a>.<br />与风格，模板和图集相关的问题，请发表在相应的 <a href="https://www.phpbb.com/community/viewforum.php?f=471">风格论坛</a>.<br /><br />如果您的问题与特定的包相关，请直接在相应包的主题后回帖.</p><h2>获得支持</h2><p><a href="https://www.phpbb.com/community/viewtopic.php?f=14&amp;t=571070">phpBB欢迎包</a><br /><a href="https://www.phpbb.com/support/">支持部分</a><br /><a href="https://www.phpbb.com/support/docs/en/3.1/ug/quickstart/">快速开始指南</a><br /><br />为了确保您获得最新版本的相关信息，为什么不<a href="https://www.phpbb.com/support/">订阅我们的邮件列表</a>?<br /><br />',
	'SYNC_FORUMS'				=> '开始同步版面',
	'SYNC_POST_COUNT'			=> '正在同步帖子',
	'SYNC_POST_COUNT_ID'		=> '正在同步<var>序号</var>为 %1$s 到 %2$s 的帖子。',
	'SYNC_TOPICS'				=> '开始同步主题',
	'SYNC_TOPIC_ID'				=> '正在同步主题：<var>topic_id</var> %1$s 至 %2$s',

	'TABLES_MISSING'			=> '无法找到这些表格<br />» <strong>%s</strong>',
	'TABLE_PREFIX'				=> '为数据库中的表格名称添加前缀',
	'TABLE_PREFIX_EXPLAIN'		=> '前缀必须以字母开头并且只能包含字母、数字和下划线。',
	'TABLE_PREFIX_SAME'			=> '表格前缀需要与转换之前所使用的相同。<br />»  之前所使用的表格前缀是 %s',
	'TESTS_PASSED'				=> '检测通过',
	'TESTS_FAILED'				=> '检测未通过',

	'UNABLE_WRITE_LOCK'			=> '无法写入锁定文件',
	'UNAVAILABLE'				=> '不可用',
	'UNWRITABLE'				=> '不可写',
	'UPDATE_TOPICS_POSTED'		=> '正在生成主题发布信息',
	'UPDATE_TOPICS_POSTED_ERR'	=> '生成主题发布信息时发生错误。您可以在转换结束后到管理员控制面板中重试这个操作。',
	'VERIFY_OPTIONS'			=> '检测转换选项',
	'VERSION'					=> '版本',

	'WELCOME_INSTALL'			=> '欢迎安装 phpBB 3',
	'WRITABLE'					=> '可写',
));

// Updater
$lang = array_merge($lang, array(
	'ALL_FILES_UP_TO_DATE'		=> '所有文件已更新到phpBB的最新版本。',
	'ARCHIVE_FILE'				=> '文档中的源文件',

	'BACK'				=> '后退',
	'BINARY_FILE'		=> '二进制文件',
	'BOT'				=> '蜘蛛/机器人',

	'CHANGE_CLEAN_NAMES'			=> '这项功能被用来确保一个被更改的用户名没有被多个用户使用。在应用新方法时，一些用户会得到相同的名称。在继续之前，您必须将这些用户删除或改名以确保每个用户名只有一个用户使用。',
	'CHECK_FILES'					=> '检查文件',
	'CHECK_FILES_AGAIN'				=> '再次检查文件',
	'CHECK_FILES_EXPLAIN'			=> '在下面的步骤中相关联的文件都将被检查 - 如果这是第一次文件检查，将花费一定的时间。',
	'CHECK_FILES_UP_TO_DATE'		=> '依照您数据库的版本已是最新。您需要处理一个文件检查确信所有的文件已经更新到了phpBB最近版本的文件。',
	'CHECK_UPDATE_DATABASE'			=> '继续升级进程',
	'COLLECTED_INFORMATION'			=> '收集到的文件信息',
	'COLLECTED_INFORMATION_EXPLAIN'	=> '下面的列表显示了需要更新的文件的信息。请阅读每个状态前的信息并并理解其含义，从而了解在升级过程中您需要做的事情。',
	'COLLECTING_FILE_DIFFS'			=> '收集文件差异',
	'COMPLETE_LOGIN_TO_BOARD'		=> '现在您应该 <a href="../ucp.php?mode=login">登陆到论坛</a> 并检查系统是否正常工作。不要忘记删除或者重命名（移动）install目录！',
	'CONTINUE_UPDATE_NOW'			=> '现在继续升级进程', // Shown within the database update script at the end if called from the updater
	'CONTINUE_UPDATE'				=> '现在继续升级',					// Shown after file upload to indicate the update process is not yet finished
	'CURRENT_FILE'					=> '当前源文件开头 - 冲突部分',
	'CURRENT_VERSION'				=> '当前版本',

	'DATABASE_TYPE'						=> '数据库类型',
	'DATABASE_UPDATE_COMPLETE'			=> '数据库升级完成！',
	'DATABASE_UPDATE_CONTINUE'			=> '继续数据库更新',
	'DATABASE_UPDATE_INFO_OLD'			=> '在安装目录中的数据库升级文件是过时的。请确定上传正确版本的文件。',
	'DATABASE_UPDATE_NOT_COMPLETED'		=> '数据库更新还未完成。',
	'DELETE_USER_REMOVE'				=> '删除用户并删除他的帖子',
	'DELETE_USER_RETAIN'				=> '删除用户但保留他的帖子',
	'DESTINATION'						=> '目标文件',
	'DIFF_INLINE'						=> '行内',
	'DIFF_RAW'							=> '裸标准对比',
	'DIFF_SEP_EXPLAIN'					=> '新文件或已更新文件使用的代码段',
	'DIFF_SIDE_BY_SIDE'					=> '对齐',
	'DIFF_UNIFIED'						=> '标准对比',
	'DO_NOT_UPDATE'						=> '不要上载这个文件',
	'DONE'								=> '完成',
	'DOWNLOAD'							=> '下载',
	'DOWNLOAD_AS'						=> '下载为',
	'DOWNLOAD_UPDATE_METHOD_BUTTON'      => '下载修改过的文件 (推荐)',
	'DOWNLOAD_CONFLICTS'            => '下载此文件的冲突部分',
	'DOWNLOAD_CONFLICTS_EXPLAIN'      => '查找 &lt;&lt;&lt; 标出冲突',
	'DOWNLOAD_UPDATE_METHOD'			=> '下载已修改的文档',
	'DOWNLOAD_UPDATE_METHOD_EXPLAIN'	=> '当您下载并解压缩档案后，您需要上载安装包内的文件到phpBB安装目录。请上传文件到各个正确位置。当你完成所有文件的上传后，请用下面的按钮做文件检查。',

	'EDIT_USERNAME'	=> '编辑用户名',
	'ERROR'			=> '错误',
	'EVERYTHING_UP_TO_DATE'		=> '所有东西都升级到了最新phpBB版本，你现在应该<a href="%1$s">登录你的论坛</a>检查所有功能是否工作正常。别忘了删除、重命名或移动install文件夹！请给我们发送升级信息有关你的服务器和论坛配置从<a href="%2$s">发送统计信息</a>模块在ACP里。',

	'FILE_ALREADY_UP_TO_DATE'		=> '文件已经是最新',
	'FILE_DIFF_NOT_ALLOWED'			=> '文件不允许被比较',
	'FILE_USED'						=> '信息来自于',			// Single file
	'FILES_CONFLICT'				=> '有冲突的文件',
	'FILES_CONFLICT_EXPLAIN'		=> '下面的文件已经修改过，不是旧版本的原始文件。phpBB 认为合并这些文件会产生冲突。请检查冲突并尝试手工的解决，或者选择一种合并的方式继续更新。如果您手工修改消除了冲突，请再次运行文件检查。您也可以选择为每个文件自动首选合并。这将抛弃旧版本文件的冲突代码而丢失您于这个文件上的修改。',
	'FILES_DELETED'					=> '删除的文件',
	'FILES_DELETED_EXPLAIN'			=> '下面的文件在新版里不存在，这些文件不得不从安装里删除。',
	'FILES_MODIFIED'				=> '修改的文件',
	'FILES_MODIFIED_EXPLAIN'		=> '下面的文件已经修改，不是旧版本的原始文件。更新文件将合并你修改过的文件。',
	'FILES_NEW'						=> '新文件',
	'FILES_NEW_EXPLAIN'				=> '以下的文件在安装中不存在。',
	'FILES_NEW_CONFLICT'			=> '新的冲突文件',
	'FILES_NEW_CONFLICT_EXPLAIN'	=> '下面的文件在新版本中已更新，但是再对应目录已经存在同名文件，这个文件将被新文件覆盖。',
	'FILES_NOT_MODIFIED'			=> '未修改的文件',
	'FILES_NOT_MODIFIED_EXPLAIN'	=> '下面的文件在老版本的phpBB文件再新版本中没有修改。',
	'FILES_UP_TO_DATE'				=> '已经升级的文件',
	'FILES_UP_TO_DATE_EXPLAIN'		=> '以下的文件已经是最新的，不需要升级。',
	'FTP_SETTINGS'					=> 'FTP 设定',
	'FTP_UPDATE_METHOD'				=> 'FTP 上传',

	'INCOMPATIBLE_UPDATE_FILES'		=> '找到的升级文件不适用于您当前的版本。您的安装版本是 %1$s 而升级文件是用于升级 phpBB %2$s 到 %3$s。',
	'INCOMPLETE_UPDATE_FILES'		=> '上载的文件不完全',
	'INLINE_UPDATE_SUCCESSFUL'		=> '数据库升级成功。现在您需要继续升级过程。',

	'KEEP_OLD_NAME'		=> '保留用户名',

	'LATEST_VERSION'		=> '最新版本',
	'LINE'					=> '行',
	'LINE_ADDED'			=> '已添加',
	'LINE_MODIFIED'			=> '已修改',
	'LINE_REMOVED'			=> '已删除',
	'LINE_UNMODIFIED'		=> '未修改',
	'LOGIN_UPDATE_EXPLAIN'	=> '您必须登录后才能升级您的论坛。',

	'MAPPING_FILE_STRUCTURE'	=> '为了方便上载，这里有安装文件位置的对应表。',

	'MERGE_MODIFICATIONS_OPTION'	=> '合并更改',

	'MERGE_NO_MERGE_NEW_OPTION'	=> '不要合并 - 使用新文件',
	'MERGE_NO_MERGE_MOD_OPTION'	=> '不要合并 - 使用当前安装的文件',
	'MERGE_MOD_FILE_OPTION'		=> '合并不同之处并使用修改过的代码替代冲突代码',
	'MERGE_NEW_FILE_OPTION'		=> '合并不同之处并使用新文件的代码替代冲突代码',
	'MERGE_SELECT_ERROR'		=> '没有正确选择冲突文件合并方式。',
	'MERGING_FILES'				=> '合并差异',
	'MERGING_FILES_EXPLAIN'		=> '正在进行最后的文件差异收集.<br /><br />请等待直到 phpBB 完成文件上的所有操作。',

	'NEW_FILE'						=> '新升级的文件末尾',
	'NEW_USERNAME'					=> '新的用户名',
	'NO_AUTH_UPDATE'				=> '无权进行升级',
	'NO_ERRORS'						=> '没有错误',
	'NO_UPDATE_FILES'				=> '不要升级以下文件',
	'NO_UPDATE_FILES_EXPLAIN'		=> '以下的文件有更新或者修改过的版本，但是在您的安装的目录中找到。如果列表中包含了除了language/或者styles/ 目录以外的文件，可能您曾经修改过目录结构，升级程序可能没有执行完全。',
	'NO_UPDATE_FILES_OUTDATED'		=> '没有发现有效的更新目录，请确认上传了相关文件.<br /><br />您的安装似乎<strong>不是</strong>最新版本。您的版本 %1$s 的更新已经可以下载，请访问 <a href="https://www.phpbb.com/downloads/" rel="external">https://www.phpbb.com/downloads/</a> 获得正确的升级包从版本 %2$s 升级到版本 %3$s。',
	'NO_UPDATE_FILES_UP_TO_DATE'	=> '您的版本已经是最新版本。没有必要进行升级。如果您希望做一个完全的文件检查，请确信您上传了正确的更新文件。',
	'NO_UPDATE_INFO'				=> '无法找到升级文件信息。',
	'NO_UPDATES_REQUIRED'			=> '不需要升级',
	'NO_VISIBLE_CHANGES'			=> '没有可见的更改',
	'NOTICE'						=> '注意',
	'NUM_CONFLICTS'					=> '冲突的数量',
	'NUMBER_OF_FILES_COLLECTED'		=> '正在检查 %2$d 个文件中 %1$d 个文件的差异.<br />请等待直到文件检查完成。',

	'OLD_UPDATE_FILES'		=> '升级文件已经过期。找到的升级文件是用于 phpBB %1$s 到 phpBB %2$s 的升级，但是最新的 phpBB 版本是 %3$s。',

	'PACKAGE_UPDATES_TO'				=> '当前升级包将升级至版本',
	'PERFORM_DATABASE_UPDATE'			=> '进行数据库升级',
	'PERFORM_DATABASE_UPDATE_EXPLAIN'	=> '点击下面的按钮将执行数据库升级脚本。升级数据库需要花费一定时间，所以即使它看起来没有反应，也请不要关闭。您只要等数据库升级完成之后，再根据提示继续即可。',
	'PREVIOUS_VERSION'					=> '上一个版本',
	'PROGRESS'							=> '进度',

	'RELEASE_ANNOUNCEMENT'		=> '公告',
	'RESULT'					=> '结果',
	'RUN_DATABASE_SCRIPT'		=> '现在升级我的数据库',

	'SELECT_DIFF_MODE'			=> '选择对比模式',
	'SELECT_DOWNLOAD_FORMAT'	=> '选择下载文档格式',
	'SELECT_FTP_SETTINGS'		=> '选择 FTP 设定',
	'SHOW_DIFF_CONFLICT'		=> '显示差异/冲突',
	'SHOW_DIFF_DELETED'			=> '显示文件内容',
	'SHOW_DIFF_FINAL'			=> '显示结果文件',
	'SHOW_DIFF_MODIFIED'		=> '显示合并的差异',
	'SHOW_DIFF_NEW'				=> '显示文件内容',
	'SHOW_DIFF_NEW_CONFLICT'	=> '显示有冲突的差异',
	'SHOW_DIFF_NOT_MODIFIED'	=> '显示差异',
	'SOME_QUERIES_FAILED'		=> '一些查询失败，语句和错误在下面列出。',
	'SQL'						=> 'SQL',
	'SQL_FAILURE_EXPLAIN'		=> '这不需要太多担心，更新会继续。如果想解决这个问题您需要到我们的技术支持论坛中查看或寻求帮助。请查看 <a href="../docs/README.html">README</a> 了解如何获取更多建议。',
	'STAGE_FILE_CHECK'			=> '检查文件',
	'STAGE_UPDATE_DB'			=> '升级数据库',
	'STAGE_UPDATE_FILES'		=> '升级文件',
	'STAGE_VERSION_CHECK'		=> '版本检查',
	'STATUS_CONFLICT'			=> '修改的文件产生冲突',
	'STATUS_DELETED'			=> '删除文件',
	'STATUS_MODIFIED'			=> '已修改的文件',
	'STATUS_NEW'				=> '新文件',
	'STATUS_NEW_CONFLICT'		=> '有冲突的新文件',
	'STATUS_NOT_MODIFIED'		=> '未修改的文件',
	'STATUS_UP_TO_DATE'			=> '已升级的文件',
	
	'TOGGLE_DISPLAY'			=> '查看/隐藏文件列表',
	'TRY_DOWNLOAD_METHOD'      => '您也许希望尝试下载已修改文件的方式.<br />我们推荐这种方式因为其较稳定。',
  'TRY_DOWNLOAD_METHOD_BUTTON'=> '尝试此方式',

	'UPDATE_COMPLETED'				=> '升级完成',
	'UPDATE_DATABASE'				=> '更新数据库',
	'UPDATE_DATABASE_EXPLAIN'		=> '下一步数据库将被更新。',
	'UPDATE_DATABASE_SCHEMA'		=> '升级数据库结构',
	'UPDATE_FILES'					=> '升级文件',
	'UPDATE_FILES_NOTICE'			=> '请确认您已经升级了论坛文件，这个文件仅用于升级您的论坛数据库。',
	'UPDATE_INSTALLATION'			=> '升级安装',
	'UPDATE_INSTALLATION_EXPLAIN'	=> '本选项将升级您的phpBB安装到最新版本。<br />在升级处理期间所有的文件都将被检查是否完整。您可以查看文件和升级前的变化。<br /><br />文件自动升级的途径有两条。</p><h2>手动升级</h2><p>这个选项，您只需要下载有改动的文件以确保不会丢失你对其他文件的修改。下载后将文件上载到phpBB目录的相应位置。之后，您可以再次进行文件检查，检查是否将文件放到了正确的位置。</p><h2>通过FTP自动升级</h2><p>这个方法和第一个类似，但是不需要下载有变动的文件和手动上传它们。系统将为你自动做这个工作。用这个方法进行升级您需要知道您的FTP登录的详细信息。 一旦完成设置，系统将重新定向到文件检查功能完成系统升级。.<br /><br />',
	'UPDATE_INSTRUCTIONS'			=> '

		<h1>发行公告</h1>

		<p>在升级程序前，请阅读最新版本的发行公告，它包含很多有用的信息。它也包含完整的下载链接和代码变动日志。</p>

		<br />

		<h1>如何使用自动升级程序升级您的论坛</h1>

		<p>下面列出的是推荐的升级方法，它只对自动升级包有效。您也可以采用 INSTALL.html 文档中列出的方式升级。自动升级 phpBB3 的步骤是：</p>

		<ul style="margin-left: 20px; font-size: 1.1em;">
			<li>到 <a href="https://www.phpbb.com/downloads/" title="https://www.phpbb.com/downloads/">phpBB.com 下载页</a>下载 "Automatic Update Package"。<br /><br /></li>
			<li>解压缩档案.<br /><br /></li>
			<li>上传解压完的 "install" 和 "vendor" 文件夹到你的phpBB根目录(config.php文件所在目录)。<br /><br /></li>
		</ul>

		<p>一旦上传普通用户将无法访问论坛，因为有install目录。<br /><br />
		<strong><a href="%1$s" title="%1$s">现在输入install地址开始更新程序</a>。</strong><br />
		<br />
		按向导完成开始更新。完成后更新程序将给您发送一个通知。
		</p>
	',
	'UPDATE_METHOD'					=> '升级方式',
	'UPDATE_METHOD_EXPLAIN'			=> '你可以选择合适的上载方式。使用FTP上载你需要提供FTP帐号的详细信息。 使用这种方法文件将自动移动到对应目录并且通过在原文件后添加.bak扩展名的方式备份原来的文件 。如果你选择下载修改的文件则你要解压缩包后手动的上载文件到相应的目录。',
	'UPDATE_REQUIRES_FILE'			=> '升级程序需要如下文件: %s',
	'UPDATE_SUCCESS'				=> '更新完成',
	'UPDATE_SUCCESS_EXPLAIN'		=> '成功更新所有文件。下一步将重新校验所有文件以确保文件被正确升级。',
	'UPDATE_VERSION_OPTIMIZE'		=> '更新版本，优化数据库表单',
	'UPDATING_DATA'					=> '更新数据',
	'UPDATING_TO_LATEST_STABLE'		=> '更新数据库至最新的稳定版本',
	'UPDATED_VERSION'				=> '已更新的版本',
	'UPLOAD_METHOD'					=> '上载方式',

	'UPDATE_DB_SUCCESS'				=> '数据库更新完成',
	'UPDATE_FILE_SUCCESS'			=> '文件更新成功。',
	'USER_ACTIVE'					=> '已激活用户',
	'USER_INACTIVE'					=> '未激活用户',

	'VERSION_CHECK'				=> '版本检查',
	'VERSION_CHECK_EXPLAIN'		=> '检查您当前运行的论坛是否是最新版本。',
	'VERSION_NOT_UP_TO_DATE'	=> '您的论坛版本不是最新的，请继续升级进程。',
	'VERSION_NOT_UP_TO_DATE_ACP'=> '您的论坛版本不是最新的.<br />下面是最新版本的发布和更新帮助链接。',
	'VERSION_NOT_UP_TO_DATE_TITLE'	=> '您的论坛版本不是最新的。',
	'VERSION_UP_TO_DATE'		=> '您的版本是最新的，没有可用的更新。您也许想进行一次论坛程序文件的校验。',
	'VERSION_UP_TO_DATE_ACP'	=> '您的版本是最新的，没有可用的更新。',
	'VIEWING_FILE_CONTENTS'		=> '查看文件内容',
	'VIEWING_FILE_DIFF'			=> '查看文件差异',

	'WRONG_INFO_FILE_FORMAT'	=> '错误的信息文件格式',
));

// Default database schema entries...
$lang = array_merge($lang, array(
	'CONFIG_BOARD_EMAIL_SIG'		=> '非常感谢，论坛管理团队',
	'CONFIG_SITE_DESC'				=> '用于描述您的论坛的一小段文字',
	'CONFIG_SITENAME'				=> '你的论坛',

	'DEFAULT_INSTALL_POST'			=> '这是新安装好的phpBB3论坛中的一个样本帖子。您可以删除这个帖子、这个主题甚至这个版面， 因为一切看起来都运转正常！',

	'FORUMS_FIRST_CATEGORY'			=> '你的第一个分区',
	'FORUMS_TEST_FORUM_DESC'		=> '描述你的第一个版面',
	'FORUMS_TEST_FORUM_TITLE'		=> '你的第一个版面',

	'RANKS_SITE_ADMIN_TITLE'		=> '网站管理员',
	'REPORT_WAREZ'					=> '帖子包含非法或盗版的软件。',
	'REPORT_SPAM'					=> '被举报的帖子唯一的目的就是为网站或某些产品做广告。',
	'REPORT_OFF_TOPIC'				=> '被举报的是跑题文章。',
	'REPORT_OTHER'					=> '举报的原因不符合列举的条目，请输入进一步描述。',

	'SMILIES_ARROW'					=> '箭头',
	'SMILIES_CONFUSED'				=> '疑惑',
	'SMILIES_COOL'					=> '酷！',
	'SMILIES_CRYING'				=> '哭泣或非常伤心',
	'SMILIES_EMARRASSED'			=> '困窘',
	'SMILIES_EVIL'					=> '邪恶或疯狂',
	'SMILIES_EXCLAMATION'			=> '感叹',
	'SMILIES_GEEK'					=> '滑稽',
	'SMILIES_IDEA'					=> '想法',
	'SMILIES_LAUGHING'				=> '大笑',
	'SMILIES_MAD'					=> '抓狂',
	'SMILIES_MR_GREEN'				=> '绿先生',
	'SMILIES_NEUTRAL'				=> '中立',
	'SMILIES_QUESTION'				=> '疑问',
	'SMILIES_RAZZ'					=> '冷笑',
	'SMILIES_ROLLING_EYES'			=> '滴溜的眼睛',
	'SMILIES_SAD'					=> '忧郁',
	'SMILIES_SHOCKED'				=> '震撼',
	'SMILIES_SMILE'					=> '微笑',
	'SMILIES_SURPRISED'				=> '惊讶',
	'SMILIES_TWISTED_EVIL'			=> '扭曲的恶魔',
	'SMILIES_UBER_GEEK'				=> '搞笑',
	'SMILIES_VERY_HAPPY'			=> '特开心',
	'SMILIES_WINK'					=> '眨眼',

	'TOPICS_TOPIC_TITLE'			=> '欢迎来到 phpBB3',
));
