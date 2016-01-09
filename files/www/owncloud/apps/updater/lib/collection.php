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

class Collection {
	private $notReadable = array();
	private $notWritable = array();
	
	public function addNotReadable($item) {
		if (!in_array($item, $this->notReadable)) {
			$this->notReadable[] = $item;
		}
	}
	
	public function addNotWritable($item) {
		if (!in_array($item, $this->notWritable)) {
			$this->notWritable[] = $item;
		}
	}
	
	public function getNotReadable(){
		return $this->notReadable;
	}
	
	public function getNotWritable(){
		return $this->notWritable;
	}
}