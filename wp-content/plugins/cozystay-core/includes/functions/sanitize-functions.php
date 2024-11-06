<?php
namespace LoftOcean;
/**
* Sanitize functions used by this plugin
*/

/**
* Sanitize value type array
* @param mix
* @return array
*/
function sanitize_array( $value ) {
	return \LoftOcean\is_valid_array( $value ) ? $value : array();
}
/**
* Sanitize value from checkbox
* @param mix
* @return string
*/
function sanitize_checkbox( $value ) {
	return empty( $value ) ? '' : 'on';
}
/**
* Always rturn empty string
* @param mix
* @return string
*/
function sanitize_empty( $value ) {
	return '';
}
/**
* Sanitize html string
* @param mix
* @return string
*/
function sanitize_html( $text ) {
	return empty( $text ) ? '' : apply_filters( 'format_to_edit', $text );
}
/**
* Allowed html tag for img tag
* @return array
*/
function get_img_allowed_attrs() {
	return array(
		'img' => array(
			'src' => array(),
			'id' => array(),
			'class' => array(),
			'alt' => array(),
			'srcset' => array(),
			'title' => array(),
			'sizes' => array(),
			'width' => array(),
			'height' => array(),
			'data-*' => 1
		)
	);
}
/**
* Allowed html tag for custom content
*/
function get_custom_content_allowed_html() {
	$allowed_html = wp_kses_allowed_html( 'post' );
	$img_attrs = \LoftOcean\get_img_allowed_attrs();
	$iframes = array( 'src' => 1, 'width' => 1, 'height' => 1, 'class' => 1, 'id' => 1, 'frameborder' => 1, 'allow' => 1, 'allowfullscreen' => 1, 'style' => 1, 'data-*' => 1 );
	$scripts = array( 'async' => 1, 'src' => 1, 'type' => 1, 'data-*' => 1, 'crossorigin' => 1 );
	$videos = array( 'class' => 1, 'id' => 1, 'width' => 1, 'height' => 1, 'preload' => 1, 'controls' => 1, 'src' => 1, 'data-*' => 1 );
	$audios = array( 'class' => 1, 'id' => 1, 'width' => 1, 'height' => 1, 'preload' => 1, 'controls' => 1, 'src' => 1, 'data-*' => 1 );
	$sources = array( 'type' => 1, 'src' => 1, 'data-*' => 1 );
	$divs = array( 'data-*' => 1, 'class' => 1, 'style' => 1, 'id' => 1, 'overflow' => 1 );
	$ins = array( 'style' => 1, 'class' => 1, 'data-*' => 1 );
	$amp_ad = array( 'width' => 1, 'height' => 1, 'type' => 1, 'data-*' => 1 );

	$allowed_html['img'] = isset( $allowed_html['img' ] ) ? array_merge( $img_attrs['img'] , $allowed_html['img'] ) : $img_attrs['img'];
	$allowed_html['source'] = isset( $allowed_html['source'] ) ? array_merge( $sources, $allowed_html['source'] ) : $sources;
	$allowed_html['video'] = isset( $allowed_html['video'] ) ? array_merge( $videos, $allowed_html['video'] ) : $videos;
	$allowed_html['audio'] = isset( $allowed_html['audio'] ) ? array_merge( $audios, $allowed_html['audio'] ) : $audios;
	$allowed_html['div'] = isset( $allowed_html['div'] ) ? array_merge( $divs, $allowed_html['div'] ) : $divs;
	$allowed_html['iframe'] = isset( $allowed_html['iframe'] ) ? array_merge( $iframes, $allowed_html['iframe'] ) : $iframes;
	$allowed_html['script'] = isset( $allowed_html['script'] ) ? array_merge( $scripts, $allowed_html['script'] ) : $scripts;
	$allowed_html['amp-ad'] = isset( $allowed_html['amp-ad'] ) ? array_merge( $amp_ad, $allowed_html['amp-ad'] ) : $amp_ad;
	$allowed_html['ins'] = isset( $allowed_html['ins'] ) ? array_merge( $ins, $allowed_html['ins'] ) : $ins;

	return $allowed_html;
}
/**
* Allowed html tag for shortcode tmpl
* @return array
*/
function get_shortcode_tmpl_allowed_html() {
	$allowed_html = wp_kses_allowed_html( 'post' );
	if ( isset( $allowed_html['span'] ) ) {
		$allowed_html['span'] = array_merge( $allowed_html, array(
			'class' => true,
			'id' => true,
			'style' => true,
			'data-*' => true
		) );
	} else {
		$allowed_html['span'] = array(
			'class' => true,
			'id' => true,
			'style' => true,
			'data-*' => true
		);
	}
	return $allowed_html;
}
