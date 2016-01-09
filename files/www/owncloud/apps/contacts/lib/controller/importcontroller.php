<?php
/**
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts\Controller;

use OCA\Contacts\App,
	OCA\Contacts\JSONResponse,
	OCA\Contacts\Controller,
	Sabre\VObject,
	OCA\Contacts\VObject\VCard as MyVCard,
	OCA\Contacts\ImportManager,
	OCP\IRequest,
	OCP\ICache,
	OCP\ITags;

/**
 * Controller importing contacts
 */
class ImportController extends Controller {

	public function __construct($appName, IRequest $request, App $app, ICache $cache, ITags $tags) {
		parent::__construct($appName, $request, $app);
		$this->cache = $cache;
		$this->tags = $tags;
	}

	/**
	 * @NoAdminRequired
	 */
	public function upload() {
		$request = $this->request;
		$params = $this->request->urlParams;
		$addressBookId = $params['addressBookId'];
		$format = $params['importType'];
		$response = new JSONResponse();

		$view = \OCP\Files::getStorage('contacts');
		if(!$view->file_exists('imports')) {
			$view->mkdir('imports');
		}

		if (!isset($request->files['file'])) {
			$response->bailOut(App::$l10n->t('No file was uploaded. Unknown error'));
			return $response;
		}

		$file=$request->files['file'];

		if($file['error'] !== UPLOAD_ERR_OK) {
			$error = $file['error'];
			$errors = array(
				UPLOAD_ERR_OK			=> App::$l10n->t("There is no error, the file uploaded with success"),
				UPLOAD_ERR_INI_SIZE		=> App::$l10n->t("The uploaded file exceeds the upload_max_filesize directive in php.ini")
					.ini_get('upload_max_filesize'),
				UPLOAD_ERR_FORM_SIZE	=> App::$l10n->t("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"),
				UPLOAD_ERR_PARTIAL		=> App::$l10n->t("The uploaded file was only partially uploaded"),
				UPLOAD_ERR_NO_FILE		=> App::$l10n->t("No file was uploaded"),
				UPLOAD_ERR_NO_TMP_DIR	=> App::$l10n->t('Missing a temporary folder'),
				UPLOAD_ERR_CANT_WRITE	=> App::$l10n->t('Failed to write to disk'),
			);
			$response->bailOut($errors[$error]);
			return $response;
		}

		$maxUploadFilesize = \OCP\Util::maxUploadFilesize('/');
		$maxHumanFilesize = \OCP\Util::humanFileSize($maxUploadFilesize);

		$totalSize = $file['size'];
		if ($maxUploadFilesize >= 0 and $totalSize > $maxUploadFilesize) {
			$response->bailOut(App::$l10n->t('Not enough storage available'));
			return $response;
		}

		$tmpname = $file['tmp_name'];
		$filename = strtr($file['name'], array('/' => '', "\\" => ''));
		if(is_uploaded_file($tmpname)) {
			if(\OC\Files\Filesystem::isFileBlacklisted($filename)) {
				$response->bailOut(App::$l10n->t('Attempt to upload blacklisted file:') . $filename);
				return $response;
			}
			$content = file_get_contents($tmpname);
			$proxyStatus = \OC_FileProxy::$enabled;
			\OC_FileProxy::$enabled = false;
			if($view->file_put_contents('/imports/'.$filename, $content)) {
				\OC_FileProxy::$enabled = $proxyStatus;
				$progresskey = 'contacts-import-' . rand();
				$response->setParams(
					array(
						'filename'=>$filename,
						'progresskey' => $progresskey,
						'backend' => $params['backend'],
						'addressBookId' => $params['addressBookId'],
						'importType' => $format
					)
				);
			} else {
				\OC_FileProxy::$enabled = $proxyStatus;
				$response->bailOut(App::$l10n->t('Error uploading contacts to storage.'));
			return $response;
			}
		} else {
			$response->bailOut('Temporary file: \''.$tmpname.'\' has gone AWOL?');
			return $response;
		}
		return $response;
	}

	/**
	 * @NoAdminRequired
	 */
	public function prepare() {
		$request = $this->request;
		$params = $this->request->urlParams;
		$addressBookId = $params['addressBookId'];
		$format = $params['importType'];
		$response = new JSONResponse();
		$filename = $request->post['filename'];
		$path = $request->post['path'];

		$view = \OCP\Files::getStorage('contacts');
		if(!$view->file_exists('imports')) {
			$view->mkdir('imports');
		}

		$proxyStatus = \OC_FileProxy::$enabled;
		\OC_FileProxy::$enabled = false;
		$content = \OC_Filesystem::file_get_contents($path . '/' . $filename);
		//$content = file_get_contents('oc://' . $path . '/' . $filename);
		if($view->file_put_contents('/imports/' . $filename, $content)) {
			\OC_FileProxy::$enabled = $proxyStatus;
			$progresskey = 'contacts-import-' . rand();
			$response->setParams(
				array(
					'filename'=>$filename,
					'progresskey' => $progresskey,
					'backend' => $params['backend'],
					'addressBookId' => $params['addressBookId'],
					'importType' => $params['importType']
				)
			);
		} else {
			\OC_FileProxy::$enabled = $proxyStatus;
			$response->bailOut(App::$l10n->t('Error moving file to imports folder.'));
		}
		return $response;
	}

