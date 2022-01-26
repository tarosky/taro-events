<?php

namespace Tarosky\Events\Services;

use Tarosky\Events\Pattern\Singleton;

/**
 * Class MetaInfo
 *
 * @package taro-events
 */
class MetaInfo extends Singleton {

	protected function init() {
		add_action( 'wp_head', [ $this, 'display_json_ld' ] );
	}

	/**
	 * Display JSON LD on every page.
	 */
	public function display_json_ld() {
		if ( ! apply_filters( 'taro_events_is_display_json_ld', true ) ) {
			return;
		}
		if ( ! is_singular( taro_events_post_type() ) ) {
			return;
		}
		$json = $this->get_json();
		if ( ! $json ) {
			return;
		}
		$json = json_encode( $json );
		echo <<<HTML
<script type="application/ld+json">
{$json}
</script>
HTML;
	}

	/**
	 * Get business location.
	 *
	 * @param null|int|WP_Post $post Post object.
	 *
	 * @return array
	 *
	 * @link https://developers.google.com/search/docs/advanced/structured-data/event
	 */
	public function get_json( $post = null ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return null;
		}

		// Basic information.
		$json = [
			'@context' => 'http://schema.org',
			'@type'    => 'Event',
			'name'     => get_the_title( $post ),
		];

		// Date
		$offset = $this->get_gmt_offset_string();
		$dates  = [];
		foreach ( [ 'start_date', 'end_date' ] as $key ) {
			$date = get_post_meta( $post->ID, taro_events_meta_prefix() . $key, true );
			if ( ! $date ) {
				continue;
			}
			$date_time = get_post_meta( $post->ID, taro_events_meta_prefix() . $key . '_time', true );
			if ( $date_time ) {
				$date = sprintf( '%sT%s%s', wp_date( 'Y-m-d', strtotime( $date ) ), $date_time, $offset );
			} else {
				$date = wp_date( 'Y-m-d', strtotime( $date ) );
			}
			$dates[] = $date;
		}
		if ( count( $dates ) !== 2 ) {
			return null;
		}
		$json['startDate'] = $dates[0];
		$json['endDate']   = $dates[1];

		// Location
		$locations = [];
		// Offline event.
		$is_offline = get_post_meta( $post->ID, taro_events_meta_prefix() . 'is_offline', true );
		if ( $is_offline ) {
			$location_name    = get_post_meta( $post->ID, taro_events_meta_prefix() . 'location_name', true );
			$location_address = get_post_meta( $post->ID, taro_events_meta_prefix() . 'location_address', true );
			if ( empty( $location_name ) || empty( $location_address ) ) {
				return;
			}
			$locations[] = [
				'@type'   => 'Place',
				'name'    => $location_name,
				'address' => [
					'@type' => 'PostalAddress',
					'name'  => $location_address,
				],
			];
		}
		// Online event.
		$is_online = get_post_meta( $post->ID, taro_events_meta_prefix() . 'is_online', true );
		if ( $is_online ) {
			$location_url = get_post_meta( $post->ID, taro_events_meta_prefix() . 'location_url', true );
			if ( empty( $location_url ) ) {
				return;
			}
			$locations[] = [
				'@type' => 'VirtualLocation',
				'url'   => $location_url,
			];
		}
		$json['location'] = $locations;

		return apply_filters( 'taro_events_get_json_ld', $json, $post );
	}

	/**
	 * Get gmt offset string. (e.g, "+09:00")
	 *
	 * @return string
	 */
	public function get_gmt_offset_string() {
		$offset  = get_option( 'gmt_offset' );
		$hours   = (int) $offset;
		$minutes = abs( ( $offset - (int) $offset ) * 60 );

		return apply_filters( 'taro_events_get_gmt_offset_string', sprintf( '%+03d:%02d', $hours, $minutes ) );
	}
}
