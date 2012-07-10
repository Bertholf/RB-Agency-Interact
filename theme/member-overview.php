<?php
/*
Template Name: Member Details
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
$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_registerallow = (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_registerallow'];
$rb_agencyinteract_option_overviewpagedetails = (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_overviewpagedetails'];


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



echo "<div class=\"content_wrapper\">\n"; // Theme Wrapper 
	if ($profiletype == 0) {
		echo "<div class=\"PageTitle\"><h1>Talent Account Area</h1></div>\n";	 // Profile Name
	} else {
		echo "<div class=\"PageTitle\"><h1>Agent Account Area</h1></div>\n";	 // Profile Name
		
	}




	
	echo "<div id=\"container\" class=\"one-column rb-agency-interact rb-agency-interact-overview\">\n";
	echo "  <div id=\"content\">\n";
	
		// ****************************************************************************************** //
		// Check if User is Logged in or not
		if (is_user_logged_in()) { 
			
			/// Show registration steps
			//echo "<div id=\"profile-steps\">Profile Setup: Step 1 of 4</div>\n";
			
			echo "<div id=\"profile-manage\" class=\"profile-admin overview\">\n";
				
			/* Check if the user is regsitered *****************************************/ 
			$sql = "SELECT ProfileID FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
			$results = mysql_query($sql);
			$count = mysql_num_rows($results);
			if ($count > 0) {

			  // Menu
			  include("include-menu.php"); 	
			  echo " <div class=\"profile-overview-inner inner\">\n";
			  
			  while ($data = mysql_fetch_array($results)) {
				  
				echo "	 <div class=\"welcome\">\n";
			
				//echo "	 <h1>". __("Welcome back", rb_agencyinteract_TEXTDOMAIN) ." ". $current_user->first_name ."!</h1>";
				// Record Exists
			
				/* Show account information here *****************************************/
				 
				//echo "	 <div class=\"account\">\n"; // .account
				//echo "      <div><a href=\"account/\">Edit Your Account Details</a></div>\n";
				//echo "      <div><a href=\"manage/\">Manage Your Profile Information</a></div>\n";
				//echo "      <div><a href=\"media/\">Manage Photos and Media</a></div>\n";
				//echo "      <div><a href=\"subscription/\">Manage your Subscription</a></div>\n";
				//echo "	 </div>\n";
						
			  } // is there record?

			  echo "	 <h1>". __("Welcome back", rb_agencyinteract_TEXTDOMAIN) ." ". $current_user->first_name ."!</h1>";
			  echo "	 <div class=\"hr-half\"></div>\n";

			  // For Clients Only on overview/welcome page tab
			 if ($profiletype == 1) {
			 echo "<p>Currently, Aramarts Talent Agency does not require subscriptions on agents, clients, and studios. As an Agent, you are welcome to use Aramarts services, database, contact us about your projects, and request other services offered by Aramarts.</p> \n";
			 echo "<h3>Please check our other services that Aramarts offers:</h3>\n";
			 echo "<a href=\"/media-stock/\" class=\"button orange small\" style=\"margin-right:20px;\">Media stock</a>\n";
			 echo "<a href=\"/production/\" class=\"button orange small\" style=\"margin-right:20px;\">Production</a>\n";
			 echo "<a href=\"/facilities/\" class=\"button orange small\" style=\"margin-right:20px;\">Faciliation</a>\n";
			 echo "<a href=\"/consultation/\" class=\"button orange small\" style=\"margin-right:20px;\">Consulting</a>\n";
			 echo "<a href=\"/category/news/events/\" class=\"button orange small\" style=\"margin-right:20px;\">Events</a>\n";
			 echo "<a href=\"/category/news/jobs/\" class=\"button orange small\" style=\"margin-right:20px;\">Job Posts</a>\n";

			 }
			
			 if ($profiletype == 0) {
			  echo "	 <div id=\"subscription-customtext\">\n";
							$Page = get_page($rb_agencyinteract_option_overviewpagedetails);
			  echo		 apply_filters('the_content', $Page->post_content);
			  }
			  
			 
			 
			  echo "	 </div>";
			  echo " </div>\n"; // .profile-manage-inner
			  
			// No Record Exists, register them
			} else {
					
			  include("include-menu.php"); 	
					echo "<div id=\"confirm-registration\">\n";
					
					echo "<h1>". __("Welcome", rb_agencyinteract_TEXTDOMAIN) ." ". $current_user->first_name ."!</h1>";

					if ($profiletype == 1) {
						echo "<span style=\"font-size:14px;\">". __("We have you registered as", rb_agencyinteract_TEXTDOMAIN) ." <strong>". $profiletypetext ."</strong>. Begin your search below and view your marked favorites.</span>";
						
						
						//echo "<h2><a href=\"". $rb_agencyinteract_WPURL ."/profile-search/\">". __("Begin Your Search", rb_agencyinteract_TEXTDOMAIN) ."</a></h2>";


						// New Replacements for Agency Page
						echo "<div class=\"spacer20\"></div>\n"; 
						echo "<a href=\"/profile-category/\" class=\"button medium orange\" style=\"margin-right:40px;\">Begin Talent Search</a>\n";
						echo "<a href=\"/profile-favorite/\" class=\"button medium orange\" style=\"margin-right:40px;\">View Your Favorites</a>\n";
						echo "<a href=\"/community/help-and-faq/\" class=\"button medium orange\" style=\"margin-right:40px;\">Help and FAQ</a>\n";
						echo "<a href=\"/contact/\" class=\"button medium orange\">Contact Us</a>\n";
						echo "<div class=\"clear\"></div>\n";




						
						echo "  <div id=\"subscription-customtext\">\n";
							$Page = get_page($rb_agencyinteract_option_subscribepagedetails);
						//	echo apply_filters('the_content', $Page->post_content);
						echo " </div>";

					} else {
					  if ($rb_agencyinteract_option_registerallow == 1) {
						
						
						
						// Users CAN register themselves
						echo "". __("We have you registered as", rb_agencyinteract_TEXTDOMAIN) ." <strong>". $profiletypetext ."</strong>. Lets finish setting up your profile.<br><br>";
						
						//echo "<h2>". __("Setup Your Profile", rb_agencyinteract_TEXTDOMAIN) ."</h2>";
						
						// Register Profile
						include("include-profileregister.php"); 	
						
						
					  } else {
						// Cant register
						echo "<strong>". __("Self registration is not permitted.", rb_agencyinteract_TEXTDOMAIN) ."</strong>";
					  }
					}
					
			}
			
			echo "	 </div>\n";
			

				
			echo "</div>\n"; // #profile-manage
		} else {
			// Show Login Form
			include("include-login.php"); 	
		}
		
	echo "  </div><!-- #content -->\n";
	echo "</div><!-- #container -->\n";



echo "</div>\n"; //END .content_wrapper 




	
// Get Sidebar 
$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_profilemanage_sidebar = $rb_agencyinteract_options_arr['rb_agencyinteract_option_profilemanage_sidebar'];
	$LayoutType = "";
	if ($rb_agencyinteract_option_profilemanage_sidebar) {
		echo "	<div id=\"profile-sidebar\" class=\"manage\">\n";
			$LayoutType = "profile";
			get_sidebar(); 
		echo "	</div>\n";
	}
// Get Footer
get_footer();
?>
