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

namespace OCA\TemplateEditor\Controller;

use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCA\TemplateEditor\MailTemplate;

class AdminSettingsController extends ApiController {

	public function __construct($appName, IRequest $request) {
		parent::__construct($appName, $request);
	}

	/**
	 * @param string $theme
	 * @param string $template
	 * @return \OCA\TemplateEditor\Http\MailTemplateResponse
	 */
	public function render( $theme, $template ) {
		try {
			$template = new MailTemplate( $theme, $template );
			return $template->getResponse();
		} catch (\Exception $ex) {
			return new JSONResponse(array('message' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * @param string $theme
	 * @param string $template
	 * @param string $content
	 * @return JSONResponse
	 */
	public function update( $theme, $template, $content ) {
		try {
			$template = new MailTemplate( $theme, $template );
			$template->setContent( $content );
			return new JSONResponse();
		} catch (\Exception $ex) {
			return new JSONResponse(array('message' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * @param string $theme
	 * @param string $template
	 * @return JSONResponse
	 */
	public function reset( $theme, $template ) {
		try {
			$template = new MailTemplate( $theme, $template );
			$template->reset();
			return new JSONResponse();
		} catch (\Exception $ex) {
			return new JSONResponse(array('message' => $ex->getMessage()), $ex->getCode());
		}
	}

}
