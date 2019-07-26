<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      AJdG Solutions Update and Support Library
 Version:	1.4
---- CHANGELOG ------------------------------------------------
1.4 - 1/july/2019
	* Now uses API 7

1.3.2 - 1/july/2019
	* Improved error checking
	* Improved backwards compatibility for older PHP versions

1.3.1 - 27/june/2019
	* Added error checking for update and info requests

1.3 - 29/may/2019
	* All new basic_check request
	* All new plugin_information request

1.2.4 - 21/feb/2019
	* Fixed slug not listed correctly on line 72

1.2.3 - 4/apr/2018
	* Dropped support for 101 licenses

1.2.2 - 28/feb/2016
	* changed unserialize() into maybe_unserialize() on line 49

1.2.1 - 5/6/2015
	* Added extra check if plugin exists in update array
-------------------------------------------------------------*/
function adrotate_licensed_update() {
	add_filter('site_transient_update_plugins', 'adrotate_update_check');
	add_filter('plugins_api', 'adrotate_get_plugin_information', 20, 3);
}

/*-------------------------------------------------------------
 Name:      adrotate_update_check
 Purpose:   Check for new version and grab basic details
-------------------------------------------------------------*/
function adrotate_update_check($transient) {
	if(empty($transient->checked)) {
		return $transient;	
	}

   	$license = get_option('adrotate_activate');

	if($license['status'] == 1) {
		$plugin_version = get_plugins();
		$plugin_version = $plugin_version['adrotate-pro/adrotate-pro.php']['Version'];
				
		$request_args = array(
			'slug' => "adrotate-pro", 
			'version' => $plugin_version, 
			'instance' => $license['instance'], 
			'platform' => get_option('siteurl')
		);
		$request = wp_remote_post('https://ajdg.solutions/api/updates/7/', adrotate_prepare_request('basic_check', $request_args));

		if(!is_wp_error($request) AND wp_remote_retrieve_response_code($request) === 200) {
			$request = json_decode($request['body'], 1);

			if(version_compare($plugin_version, $request['new_version'], '<') AND version_compare($request['requires_wp'], get_bloginfo('version'), '<')) {
				$res = new stdClass();
				$res->id = $request['plugin_url'];
				$res->slug = "adrotate-pro";
				$res->plugin = "adrotate-pro/adrotate-pro.php";

				$res->new_version = $request['new_version'];
				$res->tested = $request['tested'];
				$res->requires_php = $request['requires_php'];

				$res->url = $request['plugin_url'];
				$res->package = $request['download_url'];
				$res->upgrade_notice = "<strong>Update Summary:</strong> ".$request['upgrade_note'];

				$res->icons = array(
					'1x' => $request['icons']['low'],
		        	'2x' => $request['icons']['high']
				);
				$res->banners = array(
					'1x' => $request['banners']['low'],
		        	'2x' => $request['banners']['high']
				);

				$transient->response[$res->plugin] = $res;
				$transient->checked[$res->plugin] = $request['new_version'];
			}
		}
	}

	return $transient;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_plugin_information
 Purpose:   Grab info from API for popup screen in dashboard (Update Information)
-------------------------------------------------------------*/
function adrotate_get_plugin_information($false, $action, $args) {
	if($action !== 'plugin_information') {
		return false;
	}

	if(!isset($args->slug) OR $args->slug != "adrotate-pro") {
		return $false;
	}
	
	$plugin_version = get_plugins()['adrotate-pro/adrotate-pro.php']['Version']; 
	$license = get_option('adrotate_activate');
	
	$request_args = array(
		'slug' => "adrotate-pro", 
		'version' => $plugin_version, 
		'instance' => $license['instance'], 
		'email' => $license['email'], 
		'platform' => get_option('siteurl')
	);
	$request = wp_remote_post('https://ajdg.solutions/api/updates/7/', adrotate_prepare_request($action, $request_args));
 	
	if(!is_wp_error($request) AND wp_remote_retrieve_response_code($request) === 200) {
		$request = json_decode($request['body'], 1);

		$res = new stdClass();
		$res->name = $request['name'];
		$res->slug = "adrotate-pro";
		$res->last_updated = $request['release_date'];

		$res->version = $request['version'];
		$res->tested = $request['tested'];
		$res->requires = $request['requires_wp'];
		$res->requires_php = $request['requires_php'];

		$res->author = $request['author'];
		$res->donate_link = $request['donate_link'];

		$res->homepage = $request['plugin_url'];
		$res->download_link = $request['download_url'];
		$res->active_installs = $request['active_installs'];

		$res->sections = array(
			'description' => $request['sections']['description'],
			'changelog' => $request['sections']['changelog'],
		); 
		if(isset($request['sections']['debug'])) {
			$res->sections['debug'] = $request['sections']['debug'];
		}

		$res->banners = array(
			'low' => $request['banners']['low'],
        	'high' => $request['banners']['high']
		);
 	} else {
		$response_code = wp_remote_retrieve_response_code($request);
		$response_message = wp_remote_retrieve_response_message($request);
		$res = new WP_Error('plugins_api_failed', 'An Error occurred: [ERROR '.$response_code.'] '.$response_message.'. Try again in a few minutes or contact support if the error persists.');
	}

	return $res;
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_request
 Purpose:   Set Headers and prepare update request
-------------------------------------------------------------*/
function adrotate_prepare_request($action, $args) {
	return array(
		'body' => array(
			'action' => $action, 
			'request' => serialize($args),
		),
		'user-agent' => 'AdRotate Pro/' . $args['version'] . '; ' . $args['platform'],
		'sslverify' => false,
		'content-type' => 'application/x-www-form-urlencoded',
	);	
}
?>