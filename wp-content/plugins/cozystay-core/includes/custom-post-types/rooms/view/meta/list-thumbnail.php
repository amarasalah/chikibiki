<?php
	$pid = $post->ID;
	$list_thumbnail_id = get_post_meta( $pid, 'loftocean_room_list_thumbnail_id', true );
	$has_list_thumbnail = ( ! empty( $list_thumbnail_id ) ) && \LoftOcean\media_exists( $list_thumbnail_id ); ?>
	<p class="hide-if-no-js">
		<span class="list-thumbnail-description"><?php esc_html_e( 'This image will be displayed in the Rooms List instead of the default featured image.', 'loftocean' ); ?></span>
	</p>
	<div class="list-thumbnail-has-image-wrapper"<?php if ( ! $has_list_thumbnail ) : ?> style="display: none;"<?php endif; ?>>
		<p class="hide-if-no-js">
			<a href="#" class="set-list-thumbnail"><?php
			if ( $has_list_thumbnail ) :
				echo wp_get_attachment_image( $list_thumbnail_id, array( 254, 9999999 ) );
			endif; ?>
			</a>
		</p>
		<p class="hide-if-no-js howto set-post-thumbnail-desc"><?php esc_html_e( 'Click the image to edit or update', 'loftocean' ); ?></p>
		<p class="hide-if-no-js"><a href="#" class="remove-list-thumbnail"><?php esc_html_e( 'Remove list thumbnail image', 'loftocean' ); ?></a></p>
	</div>
	<div class="list-thumbnail-no-image-wrapper"<?php if ( $has_list_thumbnail ) : ?> style="display: none;"<?php endif; ?>>
		<p class="hide-if-no-js">
			<a href="#" class="set-list-thumbnail"><?php esc_html_e( 'Set a list thumbnail image', 'loftocean' ); ?></a>
		</p>
	</div>
	<input type="hidden" id="loftocean_room_list_thumbnail_id" name="loftocean_room_list_thumbnail_id" value="<?php echo esc_attr( $list_thumbnail_id ); ?>" />
	<input type="hidden" name="loftocean_room_nonce" value="<?php echo esc_attr( wp_create_nonce( 'loftocean_room_nonce' ) ); ?>" />
