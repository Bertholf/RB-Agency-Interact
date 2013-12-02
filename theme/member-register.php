<?php
// *************************************************************************************************** //
// Prepare Page

	/* Load registration file. */
	//require_once( ABSPATH . WPINC . '/registration.php' );	
	
	/* Get Options */
	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');

	//Sidebar
	$rb_agency_interact_option_profilemanage_sidebar = $rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar'];
	if($rb_agency_interact_option_profilemanage_sidebar){
		$columnWidth = "nine";
	} else {
		$columnWidth = "twelve";
	}
	
	//Facebook Integration
	$rb_agency_interact_option_fb_app_id = $rb_agency_interact_options_arr['rb_agencyinteract_option_fb_app_id'];
	$rb_agency_interact_option_fb_app_secret = $rb_agency_interact_options_arr['rb_agencyinteract_option_fb_app_secret'];
	$rb_agency_interact_option_fb_app_register_uri = $rb_agency_interact_options_arr['rb_agencyinteract_option_fb_app_register_uri'];
    $rb_agency_interact_option_fb_registerallow = $rb_agency_interact_options_arr['rb_agencyinteract_option_fb_registerallow'];

    //+Registration
    // - show/hide registration for Agent/Producers
	$rb_agency_interact_option_registerallowAgentProducer = $registration['rb_agencyinteract_option_registerallowAgentProducer'];

	// - show/hide  self-generate password
	$rb_agency_interact_option_registerconfirm = (int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerconfirm'];
	
   	if($rb_agency_interact_option_fb_registerallow == 1){
	 	if(!class_exists("FacebookApiException")){   
	   		require_once(ABSPATH."wp-content/plugins/".rb_agency_interact_TEXTDOMAIN."/tasks/facebook.php");
	 	}
    }

	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );	
	
	define('FACEBOOK_APP_ID', $rb_agency_interact_option_fb_app_id);
	define('FACEBOOK_SECRET', $rb_agency_interact_option_fb_app_secret);
	
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
		
		if ($rb_agency_interact_option_registerconfirm == 1) {
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
		if ( !is_email($userdata['user_email'], true)) {
			$error .= __("You must enter a valid email address.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( email_exists($userdata['user_email'])) {
			$error .= __("Sorry, that email address is already used!<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( $_POST['profile_agree'] <> "yes") {
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
			update_usermeta($new_user, 'rb_agency_interact_profiletype', $new_user_type);
			update_usermeta($new_user, 'rb_agency_interact_pgender', $gender);
			
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
			
			add_user_meta($new_user, 'rb_agency_new_registeredUser',$arr);			
			
			// Log them in if no confirmation required.			
			if ($rb_agency_interact_option_registerconfirm == 1) {

				global $error;
				
				$login = wp_login( $user_login, $user_pass );
				$login = wp_signon( array( 'user_login' => $user_login, 'user_password' => $user_pass, 'remember' => 1 ), false );	
			}				
				// Notify admin and user
				wp_new_user_notification($new_user, $user_pass);
		}
		
		// Log them in if no confirmation required.
		if ($rb_agency_interact_option_registerconfirm == 1) {
			if($login){
				header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
			}
		}	
	}
 

// *************************************************************************************************** //
// Prepare Page

    get_header();

	echo "<div id=\"primary\" class=\"".$columnWidth." column rb-agency-interact rb-agency-interact-register\">\n";
	echo "  <div id=\"content\">\n";
   
	// ****************************************************************************************** //
	// Already logged in 

	if ( is_user_logged_in() && !current_user_can( 'create_users' ) ) {

	echo "    <p class=\"log-in-out alert\">\n";
	echo "		". __("You are currently logged in as .", rb_agency_interact_TEXTDOMAIN) ." <a href=\"/profile-member/\" title=\"". $login->display_name ."\">". $login->display_name ."</a>\n";
				//printf( __("You are logged in as <a href="%1$s" title="%2$s">%2$s</a>.  You don\'t need another account.', rb_agency_interact_TEXTDOMAIN), get_author_posts_url( $curauth->ID ), $user_identity );
	echo "		 <a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __("Log out of this account", rb_agency_interact_TEXTDOMAIN) ."\">". __("Log out", rb_agency_interact_TEXTDOMAIN) ." &raquo;</a>\n";
	echo "    </p><!-- .alert -->\n";

	} elseif ( $new_user ) {

	echo "    <p class=\"alert\">\n";
				if ( current_user_can( 'create_users' ) )
					printf( __("A user account for %1$s has been created.", rb_agency_interact_TEXTDOMAIN), $_POST['user-name'] );
				else 
					printf( __("Thank you for registering, %1$s.", rb_agency_interact_TEXTDOMAIN), $_POST['user-name'] );
					echo "<br/>";
					printf( __("Please check your email address. That's where you'll receive your login password.<br/> (It might go into your spam folder)", rb_agency_interact_TEXTDOMAIN) );
	echo "    </p><!-- .alert -->\n";

	} else {

		if ( $error ) {
			echo "<p class=\"error\">". $error ."</p>\n";
		}
		// Show some admin loving.... (Admins can create)
		if ( current_user_can("create_users") && $registration ) {

	echo "    <p class=\"alert\">\n";
	echo "      ". __("Users can register themselves or you can manually create users here.", rb_agency_interact_TEXTDOMAIN);
	echo "    </p><!-- .alert -->\n";
		} elseif ( current_user_can("create_users")) {
	echo "    <p class=\"alert\">\n";
	echo "      ". __("Users cannot currently register themselves, but you can manually create users here.", rb_agency_interact_TEXTDOMAIN);
	echo "    </p><!-- .alert -->\n";
		}

	// Self Registration
	if ( $registration || current_user_can("create_users") ) {
	echo "  <header class=\"entry-header\">";
	echo "  	<h1 class=\"entry-title\">Join Our Team</h1>";
	echo "  </header>";
	echo "  <div id=\"member-register\" class=\"rbform\">";
	echo "	<p class=\"rbform-description\">To Join Our Team please complete the application below.</p>";
	echo "  <form method=\"post\" action=\"". $rb_agency_interact_WPURL ."/profile-register/talent\">\n";    				
	echo "       <div id=\"profile-username\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"profile_user_name\">". __("Username (required)", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_user_name\" type=\"text\" id=\"profile_user_name\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_user_name'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #rofile-username -->\n";
			
	if ($rb_agency_interact_option_registerconfirm == 1) {
	echo "       <div id=\"profile-password\" class=\"rbfield rbpassword rbsingle\">\n";
	echo "       	<label for=\"profile_password\">". __("Password (required)", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_password\" type=\"password\" id=\"profile_password\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_password'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-password -->\n";
	}
				
	echo "       <div id=\"profile-first-name\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"profile_first_name\">". __("First Name", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_first_name\" type=\"text\" id=\"profile_first_name\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_first_name'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-first-name -->\n";
				
	echo "       <div id=\"profile-last-name\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"profile_last_name\">". __("Last Name", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_last_name\" type=\"text\" id=\"profile_last_name\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_last_name'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile_last_name -->\n";
				
	echo "       <div id=\"profile-email\" class=\"rbfield rbemail rbsingle\">\n";
	echo "       	<label for=\"email\">". __("E-mail (required)", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_email\" type=\"text\" id=\"profile_email\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_email'], 1 ); echo "\" /></div>\n";
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
	echo "      	<div><input type=\"checkbox\" name=\"profile_agree\" value=\"yes\" /> ". sprintf(__("I agree to the %s terms of service", rb_agency_interact_TEXTDOMAIN), "<a href=\"/terms-of-use/\" target=\"_blank\">") ."</a></div>\n";
	echo "      </div><!-- #profile-agree -->\n";
 
	echo "      <div id=\"profile-submit\" class=\"rbfield rbsubmit rbsingle\">\n";
	echo "       	<input name=\"adduser\" type=\"submit\" id=\"addusersub\" class=\"submit button\" value='Register'/>";

					// if ( current_user_can("create_users") ) {  _e("Add User", rb_agency_interact_TEXTDOMAIN); } else {  _e("Register", rb_agency_interact_TEXTDOMAIN); } echo "\" />\n";
					
					wp_nonce_field("add-user");
					$fb_app_register_uri = "";

					if($rb_agency_interact_option_fb_app_register_uri == 1){
						$fb_app_register_uri = $rb_agency_interact_option_fb_app_register_uri;
					}else{
						$fb_app_register_uri = network_site_url("/")."profile-register/";
					}

					// Allow facebook login/registration
					if($rb_agency_interact_option_fb_registerallow ==1){
						echo "<div>\n";
						echo "<span>Or</span>\n";
						echo "<div id=\"fb_RegistrationForm\">\n";
						if ($rb_agency_interact_option_registerconfirm == 1) {	 // With custom password fields
							echo "<iframe src=\"https://www.facebook.com/plugins/registration?client_id=".$rb_agency_interact_option_fb_app_id."&redirect_uri=".$fb_app_register_uri."&fields=[ {'name':'name'}, {'name':'email'}, {'name':'location'}, {'name':'gender'}, {'name':'birthday'}, {'name':'username',  'description':'Username',  'type':'text'},{'name':'password'},{'name':'tos','description':'I agree to the terms of service','type':'checkbox'}]\"		 
								  scrolling=\"auto\"
								  frameborder=\"no\"
								  style=\"border:none\"
								  allowTransparency=\"true\"
								  width=\"100%\"
								  height=\"330\">
							</iframe>";
						}else{
							echo "<iframe src=\"https://www.facebook.com/plugins/registration?client_id=".$rb_agency_interact_option_fb_app_id."&redirect_uri=".$fb_app_register_uri."&fields=[ {'name':'name'}, {'name':'email'}, {'name':'location'}, {'name':'gender'}, {'name':'birthday'}, {'name':'username',  'description':'Username',  'type':'text'},{'name':'password'},{'name':'tos','description':'I agree to the terms of service','type':'checkbox'}]\"		 
								  scrolling=\"auto\"
								  frameborder=\"no\"
								  style=\"border:none\"
								  allowTransparency=\"true\"
								  width=\"100%\"
								  height=\"330\">
							</iframe>";
						}
					
						echo "</div>\n";
						
					}
					
	echo "       	<input name=\"action\" type=\"hidden\" id=\"action\" value=\"adduser\" />\n";
	echo "       </div><!-- #profile-submit -->\n";
	// Facebook connect
	?>
    
         
     
<?php	
	echo "   </form>\n";
	echo "   </div><!-- .rbform -->\n";

			}

}

if(!$registration){ echo "<p class='alert'>The administrator currently disabled the registration.<p>"; }

echo "  </div><!-- #content -->\n";
echo "</div><!-- #container -->\n";
   
// Get Sidebar 
	$LayoutType = "";
	if ($rb_agency_interact_option_profilemanage_sidebar) {
		$LayoutType = "profile";
		get_sidebar(); 
	}
	
// Get Footer
get_footer();
?>
