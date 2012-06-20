<?php

namespace li3_hansd\extensions\analysis;

class SubjectLogger extends \lithium\analysis\Logger {

	/**
	 * Reusing priorities as subjects. The subject must be the key.
	 * As long as the file adapter is used, the value is not of
	 * importance.
	 */

	protected static $_configurations = array();
	protected static $_priorities = array();

	public static function __callStatic($priority, $params) {

		$params += array(null, array());
		return static::write($priority, $params[0], $params[1]);
	}

	public static function add($subjects) {
		if (!is_array($subjects)) {
			$subjects = (array)$subjects;
		}
		foreach ($subjects as $subject) {
			static::$_priorities[$subject] = $subject;
		}
	}

	protected static function _configsByPriority($priority, $message, array $options = array()) {

		$configs = array();

		foreach (array('default', 'dev', $priority) as $name) {
			if (!isset(static::$_configurations[$name])) {
				continue;
			}
			$config = static::config($name);
			$method = static::adapter($name)->write($priority, $message, $options);
			$method ? $configs[$name] = $method : null;
		}
		return $configs;
	}

	/**
	 *
	 * TODO: make $message accept array for easy string insert
	 *
	 * @see
	 */
	public static function write($priority, $message, array $options = array()) {
		return parent::write($priority, $message, $options );
	}

}