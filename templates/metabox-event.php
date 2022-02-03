<?php
/**
 * @var WP_Post $post
 * @package taro-events
 */
wp_nonce_field( 'taro_events_change', '_taroeventsnonce' );
?>
<h3><?php esc_html_e( 'Basic', 'taro-events' ); ?></h3>
<table class="form-table">
	<tbody>
	<?php
	$meta_key = taro_events_meta_prefix() . 'name';
	if ( taro_events_is_available_meta_key( $meta_key ) ):
		?>
		<tr>
			<th><label
					for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Name', 'taro-events' ); ?></label>
			</th>
			<td>
				<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
				       id="<?php echo esc_attr( $meta_key ); ?>"
				       class="regular-text"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'description';
	if ( taro_events_is_available_meta_key( $meta_key ) ):
		?>
		<tr>
			<th><label
					for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Description', 'taro-events' ); ?></label>
			</th>
			<td>
			<textarea rows="5" class="large-text code" name="<?php echo esc_attr( $meta_key ); ?>"
			          id="<?php echo esc_attr( $meta_key ); ?>"><?php echo esc_textarea( get_post_meta( $post->ID, $meta_key, true ) ); ?></textarea>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'event_status';
	if ( taro_events_is_available_meta_key( $meta_key ) ):
		?>
		<tr>
			<th><label
					for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Event status', 'taro-events' ); ?></label>
			</th>
			<td>
				<select name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>">
					<option value=""><?php echo esc_html( __( 'Not specified', 'taro-events' ) ); ?></option>
					<?php
					foreach (
						[
							'EventScheduled'   => __( 'The event is scheduled to happen', 'taro-events' ),
							'EventCancelled'   => __( 'The event has been canceled', 'taro-events' ),
							'EventMovedOnline' => __( 'The event has been moved from in-person attendance to online attendance', 'taro-events' ),
							'EventPostponed'   => __( "The event has been postponed to a later date, but the date isn't known yet", 'taro-events' ),
							'EventRescheduled' => __( 'The event has been rescheduled to a later date', 'taro-events' ),
						] as $key => $label
					) :
						?>
						<option
							value="<?php echo esc_attr( $key ); ?>" <?php selected( get_post_meta( $post->ID, $meta_key, true ), $key ); ?>><?php echo esc_html( $label ); ?></option>
					<?php
					endforeach;
					?>
				</select>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>

