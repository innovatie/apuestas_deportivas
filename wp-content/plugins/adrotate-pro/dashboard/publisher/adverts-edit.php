<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

if(!$ad_edit_id) { 
	$edit_id = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'empty' ORDER BY `id` DESC LIMIT 1;");
	if($edit_id == 0) {
	    $wpdb->insert($wpdb->prefix."adrotate", array('title' => '', 'bannercode' => '', 'thetime' => $now, 'updated' => $now, 'author' => $userdata->user_login, 'imagetype' => 'dropdown', 'image' => '', 'tracker' => 'N', 'show_everyone' => 'Y', 'desktop' => 'Y', 'mobile' => 'Y', 'tablet' => 'Y', 'os_ios' => 'Y', 'os_android' => 'Y', 'os_other' => 'Y', 'type' => 'empty', 'weight' => 6, 'autodelete' => 'N', 'budget' => 0, 'crate' => 0, 'irate' => 0, 'cities' => serialize(array()), 'countries' => serialize(array())));
	    $edit_id = $wpdb->insert_id;
	}
	$ad_edit_id = $edit_id;
}

$edit_banner = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `id` = '$ad_edit_id';");
if($edit_banner) {
	$groups	= $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;"); 
	$schedules = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` != '' AND `stoptime` > $now ORDER BY `id` ASC;");
	
	if($adrotate_config['enable_advertisers'] == 'Y') {
		$user_list = $wpdb->get_results("SELECT `ID`, `display_name` FROM `$wpdb->users`, `$wpdb->usermeta` WHERE `ID` = `user_id` AND `meta_key` = 'adrotate_is_advertiser' AND `meta_value` = 'Y' ORDER BY `user_nicename` ASC;");
		$saved_user = $wpdb->get_var("SELECT `user` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '$edit_banner->id' AND `group` = 0 AND `schedule` = 0;");
	} else {
		$user_list = $saved_user = 0;
	}
	
	$linkmeta = $wpdb->get_results("SELECT `group` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '$edit_banner->id' AND `user` = 0 AND `schedule` = 0;");
	$schedulemeta = $wpdb->get_results("SELECT `schedule` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '$edit_banner->id' AND `group` = 0 AND `user` = 0;");
	
	wp_enqueue_media();
	wp_enqueue_script('uploader-hook', plugins_url().'/adrotate-pro/library/uploader-hook.js', array('jquery'));
	
	$meta_array = $schedule_array = array();
	foreach($linkmeta as $meta) {
		$meta_array[] = $meta->group;
		unset($meta);
	}
	
	foreach($schedulemeta as $meta) {
		$schedule_array[] = $meta->schedule;
		unset($meta);
	}
	
	// Random banner for Media.net
	$partner = mt_rand(1,3);
	
	if($ad_edit_id AND $edit_banner->type != 'empty' AND $edit_banner->type != 'generator') {
		// Errors
		if(strlen($edit_banner->bannercode) < 1 AND $edit_banner->type != 'empty') 
			echo '<div class="error"><p>'. __('The AdCode cannot be empty!', 'adrotate-pro').'</p></div>';
	
		if($edit_banner->tracker == 'N' AND $saved_user > 0) 
			echo '<div class="error"><p>'. __('You have set an advertiser but didn\'t enable tracking!', 'adrotate-pro').'</p></div>';
	
		if(!preg_match("/(%image%|%asset%)/i", $edit_banner->bannercode, $things) AND $edit_banner->image != '') 
			echo '<div class="error"><p>'. __('You did not use %asset% (or %image%) in your AdCode but did select a file to use!', 'adrotate-pro').'</p></div>';
	
		if(preg_match("/(%image%|%asset%)/i", $edit_banner->bannercode, $things) AND $edit_banner->image == '') 
			echo '<div class="error"><p>'. __('You did use %asset% (or %image%) in your AdCode but did not select a file to use!', 'adrotate-pro').'</p></div>';
		
		if((($edit_banner->imagetype != '' AND $edit_banner->image == '') OR ($edit_banner->imagetype == '' AND $edit_banner->image != ''))) 
			echo '<div class="error"><p>'. __('There is a problem saving the asset. Please try again with the media picker and re-save the advert!', 'adrotate-pro').'</p></div>';
		
		if(($edit_banner->crate > 0 OR $edit_banner->irate > 0) AND $edit_banner->budget < 1) 
			echo '<div class="error"><p>'. __('This advert has run out of budget. Add more budget to the advert or reset the rate to zero!', 'adrotate-pro').'</p></div>';
		
		if(count($schedule_array) == 0) 
			echo '<div class="error"><p>'. __('This advert has no schedules!', 'adrotate-pro').'</p></div>';
		
		if(!preg_match_all('/<(a|script|embed|iframe)[^>](.*?)>/i', stripslashes(htmlspecialchars_decode($edit_banner->bannercode, ENT_QUOTES)), $things) AND $edit_banner->tracker == 'Y')
			echo '<div class="error"><p>'. __("Tracking is enabled but no valid link/tag was found in the adcode!", 'adrotate-pro').'</p></div>';
	
		if($edit_banner->tracker == 'N' AND $edit_banner->crate > 0)
			echo '<div class="error"><p>'. __("CPC is enabled but Tracking is not active!", 'adrotate-pro').'</p></div>';
	
		// Ad Notices
		$adstate = adrotate_evaluate_ad($edit_banner->id);
		if($edit_banner->type == 'error' AND $adstate == 'active')
			echo '<div class="error"><p>'. __('AdRotate cannot find an error but the advert is marked erroneous, check the settings and try re-saving the advert!', 'adrotate-pro').'</p></div>';
	
		if($edit_banner->type == 'reject')
			echo '<div class="error"><p>'. __('This advert has been rejected by staff. Please adjust the advert to conform with the requirements!', 'adrotate-pro').'</p></div>';
	
		if($edit_banner->type == 'queue')
			echo '<div class="error"><p>'. __('This advert is queued and awaiting review!', 'adrotate-pro').'</p></div>';
	
		if($adstate == 'expired')
			echo '<div class="error"><p>'. __('This advert is expired and currently not shown on your website!', 'adrotate-pro').'</p></div>';
	
		if($adstate == '2days')
			echo '<div class="updated"><p>'. __('The advert will expire in less than 2 days!', 'adrotate-pro').'</p></div>';
	
		if($adstate == '7days')
			echo '<div class="updated"><p>'. __('This advert will expire in less than 7 days!', 'adrotate-pro').'</p></div>';
	
		if($edit_banner->type == 'disabled') 
			echo '<div class="error"><p>'. __('This advert has been disabled and does not rotate on your site!', 'adrotate-pro').'</p></div>';
	
		if($edit_banner->type == 'archived') 
			echo '<div class="error"><p>'. __('This advert has been archived and can not be activated anymore!', 'adrotate-pro').'</p></div>';
	}
	
	// Determine image field
	if($edit_banner->imagetype == "field") {
		$image_field = $edit_banner->image;
		$image_dropdown = '';
	} else if($edit_banner->imagetype == "dropdown") {
		$image_field = '';
		$image_dropdown = $edit_banner->image;
	} else {
		$image_field = '';
		$image_dropdown = '';
	}
	?>
	
		<?php if($adrotate_config['live_preview'] == "Y") { ?>
			<!-- AdRotate JS -->
			<script type="text/javascript">
			jQuery(document).ready(function(){
			    function livePreview(){
			        var input = jQuery("#adrotate_bannercode").val();
			        if(jQuery("#adrotate_title").val().length > 0) var ad_title = jQuery("#adrotate_title").val();
			        var ad_image = '';
			        if(jQuery("#adrotate_image_dropdown").val().length > 0) var ad_image = '<?php echo WP_CONTENT_URL.'/'.$adrotate_config['banner_folder']; ?>/'+jQuery("#adrotate_image_dropdown").val();
			        if(jQuery("#adrotate_image").val().length > 0) var ad_image = jQuery("#adrotate_image").val();
			
			        var input = input.replace(/%id%/g, <?php echo $edit_banner->id;?>);
			        var input = input.replace(/%title%/g, ad_title);
			        var input = input.replace(/%asset%/g, ad_image);
			        var input = input.replace(/%image%/g, ad_image);
			        var input = input.replace(/%random%/g, <?php echo rand(100000,999999); ?>);
			        jQuery("#adrotate_preview").html(input);
			    }       
			    livePreview();
			
			    jQuery('#adrotate_bannercode').on("paste change focus focusout input", function(){ livePreview(); });
			    jQuery('#adrotate_image').on("paste change focusout input", function(){ livePreview(); });
			    jQuery('#adrotate_image_dropdown').on("change", function(){ livePreview(); });
			});
			</script>
			<!-- /AdRotate JS -->
		<?php } ?>
	
		<form method="post" action="admin.php?page=adrotate-ads">
		<?php wp_nonce_field('adrotate_save_ad','adrotate_nonce'); ?>
		<input type="hidden" name="adrotate_username" value="<?php echo $userdata->user_login;?>" />
		<input type="hidden" name="adrotate_id" value="<?php echo $edit_banner->id;?>" />
		<input type="hidden" name="adrotate_type" value="<?php echo $edit_banner->type;?>" />
	
	<?php if($edit_banner->type == 'empty') { ?>
		<h2><?php _e('New Advert', 'adrotate-pro'); ?></h2>
	<?php } else { ?> 
		<h2><?php _e('Edit Advert', 'adrotate-pro'); ?></h2>
	<?php } ?>
	
		<table class="widefat" style="margin-top: .5em">
	
			<tbody>
	      	<tr>
		        <th width="15%"><?php _e('Name', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_title"><input tabindex="1" id="adrotate_title" name="adrotate_title" type="text" size="70" class="ajdg-inputfield ajdg-fullwidth" value="<?php echo stripslashes($edit_banner->title);?>" autocomplete="off" /></label>
		        </td>
		        <td width="35%">
		        	<em><?php _e('Visible to Advertisers!', 'adrotate-pro'); ?></em>
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('AdCode', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_bannercode"><textarea tabindex="2" id="adrotate_bannercode" name="adrotate_bannercode" cols="70" rows="15" class="ajdg-fullwidth"><?php echo stripslashes($edit_banner->bannercode); ?></textarea></label>
		        </td>
		        <td width="35%" rowspan="2">
			        <p><strong><?php _e('Basic Examples:', 'adrotate-pro'); ?></strong></p>
			        <p><em><?php _e('Click any of the examples to use it.', 'adrotate-pro'); ?></em></p>
					<p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;a href=&quot;https://ajdg.solutions/&quot;&gt;&lt;img src=&quot;%asset%&quot; /&gt;&lt;/a&gt;');return false;">&lt;a href="https://ajdg.solutions/"&gt;&lt;img src="%asset%" /&gt;&lt;/a&gt;</a></em></p>
			        <p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;iframe src=&quot;%asset%&quot; height=&quot;250&quot; frameborder=&quot;0&quot; style=&quot;border:none;&quot;&gt;&lt;/iframe&gt;');return false;">&lt;iframe src=&quot;%asset%&quot; height=&quot;250&quot; frameborder=&quot;0&quot; style=&quot;border:none;&quot;&gt;&lt;/iframe&gt;</a></em></p>
					<p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;a href=&quot;http://www.arnan.me/&quot;&gt;Visit arnan.me&lt;/a&gt;');return false;">&lt;a href="http://www.arnan.me/"&gt;Visit arnan.me&lt;/a&gt;</a></em></p>
					<p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;a href=&quot;https://ajdg.solutions/&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;%asset%&quot; /&gt;&lt;/a&gt;');return false;">&lt;a href="https://ajdg.solutions/" target=&quot;_blank&quot;&gt;&lt;img src="%asset%" /&gt;&lt;/a&gt;</a></em></p>
					<p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;a href=&quot;https://ajdg.solutions/?timestamp=%random%&quot;&gt;&lt;img src=&quot;%asset%&quot; /&gt;&lt;/a&gt;');return false;">&lt;a href="https://ajdg.solutions/?timestamp=%random%"&gt;&lt;img src="%asset%" /&gt;&lt;/a&gt;</a></em></p>
		        </td>
			</tr>
			<tr>
		        <th valign="top"><?php _e('Useful tags', 'adrotate-pro'); ?></th>
		        <td>
			        <p><em><a href="#" title="<?php _e('Insert the advert ID Number.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','%id%');return false;">%id%</a>, <a href="#" title="<?php _e('Required when selecting a image/asset below.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','%asset%');return false;">%asset%</a>, <a href="#" title="<?php _e('Insert the advert name.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','%title%');return false;">%title%</a>, <a href="#" title="<?php _e('Insert a random seed. Useful for DFP/DoubleClick type adverts.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','%random%');return false;">%random%</a>, <a href="#" title="<?php _e('Add inside the <a> tag to open advert in a new window.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','target=&quot;_blank&quot;');return false;">target="_blank"</a>, <a href="#" title="<?php _e('Add inside the <a> tag to tell crawlers to ignore this link', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','rel=&quot;nofollow&quot;');return false;">rel="nofollow"</a>, <a href="#" title="<?php _e('Add inside the <img> tag to set the width of the banner, add your own size in pixels', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','width=&quot;728&quot;');return false;">width="728"</a>, <a href="#" title="<?php _e('Add inside the <img> tag to set the height of the banner, add your own size in pixels', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','height=&quot;90&quot;');return false;">height="90"</a></em><br /><?php _e('Place the cursor in your AdCode where you want to add any of these tags and click to add it.', 'adrotate-pro'); ?></p>
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('Preview', 'adrotate-pro'); ?></th>
		        <td colspan="2">
		        	<div id="adrotate_preview"><?php echo ($adrotate_config['live_preview'] == "N") ? adrotate_preview($edit_banner->id) : ''; ?></div>
			        <br /><em><?php _e('Note: While this preview is an accurate one, it might look different then it does on the website.', 'adrotate-pro'); ?>
					<br /><?php _e('This is because of CSS differences. Your themes CSS file is not active here!', 'adrotate-pro'); ?></em>
				</td>
	      	</tr>
			<tr>
		        <th valign="top"><?php _e('Banner asset', 'adrotate-pro'); ?></th>
				<td colspan="2">
					<label for="adrotate_image">
						<?php _e('WordPress media', 'adrotate-pro'); ?> <input tabindex="3" id="adrotate_image" type="text" size="50" name="adrotate_image" value="<?php echo $image_field; ?>" class="ajdg-inputfield" /> <input tabindex="4" id="adrotate_image_button" class="button" type="button" value="<?php _e('Select Banner', 'adrotate-pro'); ?>" />
					</label><br />
					<?php _e('- OR -', 'adrotate-pro'); ?><br />
					<label for="adrotate_image_dropdown">
						<?php _e('Banner folder', 'adrotate-pro'); ?> <select tabindex="5" id="adrotate_image_dropdown" name="adrotate_image_dropdown" style="min-width: 200px;">
	   						<option value=""><?php _e('No file selected', 'adrotate-pro'); ?></option>
							<?php echo adrotate_folder_contents($image_dropdown); ?>
						</select><br />
					</label>
					<em><?php _e('Use %asset% in the adcode instead of the file path.', 'adrotate-pro'); ?> <?php _e('Use either the text field or the dropdown. If the textfield has content, that field has priority.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
			<?php if($adrotate_config['stats'] > 0) { ?>
			<tr>
		        <th valign="top"><?php _e('Statistics', 'adrotate-pro'); ?></th>
		        <td colspan="2">
		        	<label for="adrotate_tracker"><input tabindex="6" type="checkbox" name="adrotate_tracker" <?php if($edit_banner->tracker == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Enable click and impression tracking for this advert.', 'adrotate-pro'); ?> <br />
		        	<em><?php _e('Note: Clicktracking does not work for Javascript adverts such as those provided by Google AdSense/DFP/DoubleClick. HTML5/Flash adverts are not always supported.', 'adrotate-pro'); ?></em>
			        </label>
		        </td>
			</tr>
			<?php } ?>
			<?php if($edit_banner->type != "archived") { ?>
			<tr>
		        <th valign="top"><?php _e('Status', 'adrotate-pro'); ?></th>
		        <td colspan="2">
			        <label for="adrotate_active">
				        <select tabindex="7" name="adrotate_active">
							<option value="active" <?php if($edit_banner->type == "active" OR $edit_banner->type == "error") { echo 'selected'; } ?>><?php _e('Enabled, this advert will be visible', 'adrotate-pro'); ?></option>
							<option value="disabled" <?php if($edit_banner->type == "disabled") { echo 'selected'; } ?>><?php _e('Disabled, do not show this advert anywhere', 'adrotate-pro'); ?></option>
							<?php if($adrotate_config['enable_advertisers'] == 'Y' AND $adrotate_config['enable_editing'] == 'Y' AND $saved_user > 0) { ?>
							<option value="queue" <?php if($edit_banner->type == "queue" OR $edit_banner->type == "a_error") { echo 'selected'; } ?>><?php _e('Maybe, this advert is queued for review', 'adrotate-pro'); ?></option>
							<option value="reject" <?php if($edit_banner->type == "reject") { echo 'selected'; } ?>><?php _e('No, this advert is rejected (advertiser can make changes)', 'adrotate-pro'); ?></option>
							<option value="archived" <?php if($edit_banner->type == "archived") { echo 'selected'; } ?>><?php _e('Archive this advert permanently', 'adrotate-pro'); ?></option>
							<?php } ?>
						</select>
					</label>
				</td>
	      	</tr>
			<?php } ?>
			</tbody>
	
		</table>
	
		<p class="submit">
			<input tabindex="8" type="submit" name="adrotate_ad_submit" class="button-primary" value="<?php _e('Save Advert', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-ads&view=manage" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>
			
		<h2><?php _e('Usage', 'adrotate-pro'); ?></h2>
		<table class="widefat" style="margin-top: .5em">
			<tbody>
	      	<tr>
		        <th width="15%"><?php _e('Widget', 'adrotate-pro'); ?></th>
		        <td colspan="3"><?php _e('In the Appearance > Widgets dashboard. Drag the AdRotate widget to the sidebar where you want to place the advert and select the advert or the group the advert is in.', 'adrotate-pro'); ?></td>
	      	</tr>
	      	<tr>
		        <th width="15%"><?php _e('In a post or page', 'adrotate-pro'); ?></th>
		        <td width="35%">[adrotate banner="<?php echo $edit_banner->id; ?>"]</td>
		        <th width="15%"><?php _e('Directly in a theme', 'adrotate-pro'); ?></th>
		        <td>&lt;?php echo adrotate_ad(<?php echo $edit_banner->id; ?>); ?&gt;</td>
	      	</tr>
	      	</tbody>
		</table>
	
		<?php
		if($adrotate_config['affiliates'] == 'N') {
			$partner = mt_rand(1,4);
			if($partner < 4) {
			?>
			<h2><?php _e('Get your adverts from Media.net', 'adrotate-pro'); ?></h2>
			<table class="widefat" style="margin-top: .5em">
				<tbody>
		      	<tr>
			        <th width="40%"><center><a href="https://ajdg.solutions/go/medianet/" target="_blank"><img src="<?php echo plugins_url("../images/offers/medianet-large-$partner.jpg", dirname(__FILE__)); ?>" width="440" /></a></center></th>
			        <td>
				        <p><a href="https://ajdg.solutions/go/medianet/" target="_blank">Media.net</a> is the <strong>#2 largest contextual ads platform</strong> in the world that provides its publishers with an <strong>exclusive access to the Yahoo! Bing Network of advertisers and $6bn worth of search demand.</strong></p>
			        	<p><a href="https://ajdg.solutions/go/medianet/" target="_blank">Media.net</a> <strong>ads are contextual</strong> and hence always relevant to your content. They are also <strong>native by design</strong> and highly customizable, delivering a great user experience and higher CTRs.</p>
					</td>
		      	</tr>
		      	</tbody>
			</table>
			<?php } else { ?>
			<h2><?php _e('Sign up with Blind Ferret', 'adrotate-pro'); ?></h2>
			<table class="widefat" style="margin-top: .5em">
				<tbody>
		      	<tr>
			        <th width="40%"><center><a href="https://ajdg.solutions/go/blindferret/" target="_blank"><img src="<?php echo plugins_url("../images/offers/blindferret.jpg", dirname(__FILE__)); ?>" width="440" /></a></center></th>
			        <td>
				        <p>At <a href="https://ajdg.solutions/go/blindferret/" target="_blank">Blind Ferret</a>, we are publishers too, which means we know what's needed to create successful campaigns! We know that advertising isn't just "set it and forget it" anymore. Our Publisher Network features a wide range of creative and comic sites, is simple to take advantage of and intensely UI/UX focused.</p>
		
						<p>With over 15 years of experience, <a href="https://ajdg.solutions/go/blindferret/" target="_blank">Blind Ferret</a> can offer great ads at top dollar via header bidding, ensuring advertisers vie for your ad space, which brings in higher quality ads and makes you more money!</p>
					</td>
		      	</tr>
		      	</tbody>
			</table>
		<?php 
			} 
		}
		?>
	
		<h2><?php _e('Create a new schedule', 'adrotate-pro'); ?></h2>
		<p><em><?php _e('Time uses a 24 hour clock. When you\'re used to the AM/PM system keep this in mind: If the start or end time is after lunch, add 12 hours. 2PM is 14:00 hours. 6AM is 6:00 hours.', 'adrotate-pro'); ?></em></p>
		<table class="widefat" style="margin-top: .5em">
			<tr>
		        <th width="15%"><?php _e('Start date', 'adrotate-pro'); ?> (dd-mm-yyyy)</th>
		        <td width="35%">
					<input tabindex="9" type="text" id="startdate_picker" name="adrotate_start_date" value="" class="datepicker ajdg-inputfield" autocomplete="off"/>
		        </td>
		        <th width="15%"><?php _e('End date', 'adrotate-pro'); ?> (dd-mm-yyyy)</th>
		        <td>
					<input tabindex="10" type="text" id="enddate_picker" name="adrotate_end_date" value="" class="datepicker ajdg-inputfield" autocomplete="off" />
				</td>
	      	</tr>	
			<tr>
		        <th><?php _e('Start time', 'adrotate-pro'); ?> (hh:mm)</th>
		        <td>
		        	<label for="adrotate_sday">
					<input tabindex="11" name="adrotate_start_hour" class="ajdg-inputfield" type="text" size="2" maxlength="4" value="" autocomplete="off" /> :
					<input tabindex="12" name="adrotate_start_minute" class="ajdg-inputfield" type="text" size="2" maxlength="4" value="" autocomplete="off" />
					</label>
		        </td>
		        <th><?php _e('End time', 'adrotate-pro'); ?> (hh:mm)</th>
		        <td>
		        	<label for="adrotate_eday">
					<input tabindex="13" name="adrotate_end_hour" class="ajdg-inputfield" type="text" size="2" maxlength="4" value="" autocomplete="off" /> :
					<input tabindex="14" name="adrotate_end_minute" class="ajdg-inputfield" type="text" size="2" maxlength="4" value="" autocomplete="off" />
					</label>
				</td>
	      	</tr>	
			<?php if($adrotate_config['stats'] == 1) { ?>
	      	<tr>
	      		<th><?php _e('Maximum Clicks', 'adrotate-pro'); ?></th>
		        <td><input tabindex="15" name="adrotate_maxclicks" type="text" size="5" class="ajdg-inputfield" autocomplete="off" value="" /> <em><?php _e('Leave empty to skip this.', 'adrotate-pro'); ?></em></td>
			    <th><?php _e('Maximum Impressions', 'adrotate-pro'); ?></th>
		        <td><input tabindex="16" name="adrotate_maxshown" type="text" size="5" class="ajdg-inputfield" autocomplete="off" value="" /> <em><?php _e('Leave empty to skip this.', 'adrotate-pro'); ?></em></td>
			</tr>
		    <tr>
				<th valign="top"><?php _e('Spread Impressions', 'adrotate-pro'); ?></th>
				<td colspan="3"><label for="adrotate_spread"><input tabindex="17" type="checkbox" name="adrotate_spread" value="1" /> <?php _e('Try to evenly spread impressions for each advert over the duration of this schedule. This may cause adverts to intermittently not show.', 'adrotate-pro'); ?></label></td>
			</tr>
			<?php } ?>
		</table>
	
		<h2><?php _e('Choose an existing schedules', 'adrotate-pro'); ?></h2>
		<p><em><?php _e('You can add, edit or delete schedules from the', 'adrotate-pro'); ?>  '<a href="admin.php?page=adrotate-schedules"><?php _e('Schedules', 'adrotate-pro'); ?></a>' <?php _e('dashboard. Save your advert first!', 'adrotate-pro'); ?></em></p>
		<table class="widefat" style="margin-top: .5em">
	
			<thead>
			<tr>
				<td width="2%" scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
		        <th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
		        <th width="20%"><?php _e('Start / End', 'adrotate-pro'); ?></th>
		        <th>&nbsp;</th>
		        <?php if($adrotate_config['stats'] == 1) { ?>
			        <th width="10%"><center><?php _e('Max Shown', 'adrotate-pro'); ?></center></th>
			        <th width="10%"><center><?php _e('Max Clicks', 'adrotate-pro'); ?></center></th>
				<?php } ?>
			</tr>
			</thead>
	
			<tbody>
			<?php
			$tick = '<img src="'.plugins_url('../../images/tick.png', __FILE__).'" width="10" height"10" />';
			$cross = '<img src="'.plugins_url('../../images/cross.png', __FILE__).'" width="10" height"10" />';
	
			$class = '';
			foreach($schedules as $schedule) {
				if(!in_array($schedule->id, $schedule_array) AND $adrotate_config['hide_schedules'] == "Y") continue;
				if($adrotate_config['stats'] == 1) {
					if($schedule->maxclicks == 0) $schedule->maxclicks = '&infin;';
					if($schedule->maximpressions == 0) $schedule->maximpressions = '&infin;';
				}
	
				$class = ('alternate' != $class) ? 'alternate' : '';
				if(in_array($schedule->id, $schedule_array)) $class = 'row_green'; 
				if($schedule->stoptime < $in2days) $class = 'row_orange';
	
				$sdayhour = substr($schedule->daystarttime, 0, 2);
				$sdayminute = substr($schedule->daystarttime, 2, 2);
				$edayhour = substr($schedule->daystoptime, 0, 2);
				$edayminute = substr($schedule->daystoptime, 2, 2);
			?>
	      	<tr id='schedule-<?php echo $schedule->id; ?>' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="scheduleselect[]" value="<?php echo $schedule->id; ?>" <?php if(in_array($schedule->id, $schedule_array)) echo "checked"; ?> /></th>
				<td><center><?php echo $schedule->id; ?></center></td>
				<td><?php echo date_i18n("F d, Y H:i", $schedule->starttime);?><br /><span style="color: <?php echo adrotate_prepare_color($schedule->stoptime);?>;"><?php echo date_i18n("F d, Y H:i", $schedule->stoptime);?></span></td>
				<td><a href="<?php echo admin_url('/admin.php?page=adrotate-schedules&view=edit&schedule='.$schedule->id);?>"><?php echo stripslashes(html_entity_decode($schedule->name)); ?></a><span style="color:#999;"><br /><?php _e('Mon:', 'adrotate-pro'); ?> <?php echo ($schedule->day_mon == 'Y') ? $tick : $cross; ?> <?php _e('Tue:', 'adrotate-pro'); ?> <?php echo ($schedule->day_tue == 'Y') ? $tick : $cross; ?> <?php _e('Wed:', 'adrotate-pro'); ?> <?php echo ($schedule->day_wed == 'Y') ? $tick : $cross; ?> <?php _e('Thu:', 'adrotate-pro'); ?> <?php echo ($schedule->day_thu == 'Y') ? $tick : $cross; ?> <?php _e('Fri:', 'adrotate-pro'); ?> <?php echo ($schedule->day_fri == 'Y') ? $tick : $cross; ?> <?php _e('Sat:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sat == 'Y') ? $tick : $cross; ?> <?php _e('Sun:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sun == 'Y') ? $tick : $cross; ?> <?php if($schedule->daystarttime  > 0) { ?><?php _e('Between:', 'adrotate-pro'); ?> <?php echo $sdayhour; ?>:<?php echo $sdayminute; ?> - <?php echo $edayhour; ?>:<?php echo $edayminute; ?> <?php } ?><br /><?php _e('Impression spread:', 'adrotate-pro'); ?> <?php echo ($schedule->spread == 'Y') ? $tick : $cross; ?>, <?php _e('Auto Delete:', 'adrotate-pro'); ?> <?php echo ($schedule->autodelete == 'Y') ? $tick : $cross; ?></span></td>
		        <?php if($adrotate_config['stats'] == 1) { ?>
			        <td><center><?php echo $schedule->maximpressions; ?></center></td>
			        <td><center><?php echo $schedule->maxclicks; ?></center></td>
				<?php } ?>
	      	</tr>
	      	<?php } ?>
			</tbody>
	
		</table>
		<p><center>
			<?php if($adrotate_config['hide_schedules'] == "Y") { ?><?php _e("Schedules not in use by this advert are hidden.", "adrotate-pro"); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
			<span style="border: 1px solid #518257; height: 12px; width: 12px; background-color: #e5faee">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("In use by this advert.", "adrotate-pro"); ?>
			<span style="border: 1px solid #c80; height: 12px; width: 12px; background-color: #fdefc3">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon.", "adrotate-pro"); ?>
		</center></p>
	
		<p class="submit">
			<input tabindex="18" type="submit" name="adrotate_ad_submit" class="button-primary" value="<?php _e('Save Advert', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-ads&view=manage" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>
	
		<h2><?php _e('Advanced', 'adrotate-pro'); ?></h2>
		<table class="widefat" style="margin-top: .5em">
	
			<tbody>
			<tr>
		        <th width="15%" valign="top"><?php _e('Show to everyone', 'adrotate-pro'); ?></th>
		        <td colspan="5">
		        	<label for="adrotate_show_everyone"><input tabindex="19" type="checkbox" name="adrotate_show_everyone" <?php if($edit_banner->show_everyone == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Disable this option to hide the advert from logged-in visitors.', 'adrotate-pro'); ?>
			        </label>
	 	        </td>
			</tr>
	       	<tr>
			    <th width="15%" valign="top"><?php _e('Weight', 'adrotate-pro'); ?></th>
		        <td width="17%">
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="20" name="adrotate_weight" value="2" <?php if($edit_banner->weight == "2") { echo 'checked'; } ?> /><br /><?php _e('Few impressions', 'adrotate-pro'); ?></center>
		        	</label>
				</td>
		        <td width="17%">
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="21" name="adrotate_weight" value="4" <?php if($edit_banner->weight == "4") { echo 'checked'; } ?> /><br /><?php _e('Less than average', 'adrotate-pro'); ?></center>
		        	</label>
				</td>
		        <td width="17%">
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="22" name="adrotate_weight" value="6" <?php if($edit_banner->weight == "6") { echo 'checked'; } ?> /><br /><?php _e('Normal impressions', 'adrotate-pro'); ?></center>
		        	</label>
				</td>
		        <td width="17%">
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="23" name="adrotate_weight" value="8" <?php if($edit_banner->weight == "8") { echo 'checked'; } ?> /><br /><?php _e('More than average', 'adrotate-pro'); ?></center>
		        	</label>
				</td>
		        <td>
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="24" name="adrotate_weight" value="10" <?php if($edit_banner->weight == "10") { echo 'checked'; } ?> /><br /><?php _e('Many impressions', 'adrotate-pro'); ?>
		        	</label>
				</td>
			</tr>
			<tr>
		        <th width="15%" valign="top"><?php _e('Device', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_desktop"><center><input tabindex="25" type="checkbox" name="adrotate_desktop" <?php if($edit_banner->desktop == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Desktops/Laptops', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td>
		        	<label for="adrotate_mobile"><center><input tabindex="26" type="checkbox" name="adrotate_mobile" <?php if($edit_banner->mobile == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Smartphones', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td>
		        	<label for="adrotate_tablet"><center><input tabindex="27" type="checkbox" name="adrotate_tablet" <?php if($edit_banner->tablet == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Tablets', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td colspan="2" rowspan="2">
		        	<em><?php _e("Also enable 'Mobile Support' in the group this advert goes in or 'Device' and 'Operating System' are ignored!", 'adrotate-pro'); ?><br /><?php _e("Operating system detection only detects iOS and Android, select 'Not Mobile/Other' for everything else.", 'adrotate-pro'); ?></em>
		        </td>
			</tr>
	     	<tr>
		        <th width="15%" valign="top"><?php _e('Operating System', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_ios"><center><input tabindex="28" type="checkbox" name="adrotate_ios" <?php if($edit_banner->os_ios == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('iOS', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td>
		        	<label for="adrotate_android"><center><input tabindex="29" type="checkbox" name="adrotate_android" <?php if($edit_banner->os_android == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Android', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td>
		        	<label for="adrotate_other"><center><input tabindex="30" type="checkbox" name="adrotate_other" <?php if($edit_banner->os_other == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Not mobile/Others', 'adrotate-pro'); ?></center></label>
		        </td>
			</tr>
	     	<tr>
		        <th width="15%" valign="top"><?php _e('Auto-delete', 'adrotate-pro'); ?></th>
		        <td colspan="5">
		        	<label for="adrotate_autodelete"><input tabindex="31" type="checkbox" name="adrotate_autodelete" <?php if($edit_banner->autodelete == 'Y') { ?>checked="checked" <?php } ?> /></label> <?php _e('Automatically delete the advert 1 day after it expires?', 'adrotate-pro'); ?><br /><em><?php _e('This is useful for short running campaigns that do not require attention after they finish.', 'adrotate-pro'); ?></em>
		        </td>
			</tr>
			</tbody>
	
		</table>
	
		<?php if($adrotate_config['enable_geo'] > 0) { ?>
		<?php $cities = unserialize(stripslashes($edit_banner->cities)); ?>
		<?php $countries = unserialize(stripslashes($edit_banner->countries)); ?>
		<h2><?php _e('Geo Targeting', 'adrotate-pro'); ?></h2>
		<p><em><?php _e('Assign the advert to a group and enable that group to use Geo Targeting.', 'adrotate-pro'); ?></em></p>
	
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">
				<div id="left-column" class="ajdg-postbox-container">
		
					<div class="ajdg-postbox">				
						<h2 class="ajdg-postbox-title"><?php _e('Select Countries and or Regions', 'adrotate-pro'); ?></h2>
						<div id="countries" class="ajdg-postbox-content">
							<div class="adrotate-select ajdg-fullwidth">
						        <?php echo adrotate_select_countries($countries); ?>
							</div>
						</div>
					</div>
		
				</div>
				<div id="right-column" class="ajdg-postbox-container">
		
					<div class="ajdg-postbox">
						<h2 class="ajdg-postbox-title"><?php _e('Enter cities, metro IDs, States or State ISO codes', 'adrotate-pro'); ?></h2>
						<div id="cities" class="ajdg-postbox-content">
							<textarea tabindex="32" name="adrotate_geo_cities" class="geo-cities ajdg-fullwidth" cols="40" rows="6"><?php echo (is_array($cities)) ? implode(', ', $cities) : ''; ?></textarea><br />
							<p><em><?php _e('A comma separated list of items:', 'adrotate-pro'); ?> (Alkmaar, New York, Manila, Tokyo) <?php _e('AdRotate does not check the validity of names so make sure you spell them correctly!', 'adrotate-pro'); ?></em></p>
						</div>
					</div>
		
				</div>
			</div>
		</div>
		
	   	<div class="clear"></div>
	  	<?php } ?>
	
		<?php if($adrotate_config['enable_advertisers'] == 'Y') { ?>
		<h2><?php _e('Advertiser', 'adrotate-pro'); ?></h2>
		<table class="widefat" style="margin-top: .5em">	
	
			<tbody>
	      	<tr>
		        <th width="15%" valign="top"><?php _e('Advertiser', 'adrotate-pro'); ?></th>
		        <td colspan="3">
		        	<label for="adrotate_tracker">
		        	<select tabindex="33" name="adrotate_advertiser" style="min-width: 200px;">
						<option value="0" <?php if($saved_user == '0') { echo 'selected'; } ?>><?php _e('Not specified', 'adrotate-pro'); ?></option>
					<?php 
					foreach($user_list as $user) {
						if($user->ID == $userdata->ID) $you = ' (You)';
							else $you = '';
					?>
						<option value="<?php echo $user->ID; ?>"<?php if($saved_user == $user->ID) { echo ' selected'; } ?>><?php echo $user->display_name; ?><?php echo $you; ?></option>
					<?php } ?>
					</select>
			        <em><?php _e('Must be a registered user on your site with appropriate access roles.', 'adrotate-pro'); ?></em>
			        </label>
				</td>
	      	</tr>
			<?php if($adrotate_config['stats'] == 1) { ?>
	     	<tr>
		        <th width="15%"><?php _e('Advert Budget', 'adrotate-pro'); ?></th>
		        <td colspan="3"><label for="adrotate_budget"><input tabindex="34" name="adrotate_budget" type="text" size="10" class="ajdg-inputfield" autocomplete="off" value="<?php echo number_format($edit_banner->budget, 2, '.', '');?>" /> <em><?php _e('When the budget reaches 0, the advert will expire.', 'adrotate-pro'); ?></em></label></td>
			</tr>
			<tr>
		        <th width="15%"><?php _e('Cost-per-Click', 'adrotate-pro'); ?></th>
		        <td width="35%"><label for="adrotate_crate"><input tabindex="35" name="adrotate_crate" type="text" size="10" class="ajdg-inputfield" autocomplete="off" value="<?php echo number_format($edit_banner->crate, 4, '.', '');?>" /> <em><?php _e('Leave empty to skip this.', 'adrotate-pro'); ?></em></label></td>
		        <th width="15%"><?php _e('Cost-per-Mille', 'adrotate-pro'); ?></th>
		        <td><label for="adrotate_irate"><input tabindex="36" name="adrotate_irate" type="text" size="10" class="ajdg-inputfield" autocomplete="off" value="<?php echo number_format($edit_banner->irate, 4, '.', '');?>" /> <em><?php _e('Leave empty to skip this.', 'adrotate-pro'); ?></em></label></td>
			</tr>
			<?php } ?>
			</tbody>
	
		</table>
		<?php } ?>
	
		<h2><?php _e('Usage', 'adrotate-pro'); ?></h2>
		<table class="widefat" style="margin-top: .5em">
	
			<tbody>
	      	<tr>
		        <th width="15%"><?php _e('Widget', 'adrotate-pro'); ?></th>
		        <td colspan="3"><?php _e('In the Appearance > Widgets dashboard. Drag the AdRotate widget to the sidebar where you want to place the advert and select the advert or the group the advert is in.', 'adrotate-pro'); ?></td>
	      	</tr>
	      	<tr>
		        <th width="15%"><?php _e('In a post or page', 'adrotate-pro'); ?></th>
		        <td width="35%">[adrotate banner="<?php echo $edit_banner->id; ?>"]</td>
		        <th width="15%"><?php _e('Directly in a theme', 'adrotate-pro'); ?></th>
		        <td>&lt;?php echo adrotate_ad(<?php echo $edit_banner->id; ?>); ?&gt;</td>
	      	</tr>
	      	</tbody>
		
		</table>
	
		<p class="submit">
			<input tabindex="37" type="submit" name="adrotate_ad_submit" class="button-primary" value="<?php _e('Save Advert', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-ads&view=manage" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>
	
		<?php if($groups) { ?>
		<h2><?php _e('Select Groups', 'adrotate-pro'); ?></h2>
		<table class="widefat" style="margin-top: .5em">
			<thead>
			<tr>
				<td width="2%" scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
				<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
				<th><?php _e('Name', 'adrotate-pro'); ?></th>
				<th width="5%"><center><?php _e('Adverts', 'adrotate-pro'); ?></center></th>
				<th width="5%"><center><?php _e('Active', 'adrotate-pro'); ?></center></th>
			</tr>
			</thead>
	
			<tbody>
			<?php 
			$class = '';
			foreach($groups as $group) {
				if($group->adspeed > 0) $adspeed = $group->adspeed / 1000;
		        if($group->modus == 0) $modus[] = __('Default rotation', 'adrotate-pro').' ('.$group->adwidth.'x'.$group->adheight.'px)';
		        if($group->modus == 1) $modus[] = $adspeed.' '. __('second rotation', 'adrotate-pro').' ('.$group->adwidth.'x'.$group->adheight.'px)';
		        if($group->modus == 2) $modus[] = $group->gridrows.'x'.$group->gridcolumns.' '. __('grid', 'adrotate-pro').' ('.$group->adwidth.'x'.$group->adheight.'px)';
		        if($group->cat_loc > 0 OR $group->page_loc > 0) $modus[] = __('Post Injection', 'adrotate-pro');
		        if($group->geo == 1 AND $adrotate_config['enable_geo'] > 0) $modus[] = __('Geolocation', 'adrotate-pro');
	
				$ads_in_group = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = '{$group->id}' AND `user` = 0 AND `schedule` = 0;");
				$active_ads_in_group = $wpdb->get_var("SELECT COUNT(*) FROM  `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `{$wpdb->prefix}adrotate`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`ad` AND `type` = 'active' AND `group` = '{$group->id}';");
				$class = ('alternate' != $class) ? 'alternate' : ''; ?>
			    <tr id='group-<?php echo $group->id; ?>' class='<?php echo $class; ?>'>
					<th class="check-column"><input type="checkbox" name="groupselect[]" value="<?php echo $group->id; ?>" <?php if(in_array($group->id, $meta_array)) echo "checked"; ?> /></th>
					<td><center><?php echo $group->id; ?></center></td>
					<td><?php echo $group->name; ?><span style="color:#999;"><?php echo '<br /><span style="font-weight:bold;">'.__('Mode', 'adrotate-pro').':</span> '.implode(', ', $modus); ?></span></td>
					<td><center><?php echo $ads_in_group; ?></center></td>
					<td><center><?php echo $active_ads_in_group; ?></center></td>
				</tr>
			<?php 
				unset($modus);
			} 
			?>
			</tbody>					
		</table>
	
		<p class="submit">
			<input tabindex="38" type="submit" name="adrotate_ad_submit" class="button-primary" value="<?php _e('Save Advert', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-ads&view=manage" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>
		<?php } ?>
	</form>
<?php
} else {
	echo adrotate_error('error_loading_item');
}
?>