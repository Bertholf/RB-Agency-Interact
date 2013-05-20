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

// Were they users or agents?
$profiletype = (int)get_user_meta($current_user->id, "rb_agency_interact_profiletype", true);

if ($profiletype == 1) { $profiletypetext = __("Agent/Producer", rb_agencyinteract_TEXTDOMAIN); } else { $profiletypetext = __("Model/Talent", rb_agencyinteract_TEXTDOMAIN); }

// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
	function rb_agencyinteractive_override_title(){
		return "Manage Profile";
	}   

// Form Post
if (isset($_POST['action'])) { 

	$ProfileID					=$_POST['ProfileID'];
	$ProfileUserLinked			=$_POST['ProfileUserLinked'];
	$ProfileLanguage			=$_POST['ProfileLanguage'];
	$ProfileStatHeight			=$_POST['ProfileStatHeight'];
	$ProfileStatWeight			=$_POST['ProfileStatWeight'];
	$ProfileDateViewLast		=$_POST['ProfileDateViewLast'];
	$ProfileType				=$_POST['ProfileType'];
	  if (is_array($ProfileType)) { 
		$ProfileType = implode(",", $ProfileType);
	  } 	

    // Custom Fields
	foreach($_POST as $key => $value) {
		if ((substr($key, 0, 15) == "ProfileCustomID") && (isset($value) && !empty($value))) {
				$ProfileCustomID = substr($key, 15);
			// Remove Old Custom Field Values
			$delete1 = "DELETE FROM " . table_agency_customfield_mux . " WHERE ProfileCustomID = ". $ProfileCustomID ." AND ProfileID = ".$ProfileID."";
			$results1 = mysql_query($delete1) or die(mysql_error());	
			if(is_array($value)){
				$value =  implode(",",$value);
			}
			if(!empty($value)){
				$insert1 = "INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomValue)" . "VALUES ('" . $ProfileID . "','" . $ProfileCustomID . "','" . $value . "')";
				$results1 = $wpdb->query($insert1);
			}
		}
	}
	// Get Primary Image
	$ProfileMediaPrimaryID = $_POST['ProfileMediaPrimary'];

	// Error checking
	$error = "";
	$have_error = false;

	// Get Post State
	$action = $_POST['action'];
	switch($action) {

	// *************************************************************************************************** //
	// Edit Record
	case 'editRecord':
		if (!$have_error){
			// Update Record
			$update = "UPDATE " . table_agency_profile . " SET 
			ProfileDateUpdated=now(),
			ProfileType='" . $wpdb->escape($ProfileType) . "'
			WHERE ProfileID=$ProfileID";
			$results = $wpdb->query($update);
			$alerts = "<div id=\"message\" class=\"updated\"><p>". __("Profile updated successfully", rb_agencyinteract_TEXTDOMAIN) ."!</a></p></div>";
		} else {
			$alerts = "<div id=\"message\" class=\"error\"><p>". __("Error updating record, please ensure you have filled out all required fields.", rb_agencyinteract_TEXTDOMAIN) ."</p></div>"; 
		}
		wp_redirect( $rb_agencyinteract_WPURL ."/profile-member/media/" );
		exit;
	break;

	case 'addRecord':
		if (!$have_error){
		
		}
	}
}

/* Display Page ******************************************/ 
get_header();

// Check Sidebar
$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
$rb_agencyinteract_option_profilemanage_sidebar = $rb_agencyinteract_options_arr['rb_agencyinteract_option_profilemanage_sidebar'];
$content_class = "";
if (is_user_logged_in()) {
	$content_class = "eight";
} else {
	$content_class = "twelve";
}

	echo "<div id=\"container\" class=\"".$content_class." column rb-agency-interact rb-agency-interact-profile\">\n";
	echo "  <div id=\"content\">\n";

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
				echo "<div id=\"profile-steps\">Profile Setup: Step 2 of 2</div>\n";
			} else {
				echo "<div id=\"profile-steps\">Profile Setup: Step 2 of 3</div>\n";
			}

			echo "<div id=\"profile-manage\" class=\"overview\">\n";

			// Menu
			include("include-menu.php"); 	
			echo " <div class=\"manage-profile manage-content\">\n";

			// Show Errors & Alerts
			echo $alerts;

			/* Check if the user is regsitered *****************************************/ 
			// Verify Record
			$sql = "SELECT ProfileID FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
			$results = mysql_query($sql);
			$count = mysql_num_rows($results);
			if ($count > 0) {
			  	$data = mysql_fetch_array($results);
				// Manage Profile
				include("include-profilemanage.php"); 	
			} else {

				// No Record Exists, register them
				echo "<p>". __("Records show you are not currently linked to a model or agency profile.  Lets setup your profile now!", rb_agencyinteract_TEXTDOMAIN) ."</p>";

				// Register Profile
				include("include-profileregister.php");
			}
			echo " </div>\n"; // .profile-manage-inner
			echo "</div>\n"; // #profile-manage
		} else {
			
			// Show Login Form
			include("include-login.php"); 	
		}
	echo "    <div style=\"clear: both; \"></div>\n";
	echo "  </div><!-- #content -->\n";
	echo "</div><!-- #container -->\n";

	if (is_user_logged_in()) {

		// Get Sidebar 
		$LayoutType = "";
		if ($rb_agencyinteract_option_profilemanage_sidebar) {
			$LayoutType = "profile";
			get_sidebar();
		}
	}

// Get Footer
get_footer();
?>
