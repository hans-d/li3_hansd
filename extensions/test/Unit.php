<?php

namespace li3_hansd\extensions\test;

use lithium\analysis\Logger;

/**
 * Base class for testing. Contains some helper functions.
 */
class Unit extends \lithium\test\Unit {


	/**
	 * Quickly dump a variable to a logger.
	 *
	 * @param unknown_type $variable
	 * @param unknown_type $msg
	 * @param unknown_type $level
	 */
	public static function dumpVar($variable, $msg='', $level='info') {
		Logger::$level((!$msg ? '': "$msg: ") . print_r($variable, true));
	}

	/**
	 * Quickly log a message to a logger.
	 *
	 * @param unknown_type $msg
	 * @param unknown_type $level
	 */
	public static function dump($msg, $level='info') {
		Logger::$level($msg);
	}

}
