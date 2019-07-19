<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      adrotate_front_end

 Purpose:   Front-end advertiser dashboard
 Receive:   $atts, $content
 Return:    Mixed string
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_front_end($atts, $content = null) {
	global $wpdb, $current_user, $adrotate_config;

	$output = '';
	ob_start();

	if(is_user_logged_in()) {
		if(current_user_can('adrotate_advertiser')) {
			if($adrotate_config['enable_advertisers'] == 'Y') {

				$pagename = 'full';
				if(!empty($atts['page'])) {
					$pagename = trim(esc_attr(strtolower($atts['page']))); // full|summary|active|disabled|queued
				}

				if($pagename != '' && locate_template('adrotate-' . $pagename . '.php') != '') {
					// Load the theme template
					get_template_part('adrotate', $pagename);
				} else {
					// Load scripts, Requires jQuery
					wp_enqueue_script('raphael', plugins_url('/library/raphael-min.js', __FILE__));
					wp_enqueue_script('elycharts', plugins_url('/library/elycharts.min.js', __FILE__), array('raphael'));
					
					// Load styles
					wp_enqueue_style('adrotate-front-end', plugins_url('templates/adrotate-styles.css', __FILE__));

					// Default template
					if($pagename == 'full' OR $pagename == 'summary') adrotate_get_template_part('adrotate', 'summary');
					if($pagename == 'full' OR $pagename == 'active') adrotate_get_template_part('adrotate', 'active');
					if(adrotate_can_edit()) {
						if($pagename == 'full' OR $pagename == 'queued') adrotate_get_template_part('adrotate', 'queued');
					}
					if($pagename == 'full' OR $pagename == 'disabled') adrotate_get_template_part('adrotate', 'disabled');
				}
			} else {
				echo 'Advertisers are not enabled.';
			}
		} else {
			echo 'You do not have sufficient permissions to use this page.';
		}
	} else {
		echo 'You need to log in first.';
	}

	return ob_get_clean();
}

/*-------------------------------------------------------------
 Name:      adrotate_get_template_part

 Purpose:   Grab templates for front-end
 Receive:   $slug, $name
 Return:    -None-
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_get_template_part($slug, $name = '') {
	$path = dirname(__FILE__).'/templates/';

	// Get default slug-name.php
	if($name && file_exists($path.$slug.'-'.$name.'.php')) {
		load_template($path.$slug.'-'.$name.'.php', false);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_load_adverts

 Purpose:   Return a array of adverts to use on advertiser dashboards
 Receive:   $user_id
 Return:    array
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_load_adverts($user_id) {
	global $wpdb;

	$now 			= adrotate_now();
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;

	$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = 0 AND `user` = %d ORDER BY `ad` ASC;", $user_id));
	
	if($ads) {
		$adverts = array('active' => array(), 'disabled' => array(), 'queued' => array());
		foreach($ads as $ad) {
			$banner = $wpdb->get_row("SELECT `id`, `title`, `type`, `tracker` FROM `".$wpdb->prefix."adrotate` WHERE (`type` = 'active' OR `type` = '2days' OR `type` = '7days' OR `type` = 'disabled' OR `type` = 'error' OR `type` = 'a_error' OR `type` = 'expired' OR `type` = 'queue' OR `type` = 'reject') AND `id` = '".$ad->ad."';");
	
			// Skip if no ad
			if(!$banner) continue;
			
			$starttime = $stoptime = 0;
			$starttime = $wpdb->get_var("SELECT `starttime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$banner->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
			$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$banner->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
	
			$type = $banner->type;
			if($type == 'active' AND $stoptime <= $in7days) $type = '7days';
			if($type == 'active' AND $stoptime <= $in2days) $type = '2days';
			if($type == 'active' AND $stoptime <= $now) $type = 'expired'; 
	
			if($type == 'active' OR $type == '2days' OR $type == '7days' OR $type == 'expired') {
				$adverts['active'][$banner->id] = array(
					'id' => $banner->id,
					'title' => stripslashes(html_entity_decode($banner->title)),
					'type' => $type,
					'tracker' => $banner->tracker,
					'firstactive' => $starttime,
					'lastactive' => $stoptime
				);
			}
	
			if($type == 'disabled') {
				$adverts['disabled'][$banner->id] = array(
					'id' => $banner->id,
					'title' => stripslashes(html_entity_decode($banner->title)),
					'type' => $type
				);
			}
	
			if($type == 'queue' OR $type == 'reject' OR $type == 'error' OR $type == 'a_error') {
				$adverts['queued'][$banner->id] = array(
					'id' => $banner->id,
					'title' => stripslashes(html_entity_decode($banner->title)),
					'type' => $type
				);
			}
		}

		return $adverts;
	}
}
?>