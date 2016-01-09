<?php

/**
 * ownCloud - Activity App
 *
 * @author Joas Schilling
 * @copyright 2014 Joas Schilling nickvergessen@owncloud.com
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
 */

namespace OCA\Activity\Tests;

use OCA\Activity\Navigation;

class NavigationTest extends \PHPUnit_Framework_TestCase {
	public function getTemplateData() {
		return array(
			array('all', null),
			array('all', 'self'),
			array('random', null),
		);
	}

	/**
	 * @dataProvider getTemplateData
	 */
	public function testHooksDeleteUser($constructorActive, $forceActive) {
		$l = \OCP\Util::getL10N('activity');
		$navigation = new Navigation($l, \OC::$server->getActivityManager(), \OC::$server->getURLGenerator(), $constructorActive);
		$output = $navigation->getTemplate($forceActive)->fetchPage();

		// Get only the template part with the navigation links
		$navigationLinks = substr($output, strpos($output, '<li>') + 4);
		$navigationLinks = substr($navigationLinks, 0, strrpos($navigationLinks, '</li>'));

		// Remove tabs and new lines
		$navigationLinks = str_replace(array("\t", "\n"), '', $navigationLinks);

		// Turn the list of links into an array
		$navigationEntries = explode('</li><li>', $navigationLinks);

		$links = $navigation->getLinkList();

		// Check whether all top links are available
		foreach ($links['top'] as $link) {
			$found = false;
			foreach ($navigationEntries as $navigationEntry) {
				if (strpos($navigationEntry, 'data-navigation="' . $link['id'] . '"') !== false) {
					$found = true;
					$this->assertContains(
						'href="' . $link['url'] . '">' . $link['name']. '</a>',
						$navigationEntry
					);
					if ($forceActive == $link['id']) {
						$this->assertContains('class="active"', $navigationEntry);
					}
					else if ($forceActive == null && $constructorActive == $link['id']) {
						$this->assertContains('class="active"', $navigationEntry);
					}
				}
			}
			$this->assertTrue($found, 'Could not find navigation entry "' . $link['name'] . '"');
		}

		// Check size of app links
		$this->assertSame(1, sizeof($links['apps']));
		$this->assertNotContains('data-navigation="files"', $navigationLinks, 'Files app should not be included when there are no other apps.');
	}

}