<h3><?php esc_html_e( 'Date', 'taro-events' ); ?></h3>
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
		$meta_key = taro_events_meta_prefix() . $key;
		$meta_key_time = taro_events_meta_prefix() . $key . '_time';
		if ( ! taro_events_is_available_meta_key( $meta_key ) ) {
			continue;
		}
		$date = get_post_meta( $post->ID, $meta_key, true );
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
				<input type="time"
				       name="<?php echo esc_attr( $meta_key_time ); ?>" id="<?php echo esc_attr( $meta_key_time ); ?>"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key_time, true ) ); ?>">
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<h3><?php esc_html_e( 'Location', 'taro-events' ); ?></h3>
<table class="form-table">
	<tbody>
	<?php
	$meta_key = taro_events_meta_prefix() . 'is_offline';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
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
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'location_name';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label
					for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Location name', 'taro-events' ); ?></label>
			</th>
			<td>
				<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
				       id="<?php echo esc_attr( $meta_key ); ?>"
				       class="regular-text"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
				<p class="description"><?php esc_html_e( 'The name of the venue.', 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'location_address';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label
					for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Location address', 'taro-events' ); ?></label>
			</th>
			<td>
				<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
				       id="<?php echo esc_attr( $meta_key ); ?>"
				       class="regular-text"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
				<p class="description"><?php esc_html_e( 'The detailed address of the venue.', 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'is_online';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
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
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'location_url';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label
					for="<?php echo esc_attr( $meta_key ); ?>"><?php echo esc_html_e( 'Location URL', 'taro-events' ); ?></label>
			</th>
			<td>
				<input type="url" name="<?php echo esc_attr( $meta_key ); ?>"
				       id="<?php echo esc_attr( $meta_key ); ?>"
				       class="regular-text"
				       placeholder="https://example.com"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
				<p class="description"><?php esc_html_e( 'The URL of the online event, where people can join.', 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>

<h3><?php esc_html_e( 'Offers', 'taro-events' ); ?></h3>
<table class="form-table">
	<tbody>
	<?php
	$meta_key = taro_events_meta_prefix() . 'offers_availability';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label
					for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Availability', 'taro-events' ); ?></label>
			</th>
			<td>
				<select name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>">
					<option value=""><?php echo esc_html( __( 'Not specified', 'taro-events' ) ); ?></option>
					<?php
					foreach (
						[
							'InStock'  => __( 'In stock', 'taro-events' ),
							'SoldOut'  => __( 'Sold out', 'taro-events' ),
							'PreOrder' => __( 'Pre order', 'taro-events' ),
						] as $key => $label
					) :
						?>
						<option
							value="<?php echo esc_attr( $key ); ?>" <?php selected( get_post_meta( $post->ID, $meta_key, true ), $key ); ?>><?php echo esc_html( $label ); ?></option>
					<?php
					endforeach;
					?>
				</select>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'offers_price';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Price', 'taro-events' ); ?></label>
			</th>
			<td>
				<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
				       id="<?php echo esc_attr( $meta_key ); ?>"
				       class="regular-text"
				       placeholder="100"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
				<p class="description"><?php esc_html_e( 'The lowest available price available for your tickets, including service charges and fees.', 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'offers_currency';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label
					for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Currency', 'taro-events' ); ?></label>
			</th>
			<td>
				<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
				       id="<?php echo esc_attr( $meta_key ); ?>"
				       class="regular-text"
				       placeholder="<?php esc_html_e( 'USD', 'taro-events' ); ?>"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
				<p class="description"><?php esc_html_e( 'The 3-letter ', 'taro-events' ); ?><a
						href="https://en.wikipedia.org/wiki/ISO_4217"><?php esc_html_e( 'ISO 4217 currency code', 'taro-events' ); ?></a>
				</p>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	foreach (
		[
			'offers_valid_from' => __( 'Varid from', 'taro-events' ),
		] as $key => $label
	) :
		$meta_key = taro_events_meta_prefix() . $key;
		$meta_key_time = taro_events_meta_prefix() . $key . '_time';
		if ( ! taro_events_is_available_meta_key( $meta_key ) ) {
			break;
		}
		$date = get_post_meta( $post->ID, $meta_key, true );
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
				<input type="time"
				       name="<?php echo esc_attr( $meta_key_time ); ?>" id="<?php echo esc_attr( $meta_key_time ); ?>"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key_time, true ) ); ?>">
				<p class="description"><?php esc_html_e( 'The date and time when tickets go on sale (only required on date-restricted offers).', 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'offers_url';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'URL', 'taro-events' ); ?></label>
			</th>
			<td>
				<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
				       id="<?php echo esc_attr( $meta_key ); ?>"
				       class="regular-text"
				       placeholder="https://example.com"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
				<p class="description"><?php esc_html_e( 'The URL of a page providing the ability to buy tickets.', 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>

<h3><?php esc_html_e( 'Organizer', 'taro-events' ); ?></h3>
<table class="form-table">
	<tbody>
	<?php
	$meta_key = taro_events_meta_prefix() . 'organizer_type';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label
					for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Type', 'taro-events' ); ?></label>
			</th>
			<td>
				<?php
				foreach (
					[
						'Organization' => __( 'Organization', 'taro-events' ),
						'Person'       => __( 'Person', 'taro-events' ),
					] as $key => $label
				) :
					?>
					<label>
						<input type="radio" name="<?php echo esc_attr( $meta_key ); ?>"
						       value="<?php echo esc_attr( $key ); ?>" <?php checked( get_post_meta( $post->ID, $meta_key, true ), $key ); ?> /><?php echo esc_html( $label ); ?>
					</label>
				<?php
				endforeach;
				?>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'organizer_name';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Name', 'taro-events' ); ?></label>
			</th>
			<td>
				<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
				       id="<?php echo esc_attr( $meta_key ); ?>"
				       class="regular-text"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
				<p class="description"><?php esc_html_e( 'The person or organization that is hosting the event.', 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'organizer_url';
	if ( taro_events_is_available_meta_key( $meta_key ) ) :
		?>
		<tr>
			<th><label for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'URL', 'taro-events' ); ?></label>
			</th>
			<td>
				<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
				       id="<?php echo esc_attr( $meta_key ); ?>"
				       class="regular-text"
				       placeholder="https://example.com"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
				<p class="description"><?php esc_html_e( "The event host's domain URL.", 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
