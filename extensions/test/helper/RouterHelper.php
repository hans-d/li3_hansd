<?php

namespace li3_hansd\extensions\test\helper;

use lithium\action\Request;
use lithium\net\http\Router;
use lithium\tests\mocks\template\helper\MockFormRenderer;


class RouterHelper extends \lithium\core\Object {

	/**
	 * Stores the original config.
	 */
	protected $_config;

	public $context;

	public function setUp() {
		$this->config = Router::get();

		Router::reset();
		Router::connect('/{:controller}/{:action}/{:id}.{:type}', array('id' => null));
		Router::connect('/{:controller}/{:action}/{:args}');

		$request = new Request();
		$request->params = array('controller' => 'example', 'action' => 'index');
		$request->persist = array('controller');

		$this->context = new MockFormRenderer(compact('request'));
	}

	public function tearDown() {
		Router::reset();
		foreach ($this->_config as $route) {
			Router::connect($route);
		}
	}

}