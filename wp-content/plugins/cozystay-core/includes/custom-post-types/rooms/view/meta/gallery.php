<?php
	$pid = $post->ID;
	$gallery_ids = get_post_meta( $pid, 'loftocean_room_gallery_ids', true );
	$has_gallery = ! empty( $gallery_ids ); ?>

	<div class="gallery-has-image-wrapper"<?php if ( ! $has_gallery ) : ?> style="display: none;"<?php endif; ?>>
		<p class="hide-if-no-js">
			<ul class="gallery-preview-list"><?php
			if ( $has_gallery ) :
				$gallery_ids_array = explode( ',', $gallery_ids );
				foreach ( $gallery_ids_array as $gid ) : ?>
					<li><a href="#" class="set-gallery"><?php echo wp_get_attachment_image( $gid, array( 60, 9999999 ) ); ?></a></li><?php
				endforeach;
			endif; ?>
			</ul>
		</p>
		<p class="hide-if-no-js howto set-gallery-desc"><?php esc_html_e( 'Click the images to edit or update', 'loftocean' ); ?></p>
		<p class="hide-if-no-js"><a href="#" class="remove-gallery"><?php esc_html_e( 'Remove gallery', 'loftocean' ); ?></a></p>
	</div>
	<div class="gallery-no-image-wrapper"<?php if ( $has_gallery ) : ?> style="display: none;"<?php endif; ?>>
		<p class="hide-if-no-js">
			<a href="#" class="set-gallery"><?php esc_html_e( 'Add Gallery', 'loftocean' ); ?></a>
		</p>
	</div>
	<input type="hidden" id="loftocean_room_gallery_ids" name="loftocean_room_gallery_ids" value="<?php echo esc_attr( $gallery_ids ); ?>" />
