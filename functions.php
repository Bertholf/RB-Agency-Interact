<?php
// *************************************************************************************************** //
// Admin Head Section 

	add_action('admin_head', 'rb_agencyinteract_admin_head');
		function rb_agencyinteract_admin_head(){
		  if( is_admin() ) {
			echo "<link rel=\"stylesheet\" href=\"". rb_agencyinteract_BASEDIR ."style/admin.css\" type=\"text/css\" media=\"screen\" />\n";
		  }
		}
	
// *************************************************************************************************** //
// Page Head Section

	add_action('wp_head', 'rb_agencyinteract_inserthead');
		// Call Custom Code to put in header
		function rb_agencyinteract_inserthead() {
		  if( !is_admin() ) {
			echo "<link rel=\"stylesheet\" href=\"". rb_agencyinteract_BASEDIR ."style/style.css\" type=\"text/css\" media=\"screen\" />\n";
			echo "<script type=\"text/javascript\" src=\"". rb_agencyinteract_BASEDIR ."jquery-page.js\"></script>";
		  }
		  if(!wp_script_is('jquery')) {
			echo "<script type=\"text/javascript\" src=\"". rb_agencyinteract_BASEDIR ."style/jquery.1.8.js\"></script>";
			
			} 
		}

// *************************************************************************************************** //
// Handle Folders

	// Adding a new rule
	add_filter('rewrite_rules_array','rb_agencyinteract_rewriteRules');
		function rb_agencyinteract_rewriteRules($rules) {
			$newrules = array();
			$newrules['profile-member/(.*)$'] = 'index.php?type=$matches[1]';
			$newrules['profile-member/(.*)/(.*)$'] = 'index.php?type=$matches[0]';
			$newrules['profile-member'] = 'index.php?type=profileoverview';
			$newrules['profile-register/(.*)$'] = 'index.php?type=profileregister&typeofprofile=$matches[1]';
			$newrules['profile-register'] = 'index.php?type=profileregister';
			
			$newrules['profile-login'] = 'index.php?type=profilelogin';
			return $newrules + $rules;
		}
		
	// Get Veriables & Identify View Type
	add_action( 'query_vars', 'rb_agencyinteract_query_vars' );
		function rb_agencyinteract_query_vars( $query_vars ) {
			$query_vars[] = 'type';
			$query_vars[] = 'typeofprofile';
			return $query_vars;
		}
	
	// Set Custom Template
	add_filter('template_include', 'rb_agencyinteract_template_include', 1, 1); 
		function rb_agencyinteract_template_include( $template ) {
			if ( get_query_var( 'type' ) ) {
			  if (get_query_var( 'type' ) == "profileoverview") {
				return dirname(__FILE__) . '/theme/member-overview.php'; 
			  } elseif (get_query_var( 'type' ) == "account") {
				return dirname(__FILE__) . '/theme/member-account.php'; 
			  } elseif (get_query_var( 'type' ) == "subscription") {
				return dirname(__FILE__) . '/theme/member-subscription.php'; 
			  } elseif (get_query_var( 'type' ) == "manage") {
				return dirname(__FILE__) . '/theme/member-profile.php'; 
			  } elseif (get_query_var( 'type' ) == "media") {
				return dirname(__FILE__) . '/theme/member-media.php'; 
			  } elseif (get_query_var( 'type' ) == "profileregister") {
				return dirname(__FILE__) . '/theme/member-register.php'; 
			  } elseif (get_query_var( 'type' ) == "profilelogin") {
				return dirname(__FILE__) . '/theme/member-login.php'; 
			  }
			}
			return $template;
		}
	
	// Remember to flush_rules() when adding rules
	add_filter('init','rb_agencyinteract_flushrules');
		function rb_agencyinteract_flushRules() {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}
	
	
	/*/
	 *  Fix form post url for multi language.
	/*/
