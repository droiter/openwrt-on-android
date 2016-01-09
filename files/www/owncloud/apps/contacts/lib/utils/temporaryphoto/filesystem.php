<?php
/**
 * ownCloud - Load an image from the virtual file system.
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
	OCP\AppFramework\Http,
	OCP\ICache;

/**
 * This class loads an image from the virtual file system.
 */
class FileSystem extends BaseTemporaryPhoto {

	/**
	 * The virtual file system path to load the image from
	 *
	 * @var string
	 */
	protected $path;

	public function __construct(ICache $cache, $path) {
		\OCP\Util::writeLog('contacts', __METHOD__.' path: ' . $path, \OCP\Util::DEBUG);
		if (!is_string($path)) {
			throw new \Exception(
				__METHOD__ . ' Second argument must a string'
			);
		}

		parent::__construct($cache);
		$this->path = $path;
		$this->processImage();
	}

	/**
	 * Load the image.
	 */
	protected function processImage() {
		$localPath = \OC\Files\Filesystem::getLocalFile($this->path);

		if (!file_exists($localPath)) {
			throw new \Exception(
				'The file does not exist: ' . $localPath,
				Http::STATUS_NOT_FOUND
			);
		}

		$this->image = new \OCP\Image();
		$this->image->loadFromFile($localPath);
	}

}