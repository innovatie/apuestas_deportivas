<?php
/**
 * GoApostas functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package GoApostas
 */

if ( ! function_exists( 'goapostas_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function goapostas_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on GoApostas, use a find and replace
		 * to change 'goapostas' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'goapostas', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'goapostas' ),
			'menu-l' => esc_html__( 'Primary Left', 'goapostas' ),
			'menu-r' => esc_html__( 'Primary Right', 'goapostas' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
	}
endif;
add_action( 'after_setup_theme', 'goapostas_setup' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function goapostas_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'goapostas' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'goapostas' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'goapostas_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function goapostas_scripts() {

	$theme = wp_get_theme();

    wp_enqueue_style( 'goapostas-style', get_stylesheet_directory_uri() . '/style.css', '', $theme->get( 'Version' ) );
    wp_enqueue_script( 'goapostas-slick-js', get_template_directory_uri() . '/js/slick.min.js', array( 'jquery' ), $theme->get( 'Version' ), true);
	wp_enqueue_script( 'goapostas-js', get_template_directory_uri() . '/js/theme.js', array( 'jquery' ), '1.1.0', true);

	wp_localize_script('my-ajax-handle', 'the_ajax_script', array('ajaxurl' => admin_url('admin-ajax.php')));

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if( is_singular() ) {
		wp_enqueue_script( 'sharethis', '//platform-api.sharethis.com/js/sharethis.js#property=5cd1f5f53f59c700126bacd4&product=inline-share-buttons', [], $theme->get( 'Version' ) );
	}

	$script_data = array(
		'base_url'	=> site_url(),
		'ajax_url'	=> site_url('wp-admin/admin-ajax.php')
	);
	$extra_data = apply_filters('goapostas_script_extra_data', []);
	$script_data = array_merge( $script_data, $extra_data );
	wp_localize_script( 'goapostas-js', 'base', $script_data );
}
add_action( 'wp_enqueue_scripts', 'goapostas_scripts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

require get_template_directory() . '/inc/template-shortcodes.php';

require get_template_directory() . '/inc/classes/init.php';

/* Goapostas Widgets */
require get_template_directory() . '/inc/widgets/init.php';


/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

function goapostas_remove_vc_styles() {
    wp_dequeue_style( 'js_composer_front');
    wp_deregister_style( 'js_composer_front');
}
add_action('wp_enqueue_scripts', 'goapostas_remove_vc_styles', 9999);

function goapostas_remove_extra_vc() {

    if (function_exists('vc_remove_element')) {
        vc_remove_element('vc_icon');
        vc_remove_element('vc_btn');
        vc_remove_element('vc_separator');
        vc_remove_element('vc_text_separator');
        vc_remove_element('vc_message');
        vc_remove_element('vc_hoverbox');
        vc_remove_element('vc_facebook');
        vc_remove_element('vc_tweetmeme');
        vc_remove_element('vc_googleplus');
        vc_remove_element('vc_pinterest');
        vc_remove_element('vc_toggle');
        vc_remove_element('vc_accordion_tab');
        vc_remove_element('vc_images_carousel');
        vc_remove_element('vc_tabs');
        vc_remove_element('vc_tta_tabs');
        vc_remove_element('vc_tour');
        vc_remove_element('vc_tta_tour');
        vc_remove_element('vc_accordion');
        //vc_remove_element('vc_tta_accordion');
        vc_remove_element('vc_tta_pageable');
        vc_remove_element('vc_custom_heading');
        vc_remove_element('vc_cta');
        vc_remove_element('vc_widget_sidebar');
        vc_remove_element('vc_posts_slider');
        vc_remove_element('vc_video');
        vc_remove_element('vc_gmaps');
        vc_remove_element('vc_raw_js');
        vc_remove_element('vc_flickr');
        vc_remove_element('vc_zigzag');
        vc_remove_element('vc_gallery');
        // vc_remove_element('vc_basic_grid');
        vc_remove_element('vc_pie');
        vc_remove_element('vc_round_chart');
        vc_remove_element('vc_line_chart');
        vc_remove_element('vc_progress_bar');
        vc_remove_element('vc_posts_grid');
        vc_remove_element('vc_media_grid');
        vc_remove_element('vc_masonry_grid');
        vc_remove_element('vc_masonry_media_grid');
    }
}

add_action( 'vc_after_init', 'goapostas_remove_extra_vc' );

/**
 * Disable the emoji's
 */
function goapostas_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'goapostas_disable_emojis_tinymce' );
    add_filter( 'wp_resource_hints', 'goapostas_disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'goapostas_disable_emojis' );

// here
// function startapp_child_modify_mapping() {
//     vc_add_param( 'vc_row', array(
//         'param_name'  => 'mobile_cl',
//         'type'        => 'textfield',
//         'weight'      => - 3,
//         'heading'     => esc_html__( 'Mobile Class', 'goapostas' ),
//         'description' => esc_html__( 'class only for mobile', 'goapostas' ),
// 	) );
// }
// add_action( 'vc_after_init', 'startapp_child_modify_mapping', 15 );


// Element Mapping

// Endf
/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param array $plugins
 * @return array Difference betwen the two arrays
 */
function goapostas_disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function goapostas_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' == $relation_type ) {
        /** This filter is documented in wp-includes/formatting.php */
        $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

        $urls = array_diff( $urls, array( $emoji_svg_url ) );
    }

    return $urls;
}


