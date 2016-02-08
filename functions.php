<?php
// *************************************************************************************************** //
// Init - Remove Seasion already sent
	add_action('init', 'rb_agency_interact_init_sessions');
		function rb_agency_interact_init_sessions() {
			if (!session_id()) {
				session_start();
			}
		}
// *************************************************************************************************** //
// Admin Head Section 

	add_action('admin_head', 'rb_agency_interact_admin_head');
		function rb_agency_interact_admin_head(){
			if( is_admin() ) {
				echo "<link rel=\"stylesheet\" href=\"". RBAGENCY_interact_BASEDIR ."style/admin.css\" type=\"text/css\" media=\"screen\" />\n";
			}
		}

// *************************************************************************************************** //
// Page Head Section

	add_action('wp_head', 'rb_agency_interact_inserthead');
		// Call Custom Code to put in header
		function rb_agency_interact_inserthead() {
			if(!wp_script_is('jquery')) {
				echo "<script type=\"text/javascript\" src=\"". RBAGENCY_interact_BASEDIR ."style/jquery.1.8.js\"></script>";
			}
			if( !is_admin() ) {
				echo "<link rel=\"stylesheet\" href=\"". RBAGENCY_interact_BASEDIR ."style/style.css\" type=\"text/css\" media=\"screen\" />\n";
				echo "<script type=\"text/javascript\" src=\"". RBAGENCY_interact_BASEDIR ."jquery-page.js\"></script>";
			}
		}

// *************************************************************************************************** //
// Handle Folders

	// Adding a new rule
	add_filter('rewrite_rules_array','rb_agency_interact_rewriteRules');
		function rb_agency_interact_rewriteRules($rules) {
			$newrules = array();
			$newrules['profile-member/(.*)$'] = 'index.php?type=$matches[1]&rbgroup=models';
			$newrules['profile-member/(.*)/(.*)$'] = 'index.php?type=$matches[0]&rbgroup=models';
			$newrules['profile-member'] = 'index.php?type=profileoverview&rbgroup=models';
			$newrules['profile-register/(.*)$'] = 'index.php?type=profileregister&typeofprofile=$matches[1]&rbgroup=models';
			$newrules['profile-register'] = 'index.php?type=profileregister&rbgroup=models';
			$newrules['profile-login'] = 'index.php?type=profilelogin&rbgroup=models';

			$newrules['membership'] = 'index.php?type=membership&v=$matches[1]';
			$newrules['registration-success'] = 'index.php?type=membershipsuccess';
			$newrules['user-membership-page'] = 'index.php?type=usermembershippage';
			return $newrules + $rules;
		}

	// Get Veriables & Identify View Type
	add_action( 'query_vars', 'rb_agency_interact_query_vars' );
		function rb_agency_interact_query_vars( $query_vars ) {
			$query_vars[] = 'type';
			$query_vars[] = 'typeofprofile';
			$query_vars[] = 'ref';
			$query_vars[] = 'rbgroup';

			return $query_vars;
		}

	// Set Custom Template
	add_filter('template_include', 'rb_agency_interact_template_include', 1, 1); 
		function rb_agency_interact_template_include( $template ) {
			if ( get_query_var( 'type' ) && get_query_var( 'rbgroup' ) == "models") {

				if(function_exists("rb_agency_group_permission")){
					rb_agency_group_permission(get_query_var( 'rbgroup' ));
				}
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
				} elseif (get_query_var( 'type' ) == "profilelogin") {
					return dirname(__FILE__) . '/theme/member-login.php'; 
				} elseif (get_query_var( 'typeofprofile' ) == "client") {
					return dirname(__FILE__) . '/theme/client-register.php'; 
				} elseif (get_query_var( 'type' ) == "profileregister") {
					return dirname(__FILE__) . '/theme/member-register.php'; 
				} elseif (get_query_var( 'type' ) == "dashboard") {
					return dirname(__FILE__) . '/theme/view-dashboard.php';
				} elseif (get_query_var( 'type' ) == "pending") {
					return dirname(__FILE__) . '/theme/member-pending.php';
				} elseif (get_query_var('type') == 'auditions'){
					return dirname(__FILE__) . '/theme/member-auditions.php';
				}
			}

			if (get_query_var( 'type' ) == "membership"){
				return dirname(__FILE__). '/theme/s2member/membership_page.php';
			}elseif (get_query_var( 'type' ) == "membershipsuccess"){
				return dirname(__FILE__). '/theme/s2member/membership_success.php';
			}elseif (get_query_var('type') == "usermembershippage"){
				return dirname(__FILE__).'/theme/s2member/user-membership-page.php';
			}
			return $template;
		}

	// Remember to flush_rules() when adding rules
	// Todo: Remove lines below. Causes permalink incompatibility with other plugins such as woocommerce
	/*add_filter('init','rb_agency_interact_flushrules');
		function rb_agency_interact_flushRules() {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}*/
	
   
	


	/*/
	 *  Fix form post url for multi language.
	/*/
