<?php

namespace li3_hansd\extensions\util;

/**
 * UUID value and utility class. An instance acts a UUID value class.
 *
 * Based on cody posted by Andrew Moore 04-Dec-2009 08:45
 * Generates valid [RFC 4122][ref-rfc-4122] compliant Universally Unique IDentifiers
 * (UUID) version 4. This is a pure PHP implementation.
 *
 * Adapted from code published by [Andrew Moore][ref-php-94959].
 *
 * The formal definition of the UUID string representation is provided by the following ABNF:
 *    UUID                   = time-low "-" time-mid "-"
 *                             time-high-and-version "-"
 *                             clock-seq-and-reserved
 *                             clock-seq-low "-" node
 *    time-low               = 4hexOctet // 32-bit
 *    time-mid               = 2hexOctet // 16-bit
 *    time-high-and-version  = 2hexOctet // 16 bit
 *    clock-seq-and-reserved = hexOctet  //  8 bit
 *    clock-seq-low          = hexOctet  //  8 bit
 *    node                   = 6hexOctet // 48 bit
 *    hexOctet               = hexDigit hexDigit
 *
 * For UUID version 4, the node field is a randomly or pseudo-randomly
 * generated 48-bit value.
 *
 * [ref-rfc-4122]: http://www.ietf.org/rfc/rfc4122.txt
 * [ref-php-94959]: http://www.php.net/manual/en/function.uniqid.php#94959
 *
 */
class UUID {

	/**
	 * Holds the current UUID value for the value class.
	 *
	 * @var string
	 */
	protected $uuid = null;


	/**
	 * The official format for a string based UUID
	 *
	 * @var string
	 */
	protected static $_format =
		'[\da-f]{8}\-?[\da-f]{4}\-?[\da-f]{4}\-?[\da-f]{4}\-?[\da-f]{12}';


	/**
	 * Validates if the given string UUID is valid.
	 *
	 * UUID may optionally be enclosed in `{}`
	 *
	 * @param string $uuid
	 * @return boolean
	 */
	public static function isValid($uuid) {
		return (preg_match( '/^\{?' . self::$_format . '\}?$/i', $uuid) === 1);
	}


	/**
	 * Convert string UUID to binary
	 *
	 * @param string $uuid
	 */
	public static function asBinary($uuid) {
		if (!self::valid($uuid)) {
			return false;
		}
		$hex = static::asHexString($uuid);
		$bin = '';
		for ($i = 0, $max = strlen($hex); $i < $max; $i += 2) {
			$bin .= chr(hexdec($hex[$i].$hex[$i + 1]));
		}
		return $bin;
	}

	public static function asHexString($uuid) {
		if (!self::valid($uuid)) {
			return false;
		}
		return str_replace(array('-','{','}'), '', $uuid);
	}

	/**
	 * Convert binary UUID to string
	 *
	 * @param unknown_type $uuid
	 */
	public static function asString($uuid){
		$str = '';
		for ($i = 0, $max = strlen($uuid); $i < $max; $i++) {
			if ($i >= 4 AND $i <= 10 AND ($i % 2) === 0) {
				$str .= '-';
			}
			$str .= sprintf('%02x', ord($uuid[$i]));
		}
		return $str;
	}


	/**
	 * Version 4 UUIDs are pseudo-random.
	 *
	 * The formal definition of the UUID string representation is provided by the following ABNF:
	 *    UUID                   = time-low "-" time-mid "-"
	 *                             time-high-and-version "-"
	 *                             clock-seq-and-reserved
	 *                             clock-seq-low "-" node
	 *    time-low               = 4hexOctet // 32-bit
	 *    time-mid               = 2hexOctet // 16-bit
	 *    time-high-and-version  = 2hexOctet // 16 bit
	 *    clock-seq-and-reserved = hexOctet  //  8 bit
	 *    clock-seq-low          = hexOctet  //  8 bit
	 *    node                   = 6hexOctet // 48 bit
	 *    hexOctet               = hexDigit hexDigit
	 *
	 * For UUID version 4, the node field is a randomly or pseudo-randomly
	 * generated 48-bit value.
	 *
	 * @return  string
	 */
	public static function v4(){
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	public static function generate() {
		return self::v4();
	}

	/**
	 * The instance of the class acts a non-mutatble value class.
	 */
	public function __construct() {
		$this->uuid = self::v4();
		return $this;
	}

// 	public function asString() {
// 		return $this->uuid;
// 	}

// 	public function asBinary() {
// 		return self::asBinary($this->uuid);
// 	}

	public function __toString(){
		return $this->uuid;
	}

	public function __invoke(){
		return $this->uuid;
	}

	public function __call($name, $arguments) {
		self::$name($this->uuid);
	}

}