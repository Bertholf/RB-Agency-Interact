<?php
/*
Template Name: Member Details
 * @name		Member Details
 * @type		PHP page
 * @desc		Member Details
*/
ini_set("post_max_size", "30M");
ini_set("upload_max_filesize", "30M");
ini_set("memory_limit", "20000M"); 
if (!headers_sent()) {
header("Cache-control: private"); //IE 6 Fix
}
global $wpdb;

/* Get User Info ******************************************/ 
global $current_user;
get_currentuserinfo();

$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_agencyimagemaxheight 	= $rb_agency_options_arr['rb_agency_option_agencyimagemaxheight'];
		if (empty($rb_agency_option_agencyimagemaxheight) || $rb_agency_option_agencyimagemaxheight < 500) {$rb_agency_option_agencyimagemaxheight = 800; }
	$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
	$rb_agency_option_locationtimezone 		= (int)$rb_agency_options_arr['rb_agency_option_locationtimezone'];
	
$rb_agency_option_inactive_profile_on_update = (int)$rb_agency_options_arr['rb_agency_option_inactive_profile_on_update'];

// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
	function rb_agencyinteractive_override_title(){
		return __("Manage Media",RBAGENCY_interact_TEXTDOMAIN);
	}

// Form Post
if (isset($_POST['action'])) {

	$ProfileID					= isset($_POST['ProfileID'])?$_POST['ProfileID']:"";
	$ProfileUserLinked			= isset($_POST['ProfileUserLinked']) ?$_POST['ProfileUserLinked']:"";
	$ProfileGallery				= isset($_POST['ProfileGallery']) ?$_POST['ProfileGallery']:"";

   // Get Primary Image
	$ProfileMediaPrimaryID		= isset($_POST['ProfileMediaPrimary']) ?$_POST['ProfileMediaPrimary']:"";

	// Error checking
	$error = "";
	$have_error = false;

	// Get Post State
	$action = $_POST['action'];
	switch($action) {

	// *************************************************************************************************** //
	// Edit Record
	case 'editRecord':
		
	break;
	}
}

global $current_user;
$check_type = get_user_meta($current_user->ID, 'rb_agency_interact_clientdata', true);
/* 

echo 'xxxx';
print_r($current_user);
exit; */

/* Display Page ******************************************/ 

// Call Header
echo $rb_header = RBAgency_Common::rb_header();

// Check Sidebar
$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
$rb_agencyinteract_option_profilemanage_sidebar = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar']:"";


	echo "<div rb-agency-interact rb-agency-interact-media\"><!-- member-media.php --!>\n";
	echo "  <div id=\"rbcontent\">\n";

		// ****************************************************************************************** //
		// Check if User is Logged in or not
		if (is_user_logged_in()) {
			$rb_agency_new_registeredUser = get_user_meta($current_user->ID,'rb_agency_new_registeredUser');
			if(!empty($rb_agency_new_registeredUser)){
				/// Show registration steps
				echo "<div id=\"profile-steps\">". __("Profile Setup: Step 3 of 3", RBAGENCY_interact_TEXTDOMAIN) ."</div>\n";
			}
			echo "<div id=\"profile-manage\" class=\"profile-media\">\n";

			// Menu
			include("include-menu.php"); 
			echo " <div class=\"manage-accounts manage-content\">\n";

			if(isset($_GET['st']) && $_GET['st'] == 'updated'){
				echo "<div id=\"message\" class=\"updated\"><p>". __("Profile updated successfully", RBAGENCY_interact_TEXTDOMAIN) ."!</a></p></div>";
			}

			/* Check if the user is regsitered *****************************************/ 
			// Verify Record
			$sql = "SELECT ProfileID FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
			$results = $wpdb->get_results($sql,ARRAY_A);
			$count = $wpdb->num_rows;
			if ($count > 0) {
					foreach($results as $data) {

				// Manage Profile
				include("include-profileaccounts.php"); 

					}// is there record?
			} else {

				// No Record Exists, register them
				echo "<p>".__("Records show you are not currently linked to a model or agency profile. ", RBAGENCY_interact_TEXTDOMAIN)."</p>";

			}
			echo " </div>\n"; // .profile-manage-inner
			echo "</div>\n"; // #profile-manage
		} else {

			// Show Login Form
			include("include-login.php"); 
		}

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
