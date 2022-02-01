<?php

namespace Tarosky\Events\Utility;

use Tarosky\Events\Pattern\Singleton;

/**
 * Class Date
 *
 * @package taro-events
 */
class Date extends Singleton {

	/**
	 * Get gmt offset string. (e.g, "+09:00")
	 *
	 * @return string
	 */
	public static function get_gmt_offset_string() {
		$offset  = get_option( 'gmt_offset' );
		$hours   = (int) $offset;
		$minutes = abs( ( $offset - (int) $offset ) * 60 );

		return apply_filters( 'taro_events_get_gmt_offset_string', sprintf( '%+03d:%02d', $hours, $minutes ) );
	}
}