add_filter('acf/settings/save_json', function() {
	return get_stylesheet_directory() . '/acf-json';
});

add_filter('acf/settings/load_json', function($paths) {
	$paths = array(get_template_directory() . '/acf-json');

	if(is_child_theme())
	{
		$paths[] = get_stylesheet_directory() . '/acf-json';
	}

	return $paths;
});

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'GoApostas Settings',
		'menu_title'	=> 'GoApostas Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	
}

function goaposta_header_logo() {
    $logo               = new StdClass;
    $logo->light        = get_field( 'goapostas_logo_light','options' );
    $logo->dark         = get_field( 'goapostas_logo_dark','options' );
    $logo->light_mobile = get_field( 'goapostas_logo_light_mobile','options' );
    $logo->dark_mobile  = get_field( 'goapostas_logo_dark_mobile','options' );
    return $logo;
}

function custom_logo() {
    if($logo = get_field('logo','options'))
        echo '<a href="'.get_site_url().'" alt="goaposta"><img src="'.$logo.'" title="goaposta" /></a>';
    else 
        echo '<a href="'.get_site_url().'" alt="goaposta"><img src="'.get_stylesheet_directory_uri().'/assets/images/logo.svg" title="goaposta"/></a>';
}


add_action( 'init', 'goapostas_init' );
function goapostas_init() {
	remove_image_size( 'thumbnail' );
	add_image_size( 'thumbnail', 280, 180, false );
	
	remove_image_size( 'medium' );
	add_image_size( 'medium', 480, 360, false );

	remove_image_size( 'medium_large' );
	add_image_size( 'medium_large', 640, 740, false );
}

add_filter( 'widget_text', 'do_shortcode' );

/* shortcode shortcode */
add_shortcode('cats_loop', 'goapostas_cat_loop');
function goapostas_cat_loop($atts,$content){
	$out = '';
	$categories = get_categories(array(
	    'orderby' => 'term_id',
	    'order'   => 'DESC'
	));
	/*
	esc_url( get_category_link( $category->term_id ) ),
        esc_attr( sprintf( __( 'View all posts in %s', 'textdomain' ), $category->name ) ),
        esc_html( $category->name )
	*/
	foreach ($categories as $category) {
		$out .= '<a href="'.get_category_link($category->term_id).'">'.$category->name.'</a>';
	}
	return $out;
}

/**
 * Show and Add predefined font-sizes for wysiwyg editor
 */
add_filter( 'mce_buttons_2', 'goa_postas_mce_buttons_2' );
function goa_postas_mce_buttons_2( $buttons ) {
	array_unshift( $buttons, 'fontsizeselect' ); 
	return $buttons;
}

add_filter( 'tiny_mce_before_init', 'goapostas_mce_font_size' );
function goapostas_mce_font_size( $initArray ){
	$initArray['fontsize_formats'] = "12px 14px 16px 18px 20px 22px 24px";
	return $initArray;
}


