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
 Name:      adrotate_ad
 Purpose:   Show requested ad
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_ad($banner_id, $individual = true, $group = null, $site = 0) {
	global $wpdb, $adrotate_config, $adrotate_crawlers, $adrotate_debug;

	$output = '';

	if($banner_id) {
		$license = get_site_option('adrotate_activate');
		$network = get_site_option('adrotate_network_settings');
	
		if($site > 0 AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			$current_blog = $wpdb->blogid;
			switch_to_blog($network['primary']);
		}
		
		$banner = $wpdb->get_row($wpdb->prepare("SELECT `id`, `title`, `bannercode`, `paid`, `tracker`, `show_everyone`, `image`, `crate`, `irate`, `budget` FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d AND `paid` != 'N' AND (`type` = 'active' OR `type` = '2days' OR `type` = '7days');", $banner_id));

		if($banner) {
			if($adrotate_debug['general'] == true) {
				echo "<p><strong>[DEBUG][adrotate_ad()] Selected Ad ID</strong><pre>";
				print_r($banner->id.', '.$banner->title.'<br>');
				echo "</pre></p>"; 
			}
			
			$selected = array($banner->id => 0);			
			$selected = adrotate_filter_show_everyone($selected, $banner);
			$selected = adrotate_filter_schedule($selected, $banner);

			if($adrotate_config['enable_advertisers'] == 'Y' AND ($banner->crate > 0 OR $banner->irate > 0)) {
				$selected = adrotate_filter_budget($selected, $banner);
			}
		} else {
			$selected = false;
		}
		
		if($selected) {
			$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);

			if($individual == true) $output .= '<div class="a'.$adrotate_config['adblock_disguise'].'-single a'.$adrotate_config['adblock_disguise'].'-'.$banner->id.'">';
			$output .= adrotate_ad_output($banner->id, 0, $banner->title, $banner->bannercode, $banner->tracker, $image);
			if($individual == true) $output .= '</div>';

			if($adrotate_config['stats'] == 1) {
				adrotate_count_impression($banner->id, 0, $site);
			}
		} else {
			$output .= adrotate_error('ad_expired', array($banner_id));
		}
		unset($banner);
		
		if($site > 0 AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			switch_to_blog($current_blog);
		}
	
	} else {
		$output .= adrotate_error('ad_no_id');
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_group
 Purpose:   Group output
 Added:		3.12.3
-------------------------------------------------------------*/
function adrotate_group($group_ids, $fallback = 0, $weight = 0, $site = 0) { 
	global $wpdb, $adrotate_config, $adrotate_debug;

	$output = $group_select = $weightoverride = $mobileoverride = $mobileosoverride = $showoverride = '';
	if($group_ids) {
		$license = get_site_option('adrotate_activate');
		$network = get_site_option('adrotate_network_settings');

		if($site > 0 AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			$current_blog = $wpdb->blogid;
			switch_to_blog($network['primary']);
		}

		$now = adrotate_now();

		$group_array = (preg_match('/,/is', $group_ids)) ? explode(",", $group_ids) : array($group_ids);
		$group_array = array_filter($group_array);

		foreach($group_array as $key => $value) {
			$group_select .= " `{$wpdb->prefix}adrotate_linkmeta`.`group` = {$value} OR";
		}
		$group_select = rtrim($group_select, " OR");

		// Grab settings to use from first group
		$group = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `id` = %d;", $group_array[0]));

		if($adrotate_debug['general'] == true) {
			echo "<p><strong>[DEBUG][adrotate_group] Group</strong><pre>"; 
			echo "Group ID: ".$group->id.', '.$group->name;
			echo "<br />Group mobile: "; echo ($group->mobile == 1) ? "yes" : "no";
			echo "<br />User is mobile: "; echo (adrotate_is_mobile()) ? "yes" : "no";
			echo "<br />User is tablet: "; echo (adrotate_is_tablet()) ? "yes" : "no";
			echo "<br />User iOS: "; echo (adrotate_is_ios()) ? "yes" : "no";
			echo "<br />User Android: "; echo (adrotate_is_android()) ? "yes" : "no";
			echo "</pre></p>";
		}

		if($group) {
			if($group->mobile == 1) {
				if(!adrotate_is_mobile() AND !adrotate_is_tablet()) { // Desktop
					$mobileoverride = "AND `{$wpdb->prefix}adrotate`.`desktop` = 'Y'";
				} else if(adrotate_is_mobile()) { // Phones
					$mobileoverride = "AND `{$wpdb->prefix}adrotate`.`mobile` = 'Y'";
				} else if(adrotate_is_tablet()) { // Tablets
					$mobileoverride = "AND `{$wpdb->prefix}adrotate`.`tablet` = 'Y'";
				}

				if(!adrotate_is_ios() AND !adrotate_is_android()) { // Other OS
					$mobileosoverride = "AND `{$wpdb->prefix}adrotate`.`os_other` = 'Y'";
				} else if(adrotate_is_ios()) { // iOS
					$mobileosoverride = "AND `{$wpdb->prefix}adrotate`.`os_ios` = 'Y'";
				} else if(adrotate_is_android()) { // Android
					$mobileosoverride = "AND `{$wpdb->prefix}adrotate`.`os_android` = 'Y'";
				}
			}

			$weightoverride = ($weight > 0) ? "AND `{$wpdb->prefix}adrotate`.`weight` >= {$weight} " : '';
			$fallback = ($fallback == 0) ? $group->fallback : $fallback;

			// Get all ads in all selected groups
			$ads = $wpdb->get_results(
				"SELECT 
					`{$wpdb->prefix}adrotate`.`id`, 
					`{$wpdb->prefix}adrotate`.`title`, 
					`{$wpdb->prefix}adrotate`.`bannercode`, 
					`{$wpdb->prefix}adrotate`.`image`, 
					`{$wpdb->prefix}adrotate`.`paid`, 
					`{$wpdb->prefix}adrotate`.`tracker`, 
					`{$wpdb->prefix}adrotate`.`show_everyone`, 
					`{$wpdb->prefix}adrotate`.`weight`,
					`{$wpdb->prefix}adrotate`.`crate`, 
					`{$wpdb->prefix}adrotate`.`irate`, 
					`{$wpdb->prefix}adrotate`.`budget`, 
					`{$wpdb->prefix}adrotate`.`cities`, 
					`{$wpdb->prefix}adrotate`.`countries`,
					`{$wpdb->prefix}adrotate_linkmeta`.`group`
				FROM 
					`{$wpdb->prefix}adrotate`, 
					`{$wpdb->prefix}adrotate_linkmeta` 
				WHERE 
					({$group_select}) 
					AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = 0 
					AND `{$wpdb->prefix}adrotate`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`ad` 
					{$mobileoverride}
					{$mobileosoverride}
					{$weightoverride}
					AND `{$wpdb->prefix}adrotate`.`paid` != 'N' 
					AND (`{$wpdb->prefix}adrotate`.`type` = 'active' 
						OR `{$wpdb->prefix}adrotate`.`type` = '2days' 
						OR `{$wpdb->prefix}adrotate`.`type` = '7days') 
				GROUP BY `{$wpdb->prefix}adrotate`.`id` 
				ORDER BY `{$wpdb->prefix}adrotate`.`id`;");
		
			if($ads) {
				if($adrotate_debug['general'] == true) {
					echo "<p><strong>[DEBUG][adrotate_group()] Selected ads</strong><pre>";
					foreach($ads as $ad) {
						print_r($ad->id.', '.$ad->title.'<br>');
					} 
					echo "</pre></p>"; 
				}			

				foreach($ads as $ad) {
					$selected[$ad->id] = $ad;

					$selected = adrotate_filter_show_everyone($selected, $ad);
					$selected = adrotate_filter_schedule($selected, $ad);
	
					if($adrotate_config['enable_advertisers'] == 'Y' AND ($ad->crate > 0 OR $ad->irate > 0)) {
						$selected = adrotate_filter_budget($selected, $ad);
					}

					if($adrotate_config['enable_geo'] > 0 AND $group->geo == 1) {
						$selected = adrotate_filter_location($selected, $ad);
					}
				}

				if($adrotate_debug['general'] == true) {
					echo "<p><strong>[DEBUG][adrotate_group] Reduced array based on settings</strong><pre>"; 
					print_r($selected); 
					echo "</pre></p>"; 
				}			

				$array_count = count($selected);
				if($array_count > 0) {
					$before = $after = '';
					$before = str_replace('%id%', $group_array[0], stripslashes(html_entity_decode($group->wrapper_before, ENT_QUOTES)));
					$after = str_replace('%id%', $group_array[0], stripslashes(html_entity_decode($group->wrapper_after, ENT_QUOTES)));

					$output .= '<div class="g'.$adrotate_config['adblock_disguise'].' g'.$adrotate_config['adblock_disguise'].'-'.$group->id.'">';

					// Kill dynamic mode for mobile users
					if($adrotate_config['mobile_dynamic_mode'] == 'Y' AND $group->modus == 1 AND (adrotate_is_mobile() OR adrotate_is_tablet())) {
						$group->modus = 0;
					}

					if($group->modus == 1) { // Dynamic ads
						$i = 1;

						// Limit group to save resources
						$amount = ($group->adspeed >= 10000) ? 10 : 20;
						
						// Randomize and trim output
						$selected = adrotate_shuffle($selected);
						foreach($selected as $key => $banner) {
							if($i <= $amount) {
								$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);
	
								$output .= '<div class="g'.$adrotate_config['adblock_disguise'].'-dyn a'.$adrotate_config['adblock_disguise'].'-'.$banner->id.' c-'.$i.'">';
								$output .= $before.adrotate_ad_output($banner->id, $group->id, $banner->title, $banner->bannercode, $banner->tracker, $image).$after;
								$output .= '</div>';
								$i++;
							}
						}
					} else if($group->modus == 2) { // Block of ads
						$block_count = $group->gridcolumns * $group->gridrows;
						if($array_count < $block_count) $block_count = $array_count;
						$columns = 1;

						for($i=1;$i<=$block_count;$i++) {
							$banner_id = adrotate_pick_weight($selected);

							$image = str_replace('%folder%', $adrotate_config['banner_folder'], $selected[$banner_id]->image);

							$output .= '<div class="g'.$adrotate_config['adblock_disguise'].'-col b'.$adrotate_config['adblock_disguise'].'-'.$group->id.' a'.$adrotate_config['adblock_disguise'].'-'.$selected[$banner_id]->id.'">';
							$output .= $before.adrotate_ad_output($selected[$banner_id]->id, $group->id, $selected[$banner_id]->title, $selected[$banner_id]->bannercode, $selected[$banner_id]->tracker, $image).$after;
							$output .= '</div>';

							if($columns == $group->gridcolumns AND $i != $block_count) {
								$output .= '</div><div class="g'.$adrotate_config['adblock_disguise'].' g'.$adrotate_config['adblock_disguise'].'-'.$group->id.'">';
								$columns = 1;
							} else {
								$columns++;
							}

							if($adrotate_config['stats'] == 1){
								adrotate_count_impression($selected[$banner_id]->id, $group->id, $site);
							}

							unset($selected[$banner_id]);
						}
					} else { // Default (single ad)
						$banner_id = adrotate_pick_weight($selected);

						$image = str_replace('%folder%', $adrotate_config['banner_folder'], $selected[$banner_id]->image);

						$output .= '<div class="g'.$adrotate_config['adblock_disguise'].'-single a'.$adrotate_config['adblock_disguise'].'-'.$selected[$banner_id]->id.'">';
						$output .= $before.adrotate_ad_output($selected[$banner_id]->id, $group->id, $selected[$banner_id]->title, $selected[$banner_id]->bannercode, $selected[$banner_id]->tracker, $image).$after;
						$output .= '</div>';

						if($adrotate_config['stats'] == 1){
							adrotate_count_impression($selected[$banner_id]->id, $group->id, $site);
						}
					}

					$output .= '</div>';

					unset($selected);
				} else {
					if($site > 0 AND adrotate_is_networked() AND $license['type'] == 'Developer') {
						switch_to_blog($current_blog);
					}
					$output .= adrotate_fallback($fallback, 'expired', $site);
				}
			} else { 
				if($site > 0 AND adrotate_is_networked() AND $license['type'] == 'Developer') {
					switch_to_blog($current_blog);
				}
				$output .= adrotate_fallback($fallback, 'unqualified', $site);
			}
		} else {
			$output .= adrotate_error('group_not_found', array($group_array[0]));
		}

		if($site > 0 AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			switch_to_blog($current_blog);
		}
	
	} else {
		$output .= adrotate_error('group_no_id');
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_shortcode
 Purpose:   Prepare function requests for calls on shortcodes
 Since:		0.7
-------------------------------------------------------------*/
function adrotate_shortcode($atts, $content = null) {
	global $adrotate_config;

	$banner_id = $group_ids = $fallback = $weight = $site = 0;
	if(!empty($atts['banner'])) $banner_id = trim($atts['banner'], "\r\t ");
	if(!empty($atts['group'])) $group_ids = trim($atts['group'], "\r\t ");
	if(!empty($atts['fallback'])) $fallback	= trim($atts['fallback'], "\r\t "); // Optional for groups (override)
	if(!empty($atts['weight']))	$weight	= trim($atts['weight'], "\r\t "); // Optional for groups (override)
	if(!empty($atts['site'])) $site = trim($atts['site'], "\r\t "); // Optional for site (override)

	$output = '';
	if($adrotate_config['w3caching'] == "Y") {
		$output .= '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
	
		if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
			$output .= 'echo adrotate_ad('.$banner_id.', true, 0, '.$site.');';
		}
	
		if($banner_id == 0 AND $group_ids > 0) { // Show group
			$output .= 'echo adrotate_group('.$group_ids.', '.$fallback.', '.$weight.', '.$site.');';
		}
	
		$output .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
	} else if($adrotate_config['borlabscache'] == "Y" AND function_exists('BorlabsCacheHelper') AND BorlabsCacheHelper()->willFragmentCachingPerform()) {
		$borlabsphrase = BorlabsCacheHelper()->getFragmentCachingPhrase();

		$output .= '<!--[borlabs cache start: '.$borlabsphrase.']--> ';
		if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
			$output .= 'echo adrotate_ad('.$banner_id.', true, 0, '.$site.');';
		}		
		if($banner_id == 0 AND $group_ids > 0) { // Show group
			$output .= 'echo adrotate_group('.$group_ids.', '.$fallback.', '.$weight.', '.$site.');';
		}
		$output .= ' <!--[borlabs cache end: '.$borlabsphrase.']-->';

		unset($borlabsphrase);
	} else {
		if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
			$output .= adrotate_ad($banner_id, true, 0, $site);
		}
	
		if($banner_id == 0 AND $group_ids > 0) { // Show group
			$output .= adrotate_group($group_ids, $fallback, $weight, $site);
		}
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_inject_posts
 Purpose:   Add an advert to a single page or post
 Added:		3.12.8
-------------------------------------------------------------*/
function adrotate_inject_posts($post_content) { 
	global $wpdb, $post, $adrotate_config, $adrotate_debug;

	$group_array = array();
	if(is_page()) {
		// Inject ads into page
		$ids = $wpdb->get_results("SELECT `id`, `page`, `page_loc`, `page_par` FROM `{$wpdb->prefix}adrotate_groups` WHERE `page_loc` > 0 AND  `page_loc` < 5;");
		
		foreach($ids as $id) {
			$pages = explode(",", $id->page);
			if(!is_array($pages)) $pages = array();

			if(in_array($post->ID, $pages)) {
				$group_array[$id->id] = array('location' => $id->page_loc, 'paragraph' => $id->page_par, 'ids' => $pages);
			}
		}
		unset($ids, $pages);
	}
	
	if(is_single()) {
		// Inject ads into posts in specified category
		$ids = $wpdb->get_results("SELECT `id`, `cat`, `cat_loc`, `cat_par` FROM `{$wpdb->prefix}adrotate_groups` WHERE `cat_loc` > 0 AND `cat_loc` < 5;");
		$wp_categories = get_terms('category', array('fields' => 'ids'));

		foreach($ids as $id) {
			$categories = explode(",", $id->cat);
			if(!is_array($categories)) $categories = array();

			foreach($wp_categories as &$value) {
				if(in_array($value, $categories)) {
					$group_array[$id->id] = array('location' => $id->cat_loc, 'paragraph' => $id->cat_par, 'ids' => $categories);
				}
			}
		}
		unset($ids, $wp_categories, $categories);
	}

	$group_array = adrotate_shuffle($group_array);	
	$group_count = count($group_array);

	if($adrotate_debug['general'] == true) {
		echo "<p><strong>[DEBUG][adrotate_inject_posts()] group_array</strong><pre>"; 
		echo "Group count: ".$group_count."</br>";
		print_r($group_array); 
		echo "</pre></p>"; 
	}

	if($group_count > 0) {
		$before = $after = $inside = 0;
		$advert_output = '';
		foreach($group_array as $group_id => $group) {
			if(is_page($group['ids']) OR has_category($group['ids'])) {
				// Caching or not?
				if($adrotate_config['w3caching'] == 'Y') {
					$advert_output = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
					$advert_output .= 'echo adrotate_group('.$group_id.');';
					$advert_output .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
				} else if($adrotate_config['borlabscache'] == "Y" AND function_exists('BorlabsCacheHelper') AND BorlabsCacheHelper()->willFragmentCachingPerform()) {
					$borlabsphrase = BorlabsCacheHelper()->getFragmentCachingPhrase();

					$advert_output = '<!--[borlabs cache start: '.$borlabsphrase.']-->';
					$advert_output .= 'echo adrotate_group('.$group_id.');';
					$advert_output .= '<!--[borlabs cache end: '.$borlabsphrase.']-->';

					unset($borlabsphrase);
				} else {
					$advert_output = adrotate_group($group_id);
				}

				// Advert in front of content
				if(($group['location'] == 1 OR $group['location'] == 3) AND $before == 0) {
					$post_content = $advert_output.$post_content;
					unset($group_array[$group_id]);
					$before = 1;
				}
	
				// Advert behind the content
				if(($group['location'] == 2 OR $group['location'] == 3) AND $after == 0) {
					$post_content = $post_content.$advert_output;
					unset($group_array[$group_id]);
					$after = 1;
				}

				// Adverts inside the content
				if($group['location'] == 4) {
				    $paragraphs = explode('</p>', $post_content);
					$paragraph_count = count($paragraphs);
					$count_p = ($group['paragraph'] == 99) ? ceil($paragraph_count / 2) : $group['paragraph'];

				    foreach($paragraphs as $index => $paragraph) {
				        if(trim($paragraph)) {
				            $paragraphs[$index] .= '</p>';
				        }

				        if($count_p == $index + 1 AND $inside == 0) {
				            $paragraphs[$index] .= $advert_output;
							unset($group_array[$group_id]);
				            $inside = 1;
				        }
				    }

				    $inside = 0; // Reset for the next paragraph
				    $post_content = implode('', $paragraphs);
					unset($paragraphs, $paragraph_count);
				}
			}
		}
		unset($group_array, $before, $after, $inside, $advert_output);
	}

	return $post_content;
}

/*-------------------------------------------------------------
 Name:      adrotate_preview
 Purpose:   Show preview of selected ad (Dashboard)
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_preview($banner_id) {
	global $wpdb, $adrotate_config, $adrotate_debug;

	if($banner_id) {
		$now = adrotate_now();
		
		$banner = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $banner_id));

		if($adrotate_debug['general'] == true) {
			echo "<p><strong>[DEBUG][adrotate_preview()] Ad information</strong><pre>"; 
			print_r($banner); 
			echo "</pre></p>"; 
		}			

		if($banner) {
			$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);		
			$output = adrotate_ad_output($banner->id, 0, $banner->title, $banner->bannercode, $banner->tracker, $image);
		} else {
			$output = adrotate_error('ad_expired');
		}
	} else {
		$output = adrotate_error('ad_no_id');
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_ad_output
 Purpose:   Prepare the output for viewing
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_ad_output($id, $group = 0, $name, $bannercode, $tracker, $image) {
	global $blog_id, $adrotate_debug, $adrotate_config;

	$banner_output = $bannercode;
	$banner_output = stripslashes(htmlspecialchars_decode($banner_output, ENT_QUOTES));

	if($adrotate_config['stats'] > 0 AND $tracker == "Y") {
		if(empty($blog_id) or $blog_id == '') {
			$blog_id = 0;
		}

		$tracking_pixel = "data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==";
		
		if($adrotate_config['stats'] == 1) { // Internal tracker
			preg_match_all('/<a[^>](?:.*?)>/i', $banner_output, $matches, PREG_SET_ORDER);
			if(isset($matches[0])) {
				$banner_output = str_replace('<a ', '<a data-track="'.adrotate_hash($id, $group, $blog_id).'" ', $banner_output);
				foreach($matches[0] as $value) {
					if(preg_match('/<a[^>]+class=\"(.+?)\"[^>]*>/i', $value, $regs)) {
					    $result = $regs[1]." gofollow";
						$banner_output = str_ireplace('class="'.$regs[1].'"', 'class="'.$result.'"', $banner_output);	    
					} else {
						$banner_output = str_ireplace('<a ', '<a class="gofollow" ', $banner_output);
					}
					unset($value, $regs, $result);
				}
			}
			if($adrotate_debug['timers'] == true) {
				$banner_output = str_ireplace('<a ', '<a data-debug="1" ', $banner_output);
			}
		}

		if($adrotate_config['stats'] == 2) { // Piwik Analytics
			preg_match_all('/<(?:a|img|object|embed|iframe)[^>](?:.*?)>/i', $banner_output, $matches, PREG_SET_ORDER);
			
			if(isset($matches[0])) {
				$click_event = "data-track-content data-content-name=\"$name\" ";
				$content_piece = (!empty($image)) ? basename($image) : $name;
				$impression_event = "data-content-piece=\"$content_piece\" ";

				// Image banner
				if(stripos($banner_output, '<a') !== false AND stripos($banner_output, '<img') !== false) {
					if(!preg_match('/<a[^>]+data-track-content[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<a ', '<a '.$click_event, $banner_output);
					}
					if(!preg_match('/<img[^>]+data-content-piece[^>]*>/i', $banner_output, $img)) {
						$banner_output = str_ireplace('<img ', '<img '.$impression_event, $banner_output);
					}
				}

				// Text banner (With tagged tracking pixel for impressions)
				if(stripos($banner_output, '<a') !== false AND stripos($banner_output, '<img') === false) {
					if(!preg_match('/<a[^>]+data-track-content[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<a ', '<a '.$click_event, $banner_output);
					}
					$banner_output .= '<img width="0" height="0" src="'.$tracking_pixel.'" '.$impression_event.'/>';
				}

				// HTML5/iFrame advert (Only supports impressions)
				if(stripos($banner_output, '<iframe') !== false) {
					if(preg_match('/<iframe[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<iframe ', '<iframe '.$click_event, $banner_output);
					}
				}
				unset($content_piece, $url, $img);
			}
		}

		if($adrotate_config['stats'] == 3 OR $adrotate_config['stats'] == 4) { // Google Analytics
			preg_match_all('/<(?:a|img|object|iframe)[^>](?:.*?)>/i', $banner_output, $matches, PREG_SET_ORDER);

			if(isset($matches[0])) {
				if($adrotate_config['stats'] == 3) { // analytics.js
					// ga('send', 'event', [eventCategory], [eventAction], [eventLabel], [eventValue], [fieldsObject]);
					// ga('send', 'event', 'Banner', 'click', 'Banner name', 1.00, {'nonInteraction': 1});
					$click_event = "ga('send', 'event', 'banner', 'click', '$name', ".$adrotate_config['google_click_value'].", {'nonInteraction': 1});";
					$impression_event = "ga('send', 'event', 'banner', 'impression', '$name', ".$adrotate_config['google_impression_value'].", {'nonInteraction': 1});";
				}
				if($adrotate_config['stats'] == 4) { // gtag.js
					// gtag('event', 'event_name', {'event_category': categoryName, 'event_label': 'Banner name', 'value': 1.00, 'non_interaction': true});
					// gtag('event', 'Click', {'event_category': 'Banner', 'event_label': labelName, 'value': 1.00, 'non_interaction': true});
					$click_event = "gtag('event', 'click', {'event_category': 'banner', 'event_label': '$name', 'value': ".$adrotate_config['google_click_value'].",  'non_interaction': true});";
					$impression_event = "gtag('event', 'impression', {'event_category': 'banner', 'event_label': '$name', 'value': ".$adrotate_config['google_impression_value'].", 'non_interaction': true});";
				}
				
				// Image banner
				if(stripos($banner_output, '<a') !== false AND stripos($banner_output, '<img') !== false) {
					if(!preg_match('/<a[^>]+onClick[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<a ', '<a onClick="'.$click_event.'" ', $banner_output);
					}
					if(!preg_match('/<img[^>]+onload[^>]*>/i', $banner_output, $img)) {
						$banner_output = str_ireplace('<img ', '<img onload="'.$impression_event.'" ', $banner_output);
					}
				}

				// Text banner (With tagged tracking pixel for impressions)
				if(stripos($banner_output, '<a') !== false AND stripos($banner_output, '<img') === false) {
					if(!preg_match('/<a[^>]+onClick[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<a ', '<a onClick="'.$click_event.'" ', $banner_output);
					}
					$banner_output .= '<img width="0" height="0" src="'.$tracking_pixel.'" onload="'.$impression_event.'" />';
				}

				// HTML5/iFrame advert (Only supports impressions)
				if(stripos($banner_output, '<iframe') !== false) {
					if(!preg_match('/<iframe[^>]+onload[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<iframe ', '<iframe onload="'.$impression_event.'" ', $banner_output);
					}
				}
				unset($url, $img, $click_event, $impression_event);
			}
		}
		unset($matches);
	}

	$image = apply_filters('adrotate_apply_photon', $image);

	$banner_output = str_replace('%title%', $name, $banner_output);		
	$banner_output = str_replace('%random%', rand(100000,999999), $banner_output);
	$banner_output = str_replace('%asset%', $image, $banner_output); // Replaces %image%
	$banner_output = str_replace('%image%', $image, $banner_output); // Depreciated, remove in AdRotate 5.0
	$banner_output = str_replace('%id%', $id, $banner_output);
	$banner_output = do_shortcode($banner_output);

	return $banner_output;
}

/*-------------------------------------------------------------
 Name:      adrotate_fallback
 Purpose:   Fall back to the set group or show an error if no fallback is set
 Added:		2.6
-------------------------------------------------------------*/
function adrotate_fallback($group, $case, $site = 0) {

	$fallback_output = '';
	if($group > 0) {
		$fallback_output = adrotate_group($group, 0, 0, $site);
	} else {
		if($case == 'expired') {
			$fallback_output = adrotate_error('ad_expired');
		}
		
		if($case == 'unqualified') {
			$fallback_output = adrotate_error('ad_unqualified');
		}
	}
	
	return $fallback_output;
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_scripts
 Purpose:   Add required scripts to site head
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_custom_scripts() {
	global $adrotate_config;

	$in_footer = ($adrotate_config['jsfooter'] == "Y") ? true : false;
	
	if($adrotate_config['jquery'] == 'Y') wp_enqueue_script('jquery', false, false, null, $in_footer);
	if(get_option('adrotate_dynamic_required') > 0) wp_enqueue_script('adrotate-dyngroup', plugins_url('/library/jquery.adrotate.dyngroup.js', __FILE__), false, null, $in_footer);

	// Make clicktracking and impression tracking a possibility
	if($adrotate_config['stats'] == 1) {
		wp_enqueue_script('adrotate-clicktracker', plugins_url('/library/jquery.adrotate.clicktracker.js', __FILE__), false, null, $in_footer);
		wp_localize_script('adrotate-clicktracker', 'click_object', array('ajax_url' => admin_url('admin-ajax.php')));
		wp_localize_script('adrotate-dyngroup', 'impression_object', array('ajax_url' => admin_url( 'admin-ajax.php')));
	}

	if(!$in_footer) {
		add_action('wp_head', 'adrotate_custom_javascript');
	} else {
		add_action('wp_footer', 'adrotate_custom_javascript', 100);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_javascript
 Purpose:   Add required JavaScript to site
 Since:		3.10.5
-------------------------------------------------------------*/
function adrotate_custom_javascript() {
	global $wpdb, $adrotate_config;

	$groups = $groups_network = array();
	// Grab group settings from primary site
	$network = get_site_option('adrotate_network_settings');
	$license = get_site_option('adrotate_activate');
	if(adrotate_is_networked() AND $license['type'] == 'Developer') {
		$current_blog = $wpdb->blogid;
		switch_to_blog($network['primary']);
		$groups_network = $wpdb->get_results("SELECT `id`, `adspeed`, `repeat_impressions` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `modus` = 1 ORDER BY `id` ASC;", ARRAY_A);
		switch_to_blog($current_blog);
	}

	$groups = $wpdb->get_results("SELECT `id`, `adspeed`, `repeat_impressions` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `modus` = 1 ORDER BY `id` ASC;", ARRAY_A);
	$groups = array_merge($groups, $groups_network);

	if(count($groups) > 0) {
		$output = "<!-- AdRotate JS -->\n";
		$output .= "<script type=\"text/javascript\">\n";
		$output .= "jQuery(document).ready(function(){if(jQuery.fn.gslider) {\n";
		foreach($groups as $group) {
			$output .= "\tjQuery('.g".$adrotate_config['adblock_disguise']."-".$group['id']."').gslider({groupid:".$group['id'].",speed:".$group['adspeed'].",repeat_impressions:'".$group['repeat_impressions']."'});\n";
		}
		$output .= "}});\n";
		$output .= "</script>\n";
		$output .= "<!-- /AdRotate JS -->\n\n";
		unset($groups);
		echo $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_css
 Purpose:   Add required CSS to site head
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_custom_css() {
	global $wpdb, $adrotate_config;
	
	$output = "\n<!-- This site is using AdRotate v".ADROTATE_DISPLAY." to display their advertisements - https://ajdg.solutions/products/adrotate-for-wordpress/ -->\n";

	$groups = $groups_network = array();
	// Grab group settings from primary site
	$network = get_site_option('adrotate_network_settings');
	$license = get_site_option('adrotate_activate');
	if(adrotate_is_networked() AND $license['type'] == 'Developer') {
		$current_blog = $wpdb->blogid;
		switch_to_blog($network['primary']);
		$groups_network = $wpdb->get_results("SELECT `id`, `modus`, `gridrows`, `gridcolumns`, `adwidth`, `adheight`, `admargin`, `admargin_bottom`, `admargin_left`, `admargin_right`, `align` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;", ARRAY_A);
		switch_to_blog($current_blog);
	}

	$groups = $wpdb->get_results("SELECT `id`, `modus`, `gridrows`, `gridcolumns`, `adwidth`, `adheight`, `admargin`, `admargin_bottom`, `admargin_left`, `admargin_right`, `align` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;", ARRAY_A);
	$groups = array_merge($groups, $groups_network);

	if(count($groups) > 0) {
		$output_css = "\t.g".$adrotate_config['adblock_disguise']." { margin:0px; padding:0px; overflow:hidden; line-height:1; zoom:1; }\n";
		$output_css .= "\t.g".$adrotate_config['adblock_disguise']." img { height:auto; }\n";
		$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-col { position:relative; float:left; }\n";
		$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-col:first-child { margin-left: 0; }\n";
		$output_css .= "\t.g".$adrotate_config['adblock_disguise']."-col:last-child { margin-right: 0; }\n";

		foreach($groups as $group) {
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
		}
		$output_css .= "\t@media only screen and (max-width: 480px) {\n";
		$output_css .= "\t\t.g".$adrotate_config['adblock_disguise']."-col, .g".$adrotate_config['adblock_disguise']."-dyn, .g".$adrotate_config['adblock_disguise']."-single { width:100%; margin-left:0; margin-right:0; }\n";
		$output_css .= "\t}\n";
		unset($groups);
	}

	if(isset($output_css) OR $adrotate_config['widgetpadding'] == "Y") {
		$output .= "<!-- AdRotate CSS -->\n";
		$output .= "<style type=\"text/css\" media=\"screen\">\n";
		if(isset($output_css)) {
			$output .= $output_css;
			unset($output_css);
		}
		if($adrotate_config['widgetpadding'] == "Y") { 
			$output .= ".adrotate_widgets, .ajdg_bnnrwidgets, .ajdg_grpwidgets { overflow:hidden; padding:0; }\n";
		}
		$output .= "</style>\n";
		$output .= "<!-- /AdRotate CSS -->\n\n";
	}

	echo $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_profile_fields
 Purpose:   Add profile fields to user creation and editing dashboards
 Since:		3.22.2b1
-------------------------------------------------------------*/
function adrotate_custom_profile_fields($user){
    
    if(current_user_can('adrotate_advertiser_manage')) {
		if($user != 'add-new-user') {
		    $advertiser = get_user_meta($user->ID, 'adrotate_is_advertiser', 1);
		    $permissions = get_user_meta($user->ID, 'adrotate_permissions', 1);
			if(!isset($permissions['edit'])) $permissions['edit'] = 'N';
			if(!isset($permissions['mobile'])) $permissions['mobile'] = 'N';
			if(!isset($permissions['geo'])) $permissions['geo'] = 'N';
		    $notes = get_user_meta($user->ID, 'adrotate_notes', 1);
		} else {
			$advertiser = 'N';
			$permissions = array('edit' => 'N', 'mobile' => 'N', 'geo' => 'N');
			$notes = '';
		}
		?>
	    <h3><?php _e('AdRotate Advertiser', 'adrotate-pro'); ?></h3>
	    <table class="form-table">
	      	<tr>
		        <th valign="top"><?php _e('Enable', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_is_advertiser"><input tabindex="1" type="checkbox" name="adrotate_is_advertiser" <?php if($advertiser == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Is this user an AdRotate Advertiser?', 'adrotate-pro'); ?></label><br />
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('Permissions', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_can_edit"><input tabindex="1" type="checkbox" name="adrotate_can_edit" <?php if($permissions['edit'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can create and edit their own adverts?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_mobile"><input tabindex="1" type="checkbox" name="adrotate_can_mobile" <?php if($permissions['mobile'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can specify mobile devices?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_geo"><input tabindex="1" type="checkbox" name="adrotate_can_geo" <?php if($permissions['geo'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can use Geo Targeting?', 'adrotate-pro'); ?></label><br />
		        	<em><?php _e('These settings only have effect if you enable the global setting in AdRotate Settings.', 'adrotate-pro'); ?></em>
		        </td>
	      	</tr>
		    <tr>
				<th valign="top"><label for="adrotate_notes"><?php _e('Notes', 'adrotate-pro'); ?></label></th>
				<td>
					<textarea tabindex="3" name="adrotate_notes" cols="50" rows="5"><?php echo esc_attr($notes); ?></textarea><br />
					<em><?php _e('Also visible in the advertiser profile.', 'adrotate-pro'); ?></em>
					</td>
			</tr>
	    </table>
<?php
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_nonce_error
 Purpose:   Display a formatted error if Nonce fails
 Since:		3.7.4.2
-------------------------------------------------------------*/
function adrotate_nonce_error() {
	echo '	<h2 style="text-align: center;">'.__('Oh no! Something went wrong!', 'adrotate-pro').'</h2>';
	echo '	<p style="text-align: center;">'.__('WordPress was unable to verify the authenticity of the url you have clicked. Verify if the url used is valid or log in via your browser.', 'adrotate-pro').'</p>';
	echo '	<p style="text-align: center;">'.__('If you have received the url you want to visit via email, you are being tricked!', 'adrotate-pro').'</p>';
	echo '	<p style="text-align: center;">'.__('Contact support if the issue persists:', 'adrotate-pro').' <a href="https://ajdg.solutions/forums/?utm_campaign=forums&utm_medium=nonce-error&utm_source=adrotate-pro" title="AdRotate Support" target="_blank">AJdG Solutions Support</a>.</p>';
}

/*-------------------------------------------------------------
 Name:      adrotate_error
 Purpose:   Show errors for problems in using AdRotate, should they occur
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_error($action, $arg = null) {
	global $adrotate_debug;

	switch($action) {
		// Ads
		case "ad_expired" :
			if($adrotate_debug['general'] == true) {
				$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, Ad is not available at this time due to schedule/budgeting/geolocation/mobile restrictions or does not exist!', 'adrotate-pro').'</span>';
			} else {
				$result = '<!-- '.__('Error, Ad is not available at this time due to schedule/budgeting/geolocation/mobile restrictions!', 'adrotate-pro').' -->';
			}
			return $result;
		break;
		
		case "ad_unqualified" :
			if($adrotate_debug['general'] == true) {
				$result = '<span style="font-weight: bold; color: #f00;">'.__('Either there are no banners, they are disabled or none qualified for this location!', 'adrotate-pro').'</span>';
			} else {
				$result = '<!-- '.__('Either there are no banners, they are disabled or none qualified for this location!', 'adrotate-pro').' -->';
			}
			return $result;
		break;
		
		case "ad_no_id" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, no Ad ID set! Check your syntax!', 'adrotate-pro').'</span>';
			return $result;
		break;

		// Groups
		case "group_no_id" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, no group ID set! Check your syntax!', 'adrotate-pro').'</span>';
			return $result;
		break;

		case "group_not_found" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, group does not exist! Check your syntax!', 'adrotate-pro').' (ID: '.$arg[0].')</span>';
			return $result;
		break;

		// Database
		case "db_error" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('There was an error locating the database tables for AdRotate. Please deactivate and re-activate AdRotate from the plugin page!!', 'adrotate-pro').'<br />'.__('If this does not solve the issue please seek support at', 'adrotate-pro').' <a href="https://ajdg.solutions/forums/forum/adrotate-for-wordpress/?utm_campaign=adrotate-forum&utm_medium=error&utm_source=adrotate-pro">ajdg.solutions/forums/forum/adrotate-for-wordpress/</a></span>';
			return $result;
		break;

		// Misc
		default:
			$result = '<span style="font-weight: bold; color: #f00;">'.__('An unknown error occured.', 'adrotate-pro').' (ID: '.$arg[0].')</span>';
			return $result;
		break;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_error
 Purpose:   Show errors for problems in using AdRotate
 Since:		3.19.1
-------------------------------------------------------------*/
function adrotate_dashboard_error() {
	global $adrotate_config;

	// License
	$license = (adrotate_is_networked()) ? get_site_option('adrotate_activate') : get_option('adrotate_activate');
	if($license['status'] == 0) {
		$error['adrotate_license'] = __('You did not yet activate your AdRotate Pro license. Activate and get updates, premium support and access to AdRotate Geo!', 'adrotate-pro'). ' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=license').'">'.__('Activate license', 'adrotate-pro').'</a>!';
	}

	if($license['type'] == 'Network') {
		$error['adrotate_license_type'] = __('You have a Network license, these are being depreciated. Please contact AdRotate Support with your order number and license key for a FREE upgrade to a Developer License!', 'adrotate-pro'). ' <a href="'.admin_url('/admin.php?page=adrotate').'">Contact Support</a>!';
	}

	// Adverts
	$status = get_option('adrotate_advert_status');
	$adrotate_notifications	= get_option("adrotate_notifications");
	if($adrotate_notifications['notification_dash'] == "Y") {
		if($status['expired'] > 0 AND $adrotate_notifications['notification_dash_expired'] == "Y") {
			$error['advert_expired'] = sprintf(_n('One advert is expired.', '%1$s adverts expired!', $status['expired'], 'adrotate-pro'), $status['expired']).' <a href="'.admin_url('admin.php?page=adrotate-ads').'">'.__('Check adverts', 'adrotate-pro').'</a>!';
		} 
		if($status['expiressoon'] > 0 AND $adrotate_notifications['notification_dash_soon'] == "Y") {
			$error['advert_soon'] = sprintf(_n('One advert expires soon.', '%1$s adverts are almost expiring!', $status['expiressoon'], 'adrotate-pro'), $status['expiressoon']).' <a href="'.admin_url('admin.php?page=adrotate-ads').'">'.__('Check adverts', 'adrotate-pro').'</a>!';
		} 
	}
	if($status['error'] > 0) {
		$error['advert_config'] = sprintf(_n('One advert with configuration errors.', '%1$s adverts have configuration errors!', $status['error'], 'adrotate-pro'), $status['error']).' <a href="'.admin_url('admin.php?page=adrotate-ads').'">'.__('Check adverts', 'adrotate-pro').'</a>!';
	}

	// Caching
	if($adrotate_config['w3caching'] == "Y" AND !is_plugin_active('w3-total-cache/w3-total-cache.php')) {
		$error['w3tc_not_active'] = __('You have enabled caching support but W3 Total Cache is not active on your site!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=misc').'">'.__('Disable W3 Total Cache Support', 'adrotate-pro').'</a>.';
	}
	if($adrotate_config['w3caching'] == "Y" AND !defined('W3TC_DYNAMIC_SECURITY')) {
		$error['w3tc_no_hash'] = __('You have enable caching support but the W3TC_DYNAMIC_SECURITY definition is not set.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=misc').'">'.__('How to configure W3 Total Cache', 'adrotate-pro').'</a>.';
	}

	if($adrotate_config['borlabscache'] == "Y" AND !class_exists('\Borlabs\Factory') AND \Borlabs\Factory::get('Cache\Config')->get('cacheActivated') != 'yes') {
		$error['borlabs_not_active'] = __('You have enable caching support but Borlabs Cache is not active on your site!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=misc').'">'.__('Disable Borlabs Cache Support', 'adrotate-pro').'</a>.';
	}
	if(class_exists('\Borlabs\Factory') AND \Borlabs\Factory::get('Cache\Config')->get('cacheActivated') == 'yes') {
		$borlabscache = '';
		if(class_exists('\Borlabs\Factory')) {
			$borlabscache = \Borlabs\Factory::get('Cache\Config')->get('fragmentCaching');
		}
		if($adrotate_config['borlabscache'] == "Y" AND $borlabscache == '') {
			$error['borlabs_fragment_error'] = __('You have enabled Borlabs Cache support but Fragment caching is not enabled!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=borlabs-cache-fragments').'">'.__('Enable Fragment Caching', 'adrotate-pro').'</a>.';
		}
	}

	// Notifications
	if($adrotate_notifications['notification_email'] == 'Y' AND $adrotate_notifications['notification_mail_geo'] == 'N' AND $adrotate_notifications['notification_mail_status'] == 'N' AND $adrotate_notifications['notification_mail_queue'] == 'N' AND $adrotate_notifications['notification_mail_approved'] == 'N' AND $adrotate_notifications['notification_mail_rejected'] == 'N') {
		$error['mail_not_configured'] = __('You have enabled email notifications but did not select anything to be notified about. You are wasting server resources!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=notifications').'">'.__('Set up notifications', 'adrotate-pro').'</a>.';
	}

	// Geo Related
	$lookups = get_option('adrotate_geo_requests');

	if($license['status'] == 0 AND $adrotate_config['enable_geo'] == 5) {
		$error['geo_license'] = __('The AdRotate Geo service can only be used after you activate your license for this website.', 'adrotate-pro'). ' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=license').'">'.__('Activate license', 'adrotate-pro').'</a>!';
	}
	if(($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4 OR $adrotate_config['enable_geo'] == 5) AND $lookups > 0 AND $lookups < 1000) {
		$error['geo_lookups'] = sprintf(__('You are running out of Geo Lookups for AdRotate. You have less than %d remaining lookups.', 'adrotate-pro'), $lookups);
	}
	if($adrotate_config['enable_geo'] == 5 AND $lookups < 1) {
		$error['geo_adrotategeo'] = __('AdRotate Geo is no longer working because you have no more lookups for today. This resets at midnight UTC/GMT.', 'adrotate-pro');
	}
	if(($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4) AND $lookups < 1) {
		$error['geo_maxmind'] = __('MaxMind Geo Targeting is no longer working because you have no more lookups. Buy more lookups from the Maxmind website!', 'adrotate-pro');
	}
	if(($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4) AND (strlen($adrotate_config["geo_email"]) < 1 OR strlen($adrotate_config["geo_pass"]) < 1)) {
		$error['geo_maxmind_details'] = __('Geo Targeting is not working because your MaxMind account details are incomplete.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=geo').'">'.__('Set up Geo Targeting', 'adrotate-pro').'</a>.';
	}
	if($adrotate_config['enable_geo'] == 6 AND !isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
		$error['geo_cloudflare_header'] = __('Geo Targeting is not working. Check if IP Geolocation is enabled in your CloudFlare account or choose another Geo Service.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=geo').'">'.__('Configure Geo Targeting', 'adrotate-pro').'</a>.';
	}
	if($adrotate_config['enable_geo'] == 7 AND strlen($adrotate_config["geo_pass"]) < 1) {
		$error['geo_maxmind_details'] = __('Geo Targeting is not working because your Ipstack account API key is missing.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=geo').'">'.__('Set up Geo Targeting', 'adrotate-pro').'</a>.';
	}

	// Misc
	if(!is_writable(WP_CONTENT_DIR.'/'.$adrotate_config['banner_folder'])) {
		$error['banners_folder'] = __('Your AdRotate Banner folder is not writable or does not exist.', 'adrotate-pro').' <a href="https://ajdg.solutions/manuals/adrotate-manuals/manage-banner-images/?utm_campaign=adrotate-image-manual&utm_medium=dashboard-notifications&utm_source=adrotate-pro" target="_blank">'.__('Set up your banner folder', 'adrotate-pro').'</a>.';
	}

	$error = (isset($error) AND is_array($error)) ? $error : false;

	return $error;
}

/*-------------------------------------------------------------
 Name:      adrotate_notifications_dashboard
 Purpose:   Show dashboard notifications
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_notifications_dashboard() {
	if(current_user_can('adrotate_ad_manage')) {
		$adrotate_has_error = adrotate_dashboard_error();
		if($adrotate_has_error) {
			echo '<div class="error" style="padding: 0; margin: 0;">';
			echo '	<div class="ajdg_notification">';
			echo '	  <div class="text">'.__('AdRotate has detected', 'adrotate-pro').' '._n('one issue that requires', 'several issues that require', count($adrotate_has_error), 'adrotate-pro').' '.__('your attention', 'adrotate-pro').':<br /><span>';
			foreach($adrotate_has_error as $error => $message) {
				echo '&raquo; '.$message.'<br />';				
			}
			echo '	  </span></div>';
			echo '	  <div class="icon"><img title="Logo" src="'.plugins_url('/images/logo-60x60.png', __FILE__).'" alt=""/></div>';
			echo '	</div>';
			echo '</div>';
		}
	
		$page = (isset($_GET['page'])) ? $_GET['page'] : '';
		if(strpos($page, 'adrotate') !== false) {

			if(isset($_GET['hide']) AND $_GET['hide'] == 2) update_option('adrotate_hide_review', 1);
			if(isset($_GET['hide']) AND $_GET['hide'] == 3) update_option('adrotate_hide_competition', 1);

			$review_banner = get_option('adrotate_hide_review');
			if($review_banner != 1 AND $review_banner < (adrotate_now() - 2419200)) {
				echo '<div class="updated" style="padding: 0; margin: 0;">';
				echo '	<div class="ajdg_notification">';
				echo '		<div class="button_div"><a class="button" target="_blank" href="https://wordpress.org/support/view/plugin-reviews/adrotate?rate=5#postform">Rate AdRotate</a></div>';
				echo '		<div class="text">If you like <strong>AdRotate Pro</strong> please let the world know that you do. Thanks for your support!<br /><span>If you have questions, suggestions or something else that doesn\'t belong in a review, please <a href="https://ajdg.solutions/forums/forum/adrotate-for-wordpress/?utm_campaign=adrotate-forum&utm_medium=review-banner&utm_source=adrotate-pro" target="_blank">get in touch</a>!</span></div>';
				echo '		<a class="close_notification" href="admin.php?page=adrotate&hide=2"><img title="Close" src="'.plugins_url('/images/icon-close.png', __FILE__).'" alt=""/></a>';
				echo '		<div class="icon"><img title="Logo" src="'.plugins_url('/images/logo-60x60.png', __FILE__).'" alt=""/></div>';
				echo '	</div>';
				echo '</div>';
			}
	
			$competition_banner = get_option('adrotate_hide_competition');
			if($competition_banner != 1) {
				$adrotate_has_competition = adrotate_check_competition();
				if($adrotate_has_competition) {
					echo '<div class="updated" style="padding: 0; margin: 0;">';
					echo '	<div class="ajdg_notification">';
					echo '		<div class="button_div"><a class="button button_large" data-slug="adrotate-switch" href="'.admin_url('plugin-install.php?tab=search&s=adrotate+switch+adegans').'" aria-label="Install AdRotate Switch now" data-name="AdRotate Switch">Get AdRotate Switch</a></div>';
					echo '		<div class="text">'.__('AdRotate found', 'adrotate-pro').' '._n('one plugin', 'several plugins', count($adrotate_has_competition), 'adrotate-pro').' '.__('that can be imported', 'adrotate-pro').':<br /><span>';
					foreach($adrotate_has_competition as $plugin) {
						echo '&raquo; '.$plugin.'<br />';				
					}
					echo '		</span>'.__('Configured plugins can be imported into AdRotate! What is', 'adrotate-pro').' <a target="_blank" href="https://ajdg.solutions/products/adrotate-switch/?utm_campaign=pk_campaign=adrotate-pro&pk_kwd=switch-banner">AdRotate Switch</a>?</div>';
					echo '		<a class="close_notification" href="admin.php?page=adrotate&hide=3"><img title="Close" src="'.plugins_url('/images/icon-close.png', __FILE__).'" alt=""/></a>';
					echo '		<div class="icon"><img title="Logo" src="'.plugins_url('/images/logo-60x60.png', __FILE__).'" alt=""/></div>';
					echo '	</div>';
					echo '</div>';
				}
			}
		}
	}

	if(isset($_GET['upgrade']) AND $_GET['upgrade'] == 1) adrotate_check_upgrade();
	$adrotate_db_version = get_option("adrotate_db_version");
	$adrotate_version = get_option("adrotate_version");
	if($adrotate_db_version['current'] < ADROTATE_DB_VERSION OR $adrotate_version['current'] < ADROTATE_VERSION) {
		echo '<div class="error" style="padding: 0; margin: 0;">';
					echo '	<div class="ajdg_notification">';
		echo '		<div class="button_div"><a class="button" href="admin.php?page=adrotate&upgrade=1">'.__('Finish Update', 'adrotate-pro').'</a></div>';
		echo '		<div class="text text">'.__('You have almost completed updating <strong>AdRotate</strong> to version', 'adrotate-pro').' <strong>'.ADROTATE_DISPLAY.'</strong>!<br /><span>'.__('To complete the update click the button on the left. This may take a few seconds to complete!', 'adrotate-pro').'</span></div>';
		echo '		<div class="icon"><img title="Logo" src="'.plugins_url('/images/logo-60x60.png', __FILE__).'" alt=""/></div>';
		echo '	</div>';
		echo '</div>';
	}

	if(isset($_GET['tasks']) AND $_GET['tasks'] == 1) adrotate_check_schedules();
}

/*-------------------------------------------------------------
 Name:      adrotate_welcome_pointer
 Purpose:   Show dashboard pointers
 Since:		3.9.14
-------------------------------------------------------------*/
function adrotate_welcome_pointer() {
    $pointer_content = '<h3>AdRotate '.ADROTATE_DISPLAY.'</h3>';
    $pointer_content .= '<p>'.__('Thanks for choosing AdRotate. Everything related to AdRotate is in this menu. If you need help getting started take a look at the', 'adrotate-pro').' <a href="http:\/\/ajdg.solutions\/manuals\/adrotate-manuals\/" target="_blank">'.__('manuals', 'adrotate-pro').'</a> '.__('and', 'adrotate-pro').' <a href="https:\/\/ajdg.solutions\/forums\/forum\/adrotate-for-wordpress\/" target="_blank">'.__('forums', 'adrotate-pro').'</a>. '.__('You can also ask questions via', 'adrotate-pro').' <a href="admin.php?page=adrotate">'.__('email', 'adrotate-pro').'</a> '.__('if you have a valid license.', 'adrotate-pro').' These links are also available in the help tab in the top right.</p>';

    $pointer_content .= '<p><strong>'.__('Ad blockers', 'adrotate-pro').'</strong><br />'.__('Disable your ad blocker in your browser so your adverts and dashboard show up correctly. Use', 'adrotate-pro').' <a href="admin.php?page=adrotate-settings">AdBlock disguise</a> '.__('to help your adverts avoid ad blockers.', 'adrotate-pro').'</p>';
?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#toplevel_page_adrotate').pointer({
				'content':'<?php echo $pointer_content; ?>',
				'position':{ 'edge':'left', 'align':'middle'	},
				close: function() {
	                $.post(ajaxurl, {
	                    pointer:'adrotate_pro',
	                    action:'dismiss-wp-pointer'
	                });
				}
			}).pointer("open");
		});
	</script>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_help_info
 Purpose:   Help tab on all pages
 Since:		3.10.17
-------------------------------------------------------------*/
function adrotate_help_info() {
    $screen = get_current_screen();

    $screen->add_help_tab(array(
        'id' => 'adrotate_thanks',
        'title' => 'Thanks to you',
        'content' => '<h4>Thank you for using AdRotate</h4>'.
        '<p>AdRotate is growing to be one of the most popular WordPress plugins for Advertising and is a household name for many companies around the world. AdRotate wouldn\'t be possible without your support and my life wouldn\'t be what it is today without your help.</p><p><em>- Arnan</em></p>'.

        '<p><strong>Social:</strong> <a href="https://www.facebook.com/ajdgsolutions/" target="_blank">Facebook</a> & <a href="https://linkedin.com/in/arnandegans/" target="_blank">LinkedIn</a>. <strong>Business:</strong> <a href="https://ajdg.solutions/" target="_blank">ajdg.solutions</a>. <strong>Personal:</strong> <a href="https://www.arnan.me" target="_blank">arnan.me</a>.</p>'
		)
    );
    $screen->add_help_tab(array(
        'id' => 'adrotate_partners',
        'title' => 'Advertising Partners',
        'content' => '<h4>Our partners</h4>'.
        '<p>Try these great advertising partners for getting relevant adverts to your site. Increase revenue with their contextual adverts and earn more money with AdRotate!</p>'.

        '<p><strong>Blind Ferret:</strong> <a href="https://ajdg.solutions/go/blindferret/" target="_blank">Sign up with the Blind Ferret Publisher Network</a><br />Industry leader in Header Bidding adverts!'.
        
        '<p><strong>Media.net:</strong> <a href="https://ajdg.solutions/go/medianet/" target="_blank">Sign up for Media.net Contextual Adverts</a><br />Get 10% extra earnings commission for the first 3 months!</p>'.

        '<p><strong>Social:</strong> <a href="https://www.facebook.com/ajdgsolutions/" target="_blank">Facebook</a> & <a href="https://linkedin.com/in/arnandegans/" target="_blank">LinkedIn</a>. <strong>Business:</strong> <a href="https://ajdg.solutions/" target="_blank">ajdg.solutions</a>. <strong>Personal:</strong> <a href="https://www.arnan.me" target="_blank">arnan.me</a>.</p>'.

        '<p><small><em>These are affiliate links, using them supports the future of AdRotate!</em></small></p>'
		)
    );
    $screen->add_help_tab(array(
        'id' => 'adrotate_support',
        'title' => 'Getting Support',
        'content' => '<h4>Get help using AdRotate</h4>'.
        '<p>Everyone needs a little help sometimes. AdRotate has many guides and manuals as well as a Support Forum on the AdRotate website to get you going.</p>'.
        '<p>Exclusive for AdRotate Professional users there is a contact form right here in your dashboard, for extra fast support. Check out the General Info tab to see it.</p>'.

        '<p>Take a look at the <a href="https://ajdg.solutions/support/adrotate-manuals/" target="_blank">AdRotate Manuals</a> and the <a href="https://ajdg.solutions/forums/forum/adrotate-for-wordpress/" target="_blank">Support Forum</a>.</p>'.

        '<p><strong>Social:</strong> <a href="https://www.facebook.com/ajdgsolutions/" target="_blank">Facebook</a> & <a href="https://linkedin.com/in/arnandegans/" target="_blank">LinkedIn</a>. <strong>Business:</strong> <a href="https://ajdg.solutions/" target="_blank">ajdg.solutions</a>. <strong>Personal:</strong> <a href="https://www.arnan.me" target="_blank">arnan.me</a>.</p>'.

        '<p><strong>Useful Links:</strong> <a href="https://ajdg.solutions/support/adrotate-manuals/" target="_blank">AdRotate Manuals</a> and <a href="https://ajdg.solutions/forums/forum/adrotate-for-wordpress/" target="_blank">Support Forum</a>.</p>'
		)
    );
}

/*-------------------------------------------------------------
 Name:      adrotate_action_links
 Purpose:	Plugin page link
 Since:		4.13
-------------------------------------------------------------*/
function adrotate_action_links($links) {
	$custom_actions = array();
	$custom_actions['adrotate-help'] = sprintf('<a href="%s" target="_blank">%s</a>', 'https://ajdg.solutions/support/', 'Support');
	$custom_actions['adrotate-arnan'] = sprintf('<a href="%s" target="_blank">%s</a>', 'https://www.arnan.me/', 'arnan.me');

	return array_merge($custom_actions, $links);
}

/*-------------------------------------------------------------
 Name:      adrotate_user_notice
 Purpose:   Credits shown on user statistics
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_user_notice() {

	echo '<table class="widefat" style="margin-top: .5em">';

	echo '<thead>';
	echo '<tr valign="top">';
	echo '	<th colspan="2">'.__('Your adverts', 'adrotate-pro').'</th>';
	echo '</tr>';
	echo '</thead>';

	echo '<tbody>';
	echo '<tr>';
	echo '<td><center><a href="https://ajdg.solutions/products/adrotate-for-wordpress/" title="AdRotate plugin for WordPress"><img src="'.plugins_url('/images/logo-60x60.png', __FILE__).'" alt="logo-60x60" width="60" height="60" /></a></center></td>';
	echo '<td>'.__('The overall stats do not take adverts from other advertisers into account.', 'adrotate-pro').'<br />'.__('All statistics are indicative. They do not nessesarily reflect results counted by other parties.', 'adrotate-pro').'<br />'.__('Your ads are published with', 'adrotate-pro').' <a href="https://ajdg.solutions/products/adrotate-for-wordpress/" target="_blank">AdRotate for WordPress</a>.</td>';

	echo '</tr>';
	echo '</tbody>';

	echo '</table>';
	echo adrotate_trademark();
}

/*-------------------------------------------------------------
 Name:      adrotate_trademark
 Purpose:   Trademark notice
 Since:		3.9.14
-------------------------------------------------------------*/
function adrotate_trademark() {
 return '<center><small>AdRotate<sup>&reg;</sup> '.__('is a registered trademark', 'adrotate-pro').'</small></center>';
}
?>