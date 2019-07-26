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
 Name:      adrotate_can_edit
 Purpose:   Return a array of adverts to use on advertiser dashboards
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_can_edit() {
	global $adrotate_config;
	
	if($adrotate_config['enable_editing'] == 'Y') {
		return true;
	} else {
		return false;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_is_networked
 Purpose:   Determine if AdRotate is network activated
 Since:		3.9.8
-------------------------------------------------------------*/
function adrotate_is_networked() {
	if(!function_exists('is_plugin_active_for_network')) require_once(ABSPATH.'/wp-admin/includes/plugin.php');

	if(is_plugin_active_for_network('adrotate-pro/adrotate-pro.php')) {
		return true;
	}		
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_human
 Purpose:   Check if visitor is a bot
 Since:		3.11.10
-------------------------------------------------------------*/
function adrotate_is_human() {
	global $adrotate_crawlers;

	if(is_array($adrotate_crawlers)) {
		$crawlers = $adrotate_crawlers;
	} else {
		$crawlers = array();
	}

	$useragent = adrotate_get_useragent();

	$nocrawler = array(true);
	if(strlen($useragent) > 0) {
		foreach($crawlers as $key => $crawler) {
			if(preg_match('/'.$crawler.'/i', $useragent)) $nocrawler[] = false;
		}
	}
	$nocrawler = (!in_array(false, $nocrawler)) ? true : false; // If no bool false in array it's not a bot
	
	// Returns true if no bot.
	return $nocrawler;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_ios
 Purpose:   Check if OS is iOS
 Since:		4.1
-------------------------------------------------------------*/
function adrotate_is_ios() {
	if(!class_exists('Mobile_Detect')) {
		require_once(dirname(__FILE__).'/library/mobile-detect.php');
	}
	$detect = new Mobile_Detect;
	 
	if($detect->isiOS() AND !$detect->isAndroidOS()) {
		return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_android
 Purpose:   Check if OS is Android
 Since:		4.1
-------------------------------------------------------------*/
function adrotate_is_android() {
	if(!class_exists('Mobile_Detect')) {
		require_once(dirname(__FILE__).'/library/mobile-detect.php');
	}
	$detect = new Mobile_Detect;
	 
	if(!$detect->isiOS() AND $detect->isAndroidOS()) {
		return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_mobile
 Purpose:   Check if visitor is on a smartphone
 Since:		3.12.6
-------------------------------------------------------------*/
function adrotate_is_mobile() {
	if(!class_exists('Mobile_Detect')) {
		require_once(dirname(__FILE__).'/library/mobile-detect.php');
	}
	$detect = new Mobile_Detect;
	 
	if($detect->isMobile() AND !$detect->isTablet()) {
		return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_tablet
 Purpose:   Check if visitor is on a tablet
 Since:		3.16
-------------------------------------------------------------*/
function adrotate_is_tablet() {
	if(!class_exists('Mobile_Detect')) {
		require_once(dirname(__FILE__).'/library/mobile-detect.php');
	}
	$detect = new Mobile_Detect;
	 
	if($detect->isTablet()) {
		return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_filter_schedule
 Purpose:   Weed out ads that are over the limit of their schedule
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_filter_schedule($selected, $banner) { 
	global $wpdb, $adrotate_config, $adrotate_debug;

	$now = adrotate_now();
	$day = date('D', $now);
	$hour = date('Hi', $now);

	if($adrotate_debug['general'] == true) {
		echo "<p><strong>[DEBUG][adrotate_filter_schedule()] Filtering banner</strong><pre>";
		print_r($banner->id); 
		echo "</pre></p>"; 
	}
	
	$cachekey = "adrotate_schedule_".$banner->id;
	$schedules = wp_cache_get($cachekey);
	if(false === $schedules) {
		$schedules = $wpdb->get_results("SELECT `{$wpdb->prefix}adrotate_schedule`.* FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` AND `ad` = {$banner->id} ORDER BY `starttime` ASC;"); 
		wp_cache_set($cachekey, $schedules, '', 300);
	}

	$current = array();
	foreach($schedules as $schedule) {
		if($adrotate_debug['general'] == true) {
			echo "<p><strong>[DEBUG][adrotate_filter_schedule] Ad ".$banner->id." - Schedule (id: ".$schedule->id.")</strong><pre>";
			echo "<br />Start: ".$schedule->starttime." (".date("F j, Y, g:i a", $schedule->starttime).")";
			echo "<br />End: ".$schedule->stoptime." (".date("F j, Y, g:i a", $schedule->stoptime).")";
			echo "<br />Impression Spread: ".$schedule->spread;
			echo "<br />Show on: Mon ".$schedule->day_mon.", Tue ".$schedule->day_tue.", Wed ".$schedule->day_wed.", Thu ".$schedule->day_thu.", Fri ".$schedule->day_fri.", Sat ".$schedule->day_sat.", Sun ".$schedule->day_sun;
			echo "<br />Show between: ".$schedule->daystarttime." and ".$schedule->daystoptime." (Current: ".$hour.")";
			echo "</pre></p>";
		}

		if($schedule->starttime > $now OR $schedule->stoptime < $now) {
			$current[] = 0;
		} else if(($schedule->day_mon != 'Y' AND $day == 'Mon') OR ($schedule->day_tue != 'Y' AND $day == 'Tue') OR ($schedule->day_wed != 'Y' AND $day == 'Wed') OR ($schedule->day_thu != 'Y' AND $day == 'Thu') OR ($schedule->day_fri != 'Y' AND $day == 'Fri') OR ($schedule->day_sat != 'Y' AND $day == 'Sat') OR ($schedule->day_sun != 'Y' AND $day == 'Sun')) {
			$current[] = 0;
		} else if(($schedule->daystarttime > 0 OR $schedule->daystoptime > 0) AND ($schedule->daystarttime > $hour OR $schedule->daystoptime < $hour)) {
			$current[] = 0;
		} else {
			if($adrotate_config['stats'] == 1 AND $banner->tracker == 'Y') {
				$stat = adrotate_stats($banner->id, false, $schedule->starttime, $schedule->stoptime);
				$temp_max_impressions = $schedule->maximpressions / ($schedule->stoptime - $schedule->starttime) * ($now - $schedule->starttime);

				if(!is_array($stat)) $stat = array('clicks' => 0, 'impressions' => 0);
	
				if($stat['clicks'] >= $schedule->maxclicks AND $schedule->maxclicks > 0) {
					$current[] = 0;
				} else if($schedule->spread == 'Y' AND $stat['impressions'] > $temp_max_impressions) {
					$current[] = 0;
				} else if($stat['impressions'] >= $schedule->maximpressions AND $schedule->maximpressions > 0) {
					$current[] = 0;
				} else {
					$current[] = 1;
				}
			} else {
				$current[] = 1;
			}
		}
	}
	
	// Remove advert from array if all schedules are false (0)
	if(!in_array(1, $current)) {
		unset($selected[$banner->id]);
	}
	unset($current, $schedules, $now, $day, $hour, $max_impressions);
	
	return $selected;
} 

/*-------------------------------------------------------------
 Name:      adrotate_filter_show_everyone
 Purpose:   Remove adverts that don't show to logged in users
 Since:		4.8
-------------------------------------------------------------*/
function adrotate_filter_show_everyone($selected, $banner) { 
	global $wpdb, $adrotate_debug;

	if($adrotate_debug['general'] == true) {
		echo "<p><strong>[DEBUG][adrotate_filter_show_everyone] Ad ".$banner->id."</strong><pre>";
		echo "Show Everyone: ".$banner->show_everyone;
		echo "<br />Is logged in: "; echo (is_user_logged_in()) ? "Y" : "N";
		echo "</pre></p>";
	}

	if(($banner->show_everyone == "N") AND is_user_logged_in()) {
		unset($selected[$banner->id]);
	} 

	return $selected;
} 

/*-------------------------------------------------------------
 Name:      adrotate_filter_budget
 Purpose:   Weed out ads that are over the limit of their schedule
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_filter_budget($selected, $banner) { 
	global $wpdb, $adrotate_debug;

	if($banner->budget == null) $banner->budget = '0';
	if($banner->crate == null) $banner->crate = '0';
	if($banner->irate == null) $banner->irate = '0';

	if($adrotate_debug['general'] == true) {
		echo "<p><strong>[DEBUG][adrotate_filter_budget] Ad ".$banner->id."</strong><pre>";
		echo "Advert Budget: ".number_format($banner->budget, 4, '.', '');
		echo "<br />Cost per click: ".number_format($banner->crate, 4, '.', '');
		echo "<br />Cost per impression: ".number_format($banner->irate, 4, '.', '');
		echo "</pre></p>";
	}

	if(($banner->budget <= 0 AND $banner->crate > 0) OR ($banner->budget <= 0 AND $banner->irate > 0)) {
		unset($selected[$banner->id]);
		return $selected;
	} 
	if($banner->budget > 0 AND $banner->irate > 0) {
		$cpm = number_format($banner->irate / 1000, 4, '.', '');
		$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `budget` = `budget` - {$cpm} WHERE `id` = {$banner->id};");
	}

	return $selected;
} 

/*-------------------------------------------------------------
 Name:      adrotate_filter_location

 Purpose:   Determine the users location, the ads geo settings and filter out ads
 Receive:  	$selected, $banner
 Return:    $selected|array
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_filter_location($selected, $banner) { 
	global $adrotate_debug;

	// Grab geo data from session or from cookie data
	if(adrotate_has_cookie('geo')) {
		$geo = adrotate_get_cookie('geo');
		$geo_source = 'Cookie';
	} else {
		$geo = $_SESSION['adrotate-geo'];
		$geo_source = 'Session data';
	}

	if(is_array($geo)) {
		$cities = unserialize(stripslashes($banner->cities));
		$countries = unserialize(stripslashes($banner->countries));
		if(!is_array($cities)) $cities = array();
		if(!is_array($countries)) $countries = array();
		
		if($adrotate_debug['general'] == true OR $adrotate_debug['geo'] == true) {
			echo "<p><strong>[DEBUG][adrotate_filter_location] Ad (id: ".$banner->id.")</strong><pre>";
			echo "Cookie or _SESSION: ".$geo_source;
			echo "<br />Geo Provider: ".$geo['provider']." (Code: ".$geo['status'].")";
			echo "<br />Visitor City and State: ".$geo['city']." (DMA: ".$geo['dma']."), " .$geo['state']." (ISO: ".$geo['statecode'].")";
			echo "<br />Advert Cities/States (".count($cities)."): ";
			print_r($cities);
			echo "<br />Visitor Country: ".$geo['countrycode'];
			echo "<br />Advert Countries (".count($countries)."): ";
			print_r($countries);
			echo "</pre></p>";
		}
	
		if($geo['status'] == 200) {
			if(count($cities) > 0 AND count(array_intersect($cities, array($geo['city'], $geo['dma'], $geo['state'], $geo['statecode']))) == 0) {
				unset($selected[$banner->id]);
				return $selected;
			}
			if(count($countries) > 0 AND !in_array($geo['countrycode'], $countries)) {
				unset($selected[$banner->id]);
				return $selected;
			}
		}
	} else {
		if($adrotate_debug['general'] == true OR $adrotate_debug['geo'] == true) {
			echo "<p><strong>[DEBUG][adrotate_filter_location] Ad (id: ".$banner->id.")</strong><pre>";
			print_r($geo);
			echo "</pre></p>";
		}
	}

	return $selected;
} 

/*-------------------------------------------------------------
 Name:      adrotate_filter_content

 Purpose:   Find the location of the visitor
 Since:		4.14
-------------------------------------------------------------*/
function adrotate_filter_content($content) {
	// Deal with <blockquote>
    $array = preg_split("/<blockquote>/", $content);
    $content = array_shift($array);
    foreach ($array as $string) {
        $content .= "<blockquote>";
        $array2 = preg_split(",</blockquote>,", $string);
        $content .= preg_replace("/./", " ", array_shift($array2));
        $content .= "</blockquote>";
        if (!empty($array2)) {
            $content .= $array2[0];
        }
    }
    unset($array, $array2, $string);

	// Deal with <pre>
    $array = preg_split("/<pre>/", $content);
    $content = array_shift($array);
    foreach ($array as $string) {
        $content .= "<pre>";
        $array2 = preg_split(",</pre>,", $string);
        $content .= preg_replace("/./", " ", array_shift($array2));
        $content .= "</pre>";
        if (!empty($array2)) {
            $content .= $array2[0];
        }
    }
    unset($array, $array2, $string);

	// Deal with <code>
    $array = preg_split("/<code>/", $content);
    $content = array_shift($array);
    foreach ($array as $string) {
        $content .= "<code>";
        $array2 = preg_split(",</code>,", $string);
        $content .= preg_replace("/./", " ", array_shift($array2));
        $content .= "</code>";
        if (!empty($array2)) {
            $content .= $array2[0];
        }
    }
    unset($array, $array2, $string);

    return $content;
}

/*-------------------------------------------------------------
 Name:      adrotate_geolocation

 Purpose:   Find the location of the visitor
 Receive:   -None-
 Return:    $array
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_geolocation() {
	global $wpdb;

	if((!adrotate_has_cookie('geo') AND adrotate_is_human())) {
		$adrotate_config = get_option('adrotate_config');
		$remote_ip = adrotate_get_remote_ip();
		$geo_result = array();

		if($adrotate_config['enable_geo'] == 1 OR $adrotate_config['enable_geo'] == 2) { // Telize OR GeoBytes (deprecated)
			// Correct setting, assume AdRotate Geo
			$adrotate_config['enable_geo'] = 5; // AdRotate Geo
			update_option('adrotate_config', $adrotate_config);
		}

		if($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4) { // MaxMind
			if($adrotate_config['enable_geo'] == 3) {
				$service_type = 'country';
			}
			if($adrotate_config['enable_geo'] == 4) {
				$service_type = 'city';
			}
	
			$args = array('timeout' => 3, 'sslverify' => false, 'headers' => array('user-agent' => 'AdRotate Pro;', 'Authorization' => 'Basic '.base64_encode($adrotate_config["geo_email"].':'.$adrotate_config["geo_pass"])));
			$raw_response = wp_remote_get('https://geoip.maxmind.com/geoip/v2.1/'.$service_type.'/'.$remote_ip, $args);
		    
			$geo_result['provider'] = 'MaxMind '.$service_type;
		    if(!is_wp_error($raw_response)) {	
			    $response = json_decode($raw_response['body'], true);
				$geo_result['status'] = $raw_response['response']['code'];
	
			    if($geo_result['status'] == 200) {
					$geo_result['city'] = (isset($response['city']['names']['en'])) ? strtolower($response['city']['names']['en']) : '';
					$geo_result['dma'] = (isset($response['location']['metro_code'])) ? strtolower($response['location']['metro_code']) : '';
					$geo_result['countrycode'] = (isset($response['country']['iso_code'])) ? $response['country']['iso_code'] : '';
					$geo_result['state'] = (isset($response['subdivisions'][0]['names']['en'])) ? strtolower($response['subdivisions'][0]['names']['en']) : '';
					$geo_result['statecode'] = (isset($response['subdivisions'][0]['iso_code'])) ? strtolower($response['subdivisions'][0]['iso_code']) : '';
				} else { 			
					$geo_result['status'] = $response['code'];
					$geo_result['error'] = $response['error'];

					if($response['code'] == 'IP_ADDRESS_RESERVED') $response['maxmind']['queries_remaining'] = 0;
					if($response['code'] == 'OUT_OF_QUERIES') $response['maxmind']['queries_remaining'] = 0;
				}
				update_option('adrotate_geo_requests', $response['maxmind']['queries_remaining']);
			} else {
				$geo_result['status'] = $raw_response->get_error_code();
				$geo_result['error'] = $raw_response->get_error_message();
			}
		}

		if($adrotate_config['enable_geo'] == 5) { // AdRotate Geo
			$lookups = get_option('adrotate_geo_requests');
			
			// Figure out if a lookup should be made
			if($lookups < 1) {
				$daystart = gmdate('U', gmmktime(0, 0, 0, gmdate('n'), gmdate('j')));
				if(get_option('adrotate_geo_reset') < $daystart) {
					$lookups = 2; // 2 attempts to reset quota
					update_option('adrotate_geo_requests', $lookups);
					update_option('adrotate_geo_reset', $daystart);
				}
				unset($daystart);	
			}

			// Do a lookup if there are enough lookups available
			if($lookups > 0) {
				if(adrotate_is_networked()) {
					$adrotate_activate = get_site_option('adrotate_activate');
				} else {
					$adrotate_activate = get_option('adrotate_activate');
				}
	
				$args = array('timeout' => 3, 'sslverify' => false, 'headers' => array('User-Agent' => 'AdRotate Pro;' . get_option('siteurl')));
				$auth = base64_encode($adrotate_activate["instance"].':'.$adrotate_activate["key"]);
				$raw_response = wp_remote_get('https://ajdg.solutions/api/geo/5/?auth='.$auth.'&ip='.$remote_ip, $args);

				$geo_result['provider'] = 'AdRotate Geo';
			    if(!is_wp_error($raw_response)) {	
				    $response = json_decode($raw_response['body'], true);
					$geo_result['status'] = $raw_response['response']['code'];
		
				    if($geo_result['status'] == 200) {
						$geo_result['city'] = (isset($response['city'])) ? strtolower($response['city']) : '';
						$geo_result['dma'] = (isset($response['dma'])) ? strtolower($response['dma']) : '';
						$geo_result['countrycode'] = (isset($response['countrycode'])) ? $response['countrycode'] : '';
						$geo_result['state'] = (isset($response['state'])) ? strtolower($response['state']) : '';
						$geo_result['statecode'] = (isset($response['statecode'])) ? strtolower($response['statecode']) : '';
					} else { 			
						$geo_result['error'] = $raw_response['response']['message'];
					}
				} else {
					$geo_result['status'] = $raw_response->get_error_code();
					$geo_result['error'] = $raw_response->get_error_message();
					
					if($response['queries_remaining'] == -1) {
						$adrotate_config['enable_geo'] = 0; // Disable
						update_option('adrotate_config', $adrotate_config);
						$response['queries_remaining'] = 0;
					}
				}
				update_option('adrotate_geo_requests', $response['queries_remaining']);
				update_option('adrotate_geo_reset', time()); // Yes, GMT+0
			}
		}
	    unset($raw_response, $response);

		if($adrotate_config['enable_geo'] == 6) { // CloudFlare
			$geo_result['provider'] = 'CloudFlare';
		    if(isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
				$geo_result['status'] = 200;
				$geo_result['city'] = '';
				$geo_result['dma'] = '';
				$geo_result['countrycode'] = ($_SERVER["HTTP_CF_IPCOUNTRY"] == 'xx') ? '' : $_SERVER["HTTP_CF_IPCOUNTRY"];
				$geo_result['state'] = '';
				$geo_result['statecode'] = '';
			} else {
				$geo_result['status'] = 503;
				$geo_result['error'] = 'Header not found, check if Geo feature in CloudFlare is enabled.';
			}
		}

		if($adrotate_config['enable_geo'] == 7) { // FreegeoIP/ipstack
			// Does not report lookups
			
			$args = array('timeout' => 3, 'headers' => array('User-Agent' => 'AdRotate Pro;' . get_option('siteurl')));
			$raw_response = wp_remote_get('http://api.ipstack.com/'.$remote_ip.'?access_key='.$adrotate_config["geo_pass"], $args);

			$geo_result['provider'] = 'ipstack';
		    if(!is_wp_error($raw_response)) {	
			    $response = json_decode($raw_response['body'], true);
				$geo_result['status'] = $raw_response['response']['code'];
	
			    if($geo_result['status'] == 200) {
					$geo_result['city'] = (isset($response['city'])) ? strtolower($response['city']) : '';
					$geo_result['dma'] = (isset($response['geoname_id'])) ? strtolower($response['geoname_id']) : '';
					$geo_result['countrycode'] = (isset($response['country_code'])) ? $response['country_code'] : '';
					$geo_result['state'] = (isset($response['region_name'])) ? strtolower($response['region_name']) : '';
					$geo_result['statecode'] = (isset($response['region_code'])) ? strtolower($response['region_code']) : '';
				} else { 			
					$geo_result['error'] = $raw_response['response']['message'];
				}
			} else {
				$geo_result['status'] = $raw_response->get_error_code();
				$geo_result['error'] = $raw_response->get_error_message();
			}
		}
	
		@setcookie('adrotate-geo', serialize($geo_result), time() + $adrotate_config['geo_cookie_life'], COOKIEPATH, COOKIE_DOMAIN);
		if(!isset($_SESSION['adrotate-geo'])) $_SESSION['adrotate-geo'] = $geo_result;
	}	
}

/*-------------------------------------------------------------
 Name:      adrotate_has_cookie

 Purpose:   Check if a certain AdRotate Cookie exists
 Receive:   $get
 Return:    Boolean
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_has_cookie($get) {
	if($get == 'geo') {
		if(!empty($_COOKIE['adrotate-geo'])) return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_cookie

 Purpose:   Get a certain AdRotate Cookie
 Receive:   $get
 Return:    $data
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_get_cookie($get) {

	$data = false;
	if($get == 'geo') {
		if(!empty($_COOKIE['adrotate-geo'])) $data = $_COOKIE['adrotate-geo'];
	}
	return maybe_unserialize(stripslashes($data));
}
	
/*-------------------------------------------------------------
 Name:      adrotate_object_to_array

 Purpose:   Convert an object to a array
 Receive:   $data
 Return:    $data|$result
 Since:		3.9.9
-------------------------------------------------------------*/
function adrotate_object_to_array($data) {
	if(is_array($data)) {
		return $data;
	}

	if(is_object($data)) {
		$result = array();
		foreach($data as $key => $value) {
			$result[$key] = adrotate_object_to_array($value);
		}
		return $result;
	}
	return $data;
}

/*-------------------------------------------------------------
 Name:      adrotate_array_unique

 Purpose:   Filter out duplicate records in multidimensional arrays
 Receive:   $array
 Return:    $array|$return
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_array_unique($array) {
	if(count($array) > 0) {
		if(is_array($array[0])) {
			$return = array();
			// multidimensional
			foreach($array as $row) {
				if(!in_array($row, $return)) {
					$return[] = $row;
				}
			}
			return $return;
		} else {
			// not multidimensional
			return array_unique($array);
		}
	} else {
		return $array;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_array_unique

 Purpose:   Generate a random string
 Receive:   $length
 Return:    $result
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_rand($length = 8) {
	$available_chars = "abcdefghijklmnopqrstuvwxyz";	

	$result = '';
	for($i = 0; $i < $length; $i++) {
		$result .= $available_chars[mt_rand(0, 25)];
	}

	return $result;
}

/*-------------------------------------------------------------
 Name:      adrotate_pick_weight

 Purpose:   Sort out and pick a random ad based on weight
 Receive:   $selected
 Return:    $ads[$key]
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_pick_weight($selected) { 
    $ads = array_keys($selected); 
    foreach($selected as $banner) {
		$weight[] = $banner->weight;
		unset($banner);
	}
     
    $sum_of_weight = array_sum($weight)-1; 
    $rnd = mt_rand(0,$sum_of_weight); 

    foreach($ads as $key => $var){ 
        if($rnd<$weight[$key]){ 
            return $ads[$key]; 
        } 
        $rnd  -= $weight[$key]; 
    }
    unset($ads, $weight, $sum_of_weight, $rnd);
} 

/*-------------------------------------------------------------
 Name:      adrotate_shuffle

 Purpose:   Randomize and slice an array but keep keys intact
 Receive:   $array
 Return:    $shuffle
 Since:		3.8.8.3
-------------------------------------------------------------*/
function adrotate_shuffle($array, $amount = 20) { 
	if(!is_array($array)) return $array; 
	$keys = array_keys($array); 
	shuffle($keys);
	
	$shuffle = array(); 
	foreach($keys as $key) {
		$shuffle[$key] = $array[$key];
	}
	return $shuffle; 
}

/*-------------------------------------------------------------
 Name:      adrotate_select_categories

 Purpose:   Create scrolling menu of all categories.
 Receive:   $savedcats, $count, $child_of, $parent
 Return:    $output
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_select_categories($savedcats, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedcats)) $savedcats = explode(',', $savedcats);
	$categories = get_categories(array('child_of' => $parent, 'parent' => $parent,  'orderby' => 'id', 'order' => 'asc', 'hide_empty' => 0));

	if(!empty($categories)) {
		$output = '';
		if($parent == 0) {
			$output .= '<table width="100%">';
			$output .= '<thead>';
			$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr>';
			$output .= '</thead>';
			$output .= '<tbody>';
		}
		foreach($categories as $category) {
			if($category->parent > 0) {
				if($category->parent != $child_of) {
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<tr>';

			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_categories[]" value="'.$category->cat_ID.'"';
			$output .= (in_array($category->cat_ID, $savedcats)) ? ' checked' : '';
			$output .= '></td><td style="padding: 0px;">'.$indent.$category->name.' ('.$category->category_count.')</td>';

			$output .= '</tr>';
			$output .= adrotate_select_categories($savedcats, $count, $category->parent, $category->cat_ID);
			$child_of = $parent;
		}
		if($parent == 0) {
			$output .= '</tbody></table>';
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_select_pages

 Purpose:   Create scrolling menu of all pages.
 Receive:   $savedpages, $count, $child_of, $parent
 Return:    $output
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_select_pages($savedpages, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedpages)) $savedpages = explode(',', $savedpages);
	$pages = get_pages(array('child_of' => $parent, 'parent' => $parent, 'sort_column' => 'ID', 'sort_order' => 'asc'));

	if(!empty($pages)) {
		$output = '';
		if($parent == 0) {
			$output = '<table width="100%">';
			if(count($pages) > 5) {
				$output .= '<thead><tr><td class="check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr></thead>';
			}
			$output .= '<tbody>';
		}
		foreach($pages as $page) {
			if($page->post_parent > 0) {
				if($page->post_parent != $child_of) {
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<tr>';
			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_pages[]" value="'.$page->ID.'"';
			if(in_array($page->ID, $savedpages)) {
				$output .= ' checked';
			}
			$output .= '></td><td style="padding: 0px;">'.$indent.$page->post_title.'</td>';
			$output .= '</tr>';
			$output .= adrotate_select_pages($savedpages, $count, $page->post_parent, $page->ID);
			$child_of = $parent;
		}
		if($parent == 0) {
			$output .= '</tbody></table>';
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_countries

 Purpose:   List of countries
 Receive:   -None-
 Return:    array
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_countries() {
	return array(
		// Europe
		'EUROPE' => "Europe",
		'AL' => "Albania",
		'AM' => "Armenia",
		'AD' => "Andorra",
		'AT' => "Austria",
		'AZ' => "Azerbaijan",
		'BY' => "Belarus",
		'BE' => "Belgium",
		'BA' => "Bosnia and Herzegovina",
		'BG' => "Bulgaria",
		'HR' => "Croatia",
		'CY' => "Cyprus",
		'CZ' => "Czech Republic",
		'DK' => "Denmark",
		'EE' => "Estonia",
		'FI' => "Finland",
		'FR' => "France",
		'GE' => "Georgia",
		'DE' => "Germany",
		'GR' => "Greece",
		'HU' => "Hungary",
		'IS' => "Iceland",
		'IE' => "Ireland",
		'IT' => "Italy",
		'LV' => "Latvia",
		'LI' => "Liechtenstein",
		'LT' => "Lithuania",
		'LU' => "Luxembourg",
		'MK' => "Macedonia",
		'MT' => "Malta",
		'MD' => "Moldova",
		'MC' => "Monaco",
		'ME' => "Montenegro",
		'NL' => "the Netherlands",
		'NO' => "Norway",
		'PL' => "Poland",
		'PT' => "Portugal",
		'RO' => "Romania",
		'SM' => "San Marino",
		'RS' => "Serbia",
		'ES' => "Spain",
		'SK' => "Slovakia",
		'SI' => "Slovenia",
		'SE' => "Sweden",
		'CH' => "Switzerland",
		'VA' => "Vatican City",
		'TR' => "Turkey",
		'UA' => "Ukraine",
		'GB' => "United Kingdom",

		// North America
		'NORTHAMERICA' => "North America",
		'AG' => "Antigua and Barbuda",
		'BS' => "Bahamas",
		'BB' => "Barbados",
		'BZ' => "Belize",
		'CA' => "Canada",
		'CR' => "Costa Rica",
		'CU' => "Cuba",
		'DM' => "Dominica",
		'DO' => "Dominican Republic",
		'SV' => "El Salvador",
		'GD' => "Grenada",
		'GT' => "Guatemala",
		'HT' => "Haiti",
		'HN' => "Honduras",
		'JM' => "Jamaica",
		'MX' => "Mexico",
		'NI' => "Nicaragua",
		'PA' => "Panama",
		'KN' => "Saint Kitts and Nevis",
		'LC' => "Saint Lucia",
		'VC' => "Saint Vincent",
		'TT' => "Trinidad and Tobago",
		'US' => "United States",

		// South America
		'SOUTHAMERICA' => "South America",
		'AR' => "Argentina",
		'BO' => "Bolivia",
		'BR' => "Brazil",
		'CL' => "Chile",
		'CO' => "Colombia",
		'EC' => "Ecuador",
		'GY' => "Guyana",
		'PY' => "Paraguay",
		'PE' => "Peru",
		'SR' => "Suriname",
		'UY' => "Uruguay",
		'VE' => "Venezuela",

		// South East Asia + Australia + New Zealand
		'SOUTHEASTASIA' => "Southeast Asia, Australia and New Zealand",
		'AU' => "Australia",
		'BN' => "Brunei",
		'KH' => "Cambodia",
		'TL' => "East Timor (Timor Timur)",
		'ID' => "Indonesia",
		'LA' => "Laos",
		'MY' => "Malaysia",
		'MM' => "Myanmar",
		'NZ' => "New Zealand",
		'PH' => "Philippines",
		'SG' => "Singapore",
		'TH' => "Thailand",
		'VN' => "Vietnam",

		// Misc
		'MISC' => "Rest of the world",
		'AF' => "Afghanistan",
		'DZ' => "Algeria",
		'AO' => "Angola",
		'BH' => "Bahrain",
		'BD' => "Bangladesh",
		'BJ' => "Benin",
		'BT' => "Bhutan",
		'BF' => "Burkina Faso",
		'BI' => "Burundi",
		'CM' => "Cameroon",
		'CV' => "Cape Verde",
		'CF' => "Central African Republic",
		'TD' => "Chad",
		'CN' => "China",
		'KM' => "Comoros",
		'CG' => "Congo (Brazzaville)",
		'CD' => "Congo",
		'CI' => "Cote d'Ivoire",
		'DJ' => "Djibouti",
		'EG' => "Egypt",
		'GQ' => "Equatorial Guinea",
		'ER' => "Eritrea",
		'ET' => "Ethiopia",
		'FJ' => "Fiji",
		'GA' => "Gabon",
		'GM' => "Gambia",
		'GH' => "Ghana",
		'GN' => "Guinea",
		'GW' => "Guinea-Bissau",
		'IN' => "India",
		'IR' => "Iran",
		'IQ' => "Iraq",
		'IS' => "Israel",
		'JP' => "Japan",
		'JO' => "Jordan",
		'KZ' => "Kazakhstan",
		'KE' => "Kenya",
		'KI' => "Kiribati",
		'KP' => "north Korea",
		'KR' => "south Korea",
		'KW' => "Kuwait",
		'KG' => "Kyrgyzstan",
		'LV' => "Latvia",
		'LB' => "Lebanon",
		'LS' => "Lesotho",
		'LR' => "Liberia",
		'LY' => "Libya",
		'MG' => "Madagascar",
		'MW' => "Malawi",
		'MV' => "Maldives",
		'MN' => "Mongolia",
		'ML' => "Mali",
		'MH' => "Marshall Islands",
		'MR' => "Mauritania",
		'MU' => "Mauritius",
		'FM' => "Micronesia",
		'MA' => "Morocco",
		'MZ' => "Mozambique",
		'NA' => "Namibia",
		'NR' => "Nauru",
		'NP' => "Nepal",
		'NE' => "Niger",
		'NG' => "Nigeria",
		'OM' => "Oman",
		'PK' => "Pakistan",
		'PW' => "Palau",
		'PG' => "Papua New Guinea",
		'QA' => "Qatar",
		'RU' => "Russia",
		'RW' => "Rwanda",
		'WS' => "Samoa",
		'ST' => "Sao Tome and Principe",
		'SA' => "Saudi Arabia",
		'SN' => "Senegal",
		'SC' => "Seychelles",
		'SL' => "Sierra Leone",
		'SB' => "Solomon Islands",
		'SO' => "Somalia",
		'ZA' => "South Africa",
		'LK' => "Sri Lanka",
		'SY' => "Syria",
		'SD' => "Sudan",
		'SZ' => "Swaziland",
		'TW' => "Taiwan",
		'TJ' => "Tajikistan",
		'TO' => "Tonga",
		'TM' => "Turkmenistan",
		'TV' => "Tuvalu",
		'TZ' => "Tanzania",
		'TG' => "Togo",
		'TN' => "Tunisia",
		'UG' => "Uganda",
		'AE' => "United Arab Emirates",
		'UZ' => "Uzbekistan",
		'VU' => "Vanuatu",
		'YE' => "Yemen",
		'ZM' => "Zambia",
		'ZW' => "Zimbabwe"
	);
}

/*-------------------------------------------------------------
 Name:      adrotate_select_countries

 Purpose:   Create scrolling menu of all countries.
 Receive:   $savedcountries
 Return:    $output
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_select_countries($savedcountries) {
	if(!is_array($savedcountries)) $savedcountries = array();
	$countries = adrotate_countries();

	$output = '<table width="100%">';
	$output .= '<thead>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr>';
	$output .= '</thead>';

	$output .= '<tbody>';
	$output .= '<tr><td colspan="2" style="padding: 0px;"><em>--- Regions ---</em></td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_westeurope" value="1" /></td><td style="padding: 0px;">West/Central Europe</td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_easteurope" value="1" /></td><td style="padding: 0px;">East/Central Europe</td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_northamerica" value="1" /></td><td style="padding: 0px;">North America</td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_southamerica" value="1" /></td><td style="padding: 0px;">South America</td></tr>';
	$output .= '<tr><td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_southeastasia" value="1" /></td><td style="padding: 0px;">Southeast Asia, Australia and New Zealand</td></tr>';
	foreach($countries as $k => $v) {
		$output .= '<tr>';
		if(strlen($k) > 2) {
			$output .= '<td colspan="2" style="padding: 0px;"><em>--- '.$v.' ---</em></td>';
		} else {
			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_countries[]"  value="'.$k.'"';
			$output .= (in_array($k, $savedcountries)) ? ' checked' : '';
			$output .= '></td><td style="padding: 0px;">'.$v.'</td>';
		}
		$output .= '</tr>';
	}
	$output .= '</tbody></table>';
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_evaluate_ads

 Purpose:   Initiate evaluations for errors and determine the ad status
 Receive:   $return, $id
 Return:    opt|int
 Since:		3.6.5
-------------------------------------------------------------*/
function adrotate_prepare_evaluate_ads($return = true) {
	global $wpdb;
	
	// Fetch ads
	$ads = $wpdb->get_results("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` != 'disabled' AND `type` != 'generator' AND `type` != 'a_empty' AND `type` != 'a_error' AND `type` != 'queue' AND `type` != 'reject' AND `type` != 'archived' AND `type` != 'trash' AND `type` != 'empty' ORDER BY `id` ASC;");

	// Determine error states
	$error = $expired = $expiressoon = $normal = $unknown = 0;
	foreach($ads as $ad) {
		$result = adrotate_evaluate_ad($ad->id);
		if($result == 'error') {
			$error++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'error' WHERE `id` = '{$ad->id}';");
		} 

		if($result == 'expired') {
			$expired++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'expired' WHERE `id` = '{$ad->id}';");
		} 
		
		if($result == '2days') {
			$expiressoon++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = '2days' WHERE `id` = '{$ad->id}';");
		}
		
		if($result == '7days') {
			$normal++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = '7days' WHERE `id` = '{$ad->id}';");
		}
		
		if($result == 'active') {
			$normal++;
			$wpdb->query("UPDATE `{$wpdb->prefix}adrotate` SET `type` = 'active' WHERE `id` = '{$ad->id}';");
		}
		
		if($result == 'unknown') {
			$unknown++;
		}
		unset($ad);
	}

	$result = array('error' => $error, 'expired' => $expired, 'expiressoon' => $expiressoon, 'normal' => $normal, 'unknown' => $unknown);
	update_option('adrotate_advert_status', $result);
	unset($ads, $result);
	if($return) adrotate_return('adrotate-settings', 405, array('tab' => 'maintenance'));
}

/*-------------------------------------------------------------
 Name:      adrotate_evaluate_ads
 Purpose:   Initiate automated evaluations for errors and determine the ad status
 Since:		3.8.7.1
-------------------------------------------------------------*/
function adrotate_evaluate_ads() {
	// Verify all ads
	adrotate_prepare_evaluate_ads(false);
}

/*-------------------------------------------------------------
 Name:      adrotate_evaluate_ad
 Purpose:   Evaluates ads for errors
 Since:		3.6.5
-------------------------------------------------------------*/
function adrotate_evaluate_ad($ad_id) {
	global $wpdb, $adrotate_config;
	
	$now = adrotate_now();
	$in2days = $now + 172800;
	$in7days = $now + 604800;

	// Fetch ad
	$ad = $wpdb->get_row($wpdb->prepare("SELECT `id`, `bannercode`, `tracker`, `imagetype`, `image`, `budget`,`crate`, `irate` FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $ad_id));
	$advertiser = $wpdb->get_var("SELECT `user` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$ad->id}' AND `group` = 0 AND `user` > 0 AND `schedule` = 0;");
	$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$ad->id}' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
	$schedules = $wpdb->get_var("SELECT COUNT(`schedule`) FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '{$ad->id}' AND `group` = 0 AND `user` = 0;");

	$bannercode = stripslashes(htmlspecialchars_decode($ad->bannercode, ENT_QUOTES));
	// Determine error states
	if(
		strlen($bannercode) < 1 // AdCode empty
		OR ($ad->tracker == 'N' AND $advertiser > 0) // Didn't enable click-tracking, DID set a advertiser
		OR (!preg_match_all('/<(a|script|embed|iframe)[^>](.*?)>/i', $bannercode, $things) AND $ad->tracker == 'Y') // Clicktracking active but no valid link/tag present
		OR ($ad->tracker == 'N' AND $ad->crate > 0)	// Clicktracking in-active but set a Click rate
		OR (preg_match_all("/(%image%|%asset%)/i", $bannercode, $things) AND $ad->image == '' AND $ad->imagetype == '') // Did use %image% but didn't select an image
		OR (!preg_match_all("/(%image%|%asset%)/i", $bannercode, $things) AND $ad->image != '' AND $ad->imagetype != '') // Didn't use %image% but selected an image
		OR (($ad->image == '' AND $ad->imagetype != '') OR ($ad->image != '' AND $ad->imagetype == '')) // Image and Imagetype mismatch
		OR $schedules == 0 // No Schedules for this ad
	) {
		return 'error';
	} else if(
		$stoptime <= $now // Past the enddate
		OR (($ad->crate > 0 OR $ad->irate > 0) AND $ad->budget <= 0) // Ad ran out of money
	){
		return 'expired';
	} else if(
		$stoptime <= $in2days AND $stoptime >= $now	// Expires in 2 days
	){
		return '2days';
	} else if(
		$stoptime <= $in7days AND $stoptime >= $now	// Expires in 7 days
	){
		return '7days';
	} else {
		return 'active';
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_color
 Purpose:   Check if ads are expired and set a color for its end date
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_prepare_color($enddate) {
	$now = adrotate_now();
	$in2days = $now + 172800;
	$in7days = $now + 604800;
	
	if($enddate <= $now) {
		return '#CC2900'; // red
	} else if($enddate <= $in2days AND $enddate >= $now) {
		return '#F90'; // orange
	} else if($enddate <= $in7days AND $enddate >= $now) {
		return '#E6B800'; // yellow
	} else {
		return '#009900'; // green
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_ad_is_in_groups
 Purpose:   Build list of groups the ad is in (overview)
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_ad_is_in_groups($id) {
	global $wpdb;

	$output = '';
	$groups	= $wpdb->get_results("
		SELECT 
			`{$wpdb->prefix}adrotate_groups`.`name` 
		FROM 
			`{$wpdb->prefix}adrotate_groups`, 
			`{$wpdb->prefix}adrotate_linkmeta` 
		WHERE 
			`{$wpdb->prefix}adrotate_linkmeta`.`ad` = '".$id."'
			AND `{$wpdb->prefix}adrotate_linkmeta`.`group` = `{$wpdb->prefix}adrotate_groups`.`id`
			AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = 0
		;");
	if($groups) {
		foreach($groups as $group) {
			$output .= $group->name.", ";
		}
	}
	$output = rtrim($output, ", ");
	
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_hash
 Purpose:   Generate the adverts clicktracking hash
 Since:		3.9.12
-------------------------------------------------------------*/
function adrotate_hash($ad, $group = 0, $blog_id = 0) {
	global $adrotate_debug, $adrotate_config;
	
	// For Javascript
	if($adrotate_debug['timers'] == true) {
		$timer = 0;
	} else {
		$timer = $adrotate_config['impression_timer'];
	}
		
	if($adrotate_debug['track'] == true) {
		return "$ad,$group,$blog_id,$timer";
	} else {
		return base64_encode("$ad,$group,$blog_id,$timer");
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_get_remote_ip
 Purpose:   Get the remote IP from the visitor
 Since:		3.6.2
-------------------------------------------------------------*/
function adrotate_get_remote_ip(){
	if(empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$remote_ip = $_SERVER["REMOTE_ADDR"];
	} else {
		$remote_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	$buffer = explode(',', $remote_ip, 2);

	return $buffer[0];
}

/*-------------------------------------------------------------
 Name:      adrotate_get_useragent
 Purpose:   Get the useragent from the visitor
 Since:		3.18.3
-------------------------------------------------------------*/
function adrotate_get_useragent(){
	if(isset($_SERVER['HTTP_USER_AGENT'])) {
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$useragent = trim($useragent, ' \t\r\n\0\x0B');
	} else {
		$useragent = '';
	}
	
	return $useragent;
}

/*-------------------------------------------------------------
 Name:      adrotate_apply_jetpack_photon
 Purpose:   Use Jetpack Photon if possible
 Since:		4.11
-------------------------------------------------------------*/
function adrotate_apply_jetpack_photon($image) {
	if(class_exists('Jetpack_Photon') AND Jetpack::is_module_active('photon') AND function_exists('jetpack_photon_url')) {
		return jetpack_photon_url($image);
	} else {
		return $image;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_sanitize_file_name
 Purpose:   Clean up file names of files that are being uploaded.
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_sanitize_file_name($filename) {
    $filename_raw = $filename;
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
    $filename = str_replace($special_chars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = strtolower(trim($filename, '.-_'));
    return $filename;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_sorted_roles
 Purpose:   Returns all roles and capabilities, sorted by user level. Lowest to highest.
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_get_sorted_roles() {	
	global $wp_roles;

	$editable_roles = apply_filters('editable_roles', $wp_roles->roles);
	$sorted = array();
	
	foreach($editable_roles as $role => $details) {
		$sorted[$details['name']] = get_role($role);
	}

	$sorted = array_reverse($sorted);

	return $sorted;
}

/*-------------------------------------------------------------
 Name:      adrotate_set_capability
 Purpose:   Grant or revoke capabilities to a role and all higher roles
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_set_capability($lowest_role, $capability){
	$check_order = adrotate_get_sorted_roles();
	$add_capability = false;
	
	foreach($check_order as $role) {
		if($lowest_role == $role->name) $add_capability = true;
		if(empty($role)) continue;
		$add_capability ? $role->add_cap($capability) : $role->remove_cap($capability) ;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_remove_capability
 Purpose:   Remove the $capability from the all roles
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_remove_capability($capability){
	$check_order = adrotate_get_sorted_roles();

	foreach($check_order as $role) {
		$role = get_role($role->name);
		$role->remove_cap($capability);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_notifications
 Purpose:   Contact admins/moderators about various things
 Since:		4.0
-------------------------------------------------------------*/
function adrotate_notifications($action = false, $adid = false) {
	global $wpdb, $adrotate_config;

	$notifications = get_option("adrotate_notifications");
	$advert_status = get_option('adrotate_advert_status');

	$title = '';
	$message = array();
	$test = (isset($_POST['adrotate_notification_test_submit'])) ? true : false;

	if($test) {
		$title = "Test notification";
		$message[] = "This is a test notification.";
		$message[] = "Have a nice day!";
	} else {
		// Advert status
		if($notifications['notification_mail_status'] == 'Y') {
			$title = "Status update";
			if($advert_status['error'] > 0) $message[] = $advert_status['error']." ".__('advert(s) with errors!', 'adrotate-pro');
			if($advert_status['expired'] > 0) $message[] = $advert_status['expired']." ".__('advert(s) expired!', 'adrotate-pro');
			if($advert_status['expiressoon'] > 0) $message[] = $advert_status['expiressoon']." ".__('advert(s) will expire in less than 2 days.', 'adrotate-pro');
			if($advert_status['unknown'] > 0) $message[] = $advert_status['unknown']." ".__('advert(s) have an unknown status.', 'adrotate-pro');
		}

		// Geo Targeting
		if($notifications['notification_mail_geo'] == 'Y') {
			$geo_lookups = get_option('adrotate_geo_requests');
			if($adrotate_config['enable_geo'] > 2 AND $adrotate_config['enable_geo'] < 6 AND $geo_lookups < 1000) { 
				$title = "Geo targeting";
				if($geo_lookups > 0) $message[] = "Your website has less than 1000 lookups left for Geo Targeting. If you run out of lookups, Geo Targeting will stop working.";
				if($geo_lookups < 1) $message[] = "Your website has no lookups for Geo Targeting. Geo Targeting is currently not working.";
			}
		}

		// User (Advertiser) invoked actions (not on a timer)
		if($notifications['notification_mail_queue'] == 'Y') {
			if($action == 'queued') {
				$name = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$adid};");
				$queued = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'queue' OR `type` = 'reject';");
		
				$title = "Moderation queue";
				$message[] = "An advertiser has just queued one of their adverts.";
				$message[] = "Name '".$name."' (ID: ".$adid.")";
				$message[] = "Awaiting moderation: ".$queued." adverts.";
			}
		}
		
		if($notifications['notification_mail_approved'] == 'Y') {
			if($action == 'approved') {
				$name = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$adid};");
				
				$title = "Advert approved";
				$message[] = "A moderator has just approved an advert;";
				$message[] = $name." (ID: ".$adid.")";
			}
		}

		if($notifications['notification_mail_rejected'] == 'Y') {
			if($action == 'rejected') {
				$name = $wpdb->get_var("SELECT `title` FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$adid};");
				
				$title = "Advert rejected";
				$message[] = "A moderator has just rejected advert;";
				$message[] = $name." (ID: ".$adid.")";
			}
		}
	}
	
	// Maybe send some alerts (Test or real)
	if(count($message) > 0) {
		if($notifications['notification_email'] == 'Y') {
			adrotate_mail_notifications($notifications['notification_email_publisher'], $title, $message);
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_notifications
 Purpose:   Send emails to appointed recipients
 Since:		4.0
-------------------------------------------------------------*/
function adrotate_mail_notifications($emails, $title, $messages) {
	$messages = implode("\n", $messages);

	$blogname = get_option('blogname');
	$dashboardurl = get_option('siteurl')."/wp-admin/admin.php?page=adrotate-ads";
	$pluginurl = "https://ajdg.solutions/products/adrotate-for-wordpress/";

	$subject = '[AdRotate Alert] '.$title;
	
	$message = "<p>".__('Hello', 'adrotate-pro').",</p>";
	$message .= "<p>".__('This notification is sent to you from your website', 'adrotate-pro')." '$blogname'.<br />";
	$message .= "<p>".$messages."</p>";
	$message .= "<p>".__('Access your dashboard here:', 'adrotate-pro')." $dashboardurl<br />";	
	$message .= __('Have a nice day!', 'adrotate-pro')."</p>";
	$message .= "<p>".__('Your AdRotate Notifier', 'adrotate-pro')."<br />";
	$message .= "$pluginurl</p>";

	$x = count($emails);
	for($i=0;$i<$x;$i++) {
	    $headers = "Content-Type: text/html; charset=UTF-8\r\nFrom: AdRotate Plugin <".$emails[$i].">" . "\r\n";
		wp_mail($emails[$i], $subject, $message, $headers);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_advertiser
 Purpose:   Email a selected advertiser about his account/adverts/whatever
 Since:		4.0
-------------------------------------------------------------*/
function adrotate_mail_advertiser() {
	global $wpdb;

	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_email_advertiser')) {
		$author = $_POST['adrotate_username'];
		$useremail = $_POST['adrotate_email'];
		$subject = strip_tags(stripslashes(trim($_POST['adrotate_subject'], "\t\n ")));
		$advert_id	= trim($_POST['adrotate_advert'], "\t\n ");
		$text = strip_tags(stripslashes(trim($_POST['adrotate_message'], "\t\n ")));

		$advert = $wpdb->get_row("SELECT `id`, `title`, `type` FROM `{$wpdb->prefix}adrotate` WHERE `id` = `{$advert_id}`;");

		if(strlen($subject) < 1) $subject = "Publisher notification";
		if(strlen($text) < 1) $text = "No message given";

		$sitename = strtolower($_SERVER['SERVER_NAME']);
        if(substr($sitename, 0, 4) == 'www.') $sitename = substr($sitename, 4);
		
		$siteurl = get_option('siteurl');
		$adurl = $siteurl."/wp-admin/admin.php?page=adrotate-advertiser&view=edit&ad=".$advert->id;

	    $headers = "Content-Type: text/html; charset=UTF-8\r\n"."From: AdRotate Pro <wordpress@$sitename>\r\n";

		$subject = __('[AdRotate]', 'adrotate-pro').' '.$subject;
		
		$message = "<p>Hello $author,</p>";			
		if($advert->id > 0) $message .= "<p>About: ".$advert->id." - ".$advert->title." (".$advert->type.")</p>";
		$message .= "<p>$text</p>";
		$message .= "<p>".__('You can reply to this message by clicking reply in your email client.', 'adrotate-pro')."</p>";
		$message .= "<p>".__('Have a nice day!', 'adrotate-pro')."<br />";
		$message .= __('Your AdRotate Notifier', 'adrotate-pro')."</p>";

		wp_mail($useremail, $subject, $message, $headers);
	
		adrotate_return('adrotate-advertisers', 223);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_publisher

 Purpose:   Email the publisher that an advertiser wants something
 Receive:   -None-
 Return:    -None-
 Since:		3.1
-------------------------------------------------------------*/
function adrotate_mail_publisher() {
	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_email_advertiser') OR wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_email_moderator')) {
		$notifications = get_option("adrotate_notifications");
		$id = $_POST['adrotate_id'];
		$request = $_POST['adrotate_request'];
		$author = $_POST['adrotate_username'];
		$useremail = $_POST['adrotate_email'];
		$text = strip_tags(stripslashes(trim($_POST['adrotate_message'], "\t\n ")));
	
		if(strlen($text) < 1) $text = "";
		
		$emails = $notifications['notification_email_advertiser'];
		
		$siteurl = get_option('siteurl');
		$adurl = $siteurl."/wp-admin/admin.php?page=adrotate-ads&view=edit&ad=".$id;
		$pluginurl = "https://ajdg.solutions/products/adrotate-for-wordpress/";

	    $headers = "Content-Type: text/html; charset=UTF-8\r\n"."From: $author <$useremail>\r\n";
			
		if($request == "renew") $subject = __('[AdRotate] An advertiser has put in a request for renewal!', 'adrotate-pro');
		if($request == "remove") $subject = __('[AdRotate] An advertiser wants his ad removed.', 'adrotate-pro');
		if($request == "other") $subject = __('[AdRotate] An advertiser wrote a comment on his ad!', 'adrotate-pro');
		if($request == "issue") $subject = __('[AdRotate] An advertiser has a problem!', 'adrotate-pro');
		
		$message = "<p>".__('Hello moderator', 'adrotate-pro').",</p>";
		if($request == "renew") $message .= "<p>$author ".__('requests ad', 'adrotate-pro')." <strong>$id</strong> ".__('renewed!', 'adrotate-pro')."</p>";
		if($request == "remove") $message .= "<p>$author ".__('requests ad', 'adrotate-pro')." <strong>$id</strong> ".__('removed.', 'adrotate-pro')."</p>";
		if($request == "other") $message .= "<p>$author ".__('has something to say about ad', 'adrotate-pro')." <strong>$id</strong>.</p>";
		if($request == "issue") $message .= "<p>$author ".__('has a problem with AdRotate.', 'adrotate-pro')."</p>";
		$message .= "<p>".__('Attached message:', 'adrotate-pro')." $text</p>";
		$message .= "<p>".__('You can reply to this message to contact', 'adrotate-pro')." $author";
		if($request != "issue") $message .= "<br />".__('Review the ad here:', 'adrotate-pro')." $adurl";
		$message .= "</p>";		
		$message .= "<p>".__('Have a nice day!', 'adrotate-pro')."<br />";
		$message .= __('Your AdRotate Notifier', 'adrotate-pro')."<br />";
		$message .= "$pluginurl</p>";

		for($i=0;$i<count($emails);$i++) {
			wp_mail($emails[$i], $subject, $message, $headers);
		}
	
		adrotate_return('adrotate-advertiser', 300);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_scripts
 Purpose:   Load file uploaded popup
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_dashboard_scripts() {
	$page = (isset($_GET['page'])) ? $_GET['page'] : '';
    if(strpos($page, 'adrotate') !== false) {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('raphael', plugins_url('/library/raphael-min.js', __FILE__), array('jquery'));
		wp_enqueue_script('elycharts', plugins_url('/library/elycharts.min.js', __FILE__), array('jquery', 'raphael'));
		wp_enqueue_script('textatcursor', plugins_url('/library/textatcursor.js', __FILE__));
		wp_enqueue_script('tablesorter', plugins_url('/library/jquery.tablesorter.min.js', __FILE__), array('jquery'));
		wp_enqueue_script('adrotate-tablesorter', plugins_url('/library/jquery.adrotate.tablesorter.js', __FILE__), array('tablesorter'));
		wp_enqueue_script('adrotate-datepicker', plugins_url('/library/jquery.adrotate.datepicker.js', __FILE__), array('jquery'));
	}

	// WP Pointers
	$seen_it = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
	if(!in_array('adrotate_pro', $seen_it)) {
		wp_enqueue_script('wp-pointer');
		add_action('admin_print_footer_scripts', 'adrotate_welcome_pointer');
    }
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_styles
 Purpose:   Load file uploaded popup
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_dashboard_styles() {
	// Keep global for notifications
	wp_enqueue_style('adrotate-admin-stylesheet', plugins_url('library/dashboard.css', __FILE__));

	$page = (isset($_GET['page'])) ? $_GET['page'] : '';
    if(strpos($page, 'adrotate') !== false) {
		wp_enqueue_style('jquery-ui-datepicker');
	}

	// WP Pointers
	$seen_it = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
	if(!in_array('adrotate_pro', $seen_it)) {
		wp_enqueue_style('wp-pointer');
    }
}


/*-------------------------------------------------------------
 Name:      adrotate_folder_contents
 Purpose:   List folder contents for dropdown menu
 Since:		0.4
-------------------------------------------------------------*/
function adrotate_folder_contents($current, $kind = 'all') {
	global $adrotate_config;

	$output = '';
	$asset_folder = WP_CONTENT_DIR."/".$adrotate_config['banner_folder'];
	$files = array();

	// Read Banner folder
	if($handle = opendir($asset_folder)) {
		if($kind == "image") {
			$extensions = array('jpg', 'jpeg', 'gif', 'png');
		} else if($kind == "html5") {
			$extensions = array('swf', 'flv', 'html', 'htm');
		} else {
			$extensions = array('jpg', 'jpeg', 'gif', 'png', 'swf', 'flv', 'html', 'htm');
		}

	    while (false !== ($file = readdir($handle))) {
	        if($file != "." AND $file != ".." AND $file != "index.php" AND $file != ".DS_Store" AND !is_dir($asset_folder.'/'.$file)) {
				$files[] = $file;
	        }
	    }
	    closedir($handle);

		$i = count($files);
	    if($i > 0) {
			sort($files);
			foreach($files as $file) {
				$fileinfo = pathinfo($file);
				if(in_array($fileinfo['extension'], $extensions)) {
				    $output .= "<option value='".$file."'";
				    if(($current == WP_CONTENT_URL.'/banners/'.$file) OR ($current == WP_CONTENT_URL."/%folder%/".$file)) { $output .= "selected"; }
				    $output .= ">".$file."</option>";
				}
			}
		} else {
	    	$output .= "<option disabled>&nbsp;&nbsp;&nbsp;".__('No files found', 'adrotate-pro')."</option>";
		}
	} else {
    	$output .= "<option disabled>&nbsp;&nbsp;&nbsp;".__('Folder not found or not accessible', 'adrotate-pro')."</option>";
	}
	
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_subfolder_contents
 Purpose:   List sub-folder contents for media manager
 Since:		4.9
-------------------------------------------------------------*/
function adrotate_subfolder_contents($asset_folder, $level = 1) {
	$index = $assets = array();

	// Read Banner folder
	if($handle = opendir($asset_folder)) {
	    while(false !== ($file = readdir($handle))) {
	        if($file != "." AND $file != ".." AND $file != "index.php" AND $file != ".DS_Store") {
	            $assets[] = $file;
	        }
	    }
	    closedir($handle);

	    if(count($assets) > 0) {
			$new_level = $level + 1;
			$extensions = array('jpg', 'jpeg', 'gif', 'png', 'swf', 'flv', 'html', 'htm', 'js');

			foreach($assets as $key => $asset) {
				$fileinfo = pathinfo($asset);
				unset($fileinfo['dirname']);
				if(is_dir($asset_folder.'/'.$asset)) { // Read subfolder
					if($level <= 2) { // Not to deep
						$fileinfo['contents'] = adrotate_subfolder_contents($asset_folder.'/'.$asset, $new_level);
						$index[] = $fileinfo;
					}
				} else { // It's a file
					if(in_array($fileinfo['extension'], $extensions)) {
						$index[] = $fileinfo;
					}
				}
				unset($fileinfo);
			}
			unset($level, $new_level);
		}
	}
	
	return $index;
}

/*-------------------------------------------------------------
 Name:      adrotate_unlink

 Purpose:   Delete a file or folder from the banners folder
 Receive:   $file
 Return:    boolean
 Since:		4.9
-------------------------------------------------------------*/
function adrotate_unlink($asset) {
	global $adrotate_config;

	$access_type = get_filesystem_method();
	if($access_type === 'direct') {
		$credentials = request_filesystem_credentials(site_url().'/wp-admin/', '', false, false, array());
	
		if(!WP_Filesystem($credentials)) {
			return false;
		}	
	
		global $wp_filesystem;

		$path = WP_CONTENT_DIR."/".$adrotate_config['banner_folder']."/".$asset;
		if(!is_dir($path)) { // It's a file
			if(unlink($path)) {
				return true;
			} else {
				return false;
			}
		} else { // It's a folder
			if($wp_filesystem->rmdir($path, true)) {
				return true;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_return
 Purpose:   Internal redirects
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_return($page, $status, $args = null) {
	if(strlen($page) > 0 AND ($status > 0 AND $status < 1000)) {
		$defaults = array(
			'status' => $status
		);
		$arguments = wp_parse_args($args, $defaults);
		$redirect = 'admin.php?page=' . $page . '&'.http_build_query($arguments);
	} else {
		$redirect = 'admin.php?page=adrotate&status=1'; // Unexpected error
	}

	wp_redirect($redirect);
}

/*-------------------------------------------------------------
 Name:      adrotate_status
 Purpose:   Internal redirects
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_status($status, $args = null) {

	$defaults = array(
		'ad' => '',
		'group' => '',
		'file' => ''
	);
	$arguments = wp_parse_args($args, $defaults);

	switch($status) {
		// Management messages
		case '200' :
			echo '<div id="message" class="updated"><p>'. __('Ad saved', 'adrotate-pro') .'</p></div>';
		break;

		case '201' :
			echo '<div id="message" class="updated"><p>'. __('Group saved', 'adrotate-pro') .'</p></div>';
		break;

		case '202' :
			echo '<div id="message" class="updated"><p>'. __('Banner image saved', 'adrotate-pro') .'</p></div>';
		break;

		case '203' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) deleted', 'adrotate-pro') .'</p></div>';
		break;

		case '204' :
			echo '<div id="message" class="updated"><p>'. __('Group deleted', 'adrotate-pro') .'</p></div>';
		break;

		case '205' :
			echo '<div id="message" class="updated"><p>'. __('Advertiser updated', 'adrotate-pro') .'</p></div>';
		break;

		case '206' :
			echo '<div id="message" class="updated"><p>'. __('Asset(s) deleted', 'adrotate-pro') .'</p></div>';
		break;

		case '207' :
			echo '<div id="message" class="updated"><p>'. __('Something went wrong deleting the file or folder. Make sure your permissions are in order.', 'adrotate-pro') .'</p></div>';
		break;

		case '208' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) statistics reset', 'adrotate-pro') .'</p></div>';
		break;

		case '209' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) renewed', 'adrotate-pro') .'</p></div>';
		break;

		case '210' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) deactivated', 'adrotate-pro') .'</p></div>';
		break;

		case '211' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) activated', 'adrotate-pro') .'</p></div>';
		break;

		case '212' :
			echo '<div id="message" class="updated"><p>'. __('Email(s) with reports successfully sent', 'adrotate-pro') .'</p></div>';
		break;

		case '213' :
			echo '<div id="message" class="updated"><p>'. __('Group including it\'s Ads deleted', 'adrotate-pro') .'</p></div>';
		break;

		case '214' :
			echo '<div id="message" class="updated"><p>'. __('Weight changed', 'adrotate-pro') .'</p></div>';
		break;

		case '215' :
			echo '<div id="message" class="updated"><p>'. __('Export created', 'adrotate-pro') .'. <a href="' . WP_CONTENT_URL . '/reports/'.$arguments['file'].'">Download</a>.</p></div>';
		break;

		case '216' :
			echo '<div id="message" class="updated"><p>'. __('Adverts imported', 'adrotate-pro') .'</div>';
		break;

		case '217' :
			echo '<div id="message" class="updated"><p>'. __('Schedule saved', 'adrotate-pro') .'</div>';
		break;

		case '218' :
			echo '<div id="message" class="updated"><p>'. __('Schedule(s) deleted', 'adrotate-pro') .'</div>';
		break;

		case '219' :
			echo '<div id="message" class="updated"><p>'. __('Advert(s) duplicated', 'adrotate-pro') .'</div>';
		break;

		case '220' :
			echo '<div id="message" class="updated"><p>'. __('Advert(s) archived', 'adrotate-pro') .'</div>';
		break;

		case '221' :
			echo '<div id="message" class="updated"><p>'. __('Advert(s) moved to the trash', 'adrotate-pro') .'</div>';
		break;

		case '222' :
			echo '<div id="message" class="updated"><p>'. __('Advert(s) restored from trash', 'adrotate-pro') .'</div>';
		break;

		case '223' :
			echo '<div id="message" class="updated"><p>'. __('Your message has been sent.', 'adrotate-pro') .'</p></div>';
		break;

		case '226' :
			echo '<div id="message" class="updated"><p>'. __('Advert HTML generated and placed in the AdCode field. Configure your advert below.', 'adrotate-pro') .'</div>';
		break;

		case '227' :
			echo '<div id="message" class="updated"><p>'. __('Header & ads.txt updated.', 'adrotate-pro') .'</div>';
		break;

		// Advertiser messages
		case '300' :
			echo '<div id="message" class="updated"><p>'. __('Your message has been sent. Someone will be in touch shortly.', 'adrotate-pro') .'</p></div>';
		break;

		case '301' :
			echo '<div id="message" class="updated"><p>'. __('Advert submitted for review', 'adrotate-pro') .'</p></div>';
		break;

		case '302' :
			echo '<div id="message" class="updated"><p>'. __('Advert updated and awaiting review', 'adrotate-pro') .'</p></div>';
		break;

		case '303' :
			echo '<div id="message" class="updated"><p>'. __('Email(s) with reports successfully sent', 'adrotate-pro') .'</p></div>';
		break;

		case '304' :
			echo '<div id="message" class="updated"><p>'. __('Ad approved', 'adrotate-pro') .'</p></div>';
		break;

		case '305' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) rejected', 'adrotate-pro') .'</p></div>';
		break;

		case '306' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) queued', 'adrotate-pro') .'</p></div>';
		break;

		// Settings
		case '400' :
			echo '<div id="message" class="updated"><p>'. __('Settings saved', 'adrotate-pro') .'</p></div>';
		break;

		case '403' :
			echo '<div id="message" class="updated"><p>'. __('Database optimized', 'adrotate-pro') .'</p></div>';
		break;

		case '404' :
			echo '<div id="message" class="updated"><p>'. __('Database repaired', 'adrotate-pro') .'</p></div>';
		break;

		case '405' :
			echo '<div id="message" class="updated"><p>'. __('Ads evaluated and statuses have been corrected where required', 'adrotate-pro') .'</p></div>';
		break;

		case '406' :
			echo '<div id="message" class="updated"><p>'. __('Cleanup complete', 'adrotate-pro') .'</p></div>';
		break;

		case '407' :
			echo '<div id="message" class="updated"><p>'. __('Test notification sent', 'adrotate-pro') .'</p></div>';
		break;

		case '408' :
			echo '<div id="message" class="updated"><p>'. __('Test mailing sent', 'adrotate-pro') .'</p></div>';
		break;

		// (all) Error messages
		case '500' :
			echo '<div id="message" class="error"><p>'. __('Action prohibited', 'adrotate-pro') .'</p></div>';
		break;

		case '501' :
			echo '<div id="message" class="error"><p>'. __('The ad was saved but has an issue which might prevent it from working properly. Review the colored ad.', 'adrotate-pro') .'</p></div>';
		break;

		case '502' :
			echo '<div id="message" class="error"><p>'. __('The ad was saved but has an issue which might prevent it from working properly. Please contact staff.', 'adrotate-pro') .'</p></div>';
		break;

		case '503' :
			echo '<div id="message" class="error"><p>'. __('No data found in selected time period', 'adrotate-pro') .'</p></div>';
		break;

		case '504' :
			echo '<div id="message" class="error"><p>'. __('Database can only be optimized or cleaned once every hour', 'adrotate-pro') .'</p></div>';
		break;

		case '505' :
			echo '<div id="message" class="error"><p>'. __('Form can not be (partially) empty!', 'adrotate-pro') .'</p></div>';
		break;

		case '506' :
			echo '<div id="message" class="updated"><p>'. __('No file uploaded.', 'adrotate-pro') .'</p></div>';
		break;

		case '507' :
			echo '<div id="message" class="updated"><p>'. __('The file could not be read.', 'adrotate-pro') .'</p></div>';
		break;

		case '508' :
			echo '<div id="message" class="updated"><p>'. __('Wrong file type.', 'adrotate-pro') .'</p></div>';
		break;

		case '509' :
			echo '<div id="message" class="updated"><p>'. __('No ads found.', 'adrotate-pro') .'</p></div>';
		break;

		case '510' :
			echo '<div id="message" class="updated"><p>'. __('Wrong file type. No file uploaded.', 'adrotate-pro') .'</p></div>';
		break;

		case '511' :
			echo '<div id="message" class="updated"><p>'. __('File is too large.', 'adrotate-pro') .'</p></div>';
		break;


		// Licensing
		case '600' :
			echo '<div id="message" class="error"><p>'. __('Invalid request', 'adrotate-pro') .'</p></div>';
		break;

		case '601' :
			echo '<div id="message" class="error"><p>'. __('No license key or email provided', 'adrotate-pro') .'</p></div>';
		break;

		case '602' :
			echo '<div id="message" class="error"><p>'. __('The request did not get through or the response was invalid. Contact support.', 'adrotate-pro') .'<br />'.$arguments['error'].'</p></div>';
		break;

		case '603' :
			echo '<div id="message" class="error"><p>'. __('The email provided is invalid.', 'adrotate-pro') .'</p></div>';
		break;

		case '604' :
			echo '<div id="message" class="error"><p>'. __('Invalid license key.', 'adrotate-pro') .'</p></div>';
		break;

		case '605' :
			echo '<div id="message" class="error"><p>'. __('The purchase matching this product is not complete. Contact support.', 'adrotate-pro') .'</p></div>';
		break;

		case '606' :
			echo '<div id="message" class="error"><p>'. __('No remaining activations for this license. Manage your license activations from your account on ajdg.solutions.', 'adrotate-pro') .'</p></div>';
		break;

		case '607' :
			echo '<div id="message" class="error"><p>'. __('Could not (de)activate key. Contact support.', 'adrotate-pro') .'</p></div>';
		break;

		case '608' :
			echo '<div id="message" class="updated"><p>'. __('Thank you. Your license is now active', 'adrotate-pro') .'</p></div>';
		break;

		case '609' :
			echo '<div id="message" class="updated"><p>'. __('Thank you. Your license is now de-activated', 'adrotate-pro') .'</p></div>';
		break;

		case '610' :
			echo '<div id="message" class="updated"><p>'. __('Thank you. Your licenses have been reset', 'adrotate-pro') .'</p></div>';
		break;

		case '611' :
			echo '<div id="message" class="updated"><p>'. __('This license can not be activated for networks. Please purchase a Developer license.', 'adrotate-pro') .'</p></div>';
		break;

		// Support
		case '701' :
			echo '<div id="message" class="updated support-confirm"><p><a href="https://ajdg.solutions/products/adrotate-for-wordpress/?utm_campaign=support-icon&utm_medium=support-banner&utm_source=adrotate-pro" target="_blank"><img src="'.plugins_url('images/icon-support.png', __FILE__).'" class="alignleft pro-image" /></a><strong>Support email sent.</strong><br />I will be in touch within two business days! Meanwhile, please check out the <a href="https://ajdg.solutions/manuals/adrotate/?utm_campaign=manuals-link&utm_medium=support-banner&utm_source=adrotate-pro" target="_blank">AdRotate manuals</a>.</p><p class="red">Please do not send multiple messages with the same question. This will clutter up my inbox and delays my response to you!</p></div>';
		break;
		
		default :
			echo '<div id="message" class="updated"><p>'. __('Unexpected error', 'adrotate-pro') .'</p></div>';			
		break;
	}
	
	unset($arguments, $args);
}
?>