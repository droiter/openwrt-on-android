<?php

/**
 * ownCloud - Updater plugin
 *
 * @author Victor Dubiniuk
 * @copyright 2013 Victor Dubiniuk victor.dubiniuk@gmail.com
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 */

namespace OCA\Updater;

class Location_Apps extends Location {

	protected $type = 'apps';
	protected $appsToDisable = array();
	protected $appsToUpdate = array();

	protected function filterOld($pathArray) {
		return $pathArray;
	}
	
	public function update($tmpDir = '') {
		Helper::mkdir($tmpDir, true);
		$this->collect(true);
		try {
			foreach ($this->appsToUpdate as $appId) {
				$path = \OC_App::getAppPath($appId);
				if ($path) {
					if (!@file_exists($this->newBase . '/' . $appId)){
						$this->appsToDisable[$appId] = $appId;
					} else {
						Helper::move($path, $tmpDir . '/' . $appId);
					
						// ! reverted intentionally
						$this->done [] = array(
							'dst' => $path,
							'src' => $tmpDir . '/' . $appId
						);
					
						Helper::move($this->newBase . '/' . $appId, $path);
					}
				}
			}
			$this->finalize();
		} catch (\Exception $e) {
			$this->rollback(true);
			throw $e;
		}
	}

	protected function finalize() {
		foreach ($this->appsToDisable as $appId) {
			\OC_App::disable($appId);
		}
		parent::finalize();
	}

	protected function filterNew($pathArray) {
		return $pathArray;
	}

	public function collect($dryRun = false) {
		$dh = opendir($this->newBase);
		if (is_resource($dh)) {
			while (($file = readdir($dh)) !== false) {
				if ($file[0] != '.' && is_file($this->newBase . '/' . $file . '/appinfo/app.php')) {
					$this->appsToUpdate[$file] =  $file;
				}
			}
		}
	}

}
