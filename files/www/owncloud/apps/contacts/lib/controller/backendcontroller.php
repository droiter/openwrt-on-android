<?php
/**
 * @author Nicolas Mora
 * @copyright 2014 Nicolas Mora (mail@babelouest.org)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts\Controller;

use OCA\Contacts\App,
	OCA\Contacts\JSONResponse,
	OCA\Contacts\Utils\JSONSerializer,
	OCA\Contacts\Controller,
	OCP\AppFramework\Http;

/**
 * Controller class For Address Books
 */
class BackendController extends Controller {

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getConnectors() {
		$response = new JSONResponse();
		$prefix = "backend_ldap_";
		$suffix = "_connector.xml";
		$path = __DIR__ . "/../../formats/";
		$files = scandir($path);
		$formats = array();
		foreach ($files as $file) {
			if (!strncmp($file, $prefix, strlen($prefix)) && substr($file, - strlen($suffix)) === $suffix) {
				if (file_exists($path.$file)) {
					$format = simplexml_load_file ( $path.$file );
					if ($format) {
						if (isset($format['name'])) {
							$formatId = substr($file, strlen($prefix), - strlen($suffix));
							$formats[] = array('id' => $formatId, 'name' => (string)$format['name'], 'xml' => $format->asXML());
						}
					}
				}
			}
		}
		return $response->setData($formats);
	}
	
	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function enableBackend() {
		$response = new JSONResponse();
		$params = $this->request->urlParams;
		$backend = $params['backend'];
		$enable = $params['enable'];
		return $response->setData(\OCP\Config::setAppValue('contacts', 'backend_'.$backend, $enable));
	}
	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function backendStatus() {
		$response = new JSONResponse();
		$params = $this->request->urlParams;
		$backend = $params['backend'];
		$enabled = \OCP\Config::getAppValue('contacts', 'backend_'.$backend, "false");
		return $response->setData($enabled);
	}
}

