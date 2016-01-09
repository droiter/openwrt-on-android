<?php

class OC_Connector_Sabre_CalDAV extends \Sabre\CalDAV\Backend\AbstractBackend {
	/**
	 * List of CalDAV properties, and how they map to database fieldnames
	 *
	 * Add your own properties by simply adding on to this array
	 *
	 * @var array
	 */
	public $propertyMap = array(
		'{DAV:}displayname'						  => 'displayname',
		'{urn:ietf:params:xml:ns:caldav}calendar-description' => 'description',
		'{urn:ietf:params:xml:ns:caldav}calendar-timezone'	=> 'timezone',
		'{http://apple.com/ns/ical/}calendar-order'  => 'calendarorder',
		'{http://apple.com/ns/ical/}calendar-color'  => 'calendarcolor',
	);

	/**
	 * Returns a list of calendars for a principal.
	 *
	 * Every project is an array with the following keys:
	 *  * id, a unique id that will be used by other functions to modify the
	 *	calendar. This can be the same as the uri or a database key.
	 *  * uri, which the basename of the uri with which the calendar is
	 *	accessed.
	 *  * principalUri. The owner of the calendar. Almost always the same as
	 *	principalUri passed to this method.
	 *
	 * Furthermore it can contain webdav properties in clark notation. A very
	 * common one is '{DAV:}displayname'.
	 *
	 * @param string $principalUri
	 * @return array
	 */
	public function getCalendarsForUser($principalUri) {
		$raw = OC_Calendar_Calendar::allCalendarsWherePrincipalURIIs($principalUri);

		$calendars = array();
		foreach( $raw as $row ) {
			$components = explode(',',$row['components']);

			if($row['userid'] != OCP\USER::getUser()) {
				$row['uri'] = $row['uri'] . '_shared_by_' . $row['userid'];
			}
			$calendar = array(
				'id' => $row['id'],
				'uri' => $row['uri'],
				'principaluri' => 'principals/'.$row['userid'],
				'{' . \Sabre\CalDAV\Plugin::NS_CALENDARSERVER . '}getctag' => $row['ctag']?$row['ctag']:'0',
				'{' . \Sabre\CalDAV\Plugin::NS_CALDAV . '}supported-calendar-component-set'
					=> new \Sabre\CalDAV\Property\SupportedCalendarComponentSet($components),
			);

			foreach($this->propertyMap as $xmlName=>$dbName) {
				$calendar[$xmlName] = isset($row[$dbName]) ? $row[$dbName] : '';
			}

			$calendars[] = $calendar;
		}
		if(\OCP\App::isEnabled('contacts')) {
			$ctag = 0;
			$app = new \OCA\Contacts\App();
			$addressBooks = $app->getAddressBooksForUser();
			foreach($addressBooks as $addressBook) {
				$tmp = $addressBook->lastModified();
				if(!is_null($tmp)) {
					$ctag = max($ctag, $tmp);
				}
			}
			$ctag++;
			$calendars[] = array(
				'id' => 'contact_birthdays',
				'uri' => 'contact_birthdays',
				'{DAV:}displayname' => (string)OC_Calendar_App::$l10n->t('Contact birthdays'),
				'principaluri' => 'principals/contact_birthdays',
				'{' . \Sabre\CalDAV\Plugin::NS_CALENDARSERVER . '}getctag' => $ctag,
				'{' . \Sabre\CalDAV\Plugin::NS_CALDAV . '}supported-calendar-component-set'
					=> new \Sabre\CalDAV\Property\SupportedCalendarComponentSet(array('VEVENT')),
				'{http://apple.com/ns/ical/}calendar-color' => '#CCCCCC',
			);
		}
		return $calendars;
	}

	/**
	 * Creates a new calendar for a principal.
	 *
	 * If the creation was a success, an id must be returned that can be used to reference
	 * this calendar in other methods, such as updateCalendar
	 *
	 * @param string $principalUri
	 * @param string $calendarUri
	 * @param array $properties
	 * @return mixed
	 */
	public function createCalendar($principalUri,$calendarUri, array $properties) {
		$fieldNames = array(
			'principaluri',
			'uri',
			'ctag',
		);
		$values = array(
			':principaluri' => $principalUri,
			':uri'		  => $calendarUri,
			':ctag'		 => 1,
		);

		// Default value
		$sccs = '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set';
		$fieldNames[] = 'components';
		if (!isset($properties[$sccs])) {
			$values[':components'] = 'VEVENT,VTODO';
		} else {
			if (!($properties[$sccs] instanceof \Sabre\CalDAV\Property\SupportedCalendarComponentSet)) {
				throw new \Sabre\DAV\Exception('The ' . $sccs . ' property must be of type: \Sabre\CalDAV\Property\SupportedCalendarComponentSet');
			}
			$values[':components'] = implode(',',$properties[$sccs]->getValue());
		}

		foreach($this->propertyMap as $xmlName=>$dbName) {
			if (isset($properties[$xmlName])) {

				$myValue = $properties[$xmlName];
				$values[':' . $dbName] = $properties[$xmlName];
				$fieldNames[] = $dbName;
			}
		}

		if(!isset($newValues['displayname'])) $newValues['displayname'] = 'unnamed';
		if(!isset($newValues['components'])) $newValues['components'] = 'VEVENT,VTODO';
		if(!isset($newValues['timezone'])) $newValues['timezone'] = null;
		if(!isset($newValues['calendarorder'])) $newValues['calendarorder'] = 0;
		if(!isset($newValues['calendarcolor'])) $newValues['calendarcolor'] = null;
		if(!is_null($newValues['calendarcolor']) && strlen($newValues['calendarcolor']) == 9) {
			$newValues['calendarcolor'] = substr($newValues['calendarcolor'], 0, 7);
		}

		return OC_Calendar_Calendar::addCalendarFromDAVData($principalUri,$calendarUri,$newValues['displayname'],$newValues['components'],$newValues['timezone'],$newValues['calendarorder'],$newValues['calendarcolor']);
	}

