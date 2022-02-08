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

	/**
	 * @param string $date The date string
	 * @param string $date_time The time string
	 *
	 * @return string
	 */
	public static function get_formatted_date_string( $date, $date_time = '' ) {
		if ( empty( $date ) ) {
			return '';
		}

		$datetime = new \DateTime( $date );

		if ( $date_time ) {
			$offset = self::get_gmt_offset_string();
			$date   = sprintf( '%sT%s%s', $datetime->format( 'Y-m-d' ), $date_time, $offset );
		} else {
			$date = $datetime->format( 'Y-m-d' );
		}

		return $date;
	}
}
