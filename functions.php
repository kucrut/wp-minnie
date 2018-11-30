<?php

/**
 * Register HiDPI image sizes
 *
 * @global array $_wp_additional_image_sizes
 */
function minnie_add_hidpi_image_sizes() {
	global $_wp_additional_image_sizes;

	foreach ( (array) $_wp_additional_image_sizes as $size => $props ) {
		$new_size = array(
			"${size}-2x",
			( intval( $props['width'] ) * 2 ),
			( intval( $props['height'] ) * 2 ),
			$props['crop'],
		);

		call_user_func_array( 'add_image_size', $new_size );
	}

	foreach ( array( 'thumbnail', 'medium', 'large' ) as $size ) {
		$new_size = array(
			"${size}-2x",
			( intval( get_option( "${size}_size_w" ) ) * 2 ),
			( intval( get_option( "${size}_size_h" ) ) * 2 ),
			get_option( "${size}_crop" ),
		);

		call_user_func_array( 'add_image_size', $new_size );
	}
}

/**
 * Setup Minnie Theme
 *
 * @wp_hook action after_setup_theme
 */
function minnie_setup() {
	/**
	 * Register menu locations
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'minnie' ),
		'social'  => __( 'Social Links', 'minnie' ),
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support(
		'post-formats',
		array( 'aside', 'image', 'video', 'quote', 'link', 'audio', 'gallery', 'status' )
	);

	/*
	 * Enable support for HTML5's gallery markup.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support(
		'html5',
		array( 'gallery' )
	);

	minnie_add_hidpi_image_sizes();
}
add_action( 'after_setup_theme', 'minnie_setup' );


/**
 * Register to the Bridge plugin
 *
 * @param   array $client_ids Client IDs.
 * @wp_hook filter bridge_client_ids
 * @return  array
 */
function minnie_register_to_bridge( $client_ids ) {
	$client_ids[] = 'minnie';

	return $client_ids;
}
add_filter( 'bridge_client_ids', 'minnie_register_to_bridge' );


/**
 * Filter preview post link
 *
 * Structure: <domain>/preview/<post-type>/<post-id>
 *
 * @param  string  $link Original preview link.
 * @param  WP_Post $post Post object.
 * @return string
 */
function minnie_filter_post_link( $link, WP_Post $post ) {
	$link = sprintf(
		'%s/preview/%s/%d',
		home_url(),
		$post->post_type,
		$post->ID
	);

	return $link;
}
add_filter( 'preview_post_link', 'minnie_filter_post_link', 10, 2 );


/**
 * Add custom attributes to images
 */
function minnie_image_custom_attributes( $attr, $attachment, $size ) {
	$meta = wp_get_attachment_metadata( $attachment->ID );
	$attr['data-ow'] = $meta['width'];
	$attr['data-oh'] = $meta['height'];

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'minnie_image_custom_attributes', 10, 3 );


/**
 * Set maximum srcset image width
 *
 * @return int
 */
function minnie_max_srcset_image_width() {
	return 2880;
}
add_filter( 'max_srcset_image_width', 'minnie_max_srcset_image_width' );


/**
 * Image size names
 *
 * @global array $_wp_additional_image_sizes
 *
 * @param array $sizes Image sizes
 *
 * @return array
 */
function minnie_image_size_names( $sizes ) {
	global $_wp_additional_image_sizes;

	foreach ( $_wp_additional_image_sizes as $name => $props ) {
		// If the name is already added, skip.
		if ( ! empty( $sizes[ $name ] ) ) {
			continue;
		}

		$pos = strrpos( $name, '-2x', -3 );

		// If this is not the size we added, skip.
		if ( false === $pos ) {
			continue;
		}

		$orig_name = substr( $name, 0, $pos );

		// If the size doesn't have a name, skip.
		if ( empty( $sizes[ $orig_name ] ) ) {
			continue;
		}

		$sizes[ $name ] = sprintf( esc_html__( '%s @ 2x', 'minnie' ), $sizes[ $orig_name ] );
	}

	return $sizes;
}
add_filter( 'image_size_names_choose', 'minnie_image_size_names', 99 );


/**
 * Add `data-zoom` attribute to image linked to its original file
 *
 * @param string       $html    The image HTML markup to send.
 * @param int          $id      The attachment id.
 * @param string       $caption The image caption.
 * @param string       $title   The image title.
 * @param string       $align   The image alignment.
 * @param string       $url     The image source URL.
 * @param string|array $size    Size of image. Image size or array of width and height values
 *                              (in that order). Default 'medium'.
 * @param string       $alt     The image alternative, or alt, text.
 *
 * @return string
 */
function minnie_filter_image_send_to_editor( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	if ( ! $url ) {
		return $html;
	}

	$upload_dir = wp_upload_dir();

	if ( false !== strpos( $url, $upload_dir['baseurl'] ) ) {
		$html = str_replace( '<a', '<a data-zoom="1"', $html );
	}

	return $html;
}
add_filter( 'image_send_to_editor', 'minnie_filter_image_send_to_editor', 1, 8 );