/*
	function rb_agency_interact_postURILanguage($request_URI){
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
	if ( !function_exists('rb_new_user_notification') ) { 
		function rb_new_user_notification( $user_id, $plaintext_pass = '' ) { 

			global $wpdb;
			
			$user = new WP_User($user_id);

			$user_login = stripslashes($user->user_login);
			$user_email = stripslashes($user->user_email);

			$rbagency_initial_email_after_registration = get_option('rbagency_initial_email_after_registration');
			$rb_agency_options_arr = get_option('rb_agency_options');

			$rbagency_use_s2member = get_option('rbagency_use_s2member');
			
			if($rbagency_use_s2member == true){
				if(!empty($rbagency_initial_email_after_registration)){


					$message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "\r\n\r\n";
					$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
					$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

					$rb_agency_options_arr = get_option('rb_agency_options');
					$_sendEmailtoAdmin = isset($rb_agency_options_arr['rb_agency_option_notify_admin_new_user'])?$rb_agency_options_arr['rb_agency_option_notify_admin_new_user']:0;
					
					if($_sendEmailtoAdmin == 1){
						@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);
					}

					
					$message = str_replace('[username]',$user_login,$rbagency_initial_email_after_registration);
					$message = str_replace('[login_url]',get_option('home') ."/profile-login",$message);
					$message = str_replace('[username]',$user_login,$message);
					$message = str_replace('[password]',$plaintext_pass,$message);
					$message = str_replace('[agency_email]',get_option('admin_email'),$message);
					$message = str_replace('[agency_name]',get_option('blogname'),$message);
					$message = str_replace('[domain_url]',get_option('home'),$message);
					$message = str_replace('[path_to_logo]',$rb_agency_options_arr['rb_agency_option_agencylogo'],$message);

					$headers = 'From: '. get_option('blogname') .' <'. get_option('admin_email') .'>' . "\r\n";
					wp_mail($user_email, sprintf(__('%s Registration Successful! Login Details'), get_option('blogname')), nl2br($message), $headers);
				}else{
					$message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "\r\n\r\n";
					$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
					$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

					@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);

					if ( empty($plaintext_pass) )  
						return;
					$message  = sprintf(__("Hi  %s!", RBAGENCY_interact_TEXTDOMAIN), $user_login) . "\r\n\r\n";
					$message .= sprintf(__("Thanks for joining %s!", RBAGENCY_interact_TEXTDOMAIN), get_option('blogname')) . "\r\n\r\n"; 
					$message .= "Please use the login below to continue registration.\r\n";
					$message .= 'Login URL:'. get_option('home') ."/profile-login/\r\n"; 
					$message .= sprintf(__('Username: %s', RBAGENCY_interact_TEXTDOMAIN), $user_login) . "\r\n"; 
					$message .= sprintf(__('Password: %s', RBAGENCY_interact_TEXTDOMAIN), $plaintext_pass) . "\r\n\r\n"; 
					$message .= sprintf(__('If you have any problems, please contact us at %s.'), get_option('admin_email')) . "\r\n\r\n"; 
					$message .= __('Regards,')."\r\n";
					$message .= get_option('blogname') . __(' Team') ."\r\n"; 
					$message .= get_option('home') ."\r\n"; 

					$headers = 'From: '. get_option('blogname') .' <'. get_option('admin_email') .'>' . "\r\n";
					wp_mail($user_email, sprintf(__('%s Registration Successful! Login Details'), get_option('blogname')), $message, $headers);
				}
			}else{
				$message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "\r\n\r\n";
				$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
				$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

				$rb_agency_options_arr = get_option('rb_agency_options');
				$_sendEmailtoAdmin = isset($rb_agency_options_arr['rb_agency_option_notify_admin_new_user'])?$rb_agency_options_arr['rb_agency_option_notify_admin_new_user']:0;
				
				if($_sendEmailtoAdmin == 1){
					@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);
				}

				if ( empty($plaintext_pass) )  
					return;
				$message  = __('Hi there,', RBAGENCY_interact_TEXTDOMAIN) . "\r\n\r\n";
				$message .= sprintf(__("Thanks for joining %s! Here's how to log in:", RBAGENCY_interact_TEXTDOMAIN), get_option('blogname')) . "\r\n\r\n"; 
				$message .= get_option('home') ."/profile-login/\r\n"; 
				$message .= sprintf(__('Username: %s', RBAGENCY_interact_TEXTDOMAIN), $user_login) . "\r\n"; 
				$message .= sprintf(__('Password: %s', RBAGENCY_interact_TEXTDOMAIN), $plaintext_pass) . "\r\n\r\n"; 
				$message .= sprintf(__('If you have any problems, please contact us at %s.', RBAGENCY_interact_TEXTDOMAIN), get_option('admin_email')) . "\r\n\r\n"; 
				$message .= __('Regards,', RBAGENCY_interact_TEXTDOMAIN)."\r\n";
				$message .= get_option('blogname') . __(' Team', RBAGENCY_interact_TEXTDOMAIN) ."\r\n"; 
				$message .= get_option('home') ."\r\n"; 

				$headers = 'From: '. get_option('blogname') .' <'. get_option('admin_email') .'>' . "\r\n";
				wp_mail($user_email, sprintf(__('%s Registration Successful! Login Details', RBAGENCY_interact_TEXTDOMAIN), get_option('blogname')), $message, $headers);
			}

			
			

		}


	}

	if ( !function_exists('wp_new_user_notification_pending') ) { 
		function wp_new_user_notification_pending( $user_id , $new_user = true) { 
		
				$user = new WP_User($user_id);
				
				$user_login = stripslashes($user->user_login);
				$user_email = stripslashes($user->user_email);
				
				if($new_user == true){
					$message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "\r\n\r\n";
					$_subject = sprintf(__('[%s] New User Registration'), get_option('blogname'));
				}else{
					$message  = sprintf(__('User Account Pending for Approval on your blog %s:'), get_option('blogname')) . "\r\n\r\n";
					$_subject = sprintf(__('[%s] : Pending for Approval : %s'), get_option('blogname') , $user_login);
				}
				$message .= sprintf(__('Username: %s'), $user_login) . "\r\n";
				$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n\r\n\r\n";
				
				$message .= 'Manage Pending Account: '. admin_url('admin.php?page=rb_agency_interact_approvemembers');
			
			
				@wp_mail(get_option('admin_email'), $_subject, $message);
				
				if($new_user == true){
					$message  = __('Hi there,', RBAGENCY_interact_TEXTDOMAIN) . "\r\n\r\n";
					$message .= sprintf(__("Thanks for joining %s! ", RBAGENCY_interact_TEXTDOMAIN), get_option('blogname')) . "\r\n\r\n"; 
					$message .= sprintf(__('Your account is pending for approval. We will send you a confirmation once account is approved.', RBAGENCY_interact_TEXTDOMAIN), $user_login) . "\r\n"; 
					$message .= sprintf(__('If you have any problems, please contact us at %s.', RBAGENCY_interact_TEXTDOMAIN), get_option('admin_email')) . "\r\n\r\n"; 
					$message .= __('Regards,', RBAGENCY_interact_TEXTDOMAIN)."\r\n";
					$message .= get_option('blogname') . __(' Team') ."\r\n"; 
					$message .= get_option('home') ."\r\n"; 
	
					$headers = 'From: '. get_option('blogname') .' <'. get_option('admin_email') .'>' . "\r\n";
					//wp_mail($user_email, sprintf(__('%s Registration Successful!  Account is pending for approval'), get_option('blogname')), make_clickable($message), $headers);
					wp_mail($user_email, sprintf(__('%s Registration Successful!  Account is pending for approval', RBAGENCY_interact_TEXTDOMAIN), get_option('blogname')), $message, $headers);
				}
		}
	}

	if ( !function_exists('wp_new_user_notification_approve') ) { 
		function wp_new_user_notification_approve( $user_id) { 
				global $wpdb;
			
			$user = new WP_User($user_id);
				$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
				$rb_agencyinteract_option_registerapproval = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval']:0;

				if($user){
					$user_login = stripslashes($user->user_login);
					$user_email = stripslashes($user->user_email);

					/*if($rb_agencyinteract_option_registerapproval == 0){
						$new_pass = wp_generate_password();
						wp_set_password( $new_pass, $user_id );
						$user_pass = $new_pass;
					}*/

					$message  = __('Hi there,', RBAGENCY_interact_TEXTDOMAIN) . "\r\n\r\n";
					$message .= sprintf(__('Congratulations! Your account is approved.', RBAGENCY_interact_TEXTDOMAIN), $user_login) . "\r\n"; 
					//$message .= sprintf(__("Here's how to log in:"), get_option('blogname')) . "\r\n\r\n"; 
					//$message .= get_option('home') ."/profile-login/\r\n"; 
					//if($rb_agencyinteract_option_registerapproval == 1){ // automally approved
					//			$message .= sprintf(__('Username: %s'), $user_login) . "\r\n"; 
					//			$message .= sprintf(__('Password: %s'),  $user_pass) . "\r\n\r\n"; 
					//}/*else { // manually approved
					//			$message .= sprintf(__('Password: %s'),  "Your Password") . "\r\n\r\n"; 

					//}
					$message .= sprintf(__('If you have any problems, please contact us at %s.', RBAGENCY_interact_TEXTDOMAIN), get_option('admin_email')) . "\r\n\r\n"; 
					$message .= __('Regards,', RBAGENCY_interact_TEXTDOMAIN)."\r\n";
					$message .= get_option('blogname') . __(' Team') ."\r\n"; 
					$message .= get_option('home') ."\r\n"; 

					$headers = 'From: '. get_option('blogname') .' <'. get_option('admin_email') .'>' . "\r\n";
					//wp_mail($user_email, sprintf(__('%s Congratulations! Your account is approved.'), get_option('blogname')), make_clickable($message), $headers);
					wp_mail($user_email, sprintf(__('%s Congratulations! Your account is approved.'), get_option('blogname')), $message, $headers);
				}
		}
	}
	// Make Directory for new profile
	function rb_agency_interact_checkdir($ProfileGallery){

		if (!is_dir(RBAGENCY_UPLOADPATH . $ProfileGallery)) {
			mkdir(RBAGENCY_UPLOADPATH . $ProfileGallery, 0755);
			chmod(RBAGENCY_UPLOADPATH . $ProfileGallery, 0777);
		}
		return $ProfileGallery;
	}



