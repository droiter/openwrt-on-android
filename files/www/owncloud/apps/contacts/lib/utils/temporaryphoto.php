<?php
/**
 * ownCloud - TemporaryPhoto
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

namespace OCA\Contacts\Utils;

use OCP\ICache,
	OCP\Image;

/**
 * This class is used for getting a temporary contact photo for cropping.
 *
 * Sub-classes must override __construct() and processImage()
 */
class TemporaryPhoto {

	const MAX_SIZE = 400;

	const PHOTO_CURRENT		=	0;
	const PHOTO_FILESYSTEM	=	1;
	const PHOTO_UPLOADED	=	2;
	const PHOTO_USER		=	3;

	/**
	 * @var \OCP\ICache
	 */
	protected $cache;

	/**
	* @var \OCP\Image
	*/
	protected $image;

	/**
	* Cache key for temporary storage.
	*
	* @var string
	*/
	protected $key;


	/**
	* Whether normalizePhoto() has already been called.
	*
	* @var bool
	*/
	protected $normalized;

	/**
	* Whether the photo is cached.
	*
	* @var bool
	*/
	protected $cached;

	/**
	* Photo loader classes.
	*
	* @var array
	*/
    static public $classMap = array(
		'OCA\\Contacts\\Utils\\TemporaryPhoto\\Contact',
		'OCA\\Contacts\\Utils\\TemporaryPhoto\\FileSystem',
		'OCA\\Contacts\\Utils\\TemporaryPhoto\\Uploaded',
		'OCA\\Contacts\\Utils\\TemporaryPhoto\\User',
	);

	/**
	* Always call parents ctor in subclasses:
	*   		parent::__construct($cache);
	*/
	public function __construct(ICache $cache, $key = null) {
		$this->cache = $cache;
		$this->key = $key;
		if (!is_null($key)) {
			$this->processImage();
		}
	}

	/**
	* Returns an instance of a subclass of this class
	*
	* @param \OCP\IServerContainer $server
	* @param int|null $type One of the pre-defined types.
	* @param mixed|null $data Whatever data is needed to load the photo.
	*/
	public static function create(ICache $cache, $type = null, $data = null) {
		if (isset(self::$classMap[$type])) {
			return new self::$classMap[$type]($cache, $data);
		} else {
			return new self($cache, $data);
		}
	}

	/**
	* Remove a cached image by key.
	*
	* @param string $key
	*/
	public function remove($key) {
		return $this->cache->remove($key);
	}

	/**
	* Do what's needed to get the image from storage
	* depending on the type.
	* After this method is called $this->image must hold an
	* instance of \OCP\Image.
	*/
	protected function processImage() {
		$this->image = new Image();
		$this->image->loadFromData($this->cache->get($this->key));
	}

	/**
	* Whether this image is valied
	*
	* @return bool
	*/
	public function isValid() {
		return (($this->image instanceof Image) && $this->image->valid());
	}

	/**
	* Get the key to the cache image.
	*
	* @return string
	*/
	public function getKey() {
		$this->cachePhoto();
		return $this->key;
	}

	/**
	* Get normalized image.
	*
	* @return \OCP\Image
	*/
	public function getPhoto() {
		$this->normalizePhoto();
		return $this->image;
	}

	/**
	* Save image data to cache and return the key
	*
	* @return string
	*/
	private function cachePhoto() {
		if ($this->cached) {
			return;
		}

		if (!$this->image instanceof Image) {
			$this->processImage();
		}
		$this->normalizePhoto();

		$data = $this->image->data();
		$this->key = uniqid('photo-');
		$this->cache->set($this->key, $data, 600);
	}

	/**
	* Resize and rotate the image if needed.
	*/
	private function normalizePhoto() {
		
		if($this->normalized) {
			return;
		}

		$this->image->fixOrientation();

		if ($this->image->height() > self::MAX_SIZE
			|| $this->image->width() > self::MAX_SIZE
		) {
			$this->image->resize(self::MAX_SIZE);
		}

		$this->normalized = true;
	}

	public function getMimeType(){
		return $this->image->mimeType();
	}

	public function getImage(){
		return $this->image;
	}
}