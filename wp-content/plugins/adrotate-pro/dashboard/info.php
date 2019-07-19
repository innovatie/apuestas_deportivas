<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

$banners = $groups = $schedules = $queued = $unpaid = 0;
$banners = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate` WHERE `type` != 'empty' AND `type` != 'a_empty';");
$groups = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '';");
$schedules = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` != '';");
$queued = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'queue' OR `type` = 'reject';");
$data = get_option("adrotate_advert_status");

// Random banner for Media.net
$partner = mt_rand(1,3);
?>

<div id="dashboard-widgets-wrap">
	<div id="dashboard-widgets" class="metabox-holder">

		<div id="postbox-container-1" class="postbox-container" style="width:50%;">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				
				<h3><?php _e('Currently', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
						<table width="100%">
							<thead>
							<tr class="first">
								<td width="50%"><strong><?php _e('Your setup', 'adrotate-pro'); ?></strong></td>
								<td width="50%"><strong><?php _e('Adverts that need you', 'adrotate-pro'); ?></strong></td>
							</tr>
							</thead>
							
							<tbody>
							<tr class="first">
								<td class="first b"><a href="admin.php?page=adrotate-ads"><?php echo $banners; ?> <?php _e('Adverts', 'adrotate-pro'); ?></a></td>
								<td class="b"><a href="admin.php?page=adrotate-ads"><?php echo $data['expiressoon'] + $data['expired']; ?> <?php _e('(Almost) Expired', 'adrotate-pro'); ?></a></td>
							</tr>
							<tr>
								<td class="first b"><a href="admin.php?page=adrotate-groups"><?php echo $groups; ?> <?php _e('Groups', 'adrotate-pro'); ?></a></td>
								<td class="b"><a href="admin.php?page=adrotate-ads"><?php echo $data['error']; ?> <?php _e('Have errors', 'adrotate-pro'); ?></a></td>
							</tr>
							<tr>
								<td class="first b"><a href="admin.php?page=adrotate-schedules"><?php echo $schedules; ?> <?php _e('Schedules', 'adrotate-pro'); ?></a></td>
								<td class="b"><?php echo ($adrotate_config['enable_advertisers'] == 'Y') ? '<a href="admin.php?page=adrotate-ads&view=queue">'.$queued.' '.__('Queued', 'adrotate-pro').'</a>' : '&nbsp;'; ?></td>
							</tr>
							<tr>
								<td colspan="2">
									<p><strong><?php _e('Support AdRotate', 'adrotate-pro'); ?></strong></p>
									<p><?php _e('Consider writing a review if you like AdRotate. Also take a look at my Facebook page for updates about me and my plugins. Thank you!', 'adrotate-pro'); ?><br />
									<center><a class="button-primary" href="https://paypal.me/arnandegans/10usd" target="_blank">Donate $10 via Paypal</a>&nbsp;&nbsp;<a class="button" target="_blank" href="https://wordpress.org/support/plugin/adrotate/reviews/?rate=5#new-post">Write review on WordPress.org</a></center><br />
								</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>

				<h3><?php _e('Fast Email Support', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
					<?php if($a['status'] == 1) { ?>					
						<form name="request" id="post" method="post" action="admin.php?page=adrotate">
							<?php wp_nonce_field('ajdg_nonce_support_request','ajdg_nonce_support'); ?>
						
							<p><img src="<?php echo plugins_url('/images/icon-support.png', dirname(__FILE__)); ?>" class="alignleft pro-image" />&raquo; <?php _e('What went wrong? Or what are you trying to do?', 'adrotate-pro'); ?><br />&raquo; <?php _e('Include error messages and/or relevant information.', 'adrotate-pro'); ?><br />&raquo; <?php _e('Try to remember any actions that may cause the problem.', 'adrotate-pro'); ?><br />&raquo; <?php _e('Any code/HTML will be stripped from your message.', 'adrotate-pro'); ?></p>
						
							<p><label for="ajdg_support_username"><strong><?php _e('Your name:', 'adrotate-pro'); ?></strong><br /><input tabindex="1" name="ajdg_support_username" type="text" class="search-input" style="width:100%;" value="<?php echo $current_user->display_name;?>" autocomplete="off" /></label></p>
							<p><label for="ajdg_support_email"><strong><?php _e('Your Email Address:', 'adrotate-pro'); ?></strong><br /><input tabindex="1" name="ajdg_support_email" type="text" class="search-input" style="width:100%;" value="<?php echo $current_user->user_email;?>" autocomplete="off" /></label></p>
							<p><label for="ajdg_support_subject"><strong><?php _e('Subject:', 'adrotate-pro'); ?></strong><br /><input tabindex="2" name="ajdg_support_subject" type="text" class="search-input" style="width:100%;" value="" autocomplete="off" /></label></p>
							<p><label for="ajdg_support_message"><strong><?php _e('Problem description / Question:', 'adrotate-pro'); ?></strong><br /><textarea tabindex="3" name="ajdg_support_message" style="width:100%; height:100px;"></textarea></label></p>
							<p><label for="ajdg_support_account"><input tabindex="4" name="ajdg_support_account" type="checkbox" /> <?php _e('Please log in to my website and take a look.', 'adrotate-pro'); ?> <span class="ajdg-tooltip">What's this?<span class="ajdg-tooltiptext ajdg-tooltip-top">Checking this option will create an account for Arnan to log in and take a look at your setup.</span>
</span></label></p>
						
							<p><strong><?php _e('When you send this form the following data will be submitted:', 'adrotate-pro'); ?></strong><br/>
							<em><?php _e('Your name, Account email address, Your website url and some basic WordPress information will be included with the message.', 'adrotate-pro'); ?><br /><?php _e('This information is treated as confidential and is mandatory.', 'adrotate-pro'); ?></em></p>
						
							<p class="submit">
								<input tabindex="4" type="submit" name="adrotate_support_submit" class="button-primary" value="<?php _e('Send Email', 'adrotate-pro'); ?>" />&nbsp;&nbsp;&nbsp;<em><?php _e('Please use English or Dutch only!', 'adrotate-pro'); ?></em>
							</p>

							<p><strong><?php _e('Note:', 'adrotate-pro'); ?></strong> <?php _e('Sending multiple messages with the same question will put you at the very end of my support priorities. Please do not double post!', 'adrotate-pro'); ?></p>
						
						</form>
			
					<?php } else { ?>
						<p><img src="<?php echo plugins_url('/images/icon-support.png', dirname(__FILE__)); ?>" class="alignleft pro-image" /><?php _e('When you activate your AdRotate Pro license you can use fast email support. No more queueing up in the forums. Email support is get priority over the forums and is checked almost every workday.', 'adrotate-pro'); ?></p>

						<p class="submit">
							<?php if(adrotate_is_networked()) { ?>
								<a href="<?php echo network_admin_url('admin.php?page=adrotate'); ?>" class="button-primary"><?php _e('Activate License', 'adrotate-pro'); ?></a>
							<?php } else { ?>
								<a href="<?php echo admin_url('admin.php?page=adrotate-settings'); ?>" class="button-primary"><?php _e('Activate License', 'adrotate-pro'); ?></a>	
							<?php } ?>
							<em><?php _e('Contact your site administrator if you do not know what this means.', 'adrotate-pro'); ?></em>
						</p>
					<?php }	?>

					</div>
				</div>

			</div>
		</div>

		<div id="postbox-container-3" class="postbox-container" style="width:50%;">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
						
				<h3><?php _e('Arnan de Gans News & Updates', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
						<p><center><a href="https://www.arnan.me" title="Visit Arnan's website" target="_blank"><img src="<?php echo plugins_url("/images/buttons/1.png", dirname(__FILE__)); ?>" alt="Arnan de Gans website" /></a><a href="https://ajdg.solutions" title="Visit the AdRotate website" target="_blank"><img src="<?php echo plugins_url("/images/buttons/2.png", dirname(__FILE__)); ?>" alt="AJdG Solutions website" /></a><a href="https://www.facebook.com/ajdgsolutions/" title="AJdG Solutions on Facebook" target="_blank"><img src="<?php echo plugins_url("/images/buttons/4.png", dirname(__FILE__)); ?>" alt="Arnan de Gans on Facebook" /></a></center></p>
						<?php wp_widget_rss_output(array(
							'url' => 'http://ajdg.solutions/feed/', 
							'items' => 3, 
							'show_summary' => 1, 
							'show_author' => 0, 
							'show_date' => 1)
						); ?>
					</div>
				</div>

				<h3><?php _e('Join the Media.net advertising network', 'adrotate-pro'); ?></h3>
				<div class="postbox-ajdg">
					<div class="inside">
						<center><a href="https://ajdg.solutions/go/medianet/" target="_blank"><img src="<?php echo plugins_url("/images/offers/medianet-large-$partner.jpg", dirname(__FILE__)); ?>" width="440" /></a></center>
						<p><a href="https://ajdg.solutions/go/medianet/" target="_blank">Media.net</a> is the <strong>#2 largest contextual ads platform</strong> in the world that provides its publishers with an <strong>exclusive access to the Yahoo! Bing Network of advertisers and $6bn worth of search demand.</strong></p>

						<p><a href="https://ajdg.solutions/go/medianet/" target="_blank">Media.net</a> <strong>ads are contextual</strong> and hence always relevant to your content. They are also <strong>native by design</strong> and highly customizable, delivering a great user experience and higher CTRs.</p>
						
						<strong><u>Exclusive offer for AdRotate users</u></strong>
						<p>As an AdRotate user, sign up with <a href="https://ajdg.solutions/go/medianet/" target="_blank">Media.net</a> and you'll earn 10% more, over and above your regular earnings for your first 3 months. <strong>Sign up now!</strong></p>
						
						<p><a class="button-primary" href="https://ajdg.solutions/go/medianet/" target="_blank">Sign up with Media.net now &raquo;</a>&nbsp;&nbsp;<a class="button" target="_blank" href="https://ajdg.solutions/go/medianet/">Learn more &raquo;</a></p>
					</div>
				</div>

			</div>	
		</div>

	</div>

	<div class="clear"></div>

	<p><?php echo adrotate_trademark(); ?></p>

</div>