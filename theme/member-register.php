<?php
// *************************************************************************************************** //
// Prepare Page
    global $wpdb;
	/* Load registration file. */
	//require_once( ABSPATH . WPINC . '/registration.php' );	
	
	/* Get Options */
	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
    $rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_model_toc = isset($rb_agency_options_arr['rb_agency_option_agency_model_toc'])?$rb_agency_options_arr['rb_agency_option_agency_model_toc']: "/models-terms-of-conditions";
	$rb_agencyinteract_option_registerapproval = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval']:"";
	//Sidebar
	$rb_agencyinteract_option_profilemanage_sidebar = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar']:0;
	if($rb_agencyinteract_option_profilemanage_sidebar){
		$column_class = primary_class();
	} else {
		$column_class = fullwidth_class();
	}

	//+Registration
	// - show/hide registration for Agent/Producers
	$rb_agencyinteract_option_registerallowAgentProducer = isset($registration['rb_agencyinteract_option_registerallowAgentProducer'])?$registration['rb_agencyinteract_option_registerallowAgentProducer']:0;

	// - show/hide  self-generate password
	$rb_agencyinteract_option_registerconfirm = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerconfirm'])?(int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerconfirm']:0;

	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );

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
			
	function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
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
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'adduser' ) {
		
		$user_login = $_POST['profile_user_name'];
		$first_name = $_POST['profile_first_name'];
		$last_name  = $_POST['profile_last_name'];
		$user_email = $_POST['profile_email'];
		$ProfileGender = $_POST['ProfileGender'];
		$user_pass  = NULL;
		
		if ($rb_agencyinteract_option_registerconfirm == 1) {
			$user_pass = $_POST['profile_password'];
		} else {
			$user_pass = wp_generate_password();
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
		
		if (!$userdata['user_login']) {
			$error .= __("A username is required for registration.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( username_exists($userdata['user_login'])) {
			$error .= __("Sorry, that username already exists!<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( !is_email($userdata['user_email'])) {
			$error .= __("You must enter a valid email address.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( email_exists($userdata['user_email'])) {
			$error .= __("Sorry, that email address is already used!<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( isset($_POST['profile_agree']) &&  $_POST['profile_agree'] <> "yes") {
			$error .= __("You must agree to the terms and conditions to register.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
	
		// Bug Free!
		if($have_error == false) {
			$new_user = wp_insert_user( $userdata );
			$new_user_type = array();
			$new_user_type =implode(",", $_POST['ProfileType']);
			$gender = $_POST['ProfileGender'];
			
			// Model or Client
			update_user_meta($new_user, 'rb_agency_interact_profiletype', $new_user_type);
			update_user_meta($new_user, 'rb_agency_interact_pgender', $gender);
			
			// Insert to table_agency_profile
			$wpdb->query($wpdb->prepare("INSERT INTO ".table_agency_profile."
				(ProfileContactNameFirst,ProfileContactNameLast,ProfileGender,ProfileContactEmail,ProfileIsActive,ProfileUserLinked) VALUES(%s,%s,%s,%s,%s,%s)",
				 $first_name,
				 $last_name,
				 $ProfileGender,
				 $user_email,
				 3,
				 $new_user
				));
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
			add_user_meta($new_user, 'rb_agency_new_registeredUser',$arr);
			
			// Log them in if no confirmation required.			
			if ($rb_agencyinteract_option_registerapproval == 1) {

				global $error;
				
				//$login = wp_login( $user_login, $user_pass );
				$login = wp_signon( array( 'user_login' => $user_login, 'user_password' => $user_pass, 'remember' => 1 ), false );	
					// Notify admin and user
					wp_new_user_notification($new_user,$user_pass);
			
			}else{
					wp_new_user_notification_pending($new_user);
			}
					
			
		}
		
		// Log them in if no confirmation required.
		if ($rb_agencyinteract_option_registerapproval == 1) {
			if($login){
				header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
			}
		}	
	}
 

// *************************************************************************************************** //
// Prepare Page

	get_header();

	echo "<div id=\"primary\" class=\"".$column_class." column rb-agency-interact rb-agency-interact-register\">\n";
	echo "  <div id=\"content member-register\">\n";
   
    // ****************************************************************************************** //
	// Already logged in 

	if ( is_user_logged_in() && !current_user_can( 'create_users' ) ) {

	echo "    <p class=\"log-in-out rbalert\">\n";
	echo "		". __("You are currently logged in as .", rb_agency_interact_TEXTDOMAIN) ." <a href=\"/profile-member/\" title=\"". $login->display_name ."\">". $login->display_name ."</a>\n";
				//printf( __("You are logged in as <a href="%1$s" title="%2$s">%2$s</a>.  You don\'t need another account.', rb_agency_interact_TEXTDOMAIN), get_author_posts_url( $curauth->ID ), $user_identity );
	echo "		 <a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __("Log out of this account", rb_agency_interact_TEXTDOMAIN) ."\">". __("Log out", rb_agency_interact_TEXTDOMAIN) ." &raquo;</a>\n";
	echo "    </p><!-- .rbalert -->\n";

	} elseif ( isset($new_user) ) {

	echo "    <p class=\"rbalert\">\n";
				if ( current_user_can( 'create_users' ) )
					printf( __("A user account for %1$s has been created.", rb_agency_interact_TEXTDOMAIN), $_POST['profile_user_name'] );
				else 
					printf( __("Thank you for registering, %1$s.", rb_agency_interact_TEXTDOMAIN), $_POST['profile_user_name'] );
					echo "<br/>";
					if ($rb_agencyinteract_option_registerapproval == 1) {
					printf( __("Please check your email address. That's where you'll receive your login password.<br/> (It might go into your spam folder)", rb_agency_interact_TEXTDOMAIN) );
					}else{
					printf( __("Your account is pending for approval. We will send your login once account is approved.", rb_agency_interact_TEXTDOMAIN) );
					
					}
	echo "    </p><!-- .rbalert -->\n";

	} else {

		if ( $error ) {
			echo "<p class=\"rberror\">". $error ."</p>\n";
		}
		// Show some admin loving.... (Admins can create)
		if ( current_user_can("create_users") && $registration ) {

	echo "    <p class=\"rbalert\">\n";
	echo "      ". __("Users can register themselves or you can manually create users here.", rb_agency_interact_TEXTDOMAIN);
	echo "    </p><!-- .alert -->\n";
		} elseif ( current_user_can("create_users")) {
	echo "    <p class=\"rbalert\">\n";
	echo "      ". __("Users cannot currently register themselves, but you can manually create users here.", rb_agency_interact_TEXTDOMAIN);
	echo "    </p><!-- .alert -->\n";
		}

	// Self Registration
	if ( $registration || current_user_can("create_users") ) {
	echo "  <header class=\"entry-header member-register\">";
	echo "  	<h1 class=\"entry-title\">Join Our Team</h1>";
	echo "  </header>";
	echo "  <div id=\"member-register\" class=\"rbform\">";
	echo "	<p class=\"rbform-description\">To Join Our Team please complete the application below.</p>";
	echo "  <form method=\"post\" action=\"". $rb_agency_interact_WPURL ."/profile-register/talent\">\n";    				
	echo "       <div id=\"profile-username\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"profile_user_name\">". __("Username (required)", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_user_name\" type=\"text\" id=\"profile_user_name\" value=\""; if ( $error ) echo esc_html( $_POST['profile_user_name'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #rofile-username -->\n";
			
	if ($rb_agencyinteract_option_registerconfirm == 1) {
	echo "       <div id=\"profile-password\" class=\"rbfield rbpassword rbsingle\">\n";
	echo "       	<label for=\"profile_password\">". __("Password (required)", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_password\" type=\"password\" id=\"profile_password\" value=\""; if ( $error ) echo esc_html( $_POST['profile_password'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-password -->\n";
	}

	echo "       <div id=\"profile-first-name\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"profile_first_name\">". __("First Name", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_first_name\" type=\"text\" id=\"profile_first_name\" value=\""; if ( $error ) echo esc_html( $_POST['profile_first_name'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-first-name -->\n";

	echo "       <div id=\"profile-last-name\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"profile_last_name\">". __("Last Name", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_last_name\" type=\"text\" id=\"profile_last_name\" value=\""; if ( $error ) echo esc_html( $_POST['profile_last_name'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile_last_name -->\n";

	echo "       <div id=\"profile-email\" class=\"rbfield rbemail rbsingle\">\n";
	echo "       	<label for=\"email\">". __("E-mail (required)", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_email\" type=\"text\" id=\"profile_email\" value=\""; if ( $error ) echo esc_html( $_POST['profile_email'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-email -->\n";

	echo "       <div id=\"profile-gender\" class=\"rbfield rbselect rbsingle\">\n";
	echo "			<label for=\"profile_gender\">". __("Gender", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
					$query= "SELECT GenderID, GenderTitle FROM " .  table_agency_data_gender . " GROUP BY GenderTitle ";
	echo "			<div><select id='ProfileGender' name=\"ProfileGender\">";
						$queryShowGender = mysql_query($query);
						echo "<option value=''>--Please Select--</option>";
						while($dataShowGender = mysql_fetch_assoc($queryShowGender)){
							echo "<option value=\"".$dataShowGender["GenderID"]."\" ". selected($ProfileGender ,$dataShowGender["GenderID"],false).">".$dataShowGender["GenderTitle"]."</option>";
						}
	echo "			</select></div>";
	echo "	  </div><!-- #profile-gender -->\n";

	echo "	<fieldset id=\"profile-gender\" class=\"rbfield rbcheckbox rbmulti\">\n";
	echo "		<legend for=\"profile_type\">". __("Type of Profile", rb_agency_interact_TEXTDOMAIN) ."</legend>\n";
				$ProfileTypeArray = array();
				$query3 = "SELECT * FROM " . table_agency_data_type . " ORDER BY DataTypeTitle";
				$results3 = mysql_query($query3);
				$count3 = mysql_num_rows($results3);
	echo "		<div>";
				while ($data3 = mysql_fetch_array($results3)) {
					echo "<div><label><input type=\"checkbox\" name=\"ProfileType[]\" value=\"" . $data3['DataTypeID'] . "\" id=\"ProfileType[]\" /><span> " . $data3['DataTypeTitle'] . "</span></label></div>";
				}
	echo "		</div>";
	echo "</fieldset><!-- #profile-gender -->\n";
	
	echo "      <div id=\"profile-agree\" class=\"rbfield rbtext rbsingle\">\n";
					$profile_agree = get_the_author_meta("profile_agree", $current_user->ID );
	echo "      	<label></label>\n";
	echo "      	<div><input type=\"checkbox\" name=\"profile_agree\" value=\"yes\" /> ". sprintf(__("I agree to the %s terms of service", rb_agency_interact_TEXTDOMAIN), "<a href=\"".$rb_agency_option_model_toc ."\" target=\"_blank\">") ."</a></div>\n";
	echo "      </div><!-- #profile-agree -->\n";
 
	echo "      <div id=\"profile-submit\" class=\"rbfield rbsubmit rbsingle\">\n";
	echo "       	<input name=\"adduser\" type=\"submit\" id=\"addusersub\" class=\"submit button\" value='Register'/>";

					// if ( current_user_can("create_users") ) {  _e("Add User", rb_agency_interact_TEXTDOMAIN); } else {  _e("Register", rb_agency_interact_TEXTDOMAIN); } echo "\" />\n";
					wp_nonce_field("add-user");

	echo "       	<input name=\"action\" type=\"hidden\" id=\"action\" value=\"adduser\" />\n";
	echo "       </div><!-- #profile-submit -->\n";
	echo "   </form>\n";
	echo "   </div><!-- .rbform -->\n";

			}

}

if(!$registration){ echo "<p class='alert'>The administrator currently disabled the registration.<p>"; }

echo "  </div><!-- #content -->\n";
echo "</div><!-- #container -->\n";
   
// Get Sidebar 
	$LayoutType = "";
	if ($rb_agencyinteract_option_profilemanage_sidebar) {
		$LayoutType = "profile";
		get_sidebar(); 
	}
	
// Get Footer
get_footer();
?>
