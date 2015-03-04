<?php

/**
 * Event handle and dispatch class
 *
 * It can handle callbacks to events, add new handles, dispatch events and 
 * check events have been run (dispatch)
 *
 * @copyright  2009 SmartJobBoard
 * @version    1.1
 * @author     janson
 */

class SJB_Event
{
	// Event callbacks
	private static $events = array();

	// Events that have been run
	private static $hasRun = array();

	// Data that can be processed during events
	public static $data;
	
	public static $ignoreEvents = array();

	/**
	 * Add a callback to an event queue.
	 *
	 * @param   string   event name
	 * @param   array    http://php.net/callback
	 * @return  boolean
	 */
	public static function handle($name, $callback, $priority = 10)
	{
		if ( ! isset(self::$events[$name][$priority])) {
			self::$events[$name][$priority] = array();
		} elseif (in_array($callback, self::$events[$name][$priority], true)) {
			return false;
		}
		// Add the event
		self::$events[$name][$priority][] = $callback;

		return true;
	}

	/**
	 * Replaces an event with another event.
	 *
	 * @param   string   event name
	 * @param   array    event to replace
	 * @param   array    new callback
	 * @return  boolean
	 */
	public static function replace($name, $existing, $callback, $priority = 10)
	{
		if (empty(self::$events[$name][$priority]) OR ($key = array_search($existing, self::$events[$name][$priority], true)) === false)
			return false;

		if ( ! in_array($callback, self::$events[$name][$priority], true)) {
			// Replace the exisiting event with the new event
			self::$events[$name][$priority][$key] = $callback;
		} else {
			// Remove the existing event from the queue
			unset(self::$events[$name][$priority][$key]);

			// Reset the array so the keys are ordered properly
			self::$events[$name][$priority] = array_values(self::$events[$name]);
		}

		return true;
	}

	/**
	 * Get all callbacks for an event.
	 *
	 * @param   string  event name
	 * @return  array
	 */
	public static function get($name)
	{
		return empty(self::$events[$name]) ? array() : self::$events[$name];
	}

	/**
	 * Clear some or all callbacks from an event.
	 *
	 * @param   string  event name
	 * @param   array   specific callback to remove, FALSE for all callbacks
	 * @return  void
	 */
	public static function clear($name, $callback = false, $priority = false)
	{
		if ($callback === false) {
			if ($priority == false) {
				self::$events[$name] = array();
			} else {
				self::$events[$name][$priority] = array();
			}
		} elseif (isset(self::$events[$name])) {
			// Loop through each of the event callbacks and compare it to the
			// callback requested for removal. The callback is removed if it
			// matches.
			if ($priority != false) {
				foreach (self::$events[$name][$priority] as $i => $eventCallback) {
					if ($callback === $eventCallback) {
						unset(self::$events[$name][$priority][$i]);
					}
				}
			} else {
				foreach (self::$events[$name] as $priority => $value) {
					foreach ($value as $i => $eventCallback) {
						if ($callback === $eventCallback) {
							unset(self::$events[$name][$priority][$i]);
						}
					}
				}
			}
		}
	}

	/**
	 * Execute all of the callbacks attached to an event.
	 *
	 * @param   string   event name
	 * @param   array    data can be processed as SJB_Event::$data by the callbacks
	 * @param	boolean	 'true' if need to return actual value in $data and 'false' if not
	 * @return  void
	 */
	public static function dispatch($name, & $data = NULL, $return = false)
	{
		// check ignore list
		foreach (self::$ignoreEvents as $ignoreName=>$val) {
			if ($ignoreName == $name) {
				return false;
			}
		}
		
		if ( ! empty(self::$events[$name])) {
			// So callbacks can access SJB_Event::$data
			self::$data =& $data;
			
			$callbacks  =  self::get($name);
			// sort callbacks by priority
			ksort($callbacks);
			
			$callbacksOrder = $callbacks;
			
			foreach ($callbacksOrder as $callbacks) {
				foreach ($callbacks as $callback) {
					if ($return === false) {
						call_user_func($callback, $data);
					} else {
						$data = call_user_func($callback, $data);
					}
				}
			}

			// Do this to prevent data from getting 'stuck'
			$clearData = '';
			self::$data =& $clearData;
		}

		// The event has been run!
		self::$hasRun[$name] = $name;
	}

	/**
	 * Check if a given event has been run.
	 *
	 * @param   string   event name
	 * @return  boolean
	 */
	public static function hasRun($name)
	{
		return isset(self::$hasRun[$name]);
	}
	
	
	/**
	 * Add event name to ignore list
	 *
	 * @param string $name
	 */
	public static function addToIgnoreList($name) {
		self::$ignoreEvents[$name] = $name;
	}
	
	/**
	 * Remove event name from ignore list
	 *
	 * @param string $name
	 */
	public static function removeFromIgnoreList($name) {
		unset(self::$ignoreEvents[$name]);
	}

}