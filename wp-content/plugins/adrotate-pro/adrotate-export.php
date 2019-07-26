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
 Name:      adrotate_export_stats

 Purpose:   Export CSV data of given month
 Receive:   -- None --
 Return:    -- None --
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_export_stats() {
	global $wpdb;

	if(wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_export_ads') OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_export_groups') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_export_advertiser') OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_export_global')) {
		$id = $type = $start_date = $end_date = $adstats = $csv_emails = '';
		if(isset($_POST['adrotate_export_id'])) $id	= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_id'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_export_type'])) $type = strip_tags(htmlspecialchars(trim($_POST['adrotate_export_type'], "\t\n "), ENT_QUOTES));
		if(isset($_POST['adrotate_start_date'])) $start_date = strip_tags(trim($_POST['adrotate_start_date'], "\t\n "));
		if(isset($_POST['adrotate_end_date'])) $end_date = strip_tags(trim($_POST['adrotate_end_date'], "\t\n "));
		if(isset($_POST['adrotate_export_addresses'])) $csv_emails = trim($_POST['adrotate_export_addresses']);


		// Sort out start dates
		if(strlen($start_date) > 0) {
			$from_name = $start_date;
			list($start_day, $start_month, $start_year) = explode('-', $start_date);
			$start_date = mktime(0, 0, 0, $start_month, $start_day, $start_year);
		} else {
			$from_name = 'invalid';
			$start_date = 0;
		}

		// Sort out end dates
		if(strlen($end_date) > 0) {
			$until_name = $end_date;
			list($end_day, $end_month, $end_year) = explode('-', $end_date);
			$end_date = mktime(23, 59, 0, $end_month, $end_day, $end_year);
		} else {
			$until_name = 'invalid';
			$end_date = 0;
		}

		// Enddate is too early, reset
		if($end_date <= $start_date) $end_date = $start_date + 604800; // 7 days

		// Email addresses/delivery
		if(strlen($csv_emails) > 0) {
			$csv_emails = explode(',', trim($csv_emails));
			foreach($csv_emails as $csv_email) {
				$csv_email = strip_tags(htmlspecialchars(trim($csv_email), ENT_QUOTES));
				if(strlen($csv_email) > 0) {
					if(preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $csv_email) ) {
						$clean_advertiser_email[] = $csv_email;
					}
				}
			}
			$emails = array_unique(array_slice($clean_advertiser_email, 0, 3));
			$emailcount = count($emails);
		} else {
			$emails = array();
			$emailcount = 0;
		}
		

		$adstats = array(); // Store the result
		$generated = array("Generated on ".date_i18n("M d Y, H:i"));
	
		if($type == "single" OR $type == "group" OR $type == "global") {
			if($type == "single") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE (`thetime` >= '{$start_date}' AND `thetime` <= '{$end_date}') AND `ad` = %d GROUP BY `thetime` ASC;", $id), ARRAY_A);
				$title = $wpdb->get_var($wpdb->prepare("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $id));
		
				$filename = "AdRotate_advert_ID_".$id."_".$from_name."_".$until_name.".csv";
				$topic = array("Report for ad '".$title."'");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Impressions", "Clicks");
			}
		
			if($type == "group") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE (`thetime` >= '{$start_date}' AND `thetime` <= '{$end_date}') AND  `group` = %d GROUP BY `thetime` ASC;", $id), ARRAY_A);
				$title = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `{$wpdb->prefix}adrotate_groups` WHERE `id` = %d;", $id));
		
				$filename = "AdRotate_group_ID_".$id."_".$from_name."_".$until_name.".csv";
				$topic = array("Report for group '".$title."'");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Impressions", "Clicks");
			}
		
			if($type == "global") {
				$ads = $wpdb->get_results("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE `thetime` >= '{$start_date}' AND `thetime` <= '{$end_date}' GROUP BY `thetime` ASC;", ARRAY_A);
		
				$filename = "AdRotate_stats_".$from_name."_".$until_name.".csv";

				$topic = array("Global report");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Impressions", "Clicks");
			}

			$x = 0;
			foreach($ads as $ad) {
				// Prevent gaps in display
				if(empty($ad['impressions'])) $ad['impressions'] = 0;
				if(empty($ad['clicks'])) $ad['clicks'] = 0;
		
				// Build array
				$adstats[$x]['day']	= date_i18n("M d Y", $ad['thetime']);
				$adstats[$x]['impressions'] = $ad['impressions'];
				$adstats[$x]['clicks'] = $ad['clicks'];
				$x++;
			}
		}
	
		if($type == "advertiser") { // Global advertiser stats
			$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = 0 AND `user` = %d ORDER BY `ad` ASC;", $id));

			$x=0;
			foreach($ads as $ad) {
				$title = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = '{$ad->ad}';");
				$startshow = $wpdb->get_var("SELECT `starttime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$ad->ad}' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
				$endshow = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$ad->ad}' AND  `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
				$username = $wpdb->get_var($wpdb->prepare("SELECT `display_name` FROM `$wpdb->users`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `$wpdb->users`.`ID` = `user` AND `ad` = %d ORDER BY `user_nicename` ASC;", $id));

				$startshow = (is_null($startshow)) ? 0 : $startshow;
				$endshow = (is_null($endshow)) ? 0 : $endshow;
				$stat = adrotate_stats($ad->ad);
				
				// Prevent gaps in display
				if($stat['impressions'] == 0 OR $stat['clicks'] == 0) {
					$ctr = "0";
				} else {
					$ctr = round((100/$stat['impressions']) * $stat['clicks'],2);
				}
	
				// Build array
				$adstats[$x]['title'] = $title;			
				$adstats[$x]['id'] = $ad->ad;			
				$adstats[$x]['startshow'] = date_i18n("M d Y", $startshow);
				$adstats[$x]['endshow']	= date_i18n("M d Y", $endshow);
				$adstats[$x]['impressions']	= $stat['impressions'];
				$adstats[$x]['clicks'] = $stat['clicks'];
				$adstats[$x]['ctr']	= $ctr;
				$x++;
			}
			
			$filename = "AdRotate_advertiser_".$username.".csv";
			$topic = array("Advertiser report for ".$username);
			$period = array("Period - Not Applicable");
			$keys = array("Title", "Ad ID", "First visibility", "Last visibility", "Clicks", "Impressions", "CTR (%)");
		}
			
		if($type == "advertiser-single") { // Single advertiser stats
			$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `{$wpdb->prefix}adrotate_stats` WHERE (`thetime` >= '{$from}' AND `thetime` <= '{$until}') AND `ad` = %d GROUP BY `thetime` ASC;", $id), ARRAY_A);
			$title = $wpdb->get_var($wpdb->prepare("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $id));
			$username = $wpdb->get_var($wpdb->prepare("SELECT `display_name` FROM `$wpdb->users`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `$wpdb->users`.`ID` = `user` AND `ad` = %d ORDER BY `user_nicename` ASC;", $id));
	
			$filename = "AdRotate_stats_advert_ID_".$id."_".$from_name."_".$until_name.".csv";
			$topic = array("Advertiser report for ".$username." for ad '".$title."'");
			$period = array("Period - From: ".$from_name." Until: ".$until_name);
			$keys = array("Day", "Impressions", "Clicks");

			$x=0;
			foreach($ads as $ad) {
				// Prevent gaps in display
				if(empty($ad['impressions'])) $ad['impressions'] = 0;
				if(empty($ad['clicks'])) $ad['clicks'] = 0;
		
				// Build array
				$adstats[$x]['day']	= date_i18n("M d Y", $ad['thetime']);
				$adstats[$x]['impressions'] = $ad['impressions'];
				$adstats[$x]['clicks'] = $ad['clicks'];
				$x++;
			}
		}

		if($adstats) {
			if(!file_exists(WP_CONTENT_DIR . '/reports/')) mkdir(WP_CONTENT_DIR . '/reports/', 0755);
			$fp = fopen(WP_CONTENT_DIR . '/reports/'.$filename, 'w');
			
			if($fp) {
				fputcsv($fp, $topic);
				fputcsv($fp, $period);
				fputcsv($fp, $generated);
				fputcsv($fp, $keys);
				foreach($adstats as $stat) {
					fputcsv($fp, $stat);
				}
				
				fclose($fp);

				if($emailcount > 0) {
					$attachments = array(WP_CONTENT_DIR . '/reports/'.$filename);
					$siteurl 	= get_option('siteurl');
					$email 		= get_option('admin_email');
		
				    $headers = "MIME-Version: 1.0\r\n" .
		    					"From: AdRotate Plugin <".$email.">\r\n" . 
		    					"Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\r\n";

					$subject = __('[AdRotate] CSV Report!', 'adrotate-pro');

					$message = 	"<p>".__('Hello', 'adrotate-pro').",</p>";
					$message .= "<p>".__('Attached in this email you will find the exported CSV file you generated on ', 'adrotate-pro')." $siteurl.</p>";
					$message .= "<p>".__('Have a nice day!', 'adrotate-pro')."<br />";
					$message .= __('Your AdRotate Notifier', 'adrotate-pro')."<br />";
					$message .= "https://ajdg.solutions/products/adrotate-for-wordpress/</p>";

					wp_mail($emails, $subject, $message, $headers, $attachments);

					if($type == "single") adrotate_return('adrotate-statistics', 212, array('view' => 'advert', 'id' => $id));
					if($type == "group") adrotate_return('adrotate-statistics', 212, array('view' => 'group', 'id' => $id));
					if($type == "global") adrotate_return('adrotate-statistics', 212);
					if($type == "advertiser") adrotate_return('adrotate-advertiser', 303);
					if($type == "advertiser-single") adrotate_return('adrotate-advertiser', 303, array('view' => 'report', 'ad' => $id));
					exit;
				}
				if($type == "single") adrotate_return('adrotate-statistics', 215, array('view' => 'advert', 'id' => $id, 'file' => $filename));
				if($type == "group") adrotate_return('adrotate-statistics', 215, array('view' => 'group', 'id' => $id, 'file' => $filename));
				if($type == "global") adrotate_return('adrotate-statistics', 215, array('file' => $filename));
				if($type == "advertiser") adrotate_return('adrotate-advertiser', 215, array('file' => $filename));
				if($type == "advertiser-single") adrotate_return('adrotate-advertiser', 215, array('view' => 'report', 'ad' => $id, 'file' => $filename));
				exit;
			} else {
				if($type == "single") adrotate_return('adrotate-statistics', 507, array('view' => 'advert', 'id' => $id));
				if($type == "group") adrotate_return('adrotate-statistics', 507, array('view' => 'group', 'id' => $id));
				if($type == "global") adrotate_return('adrotate-statistics', 507);
				if($type == "advertiser") adrotate_return('adrotate-advertiser', 507);
				if($type == "advertiser-single") adrotate_return('adrotate-advertiser', 507, array('view' => 'report', 'ad' => $id));
			}
		} else {
			if($type == "single") adrotate_return('adrotate-statistics', 503, array('view' => 'advert', 'id' => $id));
			if($type == "group") adrotate_return('adrotate-statistics', 503, array('view' => 'group', 'id' => $id));
			if($type == "global") adrotate_return('adrotate-statistics', 503);
			if($type == "advertiser") adrotate_return('adrotate-advertiser', 503);
			if($type == "advertiser-single") adrotate_return('adrotate-advertiser', 503, array('view' => 'report', 'ad' => $id));
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_export_ads
 Purpose:   Export adverts in various formats
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_export_ads($ids) {
	global $wpdb;

	$where = false;
	if(count($ids) > 1) {
		$where = "`id` = ";
		foreach($ids as $key => $id) {
			$where .= "'{$id}' OR `id` = ";
		}
		$where = rtrim($where, " OR `id` = ");
	}
	
	if(count($ids) == 1) {
		$where = "`id` = '{$ids[0]}'";
	}
	
	if($where) {
		$to_export = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE {$where} ORDER BY `id` ASC;", ARRAY_A);
	}

	$adverts = array();
	foreach($to_export as $export) {
		$starttime = $wpdb->get_var("SELECT `starttime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$export['id']."' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
		$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$export['id']."' AND  `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

		$starttime = (is_null($starttime)) ? 0 : $starttime;
		$stoptime = (is_null($stoptime)) ? 0 : $stoptime;
		if(!is_array($export['cities'])) $export['cities'] = array();
		if(!is_array($export['countries'])) $export['countries'] = array();
		
		$adverts[$export['id']] = array(
			'id' => $export['id'],
			'title' => $export['title'],
			'bannercode' => stripslashes($export['bannercode']),
			'imagetype' => (empty($export['imagetype'])) ? null : $export['imagetype'],
			'image' => (empty($export['image'])) ? null : $export['image'],
			'tracker' => $export['tracker'],
			'desktop' => $export['desktop'],
			'mobile' => $export['mobile'],
			'tablet' => $export['tablet'],
			'os_ios' => $export['os_ios'],
			'os_android' => $export['os_android'],
			'os_other' => $export['os_other'],
			'weight' => $export['weight'],
			'budget' => $export['budget'],
			'crate' => $export['crate'],
			'irate' => $export['irate'],
			'cities' => (empty($export['cities'])) ? null : implode(',', maybe_unserialize($export['cities'])),
			'countries' => (empty($export['countries'])) ? null : implode(',', maybe_unserialize($export['countries'])),
			'schedule_start' => $starttime,
			'schedule_end' => $stoptime,
		);
	}

	if(count($adverts) > 0) {
		$filename = "AdRotate_export_adverts_".date_i18n("mdYHis").".csv";
		if(!file_exists(WP_CONTENT_DIR . '/reports/')) mkdir(WP_CONTENT_DIR . '/reports/', 0755);
		$fp = fopen(WP_CONTENT_DIR . '/reports/'.$filename, 'w');
		
		if($fp) {
			$generated = array('Generated', date_i18n("M d Y, H:i:s"));
			$keys = array('id', 'name', 'bannercode', 'imagetype', 'image_url', 'enable_stats', 'show_desktop', 'show_mobile', 'show_tablet', 'show_ios', 'show_android', 'show_otheros', 'weight', 'budget', 'click_rate', 'impression_rate', 'geo_cities', 'geo_countries', 'schedule_start', 'schedule_end');

			fputcsv($fp, $generated);
			fputcsv($fp, $keys);
			foreach($adverts as $advert) {
				fputcsv($fp, $advert);
			}
			
			fclose($fp);

			adrotate_return('adrotate-ads', 215, array('file' => $filename));
			exit;
		}
	} else {
		adrotate_return('adrotate-ads', 509);
	}
}
?>