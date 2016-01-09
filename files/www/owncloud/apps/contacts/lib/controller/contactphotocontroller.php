<?php
/**
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts\Controller;

use OCA\Contacts\App,
	OCA\Contacts\JSONResponse,
	OCA\Contacts\ImageResponse,
	OCA\Contacts\Utils\Properties,
	OCA\Contacts\Utils\TemporaryPhoto,
	OCA\Contacts\Controller,
	OCP\IRequest,
	OCP\ICache;

/**
 * Controller class For Contacts
 */
class ContactPhotoController extends Controller {

	/**
	 * @var \OCP\ICache
	 */
	protected $cache;

	public function __construct($appName, IRequest $request, App $app, ICache $cache) {
		parent::__construct($appName, $request, $app);
		$this->cache = $cache;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getPhoto($maxSize = 170) {
		// TODO: Cache resized photo
		$params = $this->request->urlParams;
		$etag = null;

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$contact = $addressBook->getChild($params['contactId']);

		$tempPhoto = TemporaryPhoto::create(
			$this->cache,
			TemporaryPhoto::PHOTO_CURRENT,
			$contact
		);

		if($tempPhoto->isValid()) {
			$image = $tempPhoto->getPhoto();
			$response = new ImageResponse($image);
			$lastModified = $contact->lastModified();
			// Force refresh if modified within the last minute.
			if(!is_null($lastModified)) {
				$response->setLastModified(\DateTime::createFromFormat('U', $lastModified) ?: null);
			}
			if(!is_null($etag)) {
				$response->setETag($etag);
			}
			if ($image->width() > $maxSize || $image->height() > $maxSize) {
				$image->resize($maxSize);
			}
			return $response;
		} else {
			return array("data"=> array("FN"=> $contact->getDisplayName()));
		}
	}

	/**
	 * Uploads a photo and saves in oC cache
	 * @return JSONResponse with data.tmp set to the key in the cache.
	 *
	 * @NoAdminRequired
	 */
	public function uploadPhoto() {
		$params = $this->request->urlParams;
		$response = new JSONResponse();

		$tempPhoto = TemporaryPhoto::create(
			$this->cache,
			TemporaryPhoto::PHOTO_UPLOADED,
			$this->request
		);

		return $response->setParams(array(
			'tmp'=>$tempPhoto->getKey(),
			'metadata' => array(
				'contactId'=> $params['contactId'],
				'addressBookId'=> $params['addressBookId'],
				'backend'=> $params['backend'],
			),
		));
	}

	/**
	 * Saves the photo from the contact being edited to oC cache
	 * @return JSONResponse with data.tmp set to the key in the cache.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function cacheCurrentPhoto() {
		$params = $this->request->urlParams;
		$response = new JSONResponse();

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$contact = $addressBook->getChild($params['contactId']);

		$tempPhoto = TemporaryPhoto::create(
			$this->cache,
			TemporaryPhoto::PHOTO_CURRENT,
			$contact
		);

		return $response->setParams(array(
			'tmp'=>$tempPhoto->getKey(),
			'metadata' => array(
				'contactId'=> $params['contactId'],
				'addressBookId'=> $params['addressBookId'],
				'backend'=> $params['backend'],
			),
		));
	}

	/**
	 * Saves the photo from ownCloud FS to oC cache
	 * @return JSONResponse with data.tmp set to the key in the cache.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function cacheFileSystemPhoto() {
		$params = $this->request->urlParams;
		$response = new JSONResponse();

		if(!isset($this->request->get['path'])) {
			$response->bailOut(App::$l10n->t('No photo path was submitted.'));
		}

		$tempPhoto = TemporaryPhoto::create(
			$this->cache,
			TemporaryPhoto::PHOTO_FILESYSTEM,
			$this->request->get['path']
		);

		return $response->setParams(array(
			'tmp'=>$tempPhoto->getKey(),
			'metadata' => array(
				'contactId'=> $params['contactId'],
				'addressBookId'=> $params['addressBookId'],
				'backend'=> $params['backend'],
			),
		));
	}

	/**
	 * Get a photo from the oC cache for cropping.
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getTempPhoto() {
		$params = $this->request->urlParams;
		$tmpkey = $params['key'];

		$tmpPhoto = new TemporaryPhoto($this->cache, $tmpkey);
		$image = $tmpPhoto->getPhoto();

		if($image->valid()) {
			$response = new ImageResponse($image);
			return $response;
		} else {
			$response = new JSONResponse();
			return $response->bailOut('Error getting temporary photo');
		}
	}

	/**
	 * Get a photo from the oC and crops it with the suplied geometry.
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function cropPhoto() {
		$params = $this->request->urlParams;
		$x = $this->params('x', 0);
		$y = $this->params('y', 0);
		$w = $this->params('w', -1);
		$h = $this->params('h', -1);
		$tmpkey = $params['key'];

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$contact = $addressBook->getChild($params['contactId']);

		$response = new JSONResponse();

		$tmpPhoto = new TemporaryPhoto($this->cache, $tmpkey);
		$image = $tmpPhoto->getPhoto();
		if(!$image || !$image->valid()) {
			return $response->bailOut(App::$l10n->t('Error loading image from cache'));
		}

		$w = ($w !== -1 ? $w : $image->width());
		$h = ($h !== -1 ? $h : $image->height());

		$image->crop($x, $y, $w, $h);

		if (!$contact->setPhoto($image)) {
			$tmpPhoto->remove($tmpkey);
			return $response->bailOut(App::$l10n->t('Error getting PHOTO property.'));
		}

		if(!$contact->save()) {
			return $response->bailOut(App::$l10n->t('Error saving contact.'));
		}

		$thumbnail = Properties::cacheThumbnail(
			$params['backend'],
			$params['addressBookId'],
			$params['contactId'],
			$image,
			$contact
		);

		$response->setData(array(
			'status' => 'success',
			'data' => array(
				'id' => $params['contactId'],
				'thumbnail' => $thumbnail,
			)
		));

		$tmpPhoto->remove($tmpkey);

		return $response;
	}

}