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
}

add_action( 'after_setup_theme', 'minnie_setup' );
