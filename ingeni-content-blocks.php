<?php
/*
 Plugin Name: Ingeni Content Blocks 
 Plugin URI: https://ingeni.net
 Description: Show the content of a custom post of the type 'content_block' in a widget or with a shortcode. This is an extended version of the Custom Post Widget v3.2 (http://www.vanderwijk.com/wordpress/wordpress-content-blocks-widget) but with support for multiple templates.
 Version: 2022.01
 Author: Johan van der Wijk & Bruce McKinnon
 Author URI: https://ingeni.net
 Donate link: https://www.paypal.me/vanderwijk
 Text Domain: ingeni-content-blocks-widget
 Domain Path: /languages
 License: GPL2

 Release notes: You can now use your own templates for outputting the shortcode contents

 Copyright 2021 Johan van der Wijk

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as
 published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

 v2021.02 - Added support for 'custom-fields' and 'excerpt'

 v2022.01 - Fixed issue with undefined located variable in shortcode.php
*/

// Launch the plugin.
function ingeni_content_blocks_widget_plugin_init() {
	add_action( 'widgets_init', 'ingeni_content_blocks_widget_load_widgets' );

	// Init auto-update from GitHub repo
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/BruceMcKinnon/IngeniContentBlocks',
		__FILE__,
		'ingeni-content-blocks'
	);

}
add_action( 'plugins_loaded', 'ingeni_content_blocks_widget_plugin_init' );

// Loads the widgets packaged with the plugin.
function ingeni_content_blocks_widget_load_widgets() {
	require_once( 'post-type.php' );
	require_once( 'shortcode.php' );
	require_once( 'widget.php' );
	register_widget( 'ingeni_content_blocks_widget' );
}

// Load plugin textdomain.
function ingeni_content_blocks_widget_load_textdomain() {
	load_plugin_textdomain( 'ingeni-content-blocks-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ingeni_content_blocks_widget_load_textdomain' );

// Add featured image support
/*
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}
*/

/*
//add_action( 'init', 'ingeni_content_blocks_add_support' );
function ingeni_content_blocks_add_support() {
     add_post_type_support( 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ); //change page with your post type slug.
}
*/

// Admin-only functions
if ( is_admin() ) {

	// Add donation and review links to plugin description
	/* if ( ! function_exists ( 'cpw_plugin_links' ) ) {
	
		function cpw_plugin_links( $links, $file ) {
			$base = plugin_basename( __FILE__ );
			if ( $file == $base ) {
				$links[] = '<a href="https://wordpress.org/support/plugin/content-blocks-widget/reviews/" target="_blank">' . __( 'Review', 'content-blocks-widget' ) . ' <span class="dashicons dashicons-thumbs-up"></span></a> | <a href="https://paypal.me/vanderwijk">' . __( 'Donate', 'content-blocks-widget' ) . ' <span class="dashicons dashicons-money"></span></a>';
			}
			return $links;
		}
	}
	add_filter( 'plugin_row_meta', 'cpw_plugin_links', 10, 2 );
	*/

	require_once( 'meta-box.php' );
	require_once( 'popup.php' );

	// Enqueue styles and scripts on content_block edit page
	function ingeni_cpw_enqueue() {
		$screen = get_current_screen();
		// Check screen base and current post type
		if ( 'post' === $screen -> base && 'content_block' === $screen -> post_type ) {
			wp_enqueue_style( 'cpw-style', plugins_url( '/assets/css/content-blocks-widget.css', __FILE__ ) );
			wp_enqueue_script( 'clipboard', plugins_url( '/assets/js/clipboard.min.js', __FILE__ ), array(), '2.0.6', true );
			wp_enqueue_script( 'clipboard-init', plugins_url( '/assets/js/clipboard.js', __FILE__ ), array(), false, true );
		}
	}
	add_action( 'admin_enqueue_scripts', 'ingeni_cpw_enqueue' );

	// Only add content_block icon above posts and pages
	function ingeni_cpw_add_content_block_button() {
		global $current_screen;
		if ( ( 'content_block' != $current_screen -> post_type ) && ( 'toplevel_page_revslider' != $current_screen -> id ) ) {
			add_action( 'media_buttons', 'add_content_block_icon' );
			add_action( 'admin_footer', 'add_content_block_popup' );
		}
	}
	add_action( 'admin_head', 'ingeni_cpw_add_content_block_button' );

}