add_action('gp_after_header', 'goapostas_after_header');
function goapostas_after_header() {
	$image = get_field('hero_image');
	if( !$image ) {
		$image = get_the_post_thumbnail_url();
		$image = $image? $image : get_stylesheet_directory_uri() . '/assets/images/news-hero-bg.jpg';
	}
	if( is_singular('news') || is_singular('palpite') ) {
		?>
		<section class="hero hero-shadow" style="background-image: url(<?php echo $image; ?>)">
			<?php
			if (has_post_thumbnail()) {
				$thumb_id = get_post_thumbnail_id();
				$thumb_url = wp_get_attachment_image_src($thumb_id,'full', true);
				?>
				<div class="image-thm" style="background:url(<?php echo $thumb_url[0]; ?>);"></div>
				<div class="img-th"><img src="<?php echo $thumb_url[0]; ?>" /></div>
				<?php
			}
			?>
			<div class="wrap">
				<h1><?php echo get_the_title(); ?></h1>
			</div>
		</section>
		<?php
	}
	else if( is_page() ) {
		$type = get_field('hero_type');
		$title = get_field('hero_title');
		$content = get_field('hero_content');
		if( get_field('hero_image') ) {
			$image = get_field('hero_image');
		}
		?>
		<section class="hero-deg hero-type-<?php echo $type; ?>" style="background-image: url(<?php echo $image; ?>)">
			<a href="#" class="scroll-to-sc">
				<span>Rolar para baixo</span>
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icon-scrollto.svg" alt="scroll below">
			</a>
			<div class="wrap">
				<?php if( $title ): ?>
				<h1><?php echo $title; ?></h1>
				<?php endif; ?>

				<?php if( $content ): ?>
				<div class="hero-parph">
					<h1><?php echo $content; ?></h1>
				</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
	else if( is_singular('review') || is_singular('bonus') ) {
		$image = ( strpos($image, 'news-hero-bg') === false )? $image : get_stylesheet_directory_uri() . '/assets/images/review-hero-default.jpg';
		?>
		<section class="hero hero-shadow" style="background-image: url(<?php echo $image; ?>)">
			<div class="wrap"></div>
		</section>
		<?php
	}elseif($_GET['search']) {
		$s = $_GET['search'];
		$args = array('posts_per_page' => -1, 's' => $_GET['search'], 'paged' => $paged, 'post_type' => array('post', 'news', 'review'));
		$search_query = new WP_Query( $args );
		?>
		<section class="hero hero-shadow" style="background-image: url(<?php echo get_stylesheet_directory_uri().'/assets/images/bg-search.svg'; ?>)">
			<div class="wrap">
				<h1 style="margin-bottom:0;"><?php echo __('Resultados: “', 'goapostas'); ?><span><?php echo $s.__('”', 'goapostas'); ?></span></h1>
				<p style="text-align:center;"><?php echo __('Aproximadamente ', 'goapostas').$search_query->post_count.__(' resultados', 'goapostas'); ?></p>
			</div>
		</section>
		<?php
	}
}

add_filter( 'pre_get_posts', 'jetel_search' );
function jetel_search( $query ) {
if ( $query->is_search ) {
	$query->set( 'post_type', array( 'page', 'news', 'review' ) );
}
return $query;
}

/* After Header Landing */
add_action('gp_after_header_landing', 'goapostas_after_header_landing');
function goapostas_after_header_landing() {
	$image = get_field('hero_image');
	if( !$image ) {
		$image = get_the_post_thumbnail_url();
		$image = $image? $image : get_stylesheet_directory_uri() . '/assets/images/news-hero-bg.jpg';
	}
	$type = get_field('hero_type');
	$title = get_field('hero_title');
	$content = get_field('hero_description');
	if( get_field('hero_image') ) {
		$image = get_field('hero_image');
	}
	?>
	<section class="hero-deg hero-landing hero-type-<?php echo $type; ?>" style="background-image: url(<?php echo $image; ?>)">
		<div class="wrap">
			<div class="dsc-hero">
				<?php if( $title ): ?>
				<h1><?php echo $title; ?></h1>
				<?php endif; ?>

				<?php if( $content ): ?>
				<div class="hero-parph">
					<p><?php echo $content; ?></p>
				</div>
				<?php endif; ?>
				<div>
					<?php echo do_shortcode(get_field('hero_form_shortcode')); ?>
				</div>
			</div>
			<div class="hero-book">
				<img src="<?php echo get_field('hero_book_image'); ?>" />
			</div>
		</div>
	</section>
	<?php
}

/**
 * Page bottom content
 */
add_action('gp_after_content', 'goapostas_after_content');
function goapostas_after_content() {
	if ( is_singular('news') || is_singular('review') || is_singular('bonus') ) {
		get_template_part( 'template-parts/content','sharing-comments' );
		get_template_part( 'template-parts/content','bottom' );
		get_template_part( 'template-parts/content','bottom-adverts' );
	}
	else if( is_page() ) {
		if( get_field('show_bottom_content') ) {
			get_template_part( 'template-parts/content','bottom' );
			get_template_part( 'template-parts/content','bottom-adverts' );
		}
	}
}
add_filter('comment_form_default_fields', 'website_remove');
function website_remove($fields)
{
	if(isset($fields['url']))
	unset($fields['url']);
	return $fields;
}


/* Changing the Logo on dashboard Login Form */
add_action( 'login_enqueue_scripts', 'my_login_logo_one' );
function my_login_logo_one() {
	$logo = goaposta_header_logo();
	if($logo){
		?>
		<style type="text/css">
		body.login div#login h1 a {
		 	background-image: url(<?php echo esc_url( $logo->dark['url'] ); ?>);
			background-size: cover;
    		width: 130px;
		}
		</style>
		<?php
	}
}

