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

if(get_user_meta($current_user->ID, 'rb_agency_interact_clientdata', true)) {$profiletypetext = __("Agent/Producer", RBAGENCY_interact_TEXTDOMAIN); } else {$profiletypetext = __("Model/Talent", RBAGENCY_interact_TEXTDOMAIN); }

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
				} else {
					$ProfileCustomID = substr($key, 15);
				}
			// Remove Old Custom Field Values
			$delete1 = "DELETE FROM " . table_agency_customfield_mux . " WHERE ProfileCustomID = ". $ProfileCustomID ." AND ProfileID = ".$ProfileID."";
			$results1 = $wpdb->query($delete1); 
			if(is_array($value)){
				$value =  implode(",",$value);
			}
				if(count($profilecustomfield_date) == 2){ // customfield date
					$value = date("y-m-d h:i:s",strtotime($value));
					$insert1 = $wpdb->prepare("INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomDateValue)" . "VALUES (%d,%d,%s)",$ProfileID,$ProfileCustomID,$value);
				} else {
					$insert1 = $wpdb->prepare("INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomValue)" . "VALUES (%d,%d,%s)",$ProfileID,$ProfileCustomID,$value);
				}
				$results1 = $wpdb->query($insert1);

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
		
			$rb_agency_options_arr = get_option('rb_agency_options');
			$rb_agency_option_inactive_profile_on_update = (int)$rb_agency_options_arr['rb_agency_option_inactive_profile_on_update'];
			
			
			//nevermind if your admin
			$ProfileStatus = '';
			if($rb_agency_option_inactive_profile_on_update == 1){
				//nevermind if your admin
				if(is_user_logged_in() && current_user_can( 'edit_posts' )){
					$ProfileStatus = '';//stay active admin account
				}else{
					$ProfileStatus = " ProfileIsActive = 3, "; //inactive
					
				}
			}else{
				// get user current status so theres no changes would be happen,
				(int)$ProfileStatus_int = $wpdb->get_var("SELECT  ProfileIsActive FROM " . table_agency_profile . " WHERE ProfileID=$ProfileID");
				
				$ProfileStatus = " ProfileIsActive = $ProfileStatus_int, ";
			}
			
		
			rb_interact_sendadmin_pending_info($ProfileID);
			
			// Update Record
			$update = "UPDATE " . table_agency_profile . " SET 
			ProfileDateUpdated=now(), $ProfileStatus
			ProfileType='" . $wpdb->escape($ProfileType) . "'
			WHERE ProfileID=$ProfileID ";

			$results = $wpdb->query($update);
			$alerts = "<div id=\"message\" class=\"updated\"><p>". __("Profile updated successfully", RBAGENCY_interact_TEXTDOMAIN) ."!</a></p></div>";
		} else {
			$alerts = "<div id=\"message\" class=\"error\"><p>". __("Error updating record, please ensure you have filled out all required fields.", RBAGENCY_interact_TEXTDOMAIN) ."</p></div>"; 
		}
		
		//wp_new_user_notification_pending($current_user->ID , false);
		//exist user should be in pending page
		$old_exist_user = get_user_meta( $current_user->ID, 'rb_agency_interact_clientold', true);
        if(!empty($old_exist_user)){
            wp_redirect( $rb_agency_interact_WPURL ."/profile-member/pending/?e" );
            exit;
        }
		wp_redirect( $rb_agency_interact_WPURL ."/profile-member/media/" );
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

	echo "<div class=\"".$column_class." column rb-agency-interact member-profile\">\n";
	echo "  <div id=\"rbcontent\">\n";

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
							echo "<div id=\"profile-steps\">".__('Profile Setup: Step 2 of 2',RBAGENCY_interact_TEXTDOMAIN)."</div>\n";
						} else {
							echo "<div id=\"profile-steps\">".__('Profile Setup: Step 2 of 3',RBAGENCY_interact_TEXTDOMAIN)."</div>\n";
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
			$data = $wpdb->get_row($sql,ARRAY_A);
			$count =  $wpdb->num_rows;
			if ($count > 0) {
					// Manage Profile
				include("include-profilemanage.php");
			} else {

				// No Record Exists, register them
				echo "<p>". __("Records show you are not currently linked to a model or agency profile.  Lets setup your profile now!", RBAGENCY_interact_TEXTDOMAIN) ."</p>";

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
