<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package GoApostas
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php
	$favicon = get_field('favicon', 'option') ? get_field('favicon', 'option') : '';
	if ($favicon) {
		?>
		<link rel="icon" href="<?php echo get_field('favicon', 'option'); ?>" type="image/x-icon" />
		<?php
	}
	if (is_singular('review')) {
		$rating = get_field('rating_stars') ? floatval(get_field('rating_stars')) : 0;
		
		$rating = floatval($rating) % 100;
		
		?>
		<script type=application/ld+json>
		{“@context”:“http:\/\/schema.org\/“,”@type”:“Product”,“name”:“<?php echo get_the_title(); ?>“,”Review”:{“@type”:“Review”,“name”:“<?php echo get_the_title(); ?>“,”author”:{“@type”:“Person”,“name”:“<?php echo get_the_author(); ?>”},“datePublished”:“<?php echo get_the_date('Y-m-d'); ?>",“reviewRating”:{“@type”:“Rating”,“ratingValue”:“<?php echo $rating; ?>”}}}
		</script>
		<?php
	}
	?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'goapostas' ); ?></a>
	<?php
	$style_header = get_field('style_header') ? get_field('style_header') : '';
	$style_h = '';
	if(!$style_header || $style_header == 'Type 1'){
		$style_h = '';
	}
	if($style_header == 'Type 2'){
		$style_h = 'top-logo';
	}
	if($style_header == 'Type 3'){
		$style_h = 'center-logo';
	}
	if($style_header == 'Type 4'){
		$style_h = 'center-logo-black';
	}
	?>
	<header id="masthead" class="site-header <?php echo $style_h; ?>">
		<div id="goapostas-top-sticky-holder">
		    <div class="goapostas-sticky-bar fixed" id="header-outer">
		        <button id="goapostas-menu-toggle" aria-label="toggle show/hide primary menu" aria-expanded="false">
		            <span class="menu"><?php _e('Menu', 'goapostas'); ?></span><i class="icon-burger-menu"></i>
		            <span class="close"><?php _e('Close', 'goapostas'); ?></span><i class="icon-close"></i>
		        </button>
		        <div class="header-navigation">
		        	<div class="navigation-container">
		        		<?php
		        		if(!$style_header || $style_header == 'Type 1' || $style_header == 'Type 2'){
		        		?>
							<a class="primary-logo as dark" href="<?php echo get_site_url(); ?>">

								<?php $logo = goaposta_header_logo(); ?>
								<!-- Light logo -->
								<img class="light"
									src="<?php echo esc_url( $logo->light['url'] ); ?>"
									alt="<?php echo esc_attr( $logo->light['alt'] ); ?>"
									title="<?php echo esc_attr( $logo->light['title'] ); ?>">

								<!-- Dark logo -->
								<img class="dark"
									src="<?php echo esc_url( $logo->dark['url'] ); ?>"
									alt="<?php echo esc_attr( $logo->dark['alt'] ); ?>"
									title="<?php echo esc_attr( $logo->dark['title'] ); ?>">

								<!-- Light mobile logo -->
								<img class="light-mobile"
									src="<?php echo esc_url( $logo->light_mobile['url'] ); ?>"
									alt="<?php echo esc_attr( $logo->light_mobile['alt'] ); ?>"
									title="<?php echo esc_attr( $logo->light_mobile['title'] ); ?>">

								<!-- Dark mobile logo -->
								<img class="dark-mobile"
									src="<?php echo esc_url( $logo->dark_mobile['url'] ); ?>"
									alt="<?php echo esc_attr( $logo->dark_mobile['alt'] ); ?>"
									title="<?php echo esc_attr( $logo->dark_mobile['title'] ); ?>">
							</a>
							<?php
							if ( class_exists( 'Goapostas\Nav_Menu_Walker' ) ) {
								$walker = new Goapostas\Nav_Menu_Walker;
							} else {
								$walker = '';
							}
							wp_nav_menu( [
								'walker'          => $walker,
								'menu'            => 'main-menu',
								'theme_location'  => 'primary',
								'menu_id'         => 'primary-menu',
								'menu_class'      => 'sf-menu',
								'container'       => 'nav',
								'container_id'    => '',
								'site-navigation',
								'container_class' => 'main-navigation'
							] );
							?>
						<?php
						}if($style_header == 'Type 3' || $style_header == 'Type 4'){
							if ( class_exists( 'Goapostas\Nav_Menu_Walker' ) ) {
								$walker = new Goapostas\Nav_Menu_Walker;
							} else {
								$walker = '';
							}
							wp_nav_menu( [
								'walker'          => $walker,
								'menu'            => 'menu-l',
								'theme_location'  => 'menu-l',
								'menu_id'         => 'menu-l',
								'menu_class'      => 'sf-menu',
								'container'       => 'nav',
								'container_id'    => '',
								'site-navigation',
								'container_class' => 'main-navigation'
							] );
							?>
							<a class="primary-logo " href="<?php echo get_site_url(); ?>">

								<?php $logo = goaposta_header_logo(); ?>
								<!-- Light logo -->
								<img class="light"
									src="<?php echo esc_url( $logo->light['url'] ); ?>"
									alt="<?php echo esc_attr( $logo->light['alt'] ); ?>"
									title="<?php echo esc_attr( $logo->light['title'] ); ?>">

								<!-- Dark logo -->
								<img class="dark"
									src="<?php echo esc_url( $logo->dark['url'] ); ?>"
									alt="<?php echo esc_attr( $logo->dark['alt'] ); ?>"
									title="<?php echo esc_attr( $logo->dark['title'] ); ?>">

								<!-- Light mobile logo -->
								<img class="light-mobile"
									src="<?php echo esc_url( $logo->light_mobile['url'] ); ?>"
									alt="<?php echo esc_attr( $logo->light_mobile['alt'] ); ?>"
									title="<?php echo esc_attr( $logo->light_mobile['title'] ); ?>">

								<!-- Dark mobile logo -->
								<img class="dark-mobile"
									src="<?php echo esc_url( $logo->dark_mobile['url'] ); ?>"
									alt="<?php echo esc_attr( $logo->dark_mobile['alt'] ); ?>"
									title="<?php echo esc_attr( $logo->dark_mobile['title'] ); ?>">
							</a>
							<?php
							wp_nav_menu( [
								'walker'          => $walker,
								'menu'            => 'menu-r',
								'theme_location'  => 'menu-r',
								'menu_id'         => 'menu-r',
								'menu_class'      => 'sf-menu',
								'container'       => 'nav',
								'container_id'    => '',
								'site-navigation',
								'container_class' => 'main-navigation'
							] );
						}
						?>
						</div>
					</div>

				</div>

			</div>
		</div>
	</header><!-- #masthead -->

	<?php do_action('gp_after_header'); ?>

	<div id="content" class="site-content">
		<div class="wrap"><!-- #wrap -->
