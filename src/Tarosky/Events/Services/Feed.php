<?php

namespace Tarosky\Events\Services;

use Tarosky\Events\Pattern\Singleton;

/**
 * Class Feed
 *
 * @package taro-events
 */
class Feed extends Singleton {

	protected function init() {
		add_action( 'rss2_ns', [ $this, 'add_rss2_ns' ] );
		add_action( 'rss2_item', [ $this, 'add_rss2_item' ] );
	}

	/**
	 * Where here is the event feed.
	 *
	 * @return bool
	 */
	public function is_event_feed() {
		return ( is_feed( 'rss2' ) && taro_events_post_type() === get_post_type() );
	}

	/**
	 * Add namespaces to the RSS root.
	 */
	public function add_rss2_ns() {
		if ( ! $this->is_event_feed() ) {
			return;
		}

		echo 'xmlns:ev="http://purl.org/rss/1.0/modules/event/"';
	}

	/**
	 * Add items to the end of each RSS feed item.
	 */
	public function add_rss2_item() {
		if ( ! $this->is_event_feed() ) {
			return;
		}

		// Add the attachment image.
		if ( has_post_thumbnail( get_the_ID() ) ) {
			$attachment_id = get_post_thumbnail_id( get_the_ID() );
			$image         = wp_get_attachment_image_src( $attachment_id, apply_filters( 'taro_events_rss2_item_enclosure_image_size', 'large' ) );
			if ( $image ) {
				$length   = $this->get_remote_image_size( $image[0] );
				$mimetype = get_post_mime_type( $attachment_id );
				echo '<enclosure url="' . esc_attr( $image[0] ) . '" length="' . esc_attr( $length ) . '" type="' . esc_attr( $mimetype ) . '"/>';
			}
		}

		// Startdate, Enddate
		$offset = \Tarosky\Events\Utility\Date::get_gmt_offset_string();
		foreach ( [ 'start_date', 'end_date' ] as $key ) {
			$date = get_post_meta( get_the_ID(), taro_events_meta_prefix() . $key, true );
			if ( ! $date ) {
				continue;
			}
			$date_time = get_post_meta( get_the_ID(), taro_events_meta_prefix() . $key . '_time', true );
			if ( $date_time ) {
				$date = sprintf( '%sT%s%s', wp_date( 'Y-m-d', strtotime( $date ) ), $date_time, $offset );
			} else {
				$date = wp_date( 'Y-m-d', strtotime( $date ) );
			}
			echo '<ev:' . str_replace( '_', '', $key ) . '>' . esc_html( $date ) . '</ev:' . $key . ">\n";
		}

		// Location
		foreach (
			[
				'is_offline' => 'location_name',
				'is_online'  => 'location_url',
			] as $meta_key_format => $meta_key_location
		) {
			if ( get_post_meta( get_the_ID(), taro_events_meta_prefix() . $meta_key_format, true ) ) {
				$location = get_post_meta( get_the_ID(), taro_events_meta_prefix() . $meta_key_location, true );
				if ( $location ) {
					echo "<ev:location><![CDATA[" . esc_html( $location ) . "]]</ev:location>\n";
					break;
				}
			}
		}

		// Organizer
		$organizer = get_post_meta( get_the_ID(), taro_events_meta_prefix() . 'organizer_name', true );
		if ( $organizer ) {
			echo "<ev:organizer><![CDATA[" . esc_html( $organizer ) . "]]</ev:organizer>\n";
		}

		// Type
		if ( taro_events_is_available_event_type() ) {
			$terms = get_the_terms( get_the_ID(), taro_events_taxonomy_event_type() );
			if ( $terms ) {
				$term_names = [];
				foreach ( $terms as $term ) {
					$term_names[] = $term->name;
				}
				if ( ! empty( $term_names ) ) {
					echo "<ev:type><![CDATA[" . esc_html( implode( ',', $term_names ) ) . "]]</ev:type>\n";
				}
			}
		}
	}

	/**
	 * Get remote image file size.
	 *
	 * @param string $url The image url.
	 *
	 * @return int
	 */
	public function get_remote_image_size( $url ) {
		$headers = @get_headers( $url, true );
		if ( ! $headers ) {
			return null;
		}

		return (int) $headers['Content-Length'];
	}

}
