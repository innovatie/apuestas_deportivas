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
 Name:      adrotate_import_ads

 Purpose:   Import adverts from file
 Receive:   -None-
 Return:	-None-
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_import_ads() {
	global $wpdb, $current_user, $userdata;

	if(wp_verify_nonce($_POST['adrotate_nonce_tools'], 'adrotate_import')) {
		if(current_user_can('adrotate_ad_manage')) {	
			if($_FILES["adrotate_file"]["error"] == 4) {
				adrotate_return('adrotate-settings', 506, array('tab' => 'tools'));
				exit;
			} else if ($_FILES["adrotate_file"]["error"] > 0) {
				adrotate_return('adrotate-settings', 507, array('tab' => 'tools'));
				exit;
			} else if($_FILES["adrotate_file"]["size"] > 4096000) {
				adrotate_return('adrotate-settings', 511, array('tab' => 'tools'));
				exit;
			} else {
				$now = adrotate_now();
				$ninetydays = $now + (90 * 86400);
	
				if($_FILES["adrotate_file"]["type"] == "text/csv") {
					$csv_name = $_FILES["adrotate_file"]["tmp_name"];
					$handle = fopen($csv_name, 'r');
					
					while($data = fgetcsv($handle, 1000)) {
						if($data[0] == 'Generated' OR $data[0] == 'id') continue;

						$advert = array(
							'title' => '[import] '.(!empty($data[1])) ? strip_tags(htmlspecialchars_decode(trim($data[1], "\t\n "))) : 'Advert '.$data[0],
							'bannercode' => (!empty($data[2])) ? htmlspecialchars_decode(trim($data[2], "\t\n ")) : '',
							'thetime' => $now,
							'updated' => $now,
							'author' => $current_user->user_login,
							'imagetype' => ($data[3] == "image" OR $data[3] == "dropdown") ? strip_tags(trim($data[3], "\t\n ")) : '',
							'image' => (!empty($data[4])) ? strip_tags(trim($data[4], "\t\n ")) : '',
							'tracker' => ($data[6] == "Y" OR $data[6] == "N") ? strip_tags(trim($data[6], "\t\n ")) : 'N',
							'desktop' => ($data[7] == "Y" OR $data[7] == "N") ? strip_tags(trim($data[7], "\t\n ")) : 'Y',
							'mobile' => ($data[8] == "Y" OR $data[8] == "N") ? strip_tags(trim($data[8], "\t\n ")) : 'Y',
							'tablet' => ($data[9] == "Y" OR $data[9] == "N") ? strip_tags(trim($data[9], "\t\n ")) : 'Y',
							'os_ios' => ($data[10] == "Y" OR $data[10] == "N") ? strip_tags(trim($data[10], "\t\n ")) : 'Y',
							'os_android' => ($data[11] == "Y" OR $data[11] == "N") ? strip_tags(trim($data[11], "\t\n ")) : 'Y',
							'os_other' => ($data[12] == "Y" OR $data[12] == "N") ? strip_tags(trim($data[12], "\t\n ")) : 'Y',
							'type' => 'import',
							'weight' => (is_numeric($data[13])) ? strip_tags(trim($data[13], "\t\n ")) : 6,
							'autodelete' => 'N',
							'budget' => (is_numeric($data[14])) ? strip_tags(trim($data[14], "\t\n ")) : 0,
							'crate' => (is_numeric($data[15])) ? strip_tags(trim($data[15], "\t\n ")) : 0,
							'irate' => (is_numeric($data[16])) ? strip_tags(trim($data[16], "\t\n ")) : 0,
							'cities' => (!empty($data[17])) ? serialize(explode(',', strip_tags(trim($data[17], "\t\n ")))) : 'a:0:{}',
							'countries' => (!empty($data[18])) ? serialize(explode(',', strip_tags(trim($data[18], "\t\n ")))) : 'a:0:{}',
						);
						$wpdb->insert($wpdb->prefix."adrotate", $advert);

						$advert_id = $wpdb->insert_id;
						$schedule = array(
							'name' => 'Schedule for advert '.$advert_id,
							'starttime' => (is_numeric($data[19])) ? strip_tags(trim($data[19], "\t\n ")) : $now,
							'stoptime' => (is_numeric($data[20])) ? strip_tags(trim($data[20], "\t\n ")) : $ninetydays,
							'maxclicks' => 0,
							'maximpressions' => 0,
							'spread' => 'N',
							'daystarttime' => '0000',
							'daystoptime' => '0000',
							'day_mon' => 'Y',
							'day_tue' => 'Y',
							'day_wed' => 'Y',
							'day_thu' => 'Y',
							'day_fri' => 'Y',
							'day_sat' => 'Y',
							'day_sun' => 'Y',
							'autodelete' => 'N',
						);
						$wpdb->insert($wpdb->prefix."adrotate_schedule", $schedule);

						$schedule_id = $wpdb->insert_id;
						$linkmeta = array(
							'ad' => $advert_id,
							'group' => 0,
							'user' => 0,
							'schedule' => $schedule_id,
						);
						$wpdb->insert($wpdb->prefix."adrotate_linkmeta", $linkmeta);
						
						unset($advert, $advert, $advert_id, $schedule, $schedule_id, $linkmeta);
					}
					
					// Delete uploaded file
					unlink($csv_name);
				} 

				// Verify all ads
				adrotate_prepare_evaluate_ads(false);
			
				// return to dashboard
				adrotate_return('adrotate-settings', 216, array('tab' => 'tools'));
				exit;
			}
		} else {
			adrotate_return('adrotate-settings', 500, array('tab' => 'tools'));
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}
?>