<?php
/***************************************
*	Custom Widgets
***************************************/
add_action( 'vc_before_init', 'goapostas_vc_before_init_actions' );
function goapostas_vc_before_init_actions() {
    // Require new custom Widget
    require_once( get_template_directory().'/inc/widgets/review-block.php' );
    require_once( get_template_directory().'/inc/widgets/review-block-dark.php' );
    require_once( get_template_directory().'/inc/widgets/featured-block.php' );
    require_once( get_template_directory().'/inc/widgets/cta-links.php' );
    require_once( get_template_directory().'/inc/widgets/stars-rating.php' );

    require_once( get_template_directory().'/inc/widgets/review-loop.php' );
    require_once( get_template_directory().'/inc/widgets/post-loop-single.php' );
    require_once( get_template_directory().'/inc/widgets/bonus-loop.php' );

    require_once( get_template_directory().'/inc/widgets/palpites-news.php' );
    require_once( get_template_directory().'/inc/widgets/palpite-single.php' );
    require_once( get_template_directory().'/inc/widgets/palpites-loop.php' );

    require_once( get_template_directory().'/inc/widgets/new-single.php' );
    require_once( get_template_directory().'/inc/widgets/news-loop.php' );
    require_once( get_template_directory().'/inc/widgets/news-bars.php' );
    require_once( get_template_directory().'/inc/widgets/news-two-bars.php' );
    require_once( get_template_directory().'/inc/widgets/general-last-news.php' );

    require_once( get_template_directory().'/inc/widgets/guias-loop-filter.php' );

    require_once( get_template_directory().'/inc/widgets/news-loop-filter.php' );

    require_once( get_template_directory().'/inc/widgets/sports-loop.php' );

    require_once( get_template_directory().'/inc/widgets/review-analysis-block.php' );
    require_once( get_template_directory().'/inc/widgets/cta-image-block.php' );

    require_once( get_template_directory().'/inc/widgets/gadget-icon.php' );
    
    require_once( get_template_directory().'/inc/widgets/quote-block.php' );
    require_once( get_template_directory().'/inc/widgets/cta-two-images-block.php' );
    require_once( get_template_directory().'/inc/widgets/bonus-box.php' );

    require_once( get_template_directory().'/inc/widgets/navigation-apostas.php' );
    require_once( get_template_directory().'/inc/widgets/quick-nav.php' );
    require_once( get_template_directory().'/inc/widgets/gadgets-devices.php' );
    require_once( get_template_directory().'/inc/widgets/gadgets-payments.php' );
    require_once( get_template_directory().'/inc/widgets/site-langs-block.php' );
    require_once( get_template_directory().'/inc/widgets/corporate-info.php' );
    require_once( get_template_directory().'/inc/widgets/ads-block.php' );
    require_once( get_template_directory().'/inc/widgets/ads-page.php' );
    require_once( get_template_directory().'/inc/widgets/betting-block.php' );

    require_once( get_template_directory().'/inc/widgets/chapter-book.php' );
}