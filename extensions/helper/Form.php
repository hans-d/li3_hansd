<?php

namespace li3_hansd\extensions\helper;

use lithium\util\Inflector;
use lithium\util\Set;

/**
 * Enhanced Form helper: set default options for form elements and allow for
 * additional templates in the current defined set. Also adds a fieldLabeled.
 *
 * TODO: make seperate plugin
 */
class Form extends \lithium\template\helper\Form {

	/**
	 * Holds the default options.
	 *
	 * @see config()
	 */
	protected $_defaultOptions = array();

	/**
	 * Holds the extra templates.
	 *
	 * @see config()
	 */
	protected $_extraTemplates = array();

	/**
	 *
	 * @param array $config
	 */
	public function config(array $config = array()) {
		if ($config) {
			if (isset($config['options'])) {
				$this->_defaultOptions = $config['options'];
				unset($config['options']);
			}
			if (isset($config['extend'])) {
				$this->_extraTemplates = $config['extend'];
				unset($config['extend']);
			}
			if (isset($config['merge']) && $config['merge'])
				$config = Set::merge($config, $this->_config);
				unset($config['adjustConfig']);
		}
		return parent::config($config);
	}


	/**
	 * Shortcut for `$this->form->field('name', array('label' => 'New Label)`
	 *
	 * @see field()
	 * @param string $name Field name, just like in `field`
	 * @param string $label The label for the field
	 * @param array $options Further options, just like in `field`
	 */
	public function fieldLabeled ($name, $label, array $options = array()) {
		$options['label'] = $label;
		return $this->field($name, $options);
	}

	/**
     * Submit becomes a button (type => submit)
	 */
	public function submit($title=null, array $options = array()) {
		$options['type'] = 'submit';
		return $this->button($title, $options);
	}

	/**
	 * Shortcut for `$this->form->submit('Title', array('name' => 'new_name)`
	 *
	 * @param unknown_type $name
	 * @param unknown_type $title
	 * @param array $options
	 */
	public function submitNamed($name, $title, array $options = array()) {
		$options['name'] = $name;
		return $this->submit($title, $options);
	}

	/**
	 * Renders the extra templates.
	 *
	 * @see \lithium\template\Helper\Form->field()
	 */
	public function field($name, array $options = array()) {
		if (is_array($name)) {
			return $this->_fields($name, $options);
		}

		$origOptions = $options;
		$extra = array();
		foreach ($this->_extraTemplates as $key) {
			$template = $this->_templateMap[$key];
			$options[$key] = isset($options[$key]) ? $options[$key] : '';
			if (strpos($key, '?') == strlen($key) - 1) {
				if (!is_array($template) || !isset($template['hasValue'])) {
					continue;
				}
				$template += array('true' => '', 'false' => '');
				$condition = $template['hasValue'];
				if ($condition == 'error') {
					$condition = $this->hasErrors();
				} else {
					$condition = isset($origOptions[$condition]) && $origOptions[$condition];
				}
				$condition = ($condition === true ? 'true' : 'false');
				$template = $template[$condition];
			}
			$extra[$key] = $this->_render(__METHOD__, $template, $options + $extra);
			unset($options[$key]);
		}

		$partial = parent::field($name, $options);
		return $this->_render(__METHOD__, $partial, $extra);
	}

	/**
	 * Prior to callinig the parent: if default options are configured, use them.
	 *
	 * @see \lithium\template\Helper->_render()
	 */
	protected function _render($method, $string, $params, array $options = array()) {
		$defaultOptions = $this->_defaultOptions;

		if ($defaultOptions) {
			$key = $string;
			if (!isset($defaultOptions[$key])) {
				$key = array_search($string, $this->_templateMap);
			}
			if (isset($defaultOptions[$key])) {
				$given = isset($params['options']) ? $params['options'] : array();
				$defaultOptions = $defaultOptions[$key];
				$params['options'] = $given + $defaultOptions;
			}
		}

		return parent::_render($method, $string, $params, $options );
	}

	/**
	 * Checks if the binded model has any errors.
	 *
	 * If not model is bound, return `false`.
	 *
	 * @return boolean
	 */
	public function hasErrors() {
		if (!$this->_binding) {
			return false;
		}
		return $this->_binding->errors();
	}
}