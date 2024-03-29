<?php

// Meta boxes on content_block edit page
function ingeni_cpw_add_meta_boxes() {
	add_meta_box( 'ingeni_cpw_info', __( 'Content Block Information', 'ingeni-content-blocks-widget' ), 'ingeni_cpw_info_meta_box', 'ingeni_content_block', 'side' );
	add_meta_box( 'ingeni_cpw_shortcode', __( 'Content Block Shortcodes', 'ingeni-content-blocks-widget' ), 'ingeni_cpw_shortcode_meta_box', 'ingeni_content_block', 'side' );
}
add_action( 'add_meta_boxes', 'ingeni_cpw_add_meta_boxes' );

// Shortcode meta box
function ingeni_cpw_shortcode_meta_box( $post ) { ?>
	<p><?php _e( 'You can place this content block into your posts, pages, custom post types or widgets using the shortcode below:','ingeni-content-blocks-widget' ); ?></p>
	<code class="cpw-code" id="cpw-shortcode-1"><?php echo '[content_block id=' . $post -> ID . ']'; ?></code>
	<span class="cpw-clipboard" data-clipboard-target="#cpw-shortcode-1"><?php _e( 'Copy to clipboard', 'ingeni-content-blocks-widget' ); ?></span>

	<p><?php _e( 'Shortcode to use if you prefer using the slug instead of the post ID:','ingeni-content-blocks-widget' ); ?></p>
	<code class="cpw-code" id="cpw-shortcode-2"><?php echo '[content_block slug=' . $post -> post_name . ']'; ?></code>
	<span class="cpw-clipboard" data-clipboard-target="#cpw-shortcode-2"><?php _e( 'Copy to clipboard', 'ingeni-content-blocks-widget' ); ?></span>

	<p><?php _e( 'Use this shortcode to include the content block title:','ingeni-content-blocks-widget' ); ?></p>
	<code class="cpw-code" id="cpw-shortcode-3"><?php echo '[content_block id=' . $post -> ID . ' title=yes title_tag=h3]'; ?></code>
	<span class="cpw-clipboard" data-clipboard-target="#cpw-shortcode-3"><?php _e( 'Copy to clipboard', 'ingeni-content-blocks-widget' ); ?></span>
<?php
}

// Info meta box
function ingeni_cpw_info_meta_box( $post ) {
	wp_nonce_field( 'ingeni_cpw_info_meta_box', 'ingeni_cpw_info_meta_box_nonce' );
	$value = get_post_meta( $post -> ID, '_ingeni_content_block_information', true );
	echo '<p>' . __( 'You can use this field to describe this content block:','ingeni-content-blocks-widget' ) . '</p>';
	echo '<textarea class="cpw-information" id="ingeni_cpw_content_block_information" cols="40" rows="4" name="ingeni_cpw_content_block_information">' . esc_attr( $value ) . '</textarea>';
}

function ingeni_cpw_save_postdata( $post_id ) {
	if ( ! isset( $_POST['ingeni_cpw_info_meta_box_nonce'] ) )
		return $post_id;

	$nonce = $_POST['ingeni_cpw_info_meta_box_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'ingeni_cpw_info_meta_box' ) )
		return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	if ( 'ingeni_content_block' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
	}

	//$content_block_information = sanitize_text_field( $_POST['ingeni_cpw_content_block_information'] );
	//update_post_meta( $post_id, '_content_block_information', $content_block_information );
	$content_block_information = sanitize_text_field( $_POST['ingeni_cpw_content_block_information'] );
	update_post_meta( $post_id, '_ingeni_content_block_information', $content_block_information );
}
add_action( 'save_post', 'ingeni_cpw_save_postdata' );

// Add content block information column to overview
function ingeni_cpw_modify_content_block_table( $column ) {
	$column['ingeni_content_block_information'] = __( 'Content Block Information', 'ingeni-content-blocks-widget' );
	return $column;
}
add_filter( 'manage_edit-content_block_columns', 'cpw_modify_content_block_table' );

function ingeni_cpw_modify_post_table_row( $column_name, $post_id ) {
	$custom_fields = get_post_custom( $post_id );
	switch ( $column_name ) {
		case 'ingeni_content_block_information' :
			if ( !empty( $custom_fields['_ingeni_content_block_information'][0] ) ) {
				echo $custom_fields['_ingeni_content_block_information'][0];
			}
		break;
	}
}
add_action( 'manage_posts_custom_column', 'ingeni_cpw_modify_post_table_row', 10, 2 );