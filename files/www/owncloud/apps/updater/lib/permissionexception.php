<?php

/**
 * ownCloud - Updater plugin
 *
 * @author Victor Dubiniuk
 * @copyright 2014 Victor Dubiniuk victor.dubiniuk@gmail.com
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 */


namespace OCA\Updater;

class PermissionException extends \Exception{

	/**
	 * @var \OCA\Updater\Collection
	 */
	private $collection;
	
	public function getExtendedMessage(){
		$message = '';
		if (count($this->collection->getNotReadable())) {
			$message .= App::$l10n->t('Make sure that your web server has read access to the following files and directories:');
			$message .= '<br />' . implode('<br />', $this->collection->getNotReadable());
			$message .= '<br /><br />';
		}
		if (count($this->collection->getNotWritable())) {
			$message .= App::$l10n->t('Make sure that your web server has write access to the following files and directories:');
			$message .= '<br />' . implode('<br />', $this->collection->getNotWritable());
		}
		return $message;
	}
	public function setCollection(\OCA\Updater\Collection $collection){
		$this->collection = $collection;
		return $this;
	} 
}
