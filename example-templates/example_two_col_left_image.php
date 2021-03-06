<?php 
//
// Two Column Left Image custom-post-widget template
//
//
// IMPORTANT - The template must be a class with the same name as the filename.
// filename = my_custom_template.php
// class = my_custom_template
//
// The custom template class MUST contain a public function called shortcode_template().
//
class example_two_col_left_image {

	public function shortcode_template( $this_post ) {
		try {
			$slug = $this_post->post_name;

			if ( has_post_thumbnail( $this_post->ID ) ) {
				$thumb_id = get_post_thumbnail_id($this_post->ID);
				$thumb_url = wp_get_attachment_image_src($thumb_id,'full', false);
				$style = 'background-image: url('. $thumb_url[0] .')';
			}

			$retHtml .= '<div class="grid-container full-height top-pad-30 bottom-pad-30 '.basename(__FILE__ ,'.php').'" id="'.$slug.'"><div class="grid-x grid-padding-x full-height align-middle">';

				$retHtml .= '<div class="cell small-12 medium-6 medium-order-2">';
						$retHtml .= apply_filters('the_content', $this_post->post_content);
				$retHtml .= '</div>';
				$retHtml .= '<div class="cell small-12 medium-6 medium-order-1">';
					$retHtml .= '<div class="bg_img" style="'.$style.'"></div>';
				$retHtml .= '</div>';
			$retHtml .= '</div></div>';

		} catch (Exception $ex) {
			$retHtml = '<p>' . basename(__FILE__) . ': '.$ex->getMessage().'</p>';
		}

		return $retHtml;
	}
}
?>