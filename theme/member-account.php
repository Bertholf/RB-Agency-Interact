<?php
/*
Template Name: Edit Member Details
* @name		Edit Member Details
* @type		PHP page
* @desc		Edit Member Details
*/
session_start();
header("Cache-control: private"); //IE 6 Fix
global $wpdb;
/* Get User Info ******************************************/ 
global $current_user, $wp_roles;
get_currentuserinfo();
// Get Settings
$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_registerallow = (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_registerallow'];

// Were they users or agents?
$profiletype = (int)get_user_meta($current_user->id, "rb_agency_interact_profiletype", true);
if ($profiletype == 1) { $profiletypetext = __("Agent/Producer", rb_agencyinteract_TEXTDOMAIN); } else { $profiletypetext = __("Model/Talent", rb_agencyinteract_TEXTDOMAIN); }

	// Change Title
	add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
		function rb_agencyinteractive_override_title(){
			return __("Manage Profile", rb_agencyinteract_TEXTDOMAIN);
		}   
	
	/* Load the registration file. */
	require_once( ABSPATH . WPINC . '/registration.php' );
	require_once( ABSPATH . 'wp-admin/includes' . '/template.php' ); // this is only for the selected() function

// Form Post
if (isset($_POST['action'])) {
	$ProfileID					=$_POST['ProfileID'];
	$ProfileUsername			=$_POST['ProfileUsername'];
	$ProfilePassword			=$_POST['ProfilePassword'];
	$ProfilePasswordConfirm		=$_POST['ProfilePasswordConfirm'];
	$ProfileUserLinked			=$_POST['ProfileUserLinked'];
	$ProfileContactNameFirst	=trim($_POST['ProfileContactNameFirst']);
	$ProfileContactNameLast		=trim($_POST['ProfileContactNameLast']);
	$ProfileContactDisplay		=trim($_POST['ProfileContactDisplay']);

  	if (empty($ProfileContactDisplay)) {  // Probably a new record... 
		if ($rb_agency_option_profilenaming == 0) { 
			$ProfileContactDisplay = $ProfileContactNameFirst . " ". $ProfileContactNameLast;
		} elseif ($rb_agency_option_profilenaming == 1) { 
			$ProfileContactDisplay = $ProfileContactNameFirst . " ". substr($ProfileContactNameLast, 0, 1);
		} elseif ($rb_agency_option_profilenaming == 2) { 
			$error .= "<b><i>". __(LabelSingular ." must have a display name identified", rb_agencyinteract_TEXTDOMAIN) . ".</i></b><br>";
			$have_error = true;
		} elseif ($rb_agency_option_profilenaming == 3) { // by firstname
			$ProfileContactDisplay = "ID ". $ProfileID;
		} elseif ($rb_agency_option_profilenaming == 4) {
                        $ProfileContactDisplay = $ProfileContactNameFirst;
                }
  	}

	$ProfileGallery				=$_POST['ProfileGallery'];

  	if (empty($ProfileGallery)) {  // Probably a new record... 
		$ProfileGallery = rb_agency_safenames($ProfileContactDisplay); 
  	}

	$ProfileContactEmail		=$_POST['ProfileContactEmail'];
	$ProfileContactWebsite		=$_POST['ProfileContactWebsite'];
	$ProfileContactLinkFacebook	=$_POST['ProfileContactLinkFacebook'];
	$ProfileContactLinkTwitter	=$_POST['ProfileContactLinkTwitter'];
	$ProfileContactLinkYouTube	=$_POST['ProfileContactLinkYouTube'];
	$ProfileContactLinkFlickr	=$_POST['ProfileContactLinkFlickr'];
	$ProfileContactPhoneHome	=$_POST['ProfileContactPhoneHome'];
	$ProfileContactPhoneCell	=$_POST['ProfileContactPhoneCell'];
	$ProfileContactPhoneWork	=$_POST['ProfileContactPhoneWork'];
	$ProfileGender    		=$_POST['ProfileGender'];
	$ProfileType    		=$_POST['ProfileType'];
	$ProfileDateBirth	    		=$_POST['ProfileDateBirth'];
	$ProfileLocationStreet		=$_POST['ProfileLocationStreet'];
	$ProfileLocationCity		=rb_agency_strtoproper($_POST['ProfileLocationCity']);
	$ProfileLocationState		=strtoupper($_POST['ProfileLocationState']);
	$ProfileLocationZip		=$_POST['ProfileLocationZip'];
	$ProfileLocationCountry		=$_POST['ProfileLocationCountry'];
	$ProfileLanguage			=$_POST['ProfileLanguage'];

	if ($rb_agencyinteract_option_registerapproval == 1) {

		// 0 Inactive | 1 Active | 2 Archived | 3 Pending Approval
		$ProfileIsActive			= 0; 
	} else {
		$ProfileIsActive			= 3; 
	}

	// Error checking
	$error = "";
	$have_error = false;
	if(trim($ProfileContactNameFirst) == ""){
		$error .= "<b><i>".__("Name is required.", rb_agencyinteract_TEXTDOMAIN) ."</i></b><br>";
		$have_error = true;
	}
	
	/* Update user password. */
	if ( !empty($ProfilePassword) && !empty($ProfilePasswordConfirm) ) {
		if ( $ProfilePassword == $ProfilePasswordConfirm ) {
			wp_update_user( array( 'ID' => $current_user->id, 'user_pass' => esc_attr( $ProfilePassword ) ) );
		} else {
			$have_error = true;
			$error .= __("The passwords you entered do not match.  Your password was not updated.", rb_agencyinteract_TEXTDOMAIN);
		}
	}
	
	// Get Post State
	$action = $_POST['action'];
	switch($action) {

	// *************************************************************************************************** //
	// Add Record
	case 'addRecord':
		if(!$have_error){
			$ProfileIsActive		= 3;
			$ProfileIsFeatured	= 0;
			$ProfileIsPromoted	= 0;
			$ProfileStatHits		= 0;
			$ProfileDateBirth	    	= $_POST['ProfileDateBirth_Year'] ."-". $_POST['ProfileDateBirth_Month'] ."-". $_POST['ProfileDateBirth_Day'];
			$ProfileGallery 		= rb_agencyinteract_checkdir($ProfileGallery); // Check directory existence , create if does not exist.

			// Create Record
			$insert = "INSERT INTO " . table_agency_profile .
			" (ProfileUserLinked,ProfileGallery,ProfileContactDisplay,ProfileContactNameFirst,ProfileContactNameLast,
			   ProfileContactEmail,ProfileContactWebsite,ProfileGender,ProfileType, ProfileDateBirth,
			   ProfileContactLinkFacebook,ProfileContactLinkTwitter,ProfileContactLinkYouTube,ProfileContactLinkFlickr,
			   ProfileLocationStreet,ProfileLocationCity,ProfileLocationState,ProfileLocationZip,ProfileLocationCountry,
			   ProfileContactPhoneHome, ProfileContactPhoneCell, ProfileContactPhoneWork,
			   ProfileDateUpdated,ProfileIsActive)" .
			"VALUES (". $ProfileUserLinked . 
			         ",'" . $wpdb->escape($ProfileGallery) . "','" . 
					 $wpdb->escape($ProfileContactDisplay) . 
					 "','" . $wpdb->escape($ProfileContactNameFirst) . "','" . 
					 $wpdb->escape($ProfileContactNameLast) . 
					 "','" . $wpdb->escape($ProfileContactEmail) . "','" . 
					 $wpdb->escape($ProfileContactWebsite) . "','" . 
					 $wpdb->escape($ProfileGender) .  "','" .
                                         $wpdb->escape($ProfileType) .  "','" .
                                         $wpdb->escape($ProfileDateBirth) . "','" . 
					 $wpdb->escape($ProfileContactLinkFacebook) . "','" . 
					 $wpdb->escape($ProfileContactLinkTwitter) . "','" . 
					 $wpdb->escape($ProfileContactLinkYouTube) . "','" . 
					 $wpdb->escape($ProfileContactLinkFlickr) . "','" . 
					 $wpdb->escape($ProfileLocationStreet) . "','" . 
					 $wpdb->escape($ProfileLocationCity) . "','" . 
					 $wpdb->escape($ProfileLocationState) . "','" . 
					 $wpdb->escape($ProfileLocationZip) . "','" . 
					 $wpdb->escape($ProfileLocationCountry) . "','" . 
					 $wpdb->escape($ProfileContactPhoneHome) . "','" . 
					 $wpdb->escape($ProfileContactPhoneCell) . "','" . 
					 $wpdb->escape($ProfileContactPhoneWork) . "',now(), ". 
					 $ProfileIsActive .")";

		      $results = $wpdb->query($insert) or die(mysql_error());
              $ProfileID = $wpdb->insert_id;
 			

			// delete temporary storage
			delete_user_meta($ProfileUserLinked, 'rb_agency_new_registeredUser');
            
			// Add New Custom Field Values
			$pos = 0;
			foreach($_POST as $key => $value) {			
			         
				if ((substr($key, 0, 15) == "ProfileCustomID") && (isset($value) && !empty($value))) {

					$pos++; 
					if($pos == 1){
						// Remove Old Custom Field Values
						$delete1 = "DELETE FROM " . table_agency_customfield_mux . " WHERE ProfileID = \"". $ProfileID ."\"";
						$results1 = $wpdb->query($delete1);// or die(mysql_error());	
					}

					$ProfileCustomID = substr($key, 15);
					if(is_array($value)){
						$value =  implode(",",$value);
					}
					if(!empty($value)){
						$insert1 = "INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomValue)" . "VALUES ('" . $ProfileID . "','" . $ProfileCustomID . "','" . $value . "')";
						$results1 = $wpdb->query($insert1);
					}
				}
			}
			/* Update WordPress user information. */
			update_usermeta( $current_user->id, 'first_name', esc_attr( $ProfileContactNameFirst ) );
			update_usermeta( $current_user->id, 'last_name', esc_attr( $ProfileContactNameLast ) );
			update_usermeta( $current_user->id, 'nickname', esc_attr( $ProfileContactDisplay ) );
			update_usermeta( $current_user->id, 'display_name', esc_attr( $ProfileContactDisplay ) );
			update_usermeta( $current_user->id, 'user_email', esc_attr( $ProfileContactEmail ) );
			update_usermeta( $current_user->id, 'rb_agency_interact_pgender', esc_attr( $ProfileGender ) );			
			
	#DEBUG
	#echo "<script>alert('".$ProfileUsername."');<\/script>";		 
			// Link to Wordpress user_meta
			 
			if ( username_exists( $ProfileUsername) ) {

				$isLinked =  mysql_query("UPDATE ". table_agency_profile ." SET ProfileUserLinked =  ". $current_user->ID ." WHERE ProfileID = ".$ProfileID." ");
				if($isLinked){

					wp_redirect(get_bloginfo("wpurl") . "/profile-member/media/");

				}  else {
				    die(mysql_error());	 				    				}
			} else {
				$user_data = array(
				    'ID' => $current_user->id,
				    'user_pass' => wp_generate_password(),
				    'user_login' => $ProfileUsername,
				    'user_email' => $ProfileContactEmail,
				    'display_name' => $ProfileContactDisplay,
				    'first_name' => $ProfileContactNameFirst,
				    'last_name' => $ProfileContactNameLast,
				    'role' =>  get_option('default_role') // Use default role or another role, e.g. 'editor'
				);
				$user_id = wp_insert_user( $user_data );
				wp_set_password($ProfilePassword, $user_id);
			}

			// Set Display Name as Record ID (We have to do this after so we know what record ID to use... right ;)
			if ($rb_agency_option_profilenaming == 3) {
				$ProfileContactDisplay = "ID-". $ProfileID;
				$ProfileGallery = "ID". $ProfileID."-";
				$update = $wpdb->query("UPDATE " . table_agency_profile . " SET ProfileContactDisplay='". $ProfileContactDisplay. "', ProfileGallery='". $ProfileGallery. "' WHERE ProfileID='". $ProfileID ."'");
				$updated = $wpdb->query($update);
			}			
			
			$alerts = "<div id=\"message\" class=\"updated\"><p>". __("New Profile added successfully", rb_agencyinteract_TEXTDOMAIN) ."!</p></div>"; 
					
			/* Redirect so the page will show updated info. */
			if ( !$error ) {
				
				wp_redirect(get_bloginfo("wpurl") . "/profile-member/manage/");
				//exit;
			}
		} else {
			
       	$alerts = "<div id=\"message\" class=\"error\"><p>". __("Error creating record, please ensure you have filled out all required fields.", rb_agencyinteract_TEXTDOMAIN) ."<br />". $error ."</p></div>"; 
		}
	break;
	
	// *************************************************************************************************** //
	// Edit Record
	case 'editRecord':
		if(!$have_error){
			
			// Update Record
			$update = "UPDATE " . table_agency_profile . " SET 
			ProfileContactNameFirst='" . $wpdb->escape($ProfileContactNameFirst) . "',
			ProfileContactNameLast='" . $wpdb->escape($ProfileContactNameLast) . "',
			ProfileContactEmail='" . $wpdb->escape($ProfileContactEmail) . "',
			ProfileContactWebsite='" . $wpdb->escape($ProfileContactWebsite) . "',
			ProfileContactLinkFacebook='" . $wpdb->escape($ProfileContactLinkFacebook) . "',
			ProfileContactLinkTwitter='" . $wpdb->escape($ProfileContactLinkTwitter) . "',
			ProfileContactLinkYouTube='" . $wpdb->escape($ProfileContactLinkYouTube) . "',
			ProfileContactLinkFlickr='" . $wpdb->escape($ProfileContactLinkFlickr) . "',
			ProfileContactPhoneHome='" . $wpdb->escape($ProfileContactPhoneHome) . "',
			ProfileContactPhoneCell='" . $wpdb->escape($ProfileContactPhoneCell) . "',
			ProfileContactPhoneWork='" . $wpdb->escape($ProfileContactPhoneWork) . "',
			ProfileGender='" . $wpdb->escape($ProfileGender) . "',
			ProfileDateBirth ='" . $wpdb->escape($ProfileDateBirth) . "',
			ProfileLocationStreet='" . $wpdb->escape($ProfileLocationStreet) . "',
			ProfileLocationCity='" . $wpdb->escape($ProfileLocationCity) . "',
			ProfileLocationState='" . $wpdb->escape($ProfileLocationState) . "',
			ProfileLocationZip ='" . $wpdb->escape($ProfileLocationZip) . "',
			ProfileLocationCountry='" . $wpdb->escape($ProfileLocationCountry) . "',
			ProfileDateUpdated=now()
			WHERE ProfileID=$ProfileID";
		    $results = $wpdb->query($update);             
		    
			/* Update WordPress user information. */
			update_usermeta( $current_user->id, 'first_name', esc_attr( $ProfileContactNameFirst ) );
			update_usermeta( $current_user->id, 'last_name', esc_attr( $ProfileContactNameLast ) );
			update_usermeta( $current_user->id, 'nickname', esc_attr( $ProfileContactDisplay ) );
			update_usermeta( $current_user->id, 'display_name', esc_attr( $ProfileContactDisplay ) );
			update_usermeta( $current_user->id, 'user_email', esc_attr( $ProfileContactEmail ) );
			update_usermeta( $current_user->id, 'rb_agency_interact_pgender', esc_attr( $ProfileGender ) );	

			// Add New Custom Field Values			 
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
		
			
			$alerts = "<div id=\"message\" class=\"updated\"><p>". __("Profile updated successfully", rb_agencyinteract_TEXTDOMAIN) ."!</a></p></div>";
		} else {
			$alerts = "<div id=\"message\" class=\"error\"><p>". __("Error updating record, please ensure you have filled out all required fields.", rb_agencyinteract_TEXTDOMAIN) ."<br />". $error ."</p></div>"; 
		}
		
		wp_redirect( $rb_agencyinteract_WPURL ."/profile-member/" );
		//exit;
	break;
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

		// get profile Custom fields value
	echo "<div id=\"container\" class=\"".$content_class." column rb-agency-interact-account\">\n";
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
				echo "<div id=\"profile-steps\">Profile Setup: Step 1 of 2</div>\n";
			} else {
				echo "<div id=\"profile-steps\">Profile Setup: Step 1 of 3</div>\n";
			}
			
			
			echo "<div id=\"profile-manage\" class=\"profile-account\">\n";
			$rb_agency_new_registeredUser = get_user_meta($current_user->id,'rb_agency_new_registeredUser');
			
			// Menu
			include("include-menu.php"); 	
			echo " <div class=\"manage-account manage-content\">\n";
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
					include("include-profileaccount.php"); 	
						
						
			  	} // is there record?
			} else {
			  if ($rb_agencyinteract_option_registerallow  == 1) {
				// Users CAN register themselves
				
				// No Record Exists, register them
				echo "<p>". __("Records show you are not currently linked to a model or agency profile.  Lets setup your profile now!", rb_agencyinteract_TEXTDOMAIN) ."</p>";
				
				// Register Profile
				include("include-profileregister.php"); 	
				
				
			  } else {
				// Cant register
				echo "<strong>". __("Self registration is not permitted.", rb_agencyinteract_TEXTDOMAIN) ."</strong>";
			  }
				
			}
			echo " </div>\n"; // .manage-account
			echo "</div>\n"; // #profile-manage
		} else {
			echo "<p class=\"warning\">\n";
					_e('You must be logged in to edit your profile.', 'frontendprofile');
			echo "</p><!-- .warning -->\n";
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

// Get Footer
get_footer();
?>
