<?php
/**
 * ownCloud - Load an uploaded image from system.
 *
 * @author Thomas Tanghus
 * @copyright 2014 Thomas Tanghus (thomas@tanghus.net)
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

namespace OCA\Contacts\Utils\TemporaryPhoto;

use OCA\Contacts\Contact as ContactObject,
	OCA\Contacts\Utils\TemporaryPhoto as BaseTemporaryPhoto,
	OCP\IRequest,
	OCP\AppFramework\Http,
	OCP\Image,
	OCP\ICache;

/**
 * This class loads an image from the virtual file system.
 */
class Uploaded extends BaseTemporaryPhoto {

	/**
	 * The request to read the data from
	 *
	 * @var \OCP\IRequest
	 */
	protected $request;

	public function __construct(ICache $cache, IRequest $request) {
		\OCP\Util::writeLog('contacts', __METHOD__, \OCP\Util::DEBUG);
		if (!$request instanceOf IRequest) {
			throw new \Exception(
				__METHOD__ . ' Second argument must be an instance of \\OCP\\IRequest'
			);
		}

		parent::__construct($cache);
		$this->request = $request;
		$this->processImage();
	}

	/**
	 * Load the image.
	 */
	protected function processImage() {
		// If image has already been read return
		if ($this->image instanceOf Image) {
			return;
		}
		$this->image = new Image();
		\OCP\Util::writeLog('contacts', __METHOD__ . ', Content-Type: ' . $this->request->getHeader('Content-Type'), \OCP\Util::DEBUG);
		\OCP\Util::writeLog('contacts', __METHOD__ . ', Content-Length: ' . $this->request->getHeader('Content-Length'), \OCP\Util::DEBUG);

		if (substr($this->request->getHeader('Content-Type'), 0, 6) !== 'image/') {
			throw new \Exception(
				'Only images can be used as contact photo',
				Http::STATUS_UNSUPPORTED_MEDIA_TYPE
			);
		}

		$maxSize = \OCP\Util::maxUploadFilesize('/');
		if ($this->request->getHeader('Content-Length') > $maxSize) {
			throw new \Exception(
				sprintf(
					'The size of the file exceeds the maximum allowed %s',
					\OCP\Util::humanFileSize($maxSize)
				),
				Http::STATUS_REQUEST_ENTITY_TOO_LARGE
			);
		}

		$this->image->loadFromFileHandle($this->request->put);
	}

}