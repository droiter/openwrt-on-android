<?php
/**
 * ownCloud - Load the avatar of an contact via an ownCloud user avatar
 *
 * @author Tobia De Koninck
 * @copyright 2014 Tobia De Koninck  (tobia@ledfan.be)
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
	OCP\ICache;

/**
 * This class loads the PHOTO or LOGO property from a contact.
 */
class User extends BaseTemporaryPhoto {

	/**
	 * The Contact object to load the image from
	 *
	 * @var OCA\Contacts\Contact
	 */
	protected $userId;

	public function __construct(ICache $cache, $userId) {
		parent::__construct($cache);
		// check if userId is a real ownCloud user
		if(!in_array($userId, \OCP\User::getUsers())){
			throw new \Exception('Second argument must be an ownCloud user ID');
		}
		$this->userId = $userId;
		$this->processImage();
	}

	/**
	 * Do what's needed to get the image from storage
	 * depending on the type.
	 */
	protected function processImage() {
		$localPath = \OCP\Config::getSystemValue('datadirectory') . '/' . $this->userId . '/avatar.';
		if (file_exists($localPath . 'png')){
			$localPath .= 'png';
		} else if (file_exists($localPath . 'jpg')){
			$localPath .= 'jpg';
		}

		$this->image = new \OCP\Image();
		$this->image->loadFromFile($localPath);
	}

}