<?php

namespace li3_hansd\extensions\util;

class Sanitze {

	public static function implodeSpaces($string, $trim=true) {
		$string = preg_replace('/ +/', ' ', $string);
		return ($trim ? trim($string) : $string);
	}

}