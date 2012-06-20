<?php

namespace li3_hansd\extensions\data;

/**
 * Base class for a Domain Service.
 *
 * @author hdonner
 *
 */
class DomainService extends \lithium\core\Object {

	/**
	 * Holds dependencies.
	 *
	 * @var array
	 */
	protected $_classes = array();

	/**
	 * Auto configure paramaters
	 *
	 * @var array
	 */
	protected $_autoConfig = array('classes' => 'merge');
}
