<?php

vc_map( array(
    "name" => __("Top Betting Block", 'goapostas'),
    "base" => "top-betting-block",
    "class"=> 'wpb_vc_single_image',
    "category" => __('Content'),
    "params" => array(
        array(
            "type" => "checkbox",
            "holder" => "div",
            "class" => "",
            "heading" => __("Show Aside Image", 'goapostas'),
            "param_name" => "check_aside",
            "value" => '',
            'description' => __('check to show an image on the right side', 'goapostas')
        ),
        array(
            "type" => "attach_image",
            "holder" => "img",
            "class" => "attachment-thumbnail",
            "heading" => __("Aside AD Image", 'goapostas'),
            "param_name" => "aside_image",
            "value" => '',
            "description" => ''
        ),
    )
) );

add_shortcode('top-betting-block', 'goapostas_betting_block');
function goapostas_betting_block($atts) {
    $atts = shortcode_atts( array(
        'check_aside' => false,
        'aside_image' => ''
    ), $atts );

    $out = '';

    if( true ):

    $image = $atts['aside_image']? wp_get_attachment_image_src( $atts['aside_image'], 'medium' ) : '';
    $extra_class = $atts['aside_image']? 'block-lg' : '';
    $cols = $atts['check_aside']? 5 : '';

    ob_start();
    ?>
    <div class="betting-houses-block <?php echo $extra_class; ?>">
        <div class="row-grid">
            <?php 
            $title_review = 'iBetting90';
            $evaluation_review = '9.8';

            $args = [
                'post_type' => 'review',
                'posts_per_page' => 4
            ];
            $query = new WP_Query( $args );
            if ( $query->have_posts() ):

                while ( $query->have_posts() ): $query->the_post();

                    $rating = get_field('rating_stars');
                    $rating = $rating? $rating : 0;

                    $evaluation = round( $rating / 10.0, 1);
            ?>
            <div class="col col-<?php echo $cols; ?>">
                <div class="review-block">
                    <div class="image-container">
                        <img src="<?php echo get_field('review_logo'); ?>"/>
                    </div>
                    <h5><?php echo get_the_title(); ?></h5>
                    <?php echo do_shortcode('[stars-rating-block stars_rating="' .$rating. '" stars_eval="' .$evaluation. '" ]'); ?>
                    <div>
                        <a href="<?php echo get_field('external_link'); ?>" target="_blank" class="btn-solid"><?php echo __('Crie sua conta', 'goapostas'); ?></a>
                    </div>
                </div>
            </div>
            <?php endwhile; 
                wp_reset_postdata();
            endif;
            ?>

            <?php if( $atts['check_aside'] && $image ): ?>
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
                        <label><?php echo __('Avaliação', 'goapostas'); ?></label> <strong> &nbsp; </strong>
                    </div>
                    <div>
                        <a href="#" class="btn-solid"><?php echo __('Crie sua conta', 'goapostas'); ?></a>
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