// *************************************************************************************************** //
// Functions

	// Move Login Page
	add_filter("login_init", "rb_agency_interact_login_movepage", 10, 2);
		function rb_agency_interact_login_movepage( $url ) {
			global $action;
				$rb_agency_options_arr = get_option('rb_agencyinteract_options');
				$rb_agency_option_redirect_custom_logins = isset($rb_agency_options_arr['rb_agencyinteract_option_redirect_custom_login']) ? $rb_agency_options_arr['rb_agencyinteract_option_redirect_custom_login'] :0;

			if (empty($action) || 'login' == $action) {
				if($rb_agency_option_redirect_custom_logins == 0 || $rb_agency_option_redirect_custom_logins == 2){
					wp_safe_redirect(get_bloginfo("wpurl"). "/profile-login/");
				}
			}
		}

	// Rewrite Login
	// TODO : Refactor
	add_action( 'init', 'rb_agency_interact_login_rewrite' );
		function rb_agency_interact_login_rewrite() {
			$url = get_bloginfo("wpurl");
			add_rewrite_rule($url.(substr($url,-1) != '/' ? '/' : '')."profile-register/?$", 'wp-login.php', 'top');
		}


	// Redirect after Login
		function rb_agency_interact_login_redirect() {
			global $user_ID, $current_user, $wp_roles;
			$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
			$rb_agencyinteract_option_redirect_custom_logins = $rb_agencyinteract_options_arr["rb_agencyinteract_option_redirect_custom_login"];
			if( is_user_logged_in() ) {
				$user_info = get_userdata( $user_ID ); 

				if( current_user_can( 'edit_posts' )) {
					// Is Admin, Redirect to Admin Area
					wp_safe_redirect(admin_url());
				} elseif ( strtotime( $user_info->user_registered ) > ( time() - 172800 ) ) {
					// TODO REFACTOR
					// If user_registered date/time is less than 48hrs from now
					// Message will show for 48hrs after registration
					header("Location: ". get_bloginfo("wpurl"). "/profile-member/account/"); 
				} else {
					header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
				}
			}
		}

		/**
		 * Switch profile-login sidebars to widget
		 *
		 */
		function rb_profilelogin_widgets_init() {
			$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
			$rb_agencyinteract_option_switch_sidebar = isset($rb_agencyinteract_options_arr["rb_agencyinteract_option_switch_sidebar"])?(int)$rb_agencyinteract_options_arr["rb_agencyinteract_option_switch_sidebar"]:"";
			if(isset($rb_agencyinteract_option_switch_sidebar) && $rb_agencyinteract_option_switch_sidebar == 0){
				register_sidebar( array(
					'name' => 'RB Agency Interact: Login Sidebar',
					'id' => 'rb-agency-interact-login-sidebar',
					'before_widget' => '<div>',
					'after_widget' => '</div>',
					'before_title' => '<h3>',
					'after_title' => '</h3>',
				) );
			}
		}
		add_action( 'widgets_init', 'rb_profilelogin_widgets_init' );


	/*

	OBSOLETE  Just use for reference

	add_filter("registration_redirect", "rb_agency_interact_register_redirect");
		function rb_agency_interact_register_redirect() {
			return "/profile-member/";
		}
	add_filter('register', 'rb_agency_interact_register_movepage');
		function rb_agency_interact_register_movepage($link) {
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
	add_filter("register_redirect", "rb_agency_interact_register_redirect");
		function rb_agency_interact_register_redirect() {
			return "/profile-member/";
		}


	// Change Login URL
	// Change Registration Form Submit Titles
	add_filter('register', 'change_admin');
		function change_admin($link) {
			$link = str_replace("Site Admin", "Your Account", $link);
			return $link;
		}
	add_filter('register', 'rb_agency_interact_register_changenames');
		function rb_agency_interact_register_changenames($link) {
			$link = str_replace(">Register<", ">Sign up<", $link);
			return $link;
		}
	 */



	// function for checking male and female filter
	if ( !function_exists('gender_filter') ) {
		function gender_filter($gender=0) {
			global $wpdb;

			$query_gender = "SELECT GenderTitle FROM ".table_agency_data_gender." WHERE GenderID = %d  LIMIT 1";
			$results = $wpdb->get_results($wpdb->prepare($query_gender,$gender));

			$gender_title = "";
			foreach($results as $gname){
				$gender_title = strtolower($gname->GenderTitle);
			}

			if($gender_title == 'male'){
				return "male_filter";
			} elseif($gender_title == 'female'){
				return "female_filter";
			} else {
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
					$result = $wpdb->get_results($wpdb->prepare("SELECT ProfileCustomValue FROM "
							. table_agency_customfield_mux .
							" WHERE ProfileCustomID = ". $customID .
							" AND ProfileCustomValue = %s "
							." AND ProfileID = "
							. $ID,$val),ARRAY_A);
				} else if($type == "date"){
					$result = $wpdb->get_results("SELECT ProfileCustomDateValue FROM "
							. table_agency_customfield_mux .
							" WHERE ProfileCustomID = ". $customID 
							." AND ProfileID = "
							. $ID,ARRAY_A);
				} else {
					$result = $wpdb->get_results("SELECT ProfileCustomValue FROM "
							. table_agency_customfield_mux .
							" WHERE ProfileCustomID = ". $customID ." AND ProfileID = "
							. $ID,ARRAY_A);
				}

				foreach($result as $row){
					if($type == "textbox"){
						return $row["ProfileCustomValue"];
					} elseif($type == "date"){
						return $row["ProfileCustomDateValue"];
					} elseif($type == "dropdown") {
						return "selected";
					} elseif($type == "multiple" && in_array($val,explode(",",$row["ProfileCustomValue"]))) {
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

			$check_type = "SELECT DataTypeTitle FROM ". table_agency_data_type ." WHERE DataTypeID = %d";
			$check_query = $wpdb->get_results($wpdb->prepare($check_type, $id),ARRAY_A);// OR die($wpdb->print_error());
			if(count($check_query) > 0){
				$fetch = current($check_query);
				return $fetch['DataTypeTitle'];
			} else {
				return false;
			}
		}
	}

	// Remove user Profile

	function Profile_Account(){ 
		global $rb_profile_delete;
		echo "<h2>Account Settings</h2><br/>";
		echo "<input type='hidden' id='delete_opt' value='".$rb_profile_delete."'>";
		echo "<input id='self_del' type='button' name='remove' value='Remove My Profile' class='btn-primary'>";

	}

	function Delete_Owner(){

		$page_title = 'RB Account';
		$menu_title = 'Account';
		$capability = 'subscriber';
		$menu_slug = 'delete_profile';

		add_object_page( $page_title, $menu_title, $capability, $menu_slug, 'Profile_Account');

	}

	/*
	 * Self Delete Process for 
	 * Users
	 */

	$rb_profile_delete = isset($rb_agency_options_arr['rb_agency_option_profiledeletion']) ? $rb_agency_options_arr['rb_agency_option_profiledeletion'] : 1;

	if($rb_profile_delete == 2 || $rb_profile_delete == 3){

			add_action('admin_menu', 'Delete_Owner');

			add_action('wp_before_admin_bar_render', 'self_delete');
			if(is_admin()){
				add_action( 'admin_print_footer_scripts', 'delete_script' );
			} else {
				add_action('wp_footer', 'delete_script');
			}
	}



function delete_script() { ?>

	<script type="text/javascript">
		jQuery(document).ready(function(){

			jQuery("#self_del").click(function(){

				var continue_delete = confirm("Are you sure you want to delete your profile?");

				if (continue_delete) {
					// ajax delete
					// alert(jQuery('#delete_opt').val());
					jQuery.ajax({
						type: "POST",
						url: '<?php echo plugins_url( 'rb-agency-interact/theme/userdelete.php' , dirname(__FILE__) ); ?>',
						dataType: "html",
						data: {ID : "<?php echo rb_agency_get_current_userid(); ?>", OPT: jQuery('#delete_opt').val() },

						beforeSend: function() {
						},

						error: function() {
							setTimeout(function(){
							alert("Process Failed. Please try again later.");
							}, 1000);
						},

						success: function(data) {
							if (data != "") {
								setTimeout(function(){
									// alert(data);
									//alert("Deletion success! You will now be redirected to our homepage.");
									window.location.href = "<?php echo get_bloginfo('wpurl'); ?>";
								}, 1000);
							} else {
								setTimeout(function(){
									alert("Failed. Please try again later.");
								}, 1000);
							}
						}
					});
				}
			});
		});
	</script>
	<?php
}

function self_delete() {

	global $wp_admin_bar;

	$href = get_bloginfo('wpurl');
	$title = '<div>' . '<div class="ab-item">User Profile</div></div>';
	$prof_href = $href . '/wp-admin/profile.php'; 
	$account = $href . '/wp-admin/admin.php?page=delete_profile';

	$wp_admin_bar->add_menu( array(
		'parent' => false,
		'id' => 'self_delete',
		'title' => __($title)
	));

	$wp_admin_bar->add_menu(array(
		'parent' => 'self_delete',
		'id' => 'profile_manage',
		'title' => __('<a class="ab-item" href="'.$prof_href.'">Manage Profile</a>'),
	)); 
	$wp_admin_bar->add_menu(array(
		'parent' => 'self_delete',
		'id' => 'actual_delete',
		'title' => __('<a class="ab-item"  href="'.$account.'">Account Settings</a>'),
	));

}


function rb_get_user_linkedID($ProfileID){
		global $wpdb;

		$result = $wpdb->get_row($wpdb->prepare("SELECT ProfileUserLinked FROM ".table_agency_profile." WHERE ProfileID = %d",$ProfileID));
		$found = $wpdb->num_rows;
		if($found > 0)
		return $result->ProfileUserLinked;
		else
		return 0;
}

function rb_get_user_profilstatus(){
		global $wpdb, $current_user;
		$query = "SELECT ProfileIsActive FROM ". table_agency_profile ." WHERE ProfileUserLinked = ". $current_user->ID;
		$results = $wpdb->get_row($query);
		if(isset($results->ProfileIsActive)){
			return $results->ProfileIsActive;
		} else {
			return null;
		}

}


		/**
		 * Switch profile-login sidebars to widget
		 *
		 */
		function rb_interactlogin_widgets_init() {
			register_sidebar( array(
					'name' => 'RB Agency Interact: Login Sidebar',
					'id' => 'rb-agency-interact-login-sidebar',
					'before_widget' => '<div>',
					'after_widget' => '</div>',
					'before_title' => '<h3>',
					'after_title' => '</h3>',
				) );
		}
		add_action( 'widgets_init', 'rb_interactlogin_widgets_init' );

	if(!function_exists('rb_days_diff')){
	function rb_days_diff($_day1 , $day_2 = '',$ret_all = false){
		//$date_dbupdate = '2015-08-28 08:33:52';
		$date_2 = empty( $day_2) ? date("Y-m-d H:i:s") : $day_2;
		$datetime1 = date_create($_day1);
		$datetime2 = date_create($date_2);
		if(function_exists('date_diff')){
			$interval = date_diff($datetime1, $datetime2);
		}else{
			return 0;
		}
		if($ret_all == true){
			return $interval;
		}
		$last_day_update = $interval->days;
		return (int)$last_day_update;	
	}
	}
	
	function rb_interact_sendadmin_pending_info($_userID){
		global $wpdb, $current_user, $wp_roles;
		$query_lastinfo = "SELECT ProfileDateUpdated,ProfileIsActive,ProfileUserLinked FROM  " . table_agency_profile . " WHERE ProfileID=$_userID";
		$results_lastinfo = $wpdb->get_row( $wpdb->prepare( $query_lastinfo ), ARRAY_A );
		
		$_lastinfoDateUpdated = $results_lastinfo['ProfileDateUpdated'];// => 2015-08-27 08:57:33
		$_lastinfoStatus = $results_lastinfo['ProfileIsActive'];
		$_wp_userID = $results_lastinfo['ProfileUserLinked'];
		
		//admin is requesting for this event
		if(is_user_logged_in() && current_user_can( 'edit_posts' )){
			wp_new_user_notification_pending($_wp_userID , false);
			return true;
		}
		
		
		//echo 'test - '.$_lastinfoStatus;
		//means its active before then proceed to email.
		
		if($_lastinfoStatus == 1){
			//echo 'active before';
			wp_new_user_notification_pending($_wp_userID , false);
			return true;
		}else{
			//either currently inactive or pending for approval.
			$last_day_update = rb_days_diff($_lastinfoDateUpdated);
			if($last_day_update >=1){
				wp_new_user_notification_pending($_wp_userID  , false);
				return true;
				//echo 'More than 1 day last update.';
			}
			//echo 'day based';
		}
		return false;
	}
		
		
?>