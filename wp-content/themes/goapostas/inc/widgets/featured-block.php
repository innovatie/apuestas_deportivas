<?php

// feactured block
vc_map( array(
   "name" => __("Featured Block"),
   "base" => "featured-block",
   "class"=> 'wpb_vc_single_image',
   "category" => __('Content'),
   "params" => array(
      array(
         "type" => "attach_image",
         "holder" => "img",
         "class" => "attachment-thumbnail",
         "heading" => __("Image"),
         "param_name" => "feat_image",
         "value" => '',
         "description" => ''
      ),
      array(
         "type" => "textfield",
         "holder" => "div",
         "class" => "",
         "heading" => __("Title"),
         "param_name" => "feat_title",
         "value" => '',
         "description" => ''
      ),
      array(
         'type' => 'textarea',
         'holder' => 'div',
         'class' => 'text-class',
         'heading' => __( 'Text', 'text-domain' ),
         'param_name' => 'feat_content',
         'value' => '',
         'description' => '',
      ),
      array(
         'type' => 'vc_link',
         'holder' => '',
         'class' => '',
         'heading' => __( 'Link', 'text-domain' ),
         'param_name' => 'feat_link',
         'value' => '',
         'description' =>'',
      )
   )
 ) );

 add_shortcode( 'featured-block', 'goapostas_featured_block' );
 function goapostas_featured_block($atts) {
   $atts = shortcode_atts( array(
      'feat_image' => '',
      'feat_title' => '',
      'feat_content' => '',
      'feat_link' => '',
   ), $atts );
   // error_log( print_r($atts, true) );

   $output = '';

   if( $atts['feat_image'] || $atts['feat_title'] || $atts['feat_content'] ):
      ob_start();

      $image = wp_get_attachment_image_src( $atts['feat_image'], 'medium_large' );
      $link = vc_build_link( $atts['feat_link'] );
      ?>
      <div class="featured-box">
         <?php if( $link['url'] ): ?>
            <a href="<?php echo $link['url'] ?>"></a>
         <?php endif; ?>
         <picture>
            <?php if($image): ?>
            <img src="<?php echo $image[0]; ?>" alt="">
            <?php endif; ?>
         </picture>
         <div class="featured-box-content">
			   <h6><?php echo $atts['feat_title']; ?></h6>
            <p><?php echo $atts['feat_content']; ?></p>
		   </div>
      </div>
      <?php
      $output = ob_get_clean();
   endif;
   return $output;
 }