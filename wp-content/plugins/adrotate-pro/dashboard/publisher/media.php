<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
?>

<?php $assets = adrotate_subfolder_contents(WP_CONTENT_DIR."/".$adrotate_config['banner_folder']); ?>

<form method="post" action="admin.php?page=adrotate-media" enctype="multipart/form-data">
	<?php wp_nonce_field('adrotate_save_media','adrotate_nonce'); ?>
	<input type="hidden" name="MAX_FILE_SIZE" value="512000" />

	<h2><?php _e('Upload new file', 'adrotate-pro'); ?></h2>
	<select tabindex="5" id="adrotate_image_location" name="adrotate_image_location" style="min-width: 200px;">
		<option value="<?php echo $adrotate_config['banner_folder']; ?>"><?php echo $adrotate_config['banner_folder']; ?></option>
	<?php
	if(count($assets) > 0) {
		foreach($assets as $asset) {
			if(array_key_exists("contents", $asset)) {
				echo '<option value="'.$adrotate_config['banner_folder'].'/'.$asset['basename'].'">&mdash; '.$asset['basename'].'</option>';
				foreach($asset['contents'] as $level_one) {
					if(array_key_exists("contents", $level_one)) {
						echo '<option value="'.$adrotate_config['banner_folder'].'/'.$asset['basename'].'/'.$level_one['basename'].'">&mdash; &mdash; '.$level_one['basename'].'</option>';
					}
				}		
			}
		}
	}
	?>
	</select>
	<label for="adrotate_image"><input tabindex="1" type="file" name="adrotate_image" /><br /><em><strong><?php _e('Accepted files:', 'adrotate-pro'); ?></strong> jpg, jpeg, gif, png, html, js, swf and flv. <?php _e('Maximum size is 512Kb per file.', 'adrotate-pro'); ?></em><br /><em><strong><?php _e('Important:', 'adrotate-pro'); ?></strong> <?php _e('Make sure your file has no spaces or special characters in the name. Replace spaces with a - or _.', 'adrotate-pro'); ?><br /><?php _e('If you remove spaces from filenames for HTML5 adverts also edit the html file so it knows about the changed name. For example for the javascript file.', 'adrotate-pro'); ?></em></label>

	<p class="submit">
		<input tabindex="2" type="submit" name="adrotate_media_submit" class="button-primary" value="<?php _e('Upload file', 'adrotate-pro'); ?>" /> <em><?php _e('Click only once per file!', 'adrotate-pro'); ?></em>
	</p>
</form>

<h2><?php _e('Available files in', 'adrotate-pro'); ?> '<?php echo '/'.$adrotate_config['banner_folder']; ?>'</h2>
<table class="widefat" style="margin-top: .5em">

	<thead>
	<tr>
        <th><?php _e('Name', 'adrotate-pro'); ?></th>
	</tr>
	</thead>

	<tbody>
	<?php
	if(count($assets) > 0) {
		$class = '';
		foreach($assets as $asset) {
			$class = ($class != 'alternate') ? 'alternate' : '';
			
			echo "<tr class=\"$class\">";
			echo "<td>";
			echo $asset['basename'];
			echo "<span style=\"float:right;\"><a href=\"".admin_url('/admin.php?page=adrotate-media&file='.$asset['basename'])."&_wpnonce=".wp_create_nonce('adrotate_delete_media_'.$asset['basename'])."\" title=\"".__('Delete', 'adrotate-pro')."\">".__('Delete', 'adrotate-pro')."</a></span>";
			if(array_key_exists("contents", $asset)) {
				echo "<small>";
				foreach($asset['contents'] as $level_one) {
					echo "<br />&mdash; ".$level_one['basename'];
					echo "<span style=\"float:right;\"><a href=\"".admin_url('/admin.php?page=adrotate-media&file='.$asset['basename'].'/'.$level_one['basename'])."&_wpnonce=".wp_create_nonce('adrotate_delete_media_'.$asset['basename'].'/'.$level_one['basename'])."\" title=\"".__('Delete', 'adrotate-pro')."\">".__('Delete', 'adrotate-pro')."</a></span>";
					if(array_key_exists("contents", $level_one)) {
						foreach($level_one['contents'] as $level_two) {
							echo "<br />&mdash;&mdash; ".$level_two['basename'];
							echo "<span style=\"float:right;\"><a href=\"".admin_url('/admin.php?page=adrotate-media&file='.$asset['basename'].'/'.$level_one['basename'].'/'.$level_two['basename'])."&_wpnonce=".wp_create_nonce('adrotate_delete_media_'.$asset['basename'].'/'.$level_one['basename'].'/'.$level_two['basename'])."\" title=\"".__('Delete', 'adrotate-pro')."\">".__('Delete', 'adrotate-pro')."</a></span>";
						}		
					}
				}		
				echo "</small>";
			}
			echo "</td>";
			echo "</tr>";
		}
	} else {
		echo "<tr class=\"alternate\">";
		echo "<td><em>".__('No files found!', 'adrotate-pro')."</em></td>";
		echo "</tr>";
	}
	?>
	</tbody>
</table>
<p><center><small>
	<?php _e("Make sure the banner images are not in use by adverts when you delete them!", "adrotate-pro"); ?> <?php _e("Deleting a folder deletes everything inside that folder as well!", "adrotate-pro"); ?>
</small></center></p>