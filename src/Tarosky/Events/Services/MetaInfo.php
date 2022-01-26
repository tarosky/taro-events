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

		$context = 'https://schema.org';

		// Basic information.
		$json = [
			'@context' => $context,
			'@type'    => 'Event',
		];

		// Name
		$name = get_post_meta( $post->ID, taro_events_meta_prefix() . 'name', true );
		if ( ! $name ) {
			$name = get_the_title( $post );
		}
		$json['name'] = $name;

		// Description
		$description = get_post_meta( $post->ID, taro_events_meta_prefix() . 'description', true );
		if ( $description ) {
			$json['description'] = $description;
		}

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
			// Required fields is empty.
			return null;
		}
		$json['startDate'] = $dates[0];
		$json['endDate']   = $dates[1];

		// Event Status
		$event_status = get_post_meta( $post->ID, taro_events_meta_prefix() . 'event_status', true );
		if ( $event_status ) {
			$json['eventStatus'] = trailingslashit( $context ) . $event_status;
		}

		// eventAttendanceMode
		$is_offline = get_post_meta( $post->ID, taro_events_meta_prefix() . 'is_offline', true );
		$is_online  = get_post_meta( $post->ID, taro_events_meta_prefix() . 'is_online', true );
		if ( $is_offline && $is_online ) {
			$json['eventAttendanceMode'] = trailingslashit( $context ) . 'MixedEventAttendanceMode';
		} elseif ( $is_offline ) {
			$json['eventAttendanceMode'] = trailingslashit( $context ) . 'OfflineEventAttendanceMode';
		} elseif ( $is_online ) {
			$json['eventAttendanceMode'] = trailingslashit( $context ) . 'OnlineEventAttendanceMode';
		}

		// Location
		$locations = [];
		// Offline event.
		if ( $is_offline ) {
			$location_name    = get_post_meta( $post->ID, taro_events_meta_prefix() . 'location_name', true );
			$location_address = get_post_meta( $post->ID, taro_events_meta_prefix() . 'location_address', true );
			if ( empty( $location_name ) || empty( $location_address ) ) {
				// Required fields is empty.
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
		if ( $is_online ) {
			$location_url = get_post_meta( $post->ID, taro_events_meta_prefix() . 'location_url', true );
			if ( empty( $location_url ) ) {
				// Required fields is empty.
				return;
			}
			$locations[] = [
				'@type' => 'VirtualLocation',
				'url'   => $location_url,
			];
		}
		$json['location'] = $locations;

		// Offers
		$offers['@type']     = 'Offer';
		$offers_availability = get_post_meta( $post->ID, taro_events_meta_prefix() . 'offers_availability', true );
		if ( $offers_availability ) {
			$offers['availability'] = trailingslashit( $context ) . $offers_availability;
		}
		$offers_price = get_post_meta( $post->ID, taro_events_meta_prefix() . 'offers_price', true );
		if ( $offers_price ) {
			$offers['price'] = $offers_price;
		}
		$offers_currency = get_post_meta( $post->ID, taro_events_meta_prefix() . 'offers_currency', true );
		if ( $offers_currency ) {
			$offers['priceCurrency'] = $offers_currency;
		}
		$offers_valid_from = get_post_meta( $post->ID, taro_events_meta_prefix() . 'offers_valid_from', true );
		if ( $offers_valid_from ) {
			$offers_valid_from_time = get_post_meta( $post->ID, taro_events_meta_prefix() . 'offers_valid_from_time', true );
			if ( $offers_valid_from_time ) {
				$valid_from = sprintf( '%sT%s%s', wp_date( 'Y-m-d', strtotime( $offers_valid_from ) ), $offers_valid_from_time, $offset );
			} else {
				$valid_from = wp_date( 'Y-m-d', strtotime( $offers_valid_from ) );
			}
			$offers['validFrom'] = $valid_from;
		}
		$offers_url = get_post_meta( $post->ID, taro_events_meta_prefix() . 'offers_url', true );
		if ( $offers_url ) {
			$offers['url'] = $offers_url;
		}
		$json['offers'] = $offers;

		// Organizer
		$organizer['@type'] = 'Organization';
		$organizer_name     = get_post_meta( $post->ID, taro_events_meta_prefix() . 'organizer_name', true );
		if ( $organizer_name ) {
			$organizer['name'] = $organizer_name;
		}
		$organizer_url = get_post_meta( $post->ID, taro_events_meta_prefix() . 'organizer_url', true );
		if ( $organizer_url ) {
			$organizer['url'] = $organizer_url;
		}
		$json['organizer'] = $organizer;

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
