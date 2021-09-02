<?php

// Create the Content Block custom post type
function ingeni_cpw_post_type_init() {
	$labels = array(
		'name' => _x( 'Ingeni Content Blocks', 'post type general name', 'ingeni-content-blocks-widget' ),
		'singular_name' => _x( 'Ingeni Content Block', 'post type singular name', 'ingeni-content-blocks-widget' ),
		'plural_name' => _x( 'Ingeni Content Blocks', 'post type plural name', 'ingeni-content-blocks-widget' ),
		'add_new' => _x( 'Add Ingeni Content Block', 'block', 'ingeni-content-blocks-widget' ),
		'add_new_item' => __( 'Add New Ingeni Content Block', 'ingeni-content-blocks-widget' ),
		'edit_item' => __( 'Edit Ingeni Content Block', 'ingeni-content-blocks-widget' ),
		'new_item' => __( 'New Ingeni Content Block', 'ingeni-content-blocks-widget' ),
		'view_item' => __( 'View Ingeni Content Block', 'ingeni-content-blocks-widget' ),
		'search_items' => __( 'Search Ingeni Content Blocks', 'ingeni-content-blocks-widget' ),
		'not_found' =>  __( 'No Ingeni Content Blocks Found', 'ingeni-content-blocks-widget' ),
		'not_found_in_trash' => __( 'No Ingeni Content Blocks found in Trash', 'ingeni-content-blocks-widget' )
	);
	$ingeni_content_block_public = false; // added to make this a filterable option
	$options = array(
		'labels' => $labels,
		'public' => apply_filters( 'content_block_post_type', $ingeni_content_block_public ),
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'show_in_rest' => true,
		'hierarchical' => false,
		'menu_icon' => 'dashicons-screenoptions',
		'supports' => array( 'title','editor','revisions','thumbnail','author','custom-fields','excerpt' )
	);
	register_post_type( 'ingeni_content_block',$options );
}
add_action( 'init', 'ingeni_cpw_post_type_init' );

function ingeni_content_block_messages( $messages ) {
	$messages['content_block'] = array(
		0 => '',
		1 => current_user_can( 'edit_theme_options' ) ? sprintf( __( 'Ingeni Content Block updated. <a href="%s">Manage Widgets</a>', 'ingeni-content-blocks-widget' ), esc_url( 'widgets.php' ) ) : sprintf( __( 'Content Block updated.', 'ingeni-content-blocks-widget' ), esc_url( 'widgets.php' ) ),
		2 => __( 'Ingeni Content Blockfield updated.', 'ingeni-content-blocks-widget' ),
		3 => __( 'Ingeni Content Blockfield deleted.', 'ingeni-content-blocks-widget' ),
		4 => __( 'Ingeni Content Block updated.', 'ingeni-content-blocks-widget' ),
		5 => isset($_GET['revision']) ? sprintf( __( 'Ingeni Content Block restored to revision from %s', 'ingeni-content-blocks-widget' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => current_user_can( 'edit_theme_options' ) ? sprintf( __( 'Ingeni Content Block published. <a href="%s">Manage Widgets</a>', 'ingeni-content-blocks-widget' ), esc_url( 'widgets.php' ) ) : sprintf( __( 'Content Block published.', 'ingeni-content-blocks-widget' ), esc_url( 'widgets.php' ) ),
		7 => __( 'Block saved.', 'ingeni-content-blocks-widget' ),
		8 => current_user_can( 'edit_theme_options' ) ? sprintf( __( 'Ingeni Content Block submitted. <a href="%s">Manage Widgets</a>', 'ingeni-content-blocks-widget' ), esc_url( 'widgets.php' ) ) : sprintf( __( 'Content Block submitted.', 'ingeni-content-blocks-widget' ), esc_url( 'widgets.php' ) ),
		9 => sprintf( __( 'Ingeni Content Block scheduled for: <strong>%1$s</strong>.', 'ingeni-content-blocks-widget' ), date_i18n( __( 'M j, Y @ G:i' , 'ingeni-content-blocks-widget' ), strtotime(isset($post->post_date) ? $post->post_date : null) ), esc_url( 'widgets.php' ) ),
		10 => current_user_can( 'edit_theme_options' ) ? sprintf( __( 'Ingeni Content Block draft updated. <a href="%s">Manage Widgets</a>', 'ingeni-content-blocks-widget' ), esc_url( 'widgets.php' ) ) : sprintf( __( 'Content Block draft updated.', 'ingeni-content-blocks-widget' ), esc_url( 'widgets.php' ) ),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'ingeni_content_block_messages' );