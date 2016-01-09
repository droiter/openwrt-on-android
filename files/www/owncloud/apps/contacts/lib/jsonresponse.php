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
use OCP\AppFramework\Http\JSONResponse as OriginalResponse,
	OCP\AppFramework\Http;


/**
 * A renderer for JSON calls
 */
class JSONResponse extends OriginalResponse {

	public function __construct($params = array(), $statusCode = Http::STATUS_OK) {
		parent::__construct(array(), $statusCode);
		$this->data = $params;
	}

	/**
	 * Sets values in the data json array
	 * @param array|object $params an array or object which will be transformed
	 *                             to JSON
	 */
	public function setParams(array $params) {
		$this->setData($params);
		return $this;
	}

	public function setData($data) {
		$this->data = $data;
		return $this;
	}

	public function setStatus($status) {
		parent::setStatus($status);
		return $this;
	}

	/**
	 * in case we want to render an error message, also logs into the owncloud log
	 * @param string $message the error message
	 */
	public function setErrorMessage($message){
		$this->error = true;
		$this->data = array('status' => 'error', 'data' => array('message' => $message));
		return $this;
	}

	public function bailOut($msg, $tracelevel = 1, $debuglevel = \OCP\Util::ERROR) {
		if($msg instanceof \Exception) {
			$this->setStatus($msg->getCode());
			$msg = $msg->getMessage();
		}
		$this->setErrorMessage($msg);
		return $this->debug($msg, $tracelevel, $debuglevel);
	}

	public function debug($msg, $tracelevel = 0, $debuglevel = \OCP\Util::DEBUG) {
		if(!is_numeric($tracelevel)) {
			return $this;
		}

		if(PHP_VERSION >= "5.4") {
			$call = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $tracelevel + 1);
		} else {
			$call = debug_backtrace(false);
		}

		$call = $call[$tracelevel];
		if($debuglevel !== false) {
			\OCP\Util::writeLog('contacts',
				$call['file'].'. Line: '.$call['line'].': '.$msg,
				$debuglevel);
		}
		return $this;
	}

}