<?php

namespace Tarosky\Events\Metaboxes;

use Tarosky\Events\Pattern\Singleton;

/**
 * Event meta box.
 *
 * @package taro-events
 */
class EventMetaBox extends Singleton {

	/**
	 * @inheritDoc
	 */
	protected function init() {
		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
	}

	/**
	 * Register meta box.
	 *
	 * @param string $post_type Post type.
	 */
	public function register_meta_boxes( $post_type ) {
		if ( ! taro_events_can_be( $post_type ) ) {
			// This post type is not for events.
			return;
		}
		add_meta_box( 'taro-events-setting', __( 'Events Setting', 'taro-events' ), [
			$this,
			'meta_box_callback',
		], $post_type );
	}

	/**
	 * Meta box callback.
	 *
	 * @param \WP_Post $post post object.
	 */
	public function meta_box_callback( \WP_Post $post ) {
		$file = taro_events_template( 'metabox-event.php' );
		if ( $file ) {
			include_once $file;
		}
	}

	/**
	 * Save post meta.
	 *
	 * @param int $post_id Post ID.
	 * @param \WP_Post $post Post object.
	 */
	public function save_post( $post_id, $post ) {
		if ( ! taro_events_can_be( $post->post_type ) ) {
			// This post type is not for events.
			return;
		}
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_taroeventsnonce' ), 'taro_events_change' ) ) {
			return;
		}

		foreach (
			[
				'start_date',
				'start_date_time',
				'end_date',
				'end_date_time',
				'reception_start_date',
				'reception_start_date_time',
				'reception_end_date',
				'reception_end_date_time',
			] as $key
		) {
			$value = filter_input( INPUT_POST, taro_events_meta_prefix() . $key );
			if ( $value ) {
				if ( in_array( $key, [ 'start_date', 'reception_start_date' ], true ) ) {
					$related_time = filter_input( INPUT_POST, taro_events_meta_prefix() . $key . '_time' ) ?: '00:00:00';
					$value        = $value . ' ' . $related_time;
				} elseif ( in_array( $key, [ 'end_date', 'reception_end_date' ], true ) ) {
					$related_time = filter_input( INPUT_POST, taro_events_meta_prefix() . $key . '_time' ) ?: '23:59:59';
					$value        = $value . ' ' . $related_time;
				}
			}
			update_post_meta( $post_id, taro_events_meta_prefix() . $key, $value );
		}

		foreach (
			[
				'name',
				'description',
				'event_status',
				'is_offline',
				'location_name',
				'location_address',
				'is_online',
				'location_url',
				'offers_availability',
				'offers_price',
				'offers_currency',
				'offers_valid_from',
				'offers_valid_from_time',
				'offers_url',
				'organizer_name',
				'organizer_url',
			] as $key
		) {
			update_post_meta( $post_id, taro_events_meta_prefix() . $key, filter_input( INPUT_POST, taro_events_meta_prefix() . $key ) );
		}
	}
}
