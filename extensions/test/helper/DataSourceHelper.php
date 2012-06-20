<?php

namespace li3_hansd\extensions\test\helper;

use li3_mockery\test\Mockery;
use lithium\data\Connections;
use app\extensions\test\Unit;

class DataSourceHelper extends \lithium\core\Object {

	/**
	 * For storing original configs if they need modifying during testing.
	 *
	 * @var array
	 */
	protected $_configs;

	public static $traceDataSource = false;
	public static $traceDataSourceLevel;


	/**
	 * Holds the mocked datasource
	 *
	 * @var mockobject
	 */
	public $datasource;

	public function _init() {
		parent::_init();
		static::$traceDataSourceLevel = static::dataSourceTraceFirstValue();
	}

	/**
	 * Helper function for the trace level. Prints out the first value
	 * of each nested array.
	 *
	 * @see setUpDataSource()
	 */
	public static function dataSourceTraceFirstValue() {
		return function($result) {
			return print_r(array_map(function($val) {
				return $val[0];
			},
			$result->to('array')
			),
			true);
		};
	}

	/**
	 * Set up a mocked data source. Include in your `setUp` if you want to it.
	 *
	 * Stores the original `Connections` config, and replaces all of the found
	 * configs with a mocked data source.
	 * The mocked data source includes callback functionality, accessible via
	 * `$dataSource`, and uses Mockery.
	 * {{{
	 * 		$this->dataSource
	 * 			->shouldReceive('execute')
	 * 			->with($m::on(static::sqlPatterns(array(
	 * 					'/SELECT \* FROM customer/'
	 * 				))))
	 * 		->once()
	 * 		->andReturn($whatever);
	 * }}}
	 *
	 * Do not forget to also call `tearDownDataSource()` in `tearDown()`.
	 *
	 * @see tearDownDataSource()
	 * @param bool $trace Trace during the callback.
	 * @param callable|string $level Controls the level of detail outputted
	 *                        for the results.
	 */
	public function setUp(array $options=array()) {
		$options += array('trace' => false, 'callback' => false);

		$this->_configs = Connections::config();

		if($options['callback']) {
			$this->setUpCallback();
		} else {
			$config = Connections::config();
			foreach (array_keys($config) as $name) {
				$config[$name] = array('type' => 'Mock');
			}
			Connections::config($config);
		}
	}

	protected function setUpCallback() {
		$this->dataSource = Mockery::aliasMock('datasource', array('container' => 'datasource'));
		$callbackClass = $this->dataSource;


		$self = get_called_class();

		$callback = function ($type, $args) use ($callbackClass, $trace, $self) {
			if ($trace || $self::$traceDataSource) {
				$dump = print_r($args, true);
				Unit::dump ("dataSource: type `$type`, args:\n$dump");
			}
			$result = $callbackClass::$type($args);
			if ($trace || $self::$traceDataSource) {
				$level = $self::$traceDataSourceLevel;
				$level = $level ?: function($result) {
					return print_r($result, true);
				};
				$dump = is_callable($level) ? $level($result) : $level;
				Unit::dump("result: `$dump`");
			}
			return $result;
		};

		$config = Connections::config();
		foreach (array_keys($config) as $name) {
			$config[$name] = array(
					'type' => 'database',
					'adapter' => 'MockDatabase',
					'callback' => $callback
			);
		}
		Connections::config($config);
	}

	/**
	 * Tear down the data source. Include in your `tearDown` if you use it.
	 *
	 * Restores the original `Connections` configuration
	 *
	 * @see setUpDataSource()
	 */
	public function tearDown() {
		Connections::config($this->_configs);
		Mockery::close('datasource');
		unset($this->dataSource);
	}

	/**
	 * Help function to dump the sql collected.
	 */
	public static function dumpSql() {
		foreach (array_keys(Connections::config()) as $name) {
			$conn = Connections::get($name);
			$sql = $conn->_sql;
			static::dump(" \nConnection: `{$name}`:");
			static::dumpVar($sql);
		}
	}

	/**
	 * Help function to match sql.
	 *
	 * @see app\extensions\data\source\database\MockDatabase
	 * @param array $patterns Holding regexes that all must match
	 */
	public static function sqlPatterns($patterns=array()) {
		return function($val) use ($patterns) {
			if (!is_array($val) || !isset($val['sql'])) {
				return false;
			}
			foreach ($patterns as $pattern) {
				if (!preg_match($pattern, $val['sql']) > 0) {
					return false;
				}
			}
			return true;
		};
	}

	public static function toSqlResult($phpArray) {
		$data = array();
		$data[] = array_keys($phpArray[0]);
		foreach ($phpArray as $row) {
			$data[] = array_values($row);
		}
		$result = new Collection(compact('data'));
		return $result;
	}

}