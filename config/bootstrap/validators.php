<?php
use lithium\util\Validator;

// TODO: move to class and make easier ad hoc accessible


Validator::add('match', function($value, $format = null, array $options = array()) {
	$options += array(
			'against' => '',
			'values' => array()
	);
	extract($options);
	if (array_key_exists($against, $values)) {
		return $values[$against] == $value;
	}
	return false;
});


Validator::add('conditional', function($value, $format = null, array $options = array()) {
	extract($options);

	switch (true) {
		case isset($testValue):
			$match = $values[$dependsOn] === $testValue;
			break;
		default:
			return false;
	}

	if (!$match) {
		return true;
	}

	return Validator::$subValidation($value);

});
