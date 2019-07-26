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
 Name:      adrotate_generate_input
 Purpose:   Generate advert code based on user input
 Since:		4.8
-------------------------------------------------------------*/
function adrotate_generate_input() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_generate_ad')) {
		// Mandatory
		$id = '';
		if(isset($_POST['adrotate_id'])) $id = $_POST['adrotate_id'];

		// Basic advert
		$fullsize_image = $targeturl = '';
		if(isset($_POST['adrotate_fullsize_dropdown'])) $fullsize_image = strip_tags(trim($_POST['adrotate_fullsize_dropdown'], "\t\n "));
		if(isset($_POST['adrotate_targeturl'])) $targeturl = strip_tags(trim($_POST['adrotate_targeturl'], "\t\n "));

		$small_image = $medium_image = $large_image = '';		
		if(isset($_POST['adrotate_small_dropdown'])) $small_image = strip_tags(trim($_POST['adrotate_small_dropdown'], "\t\n "));
		if(isset($_POST['adrotate_medium_dropdown'])) $medium_image = strip_tags(trim($_POST['adrotate_medium_dropdown'], "\t\n "));
		if(isset($_POST['adrotate_large_dropdown'])) $large_image = strip_tags(trim($_POST['adrotate_large_dropdown'], "\t\n "));

		$new_window = $nofollow = $title_attr = $alt_attr = '';
		if(isset($_POST['adrotate_newwindow'])) $new_window = strip_tags(trim($_POST['adrotate_newwindow'], "\t\n "));	
		if(isset($_POST['adrotate_nofollow'])) $nofollow = strip_tags(trim($_POST['adrotate_nofollow'], "\t\n "));	
		if(isset($_POST['adrotate_title_attr'])) $title_attr = strip_tags(trim($_POST['adrotate_title_attr'], "\t\n "));	

		if(current_user_can('adrotate_ad_manage')) {	
			// Fullsize Image & figure out adwidth and adheight
			$fullsize_path = WP_CONTENT_URL."/".$adrotate_config['banner_folder']."/".$fullsize_image;
			$fullsize_size = @getimagesize($fullsize_path);
			if($fullsize_size){
				$adwidth = ' width="'.$fullsize_size[0].'"';
				$adheight = ' height="'.$fullsize_size[1].'"';
			} else {
				$adwidth = $adheight = '';
			}

			// Open in a new window?
			if(isset($new_window) AND strlen($new_window) != 0) {
				$new_window = ' target="_blank"';
			} else {
				$new_window = '';
			}

			// Set nofollow?
			if(isset($nofollow) AND strlen($nofollow) != 0) {
				$nofollow = ' rel="nofollow"';
			} else {
				$nofollow = '';
			}

			// Add alt and title attributes?
			if(isset($title_attr) AND strlen($title_attr) != 0) {
				$fileinfo = pathinfo($fullsize_path);

				$title_attr = ' title="'.$fileinfo['filename'].'"';
				$alt_attr = ' alt="'.$fileinfo['filename'].'"';
			} else {
				$title_attr = $alt_attr = '';
			}

			// Viewports
			$srcset = $sizes = array();
			if(strlen($small_image) > 0) {
				$small_path = WP_CONTENT_URL."/".$adrotate_config['banner_folder']."/".$small_image;
				$small_width = @getimagesize($small_path);
				if($small_width){
					$srcset[] = $small_path.' '.$small_width[0].'w';
					$sizes[] = '(max-width: 480px) '.$small_width[0].'px';
					unset($small_path, $small_image, $small_width);
				}
			}
			if(strlen($medium_image) > 0) {
				$medium_path = WP_CONTENT_URL."/".$adrotate_config['banner_folder']."/".$medium_image;
				$medium_width = @getimagesize($medium_path);
				if($medium_width){
					$srcset[] = $medium_path.' '.$medium_width[0].'w';
					$sizes[] = '(max-width: 960px) '.$medium_width[0].'px';
					unset($medium_path, $medium_image, $medium_width);
				}
			}
			if(strlen($large_image) > 0) {
				$large_path = WP_CONTENT_URL."/".$adrotate_config['banner_folder']."/".$large_image;
				$large_width = @getimagesize($large_path);
				if($large_width){
					$srcset[] = $large_path.' '.$large_width[0].'w';
					$sizes[] = '(max-width: 1280px) '.$large_width[0].'px';
					unset($large_path, $large_image, $large_width);
				}
			}

			$srcset[] = $fullsize_path.' '.$fullsize_size[0].'w';
			$sizes[] = '1281px';

			$scrset_html = (count($srcset) > 0) ? ' srcset="'.implode(", ", $srcset).'"' : '';
			$sizes_html = (count($sizes) > 0) ? ' sizes="'.implode(", ", $sizes).'"' : '';

			// Determine image settings
			$imagetype = "dropdown";
			$image = WP_CONTENT_URL."/%folder%/".$fullsize_image;
			$asset = "<img src=\"%asset%\"".$adwidth.$adheight.$alt_attr.$scrset_html.$sizes_html." />";

			// Generate code
			$bannercode = "<a href=\"".$targeturl."\"".$new_window.$nofollow.$title_attr.">".$asset."</a>";

			// Save the ad to the DB
			$wpdb->update($wpdb->prefix.'adrotate', array('bannercode' => $bannercode, 'imagetype' => $imagetype, 'image' => $image), array('id' => $id));

			adrotate_return('adrotate-ads', 226, array('view' => 'edit', 'ad'=> $id));
			exit;
		} else {
			adrotate_return('adrotate-ads', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_save_header
 Purpose:   Generate header code based on user input
 Since:		5.0
-------------------------------------------------------------*/
function adrotate_save_header() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce_header'], 'adrotate_nonce')) {
		// Mandatory
		$headercode = $adstxt = '';
		if(isset($_POST['adrotate_header'])) $headercode = htmlspecialchars(trim($_POST['adrotate_header'], "\t\n "), ENT_QUOTES);
		if(isset($_POST['adrotate_adstxt'])) $adstxt = htmlspecialchars(trim($_POST['adrotate_adstxt'], "\t "), ENT_QUOTES);

		if(current_user_can('adrotate_ad_manage')) {	
			// ads.txt

			// Format new lines
			$adstxt = str_ireplace("\n\n", "\n", $adstxt);
			$adstxt = explode("\n", $adstxt);

			if(is_array($adstxt)) {
				foreach($adstxt as $ad) {
					$adpieces = explode(",", $ad);
					foreach($adpieces as $key => $piece) {
						$piece = trim($piece, "\t\n, ");
						$piece = preg_replace("/\s+/", " ", $piece);
						$adpieces[$key] = $piece;
					}
					$ad = implode(", ", $adpieces);
					$lines[] = "$ad\n";
					unset($ad, $adpieces);
				}

				// Write new rules
				$fp = fopen(ABSPATH.'ads.txt', 'w');
				foreach($lines as $line){
					fwrite($fp, "$line");
				}
				fclose($fp);
				unset($fp);
			}
			
			// Header snippet
			// Clean things up
			if(preg_match("/%RANDOM%/", $headercode)) $headercode = str_replace('%RANDOM%', '%random%', $headercode);

			// Save the code
			update_option('adrotate_header_output', $headercode);

			adrotate_return('adrotate-ads', 227, array('view' => 'advanced'));
			exit;
		} else {
			adrotate_return('adrotate-ads', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_input
 Purpose:   Prepare input form on saving new or updated banners
 Since:		0.1 
-------------------------------------------------------------*/
function adrotate_insert_input() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_ad')) {
		// Mandatory
		$id = $author = $title = $bannercode = $active = '';
		if(isset($_POST['adrotate_id'])) $id = $_POST['adrotate_id'];
		if(isset($_POST['adrotate_username'])) $author = $_POST['adrotate_username'];
		if(isset($_POST['adrotate_title'])) $title = strip_tags(htmlspecialchars(trim($_POST['adrotate_title'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_bannercode'])) $bannercode = htmlspecialchars(trim($_POST['adrotate_bannercode'], "\t\n "), ENT_QUOTES);
		$thetime = adrotate_now();
		if(isset($_POST['adrotate_active'])) $active = strip_tags(htmlspecialchars(trim($_POST['adrotate_active'], "\t\n "), ENT_QUOTES));

		// Schedule
		$start_date = $start_hour = $start_minute = $end_date = $end_hour = $end_minute = '';
		if(isset($_POST['adrotate_start_date'])) $start_date = strip_tags(trim($_POST['adrotate_start_date'], "\t\n "));
		if(isset($_POST['adrotate_start_hour'])) $start_hour = strip_tags(trim($_POST['adrotate_start_hour'], "\t\n "));
		if(isset($_POST['adrotate_start_minute'])) $start_minute = strip_tags(trim($_POST['adrotate_start_minute'], "\t\n "));
		if(isset($_POST['adrotate_end_date'])) $end_date = strip_tags(trim($_POST['adrotate_end_date'], "\t\n "));
		if(isset($_POST['adrotate_end_hour'])) $end_hour = strip_tags(trim($_POST['adrotate_end_hour'], "\t\n "));
		if(isset($_POST['adrotate_end_minute'])) $end_minute = strip_tags(trim($_POST['adrotate_end_minute'], "\t\n "));

		$maxclicks = $maxshown = $spread = '';
		if(isset($_POST['adrotate_maxclicks'])) $maxclicks = strip_tags(trim($_POST['adrotate_maxclicks'], "\t\n "));
		if(isset($_POST['adrotate_maxshown'])) $maxshown = strip_tags(trim($_POST['adrotate_maxshown'], "\t\n "));	
		if(isset($_POST['adrotate_spread'])) $spread = strip_tags(trim($_POST['adrotate_spread'], "\t\n "));	

		// Schedules and Timeframe
		$schedules = $groups = array();
		if(isset($_POST['scheduleselect'])) $schedules = $_POST['scheduleselect'];
		if(isset($_POST['groupselect'])) $groups = $_POST['groupselect'];

		// Advert options
		$image_field = $image_dropdown = $tracker = $mobile = $tablet = $os_ios = $os_android = $os_other = $type = $weight = '';
		if(isset($_POST['adrotate_image'])) $image_field = strip_tags(trim($_POST['adrotate_image'], "\t\n "));
		if(isset($_POST['adrotate_image_dropdown'])) $image_dropdown = strip_tags(trim($_POST['adrotate_image_dropdown'], "\t\n "));
		if(isset($_POST['adrotate_tracker'])) $tracker = strip_tags(trim($_POST['adrotate_tracker'], "\t\n "));
		if(isset($_POST['adrotate_show_everyone'])) $show_everyone = strip_tags(trim($_POST['adrotate_show_everyone'], "\t\n "));
		if(isset($_POST['adrotate_desktop'])) $desktop = strip_tags(trim($_POST['adrotate_desktop'], "\t\n "));
		if(isset($_POST['adrotate_mobile'])) $mobile = strip_tags(trim($_POST['adrotate_mobile'], "\t\n "));
		if(isset($_POST['adrotate_tablet'])) $tablet = strip_tags(trim($_POST['adrotate_tablet'], "\t\n "));
		if(isset($_POST['adrotate_ios'])) $os_ios = strip_tags(trim($_POST['adrotate_ios'], "\t\n "));
		if(isset($_POST['adrotate_android'])) $os_android = strip_tags(trim($_POST['adrotate_android'], "\t\n "));
		if(isset($_POST['adrotate_other'])) $os_other = strip_tags(trim($_POST['adrotate_other'], "\t\n "));
		if(isset($_POST['adrotate_type'])) $type = strip_tags(trim($_POST['adrotate_type'], "\t\n "));
		if(isset($_POST['adrotate_weight'])) $weight = strip_tags($_POST['adrotate_weight']);
		if(isset($_POST['adrotate_autodelete'])) $autodelete = strip_tags($_POST['adrotate_autodelete']);
	
		// GeoTargeting
		$cities = '';
		$countries = $countries_westeurope = $countries_easteurope = $countries_northamerica = $countries_southamerica = $countries_southeastasia = array();
		if(isset($_POST['adrotate_geo_cities'])) $cities = strip_tags(trim($_POST['adrotate_geo_cities'], "\t\n "));
		if(isset($_POST['adrotate_geo_countries'])) $countries = $_POST['adrotate_geo_countries'];
		if(isset($_POST['adrotate_geo_westeurope'])) $countries_westeurope = array('AD', 'AT', 'BE', 'DK', 'FR', 'DE', 'GR', 'IS', 'IE', 'IT', 'LI', 'LU', 'MT', 'MC', 'NL', 'NO', 'PT', 'SM', 'ES', 'SE', 'CH', 'VA', 'GB');
		if(isset($_POST['adrotate_geo_easteurope'])) $countries_easteurope = array('AL', 'AM', 'AZ', 'BY', 'BA', 'BG', 'HR', 'CY', 'CZ', 'EE', 'FI', 'GE', 'HU', 'LV', 'LT', 'MK', 'MD', 'PL', 'RO', 'RS', 'SK', 'SI', 'TR', 'UA');
		if(isset($_POST['adrotate_geo_northamerica'])) $countries_northamerica = array('AG', 'BS', 'BB', 'BZ', 'CA', 'CR', 'CU', 'DM', 'DO', 'SV', 'GD', 'GT', 'HT', 'HN', 'JM', 'MX', 'NI', 'PA', 'KN', 'LC', 'VC', 'TT', 'US');
		if(isset($_POST['adrotate_geo_southamerica'])) $countries_southamerica = array('AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'GY', 'PY', 'PE', 'SR', 'UY', 'VE');
		if(isset($_POST['adrotate_geo_southeastasia'])) $countries_southeastasia = array('AU', 'BN', 'KH', 'TL', 'ID', 'LA', 'MY', 'MM', 'NZ', 'PH', 'SG', 'TH', 'VN');
	
		// advertiser
		$advertiser = $budget = $crate = $irate = 0;
		if(isset($_POST['adrotate_advertiser'])) $advertiser = $_POST['adrotate_advertiser'];
		if(isset($_POST['adrotate_budget'])) $budget = strip_tags(trim($_POST['adrotate_budget'], "\t\n "));
		if(isset($_POST['adrotate_crate'])) $crate = strip_tags(trim($_POST['adrotate_crate'], "\t\n "));
		if(isset($_POST['adrotate_irate'])) $irate = strip_tags(trim($_POST['adrotate_irate'], "\t\n "));

		if(current_user_can('adrotate_ad_manage')) {
			if(strlen($title) < 1) {
				$title = 'Advert '.$id;
			}
	
			// Clean up bannercode
			if(preg_match("/%ID%/", $bannercode)) $bannercode = str_replace('%ID%', '%id%', $bannercode);
			if(preg_match("/%ASSET%/", $bannercode)) $bannercode = str_replace('%ASSET%', '%asset%', $bannercode);
			if(preg_match("/%IMAGE%/", $bannercode)) $bannercode = str_replace('%IMAGE%', '%image%', $bannercode);
			if(preg_match("/%TITLE%/", $bannercode)) $bannercode = str_replace('%TITLE%', '%title%', $bannercode);
			if(preg_match("/%RANDOM%/", $bannercode)) $bannercode = str_replace('%RANDOM%', '%random%', $bannercode);

			// Sort out start dates
			if(strlen($start_date) > 0) {
				list($start_day, $start_month, $start_year) = explode('-', $start_date);
			} else {
				$start_year = $start_month = $start_day = 0;
			}

			if(($start_year > 0 AND $start_month > 0 AND $start_day > 0) AND strlen($start_hour) == 0) $start_hour = '00';
			if(($start_year > 0 AND $start_month > 0 AND $start_day > 0) AND strlen($start_minute) == 0) $start_minute = '00';
	
			if($start_month > 0 AND $start_day > 0 AND $start_year > 0) {
				$start_date = mktime($start_hour, $start_minute, 0, $start_month, $start_day, $start_year);
			} else {
				$start_date = 0;
			}
			
			// Sort out end dates
			if(strlen($end_date) > 0) {
				list($end_day, $end_month, $end_year) = explode('-', $end_date);
			} else {
				$end_year = $end_month = $end_day = 0;
			}

			if(($end_year > 0 AND $end_month > 0 AND $end_day > 0) AND strlen($end_hour) == 0) $end_hour = '00';
			if(($end_year > 0 AND $end_month > 0 AND $end_day > 0) AND strlen($end_minute) == 0) $end_minute = '00';
	
			if($end_month > 0 AND $end_day > 0 AND $end_year > 0) {
				$end_date = mktime($end_hour, $end_minute, 0, $end_month, $end_day, $end_year);
			} else {
				$end_date = 0;
			}
			
			// Enddate is too early, reset to default
			if($end_date <= $start_date) $end_date = $start_date + 7257600; // 84 days (12 weeks)
	
			// Sort out click and impressions restrictions
			if(strlen($maxclicks) < 1 OR !is_numeric($maxclicks)) $maxclicks = 0;
			if(strlen($maxshown) < 1 OR !is_numeric($maxshown))	$maxshown = 0;
	
			// Impression Spread
			if(isset($spread) AND strlen($spread) != 0 AND $maxshown >= 100) {
				$spread = 'Y';
			} else {
				$spread = 'N';
			}

			// Save the schedule to the DB
			if($start_date > 0 AND $end_date > 0) {
				$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Schedule for ad '.$id, 'starttime' => $start_date, 'stoptime' => $end_date, 'maxclicks' => $maxclicks, 'maximpressions' => $maxshown, 'spread' => $spread));
				$schedules[] = $wpdb->insert_id;
			}

			if(isset($tracker) AND strlen($tracker) != 0) $tracker = 'Y';
				else $tracker = 'N';
			if(isset($show_everyone) AND strlen($show_everyone) != 0) $show_everyone = 'Y';
				else $show_everyone = 'N';
			if(isset($desktop) AND strlen($desktop) != 0) $desktop = 'Y';
				else $desktop = 'N';
			if(isset($mobile) AND strlen($mobile) != 0) $mobile = 'Y';
				else $mobile = 'N';
			if(isset($tablet) AND strlen($tablet) != 0) $tablet = 'Y';
				else $tablet = 'N';
			if(isset($os_ios) AND strlen($os_ios) != 0) $os_ios = 'Y';
				else $os_ios = 'N';
			if(isset($os_android) AND strlen($os_android) != 0) $os_android = 'Y';
				else $os_android = 'N';
			if(isset($os_other) AND strlen($os_other) != 0) $os_other = 'Y';
				else $os_other = 'N';
			if(isset($autodelete) AND strlen($autodelete) != 0) $autodelete = 'Y';
				else $autodelete = 'N';
			
			// Rate and Budget settings
			if(!is_numeric($crate) OR $crate < 0 OR $crate > 999) $crate = 0;
			if(!is_numeric($irate) OR $irate < 0 OR $irate > 999) $irate = 0;
			if($advertiser == 0 AND $crate == 0 AND $irate == 0) $budget = 0;
			
			$budget = number_format($budget, 4, '.', '');
			$crate = number_format($crate, 4, '.', '');
			$irate = number_format($irate, 4, '.', '');
			
			// Determine image settings ($image_field has priority!)
			if(strlen($image_field) > 1) {
				$imagetype = "field";
				$image = $image_field;
			} else if(strlen($image_dropdown) > 1) {
				$imagetype = "dropdown";
				$image = WP_CONTENT_URL."/%folder%/".$image_dropdown;
			} else {
				$imagetype = "";
				$image = "";
			}
	
			// Geo Targeting
			if(strlen($cities) > 0) {
				$cities = explode(",", strtolower($cities));
				foreach($cities as $key => $value) {
					$cities_clean[] = trim($value);
					unset($value);
				}
				unset($cities);
				$cities = serialize($cities_clean);
			} else {
				$cities = serialize(array());
			}

			$countries = array_merge($countries, $countries_westeurope, $countries_easteurope, $countries_northamerica, $countries_southamerica, $countries_southeastasia);
			$countries = array_unique($countries);
			if(count($countries) == 0) {
				$countries = serialize(array());
			} else {
				foreach($countries as $key => $value) {
					$countries_clean[] = trim($value);
					unset($value);
				}
				unset($countries);
				$countries = serialize($countries_clean);
			}

			// Save the ad to the DB
			$wpdb->update($wpdb->prefix.'adrotate', array('title' => $title, 'bannercode' => $bannercode, 'updated' => $thetime, 'author' => $author, 'imagetype' => $imagetype, 'image' => $image, 'tracker' => $tracker, 'show_everyone' => $show_everyone, 'desktop' => $desktop, 'mobile' => $mobile, 'tablet' => $tablet, 'os_ios' => $os_ios, 'os_android' => $os_android, 'os_other' => $os_other, 'type' => $active, 'weight' => $weight, 'autodelete' => $autodelete, 'budget' => $budget, 'crate' => $crate, 'irate' => $irate, 'cities' => $cities, 'countries' => $countries), array('id' => $id));

			// Fetch group records for the ad
			$groupmeta = $wpdb->get_results($wpdb->prepare("SELECT `group` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `user` = 0 AND `schedule` = 0;", $id));
			$group_array = array();
			foreach($groupmeta as $meta) {
				$group_array[] = $meta->group;
				unset($meta);
			}
			
			// Add new groups to this ad
			$insert = array_diff($groups, $group_array);
			foreach($insert as &$value) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => $value, 'user' => 0, 'schedule' => 0));
			}
			unset($insert, $value);
			
			// Remove groups from this ad
			$delete = array_diff($group_array, $groups);
			foreach($delete as &$value) {
				$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = %d AND `user` = 0 AND `schedule` = 0;", $id, $value)); 
			}
			unset($delete, $value, $groupmeta, $group_array);
	
			// Fetch schedules for the ad
			$schedulemeta = $wpdb->get_results($wpdb->prepare("SELECT `schedule` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` = 0;", $id));
			$schedule_array = array();
			foreach($schedulemeta as $meta) {
				$schedule_array[] = $meta->schedule;
				unset($meta);
			}
			
			// Add new schedules to this ad
			$insert = array_diff($schedules, $schedule_array);
			foreach($insert as &$value) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => 0, 'user' => 0, 'schedule' => $value));
			}
			unset($insert, $value);
			
			// Remove schedules from this ad
			$delete = array_diff($schedule_array, $schedules);
			foreach($delete as &$value) {
				$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` = 0 AND `schedule` = %d;", $id, $value)); 
			}
			unset($delete, $value, $schedulemeta, $schedule_array);

			// Fetch records for the ad, see if a publisher is set
			$linkmeta = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` > 0 AND `schedule` = 0;", $id));
	
			// Add/update/remove advertiser on this ad
			if($linkmeta == 0 AND $advertiser > 0) $wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => 0, 'user' => $advertiser, 'schedule' => 0));
			if($linkmeta == 1 AND $advertiser > 0) $wpdb->query($wpdb->prepare("UPDATE `{$wpdb->prefix}adrotate_linkmeta` SET `user` = $advertiser WHERE `ad` = %d AND `group` = 0 AND `schedule` = 0;", $id)); 
			if($linkmeta == 1 AND $advertiser == 0) $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `schedule` = 0;", $id)); 

			// Verify ad
			if($active == "active") {
				adrotate_evaluate_ads();
			}

			// Archive stats?
			if($active == "archived") {
				adrotate_archive_stats($id);
				adrotate_return('adrotate-ads', 220, array('view' => 'archive'));
				exit;
			}

			adrotate_return('adrotate-ads', 200);
			exit;
		} else {
			adrotate_return('adrotate-ads', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_group
 Purpose:   Save provided data for groups, update linkmeta where required
 Since:		0.4
-------------------------------------------------------------*/
function adrotate_insert_group() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_group')) {
		$action = $id = $name = $modus = '';
		if(isset($_POST['adrotate_action'])) $action = $_POST['adrotate_action'];
		if(isset($_POST['adrotate_id'])) $id = $_POST['adrotate_id'];
		if(isset($_POST['adrotate_groupname'])) $name = strip_tags(trim($_POST['adrotate_groupname'], "\t\n "));
		if(isset($_POST['adrotate_modus'])) $modus = strip_tags(trim($_POST['adrotate_modus'], "\t\n "));

		$rows = $columns = $adwidth = $adheight = $admargin = $adspeed = '';
		if(isset($_POST['adrotate_gridrows'])) $rows = strip_tags(trim($_POST['adrotate_gridrows'], "\t\n "));
		if(isset($_POST['adrotate_gridcolumns'])) $columns = strip_tags(trim($_POST['adrotate_gridcolumns'], "\t\n "));
		if(isset($_POST['adrotate_adwidth'])) $adwidth = strip_tags(trim($_POST['adrotate_adwidth'], "\t\n "));
		if(isset($_POST['adrotate_adheight'])) $adheight = strip_tags(trim($_POST['adrotate_adheight'], "\t\n "));
		if(isset($_POST['adrotate_admargin_top'])) $admargin_top = strip_tags(trim($_POST['adrotate_admargin_top'], "\t\n "));
		if(isset($_POST['adrotate_admargin_bottom'])) $admargin_bottom = strip_tags(trim($_POST['adrotate_admargin_bottom'], "\t\n "));
		if(isset($_POST['adrotate_admargin_left'])) $admargin_left = strip_tags(trim($_POST['adrotate_admargin_left'], "\t\n "));
		if(isset($_POST['adrotate_admargin_right'])) $admargin_right = strip_tags(trim($_POST['adrotate_admargin_right'], "\t\n "));
		if(isset($_POST['adrotate_adspeed'])) $adspeed = strip_tags(trim($_POST['adrotate_adspeed'], "\t\n "));
		if(isset($_POST['adrotate_repeat_impressions'])) $repeat_impressions = strip_tags(trim($_POST['adrotate_repeat_impressions'], "\t\n "));

		$fallback = $ads = $geo = $mobile = $align = '';
		if(isset($_POST['adrotate_geo'])) $geo = $_POST['adrotate_geo'];
		if(isset($_POST['adrotate_mobile'])) $mobile = $_POST['adrotate_mobile'];
		if(isset($_POST['adrotate_fallback'])) $fallback = $_POST['adrotate_fallback'];
		if(isset($_POST['adselect'])) $ads = $_POST['adselect'];
		if(isset($_POST['adrotate_align'])) $align = strip_tags(trim($_POST['adrotate_align'], "\t\n "));

		$categories = $category_loc = $category_par = $pages = $page_loc = $page_par = '';
		if(isset($_POST['adrotate_categories'])) $categories = $_POST['adrotate_categories'];
		if(isset($_POST['adrotate_cat_location'])) $category_loc = $_POST['adrotate_cat_location'];
		if(isset($_POST['adrotate_cat_paragraph'])) $category_par = $_POST['adrotate_cat_paragraph'];
		if(isset($_POST['adrotate_pages'])) $pages = $_POST['adrotate_pages'];
		if(isset($_POST['adrotate_page_location'])) $page_loc = $_POST['adrotate_page_location'];
		if(isset($_POST['adrotate_page_paragraph'])) $page_par = $_POST['adrotate_page_paragraph'];

		$wrapper_before = $wrapper_after = '';
		if(isset($_POST['adrotate_wrapper_before'])) $wrapper_before = trim($_POST['adrotate_wrapper_before'], "\t\n ");
		if(isset($_POST['adrotate_wrapper_after'])) $wrapper_after = trim($_POST['adrotate_wrapper_after'], "\t\n ");
	
		if(current_user_can('adrotate_group_manage')) {
			if(strlen($name) < 1) $name = 'Group '.$id;

			if($modus < 0 OR $modus > 2) $modus = 0;
			if($adspeed < 0 OR $adspeed > 99999) $adspeed = 6000;
			if($align < 0 OR $align > 3) $align = 0;

			if(isset($repeat_impressions) AND strlen($repeat_impressions) != 0) $repeat_impressions = "Y";
				else $repeat_impressions = "N";
			if(is_numeric($geo)) $geo = 1;
				else $geo = 0;
			if(is_numeric($mobile)) $mobile = 1;
				else $mobile = 0;
			if(!is_numeric($fallback) OR $fallback == $id) $fallback = 0;
			
			// Sort out block shape
			if($rows < 1 OR $rows == '' OR !is_numeric($rows)) $rows = 2;
			if($columns < 1 OR $columns == '' OR !is_numeric($columns)) $columns = 2;
			if((is_numeric($adwidth) AND $adwidth < 1 OR $adwidth > 9999) OR $adwidth == '' OR (!is_numeric($adwidth) AND $adwidth != 'auto')) $adwidth = '125';
			if((is_numeric($adheight) AND $adheight < 1 OR $adheight > 9999) OR $adheight == '' OR (!is_numeric($adheight) AND $adheight != 'auto')) $adheight = '125';
			if($admargin_top < 0 OR $admargin_top > 99 OR $admargin_top == '' OR !is_numeric($admargin_top)) $admargin_top = 0;
			if($admargin_bottom < 0 OR $admargin_bottom > 99 OR $admargin_bottom == '' OR !is_numeric($admargin_bottom)) $admargin_bottom = 0;
			if($admargin_left < 0 OR $admargin_left > 99 OR $admargin_left == '' OR !is_numeric($admargin_left)) $admargin_left = 0;
			if($admargin_right < 0 OR $admargin_right > 99 OR $admargin_right == '' OR !is_numeric($admargin_right)) $admargin_right = 0;

			// Categories
			if(!is_array($categories)) $categories = array();
			$category = '';
			foreach($categories as $key => $value) {
				$category = $category.','.$value;
			}
			$category = trim($category, ',');
			if(strlen($category) < 1) $category = '';

			
			if($category_par > 0) $category_loc = 4;
			if($category_loc != 4) $category_par = 0;
			
			// Pages
			if(!is_array($pages)) $pages = array();
			$page = '';
			foreach($pages as $key => $value) {
				$page = $page.','.$value;
			}
			$page = trim($page, ',');
			if(strlen($page) < 1) $page = '';
			
			if($page_par > 0) $page_loc = 4;
			if($page_loc != 4) $page_par = 0;

			// Fetch records for the group
			$linkmeta = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = %d AND `user` = 0;", $id));
			foreach($linkmeta as $meta) {
				$meta_array[] = $meta->ad;
			}
			
			if(empty($meta_array)) $meta_array = array();
			if(empty($ads)) $ads = array();
	
			// Add new ads to this group
			$insert = array_diff($ads,$meta_array);
			foreach($insert as &$value) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $value, 'group' => $id, 'user' => 0));
			}
			unset($value);
			
			// Remove ads from this group
			$delete = array_diff($meta_array,$ads);
			foreach($delete as &$value) {
				$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = %d AND `user` = 0;", $value, $id)); 
			}
			unset($value);
	
			// Update the group itself
			$wpdb->update($wpdb->prefix.'adrotate_groups', array('name' => $name, 'modus' => $modus, 'fallback' => $fallback, 'cat' => $category, 'cat_loc' => $category_loc,  'cat_par' => $category_par, 'page' => $page, 'page_loc' => $page_loc, 'page_par' => $page_par, 'mobile' => $mobile, 'geo' => $geo, 'wrapper_before' => $wrapper_before, 'wrapper_after' => $wrapper_after, 'align' => $align, 'gridrows' => $rows, 'gridcolumns' => $columns, 'admargin' => $admargin_top, 'admargin_bottom' => $admargin_bottom, 'admargin_left' => $admargin_left, 'admargin_right' => $admargin_right, 'adwidth' => $adwidth, 'adheight' => $adheight, 'adspeed' => $adspeed, 'repeat_impressions' => $repeat_impressions), array('id' => $id));

			// Determine GeoLocation Library requirement
			$geo_count = $wpdb->get_var("SELECT COUNT(*) as `total` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `geo` = 1;");
			update_option('adrotate_geo_required', $geo_count);

			// Determine Dynamic Library requirement
			$dynamic_count = $wpdb->get_var("SELECT COUNT(*) as `total` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `modus` = 1;");
			update_option('adrotate_dynamic_required', $dynamic_count);
	
			// Generate CSS for group
			if($align == 0) { // None
				$group_align = '';
			} else if($align == 1) { // Left
				$group_align = ' float:left; clear:left;';
			} else if($align == 2) { // Right
				$group_align = ' float:right; clear:right;';
			} else if($align == 3) { // Center
				$group_align = ' margin: 0 auto;';
			}

			$output_css = "";
			if($modus == 0 AND ($admargin_top > 0 OR $admargin_right > 0 OR $admargin_bottom > 0 OR $admargin_left > 0 OR $align > 0)) { // Single ad group
				if($align < 3) {
					$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$id." { margin:".$admargin_top."px ".$admargin_right."px ".$admargin_bottom."px ".$admargin_left."px;".$group_align." }\n";
				} else {
					$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$id." { ".$group_align." }\n";	
				}
			}
	
			if($modus == 1) { // Dynamic group
				if($adwidth != 'auto') {
					$width = "width:100%; max-width:".$adwidth."px;";
				} else {
					$width = "width:auto;";
				}
				
				if($adheight != 'auto') {
					$height = "height:100%; max-height:".$adheight."px;";
				} else {
					$height = "height:auto;";
				}

				if($align < 3) {
					$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$id." { margin:".$admargin_top."px ".$admargin_right."px ".$admargin_bottom."px ".$admargin_left."px;".$width." ".$height.$group_align." }\n";
				} else {
					$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$id." { ".$width." ".$height.$group_align." }\n";	
				}

				unset($width_sum, $width, $height_sum, $height);
			}
	
			if($modus == 2) { // Block group
				if($adwidth != 'auto') {
					$width_sum = $columns * ($admargin_left + $adwidth + $admargin_right);
					$grid_width = "min-width:".$admargin_left."px; max-width:".$width_sum."px;";
				} else {
					$grid_width = "width:auto;";
				}
				
				$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-".$id." { ".$grid_width.$group_align." }\n";
				$output_css .= "\t.b".$adrotate_config['adblock_disguise']."-".$id." { margin:".$admargin_top."px ".$admargin_right."px ".$admargin_bottom."px ".$admargin_left."px; }\n";
				unset($width_sum, $grid_width, $height_sum, $grid_height);
			}
			
			$group_css = get_option('adrotate_group_css');
			$group_css[$id] = $output_css;
			update_option('adrotate_group_css', $group_css);
			// End CSS

			adrotate_return('adrotate-groups', 201);
			exit;
		} else {
			adrotate_return('adrotate-groups', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_schedule
 Purpose:   Prepare input form on saving new or updated schedules
 Since:		3.8.9 
-------------------------------------------------------------*/
function adrotate_insert_schedule() {
	global $wpdb;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_schedule')) {
		// Mandatory
		$id = $ad = '';
		if(isset($_POST['adrotate_id'])) $id = esc_attr($_POST['adrotate_id']);
		if(isset($_POST['adrotate_schedulename'])) $name = esc_attr($_POST['adrotate_schedulename']);

		// Schedule variables
		$start_date = $start_hour = $start_minute = $end_date = $end_hour = $end_minute = '';
		if(isset($_POST['adrotate_start_date'])) $start_date = strip_tags(trim($_POST['adrotate_start_date'], "\t\n "));
		if(isset($_POST['adrotate_start_hour'])) $start_hour = strip_tags(trim($_POST['adrotate_start_hour'], "\t\n "));
		if(isset($_POST['adrotate_start_minute'])) $start_minute = strip_tags(trim($_POST['adrotate_start_minute'], "\t\n "));
		if(isset($_POST['adrotate_end_date'])) $end_date = strip_tags(trim($_POST['adrotate_end_date'], "\t\n "));
		if(isset($_POST['adrotate_end_hour'])) $end_hour = strip_tags(trim($_POST['adrotate_end_hour'], "\t\n "));
		if(isset($_POST['adrotate_end_minute'])) $end_minute = strip_tags(trim($_POST['adrotate_end_minute'], "\t\n "));
	
		$maxclicks = $maxshown = $spread = $autodelete = '';
		if(isset($_POST['adrotate_maxclicks'])) $maxclicks = strip_tags(trim($_POST['adrotate_maxclicks'], "\t\n "));
		if(isset($_POST['adrotate_maxshown'])) $maxshown = strip_tags(trim($_POST['adrotate_maxshown'], "\t\n "));	
		if(isset($_POST['adrotate_spread'])) $spread = strip_tags(trim($_POST['adrotate_spread'], "\t\n "));	
		if(isset($_POST['adrotate_autodelete'])) $autodelete = strip_tags($_POST['adrotate_autodelete']);

		$start_day_hour = $start_day_minute = $end_day_hour = $end_day_minute = $day_mon = $day_tue = $day_wed = $day_thu = $day_fri = $day_sat = $day_sun = '';
		if(isset($_POST['adrotate_start_day_hour'])) $start_day_hour = strip_tags(trim($_POST['adrotate_start_day_hour'], "\t\n "));	
		if(isset($_POST['adrotate_start_day_minute'])) $start_day_minute = strip_tags(trim($_POST['adrotate_start_day_minute'], "\t\n "));	
		if(isset($_POST['adrotate_end_day_hour'])) $end_day_hour = strip_tags(trim($_POST['adrotate_end_day_hour'], "\t\n "));	
		if(isset($_POST['adrotate_end_day_minute'])) $end_day_minute = strip_tags(trim($_POST['adrotate_end_day_minute'], "\t\n "));	
		if(isset($_POST['adrotate_mon'])) $day_mon = strip_tags(trim($_POST['adrotate_mon'], "\t\n "));	
		if(isset($_POST['adrotate_tue'])) $day_tue = strip_tags(trim($_POST['adrotate_tue'], "\t\n "));	
		if(isset($_POST['adrotate_wed'])) $day_wed = strip_tags(trim($_POST['adrotate_wed'], "\t\n "));	
		if(isset($_POST['adrotate_thu'])) $day_thu = strip_tags(trim($_POST['adrotate_thu'], "\t\n "));	
		if(isset($_POST['adrotate_fri'])) $day_fri = strip_tags(trim($_POST['adrotate_fri'], "\t\n "));	
		if(isset($_POST['adrotate_sat'])) $day_sat = strip_tags(trim($_POST['adrotate_sat'], "\t\n "));	
		if(isset($_POST['adrotate_sun'])) $day_sun = strip_tags(trim($_POST['adrotate_sun'], "\t\n "));	

		$ads = '';
		if(isset($_POST['adselect'])) $ads = $_POST['adselect'];
	
		if(current_user_can('adrotate_schedule_manage')) {	
			if(strlen($name) < 1) {
				$name = 'Schedule '.$id;
			}
	
			// Sort out start dates
			if(strlen($start_date) > 0) {
				list($start_day, $start_month, $start_year) = explode('-', $start_date);
			} else {
				$start_year = $start_month = $start_day = 0;
			}

			if(($start_year > 0 AND $start_month > 0 AND $start_day > 0) AND strlen($start_hour) == 0) $start_hour = '00';
			if(($start_year > 0 AND $start_month > 0 AND $start_day > 0) AND strlen($start_minute) == 0) $start_minute = '00';
	
			if($start_month > 0 AND $start_day > 0 AND $start_year > 0) {
				$start_date = mktime($start_hour, $start_minute, 0, $start_month, $start_day, $start_year);
			} else {
				$start_date = 0;
			}
			
			// Sort out end dates
			if(strlen($end_date) > 0) {
				list($end_day, $end_month, $end_year) = explode('-', $end_date);
			} else {
				$end_year = $end_month = $end_day = 0;
			}

			if(($end_year > 0 AND $end_month > 0 AND $end_day > 0) AND strlen($end_hour) == 0) $end_hour = '00';
			if(($end_year > 0 AND $end_month > 0 AND $end_day > 0) AND strlen($end_minute) == 0) $end_minute = '00';
	
			if($end_month > 0 AND $end_day > 0 AND $end_year > 0) {
				$end_date = mktime($end_hour, $end_minute, 0, $end_month, $end_day, $end_year);
			} else {
				$end_date = 0;
			}
			
			// Enddate is too early, reset to default
			if($end_date <= $start_date) $end_date = $start_date + 7257600; // 84 days (12 weeks)
	
			// Sort out click and impressions restrictions
			if(strlen($maxclicks) < 1 OR !is_numeric($maxclicks)) $maxclicks = 0;
			if(strlen($maxshown) < 1 OR !is_numeric($maxshown))	$maxshown = 0;
			
			// Impression Spread
			if(isset($spread) AND strlen($spread) != 0 AND $maxshown >= 100) {
				$spread = 'Y';
			} else {
				$spread = 'N';
			}

			// Auto delete
			if(isset($autodelete) AND strlen($autodelete) != 0) {
				$autodelete = 'Y';
			} else {
				$autodelete = 'N';
			}

			// Day schedules
			if(!isset($start_day_hour) AND $start_day_hour < 1) $start_day_hour = '00';
			if(!isset($start_day_minute) AND $start_day_minute < 1) $start_day_minute = '00';
			$day_start = str_pad($start_day_hour, 2, 0, STR_PAD_LEFT).str_pad($start_day_minute, 2, 0, STR_PAD_LEFT);

			if(!isset($end_day_hour) AND $end_day_hour < 1) $end_day_hour = '00';
			if(!isset($end_day_minute) AND $end_day_minute < 1) $end_day_minute = '00';
			$day_stop = str_pad($end_day_hour, 2, 0, STR_PAD_LEFT).str_pad($end_day_minute, 2, 0, STR_PAD_LEFT);

			if(isset($day_mon) AND strlen($day_mon) != 0) $day_mon = 'Y';
				else $day_mon = 'N';
			if(isset($day_tue) AND strlen($day_tue) != 0) $day_tue = 'Y';
				else $day_tue = 'N';
			if(isset($day_wed) AND strlen($day_wed) != 0) $day_wed = 'Y';
				else $day_wed = 'N';
			if(isset($day_thu) AND strlen($day_thu) != 0) $day_thu = 'Y';
				else $day_thu = 'N';
			if(isset($day_fri) AND strlen($day_fri) != 0) $day_fri = 'Y';
				else $day_fri = 'N';
			if(isset($day_sat) AND strlen($day_sat) != 0) $day_sat = 'Y';
				else $day_sat = 'N';
			if(isset($day_sun) AND strlen($day_sun) != 0) $day_sun = 'Y';
				else $day_sun = 'N';

			// Fetch records for the schedule
			$linkmeta = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` = %d AND `user` = 0;", $id));
			foreach($linkmeta as $meta) {
				$meta_array[] = $meta->ad;
			}
			
			if(empty($meta_array)) $meta_array = array();
			if(empty($ads)) $ads = array();

			// Add new ads to this schedule
			$insert = array_diff($ads, $meta_array);
			foreach($insert as &$value) {
				$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $value, 'group' => 0, 'user' => 0, 'schedule' => $id));
			}
			unset($value);
			
			// Remove ads from this schedule
			$delete = array_diff($meta_array, $ads);
			foreach($delete as &$value) {
				$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` = 0 AND `schedule` = %d;", $value, $id)); 
			}
			unset($value);

			// Save the schedule to the DB
			$wpdb->update($wpdb->prefix.'adrotate_schedule', array('name' => $name, 'starttime' => $start_date, 'stoptime' => $end_date, 'maxclicks' => $maxclicks, 'maximpressions' => $maxshown, 'spread' => $spread, 'daystarttime' => $day_start, 'daystoptime' => $day_stop, 'day_mon' => $day_mon, 'day_tue' => $day_tue, 'day_wed' => $day_wed, 'day_thu' => $day_thu, 'day_fri' => $day_fri, 'day_sat' => $day_sat, 'day_sun' => $day_sun, 'autodelete' => $autodelete), array('id' => $id));

			// Verify all ads
			adrotate_prepare_evaluate_ads(false);

			adrotate_return('adrotate-schedules', 217);
			exit;
		} else {
			adrotate_return('adrotate-schedules', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_media
 Purpose:   Prepare input form on saving new or updated banners
 Since:		0.1 
-------------------------------------------------------------*/
function adrotate_insert_media() {
	global $wpdb, $adrotate_config;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_media')) {
		if(current_user_can('adrotate_ad_manage')) {
			
			if($_FILES["adrotate_image"]["size"] > 0 AND $_FILES["adrotate_image"]["size"] <= 512000) {
				$allowedExts = array("jpg", "jpeg", "gif", "png", "flv", "swf", "html", "js");
				$filename = adrotate_sanitize_file_name($_FILES["adrotate_image"]["name"]);
				$extension = explode(".", $filename);
				$extension = end($extension);
				$location = esc_attr($_POST['adrotate_image_location']);
				$image_path = WP_CONTENT_DIR."/".$location."/";

				if(
					(
						//Images
						$_FILES["adrotate_image"]["type"] == "image/gif"
						OR $_FILES["adrotate_image"]["type"] == "image/jpeg" 
						OR $_FILES["adrotate_image"]["type"] == "image/pjpeg"
						OR $_FILES["adrotate_image"]["type"] == "image/jpg" 
						OR $_FILES["adrotate_image"]["type"] == "image/png"
						
						// HTML5 Assets
						OR $_FILES["adrotate_image"]["type"] == "text/html"
						OR $_FILES["adrotate_image"]["type"] == "application/x-javascript"
						OR $_FILES["adrotate_image"]["type"] == "application/javascript"
						OR $_FILES["adrotate_image"]["type"] == "text/javascript"
						
						// Flash :(
						OR $_FILES["adrotate_image"]["type"] == "application/x-shockwave-flash"
					)
					AND in_array($extension, $allowedExts)
				) {
					if ($_FILES["adrotate_image"]["error"] > 0) {
						if($_FILES["adrotate_image"]["error"] == 1 OR $_FILES["adrotate_image"]["error"] == 2) $errorcode = 511;
						else if($_FILES["adrotate_image"]["error"] == 3) $errorcode = 506;
						else if($_FILES["adrotate_image"]["error"] == 4) $errorcode = 506;
						else if($_FILES["adrotate_image"]["error"] == 6 OR $_FILES["adrotate_image"]["error"] == 7) $errorcode = 506;
						else $errorcode = '';
						adrotate_return('adrotate-media', $errorcode); // Other error
					} else {
/*
echo '<pre>';
print_r($image_path);
print_r($filename);
echo '</pre>';
die();
*/
						if(!move_uploaded_file($_FILES["adrotate_image"]["tmp_name"], $image_path . $filename)) {
							adrotate_return('adrotate-media', 506); // Upload error
						}
						adrotate_return('adrotate-media', 202); // Success
					}
				} else {
					adrotate_return('adrotate-media', 510); // Filetype
				}
			} else {
				adrotate_return('adrotate-media', 511); // Size
			}
		} else {
			adrotate_return('adrotate-media', 500); // No access/permission
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_insert_advertiser
 Purpose:   Prepare input form on saving new or updated schedules
 Since:		4.0
-------------------------------------------------------------*/
function adrotate_insert_advertiser() {
	global $wpdb;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_save_advertiser')) {
		// Mandatory
		$id = $notes = $can_edit = $can_mobile = $can_geo = '';
		if(isset($_POST['adrotate_user'])) $user = esc_attr($_POST['adrotate_user']);
		if(isset($_POST['adrotate_notes'])) $notes = strip_tags(trim($_POST['adrotate_notes'], "\t\n "));
		if(isset($_POST['adrotate_can_edit'])) $can_edit = esc_attr($_POST['adrotate_can_edit']);
		if(isset($_POST['adrotate_can_mobile'])) $can_mobile = esc_attr($_POST['adrotate_can_mobile']);
		if(isset($_POST['adrotate_can_geo'])) $can_geo = esc_attr($_POST['adrotate_can_geo']);
	
		if(current_user_can('adrotate_advertiser_manage')) {	
			// User
			if(!is_numeric($user)) $user = 0;

			// Premissions
			if(isset($can_edit) AND strlen($can_edit) != 0) {
				$can_edit = 'Y';
			} else {
				$can_edit = 'N';
			}
			if(isset($can_mobile) AND strlen($can_mobile) != 0) {
				$can_mobile = 'Y';
			} else {
				$can_mobile = 'N';
			}
			if(isset($can_geo) AND strlen($can_geo) != 0) {
				$can_geo = 'Y';
			} else {
				$can_geo = 'N';
			}
			update_user_meta($user, 'adrotate_permissions', array('edit' => $can_edit, 'mobile' => $can_mobile, 'geo' => $can_geo));
			update_user_meta($user, 'adrotate_notes', $notes);

			adrotate_return('adrotate-advertisers', 205, array('view' => 'profile', 'user' => $user));
			exit;
		} else {
			adrotate_return('adrotate-advertisers', 500);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_request_action
 Purpose:   Prepare action for banner or group from database
 Since:		2.2
-------------------------------------------------------------*/
function adrotate_request_action() {
	global $adrotate_config;

	$banner_ids = $group_ids = $schedule_ids = '';

	if(wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_active') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_disable') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_error') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_queue') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_reject') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_archive') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_ads_trash') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_groups') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_bulk_schedules')) {
		$banner_ids = $group_ids = $schedule_ids = '';
		if(!empty($_POST['adrotate_id'])) $banner_ids = array($_POST['adrotate_id']);
		if(!empty($_POST['bannercheck'])) $banner_ids = $_POST['bannercheck'];
		if(!empty($_POST['groupcheck'])) $group_ids = $_POST['groupcheck'];
		if(!empty($_POST['schedulecheck'])) $schedule_ids = $_POST['schedulecheck'];
		
		// Determine which kind of action to use
		if(!empty($_POST['adrotate_action'])) {
			// Default action call
			$actions = $_POST['adrotate_action'];
		}
		if(preg_match("/-/", $actions)) {
			list($action, $specific) = explode("-", $actions);	
		} else {
		   	$action = $actions;
		}

		if($banner_ids != '') {
			$return = 'adrotate-ads';
			if($action == 'export') {
				if(current_user_can('adrotate_ad_manage')) {
					adrotate_export($banner_ids);
					$result_id = 215;
				} else {
					adrotate_return($return, 500);
				}
			}
			foreach($banner_ids as $banner_id) {
				if($action == 'duplicate') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_duplicate($banner_id);
						$result_id = 219;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'deactivate') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_active($banner_id, 'deactivate');
						$result_id = 210;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'activate') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_active($banner_id, 'activate');
						$result_id = 211;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'archive') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_active($banner_id, 'archive');
						$result_id = 220;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'trash') {
					if(current_user_can('adrotate_ad_delete')) {
						adrotate_delete($banner_id, 'trash');
						$result_id = 221;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'restore') {
					if(current_user_can('adrotate_ad_delete')) {
						adrotate_active($banner_id, 'restore');
						$result_id = 222;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'delete') {
					if(current_user_can('adrotate_ad_delete')) {
						adrotate_delete($banner_id, 'banner');
						$result_id = 203;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'reset') {
					if(current_user_can('adrotate_ad_delete')) {
						adrotate_reset($banner_id);
						$result_id = 208;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'renew') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_renew($banner_id, $specific);
						$result_id = 209;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'weight') {
					if(current_user_can('adrotate_ad_manage')) {
						adrotate_weight($banner_id, $specific);
						$result_id = 214;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'approve') {
					if(current_user_can('adrotate_moderate_approve')) {
						adrotate_approve($banner_id);
						$return = 'adrotate-moderate';
						$result_id = 304;
					} else {
						adrotate_return('adrotate-moderate', 500);
					}
				}
				if($action == 'reject') {
					if(current_user_can('adrotate_moderate')) {
						adrotate_reject($banner_id);
						$return = 'adrotate-moderate';
						$result_id = 305;
					} else {
						adrotate_return('adrotate-moderate', 500);
					}
				}
				if($action == 'queue') {
					if(current_user_can('adrotate_moderate')) {
						adrotate_queue($banner_id);
						$return = 'adrotate-moderate';
						$result_id = 306;
					} else {
						adrotate_return('adrotate-moderate', 500);
					}
				}
			}
			// Verify all ads
			adrotate_prepare_evaluate_ads(false);
		}
		
		if($group_ids != '') {
			$return = 'adrotate-groups';
			foreach($group_ids as $group_id) {
				if($action == 'group_delete') {
					if(current_user_can('adrotate_group_delete')) {
						adrotate_delete($group_id, 'group');
						$result_id = 204;
					} else {
						adrotate_return($return, 500);
					}
				}
				if($action == 'group_delete_banners') {
					if(current_user_can('adrotate_group_delete')) {
						adrotate_delete($group_id, 'bannergroup');
						$result_id = 213;
					} else {
						adrotate_return($return, 500);
					}
				}
			}
		}
	
		if($schedule_ids != '') {
			$return = 'adrotate-schedules';
			foreach($schedule_ids as $schedule_id) {
				if($action == 'schedule_delete') {
					if(current_user_can('adrotate_schedule_delete')) {
						adrotate_delete($schedule_id, 'schedule');
						$result_id = 218;
					} else {
						adrotate_return($return, 500);
					}
				}
			}
		}
		
		adrotate_return($return, $result_id);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_duplicate
 Purpose:   Duplicate a banner
 Since:		3.17
-------------------------------------------------------------*/
function adrotate_duplicate($id) {
	global $wpdb;

	if($id > 0) {
		$thetime = adrotate_now();

		$duplicate_id = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$id};");

		$wpdb->insert($wpdb->prefix.'adrotate', array('title' => $duplicate_id->title.' (Copy of #'.$id.')', 'bannercode' => $duplicate_id->bannercode, 'thetime' => $thetime, 'updated' => $thetime, 'author' => $duplicate_id->author, 'imagetype' => $duplicate_id->imagetype, 'image' => $duplicate_id->image, 'tracker' => $duplicate_id->tracker, 'desktop' => $duplicate_id->desktop, 'mobile' => $duplicate_id->mobile, 'tablet' => $duplicate_id->tablet, 'os_ios' => $duplicate_id->os_ios, 'os_android' => $duplicate_id->os_android, 'os_other' => $duplicate_id->os_other, 'type' => $duplicate_id->type, 'weight' => $duplicate_id->weight, 'budget' => $duplicate_id->budget, 'crate' => $duplicate_id->crate, 'irate' => $duplicate_id->irate, 'cities' => $duplicate_id->cities, 'countries' => $duplicate_id->countries));

		$new_id = $wpdb->insert_id;

		$duplicate_meta = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$id};");
		foreach($duplicate_meta as $meta) {
			$schedule = ($meta->schedule > 0) ? $meta->schedule : 0;
			$group = ($meta->group > 0) ? $meta->group : 0;
			$user = ($meta->user > 0) ? $meta->user : 0;
			$wpdb->insert("{$wpdb->prefix}adrotate_linkmeta", array('ad' => $new_id, 'group' => $group, 'user' => $user, 'schedule' => $schedule));
			unset($schedule, $user, $user);
		}
	}
}


/*-------------------------------------------------------------
 Name:      adrotate_delete
 Purpose:   Remove banner or group from database
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_delete($id, $what) {
	global $wpdb;

	$now = adrotate_now();

	if($id > 0) {
		if($what == 'banner') {
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_stats` WHERE `ad` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_stats_archive` WHERE `ad` = %d;", $id));
		} else if($what == 'trash') {
			$wpdb->update("{$wpdb->prefix}adrotate", array('type' => 'trash', 'updated' => $now), array('id' => $id));
		} else if($what == 'group') {
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_groups` WHERE `id` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_stats` WHERE `group` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_stats_archive` WHERE `group` = %d;", $id));
			
			// Remove CSS from group
			$group_css = get_option('adrotate_group_css');
			unset($group_css[$id]);
			update_option('adrotate_group_css', $group_css);
		} else if($what == 'bannergroup') {
			$linkmeta = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = %d AND `user` = '0' AND `schedule` = '0';", $id));
			foreach($linkmeta as $meta) {
				adrotate_delete($meta->ad, 'trash');
			}
			unset($linkmeta);
			adrotate_delete($id, 'group');
		} else if($what == 'schedule') {
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_schedule` WHERE `id` = %d;", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` = %d;", $id));
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_active
 Purpose:   Activate or Deactivate a banner
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_active($id, $what) {
	global $wpdb;

	if($id > 0) {
		if($what == 'deactivate') {
			$wpdb->update("{$wpdb->prefix}adrotate", array('type' => 'disabled'), array('id' => $id));
		}
		if ($what == 'activate') {
			// Determine status of ad 
			$adstate = adrotate_evaluate_ad($id);
			$adtype = ($adstate == 'error' OR $adstate == 'expired') ? 'error' : 'active';

			$wpdb->update("{$wpdb->prefix}adrotate", array('type' => $adtype), array('id' => $id));
		}
		if ($what == 'archive') {
			$wpdb->update("{$wpdb->prefix}adrotate", array('type' => 'archived'), array('id' => $id));
			adrotate_archive_stats($id);
		}
		if ($what == 'restore') {
			// Determine status of ad 
			$updated = adrotate_now();
			$adstate = adrotate_evaluate_ad($id);
			$adtype = ($adstate == 'error' OR $adstate == 'expired') ? 'error' : 'active';

			$wpdb->update("{$wpdb->prefix}adrotate", array('type' => $adtype, 'updated' => $updated), array('id' => $id));
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_reset
 Purpose:   Reset statistics for a banner
 Since:		2.2
-------------------------------------------------------------*/
function adrotate_reset($id) {
	global $wpdb;

	if($id > 0) {
		$wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->prefix}adrotate_stats` WHERE `ad` = %d", $id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_renew
 Purpose:   Renew the end date of a banner with a new schedule starting where the last ended
 Since:		2.2
-------------------------------------------------------------*/
function adrotate_renew($id, $howlong = 2592000) {
	global $wpdb;

	if($id > 0) {
		$schedule_id = $wpdb->get_var($wpdb->prepare("SELECT `schedule` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = %d AND `group` = 0 AND `user` = 0 ORDER BY `id` DESC LIMIT 1;", $id)); 
		if($schedule_id > 0) {
			$schedule = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}adrotate_schedule` WHERE `id` = %d ORDER BY `id` DESC LIMIT 1;", $schedule_id));
			$stoptime = $schedule->stoptime + $howlong;
			$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Schedule for ad '.$id, 'starttime' => $schedule->stoptime, 'stoptime' => $stoptime, 'maxclicks' => $schedule->maxclicks, 'maximpressions' => $schedule->maximpressions, 'spread' => $schedule->spread, 'daystarttime' => $schedule->daystarttime, 'daystoptime' => $schedule->daystoptime, 'day_mon' => $schedule->day_mon, 'day_tue' => $schedule->day_tue, 'day_wed' => $schedule->day_wed, 'day_thu' => $schedule->day_thu, 'day_fri' => $schedule->day_fri, 'day_sat' => $schedule->day_sat, 'day_sun' => $schedule->day_sun));
		} else {
			$now = adrotate_now();
			$stoptime = $now + $howlong;
			$wpdb->insert($wpdb->prefix.'adrotate_schedule', array('name' => 'Schedule for ad '.$id, 'starttime' => $now, 'stoptime' => $stoptime, 'maxclicks' => 0, 'maximpressions' => 0, 'spread' => 'N', 'daystarttime' => '0000', 'daystoptime' => '0000', 'day_mon' => 'Y', 'day_tue' => 'Y', 'day_wed' => 'Y', 'day_thu' => 'Y', 'day_fri' => 'Y', 'day_sat' => 'Y', 'day_sun' => 'Y'));
		}
		$wpdb->insert($wpdb->prefix.'adrotate_linkmeta', array('ad' => $id, 'group' => 0, 'user' => 0, 'schedule' => $wpdb->insert_id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_weight
 Purpose:   Renew the end date of a banner
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_weight($id, $weight = 6) {
	global $wpdb;

	if($id > 0) {
		$wpdb->update($wpdb->prefix.'adrotate', array('weight' => $weight), array('id' => $id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_approve
 Purpose:   Approve a queued banner
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_approve($id) {
	global $wpdb;

	if($id > 0) {
		$wpdb->update($wpdb->prefix.'adrotate', array('type' => 'active'), array('id' => $id));
		adrotate_notifications('approved', $id);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_reject
 Purpose:   Reject a queued banner
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_reject($id) {
	global $wpdb;

	if($id > 0) {
		$wpdb->update($wpdb->prefix.'adrotate', array('type' => 'reject'), array('id' => $id));
		adrotate_notifications('rejected', $id);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_queue
 Purpose:   Queue a rejected banner
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_queue($id) {
	global $wpdb;

	if($id > 0) {
		$wpdb->update($wpdb->prefix.'adrotate', array('type' => 'queue'), array('id' => $id));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_export
 Purpose:   Export selected banners
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_export($ids) {
	if(is_array($ids)) {
		adrotate_export_ads($ids);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_save_options
 Purpose:   Save options from dashboard
 Since:		0.1
-------------------------------------------------------------*/
function adrotate_save_options() {
	if(wp_verify_nonce($_POST['adrotate_nonce_settings'],'adrotate_settings')) {

		$settings_tab = esc_attr($_POST['adrotate_settings_tab']);

		if($settings_tab == 'general') {  
			$config = get_option('adrotate_config');

			$config['textwidget_shortcodes'] = (isset($_POST['adrotate_textwidget_shortcodes'])) ? 'Y' : 'N';
			$config['live_preview'] = (isset($_POST['adrotate_live_preview'])) ? 'N' : 'Y';
			$config['mobile_dynamic_mode'] = (isset($_POST['adrotate_mobile_dynamic_mode'])) ? 'Y' : 'N';
			$config['jquery'] = (isset($_POST['adrotate_jquery'])) ? 'Y' : 'N';
			$config['jsfooter'] = (isset($_POST['adrotate_jsfooter'])) ? 'Y' : 'N';
			$adblock_disguise = strtolower(trim($_POST['adrotate_adblock_disguise']));
			$config['adblock_disguise'] = (strlen($adblock_disguise) > 0) ? preg_replace('/[^a-z]/', '', strtolower(substr($adblock_disguise, 0, 6))) : "";
			$banner_folder = strtolower(trim($_POST['adrotate_banner_folder']));
			$config['banner_folder'] = (strlen($banner_folder) > 0) ? preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $banner_folder) : "banners";
			update_option('adrotate_config', $config);

			// Sort out crawlers
			$crawlers = explode(',', trim($_POST['adrotate_crawlers']));
			$new_crawlers = array();
			foreach($crawlers as $crawler) {
				$crawler = preg_replace('/[^a-zA-Z0-9\[\]\-_:; ]/i', '', trim($crawler));
				if(strlen($crawler) > 0) $new_crawlers[] = $crawler;
			}
			update_option('adrotate_crawlers', $new_crawlers);
		}

		if($settings_tab == 'notifications') {  
			$notifications = get_option('adrotate_notifications');

			// Notifications
			$notifications['notification_dash'] = (isset($_POST['adrotate_notification_dash'])) ? 'Y' : 'N';
			$notifications['notification_email'] = (isset($_POST['adrotate_notification_email'])) ? 'Y' : 'N';

			// Dashboard Notifications
			$notifications['notification_dash_expired'] = (isset($_POST['adrotate_notification_dash_expired'])) ? 'Y' : 'N';
			$notifications['notification_dash_soon'] = (isset($_POST['adrotate_notification_dash_soon'])) ? 'Y' : 'N';

			// Email notifications
			$notifications['notification_mail_geo'] = (isset($_POST['adrotate_notification_mail_geo'])) ? 'Y' : 'N';
			$notifications['notification_mail_status'] = (isset($_POST['adrotate_notification_mail_status'])) ? 'Y' : 'N';
			$notifications['notification_mail_queue'] = (isset($_POST['adrotate_notification_mail_queue'])) ? 'Y' : 'N';
			$notifications['notification_mail_approved'] = (isset($_POST['adrotate_notification_mail_approved'])) ? 'Y' : 'N';
			$notifications['notification_mail_rejected'] = (isset($_POST['adrotate_notification_mail_rejected'])) ? 'Y' : 'N';

			$notification_emails = $_POST['adrotate_notification_email_publisher'];
			if(strlen($notification_emails) > 0) {
				$notification_emails = explode(',', trim($notification_emails));
				foreach($notification_emails as $notification_email) {
					$notification_email = trim($notification_email);
					if(strlen($notification_email) > 0) {
		  				if(is_email($notification_email) ) {
							$clean_email[] = $notification_email;
						}
					}
				}
				$notifications['notification_email_publisher'] = array_unique(array_slice($clean_email, 0, 5));
				unset($clean_email);
			} else {
				$notifications['notification_email_publisher'] = array(get_option('admin_email'));
			}

			// Filter and validate advertiser addresses
			$advertiser_emails = $_POST['adrotate_notification_email_advertiser'];
			if(strlen($advertiser_emails) > 0) {
				$advertiser_emails = explode(',', trim($advertiser_emails));
				foreach($advertiser_emails as $advertiser_email) {
					$advertiser_email = trim($advertiser_email);
					if(strlen($advertiser_email) > 0) {
		  				if(is_email($advertiser_email) ) {
							$clean_email[] = $advertiser_email;
						}
					}
				}
				$notifications['notification_email_advertiser'] = array_unique(array_slice($clean_email, 0, 5));
				unset($clean_email);
			} else {
				$notifications['notification_email_advertiser'] = array(get_option('admin_email'));
			}
		
			update_option('adrotate_notifications', $notifications);
		}

		if($settings_tab == 'stats') {  
			$config = get_option('adrotate_config');

			$stats = trim($_POST['adrotate_stats']);
			$config['stats'] = (is_numeric($stats) AND $stats >= 0 AND $stats <= 5) ? $stats : 1;
			$config['enable_loggedin_impressions'] = (isset($_POST['adrotate_enable_loggedin_impressions'])) ? 'Y' : 'N';
			$config['enable_loggedin_clicks'] = (isset($_POST['adrotate_enable_loggedin_clicks'])) ? 'Y' : 'N';
			$config['enable_clean_trackerdata'] = (isset($_POST['adrotate_enable_clean_trackerdata'])) ? 'Y' : 'N';

			if($config['enable_clean_trackerdata'] == "Y" AND !wp_next_scheduled('adrotate_delete_transients')) {
				wp_schedule_event(adrotate_now(), 'twicedaily', 'adrotate_delete_transients');
			} 
			if($config['enable_clean_trackerdata'] == "N" AND wp_next_scheduled('adrotate_delete_transients')) {
				wp_clear_scheduled_hook('adrotate_delete_transients');
			} 

			$impression_timer = trim($_POST['adrotate_impression_timer']);
			$config['impression_timer'] = (is_numeric($impression_timer) AND $impression_timer >= 10 AND $impression_timer <= 3600) ? $impression_timer : 60;
			$click_timer = trim($_POST['adrotate_click_timer']);
			$config['click_timer'] = (is_numeric($click_timer) AND $click_timer >= 60 AND $click_timer <= 86400) ? $click_timer : 86400;

			$google_click_value = trim($_POST['adrotate_google_click_value']);
			$config['google_click_value'] = (is_float($google_click_value) AND $google_click_value >= 0 AND $google_click_value <= 10) ? $google_click_value : '1.00';
			$google_impression_value = trim($_POST['adrotate_google_impression_value']);
			$config['google_impression_value'] = (is_float($google_impression_value) AND $google_impression_value >= 0 AND $google_impression_value <= 10) ? $google_impression_value : '2.00';
	
			update_option('adrotate_config', $config);
		}

		if($settings_tab == 'geo') {  
			$config = get_option('adrotate_config');

			$geo = trim($_POST['adrotate_enable_geo']);
			$config['enable_geo'] = (is_numeric($geo) AND $geo >= 0 AND $geo <= 7) ? $geo : 0;
			$geo_cookie = trim($_POST['adrotate_geo_cookie_life']);
			$config['geo_cookie_life'] = (is_numeric($geo_cookie)) ? $geo_cookie : 86400;
			$geo_email = trim($_POST['adrotate_geo_email']);
			$config['geo_email'] = (strlen($geo_email) > 0) ? $geo_email : '';
			$geo_pass = trim($_POST['adrotate_geo_pass']);
			$config['geo_pass'] = (strlen($geo_pass) > 0) ? $geo_pass : '';

			if($config['enable_geo'] > 0) {
				// Purge
				@setcookie('adrotate-geo', '', time() - 300, COOKIEPATH, COOKIE_DOMAIN);
				// New cookie
				adrotate_geolocation();
			}
			
			// If no quotas are kept or Geo Targeting is disabled, reset possible old values to 0
			if($config['enable_geo'] == 0 OR $config['enable_geo'] == 6 OR $config['enable_geo'] == 7) {
				update_option('adrotate_geo_requests', 0);
				update_option('adrotate_geo_reset', 0);
			}

			update_option('adrotate_config', $config);
		}

		if($settings_tab == 'advertisers') {  
			$config = get_option('adrotate_config');

			$config['enable_advertisers'] = (isset($_POST['adrotate_enable_advertisers'])) ? 'Y' : 'N';
			$config['enable_editing'] = (isset($_POST['adrotate_enable_editing'])) ? 'Y' : 'N';
			$config['enable_mobile_advertisers'] = (isset($_POST['adrotate_enable_mobile_advertisers'])) ? 1 : 0;
			$config['enable_geo_advertisers'] = (isset($_POST['adrotate_enable_geo_advertisers'])) ? 1 : 0;

			if(isset($_POST['adrotate_role'])) {
				adrotate_prepare_roles('add');
			} else {
				adrotate_prepare_roles('remove');
			}

			update_option('adrotate_config', $config);
		}

		if($settings_tab == 'roles') {
			$config = get_option('adrotate_config');

			adrotate_set_capability($_POST['adrotate_advertiser'], "adrotate_advertiser");
			adrotate_set_capability($_POST['adrotate_global_report'], "adrotate_global_report");
			adrotate_set_capability($_POST['adrotate_ad_manage'], "adrotate_ad_manage");
			adrotate_set_capability($_POST['adrotate_ad_delete'], "adrotate_ad_delete");
			adrotate_set_capability($_POST['adrotate_group_manage'], "adrotate_group_manage");
			adrotate_set_capability($_POST['adrotate_group_delete'], "adrotate_group_delete");
			adrotate_set_capability($_POST['adrotate_schedule_manage'], "adrotate_schedule_manage");
			adrotate_set_capability($_POST['adrotate_schedule_delete'], "adrotate_schedule_delete");
			adrotate_set_capability($_POST['adrotate_advertiser_manage'], "adrotate_advertiser_manage");
			adrotate_set_capability($_POST['adrotate_moderate'], "adrotate_moderate");
			adrotate_set_capability($_POST['adrotate_moderate_approve'], "adrotate_moderate_approve");
			$config['advertiser'] = $_POST['adrotate_advertiser'];
			$config['global_report'] = $_POST['adrotate_global_report'];
			$config['ad_manage'] = $_POST['adrotate_ad_manage'];
			$config['ad_delete'] = $_POST['adrotate_ad_delete'];
			$config['group_manage'] = $_POST['adrotate_group_manage'];
			$config['group_delete'] = $_POST['adrotate_group_delete'];
			$config['schedule_manage'] = $_POST['adrotate_schedule_manage'];
			$config['schedule_delete'] = $_POST['adrotate_schedule_delete'];
			$config['advertiser_manage'] = $_POST['adrotate_advertiser_manage'];
			$config['moderate'] = $_POST['adrotate_moderate'];
			$config['moderate_approve']	= $_POST['adrotate_moderate_approve'];

			update_option('adrotate_config', $config);
		}

		if($settings_tab == 'misc') {  
			$config = get_option('adrotate_config');

			$config['widgetalign'] = (isset($_POST['adrotate_widgetalign'])) ? 'Y' : 'N';
			$config['widgetpadding'] = (isset($_POST['adrotate_widgetpadding'])) ? 'Y' : 'N';
			$config['hide_schedules'] = (isset($_POST['adrotate_hide_schedules'])) ? 'Y' : 'N';
			$config['w3caching'] = (isset($_POST['adrotate_w3caching'])) ? 'Y' : 'N';
			$config['borlabscache'] = (isset($_POST['adrotate_borlabscache'])) ? 'Y' : 'N';
			$config['affiliates'] = (isset($_POST['adrotate_affiliates'])) ? 'Y' : 'N';
	
			update_option('adrotate_config', $config);
		}

		if($settings_tab == 'maintenance') {  
			$debug = get_option('adrotate_debug');

			$debug['general'] = (isset($_POST['adrotate_debug'])) ? true : false;
			$debug['publisher'] = (isset($_POST['adrotate_debug_publisher'])) ? true : false;
			$debug['advertiser'] = (isset($_POST['adrotate_debug_advertiser'])) ? true : false;
			$debug['geo'] = (isset($_POST['adrotate_debug_geo'])) ? true : false;
			$debug['timers'] = (isset($_POST['adrotate_debug_timers'])) ? true : false;
			$debug['track'] = (isset($_POST['adrotate_debug_track'])) ? true : false;

			update_option('adrotate_debug', $debug);
		}
	
		// Return to dashboard
		adrotate_return('adrotate-settings', 400, array('tab' => $settings_tab));
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_save_network_options
 Purpose:   Save options from dashboard
 Since:		4.1
-------------------------------------------------------------*/
function adrotate_save_network_options() {
	if(wp_verify_nonce($_POST['adrotate_nonce_settings'],'adrotate_settings')) {

		$config = get_site_option('adrotate_network_settings');

		$site_id = trim($_POST['adrotate_network_primary']);
		$config['primary'] = (strlen($site_id) > 0) ? preg_replace('/\D/', '', $site_id) : 1;
		$config['site_dashboard'] = (isset($_POST['adrotate_network_site_dashboard'])) ? 'Y' : 'N';
		update_site_option('adrotate_network_settings', $config);
	
		// Return to dashboard
		adrotate_return('adrotate-network-settings', 400, array('tab' => $settings_tab));
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_save_profile_fields
 Purpose:   Save custom profile fields
 Since:		3.22.2b1
-------------------------------------------------------------*/
function adrotate_save_profile_fields($user_id){
    if(current_user_can('adrotate_advertiser_manage')) {
		// Is the user an advertiser?
		$advertiser = (isset($_POST['adrotate_is_advertiser']) AND strlen($_POST['adrotate_is_advertiser']) != 0) ? 'Y' : 'N';
	    update_user_meta($user_id, 'adrotate_is_advertiser', $advertiser);
	
		// Set user permissions
		$permissions['edit'] = (isset($_POST['adrotate_can_edit']) AND strlen($_POST['adrotate_can_edit']) != 0 AND $advertiser == "Y") ? 'Y' : 'N';
		$permissions['mobile'] = (isset($_POST['adrotate_can_mobile']) AND strlen($_POST['adrotate_can_mobile']) != 0 AND $advertiser == "Y") ? 'Y' : 'N';
		$permissions['geo'] = (isset($_POST['adrotate_can_geo']) AND strlen($_POST['adrotate_can_geo']) != 0 AND $advertiser == "Y") ? 'Y' : 'N';
	    update_user_meta($user_id, 'adrotate_permissions', $permissions);
	
		// User notes
		$notes = htmlspecialchars(trim($_POST['adrotate_notes'], "\t\n "), ENT_QUOTES);
	    update_user_meta($user_id, 'adrotate_notes', $notes);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_roles
 Purpose:   Prepare user roles for WordPress
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_prepare_roles($action) {
	
	if($action == 'add') {
		add_role('adrotate_advertiser', __('AdRotate Advertiser', 'adrotate-pro'), array('read' => 1));		
	} 
	if($action == 'remove') {
		remove_role('adrotate_advertiser');
	} 
}
?>