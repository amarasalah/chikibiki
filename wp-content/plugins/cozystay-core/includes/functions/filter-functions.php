<?php
namespace LoftOcean;
/**
* Get allowed html for kses function
* @param array
* @return array
*/
function kses_allowed_tags( $tags ) {
	return array(
		'a' => array(),
		'b' => array(),
		'em' => array(),
		'span' => array(),
		'strong' => array(),
		'italic' => array(),
		'i' => array()
	);
}
add_filter( 'loftocean_sanitize_get_kses_allowed_tags', '\LoftOcean\kses_allowed_tags' );

/**
* Disable default recent comment inline style
* @param boolean
* @param string
* @return boolean
*/
function disable_recent_comment_style( $active, $id ) {
	return false;
}
add_filter( 'show_recent_comments_widget_style', '\LoftOcean\disable_recent_comment_style', 10, 2 );

/*
* Get WP_Query argument for widget posts
* @param array original args
* @param array settings
* @return array
*/
function widget_posts_args( $args, $settings ) {
	$args['post_status'] = is_user_logged_in() ? array( 'publish', 'private' ) : 'publish';
	if ( $settings['filter-by'] ) {
		switch ( $settings['filter-by'] ) {
			case 'latest':
				break;
			case 'category':
				$category_ids = \LoftOcean\convert_tax_slug2id( (array) $settings['category'] );
				if ( $category_ids ) {
					$args['cat'] = implode( ',', $category_ids );
				}
				break;
			case 'tag':
				$tags = (array)$settings['tag'];
				$tags = array_filter( $tags );
				if ( \LoftOcean\is_valid_array( $tags ) ) {
					$args['tag'] = implode( ',', $tags );
				}
				break;
			case 'featured':
				$args = array_merge( $args, array(
					'meta_key' => 'loftocean-featured-post',
					'meta_value' => 'on'
				) );
				break;
			case 'views':
				$args = array_merge( $args, array(
					'orderby' => 'meta_value_num',
					'meta_key' => 'loftocean-view-count',
					'order' => 'DESC'
				) );
				break;
			case 'likes':
				$args = array_merge( $args, array(
					'orderby' => 'meta_value_num',
					'meta_key' => 'loftocean-like-count',
					'order' => 'DESC'
				) );
				break;
			case 'comments':
				$args = array_merge( $args, array( 'orderby' => 'comment_count', 'order' => 'DESC' ) );
				break;
			case 'format':
				$format = $settings['post-format'];
				if ( 'standard' == $format ) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'post_format',
							'operator' => 'NOT EXISTS'
						)
					);
				} else {
					$args['tax_query'] =  array(
						array(
							'taxonomy' => 'post_format',
							'field'    => 'slug',
							'terms'    => array( sprintf( 'post-format-%s', $format ) )
						)
					);
				}
				break;
			case 'static':
				if ( ! empty( $settings['staticids'] ) ) {
					$args['post__in'] = explode( ',', $settings['staticids'] );
				}
				break;
			default:
				$filter = $settings['filter-by'];
				if ( ( 0 === strpos( $filter, 'recipe_taxonomy_' ) ) && ( ! empty( $settings['recipetax'] ) ) ) {
					$args['post_type'] = 'wprm_recipe';
					$args['tax_query'] =  array( array( 'taxonomy' => str_replace( 'recipe_taxonomy_', '', $filter ), 'field' => 'slug', 'terms' => $settings[ 'recipetax' ] ) );
					$args['meta_query'] = array( array( 'key' => 'wprm_parent_post_id', 'compare' => 'EXISTS' ) );
				}
				break;
		}
	}
	return $args;
}
add_filter( 'loftocean_get_widget_posts_query_args', '\LoftOcean\widget_posts_args', 10, 2 );
