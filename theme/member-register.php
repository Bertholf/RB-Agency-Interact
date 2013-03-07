<?php
// *************************************************************************************************** //
// Prepare Page

	/* Load registration file. */
	//require_once( ABSPATH . WPINC . '/registration.php' );
   
	
	
	/* Get Options */
	$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');

		//Sidebar
		$rb_agencyinteract_option_profilemanage_sidebar = $rb_agencyinteract_options_arr['rb_agencyinteract_option_profilemanage_sidebar'];
		if($rb_agencyinteract_option_profilemanage_sidebar){
			$columnWidth = "nine";
		} else {
			$columnWidth = "twelve";
		}
		
		//Facebook Integration
		$rb_agencyinteract_option_fb_app_id = $rb_agencyinteract_options_arr['rb_agencyinteract_option_fb_app_id'];
		$rb_agencyinteract_option_fb_app_secret = $rb_agencyinteract_options_arr['rb_agencyinteract_option_fb_app_secret'];
		$rb_agencyinteract_option_fb_app_register_uri = $rb_agencyinteract_options_arr['rb_agencyinteract_option_fb_app_register_uri'];
	    $rb_agencyinteract_option_fb_registerallow = $rb_agencyinteract_options_arr['rb_agencyinteract_option_fb_registerallow'];

	    //+Registration
	    // - show/hide registration for Agent/Producers
		$rb_agencyinteract_option_registerallowAgentProducer = $registration['rb_agencyinteract_option_registerallowAgentProducer'];

		// - show/hide  self-generate password
		$rb_agencyinteract_option_registerconfirm = (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_registerconfirm'];
		
	   	if($rb_agencyinteract_option_fb_registerallow == 1){
		 	if(!class_exists("FacebookApiException")){   
		   		require_once(ABSPATH."wp-content/plugins/".rb_agencyinteract_TEXTDOMAIN."/tasks/facebook.php");
		 	}
	    }

	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );	
	
	define('FACEBOOK_APP_ID', $rb_agencyinteract_option_fb_app_id);
	define('FACEBOOK_SECRET', $rb_agencyinteract_option_fb_app_secret);
	
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
			'user_pass' => esc_attr( $user_pass ),
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
			$error .= __("A username is required for registration.<br />", rb_agencyinteract_TEXTDOMAIN);
			$have_error = true;
		}
		if ( username_exists($userdata['user_login'])) {
			$error .= __("Sorry, that username already exists!<br />", rb_agencyinteract_TEXTDOMAIN);
			$have_error = true;
		}
		if ( !is_email($userdata['user_email'], true)) {
			$error .= __("You must enter a valid email address.<br />", rb_agencyinteract_TEXTDOMAIN);
			$have_error = true;
		}
		if ( email_exists($userdata['user_email'])) {
			$error .= __("Sorry, that email address is already used!<br />", rb_agencyinteract_TEXTDOMAIN);
			$have_error = true;
		}
		if ( $_POST['profile_agree'] <> "yes") {
			$error .= __("You must agree to the terms and conditions to register.<br />", rb_agencyinteract_TEXTDOMAIN);
			$have_error = true;
		}
	
		// Bug Free!
		if($have_error == false){
			$new_user = wp_insert_user( $userdata );
			$new_user_type = $_POST['profile_type'];
			
			
			// Model or Client
			update_usermeta($new_user, 'rb_agency_interact_profiletype', $new_user_type);
			
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
			if ($rb_agencyinteract_option_registerconfirm == 1) {

				global $error;
				
				$login = wp_login( $user_login, $user_pass );
				$login = wp_signon( array( 'user_login' => $user_login, 'user_password' => $user_pass, 'remember' => 1 ), false );	
			}				
				// Notify admin and user
				wp_new_user_notification($new_user, $user_pass);	
			
		}
		
		// Log them in if no confirmation required.
		if ($rb_agencyinteract_option_registerconfirm == 1) {
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
	echo "		". __("You are currently logged in as .", rb_agencyinteract_TEXTDOMAIN) ." <a href=\"/profile-member/\" title=\"". $login->display_name ."\">". $login->display_name ."</a>\n";
				//printf( __("You are logged in as <a href="%1$s" title="%2$s">%2$s</a>.  You don\'t need another account.', rb_agencyinteract_TEXTDOMAIN), get_author_posts_url( $curauth->ID ), $user_identity );
	echo "		 <a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __("Log out of this account", rb_agencyinteract_TEXTDOMAIN) ."\">". __("Log out", rb_agencyinteract_TEXTDOMAIN) ." &raquo;</a>\n";
	echo "    </p><!-- .alert -->\n";


		} elseif ( $new_user ) {

	echo "    <p class=\"alert\">\n";
				if ( current_user_can( 'create_users' ) )
					printf( __("A user account for %1$s has been created.", rb_agencyinteract_TEXTDOMAIN), $_POST['user-name'] );
				else 
					printf( __("Thank you for registering, %1$s.", rb_agencyinteract_TEXTDOMAIN), $_POST['user-name'] );
					echo "<br/>";
					printf( __("Please check your email address. That's where you'll recieve your login password.<br/> (It might go into your spam folder)", rb_agencyinteract_TEXTDOMAIN) );
	echo "    </p><!-- .alert -->\n";

		} else {

			if ( $error ) {
				echo "<p class=\"error\">". $error ."</p>\n";
			}

			// Show some admin loving.... (Admins can create)
			if ( current_user_can("create_users") && $registration ) {
	echo "    <p class=\"alert\">\n";
	echo "      ". __("Users can register themselves or you can manually create users here.", rb_agencyinteract_TEXTDOMAIN);
	echo "    </p><!-- .alert -->\n";
			} elseif ( current_user_can("create_users")) {
	echo "    <p class=\"alert\">\n";
	echo "      ". __("Users cannot currently register themselves, but you can manually create users here.", rb_agencyinteract_TEXTDOMAIN);
	echo "    </p><!-- .alert -->\n";
			}	

			// Self Registration
			if ( $registration || current_user_can("create_users") ) {

	echo "    <form method=\"post\" id=\"adduser\" class=\"user-forms\" action=\"". $rb_agencyinteract_WPURL ."/profile-register/\">\n";
      echo "<h1 class=\"entry-title\">JOIN OUR TEAM</h1>";
	echo "<p class=\"form-title\">To Join Our Team please complete the application below.</p>";		
	//echo "    <h1>Register</h1>\n";
				
	echo "       <p class=\"form-username\">\n";
	echo "       	<label for=\"profile_user_name\">". __("Username (required)", rb_agencyinteract_TEXTDOMAIN) ."</label>\n";
	echo "       	<input class=\"text-input\" name=\"profile_user_name\" type=\"text\" id=\"profile_user_name\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_user_name'], 1 ); echo "\" />\n";
	echo "       </p><!-- .form-username -->\n";
			
	if ($rb_agencyinteract_option_registerconfirm == 1) {
	echo "       <p class=\"form-password\">\n";
	echo "       	<label for=\"profile_password\">". __("Password (required)", rb_agencyinteract_TEXTDOMAIN) ."</label>\n";
	echo "       	<input class=\"text-input\" name=\"profile_password\" type=\"password\" id=\"profile_password\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_password'], 1 ); echo "\" />\n";
	echo "       </p><!-- .form-username -->\n";
	}
				
	echo "       <p class=\"profile_first_name\">\n";
	echo "       	<label for=\"profile_first_name\">". __("First Name", rb_agencyinteract_TEXTDOMAIN) ."</label>\n";
	echo "       	<input class=\"text-input\" name=\"profile_first_name\" type=\"text\" id=\"profile_first_name\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_first_name'], 1 ); echo "\" />\n";
	echo "       </p><!-- .profile_first_name -->\n";
				
	echo "       <p class=\"profile_last_name\">\n";
	echo "       	<label for=\"profile_last_name\">". __("Last Name", rb_agencyinteract_TEXTDOMAIN) ."</label>\n";
	echo "       	<input class=\"text-input\" name=\"profile_last_name\" type=\"text\" id=\"profile_last_name\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_last_name'], 1 ); echo "\" />\n";
	echo "       </p><!-- .profile_last_name -->\n";
				
	echo "       <p class=\"form-email\">\n";
	echo "       	<label for=\"email\">". __("E-mail (required)", rb_agencyinteract_TEXTDOMAIN) ."</label>\n";
	echo "       	<input class=\"text-input\" name=\"profile_email\" type=\"text\" id=\"profile_email\" value=\""; if ( $error ) echo wp_specialchars( $_POST['profile_email'], 1 ); echo "\" />\n";
	echo "       </p><!-- .form-email -->\n";
     
	
	echo "       <p class=\"form-profile_type\">\n";
      
	echo "       	<label for=\"profile_type\">". __("Type of Profile", rb_agencyinteract_TEXTDOMAIN) ."</label>\n";
	
	echo "       		<select name=\"profile_type\">\n";
      $target = get_query_var("typeofprofile");
	
	if($target == "model-talent"){
	echo "       			<option value=\"0\">". __("Talent / Model", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";
	} elseif ($target == "agent-partners"){
	
		echo "       			<option value=\"1\">". __("Agent / Producer", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";	
		
	} else {
		echo "       			<option value=\"0\">". __("Talent / Model", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";
	     	echo "       			<option value=\"1\">". __("Agent / Producer", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";
	}
	
	echo "       		</select>\n";
	echo "       </p><!-- .form-profile_type -->\n";
  	
        echo "     <p class=\"form-profile_gender\">\n";
     	echo "		<label for=\"profile_gender\">". __("Gender", rb_agencyinteract_TEXTDOMAIN) ."</label>\n";
					$query= "SELECT GenderID, GenderTitle FROM " .  table_agency_data_gender . " GROUP BY GenderTitle ";
					echo "<select id='ProfileGender' name=\"ProfileGender\">";
					echo "<option value=\"\">All Gender</option>";
					$queryShowGender = mysql_query($query);
					while($dataShowGender = mysql_fetch_assoc($queryShowGender)){
						echo "<option value=\"".$dataShowGender["GenderID"]."\" ". selected($ProfileGender ,$dataShowGender["GenderID"],false).">".$dataShowGender["GenderTitle"]."</option>";
					}
					echo "</select>";
		echo "	  </p><!-- .form-profile_gender-->\n";

	      $rb_agency_options_arr = get_option('rb_agency_options');
		$rb_agency_option_unittype  			= $rb_agency_options_arr['rb_agency_option_unittype'];
		$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
		$rb_agency_option_locationtimezone 		= (int)$rb_agency_options_arr['rb_agency_option_locationtimezone'];
	

		
	$query3 = "SELECT * FROM ". table_agency_customfields ." WHERE ProfileCustomView = 0 AND ProfileCustomShowRegistration = 1 ORDER BY ProfileCustomOrder";
	$results3 = mysql_query($query3) or die(mysql_error());
	$count3 = mysql_num_rows($results3);
	
	while ($data3 = mysql_fetch_assoc($results3)) {
	 	
		$ProfileCustomTitle = $data3['ProfileCustomTitle'];
		$ProfileCustomType  = $data3['ProfileCustomType'];
	       
			 // SET Label for Measurements
			 // Imperial(in/lb), Metrics(ft/kg)
			 $rb_agency_options_arr = get_option('rb_agency_options');
			  $rb_agency_option_unittype  = $rb_agency_options_arr['rb_agency_option_unittype'];
			  $measurements_label = "";
			 if ($ProfileCustomType == 7) { //measurements field type
			    if($rb_agency_option_unittype ==0){ // 0 = Metrics(ft/kg)						
					if($data3['ProfileCustomOptions'] == 1){
					    $measurements_label  ="<em>(cm)</em>";
					}elseif($data3['ProfileCustomOptions'] == 2){
					    $measurements_label  ="<em>(kg)</em>";
					}elseif($data3['ProfileCustomOptions'] == 3){
					  	$measurements_label  ="<em>(In Inches/Feet)</em>";
					}
					} elseif($rb_agency_option_unittype ==1){ //1 = Imperial(in/lb)
						if($data3['ProfileCustomOptions'] == 1){
						    $measurements_label  ="<em>(In Inches)</em>";
						}elseif($data3['ProfileCustomOptions'] == 2){
						  	$measurements_label  ="<em>(In Pounds)</em>";
						}elseif($data3['ProfileCustomOptions'] == 3){
						  	$measurements_label  ="<em>(In Inches/Feet)</em>";
						}
					}					
			 }  
		 
		 echo "       <p class=\"form-".strtolower(trim($data3['ProfileCustomTitle']))." ".gender_filter($data3['ProfileCustomShowGender'])."\">\n"; 
		 echo "       <label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">". __( $data3['ProfileCustomTitle'].$measurements_label, rb_agencyinteract_TEXTDOMAIN) ."</label>\n";		  
			if ($ProfileCustomType == 1) { //TEXT
				echo "<input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\"". $_REQUEST["ProfileCustomID". $data3['ProfileCustomID']] ."\" /><br />\n";
			}
			
			/* elseif ($ProfileCustomType == 2) { // Min Max
			
				$ProfileCustomOptions_String = str_replace(",",":",strtok(strtok($data3['ProfileCustomOptions'],"}"),"{"));
				list($ProfileCustomOptions_Min_label,$ProfileCustomOptions_Min_value,$ProfileCustomOptions_Max_label,$ProfileCustomOptions_Max_value) = explode(":",$ProfileCustomOptions_String);
			 
				if (!empty($ProfileCustomOptions_Min_value) && !empty($ProfileCustomOptions_Max_value)) {
						echo "<br /><br /> <label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Min", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\"". $ProfileCustomOptions_Min_value ."\" />\n";
						echo "<br /><br /><br /><label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Max", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\"". $ProfileCustomOptions_Max_value ."\" /><br />\n";
				} else {
						echo "<br /><br />  <label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Min", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\"".$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]."\" />\n";
						echo "<br /><br /><br /><label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Max", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\"".$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]."\" /><br />\n";
				}
			 
			} */
			
			elseif ($ProfileCustomType == 3) {  // Drop Down
				list($option1,$option2) = explode(":",$data3['ProfileCustomOptions']);	
				$data = explode("|",$option1);
				$data2 = explode("|",$option2);
				echo "<label>".$data[0]."</label>";
				echo "<select name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\">\n";
				echo "<option value=\"\">--</option>";
					$pos = 0;
					foreach($data as $val1){
						if(!empty($val1)){
							echo "<option value=\"".$val1."\" ".selected($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']],$val1,false)." >".$val1."</option>";
						}
					}
					echo "</select>\n";
				/*if (!empty($data2) && !empty($option2)) {
					echo "<label>".$data2[0]."</label>";
						$pos2 = 0;
						echo "<select name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\">\n";
						echo "<option value=\"\">--</option>";
						foreach($data2 as $val2){
								if($val2 != end($data2) && $val2 !=  $data2[0]){
									echo "<option value=\"".$val2."\" ". selected($val2, $_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]) ." >".$val2."</option>";
								}
							}
						echo "</select>\n";
				}*/
			} elseif ($ProfileCustomType == 4) {
				echo "<textarea style=\"width: 100%; min-height: 100px;\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\">". $_REQUEST["ProfileCustomID". $data3['ProfileCustomID']] ."</textarea>";

			} elseif ($ProfileCustomType == 5) {

				$array_customOptions_values = explode("|",$data3['ProfileCustomOptions']);
				echo "<div style=\"width:300px;float:left;\">";
				foreach($array_customOptions_values as $val){
					$xplode = explode(",",$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]);
					echo "<label><input type=\"checkbox\" value=\"". $val."\"   "; if(in_array($val,$xplode)){ echo "checked=\"checked\""; } echo" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\" />";
					echo "". $val."</label>";
				}      echo "<br/>";
				echo "</div>";
				   
			} elseif ($ProfileCustomType == 6) {
				
				$array_customOptions_values = explode("|",$data3['ProfileCustomOptions']);				
				foreach($array_customOptions_values as $val){
					
					echo "<input type=\"radio\" value=\"". $val."\" "; checked($val, $_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]); echo" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\" />";
					echo "<span>". $val."</span><br/>";
				}
			} elseif ($ProfileCustomType == 7) { //Imperial/Metrics			

			    // if feet and inches
				// display dropdown selection box
				if($data3['ProfileCustomOptions']==3){

                	echo "<select name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\">\n";

						if (empty($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']])) {

							 echo "<option value=\"\">--</option>\n";
						
						}

						$i=36;	$heightraw = 0; $heightfeet = 0;  $heightinch = 0;

                        while($i<=90)  {
							
							  $heightraw = $i;
							  $heightfeet = floor($heightraw/12);
						      $heightinch = $heightraw - floor($heightfeet*12);

							  echo " <option value=\"". $i ."\" "
							        . selected($_REQUEST["ProfileCustomID". 
									  $data3['ProfileCustomID']], $i) .">"
									. $heightfeet ." ft "
									. $heightinch 
									." in</option>\n";

							  $i++;

					   }

					echo " </select>\n";


                // use textbox if
				// not feet and inches 
				}else{
				
				    echo "<input type=\"text\" name=\"ProfileCustomID"
					. $data3['ProfileCustomID'] ."\" value=\""
					.$_REQUEST["ProfileCustomID"
					. $data3['ProfileCustomID']]."\" /><br />\n";

			    }
			
	    echo "       </p>\n";
		} 
			
	}// End while
	echo "       <p class=\"form-profile_agree\">\n";
					$profile_agree = get_the_author_meta("profile_agree", $current_user->ID );
	echo "       		<input type=\"checkbox\" name=\"profile_agree\" value=\"yes\" /> ". sprintf(__("I agree to the %s terms of service", rb_agencyinteract_TEXTDOMAIN), "<a href=\"/terms-of-use/\" target=\"_blank\">") ."</a>\n";
	echo "       </p><!-- .form-profile_agree -->\n";
 
	echo "       <p class=\"form-submit\">\n";
	echo "       	<input name=\"adduser\" type=\"submit\" id=\"addusersub\" class=\"submit button\" value=\"";

					if ( current_user_can("create_users") ) {  _e("Add User", rb_agencyinteract_TEXTDOMAIN); } else {  _e("Register", rb_agencyinteract_TEXTDOMAIN); } echo "\" />\n";
					wp_nonce_field("add-user");
					$fb_app_register_uri = "";

					if($rb_agencyinteract_option_fb_app_register_uri == 1){
						$fb_app_register_uri = $rb_agencyinteract_option_fb_app_register_uri;
					}else{
						$fb_app_register_uri = network_site_url("/")."profile-register/";
					}

					// Allow facebook login/registration
					if($rb_agencyinteract_option_fb_registerallow ==1){
						echo "<div>\n";
						echo "<span>Or</span>\n";
						echo "<div id=\"fb_RegistrationForm\">\n";
						if ($rb_agencyinteract_option_registerconfirm == 1) {	 // With custom password fields
							echo "<iframe src=\"https://www.facebook.com/plugins/registration?client_id=".$rb_agencyinteract_option_fb_app_id."&redirect_uri=".$fb_app_register_uri."&fields=[ {'name':'name'}, {'name':'email'}, {'name':'location'}, {'name':'gender'}, {'name':'birthday'}, {'name':'username',  'description':'Username',  'type':'text'},{'name':'password'},{'name':'tos','description':'I agree to the terms of service','type':'checkbox'}]\"		 
								  scrolling=\"auto\"
								  frameborder=\"no\"
								  style=\"border:none\"
								  allowTransparency=\"true\"
								  width=\"100%\"
								  height=\"330\">
							</iframe>";
						}else{
							echo "<iframe src=\"https://www.facebook.com/plugins/registration?client_id=".$rb_agencyinteract_option_fb_app_id."&redirect_uri=".$fb_app_register_uri."&fields=[ {'name':'name'}, {'name':'email'}, {'name':'location'}, {'name':'gender'}, {'name':'birthday'}, {'name':'username',  'description':'Username',  'type':'text'},{'name':'password'},{'name':'tos','description':'I agree to the terms of service','type':'checkbox'}]\"		 
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
	echo "       </p><!-- .form-submit -->\n";
	// Facebook connect
	?>
    
         
     
<?php	
	echo "   </form><!-- #adduser -->\n";

			}

}
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