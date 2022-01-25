<?php

namespace Tarosky\Events;

use Tarosky\Events\Metaboxes\EventMetaBox;
use Tarosky\Events\Controller\Filter;
use Tarosky\Events\Pattern\Singleton;
use Tarosky\Events\Services\MetaInfo;

/**
 * Boostrap.
 *
 * @package taro-events
 */
class Bootstrap extends Singleton {

	/**
	 * @inheritDoc
	 */
	protected function init() {
		// Post type.
		add_action( 'init', [ $this, 'register_events_type' ], 20 );

		// Taxonomy
		add_action( 'init', [ $this, 'register_events_taxonomies' ], 20 );

		// Controllers.
		EventMetaBox::get_instance();
		Filter::get_instance();

		// Services.
		MetaInfo::get_instance();
	}

	/**
	 * Register post type for event.
	 */
	public function register_events_type() {
		$post_type = taro_events_post_type();
		if ( ! $post_type ) {
			return;
		}
		$args = taro_events_post_type_args();
		register_post_type( $post_type, $args );
	}

	/**
	 * Register taxonomies for event.
	 */
	public function register_events_taxonomies() {
		$post_type = taro_events_post_type();
		if ( ! $post_type ) {
			return;
		}

		$taxonomies = [];

		// Event category
		if ( taro_events_is_available_event_category() ) {
			$taxonomy = taro_events_taxonomy_event_category();
			if ( $taxonomy ) {
				$taxonomies[ $taxonomy ] = taro_events_taxonomy_event_category_args();
			}
		}

		// Event type
		if ( taro_events_is_available_event_type() ) {
			$taxonomy = taro_events_taxonomy_event_type();
			if ( $taxonomy ) {
				$taxonomies[ $taxonomy ] = taro_events_taxonomy_event_type_args();
			}
		}

		foreach ( $taxonomies as $taxonomy => $args ) {
			register_taxonomy( $taxonomy, [ $post_type ], $args );
		}
	}
}
