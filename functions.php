<?php

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
