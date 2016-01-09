<?php
namespace OCA\Contacts\Utils;

class UUID {

	protected $urand;

	public function __construct() {
		$this->urand = @fopen ( '/dev/urandom', 'rb' );
	}

	/**
	* @brief Generates a Universally Unique IDentifier, version 4.
	*
	* This function generates a truly random UUID. The built in CakePHP String::uuid() function
	* is not cryptographically secure. You should uses this function instead.
	* From http://php.net/manual/en/function.uniqid.php comments
	*
	* @see http://tools.ietf.org/html/rfc4122#section-4.4
	* @see http://en.wikipedia.org/wiki/UUID
	* @return string A UUID, made up of 32 hex digits and 4 hyphens.
	*/
	public function get() {

		$prBits = false;
		if (is_a($this, 'uuid')) {
			if (is_resource($this->urand)) {
				$prBits .= @fread($this->urand, 16);
			}
		}
		if (!$prBits) {
			$fp = @fopen('/dev/urandom', 'rb');
			if ($fp !== false) {
				$prBits .= @fread($fp, 16);
				@fclose ( $fp );
			} else {
				// If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
				$prBits = "";
				for($cnt = 0; $cnt < 16; $cnt ++) {
					$prBits .= chr(mt_rand(0, 255));
				}
			}
		}
		$timeLow = bin2hex(substr($prBits, 0, 4));
		$timeMid = bin2hex(substr($prBits, 4, 2));
		$timeHiAndVersion = bin2hex(substr($prBits, 6, 2));
		$clockSeqHiAndReserved = bin2hex(substr($prBits, 8, 2));
		$node = bin2hex(substr($prBits, 10, 6));

		/**
		* Set the four most significant bits (bits 12 through 15) of the
		* time_hi_and_version field to the 4-bit version number from
		* Section 4.1.3.
		* @see http://tools.ietf.org/html/rfc4122#section-4.1.3
		*/
		$timeHiAndVersion = hexdec($timeHiAndVersion);
		$timeHiAndVersion = $timeHiAndVersion >> 4;
		$timeHiAndVersion = $timeHiAndVersion | 0x4000;

		/**
		* Set the two most significant bits (bits 6 and 7) of the
		* clock_seq_hi_and_reserved to zero and one, respectively.
		*/
		$clockSeqHiAndReserved = hexdec($clockSeqHiAndReserved);
		$clockSeqHiAndReserved = $clockSeqHiAndReserved >> 2;
		$clockSeqHiAndReserved = $clockSeqHiAndReserved | 0x8000;

		return sprintf('%08s-%04s-%04x-%04x-%012s', $timeLow, $timeMid, $timeHiAndVersion, $clockSeqHiAndReserved, $node);
	}

}