	/**
	 * Updates a calendars properties
	 *
	 * The properties array uses the propertyName in clark-notation as key,
	 * and the array value for the property value. In the case a property
	 * should be deleted, the property value will be null.
	 *
	 * This method must be atomic. If one property cannot be changed, the
	 * entire operation must fail.
	 *
	 * If the operation was successful, true can be returned.
	 * If the operation failed, false can be returned.
	 *
	 * Deletion of a non-existant property is always succesful.
	 *
	 * Lastly, it is optional to return detailed information about any
	 * failures. In this case an array should be returned with the following
	 * structure:
	 *
	 * array(
	 *   403 => array(
	 *	  '{DAV:}displayname' => null,
	 *   ),
	 *   424 => array(
	 *	  '{DAV:}owner' => null,
	 *   )
	 * )
	 *
	 * In this example it was forbidden to update {DAV:}displayname.
	 * (403 Forbidden), which in turn also caused {DAV:}owner to fail
	 * (424 Failed Dependency) because the request needs to be atomic.
	 *
	 * @param string $calendarId
	 * @param array $properties
	 * @return bool|array
	 */
	public function updateCalendar($calendarId, array $properties) {

		$newValues = array();
		$result = array(
			200 => array(), // Ok
			403 => array(), // Forbidden
			424 => array(), // Failed Dependency
		);

		$hasError = false;

		foreach($properties as $propertyName=>$propertyValue) {

			// We don't know about this property.
			if (!isset($this->propertyMap[$propertyName])) {
				$hasError = true;
				$result[403][$propertyName] = null;
				unset($properties[$propertyName]);
				continue;
			}

			$fieldName = $this->propertyMap[$propertyName];
			$newValues[$fieldName] = $propertyValue;

		}

		// If there were any errors we need to fail the request
		if ($hasError) {
			// Properties has the remaining properties
			foreach($properties as $propertyName=>$propertyValue) {
				$result[424][$propertyName] = null;
			}

			// Removing unused statuscodes for cleanliness
			foreach($result as $status=>$properties) {
				if (is_array($properties) && count($properties)===0) unset($result[$status]);
			}

			return $result;

		}

		// Success
		if(!isset($newValues['displayname'])) $newValues['displayname'] = null;
		if(!isset($newValues['timezone'])) $newValues['timezone'] = null;
		if(!isset($newValues['calendarorder'])) $newValues['calendarorder'] = null;
		if(!isset($newValues['calendarcolor'])) $newValues['calendarcolor'] = null;
		if(!is_null($newValues['calendarcolor']) && strlen($newValues['calendarcolor']) == 9) {
			$newValues['calendarcolor'] = substr($newValues['calendarcolor'], 0, 7);
		}

		OC_Calendar_Calendar::editCalendar($calendarId,$newValues['displayname'],null,$newValues['timezone'],$newValues['calendarorder'],$newValues['calendarcolor']);

		return true;

	}

	/**
	 * Delete a calendar and all it's objects
	 *
	 * @param string $calendarId
	 * @return void
	 */
	public function deleteCalendar($calendarId) {
	    if(preg_match( '=iCal/[1-4]?.*Mac OS X/10.[1-6](.[0-9])?=', $_SERVER['HTTP_USER_AGENT'] )) {
	    	throw new \Sabre\DAV\Exception\Forbidden("Action is not possible with OSX 10.6.x", 403);
		}

		OC_Calendar_Calendar::deleteCalendar($calendarId);
	}

