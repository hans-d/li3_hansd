<?php

namespace li3_hansd\extensions\analysis;

use \lithium\analysis\Logger;
use \lithium\analysis\Debugger as li3Debugger;

class Debugger {

	public static function dump($var, $msg='', array $options=array()) {
		$options += array('level' => 'debug', 'dump' => 'li3');
		$level = $options['level'];
		$msg .= $msg ? ' - ' : '';
		switch ($options['dump']) {
			case 'print_r':
				Logger::$level($msg . print_r($var, true));
				break;
			case 'li3':
			default:
				Logger::$level($msg . li3Debugger::export($var));
				break;
		}
	}

	public static function log($info, $msg='', array $options=array()) {
		$options += array('level' => 'debug');
		$level = $options['level'];
		$msg .= $msg ? ' - ' : '';
		Logger::$level($msg . $info);
	}

}