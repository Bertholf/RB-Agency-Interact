<?php
// *************************************************************************************************** //
// Prepare Page

	/* Load registration file. */
	//require_once( ABSPATH . WPINC . '/registration.php' );
	/* Get Options */
	$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
	$rb_agency_option_model_toc = isset($rb_agency_options_arr['rb_agency_option_agency_model_toc'])?$rb_agency_options_arr['rb_agency_option_agency_model_toc']: "/models-terms-of-conditions";
	

	//Sidebar
	$rb_agencyinteract_option_profilemanage_sidebar = $rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar'];
	if($rb_agencyinteract_option_profilemanage_sidebar){
		$column_class = primary_class();
	} else {
		$column_class = fullwidth_class();
	}

	    //+Registration
	    // - show/hide registration for Agent/Producers
		$rb_agencyinteract_option_registerallowAgentProducer = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerallowAgentProducer'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_registerallowAgentProducer']:0;

		// - show/hide  self-generate password
		$rb_agencyinteract_option_registerconfirm = (int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerconfirm'];

		$rb_agencyinteract_option_registerapproval = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval']:"";
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
		if (!is_email($userdata['user_email'])) {
			$error .= __("You must enter a valid email address.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( email_exists($userdata['user_email'])) {
			$error .= __("Sorry, that email address is already used!<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		
		if ( empty($_POST['profile_company'])) {
			$error .= __("Company is required.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( empty($_POST['profile_website'])) {
			$error .= __("website is required.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( empty($_POST['profile_address'])) {
			$error .= __("Address is required.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( empty($_POST['profile_city'])) {
			$error .= __("City is required.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( empty($_POST['profile_state'])) {
			$error .= __("State is required.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( empty($_POST['profile_zip'])) {
			$error .= __("Zip is required.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
		if ( empty($_POST['profile_country'])) {
			$error .= __("Country is required.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}



		if ( $_POST['profile_agree'] <> "yes") {
			$error .= __("You must agree to the terms and conditions to register.<br />", rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
		}
	
		// Bug Free!
		if($have_error == false){
			$new_user = wp_insert_user( $userdata );
			$new_user_type = array();
			$new_user_type =implode(",", $_POST['ProfileType']);
			$gender = $_POST['ProfileGender'];
			
			$data = array();
			$data['company'] = $_POST['profile_company'];
			$data['website'] = $_POST['profile_website'];
			$data['address'] = $_POST['profile_address'];
			$data['city'] = $_POST['profile_city'];
			$data['state'] = $_POST['profile_state'];
			$data['zip'] = $_POST['profile_zip'];
			$data['country'] = $_POST['profile_country'];
						
			// Model or Client
			update_user_meta($new_user, 'rb_agency_interact_clientdata', $data);
			
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
			
			//  Log them in if register auto approval		
			if ($rb_agencyinteract_option_registerapproval == 1) {

				global $error;
				
				//$login = wp_login( $user_login, $user_pass );
				$login = wp_signon( array( 'user_login' => $user_login, 'user_password' => $user_pass, 'remember' => 1 ), false );	
			}				
				// Notify admin and user
				wp_new_user_notification($new_user);	
			
		}
		
		// Log them in if register auto approval.
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
	echo "  <div id=\"content client-register\">\n";

   
		// ****************************************************************************************** //
		// Already logged in 
			
		if ( is_user_logged_in() && !current_user_can( 'create_users' ) ) {

	echo "    <p class=\"log-in-out rbalert\">\n";
	echo "		". __("You are currently logged in as .", rb_agency_interact_TEXTDOMAIN) ." <a href=\"/profile-member/\" title=\"". $login->display_name ."\">". $login->display_name ."</a>\n";
				//printf( __("You are logged in as <a href="%1$s" title="%2$s">%2$s</a>.  You don\'t need another account.', rb_agency_interact_TEXTDOMAIN), get_author_posts_url( $curauth->ID ), $user_identity );
	echo "		 <a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __("Log out of this account", rb_agency_interact_TEXTDOMAIN) ."\">". __("Log out", rb_agency_interact_TEXTDOMAIN) ." &raquo;</a>\n";
	echo "    </p><!-- .alert -->\n";


		} elseif ( $new_user ) {

	echo "    <p class=\"rbalert\">\n";
				if ( current_user_can( 'create_users' ) )
					printf( __("A user account for %1$s has been created.", rb_agency_interact_TEXTDOMAIN), $_POST['user-name'] );
				else 
					printf( __("Thank you for registering, %1$s.", rb_agency_interact_TEXTDOMAIN), $_POST['user-name'] );
					echo "<br/>";
					if ($rb_agencyinteract_option_registerapproval == 1) {
					printf( __("Please check your email address. That's where you'll receive your login password.<br/> (It might go into your spam folder)", rb_agency_interact_TEXTDOMAIN) );
					}else{
					printf( __("Your account is pending for approval. We will notify once your account is approved.", rb_agency_interact_TEXTDOMAIN) );
					
					}
	echo "    </p><!-- .alert -->\n";

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
	echo "  <header class=\"entry-header client-register\">";
	echo "  	<h1 class=\"entry-title\">Join Our Team</h1>";
	echo "  </header>";
	echo "  <div id=\"client-register\" class=\"rbform\">";
	echo "	  <p class=\"rbform-description\">To Join Our Team please complete the application below.</p>";
	echo "    <form method=\"post\" action=\"". $rb_agency_interact_WPURL ."/profile-register/client\">\n";
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

	echo "       <div id=\"profile-company\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"company\">". __("Company", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_company\" type=\"text\" id=\"profile_email\" value=\""; if ( $error ) echo esc_html( $_POST['profile_company'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-company -->\n";

	echo "       <div id=\"profile-website\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"website\">". __("Website", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_website\" type=\"text\" id=\"profile_email\" value=\""; if ( $error ) echo esc_html( $_POST['profile_website'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-website -->\n";
	
	echo "       <div id=\"profile-street-address\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"street-address\">". __("Street Address", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_address\" type=\"text\" value=\""; if ( $error ) echo esc_html( $_POST['profile_address'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-street-address -->\n";

	echo "       <div id=\"profile-city\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"city\">". __("City", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_city\" type=\"text\" id=\"profile_email\" value=\""; if ( $error ) echo esc_html( $_POST['profile_city'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-city -->\n";	

	echo "       <div id=\"profile-state\" class=\"rbfield rbselect rbsingle\">\n";
	echo "       	<label for=\"state\">". __("State", rb_agency_interact_TEXTDOMAIN) ."</label>\n";

	$state_list = array('AL'=>"Alabama", 'AK'=>"Alaska", 'AZ'=>"Arizona", 'AR'=>"Arkansas", 'CA'=>"California",  'CO'=>"Colorado",  
						'CT'=>"Connecticut", 'DE'=>"Delaware", 'DC'=>"District Of Columbia", 'FL'=>"Florida",  
						'GA'=>"Georgia", 'HI'=>"Hawaii", 'ID'=>"Idaho", 'IL'=>"Illinois", 'IN'=>"Indiana",  
						'IA'=>"Iowa", 'KS'=>"Kansas", 'KY'=>"Kentucky", 'LA'=>"Louisiana", 'ME'=>"Maine",  
						'MD'=>"Maryland", 'MA'=>"Massachusetts", 'MI'=>"Michigan", 'MN'=>"Minnesota",  
						'MS'=>"Mississippi", 'MO'=>"Missouri", 'MT'=>"Montana", 'NE'=>"Nebraska", 'NV'=>"Nevada",
						'NH'=>"New Hampshire", 'NJ'=>"New Jersey", 'NM'=>"New Mexico", 'NY'=>"New York",
						'NC'=>"North Carolina", 'ND'=>"North Dakota", 'OH'=>"Ohio", 'OK'=>"Oklahoma",  
						'OR'=>"Oregon", 'PA'=>"Pennsylvania", 'RI'=>"Rhode Island", 'SC'=>"South Carolina",  
						'SD'=>"South Dakota", 'TN'=>"Tennessee", 'TX'=>"Texas", 'UT'=>"Utah",  
						'VT'=>"Vermont", 'VA'=>"Virginia", 'WA'=>"Washington", 'WV'=>"West Virginia",  
						'WI'=>"Wisconsin", 'WY'=>"Wyoming");				
   
   echo '<div><select name="profile_state">';
   
   echo '<option value="">Choose One</option>';
   
   foreach($state_list as $key => $val){
		
		$selected = (isset($_POST['profile_state'])) ? $_POST['profile_state'] : "";
		
		echo '<option value="'.$key.'" '. selected($key ,$selected, false). '>'.$val.'</option>';
   
   }
	echo "    </select></div>";

	echo "       </div><!-- #profile-state -->\n";

	echo "       <div id=\"profile-zip\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"zip\">". __("Zip", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_zip\" type=\"text\" id=\"profile_email\" value=\""; if ( $error ) echo esc_html( $_POST['profile_zip'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-zip -->\n";

	echo "       <div id=\"profile-country\" class=\"rbfield rbtext rbsingle\">\n";
	echo "       	<label for=\"country\">". __("Country", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "       	<div><input class=\"text-input\" name=\"profile_country\" type=\"text\" id=\"profile_email\" value=\""; if ( $error ) echo esc_html( $_POST['profile_country'], 1 ); echo "\" /></div>\n";
	echo "       </div><!-- #profile-country -->\n";

	
	echo "       <div id=\"profile-argee\" class=\"rbfield rbcheckbox rbsingle\">\n";
					$profile_agree = get_the_author_meta("profile_agree", $current_user->ID );
	echo "       	<label></label>\n";
	echo "       	<div><input type=\"checkbox\" name=\"profile_agree\" value=\"yes\" /> ". sprintf(__("I agree to the %s terms of service", rb_agency_interact_TEXTDOMAIN), "<a href=\"".$rb_agency_option_model_toc ."\" target=\"_blank\">") ."</a></div>\n";
	echo "       </div><!-- #profile-agree -->\n";
 
	echo "       <div id=\"profile-submit\" class=\"rbfield rbsubmit rbsingle\">\n";
	echo "       	<input name=\"adduser\" type=\"submit\" id=\"addusersub\" class=\"submit button\" value='Register'/>";

					// if ( current_user_can("create_users") ) {  _e("Add User", rb_agency_interact_TEXTDOMAIN); } else {  _e("Register", rb_agency_interact_TEXTDOMAIN); } echo "\" />\n";
					
					wp_nonce_field("add-user");

	echo "       	<input name=\"action\" type=\"hidden\" id=\"action\" value=\"adduser\" />\n";
	echo "       </div><!-- #profile-submit -->\n";
	// Facebook connect
	?>
    
         
     
<?php	
	echo "   </form>\n";
	echo "   </div><!-- .rbform -->\n";

			}

}

if(!$registration){ echo "<p class='rbalert'>The administrator currently disabled the registration.<p>"; }

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
