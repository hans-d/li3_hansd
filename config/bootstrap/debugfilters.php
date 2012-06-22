<?php

/**
 * Some debugging filter aids.
 *
 * TODO: make more easier to use
 *
 */

use app\extensions\analysis\Debugger;
use app\extensions\analysis\SubjectLogger;
use lithium\action\Dispatcher;
use lithium\core\Environment;
use lithium\data\Connections;



if (false) {
	// SQL logging
	// Enabling when having no MySql available, eg for unit tests, caused issues...
	foreach (array('db1', 'db2') as $db) {
		Connections::get($db)->applyFilter('_execute', function($self, $params, $chain) {
			SubjectLogger::sql($params['sql']);
			$result = $chain->next($self, $params, $chain);
			return $result;
		});
	}
}

if (false) {
	// Dispatcher logging
	Dispatcher::applyFilter('run', function($self, $params, $chain) {
		Debugger::log($params['request']->url, 'dispatcher run (pre)');
		$next = $chain->next($self, $params, $chain);
		Debugger::dump($params['request']->params, 'dispatcher run (post)');
		return $next;
	});
}

if (false) {
	Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
		Debugger::dump($params['params'], 'dispatcher callable - pre');
		$next = $chain->next($self, $params, $chain);
		Debugger::dump($params['params'], 'dispatcher callable - post');
		return $next;
	});
}

// \app\extensions\analysis\Debugger::dump(Router::get(), 'Router');
