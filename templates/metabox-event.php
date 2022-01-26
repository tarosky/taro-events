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
	foreach (
		[
			'name' => __( 'Name', 'taro-events' ),
			'description' => __( 'Description', 'taro-events' ),
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
			</td>
		</tr>
	<?php
	endforeach;
	?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'event_status';
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
						'EventScheduled'  => __( 'The event is scheduled to happen', 'taro-events' ),
						'EventCancelled'  => __( 'The event has been canceled', 'taro-events' ),
						'EventMovedOnline'  => __( 'The event has been moved from in-person attendance to online attendance', 'taro-events' ),
						'EventPostponed'  => __( "The event has been postponed to a later date, but the date isn't known yet", 'taro-events' ),
						'EventRescheduled'  => __( 'The event has been rescheduled to a later date', 'taro-events' ),
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

<h3><?php esc_html_e( 'Location', 'taro-events' ); ?></h3>
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
	$meta_key = taro_events_meta_prefix() . 'location_name';
	?>
	<tr>
		<th><label for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Location name', 'taro-events' ); ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
			       id="<?php echo esc_attr( $meta_key ); ?>"
			       class="regular-text"
			       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
			<p class="description"><?php esc_html_e( "The name of the venue.", 'taro-events' ); ?></p>
		</td>
	</tr>
	<?php
	$meta_key = taro_events_meta_prefix() . 'location_address';
	?>
	<tr>
		<th><label for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Location addrss', 'taro-events' ); ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
			       id="<?php echo esc_attr( $meta_key ); ?>"
			       class="regular-text"
			       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
			<p class="description"><?php esc_html_e( "The venue’s detailed address.", 'taro-events' ); ?></p>
		</td>
	</tr>
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
			'location_url' => __( 'Location URL', 'taro-events' ),
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
				       placeholder="https://example.com"
				       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
				<p class="description"><?php esc_html_e( 'The URL of the online event, where people can join.', 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php
	endforeach;
	?>
	</tbody>
</table>

<h3><?php esc_html_e( 'Offers', 'taro-events' ); ?></h3>
<table class="form-table">
	<tbody>
	<?php
	$meta_key = taro_events_meta_prefix() . 'offers_availability';
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
	<?php
	$meta_key = taro_events_meta_prefix() . 'offers_price';
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
	<?php
	$meta_key = taro_events_meta_prefix() . 'offers_currency';
	?>
	<tr>
		<th><label for="<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Currency', 'taro-events' ); ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo esc_attr( $meta_key ); ?>"
			       id="<?php echo esc_attr( $meta_key ); ?>"
			       class="regular-text"
			       placeholder="<?php esc_html_e( 'USD', 'taro-events' ); ?>"
			       value="<?php echo esc_attr( get_post_meta( $post->ID, $meta_key, true ) ); ?>"/>
			<p class="description"><?php esc_html_e( 'The 3-letter ', 'taro-events' ); ?><a href="https://en.wikipedia.org/wiki/ISO_4217"><?php esc_html_e( 'ISO 4217 currency code', 'taro-events' ); ?></a></p>
		</td>
	</tr>
	<?php
	foreach (
		[
			'offers_valid_from' => __( 'Varid from', 'taro-events' ),
		] as $key => $label
	) :
		$meta_key = taro_events_meta_prefix() . $key;
		$meta_key_time = taro_events_meta_prefix() . $key . '_time';
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
				<p class="description"><?php esc_html_e( 'The date and time when tickets go on sale (only required on date-restricted offers).', 'taro-events' ); ?></p>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php
	$meta_key = taro_events_meta_prefix() . 'offers_url';
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
	</tbody>
</table>

<h3><?php esc_html_e( 'Organizer', 'taro-events' ); ?></h3>
<table class="form-table">
	<tbody>
	<?php
	$meta_key = taro_events_meta_prefix() . 'organizer_name';
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
	<?php
	$meta_key = taro_events_meta_prefix() . 'organizer_url';
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
	</tbody>
</table>
