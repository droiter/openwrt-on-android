<?php

/**
 * ownCloud - Template Editor
 *
 * @author Jörn Dreyer
 * @copyright 2014 Jörn Dreyer <jfd@owncloud.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

\OC_Util::checkAdminUser();

\OCP\Util::addStyle('templateeditor', 'settings-admin');
\OCP\Util::addScript('templateeditor', 'settings-admin');

$themes = \OCA\TemplateEditor\MailTemplate::getEditableThemes();
$editableTemplates = \OCA\TemplateEditor\MailTemplate::getEditableTemplates();

$tmpl = new \OCP\Template('templateeditor', 'settings-admin');
$tmpl->assign('themes', $themes);
$tmpl->assign('editableTemplates', $editableTemplates);

return $tmpl->fetchPage();
