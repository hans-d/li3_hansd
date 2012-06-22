<?php


use li3_hansd\extensions\analysis\SubjectLogger;
use lithium\analysis\Logger;
use lithium\core\Environment;
use lithium\core\Libraries;

/**
 * Logger for 'normal' activity logging. In specific cases a SubjectLogger can be used.
 *  `\li3_hansd\extensions\analysis\Debugger` defines some shortcuts..
 *
 * Logger levels - overview of the levels and what they should be used for:
 * -  emergency  - very severe error events that will lead the application to abort directly
 * -  alert      - very severe error events that will lead the application to abort shortly
 * -  critical   - very severe error events that will lead the application to abort quite soon
 *  -  error     - error events that might still allow the application to continue running
 *  -  warning   - potentially harmful situations
 *  -  notice    - informational messages that highlight the progress of the application at coarse-grained level
 *  -  info      - fine-grained informational events that are most useful to debug the application
 *  -  debug     - finer-grained informational events than `info`
 *
 */

Logger::config(array(
		'default' => array(
				'adapter' => 'File',
				'file' => function ($data, $config) { return date('Y-m-d') . '-4-normal.log';},
				'format'=> "{:timestamp} [{:priority}] {:message}\n",
				'priority' => array('emergency', 'alert', 'critical', 'error', 'warning', 'notice'),
		),
		'urgent' => array(
				// TODO: notify (eg mail) directly / every minute
				'adapter' => 'File',
				'file' => function ($data, $config) { return date('Y-m-d') . '-1-urgent- ' . date('H-i') . '.log'; },
				'format'=> "{:timestamp} [{:priority}] {:message}\n",
				'priority' => array('emergency', 'alert'),
		),
		'high' => array(
				//TODO: notify (eg mail) every hour
				'adapter' => 'File',
				'file' => function ($data, $config) { return date('Y-m-d') . '-2-high-' . date('H') . '.log'; },
				'format'=> "{:timestamp} [{:priority}] {:message}\n",
				'priority' => array('critical'),
		),
		'medium' => array(
				//TODO: notify (eg mail) every day
				'adapter' => 'File',
				'file' => function ($data, $config) { return date('Y-m-d') . '-3-medium.log'; },
				'format'=> "{:timestamp} [{:priority}] {:message}\n",
				'priority' => array('error', 'warning'),
		),
		'full' => array(
				// TODO: make environment/situation specific
				'adapter' => 'File',
				'file' => function ($data, $config) { return date('Y-m-d') . '-5-full.log'; },
				'format'=> "{:timestamp} [{:priority}] {:message}\n",
		),
		'dev' => array(
				// TODO: make environment/situation specific
				'adapter' => 'File'
		),
));

/**
 * Logging specific topics.
 *
 */
SubjectLogger::add(array('sql', 'access'));
SubjectLogger::config(array(
	'default' => array(
			'adapter' => 'File',
			'file' => function ($data, $config) {return date('Y-m-d') . '-6-subject.log';},
			'format'=> "{:timestamp} [{:priority}] {:message}\n",
	),
	'access' => array(
			'adapter' => 'File',
			'file' => function ($data, $config) {
				// Requires the Environment to be configured with host information
				return date('Y-m') . '-' . Environment::get('host') . '.log';
			},
			'format'=> "{:timestamp} {:message}\n",
	),
	'dev' => array(
			// TODO: make environment/situation specific
			'adapter' => 'File'
	),

));