/*
	function rb_agencyinteract_postURILanguage($request_URI){
	     if(!in_array(substr($_SERVER['REQUEST_URI'],1,2), array("en","nl"))){
			if (function_exists('trans_getLanguage')) {
				 if(qtrans_getLanguage()=='nl') {
					return "/".qtrans_getLanguage();
				
				} elseif(qtrans_getLanguage()=='en') {
					return "/".qtrans_getLanguage();
				}
			 }
	    }
	}
	*/

// *************************************************************************************************** //
// Handle Emails

	// Redefine user notification function  
	if ( !function_exists('wp_new_user_notification') ) {  
		function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {  
			$user = new WP_User($user_id);  
	  
			$user_login = stripslashes($user->user_login);  
			$user_email = stripslashes($user->user_email);  
	  
			$message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "\r\n\r\n";  
			$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";  
			$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";  
	  
			@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);  
	  
			if ( empty($plaintext_pass) )  
				return;  
			$message  = __('Hi there,') . "\r\n\r\n";  
			$message .= sprintf(__("Thanks for joining %s! Here's how to log in:"), get_option('blogname')) . "\r\n\r\n"; 
			$message .= get_option('home') ."/profile-login/\r\n"; 
			$message .= sprintf(__('Username: %s'), $user_login) . "\r\n"; 
			$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n\r\n"; 
			$message .= sprintf(__('If you have any problems, please contact us at %s.'), get_option('admin_email')) . "\r\n\r\n"; 
			$message .= __('Regards,')."\r\n";
			$message .= get_option('blogname') . __(' Team') ."\r\n"; 
			$message .= get_option('home') ."\r\n"; 
	 
	 		$headers = 'From: '. get_option('blogname') .' <'. get_option('admin_email') .'>' . "\r\n";
			wp_mail($user_email, sprintf(__('%s Registration Successful! Login Details'), get_option('blogname')), $message, $headers);  
	  
		}  
	}  
	// Make Directory for new profile
     function rb_agencyinteract_checkdir($ProfileGallery){
	      	
			if (!is_dir(rb_agency_UPLOADPATH . $ProfileGallery)) {
				mkdir(rb_agency_UPLOADPATH . $ProfileGallery, 0755);
				chmod(rb_agency_UPLOADPATH . $ProfileGallery, 0777);
			}
			return $ProfileGallery;
     }



