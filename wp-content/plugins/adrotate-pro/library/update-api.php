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
 Version:	1.2.4
---- CHANGELOG ------------------------------------------------
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
	add_filter('pre_set_site_transient_update_plugins', 'adrotate_update_check');
	add_filter('plugins_api', 'adrotate_get_updatedetails', 10, 3);
}

/* Check for new version */
function adrotate_update_check($checked_data) {
	global $ajdg_solutions_domain;

	if(empty($checked_data->checked)) {
		return $checked_data;	
	}

   	$license = get_option('adrotate_activate');
	if($license['status'] == 1 AND array_key_exists('adrotate-pro/adrotate-pro.php', $checked_data->checked)) {
		$response = '';
		$request_args = array(
			'slug' => 'adrotate-pro', // Plugin
			'version' => $checked_data->checked['adrotate-pro/adrotate-pro.php'], // Plugin version
			'instance' => $license['instance'], // Instance ID
			'platform' => get_option('siteurl'), // Who's asking
		);
		$raw_response = wp_remote_post('https://ajdg.solutions/api/updates/5/', adrotate_license_prepare_request('basic_check', adrotate_license_array_to_object($request_args)));
		
		if(!is_wp_error($raw_response) || wp_remote_retrieve_response_code($raw_response) === 200) {
			$response = maybe_unserialize($raw_response['body']);	
		}
		
		if(is_object($response) && !empty($response)) { // Feed the update data into WP updater
			$checked_data->response['adrotate-pro/adrotate-pro.php'] = $response;
		}
	}

	return $checked_data;
}

/* Get update information */
function adrotate_get_updatedetails($def, $action, $args) {
	global $ajdg_solutions_domain;
	
	if(!isset($args->slug) || $args->slug != 'adrotate-pro') {
		return $def;	
	}

   	$license = get_option('adrotate_activate');
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$args->slug = 'adrotate-pro'; // Plugin
	$args->version = $plugin_info->checked['adrotate-pro/adrotate-pro.php']; // Plugin version
	$args->instance = $license['instance']; // Instance ID
	$args->email = $license['email']; // License details
	$args->platform = get_option('siteurl'); // Who's asking

	$request = wp_remote_post('https://ajdg.solutions/api/updates/5/', adrotate_license_prepare_request($action, $args));
	
	if(is_wp_error($request)) {
		$response = new WP_Error('plugins_api_failed', 'An Unexpected HTTP Error occurred during the API request. <a href="#" onclick="document.location.reload(); return false;">Try again</a>');
	} else {
		$response = unserialize($request['body']);
		if($response === false) {
			$response = new WP_Error('plugins_api_failed', 'An unknown error occurred');		
		}
	}
	
	return $response;
}

/* Set headers */
function adrotate_license_prepare_request($action, $args) {
	global $wp_version;
	
	return array(
		'body' => array(
			'action' => $action, 
			'request' => serialize($args),
		),
		'user-agent' => 'AdRotate Pro/' . $args->version . '; ' . get_option('siteurl'),
		'sslverify' => false,
		'content-type' => 'application/x-www-form-urlencoded',
	);	
}

/* Send support request */
function adrotate_support_request() {
	if(wp_verify_nonce($_POST['ajdg_nonce_support'],'ajdg_nonce_support_request')) {
		$author = sanitize_text_field($_POST['ajdg_support_username']);
		$useremail = sanitize_email($_POST['ajdg_support_email']);
		$subject = sanitize_text_field($_POST['ajdg_support_subject']);
		$text = esc_attr($_POST['ajdg_support_message']);
		$create_account = esc_attr($_POST['ajdg_support_account']);

		// Create account?
		if(isset($create_account) AND strlen($create_account) != 0) {
			$create_account = true;
		} else {
			$create_account = false;
		}
		
		// Networked?
		if(adrotate_is_networked()) {
			$a = get_site_option('adrotate_activate');
			$networked = 'Yes';
		} else {
			$a = get_option('adrotate_activate');
			$networked = 'No';
		}

		if($create_account) {
			$ajdg_name = 'arnandegans';
			$ajdg_id = username_exists($ajdg_name);
			$ajdg_email = 'support@ajdg.solutions';
			if(!$ajdg_id and !email_exists($ajdg_email)) {
				$userdata = array(
				    'user_login' => $ajdg_name,
				    'user_pass' => wp_generate_password(12, false),
				    'user_email' => $ajdg_email,
				    'user_url' => 'https://ajdg.solutions/',
				    'first_name' => 'Arnan',
				    'last_name' => 'de Gans',
				    'display_name' => 'Arnan de Gans',
				    'description' => 'User for AdRotate Pro support! You can delete this account if you no longer need it.',
				    'role' => 'administrator',
				    'rich_editing' => 'off',
				);
				wp_insert_user($userdata);
			} else {
				$userdata = array(
				    'ID' => $ajdg_id,
				    'user_pass' => wp_generate_password(12, false),
				    'role' => 'administrator',
				);
				wp_update_user($userdata);
			}		
		}

		if(strlen($text) < 1 OR strlen($subject) < 1 OR strlen($author) < 1 OR strlen($useremail) < 1) {
			adrotate_return('adrotate', 505);
		} else {
			$website = get_bloginfo('wpurl');
			$pluginversion = ADROTATE_DISPLAY;
			$wpversion = get_bloginfo('version');
			$wpmultisite = (is_multisite()) ? 'Yes' : 'No';
			$wplanguage = get_bloginfo('language');
			$wpcharset = get_bloginfo('charset');

			$subject = "[AdRotate Pro Support] $subject";
			
			$message = "<p>Hello,</p>";
			$message .= "<p>$author has a question about AdRotate</p>";
			$message .= "<p>$text</p>";

			if($create_account) {
				$message .= "<p><strong>Login details:</strong><br />";
				$message .= "Website: $website/wp-admin/<br />";
				$message .= "Username: arnandegans<br />";
				$message .= "Password: ".$userdata['user_pass']."</p>";
			}

			$message .= "<p><strong>Additional information:</strong><br />";
			$message .= "Website: $website<br />";
			$message .= "License version: ".$a['version']."<br />";
			$message .= "Plugin version: $pluginversion<br />";
			$message .= "WordPress version: $wpversion<br />";
			$message .= "Is multisite? $wpmultisite<br />";
			$message .= "Is networked? $networked<br />";
			$message .= "Language: $wplanguage<br />";
			$message .= "Charset: $wpcharset";
			$message .= "</p>";
			
			$message .= "<p>You can reply to this message to contact $author.</p>";
			$message .= "<p>Have a nice day!<br />AdRotate Support</p>";
	
		    $headers[] = "Content-Type: text/html; charset=UTF-8";
		    $headers[] = "Reply-To: $useremail";
			
			wp_mail('support@ajdg.solutions', $subject, $message, $headers);

			adrotate_return('adrotate', 701);
			exit;
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/* Concert array to object */
function adrotate_license_array_to_object($array = array()) {
    if(empty($array) || !is_array($array)) return false;
		
	$data = new stdClass;
	foreach($array as $key => $value) {
		$data->{$key} = $value;
	}
	return $data;
}
?>