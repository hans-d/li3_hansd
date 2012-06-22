<?php

use li3_hansd\extensions\analysis\SubjectLogger;

use lithium\action\Dispatcher;
use lithium\action\Response;
use lithium\security\Auth;

/**
 * @see lithium\security\auth\adapter\Form
 * @see lithium\action\Request::$data
 * @see lithium\security\Auth
 *
 */
Auth::config(array(
		'default' => array(
				'adapter' => 'Form',
				'session' => array('key' => 'auth-user'),
				'fields' => array('username' => 'username', 'password' => 'password_hash')
		)
));


Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
// 	\app\extensions\analysis\Debugger::dump($params);

	$controller = $chain->next($self, $params, $chain);

	$request = isset($params['request']) ? $params['request'] : null;
	$actionName  = $params['params']['action'];
	$controllerName  = $params['params']['controller'];

// 	if (Auth::check('default')) {
		SubjectLogger::access($request->url . ' authorized');
		return $controller;
// 	}
	// TODO: handle public actions central
// 	if (isset($controller->publicActions) && in_array($actionName, $controller->publicActions)) {
// 		SubjectLogger::access($request->url . ' public');
// 		return $controller;
// 	}

	// TODO: only dev
// 	if (isset($request->params['library']) && $request->params['library'] == 'li3_docs') {
// 		SubjectLogger::access($request->url . ' li3_docs');
// 		return $controller;
// 	}


// 	return function() use ($request) {
// 		SubjectLogger::access($request->url . ' access denied / requires login');
// 		return new Response(compact('request') + array('location' => 'Session::add'));
// 	};
});