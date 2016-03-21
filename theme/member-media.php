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

$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_agencyimagemaxheight 	= $rb_agency_options_arr['rb_agency_option_agencyimagemaxheight'];
		if (empty($rb_agency_option_agencyimagemaxheight) || $rb_agency_option_agencyimagemaxheight < 500) {$rb_agency_option_agencyimagemaxheight = 800; }
	$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
	$rb_agency_option_locationtimezone 		= (int)$rb_agency_options_arr['rb_agency_option_locationtimezone'];
	
$rb_agency_option_inactive_profile_on_update = (int)$rb_agency_options_arr['rb_agency_option_inactive_profile_on_update'];

// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
	function rb_agencyinteractive_override_title(){
		return "Manage Media";
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
		if (!$have_error){

		
			/* $ProfileStatus = 0;
			if($rb_agency_option_inactive_profile_on_update == 1){
				//nevermind if your admin
				if(is_user_logged_in() && current_user_can( 'edit_posts' )){
					$ProfileStatus = 1;//stay active admin account
				}else{
					$ProfileStatus = 3; //inactive
				}
			}  */
			//nevermind if your admin
			$ProfileStatus = 0;
			if($rb_agency_option_inactive_profile_on_update == 1){
				//nevermind if your admin
				if(is_user_logged_in() && current_user_can( 'edit_posts' )){
					$ProfileStatus = 1;//stay active admin account
				}else{
					$ProfileStatus = 3; //inactive
					
				}
			}else{
				// get user current status so theres no changes would be happen,
				$ProfileStatus = $wpdb->get_var("SELECT  ProfileIsActive FROM " . table_agency_profile . " WHERE ProfileID=$ProfileID");
					
			}
			
			
			
        // fixed error of folder is not created 
		$ProfileGallery = rb_agency_createdir($ProfileGallery,false);// Check Directory - create directory if does not exist

		// Upload Image & Add to Database
			$i = 0;
			while ($i < 10) {
				if(isset($_FILES['profileMedia'. $i]['tmp_name']) && $_FILES['profileMedia'. $i]['tmp_name'] != ""){

					$UploadMedia[] = $_FILES['profileMedia'. $i]['name'];

					$uploadMediaType = $_POST['profileMedia'. $i .'Type'];
					if ($have_error != true) {
					// Upload if it doesnt exist already
						$path_parts = pathinfo($_FILES['profileMedia'. $i]['name']);
						$safeProfileMediaFilename =  RBAgency_Common::format_stripchars($path_parts['filename'] ."_". RBAgency_Common::generate_random_string(6) . ".".$path_parts['extension']);
						$results = $wpdb->get_results("SELECT * FROM " . table_agency_profile_media . " WHERE ProfileID='". $ProfileID ."' AND ProfileMediaURL = '".$safeProfileMediaFilename ."'",ARRAY_A);
						$count = $wpdb->num_rows;

						if ($count < 1) {
							if($uploadMediaType == "Image") {
								if($_FILES['profileMedia'. $i]['type'] == "image/pjpeg" || $_FILES['profileMedia'. $i]['type'] == "image/jpeg" || $_FILES['profileMedia'. $i]['type'] == "image/gif" || $_FILES['profileMedia'. $i]['type'] == "image/png"){

										$image = new rb_agency_image();
										$image->load($_FILES['profileMedia'. $i]['tmp_name']);

										if ($image->getHeight() > $rb_agency_option_agencyimagemaxheight) {
											$image->resizeToHeight($rb_agency_option_agencyimagemaxheight);
										}
										$image->save(RBAGENCY_UPLOADPATH . $ProfileGallery ."/". $safeProfileMediaFilename);
										// Add to database
									$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $uploadMediaType ."','". $safeProfileMediaFilename ."','". $safeProfileMediaFilename ."')");
								} else {
									$error .= "<b><i>".__("Please upload an image file only", RBAGENCY_interact_TEXTDOMAIN)."</i></b><br />";
									  $have_error = true;
								}
							}
							else if($uploadMediaType =="VoiceDemo"){
								// Add to database
								$MIME = array('audio/mpeg', 'audio/mp3');
								if(in_array($_FILES['profileMedia'. $i]['type'], $MIME)){
									$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $uploadMediaType ."','". $safeProfileMediaFilename ."','". $safeProfileMediaFilename ."')");
						       		move_uploaded_file($_FILES['profileMedia'. $i]['tmp_name'], RBAGENCY_UPLOADPATH . $ProfileGallery ."/".$safeProfileMediaFilename);
								} else {
									$error .= "<b><i>".__("Please upload a mp3 file only", RBAGENCY_interact_TEXTDOMAIN) ."</i></b><br />";
									$have_error = true;
								}
							}
							else if($uploadMediaType =="Resume"){
								// Add to database
								if ($_FILES['profileMedia'. $i]['type'] == "application/msword" || $_FILES['profileMedia'. $i]['type'] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document"  || $_FILES['profileMedia'. $i]['type'] == "application/pdf" || $_FILES['profileMedia'. $i]['type'] == "application/rtf")
								{
										$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $uploadMediaType ."','". $safeProfileMediaFilename ."','". $safeProfileMediaFilename ."')");
						        		move_uploaded_file($_FILES['profileMedia'. $i]['tmp_name'], RBAGENCY_UPLOADPATH . $ProfileGallery ."/".$safeProfileMediaFilename);
								} else {
										$error .= "<b><i>".__("Please upload PDF/MSword/RTF files only", RBAGENCY_interact_TEXTDOMAIN) ."</i></b><br />";
									  $have_error = true;
								}
							}
							else if($uploadMediaType =="Headshot"){
								// Add to database
								if ($_FILES['profileMedia'. $i]['type'] == "application/msword"|| $_FILES['profileMedia'. $i]['type'] == "application/pdf" || $_FILES['profileMedia'. $i]['type'] == "application/rtf" || $_FILES['profileMedia'. $i]['type'] == "image/jpeg" || $_FILES['profileMedia'. $i]['type'] == "image/gif" || $_FILES['profileMedia'. $i]['type'] == "image/png")
								{
									$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $uploadMediaType ."','". $safeProfileMediaFilename ."','". $safeProfileMediaFilename ."')");
						        		move_uploaded_file($_FILES['profileMedia'. $i]['tmp_name'], RBAGENCY_UPLOADPATH . $ProfileGallery ."/".$safeProfileMediaFilename);
								} else {
										$error .= "<b><i>".__("Please upload PDF/MSWord/RTF/Image files only", RBAGENCY_interact_TEXTDOMAIN) ."</i></b><br />";
									  $have_error = true;
								}
							}
							else if($uploadMediaType =="CompCard"){
								// Add to database
								if ($_FILES['profileMedia'. $i]['type'] == "image/jpeg" || $_FILES['profileMedia'. $i]['type'] == "image/png")
								{
									$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $uploadMediaType ."','". $safeProfileMediaFilename ."','". $safeProfileMediaFilename ."')");
						            move_uploaded_file($_FILES['profileMedia'. $i]['tmp_name'], RBAGENCY_UPLOADPATH . $ProfileGallery ."/".$safeProfileMediaFilename);
								} else {
										$error .= "<b><i>".__("Please upload jpeg or png files only", RBAGENCY_interact_TEXTDOMAIN) ."</i></b><br />";
									$have_error = true;
								}
							}// Custom Media Categories
							else if (strpos($uploadMediaType,"rbcustommedia") !== false) {
															// Add to database
															$custom_media_info = explode("_",$uploadMediaType);
															$custom_media_title = $custom_media_info[1];
															$custom_media_type = $custom_media_info[2];
															$custom_media_extenstion = $custom_media_info[3];
															$arr_extensions = array();

															array_push($arr_extensions, $custom_media_extenstion);
		
															if($custom_media_extenstion == "doc"){
																array_push($arr_extensions,"application/octet-stream");
																array_push($arr_extensions,"docx");
															} elseif($custom_media_extenstion == "mp3"){
																array_push($arr_extensions,"audio/mpeg");
																array_push($arr_extensions,"audio/mp3");
															} elseif($custom_media_extenstion == "pdf"){
																array_push($arr_extensions,"application/pdf");
															}

															if (in_array($_FILES['profileMedia' . $i]['type'], $arr_extensions)) {
																$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('" . $ProfileID . "','" . $uploadMediaType . "','" . $safeProfileMediaFilename . "','" . $safeProfileMediaFilename . "')");
																move_uploaded_file($_FILES['profileMedia' . $i]['tmp_name'], RBAGENCY_UPLOADPATH . $ProfileGallery . "/" . $safeProfileMediaFilename);
															} else {
																$errorValidation['profileMedia'] = "<b><i>".__("Please upload ".$custom_media_extenstion." files only", RBAGENCY_TEXTDOMAIN)."</i></b><br />";
																$have_error = true;

															}
							} else {
								// Add to database
									if($_FILES['profileMedia'. $i]['type'] == "image/pjpeg" || $_FILES['profileMedia'. $i]['type'] == "image/jpeg" || $_FILES['profileMedia'. $i]['type'] == "image/gif" || $_FILES['profileMedia'. $i]['type'] == "image/png"){
									$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('". $ProfileID ."','". $uploadMediaType ."','". $safeProfileMediaFilename ."','". $safeProfileMediaFilename ."')");
						       			move_uploaded_file($_FILES['profileMedia'. $i]['tmp_name'], RBAGENCY_UPLOADPATH . $ProfileGallery ."/".$safeProfileMediaFilename);
								} else {
										$error .= "<b><i>".__("Please upload jpeg or png files only", RBAGENCY_interact_TEXTDOMAIN) ."</i></b><br />";
									$have_error = true;
								}
							}
						}// End count
					}// End have error = false
				}//End:: if profile media is not empty.
				$i++;
			}// endwhile

			// Upload Videos to Database
			if (isset($_POST['profileMediaV1']) && !empty($_POST['profileMediaV1'])) {
				$profileMediaType = $_POST['profileMediaV1Type'];
				$profileMediaURL = rb_agency_get_VideoFromObject($_POST['profileMediaV1']);
				$profileVideoType = rb_agency_get_videotype($_POST['profileMediaV1']);
				$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL, ProfileVideoType) VALUES ('". $ProfileID ."','". $profileMediaType ."','". $profileMediaType ."','". $profileMediaURL ."','".$profileVideoType."')");
			}
			if (isset($_POST['profileMediaV2']) && !empty($_POST['profileMediaV2'])) {
				$profileMediaType	=$_POST['profileMediaV2Type'];
				$profileMediaURL = rb_agency_get_VideoFromObject($_POST['profileMediaV2']);
				$profileVideoType = rb_agency_get_videotype($_POST['profileMediaV2']);
				$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL, ProfileVideoType) VALUES ('". $ProfileID ."','". $profileMediaType ."','". $profileMediaType ."','". $profileMediaURL ."','".$profileVideoType."')");
			}
			if (isset($_POST['profileMediaV3']) && !empty($_POST['profileMediaV3'])) {
				$profileMediaType	=$_POST['profileMediaV3Type'];
				$profileMediaURL = rb_agency_get_VideoFromObject($_POST['profileMediaV3']);
				$profileVideoType = rb_agency_get_videotype($_POST['profileMediaV3']);
				$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL, ProfileVideoType) VALUES ('". $ProfileID ."','". $profileMediaType ."','". $profileMediaType ."','". $profileMediaURL ."','".$profileVideoType."')");
			}
			if (isset($_POST['profileMediaV4']) && !empty($_POST['profileMediaV4'])) {
				$profileMediaType	=$_POST['profileMediaV4Type'];
				$profileMediaURL = rb_agency_get_VideoFromObject($_POST['profileMediaV4']);
				$profileVideoType = rb_agency_get_videotype($_POST['profileMediaV4']);
				$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL, ProfileVideoType) VALUES ('". $ProfileID ."','". $profileMediaType ."','". $profileMediaType ."','". $profileMediaURL ."','".$profileVideoType."')");
			}

			/* --------------------------------------------------------- CLEAN THIS UP -------------- */
			// Do we have a custom image yet? Lets just set the first one as primary.
			$results = $wpdb->get_results("SELECT * FROM " . table_agency_profile_media . " WHERE ProfileID='". $ProfileID ."' AND ProfileMediaType = 'Image' AND ProfileMediaPrimary='1'",ARRAY_A);
			$count = $wpdb->num_rows;
			if ($count < 1) {
				$resultsNeedOne = $wpdb->get_results("SELECT * FROM " . table_agency_profile_media . " WHERE ProfileID='". $ProfileID ."' AND ProfileMediaType = 'Image' LIMIT 0, 1",ARRAY_A);
				foreach ($resultsNeedOne as $dataNeedOne) {
					$resultsFoundOne = $wpdb->query("UPDATE " . table_agency_profile_media . " SET ProfileMediaPrimary='1' WHERE ProfileID='". $ProfileID ."' AND ProfileMediaID = '". $dataNeedOne['ProfileMediaID'] . "'");
					break;
				}
			}
				if ($ProfileMediaPrimaryID > 0) {
					// Update Primary Image
					$results = $wpdb->query("UPDATE " . table_agency_profile_media . " SET ProfileMediaPrimary='0' WHERE ProfileID=$ProfileID");
					$results = $wpdb->query("UPDATE " . table_agency_profile_media . " SET ProfileMediaPrimary='1' WHERE ProfileID=$ProfileID AND ProfileMediaID=$ProfileMediaPrimaryID");
			}
			
			
			rb_interact_sendadmin_pending_info($ProfileID);
			
			// Update Record
				$update = "UPDATE " . table_agency_profile . " SET ProfileDateUpdated=now(), ProfileIsActive='" . $ProfileStatus. "' WHERE ProfileID=$ProfileID";
				$results = $wpdb->query($update);     

			/* --------------------------------------------------------- CLEAN THIS UP -------------- */

			$alerts = "<div id=\"message\" class=\"updated\"><p>". __("Profile updated successfully", RBAGENCY_interact_TEXTDOMAIN) ."!</a></p></div>";
		} else {
			$alerts = "<div id=\"message\" class=\"error\"><p>". __("Error updating record, please ensure you have filled out all required fields.", RBAGENCY_interact_TEXTDOMAIN) ."</p></div>"; 
		}

		if ($have_error != true) {
					// redirect only, if requirement of Redirect page is not  "/profile-member/media/ after successful files upload"
						// delete temporary storage
			delete_user_meta($current_user->ID, 'rb_agency_new_registeredUser');
             $rb_agency_new_registeredUser = get_user_meta($current_user->ID,'rb_agency_new_registeredUser');
             
            //exist user should be in pending page
			$old_exist_user = get_user_meta( $current_user->ID, 'rb_agency_interact_clientold', true);
            if(!empty($old_exist_user)){
                //wp_redirect( $rb_agency_interact_WPURL ."/profile-member/pending/?e" );
                //exit;
            }
            
			if(empty($rb_agency_new_registeredUser) || rb_get_user_profilstatus() == 1){

				wp_new_user_notification_pending($current_user->ID , false);

				global $user_ID;
				$user_info = new WP_User($user_ID);
				$s2role = '';

				foreach($user_info->roles as $k=>$v){
					$s2role = strtolower($v);
				}

				$useS2member = get_option('rbagency_use_s2member');
				if($useS2member == true){
					//if not yet paid
					$role_found = strpos($s2role,'s2member');
			    	if($role_found === false){			    		
						wp_redirect($rb_agency_interact_WPURL."/user-membership-page/");
					}else{
						//wp_redirect( $rb_agency_interact_WPURL ."/profile-member/pending/" );
						wp_redirect( $rb_agency_interact_WPURL ."/profile-member/media/?st=updated" );
					}
				}else{
					//wp_redirect( $rb_agency_interact_WPURL ."/profile-member/pending/" );
					wp_redirect( $rb_agency_interact_WPURL ."/profile-member/media/?st=updated" );
				}
				exit;	
			}

		}
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
				echo "<div id=\"profile-steps\">Profile Setup: Step 3 of 3</div>\n";
			}
			echo "<div id=\"profile-manage\" class=\"profile-media\">\n";

			// Menu
			include("include-menu.php"); 
			echo " <div class=\"manage-media manage-content\">\n";

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
				include("include-profilemedia.php"); 

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
