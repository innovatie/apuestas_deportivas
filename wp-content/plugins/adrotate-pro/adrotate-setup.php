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
 Name:      adrotate_activate
 Purpose:   Set up AdRotate on your current blog
 Since:		3.9.8
-------------------------------------------------------------*/
function adrotate_activate($network_wide) {
	if(is_multisite() && $network_wide) {
		global $wpdb;
 
		$current_blog = $wpdb->blogid;
 		$blog_ids = $wpdb->get_col("SELECT `blog_id` FROM $wpdb->blogs;");

		foreach($blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			adrotate_activate_setup();
		}
 
		switch_to_blog($current_blog);
		return;
	}
	adrotate_activate_setup();
	if(adrotate_is_networked()) add_site_option('adrotate_network_settings', array('primary' => 1, 'site_dashboard' => 'Y'));
}

/*-------------------------------------------------------------
 Name:      adrotate_activate_setup
 Purpose:   Set up AdRotate for first use with default settings and database tables
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_activate_setup() {
	global $wpdb, $userdata;

	if(version_compare(PHP_VERSION, '5.3.0', '<') == -1) { 
		deactivate_plugins(plugin_basename('adrotate-pro/adrotate.php'));
		wp_die('AdRotate 3.10.8 and newer requires PHP 5.3 or higher. Your server reports version '.PHP_VERSION.'. Contact your hosting provider about upgrading your server!<br /><a href="'. get_option('siteurl').'/wp-admin/plugins.php">Back to dashboard</a>.'); 
		return; 
	} else {
		if(!current_user_can('activate_plugins')) {
			deactivate_plugins(plugin_basename('adrotate-pro/adrotate.php'));
			wp_die('You do not have appropriate access to activate this plugin! Contact your administrator!<br /><a href="'. get_option('siteurl').'/wp-admin/plugins.php">Back to dashboard</a>.'); 
			return; 
		} else {
			// Set defaults for internal versions
			add_option('adrotate_db_version', array('current' => ADROTATE_DB_VERSION, 'previous' => ''));
			add_option('adrotate_version', array('current' => ADROTATE_VERSION, 'previous' => ''));

			// Set default settings and values
			add_option('adrotate_config', array());
			add_option('adrotate_notifications', array());
			add_option('adrotate_crawlers', array());
			add_option('adrotate_db_timer', date('U'));
			add_option('adrotate_debug', array('general' => false, 'publisher' => false, 'advertiser' => false, 'geo' => false, 'timers' => false, 'track' => false));
			add_option('adrotate_advert_status', array('error' => 0, 'expired' => 0, 'expiressoon' => 0, 'normal' => 0, 'total' => 0));
			add_option('adrotate_geo_required', 0);
			add_option('adrotate_geo_requests', 0);
			add_option('adrotate_geo_reset', time()); // Yes GMT+0
			add_option('adrotate_header_output', '');
			add_option('adrotate_dynamic_required', 0);
			add_option('adrotate_hide_banner', adrotate_now());
			add_option('adrotate_hide_review', adrotate_now());
	
			// Install new database
			adrotate_database_install();
			adrotate_dummy_data();
			adrotate_check_config();
			adrotate_check_schedules();

			// Set the capabilities for the administrator
			$role = get_role('administrator');		
			$role->add_cap("adrotate_advertiser");
			$role->add_cap("adrotate_global_report");
			$role->add_cap("adrotate_ad_manage");
			$role->add_cap("adrotate_ad_delete");
			$role->add_cap("adrotate_group_manage");
			$role->add_cap("adrotate_group_delete");
			$role->add_cap("adrotate_schedule_manage");
			$role->add_cap("adrotate_schedule_delete");
			$role->add_cap("adrotate_moderate");
			$role->add_cap("adrotate_moderate_approve");
			$role->add_cap("adrotate_advertiser_manage");
	
			// Switch additional roles off
			if(is_object(get_role('adrotate_advertiser'))) {
				adrotate_prepare_roles('remove');
			}

			// Attempt to make the some folders
			if(!is_dir(WP_CONTENT_DIR.'/banners')) mkdir(WP_CONTENT_DIR.'/banners', 0755);
			if(!is_dir(WP_CONTENT_DIR.'/reports')) mkdir(WP_CONTENT_DIR.'/reports', 0755);
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_activate_new_blog
 Purpose:   Set up AdRotate for first use with default settings and database tables
 Since:		4.7
-------------------------------------------------------------*/
function adrotate_activate_new_blog($blog_id) {
	if(is_multisite()) {
		global $wpdb;
 
		$current_blog = $wpdb->blogid;

		switch_to_blog($blog_id);
		adrotate_activate_setup();
		switch_to_blog($current_blog);
		return;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_deactivate
 Purpose:   Deactivate script
 Since:		2.0
-------------------------------------------------------------*/
function adrotate_deactivate($network_wide) {
    adrotate_network_propagate('adrotate_deactivate_setup', $network_wide);
}

/*-------------------------------------------------------------
 Name:      adrotate_deactivate_setup
 Purpose:   Deactivate script
 Since:		2.0
-------------------------------------------------------------*/
function adrotate_deactivate_setup() {
	// Clear out roles
	if(is_object(get_role('adrotate_advertiser'))) {
		adrotate_prepare_roles('remove');
	}

	// Clean up capabilities from ALL users
	adrotate_remove_capability("adrotate_advertiser");
	adrotate_remove_capability("adrotate_global_report");
	adrotate_remove_capability("adrotate_ad_manage");
	adrotate_remove_capability("adrotate_ad_delete");
	adrotate_remove_capability("adrotate_group_manage");
	adrotate_remove_capability("adrotate_group_delete");
	adrotate_remove_capability("adrotate_schedule_manage");
	adrotate_remove_capability("adrotate_schedule_delete");
	adrotate_remove_capability("adrotate_moderate");
	adrotate_remove_capability("adrotate_moderate_approve");
	adrotate_remove_capability("adrotate_advertiser_manage");

	// Clear out wp_cron
	wp_clear_scheduled_hook('adrotate_notification');
	wp_clear_scheduled_hook('adrotate_evaluate_ads');
	wp_clear_scheduled_hook('adrotate_empty_trash');
	wp_clear_scheduled_hook('adrotate_empty_trackerdata');
}

/*-------------------------------------------------------------
 Name:      adrotate_uninstall
 Purpose:   Initiate uninstallation
 Since:		2.4.2
-------------------------------------------------------------*/
function adrotate_uninstall($network_wide) {
    adrotate_network_propagate('adrotate_uninstall_setup', $network_wide);
}

/*-------------------------------------------------------------
 Name:      adrotate_uninstall_setup
 Purpose:   Delete the entire AdRotate database and remove the options on uninstall
 Since:		2.4.2
-------------------------------------------------------------*/
function adrotate_uninstall_setup() {
	global $wpdb, $wp_roles;

	// Clean up roles and scheduled tasks
	adrotate_deactivate_setup();

	// Drop MySQL Tables
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_groups`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_linkmeta`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_stats`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_stats_archive`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_schedule`");
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_transactions`"); // Obsolete
	$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_tracker`");

	// De-activate License
	adrotate_license_deactivate_uninstall();
	
	// Delete Options	
	delete_option('adrotate_activate');
	delete_option('adrotate_advert_status');
	delete_option('adrotate_config');
	delete_option('adrotate_crawlers');
	delete_option('adrotate_db_timer');
	delete_option('adrotate_db_version');
	delete_option('adrotate_debug');
	delete_option('adrotate_hide_license');
	delete_option('adrotate_hide_banner');
	delete_option('adrotate_hide_review');
	delete_option('adrotate_notifications');
	delete_option('adrotate_geo_required');
	delete_option('adrotate_geo_requests');
	delete_option('adrotate_geo_reset');
	delete_option('adrotate_header_output');
	delete_option('adrotate_dynamic_required');
	delete_option('adrotate_version');

	// Cleanup user meta
	$wpdb->query("DELETE FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = 'adrotate_is_advertiser';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = 'adrotate_notes';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = 'adrotate_permissions';");

	// Clear out userroles
	remove_role('adrotate_advertiser');
}

/*-------------------------------------------------------------
 Name:      adrotate_network_propagate
 Purpose:   Check how many sites use AdRotate
 Since:		3.9.9
-------------------------------------------------------------*/
function adrotate_network_propagate($pfunction, $network_wide) {
    global $wpdb;
 
    if(is_multisite() && $network_wide) {
        $current_blog = $wpdb->blogid;
        $blogids = $wpdb->get_col("SELECT `blog_id` FROM $wpdb->blogs;");

        foreach ($blogids as $blog_id) {
            switch_to_blog($blog_id);
            call_user_func($pfunction, $network_wide);
        }
        switch_to_blog($current_blog);
        return;
    } 
    call_user_func($pfunction, $network_wide);
}

/*-------------------------------------------------------------
 Name:      adrotate_check_schedules
 Purpose:   Set or reset maintenance schedules for AdRotate
 Since:		3.12.5
-------------------------------------------------------------*/
function adrotate_check_schedules() {
	$firstrun = adrotate_now();
	if(!wp_next_scheduled('adrotate_notification')) { // Ad notifications
		wp_schedule_event($firstrun + 900, 'daily', 'adrotate_notification');
	}

	if(!wp_next_scheduled('adrotate_evaluate_ads')) { // Periodically check ads
		wp_schedule_event($firstrun + 2700, 'twicedaily', 'adrotate_evaluate_ads');
	}

	if(!wp_next_scheduled('adrotate_empty_trash')) { // Empty the trash once a day
		wp_schedule_event($firstrun + 3600, 'daily', 'adrotate_empty_trash');
	}

	if(!wp_next_scheduled('adrotate_empty_trackerdata')) { // Periodically clean trackerdata
		wp_schedule_event($firstrun + 1800, 'twicedaily', 'adrotate_empty_trackerdata');
	}

	if(!wp_next_scheduled('adrotate_auto_delete')) { // Periodically clean adverts and schedules
		wp_schedule_event($firstrun + 4500, 'daily', 'adrotate_auto_delete');
	}
}	

/*-------------------------------------------------------------
 Name:      adrotate_check_config
 Purpose:   Default options for AdRotate
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_check_config() {
	
	$config = get_option('adrotate_config');
	$notifications = get_option('adrotate_notifications');
	$crawlers = get_option('adrotate_crawlers');
	$debug = get_option('adrotate_debug');

	if(!isset($config)) $config = array();
	if(!isset($notifications)) $notifications = array();
	if(!isset($crawlers)) $crawlers = array();
	if(!isset($debug)) $debug = array();
	
	if(!isset($config['advertiser'])) $config['advertiser'] = 'subscriber';
	if(!isset($config['global_report'])) $config['global_report'] = 'administrator';
	if(!isset($config['ad_manage'])) $config['ad_manage'] = 'administrator';
	if(!isset($config['ad_delete'])) $config['ad_delete'] = 'administrator';
	if(!isset($config['group_manage'])) $config['group_manage'] = 'administrator';
	if(!isset($config['group_delete'])) $config['group_delete'] = 'administrator';
	if(!isset($config['schedule_manage'])) $config['schedule_manage'] = 'administrator';
	if(!isset($config['schedule_delete'])) $config['schedule_delete'] = 'administrator';
	if(!isset($config['advertiser_manage'])) $config['advertiser_manage'] = 'administrator';
	if(!isset($config['moderate'])) $config['moderate'] = 'administrator';
	if(!isset($config['moderate_approve'])) $config['moderate_approve'] = 'administrator';
	if(!isset($config['enable_advertisers']) OR ($config['enable_advertisers'] != 'Y' AND $config['enable_advertisers'] != 'N')) $config['enable_advertisers'] = 'N';
	if(!isset($config['enable_editing']) OR ($config['enable_editing'] != 'Y' AND $config['enable_editing'] != 'N')) $config['enable_editing'] = 'N';
	if(!isset($config['stats']) OR ($config['stats'] < 0 AND $config['stats'] > 5)) $config['stats'] = 1;
	if(!isset($config['enable_loggedin_impressions']) OR ($config['enable_loggedin_impressions'] != 'Y' AND $config['enable_loggedin_impressions'] != 'N')) $config['enable_loggedin_impressions'] = 'Y';
	if(!isset($config['enable_loggedin_clicks']) OR ($config['enable_loggedin_clicks'] != 'Y' AND $config['enable_loggedin_clicks'] != 'N')) $config['enable_loggedin_clicks'] = 'Y';
	if(!isset($config['enable_geo'])) $config['enable_geo'] = 0;
	if(!isset($config['geo_email'])) $config['geo_email'] = '';
	if(!isset($config['geo_pass'])) $config['geo_pass'] = '';
	if(!isset($config['geo_cookie_life'])) $config['geo_cookie_life'] = 86400;
	if(!isset($config['enable_geo_advertisers'])) $config['enable_geo_advertisers'] = 0;
	if(!isset($config['enable_mobile_advertisers'])) $config['enable_mobile_advertisers'] = 0;
	if(!isset($config['adblock_disguise'])) $config['adblock_disguise'] = '';
	if(!isset($config['banner_folder'])) $config['banner_folder'] = "banners";
	if(!isset($config['impression_timer']) OR $config['impression_timer'] < 10 OR $config['impression_timer'] > 3600) $config['impression_timer'] = 60;
	if(!isset($config['click_timer']) OR $config['click_timer'] < 60 OR $config['click_timer'] > 86400) $config['click_timer'] = 86400;
	if(!isset($config['google_click_value']) OR $config['google_click_value'] < 0 OR $config['google_click_value'] > 100) $config['google_click_value'] = '1.00';
	if(!isset($config['google_impression_value']) OR $config['google_impression_value'] < 0 OR $config['google_impression_value'] > 1) $config['google_impression_value'] = '2.00';
	if(!isset($config['hide_schedules']) OR ($config['hide_schedules'] != 'Y' AND $config['hide_schedules'] != 'N')) $config['hide_schedules'] = 'N';
	if(!isset($config['widgetalign']) OR ($config['widgetalign'] != 'Y' AND $config['widgetalign'] != 'N')) $config['widgetalign'] = 'N';
	if(!isset($config['widgetpadding']) OR ($config['widgetpadding'] != 'Y' AND $config['widgetpadding'] != 'N')) $config['widgetpadding'] = 'N';
	if(!isset($config['w3caching']) OR ($config['w3caching'] != 'Y' AND $config['w3caching'] != 'N')) $config['w3caching'] = 'N';
	if(!isset($config['borlabscache']) OR ($config['borlabscache'] != 'Y' AND $config['borlabscache'] != 'N')) $config['borlabscache'] = 'N';
	if(!isset($config['affiliates']) OR ($config['affiliates'] != 'Y' AND $config['affiliates'] != 'N')) $config['affiliates'] = 'N';
	if(!isset($config['textwidget_shortcodes']) OR ($config['textwidget_shortcodes'] != 'Y' AND $config['textwidget_shortcodes'] != 'N')) $config['textwidget_shortcodes'] = 'N';
	if(!isset($config['live_preview']) OR ($config['live_preview'] != 'Y' AND $config['live_preview'] != 'N')) $config['live_preview'] = 'Y';
	if(!isset($config['mobile_dynamic_mode']) OR ($config['mobile_dynamic_mode'] != 'Y' AND $config['mobile_dynamic_mode'] != 'N')) $config['mobile_dynamic_mode'] = 'Y';
	if(!isset($config['jquery']) OR ($config['jquery'] != 'Y' AND $config['jquery'] != 'N')) $config['jquery'] = 'N';
	if(!isset($config['jsfooter']) OR ($config['jsfooter'] != 'Y' AND $config['jsfooter'] != 'N')) $config['jsfooter'] = 'Y';
	update_option('adrotate_config', $config);

	if(!isset($notifications['notification_dash']) OR ($notifications['notification_dash'] != 'Y' AND $notifications['notification_dash'] != 'N')) $notifications['notification_dash'] = 'Y';
	if(!isset($notifications['notification_email']) OR ($notifications['notification_email'] != 'Y' AND $notifications['notification_email'] != 'N')) $notifications['notification_email'] = 'N';

	if(!isset($notifications['notification_dash_expired']) OR ($notifications['notification_dash_expired'] != 'Y' AND $notifications['notification_dash_expired'] != 'N')) $notifications['notification_dash_expired'] = 'Y';
	if(!isset($notifications['notification_dash_soon']) OR ($notifications['notification_dash_soon'] != 'Y' AND $notifications['notification_dash_soon'] != 'N')) $notifications['notification_dash_soon'] = 'Y';

	if(!isset($notifications['notification_mail_geo']) OR ($notifications['notification_mail_geo'] != 'Y' AND $notifications['notification_mail_geo'] != 'N')) $notifications['notification_mail_geo'] = 'N';
	if(!isset($notifications['notification_mail_status']) OR ($notifications['notification_mail_status'] != 'Y' AND $notifications['notification_mail_status'] != 'N')) $notifications['notification_mail_status'] = 'Y';
	if(!isset($notifications['notification_mail_queue']) OR ($notifications['notification_mail_queue'] != 'Y' AND $notifications['notification_mail_queue'] != 'N')) $notifications['notification_mail_queue'] = 'N';
	if(!isset($notifications['notification_mail_approved']) OR ($notifications['notification_mail_approved'] != 'Y' AND $notifications['notification_mail_approved'] != 'N')) $notifications['notification_mail_approved'] = 'N';
	if(!isset($notifications['notification_mail_rejected']) OR ($notifications['notification_mail_rejected'] != 'Y' AND $notifications['notification_mail_rejected'] != 'N')) $notifications['notification_mail_rejected'] = 'N';
	if(!isset($notifications['notification_email_publisher'])) $notifications['notification_email_publisher'] = array(get_option('admin_email'));
	if(!isset($notifications['notification_email_advertiser'])) $notifications['notification_email_advertiser'] = array(get_option('admin_email'));
	update_option('adrotate_notifications', $notifications);

	if(!isset($crawlers) OR count($crawlers) < 1) $crawlers = array("008", "bot", "crawler", "spider", "Accoona-AI-Agent", "alexa", "Arachmo", "B-l-i-t-z-B-O-T", "boitho.com-dc", "Cerberian Drtrs","Charlotte", "cosmos", "Covario IDS", "DataparkSearch","FindLinks", "Holmes", "htdig", "ia_archiver", "ichiro", "inktomi", "igdeSpyder", "L.webis", "Larbin", "LinkWalker", "lwp-trivial", "mabontland", "Mnogosearch", "mogimogi", "Morning Paper", "MVAClient", "NetResearchServer", "NewsGator", "NG-Search", "NutchCVS", "Nymesis", "oegp", "Orbiter", "Peew", "Pompos", "PostPost", "PycURL", "Qseero", "Radian6", "SBIder", "ScoutJet", "Scrubby", "SearchSight", "semanticdiscovery", "ShopWiki", "silk", "Snappy", "Sqworm", "StackRambler", "Teoma", "TinEye", "truwoGPS", "updated", "Vagabondo", "Vortex", "voyager", "VYU2", "webcollage", "Websquash.com", "wf84", "WomlpeFactory", "yacy", "Yahoo! Slurp", "Yahoo! Slurp China", "YahooSeeker", "YahooSeeker-Testing", "YandexImages", "Yeti", "yoogliFetchAgent", "Zao", "ZyBorg", "froogle","looksmart", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "Scooter", "appie", "WebBug", "Spade", "rabaz", "TechnoratiSnoop");
	update_option('adrotate_crawlers', $crawlers);

	if(!isset($debug['general'])) $debug['general'] = false;
	if(!isset($debug['publisher'])) $debug['publisher'] = false;
	if(!isset($debug['advertiser'])) $debug['advertiser'] = false;
	if(!isset($debug['geo'])) $debug['geo'] = false;
	if(!isset($debug['timers'])) $debug['timers'] = false;
	if(!isset($debug['track'])) $debug['track'] = false;
	update_option('adrotate_debug', $debug);
}

/*-------------------------------------------------------------
 Name:      adrotate_check_competition
 Purpose:   Checks if WP has other advertising plugins installed
 Since:		3.21
-------------------------------------------------------------*/
function adrotate_check_competition() {
	
	$compatible_plugins = array(
		'ad-injection/ad-injection.php', 
		'adkingpro/adkingpro.php', 
//		'advanced-advertising-system/advanced_advertising_system.php',
//		'advert/advert.php',
		'advertising-manager/advertising-manager.php',
		'bannerman/bannerman.php',
//		'easy-ads-manager/easy-ads-manager.php',
//		'easy-adsense-injection/easy-adsense-injection.php',
//		'max-adsense/adsense.php',
//		'random-banners/random-banners.php',
		'simple-ads-manager/simple-ads-manager.php',
		'useful-banner-manager/useful-banner-manager.php',
		'wp-advertize-it/bootstrap.php',
		'wp-bannerize/main.php',
		'wp-ad-manager/ad-minister.php',
		'wp125/wp125.php',
	);

	if(!function_exists('get_plugins')) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$installed_plugins = get_plugins();

	$compatible = array();
	foreach($installed_plugins as $slug => $plugin) {
		if(in_array($slug, $compatible_plugins)) {
			$compatible[$slug] = $plugin['Title'].' v'.$plugin['Version'];
		}
	}
	unset($installed_plugins, $compatible_plugins, $status);

	return $compatible;
}

/*-------------------------------------------------------------
 Name:      adrotate_dummy_data
 Purpose:   Install dummy data in empty tables
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_dummy_data() {
	global $wpdb, $current_user;

	// Initial data
	$now 			= adrotate_now();
	$in84days 		= $now + 7257600;

	$no_ads = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate` LIMIT 1;");
	$no_schedules = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate_schedule` LIMIT 1;");
	$no_linkmeta = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate_linkmeta` LIMIT 1;");

	if(is_null($no_ads) AND is_null($no_schedules) AND is_null($no_linkmeta)) {
		// Demo ad 1
	    $wpdb->insert("{$wpdb->prefix}adrotate", array('title' => 'Demo ad 468x60', 'bannercode' => '&lt;a href=\&quot;https:\/\/ajdg.solutions\&quot;&gt;&lt;img src=\&quot;http://ajdg.solutions/assets/dummy-banners/adrotate-468x60.jpg\&quot; width=&quot;468&quot; height=&quot;60&quot; /&gt;&lt;/a&gt;', 'thetime' => $now, 'updated' => $now, 'author' => $current_user->user_login, 'imagetype' => '', 'image' => '', 'tracker' => 'N', 'show_everyone' => 'Y', 'desktop' => 'Y', 'mobile' => 'Y', 'tablet' => 'Y', 'os_ios' => 'Y', 'os_android' => 'Y', 'os_other' => 'Y', 'type' => 'active', 'weight' => 6, 'budget' => 0, 'crate' => 0, 'irate' => 0, 'cities' => serialize(array()), 'countries' => serialize(array())));
	    $ad_id = $wpdb->insert_id;

		$wpdb->insert("{$wpdb->prefix}adrotate_schedule", array('name' => 'Schedule for ad '.$ad_id, 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y'));
	    $schedule_id = $wpdb->insert_id;
		$wpdb->insert("{$wpdb->prefix}adrotate_linkmeta", array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));

		unset($ad_id, $schedule_id);
	
		// Demo ad 2
	    $wpdb->insert("{$wpdb->prefix}adrotate", array('title' => 'Demo ad 200x200', 'bannercode' => '&lt;a href=\&quot;https:\/\/ajdg.solutions\&quot;&gt;&lt;img src=\&quot;http://ajdg.solutions/assets/dummy-banners/adrotate-200x200.jpg\&quot; width=&quot;200&quot; height=&quot;200&quot; /&gt;&lt;/a&gt;', 'thetime' => $now, 'updated' => $now, 'author' => $current_user->user_login, 'imagetype' => '', 'image' => '', 'tracker' => 'N', 'show_everyone' => 'Y', 'desktop' => 'Y', 'mobile' => 'Y', 'tablet' => 'Y', 'os_ios' => 'Y', 'os_android' => 'Y', 'os_other' => 'Y', 'type' => 'active', 'weight' => 6, 'budget' => 0, 'crate' => 0, 'irate' => 0, 'cities' => serialize(array()), 'countries' => serialize(array())));
	    $ad_id = $wpdb->insert_id;

		$wpdb->insert("{$wpdb->prefix}adrotate_schedule", array('name' => 'Schedule for ad '.$ad_id, 'starttime' => $now, 'stoptime' => $in84days, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y'));
	    $schedule_id = $wpdb->insert_id;
		$wpdb->insert("{$wpdb->prefix}adrotate_linkmeta", array('ad' => $ad_id, 'group' => 0, 'user' => 0, 'schedule' => $schedule_id));

		unset($ad_id, $schedule_id);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_database_install
 Purpose:   Creates database table if it doesnt exist
 Since:		3.0.3
-------------------------------------------------------------*/
function adrotate_database_install() {
	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	// Initial data
	$charset_collate = $engine = '';
	$now = adrotate_now();
	$in84days = $now + 7257600;

	if(!empty($wpdb->charset)) {
		$charset_collate .= " DEFAULT CHARACTER SET {$wpdb->charset}";
	} 
	if($wpdb->has_cap('collation') AND !empty($wpdb->collate)) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$found_engine = $wpdb->get_var("SELECT ENGINE FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".DB_NAME."' AND `TABLE_NAME` = '{$wpdb->prefix}posts';");
	if(strtolower($found_engine) == 'innodb') {
		$engine = ' ENGINE=InnoDB';
	}

	$found_tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}adrotate%';");

	if(!in_array("{$wpdb->prefix}adrotate", $found_tables)) {
		dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate` (
		  	`id` mediumint(8) unsigned NOT NULL auto_increment,
		  	`title` varchar(255) NOT NULL DEFAULT '',
		  	`bannercode` longtext NOT NULL,
		  	`thetime` int(15) NOT NULL default '0',
			`updated` int(15) NOT NULL,
		  	`author` varchar(60) NOT NULL default '',
		  	`imagetype` varchar(10) NOT NULL,
		  	`image` varchar(255) NOT NULL,
		  	`tracker` char(1) NOT NULL default 'N',
		  	`show_everyone` char(1) NOT NULL default 'Y',
		  	`desktop` char(1) NOT NULL default 'Y',
		  	`mobile` char(1) NOT NULL default 'Y',
		  	`tablet` char(1) NOT NULL default 'Y',
		  	`os_ios` char(1) NOT NULL default 'Y',
		  	`os_android` char(1) NOT NULL default 'Y',
		  	`os_other` char(1) NOT NULL default 'Y',
		  	`type` varchar(10) NOT NULL default '0',
		  	`weight` int(3) NOT NULL default '6',
		  	`autodelete` char(1) NOT NULL default 'N',
		  	`budget` double NOT NULL default '0',
		  	`crate` double NOT NULL default '0',
		  	`irate` double NOT NULL default '0',
			`cities` text NOT NULL,
			`countries` text NOT NULL,
  		PRIMARY KEY  (`id`)
		) ".$charset_collate.$engine.";");
	}

	if(!in_array("{$wpdb->prefix}adrotate_groups", $found_tables)) {
		dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_groups` (
			`id` mediumint(8) unsigned NOT NULL auto_increment,
			`name` varchar(255) NOT NULL default '',
			`modus` tinyint(1) NOT NULL default '0',
			`fallback` varchar(5) NOT NULL default '0',
			`cat` longtext NOT NULL,
			`cat_loc` tinyint(1) NOT NULL default '0',
			`cat_par` tinyint(2) NOT NULL default '0',
			`page` longtext NOT NULL,
			`page_loc` tinyint(1) NOT NULL default '0',
			`page_par` tinyint(2) NOT NULL default '0',
			`mobile` tinyint(1) NOT NULL default '0',
			`geo` tinyint(1) NOT NULL default '0',
			`wrapper_before` longtext NOT NULL,
			`wrapper_after` longtext NOT NULL,
			`align` tinyint(1) NOT NULL default '0',
			`gridrows` int(3) NOT NULL DEFAULT '2',
			`gridcolumns` int(3) NOT NULL DEFAULT '2',
			`admargin` int(2) NOT NULL DEFAULT '0',
			`admargin_bottom` int(2) NOT NULL DEFAULT '0',
			`admargin_left` int(2) NOT NULL DEFAULT '0',
			`admargin_right` int(2) NOT NULL DEFAULT '0',
			`adwidth` varchar(6) NOT NULL DEFAULT '125',
			`adheight` varchar(6) NOT NULL DEFAULT '125',
			`adspeed` int(5) NOT NULL DEFAULT '6000',
			`repeat_impressions` char(1) NOT NULL DEFAULT 'Y',
			PRIMARY KEY  (`id`)
		) ".$charset_collate.$engine.";");
	}

	if(!in_array("{$wpdb->prefix}adrotate_linkmeta", $found_tables)) {
		dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_linkmeta` (
			`id` mediumint(8) unsigned NOT NULL auto_increment,
			`ad` int(5) unsigned NOT NULL default '0',
			`group` int(5) unsigned NOT NULL default '0',
			`user` int(5) unsigned NOT NULL default '0',
			`schedule` int(5) unsigned NOT NULL default '0',
			PRIMARY KEY  (`id`)
		) ".$charset_collate.$engine.";");
	}

	if(!in_array("{$wpdb->prefix}adrotate_schedule", $found_tables)) {
		dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_schedule` (
			`id` int(8) unsigned NOT NULL auto_increment,
			`name` varchar(255) NOT NULL default '',
			`starttime` int(15) unsigned NOT NULL default '0',
			`stoptime` int(15) unsigned NOT NULL default '0',
			`maxclicks` int(15) unsigned NOT NULL default '0',
			`maximpressions` int(15) unsigned NOT NULL default '0',
		  	`spread` char(1) NOT NULL default 'N',
			`daystarttime` char(4) NOT NULL default '0000',
			`daystoptime` char(4) NOT NULL default '0000',
			`day_mon` char(1) NOT NULL default 'Y',
			`day_tue` char(1) NOT NULL default 'Y',
			`day_wed` char(1) NOT NULL default 'Y',
			`day_thu` char(1) NOT NULL default 'Y',
			`day_fri` char(1) NOT NULL default 'Y',
			`day_sat` char(1) NOT NULL default 'Y',
			`day_sun` char(1) NOT NULL default 'Y',
		  	`autodelete` char(1) NOT NULL default 'N',
			PRIMARY KEY  (`id`),
		    KEY `starttime` (`starttime`)
		) ".$charset_collate.$engine.";");
	}

	if(!in_array("{$wpdb->prefix}adrotate_stats", $found_tables)) {
		dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_stats` (
			`id` bigint(9) unsigned NOT NULL auto_increment,
			`ad` int(5) unsigned NOT NULL default '0',
			`group` int(5) unsigned NOT NULL default '0',
			`thetime` int(15) unsigned NOT NULL default '0',
			`clicks` int(15) unsigned NOT NULL default '0',
			`impressions` int(15) unsigned NOT NULL default '0',
			PRIMARY KEY  (`id`),
			INDEX `ad` (`ad`),
			INDEX `thetime` (`thetime`)
		) ".$charset_collate.$engine.";");
	}

	if(!in_array("{$wpdb->prefix}adrotate_stats_archive", $found_tables)) {
		dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_stats_archive` (
			`id` bigint(9) unsigned NOT NULL auto_increment,
			`ad` int(5) unsigned NOT NULL default '0',
			`group` int(5) unsigned NOT NULL default '0',
			`thetime` int(15) unsigned NOT NULL default '0',
			`clicks` int(15) unsigned NOT NULL default '0',
			`impressions` int(15) unsigned NOT NULL default '0',
			PRIMARY KEY  (`id`),
			INDEX `ad` (`ad`),
			INDEX `thetime` (`thetime`)
		) ".$charset_collate.$engine.";");
	}

	if(!in_array("{$wpdb->prefix}adrotate_tracker", $found_tables)) {
		dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_tracker` (
			`id` bigint(9) unsigned NOT NULL auto_increment,
			`ipaddress` varchar(15) NOT NULL default '0',
			`timer` int(15) unsigned NOT NULL default '0',
			`bannerid` int(15) unsigned NOT NULL default '0',
			`stat` char(1) NOT NULL default 'c',
			PRIMARY KEY  (`id`),
		    KEY `ipaddress` (`ipaddress`),
		    KEY `timer` (`timer`)
		) ".$charset_collate.$engine.";");
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_check_upgrade
 Purpose:   Checks if the plugin needs to upgrade stuff upon activation
 Since:		3.7.3
-------------------------------------------------------------*/
function adrotate_check_upgrade() {	
	if(version_compare(PHP_VERSION, '5.3.0', '<') == -1) { 
		deactivate_plugins(plugin_basename('adrotate-pro/adrotate-pro.php'));
		wp_die('AdRotate 3.10.8 and up requires PHP 5.3 or higher. Your server reports version '.PHP_VERSION.'. Contact your hosting provider about upgrading your server!<br /><a href="'. get_option('siteurl').'/wp-admin/plugins.php">Back to plugins</a>.'); 
		return; 
	} else {
		$adrotate_db_version = get_option("adrotate_db_version");
		if($adrotate_db_version['current'] < ADROTATE_DB_VERSION) {
			adrotate_database_upgrade();
		}
	
		$adrotate_version = get_option("adrotate_version");
		if($adrotate_version['current'] < ADROTATE_VERSION) {
			adrotate_core_upgrade();
		}

		adrotate_check_config();
		adrotate_check_schedules();
		adrotate_evaluate_ads();
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_database_upgrade
 Purpose:   Upgrades AdRotate where required
 Since:		3.0.3
-------------------------------------------------------------*/
function adrotate_database_upgrade() {
	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	// Database type and specs
	$charset_collate = $engine = '';
	$found_tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}adrotate%';");
	if(!empty($wpdb->charset)) {
		$charset_collate .= " DEFAULT CHARACTER SET {$wpdb->charset}";
	} 
	if($wpdb->has_cap('collation') AND !empty($wpdb->collate)) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$found_engine = $wpdb->get_var("SELECT ENGINE FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".DB_NAME."' AND `TABLE_NAME` = '{$wpdb->prefix}posts';");
	if(strtolower($found_engine) == 'innodb') {
		$engine = ' ENGINE=InnoDB';
	}

	$adrotate_db_version = get_option("adrotate_db_version");

	// Database: 	58
	// AdRotate:	4.0
	if($adrotate_db_version['current'] < 58) {
		$wpdb->query("ALTER TABLE `{$wpdb->prefix}adrotate_schedule` CHANGE `dayimpressions` `hourimpressions` int(15) NOT NULL default '0';");
	}

	// Database: 	59
	// AdRotate:	4.1
	if($adrotate_db_version['current'] < 59) {
		adrotate_add_column("{$wpdb->prefix}adrotate", 'paid', 'char(1) NOT NULL default \'U\' AFTER `image`');
		adrotate_add_column("{$wpdb->prefix}adrotate", 'os_ios', 'char(1) NOT NULL default \'Y\' AFTER `tablet`');
		adrotate_add_column("{$wpdb->prefix}adrotate", 'os_android', 'char(1) NOT NULL default \'Y\' AFTER `os_ios`');
		adrotate_add_column("{$wpdb->prefix}adrotate", 'os_other', 'char(1) NOT NULL default \'Y\' AFTER `os_android`');

		adrotate_del_column("{$wpdb->prefix}adrotate", 'sortorder');
		adrotate_del_column("{$wpdb->prefix}adrotate_groups", 'sortorder');

		if(!in_array("{$wpdb->prefix}adrotate_transactions", $found_tables)) {
			dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_transactions` (
				`id` mediumint(8) unsigned NOT NULL auto_increment,
				`ad` mediumint(8) unsigned NOT NULL default '0',
				`user` mediumint(8) unsigned NOT NULL default '0',
				`reference` varchar(100) NOT NULL,
				`note` longtext NOT NULL,
				`billed` int(15) unsigned NOT NULL default '0',
				`paid` int(15) unsigned NOT NULL default '0',
				`amount` double NOT NULL default '0',
				`budget` char(1) NOT NULL default 'U',
				PRIMARY KEY  (`id`),
			    KEY `ad` (`ad`)
			) ".$charset_collate.$engine.";");
		}
	}

	// Database: 	60
	// AdRotate:	4.2
	if($adrotate_db_version['current'] < 60) {
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_tracker`");
	}

	// Database: 	61
	// AdRotate:	4.3
	if($adrotate_db_version['current'] < 61) {
		adrotate_del_column("{$wpdb->prefix}adrotate_schedule", 'hourimpressions');
	}

	// Database: 	62
	// AdRotate:	4.4
	if($adrotate_db_version['current'] < 62) {
		// Make sure the table really is gone before creating a new one!
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_tracker`");

		dbDelta("CREATE TABLE `{$wpdb->prefix}adrotate_tracker` (
			`id` bigint(9) unsigned NOT NULL auto_increment,
			`ipaddress` varchar(15) NOT NULL default '0',
			`timer` int(15) unsigned NOT NULL default '0',
			`bannerid` int(15) unsigned NOT NULL default '0',
			`stat` char(1) NOT NULL default 'c',
			PRIMARY KEY  (`id`),
		    KEY `ipaddress` (`ipaddress`),
		    KEY `timer` (`timer`)
		) ".$charset_collate.$engine.";");

		$wpdb->query("DELETE FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE '\_transient\_adrotate\_%'");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE '\_transient\_timeout\_adrotate\_%'");
	}

	// Database: 	63
	// AdRotate:	4.5
	if($adrotate_db_version['current'] < 63) {
		adrotate_add_column("{$wpdb->prefix}adrotate", 'autodelete', 'char(1) NOT NULL default \'N\' AFTER `weight`');
		adrotate_add_column("{$wpdb->prefix}adrotate_schedule", 'autodelete', 'char(1) NOT NULL default \'N\' AFTER `day_sun`');
	}

	// Database: 	64
	// AdRotate:	4.8
	if($adrotate_db_version['current'] < 64) {
		adrotate_add_column("{$wpdb->prefix}adrotate", 'show_everyone', 'char(1) NOT NULL default \'Y\' AFTER `tracker`');
		adrotate_add_column("{$wpdb->prefix}adrotate_groups", 'repeat_impressions', 'char(1) NOT NULL default \'Y\' AFTER `adspeed`');
	}

	// Database: 	65
	// AdRotate:	5.4
	if($adrotate_db_version['current'] < 65) {
		adrotate_del_column("{$wpdb->prefix}adrotate", 'responsive');
		adrotate_del_column("{$wpdb->prefix}adrotate", 'paid');
		$wpdb->update("{$wpdb->prefix}adrotate", array('type' => 'trash'), array('type' => 'bin'));
		$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}adrotate_transactions`");
	}

	update_option("adrotate_db_version", array('current' => ADROTATE_DB_VERSION, 'previous' => $adrotate_db_version['current']));
}

/*-------------------------------------------------------------
 Name:      adrotate_core_upgrade
 Purpose:   Upgrades AdRotate where required
 Since:		3.5
-------------------------------------------------------------*/
function adrotate_core_upgrade() {
	global $wpdb, $wp_roles;

	$firstrun = date('U') + 3600;
	$adrotate_version = get_option("adrotate_version");
	$adrotate_config = get_option('adrotate_config');

	// 4.0
	if($adrotate_version['current'] < 382) {
		$config382 = get_option('adrotate_config');
		if($config382['enable_advertisers'] == 'Y') {
			$advertisers = $wpdb->get_results("SELECT `user` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `user` > 0;");
			foreach($advertisers as $advertiser) {
				update_user_meta($advertiser->user, 'adrotate_is_advertiser', 'Y');
				update_user_meta($advertiser->user, 'adrotate_permissions', array('edit' => $config382['enable_editing'], 'mobile' => $config382['enable_mobile_advertisers'], 'geo' => $config382['enable_geo_advertisers']));
				update_user_meta($advertiser->user, 'adrotate_notes', '');
			}
		}
		unset($config382);

		$role = get_role('administrator');		
		$role->add_cap("adrotate_advertiser_manage");
	}

	// 4.1
	if($adrotate_version['current'] < 384) {
		// Dummy
	}

	// 4.2
	if($adrotate_version['current'] < 385) {
		wp_clear_scheduled_hook('adrotate_clean_trackerdata');
	}

	// 4.2.1
	if($adrotate_version['current'] < 386) {
		if(!wp_next_scheduled('adrotate_delete_transients')) wp_schedule_event($firstrun, 'hourly', 'adrotate_delete_transients');
	}
	
	// 4.3
	if($adrotate_version['current'] < 387) {
		delete_option('adrotate_responsive_required');
	}

	// 4.4
	if($adrotate_version['current'] < 388) {
		wp_clear_scheduled_hook('adrotate_delete_transients');
		if(!wp_next_scheduled('adrotate_empty_trackerdata')) wp_schedule_event($firstrun, 'hourly', 'adrotate_empty_trackerdata');
	}

	// 4.5
	if($adrotate_version['current'] < 389) {
		adrotate_check_schedules();
	}

	// 4.7
	if($adrotate_version['current'] < 390) {
		if(!is_dir(WP_CONTENT_DIR.'/banners')) mkdir(WP_CONTENT_DIR.'/banners', 0755);
		if(!is_dir(WP_CONTENT_DIR.'/reports')) mkdir(WP_CONTENT_DIR.'/reports', 0755);
		$config390 = get_option('adrotate_config');
		$config390['banner_folder'] = "banners";
		update_option('adrotate_config', $config390);
	}

	// 5.1
	if($adrotate_version['current'] < 393) {
		$groups = $wpdb->get_results("SELECT `id`, `modus`, `gridcolumns`, `adwidth`, `adheight`, `admargin`, `admargin_bottom`, `admargin_left`, `admargin_right`, `align` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;", ARRAY_A);
	
		if(count($groups) > 0) {
			foreach($groups as $group) {
				$output_css = "";
				if($group['align'] == 0) { // None
					$group_align = '';
				} else if($group['align'] == 1) { // Left
					$group_align = ' float:left; clear:left;';
				} else if($group['align'] == 2) { // Right
					$group_align = ' float:right; clear:right;';
				} else if($group['align'] == 3) { // Center
					$group_align = ' margin: 0 auto;';
				}
	
				if($group['modus'] == 0 AND ($group['admargin'] > 0 OR $group['admargin_right'] > 0 OR $group['admargin_bottom'] > 0 OR $group['admargin_left'] > 0 OR $group['align'] > 0)) { // Single ad group
					if($group['align'] < 3) {
						$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { margin:".$group['admargin']."px ".$group['admargin_right']."px ".$group['admargin_bottom']."px ".$group['admargin_left']."px;".$group_align." }\n";
					} else {
						$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { ".$group_align." }\n";	
					}
				}
		
				if($group['modus'] == 1) { // Dynamic group
					if($group['adwidth'] != 'auto') {
						$width = "width:100%; max-width:".$group['adwidth']."px;";
					} else {
						$width = "width:auto;";
					}
					
					if($group['adheight'] != 'auto') {
						$height = "height:100%; max-height:".$group['adheight']."px;";
					} else {
						$height = "height:auto;";
					}
	
					if($group['align'] < 3) {
						$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { margin:".$group['admargin']."px ".$group['admargin_right']."px ".$group['admargin_bottom']."px ".$group['admargin_left']."px;".$width." ".$height.$group_align." }\n";
					} else {
						$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { ".$width." ".$height.$group_align." }\n";	
					}
	
					unset($width_sum, $width, $height_sum, $height);
				}
		
				if($group['modus'] == 2) { // Block group
					if($group['adwidth'] != 'auto') {
						$width_sum = $group['gridcolumns'] * ($group['admargin_left'] + $group['adwidth'] + $group['admargin_right']);
						$grid_width = "min-width:".$group['admargin_left']."px; max-width:".$width_sum."px;";
					} else {
						$grid_width = "width:auto;";
					}
					
					$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$group['id']." { ".$grid_width.$group_align." }\n";
					$output_css .= "\t.b".$adrotate_config['adblock_disguise']."-".$group['id']." { margin:".$group['admargin']."px ".$group['admargin_right']."px ".$group['admargin_bottom']."px ".$group['admargin_left']."px; }\n";
					unset($width_sum, $grid_width, $height_sum, $grid_height);
				}
				$generated_css[$group['id']] = $output_css;
				unset($output_css);
			}
			unset($groups);

			// Check/Merge existing CSS
			$group_css = get_option('adrotate_group_css');
			if(is_array($group_css)) {
				$keys = array_keys($group_css);
				foreach($keys as $i => $key) {
					if (array_key_exists($key, $generated_css)) {
						unset($generated_css[$key]);
					}
				}
				$group_css = array_merge($group_css, $generated_css);
			} else {
				$group_css = $generated_css;
			}

			update_option('adrotate_group_css', $group_css);
		}
	}
	
	update_option("adrotate_version", array('current' => ADROTATE_VERSION, 'previous' => $adrotate_version['current']));
}

/*-------------------------------------------------------------
 Name:      adrotate_optimize_database
 Purpose:   Optimizes all AdRotate tables
 Since:		3.4
-------------------------------------------------------------*/
function adrotate_optimize_database() {
	global $wpdb;
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$adrotate_db_timer 	= get_option('adrotate_db_timer');
	$now = adrotate_now();

	if($adrotate_db_timer < ($now - 86400)) {
		dbDelta("OPTIMIZE TABLE `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_groups`, `{$wpdb->prefix}adrotate_linkmeta`, `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate_stats_archive`, `{$wpdb->prefix}adrotate_tracker`, `{$wpdb->prefix}adrotate_schedule`;");
		dbDelta("REPAIR TABLE `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_groups`, `{$wpdb->prefix}adrotate_linkmeta`, `{$wpdb->prefix}adrotate_stats`, `{$wpdb->prefix}adrotate_stats_archive`, `{$wpdb->prefix}adrotate_tracker`, `{$wpdb->prefix}adrotate_schedule`;");

		update_option('adrotate_db_timer', $now);
		adrotate_return('adrotate-settings', 403, array('tab' => 'maintenance'));
	} else {
		adrotate_return('adrotate-settings', 504, array('tab' => 'maintenance'));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_cleanup_database
 Purpose:   Clean AdRotate tables
 Since:		3.5
-------------------------------------------------------------*/
function adrotate_cleanup_database() {
	global $wpdb;

	$now = adrotate_now();

	// Clean up Tracker data
	$yesterday = $now - 86400;
	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_tracker` WHERE `timer` < $yesterday;");

	// Delete empty ads, groups and schedules
	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'empty' OR `type` = 'a_empty';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` = '';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` = '';");
	
	// Remove CSS from deleted groups
	$groups = $wpdb->get_col("SELECT `id` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;");
	$group_css = get_option('adrotate_group_css');
	foreach($group_css as $group_id => $css) {
		if(!array_key_exists($group_id, $groups)) {
			unset($group_css[$group_id]);
		}
	}
	update_option('adrotate_group_css', $group_css);
	unset($groups, $group_css);

	// Clean up meta data
	$ads = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate` ORDER BY `id`;");
	$metas = $wpdb->get_results("SELECT `id`, `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` ORDER BY `id`;");

	$adverts = $linkmeta = array();
	foreach($ads as $ad) {
		$adverts[$ad->id] = $ad->id;
	}
	foreach($metas as $meta) {
		$linkmeta[$meta->id] = $meta->ad;
	}

	$delete_meta = array_diff($linkmeta, $adverts);
	foreach($delete_meta as $meta => $advert) {
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `id` = {$meta};");
		unset($delete_meta[$meta], $meta, $advert);
	}
	unset($ads, $metas, $adverts, $linkmeta, $delete_meta);

	// Clean up stray linkmeta
	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = 0 OR `ad` = '';");

	// (Optionally) Delete expired schedules
	if(isset($_POST['adrotate_db_cleanup_schedules'])) {
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_schedule` WHERE `stoptime` < $now;");
	}

	// (Optionally) Delete old stats
	if(isset($_POST['adrotate_db_cleanup_statistics'])) {
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_stats` WHERE `thetime` < $lastyear;");
	}

	// (Optionally) Delete trashed adverts and data
	if(isset($_POST['adrotate_db_cleanup_trash'])) {
		$adverts = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'trash';");
		foreach($adverts as $meta) {
			$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$meta->id};");
			$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$meta->id};");
			$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_stats` WHERE `ad` = {$meta->id};");
			$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_stats_archive` WHERE `ad` = {$meta->id};");
		}
		unset($linkmeta);
	}

	adrotate_return('adrotate-settings', 406, array('tab' => 'maintenance'));
}

/*-------------------------------------------------------------
 Name:      adrotate_cleanup_assets
 Purpose:   Clean/delete AdRotate assets
 Since:		4.13
-------------------------------------------------------------*/
function adrotate_cleanup_assets() {
	global $wpdb, $adrotate_config;

	$asset_folder = WP_CONTENT_DIR."/".$adrotate_config['banner_folder'];
	$delete_asset = $found_assets = $advert_assets = array();

	// See what files are there
	if($handle = opendir($asset_folder)) {
		$extensions = array('jpg', 'jpeg', 'gif', 'png');

	    while(false !== ($file = readdir($handle))) {
			$fileinfo = pathinfo($file);
	        if($file != "." AND $file != ".." AND !is_dir($asset_folder.'/'.$file) AND in_array($fileinfo['extension'], $extensions)) {
				$found_assets[] = $asset_folder."/".$file;
	        }
			unset($fileinfo);
	    }
	    closedir($handle);
	}
	unset($handle, $file);
	
	// See what files are used
	$assets = $wpdb->get_results("SELECT `image` FROM `{$wpdb->prefix}adrotate` WHERE `imagetype` = 'dropdown' AND `image` != '';");
	foreach($assets as $asset) {
		$advert_assets[] = $asset_folder."/".basename($asset->image);
		unset($asset);
	}
	unset($assets);
	
	// Determine which assets to delete (only those not currently used in adverts)
	$delete_asset = array_diff($found_assets, $advert_assets);

	// If any, delete them
	if(count($delete_asset) > 0) {
		array_map('unlink', $delete_asset);
	}
	
	// (Optionally) Delete export files
	if(isset($_POST['adrotate_asset_cleanup_exportfiles'])) {
		array_map('unlink', glob(WP_CONTENT_DIR.'/reports/AdRotate_export_*.csv'));
	}

	adrotate_return('adrotate-settings', 406, array('tab' => 'maintenance'));
}

/*-------------------------------------------------------------
 Name:      adrotate_empty_trackerdata
 Purpose:   Removes old statistics
 Since:		4.4
-------------------------------------------------------------*/
function adrotate_empty_trackerdata() {
	global $wpdb;

	$now = adrotate_now();
	$clicks = $now - 86400;
	$impressions = $now - 3600;

	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_tracker` WHERE `timer` < {$impressions} AND `stat` = 'i';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_tracker` WHERE `timer` < {$clicks} AND `stat` = 'c';");
	$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_tracker` WHERE `ipaddress`  = 'unknown' OR `ipaddress`  = '';");
}

/*-------------------------------------------------------------
 Name:      adrotate_empty_trash
 Purpose:   Delete expired and trashed adverts
 Since:		3.21
-------------------------------------------------------------*/
function adrotate_empty_trash() {
	global $wpdb;

	$threedaysago = adrotate_now() - 259200;

	$adverts = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'trash' AND `updated` < {$threedaysago};");
	foreach($adverts as $advert) {
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$advert->id};");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$advert->id};");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_stats` WHERE `ad` = {$advert->id};");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_stats_archive` WHERE `ad` = {$advert->id};");
	}
	unset($adverts);
}


/*-------------------------------------------------------------
 Name:      adrotate_auto_delete
 Purpose:   Auto trash selected adverts and schedules
 Since:		4.5
-------------------------------------------------------------*/
function adrotate_auto_delete() {
	global $wpdb;

	// Auto trash expired adverts
	$now = adrotate_now();
	$twentythreehoursago = $now - 82800;

	$adverts = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'expired' AND `autodelete` = 'Y';");
	foreach($adverts as $advert) {
		$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$advert->id}' AND  `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

		if($stoptime <= $twentythreehoursago) {
			$wpdb->update("{$wpdb->prefix}adrotate", array('type' => 'trash', 'updated' => $now), array('id' => $advert->id));
		}
		unset($advert, $stoptime);
	}
	unset($adverts);

	// Auto delete expired schedules
	$schedules = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate_schedule` WHERE `stoptime` <= {$twentythreehoursago} AND `autodelete` = 'Y';");
	foreach($schedules as $schedule) {
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_schedule` WHERE `id` = {$schedule->id};");
		$wpdb->query("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` = {$schedule->id};");
	}

}

/*-------------------------------------------------------------
 Name:      adrotate_add_column
 Purpose:   Check if the column exists in the table
 Since:		3.0.3
-------------------------------------------------------------*/
function adrotate_add_column($table_name, $column_name, $attributes) {
	global $wpdb;
	
	foreach ($wpdb->get_col("SHOW COLUMNS FROM $table_name;") as $column ) {
		if ($column == $column_name) return true;
	}
	
	$wpdb->query("ALTER TABLE $table_name ADD $column_name " . $attributes.";");
	
	foreach ($wpdb->get_col("SHOW COLUMNS FROM $table_name;") as $column ) {
		if ($column == $column_name) return true;
	}
	
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_del_column
 Purpose:   Check if the column exists in the table remove if it does
 Since:		3.8.3.3
-------------------------------------------------------------*/
function adrotate_del_column($table_name, $column_name) {
	global $wpdb;
	
	foreach ($wpdb->get_col("SHOW COLUMNS FROM $table_name;") as $column ) {
		if ($column == $column_name) {
			$wpdb->query("ALTER TABLE $table_name DROP $column;");
			return true;
		}
	}
	
	return false;
}
?>