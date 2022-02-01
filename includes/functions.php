<?php
/**
 * Common functions.
 *
 * @package taro-events
 * @since 1.0.0
 */

/**
 * Get event post types.
 *
 * @return string
 */
function taro_events_post_type() {
	return apply_filters( 'taro_events_post_type', 'event' );
}

/**
 * Prefix of event meta data.
 *
 * @return string
 */
function taro_events_meta_prefix() {
	return apply_filters( 'taro_events_meta_prefix', '_events_' );
}

/**
 * Post type argument for event post type.
 */
function taro_events_post_type_args() {
	return apply_filters( 'taro_events_post_type_args', [
		'label'           => __( 'Events', 'taro-events' ),
		'public'          => true,
		'hierarchical'    => false,
		'has_archive'     => true,
		'show_in_rest'    => true,
		'capability_type' => 'page',
		'supports'        => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt' ],
		'menu_icon'       => 'dashicons-calendar',
	] );
}

/**
 * Get event category taxonomy.
 *
 * @return string
 */
function taro_events_taxonomy_event_category() {
	return apply_filters( 'taro_events_taxonomy_event_category', 'event-category' );
}

/**
 * Taxonomy argument for event category.
 */
function taro_events_taxonomy_event_category_args() {
	return apply_filters( 'taro_events_taxonomy_event_category_args', [
		'label'             => __( 'Event categories', 'taro-events' ),
		'show_ui'           => true,
		'show_tagcloud'     => false,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
	] );
}

/**
 * Get event type taxonomy.
 *
 * @return string
 */
function taro_events_taxonomy_event_type() {
	return apply_filters( 'taro_events_taxonomy_event_type', 'event-type' );
}

/**
 * Taxonomy argument for event type.
 */
function taro_events_taxonomy_event_type_args() {
	return apply_filters( 'taro_events_taxonomy_event_type_args', [
		'label'             => __( 'Event types', 'taro-events' ),
		'show_ui'           => true,
		'show_typecloud'    => false,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
	] );
}

/**
 * Get event status name.
 *
 * @return string
 */
function taro_events_event_status_name() {
	return apply_filters( 'taro_events_event_status_name', 'event-status' );
}

/**
 * Can post type be a event?
 *
 * @param string $post_type Post type name.
 *
 * @return bool
 */
function taro_events_can_be( $post_type ) {
	return apply_filters( 'taro_events_can_be', ( taro_events_post_type() === $post_type ), $post_type );
}

/**
 * Get event statuses.
 *
 * @return array
 */
function taro_events_event_statuses() {
	return apply_filters( 'taro_events_event_statuses', [
		'accepting' => __( 'Accepting', 'taro-events' ),
		'opening'   => __( 'Opening', 'taro-events' ),
		'finished'  => __( 'Finished', 'taro-events' ),
	] );
}

/**
 * Display event filtering form.
 *
 * @param bool $echo
 *
 * @return string
 */
function taro_events_get_filter_form( $echo = true ) {
	$filter = \Tarosky\Events\Controller\Filter::get_instance();
	$form   = $filter->get_form();

	if ( $echo ) {
		echo $form;
	} else {
		return $form;
	}
}

/**
 * Whether the event is accepting.
 *
 * @param null|int|WP_Post $post Post object.
 *
 * @return bool|null
 */
function taro_events_is_event_accepting( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}

	$query = new \WP_Query( [
		'p'              => $post->ID,
		'post_type'      => taro_events_post_type(),
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'meta_query'     => taro_events_event_is_accepting_args(),
	] );

	return ! empty( $query->posts );
}

/**
 * Whether the event is opening.
 *
 * @param null|int|WP_Post $post Post object.
 *
 * @return bool|null
 */
function taro_events_is_event_opening( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}

	$query = new \WP_Query( [
		'p'              => $post->ID,
		'post_type'      => taro_events_post_type(),
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'meta_query'     => taro_events_event_is_opening_args(),
	] );

	return ! empty( $query->posts );
}

/**
 * Whether the event finished.
 *
 * @param null|int|WP_Post $post Post object.
 *
 * @return bool|null
 */
function taro_events_is_event_finished( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}

	$query = new \WP_Query( [
		'p'              => $post->ID,
		'post_type'      => taro_events_post_type(),
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'meta_query'     => taro_events_event_is_finished_args(),
	] );

	return ! empty( $query->posts );
}

/**
 * Get query arguments for a accepting event.
 *
 * @return array
 */
