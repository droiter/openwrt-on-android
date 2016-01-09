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

class ParameterHelperTest extends \PHPUnit_Framework_TestCase {
	/** @var string */
	protected $originalWEBROOT;
	/** @var \OCA\Activity\ParameterHelper */
	protected $parameterHelper;
	/** @var \OC\Files\View */
	protected $view;
	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $config;

	public function setUp() {
		parent::setUp();
		$this->originalWEBROOT =\OC::$WEBROOT;
		\OC::$WEBROOT = '';
		$l = \OCP\Util::getL10N('activity');
		$this->view = new \OC\Files\View('');

		$this->config = $this->getMockBuilder('OCP\IConfig')
			->disableOriginalConstructor()
			->getMock();
		$this->parameterHelper = new \OCA\Activity\ParameterHelper(
			$this->view,
			$this->config,
			$l
		);
	}

	public function tearDown() {
		\OC::$WEBROOT = $this->originalWEBROOT;
		parent::tearDown();
	}

	public function prepareParametersData() {
		return array(
			array(array(), false, false, false, array()),

			// No file position: no path strip
			array(array('/foo/bar.file'), array(), false, false, array('/foo/bar.file')),
			array(array('/foo/bar.file'), array(), true, false, array('/foo/bar.file')),
			array(array('/foo/bar.file'), array(), false, true, array('<strong>/foo/bar.file</strong>')),
			array(array('/foo/bar.file'), array(), true, true, array('<strong>/foo/bar.file</strong>')),

			// Valid file position
			array(array('/foo/bar.file'), array(0 => 'file'), true, false, array('bar.file')),
			array(array('/folder/trailingslash/fromsharing/'), array(0 => 'file'), true, false, array('fromsharing')),
			array(array('/foo/bar.file'), array(0 => 'file'), false, false, array('foo/bar.file')),
			array(array('/folder/trailingslash/fromsharing/'), array(0 => 'file'), false, false, array('folder/trailingslash/fromsharing')),
			array(array('/foo/bar.file'), array(0 => 'file'), true, true, array(
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file" title="in foo">bar.file</a>',
			)),
			array(array('/0/bar.file'), array(0 => 'file'), true, true, array(
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2F0&scrollto=bar.file" title="in 0">bar.file</a>',
			)),
			array(array('/foo/bar.file'), array(1 => 'file'), true, false, array('/foo/bar.file')),
			array(array('/foo/bar.file'), array(1 => 'file'), true, true, array('<strong>/foo/bar.file</strong>')),

			// Legacy: stored without leading slash
			array(array('foo/bar.file'), array(0 => 'file'), false, false, array('foo/bar.file')),
			array(array('foo/bar.file'), array(0 => 'file'), false, true, array(
				'<a class="filename" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file">foo/bar.file</a>',
			)),
			array(array('foo/bar.file'), array(0 => 'file'), true, false, array('bar.file')),
			array(array('foo/bar.file'), array(0 => 'file'), true, true, array(
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file" title="in foo">bar.file</a>',
			)),

			// Valid file position
			array(array('UserA', '/foo/bar.file'), array(1 => 'file'), true, false, array('UserA', 'bar.file')),
			array(array('UserA', '/foo/bar.file'), array(1 => 'file'), true, true, array(
				'<strong>UserA</strong>',
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file" title="in foo">bar.file</a>',
			)),
			array(array('UserA', '/foo/bar.file'), array(2 => 'file'), true, false, array('UserA', '/foo/bar.file')),
			array(array('UserA', '/foo/bar.file'), array(2 => 'file'), true, true, array(
				'<strong>UserA</strong>',
				'<strong>/foo/bar.file</strong>',
			)),
			array(array('UserA', '/foo/bar.file'), array(0 => 'username'), true, true, array(
				'<div class="avatar" data-user="UserA"></div><strong>UserA</strong>',
				'<strong>/foo/bar.file</strong>',
			)),
			array(array('U<ser>A', '/foo/bar.file'), array(0 => 'username'), true, true, array(
				'<div class="avatar" data-user="U&lt;ser&gt;A"></div><strong>U&lt;ser&gt;A</strong>',
				'<strong>/foo/bar.file</strong>',
			)),

			array(array('UserA', '/foo/bar.file'), array(0 => 'username', 1 => 'file'), true, true, array(
				'<div class="avatar" data-user="UserA"></div><strong>UserA</strong>',
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ffoo&scrollto=bar.file" title="in foo">bar.file</a>',
			)),
			array(array('UserA', '/tmp/test'), array(0 => 'username', 1 => 'file'), true, true, array(
				'<div class="avatar" data-user="UserA"></div><strong>UserA</strong>',
				'<a class="filename tooltip" href="/index.php/apps/files?dir=%2Ftmp%2Ftest" title="in tmp">test</a>',
			), 'tmp/test/'),

			array(array('UserA', '/foo/bar.file'), array(0 => 'username'), true, true, array(
				'<strong>UserA</strong>',
				'<strong>/foo/bar.file</strong>',
			), '', false),
		);
	}

