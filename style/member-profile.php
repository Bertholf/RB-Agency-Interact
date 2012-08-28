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
	$ProfileStatEthnicity		=$_POST['ProfileStatEthnicity'];
	$ProfileStatSkinColor		=$_POST['ProfileStatSkinColor'];
	$ProfileStatEyeColor		=$_POST['ProfileStatEyeColor'];
	$ProfileStatHairColor		=$_POST['ProfileStatHairColor'];
	$ProfileStatHeight			=$_POST['ProfileStatHeight'];
	$ProfileStatWeight			=$_POST['ProfileStatWeight'];
	$ProfileStatBust	        =$_POST['ProfileStatBust'];
	$ProfileStatWaist	   		=$_POST['ProfileStatWaist'];
	$ProfileStatHip	       	 	=$_POST['ProfileStatHip'];
	$ProfileStatShoe		    =$_POST['ProfileStatShoe'];
	$ProfileStatDress			=$_POST['ProfileStatDress'];
	$ProfileExperience			=$_POST['ProfileExperience'];
	$ProfileDateViewLast		=$_POST['ProfileDateViewLast'];
	$ProfileType				=$_POST['ProfileType'];
	  if (is_array($ProfileType)) { 
		$ProfileType = implode(",", $ProfileType);
	  } 	

	// Get Primary Image
	$ProfileMediaPrimaryID		=$_POST['ProfileMediaPrimary'];

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
			ProfileStatEthnicity='" . $wpdb->escape($ProfileStatEthnicity) . "',
			ProfileStatSkinColor='" . $wpdb->escape($ProfileStatSkinColor) . "',
			ProfileStatEyeColor='" . $wpdb->escape($ProfileStatEyeColor) . "',
			ProfileStatHairColor='" . $wpdb->escape($ProfileStatHairColor) . "',
			ProfileStatHeight='" . $wpdb->escape($ProfileStatHeight) . "',
			ProfileStatWeight='" . $wpdb->escape($ProfileStatWeight) . "',
			ProfileStatBust='" . $wpdb->escape($ProfileStatBust) . "',
			ProfileStatWaist='" . $wpdb->escape($ProfileStatWaist) . "',
			ProfileStatHip='" . $wpdb->escape($ProfileStatHip) . "',
			ProfileStatShoe='" . $wpdb->escape($ProfileStatShoe) . "',
			ProfileStatDress='" . $wpdb->escape($ProfileStatDress) . "',
			ProfileUnion='" . $wpdb->escape($ProfileUnion) . "',
			ProfileExperience='" . $wpdb->escape($ProfileExperience) . "',
			ProfileDateUpdated=now(),
			ProfileType='" . $wpdb->escape($ProfileType) . "'
			WHERE ProfileID=$ProfileID";
		    $results = $wpdb->query($update);

			// Remove Old Custom Field Values
			$delete1 = "DELETE FROM " . table_agency_customfield_mux . " WHERE ProfileID = \"". $ProfileID ."\"";
			$results1 = $wpdb->query($delete1);
			
			// Add New Custom Field Values
			foreach($_POST as $key => $value) {
				if ((substr($key, 0, 15) == "ProfileCustomID") && (isset($value) && !empty($value))) {
					$ProfileCustomID = substr($key, 15);
					
					$insert1 = "INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomValue)" . "VALUES ('" . $ProfileID . "','" . $ProfileCustomID . "','" . $value . "')";
					$results1 = $wpdb->query($insert1);
				}
			}
			
			$alerts = "<div id=\"message\" class=\"updated\"><p>". __("Profile updated successfully", rb_agencyinteract_TEXTDOMAIN) ."!</a></p></div>";
		} else {
			$alerts = "<div id=\"message\" class=\"error\"><p>". __("Error updating record, please ensure you have filled out all required fields.", rb_agencyinteract_TEXTDOMAIN) ."</p></div>"; 
		}
		
		wp_redirect( $rb_agencyinteract_WPURL ."/profile-member/media/" );
		exit;
	break;
	}
}



/* Display Page ******************************************/ 
get_header();
	
	echo "<div id=\"container\" class=\"one-column rb-agency-interact rb-agency-interact-profile\">\n";
	echo "  <div id=\"content\">\n";
	
		// ****************************************************************************************** //
		// Check if User is Logged in or not
		if (is_user_logged_in()) { 
			
			/// Show registration steps
			echo "<div id=\"profile-steps\">Profile Setup: Step 2 of 4</div>\n";
			
			echo "<div id=\"profile-manage\" class=\"overview\">\n";
			
			// Menu
			include("include-menu.php"); 	
			echo " <div class=\"profile-manage-inner inner\">\n";

			// Show Errors & Alerts
			echo $alerts;
			
			
			/* Check if the user is regsitered *****************************************/ 
			// Verify Record
			$sql = "SELECT ProfileID FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
			$results = mysql_query($sql);
			$count = mysql_num_rows($results);
			if ($count > 0) {
			  while ($data = mysql_fetch_array($results)) {
			
				// Manage Profile
				include("include-profilemanage.php"); 	
						
			  } // is there record?
			} else {
				
				// No Record Exists, register them
				echo "". __("Records show you are not currently linked to a model or agency profile.  Lets setup your profile now!", rb_agencyinteract_TEXTDOMAIN) ."";
				
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
	
get_footer();
?>
