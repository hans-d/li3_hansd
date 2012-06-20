<?php

namespace li3_hansd\extensions\test;

use li3_hansd\extensions\test\helper\DataSourceHelper;

class ModelUnit extends Unit {

	protected $dataSourceHelper;

	public function _init() {
		parent::_init();
		$this->dataSourceHelper = new dataSourceHelper();
	}

	public function setUp() {
		$this->dataSourceHelper->setUp();
	}

	public function tearDown() {
		$this->dataSourceHelper->tearDown();
	}


}
