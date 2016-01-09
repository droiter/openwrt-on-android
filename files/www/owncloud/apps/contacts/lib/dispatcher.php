<?php
/**
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts;

use OCP\AppFramework\App as MainApp,
	OCP\AppFramework\IAppContainer,
	OCA\Contacts\App,
	OCA\Contacts\Middleware\Http as HttpMiddleware,
	OCA\Contacts\Controller\PageController,
	OCA\Contacts\Controller\AddressBookController,
	OCA\Contacts\Controller\BackendController,
	OCA\Contacts\Controller\GroupController,
	OCA\Contacts\Controller\ContactController,
	OCA\Contacts\Controller\ContactPhotoController,
	OCA\Contacts\Controller\SettingsController,
	OCA\Contacts\Controller\ImportController,
	OCA\Contacts\Controller\ExportController;

/**
 * This class manages our app actions
 *
 * TODO: Merge with App
 */

class Dispatcher extends MainApp {

	/**
	 * @var string
	 */
	protected $appName;

	/**
	* @var \OCA\Contacts\App
	*/
	protected $app;

	/**
	* @var \OCP\IServerContainer
	*/
	protected $server;

	/**
	* @var \OCP\AppFramework\IAppContainer
	*/
	protected $container;

	public function __construct($params) {
		$this->appName = 'contacts';
		parent::__construct($this->appName, $params);
		$this->container = $this->getContainer();
		$this->server = $this->container->getServer();
		$this->app = new App($this->container->query('API')->getUserId());
		$this->registerServices();
		$this->container->registerMiddleware('HttpMiddleware');
	}

	public function registerServices() {
		$app = $this->app;
		$appName = $this->appName;

		$this->container->registerService('HttpMiddleware', function($container) {
			return new HttpMiddleware();
		});

		$this->container->registerService('PageController', function(IAppContainer $container) use($app, $appName) {
			$request = $container->query('Request');
			return new PageController($appName, $request);
		});
		$this->container->registerService('AddressBookController', function(IAppContainer $container) use($app, $appName) {
			$request = $container->query('Request');
			$api = $container->query('API');
			return new AddressBookController($appName, $request, $app, $api);
		});
		$this->container->registerService('BackendController', function(IAppContainer $container) use($app, $appName) {
			$request = $container->query('Request');
			return new BackendController($container, $request, $app);
		});
		$this->container->registerService('GroupController', function(IAppContainer $container) use($app, $appName) {
			$request = $container->query('Request');
			$tags = $container->getServer()->getTagManager()->load('contact');
			return new GroupController($appName, $request, $app, $tags);
		});
		$this->container->registerService('ContactController', function(IAppContainer $container) use($app, $appName) {
			$request = $container->query('Request');
			return new ContactController($appName, $request, $app);
		});
		$this->container->registerService('ContactPhotoController', function(IAppContainer $container) use($app, $appName) {
			$request = $container->query('Request');
			$cache = $container->getServer()->getCache();
			return new ContactPhotoController($appName, $request, $app, $cache);
		});
		$this->container->registerService('SettingsController', function(IAppContainer $container) use($app, $appName) {
			$request = $container->query('Request');
			return new SettingsController($appName, $request, $app);
		});
		$this->container->registerService('ImportController', function(IAppContainer $container) use($app, $appName) {
			$request = $container->query('Request');
			$cache = $container->getServer()->getCache();
			$tags = $container->getServer()->getTagManager()->load('contact');
			return new ImportController($appName, $request, $app, $cache, $tags);
		});
		$this->container->registerService('ExportController', function(IAppContainer $container) use($app, $appName) {
			$request = $container->query('Request');
			return new ExportController($appName, $request, $app);
		});
	}

}
