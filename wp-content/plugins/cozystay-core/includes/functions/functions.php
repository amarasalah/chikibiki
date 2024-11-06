<?php
namespace LoftOcean;
/**
* Test if currently is in customize.php or customization preview or saving customize settings
*/
function is_customize() {
	global $pagenow;
	$is_customize_admin 	= is_admin() && ! empty( $pagenow ) && ( 'customize.php' === $pagenow );
	$is_customize_preview 	= ! empty( $_REQUEST['customize_changeset_uuid'] );
	$is_set_customize 		= isset( $_REQUEST['wp_customize'] ) && ( 'on' === sanitize_text_field( wp_unslash( $_REQUEST['wp_customize'] ) ) );
	return current_user_can( 'customize' ) && ( $is_customize_admin || $is_set_customize || $is_customize_preview );
}
/**
* @description helper function to get image src
* @param int image id
* @param string image size
* @return mix string image url or false
*/
function get_image_src( $image_id, $image_size = false ) {
	if ( \LoftOcean\media_exists( $image_id ) ) {
		$size = empty( $image_size ) ? 'full' : $image_size;
		$image = wp_get_attachment_image_src( $image_id, $image_size );
		return $image ? $image[0] : false;
	}
	return false;
}
/**
* Get image alt text
* @param int image id
* @return string image alt text
*/
function get_image_alt( $image_id ) {
	if ( \LoftOcean\media_exists( $image_id ) ) {
		$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		if ( empty( $alt ) ) {
			$attachment = get_post( $image_id );
			$post_id = $attachment->post_parent;
			$post_title = ! empty( $post_id ) && ( false !== get_post_status( $post_id ) ) ? get_the_title( $post_id ) : false;
			if ( ! empty( $post_title ) ) {
				return esc_attr( $post_title );
			} else {
				return empty( $attachment->post_title ) ? esc_attr( $attachment->post_name ) : esc_attr( $attachment->post_title );
			}
		} else {
			return esc_attr( $alt );
		}
	}

	return '';
}
/**
* Test if image exists
* @param int image id
* @return boolean
*/
function media_exists( $id ) {
	return ! empty( $id ) && ( false !== get_post_status( $id ) );
}
/**
* Convert tax slug to id
* @param mix string or array
* @param string taxonomy
* @return mix string or array
*/
function convert_tax_slug2id( $slugs, $tax = 'category' ) {
	if ( ! empty( $slugs ) && ! empty( $tax ) ) {
		if ( is_array( $slugs ) ) {
			$ids = array();
			foreach ( $slugs as $slug ) {
				$term = get_term_by( 'slug', $slug, $tax );
				if ( ! empty( $term ) ) {
					array_push( $ids, $term->term_id );
				}
			}
			return empty( $ids ) ? false : $ids;
		} elseif ( is_string( $slugs ) ) {
			$term = get_term_by( 'slug', $slugs, $tax );
			return empty( $term ) ? false : $term->term_id;
		}
	}
	return false;
}
/*
* Get terms array by given argument
*
* @param array refer to https://developer.wordpress.org/reference/functions/get_terms/
* @param boolean flag to add the all option or not
* @param mix the all option label, if not provided use the default 'All'
* @return array of terms with term_id as index and term name as value
*/
function get_terms( $tax, $all = true, $all_label = false ) {
	$terms = \get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
	if ( ! is_wp_error( $terms ) ) {
		$array = $all ? array( '' => ( empty( $all_label ) ? esc_html__( 'All', 'loftocean' ) : $all_label ) ) : array();
		foreach ( $terms as $t ) {
			$array[ $t->slug ] = $t->name;
		}
		return $array;
	}
	return array();
}
/*
* Get Mailchimp for WP forms
* @return array of Mailchimp for WP forms with form_id as index and form title as value
*/
function mc4w_forms() {
	$forms = get_posts( array(
		'posts_per_page' => -1,
		'post_type' => 'mc4wp-form'
	) );
	if ( ! is_wp_error( $forms ) ) {
		$array = array( '' => esc_html__( 'Choose Form', 'loftocean' ) );
		foreach ( $forms as $f ) {
			$array[ $f->ID ] = $f->post_title;
		}
		return $array;
	}
	return array();
}
/**
* Get default mc4wp form id
* @return mix empty or form id
*/
function default_mc4wp_form_id() {
	if ( function_exists( 'mc4wp' ) ) {
		$forms = \LoftOcean\mc4w_forms();
		return ! empty( $forms ) && ( count( $forms ) > 1 ) ? array_keys( $forms )[1] : '';
	}
	return '';
}
/**
* Get category list with attributes name, link, count
*/
function get_category_list( $pid ) {
	$terms = get_the_terms( $pid, 'category' );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return false;
	}
	$list = array();
	foreach ( $terms as $term ) {
		$list[] = array(
			'name' => $term->name,
			'link' => get_term_link( $term, 'category' ),
			'count' => $term->count
		);
	}
	return $list;
}
/**
* Get category list html
*/
function get_categories( $pid ) {
	$terms = get_category_list( $pid );
	if ( empty( $terms ) ) {
		return '';
	}
	$list = array();
	foreach ( $terms as $term ) {
		$list[] = '<span>' . $term['name'] . '</span>';
	}
	return implode( ' ', $list );
}

