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

$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_agencyimagemaxheight 	= $rb_agency_options_arr['rb_agency_option_agencyimagemaxheight'];
		if (empty($rb_agency_option_agencyimagemaxheight) || $rb_agency_option_agencyimagemaxheight < 500) { $rb_agency_option_agencyimagemaxheight = 800; }
	$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
	$rb_agency_option_locationtimezone 		= (int)$rb_agency_options_arr['rb_agency_option_locationtimezone'];

// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
	function rb_agencyinteractive_override_title(){
		return "Manage Media";
	}   

// Form Post
if (isset($_POST['action'])) {

	$ProfileID					=$_POST['ProfileID'];
	$ProfileUserLinked			=$_POST['ProfileUserLinked'];
	$ProfileGallery				=$_POST['ProfileGallery'];

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
			
			// If the directory Doesnt Exist, make it.
			if (!is_dir(rb_agency_UPLOADPATH . $ProfileGallery)) {
				mkdir(rb_agency_UPLOADPATH . $ProfileGallery, 0755);
			}

			// Upload Image & Add to Database
			$i = 1;
			while ($i <= 10) {
				if($_FILES['profileMedia'. $i]['tmp_name'] != ""){
					$uploadMediaType = $_POST['profileMedia'. $i .'Type'];
					if($uploadMediaType == "Image") {
						if(!$_FILES['profileMedia'. $i]['type'] == "image/jpeg" || !$_FILES['profileMedia'. $i]['type'] == "image/gif" || !$_FILES['profileMedia'. $i]['type'] == "image/png"){
						$error .= "<b><i>Please upload an image file only</i></b><br />";
						$have_error = true;
						}
					} elseif($uploadMediaType == "VoiceDemo") {
						if($_FILES['profileMedia'. $i]['type'] != "audio/mp3"){
						$error .= "<b><i>Please upload a mp3 file only</i></b><br />";
						$have_error = true;
						}
					} else {
						if(!$_FILES['profileMedia'. $i]['type'] == "application/pdf" || !$_FILES['profileMedia'. $i]['type'] == "image/jpeg" || !$_FILES['profileMedia'. $i]['type'] == "image/gif" || !$_FILES['profileMedia'. $i]['type'] == "image/png"){
						$error .= "<b><i>Please upload a PDF or image file only</i></b><br />";
						$have_error = true;
						}
					}
					if ($have_error != true) {
					// Upload if it doesnt exist already
					 $safeProfileMediaFilename = rb_agency_safenames($_FILES['profileMedia'. $i]['name']);
					 $results = mysql_query("SELECT * FROM " . table_agency_profile_media . " WHERE ProfileID='". $ProfileID ."' AND ProfileMediaURL = '". $safeProfileMediaFilename ."'");
					 $count = mysql_num_rows($results);

					 if ($count < 1) {
						if($uploadMediaType == "Image") { 
						    if($_FILES['profileMedia'. $i]['type'] == "image/jpeg" || $_FILES['profileMedia'. $i]['type'] == "image/gif" || $_FILES['profileMedia'. $i]['type'] == "image/png"){
						
									$image = new rb_agency_image();
									$image->load($_FILES['profileMedia'. $i]['tmp_name']);
			
									if ($image->getHeight() > $rb_agency_option_agencyimagemaxheight) {
										$image->resizeToHeight($rb_agency_option_agencyimagemaxheight);
									}
									$image->save(rb_agency_UPLOADPATH . $ProfileGallery ."/". $safeProfileMediaFilename);
									// Add to database
								$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $uploadMediaType ."','". $safeProfileMediaFilename ."','". $safeProfileMediaFilename ."')");
						    }else{
								$error .= "<b><i>Please upload an image file only</i></b><br />";
						        $have_error = true;
							}
						}
						else if($uploadMediaType =="VoiceDemo"){
							// Add to database
							
							 $results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $uploadMediaType ."','". $safeProfileMediaFilename ."','". $safeProfileMediaFilename ."')");
			                  move_uploaded_file($_FILES['profileMedia'. $i]['tmp_name'], rb_agency_UPLOADPATH . $ProfileGallery ."/".$_FILES['profileMedia'. $i]['name']);
						}
						
					 }
					}
				}
				$i++;
			}			


			// Upload Videos to Database
			if (isset($_POST['profileMediaV1']) && !empty($_POST['profileMediaV1'])) {
				$profileMediaType = $_POST['profileMediaV1Type'];
				$profileMediaURL = rb_agency_get_VideoFromObject($_POST['profileMediaV1']);
				$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $profileMediaType ."','". $profileMediaType ."','". $profileMediaURL ."')");
			}
			if (isset($_POST['profileMediaV2']) && !empty($_POST['profileMediaV2'])) {
				$profileMediaType	=$_POST['profileMediaV2Type'];
				$profileMediaURL = rb_agency_get_VideoFromObject($_POST['profileMediaV2']);
				$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $profileMediaType ."','". $profileMediaType ."','". $profileMediaURL ."')");
			}
			if (isset($_POST['profileMediaV3']) && !empty($_POST['profileMediaV3'])) {
				$profileMediaType	=$_POST['profileMediaV3Type'];
				$profileMediaURL = rb_agency_get_VideoFromObject($_POST['profileMediaV3']);
				$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $profileMediaType ."','". $profileMediaType ."','". $profileMediaURL ."')");
			}

			/* --------------------------------------------------------- CLEAN THIS UP -------------- */
			// Do we have a custom image yet? Lets just set the first one as primary.
			 $results = mysql_query("SELECT * FROM " . table_agency_profile_media . " WHERE ProfileID='". $ProfileID ."' AND ProfileMediaType = 'Image' AND ProfileMediaPrimary='1'");
			 $count = mysql_num_rows($results);
			 if ($count < 1) {
			 	$resultsNeedOne = mysql_query("SELECT * FROM " . table_agency_profile_media . " WHERE ProfileID='". $ProfileID ."' AND ProfileMediaType = 'Image' LIMIT 0, 1");
				while ($dataNeedOne = mysql_fetch_array($resultsNeedOne)) {
					$resultsFoundOne = $wpdb->query("UPDATE " . table_agency_profile_media . " SET ProfileMediaPrimary='1' WHERE ProfileID='". $ProfileID ."' AND ProfileMediaID = '". $dataNeedOne['ProfileMediaID'] . "'");
					break;
				}
			 }
	  		 if ($ProfileMediaPrimaryID > 0) {
			  // Update Primary Image
			  $results = $wpdb->query("UPDATE " . table_agency_profile_media . " SET ProfileMediaPrimary='0' WHERE ProfileID=$ProfileID");
			  $results = $wpdb->query("UPDATE " . table_agency_profile_media . " SET ProfileMediaPrimary='1' WHERE ProfileID=$ProfileID AND ProfileMediaID=$ProfileMediaPrimaryID");
			 }
			/* --------------------------------------------------------- CLEAN THIS UP -------------- */
			
			$alerts = "<div id=\"message\" class=\"updated\"><p>". __("Profile updated successfully", rb_agencyinteract_TEXTDOMAIN) ."!</a></p></div>";
		} else {
			$alerts = "<div id=\"message\" class=\"error\"><p>". __("Error updating record, please ensure you have filled out all required fields.", rb_agencyinteract_TEXTDOMAIN) ."</p></div>"; 
		}
		
		if ($have_error != true) {
			wp_redirect( $rb_agencyinteract_WPURL ."/profile-member/media/" );
		
		exit;
	    }
	break;
	}
}



