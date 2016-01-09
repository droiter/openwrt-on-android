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
	OCA\Contacts\Utils\JSONSerializer,
	OCA\Contacts\Utils\Properties,
	OCA\Contacts\Controller,
	OCP\AppFramework\Http;

/**
 * Controller class For Contacts
 */
class ContactController extends Controller {

	/**
	 * @NoAdminRequired
	 */
	public function getContact() {

		$params = $this->request->urlParams;
		$response = new JSONResponse();

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$contact = $addressBook->getChild($params['contactId']);

		if (!$contact) {
			return $response->bailOut(App::$l10n->t('Couldn\'t find contact.'));
		}

		$data = JSONSerializer::serializeContact($contact);

		return $response->setData($data);

	}

	/**
	 * @NoAdminRequired
	 */
	public function saveContact() {

		$params = $this->request->urlParams;
		$data = isset($this->request->post['data']) ? $this->request->post['data'] : null;
		$response = new JSONResponse();

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$contact = $addressBook->getChild($params['contactId']);

		if (!$data) {
			return $response->bailOut(App::$l10n->t('No contact data in request.'));
		}

		if (!$contact) {
			return $response->bailOut(App::$l10n->t('Couldn\'t find contact.'));
		}

		if (!$contact->mergeFromArray($data)) {
			return $response->bailOut(App::$l10n->t('Error merging into contact.'));
		}

		if (!$contact->save()) {
			return $response->bailOut(App::$l10n->t('Error saving contact to backend.'));
		}

		return $response->setData(JSONSerializer::serializeContact($contact));
	}

	/**
	 * @NoAdminRequired
	 */
	public function patch() {
		$params = $this->request->urlParams;

		$patch = $this->request->patch;
		$response = new JSONResponse();

		$name = isset($patch['name']) ? $patch['name'] : null;
		$value = isset($patch['value']) ? $patch['value'] : null;
		$checksum = isset($patch['checksum']) ? $patch['checksum'] : null;
		$parameters = isset($patch['parameters']) ? $patch['parameters'] : null;

		$addressBook = $this->app->getAddressBook($params['backend'], $params['addressBookId']);
		$contact = $addressBook->getChild($params['contactId']);

		if (!$contact) {
			return $response
				->setStatus(Http::STATUS_NOT_FOUND)
				->bailOut(App::$l10n->t('Couldn\'t find contact.'));
		}

		if (!$name) {
			return $response
				->setStatus(Http::STATUS_PRECONDITION_FAILED)
				->bailOut(App::$l10n->t('Property name is not set.'));
		}

		if (!$checksum && in_array($name, Properties::$multiProperties)) {
			return $response
				->setStatus(Http::STATUS_PRECONDITION_FAILED)
				->bailOut(App::$l10n->t('Property checksum is not set.'));
		}

		if (is_array($value)) {
			// NOTE: Important, otherwise the compound value will be
			// set in the order the fields appear in the form!
			ksort($value);
		}

		$result = array('contactId' => $params['contactId']);

		if ($checksum && in_array($name, Properties::$multiProperties)) {
			try {
				if(is_null($value)) {
					$contact->unsetPropertyByChecksum($checksum);
				} else {
					$checksum = $contact->setPropertyByChecksum($checksum, $name, $value, $parameters);
					$result['checksum'] = $checksum;
				}
			} catch(\Exception $e)	{
				return $response
					->setStatus(Http::STATUS_PRECONDITION_FAILED)
					->bailOut(App::$l10n->t('Information about vCard is incorrect. Please reload the page.'));
			}
		} elseif (!in_array($name, Properties::$multiProperties)) {

			if (is_null($value)) {
				unset($contact->{$name});
			} else {

				if (!$contact->setPropertyByName($name, $value, $parameters)) {
					return $response
						->setStatus(Http::STATUS_INTERNAL_SERVER_ERROR)
						->bailOut(App::$l10n->t('Error updating contact'));
				}

			}
		}

		if (!$contact->save()) {
			return $response->bailOut(App::$l10n->t('Error saving contact to backend'));
		}

		$result['lastmodified'] = $contact->lastModified();

		return $response->setData($result);

	}

}

