<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

//Permissions
$permissions = get_user_meta($current_user->ID, 'adrotate_permissions', 1);
if($adrotate_config['enable_editing'] == 'Y' AND $permissions['edit'] == 'Y') {
	if(!$ad_edit_id) {
		$edit_id = $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'a_empty' AND 'author' = '{$current_user->user_login}' ORDER BY `id` DESC LIMIT 1;");
		if($edit_id == 0) {
		    $wpdb->insert($wpdb->prefix."adrotate", array('title' => '', 'bannercode' => '', 'thetime' => $now, 'updated' => $now, 'author' => $current_user->user_login, 'imagetype' => 'dropdown', 'image' => '', 'paid' => 'U', 'tracker' => 'Y', 'desktop' => 'Y', 'mobile' => 'Y', 'tablet' => 'Y', 'os_ios' => 'Y', 'os_android' => 'Y', 'os_other' => 'Y', 'responsive' => 'N', 'type' => 'a_empty', 'weight' => 6, 'budget' => 0, 'crate' => 0, 'irate' => 0, 'cities' => serialize(array()), 'countries' => serialize(array())));
		    $edit_id = $wpdb->insert_id;
		    $wpdb->insert("{$wpdb->prefix}adrotate_linkmeta", array('ad' => $edit_id, 'group' => 0, 'user' => $current_user->ID, 'schedule' => 0));
		}
		$ad_edit_id = $edit_id;
	}

	$edit_banner = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `id` = {$ad_edit_id};");
	$groups	= $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' ORDER BY `id` ASC;"); 
	$schedules = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` != '' AND `stoptime` > {$now} ORDER BY `id` ASC;");
	$linkmeta = $wpdb->get_results("SELECT `group` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad_edit_id} AND `user` = 0 AND `schedule` = 0;");
	$schedulemeta = $wpdb->get_results("SELECT `schedule` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = {$ad_edit_id} AND `group` = 0 AND `user` = 0;");

	
	$fallback = $meta_array = $schedule_array = array();
	foreach($groups as $group) { // Which groups are fallback groups?
		$fallback[] = $group->fallback;
	}
	foreach($linkmeta as $meta) { // Sort out meta data
		$meta_array[] = $meta->group;
		unset($meta);
	}	
	foreach($schedulemeta as $meta) { // Sort out schedules
		$schedule_array[] = $meta->schedule;
		unset($meta);
	}

	if($ad_edit_id AND $edit_banner->type != 'a_empty') {
		// Errors
		if($edit_banner->tracker == 'N') 
			echo '<div class="error"><p>'. __("Please contact staff, click tracking is not active!", 'adrotate-pro').'</p></div>';

		if(!preg_match_all("/(%image%|%asset%)/i", $edit_banner->bannercode, $things) AND $edit_banner->image != '') 
			echo '<div class="error"><p>'. __('You did not use %asset% (or %image%) in your AdCode but did select a file to use!', 'adrotate-pro') .' '. __("Please contact staff if you don't know what this means.", 'adrotate-pro').'</p></div>';

		if(preg_match_all("/(%image%|%asset%)/i", $edit_banner->bannercode, $things) AND $edit_banner->image == '') 
			echo '<div class="error"><p>'. __('You did use %asset% (or %image%) in your AdCode but did not select a file to use!', 'adrotate-pro') .' '. __("Please contact staff if you don't know what this means.", 'adrotate-pro').'</p></div>';

		if(count($schedule_array) == 0) 
			echo '<div class="error"><p>'. __('This advert has no schedules!', 'adrotate-pro').'</p></div>';
		
		if(!preg_match_all('/<(a|script|embed|iframe)[^>](.*?)>/i', stripslashes(htmlspecialchars_decode($edit_banner->bannercode, ENT_QUOTES)), $things) AND $edit_banner->tracker == 'Y')
			echo '<div class="error"><p>'. __("Clicktracking is enabled but no valid link/tag was found in the adcode!", 'adrotate-pro') .' '. __("Correct your adcode or contact staff if you don't know what this means.", 'adrotate-pro').'</p></div>';

		if($edit_banner->tracker == 'N' AND $edit_banner->crate > 0)
			echo '<div class="error"><p>'. __("Please contact staff, CPC is enabled but clicktracking is not active!", 'adrotate-pro').'</p></div>';

		// Ad Notices
		$adstate = adrotate_evaluate_ad($edit_banner->id);
		if($edit_banner->type == 'reject')
			echo '<div class="error"><p>'. __('This advert has been rejected by staff Please adjust the ad to conform with the requirements!', 'adrotate-pro').'</p></div>';

		if($edit_banner->type == 'queue')
			echo '<div class="error"><p>'. __('This advert is queued and awaiting review!', 'adrotate-pro').'</p></div>';

		if($edit_banner->type == 'error' AND $adstate == 'normal')
			echo '<div class="error"><p>'. __('AdRotate can not find an error but the ad is marked erroneous, try re-saving the ad!', 'adrotate-pro').'</p></div>';

		if($adstate == 'expires7days')
			echo '<div class="updated"><p>'. __('This ad will expire in less than 7 days!', 'adrotate-pro').'</p></div>';

		if($adstate == 'expires2days')
			echo '<div class="updated"><p>'. __('The ad will expire in less than 2 days!', 'adrotate-pro').'</p></div>';

		if($adstate == 'expired')
			echo '<div class="error"><p>'. __('This ad is expired and currently not rotating!', 'adrotate-pro').'</p></div>';

		if($edit_banner->type == 'disabled') 
			echo '<div class="updated"><p>'. __('This ad has been disabled and is not rotating!', 'adrotate-pro').'</p></div>';

		if($edit_banner->type == 'active') 
			echo '<div class="updated"><p>'. __('This advert is approved and currently showing on the site! Saving the advert now will put it in the moderation queue for review!', 'adrotate-pro').'</p></div>';
	}	

	// Determine image field
	if($edit_banner->imagetype == "dropdown") {
		$image_dropdown = $edit_banner->image;
	} else {
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
		        if(jQuery("#adrotate_image_dropdown").val().length > 0) var ad_image = '<?php echo WP_CONTENT_URL.$adrotate_config['banner_folder']; ?>'+jQuery("#adrotate_image_dropdown").val();
		
		        var input = input.replace(/%id%/g, <?php echo $edit_banner->id;?>);
		        var input = input.replace(/%title%/g, ad_title);
		        var input = input.replace(/%asset%/g, ad_image);
		        var input = input.replace(/%image%/g, ad_image);
		        var input = input.replace(/%random%/g, <?php echo mt_rand(100000,999999); ?>);
		        jQuery("#adrotate_preview").html(input);
		    }       
		    livePreview();
		
		    jQuery('#adrotate_bannercode').on("paste change focus focusout input", function(){ livePreview(); });
		    jQuery('#adrotate_image_dropdown').on("change", function(){ livePreview(); });
		});
		</script>
		<!-- /AdRotate JS -->
	<?php } ?>
		
	<form method="post" action="admin.php?page=adrotate-advertiser" enctype="multipart/form-data">
		<?php wp_nonce_field('adrotate_save_ad','adrotate_nonce'); ?>
		<input type="hidden" name="adrotate_username" value="<?php echo $current_user->user_login;?>" />
		<input type="hidden" name="adrotate_id" value="<?php echo $edit_banner->id;?>" />
		<input type="hidden" name="adrotate_type" value="<?php echo $edit_banner->type;?>" />
		<input type="hidden" name="MAX_FILE_SIZE" value="512000" />
	
	<?php if($edit_banner->type == 'a_empty') { ?>
		<h3><?php _e('New Advert', 'adrotate-pro'); ?></h3>
	<?php } else { ?> 
		<h3><?php _e('Edit Advert', 'adrotate-pro'); ?></h3>
	<?php } ?>

		<table class="widefat" style="margin-top: .5em">
	
			<tbody>
	      	<tr>
		        <th width="20%"><?php _e('Title', 'adrotate-pro'); ?></th>
		        <td colspan="2">
		        	<label for="adrotate_title"><input tabindex="1" name="adrotate_title" id="adrotate_title" type="text" size="50" class="search-input" value="<?php echo $edit_banner->title;?>" autocomplete="off" /> <em><?php _e('For your and the staffs reference.', 'adrotate-pro'); ?></em></label>
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('AdCode', 'adrotate-pro'); ?></th>
		        <td>
					<label for="adrotate_bannercode"><textarea tabindex="2" id="adrotate_bannercode" name="adrotate_bannercode" cols="65" rows="10"><?php echo stripslashes($edit_banner->bannercode); ?></textarea></label>
		        </td>
		        <td>
		        <p><strong><?php _e('Basic Examples:', 'adrotate-pro'); ?></strong></p>
				<p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;a href=&quot;http://www.adrotateforwordpress.com&quot;&gt;&lt;img src=&quot;%asset%&quot; /&gt;&lt;/a&gt;');return false;">&lt;a href="http://www.adrotateforwordpress.com"&gt;&lt;img src="%asset%" /&gt;&lt;/a&gt;</a></em></p>
		        <p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;span class=&quot;ad-%id%&quot;&gt;&lt;a href=&quot;http://www.adrotateforwordpress.com&quot;&gt;Text Link Ad!&lt;/a&gt;&lt;/span&gt;');return false;">&lt;span class="ad-%id%"&gt;&lt;a href="http://www.adrotateforwordpress.com"&gt;Text Link Ad!&lt;/a&gt;&lt;/span&gt;</a></em></p>
		        <p><em><a href="#" onclick="textatcursor('adrotate_bannercode','&lt;iframe src=&quot;%asset%&quot; height=&quot;250&quot; frameborder=&quot;0&quot; style=&quot;border:none;&quot;&gt;&lt;/iframe&gt;');return false;">&lt;iframe src=&quot;%asset%&quot; height=&quot;250&quot; frameborder=&quot;0&quot; style=&quot;border:none;&quot;&gt;&lt;/iframe&gt;</a></em></p>
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('Useful tags', 'adrotate-pro'); ?></th>
		        <td colspan="2">
			        <span class="description"><a href="#" title="<?php _e('Insert the advert ID Number.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','%id%');return false;">%id%</a>, <a href="#" title="<?php _e('Insert the %asset% tag. Required when selecting a image below.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','%asset%');return false;">%asset%</a>, <a href="#" title="<?php _e('Insert the advert name.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','%title%');return false;">%title%</a>, <a href="#" title="<?php _e('Insert a random seed. Useful for DFP/DoubleClick type adverts.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','%random%');return false;">%random%</a>, <a href="#" title="<?php _e('Add inside the <a> tag to open advert in a new window.', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','target=&quot;_blank&quot;');return false;">target="_blank"</a>, <a href="#" title="<?php _e('Add inside the <a> tag to tell crawlers to ignore this link', 'adrotate-pro'); ?>" onclick="textatcursor('adrotate_bannercode','rel=&quot;nofollow&quot;');return false;">rel="nofollow"</a></em><br /><?php _e('Place the cursor where you want to add a tag and click to add it to your AdCode.', 'adrotate-pro'); ?></p>
		        </td>
	      	</tr>
		  	<?php if($edit_banner->type != 'a_empty' AND $edit_banner->type != 'empty') { ?>
	      	<tr>
		  		<th valign="top"><?php _e('Live Preview', 'adrotate-pro'); ?></th>
		        <td colspan="2">
					<?php if($adrotate_config['live_preview'] == "Y") { ?>
			        	<div id="adrotate_preview"></div>
		        	<?php } else { ?>
			        	<div><?php echo adrotate_preview($edit_banner->id); ?></div>
		        	<?php } ?>
			        <br /><em><?php _e('Note: While this preview is an accurate one, it might look different then it does on the website.', 'adrotate-pro'); ?>
					<br /><?php _e('This is because of CSS differences. Your themes CSS file is not active here!', 'adrotate-pro'); ?></em>
				</td>
	      	</tr>
		  	<?php } ?>
			<tr>
		        <th valign="top"><?php _e('Banner asset', 'adrotate-pro'); ?></th>
				<td colspan="2">
					<label for="adrotate_image">
						<?php _e('Upload a file', 'adrotate-pro'); ?> <input tabindex="3" type="file" name="adrotate_image" /><br /><em><?php _e('Accepted files are:', 'adrotate-pro'); ?> jpg, jpeg, gif, png, swf <?php _e('and', 'adrotate-pro'); ?> flv.</em>
					</label><br />
					<?php _e('- OR -', 'adrotate-pro'); ?><br />
					<label for="adrotate_image_dropdown">
						<?php _e('Banner folder', 'adrotate-pro'); ?> <select tabindex="5" id="adrotate_image_dropdown" name="adrotate_image_dropdown" style="min-width: 200px;">
	   						<option value=""><?php _e('No file selected', 'adrotate-pro'); ?></option>
							<?php echo adrotate_folder_contents($image_dropdown); ?>
						</select><br />
					</label>
					<em><?php _e('Use %asset% in the adcode instead of the file path.', 'adrotate-pro'); ?> <?php _e('Use either the upload option or the dropdown menu.', 'adrotate-pro'); ?></em>
				</td>
			</tr>
			</tbody>
	
		</table>

		<h3><?php _e('Advanced', 'adrotate-pro'); ?></h3>
		<table class="widefat" style="margin-top: .5em">
	
			<tbody>
	       	<tr>
			    <th width="15%" valign="top"><?php _e('Weight', 'adrotate-pro'); ?></th>
		        <td width="17%">
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="5" name="adrotate_weight" value="2" <?php if($edit_banner->weight == "2") { echo 'checked'; } ?> /><br /><?php _e('Few impressions', 'adrotate-pro'); ?></center>
		        	</label>
				</td>
		        <td width="17%">
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="6" name="adrotate_weight" value="4" <?php if($edit_banner->weight == "4") { echo 'checked'; } ?> /><br /><?php _e('Less than average', 'adrotate-pro'); ?></center>
		        	</label>
				</td>
		        <td width="17%">
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="7" name="adrotate_weight" value="6" <?php if($edit_banner->weight == "6") { echo 'checked'; } ?> /><br /><?php _e('Normal impressions', 'adrotate-pro'); ?></center>
		        	</label>
				</td>
		        <td width="17%">
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="8" name="adrotate_weight" value="8" <?php if($edit_banner->weight == "8") { echo 'checked'; } ?> /><br /><?php _e('More than average', 'adrotate-pro'); ?></center>
		        	</label>
				</td>
		        <td>
		        	<label for="adrotate_weight">
		        	<center><input type="radio" tabindex="9" name="adrotate_weight" value="10" <?php if($edit_banner->weight == "10") { echo 'checked'; } ?> /><br /><?php _e('Many impressions', 'adrotate-pro'); ?>
		        	</label>
				</td>
			</tr>
			<?php if($permissions['mobile'] == 'Y' AND $adrotate_config['enable_mobile_advertisers'] == 1) { ?>
	     	<tr>
		        <th width="15%" valign="top"><?php _e('Device', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_desktop"><center><input tabindex="9" type="checkbox" name="adrotate_desktop" <?php if($edit_banner->desktop == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Computers', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td>
		        	<label for="adrotate_mobile"><center><input tabindex="10" type="checkbox" name="adrotate_mobile" <?php if($edit_banner->mobile == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Smartphones', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td>
		        	<label for="adrotate_tablet"><center><input tabindex="11" type="checkbox" name="adrotate_tablet" <?php if($edit_banner->tablet == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Tablets', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td colspan="2" rowspan="2">
		        	<em><?php _e('Operating system detection only detects iOS/Android/Others or neither. Only works if Smartphones and/or Tablets is enabled.', 'adrotate-pro'); ?></em>
		        </td>
			</tr>
	     	<tr>
		        <th width="15%" valign="top"><?php _e('Mobile OS', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_ios"><center><input tabindex="12" type="checkbox" name="adrotate_ios" <?php if($edit_banner->os_ios == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('iOS', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td>
		        	<label for="adrotate_android"><center><input tabindex="13" type="checkbox" name="adrotate_android" <?php if($edit_banner->os_android == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Android', 'adrotate-pro'); ?></center></label>
		        </td>
		        <td>
		        	<label for="adrotate_other"><center><input tabindex="14" type="checkbox" name="adrotate_other" <?php if($edit_banner->os_other == 'Y') { ?>checked="checked" <?php } ?> /><br /><?php _e('Others', 'adrotate-pro'); ?></center></label>
		        </td>
			</tr>
			<?php } ?>
			</tbody>
		</table>

		<?php if($permissions['geo'] == 'Y' AND $adrotate_config['enable_geo'] > 0 AND $adrotate_config['enable_geo_advertisers'] == 1) { ?>
			<?php $cities = unserialize(stripslashes($edit_banner->cities)); ?>
			<?php $countries = unserialize(stripslashes($edit_banner->countries)); ?>
			<h2><?php _e('Geo Targeting', 'adrotate-pro'); ?></h2>
			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">
			
					<div id="postbox-container-1" class="postbox-container" style="width:50%;">
						<div class="meta-box-sortables">
							
							<div class="postbox-ajdg">
								<div class="inside">
									<p><strong>Select Countries and or Regions</strong></p>
									<div class="adrotate-select">
								        <?php echo adrotate_select_countries($countries); ?>
									</div>
								</div>
							</div>
			
						</div>
					</div>
		
					<div id="postbox-container-3" class="postbox-container" style="width:50%;">
						<div class="meta-box-sortables">
									
							<div class="postbox-ajdg">
								<div class="inside">
		
									<p><strong>Enter cities, metro IDs, States or State ISO codes</strong></p>
									<textarea tabindex="36" name="adrotate_geo_cities" class="geo-cities" cols="40" rows="6"><?php echo (is_array($cities)) ? implode(', ', $cities) : ''; ?></textarea><br />
				        <p><em><?php _e('A comma separated list of items:', 'adrotate-pro'); ?> (Alkmaar, New York, Manila, Tokyo) <?php _e('AdRotate does not check the validity of names so make sure you spell them correctly!', 'adrotate-pro'); ?></em></p>
								</div>
							</div>
		
						</div>
					</div>
		
			    </div>
		    </div>
		   	<div class="clear"></div>
      	<?php } ?>
		
		<?php if($groups) { ?>
		<h3><?php _e('Select Groups', 'adrotate-pro'); ?></h3>
		<p><em><?php _e('Select where your ad should be visible. If your desired group/location is not listed or the specification is unclear contact your publisher.', 'adrotate-pro'); ?></em></p>
		<table class="widefat" style="margin-top: .5em">
			<thead>
			<tr>
				<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
		        <th width="4%"><?php _e('ID', 'adrotate-pro'); ?></th>
				<th>&nbsp;</th>
			</tr>
			</thead>
	
			<tbody>
			<?php 
			$class = '';
			foreach($groups as $group) {
				
				if(in_array($group->id, $fallback)) continue;

				if($group->adspeed > 0) $adspeed = $group->adspeed / 1000;
		        if($group->modus == 0) $modus[] = __('Default', 'adrotate-pro');						
		        if($group->modus == 1) $modus[] = __('Dynamic', 'adrotate-pro').' ('.$adspeed.' '. __('second rotation', 'adrotate-pro').')';
		        if($group->modus == 2) $modus[] = __('Block', 'adrotate-pro').' ('.$group->gridrows.' x '.$group->gridcolumns.' '. __('grid', 'adrotate-pro').')';
				if($group->adwidth > 0 AND $group->adheight > 0) $modus[] = $group->adwidth.'x'.$group->adheight.'px';
		        if($group->geo == 1 AND $adrotate_config['enable_geo'] > 0) $modus[] = __('Geolocation', 'adrotate-pro');
		        if($group->mobile == 1) $modus[] = __('Mobile', 'adrotate-pro');

				$class = ('alternate' != $class) ? 'alternate' : ''; ?>
			    <tr id='group-<?php echo $group->id; ?>' class='<?php echo $class; ?>'>
					<th class="check-column" width="2%"><input type="checkbox" name="groupselect[]" value="<?php echo $group->id; ?>" <?php if(in_array($group->id, $meta_array)) echo "checked"; ?> /></th>
					<td><?php echo $group->id; ?></td>
					<td><strong><?php echo $group->name; ?></strong><br /><span style="color:#999;"><?php echo implode(', ', $modus); ?></span></td>
				</tr>
			<?php 
				unset($modus);
			} 
			?>
			</tbody>					
		</table>
		<?php } ?>

		<?php if($schedules) { ?>
		<h3><?php _e('Choose Schedules', 'adrotate-pro'); ?></h3>
		<p><em><?php _e('Select when your ad should be visible. If your desired timeframe is not listed contact your publisher.', 'adrotate-pro'); ?></em></p>
		<table class="widefat" style="margin-top: .5em">
	
			<thead>
			<tr>
				<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
		        <th width="4%"><?php _e('ID', 'adrotate-pro'); ?></th>
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
			$class = '';
			foreach($schedules as $schedule) { 
				if($adrotate_config['stats'] == 1) {
					if($schedule->maxclicks == 0) $schedule->maxclicks = '&infin;';
					if($schedule->maximpressions == 0) $schedule->maximpressions = '&infin;';
				}

				$class = ('alternate' != $class) ? 'alternate' : '';
				if(in_array($schedule->id, $schedule_array)) $class = 'row_green'; 
				if($schedule->stoptime < $in2days) $class = 'row_red'; 

				$sdayhour = substr($schedule->daystarttime, 0, 2);
				$sdayminute = substr($schedule->daystarttime, 2, 2);
				$edayhour = substr($schedule->daystoptime, 0, 2);
				$edayminute = substr($schedule->daystoptime, 2, 2);
				$tick = '<img src="'.plugins_url('../../images/tick.png', __FILE__).'" width="10" height"10" />';
				$cross = '<img src="'.plugins_url('../../images/cross.png', __FILE__).'" width="10" height"10" />';
			?>
	      	<tr id='schedule-<?php echo $schedule->id; ?>' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="scheduleselect[]" value="<?php echo $schedule->id; ?>" <?php if(in_array($schedule->id, $schedule_array)) echo "checked"; ?> /></th>
				<td><?php echo $schedule->id; ?></td>
				<td><?php echo date_i18n("F d, Y H:i", $schedule->starttime);?><br /><span style="color: <?php echo adrotate_prepare_color($schedule->stoptime);?>;"><?php echo date_i18n("F d, Y H:i", $schedule->stoptime);?></span></td>
				<td><strong><?php echo stripslashes(html_entity_decode($schedule->name)); ?></strong><br /><span style="color:#999;"><?php _e('Mon:', 'adrotate-pro'); ?> <?php echo ($schedule->day_mon == 'Y') ? $tick : $cross; ?> <?php _e('Tue:', 'adrotate-pro'); ?> <?php echo ($schedule->day_tue == 'Y') ? $tick : $cross; ?> <?php _e('Wed:', 'adrotate-pro'); ?> <?php echo ($schedule->day_wed == 'Y') ? $tick : $cross; ?> <?php _e('Thu:', 'adrotate-pro'); ?> <?php echo ($schedule->day_thu == 'Y') ? $tick : $cross; ?> <?php _e('Fri:', 'adrotate-pro'); ?> <?php echo ($schedule->day_fri == 'Y') ? $tick : $cross; ?> <?php _e('Sat:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sat == 'Y') ? $tick : $cross; ?> <?php _e('Sun:', 'adrotate-pro'); ?> <?php echo ($schedule->day_sun == 'Y') ? $tick : $cross; ?> <?php if($schedule->daystarttime  > 0) { ?><?php _e('Between:', 'adrotate-pro'); ?> <?php echo $sdayhour; ?>:<?php echo $sdayminute; ?> - <?php echo $edayhour; ?>:<?php echo $edayminute; ?> <?php } ?><br /><?php _e('Impression spread:', 'adrotate-pro'); ?> <?php echo ($schedule->spread == 'Y') ? $tick : $cross; ?></span></td>
				<?php if($adrotate_config['stats'] == 1) { ?>
			        <td><center><?php echo $schedule->maximpressions; ?></center></td>
			        <td><center><?php echo $schedule->maxclicks; ?></center></td>
				<?php } ?>
	      	</tr>
	      	<?php } ?>
			</tbody>
	
		</table>
		<p><center>
			<span style="border: 1px solid #518257; height: 12px; width: 12px; background-color: #e5faee">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("In use by this advert.", "adrotate-pro"); ?>
			&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Expires soon.", "adrotate-pro"); ?>
		</center></p>
	  	<?php } ?>
	
		<p class="submit">
			<input tabindex="16" type="submit" name="adrotate_advertiser_ad_submit" class="button-primary" value="<?php _e('Submit ad for review', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate&view=adrotate-advertiser" class="button"><?php _e('Cancel', 'adrotate-pro'); ?></a>
		</p>
		
		</form>
<?php } else { ?>
	<h3><?php _e('Editing and creating adverts is not available right now', 'adrotate-pro'); ?></h3>
	<p><?php _e('The administrator has disabled editing of adverts.', 'adrotate-pro'); ?> <a href="admin.php?page=adrotate-advertiser&view=message&request=other&id=<?php echo $edit_banner->id; ?>&_wpnonce=<?php echo wp_create_nonce('adrotate_email_advertiser_'.$edit_banner->id); ?>"><?php _e('Contact sales', 'adrotate-pro'); ?></a>.</p>

<?php } ?>