	/**
	 * Returns all calendar objects within a calendar object.
	 *
	 * Every item contains an array with the following keys:
	 *   * id - unique identifier which will be used for subsequent updates
	 *   * calendardata - The iCalendar-compatible calnedar data
	 *   * uri - a unique key which will be used to construct the uri. This can be any arbitrary string.
	 *   * lastmodified - a timestamp of the last modification time
	 *   * etag - An arbitrary string, surrounded by double-quotes. (e.g.:
	 *   '  "abcdef"')
	 *   * calendarid - The calendarid as it was passed to this function.
	 *
	 * Note that the etag is optional, but it's highly encouraged to return for
	 * speed reasons.
	 *
	 * The calendardata is also optional. If it's not returned
	 * 'getCalendarObject' will be called later, which *is* expected to return
	 * calendardata.
	 *
	 * @param string $calendarId
	 * @return array
	 */
	public function getCalendarObjects($calendarId) {
		$data = array();
		if($calendarId === 'contact_birthdays') {
			$app = new \OCA\Contacts\App();
			$addressBooks = $app->getAddressBooksForUser();
			foreach($addressBooks as $addressBook) {
				foreach($addressBook->getChildren() as $contact) {
					$vevent = $contact->getBirthdayEvent();
					if(is_null($vevent)) {
						continue;
					}
					$data[] = $this->OCAddETag(array(
						'id' => 0,
						'calendarid' => 'contact_birthdays',
						'uri' => $addressBook->getBackend()->name.'::'.$addressBook->getId().'::'.$contact->getId().'.ics',
						'lastmodified' => $contact->lastModified(),
						'summary' => $vevent->SUMMARY,
						'calendardata' => $vevent->serialize()
					));
				}
			}
		} else {
			$calendar = OC_Calendar_Calendar::find($calendarId);
			$isShared = ($calendar['userid'] !== OCP\USER::getUser());

			foreach(OC_Calendar_Object::all($calendarId) as $row) {
				if (!$isShared) {
					$data[] = $this->OCAddETag($row);
				} else {
					if (substr_count($row['calendardata'], 'CLASS') === 0) {
						$data[] = $this->OCAddETag($row);
					} else {
						$object = OC_VObject::parse($row['calendardata']);
						if(!$object) {
							return false;
						}

						$isPrivate = false;
						$toCheck = array('VEVENT', 'VJOURNAL', 'VTODO');
						foreach ($toCheck as $type) {
							foreach ($object->select($type) as $vobject) {
								if (isset($vobject->{'CLASS'}) && $vobject->{'CLASS'}->getValue() === 'PRIVATE') {
									$isPrivate = true;
								}
							}
						}

						if ($isPrivate === false) {
							$data[] = $this->OCAddETag($row);
						}
					}
				}
			}
		}
		return $data;
	}

	/**
	 * Returns information from a single calendar object, based on it's object
	 * uri.
	 *
	 * The returned array must have the same keys as getCalendarObjects. The
	 * 'calendardata' object is required here though, while it's not required
	 * for getCalendarObjects.
	 *
	 * @param string $calendarId
	 * @param string $objectUri
	 * @return array
	 */
	public function getCalendarObject($calendarId,$objectUri) {
		if($calendarId === 'contact_birthdays') {
			$objectUri = substr($objectUri, 0, (strlen($objectUri) - 4));
			$objectUriArray = explode('::', $objectUri);
			if(count($objectUriArray) === 3) {
				$app = new \OCA\Contacts\App();
				list($backend, $addressBookId, $contactId) = $objectUriArray;
				$contact = $app->getContact($backend, $addressBookId, $contactId);
				$vevent = $contact->getBirthdayEvent();
				if(is_null($vevent)) {
					return false;
				}
				return $this->OCAddETag(array(
					'id' => 0,
					'calendarid' => 'contact_birthdays',
					'uri' => $contact->getBackend()->name.'::'.$contact->getParent()->getId().'::'.$contact->getId().'.ics',
					'lastmodified' => $contact->lastModified(),
					'calendardata' => $vevent->serialize()
				));
			}
		}
		$data = OC_Calendar_Object::findWhereDAVDataIs($calendarId,$objectUri);
		if(is_array($data)) {
			$data = $this->OCAddETag($data);
			$object = OC_VObject::parse($data['calendardata']);
			if(!$object) {
				return false;
			}
			$object = OC_Calendar_Object::cleanByAccessClass($data['id'], $object);
			$data['calendardata'] = $object->serialize();
		}
		return $data;
	}

	/**
	 * Creates a new calendar object.
	 *
	 * @param string $calendarId
	 * @param string $objectUri
	 * @param string $calendarData
	 * @return void
	 */
	public function createCalendarObject($calendarId,$objectUri,$calendarData) {
		OC_Calendar_Object::addFromDAVData($calendarId,$objectUri,$calendarData);
	}

	/**
	 * Updates an existing calendarobject, based on it's uri.
	 *
	 * @param string $calendarId
	 * @param string $objectUri
	 * @param string $calendarData
	 * @return void
	 */
	public function updateCalendarObject($calendarId,$objectUri,$calendarData) {
		OC_Calendar_Object::editFromDAVData($calendarId,$objectUri,$calendarData);
	}

	/**
	 * Deletes an existing calendar object.
	 *
	 * @param string $calendarId
	 * @param string $objectUri
	 * @return void
	 */
	public function deleteCalendarObject($calendarId,$objectUri) {
		OC_Calendar_Object::deleteFromDAVData($calendarId,$objectUri);
	}

	/**
	 * @brief Creates a etag
	 * @param array $row Database result
	 * @returns associative array
	 *
	 * Adds a key "etag" to the row
	 */
	private function OCAddETag($row) {
		$row['etag'] = '"'.md5($row['calendarid'].$row['uri'].$row['calendardata'].$row['lastmodified']).'"';
		return $row;
	}
}
