<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php 
	$action =	""; $active	= 'class="selected"';
	if(isset($_GET['tab']))
		$action	= esc_attr($_GET['tab']);
	?>

<div id="container"  class="pieregister-admin">
    <div class="right_section">
        <div class="go-pro">
            <h2 class="headingwidth"><?php _e("Extentions",'piereg') ?></h2>
            <p><?php _e("Upgrade to PRO plan and unlock ALL features and addons for Free. Pie Registration offers perpetual licensing - purchase once and use for a lifetime, no hassle or recurring periodic payments","piereg")?>.</p>
            <a href="https://pieregister.com/plan-and-pricing/"><?php _e("View Pricing","piereg") ?></a>
        </div>
        <ul class="go-pro-tabs">
            <li <?php echo ($action != "addons") ? $active :""; ?>><a href="admin.php?page=pie-pro-features"><?php _e("Features","piereg") ?></a></li>
            <li <?php echo ($action == "addons") ? $active :""; ?>><a href="admin.php?page=pie-pro-features&tab=addons"><?php _e("Addons","piereg") ?></a></li>
        </ul>
        <div class="pane">
        	<?php if( $action == 'addons' ) { ?> 
            	<div id="tab2" class="tab-content">
                <div class="addons-container-section">
                    <div class="addon-row">
                        <div class="addon-column margin-right">
                            <div class="addon-container">
                                <img class="addon-img" src="https://pieregister.com/wp-content/uploads/2018/11/6.jpg" alt="Authorize.net Payment Addon">
                                <div class="">
                                    <div class="addon-content-container">
                                        <h3>Authorize.net Payment Addon</h3>
                                        <p>Use Authorize.net addon to process membership payments using Pie Register.</p>
                                        <a class="get-addon" href="https://store.genetech.co/cart/?add-to-cart=878">Get this addon</a>
                                        <a class="read-more" href="https://pieregister.com/addons/authorize-net-payment-addon/"> Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="addon-column margin-right">
                            <div class="addon-container">
                                <img class="addon-img" src="https://pieregister.com/wp-content/uploads/2018/11/5.jpg" alt="Stripe Payment Addon">
                                <div class="">
                                    <div class="addon-content-container">
                                        <h3>Stripe Payment Addon</h3>
                                        <p>Use Stripe addon to process membership payments using Pie Register.</p>
                                        <a class="get-addon" href="https://store.genetech.co/cart/?add-to-cart=835">Get this addon</a>
                                        <a class="read-more" href="https://pieregister.com/addons/stripe-payment-addon/"> Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="addon-column margin-right">
                            <div class="addon-container">
                                <img class="addon-img" src="https://pieregister.com/wp-content/uploads/2018/11/3.jpg" alt="Two-step Authentication Addon">
                                <div class="">
                                    <div class="addon-content-container">
                                        <h3>Two-step Authentication Addon</h3>
                                        <p>Add an additional security layer by having users verify registration via SMS (TWILIO).</p>
                                        <a class="get-addon" href="https://store.genetech.co/cart/?add-to-cart=200">Get this addon</a>
                                        <a class="read-more" href="https://pieregister.com/addons/two-step-authentication-addon/"> Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="addon-column margin-right">
                            <div class="addon-container">
                                <img class="addon-img" src="https://pieregister.com/wp-content/uploads/2018/11/4.jpg" alt="MailChimp Addon">
                                <div class="">
                                    <div class="addon-content-container">
                                        <h3>MailChimp Addon</h3>
                                        <p>Use Pie Register to export your site users into MailChimp lists to send communication, sales and marketing emails.</p>
                                        <a class="get-addon" href="https://store.genetech.co/cart/?add-to-cart=197">Get this addon</a>
                                        <a class="read-more" href="https://pieregister.com/addons/mailchimp-addon/"> Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="addon-column margin-right">
                            <div class="addon-container">
                                <img class="addon-img" src="https://pieregister.com/wp-content/uploads/2018/11/1.jpg" alt="Social Login Addon">
                                <div class="">
                                    <div class="addon-content-container">
                                        <h3>Social Login Addon</h3>
                                        <p>Let your site or blog users to login via their Facebook, Twitter, Google, LinkedIn, Yahoo and WordPress accounts.</p>
                                        <a class="get-addon" href="https://store.genetech.co/cart/?add-to-cart=199">Get this addon</a>
                                        <a class="read-more" href="https://pieregister.com/addons/social-login-addon/"> Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="addon-column margin-right">
                            <div class="addon-container">
                                <img class="addon-img" src="https://pieregister.com/wp-content/uploads/2018/11/2.jpg" alt="Profile Search Addon">
                                <div class="">
                                    <div class="addon-content-container">
                                        <h3>Profile Search Addon</h3>
                                        <p>With the Profile Search tool, admin can provide users the feature to search or filter to display user data.</p>
                                        <a class="get-addon" href="https://store.genetech.co/cart/?add-to-cart=198">Get this addon</a>
                                        <a class="read-more" href="https://pieregister.com/addons/profile-search-addon/"> Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>			
			<?php  } else { ?> 
				<div id="tab1" class="tab-content">
                <div class="features-main-container">
                    <div class="et_pb_row et_pb_row_1 features-row">
                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/11/feature-1.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Multiple Registration Forms</h5>
                                    <p class="feature-content">Drag-drop fields to create registration forms so users can register to your blog or site.</p>
                                </div>
                            </a>
                        </div>
                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/11/feature-10.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Block Users</h5>
                                    <p class="feature-content">Block spammers by username, email and IP address.</p>
                                </div>
                            </a>
                        </div>
                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/11/feature-15.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Role Based Redirection</h5>
                                    <p class="feature-content">Rules for Role-Based Redirection to land users on different pages based on user role.</p>
                                </div>
                            </a>
                        </div>

                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/12/feature-18.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Auto Login</h5>
                                    <p class="feature-content">Auto login users after registration and let them complete verification process later on.</p>
                                </div>
                            </a>
                        </div>
                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/11/feature-16.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Built-in Pie Register Form Themes</h5>
                                    <p class="feature-content">Change the default forms UI and apply the built-in form themes according to website UI.</p>
                                </div>
                            </a>
                        </div>
                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/11/feature-9.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Customizable Login Security</h5>
                                    <p class="feature-content">Advanced security will lets you throw CAPTCHA based on the number of unsuccessful login attempts.</p>
                                </div>
                            </a>
                        </div>

                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/11/3.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Content Restriction</h5>
                                    <p class="feature-content">Restrict access to website pages or posts based on user role or current logged in status.</p>
                                </div>
                            </a>
                        </div>
                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/12/feature-19.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Timed Form Submission</h5>
                                    <p class="feature-content">Prevent bots for event timed submission.</p>
                                </div>
                            </a>
                        </div>
                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/11/feature-12.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Restrict Widgets</h5>
                                    <p class="feature-content">Set visibility of widgets for specific user roles and non-logged in users.</p>
                                </div>
                            </a>
                        </div>

                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/11/feature-6.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Import and Export</h5>
                                    <p class="feature-content">Want to quickly duplicate or move your existing WordPress user or configuration data?</p>
                                </div>
                            </a>
                        </div>
                        <div class="feature-single-container margin-right">
                            <div class="feature-icon-container" style="background-image: url(https://pieregister.com/wp-content/uploads/2018/11/feature-13.png)"></div>
                            <a class="" href="">
                                <div class="feature-content-container">
                                    <h5>Ticket Based Support</h5>
                                    <p class="feature-content">Pie Register provides a premium support directly from the development team.</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="et_pb_row et_pb_row_1 features-row-last">
                        <a class="features-last-pricing" href="https://pieregister.com/plan-and-pricing/"><?php _e("Upgrade Now","piereg") ?></a>
                        <a class="view-all-features" href="https://pieregister.com/features/"><?php _e("View all features","piereg") ?></a>
                    </div>

                </div>
            </div>			
			<?php } ?>
        </div>
    </div>
</div>