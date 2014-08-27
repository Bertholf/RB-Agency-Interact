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

/* Display Page ******************************************/ 


// Call Header
echo $rb_header = RBAgency_Common::rb_header();
	
	echo "	<div id=\"primary\" class=\"col_12 column rb-agency-interact rb-agency-interact-overview\">\n";
	echo "  	<div id=\"content\">\n";
         if(is_user_logged_in()){
			echo "	<div id=\"profile-manage\" class=\"profile-overview\">\n";

					echo " <div class=\"manage-overview manage-content\">\n";
					echo sprintf(__("<h3>Thanks for joining %s!</h3>"), get_option('blogname'));
					echo "<br/>";
					echo "Your account is pending for approval. We will send you a confirmation once account is approved.";
					echo "<br/>";
					echo "<a href=\"".get_bloginfo("url")."/profile-member/\">Edit your profile</a>";
					echo " </div>\n";

			echo " </div>\n"; // .welcome
			  
		
		} else {

			// Show Login Form
			include("include-login.php");
		}
		
	echo "  </div><!-- #content -->\n";
	echo "</div><!-- #primary -->\n";

// Call Footer
echo $rb_footer = RBAgency_Common::rb_footer();
?>			