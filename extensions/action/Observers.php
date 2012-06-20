<?php

namespace li3_hansd\extensions\action;

/**
 * Observer for the Observer pattern. When a subject sends a notification for an event,
 * the registered listeners can act upon it.
 *
 * Filters can do almost anything, but somethimes a observer might fit better.
 *
 */
class Observers extends \lithium\core\StaticObject {

	/**
	 * The registered listeners per event
	 *
	 * @var array
	 */
	protected static $_listeners = array();

	/**
	 * Adds a listener for the given event.
	 *
	 * @param string $event
	 * @param callable $callable the callback to be used
	 */
	public static function add($event, $callable) {
		if (!isset(static::$_listeners[$event])) {
			static::$_listeners[$event] = array();
		}
		return static::$_listeners[$event][] = $callable;
	}

	/**
	 * Gets a list of registered events, or a list of listeners for a
	 * specific event.
	 *
	 * @param string $name `null` to get a list of all events, or the event name
	 * @return mixed
	 */
	public static function get($event = null) {
		if (!$event) {
			return array_keys(static::$_listeners);
		}
		if (!isset(static::$_listeners[$event])) {
			return null;
		}
		return static::$_listeners[$event];
	}

	/**
	 * Called by a subject to notify the listeners of an event.
	 *
	 * For each notification, the callbacks of all the listeners for that
	 * event a called with the specified arguments.
	 *
	 * @param string $event name of the event
	 * @param array $args arguments to pass
	 */
	public static function notify($event, $args=array()) {
		if (isset(static::$_listeners[$event])) {
			foreach (static::$_listeners[$event] as $listener) {
				$listener($args);
			}
		}
	}

	/**
	 * Resets all the listeners to zero.
	 */
	public static function reset() {
		static::$_listeners = array();
	}

}