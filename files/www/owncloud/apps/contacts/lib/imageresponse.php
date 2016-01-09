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

use OCP\AppFramework\Http\Response;


/**
 * A renderer for images
 */
class ImageResponse extends Response {
	/**
	 * @var OCP\Image
	 */
	protected $image;

	/**
	 * @param \OCP\Image $image
	 */
	public function __construct($image = null) {
		if(!is_null($image)) {
			$this->setImage($image);
		}
	}

	/**
	 * @param OCP\Image $image
	 */
	public function setImage(\OCP\Image $image) {
		if(!$image->valid()) {
			throw new \InvalidArgumentException(__METHOD__. ' The image resource is not valid.');
		}
		$this->image = $image;
		$this->addHeader('Content-Type', $image->mimeType());
		return $this;
	}

	/**
	 * Return the image data stream
	 * @return Image data
	 */
	public function render() {
		if(is_null($this->image)) {
			throw new \BadMethodCallException(__METHOD__. ' Image must be set either in constructor or with setImage()');
		}
		return $this->image->data();
	}

}