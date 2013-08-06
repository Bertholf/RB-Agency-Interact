<?php
/*
Template Name: 	Member Details
 * @name		Member Details
 * @type		PHP page
 * @desc		Member Details
*/

session_start();
header("Cache-control: private"); //IE 6 Fix
global $wpdb;

/* Get User Info ******************************************/ 
global $current_user;
get_currentuserinfo();

// Get Settings
$rb_agency_options_arr 							= get_option('rb_agency_options');
$rb_agency_option_profilenaming 				= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
$rb_agencyinteract_options_arr 					= get_option('rb_agencyinteract_options');
$rb_agencyinteract_option_registerallow 		= (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_registerallow'];
$rb_agencyinteract_option_overviewpagedetails 	= (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_overviewpagedetails'];

// Check Sidebar
$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
$rb_agencyinteract_option_profilemanage_sidebar = $rb_agencyinteract_options_arr['rb_agencyinteract_option_profilemanage_sidebar'];
$rb_subscription = $rb_agency_options_arr['rb_agency_option_profilelist_subscription'];

// Were they users or agents?
$profiletype = (int)get_user_meta($current_user->id, "rb_agency_interact_profiletype", true);
if ($profiletype == 1) { $profiletypetext = __("Agent/Producer", rb_agencyinteract_TEXTDOMAIN); } else { $profiletypetext = __("Model/Talent", rb_agencyinteract_TEXTDOMAIN); }

// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
	function rb_agencyinteractive_override_title(){
		return "Member Overview";
	}

/* Display Page ******************************************/ 
get_header();
	
	echo "	<div id=\"primary\" class=\"col_12 column rb-agency-interact rb-agency-interact-overview\">\n";
	echo "  	<div id=\"content\">\n";

		// get profile Custom fields value
		$rb_agency_new_registeredUser = get_user_meta($current_user->id,'rb_agency_new_registeredUser',true);
	
		// ****************************************************************************************** //
		// Check if User is Logged in or not
		if (is_user_logged_in()) { 

                    			/*
			 * Set Media to not show to
			 * client/s, agents, producers,
			 */
                        $ptype = (int)get_user_meta($current_user->id, "rb_agency_interact_profiletype", true);
	                $ptype = retrieve_title($ptype);
			$restrict = array('client','clients','agents','agent','producer','producers');
			if(in_array(strtolower($ptype),$restrict)){
				echo "<div id=\"profile-steps\">Profile Setup: Step 1 of 2</div>\n";
			} else {
				echo "<div id=\"profile-steps\">Profile Setup: Step 1 of 3</div>\n";
			}
                        
			echo "	<div id=\"profile-manage\" class=\"profile-overview\">\n";
				
			/* Check if the user is regsitered *****************************************/ 
			$sql = "SELECT ProfileID FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
			$results = mysql_query($sql);
			$count = mysql_num_rows($results);
			if ($count > 0) {

			// Menu
			include("include-menu.php"); 	
			echo " <div class=\"manage-overview manage-content\">\n";
			  
			$data = mysql_fetch_array($results);  // is there record?
				  
				echo "	 <div class=\"manage-section welcome\">\n";			
				echo "	 <h1>". __("Welcome Back", rb_agencyinteract_TEXTDOMAIN) ." ". $current_user->first_name ."!</h1>";
				// Record Exists
			
				/* Show account information here *****************************************/
				 
				echo " <div class=\"section-content section-account\">\n"; // .account
				echo " 	<ul>\n";
				echo "      <li><a href=\"account/\">Edit Your Account Details</a></li>\n";
				echo "      <li><a href=\"manage/\">Manage Your Profile Information</a></li>\n";
				echo "      <li><a href=\"media/\">Manage Photos and Media</a></li>\n";
				if($rb_subscription){
				echo "      <li><a href=\"subscription/\">Manage your Subscription</a></li>\n";
				}
				echo "	</ul>\n";
				echo " </div>\n";
			  	echo " </div>\n"; // .welcome
			  	echo " </div>\n"; // .profile-manage-inner
			  
			// No Record Exists, register them
			} else {
					
				echo "<h1>". __("Welcome", rb_agencyinteract_TEXTDOMAIN) ." ". $current_user->first_name ."!</h1>";

				if ($profiletype == 1) {
					echo "". __("We have you registered as", rb_agencyinteract_TEXTDOMAIN) ." <strong>". $profiletypetext ."</strong>";
					echo "<h2><a href=\"". $rb_agencyinteract_WPURL ."/profile-search/\">". __("Begin Your Search", rb_agencyinteract_TEXTDOMAIN) ."</a></h2>";
					
					echo " <div id=\"subscription-customtext\">\n";
					$Page = get_page($rb_agencyinteract_option_subscribepagedetails);
					echo apply_filters('the_content', $Page->post_content);
					echo " </div>";

				} else {
				  	if ($rb_agencyinteract_option_registerallow == 1) {

						// Users CAN register themselves
						echo "". __("We have you registered as", rb_agencyinteract_TEXTDOMAIN) ." <strong>". $profiletypetext ."</strong>";
						echo "<h2>". __("Setup Your Profile", rb_agencyinteract_TEXTDOMAIN) ."</h2>";
					
						// Register Profile
						include("include-profileregister.php");						
				  	} else {
					
						// Cant register
						echo "<strong>". __("Self registration is not permitted.", rb_agencyinteract_TEXTDOMAIN) ."</strong>";
				  	}
				}					
			}			
			echo "</div><!-- #profile-manage -->\n";

		} else {

			// Show Login Form
			include("include-login.php"); 	
		}
		
	echo "  </div><!-- #content -->\n";
	echo "</div><!-- #primary -->\n";

// Get Footer
get_footer();
?>