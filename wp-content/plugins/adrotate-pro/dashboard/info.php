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
$banners = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'active';");
$groups = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '';");
$schedules = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` != '';");
$queued = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'queue' OR `type` = 'reject';");
$data = get_option("adrotate_advert_status");

// Random banner for Media.net
$partner = mt_rand(1,3);

// Review URL
$license = get_site_option('adrotate_activate');
$license = (!$license) ? 'single' : strtolower($license['type']);
?>

<div id="dashboard-widgets-wrap">
	<div id="dashboard-widgets" class="metabox-holder">
		<div id="left-column" class="ajdg-postbox-container">

			<div class="ajdg-postbox">				
				<h2 class="ajdg-postbox-title"><?php _e('At a Glance', 'adrotate-pro'); ?></h2>
				<div id="currently" class="ajdg-postbox-content">
					<table width="100%">
						<thead>
						<tr class="first">
							<td width="50%"><strong><?php _e('Your setup', 'adrotate-pro'); ?></strong></td>
							<td width="50%"><strong><?php _e('Adverts that need you', 'adrotate-pro'); ?></strong></td>
						</tr>
						</thead>
						
						<tbody>
						<tr class="first">
							<td class="first b"><a href="admin.php?page=adrotate-ads"><?php echo $banners; ?> <?php _e('Active Adverts', 'adrotate-pro'); ?></a></td>
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
								<p><?php _e('Consider writing a review if you like AdRotate. Also take a look at my Facebook page for updates about me and my plugins. Thank you!', 'adrotate-pro'); ?></p>
								
								<p><center><a class="button-primary" target="_blank" href="https://ajdg.solutions/product/adrotate-pro-<?php echo $license; ?>/#tab-reviews">Write review on ajdg.solutions</a> <a class="button" target="_blank" href="https://wordpress.org/support/plugin/adrotate/reviews/?rate=5#new-post">Write review on WordPress.org</a></center></p>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.3&appId=819007458455318"></script>
								<p><center><div class="fb-group" data-href="https://www.facebook.com/groups/ajdg.wordpress.help/" data-width="500" data-show-social-context="false" data-show-metadata="true"></div></center></p>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

		</div>
		<div id="right-column" class="ajdg-postbox-container">

			<div class="ajdg-postbox">				
				<h2 class="ajdg-postbox-title"><?php _e('News & Updates', 'adrotate-pro'); ?></h2>
				<div id="news" class="ajdg-postbox-content">
					<p><center><a href="https://www.arnan.me" title="Visit Arnan's website" target="_blank"><img src="<?php echo plugins_url("/images/buttons/1.png", dirname(__FILE__)); ?>" alt="Arnan de Gans website" /></a><a href="https://ajdg.solutions" title="Visit the AdRotate website" target="_blank"><img src="<?php echo plugins_url("/images/buttons/2.png", dirname(__FILE__)); ?>" alt="AJdG Solutions website" /></a><a href="https://www.facebook.com/ajdgsolutions/" title="AJdG Solutions on Facebook" target="_blank"><img src="<?php echo plugins_url("/images/buttons/4.png", dirname(__FILE__)); ?>" alt="Arnan de Gans on Facebook" /></a></center></p>
					<?php wp_widget_rss_output(array(
						'url' => 'http://ajdg.solutions/feed/', 
						'items' => 5, 
						'show_summary' => 1, 
						'show_author' => 0, 
						'show_date' => 1)
					); ?>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="clear"></div>

<hr>
<h1><?php _e('Advertising Partners & Affiliates', 'adrotate-pro'); ?></h1>
<em><?php _e('A selection of quality advertiser networks and useful products. If you need adverts or just want more or more diverse adverts. Check out these great options!', 'adrotate-pro'); ?></em>

<div id="dashboard-widgets-wrap">
	<div id="dashboard-widgets" class="metabox-holder">
		<div id="left-column" class="ajdg-postbox-container">

			<div class="ajdg-postbox">				
				<h2 class="ajdg-postbox-title"><?php _e('Join the Media.net advertising network', 'adrotate-pro'); ?></h2>
				<div id="currently" class="ajdg-postbox-content">
					<center><a href="https://ajdg.solutions/go/medianet/" target="_blank"><img src="<?php echo plugins_url("/images/offers/medianet-large-$partner.jpg", dirname(__FILE__)); ?>" width="440" /></a></center>
					<p><a href="https://ajdg.solutions/go/medianet/" target="_blank">Media.net</a> is the <strong>#2 largest contextual ads platform</strong> in the world that provides its publishers with an <strong>exclusive access to the Yahoo! Bing Network of advertisers and $6bn worth of search demand.</strong></p>

					<p><a href="https://ajdg.solutions/go/medianet/" target="_blank">Media.net</a> <strong>ads are contextual</strong> and hence always relevant to your content. They are also <strong>native by design</strong> and highly customizable, delivering a great user experience and higher CTRs.</p>
					
					<strong><u>Exclusive offer for AdRotate users</u></strong>
					<p>As an AdRotate user, sign up with <a href="https://ajdg.solutions/go/medianet/" target="_blank">Media.net</a> and you'll earn 10% more, over and above your regular earnings for your first 3 months. <strong>Sign up now!</strong></p>
					
					<p><a class="button-primary" href="https://ajdg.solutions/go/medianet/" target="_blank">Sign up with Media.net &raquo;</a>&nbsp;&nbsp;<a class="button" target="_blank" href="https://ajdg.solutions/go/medianet/">Learn more &raquo;</a></p>
				</div>
			</div>

<!--
			<div class="ajdg-postbox">				
				<h2 class="ajdg-postbox-title"><?php _e('DropBox online storage solutions', 'adrotate-pro'); ?></h2>
				<div id="dropbox" class="ajdg-postbox-content">
					<p>Easily share and synchronise files between computers and devices. Get a FREE account today and get started within minutes. I use Dropbox to for many development purposes as a sort of back-up. But also to sync important documents to my phone for immigration services and hotel bookings.</p>
					<p><center><a rel="nofollow" href="https://ajdg.solutions/go/dropbox/"><img src="<?php echo plugins_url("/images/offers/dropbox.jpg", dirname(__FILE__)); ?>" width="440" alt="Dropbox.com"></a></center></p>
				</div>
			</div>
-->

		</div>
		<div id="right-column" class="ajdg-postbox-container">

			<div class="ajdg-postbox">				
				<h2 class="ajdg-postbox-title"><?php _e('Sign up with Blind Ferret', 'adrotate-pro'); ?></h2>
				<div id="current" class="ajdg-postbox-content">
					<center><a href="https://ajdg.solutions/go/blindferret/" target="_blank"><img src="<?php echo plugins_url("/images/offers/blindferret.jpg", dirname(__FILE__)); ?>" width="440" /></a></center>
					<p>At <a href="https://ajdg.solutions/go/blindferret/" target="_blank">Blind Ferret</a>, we are publishers too, which means we know what's needed to create successful campaigns! We know that advertising isn't just "set it and forget it" anymore. Our Publisher Network features a wide range of creative and comic sites, is simple to take advantage of and intensely UI/UX focused.</p>
					
					<p>With over 15 years of experience, <a href="https://ajdg.solutions/go/blindferret/" target="_blank">Blind Ferret</a> can offer great ads at top dollar via header bidding, ensuring advertisers vie for your ad space, which brings in higher quality ads and makes you more money! Ahead of the industry curve, we take care of the details, leaving you more time to do what you do best: run your business!</p>
											
					<p><a class="button-primary" href="https://ajdg.solutions/go/blindferret/" target="_blank">Sign up with Blind Ferret &raquo;</a>&nbsp;&nbsp;<a class="button" target="_blank" href="https://ajdg.solutions/go/blindferret/">Learn more &raquo;</a></p>
				</div>
			</div>

			<div class="ajdg-postbox">				
				<h2 class="ajdg-postbox-title"><?php _e('Namecheap SSL Certificates', 'adrotate-pro'); ?></h2>
				<div id="namecheap" class="ajdg-postbox-content">
					<p>With the increasing risks on the internet it's smart to earn back the trust of your visitors by installing a SSL certificate on your website. Show them that you care about their privacy with a good and solid certificate. NameCheap has plenty of affordable options for all kinds of protection. I've been a customer of NameCheap for many years - So far no complaints.</p>
					<p><center><a rel="nofollow" href="https://ajdg.solutions/go/namecheap/"><img src="<?php echo plugins_url("/images/offers/namecheap-ssl.jpg", dirname(__FILE__)); ?>" width="440" alt="Namecheap.com"></a></center></p>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="clear"></div>
<p><?php echo adrotate_trademark(); ?></p>