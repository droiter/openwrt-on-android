<?php
/**
 * ownCloud - Utility class for VObject properties
 *
 * @author Thomas Tanghus
 * @copyright 2013-2014 Thomas Tanghus (thomas@tanghus.net)
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

use OCA\Contacts\App;

Properties::$l10n = \OCP\Util::getL10N('contacts');

Class Properties {

	const THUMBNAIL_PREFIX = 'contact-thumbnail-';
	const THUMBNAIL_SIZE = 32;

	private static $deleteindexstmt;
	private static $updateindexstmt;
	protected static $cardsTableName = '*PREFIX*contacts_cards';
	protected static $indexTableName = '*PREFIX*contacts_cards_properties';

	/**
	 * @brief language object for calendar app
	 *
	 * @var OC_L10N
	 */
	public static $l10n;

	/**
	 * Properties there can be more than one of.
	 *
	 * @var array
	 */
	public static $multiProperties = array('EMAIL', 'TEL', 'IMPP', 'ADR', 'URL');

	/**
	 * Properties to index.
	 *
	 * @var array
	 */
	public static $indexProperties = array(
		'BDAY', 'UID', 'N', 'FN', 'TITLE', 'ROLE', 'NOTE', 'NICKNAME',
		'ORG', 'CATEGORIES', 'EMAIL', 'TEL', 'IMPP', 'ADR', 'URL', 'GEO');

	/**
	 * Get options for IMPP properties
	 * @param string $im
	 * @return array of vcard prop => label
	 */
	public static function getIMOptions($im = null) {
		$l10n = self::$l10n;
		$ims = array(
				'jabber' => array(
					'displayname' => (string)$l10n->t('Jabber'),
					'xname' => 'X-JABBER',
					'protocol' => 'xmpp',
				),
				'sip' => array(
					'displayname' => (string)$l10n->t('Internet call'),
					'xname' => 'X-SIP',
					'protocol' => 'sip',
				),
				'aim' => array(
					'displayname' => (string)$l10n->t('AIM'),
					'xname' => 'X-AIM',
					'protocol' => 'aim',
				),
				'msn' => array(
					'displayname' => (string)$l10n->t('MSN'),
					'xname' => 'X-MSN',
					'protocol' => 'msn',
				),
				'twitter' => array(
					'displayname' => (string)$l10n->t('Twitter'),
					'xname' => 'X-TWITTER',
					'protocol' => 'twitter',
				),
				'googletalk' => array(
					'displayname' => (string)$l10n->t('GoogleTalk'),
					'xname' => null,
					'protocol' => 'xmpp',
				),
				'facebook' => array(
					'displayname' => (string)$l10n->t('Facebook'),
					'xname' => null,
					'protocol' => 'xmpp',
				),
				'xmpp' => array(
					'displayname' => (string)$l10n->t('XMPP'),
					'xname' => null,
					'protocol' => 'xmpp',
				),
				'icq' => array(
					'displayname' => (string)$l10n->t('ICQ'),
					'xname' => 'X-ICQ',
					'protocol' => 'icq',
				),
				'yahoo' => array(
					'displayname' => (string)$l10n->t('Yahoo'),
					'xname' => 'X-YAHOO',
					'protocol' => 'ymsgr',
				),
				'skype' => array(
					'displayname' => (string)$l10n->t('Skype'),
					'xname' => 'X-SKYPE',
					'protocol' => 'skype',
				),
				'qq' => array(
					'displayname' => (string)$l10n->t('QQ'),
					'xname' => 'X-SKYPE',
					'protocol' => 'x-apple',
				),
				'gadugadu' => array(
					'displayname' => (string)$l10n->t('GaduGadu'),
					'xname' => 'X-SKYPE',
					'protocol' => 'x-apple',
				),
				'owncloud-handle' => array(
				    'displayname' => (string)$l10n->t('ownCloud'),
				    'xname' => null,
				    'protocol' => 'x-owncloud-handle'
				),
		);
		if(is_null($im)) {
			return $ims;
		} else {
			$ims['ymsgr'] = $ims['yahoo'];
			$ims['gtalk'] = $ims['googletalk'];
			return isset($ims[$im]) ? $ims[$im] : null;
		}
	}

	/**
	 * Get standard set of TYPE values for different properties.
	 *
	 * @param string $prop
	 * @return array Type values for property $prop
	 */
	public static function getTypesForProperty($prop) {
		$l = self::$l10n;
		switch($prop) {
			case 'LABEL':
			case 'ADR':
			case 'IMPP':
				return array(
					'WORK' => (string)$l->t('Work'),
					'HOME' => (string)$l->t('Home'),
					'OTHER' => (string)$l->t('Other'),
				);
			case 'TEL':
				return array(
					'HOME'  =>  (string)$l->t('Home'),
					'CELL'  =>  (string)$l->t('Mobile'),
					'WORK'  =>  (string)$l->t('Work'),
					'TEXT'  =>  (string)$l->t('Text'),
					'VOICE' =>  (string)$l->t('Voice'),
					'MSG'   =>  (string)$l->t('Message'),
					'FAX'   =>  (string)$l->t('Fax'),
					'VIDEO' =>  (string)$l->t('Video'),
					'PAGER' =>  (string)$l->t('Pager'),
					'OTHER' =>  (string)$l->t('Other'),
				);
			case 'EMAIL':
				return array(
					'WORK' => (string)$l->t('Work'),
					'HOME' => (string)$l->t('Home'),
					'INTERNET' => (string)$l->t('Internet'),
					'OTHER' =>  (string)$l->t('Other'),
				);
		}
	}

	/**
	 * @brief returns the default categories of ownCloud
	 * @return (array) $categories
	 */
	public static function getDefaultCategories() {
		$l10n = self::$l10n;
		return array(
			(string)$l10n->t('Friends'),
			(string)$l10n->t('Family'),
			(string)$l10n->t('Work'),
			(string)$l10n->t('Other'),
		);
	}

	public static function generateUID($app = 'contacts') {
		$uuid = new UUID();
		return $uuid->get() . '@' . \OCP\Util::getServerHostName();
	}

	/**
	 * Purge indexed properties.
	 *
	 * @param string[] $ids
	 */
	public static function purgeIndexes($ids) {
		\OCP\Util::writeLog('contacts', __METHOD__.', ids: ' . print_r($ids, true), \OCP\Util::DEBUG);
		if(!is_array($ids) || count($ids) === 0) {
			throw new \Exception(__METHOD__ . ' takes only arrays with at least one value.');
		}
		\OCP\Util::writeLog('contacts', __METHOD__.', ids: ' . print_r($ids, true), \OCP\Util::DEBUG);
		if(!isset(self::$deleteindexstmt)) {
			self::$deleteindexstmt
				= \OCP\DB::prepare('DELETE FROM `' . self::$indexTableName . '`'
					. ' WHERE `contactid` IN (' . str_repeat('?,', count($ids)-1) . '?) ');
		}
		try {
			self::$deleteindexstmt->execute($ids);
		} catch(\Exception $e) {
			\OCP\Util::writeLog('contacts', __METHOD__.
				', exception: ' . $e->getMessage(), \OCP\Util::ERROR);
			\OCP\Util::writeLog('contacts', __METHOD__.', ids: '
				. $ids, \OCP\Util::DEBUG);
			return false;
		}
	}

	/**
	 * Update the contact property index.
	 *
	 * If vcard is null the properties for that contact will be purged.
	 * If it is a valid object the old properties will first be purged
	 * and the current properties indexed.
	 *
	 * @param string $contactid
	 * @param \OCA\VObject\VCard|null $vcard
	 */
	public static function updateIndex($contactid, $vcard = null) {
		self::purgeIndexes(array($contactid));

		if(is_null($vcard)) {
			return;
		}

		if(!isset(self::$updateindexstmt)) {
			self::$updateindexstmt = \OCP\DB::prepare( 'INSERT INTO `' . self::$indexTableName . '` '
				. '(`userid`, `contactid`,`name`,`value`,`preferred`) VALUES(?,?,?,?,?)' );
		}
		foreach($vcard->children as $property) {
			if(!in_array($property->name, self::$indexProperties)) {
				continue;
			}
			$preferred = 0;
			foreach($property->parameters as $parameter) {
				if($parameter->name == 'TYPE' && strtoupper($parameter->value) == 'PREF') {
					$preferred = 1;
					break;
				}
			}
			try {
				$result = self::$updateindexstmt->execute(
					array(
						\OCP\User::getUser(),
						$contactid,
						$property->name,
						substr($property->value, 0, 254),
						$preferred,
					)
				);
				if (\OCP\DB::isError($result)) {
					\OCP\Util::writeLog('contacts', __METHOD__. 'DB error: '
						. \OC_DB::getErrorMessage($result), \OCP\Util::ERROR);
					return false;
				}
			} catch(\Exception $e) {
				\OCP\Util::writeLog('contacts', __METHOD__.', exception: '.$e->getMessage(), \OCP\Util::ERROR);
				return false;
			}
		}
	}

	public static function cacheThumbnail($backendName, $addressBookId, $contactId,
		\OCP\Image $image = null, $vcard = null, $options = array()
	) {
		$cache = \OC::$server->getCache();
		$key = self::THUMBNAIL_PREFIX . $backendName . '::' . $addressBookId . '::' . $contactId;
		//$cache->remove($key);
		$haskey = $cache->hasKey($key);

		if (!array_key_exists('remove', $options) && !array_key_exists('update', $options)){
			if ($cache->hasKey($key) && $image === null){
				return $cache->get($key);
			}
		} else {
			if ($options['remove'] === false && $options['update'] === false){
				return $cache->get($key);
			}
		}


		if (isset($options['remove']) && $options['remove']) {
			$cache->remove($key);
			if(!isset($options['update']) || !$options['update']) {
				return false;
			}
		}

		if (is_null($image)) {
			if (is_null($vcard)) {
				$app = new App();
				$vcard = $app->getContact($backendName, $addressBookId, $contactId);
			}
			$image = new \OCP\Image();
			if (!isset($vcard->PHOTO) || !$image->loadFromBase64((string)$vcard->PHOTO)) {
				return false;
			}
		}

		if (!$image->centerCrop()) {
			\OCP\Util::writeLog('contacts',
				__METHOD__ .'. Couldn\'t crop thumbnail for ID ' . $key,
				\OCP\Util::ERROR);
			return false;
		}

		if (!$image->resize(self::THUMBNAIL_SIZE)) {
			\OCP\Util::writeLog('contacts',
				__METHOD__ . '. Couldn\'t resize thumbnail for ID ' . $key,
				\OCP\Util::ERROR);
			return false;
		}

		 // Cache as base64 for around a month
		$cache->set($key, strval($image), 3000000);
		return $cache->get($key);
	}

}