	/**
	 * @NoAdminRequired
	 */
	public function start() {
		$request = $this->request;
		$response = new JSONResponse();
		$params = $this->request->urlParams;
		$app = new App(\OCP\User::getUser());
		$addressBookId = $params['addressBookId'];
		$format = $params['importType'];

		$addressBook = $app->getAddressBook($params['backend'], $addressBookId);
		if(!$addressBook->hasPermission(\OCP\PERMISSION_CREATE)) {
			$response->setStatus('403');
			$response->bailOut(App::$l10n->t('You do not have permissions to import into this address book.'));
			return $response;
		}

		$filename = isset($request->post['filename']) ? $request->post['filename'] : null;
		$progresskey = isset($request->post['progresskey']) ? $request->post['progresskey'] : null;

		if(is_null($filename)) {
			$response->bailOut(App::$l10n->t('File name missing from request.'));
			return $response;
		}

		if(is_null($progresskey)) {
			$response->bailOut(App::$l10n->t('Progress key missing from request.'));
			return $response;
		}

		$filename = strtr($filename, array('/' => '', "\\" => ''));
		if(\OC\Files\Filesystem::isFileBlacklisted($filename)) {
			$response->bailOut(App::$l10n->t('Attempt to access blacklisted file:') . $filename);
			return $response;
		}
		$view = \OCP\Files::getStorage('contacts');
		$proxyStatus = \OC_FileProxy::$enabled;
		\OC_FileProxy::$enabled = false;
		$file = $view->file_get_contents('/imports/' . $filename);
		\OC_FileProxy::$enabled = $proxyStatus;

		$importManager = new ImportManager();
		
		$formatList = $importManager->getTypes();
		
		$found = false;
		$parts = array();
		foreach ($formatList as $formatName => $formatDisplayName) {
			if ($formatName == $format) {
				$parts = $importManager->importFile($view->getLocalFile('/imports/' . $filename), $formatName);
				$found = true;
			}
		}
		
		if (!$found) {
			// detect file type
			$mostLikelyName = "";
			$mostLikelyValue = 0;
			$probability = $importManager->detectFileType($view->getLocalFile('/imports/' . $filename));
			foreach ($probability as $probName => $probValue) {
				if ($probValue > $mostLikelyValue) {
					$mostLikelyName = $probName;
					$mostLikelyValue = $probValue;
				}
			}
			
			if ($mostLikelyValue > 0) {
				// found one (most likely...)
				$parts = $importManager->importFile($view->getLocalFile('/imports/' . $filename), $mostLikelyName);
			}
		}
		
		if ($parts) {
			//import the contacts
			$imported = 0;
			$failed = 0;
			$processed = 0;
			$total = count($parts);

			foreach($parts as $part) {
				/**
				 * TODO
				 * - Check if a contact with identical UID exists.
				 * - If so, fetch that contact and call $contact->mergeFromVCard($part);
				 * - Increment $updated var (not present yet.)
				 * - continue
				 */
				try {
					$id = $addressBook->addChild($part);
					if($id) {
						$imported++;
						$favourites = $part->select('X-FAVOURITES');
						foreach ($favourites as $favourite) {
							if ($favourite->getValue() == 'yes') {
								$this->tags->addToFavorites($id);
							}
						}
					} else {
						$failed++;
					}
				} catch (\Exception $e) {
					$response->debug('Error importing vcard: ' . $e->getMessage() . $nl . $part->serialize());
					$failed++;
				}
				$processed++;
				$this->writeProcess($processed, $total, $progresskey);
			}
		} else {
			$imported = 0;
			$failed = 0;
			$processed = 0;
			$total = 0;
		}

		$this->cleanup($view, $filename, $progresskey, $response);
		//done the import
		sleep(3); // Give client side a chance to read the progress.
		$response->setParams(
			array(
				'backend' => $params['backend'],
				'addressBookId' => $params['addressBookId'],
				'importType' => $params['importType'],
				'imported' => $imported,
				'count' => $processed,
				'total' => $total,
				'failed' => $failed,
			)
		);
		return $response;
	}

	/**
	 * @param $pct
	 * @param $total
	 * @param $progresskey
	 */
	protected function writeProcess($pct, $total, $progresskey) {
		$this->cache->set($progresskey, $pct, 300);
		$this->cache->set($progresskey . '_total', $total, 300);
	}

	/**
	 * @param $view
	 * @param $filename
	 * @param $progresskey
	 * @param $response
	 */
	protected function cleanup($view, $filename, $progresskey, $response) {
		if (!$view->unlink('/imports/' . $filename)) {
			$response->debug('Unable to unlink /imports/' . $filename);
		}
		$this->cache->remove($progresskey);
		$this->cache->remove($progresskey . '_total');
	}

	/**
	 * @NoAdminRequired
	 */
	public function status() {
		$request = $this->request;
		$response = new JSONResponse();

		$progresskey = isset($request->get['progresskey']) ? $request->get['progresskey'] : null;
		if(is_null($progresskey)) {
			$response->bailOut(App::$l10n->t('Progress key missing from request.'));
			return $response;
		}

		$response->setParams(array('progress' => $this->cache->get($progresskey), 'total' => $this->cache->get($progresskey.'_total') ));
		return $response;
	}
}
