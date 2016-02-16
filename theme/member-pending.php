<?php
/*
Template Name: 	Member Details
 * @name		Member Details
 * @type		PHP page
 * @desc		Member Details
*/

if (!headers_sent()) {
header("Cache-control: private"); //IE 6 Fix
}
global $wpdb;
global $current_user;
get_currentuserinfo();

// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
	function rb_agencyinteractive_override_title(){
		return "Profile Pending";
	}
$profile_gallery = $wpdb->get_row($wpdb->prepare("SELECT ProfileGallery FROM ".table_agency_profile." WHERE ProfileUserLinked = %d",$current_user->ID));

/* Display Page ******************************************/ 


// Call Header
echo $rb_header = RBAgency_Common::rb_header();

	echo "	<div id=\"primary\" class=\"rb-agency-interact rb-agency-interact-overview member-overview\">\n";
	echo "  	<div id=\"rbcontent\">\n";
        if(is_user_logged_in()){
         
            //so we know that he is no a first time user.. 
            update_user_meta( $current_user->ID, 'rb_agency_interact_clientold', date('m.d.y. h:i:s'));

			echo '<p class="rbalert success">';
			
			
			if(isset($_GET['e'])){
				echo 'Account updated! ';
			}else{
				echo sprintf(__("Thank you for joining %s! "), get_option('blogname'));
			}
			

			$rbagency_use_s2member_option = get_option('rbagency_use_s2member');
			if($rbagency_use_s2member_option == true){

				$rbagency_message_after_steps = get_option('rbagency_message_after_steps');
				if(!empty($rbagency_message_after_steps)){
					echo nl2br($rbagency_message_after_steps);
				}else{

					echo "<h2>Registration almost complete.</h2><br>";
					echo "<h3>To complete registration, please click button below to pay membership via paypal.</h3><br><br>";

				}
				/**
				$paypal_code = get_option('rbagency_paypal_button_code');
				$change = array(
					site_url()."/?s2member_paypal_return=1"
				);

				$change2 = array(
					'<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0; ?>', 
					'<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0; ?>',
					'<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1; ?>',
					'<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1; ?>');

				$return_url = site_url()."/?s2member_paypal_return=1&s2member_paypal_return_success=".site_url()."/registration-success";

				

				$pcode = str_replace($change2, '', $paypal_code);
				$pcode_final = str_replace($change,$return_url,$pcode);

				echo $pcode_final;
				**/

			}else{
				echo 'Your account is pending for approval. We will send you an email once your account is approved.<br/>';
			
				echo "<a href=\"".get_bloginfo("url")."/profile/".$profile_gallery->ProfileGallery."\">View My Profile</a>";
				echo "<a href=\"".get_bloginfo("url")."/profile-member/account\">Manage Account</a>";
					
				echo "</p> </div>\n"; // .welcome
			}
			

		} else {

			// Show Login Form
			include("include-login.php");
		}

	echo "  </div><!-- #rbcontent -->\n";
	echo "</div><!-- #primary -->\n";

// Call Footer
echo $rb_footer = RBAgency_Common::rb_footer();
?>			