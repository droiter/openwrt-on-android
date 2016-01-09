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
	OCA\Contacts\Utils\JSONSerializer,
	OCA\Contacts\Controller,
	OCP\AppFramework\Http,
	OCP\AppFramework\IApi,
	OCP\IRequest;

/**
 * Controller class For Address Books
 */
class AddressBookController extends Controller {

	/**
	 * @var \OCP\AppFramework\IApi
	 */
	protected $api;

	public function __construct($appName, IRequest $request, App $app, IApi $api) {
		parent::__construct($appName, $request, $app);
		$this->api = $api;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function userAddressBooks() {
		$addressBooks = $this->app->getAddressBooksForUser();
		$result = array();
		$lastModified = 0;

		foreach($addressBooks as $addressBook) {

			$data = $addressBook->getMetaData();
			$result[] = $data;

			if (!is_null($data['lastmodified'])) {
				$lastModified = max($lastModified, $data['lastmodified']);
			}

		}

		// To avoid invalid cache deletion time is saved
		/*$lastModified = max(
			$lastModified,
			\OCP\Config::getUserValue($this->api->getUserId(), 'contacts', 'last_address_book_deleted', 0)
		);*/

		$response = new JSONResponse(array(
			'addressbooks' => $result,
		));

		/** FIXME: Caching is currently disabled
		if($lastModified > 0) {
			$response->setLastModified(\DateTime::createFromFormat('U', $lastModified) ?: null);
			$response->setETag(md5($lastModified));
		}
		*/

		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getAddressBook() {
		$params = $this->request->urlParams;

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$lastModified = $addressBook->lastModified();
		$response = new JSONResponse();
		$response->setData(array('data' => $addressBook->getMetaData()));

		if (!is_null($lastModified)) {
			$response->addHeader('Cache-Control', 'private, must-revalidate');
			$response->setLastModified(\DateTime::createFromFormat('U', $lastModified) ?: null);
			$etag = md5($lastModified);
			$response->setETag($etag);
		}

		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getContacts() {
		$params = $this->request->urlParams;

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$lastModified = $addressBook->lastModified();
		$etag = null;
		$response = new JSONResponse();

		if (!is_null($lastModified)) {
			//$response->addHeader('Cache-Control', 'private, must-revalidate');
			$response->setLastModified(\DateTime::createFromFormat('U', $lastModified) ?: null);
			$etag = md5($lastModified);
			$response->setETag($etag);
		}

		if (!is_null($etag)
			&& $this->request->getHeader('If-None-Match') === '"'.$etag.'"')
		{
			return $response->setStatus(Http::STATUS_NOT_MODIFIED);
		} else {
			switch ($this->request->method) {
				case 'OPTIONS':
					$options = array('GET', 'HEAD', 'OPTIONS');
					if ($addressBook->hasPermission(\OCP\PERMISSION_DELETE)
						&& $addressBook->getBackend()->hasAddressBookMethodFor(\OCP\PERMISSION_DELETE))
					{
						$options[] = 'DELETE';
					}
					if ($addressBook->hasPermission(\OCP\PERMISSION_UPDATE)
						&& $addressBook->getBackend()->hasAddressBookMethodFor(\OCP\PERMISSION_UPDATE))
					{
						$options[] = 'POST';
					}
					$response->addHeader('Allow' , implode(',', $options));
					return $response;
				case 'HEAD':
					return $response;
				case 'GET':
					$contacts = array();

					foreach ($addressBook->getChildren() as $i => $contact) {
						$result = JSONSerializer::serializeContact($contact);
						if ($result !== null) {
							$contacts[] = $result;
						}
					}

					return $response->setData(array('contacts' => $contacts));
			}
		}
	}

	/**
	 * @NoAdminRequired
	 */
	public function addAddressBook() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$backend = $this->app->getBackend($params['backend']);

		if (!$backend->hasAddressBookMethodFor(\OCP\PERMISSION_CREATE)) {
			throw new \Exception('This backend does not support adding address books', 501);
		}

		try {
			$id = $backend->createAddressBook($this->request->post);
		} catch(\Exception $e) {
			return $response->bailOut($e->getMessage());
		}

		if ($id === false) {
			return $response->bailOut(App::$l10n->t('Error creating address book'));
		}

		return $response->setStatus('201')->setParams($backend->getAddressBook($id));
	}

	/**
	 * @NoAdminRequired
	 */
	public function updateAddressBook() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$addressBook->update($this->request['properties']);

		return $response->setParams($addressBook->getMetaData());
	}

	/**
	 * @NoAdminRequired
	 */
	public function deleteAddressBook() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$backend = $this->app->getBackend($params['backend']);

		if (!$backend->hasAddressBookMethodFor(\OCP\PERMISSION_DELETE)) {
			throw new \Exception(App::$l10n->t(
				'The "%s" backend does not support deleting address books', array($backend->name)
			), 501);
		}

		$addressBookInfo = $backend->getAddressBook($params['addressBookId']);

		if (!$addressBookInfo['permissions'] & \OCP\PERMISSION_DELETE) {
			throw new \Exception(App::$l10n->t(
				'You do not have permissions to delete the "%s" address book',
				array($addressBookInfo['displayname'])
			), 403);
		}

		if (!$backend->deleteAddressBook($params['addressBookId'])) {
			throw new \Exception(App::$l10n->t(
				'Error deleting address book'
			), 500);
		}

		\OCP\Config::setUserValue($this->api->getUserId(), 'contacts', 'last_address_book_deleted', time());
		return $response;
	}

