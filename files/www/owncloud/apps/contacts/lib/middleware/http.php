<?php

/**
 * ownCloud - HTTP Middleware
 *
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
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


namespace OCA\Contacts\Middleware;

use OCA\Contacts\Controller,
	OCA\Contacts\JSONResponse,
	OCP\AppFramework\Middleware,
	OCP\AppFramework\Http\Response,
	OCP\AppFramework\Http as HttpStatus;

/**
 * Used to intercept exceptions thrown in controllers and backends
 * and transform them into valid HTTP responses.
 */
class Http extends Middleware {

	/**
	 * If an Exception is being caught, return a JSON error response with
	 * a suitable status code
	 * @param Controller $controller the controller that is being called
	 * @param string $methodName the name of the method that will be called on
	 *                           the controller
	 * @param \Exception $exception the thrown exception
	 * @return Response a Response object
	 */
	public function afterException($controller, $methodName, \Exception $exception) {
		\OCP\Util::writeLog('contacts', __METHOD__.' method: '.$methodName, \OCP\Util::DEBUG);
		// If there's no proper status code associated, set it to 500.
		$response = new JSONResponse();
		if($exception->getCode() < 100) {
			$response->setStatus(HttpStatus::STATUS_INTERNAL_SERVER_ERROR);
		} else {
			$response->setStatus($exception->getCode());
		}

		$response->setErrorMessage($exception->getMessage());

		\OCP\Util::logException('contacts', $exception);
		return $response;
	}

}