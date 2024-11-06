<?php
	$prev_label = esc_html__( 'Prev', 'loftocean' );
	$next_label = esc_html__( 'Next', 'loftocean' );
	$prev_link 	= get_previous_posts_link( $prev_label );
	$next_link 	= get_next_posts_link( $next_label );

	if ( ! empty( $prev_link) || ! empty( $next_link ) ) : ?>
		<nav class="navigation pagination">
			<div class="pagination-container prev-next">
				<h2 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'loftocean' ); ?></h2>
				<?php if ( empty( $prev_link ) ) : ?>
					<span class="prev page-numbers"><?php esc_html_e( 'Prev', 'loftocean' ); ?> </span>
				<?php else: ?>
					<?php previous_posts_link( $prev_label ); ?>
				<?php endif; ?>
				<?php if ( empty( $next_link ) ) : ?>
					<span class="next page-numbers"> <?php esc_html_e( 'Next', 'loftocean' ); ?></span>
				<?php else: ?>
					<?php next_posts_link( $next_label ); ?>
				<?php endif; ?>
			</div>
		</nav> <?php
	endif;
