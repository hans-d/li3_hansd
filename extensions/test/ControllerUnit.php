<?php

namespace li3_hansd\extensions\test;

use lithium\action\Request;

class ControllerUnit extends Unit {

	/**
	 * TODO: auto fill in _init
	 */
	protected static $controller;

	public function setUp() {
		$this->setUpDataSource(static::$traceDataSource);
		$this->setUpMockData();
	}

	public function tearDown() {
		$this->tearDownDataSource();
		$this->tearDownMockery();
	}

	protected function setUpMockData() {}

	protected function newController($request) {
		return new static::$controller(compact('request') +
				array('classes' => array(
						'media' => 'lithium\tests\mocks\action\MockMediaClass',
				)
				)
		);
	}

	protected function goFetch($action, $request=null, $params=array()) {
		if (!$request) {
			$request = new Request();
		}
		$controller = $this->newController($request);
		$result = null;
		switch (count($params)) {
			case 0:
				$result = $controller->$action();
				break;
			case 1:
				$result = $controller->$action($params[0]);
				break;
			default:
				$result = call_user_func_array(
						array($controller, $action), $params);
		}
		$controller->render();
		$result += $controller->response->data;
		return array($result, $controller);
	}



}
