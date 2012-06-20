<?php

namespace li3_hansd\extensions\analysis;

use \lithium\analysis\Logger;
use \lithium\analysis\Debugger as li3Debugger;

class Debugger {

	public static function dump($var, $msg='', $level='debug') {
		$msg .= $msg ? ' - ' : '';
		Logger::$level($msg . li3Debugger::export($var));
	}

	public static function log($info, $msg='', $level='debug') {
		$msg .= $msg ? ' - ' : '';
		Logger::$level($msg . $info);
	}

}