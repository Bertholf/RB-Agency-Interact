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
			
			echo 'Your account is pending for approval. We will send you an email once your account is approved.<br/>';
			
			echo "<a href=\"".get_bloginfo("url")."/profile/".$profile_gallery->ProfileGallery."\">View My Profile</a>";
			echo "<a href=\"".get_bloginfo("url")."/profile-member/account\">Manage Account</a>";
					
			echo "</p> </div>\n"; // .welcome

		} else {

			// Show Login Form
			include("include-login.php");
		}

	echo "  </div><!-- #rbcontent -->\n";
	echo "</div><!-- #primary -->\n";

// Call Footer
echo $rb_footer = RBAgency_Common::rb_footer();
?>			