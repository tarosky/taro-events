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
		// Has capability.
		wp_nonce_field( 'taro_events_change', '_taroeventsnonce' );
		?>
		<h3>Date</h3>
		<table class="form-table">
			<tbody>
			<?php
			foreach (
				[
					'start_date'           => __( 'Start date', 'taro-events' ),
					'end_date'             => __( 'End date', 'taro-events' ),
					'reception_start_date' => __( 'Reception start date', 'taro-events' ),
					'reception_end_date'   => __( 'Reception end date', 'taro-events' ),
				] as $key => $label
			) :
				$meta_key      = taro_events_meta_prefix() . $key;
				$meta_key_time = taro_events_meta_prefix() . $key . '_time';
				$date          = get_post_meta( $post->ID, $meta_key, true );
				if ( $date ) {
					$date = wp_date( 'Y-m-d', strtotime( $date ) );
				}
				?>
				<tr>
					<th><label for="<?php echo esc_attr( $meta_key ); ?>"><?php echo esc_html( $label ); ?></label></th>
					<td>
						<input type="date" name="<?php echo esc_attr( $meta_key ); ?>"
							id="<?php echo esc_attr( $meta_key ); ?>" class="regular-text"
							value="<?php echo esc_attr( $date ); ?>"/>
						<select name="<?php echo esc_attr( $meta_key_time ); ?>"
							id="<?php echo esc_attr( $meta_key_time ); ?>">
							<option value=""><?php echo esc_html( __( 'Not specified', 'taro-events' ) ); ?></option>
							<?php
							foreach ( range( 0, 23 ) as $hour ) :
								foreach ( [ '00', '30' ] as $minute ) :
									?>
									<option
										value="<?php echo esc_attr( sprintf( '%02s:%02s:00', $hour, $minute ) ); ?>" <?php selected( get_post_meta( $post->ID, $meta_key_time, true ), sprintf( '%02s:%02s:00', $hour, $minute ) ); ?>>
										<?php echo esc_html( sprintf( '%02s:%02s', $hour, $minute ) ); ?></option>
									<?php
								endforeach;
							endforeach;
							?>
						</select>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<h3>Location</h3>
		<table class="form-table">
			<tbody>
			<?php
			$meta_key = taro_events_meta_prefix() . 'is_offline';
			?>
			<tr>
				<th><label
						for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Offline event', 'taro-events' ); ?></label>
				</th>
				<td>
					<label>
						<input type="checkbox" name="<?php echo esc_attr( $meta_key ); ?>"
							id="<?php echo esc_attr( $meta_key ); ?>"
							value="1" <?php checked( get_post_meta( $post->ID, $meta_key, true ) ); ?> />
						<?php esc_html_e( 'If this event is offline, please check the box.', 'taro-events' ); ?>
					</label>
				</td>
			</tr>
			<?php
			foreach (
				[
					'place_name'    => __( 'Place name', 'taro-events' ),
					'place_address' => __( 'Place address', 'taro-events' ),
				] as $key => $label
			) :
				$meta_key = taro_events_meta_prefix() . $key;
				?>
				<tr>
					<th><label
							for="<?php echo esc_attr( $meta_key ); ?>"><?php echo esc_html( $label ); ?></label>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
							id="<?php echo esc_attr( $meta_key ); ?>"
							class="regular-text"
							value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
						<p class="description"><?php esc_html_e( 'Required if the event is offline.', 'taro-events' ); ?></p>
					</td>
				</tr>
				<?php
			endforeach;
			?>
			<?php
			$meta_key = taro_events_meta_prefix() . 'is_online';
			?>
			<tr>
				<th><label
						for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Online event', 'taro-events' ); ?></label>
				</th>
				<td>
					<label>
						<input type="checkbox" name="<?php echo esc_attr( $meta_key ); ?>"
							id="<?php echo esc_attr( $meta_key ); ?>"
							value="1" <?php checked( get_post_meta( $post->ID, $meta_key, true ) ); ?> />
						<?php esc_html_e( 'If this event is online, please check the box.', 'taro-events' ); ?>
					</label>
				</td>
			</tr>
			<?php
			foreach (
				[
					'online_url' => __( 'Online URL', 'taro-events' ),
				] as $key => $label
			) :
				$meta_key = taro_events_meta_prefix() . $key;
				?>
				<tr>
					<th><label
							for="<?php echo esc_attr( $meta_key ); ?>"><?php echo esc_html( $label ); ?></label>
					</th>
					<td>
						<input type="url" name="<?php echo esc_attr( $meta_key ); ?>"
							id="<?php echo esc_attr( $meta_key ); ?>"
							class="regular-text"
							value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
						<p class="description"><?php esc_html_e( 'Required if the event is online.', 'taro-events' ); ?></p>
					</td>
				</tr>
				<?php
			endforeach;
			?>
			</tbody>
		</table>
		<?php
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
				'is_offline',
				'place_name',
				'place_address',
				'is_online',
				'online_url',
			] as $key
		) {
			update_post_meta( $post_id, taro_events_meta_prefix() . $key, filter_input( INPUT_POST, taro_events_meta_prefix() . $key ) );
		}
	}
}
