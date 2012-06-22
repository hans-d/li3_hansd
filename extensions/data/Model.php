<?php

namespace li3_hansd\extensions\data;

use li3_hansd\extensions\util\UUID;
use lithium\core\ConfigException;
use lithium\util\Inflector;

class Model extends \lithium\data\Model {

	/**
	 * Default values for fields on `create()`.
	 *
	 * The database may also supply default values in its schema. The values
	 * here have precedence.
	 */
	protected static $_defaults = array();

	/**
	 *
	 * {{{
	 * protected static $_timestamped = array(
	 *     'create' => 'created_tstamp',
	 *     'all' => 'modified_tstamp',
	 * );
	 * }}}
	 *
	 */
	protected static $_timestamped = array();

	/**
	 *
	 * {{{
	 * protected static $_generatedId = 'uuid';
	 * }}}
	 */
	protected static $_generatedId = null;

	public static function __init() {
		static::_isBase(__CLASS__, true);
		parent::__init();
	}

	/**
	 * Apply model defined defaults.
	 *
	 * @see \lithium\data\Model::create()
	 */
	public static function create(array $data = array(), array $options = array()) {
		$defaults = static::$_defaults ?:  array();
		return parent::create($data + $defaults, $options);
	}

	public function toNow($entity, $fields) {
		$fields = (array)$fields;
		$now = static::getTimestamp();

		foreach ($fields as $field) {
			$entity->$field = $now;
		}
	}

	public static function getTimestamp() {
		return date('Y-m-d H:i:s');
	}

	public function autostamp($entity) {
		$states = array('create', 'update', 'all');
		$autostamp = is_array(static::$_timestamped) ? static::$_timestamped : null;
		if ($autostamp) {
			$autostamp = array_intersect_key($autostamp, array_flip($states));
			if ($entity->exists()) {
				unset($autostamp['create']);
			} else {
				unset($autostamp['update']);
			}
			$autostamp = array_keys($autostamp) != array() ? $autostamp : null;
		}
		if ($autostamp) {
			$now = static::getTimestamp();
			$schema = self::schema();
			foreach ($autostamp as $state => $fields) {
				$fields = (array)$fields;
				foreach ($fields as $field) {
					if (!(isset($schema[$field]) &&
							$schema[$field]['type'] === 'datetime')) {
						continue;
					}
					$entity->$field = $now;
				}
			}
		}

	}

	protected function _generateUuidHexId() {
		$id = new UUID();
		return $id->hexString(); //$id->asBinary();
	}

	protected function generateId() {
		$method = '_generate' . Inflector::camelize(static::$_generatedId) . 'Id';
		if (!method_exists($this, $method)) {
			$name = static::$_generatedId;
			$message = "ID generator `{$name}` does not exist";
			throw new ConfigException($message);
		}
		return $this->$method();
	}

	public function save($entity, $data=null, array$options=array()) {
		if ($data) {
			$entity->set($data);
		}
		$entity->autostamp();
		if (!$entity->exists() && static::$_generatedId) {
			$id = $this->generateId();
			$key = $this->_meta['key'];
			if (is_array($key)) {
				throw new ConfigException('Can not generate an id for mutli-keyed model');
			}
			$entity->$key = $id;
		}
		return parent::save($entity, null, $options);
	}

}