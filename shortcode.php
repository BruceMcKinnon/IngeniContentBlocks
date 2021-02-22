<?php


function shortcode_template_callback( $post ) {
	return 'default';
}
add_filter( 'shortcode_template', 'shortcode_template_callback', 10, 1 );

// Add the ability to display the content block in a reqular post using a shortcode
function ingeni_content_blocks_shortcode( $atts ) {
	$params = shortcode_atts( array(
		'id' => '',
		'slug' => '',
		'class' => 'content_block',
		'suppress_content_filters' => 'no',
		'featured_image' => 'no',
		'featured_image_size' => 'medium',
		'title' => 'no',
		'title_tag' => 'h3',
		'markup' => 'div',
		'template' => '',
		'template_before_content' => '',
		'template_after_content' => ''
	), $atts );

	$id = $params['id'];
	$slug = $params['slug'];
	$class = $params['class'];
	$suppress_content_filters = $params['suppress_content_filters'];
	$featured_image = $params['featured_image'];
	$featured_image_size = $params['featured_image_size'];
	$title = $params['title'];
	$title_tag = $params['title_tag'];
	$markup = $params['markup'];
	$template = $params['template'];
	$template_before_content = $params['template_before_content'];
	$template_after_content = $params['template_after_content'];

	if ( $slug ) {
		$block = get_page_by_path( $slug, OBJECT, 'ingeni_content_block' );
		if ( $block ) {
			$id = $block->ID;
		}
	}

	$content = "";

	// Attempt to load a template file
	if ( $params['template'] != '' ) {

		$plugin_data = get_plugin_data( __FILE__ );
		$plugin_name = $plugin_data['TextDomain'];
		
		if ( $located = locate_template( $plugin_name . '/'. $params['template'] ) ) {
			include_once $located;
		}
	}

	if ( $id != "" ) {

		$args = array(
			'post__in' => array( $id ),
			'post_type' => 'ingeni_content_block',
		);

		$content_post = get_posts( $args );
		$content = $template_before_content;

		foreach( $content_post as $post ) :
fb_log('located: '.$located);
			if ( ( $located ) ) {

				// Template-based content
				$template_class_name = basename($located,'.php');
fb_log('class name: '.$template_class_name);				
				if ( class_exists( $template_class_name ) ) {
fb_log('need to make new class: '.$template_class_name);
					$template_class = new $template_class_name;
				}
				if ( class_exists ( $template_class_name ) ) {
fb_log('class exists: '.get_class($template_class));
					$content .= $template_class->shortcode_template( $post );
				} else {
fb_log('does not exist: '.$template_class_name );
				}


			} else {
				// Standard format content
				$content .= '<' . esc_attr( $markup ) . ' class="'. esc_attr( $class ) .'" id="custom_post_widget-' . $id . '">';
				if ( $title === 'yes' ) {
					$content .= '<' . esc_attr( $title_tag ) . '>' . $post -> post_title . '</' . esc_attr( $title_tag ) . '>';
				}
				if ( $featured_image === 'yes' ) {
					$content .= get_the_post_thumbnail( $post -> ID, $featured_image_size );
				}
				if ( $suppress_content_filters === 'no' ) {
					$content .= apply_filters( 'the_content', $post -> post_content );
				} else {
					$content .= $post -> post_content;
				}
				$content .= '</' .  esc_attr( $markup ) . '>';
			}
		endforeach;

		$content .= $template_after_content;
	}

	return $content;
}
add_shortcode( 'ingeni_content_block', 'ingeni_content_blocks_shortcode' );

if (!shortcode_exists("content_block") ) {
	add_shortcode( 'content_block', 'ingeni_content_blocks_shortcode' );
}