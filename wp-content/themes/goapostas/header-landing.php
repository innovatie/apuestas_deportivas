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
	?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'goapostas' ); ?></a>

	<header id="masthead" class="site-header">
		<div id="goapostas-top-sticky-holder">
		    <div class="goapostas-sticky-bar fixed" id="header-outer">

					<div class="header-navigation">
						<div class="navigation-container">
							<a class="primary-logo dark" href="<?php echo get_site_url(); ?>">

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
					</div>

				</div>
					
			</div>
		</div>
	</header><!-- #masthead -->

	<?php do_action('gp_after_header_landing'); ?>
	
	<div id="content" class="site-content">
		<div class="wrap"><!-- #wrap -->
