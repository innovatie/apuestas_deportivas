<?php
vc_map( array(
    "name" => __("Apuestas Deportivas Top Casa De Apostas"),
    "base" => "top-c-blockss-v2",
    "class"=> "wpb_vc_single_image",
    "category" => __("Content"),
    "params" => array(
        array(
            "type" => "checkbox",
            "holder" => "div",
            "class" => "",
            "heading" => __("Show Aside Image"),
            "param_name" => "check_asides",
            "value" => '',
            "description" => __("check to show an image on the right side")
        ),
        array(
            "type" => "attach_image",
            "holder" => "img",
            "class" => "attachment-thumbnail",
            "heading" => __("Aside AD Image"),
            "param_name" => "aside_images",
            "value" => "",
            "description" => ""
        ),
    )
) );

add_shortcode('top-c-blockss-v2', 'betting_blockc');
function betting_blockc($atts,$content) {
    $atts = shortcode_atts( array(
        'check_asides' => false,
        'aside_images' => ''
    ), $atts );

    $out = '';

    if( true ):

    $image = $atts['aside_images'] ? wp_get_attachment_image_src( $atts['aside_images'], 'medium' ) : '';
    $extra_class = $atts['aside_images'] ? 'block-lg' : '';
    $cols = $atts['check_asides'] ? 5 : '';

    ob_start();
    ?>
    <div class="betting-houses-block <?php echo $extra_class; ?>">
        <div class="row-grid">
            <?php 
            $title_review = 'iBetting90';
            $evaluation_review = '9.8';

            $args = [
                'post_type' => 'casa-apuesta',
                'posts_per_page' => 4
            ];
            $query = new WP_Query( $args );
            if ( $query->have_posts() ):

                while ( $query->have_posts() ): $query->the_post();

                    $rating = get_field('cda_review');
                    $rating = $rating ? ($rating*2)*9.9 : 0;

                    $evaluation = round( $rating / 10.0, 1);
            ?>
            <div class="col col-<?php echo $cols; ?>">
                <div class="review-block">
                    <div class="image-container">
                        <?php
                        if(has_post_thumbnail()){
                            echo get_the_post_thumbnail(get_the_ID(), 'full');
                        }else{
                            ?>
                            <img  src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/bet365.png"/>
                            <?php
                        }
                        ?>
                    </div>
                    <h5><?php echo get_the_title(); ?></h5>
                    <?php echo do_shortcode('[stars-rating-block stars_rating="' .$rating. '" stars_eval="' .$evaluation. '" ]'); ?>
                    <div>
                        <a href="<?php echo (types_render_field( 'url-juego', array() )); ?>" target="_blank" class="btn-solid"><?php echo __('Crie sua conta', 'apuestas_deportivas'); ?></a>
                    </div>
                </div>
            </div>
            <?php endwhile; 
                wp_reset_postdata();
            endif;
            ?>

            <?php if( $atts['check_asides'] && $image ): ?>
            <div class="col aside-col col-<?php echo $cols; ?>">
                <img width="440" height="732" src="<?php echo $image[0]; ?>" class="vc_single_image-img attachment-full" alt="" >
                <div class="review-block">
                    <div class="image-container">
                        <img  src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/bet365.png"/>
                    </div>
                    <h5> &nbsp; </h5>
                    <div class="star-rating">
                        <span style="width: 66.66%;">
                            <i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i>
                        </span>
                        <i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>
                    </div>
                    <div class="evaluation-sec">
                        <label><?php echo __('Avaliação', 'apuestas_deportivas'); ?></label> <strong> &nbsp; </strong>
                    </div>
                    <div>
                        <a href="#" class="btn-solid"><?php echo __('Crie sua conta', 'apuestas_deportivas'); ?></a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <div class="navigation-apostas">
            <div class="arrows">
                <div id="prev-tcapostas" class="slick-arrow s-prev" style="display: inline-block;">‹</div>
                <div id="next-tcapostas" class="slick-arrow s-next" style="display: inline-block;">›</div>
            </div>
        </div>
        
    </div>
    <?php
    $out = ob_get_clean();
    endif;

    return $out;
}