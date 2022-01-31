<?php

namespace Tarosky\Events\Controller;

use Tarosky\Events\Pattern\Singleton;

/**
 * Add filtering events.
 *
 * @package taro-events
 */
class Filter extends Singleton {

	/**
	 * @inheritDoc
	 */
	protected function init() {
		add_filter( 'query_vars', [ $this, 'query_vars' ] );
		add_filter( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
		add_shortcode( 'taro-event-filter-form', [ $this, 'display_form' ] );
	}

	/**
	 * Add query vars.
	 *
	 * @return string
	 */
	public function query_vars( $vars ) {
		$vars[] = taro_events_event_status_name();

		return $vars;
	}

	/**
	 * Customize query.
	 *
	 * @param \WP_Query $wp_query Query object.
	 */
	public function pre_get_posts( $wp_query ) {
		if ( is_admin() || ! $wp_query->is_main_query() ) {
			return;
		}

		// Add filter conditions for an event archive query.
		if ( $wp_query->is_post_type_archive( taro_events_post_type() ) ) {
			$status = get_query_var( taro_events_event_status_name() );
			if ( 'accepting' === $status ) {
				$wp_query->set( 'meta_query', taro_events_event_is_accepting_args() );

			} elseif ( 'opening' === $status ) {
				$wp_query->set( 'meta_query', taro_events_event_is_opening_args() );

			} elseif ( 'finished' === $status ) {
				$wp_query->set( 'meta_query', taro_events_event_is_finished_args() );
			}
		}
	}

	/**
	 * Shortcode callback function to display event filter form.
	 *
	 * @return string
	 */
	public function display_form() {
		ob_start();
		echo $this->get_form();

		return ob_get_clean();
	}

	/**
	 * Get event filter form
	 *
	 * @return string
	 */
	public function get_form() {
		// If there is a template in a theme directory, it can be used.
		$filter_form_template = locate_template( 'taro_event_filter_form.php' );
		if ( '' !== $filter_form_template ) {
			ob_start();
			require( $filter_form_template );
			$form = ob_get_clean();
		} else {
			$form_open = '<form role="filter method="get" class="filter-form" action="' . esc_url( get_post_type_archive_link( taro_events_post_type() ) ) . '">';
			// Event category.
			$form_event_category = '';
			if ( taro_events_is_available_filter_event_category() ) {
				$form_event_category .= '<div class="taro-events-filter-event-category-wrap">';
				$form_event_category .= '<div class="taro-events-filter-event-category-label">' . __( 'Event categories', 'taro-events' ) . '</div>';
				$form_event_category .= $this->get_taxonomy_form_html( taro_events_taxonomy_event_category() );
				$form_event_category .= '</div>';
			}
			// Event type.
			$form_event_type = '';
			if ( taro_events_is_available_filter_event_type() ) {
				$form_event_type .= '<div class="taro-events-filter-event-type-wrap">';
				$form_event_type .= '<div class="taro-events-filter-event-type-label">' . __( 'Event types', 'taro-events' ) . '</div>';
				$form_event_type .= $this->get_taxonomy_form_html( taro_events_taxonomy_event_type() );
				$form_event_type .= '</div>';
			}
			// Event status.
			$form_event_status = '';
			if ( taro_events_is_available_filter_event_status() ) {
				$form_event_status .= '<div class="taro-events-filter-event-status-wrap">';
				$form_event_status .= '<div class="taro-events-filter-event-status-label">' . __( 'Event status', 'taro-events' ) . '</div>';
				$form_event_status .= $this->get_event_status_form_dropdown();
				$form_event_status .= '</div>';
			}
			$form_submit = '<input type="submit" class="filter-submit" value="' . __( 'Filter', 'taro-events' ) . '" />';
			$form_close  = '</form>';

			$form = $form_open . $form_event_category . $form_event_type . $form_event_status . $form_submit . $form_close;

			/**
			 * Filters the HTML output of the filter form.
			 *
			 * @param string $form The filter form HTML output.
			 * @param string $form_open The part of the form open tag.
			 * @param string $form_event_category The part of the event category field.
			 * @param string $form_event_type The part of the event type field.
			 * @param string $form_event_status The part of the event status field.
			 * @param string $form_submit The part of the submit field.
			 * @param string $form_close The part of the form close tag.
			 */
			$form = apply_filters( 'taro_events_get_filter_form_html', $form, $form_open, $form_event_category, $form_event_type, $form_event_status, $form_submit, $form_close );
		}

		return $form;
	}

	/**
	 * Get a part of filter form for taxonomy.
	 *
	 * @param $taxonomy
	 *
	 * @return mixed
	 */
	public function get_taxonomy_form_html( $taxonomy ) {
		$args = apply_filters( 'taro_events_get_taxonomy_form_html_args', [
			'show_option_none'  => __( 'Do not specify', 'taro-events' ),
			'option_none_value' => '',
			'hide_empty'        => false,
			'id'                => $taxonomy,
			'name'              => $taxonomy,
			'class'             => 'taro-events-filter-' . $taxonomy,
			'selected'          => get_query_var( $taxonomy ),
			'echo'              => false,
			'taxonomy'          => $taxonomy,
			'value_field'       => 'slug',
		] );

		return wp_dropdown_categories( $args );
	}

	/**
	 * Get a part of filter form for event status. (dropdown)
	 *
	 * @return mixed
	 */
	public function get_event_status_form_dropdown() {
		$var  = get_query_var( taro_events_event_status_name() );
		$name = taro_events_event_status_name();
		$html = '<select name="' . $name . '" id="' . $name . '" class="taro-events-filter-' . $name . '">';
		$html .= '<option value="" selected="selected">' . __( 'Do not specify', 'taro-events' ) . '</option>';
		foreach ( taro_events_event_statuses() as $key => $label ) {
			$html .= '<option class="level-0" value="' . esc_attr( $key ) . '"' . selected( ( $key === $var ), true, false ) . '>' . esc_html( $label ) . '</option>';
		}
		$html .= '</select>';

		return $html;
	}

	/**
	 * Get a part of filter form for event status. (checkbox)
	 *
	 * @return mixed
	 */
	public function get_event_status_form_checkbox() {
		$html = '';
		$vars = (array) get_query_var( taro_events_event_status_name() );
		foreach ( taro_events_event_statuses() as $key => $label ) {
			$html .= '<label>';
			$html .= '<input type="checkbox" name="' . taro_events_event_status_name() . '[]" value="' . esc_attr( $key ) . '" ' . checked( in_array( $key, $vars, true ), true, false ) . '>';
			$html .= '<span>' . esc_html( $label ) . '</span>';
			$html .= '</label>';
		}

		return $html;
	}
}