/* Display Page ******************************************/ 
get_header();

echo "<div class=\"content_wrapper\">\n"; // Theme Wrapper 
	echo "<div class=\"PageTitle\"><h1>Edit Your Media Files</h1></div>\n";	 // Profile Name


	
	echo "<div id=\"container\" class=\"one-column\">\n";
	echo "  <div id=\"content\">\n";
	
		// ****************************************************************************************** //
		// Check if User is Logged in or not
		if (is_user_logged_in()) { 
			
			/// Show registration steps
			//echo "<div id=\"profile-steps\">Profile Setup: Step 3 of 4</div>\n";
			
			echo "<div id=\"profile-manage\" class=\"overview\">\n";
			
			// Menu
			include("include-menu.php"); 	
			echo " <div class=\"profile-manage-inner inner\">\n";
			
			
			/* Check if the user is regsitered *****************************************/ 
			// Verify Record
			$sql2 = "SELECT ProfileID FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
			$results2 = mysql_query($sql2);
			$count2 = mysql_num_rows($results2);
			if ($count2 > 0) {
			  while ($data2 = mysql_fetch_array($results2)) {
			
				// Manage Profile
				include("include-profilemedia.php"); 	
						
			  } // is there record?
			} else {
				
				// No Record Exists, register them
				echo "Records show you are not currently linked to a model or agency profile. ";
				
			}
			echo " </div>\n"; // .profile-manage-inner
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
