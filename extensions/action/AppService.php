<?php

namespace li3_hansd\extensions\action;

/**
 * Base class for an Application Service.
 *
 * @author hdonner
 *
 */
class AppService extends \lithium\core\Object {

	/**
	 * Holds dependencies.
	 *
	 * @var array
	 */
	protected $_classes = array();

	/**
	 * Reference to the current request
	 *
	 * @var object
	 */
	protected $_request;

	/**
	 * Auto configure paramaters
	 *
	 * @var array
	 */
	protected $_autoConfig = array('request', 'classes' => 'merge');

}
