<?php
/**
 * Li3_partials - Partial templates in Lithium.
 *
 * @package    li3_partials
 * @copyright  Copyright 2011, Ali B. (http://awhitebox.com)
 * @license    http://opensource.org/licenses/bsd-license.php The BSD License
 *
 * Modified by Hans D
 */

namespace li3_hansd\extensions\helper;

use lithium\template\TemplateException;

class Partial extends \lithium\template\Helper {

	public function __call($method, $args) {
		// original signature to more meaningfull variables
		$template = $method;
		$vars = isset($args[0]) ? $args[0] : array();
		$options = isset($args[1]) ? $args[1] : array();

		$context = $this->_context;

		$render = function ($type, $template, $vars, $options) use ($context) {
			$options += $context->request()->params;

			return $context->view()->render(
					array($type => "{$template}_partial"),
					$vars,
					$options);
		};

		try {
			// TODO: also work within layout
			// HD: adapted to also pass the current view data
			return $render('template', $template, $vars + $context->data(), $options);
		} catch(TemplateException $e) {
			return $render('element', $template, $vars + $context->data(), $options);
		}
	}
}

?>