function taro_events_event_is_accepting_args() {
	return apply_filters( 'taro_events_event_is_accepting_args', [
		'relation' => 'AND',
		[
			'key'     => taro_events_meta_prefix() . 'reception_start_date',
			'value'   => '',
			'compare' => '!=',
		],
		[
			'key'     => taro_events_meta_prefix() . 'reception_start_date',
			'value'   => wp_date( 'Y-m-d H:i:s' ),
			'compare' => '<=',
		],
		[
			'key'     => taro_events_meta_prefix() . 'reception_end_date',
			'value'   => '',
			'compare' => '!=',
		],
		[
			'key'     => taro_events_meta_prefix() . 'reception_end_date',
			'value'   => wp_date( 'Y-m-d H:i:s' ),
			'compare' => '>=',
		],
	] );
}

/**
 * Get query arguments for a opening event.
 *
 * @return array
 */
function taro_events_event_is_opening_args() {
	return apply_filters( 'taro_events_event_is_opening_args', [
		'relation' => 'AND',
		[
			'key'     => taro_events_meta_prefix() . 'start_date',
			'value'   => '',
			'compare' => '!=',
		],
		[
			'key'     => taro_events_meta_prefix() . 'start_date',
			'value'   => wp_date( 'Y-m-d H:i:s' ),
			'compare' => '<=',
		],
		[
			'key'     => taro_events_meta_prefix() . 'end_date',
			'value'   => '',
			'compare' => '!=',
		],
		[
			'key'     => taro_events_meta_prefix() . 'end_date',
			'value'   => wp_date( 'Y-m-d H:i:s' ),
			'compare' => '>=',
		],
	] );
}

/**
 * Get query arguments for a finished event.
 *
 * @return array
 */
function taro_events_event_is_finished_args() {
	return apply_filters( 'taro_events_event_is_finished_args', [
		'relation' => 'AND',
		[
			'key'     => taro_events_meta_prefix() . 'end_date',
			'value'   => '',
			'compare' => '!=',
		],
		[
			'key'     => taro_events_meta_prefix() . 'end_date',
			'value'   => wp_date( 'Y-m-d H:i:s' ),
			'compare' => '<',
		],
	] );
}

/**
 * Whether the event category is available.
 *
 * @return bool
 */
function taro_events_is_available_event_category() {
	return apply_filters( 'taro_events_is_available_event_category', true );
}

/**
 * Whether the event type is available.
 *
 * @return bool
 */
function taro_events_is_available_event_type() {
	return apply_filters( 'taro_events_is_available_event_type', true );
}

/**
 * Whether the event category is available in the filter form.
 *
 * @return bool
 */
function taro_events_is_available_filter_event_category() {
	if ( ! taro_events_is_available_event_category() ) {
		return false;
	}

	return apply_filters( 'taro_events_is_available_filter_event_category', true );
}

/**
 * Whether the event type is available in the filter form.
 *
 * @return bool
 */
function taro_events_is_available_filter_event_type() {
	if ( ! taro_events_is_available_event_type() ) {
		return false;
	}

	return apply_filters( 'taro_events_is_available_filter_event_type', true );
}

/**
 * Whether the event status is available in the filter form.
 *
 * @return bool
 */
function taro_events_is_available_filter_event_status() {
	return apply_filters( 'taro_events_is_available_filter_event_status', true );
}

/**
 * Get an event meta value.
 *
 * @param string $key Meta key.
 * @param null|int|WP_Post $post Post object.
 * @param bool $singular Is singular?
 *
 * @return mixed
 */
function taro_events_get_meta( $key, $post = null, $singular = true ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}
	if ( taro_events_post_type() !== $post->post_type ) {
		return null;
	}

	return get_post_meta( $post->ID, $key, $singular );
}

/**
 * Get event meta values.
 *
 * @param array $keys Meta keys.
 * @param null|int|WP_Post $post Post object.
 * @param bool $singular Is singular?
 *
 * @return mixed
 */
function taro_events_get_metas( $keys = [], $post = null, $singular = true ) {
	if ( empty( $keys ) ) {
		$event_meta_box = \Tarosky\Events\Metaboxes\EventMetaBox::get_instance();
		$keys           = $event_meta_box->get_meta_keys();
	} elseif ( ! is_array( $keys ) ) {
		$keys = (array) $keys;
	}
	$metas = [];
	foreach ( $keys as $key ) {
		$metas[ $key ] = taro_events_get_meta( $key, $post, $singular );
	}

	return $metas;
}
