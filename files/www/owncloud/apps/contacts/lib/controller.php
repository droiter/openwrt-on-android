<?php
/**
 * @author Thomas Tanghus
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts;

use OCP\AppFramework\IAppContainer,
	OCP\AppFramework\Controller as  BaseController,
	OCP\IRequest,
	OCA\Contacts\App;

/**
 * Base Controller class for Contacts App
 */
class Controller extends BaseController {

	/**
	* @var Api
	*/
	protected $api;

	/**
	* @var IRequest
	*/
	protected $request;

	/**
	* @var App
	*/
	protected $app;

	public function __construct($appName, IRequest $request, App $app) {
		parent::__construct($appName, $request);
		$this->app = $app;
	}

}
