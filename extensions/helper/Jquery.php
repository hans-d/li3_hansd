<?php

namespace li3_hansd\extensions\helper;

class Jquery extends \lithium\template\Helper {

	protected $_strings = array(
			'ready' => '<script type="text/javascript">$(document).ready(function() {{:ready}});</script>',
	);

	protected $_ready = array();

	public function ready($inlineJs=NULL) {
		if($inlineJs != null) {
			$this->_ready[] = $inlineJs;
		}
		else {
			$ready = implode(PHP_EOL, $this->_ready);
			return $this->_render(__METHOD__, 'ready', compact('ready'));
		}
	}

}