<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
$permissions = get_user_meta($user_id, 'adrotate_permissions', 1);
$notes = get_user_meta($user_id, 'adrotate_notes', 1);

if(!isset($permissions['edit'])) $permissions['edit'] = 'N';
if(!isset($permissions['mobile'])) $permissions['mobile'] = 'N';
if(!isset($permissions['geo'])) $permissions['geo'] = 'N';
?>

<h2><?php _e('Advertiser Profile', 'adrotate-pro'); ?></h2>
<div id="dashboard-widgets-wrap">
	<div id="dashboard-widgets" class="metabox-holder">

		<form name="request" id="post" method="post" action="admin.php?page=adrotate-advertisers&view=profile">
		<?php wp_nonce_field('adrotate_save_advertiser','adrotate_nonce'); ?>
		<input type="hidden" name="adrotate_user" value="<?php echo $user_id;?>" />

		<div id="postbox-container-1" class="postbox-container" style="width:50%;">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				
				<h3><?php _e('Profile', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
						<table width="100%">
							<thead>
							<tr class="first">
								<td width="50%"><strong><?php _e('Who', 'adrotate-pro'); ?></strong></td>
								<td width="50%"><strong><?php _e('What', 'adrotate-pro'); ?></strong></td>
							</tr>
							</thead>
							
							<tbody>
							<tr class="first">
								<td class="first b"><?php echo $advertisers[$user_id]['name']; ?><br /><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertisers&view=contact&user='.$user_id);?>" title="<?php _e('Contact', 'adrotate-pro'); ?>"><?php echo $advertisers[$user_id]['email']; ?></a></td>
								<td class="b"><?php echo $advertisers[$user_id]['has_adverts']; ?> Adverts</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>

				<h3><?php _e('Notes', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
						<textarea tabindex="1" name="adrotate_notes" cols="50" rows="5" class="noborder"><?php echo esc_attr($notes); ?></textarea><br />
						<em><?php _e('No HTML/Javascript or code allowed.', 'adrotate-pro'); ?></em>
					</div>
				</div>

			</div>
		</div>

		<div id="postbox-container-3" class="postbox-container" style="width:50%;">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
						
				<h3><?php _e('Permissions', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
			        	<label for="adrotate_can_edit"><input tabindex="2" type="checkbox" name="adrotate_can_edit" <?php if($permissions['edit'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Create and edit their own adverts?', 'adrotate-pro'); ?></label><br />
			        	<label for="adrotate_can_mobile"><input tabindex="3" type="checkbox" name="adrotate_can_mobile" <?php if($permissions['mobile'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Specify devices for mobile adverts?', 'adrotate-pro'); ?></label><br />
			        	<label for="adrotate_can_geo"><input tabindex="4" type="checkbox" name="adrotate_can_geo" <?php if($permissions['geo'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can set up Geo Targeting?', 'adrotate-pro'); ?></label>
					</div>
				</div>

			</div>	
		</div>

		<div class="clear"></div>

		<p class="submit">
			<input tabindex="4" type="submit" name="adrotate_advertiser_submit" class="button-primary" value="<?php _e('Save', 'adrotate-pro'); ?>" />
			<a href="admin.php?page=adrotate-advertisers" class="button"><?php _e('Back', 'adrotate-pro'); ?></a>
		</p>
		</form>		

	</div>
</div>