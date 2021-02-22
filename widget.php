<?php

// First create the widget for the admin panel
class ingeni_content_blocks_widget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'classname' => 'ingeni_content_blocks_widget', 'description' => __( 'Displays custom post content in a widget', 'ingeni-content-blocks-widget' ) );
		parent::__construct( 'custom_post_widget', __( 'Ingeni Content Block', 'ingeni-content-blocks-widget' ), $widget_ops );
	}

	function form( $instance ) {
		$custom_post_id = ''; // Initialize the variable
		if (isset($instance['custom_post_id'])) {
			$custom_post_id = esc_attr($instance['custom_post_id']);
		};
		$show_custom_post_title = isset( $instance['show_custom_post_title'] ) ? $instance['show_custom_post_title'] : true;
		$show_featured_image = isset( $instance['show_featured_image'] ) ? $instance['show_featured_image'] : true;
		$apply_content_filters = isset( $instance['apply_content_filters'] ) ? $instance['apply_content_filters'] : true;
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'custom_post_id' ); ?>"> <?php echo __( 'Ingeni Content Block to Display:', 'ingeni-content-blocks-widget' ) ?>
				<select class="widefat" id="<?php echo $this->get_field_id( 'custom_post_id' ); ?>" name="<?php echo $this->get_field_name( 'custom_post_id' ); ?>">
				<?php
					$args = array( 'post_type' => 'content_block', 'suppress_filters' => 0, 'numberposts' => -1, 'order' => 'ASC' );
					$content_block = get_posts( $args );
					if ($content_block) {
						foreach( $content_block as $content_block ) : setup_postdata( $content_block );
							echo '<option value="' . $content_block -> ID . '"';
							if( $custom_post_id == $content_block -> ID ) {
								echo ' selected';
								$widgetExtraTitle = $content_block -> post_title;
							};
							echo '>' . $content_block -> post_title . '</option>';
						endforeach;
					} else {
						echo '<option value="">' . __( 'No content blocks available', 'ingeni-content-blocks-widget' ) . '</option>';
					};
				?>
				</select>
			</label>
		</p>

		<input type="hidden" id="<?php echo $this -> get_field_id( 'title' ); ?>" name="<?php echo $this -> get_field_name( 'title' ); ?>" value="<?php if ( !empty( $widgetExtraTitle ) ) { echo $widgetExtraTitle; } ?>" />

		<p>
			<?php
				echo '<a href="post.php?post=' . $custom_post_id . '&action=edit">' . __( 'Edit Ingeni Content Block', 'ingeni-content-blocks-widget' ) . '</a>' ;
			?>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) isset( $instance['show_custom_post_title'] ), true ); ?> id="<?php echo $this->get_field_id( 'show_custom_post_title' ); ?>" name="<?php echo $this->get_field_name( 'show_custom_post_title' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_custom_post_title' ); ?>"><?php echo __( 'Show post title', 'ingeni-content-blocks-widget' ) ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) isset( $instance['show_featured_image'] ), true ); ?> id="<?php echo $this->get_field_id( 'show_featured_image' ); ?>" name="<?php echo $this->get_field_name( 'show_featured_image' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_featured_image' ); ?>"><?php echo __( 'Show featured image', 'ingeni-content-blocks-widget' ) ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) isset( $instance['apply_content_filters'] ), true ); ?> id="<?php echo $this->get_field_id( 'apply_content_filters' ); ?>" name="<?php echo $this->get_field_name( 'apply_content_filters' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'apply_content_filters' ); ?>"><?php echo __( 'Do not apply content filters', 'ingeni-content-blocks-widget' ) ?></label>
		</p> <?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['custom_post_id'] = strip_tags( $new_instance['custom_post_id'] );
		$instance['show_custom_post_title'] = $new_instance['show_custom_post_title'];
		$instance['show_featured_image'] = $new_instance['show_featured_image'];
		$instance['apply_content_filters'] = $new_instance['apply_content_filters'];
		return $instance;
	}

	// Display the content block content in the widget area
	function widget($args, $instance) {
		extract($args);
		$custom_post_id = ( $instance['custom_post_id'] != '' ) ? esc_attr($instance['custom_post_id']) : __( 'Find', 'ingeni-content-blocks-widget' );
		// Add support for WPML Plugin.
		if ( function_exists( 'icl_object_id' ) ){
			$custom_post_id = icl_object_id( $custom_post_id, 'content_block', true );
		}
		// Variables from the widget settings.
		$show_custom_post_title = isset( $instance['show_custom_post_title'] ) ? $instance['show_custom_post_title'] : false;
		$show_featured_image = isset($instance['show_featured_image']) ? $instance['show_featured_image'] : false;
		$apply_content_filters = isset($instance['apply_content_filters']) ? $instance['apply_content_filters'] : false;
		$content_post = get_post( $custom_post_id );
		$post_status = get_post_status( $custom_post_id );
		$content = $content_post->post_content;
		if ( $post_status == 'publish' ) {
			// Display custom widget frontend
			if ( $located = locate_template( 'ingeni-content-blocks-widget.php' ) ) {
				require $located;
				return;
			}
			if ( !$apply_content_filters ) { // Don't apply the content filter if checkbox selected
				$content = apply_filters( 'the_content', $content);
			}
			echo $before_widget;
			if ( $show_custom_post_title ) {
				echo $before_title . apply_filters( 'widget_title',$content_post->post_title) . $after_title; // This is the line that displays the title (only if show title is set)
			}
			if ( $show_featured_image ) {
				echo get_the_post_thumbnail( $content_post -> ID );
			}
			echo do_shortcode( $content ); // This is where the actual content of the custom post is being displayed
			echo $after_widget;
		}
	}
}



