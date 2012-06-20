<?php

namespace li3_hansd\extensions\data;

class Model extends \lithium\data\Model {

	/**
	 * Default values for fields on `create()`.
	 *
	 * The database may also supply default values in its schema. The values
	 * here have precedence.
	 */
	protected static $_defaults = array();

	protected static $_timestamped = array(
			'create' => 'create_tstamp',
			'all' => 'modified_tstamp',
	);


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

	public function save($entity, $data=null, array$options=array()) {
		if ($data) {
			$entity->set($data);
		}

		$states = array('create', 'update', 'all');
		$autostamp = is_array(static::$_timestamped) ? static::$_timestamped : null;
		if ($autostamp) {
			$autostamp = array_intersect_key($autostamp, array_flip($states));
			if ($params['entity']->exists()) {
				unset($autostamp['create']);
			} else {
				unset($autostamp['update']);
			}
			$autostamp = array_keys($autostamp) != array() ? $autostamp : null;
		}
		if ($autostamp) {
			$now = static::getTimestamp();
			$schema = $self::schema();
			foreach ($autostamp as $state => $fields) {
				if (!isset($schema[$field]) || !$schema[$field]['type'] === 'datetime') {
					continue;
				}
				$entity->$field = $now;
			}
		}
		return parent::save($entity, null, $options);
	}

}