// *************************************************************************************************** //
// Functions

	// Move Login Page	
	add_filter("login_init", "rb_agencyinteract_login_movepage", 10, 2);
		function rb_agencyinteract_login_movepage( $url ) {
			global $action;
		
			if (empty($action) || 'login' == $action) {
				wp_safe_redirect(get_bloginfo("wpurl"). "/profile-login/");
				die;
			}
		}

	// Rewrite Login
	add_action( 'init', 'rb_agencyinteract_login_rewrite' );
		function rb_agencyinteract_login_rewrite() {
			add_rewrite_rule(get_bloginfo("wpurl"). "profile-register/?$", 'wp-login.php', 'top');
		}
		

	// Redirect after Login
	add_filter('login_redirect', 'rb_agencyinteract_login_redirect', 10, 3);	
		function rb_agencyinteract_login_redirect() {
			global $user_ID, $current_user, $wp_roles;
			if( $user_ID ) {
				$user_info = get_userdata( $user_ID ); 

				if( current_user_can( 'manage_options' )) {
					header("Location: ". get_bloginfo("wpurl"). "/wp-admin/");
				} elseif ( strtotime( $user_info->user_registered ) > ( time() - 172800 ) ) {
					// If user_registered date/time is less than 48hrs from now
					// Message will show for 48hrs after registration
					header("Location: ". get_bloginfo("wpurl"). "/profile-member/account/");
				} else {
					header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
				}
			}
		}


	/*

	OBSOLETE  Just use for reference

	add_filter("registration_redirect", "rb_agencyinteract_register_redirect");
		function rb_agencyinteract_register_redirect() {
			return "/profile-member/";
		}
	add_filter('register', 'rb_agencyinteract_register_movepage');
		function rb_agencyinteract_register_movepage($link) {
			if(!is_user_logged_in()) {
				$link = '<a href="/profile-register/">' . __('Register') . '</a>';
			}
			$link = str_replace(site_url("wp-login.php?action=register"), "/profile-member/", $link );
			return $link;
		}
	add_filter('site_url',  'wplogin_filter', 10, 3);
		function wplogin_filter( $url, $path, $orig_scheme) {
			$old  = array( "/(wp-login\.php)/");
			$new  = array( "/profile-login/");
			return preg_replace( $old, $new, $url, 1);
		}
	// Redirect after Registration
	add_filter("register_redirect", "rb_agencyinteract_register_redirect");
		function rb_agencyinteract_register_redirect() {
			return "/profile-member/";
		}
		

	// Change Login URL
	// Change Registration Form Submit Titles
	add_filter('register', 'change_admin');
		function change_admin($link) {
			$link = str_replace("Site Admin", "Your Account", $link);
			return $link;
		}
	add_filter('register', 'rb_agencyinteract_register_changenames');
		function rb_agencyinteract_register_changenames($link) {
			$link = str_replace(">Register<", ">Sign up<", $link);
			return $link;
		}
	*/



    // function for checking male and female filter
	if ( !function_exists('gender_filter') ) {  
		function gender_filter($gender=0) {
		    global $wpdb;
			
			$gender = "SELECT GenderTitle FROM rb_agency_data_gender WHERE GenderID = ". $gender ." LIMIT 1";
			$results = $wpdb->get_results($gender);
			
			$gender_title = "";
			foreach($results as $gname){
				$gender_title = strtolower($gname->GenderTitle);
			}
			
			if($gender_title == 'male'){
				return "male_filter";
			}elseif($gender_title == 'female'){
				return "female_filter";
			}else{
				return "";
			}
		}
	}

	// retrieving value of saved fields for edit
	if ( !function_exists('retrieve_datavalue') ) {  
		function retrieve_datavalue($field="",$customID=0,$ID=0,$type="", $val="") {
			global $wpdb;
			/* 
			 *    Get data for displaying and pass to array
			 *    for comparison
			 */
			 if($ID != 0){
					 
				if($type == "dropdown"){
					$result = mysql_query("SELECT ProfileCustomValue FROM "
							. table_agency_customfield_mux .
							" WHERE ProfileCustomID = ". $customID .
							" AND ProfileCustomValue = '" . $val . "' "
							." AND ProfileID = "
							. $ID);
				} else {
					$result = mysql_query("SELECT ProfileCustomValue FROM "
							. table_agency_customfield_mux .
							" WHERE ProfileCustomID = ". $customID ." AND ProfileID = "
							. $ID);
				}

				while($row = mysql_fetch_assoc($result)){
					if($type == "textbox"){
					 return $row["ProfileCustomValue"];
					} elseif($type == "dropdown") {
					 return "selected";
					}
				}

				if($type == "textbox"){
				     return $field;
				} elseif($type == "dropdown") {
				     return "";
				}
					   
			 } else {
			 	
				if($type == "textbox"){
					 return $field;
				} elseif($type == "dropdown") {
					 return "";
				}
				
			 }
		}
	}	

	// retrieving data type title
	if ( !function_exists('retrieve_title') ) {  
		function retrieve_title($id=0) {
		   global $wpdb;
		   /* 
		    * return title
			*/
            $check_type = "SELECT DataTypeTitle FROM ". table_agency_data_type ." WHERE DataTypeID = " . $id;
			$check_query = mysql_query($check_type) OR die(mysql_error());
			if(mysql_num_rows($check_query) > 0){
				$fetch = mysql_fetch_assoc($check_query);
				return $fetch['DataTypeTitle'];
			} else {
				return false;
			}
		}
	}


?>