<?php
// *************************************************************************************************** //
// Prepare Page
	global $wpdb;

	if(is_user_logged_in()){
		wp_redirect(get_bloginfo("url")); exit;
	}

	/* Get Options */
	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
	$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_model_toc = isset($rb_agency_options_arr['rb_agency_option_agency_model_toc'])?$rb_agency_options_arr['rb_agency_option_agency_model_toc']: "/models-terms-of-conditions";
	$rb_agencyinteract_option_registerapproval = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval']:"";
	//Sidebar
	$rb_agencyinteract_option_profilemanage_sidebar = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar']:0;
	$rb_agencyinteract_option_default_registered_users = isset($rb_agency_interact_options_arr["rb_agencyinteract_option_default_registered_users"])?$rb_agency_interact_options_arr["rb_agencyinteract_option_default_registered_users"]:3;
	if($rb_agencyinteract_option_profilemanage_sidebar){
		$column_class = primary_class();
	} else {
		$column_class = fullwidth_class();
	}

	$rb_agency_uri_profiletype = get_query_var("typeofprofile");

	// Profile Naming
	$rb_agency_option_profilenaming = (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];

	//+Registration
	// - show/hide registration for Agent/Producers
	$rb_agencyinteract_option_registerallowAgentProducer = isset($registration['rb_agencyinteract_option_registerallowAgentProducer'])?$registration['rb_agencyinteract_option_registerallowAgentProducer']:0;

	// - show/hide  self-generate password
	$rb_agencyinteract_option_registerconfirm = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerconfirm'])?(int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerconfirm']:0;
	// show/hide username and password
	$rb_agencyinteract_option_useraccountcreation = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_useraccountcreation'])?(int)$rb_agency_interact_options_arr['rb_agencyinteract_option_useraccountcreation']:0;
	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );

	if(!function_exists("parse_signed_request")){
		function parse_signed_request($signed_request, $secret) {
			list($encoded_sig, $payload) = explode('.', $signed_request, 2);

			// decode the data
			$sig = base64_url_decode($encoded_sig);
			$data = json_decode(base64_url_decode($payload), true);

			if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
				error_log('Unknown algorithm. Expected HMAC-SHA256');
				return null;
			}

			// check sig
			$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
			if ($sig !== $expected_sig) {
				error_log('Bad Signed JSON signature!');
				return null;
			}
			return $data;
		}
	}

	if(!function_exists("base64_url_decode")){
		function base64_url_decode($input) {
			return base64_decode(strtr($input, '-_', '+/'));
		}
	}

	/*
	#DEBUG !
	if ($_REQUEST) {
				echo '<p>signed_request contents:</p>';
				$response = parse_signed_request($_REQUEST['signed_request'], FACEBOOK_SECRET);
				print_r($_REQUEST);
				echo '<pre>';
				print_r($response);
				echo '</pre>';
	}
	 */

	/* If user registered, input info. */
	$user_login = "";
	$user_pass = "";
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'adduser' ) {

		$first_name = $_POST['profile_first_name'];
		$last_name  = $_POST['profile_last_name'];
		$user_email = $_POST['profile_email'];
		$ProfileGender = $_POST['ProfileGender'];
		$user_pass  = NULL;

		

			//if($rb_agencyinteract_option_useraccountcreation == 1 && $rb_agencyinteract_option_registerconfirm == 1){ // generate a username if username creation is disabled
					$user_login = $_POST['profile_user_name'];
			//} else {
			//		$user_login = strtolower($first_name."_".wp_generate_password(5));
			//}// TODO Cleanup.  Disabled per @Anne

			if ($rb_agencyinteract_option_registerconfirm == 0 && $rb_agencyinteract_option_registerconfirm ==  0) {
					$user_pass = wp_generate_password();// generate a password if it's creation is disabled
			} else {
					$user_pass = $_POST['profile_password'];
			}

		$userdata = array(
			'user_pass' => $user_pass ,
			'user_login' => esc_attr( $user_login ),
			'first_name' => esc_attr( $first_name ),
			'last_name' => esc_attr( $last_name ),
			'user_email' => esc_attr( $user_email ),
			'role' => get_option( 'default_role' )
		);


		// Error checking
		$error = "";
		$have_error = false;
		//if($rb_agencyinteract_option_useraccountcreation == 1){ //username always required
			if (empty($userdata['user_login'])) {
				$error .= __("A username is required for registration.<br />", RBAGENCY_interact_TEXTDOMAIN);
				$have_error = true;
			}
			if ( username_exists($userdata['user_login'])) {
				$error .= __("Sorry, that username already exists!<br />", RBAGENCY_interact_TEXTDOMAIN);
				$have_error = true;
			}
		//}
		if ( !is_email($userdata['user_email'])) {
			$error .= __("You must enter a valid email address.<br />", RBAGENCY_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( email_exists($userdata['user_email'])) {
			$error .= __("Sorry, that email address is already used!<br />", RBAGENCY_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( empty($_POST['profile_agree'])) {
			$error .= __("You must agree to the terms and conditions to register.<br />", RBAGENCY_interact_TEXTDOMAIN);
			$have_error = true;
		}

		if (empty($ProfileGender)) {
			$error .= __("Gender is required .<br />", RBAGENCY_interact_TEXTDOMAIN);
			$have_error = true;
		}

		if (empty($_POST["ProfileType"])) {
			$error .= __("Profile Type is required .<br />", RBAGENCY_interact_TEXTDOMAIN);
			$have_error = true;
		}

		if (empty($_POST["profile_contact_display_name"]) && $rb_agency_option_profilenaming == 2) {
			$error .= __("Display Name is required .<br />", RBAGENCY_interact_TEXTDOMAIN);
			$have_error = true;
		}


		// Bug Free!
		if($have_error == false && empty($error)) {
			$new_user = wp_insert_user( $userdata );
			$new_user_type = array();
			$new_user_type =implode(",", $_POST['ProfileType']);
			$gender = $_POST['ProfileGender'];

			if ($rb_agency_option_profilenaming == 0) {
				$profile_contact_display = $first_name . " ". $last_name;
			} elseif ($rb_agency_option_profilenaming == 1) {
				$profile_contact_display = $first_name . " ". substr($last_name, 0, 1);
			} elseif ($rb_agency_option_profilenaming == 2) {
			/*	$error .= "<b><i>". __(LabelSingular ." must have a display name identified", RBAGENCY_interact_TEXTDOMAIN) . ".</i></b><br>";
				$have_error = true;
			 */
					$profile_contact_display = $_POST["profile_contact_display_name"];//$first_name . " ". $last_name;

			} elseif ($rb_agency_option_profilenaming == 3) { // by firstname
				$profile_contact_display = "ID-". $new_user;
			} elseif ($rb_agency_option_profilenaming == 4) {
				  $profile_contact_display = $first_name;
			  }

			  if($rb_agency_option_profilenaming != 3){
				$profile_gallery = RBAgency_Common::format_stripchars($profile_contact_display);
				} else {
					$profile_gallery = $profile_contact_display;
				}
			$profile_gallery = rb_agency_createdir($profile_gallery);


			// Model or Client
			update_user_meta($new_user, 'rb_agency_interact_profiletype', $new_user_type);
			update_user_meta($new_user, 'rb_agency_interact_pgender', $gender);

			$profileactive = null;
			/* if ($rb_agencyinteract_option_registerapproval == 1) { // automatically approved
				$profileactive = 1;
			} else { // manually approved
				$profileactive = $rb_agencyinteract_option_default_registered_users;
			} */
			$_registerapproval = $rb_agencyinteract_option_registerapproval;
			$_default_registered = $rb_agencyinteract_option_default_registered_users;
			//manually approve(0)
			if($_registerapproval == 0){
				if($_default_registered == 1){
					$profileactive = 1;
				}elseif($_default_registered == 2){
					$profileactive = 2;
				}elseif($_default_registered == 3){
					$profileactive = 3;
				}elseif($_default_registered == 4){
					$profileactive = 4;
				}elseif($_default_registered == 0){
					$profileactive = 0;
				}
			}else{
				//automatic but do not allow the active as default..
				/* if($_default_registered != 1){
					$profileactive = $_default_registered;
				}else{
					$profileactive = 0; //inactive
				} */
				 // decided to make it really automatic whatever it is.
				$profileactive = $_default_registered;
			}
			/* echo "
			$_registerapproval = approval <br/>
			$_default_registered = default <br/>
			$profileactive = profiule stats
			"; */


			// Insert to table_agency_profile
			$wpdb->query($wpdb->prepare("INSERT INTO ".table_agency_profile."
				(
				ProfileContactDisplay,
				ProfileContactNameFirst,
				ProfileContactNameLast,
				ProfileGender,
				ProfileContactEmail,
				ProfileIsActive,
				ProfileUserLinked,
				ProfileType,
				ProfileGallery
				)
					VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s)",
					$profile_contact_display,
				$first_name,
				$last_name,
				$ProfileGender,
				$user_email,
				$profileactive,
				$new_user,
				$new_user_type,
				$profile_gallery
				));

			$id = $wpdb->insert_id;
			$NewProfileID = $id;
			//Update and set ProfileUserLinked,ProfileGallery and ProfileContactDisplay with the ProfileID
			if($rb_agency_option_profilenaming == 3){
				$update = $wpdb->query("UPDATE " . table_agency_profile . " SET ProfileGallery='ID-" . $id . "', ProfileContactDisplay='ID-" . $id . "' WHERE ProfileID='" . $id . "'");
				$profile_gallery = "ID-".$id;
				rb_agency_createdir($profile_gallery);
			}
			add_user_meta( $new_user, 'user_profile_id', $new_user);

			//Custom Fields
			$arr = array();

			foreach($_POST as $key => $value) {
				if ((substr($key, 0, 15) == "ProfileCustomID") && (isset($value) && !empty($value))) {
					$ProfileCustomID = substr($key, 15);
					if(is_array($value)){
						$value =  implode(",",$value);
					}
					//format: _ID|value|_ID|value|_ID|value|
					if(!empty($value)){
						$arr[$ProfileCustomID] = $value;
					}
				}
			}
			$arr["new"] = true;
			$arr["step1"] = true;
			$arr["step2"] = true;
			$arr["step3"] = true;
			add_user_meta($new_user, 'rb_agency_new_registeredUser',$arr);

			// Log them in if no confirmation required.
			if ($rb_agencyinteract_option_registerapproval == 1) { // automatically approval

					// Notify admin and user
					rb_new_user_notification($new_user,$user_pass);

			} else { // manually approval

						// Notify admin and user
						rb_new_user_notification($new_user,$user_pass);
			}



		}

		// Log them in if no confirmation required.
		/*if ($rb_agencyinteract_option_registerapproval == 1) {
			if($login){
				header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
			}
		}	 */
	}


// *************************************************************************************************** //
// Prepare Page

	// Call Header
	if(!$shortcode_register){
		echo $rb_header = RBAgency_Common::rb_header();
	}
	echo "<div class=\"".$column_class." column rb-agency-interact profile-register\">\n";
	echo "  <div id=\"rbcontent\">\n";

	// ****************************************************************************************** //
	// Already logged in

	if ( is_user_logged_in() && !current_user_can( 'create_users' ) ) {

	echo "    <p class=\"log-in-out rbalert\">\n";
	echo "		". __("You are currently logged in as .", RBAGENCY_interact_TEXTDOMAIN) ." <a href=\"/profile-member/\" title=\"". $login->display_name ."\">". $login->display_name ."</a>\n";
				//printf( __("You are logged in as <a href="%1$s" title="%2$s">%2$s</a>.  You don\'t need another account.', RBAGENCY_interact_TEXTDOMAIN), get_author_posts_url( $curauth->ID ), $user_identity );
	echo "		<a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __("Log out of this account", RBAGENCY_interact_TEXTDOMAIN) ."\">". __("Log out", RBAGENCY_interact_TEXTDOMAIN) ." &raquo;</a>\n";
	echo "    </p><!-- .rbalert -->\n";

	} elseif ( isset($new_user) ) {

	echo "    <p class=\"rbalert success\">\n";
				if ( current_user_can( 'create_users' ) )
					printf( __("A user account for %s has been created.", RBAGENCY_interact_TEXTDOMAIN), $user_login );
				else

					$rbagency_use_s2member_option = get_option('rbagency_use_s2member');

					if($rbagency_use_s2member_option == true){
						$rbagency_initial_message_after_registration = get_option('rbagency_initial_message_after_registration');
						if(!empty($rbagency_initial_message_after_registration)){
							//$message = "";
							$message = str_replace('[username]',$user_login,$rbagency_initial_message_after_registration);
							echo nl2br($message);

						}else{
							printf( __("Thank you for registering, %s!", RBAGENCY_interact_TEXTDOMAIN), $user_login );
							echo "<br/><br/>";
							//if ($rb_agencyinteract_option_registerapproval == 1) { // automatically approve
							
							printf( __("Please check your email for the next step to complete registration.<br>", RBAGENCY_interact_TEXTDOMAIN), $user_login );
							echo "<br/><br/>";
							echo __("<i>(It might go to your spam folder )</i>", RBAGENCY_interact_TEXTDOMAIN);
						}
					}else{

						if($rb_agencyinteract_option_registerconfirm == 1){
							$notification_password_self_generated = trim($rb_agency_interact_options_arr['notification_password_self_generated']);
							if(!empty($notification_password_self_generated)){
								
								$message = str_replace('[agency_name]',get_option('blogname'),$notification_password_self_generated);
								$message = str_replace('[first_name]',$_POST["ProfileContactNameFirst"],$notification_password_self_generated);
								$message = str_replace('[login_url]',site_url()."/profile-login",$message);
								$message = str_replace('[username]',$user_login,$message);
								$message = str_replace('[password]',$user_pass,$message);
								$message = str_replace('[agency_email]',get_option('admin_email'),$message);
								$message = str_replace('[agency_name]',get_option('blogname'),$message);
								$message = str_replace('[domain]',site_url(),$message);
								echo nl2br($message);
							}else{
								printf( __("Thank you for registering, %s!", RBAGENCY_interact_TEXTDOMAIN), $user_login );
								echo "<br/><br/>";
								//if ($rb_agencyinteract_option_registerapproval == 1) { // automatically approve
								
								printf( __("Please click login button below to continue your registration.", RBAGENCY_interact_TEXTDOMAIN), $user_login );
								echo "<br><br>";
								printf( "<a href=\"../profile-login/\">".__("Account Login", RBAGENCY_interact_TEXTDOMAIN)."</a>", $user_login );
							}
							
						}else{

							
							$notification_password_auto_generated = trim($rb_agency_interact_options_arr['notification_password_auto_generated']);

							if(!empty($notification_password_auto_generated)) {

								$message = str_replace('[agency_name]',get_option('blogname'),$notification_password_auto_generated);
								$message = str_replace('[first_name]',$_POST["ProfileContactNameFirst"],$notification_password_auto_generated);
								$message = str_replace('[login_url]',site_url()."/profile-login",$message);
								$message = str_replace('[username]',$user_login,$message);
								$message = str_replace('[password]',$user_pass,$message);
								$message = str_replace('[agency_email]',get_option('admin_email'),$message);
								$message = str_replace('[agency_name]',get_option('blogname'),$message);
								$message = str_replace('[domain]',site_url(),$message);
								echo nl2br($message);
							}else{
								printf( __("Thank you for registering, %s!", RBAGENCY_interact_TEXTDOMAIN), $user_login );
								echo "<br/><br/>";
								//if ($rb_agencyinteract_option_registerapproval == 1) { // automatically approve
								
								printf( __("Please check your email for your login credentials to continue your registration.", RBAGENCY_interact_TEXTDOMAIN), $user_login );
								echo "<br/><br/>";
								printf( "<a href=\"../profile-login/\">".__("Account Login", RBAGENCY_interact_TEXTDOMAIN)."</a>", $user_login );
							}
							
						}
						
					}

					/**
					printf( __("Thank you for registering, %s.", RBAGENCY_interact_TEXTDOMAIN), $user_login );
					echo "<br/>";
					//if ($rb_agencyinteract_option_registerapproval == 1) { // automatically approve
					printf( __("You may now login and continue registration. <br/>", RBAGENCY_interact_TEXTDOMAIN) );

					echo "<a href=\"".get_bloginfo("url")."/profile-login/\">". __("Account Login", RBAGENCY_interact_TEXTDOMAIN) ."</a>";
					printf( __("<br/>Your login credentials are also sent to your email.", RBAGENCY_interact_TEXTDOMAIN) );
					printf( __("<br/>(It might go into your spam folder)", RBAGENCY_interact_TEXTDOMAIN) );
					//} else { // manually approve
					//	if($profileactive == 3){
					//		printf( __("Your account is pending for approval. We will send your login once account is approved.", RBAGENCY_interact_TEXTDOMAIN) );
					//	} else {
					//		printf( __("Please check your email address. That's where you'll receive your login password.<br/> (It might go into your spam folder)", RBAGENCY_interact_TEXTDOMAIN) );
					//	}

					//}

					**/
	echo "    </p><!-- .rbalert -->\n";

	} else {


		if ( $error ) {

			echo '<script type="text/javascript">
			jQuery(document).ready(function(){

				jQuery.post("'.admin_url("admin-ajax.php").'", 
				{
					GenderID: "'.$_POST["ProfileGender"].'",
					location: "registration_form",
					action:"request_datatype_bygender_memberregister"
				})
				.done(function(data) {
					console.log(data);
					jQuery("#ProfileType-div").html(data);
				});

				jQuery(".DataTypeIDClassCheckbox :checked").each(function(){

					var idValue = jQuery(this).attr("id");
					jQuery(".CDataTypeID"+idValue).toggle(this.checked);					
				});
			});

		 </script>';

			echo "<p class=\"rbalert rberror\">". $error ."</p>\n";
		}
		// Show some admin loving.... (Admins can create)
		if ( current_user_can("create_users") && $registration ) {

	echo "    <p class=\"rbalert\">\n";
	echo "      ". __("Users can register themselves or you can manually create users here.", RBAGENCY_interact_TEXTDOMAIN);
	echo "    </p><!-- .alert -->\n";
		} elseif ( current_user_can("create_users")) {
	echo "    <p class=\"rbalert\">\n";
	echo "      ". __("Users cannot currently register themselves, but you can manually create users here.", RBAGENCY_interact_TEXTDOMAIN);
	echo "    </p><!-- .alert -->\n";
		}

	// Self Registration
	if ( $registration || current_user_can("create_users") ) {
	echo "  <header class=\"entry-header member-register\">";
	echo "  	<h1 class=\"entry-title\">". __("Join Our Team", RBAGENCY_interact_TEXTDOMAIN) ."</h1>";
	echo "  </header>";
	echo "  <div id=\"member-register\" class=\"rbform\">";
	echo "	<p class=\"rbform-description\">".__("To Join Our Team please complete the application below.", RBAGENCY_interact_TEXTDOMAIN)."</p>";
	if(!$shortcode_register){
		echo "    <form method=\"post\" action=\"". $rb_agency_interact_WPURL ."/profile-register/".$rb_agency_uri_profiletype."\">\n";
	} else {
		echo "    <form method=\"post\" action=\"".get_page_link()."\">\n";
	}	echo "       <div id=\"profile-username\" class=\"rbfield rbtext rbsingle\">\n";
		echo "   		<label for=\"profile_user_name\">". __("Username (required)", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
		echo "   		<div><input class=\"text-input\" name=\"profile_user_name\" type=\"text\" id=\"profile_user_name\" value=\""; if ( $error ) echo esc_html( $_POST['profile_user_name'], 1 ); echo "\" /></div>\n";
		echo "       </div><!-- #rofile-username -->\n";
	if ($rb_agencyinteract_option_registerconfirm == 1 && $rb_agencyinteract_option_useraccountcreation == 1) {

		echo "       <div id=\"profile-password\" class=\"rbfield rbpassword rbsingle\">\n";
		echo "   		<label for=\"profile_password\">". __("Password (required)", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
		echo "   		<div><input class=\"text-input\" name=\"profile_password\" type=\"password\" id=\"profile_password\" value=\""; if ( $error ) echo esc_html( $_POST['profile_password'], 1 ); echo "\" /></div>\n";
		echo "       </div><!-- #profile-password -->\n";
	}

	if($rb_agency_option_profilenaming == 2){
		echo "       <div id=\"profile-contact-display-name\" class=\"rbfield rbtext rbsingle\">\n";
		echo "   		<label for=\"profile_contact_display_name\">". __("Display Name", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
		echo "   		<div><input class=\"text-input\" name=\"profile_contact_display_name\" type=\"text\" id=\"profile_contact_display_name\" value=\""; if ( $error ) echo esc_html( $_POST['profile_contact_display_name'], 1 ); echo "\" /></div>\n";
		echo "       </div><!-- #profile-contact-display-name -->\n";

	}
	echo "       <div id=\"profile-first-name\" class=\"rbfield rbtext rbsingle\">\n";
	echo "   		<label for=\"profile_first_name\">". __("First Name", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "   		<div><input class=\"text-input\" name=\"profile_first_name\" type=\"text\" id=\"profile_first_name\" value=\""; if ( $error ) echo esc_html( $_POST['profile_first_name'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-first-name -->\n";

	echo "       <div id=\"profile-last-name\" class=\"rbfield rbtext rbsingle\">\n";
	echo "   		<label for=\"profile_last_name\">". __("Last Name", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "   		<div><input class=\"text-input\" name=\"profile_last_name\" type=\"text\" id=\"profile_last_name\" value=\""; if ( $error ) echo esc_html( $_POST['profile_last_name'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile_last_name -->\n";

	echo "       <div id=\"profile-email\" class=\"rbfield rbemail rbsingle\">\n";
	echo "   		<label for=\"email\">". __("E-mail (required)", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "   		<div><input class=\"text-input\" name=\"profile_email\" type=\"text\" id=\"profile_email\" value=\""; if ( $error ) echo esc_html( $_POST['profile_email'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-email -->\n";

	echo "       <div id=\"profile-gender\" class=\"rbfield rbselect rbsingle\">\n";
	echo "			<label for=\"profile_gender\">". __("Gender", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
					$query= "SELECT GenderID, GenderTitle FROM " .  table_agency_data_gender . " GROUP BY GenderTitle ";
	echo "			<div><select id='ProfileGender' name=\"ProfileGender\">";
						$queryShowGender = $wpdb->get_results($query,ARRAY_A);
						echo "<option value=''>".__("--Please Select--",RBAGENCY_interact_TEXTDOMAIN)."</option>";
						foreach($queryShowGender as $dataShowGender){
							echo "<option value=\"".$dataShowGender["GenderID"]."\" ". selected($ProfileGender ,$dataShowGender["GenderID"],false).">".$dataShowGender["GenderTitle"]."</option>";
						}
	echo "			</select></div>";
	echo "		</div><!-- #profile-gender -->\n";

	echo "	<fieldset id=\"profile-gender\" class=\"rbfield rbcheckbox rbmulti\">\n";
	echo "		<legend for=\"profile_type\">". __("Type of Profile", RBAGENCY_interact_TEXTDOMAIN) ."</legend>\n";

				//check for parentid column
				$sql = "SELECT DataTypeParentID FROM ".$wpdb->prefix."agency_data_type LIMIT 1";
				$r = $wpdb->get_results($sql);
				if(count($r) == 0){
					//create column
					$queryAlter = "ALTER TABLE " . $wpdb->prefix ."agency_data_type ADD DataTypeParentID varchar(20) default 0";
					$resultsDataAlter = $wpdb->query($queryAlter,ARRAY_A);
				}

				
	echo "		<div id='ProfileType-div'>";
	
				$ProfileTypeArray = array();
				$query3 = "SELECT * FROM " . table_agency_data_type . " WHERE DataTypeParentID = 0 AND DataTypeGenderID = 0 ORDER BY DataTypeTitle";
				$results3 = $wpdb->get_results($query3,ARRAY_A);
				$count3 =  $wpdb->num_rows;
				
				   $ptype_arr = isset($_POST["ProfileType"]) && !empty($_POST["ProfileType"])?$_POST["ProfileType"]: array();
				 if($ProfileGender >= 1 ){
					$query3 = "SELECT * FROM " . table_agency_data_type . " WHERE DataTypeParentID = 0 AND (DataTypeGenderID = {$ProfileGender} OR DataTypeGenderID = 0) ORDER BY DataTypeTitle";
					$results3 = $wpdb->get_results($query3,ARRAY_A);
					$count3 =  $wpdb->num_rows;
					
					foreach($results3 as $data3) {


						$rb_agency_uri_profiletype = ucfirst($rb_agency_uri_profiletype);
							$profiletypeid = $wpdb->get_var($wpdb->prepare("SELECT DataTypeID FROM " . table_agency_data_type . " WHERE DataTypeTitle = %s",$rb_agency_uri_profiletype));

							if(!empty($rb_agency_uri_profiletype) && isset($profiletypeid)){
								if($profiletypeid ==  $data3["DataTypeID"]){
										echo "<div><label><input type=\"checkbox\" checked='checked' name=\"ProfileType[]\" value=\"" . $data3['DataTypeID'] . "\" class=\"DataTypeIDClassCheckbox\" id=".$data3['DataTypeID']." myparent=".$data3['DataTypeParentID']."  /><span> " . $data3['DataTypeTitle'] . "</span></label></div>";
								}
							} else {
								echo "<div><label><input type=\"checkbox\" ".(in_array($data3["DataTypeID"],$ptype_arr)?"checked='checked'":"")." name=\"ProfileType[]\" class=\"DataTypeIDClassCheckbox\" id=".$data3['DataTypeID']." myparent=".$data3['DataTypeParentID']."  value=\"" . $data3['DataTypeID'] . "\"  /><span> " . $data3['DataTypeTitle'] . "</span></label></div>";
							}
							do_action('rb_get_profile_type_childs_checkbox_ajax_register_display',$data3["DataTypeID"]);
						
					
					} 
				 }else{
					echo "	Please select your gender first</div>";
				 }
	echo '
			<script>
				jQuery( "#ProfileGender" ).on("change",function() {
				
				var userGenderID = jQuery(this).val();
				//var userGenderText = jQuery( "#ProfileGender option:selected").text();
						
					jQuery.post("'.admin_url("admin-ajax.php").'", 
						{
						GenderID: userGenderID,
						location: "registration_form",
						action:"request_datatype_bygender_memberregister"
						})
					.done(function(data) {
						console.log(data);
						jQuery("#ProfileType-div").html(data);
					});
					return false;
				});

				
			</script>
		';

	echo '<script type="text/javascript">
			jQuery(document).ready(function(){

				jQuery(".DataTypeIDClassCheckbox").live("click",function(){
					var idValue = jQuery(this).attr("id");
					jQuery(".CDataTypeID"+idValue).toggle(this.checked);
					
				});
			});

		 </script>';
		
	
	echo "</fieldset><!-- #profile-gender -->\n";

	echo "      <div id=\"profile-agree\" class=\"rbfield rbtext rbsingle\">\n";
					$profile_agree = get_the_author_meta("profile_agree", $current_user->ID );
	echo "  		<label></label>\n";
	echo "  		<div><input type=\"checkbox\" name=\"profile_agree\" value=\"yes\" /> ". sprintf(__("I agree to the %s terms of service", RBAGENCY_interact_TEXTDOMAIN), "<a href=\"".$rb_agency_option_model_toc ."\" target=\"_blank\">") ."</a></div>\n";
	echo "      </div><!-- #profile-agree -->\n";
    if($rb_agency_interact_options_arr['rb_agencyinteract_site_key']){
        echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        echo '<div class="rbfield rbsingle"><div class="rbfield rbsubmit rbsingle g-recaptcha" data-sitekey="'.$rb_agency_interact_options_arr['rb_agencyinteract_site_key'].'"></div></div>';
    }
	echo "      <div id=\"profile-submit\" class=\"rbfield rbsubmit rbsingle\">\n";
	echo "   		<input name=\"adduser\" type=\"submit\" id=\"addusersub\" class=\"submit button\" value='". __("Register", RBAGENCY_interact_TEXTDOMAIN) ."'/>";

					// if ( current_user_can("create_users") ) { _e("Add User", RBAGENCY_interact_TEXTDOMAIN); } else { _e("Register", RBAGENCY_interact_TEXTDOMAIN); }echo "\" />\n";
					wp_nonce_field("add-user");

	echo "   		<input name=\"action\" type=\"hidden\" id=\"action\" value=\"adduser\" />\n";
	echo "       </div><!-- #profile-submit -->\n";
	echo "   </form>\n";
	echo "   </div><!-- .rbform -->\n";

			}

}

if(!$registration){echo "<p class='alert'>".__("The administrator currently disabled the registration.", RBAGENCY_interact_TEXTDOMAIN)."<p>"; }

echo "  </div><!-- #content -->\n";
echo "</div><!-- #container -->\n";

// Get Sidebar
	$LayoutType = "";
	if ($rb_agencyinteract_option_profilemanage_sidebar) {
		$LayoutType = "profile";
		get_sidebar();
	}

// Call Footer
	if(!$shortcode_register){
		echo $rb_footer = RBAgency_Common::rb_footer();
	}
?>