	/**
	 * @dataProvider prepareParametersData
	 */
	public function testPrepareParameters($params, $filePosition, $stripPath, $highlightParams, $expected, $createFolder = '', $enableAvatars = true) {
		if ($createFolder !== '') {
			$this->view->mkdir('/' . \OCP\User::getUser() . '/files/' . $createFolder);
		}
		$this->config->expects($this->any())
			->method('getSystemValue')
			->with('enable_avatars', true)
			->willReturn($enableAvatars);
		$this->assertEquals(
			$expected,
			$this->parameterHelper->prepareParameters($params, $filePosition, $stripPath, $highlightParams)
		);
	}

	public function prepareArrayParametersData() {
		$en = \OCP\Util::getL10N('activity', 'en');
		return array(
			array(array(), 'file', true, true, ''),
			array(array('A/B.txt', 'C/D.txt'), 'file', true, false, 'B.txt and D.txt'),
			array(array('A/B.txt', 'C/D.txt'), '', true, false, 'A/B.txt and C/D.txt'),
			array(
				array('A/B.txt', 'C/D.txt', 'E/F.txt', 'G/H.txt', 'I/J.txt', 'K/L.txt', 'M/N.txt'), 'file', true, false,
				(string) $en->n('%s and %n more', '%s and %n more', 4, array(implode((string) $en->t(', '), array('B.txt', 'D.txt', 'F.txt'))))
			),
			array(
				array('A/B.txt', 'C/D.txt', 'E/F.txt', 'G/H.txt', 'I/J.txt', 'K/L.txt', 'M/N.txt'), 'file', true, true,
				(string) $en->n(
					'%s and <strong class="tooltip" title="%s">%n more</strong>',
					'%s and <strong class="tooltip" title="%s">%n more</strong>',
					4,
					array(
						implode((string) $en->t(', '), array(
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FA&scrollto=B.txt" title="in A">B.txt</a>',
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FC&scrollto=D.txt" title="in C">D.txt</a>',
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FE&scrollto=F.txt" title="in E">F.txt</a>',
						)),
						'G/H.txt, I/J.txt, K/L.txt, M/N.txt',
					)
				),
			),
			array(
				array('A"><h1>/B.txt"><h1>', 'C"><h1>/D.txt"><h1>', 'E"><h1>/F.txt"><h1>', 'G"><h1>/H.txt"><h1>', 'I"><h1>/J.txt"><h1>', 'K"><h1>/L.txt"><h1>', 'M"><h1>/N.txt"><h1>'), 'file', true, true,
				(string) $en->n(
					'%s and <strong class="tooltip" title="%s">%n more</strong>',
					'%s and <strong class="tooltip" title="%s">%n more</strong>',
					4,
					array(
						implode((string) $en->t(', '), array(
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FA%22%3E%3Ch1%3E&scrollto=B.txt%22%3E%3Ch1%3E" title="in A&quot;&gt;&lt;h1&gt;">B.txt&quot;&gt;&lt;h1&gt;</a>',
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FC%22%3E%3Ch1%3E&scrollto=D.txt%22%3E%3Ch1%3E" title="in C&quot;&gt;&lt;h1&gt;">D.txt&quot;&gt;&lt;h1&gt;</a>',
							'<a class="filename tooltip" href="/index.php/apps/files?dir=%2FE%22%3E%3Ch1%3E&scrollto=F.txt%22%3E%3Ch1%3E" title="in E&quot;&gt;&lt;h1&gt;">F.txt&quot;&gt;&lt;h1&gt;</a>',
						)),
						'G&quot;&gt;&lt;h1&gt;/H.txt&quot;&gt;&lt;h1&gt;, I&quot;&gt;&lt;h1&gt;/J.txt&quot;&gt;&lt;h1&gt;, K&quot;&gt;&lt;h1&gt;/L.txt&quot;&gt;&lt;h1&gt;, M&quot;&gt;&lt;h1&gt;/N.txt&quot;&gt;&lt;h1&gt;',
					)
				),
			),
		);
	}

	/**
	 * @dataProvider prepareArrayParametersData
	 */
	public function testPrepareArrayParameters($params, $paramType, $stripPath, $highlightParams, $expected) {
		$this->assertEquals(
			$expected,
			(string) $this->parameterHelper->prepareArrayParameter($params, $paramType, $stripPath, $highlightParams)
		);
	}

	public function getSpecialParameterListData() {
		return array(
			array('files', 'shared_group_self', array(0 => 'file')),
			array('files', 'shared_group', array(0 => 'file', 1 => 'username')),
			array('files', '', array(0 => 'file', 1 => 'username')),
			array('calendar', 'shared_group', array()),
			array('calendar', '', array()),
		);
	}

	/**
	 * @dataProvider getSpecialParameterListData
	 */
	public function testGetSpecialParameterList($app, $text, $expected) {
		$this->assertEquals($expected, $this->parameterHelper->getSpecialParameterList($app, $text));
	}
}