/**
* Helper function to convert numbers
*/
function counter_format( $num ) {
	$num = intval( $num );
	if ( empty( $num ) ) {
		return 0;
	} elseif ( $num >= 1000000 ) {
		$num = floor( $num / 100000 ) / 10;
		return $num . 'M';
	} elseif( $num >= 1000 ) {
		$num = floor( $num / 100 ) / 10;
		return $num . 'K';
	} else {
		return $num;
	}
}

/**
* Fiter content tags
*/
function filter_content_tags( $content ) {
	if ( function_exists( 'wp_filter_content_tags' ) ) {
		return wp_filter_content_tags( $content );
	} else {
		return wp_make_content_images_responsive( $content );
	}
}
/**
* Help function to test if variable is array and not empty
* @param mix
* @return boolean
*/
function is_valid_array( $var ) {
	return ! empty( $var ) && is_array( $var ) && ( count( $var ) > 0 );
}
/**
* Get WPRM taxonomies
*/
function get_wprm_taxonomies( $args = array() ) {
	$args = array_merge( (array)$args, array( 'object_type' => array( 'wprm_recipe' ) ) );
	return get_taxonomies( $args, 'objects' );
}
/**
* Get featured category block options
*/
function get_featured_category_option() {
	$recipe_taxs = get_wprm_taxonomies( array( 'public' => true ) );
	$options = array( 'category' => esc_html__( 'Categories', 'loftocean' ), 'post_tag' => esc_html__( 'Tags', 'loftocean' ) );
	if ( \LoftOcean\is_valid_array( $recipe_taxs ) ) {
		foreach ( $recipe_taxs as $rt => $object ) {
			$options[ $rt ] = $object->label;
		}
	}
	return $options;
}
/**
* Get instagram feeds
*/
function get_instagram_feeds() {
	if ( method_exists( \SB_Instagram_Feed::class, 'set_cache' ) ) {
		global $wpdb;
		$feeds_table_name = $wpdb->prefix . 'sbi_feeds';
		$feeds_list = $wpdb->get_results( "SELECT id, feed_name FROM $feeds_table_name;" );

		if ( ! empty( $feeds_list ) ) {
			$sbi_statuses = get_option( 'sbi_statuses', array() );
			$feeds_elementor  = array( '' => ( empty( $sbi_statuses[ 'support_legacy_shortcode' ] ) ? esc_html__( 'Select a Feed', 'loftocean' ) : esc_html__( 'Default', 'loftocean' ) ) );
			foreach ( $feeds_list as $feed ) {
				$feeds_elementor[ $feed->id ] = $feed->feed_name;
			}
			return $feeds_elementor;
		}
	}
	return false;
}
/**
* Get current currency
*/
function get_current_currency() {
	$settings = \LoftOcean\get_current_currency_settings();
	$is_left = in_array( $settings[ 'symbolPosition' ], array( 'left', 'left_space' ) );
	$with_space = in_array( $settings[ 'symbolPosition' ], array( 'left_space', 'right_space' ) );
	return array(
		'left' => $is_left ? ( $settings[ 'symbol' ] . ( $with_space ? ' ' : '' ) ) : '',
		'right' => $is_left ? '' : ( ( $with_space ? ' ' : '' ) . $settings[ 'symbol' ] )
	);
}
/**
* Get current currency settings
*/
function get_current_currency_settings() {
	if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
		return array(
			'precision'         => wc_get_price_decimals(),
			'symbol'            => html_entity_decode( get_woocommerce_currency_symbol() ),
			'symbolPosition'    => get_option( 'woocommerce_currency_pos' ),
			'decimalSeparator'  => wc_get_price_decimal_separator(),
			'thousandSeparator' => wc_get_price_thousand_separator()
		);
	} else {
		return array(
			'precision'         => 2,
			'symbol'            => esc_html__( '$', 'loftocean' ),
			'symbolPosition'    => 'left',
			'decimalSeparator'  => '.',
			'thousandSeparator' => ''
		);
	}
}
/**
* Get formatted price
*/
function get_formatted_price( $price, $args ) {
	if ( is_array( $args ) ) {
		return number_format( $price, ( false === strpos( $price, '.' ) ? 0 : $args[ 'precision' ] ), $args[ 'decimalSeparator' ], $args[ 'thousandSeparator' ] );
	} else {
		return $price;
	}
}
/**
* Get currently registered public post types
*/
function get_post_types() {
	$types = \get_post_types( array( 'publicly_queryable' => true, '_builtin' => false ), 'objects' );
	$posts = array(
		'post' => esc_html__( 'Post', 'loftocean' ),
		'page' => esc_html__( 'Page', 'loftocean' )
	);
	foreach ( $types as $t ) {
		$posts[ $t->name ] = $t->label;
	}
	return $posts;
}
/**
* Merge arrays
*/
function merge_array( $arg1, $arg2 ) {
	foreach ( $arg2 as $name => $val ) {
		if ( isset( $arg1[ $name ] ) ) {
			if ( is_array( $arg1[ $name ] ) ) {
				$arg1[ $name ] = \LoftOcean\merge_array( $arg1[ $name ], $val );
			} else {
				$arg1[ $name ] = $val;
			}
		} else {
			$arg1[ $name ] = $val;
		}
	}
	return $arg1;
}
/**
* Price format
*/
function price_format( $price ) {
	if ( false === strpos( $price, '.' ) ) {
		return $price;
	} else {
		$price = number_format( $price, 2, '.', '' );
		$parts = explode( '.', $price );
		$parts[ 1 ] = rtrim( $parts[ 1 ], '0' );
		return ( '' == $parts[ 1 ] ) ? $parts[ 0 ] : $parts[ 0 ] . '.' . $parts[ 1 ];
	}
}
/**
* Get room prices
*/
function get_room_prices( $room_id ) {
	$prices = array(
		'regular' => array( 'night' => 0, 'adult' => 0, 'child' => 0 ),
		'weekend' => array( 'night' => 0, 'adult' => 0, 'child' => 0 )
	);
	if ( 'loftocean_room' == get_post_type( $room_id ) ) {
		$room_details = apply_filters( 'loftocean_get_room_details', array(), $room_id );
		if ( \LoftOcean\is_valid_array( $room_details ) && \LoftOcean\is_valid_array( $room_details[ 'roomSettings' ] ) ) {
			$prices[ 'regular' ][ 'night' ] = empty( $room_details[ 'roomSettings' ][ 'regularPrice' ] ) ? 0 : $room_details[ 'roomSettings' ][ 'regularPrice' ];
			$prices[ 'regular' ][ 'adult' ] = empty( $room_details[ 'roomSettings' ][ 'pricePerAdult' ] ) ? 0 : $room_details[ 'roomSettings' ][ 'pricePerAdult' ];
			$prices[ 'regular' ][ 'child' ] = empty( $room_details[ 'roomSettings' ][ 'pricePerChild' ] ) ? 0 : $room_details[ 'roomSettings' ][ 'pricePerChild' ];

			if ( 'on' == $room_details[ 'roomSettings' ][ 'enableWeekendPrices' ] ) {
				$prices[ 'weekend' ][ 'night' ] = empty( $room_details[ 'roomSettings' ][ 'weekendPricePerNight' ] ) ? 0 : $room_details[ 'roomSettings' ][ 'weekendPricePerNight' ];
				$prices[ 'weekend' ][ 'adult' ] = empty( $room_details[ 'roomSettings' ][ 'weekendPricePerAdult' ] ) ? 0 : $room_details[ 'roomSettings' ][ 'weekendPricePerAdult' ];
				$prices[ 'weekend' ][ 'child' ] = empty( $room_details[ 'roomSettings' ][ 'weekendPricePerChild' ] ) ? 0 : $room_details[ 'roomSettings' ][ 'weekendPricePerChild' ];
			}
		}
	}
	return $prices;
}
/**
* Get special price rate
*/
function get_special_price_rate( $list, $date ) {
	$default = 1;
	foreach( $list as $item ) {
		if ( 'all' == $item[ 'id' ] ) {
			$default = $item[ 'rate' ];
		} else if ( ( $date >= $item[ 'start' ] ) && ( $date <= $item[ 'end' ] ) ) {
			return $item[ 'rate' ];
		}
	}
	return $default;
}
/**
* Get room prices by given date timestamp
*/
function get_room_actual_prices( $data, $timestamp, $extra_args = array() ) {
	if ( ( 'on' == $data[ 'enableWeekendPrices' ] ) && \LoftOcean\is_weekend( $timestamp ) ) {
		return array( 'price_per_night' => $data[ 'weekendPricePerNight' ], 'price_per_adult' => $data[ 'weekendPricePerAdult' ], 'price_per_child' => $data[ 'weekendPricePerChild' ] );
	} else {
		return array( 'price_per_night' => $data[ 'regularPrice' ], 'price_per_adult' => $data[ 'pricePerAdult' ], 'price_per_child' => $data[ 'pricePerChild' ] );
	}
	return array_fill_keys( array( 'price_per_night', 'price_per_adult', 'price_per_child' ), 0 );
}
/**
* Test if a timestamp is weekend
*/
function is_weekend( $timestamp ) {
	if ( empty( $timestamp ) ) return false;

	$day_of_week = date( 'N', $timestamp );
	$weekend_settings = \LoftOcean\get_current_weekend_settings();
	return apply_filters( 'loftocean_is_weekend', in_array( 'day' . $day_of_week, $weekend_settings ), $day_of_week );
}
/**
* 
*/
function get_current_weekend_settings() {
	$current_weekend_days = get_option( 'loftocean_room_weekend_days_setting', array( 'day5', 'day6' ) );
	return \LoftOcean\is_valid_array( $current_weekend_days ) ? $current_weekend_days : array();
}
/**
* Is WooCommerce tax enabled
*/
function is_tax_enabled() {
	if ( function_exists( '\wc_tax_enabled' ) ) {
		return \wc_tax_enabled();
	}
	return false;
}
/**
* Get tax location
*/
function get_tax_location() {
	return array(
		'country' => WC()->countries->get_base_country(),
		'state' => WC()->countries->get_base_state(),
		'postcode' => WC()->countries->get_base_postcode(),
		'city' => WC()->countries->get_base_city()
	);
}
/**
* Get tax rate
*/
function get_tax_rate() {
	$tax_location = \LoftOcean\get_tax_location();
	$tax_location[ 'tax_class' ] = '';
	$taxes = \WC_Tax::find_rates( $tax_location );
	$rates = array( 'regular_rates' => array(), 'compound_rates' => array(), 'reversed_compound_rates' => array() );
	foreach ( $taxes as $tax ) {
		if ( 'yes' === $tax[ 'compound' ] ) {
			array_push( $rates[ 'compound_rates' ], $tax );
		} else {
			array_push( $rates[ 'regular_rates' ], $tax );
		}
	}
	if ( \LoftOcean\is_valid_array( $rates[ 'compound_rates' ] ) ) {
		$rates[ 'reversed_compound_rates' ] = array_reverse( $rates[ 'compound_rates' ] );
	}
	return $rates;
}
/**
* Get room translated ids
*/
function get_room_translated_ids( $id, $included_current_id = true ) {
	$ids = apply_filters( 'loftocean_multilingual_get_room_ids', false, $id, $included_current_id );
	return \LoftOcean\is_valid_array( $ids ) ? array_unique( array_values( $ids ) ) : ( $included_current_id ? array( $id ) : array() );
}
/**
* Generate room unique relationship ID
*/
function generate_room_unique_relationship_id() {
	return 'room-' . \wp_generate_uuid4();
}
/**
* Get current domain for iCal
*/
function get_current_domain() {
	$home_url = parse_url( home_url(), PHP_URL_HOST );
	return preg_replace( '/^www\./', '', $home_url );
}
/**
* Room Booked status
*/
function get_room_booked_status() {
	return apply_filters( 'loftocean_room_booked_status', array( 'processing', 'completed', 'on-hold' ) );
}
/**
* Room available status
*/
function get_room_available_status() {
	return apply_filters( 'loftocean_room_available_status', array( 'pending', 'cancelled', 'refunded', 'failed' ) );
}
/**
* Get room bookings
*/
function get_room_orders( $roomIDs ) {
	if ( function_exists( '\wc_get_orders' ) ) {
		$ppp = 30;
		$return_orders = array();
		$query_args = array(
			'limit' => $ppp,
			'offset' => 0,
			'return' => 'ids',
			'meta_query' => array(
				array(
					'key' => '_loftocean_order_room_ID',
					'value' => $roomIDs,
					'type' => 'numeric',
					'compare' => 'IN'
				)
			)
		);
		while ( 1 ) {
			$orders = \wc_get_orders( $query_args );
			if ( \LoftOcean\is_valid_array( $orders ) ) {
				$return_orders = array_merge( $return_orders, $orders );
				$query_args[ 'offset' ] += $ppp;
			} else {
				break;
			}
		}
		return $return_orders;
	}
	return array();
}
/**
* Room section capabilities
*/
function get_room_section_capabilities() {
	$default_cap = class_exists( '\WooCommerce' ) ? 'manage_woocommerce' : 'manage_options';
	return apply_filters( 'loftocean_room_section_capability', $default_cap );
}
/**
* Get local time offet
*/
function get_local_time_offset() {
	return get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS;
}
/*
* Get room variable prices settings
*/
function get_room_variable_prices( $roomID ) {
	$default_variable_prices = array( 'enable' => false, 'mode' => 'nightly', 'guestMode' => 'simple', 'enableWeekendPrice' => false, 'prices' => array() );
    if ( empty( $roomID ) || ( 'loftocean_room' != get_post_type( $roomID ) ) ) return $default_variable_prices;

	$variable_prices = apply_filters( 'loftocean_get_room_variable_prices', $default_variable_prices, $roomID );
	return array_merge( $default_variable_prices, $variable_prices );
}
function get_room_unique_id( $rid ) {
	$room_unique_id = \LoftOcean\Room\Relationship_Tools::get_room_relationship( $rid );
	if ( false === $room_unique_id ) {
		$room_ids = \LoftOcean\get_room_translated_ids( $rid, false );
		$update_args = array( 'room_id' => $rid, 'relationship_id' => '' );
		if ( \LoftOcean\is_valid_array( $room_ids ) ) {
			$other_room_relationship_id = false;
			foreach ( $room_ids as $room_id ) {
				$other_room_relationship_id = \LoftOcean\Room\Relationship_Tools::get_room_relationship( $room_id );
				if ( false !== $other_room_relationship_id ) {
					$room_unique_id = $other_room_relationship_id;
					$update_args[ 'relationship_id' ] = $room_unique_id;
					\LoftOcean\Room\Relationship_Tools::update_room_relationship( $update_args );
					break;
				}
			}
			if ( ! $other_room_relationship_id ) {
				$room_unique_id = \LoftOcean\generate_room_unique_relationship_id();
				$update_args[ 'relationship_id' ] = $room_unique_id;
				\LoftOcean\Room\Relationship_Tools::update_room_relationship( $update_args );
			}
		} else {
			$room_unique_id = \LoftOcean\generate_room_unique_relationship_id();
			$update_args[ 'relationship_id' ] = $room_unique_id;
			\LoftOcean\Room\Relationship_Tools::update_room_relationship( $update_args );
		}
	}
	return $room_unique_id;
}