	/**
	 * @NoAdminRequired
	 */
	public function activateAddressBook() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);

		$addressBook->setActive($this->request->post['state']);

		return $response;
	}

	/**
	 * @NoAdminRequired
	 */
	public function addChild() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);

		try {
			$id = $addressBook->addChild();
		} catch(\Exception $e) {
			return $response->bailOut($e->getMessage());
		}

		if ($id === false) {
			return $response->bailOut(App::$l10n->t('Error creating contact.'));
		}

		$contact = $addressBook->getChild($id);

		$serialized = JSONSerializer::serializeContact($contact);

		if (is_null($serialized)) {
			throw new \Exception(App::$l10n->t(
				'Error creating contact'
			));
		}

		$response->setStatus('201')->setETag($contact->getETag());
		$response->addHeader('Location',
			\OCP\Util::linkToRoute(
				'contacts_contact_get',
				array(
					'backend' => $params['backend'],
					'addressBookId' => $params['addressBookId'],
					'contactId' => $id
				)
			)
		);
		return $response->setParams($serialized);
	}

	/**
	 * @NoAdminRequired
	 */
	public function deleteChild() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);

		$result = $addressBook->deleteChild($params['contactId']);

		if ($result === false) {
			throw new \Exception(App::$l10n->t(
				'Error deleting contact'
			), 500);
		}

		return $response->setStatus('204');
	}

	/**
	 * @NoAdminRequired
	 */
	public function deleteChildren() {
		$params = $this->request->urlParams;

		$response = new JSONResponse();

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$contacts = $this->request->post['contacts'];

		$result = $addressBook->deleteChildren($contacts);

		return $response->setParams(array('result' => $result));
	}

	/**
	 * @NoAdminRequired
	 */
	public function moveChild() {
		$params = $this->request->urlParams;
		$targetInfo = $this->request->post['target'];

		$response = new JSONResponse();

		// TODO: Check if the backend supports move (is 'local' or 'shared') and use that operation instead.
		// If so, set status 204 and don't return the serialized contact.
		$fromAddressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$targetAddressBook = $this->app->getAddressBook($targetInfo['backend'], $targetInfo['id']);
		$contact = $fromAddressBook->getChild($params['contactId']);

		if (!$contact) {
			throw new \Exception(App::$l10n->t(
				'Error retrieving contact'
			), 500);
		}

		$contactId = $targetAddressBook->addChild($contact);

		// Retrieve the contact again to be sure it's in sync
		$contact = $targetAddressBook->getChild($contactId);

		if (!$contact) {
			throw new \Exception(App::$l10n->t(
				'Error saving contact'
			), 500);
		}

		if (!$fromAddressBook->deleteChild($params['contactId'])) {
			// Don't bail out because we have to return the contact
			return $response->debug(App::$l10n->t('Error removing contact from other address book.'));
		}

		$serialized = JSONSerializer::serializeContact($contact);

		if (is_null($serialized)) {
			throw new \Exception(App::$l10n->t(
				'Error getting moved contact'
			));
		}

		return $response->setParams($serialized);
	}

}

