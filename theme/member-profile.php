<?php 
/*
Template Name: Member Details
 * @name		Member Details
 * @type		PHP page
 * @desc		Member Details
*/
if (!headers_sent()) {
header("Cache-control: private"); //IE 6 Fix
}
global $wpdb;

/* Get User Info ******************************************/ 
global $current_user;
get_currentuserinfo();

// Were they users or agents?
$profiletype = (int)get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);

if(get_user_meta($current_user->ID, 'rb_agency_interact_clientdata', true)) { $profiletypetext = __("Agent/Producer", rb_agency_interact_TEXTDOMAIN); } else { $profiletypetext = __("Model/Talent", rb_agency_interact_TEXTDOMAIN); }

// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
	function rb_agencyinteractive_override_title(){
		return "Manage Profile";
	}   

// declare alert
$alerts = "";

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
				$profilecustomfield_date = explode("_",$key);
				if(count($profilecustomfield_date) == 2){ // customfield date
					$ProfileCustomID = substr($profilecustomfield_date[0], 15);
				}else{	
					$ProfileCustomID = substr($key, 15);
				}
			// Remove Old Custom Field Values
			$delete1 = "DELETE FROM " . table_agency_customfield_mux . " WHERE ProfileCustomID = ". $ProfileCustomID ." AND ProfileID = ".$ProfileID."";
			$results1 = mysql_query($delete1) or die(mysql_error());	
			if(is_array($value)){
				$value =  implode(",",$value);
			}
			if(!empty($value)){
							
				if(count($profilecustomfield_date) == 2){ // customfield date
					$value = date("y-m-d h:i:s",strtotime($value));
					$insert1 = "INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomDateValue)" . "VALUES ('" . $ProfileID . "','" . $ProfileCustomID . "','" . $value . "')";
				}else{
					$insert1 = "INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomValue)" . "VALUES ('" . $ProfileID . "','" . $ProfileCustomID . "','" . $value . "')";
				}
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
			$alerts = "<div id=\"message\" class=\"updated\"><p>". __("Profile updated successfully", rb_agency_interact_TEXTDOMAIN) ."!</a></p></div>";
		} else {
			$alerts = "<div id=\"message\" class=\"error\"><p>". __("Error updating record, please ensure you have filled out all required fields.", rb_agency_interact_TEXTDOMAIN) ."</p></div>"; 
		}
		wp_redirect( $rb_agency_interact_WPURL ."/profile-member/media/" );
		exit;
	break;

	case 'addRecord':
		if (!$have_error){
		
		}
	}
}

/* Display Page ******************************************/ 

// Call Header
echo $rb_header = RBAgency_Common::rb_header();

// Check Sidebar
$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
$rb_agencyinteract_option_profilemanage_sidebar = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar']) ?$rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar']:0;

if($rb_agencyinteract_option_profilemanage_sidebar){
	$column_class = primary_class();
} else {
	$column_class = fullwidth_class();
}

	echo "<div id=\"container\" class=\"".$column_class." column rb-agency-interact rb-agency-interact-profile\">\n";
	echo "  <div id=\"content\">\n";

		// ****************************************************************************************** //
		// Check if User is Logged in or not
		if (is_user_logged_in()) { 

            /*
			 * Set Media to not show to
			 * client/s, agents, producers,
			 */
			$ptype = (int)get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);
	                $ptype = retrieve_title($ptype);
			$restrict = array('client','clients','agents','agent','producer','producers');
			$rb_agency_new_registeredUser = get_user_meta($current_user->ID,'rb_agency_new_registeredUser');
			if(!empty($rb_agency_new_registeredUser)){
						
						if(in_array(strtolower($ptype),$restrict)){
							echo "<div id=\"profile-steps\">Profile Setup: Step 2 of 2</div>\n";
						} else {
							echo "<div id=\"profile-steps\">Profile Setup: Step 2 of 3</div>\n";
						}
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
				echo "<p>". __("Records show you are not currently linked to a model or agency profile.  Lets setup your profile now!", rb_agency_interact_TEXTDOMAIN) ."</p>";

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

// Call Footer
echo $rb_footer = RBAgency_Common::rb_footer();
?>
