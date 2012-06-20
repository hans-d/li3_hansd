<?php

namespace li3_hansd\extensions\adapter\data\source\database;

class MockDatabase extends \lithium\data\source\Database {

	public $_lastid;

	public $_sql = array();

	protected $_callback;

	protected $_autoConfig = array('lastid', 'callback');

	public function reset() {
		$this ->_lastid = $this->_config['lastid'];
		$this ->_sql = array();
	}

	public function __construct(array $config = array()) {
		$defaults = array(
				'callback' => null,
				'lastid' => 0);
		parent::__construct($config + $defaults);
	}

	public function connect() {
		return true;
	}

	public function disconnect() {
		return true;
	}

	public function encoding($encoding = null) {
		return false;
	}

	public function error() {
		return null;
	}

	protected function _execute($sql) {
		$this->_sql[] = $sql;
		if ($this->_callback) {
			$function = $this->_callback;
			return $function('execute', compact('sql'));
		}
		return null;
	}

	protected function _insertId($query) {
		return $this->_lastid ++;
	}


	public function sources($model = null) {
		$this->_sql[] = '_sources `' . $model . '`';
		if ($this->_callback) {
			$function = $this->_callback;
			return $function('sources', compact('model'));
		}
		return null;
	}

	public function value($value, array $schema = array()) {
		if (is_array($value)) {
			return parent::value($value, $schema);
		}
		return "{" . $value . "}";
	}

	/**
	 * Taken from the MySQL adapter.
	 */
	public function describe($entity, array $meta = array()) {
		$name = $this->invokeMethod('_entityName', array($entity, array('quoted' => true)));
		$columns = $this->read("DESCRIBE {$name}", array('return' => 'array', 'schema' => array(
				'field', 'type', 'null', 'key', 'default', 'extra'
		)));

		$fields = array();

		foreach ($columns as $column) {
			$match = $this->invokeMethod('_column', array($column['type']));

			$fields[$column['field']] = $match + array(
					'null'     => ($column['null'] == 'YES' ? true : false),
					'default'  => $column['default']
			);
		}
		return $fields;
	}


	/**
	 * Converts database-layer column types to basic types.
	 *
	 * Taken from the MySQL adapter.
	 *
	 * @param string $real Real database-layer column type (i.e. `"varchar(255)"`)
	 * @return array Column type (i.e. "string") plus 'length' when appropriate.
	 */
	protected function _column($real) {
		if (is_array($real)) {
			return $real['type'] . (isset($real['length']) ? "({$real['length']})" : '');
		}

		if (!preg_match('/(?P<type>\w+)(?:\((?P<length>[\d,]+)\))?/', $real, $column)) {
			return $real;
		}
		$column = array_intersect_key($column, array('type' => null, 'length' => null));

		if (isset($column['length']) && $column['length']) {
			$length = explode(',', $column['length']) + array(null, null);
			$column['length'] = $length[0] ? intval($length[0]) : null;
			$length[1] ? $column['precision'] = intval($length[1]) : null;
		}

		switch (true) {
			case in_array($column['type'], array('date', 'time', 'datetime', 'timestamp')):
				return $column;
			case ($column['type'] == 'tinyint' && $column['length'] == '1'):
			case ($column['type'] == 'boolean'):
				return array('type' => 'boolean');
				break;
			case (strpos($column['type'], 'int') !== false):
				$column['type'] = 'integer';
				break;
			case (strpos($column['type'], 'char') !== false || $column['type'] == 'tinytext'):
				$column['type'] = 'string';
				break;
			case (strpos($column['type'], 'text') !== false):
				$column['type'] = 'text';
				break;
			case (strpos($column['type'], 'blob') !== false || $column['type'] == 'binary'):
				$column['type'] = 'binary';
				break;
			case preg_match('/float|double|decimal/', $column['type']):
				$column['type'] = 'float';
				break;
			default:
				$column['type'] = 'text';
				break;
		}
		return $column;
	}
}