<?php

namespace Tarosky\Events\Pattern;

/**
 * Singleton pattern
 *
 * @package taro-events
 */
abstract class Singleton {

	/**
	 * @var static[] Instances.
	 */
	private static $instances = [];

	/**
	 * Constructor.
	 */
	final protected function __construct() {
		$this->init();
	}

	/**
	 * Executed in constructor.
	 */
	protected function init() {
		// Do something.
	}

	/**
	 * Get instance.
	 *
	 * @return static
	 */
	final public static function get_instance() {
		$class_name = get_called_class();
		if ( ! isset( self::$instances[ $class_name ] ) ) {
			self::$instances[ $class_name ] = new $class_name();
		}
		return self::$instances[ $class_name ];
	}
}
