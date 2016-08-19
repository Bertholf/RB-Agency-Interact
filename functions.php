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

			if (get_query_var( 'type' ) == "membershipsuccess"){
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
		//FIXED if wp_mail function fail to send email
	remove_filter('wp_mail_from','custom_wp_mail_from');
	remove_filter('wp_mail_from_name','custom_wp_mail_from_name');
	add_filter('wp_mail_from','custom_wp_mail_from');
	function custom_wp_mail_from($email) {
	  return get_option('admin_email');
	}
	 
	add_filter('wp_mail_from_name','custom_wp_mail_from_name');
	function custom_wp_mail_from_name($name) {
	  return get_option('blogname');
	}

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
					wp_mail($user_email, sprintf(__('%s Registration Successful! Login Details',RBAGENCY_interact_TEXTDOMAIN), get_option('blogname')), nl2br($message), $headers);
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
				$headers = array();
				$message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "<br/><br/>";
				$message .= sprintf(__('Username: %s'), $user_login) . "<br/><br/>";
				$message .= sprintf(__('E-mail: %s'), $user_email) . "<br/>";

				$rb_agency_options_arr = get_option('rb_agency_options');
				$_sendEmailtoAdmin = isset($rb_agency_options_arr['rb_agency_option_notify_admin_new_user'])?$rb_agency_options_arr['rb_agency_option_notify_admin_new_user']:0;
				
				if($_sendEmailtoAdmin == 1){
					@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);
				}

				if ( empty($plaintext_pass) )  
					return;

				//add_filter( 'wp_mail_content_type', function( $content_type ) {
					//return 'text/html';
				//});
				//add_filter( 'wp_mail_content_type', 'set_html_content_type' );
				
	
				$message  = __('Hi there,', RBAGENCY_interact_TEXTDOMAIN) . "\r\n\r\n";
				$message .= sprintf(__("Thanks for joining %s! Here's how to log in:", RBAGENCY_interact_TEXTDOMAIN), get_option('blogname')) ; 
				$message .= "\r\n\r\n";
				$message .= get_option('home') .'/profile-login/'; 
				$message .= "\r\n";
				$message .= sprintf(__('Username: %s', RBAGENCY_interact_TEXTDOMAIN), $user_login) . "\r\n"; 
				$message .= sprintf(__('Password: %s', RBAGENCY_interact_TEXTDOMAIN), $plaintext_pass) . "\r\n\r\n"; 
				$message .= sprintf(__('If you have any problems, please contact us at %s.', RBAGENCY_interact_TEXTDOMAIN), get_option('admin_email')) . "\r\n\r\n"; 
				$message .= __('Regards,', RBAGENCY_interact_TEXTDOMAIN)."\r\n";
				$message .= get_option('blogname') . __(' Team', RBAGENCY_interact_TEXTDOMAIN) ."\r\n"; 
				$message .= get_option('home') ."\r\n"; 
				$message .= '<img src="'.site_url().$rb_agency_options_arr['rb_agency_option_agencylogo'].'"/>';

				//$headers[]  = 'MIME-Version: 1.0'. "\r\n";
				//$headers[] = 'Content-type: text/html; charset=iso-8859-1'. "\r\n";
				$headers[] = 'From: '. get_option('blogname') .' <'. get_option('admin_email') .'>' . "\r\n";
				
				

				

				wp_mail($user_email, sprintf(__('%s Registration Successful! Login Details', RBAGENCY_interact_TEXTDOMAIN), get_option('blogname')), $message, $headers);

				//remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
			}

			
			

		}


	}

	//add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	function set_html_content_type($content_type) {

	    return 'text/html';
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
		global $current_user,$wpdb;
		get_currentuserinfo();
		$currentUserID = $current_user->ID;
		$rb_agencyinteract_options = get_option('rb_agencyinteract_options');
		$rb_profile_delete = $rb_agencyinteract_options["rb_agencyinteract_option_profiledeletion"];
		$profileStatus = getProfileStatus();
		if($rb_profile_delete > 1){
			echo "<h2>Account Settings</h2><br/>";
			echo "<input type='hidden' id='delete_opt' value='".$rb_profile_delete."'>";
			echo "<input type='hidden' id='userID' value='".$currentUserID."'>";
			if($profileStatus == 2 || $profileStatus == 0){//archived
				$delLabel = $rb_profile_delete == 2 ? 'Remove My Profile' : 'Reactivate Profile';
				echo "<input type='hidden' id='reactivate_acc' value='1' >";
			}else{
				echo "<input type='hidden' id='reactivate_acc' value='0' >";
				$delLabel = $rb_profile_delete == 2 ? 'Remove My Profile' : 'Deactivate Profile';
			}
			
			echo "<input id='self_del' type='button' name='remove' value='".$delLabel."' class='btn-primary'>";
		}
		

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



function getProfileIDbyUserID(){
	global $current_user,$wpdb;
	get_currentuserinfo();
	$currentUserID = $current_user->ID;
	$q = "SELECT ProfileID FROM ".table_agency_profile." WHERE ProfileUserLinked = %d";
	$results = $wpdb->get_results($wpdb->prepare($q,$currentUserID));
	foreach($results as $result)
		return $result->ProfileID;
	
}

function getProfileStatus(){
	global $wpdb;
	get_currentuserinfo();
	$profileID = getProfileIDbyUserID();
	$q = "SELECT ProfileIsActive FROM ".table_agency_profile." WHERE profileID = %d";
	$results = $wpdb->get_results($wpdb->prepare($q,$profileID));

	foreach($results as $result)
		return $result->ProfileIsActive;
}

function delete_script() { ?>

	<script type="text/javascript">
		jQuery(document).ready(function(){

			jQuery("#self_del").click(function(){

				if(jQuery('#delete_opt').val() > 1){
					if(jQuery('#delete_opt').val() == 2){
						var continue_delete = confirm("Are you sure you want to delete your profile?");
					}else{
						if(jQuery("#reactivate_acc").val() == 1){
							var continue_delete = confirm("Are you sure you want to re-activate your profile?");
						}else{
							var continue_delete = confirm("Are you sure you want to de-activate your profile?");
						}
						
					}
				}
				
				

				if (continue_delete) {
					// ajax delete
					// alert(jQuery('#delete_opt').val());
					jQuery.ajax({
						type: "POST",
						url: '<?php echo plugins_url( 'rb-agency-interact/theme/userdelete.php' , dirname(__FILE__) ); ?>',
						dataType: "html",
						data: {ID : "<?php echo getProfileIDbyUserID(); ?>", OPT: jQuery('#delete_opt').val(), REACTIVATE: jQuery("#reactivate_acc").val(),USERID: jQuery("#userID").val()},

						beforeSend: function() {
						},

						error: function() {
							setTimeout(function(){
							alert("Process Failed. Please try again later.");
							}, 1000);
						},

						success: function(data) {
							console.log(data);
							if (data != "") {
								setTimeout(function(){
									// alert(data);
									//alert("Deletion success! You will now be redirected to our homepage.");
									if(jQuery('#delete_opt').val() == 2){
										console.log(data);
										alert("Profile successfully deleted! You will now be redirected to our homepage.");
										//window.location.href = "<?php echo get_bloginfo('wpurl'); ?>";
									}else{
										if(jQuery("#reactivate_acc").val() == 1){
											alert("Profile successfully re-activated!");
											window.location.href = "<?php echo get_bloginfo('wpurl')."/profile-member/"; ?>";
										}else{
											alert("Profile successfully archived! You will now be redirected to our homepage.");
											window.location.href = "<?php echo get_bloginfo('wpurl'); ?>";
										}
										
									}
									
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
		
	
function rb_login_shortcode(){
		session_start();
		$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
		$rb_agencyinteract_option_redirect_first_time = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time']:1;
		$rb_agencyinteract_option_redirect_first_time_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url']:"/profile-member/account/";
		$rb_agencyinteract_option_redirect_custom_login = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login']:0;
		$rb_agencyinteract_option_redirect_afterlogin = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin']:1;
		$rb_agencyinteract_option_redirect_afterlogin_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url']:"/profile-member/account/";
		

		//if login submit
		if ( $_SERVER['REQUEST_METHOD'] == "POST" && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) {
			
			global $error;
		    $login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => isset($_POST['remember-me'])?$_POST['remember-me']:false ), false );

		    get_currentuserinfo();


			if(!is_wp_error($login)) {
				wp_set_current_user($login->ID);// populate

				rb_get_user_info_for_shortcode_login();
			} else {
				$error .= __( $login->get_error_message(), RBAGENCY_interact_TEXTDOMAIN);
				$have_error = true;
			}
		}
		//:end login submit

		//this is for reset password
		if($_GET['action'] == 'rp'){
			$_SESSION['login_username'] = $_GET['login'];
			wp_redirect(site_url()."/wp-login.php?action=".$_GET['action']."&key=".$_GET['key']."&login=".$_GET['login']);
			exit();
		}
		if($_GET['action'] == 'resetpass'){
			global $wpdb;
			$uid = '';
			$user_table = $wpdb->prefix.'users';
			$users = $wpdb->get_results("SELECT ID FROM $user_table WHERE user_login =  '".$_SESSION['login_username']."'");
			foreach($users as $user){
				$uid = $user->ID;
			}
			//echo $uid;
			wp_set_password( $_POST['pass1-text'], $uid ) ;
		}
		//:end this is for reset password


		// Already logged in 
		if(is_user_logged_in()){
			global $user_ID;
			$user_ID = get_current_user_id();

			if(current_user_can("edit_posts")) {
				wp_redirect(admin_url("admin.php?page=rb_agency_menu"));

			}

			//redirect to job
			if(isset($_GET["h"])){
				wp_redirect(get_bloginfo("url").$_GET["h"]);
				exit();
			}

			$wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
			$is_casting  = $wpdb->num_rows;

			if( $is_casting > 0){
				wp_redirect(get_bloginfo("url"). "/casting-dashboard/");

			} else { // user is a model/talent but wp user_id is not linked to any rb profile.
				if($rb_agencyinteract_option_redirect_afterlogin == 1){
					wp_redirect(get_bloginfo("url"). "/profile-member/");

				} else {
					wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);

				}
			}

			$OUTPUT = '';

			// Call Header
			$OUTPUT .= $rb_header = RBAgency_Common::rb_header();
			$OUTPUT .= "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";

			global $user_ID; 
			$login = get_userdata( $user_ID );
			
			get_user_login_info();


			$OUTPUT .= "    <p class=\"alert\">\n";
							printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', RBAGENCY_interact_TEXTDOMAIN), "/profile-member/", $login->display_name );
			$OUTPUT .= "		<a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __('Log out of this account', RBAGENCY_interact_TEXTDOMAIN) ."\">". __('Log out &raquo;', RBAGENCY_interact_TEXTDOMAIN) ."</a>\n";
			$OUTPUT .= "    </p><!-- .alert -->\n";
			$OUTPUT .= "</div><!-- #rbcontent -->\n";

			// Call Footer
			$OUTPUT .= $rb_footer = RBAgency_Common::rb_footer();

		}// :end already logged in

		else{ //not logged in

			// Prepare Page

			// Call Header
			$OUTPUT .= $rb_header = RBAgency_Common::rb_header();

				$OUTPUT .= "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";

					// Show Login Form
					$hideregister = true;
					//include("include-login.php");
				$OUTPUT .= rb_login_form_for_shortcode();

			// Call Footer
			$OUTPUT .= $rb_footer = RBAgency_Common::rb_footer();

		}

		return $OUTPUT;

	}	

	add_shortcode('login-for-all','rb_login_shortcode');

	function rb_get_user_info_for_shortcode_login(){
		global $user_ID, $wpdb;
		$redirect = isset($_POST["lastviewed"])?$_POST["lastviewed"]:"";
		get_currentuserinfo();
		$user_info = get_userdata( $user_ID );

		// Check if user is registered as Model/Talent
    	$profile_is_active = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_profile." WHERE ProfileUserLinked = %d  ",$user_ID));
    	$is_model_or_talent  = $wpdb->num_rows;

    	// Check if user is agent
    	$casting_is_active = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
    	$is_casting_agent  = $wpdb->num_rows;

    	// login options
    	$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
		$rb_agencyinteract_option_redirect_first_time = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time']:1;
		$rb_agencyinteract_option_redirect_first_time_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url']:"/profile-member/account/";
		$rb_agencyinteract_option_redirect_custom_login = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login']:0;
		$rb_agencyinteract_option_redirect_afterlogin = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin']:1;
		$rb_agencyinteract_option_redirect_afterlogin_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url']:"/profile-member/account/";
		
		//start conditions	
		if( (isset($user_ID) && ($is_model_or_talent > 0)) || current_user_can("edit_posts") || ($is_casting_agent > 0)){

			if(!empty($redirect)){
				wp_redirect($redirect);
				exit();
			}

			if(current_user_can("edit_posts")) {
				wp_redirect(admin_url("admin.php?page=rb_agency_menu"));
				exit();
			}else{ //if none admin

				//if model or talent
				if($is_model_or_talent > 0){

					$rb_agency_new_registeredUser = get_user_meta($user_ID,'rb_agency_new_registeredUser',true);
					if(!empty($rb_agency_new_registeredUser)){

						if($rb_agencyinteract_option_redirect_first_time == 1){
							wp_redirect(get_bloginfo("url"). "/profile-member/account/");
						} else {
							wp_redirect($rb_agencyinteract_option_redirect_first_time_url);
						}

					}else{

						if(get_user_meta($user_ID, 'rb_agency_interact_clientdata', true)){
							wp_redirect(get_bloginfo("url"). "/casting-dashboard/");
						} else {
							if($rb_agencyinteract_option_redirect_afterlogin == 1){

								if(isset($_GET["h"])){
									wp_redirect(get_bloginfo("url").$_GET["h"]);
									exit();
								}else{
									wp_redirect(get_bloginfo("url"). "/profile-member/");
								}
											

							} else {
										
								if(isset($_GET["h"])){
									wp_redirect(get_bloginfo("url").$_GET["h"]);
									exit();
								}else{
									wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);
								}
											

							}
						}


					}
				} // :end if model or talent
				elseif($is_casting_agent > 0) {//if casting agent

					$casting_status = "";
					$q = "SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = ".$user_ID;
					$result = $wpdb->get_results($q);
					foreach($result as $r){
						$casting_status = $r->CastingIsActive;
					}

					$url = $rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_agent'];
					if( !empty($url)){

						$customUrl = '/casting-dashboard/';
					}else{

						$customUrl = $rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_agent_url'];
					}

					if($casting_status == 3){
						//header("Location:".get_bloginfo("wpurl").'/casting-pending?status=pending');
						wp_redirect(get_bloginfo("wpurl").'/casting-pending?status=pending');
						exit();
					}else{
						//header("Location: ". get_bloginfo("wpurl").  $customUrl);
						wp_redirect(get_bloginfo("wpurl").  $customUrl);
						exit();
					}

				} // :end if casting agent
				
			}//:end if not admin

		}//:end conditions
		elseif($profile_is_active->ProfileIsActive == 3){
			wp_redirect(get_bloginfo("url"). "/profile-member/pending/"); 
		} // :end if profile is pending
		else{

			$wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
			$is_casting  = $wpdb->num_rows;

			if( $is_casting > 0){
				wp_logout();
				wp_redirect(get_bloginfo("url"). "/profile-login/?ref=casting");

			} else { // user is a model/talent but wp user_id is not linked to any rb profile.

				if($rb_agencyinteract_option_redirect_afterlogin == 1){

					if(isset($_GET["h"])){
						wp_redirect(get_bloginfo("url").$_GET["h"]);
						exit();
					}else{
						wp_redirect(get_bloginfo("url"). "/profile-member/");
					}
										

				} else {

					if(isset($_GET["h"])){
						wp_redirect(get_bloginfo("url").$_GET["h"]);
						exit();
					}else{
						wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);
					}
							
				}
			}
		} // : end last else
	}


	function rb_login_form_for_shortcode(){
		$LOGIN_URL = trim($_SERVER['REQUEST_URI'],'/');
		$OUTPUT = '';
		/* Check if users can register. */
		$registration = get_option( 'rb_agencyinteract_options' );
		$rb_agencyinteract_option_registerallow = isset($registration["rb_agencyinteract_option_registerallow"]) ?$registration["rb_agencyinteract_option_registerallow"]:"";
		$rb_agencyinteract_option_registerallowAgentProducer = isset($registration['rb_agencyinteract_option_registerallowAgentProducer'])?$registration['rb_agencyinteract_option_registerallowAgentProducer']:0;
		$rb_agencyinteract_option_switch_sidebar = isset($registration["rb_agencyinteract_option_switch_sidebar"])?(int)$registration["rb_agencyinteract_option_switch_sidebar"]:"";

		if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow )) {
			$widthClass = "half";
		} else {
			$widthClass = "full";
		}

	// File Path: interact/theme/include-login.php
	// Site Url : /profile-login/

		$OUTPUT .= "     <div id=\"rbsignin-register\" class=\"rbinteract\">\n";
			$ref = get_query_var("ref");

		if (isset($error)){
		$OUTPUT .= "<p class=\"error\">". $error ."</p>\n";
		}
		if (isset($ref) && $ref == "pending-approval") {
		$OUTPUT .= "<p id=\"message\" class=\"updated\">". __("Your account is pending for approval.", RBAGENCY_interact_TEXTDOMAIN). "</p>\n";
		}
		if (isset($ref) && $ref == "casting") {
		$OUTPUT .= "<p id=\"message\" class=\"updated\">". __("Your account is not registered as Talent/Model.", RBAGENCY_interact_TEXTDOMAIN).  __(" Click", RBAGENCY_interact_TEXTDOMAIN)." <a href=\"".get_bloginfo("url")."/casting-login/\">".__("here", RBAGENCY_interact_TEXTDOMAIN)."</a> ".__("to login as Casting.", RBAGENCY_interact_TEXTDOMAIN)."</p>\n";
		}


		if(isset($ref) && $ref == "reset_password"){
			$OUTPUT .= "<p  id=\"message\" class=\"updated\">".__("Check your e-mail for the reset link to create a new password.", RBAGENCY_interact_TEXTDOMAIN)."</p>\n";
		}

		if(isset($_GET['action']) && $_GET['action']== "resetpass"){
			$OUTPUT .= "<p  id=\"message\" class=\"updated\">".__("Your password has been reset.", RBAGENCY_interact_TEXTDOMAIN)."</p>\n";
		}

		$OUTPUT .= "        <div id=\"rbsign-in\" class=\"inline-block\">\n";
		$OUTPUT .= "          <h1>". __("Members Sign in", RBAGENCY_interact_TEXTDOMAIN). "</h1>\n";
		if(isset($_GET["h"])){
			$OUTPUT .= "          <form name=\"loginform\" id=\"login\" action=\"".site_url().$LOGIN_URL."\" method=\"post\">\n";
		}else{
			$OUTPUT .= "          <form name=\"loginform\" id=\"login\" action=\"http://castinghive.com/cmi/new-login/\" method=\"post\">\n";
		}

		$OUTPUT .= "            <div class=\"field-row\">\n";
		$OUTPUT .= "              <label for=\"user-name\">". __("Username", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"text\" name=\"user-name\" value=\"". (isset($_POST['user-name']) ? esc_html($_POST['user-name']):"") ."\" id=\"user-name\" />\n";
		$OUTPUT .= "            </div>\n";
		$OUTPUT .= "            <div class=\"field-row\">\n";
		$OUTPUT .= "              <label for=\"password\">". __("Password", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"password\" name=\"password\" value=\"\" id=\"password\" /> <a href=\"". get_bloginfo('wpurl') ."/wp-login.php?action=lostpassword&redirect_to=".$_SERVER['REQUEST_URI']."?ref=reset_password\">". __("forgot password", RBAGENCY_interact_TEXTDOMAIN). "?</a>\n";
		$OUTPUT .= "            </div>\n";
		$OUTPUT .= "            <div class=\"field-row\">\n";
		$OUTPUT .= "              <label><input type=\"checkbox\" name=\"remember-me\" value=\"forever\" /> ". __("Keep me signed in", RBAGENCY_interact_TEXTDOMAIN). "</label>\n";
		$OUTPUT .= "            </div>\n";
		$OUTPUT .= "            <div class=\"field-row submit-row\">\n";
		$OUTPUT .= "              <input type=\"hidden\" name=\"action\" value=\"log-in\" />\n";
		$OUTPUT .= "              <input type=\"submit\" value=\"". __("Sign In", RBAGENCY_interact_TEXTDOMAIN). "\" /><br />\n";
		$OUTPUT .= "            </div>\n";
		$OUTPUT .= "          </form>\n";
		$OUTPUT .= "        </div> <!-- rbsign-in -->\n";

		if(isset($rb_agencyinteract_option_switch_sidebar) && $rb_agencyinteract_option_switch_sidebar == 1){
					$OUTPUT .= "        <div id=\"rbsign-up\" class=\"inline-block\">\n";
					if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow == 1)) {

						$OUTPUT .= "          <div id=\"talent-register\" class=\"register\">\n";
						$OUTPUT .= "            <h1>". __("Not a member", RBAGENCY_interact_TEXTDOMAIN). "?</h1>\n";
						$OUTPUT .= "            <h3>". __("Talent", RBAGENCY_interact_TEXTDOMAIN). " - ". __("Register here", RBAGENCY_interact_TEXTDOMAIN). "</h3>\n";
						$OUTPUT .= "            <ul>\n";
						$OUTPUT .= "              <li>". __("Create your free profile page", RBAGENCY_interact_TEXTDOMAIN). "</li>\n";
						$OUTPUT .= "              <li>". __("Apply to Auditions & Jobs", RBAGENCY_interact_TEXTDOMAIN). "</li>\n";
						$OUTPUT .= "            </ul>\n";
						$OUTPUT .= "              <input type=\"button\" onClick=\"location.href='". get_bloginfo("wpurl") ."/profile-register/'\" value=\"". __("Register Now", RBAGENCY_interact_TEXTDOMAIN). "\" />\n";
						$OUTPUT .= "          </div> <!-- talent-register -->\n";
						$OUTPUT .= "          <div class=\"clear line\"></div>\n";


						}
					$OUTPUT .= "        </div> <!-- rbsign-up -->\n";
		}
		else {
			$OUTPUT .= "        <div id=\"rbsign-up\" class=\"inline-block\">\n";
			$OUTPUT .= "          <div id=\"talent-register\" class=\"register\">\n";
			if ( dynamic_sidebar('rb-agency-interact-login-sidebar') ) :endif; 
			$OUTPUT .= "          </div> <!-- talent-register -->\n";
			$OUTPUT .= "          <div class=\"clear line\"></div>\n";
			$OUTPUT .= "        </div> <!-- rbsign-up -->\n";

		}

		$OUTPUT .= "      <div class=\"clear line\"></div>\n";
		$OUTPUT .= "      </div>\n";
				return $OUTPUT;
	}





	/**
	*
	* FOR TALENTs
	*
	*/
function talent_model_login_form($atts){

		$a = shortcode_atts( array(
			        'registration_widget' => "false"
			    ), $atts );
		
		session_start();
		$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
		$rb_agencyinteract_option_redirect_first_time = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time']:1;
		$rb_agencyinteract_option_redirect_first_time_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url']:"/profile-member/account/";
		$rb_agencyinteract_option_redirect_custom_login = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login']:0;
		$rb_agencyinteract_option_redirect_afterlogin = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin']:1;
		$rb_agencyinteract_option_redirect_afterlogin_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url']:"/profile-member/account/";
		

		//if login submit
		if ( $_SERVER['REQUEST_METHOD'] == "POST" && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) {
			
			global $error;
		    $login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => isset($_POST['remember-me'])?$_POST['remember-me']:false ), false );

		    get_currentuserinfo();


			if(!is_wp_error($login)) {
				wp_set_current_user($login->ID);// populate

				rb_get_user_info_for_shortcode_talent_login($show_registration);
			} else {
				$error .= __( $login->get_error_message(), RBAGENCY_interact_TEXTDOMAIN);
				$have_error = true;
			}
		}
		//:end login submit

		//this is for reset password
		if($_GET['action'] == 'rp'){
			$_SESSION['login_username'] = $_GET['login'];
			wp_redirect(site_url()."/wp-login.php?action=".$_GET['action']."&key=".$_GET['key']."&login=".$_GET['login']);
			exit();
		}
		if($_GET['action'] == 'resetpass'){
			global $wpdb;
			$uid = '';
			$user_table = $wpdb->prefix.'users';
			$users = $wpdb->get_results("SELECT ID FROM $user_table WHERE user_login =  '".$_SESSION['login_username']."'");
			foreach($users as $user){
				$uid = $user->ID;
			}
			//echo $uid;
			wp_set_password( $_POST['pass1-text'], $uid ) ;
		}
		//:end this is for reset password


		// Already logged in 
		if(is_user_logged_in()){
			global $user_ID;
			$user_ID = get_current_user_id();

			if(current_user_can("edit_posts")) {
				wp_redirect(admin_url("admin.php?page=rb_agency_menu"));

			}

			//redirect to job
			if(isset($_GET["h"])){
				wp_redirect(get_bloginfo("url").$_GET["h"]);
				exit();
			}

			$wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
			$is_casting  = $wpdb->num_rows;

			if( $is_casting > 0){
				wp_redirect(get_bloginfo("url"). "/casting-dashboard/");

			} else { // user is a model/talent but wp user_id is not linked to any rb profile.
				if($rb_agencyinteract_option_redirect_afterlogin == 1){
					wp_redirect(get_bloginfo("url"). "/profile-member/");

				} else {
					wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);

				}
			}

			$OUTPUT = '';

			// Call Header
			//$OUTPUT .= $rb_header = RBAgency_Common::rb_header();
			$OUTPUT .= "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";

			global $user_ID; 
			$login = get_userdata( $user_ID );
			
			get_user_login_info();


			$OUTPUT .= "    <p class=\"alert\">\n";
							printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', RBAGENCY_interact_TEXTDOMAIN), "/profile-member/", $login->display_name );
			$OUTPUT .= "		<a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __('Log out of this account', RBAGENCY_interact_TEXTDOMAIN) ."\">". __('Log out &raquo;', RBAGENCY_interact_TEXTDOMAIN) ."</a>\n";
			$OUTPUT .= "    </p><!-- .alert -->\n";
			$OUTPUT .= "</div><!-- #rbcontent -->\n";

			// Call Footer
			//$OUTPUT .= $rb_footer = RBAgency_Common::rb_footer();

		}// :end already logged in

		else{ //not logged in

			// Prepare Page

			// Call Header
			//$OUTPUT .= $rb_header = RBAgency_Common::rb_header();

				$OUTPUT .= "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";

					// Show Login Form
					$hideregister = true;
					//include("include-login.php");
					if($a['registration_widget'] == 'true') {
						
						$OUTPUT .= rb_login_form_for_talent_wid_reg();
					}else{
						$OUTPUT .= rb_login_form_for_talent_wo_reg();
					}
					
				

			// Call Footer
			//$OUTPUT .= $rb_footer = RBAgency_Common::rb_footer();

		}

		return $OUTPUT;

	}	

	add_shortcode('talent_login_form','talent_model_login_form');


	function rb_get_user_info_for_shortcode_talent_login($show_registration){
		global $user_ID, $wpdb;
		$redirect = isset($_POST["lastviewed"])?$_POST["lastviewed"]:"";
		get_currentuserinfo();
		$user_info = get_userdata( $user_ID );

		// Check if user is registered as Model/Talent
    	$profile_is_active = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_profile." WHERE ProfileUserLinked = %d  ",$user_ID));
    	$is_model_or_talent  = $wpdb->num_rows;

    	// Check if user is agent
    	$casting_is_active = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
    	$is_casting_agent  = $wpdb->num_rows;

    	// login options
    	$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
		$rb_agencyinteract_option_redirect_first_time = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time']:1;
		$rb_agencyinteract_option_redirect_first_time_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url']:"/profile-member/account/";
		$rb_agencyinteract_option_redirect_custom_login = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login']:0;
		$rb_agencyinteract_option_redirect_afterlogin = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin']:1;
		$rb_agencyinteract_option_redirect_afterlogin_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url']:"/profile-member/account/";
		
		//start conditions	
		if( isset($user_ID) && ($is_model_or_talent > 0) || current_user_can("edit_posts")){

			if(!empty($redirect)){
				wp_redirect($redirect);
				exit();
			}

			if(current_user_can("edit_posts")) {
				wp_redirect(admin_url("admin.php?page=rb_agency_menu"));
				exit();
			}else{ //if none admin

				//if model or talent
				if($is_model_or_talent > 0){

					$rb_agency_new_registeredUser = get_user_meta($user_ID,'rb_agency_new_registeredUser',true);
					if(!empty($rb_agency_new_registeredUser)){

						if($rb_agencyinteract_option_redirect_first_time == 1){
							wp_redirect(get_bloginfo("url"). "/profile-member/account/");
						} else {
							wp_redirect($rb_agencyinteract_option_redirect_first_time_url);
						}

					}else{

						if(get_user_meta($user_ID, 'rb_agency_interact_clientdata', true)){
							wp_redirect(get_bloginfo("url"). "/casting-dashboard/");
						} else {
							if($rb_agencyinteract_option_redirect_afterlogin == 1){

								if(isset($_GET["h"])){
									wp_redirect(get_bloginfo("url").$_GET["h"]);
									exit();
								}else{
									wp_redirect(get_bloginfo("url"). "/profile-member/");
								}
											

							} else {
										
								if(isset($_GET["h"])){
									wp_redirect(get_bloginfo("url").$_GET["h"]);
									exit();
								}else{
									wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);
								}
											

							}
						}


					}
				} // :end if model or talent
				elseif($is_casting_agent > 0) {//if casting agent

					//should notify user that we is unable to login here

				} // :end if casting agent
				
			}//:end if not admin

		}//:end conditions
		elseif($profile_is_active->ProfileIsActive == 3){
			wp_redirect(get_bloginfo("url"). "/profile-member/pending/"); 
		} // :end if profile is pending
		else{

			$wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
			$is_casting  = $wpdb->num_rows;

			if( $is_casting > 0){
				wp_logout();
				wp_redirect(get_bloginfo("url"). "/profile-login/?ref=casting");

			} else { // user is a model/talent but wp user_id is not linked to any rb profile.

				if($rb_agencyinteract_option_redirect_afterlogin == 1){

					if(isset($_GET["h"])){
						wp_redirect(get_bloginfo("url").$_GET["h"]);
						exit();
					}else{
						wp_redirect(get_bloginfo("url"). "/profile-member/");
					}
										

				} else {

					if(isset($_GET["h"])){
						wp_redirect(get_bloginfo("url").$_GET["h"]);
						exit();
					}else{
						wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);
					}
							
				}
			}
		} // : end last else
	}


		function rb_login_form_for_talent_wid_reg(){
			$LOGIN_URL = trim($_SERVER['REQUEST_URI'],'/');
			$OUTPUT = '';
			/* Check if users can register. */
			$registration = get_option( 'rb_agencyinteract_options' );
			$rb_agencyinteract_option_registerallow = isset($registration["rb_agencyinteract_option_registerallow"]) ?$registration["rb_agencyinteract_option_registerallow"]:"";
			$rb_agencyinteract_option_registerallowAgentProducer = isset($registration['rb_agencyinteract_option_registerallowAgentProducer'])?$registration['rb_agencyinteract_option_registerallowAgentProducer']:0;
			$rb_agencyinteract_option_switch_sidebar = isset($registration["rb_agencyinteract_option_switch_sidebar"])?(int)$registration["rb_agencyinteract_option_switch_sidebar"]:"";

			if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow )) {
				$widthClass = "half";
			} else {
				$widthClass = "full";
			}

		// File Path: interact/theme/include-login.php
		// Site Url : /profile-login/

			$OUTPUT .= "     <div id=\"rbsignin-register\" class=\"rbinteract\">\n";
				$ref = get_query_var("ref");

			if (isset($error)){
			$OUTPUT .= "<p class=\"error\">". $error ."</p>\n";
			}
			if (isset($ref) && $ref == "pending-approval") {
			$OUTPUT .= "<p id=\"message\" class=\"updated\">". __("Your account is pending for approval.", RBAGENCY_interact_TEXTDOMAIN). "</p>\n";
			}
			if (isset($ref) && $ref == "casting") {
			$OUTPUT .= "<p id=\"message\" class=\"updated\">". __("Your account is not registered as Talent/Model.", RBAGENCY_interact_TEXTDOMAIN).  __(" Click", RBAGENCY_interact_TEXTDOMAIN)." <a href=\"".get_bloginfo("url")."/casting-login/\">".__("here", RBAGENCY_interact_TEXTDOMAIN)."</a> ".__("to login as Casting.", RBAGENCY_interact_TEXTDOMAIN)."</p>\n";
			}


			if(isset($ref) && $ref == "reset_password"){
				$OUTPUT .= "<p  id=\"message\" class=\"updated\">".__("Check your e-mail for the reset link to create a new password.", RBAGENCY_interact_TEXTDOMAIN)."</p>\n";
			}

			if(isset($_GET['action']) && $_GET['action']== "resetpass"){
				$OUTPUT .= "<p  id=\"message\" class=\"updated\">".__("Your password has been reset.", RBAGENCY_interact_TEXTDOMAIN)."</p>\n";
			}

			$OUTPUT .= "        <div id=\"rbsign-in\" class=\"inline-block\">\n";
			$OUTPUT .= "          <h1>". __("Talents Sign In", RBAGENCY_interact_TEXTDOMAIN). "</h1>\n";
			if(isset($_GET["h"])){
				$OUTPUT .= "          <form name=\"loginform\" id=\"login\" action=\"". network_site_url("/"). "profile-login/?h=".$_GET["h"]."\" method=\"post\">\n";
			}else{
				$OUTPUT .= "          <form name=\"loginform\" id=\"login\" action=\"". network_site_url("/"). "profile-login/\" method=\"post\">\n";
			}

			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label for=\"user-name\">". __("Username", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"text\" name=\"user-name\" value=\"". (isset($_POST['user-name']) ? esc_html($_POST['user-name']):"") ."\" id=\"user-name\" />\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label for=\"password\">". __("Password", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"password\" name=\"password\" value=\"\" id=\"password\" /> <a href=\"". get_bloginfo('wpurl') ."/wp-login.php?action=lostpassword&redirect_to=".$_SERVER['REQUEST_URI']."?ref=reset_password\">". __("forgot password", RBAGENCY_interact_TEXTDOMAIN). "?</a>\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label><input type=\"checkbox\" name=\"remember-me\" value=\"forever\" /> ". __("Keep me signed in", RBAGENCY_interact_TEXTDOMAIN). "</label>\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row submit-row\">\n";
			$OUTPUT .= "              <input type=\"hidden\" name=\"action\" value=\"log-in\" />\n";
			$OUTPUT .= "              <input type=\"submit\" value=\"". __("Sign In", RBAGENCY_interact_TEXTDOMAIN). "\" /><br />\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "          </form>\n";
			$OUTPUT .= "        </div> <!-- rbsign-in -->\n";

			//if( isset($rb_agencyinteract_option_switch_sidebar) && $rb_agencyinteract_option_switch_sidebar == 1){
						$OUTPUT .= "        <div id=\"rbsign-up\" class=\"inline-block\">\n";
						if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow == 1)) {

							$OUTPUT .= "          <div id=\"talent-register\" class=\"register\">\n";
							$OUTPUT .= "            <h1>". __("Not a member", RBAGENCY_interact_TEXTDOMAIN). "?</h1>\n";
							$OUTPUT .= "            <h3>". __("Talent", RBAGENCY_interact_TEXTDOMAIN). " - ". __("Register here", RBAGENCY_interact_TEXTDOMAIN). "</h3>\n";
							$OUTPUT .= "            <ul>\n";
							$OUTPUT .= "              <li>". __("Create your free profile page", RBAGENCY_interact_TEXTDOMAIN). "</li>\n";
							$OUTPUT .= "              <li>". __("Apply to Auditions & Jobs", RBAGENCY_interact_TEXTDOMAIN). "</li>\n";
							$OUTPUT .= "            </ul>\n";
							$OUTPUT .= "              <input type=\"button\" onClick=\"location.href='". get_bloginfo("wpurl") ."/profile-register/'\" value=\"". __("Register Now", RBAGENCY_interact_TEXTDOMAIN). "\" />\n";
							$OUTPUT .= "          </div> <!-- talent-register -->\n";
							$OUTPUT .= "          <div class=\"clear line\"></div>\n";


							}
						$OUTPUT .= "        </div> <!-- rbsign-up -->\n";
			//}
			//else {
				$OUTPUT .= "        <div id=\"rbsign-up\" class=\"inline-block\">\n";
				$OUTPUT .= "          <div id=\"talent-register\" class=\"register\">\n";
				if ( dynamic_sidebar('rb-agency-interact-login-sidebar') ) :endif; 
				$OUTPUT .= "          </div> <!-- talent-register -->\n";
				$OUTPUT .= "          <div class=\"clear line\"></div>\n";
				$OUTPUT .= "        </div> <!-- rbsign-up -->\n";

			//}

			$OUTPUT .= "      <div class=\"clear line\"></div>\n";
			$OUTPUT .= "      </div>\n";
					return $OUTPUT;
	}

	function rb_login_form_for_talent_wo_reg(){
			$LOGIN_URL = trim($_SERVER['REQUEST_URI'],'/');
			$OUTPUT = '';
			/* Check if users can register. */
			$registration = get_option( 'rb_agencyinteract_options' );
			$rb_agencyinteract_option_registerallow = isset($registration["rb_agencyinteract_option_registerallow"]) ?$registration["rb_agencyinteract_option_registerallow"]:"";
			$rb_agencyinteract_option_registerallowAgentProducer = isset($registration['rb_agencyinteract_option_registerallowAgentProducer'])?$registration['rb_agencyinteract_option_registerallowAgentProducer']:0;
			$rb_agencyinteract_option_switch_sidebar = isset($registration["rb_agencyinteract_option_switch_sidebar"])?(int)$registration["rb_agencyinteract_option_switch_sidebar"]:"";

			if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow )) {
				$widthClass = "half";
			} else {
				$widthClass = "full";
			}

		// File Path: interact/theme/include-login.php
		// Site Url : /profile-login/

			$OUTPUT .= "     <div id=\"rbsignin-register\" class=\"rbinteract\">\n";
				$ref = get_query_var("ref");

			if (isset($error)){
			$OUTPUT .= "<p class=\"error\">". $error ."</p>\n";
			}
			if (isset($ref) && $ref == "pending-approval") {
			$OUTPUT .= "<p id=\"message\" class=\"updated\">". __("Your account is pending for approval.", RBAGENCY_interact_TEXTDOMAIN). "</p>\n";
			}
			if (isset($ref) && $ref == "casting") {
			$OUTPUT .= "<p id=\"message\" class=\"updated\">". __("Your account is not registered as Talent/Model.", RBAGENCY_interact_TEXTDOMAIN).  __(" Click", RBAGENCY_interact_TEXTDOMAIN)." <a href=\"".get_bloginfo("url")."/casting-login/\">".__("here", RBAGENCY_interact_TEXTDOMAIN)."</a> ".__("to login as Casting.", RBAGENCY_interact_TEXTDOMAIN)."</p>\n";
			}


			if(isset($ref) && $ref == "reset_password"){
				$OUTPUT .= "<p  id=\"message\" class=\"updated\">".__("Check your e-mail for the reset link to create a new password.", RBAGENCY_interact_TEXTDOMAIN)."</p>\n";
			}

			if(isset($_GET['action']) && $_GET['action']== "resetpass"){
				$OUTPUT .= "<p  id=\"message\" class=\"updated\">".__("Your password has been reset.", RBAGENCY_interact_TEXTDOMAIN)."</p>\n";
			}

			$OUTPUT .= "        <div id=\"rbsign-in\" class=\"inline-block\">\n";
			$OUTPUT .= "          <h1>". __("Talents Sign In", RBAGENCY_interact_TEXTDOMAIN). "</h1>\n";
			if(isset($_GET["h"])){
				$OUTPUT .= "          <form name=\"loginform\" id=\"login\" action=\"". network_site_url("/"). "profile-login/?h=".$_GET["h"]."\" method=\"post\">\n";
			}else{
				$OUTPUT .= "          <form name=\"loginform\" id=\"login\" action=\"". network_site_url("/"). "profile-login/\" method=\"post\">\n";
			}

			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label for=\"user-name\">". __("Username", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"text\" name=\"user-name\" value=\"". (isset($_POST['user-name']) ? esc_html($_POST['user-name']):"") ."\" id=\"user-name\" />\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label for=\"password\">". __("Password", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"password\" name=\"password\" value=\"\" id=\"password\" /> <a href=\"". get_bloginfo('wpurl') ."/wp-login.php?action=lostpassword&redirect_to=".$_SERVER['REQUEST_URI']."?ref=reset_password\">". __("forgot password", RBAGENCY_interact_TEXTDOMAIN). "?</a>\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label><input type=\"checkbox\" name=\"remember-me\" value=\"forever\" /> ". __("Keep me signed in", RBAGENCY_interact_TEXTDOMAIN). "</label>\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row submit-row\">\n";
			$OUTPUT .= "              <input type=\"hidden\" name=\"action\" value=\"log-in\" />\n";
			$OUTPUT .= "              <input type=\"submit\" value=\"". __("Sign In", RBAGENCY_interact_TEXTDOMAIN). "\" /><br />\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "          </form>\n";
			$OUTPUT .= "        </div> <!-- rbsign-in -->\n";

			

			$OUTPUT .= "      <div class=\"clear line\"></div>\n";
			$OUTPUT .= "      </div>\n";
					return $OUTPUT;
	}

	/**
	*
	* FOR CASTING AGENT
	*
	*/

	function agent_login_form($atts){

		$a = shortcode_atts( array(
			        'registration_widget' => "false"
			    ), $atts );

		

		session_start();
		$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
		$rb_agencyinteract_option_redirect_first_time = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time']:1;
		$rb_agencyinteract_option_redirect_first_time_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url']:"/profile-member/account/";
		$rb_agencyinteract_option_redirect_custom_login = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login']:0;
		$rb_agencyinteract_option_redirect_afterlogin = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin']:1;
		$rb_agencyinteract_option_redirect_afterlogin_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url']:"/profile-member/account/";
		

		//if login submit
		if ( $_SERVER['REQUEST_METHOD'] == "POST" && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) {
			
			global $error;
		    $login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => isset($_POST['remember-me'])?$_POST['remember-me']:false ), false );

		    get_currentuserinfo();


			if(!is_wp_error($login)) {
				wp_set_current_user($login->ID);// populate

				rb_get_user_info_for_shortcode_agent_login();
			} else {
				$error .= __( $login->get_error_message(), RBAGENCY_interact_TEXTDOMAIN);
				$have_error = true;
			}
		}
		//:end login submit

		//this is for reset password
		if($_GET['action'] == 'rp'){
			$_SESSION['login_username'] = $_GET['login'];
			wp_redirect(site_url()."/wp-login.php?action=".$_GET['action']."&key=".$_GET['key']."&login=".$_GET['login']);
			exit();
		}
		if($_GET['action'] == 'resetpass'){
			global $wpdb;
			$uid = '';
			$user_table = $wpdb->prefix.'users';
			$users = $wpdb->get_results("SELECT ID FROM $user_table WHERE user_login =  '".$_SESSION['login_username']."'");
			foreach($users as $user){
				$uid = $user->ID;
			}
			//echo $uid;
			wp_set_password( $_POST['pass1-text'], $uid ) ;
		}
		//:end this is for reset password


		// Already logged in 
		if(is_user_logged_in()){
			global $user_ID;
			$user_ID = get_current_user_id();

			if(current_user_can("edit_posts")) {
				wp_redirect(admin_url("admin.php?page=rb_agency_menu"));

			}

			//redirect to job
			if(isset($_GET["h"])){
				wp_redirect(get_bloginfo("url").$_GET["h"]);
				exit();
			}

			$wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
			$is_casting  = $wpdb->num_rows;

			if( $is_casting > 0){
				wp_redirect(get_bloginfo("url"). "/casting-dashboard/");

			} else { // user is a model/talent but wp user_id is not linked to any rb profile.
				if($rb_agencyinteract_option_redirect_afterlogin == 1){
					wp_redirect(get_bloginfo("url"). "/profile-member/");

				} else {
					wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);

				}
			}

			$OUTPUT = '';

			// Call Header
			//$OUTPUT .= $rb_header = RBAgency_Common::rb_header();
			$OUTPUT .= "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";

			global $user_ID; 
			$login = get_userdata( $user_ID );
			
			get_user_login_info();


			$OUTPUT .= "    <p class=\"alert\">\n";
							printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', RBAGENCY_interact_TEXTDOMAIN), "/profile-member/", $login->display_name );
			$OUTPUT .= "		<a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __('Log out of this account', RBAGENCY_interact_TEXTDOMAIN) ."\">". __('Log out &raquo;', RBAGENCY_interact_TEXTDOMAIN) ."</a>\n";
			$OUTPUT .= "    </p><!-- .alert -->\n";
			$OUTPUT .= "</div><!-- #rbcontent -->\n";

			// Call Footer
			//$OUTPUT .= $rb_footer = RBAgency_Common::rb_footer();

		}// :end already logged in

		else{ //not logged in

			// Prepare Page

			// Call Header
			//$OUTPUT .= $rb_header = RBAgency_Common::rb_header();

				$OUTPUT .= "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";

					// Show Login Form
					$hideregister = true;
					//include("include-login.php");

					if($a['registration_widget'] == "true"){
						$OUTPUT .= rb_login_form_for_agent_wid_reg();
					}else{
						$OUTPUT .= rb_login_form_for_agent_wo_reg();
					}
				

			// Call Footer
			//$OUTPUT .= $rb_footer = RBAgency_Common::rb_footer();

		}

		return $OUTPUT;

	}	

	add_shortcode('agent_login_form','agent_login_form');


	function rb_get_user_info_for_shortcode_agent_login(){
		global $user_ID, $wpdb;
		$redirect = isset($_POST["lastviewed"])?$_POST["lastviewed"]:"";
		get_currentuserinfo();
		$user_info = get_userdata( $user_ID );

		// Check if user is registered as Model/Talent
    	$profile_is_active = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_profile." WHERE ProfileUserLinked = %d  ",$user_ID));
    	$is_model_or_talent  = $wpdb->num_rows;

    	// Check if user is agent
    	$casting_is_active = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
    	$is_casting_agent  = $wpdb->num_rows;

    	// login options
    	$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
		$rb_agencyinteract_option_redirect_first_time = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time']:1;
		$rb_agencyinteract_option_redirect_first_time_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url']:"/profile-member/account/";
		$rb_agencyinteract_option_redirect_custom_login = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login']:0;
		$rb_agencyinteract_option_redirect_afterlogin = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin']:1;
		$rb_agencyinteract_option_redirect_afterlogin_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url']:"/profile-member/account/";
		
		//start conditions	
		if( isset($user_ID) && ($is_model_or_talent > 0) || current_user_can("edit_posts")){

			if(!empty($redirect)){
				wp_redirect($redirect);
				exit();
			}

			if(current_user_can("edit_posts")) {
				wp_redirect(admin_url("admin.php?page=rb_agency_menu"));
				exit();
			}else{ //if none admin

				//if model or talent
				if($is_model_or_talent > 0){

					//should notify user that we is unable to login here

				} // :end if model or talent
				elseif($is_casting_agent > 0) {//if casting agent

					
					$casting_status = "";
					$q = "SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = ".$user_ID;
					$result = $wpdb->get_results($q);
					foreach($result as $r){
						$casting_status = $r->CastingIsActive;
					}

					$url = $rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_agent'];
					if( !empty($url)){

						$customUrl = '/casting-dashboard/';
					}else{

						$customUrl = $rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_agent_url'];
					}

					if($casting_status == 3){
						//header("Location:".get_bloginfo("wpurl").'/casting-pending?status=pending');
						wp_redirect(get_bloginfo("wpurl").'/casting-pending?status=pending');
						exit();
					}else{
						//header("Location: ". get_bloginfo("wpurl").  $customUrl);
						wp_redirect(get_bloginfo("wpurl").  $customUrl);
						exit();
					}

				} // :end if casting agent
				
			}//:end if not admin

		}//:end conditions
		elseif($profile_is_active->ProfileIsActive == 3){
			wp_redirect(get_bloginfo("url"). "/profile-member/pending/"); 
		} // :end if profile is pending
		else{

			$wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
			$is_casting  = $wpdb->num_rows;

			if( $is_casting > 0){
				wp_logout();
				wp_redirect(get_bloginfo("url"). "/profile-login/?ref=casting");

			} else { // user is a model/talent but wp user_id is not linked to any rb profile.

				if($rb_agencyinteract_option_redirect_afterlogin == 1){

					if(isset($_GET["h"])){
						wp_redirect(get_bloginfo("url").$_GET["h"]);
						exit();
					}else{
						wp_redirect(get_bloginfo("url"). "/profile-member/");
					}
										

				} else {

					if(isset($_GET["h"])){
						wp_redirect(get_bloginfo("url").$_GET["h"]);
						exit();
					}else{
						wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);
					}
							
				}
			}
		} // : end last else
	}


		function rb_login_form_for_agent_wid_reg(){
			
			$OUTPUT = "";

			/* Check if users can register. */
			$registration = get_option( 'rb_agencyinteract_options' );
			$rb_agencyinteract_option_registerallow = isset($registration["rb_agencyinteract_option_registerallow"]) ? $registration["rb_agencyinteract_option_registerallow"]:0;
			$rb_agencyinteract_option_registerallowAgentProducer = isset($registration['rb_agencyinteract_option_registerallowAgentProducer'])?$registration['rb_agencyinteract_option_registerallowAgentProducer']:0;

			$rb_agencyinteract_option_switch_sidebar_agent = isset($registration["rb_agencyinteract_option_switch_sidebar_agent"])?(int)$registration["rb_agencyinteract_option_switch_sidebar_agent"]:"";

			if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow )) {
				$widthClass = "half";
			} else {
				$widthClass = "full";
			}

			$OUTPUT .= "     <div id=\"rbsignin-register\" class=\"rbinteract\">\n";

			if ( $error ) {
			$OUTPUT .= "<p class=\"error\">". $error ."</p>\n";
			}

			$OUTPUT .= "        <div id=\"rbsign-in\" class=\"inline-block\">\n";
			$OUTPUT .= "          <h1>". __("Clients Sign In", RBAGENCY_interact_TEXTDOMAIN). "</h1>\n";
			$OUTPUT .= "          <form name=\"loginform\" id=\"login\" action=\"". network_site_url("/"). "casting-login/\" method=\"post\">\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label for=\"user-name\">". __("Username", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"text\" name=\"user-name\" value=\"". esc_attr( isset($_POST['user-name'])?$_POST['user-name']:"", 1 ) ."\" id=\"user-name\" />\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label for=\"password\">". __("Password", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"password\" name=\"password\" value=\"\" id=\"password\" /> <a href=\"". get_bloginfo('wpurl') ."/wp-login.php?action=lostpassword\">". __("forgot password", RBAGENCY_interact_TEXTDOMAIN). "?</a>\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label><input type=\"checkbox\" name=\"remember-me\" value=\"forever\" /> ". __("Keep me signed in", RBAGENCY_interact_TEXTDOMAIN). "</label>\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row submit-row\">\n";
			$OUTPUT .= "              <input type=\"hidden\" name=\"action\" value=\"log-in\" />\n";
			$OUTPUT .= "              <input type=\"submit\" value=\"". __("Sign In", RBAGENCY_interact_TEXTDOMAIN). "\" /><br />\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "          </form>\n";
			$OUTPUT .= "        </div> <!-- rbsign-in -->\n";




			//if($registration == "true"){

				//if ( $registration == "true") {

						/*	echo "        <div id=\"rbsign-up\" class=\"inline-block\">\n";
							echo "          <div id=\"talent-register\" class=\"register\">\n";
							echo "            <h1>". __("Not a member", RBAGENCY_interact_TEXTDOMAIN). "?</h1>\n";
							echo "            <h3>". __("Client", RBAGENCY_interact_TEXTDOMAIN). " - ". __("Register here", RBAGENCY_interact_TEXTDOMAIN). "</h3>\n";
							echo "            <ul>\n";
							echo "              <li>". __("Create your free profile page", RBAGENCY_interact_TEXTDOMAIN). "</li>\n";
							echo "              <li><a href=\"". get_bloginfo("wpurl") ."/casting-register\" class=\"rb_button\">". __("Register as Casting Agent", RBAGENCY_interact_TEXTDOMAIN). "</a></li>\n";
							echo "            </ul>\n";
							echo "          </div> <!-- talent-register -->\n";
							echo "          <div class=\"clear line\"></div>\n";*/
							$OUTPUT .= "        <div id=\"rbsign-up\" class=\"inline-block\">\n";
							$OUTPUT .= "          <div id=\"talent-register\" class=\"register\">\n";
							$OUTPUT .= "            <h1>". __("Not a member", RBAGENCY_interact_TEXTDOMAIN). "?</h1>\n";

							$OUTPUT .= "<h3>". __("Client - Register here", RBAGENCY_interact_TEXTDOMAIN). "</h3>";
							$OUTPUT .= "<ul>";
							$OUTPUT .= "	<li>". __("Create your free profile page", RBAGENCY_casting_TEXTDOMAIN). "</li>";
							$OUTPUT .= "	<li>". __("List Auditions & Jobs Free", RBAGENCY_casting_TEXTDOMAIN). "</li>";
							$OUTPUT .= "	<li>". __("Contact People in the talent Directory", RBAGENCY_casting_TEXTDOMAIN). "</li>";
							$OUTPUT .= "</ul>";
							$OUTPUT .= "<input type=\"button\" onclick=\"location.href='".site_url()."/casting-register'\" value=\"". __("Register Now", RBAGENCY_casting_TEXTDOMAIN). "\">";
							$OUTPUT .= "          </div> <!-- talent-register -->\n";
							$OUTPUT .= "          <div class=\"clear line\"></div>\n";
							$OUTPUT .= "        </div> <!-- rbsign-up -->\n";

							$OUTPUT .= "        </div> <!-- rbsign-up -->\n";
				//}
			//}
			//else {
				$OUTPUT .= "        <div id=\"rbsign-up\" class=\"inline-block\">\n";
				$OUTPUT .= "          <div id=\"talent-register\" class=\"register\">\n";
				if ( dynamic_sidebar('rb-agency-casting-login-sidebar') ) :endif;
				$OUTPUT .= "          </div> <!-- talent-register -->\n";
				$OUTPUT .= "          <div class=\"clear line\"></div>\n";
				$OUTPUT .= "        </div> <!-- rbsign-up -->\n";

			//}


			$OUTPUT .= "      <div class=\"clear line\"></div>\n";
			$OUTPUT .= "      </div>\n";
			return $OUTPUT;
		}

		function rb_login_form_for_agent_wo_reg(){
			
			$OUTPUT = "";

			/* Check if users can register. */
			$registration = get_option( 'rb_agencyinteract_options' );
			$rb_agencyinteract_option_registerallow = isset($registration["rb_agencyinteract_option_registerallow"]) ? $registration["rb_agencyinteract_option_registerallow"]:0;
			$rb_agencyinteract_option_registerallowAgentProducer = isset($registration['rb_agencyinteract_option_registerallowAgentProducer'])?$registration['rb_agencyinteract_option_registerallowAgentProducer']:0;

			$rb_agencyinteract_option_switch_sidebar_agent = isset($registration["rb_agencyinteract_option_switch_sidebar_agent"])?(int)$registration["rb_agencyinteract_option_switch_sidebar_agent"]:"";

			if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow )) {
				$widthClass = "half";
			} else {
				$widthClass = "full";
			}

			$OUTPUT .= "     <div id=\"rbsignin-register\" class=\"rbinteract\">\n";

			if ( $error ) {
			$OUTPUT .= "<p class=\"error\">". $error ."</p>\n";
			}

			$OUTPUT .= "        <div id=\"rbsign-in\" class=\"inline-block\">\n";
			$OUTPUT .= "          <h1>". __("Clients Sign In", RBAGENCY_interact_TEXTDOMAIN). "</h1>\n";
			$OUTPUT .= "          <form name=\"loginform\" id=\"login\" action=\"". network_site_url("/"). "casting-login/\" method=\"post\">\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label for=\"user-name\">". __("Username", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"text\" name=\"user-name\" value=\"". esc_attr( isset($_POST['user-name'])?$_POST['user-name']:"", 1 ) ."\" id=\"user-name\" />\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label for=\"password\">". __("Password", RBAGENCY_interact_TEXTDOMAIN). "</label><input type=\"password\" name=\"password\" value=\"\" id=\"password\" /> <a href=\"". get_bloginfo('wpurl') ."/wp-login.php?action=lostpassword\">". __("forgot password", RBAGENCY_interact_TEXTDOMAIN). "?</a>\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row\">\n";
			$OUTPUT .= "              <label><input type=\"checkbox\" name=\"remember-me\" value=\"forever\" /> ". __("Keep me signed in", RBAGENCY_interact_TEXTDOMAIN). "</label>\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "            <div class=\"field-row submit-row\">\n";
			$OUTPUT .= "              <input type=\"hidden\" name=\"action\" value=\"log-in\" />\n";
			$OUTPUT .= "              <input type=\"submit\" value=\"". __("Sign In", RBAGENCY_interact_TEXTDOMAIN). "\" /><br />\n";
			$OUTPUT .= "            </div>\n";
			$OUTPUT .= "          </form>\n";
			$OUTPUT .= "        </div> <!-- rbsign-in -->\n";



			$OUTPUT .= "      <div class=\"clear line\"></div>\n";
			$OUTPUT .= "      </div>\n";
			return $OUTPUT;
		}

function rb_get_customfields_registration(){
	global $wpdb;
	require_once("theme/view-custom-fields-registration.php");
	$implodedProfileType = implode(",",$_REQUEST['profile_types']);
	$arrChecker = [];
	$find_in_set_arr = [];
	if(is_array($_REQUEST['profile_types'])){
		foreach($_REQUEST['profile_types'] as $k=>$v){
			$find_in_set_arr[] = " FIND_IN_SET('".$v."',b.ProfileCustomTypes)>0 ";
		}
		$find_in_set = implode("OR",$find_in_set_arr);
	}else{
		$find_in_set = " FIND_IN_SET('".$_REQUEST['profile_types']."',b.ProfileCustomTypes)>0 ";
	}
	
	

	$sql = "SELECT a.*,b.ProfileCustomTypes FROM ".table_agency_customfields." a INNER JOIN ".table_agency_customfields_types." b ON a.ProfileCustomID = b.ProfileCustomID AND a.ProfileCustomShowInitialRegistration = 1 WHERE ".$find_in_set." ORDER BY a.ProfileCustomOrder ASC";

		$results = $wpdb->get_results($sql,ARRAY_A);

		$q =  "SELECT a.*,b.ProfileCustomTypes FROM ".table_agency_customfields." a INNER JOIN ".table_agency_customfields_types." b ON a.ProfileCustomID = b.ProfileCustomID AND a.ProfileCustomShowInitialRegistration = 1";
		$res = $wpdb->get_results($q,ARRAY_A);
		//echo "<pre>";
		//print_r($res);
		//echo "</pre>";
		foreach($results as $data){
			if(!in_array($data["ProfileCustomID"],$arrChecker)){
				if($data["ProfileCustomShowGender"] == 0 || $data["ProfileCustomShowGender"] == $_REQUEST['gender']){
					rb_model_registration_form(1,$data);
				}
			}
			array_push($arrChecker,$data["ProfileCustomID"]);			
		}
	

	die();
}
add_action( 'wp_ajax_rb_get_customfields_registration', 'rb_get_customfields_registration' );
add_action( 'wp_ajax_nopriv_rb_get_customfields_registration', 'rb_get_customfields_registration' );


function registration_form($atts){
	$a = shortcode_atts( array(
        'type' => 'common'
    ), $atts );

    $registrationType = $a['type'];
    if( !empty($registrationType) && $registrationType != 'common'){
    	$type = str_replace(" ","_",getProfileTypeTitleByTag($registrationType));
    	return rbcustom_registration_form_param($_POST,$type);
    	
    }else{
    	return rbcustom_registration_form($_POST);    	    	
    }
}
add_shortcode( 'registration_form', 'registration_form' );

function getProfileTypeTitleByTag($tag){
	global $wpdb;
	$sql = "SELECT DataTypeTitle FROM ".table_agency_data_type." WHERE DataTypeTag = '".$tag."' ";
	$results = $wpdb->get_results($sql,ARRAY_A);
	foreach($results as $result)
	return $result["DataTypeTitle"];
}

function rbcustom_registration_form($request){

	global $wpdb;

	$q = "SELECT * FROM ".table_agency_customfields_types;
	$res = $wpdb->get_results($q,ARRAY_A);
	//echo "<pre>";
	//print_r($res);
	//echo "</pre>";

	echo "<style>div label{text-align:left!important;}</style>";
	$getErrorMessage = "";
	$error_msg = array();
	# Get options
	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
	$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_model_toc = isset($rb_agency_options_arr['rb_agency_option_agency_model_toc'])?$rb_agency_options_arr['rb_agency_option_agency_model_toc']: "/models-terms-of-conditions";
	$rb_agencyinteract_option_registerapproval = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval']:"";

	$rb_agencyinteract_option_default_registered_users = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_default_registered_users'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_default_registered_users']:"";

	//+Registration
	// - show/hide registration for Agent/Producers
	$rb_agencyinteract_option_registerallowAgentProducer = isset($registration['rb_agencyinteract_option_registerallowAgentProducer'])?$registration['rb_agencyinteract_option_registerallowAgentProducer']:0;

	// - show/hide  self-generate password
	$rb_agencyinteract_option_registerconfirm = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerconfirm'])?(int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerconfirm']:0;
	// show/hide username and password
	$rb_agencyinteract_option_useraccountcreation = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_useraccountcreation'])?(int)$rb_agency_interact_options_arr['rb_agencyinteract_option_useraccountcreation']:0;
	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );

	$pageName = get_query_var('pagename');
	if(empty($pageName)){
		$customRegistrationPageURL = site_url()."/profile-register/";
	}else{
		$customRegistrationPageURL = site_url()."/".$pageName;
	}
	
	
	# Process registration
	if(isset($_POST['agree_btn'])){
		$first_name = !empty($_POST['ProfileContactNameFirst']) ? $_POST['ProfileContactNameFirst'] : "";
		$last_name  = !empty($_POST['ProfileContactNameLast']) ? $_POST['ProfileContactNameLast'] : "";
		$user_display = !empty($_POST['ProfileDisplay']) ? $_POST['ProfileDisplay'] : "";
		$user_email = !empty($_POST['ProfileEmail']) ? $_POST['ProfileEmail'] : "";
		$user_login = !empty($_POST['ProfileUserName']) ? $_POST['ProfileUserName'] : "";
		$user_pass  = NULL;

		if ($rb_agencyinteract_option_registerconfirm == 0) {
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

		$ProfileDateBirth = $_POST['ProfileDateBirth'];
		$ProfileAge = $_POST['ProfileAge'];
		$ProfileGender = $_POST['ProfileGender'];
		$ProfileResidence = $_POST['ProfileResidence'];
		$ProfileType =implode(",", $_POST['ProfileType']);
		$ProfileLocationStreet = isset($_POST['ProfileLocationStreet'])?$_POST['ProfileLocationStreet']:"";
		$ProfileLocationCity = RBAgency_Common::format_propercase(isset($_POST['ProfileLocationCity'])?$_POST['ProfileLocationCity']:"");
		$ProfileLocationState = strtoupper(isset($_POST['ProfileLocationState'])?$_POST['ProfileLocationState']:"");
		$ProfileLocationZip = isset($_POST['ProfileLocationZip'])?$_POST['ProfileLocationZip']:"";
		$ProfileLocationCountry = isset($_POST['ProfileLocationCountry'])?$_POST['ProfileLocationCountry']:"";

		$yearDob = !empty($_POST["ProfileDateBirth_Year"]) ? $_POST["ProfileDateBirth_Year"] : "0000";
		$monthDob = !empty($_POST["ProfileDateBirth_Month"]) ? $_POST["ProfileDateBirth_Month"] : "00";
		$dayDob = !empty($_POST["ProfileDateBirth_Day"]) ? $_POST["ProfileDateBirth_Day"] : "00";
		$ProfileDateBirth = $yearDob."-".$monthDob."-".$dayDob;

		if(empty($userdata['user_login'])) {
			$error_msg[] = __("A username is required for registration.<br />", RBAGENCY_interact_TEXTDOMAIN);
		}
		if(username_exists($userdata['user_login'])) {
			$error_msg[] = __("Sorry, that username already exists!<br />", RBAGENCY_interact_TEXTDOMAIN);
		}
		if(!is_email($userdata['user_email'])) {
			$error_msg[] = __("You must enter a valid email address.<br />", RBAGENCY_interact_TEXTDOMAIN);
		}
		if(email_exists($userdata['user_email'])) {
			$error_msg[] = __("Sorry, that email address is already used!<br />", RBAGENCY_interact_TEXTDOMAIN);
		}

		if(!empty($error_msg)){
			
			foreach($error_msg as $err){
				$getErrorMessage .= "<p style=\"color:red;\">".$err."</p>";
			}
		}else{
			# create user			
			$new_user = wp_insert_user( $userdata );

			# Set Gallery
			if ($rb_agency_option_profilenaming == 0) {
				$profile_contact_display = $first_name . " ". $last_name;
			} elseif ($rb_agency_option_profilenaming == 1) {
				$profile_contact_display = $first_name . " ". substr($last_name, 0, 1);
			} elseif ($rb_agency_option_profilenaming == 2) {
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
			# Profile Gallery
			$profile_gallery = rb_agency_createdir($profile_gallery);

			update_user_meta($new_user, 'rb_agency_interact_pgender', $ProfileGender);
			$profileactive = null;

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
				$profileactive = $_default_registered;
			}

			# Insert to table_agency_profile
			$sql = "INSERT INTO ".table_agency_profile."
				(
				ProfileContactDisplay,
				ProfileContactNameFirst,
				ProfileContactNameLast,
				ProfileGender,
				ProfileContactEmail,
				ProfileIsActive,
				ProfileUserLinked,
				ProfileType,
				ProfileGallery,
				ProfileDateBirth,
				ProfileLocationStreet,
				ProfileLocationCity,
				ProfileLocationState,
				ProfileLocationZip,
				ProfileLocationCountry
				)
				VALUES(
				'".$profile_contact_display."',
				'".$first_name."',
				'".$last_name."',
				'".$ProfileGender."',
				'".$user_email."',
				".$profileactive.",
				".$new_user.",
				'".$ProfileType."',
				'".$profile_gallery."',
				'".$ProfileDateBirth."',
				'".$ProfileLocationStreet."',
				'".$ProfileLocationCity."',
				'".$ProfileLocationState."',
				'".$ProfileLocationZip."',
				'".$ProfileLocationCountry."')";

			$wpdb->query($sql);
			$id = $wpdb->insert_id;
			$NewProfileID = $id;
			# Update and set ProfileUserLinked,ProfileGallery and ProfileContactDisplay with the ProfileID
			if($rb_agency_option_profilenaming == 3){
				$update = $wpdb->query("UPDATE " . table_agency_profile . " SET ProfileGallery='ID-" . $id . "', ProfileContactDisplay='ID-" . $id . "' WHERE ProfileID='" . $id . "'");
				$profile_gallery = "ID-".$id;
				rb_agency_createdir($profile_gallery);
			}
			add_user_meta( $new_user, 'user_profile_id', $new_user);

			# Add Custom Field Values stored in Mux
			# For testing , add this array
			foreach($_POST as $key => $value) {
				if ((substr($key, 0, 15) == "ProfileCustomID") && (isset($value) && !empty($value))) {
						$profilecustomfield_date = explode("_",$key);
						if(count($profilecustomfield_date) == 2){ // customfield date
							$ProfileCustomID = substr($profilecustomfield_date[0], 15);
						} else {
							$ProfileCustomID = substr($key, 15);
						}
					 
					if(is_array($value)){
						$value =  implode(",",$value);
					}
					if(count($profilecustomfield_date) == 2){ // customfield date
						$value = date("y-m-d h:i:s",strtotime($value));
						$insert1 = $wpdb->prepare("INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomDateValue)" . "VALUES (%d,%d,%s)",$NewProfileID,$ProfileCustomID,$value);
					} else {
						$insert1 = $wpdb->prepare("INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomValue)" . "VALUES (%d,%d,%s)",$NewProfileID,$ProfileCustomID,$value);
					}
					$results1 = $wpdb->query($insert1);

				}
			} # End : Add Custom Field Values stored in Mux

			
			$arr["new"] = true;
			$arr["step1"] = true;
			$arr["step2"] = true;
			$arr["step3"] = true;
			add_user_meta($new_user, 'rb_agency_new_registeredUser',$arr);
			if ($rb_agencyinteract_option_registerapproval == 1) { // automatically approval
				// Notify admin and user
				rb_new_user_notification($new_user,$user_pass);
			} else { // manually approval
				// Notify admin and user
				rb_new_user_notification($new_user,$user_pass);
			}
			$_SESSION["ProfileUserName"] = $_POST["ProfileUserName"];
			$_SESSION["PW"] = $user_pass;

			$uri = substr($_SERVER['REQUEST_URI'],1);
			$uriArr = explode("/",$uri);
			if(!empty($uriArr[1])){
				wp_redirect($customRegistrationPageURL."/".$uriArr[1]."/?st=success");
				die;
			}else{
				wp_redirect($customRegistrationPageURL."/?st=success");
			}
			
		} # End : create user
		
	}
	$uri = substr($_SERVER['REQUEST_URI'],1);
	$requestURI =explode('/', $uri);
	

	echo '
			<script type="text/javascript">
			 jQuery(document).ready(function() {

				jQuery( "#ProfileGender" ).on("change",function() {
				jQuery(".custom-fields-registration").empty();
				var userGenderID = jQuery(this).val();
				console.log(userGenderID);
				//var userGenderText = jQuery( "#ProfileGender option:selected").text();
						
					jQuery.post("'.admin_url("admin-ajax.php").'", 
						{
						GenderID: userGenderID,
						action:"request_datatype_bygender_memberregister"
						})
					.done(function(data) {
						console.log(data);
						jQuery("#ProfileType-div").html(data);
						jQuery("#consent-processing").attr("style","display:none!important");
						jQuery("#registration-submit-btn").removeAttr("style");

						


					});
					return false;
				});
			});
			</script>
		';

	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			var profileTypeIDs = [];
			jQuery(".DataTypeIDClassCheckbox").live('click',function(){
				var ajaxLoader = "<?php echo site_url().'/wp-content/plugins/rb-agency-interact/theme/ajax-loader-white.gif';  ?>";

				jQuery(".custom-fields-registration").html('<img src="'+ajaxLoader+'" />');

				var checkedProfileType = jQuery(this).attr('profile-type-title');
				var checked = jQuery(this).is(':checked');
				profileTypeIDs.push(checkedProfileType);
				//remove if unchecked
				if(checked!==true){
					profileTypeIDs = jQuery.grep(profileTypeIDs, function(value) {
						return value != checkedProfileType;
					});
				}else{
					
					jQuery(this).attr('checked','checked');
				}												
				
				
				console.log(jQuery.inArray('Models',profileTypeIDs));
				if(jQuery.inArray('Models',profileTypeIDs) > -1){
					//display consent and stage name
					jQuery("#profile-stagename").removeAttr('style');
					//display consent
					jQuery("#consent-processing").removeAttr('style');
					//hide default submit button
					jQuery("#registration-submit-btn").attr('style','display:none!important');
				}else{
					jQuery("#profile-stagename").attr('style','display:none!important');
					jQuery("#consent-processing").attr('style','display:none!important');
					jQuery("#registration-submit-btn").removeAttr('style');
				}
										
				//here , make ajax call to get custom fields
				jQuery.ajax({
					type: "POST",
					url: "<?php echo admin_url('admin-ajax.php') ?>",
					data: {
						action: "rb_get_customfields_registration",
							'profile_types': profileTypeIDs,
							'gender': jQuery("#ProfileGender").val()
						},
						success: function (results) {
							//console.log(results);
							jQuery(".custom-fields-registration").html();
							jQuery(".custom-fields-registration").html(results);
						}
				});	
																			
			});


			jQuery("#ProfileLocationCountry").change(function(){
				jQuery("#profile-state").removeAttr('style');
			});


								
		});
	</script>
	<?php

	if(!isset($_GET['st'])){
		//print (new ReflectionFunction("request_datatype_bygender_memberregister"))->getFileName();
		# Default fields
		echo "<form method=\"POST\" action=\"".$customRegistrationPageURL."\" >";
		echo "<div id='member-register' class='rbform'>";
		echo "	<div id=\"profile-registration-error\" class=\"rbfield\">\n";
		echo !empty($getErrorMessage) ? $getErrorMessage : "";
		echo "</div>";
		echo "	<div id=\"profile-firstname\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("First Name", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactNameFirst\" name=\"ProfileContactNameFirst\" value=\"".$first_name."\"/></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-surname\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Surname", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactNameLast\" name=\"ProfileContactNameLast\" value=\"".$last_name."\"/></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-username\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Username", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileUserName\" name=\"ProfileUserName\" value=\"".$user_login."\"/></div>\n";
		echo "	</div>\n";
		if ($rb_agencyinteract_option_registerconfirm == 1 && $rb_agencyinteract_option_useraccountcreation == 1) {

		echo "       <div id=\"profile-password\" class=\"rbfield rbpassword rbsingle\">\n";
		echo "   		<label for=\"profile_password\">". __("Password (required)", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
		echo "   		<div><input class=\"text-input\" name=\"profile_password\" type=\"password\" id=\"profile_password\" value=\""; if ( $error ) echo esc_html( $_POST['profile_password'], 1 ); echo "\" /></div>\n";
		echo "       </div><!-- #profile-password -->\n";
		}
		echo "	<div id=\"profile-email\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("E-mail", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileEmail\" name=\"ProfileEmail\" value=\"".$user_email."\"/></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-birthdate\" class=\"rbfield rbselect rbmulti rbblock\">\n";
			echo "		<label>". __("Birthdate", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
			echo "		<div>\n";
								/* Month */ 
				echo "		<div style=\"display:inline-block;width:33%;margin-right:1px;\">\n";
								$monthName = array(1=> "January", "February", "March","April", "May", "June", "July", "August","September", "October", "November", "December"); 
				echo "			<select name=\"ProfileDateBirth_Month\" id=\"ProfileDateBirth_Month\">\n";
				echo "			<option value=\"\"><span style=\"font-size:9px;\" >".__("Month",RBAGENCY_interact_TEXTDOMAIN)."</span> </option>\n";
					for ($currentMonth = 1; $currentMonth <= 12; $currentMonth++ ) {
						echo "			<option value=\"". $currentMonth ."\">". $monthName[$currentMonth] ."</option>\n";
				}
				echo "			</select>\n";
				echo "		</div>\n";
								/* Day */ 
				echo "		<div style=\"display:inline-block;width:33%;\">\n";
				echo "			<select name=\"ProfileDateBirth_Day\" id=\"ProfileDateBirth_Day\">\n";
				echo "			<option value=\"\"><span style=\"font-size:9px;\" >".__("Day",RBAGENCY_interact_TEXTDOMAIN)." </span> </option>\n";
					for ($currentDay = 1; $currentDay <= 31; $currentDay++ ) {
						echo "			<option value=\"". $currentDay ."\">". $currentDay ."</option>\n";
					}
				echo "			</select>\n";
				echo "		</div>\n";

								/* Year */ 
				echo "		<div style=\"display:inline-block;width:33%;\">\n";
				echo "			<select name=\"ProfileDateBirth_Year\" id=\"ProfileDateBirth_Year\">\n";
				echo "			<option value=\"\"><span style=\"font-size:9px;\" >".__("Year",RBAGENCY_interact_TEXTDOMAIN)." </span> </option>\n";
					for ($currentYear = 1940; $currentYear <= date("Y")+6; $currentYear++ ) {
						echo "			<option value=\"". $currentYear ."\">". $currentYear ."</option>\n";
				}
				echo "			</select>\n";
				echo "		</div>\n";
			echo "		</div>\n";
		echo "		</div>\n";
		# End : Default fields

		# Location address
		echo "	<div id=\"profile-country\" class=\"rbfield rbselect rbsingle\">\n";
		echo "      <label>" . __("Country", RBAGENCY_interact_TEXTDOMAIN) . ":</label>\n";
		echo "      <div>\n";

		$query_get ="SELECT * FROM `". table_agency_data_country ."` ORDER BY CountryTitle ASC" ;
		$result_query_get = $wpdb->get_results($query_get);
		$location= site_url();
		echo '<input type="hidden" id="url" value="'.$location.'">';
		echo "<select name=\"ProfileLocationCountry\" id=\"ProfileLocationCountry\"  onchange='javascript:populateStates(\"ProfileLocationCountry\",\"ProfileLocationState\");'>";
		echo '<option value="">'. __("Select country", RBAGENCY_interact_TEXTDOMAIN) .'</option>';
		foreach($result_query_get as $r){
				$selected =$ProfileLocationCountry==$r->CountryID?"selected=selected":"";
			echo '<option '.$selected.' value='.$r->CountryID.' >'.$r->CountryTitle.'</option>';
		}
		echo '</select>';
		echo "      </div>\n";
		echo "    </div>\n";


		echo "	<div id=\"profile-state\" class=\"rbfield rbtext rbsingle\" style=\"display:none;\">\n";
		echo "      <label>" . __("State", RBAGENCY_interact_TEXTDOMAIN) . ":</label>\n";
		echo "      <div>\n";
		$query_get ="SELECT * FROM `".table_agency_data_state."` ORDER BY StateTitle ASC" ;
		$result_query_get = $wpdb->get_results($query_get);
		echo '<select name="ProfileLocationState" id="ProfileLocationState">';
		echo '<option value="">'. __("Select state", RBAGENCY_interact_TEXTDOMAIN) .'</option>';
		foreach($result_query_get as $r){
			$selected =$ProfileLocationState==$r->StateID?"selected=selected":"";
			echo '<option '.$selected.' value='.$r->StateID.' >'.$r->StateTitle.'</option>';
		}
		echo '</select>';

		echo "      </div>\n";
		echo "    </div>\n";

		echo "	<div id=\"profile-street\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Street", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileLocationStreet\" name=\"ProfileLocationStreet\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-city\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("City", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileLocationCity\" name=\"ProfileLocationCity\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-zip\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Zip", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileLocationZip\" name=\"ProfileLocationZip\" /></div>\n";
		echo "	</div>\n";


		# Gender
		echo "	<div id=\"profile-gender\" class=\"rbfield rbselect rbsingle\">\n";
		echo "		<label>". __("Gender", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "			<div><select id='ProfileGender' name=\"ProfileGender\">";
		$query= "SELECT GenderID, GenderTitle FROM " .  table_agency_data_gender . " GROUP BY GenderTitle ";
							$queryShowGender = $wpdb->get_results($query,ARRAY_A);
							echo "<option value=''>".__("--Please Select--",RBAGENCY_interact_TEXTDOMAIN)."</option>";
							foreach($queryShowGender as $dataShowGender){
								echo "<option value=\"".$dataShowGender["GenderID"]."\" ". selected($ProfileGender ,$dataShowGender["GenderID"],false).">".$dataShowGender["GenderTitle"]."</option>";
							}
		echo "			</select></div>";
		echo "	</div>\n";

		# Display Profile Type

		echo "	<div id=\"profile-type\" class=\"rbfield rbcheckbox rbmulti\">\n";
		echo "		<label for=\"profile_type\">". __("Type of Profile", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";

					//check for parentid column
					$sql = "SELECT DataTypeParentID FROM ".$wpdb->prefix."agency_data_type LIMIT 1";
					$r = $wpdb->get_results($sql);
					if(count($r) == 0){
						//create column
						$queryAlter = "ALTER TABLE " . $wpdb->prefix ."agency_data_type ADD DataTypeParentID varchar(20) default 0";
						$resultsDataAlter = $wpdb->query($queryAlter,ARRAY_A);
					}

					
		echo "		<div id='ProfileType-div'>";
		
		echo "			Please select your gender first";
			
		echo "		</div>";
		echo "</div><!-- #profile-type -->\n";
		# End : Display Profile Type

		# will show only if model
		echo "	<div id=\"profile-stagename\" class=\"rbfield rbtext rbsingle\" style=\"display:none;\">\n";
		echo "		<label>". __("Stage name", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileDisplay\" name=\"ProfileDisplay\"/></div>\n";
		echo "	</div>\n";

		

		
		# Display custom fields
		echo "<div class=\"custom-fields-registration\">";
		echo "</div>";
		# End : Display custom fields	

		# Consent processing
		echo "	<div id=\"consent-processing\" class=\"rbfield\" style=\"display:none;\">\n";
		echo "		<h3 style=\"font-size:22px;text-align:center;\">". __("Consent to the processing of personal data", RBAGENCY_interact_TEXTDOMAIN) ."</h3><br/>\n";
		echo "<p style=\"text-align:justify;font-size:12px;\">".__("Please note that Nikkis Models is an agency recruiting models for the production of erotic and sexual themes. The cooperation between the model and the agency take place only after the model was properly instructed about the content of such cooperation and only after granting unconditional consent to such type of cooperation.",RBAGENCY_interact_TEXTDOMAIN)."</p><br />";

		# Yes, I Agree
		echo "<input type=\"submit\" name=\"agree_btn\" class=\"agree-btn\" value=\"".__('Yes, I agree', RBAGENCY_interact_TEXTDOMAIN)."\" style=\"padding: 15px 30px;margin-left:165px;\"/>";
		echo "	</div>\n";
		# End : Consent processing

		# Default submit button
		echo "	<div id=\"registration-submit-btn\" class=\"rbfield\">\n";	
		echo "<input type=\"submit\" name=\"agree_btn\" class=\"agree-btn\" value=\"".__('Submit', RBAGENCY_interact_TEXTDOMAIN)."\" style=\"padding: 15px 30px;margin-left:165px;\"/>";
		echo "	</div>\n";

		echo "</form>";
		echo "</div>";


	}else{ # End : st is not set

		# Put in here what will happen after registration

		printf( "<p style=\"text-align:center;\">".__("Thank you for registering, %s!", RBAGENCY_interact_TEXTDOMAIN)."<p>", $_SESSION['ProfileUserName'] );		
		echo "<p style=\"text-align:center;\">".__("Please check your email for the next step to complete registration.", RBAGENCY_interact_TEXTDOMAIN)."</p>";		
		echo "<p style=\"text-align:center;\">".__("<i>(It might go to your spam folder )</i>", RBAGENCY_interact_TEXTDOMAIN)."</p>";
		//echo $_SESSION["PW"];
		//echo "<input type=\"hidden\" value=\"".$_SESSION["PW"]."\" />";

	} # End
	
}


//custom registration form with parameters
function rbcustom_registration_form_param($request,$ptype){

	global $wpdb;

	$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_agencyimagemaxheight 	= $rb_agency_options_arr['rb_agency_option_agencyimagemaxheight'];
	if (empty($rb_agency_option_agencyimagemaxheight) || $rb_agency_option_agencyimagemaxheight < 500) {
		$rb_agency_option_agencyimagemaxheight = 800; 
	}

	$uri = substr($_SERVER['REQUEST_URI'],1);
	$uriArr = explode("/",$uri);
	//echo $uriArr[1];
	echo "<style>div label{text-align:left!important;}</style>";
	$getErrorMessage = "";
	$error_msg = array();
	# Get options
	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
	$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_model_toc = isset($rb_agency_options_arr['rb_agency_option_agency_model_toc'])?$rb_agency_options_arr['rb_agency_option_agency_model_toc']: "/models-terms-of-conditions";
	$rb_agencyinteract_option_registerapproval = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_registerapproval']:"";

	$rb_agencyinteract_option_default_registered_users = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_default_registered_users'])?$rb_agency_interact_options_arr['rb_agencyinteract_option_default_registered_users']:"";
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

	$pageName = get_query_var('pagename');
	if(empty($pageName)){
		$customRegistrationPageURL = site_url()."/profile-register/";
	}else{
		$customRegistrationPageURL = site_url()."/".$pageName;
	}
	
	
	# Process registration
	if(isset($_POST['agree_btn'])){
		$first_name = !empty($_POST['ProfileContactNameFirst']) ? $_POST['ProfileContactNameFirst'] : "";
		$last_name  = !empty($_POST['ProfileContactNameLast']) ? $_POST['ProfileContactNameLast'] : "";
		$user_display = !empty($_POST['ProfileDisplay']) ? $_POST['ProfileDisplay'] : "";
		$user_email = !empty($_POST['ProfileEmail']) ? $_POST['ProfileEmail'] : "";
		$user_login = !empty($_POST['ProfileUserName']) ? $_POST['ProfileUserName'] : "";
		$user_pass  = NULL;

		if ($rb_agencyinteract_option_registerconfirm == 0) {
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

		//$ProfileDateBirth = $_POST['ProfileDateBirth'];
		$ProfileAge = $_POST['ProfileAge'];
		$ProfileGender = $_POST['ProfileGender'];
		$ProfileResidence = $_POST['ProfileResidence'];
		$ProfileType =implode(",", $_POST['ProfileType']);
		$ProfileLocationStreet = isset($_POST['ProfileLocationStreet'])?$_POST['ProfileLocationStreet']:"";
		$ProfileLocationCity = RBAgency_Common::format_propercase(isset($_POST['ProfileLocationCity'])?$_POST['ProfileLocationCity']:"");
		$ProfileLocationState = strtoupper(isset($_POST['ProfileLocationState'])?$_POST['ProfileLocationState']:"");
		$ProfileLocationZip = isset($_POST['ProfileLocationZip'])?$_POST['ProfileLocationZip']:"";
		$ProfileLocationCountry = isset($_POST['ProfileLocationCountry'])?$_POST['ProfileLocationCountry']:"";

		$yearDob = !empty($_POST["ProfileDateBirth_Year"]) ? $_POST["ProfileDateBirth_Year"] : "0000";
		$monthDob = !empty($_POST["ProfileDateBirth_Month"]) ? $_POST["ProfileDateBirth_Month"] : "00";
		$dayDob = !empty($_POST["ProfileDateBirth_Day"]) ? $_POST["ProfileDateBirth_Day"] : "00";
		$ProfileDateBirth = $yearDob."-".$monthDob."-".$dayDob;

		$ProfileContactPhoneHome = !empty($_POST["ProfileContactPhoneHome"]) ? $_POST["ProfileContactPhoneHome"] : "";

		if(empty($userdata['user_login'])) {
			$error_msg[] = __("A username is required for registration.<br />", RBAGENCY_interact_TEXTDOMAIN);
		}
		if(username_exists($userdata['user_login'])) {
			$error_msg[] = __("Sorry, that username already exists!<br />", RBAGENCY_interact_TEXTDOMAIN);
		}
		if(!is_email($userdata['user_email'])) {
			$error_msg[] = __("You must enter a valid email address.<br />", RBAGENCY_interact_TEXTDOMAIN);
		}
		if(email_exists($userdata['user_email'])) {
			$error_msg[] = __("Sorry, that email address is already used!<br />", RBAGENCY_interact_TEXTDOMAIN);
		}
		if(count($_FILES['profile_photo_registration']['name'])>5){
			$error_msg[] = __("Unable to upload photos, please select up to 5 photos only!<br />", RBAGENCY_interact_TEXTDOMAIN);
		}

		if(!empty($error_msg)){
			
			foreach($error_msg as $err){
				$getErrorMessage .= "<p style=\"color:red;\">".$err."</p>";
			}
		}else{
			# create user			
			$new_user = wp_insert_user( $userdata );

			# Set Gallery
			if ($rb_agency_option_profilenaming == 0) {
				$profile_contact_display = $first_name . " ". $last_name;
			} elseif ($rb_agency_option_profilenaming == 1) {
				$profile_contact_display = $first_name . " ". substr($last_name, 0, 1);
			} elseif ($rb_agency_option_profilenaming == 2) {
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
			# Profile Gallery
			$profile_gallery = rb_agency_createdir($profile_gallery);

			update_user_meta($new_user, 'rb_agency_interact_pgender', $ProfileGender);
			$profileactive = null;

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
				$profileactive = $_default_registered;
			}

			# Insert to table_agency_profile
			$sql = "INSERT INTO ".table_agency_profile."
				(
				ProfileContactDisplay,
				ProfileContactNameFirst,
				ProfileContactNameLast,
				ProfileGender,
				ProfileContactEmail,
				ProfileIsActive,
				ProfileUserLinked,
				ProfileType,
				ProfileGallery,
				ProfileDateBirth,
				ProfileLocationStreet,
				ProfileLocationCity,
				ProfileLocationState,
				ProfileLocationZip,
				ProfileLocationCountry,
				ProfileContactPhoneHome
				)
				VALUES(
				'".$profile_contact_display."',
				'".$first_name."',
				'".$last_name."',
				'".$ProfileGender."',
				'".$user_email."',
				".$profileactive.",
				".$new_user.",
				'".$ProfileType."',
				'".$profile_gallery."',
				'".$ProfileDateBirth."',
				'".$ProfileLocationStreet."',
				'".$ProfileLocationCity."',
				'".$ProfileLocationState."',
				'".$ProfileLocationZip."',
				'".$ProfileLocationCountry."',
				'".$ProfileContactPhoneHome."')";

			$wpdb->query($sql);
			$id = $wpdb->insert_id;
			$NewProfileID = $id;
			# Update and set ProfileUserLinked,ProfileGallery and ProfileContactDisplay with the ProfileID
			if($rb_agency_option_profilenaming == 3){
				$update = $wpdb->query("UPDATE " . table_agency_profile . " SET ProfileGallery='ID-" . $id . "', ProfileContactDisplay='ID-" . $id . "' WHERE ProfileID='" . $id . "'");
				$profile_gallery = "ID-".$id;
				rb_agency_createdir($profile_gallery);
			}
			add_user_meta( $new_user, 'user_profile_id', $new_user);

			# Add Custom Field Values stored in Mux
			# For testing , add this array
			foreach($_POST as $key => $value) {
				if ((substr($key, 0, 15) == "ProfileCustomID") && (isset($value) && !empty($value))) {
						$profilecustomfield_date = explode("_",$key);
						if(count($profilecustomfield_date) == 2){ // customfield date
							$ProfileCustomID = substr($profilecustomfield_date[0], 15);
						} else {
							$ProfileCustomID = substr($key, 15);
						}
					 
					if(is_array($value)){
						$value =  implode(",",$value);
					}
					if(count($profilecustomfield_date) == 2){ // customfield date
						$value = date("y-m-d h:i:s",strtotime($value));
						$insert1 = $wpdb->prepare("INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomDateValue)" . "VALUES (%d,%d,%s)",$NewProfileID,$ProfileCustomID,$value);
					} else {
						$insert1 = $wpdb->prepare("INSERT INTO " . table_agency_customfield_mux . " (ProfileID,ProfileCustomID,ProfileCustomValue)" . "VALUES (%d,%d,%s)",$NewProfileID,$ProfileCustomID,$value);
					}
					$results1 = $wpdb->query($insert1);

				}
			} # End : Add Custom Field Values stored in Mux

			
			# upload photo here

			for($idx=0;$idx<=4;$idx++){
				$path_parts = pathinfo($_FILES["profile_photo_registration_".$idx]['name']);
				$safeProfileMediaFilename = RBAgency_Common::format_stripchars($path_parts['filename'] ."_". RBAgency_Common::generate_random_string(6) . "." . $path_parts['extension']);
				if(!empty($path_parts['extension'])){
					move_uploaded_file($_FILES["profile_photo_registration_".$idx]['tmp_name'], RBAGENCY_UPLOADPATH . $profile_gallery ."/".$safeProfileMediaFilename);
					if($ptype == "Models"){
						$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('" . $NewProfileID . "','Polaroid','" . $safeProfileMediaFilename . "','" . $safeProfileMediaFilename . "')");
					}else{
						$results = $wpdb->query("INSERT INTO " . table_agency_profile_media . " (ProfileID, ProfileMediaType, ProfileMediaTitle, ProfileMediaURL) VALUES ('" . $NewProfileID . "','Image','" . $safeProfileMediaFilename . "','" . $safeProfileMediaFilename . "')");
					}
				}
			}
				
			
				
			

			
			
			$arr["new"] = true;
			$arr["step1"] = true;
			$arr["step2"] = true;
			$arr["step3"] = true;
			add_user_meta($new_user, 'rb_agency_new_registeredUser',$arr);
			if ($rb_agencyinteract_option_registerapproval == 1) { // automatically approval
				// Notify admin and user
				rb_new_user_notification($new_user,$user_pass);
			} else { // manually approval
				// Notify admin and user
				rb_new_user_notification($new_user,$user_pass);
			}
			$_SESSION["ProfileUserName"] = $_POST["ProfileUserName"];
			$_SESSION["PW"] = $user_pass;
			wp_redirect($customRegistrationPageURL."/".$uriArr[1]."/?st=success");
		} # End : create user
		
	}
	$uri = substr($_SERVER['REQUEST_URI'],1);
	$requestURI =explode('/', $uri);
	
	

	echo '
			<script type="text/javascript">
			 jQuery(document).ready(function() {

				jQuery( "#ProfileGender" ).on("change",function() {
				jQuery(".custom-fields-registration").empty();
				var userGenderID = jQuery(this).val();
				console.log(userGenderID);
				//var userGenderText = jQuery( "#ProfileGender option:selected").text();
					
					var ptypeParam = "'.$ptype.'";

					jQuery.post("'.admin_url("admin-ajax.php").'", 
						{
						GenderID: userGenderID,
						action:"request_datatype_bygender_memberregister",
						profileType : "'.$ptype.'"
						})
					.done(function(data) {
						console.log(data);
											

						if(data.length>0){
							jQuery("#ProfileType-div").html(data);
							jQuery("#consent-processing").attr("style","display:none!important");
							jQuery("#registration-submit-btn").removeAttr("style");
							jQuery.ajax({
								type: "POST",
								url: "' . admin_url('admin-ajax.php').'",
								data: {
									action: "rb_get_customfields_registration",
										profile_types: "'.$ptype.'",
										gender: jQuery("#ProfileGender").val()
									},
									success: function (results) {
										console.log(results);
										jQuery(".custom-fields-registration").html();
										jQuery(".custom-fields-registration").html(results);

										if(results.length > 0){
											//hide necessary
											if(ptypeParam.length && ptypeParam == "Models" ){
												//display consent and stage name
												jQuery("#profile-stagename").removeAttr("style");
												//display consent
												jQuery("#consent-processing").removeAttr("style");
												//hide default submit button
												jQuery("#registration-submit-btn").attr("style","display:none!important");
											}else{
												jQuery("#profile-stagename").attr("style","display:none!important");
												jQuery("#consent-processing").attr("style","display:none!important");
												jQuery("#registration-submit-btn").removeAttr("style");
											}
										}
										
									}
							});	
						}else{
							jQuery("#profile-stagename").attr("style","display:none!important");
							jQuery("#ProfileType-div").html("Please select your gender first");
							jQuery("#consent-processing").attr("style","display:none!important");
							jQuery("#registration-submit-btn").removeAttr("style");
						}
						

					});
					return false;
				});


			});
			</script>
		';

	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			var profileTypeIDs = [];
			var ptypeParam = '<?php echo $ptype; ?>';
			jQuery(".DataTypeIDClassCheckbox").live('click',function(){
				var ajaxLoader = "<?php echo site_url().'/wp-content/plugins/rb-agency-interact/theme/ajax-loader-white.gif';  ?>";

				jQuery(".custom-fields-registration").html('<img src="'+ajaxLoader+'" />');

				var checkedProfileType = jQuery(this).attr('profile-type-title');
				var checked = jQuery(this).is(':checked');
				profileTypeIDs.push(checkedProfileType);
				//remove if unchecked
				if(checked!==true){
					profileTypeIDs = jQuery.grep(profileTypeIDs, function(value) {
						return value != checkedProfileType;
					});
				}else{
					
					jQuery(this).attr('checked','checked');
				}												
				
				
				console.log(jQuery.inArray('Models',profileTypeIDs));
				if(ptypeParam.length && ptypeParam == 'models'){
					//display consent and stage name
					jQuery("#profile-stagename").removeAttr('style');
					//display consent
					jQuery("#consent-processing").removeAttr('style');
					//hide default submit button
					jQuery("#registration-submit-btn").attr('style','display:none!important');
				}else{
					jQuery("#profile-stagename").attr('style','display:none!important');
					jQuery("#consent-processing").attr('style','display:none!important');
					jQuery("#registration-submit-btn").removeAttr('style');
				}
										
				//here , make ajax call to get custom fields
				jQuery.ajax({
					type: "POST",
					url: "<?php echo admin_url('admin-ajax.php') ?>",
					data: {
						action: "rb_get_customfields_registration",
							'profile_types': profileTypeIDs,
							'gender': jQuery("#ProfileGender").val()
						},
						success: function (results) {
							//console.log(results);
							jQuery(".custom-fields-registration").html();
							jQuery(".custom-fields-registration").html(results);
						}
				});	
																			
			});


			jQuery("#ProfileLocationCountry").change(function(){
				jQuery("#profile-state").removeAttr('style');
			});


				
				jQuery("#ProfileDateBirth_Year").on("change",function(){
					var m = jQuery("#ProfileDateBirth_Month option:selected").val();
				var d = jQuery("#ProfileDateBirth_Day option:selected").val();
				var y = jQuery("#ProfileDateBirth_Year option:selected").val();
					jQuery("input[name=hidden_dob]").val(y+"-"+m+"-"+d);
				});
				jQuery("#ProfileDateBirth_Month").change(function(){
					var m = jQuery("#ProfileDateBirth_Month option:selected").val();
				var d = jQuery("#ProfileDateBirth_Day option:selected").val();
				var y = jQuery("#ProfileDateBirth_Year option:selected").val();
					jQuery("input[name=hidden_dob]").val(y+"-"+m+"-"+d);
				});
				jQuery("#ProfileDateBirth_Day").on("change",function(){
					var m = jQuery("#ProfileDateBirth_Month option:selected").val();
				var d = jQuery("#ProfileDateBirth_Day option:selected").val();
				var y = jQuery("#ProfileDateBirth_Year option:selected").val();
					jQuery("input[name=hidden_dob]").val(y+"-"+m+"-"+d);
				});
								
		});
	</script>
	<?php

	if(!isset($_GET['st'])){
		
		//print (new ReflectionFunction("request_datatype_bygender_memberregister"))->getFileName();
		# Default fields
		echo "<form method=\"POST\" action=\"".$customRegistrationPageURL.$uriArr[1]. "\" enctype=\"multipart/form-data\">";
		echo "<div id='member-register' class='rbform'>";
		echo "	<div id=\"profile-registration-error\" class=\"rbfield\">\n";
		echo !empty($getErrorMessage) ? $getErrorMessage : "";
		echo "</div>";
		echo "	<div id=\"profile-firstname\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("First Name", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactNameFirst\" name=\"ProfileContactNameFirst\" value=\"".$first_name."\"/></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-surname\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Surname", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactNameLast\" name=\"ProfileContactNameLast\" value=\"".$last_name."\"/></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-username\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Username", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileUserName\" name=\"ProfileUserName\" value=\"".$user_login."\"/></div>\n";
		echo "	</div>\n";
		if ($rb_agencyinteract_option_registerconfirm == 1 && $rb_agencyinteract_option_useraccountcreation == 1) {

		echo "       <div id=\"profile-password\" class=\"rbfield rbpassword rbsingle\">\n";
		echo "   		<label for=\"profile_password\">". __("Password (required)", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
		echo "   		<div><input class=\"text-input\" name=\"profile_password\" type=\"password\" id=\"profile_password\" value=\""; if ( $error ) echo esc_html( $_POST['profile_password'], 1 ); echo "\" /></div>\n";
		echo "       </div><!-- #profile-password -->\n";
		}
		echo "	<div id=\"profile-email\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("E-mail", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileEmail\" name=\"ProfileEmail\" value=\"".$user_email."\"/></div>\n";
		echo "	</div>\n";

		if($ptype == 'Locations'){
			echo "	<div id=\"profile-birthdate\" class=\"rbfield rbselect rbmulti rbblock\" style=\"display:none;\">\n";
				echo "		<label>". __("Birthdate", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
					echo "		<div>\n";
									/* Month */ 
					echo "		<div style=\"display:inline-block;width:33%;margin-right:1px;\">\n";
									$monthName = array(1=> "January", "February", "March","April", "May", "June", "July", "August","September", "October", "November", "December"); 
					echo "			<select name=\"ProfileDateBirth_Month\" id=\"ProfileDateBirth_Month\">\n";
					echo "			<option value=\"\"><span style=\"font-size:9px;\" >".__("Month",RBAGENCY_interact_TEXTDOMAIN)."</span> </option>\n";
						for ($currentMonth = 1; $currentMonth <= 12; $currentMonth++ ) {
							echo "			<option value=\"". $currentMonth ."\">". $monthName[$currentMonth] ."</option>\n";
					}
					echo "			</select>\n";
					echo "		</div>\n";
									/* Day */ 
					
					echo "		<div style=\"display:inline-block;width:33%;\">\n";
					echo "			<select name=\"ProfileDateBirth_Day\" id=\"ProfileDateBirth_Day\">\n";
					echo "			<option value=\"\"><span style=\"font-size:9px;\" >".__("Day",RBAGENCY_interact_TEXTDOMAIN)." </span> </option>\n";
						for ($currentDay = 1; $currentDay <= 31; $currentDay++ ) {
							echo "			<option value=\"". $currentDay ."\">". $currentDay ."</option>\n";
						}
					echo "			</select>\n";
					echo "		</div>\n";

									/* Year */ 
					echo "		<div style=\"display:inline-block;width:33%;\">\n";
					echo "			<select name=\"ProfileDateBirth_Year\" id=\"ProfileDateBirth_Year\">\n";
					echo "			<option value=\"\"><span style=\"font-size:9px;\" >".__("Year",RBAGENCY_interact_TEXTDOMAIN)." </span> </option>\n";
						for ($currentYear = 1940; $currentYear <= date("Y")+6; $currentYear++ ) {
							echo "			<option value=\"". $currentYear ."\">". $currentYear ."</option>\n";
					}
					echo "			</select>\n";
					echo "		</div>\n";
				echo "		</div>\n";
			echo "		</div>\n";
		}else{
			echo " <input type=\"hidden\" name=\"hidden_dob\" />";
			echo "	<div id=\"profile-birthdate\" class=\"rbfield rbselect rbmulti rbblock\">\n";
				echo "		<label>". __("Birthdate", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
					echo "		<div>\n";
									/* Month */ 
					echo "		<div style=\"display:inline-block;width:33%;margin-right:1px;\">\n";
									$monthName = array(1=> "January", "February", "March","April", "May", "June", "July", "August","September", "October", "November", "December"); 
					echo "			<select name=\"ProfileDateBirth_Month\" id=\"ProfileDateBirth_Month\">\n";
					echo "			<option value=\"\"><span style=\"font-size:9px;\" >".__("Month",RBAGENCY_interact_TEXTDOMAIN)."</span> </option>\n";
						for ($currentMonth = 1; $currentMonth <= 12; $currentMonth++ ) {
							echo "			<option value=\"". $currentMonth ."\">". $monthName[$currentMonth] ."</option>\n";
					}
					echo "			</select>\n";
					echo "		</div>\n";
									/* Day */ 
					echo "		<div style=\"display:inline-block;width:33%;\">\n";
					echo "			<select name=\"ProfileDateBirth_Day\" id=\"ProfileDateBirth_Day\">\n";
					echo "			<option value=\"\"><span style=\"font-size:9px;\" >".__("Day",RBAGENCY_interact_TEXTDOMAIN)." </span> </option>\n";
						for ($currentDay = 1; $currentDay <= 31; $currentDay++ ) {
							echo "			<option value=\"". $currentDay ."\">". $currentDay ."</option>\n";
						}
					echo "			</select>\n";
					echo "		</div>\n";

									/* Year */ 
					echo "		<div style=\"display:inline-block;width:33%;\">\n";
					echo "			<select name=\"ProfileDateBirth_Year\" id=\"ProfileDateBirth_Year\">\n";
					echo "			<option value=\"\"><span style=\"font-size:9px;\" >".__("Year",RBAGENCY_interact_TEXTDOMAIN)." </span> </option>\n";
						for ($currentYear = 1940; $currentYear <= date("Y")+6; $currentYear++ ) {
							echo "			<option value=\"". $currentYear ."\">". $currentYear ."</option>\n";
					}
					echo "			</select>\n";
					echo "		</div>\n";
				echo "		</div>\n";
			echo "		</div>\n";
		}

		
		# End : Default fields

		# Location address
		echo "	<div id=\"profile-country\" class=\"rbfield rbselect rbsingle\">\n";
		echo "      <label>" . __("Country", RBAGENCY_interact_TEXTDOMAIN) . ":</label>\n";
		echo "      <div>\n";

		$query_get ="SELECT * FROM `". table_agency_data_country ."` ORDER BY CountryTitle ASC" ;
		$result_query_get = $wpdb->get_results($query_get);
		$location= site_url();
		echo '<input type="hidden" id="url" value="'.$location.'">';
		echo "<select name=\"ProfileLocationCountry\" id=\"ProfileLocationCountry\"  onchange='javascript:populateStates(\"ProfileLocationCountry\",\"ProfileLocationState\");'>";
		echo '<option value="">'. __("Select country", RBAGENCY_interact_TEXTDOMAIN) .'</option>';
		foreach($result_query_get as $r){
				$selected =$ProfileLocationCountry==$r->CountryID?"selected=selected":"";
			echo '<option '.$selected.' value='.$r->CountryID.' >'.$r->CountryTitle.'</option>';
		}
		echo '</select>';
		echo "      </div>\n";
		echo "    </div>\n";


		echo "	<div id=\"profile-state\" class=\"rbfield rbtext rbsingle\" style=\"display:none;\">\n";
		echo "      <label>" . __("State", RBAGENCY_interact_TEXTDOMAIN) . ":</label>\n";
		echo "      <div>\n";
		$query_get ="SELECT * FROM `".table_agency_data_state."` ORDER BY StateTitle ASC" ;
		$result_query_get = $wpdb->get_results($query_get);
		echo '<select name="ProfileLocationState" id="ProfileLocationState">';
		echo '<option value="">'. __("Select state", RBAGENCY_interact_TEXTDOMAIN) .'</option>';
		foreach($result_query_get as $r){
			$selected =$ProfileLocationState==$r->StateID?"selected=selected":"";
			echo '<option '.$selected.' value='.$r->StateID.' >'.$r->StateTitle.'</option>';
		}
		echo '</select>';

		echo "      </div>\n";
		echo "    </div>\n";

		if($ptype == 'Locations' || $ptype == 'Make-Up_Artists'){
			echo "	<div id=\"profile-street\" class=\"rbfield rbtext rbsingle\" style=\"display:none;\">\n";
			echo "		<label>". __("Street", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
			echo "		<div><input type=\"text\" id=\"ProfileLocationStreet\" name=\"ProfileLocationStreet\" /></div>\n";
			echo "	</div>\n";
		}else{
			echo "	<div id=\"profile-street\" class=\"rbfield rbtext rbsingle\" >\n";
			echo "		<label>". __("Street", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
			echo "		<div><input type=\"text\" id=\"ProfileLocationStreet\" name=\"ProfileLocationStreet\" /></div>\n";
			echo "	</div>\n";
		}

		if($ptype == 'Locations'){
			echo "	<div id=\"profile-city\" class=\"rbfield rbtext rbsingle\" style=\"display:none;\">\n";
			echo "		<label>". __("City", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
			echo "		<div><input type=\"text\" id=\"ProfileLocationCity\" name=\"ProfileLocationCity\" /></div>\n";
			echo "	</div>\n";
		}else{
			echo "	<div id=\"profile-city\" class=\"rbfield rbtext rbsingle\" >\n";
			echo "		<label>". __("City", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
			echo "		<div><input type=\"text\" id=\"ProfileLocationCity\" name=\"ProfileLocationCity\" /></div>\n";
			echo "	</div>\n";

		}


		if($ptype == 'Locations' || $ptype == 'Make-Up_Artists'){
			echo "	<div id=\"profile-zip\" class=\"rbfield rbtext rbsingle\" style=\"display:none;\" >\n";
			echo "		<label>". __("Zip", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
			echo "		<div><input type=\"text\" id=\"ProfileLocationZip\" name=\"ProfileLocationZip\" /></div>\n";
			echo "	</div>\n";
		}else{
			echo "	<div id=\"profile-zip\" class=\"rbfield rbtext rbsingle\" >\n";
			echo "		<label>". __("Zip", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
			echo "		<div><input type=\"text\" id=\"ProfileLocationZip\" name=\"ProfileLocationZip\" /></div>\n";
			echo "	</div>\n";
		}

		# Tel no.
		echo "	<div id=\"profile-telno\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Telephone Number", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactPhoneHome\" name=\"ProfileContactPhoneHome\" /></div>\n";
		echo "	</div>\n";


		$q = "SELECT DataTypeID FROM ".table_agency_data_type." WHERE DataTypeTag = '".$uriArr[1]."'";
		$getDataTypeIDs = $wpdb->get_results($q,ARRAY_A);
		$getDataTypeID = "";
		foreach($getDataTypeIDs as $DataType){
			$getDataTypeID = $DataType["DataTypeID"];
		}
		$getGendersBasedOnDataType = get_option("DataTypeID_".$getDataTypeID);

		# Gender
		echo "	<div id=\"profile-gender\" class=\"rbfield rbselect rbsingle\">\n";
		echo "		<label>". __("Gender", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "			<div><select id='ProfileGender' name=\"ProfileGender\">";
		$query= "SELECT GenderID, GenderTitle FROM " .  table_agency_data_gender . " GROUP BY GenderTitle ";
							$queryShowGender = $wpdb->get_results($query,ARRAY_A);
							echo "<option value=''>".__("--Please Select--",RBAGENCY_interact_TEXTDOMAIN)."</option>";
							if(!empty($uriArr[1])){
								foreach($queryShowGender as $dataShowGender){
									if(strpos($getGendersBasedOnDataType, $dataShowGender["GenderTitle"])>-1){
										echo "<option value=\"".$dataShowGender["GenderID"]."\" ". selected($ProfileGender ,$dataShowGender["GenderID"],false).">".$dataShowGender["GenderTitle"]."</option>";
									}
									
								}
							}else{
								foreach($queryShowGender as $dataShowGender){
									echo "<option value=\"".$dataShowGender["GenderID"]."\" ". selected($ProfileGender ,$dataShowGender["GenderID"],false).">".$dataShowGender["GenderTitle"]."</option>";
									
								}
							}
							
		echo "			</select></div>";
		echo "	</div>\n";

		# Display Profile Type

		echo "	<div id=\"profile-type\" class=\"rbfield rbcheckbox rbmulti\">\n";
		echo "		<label for=\"profile_type\">". __("Type of Profile", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";

					//check for parentid column
					$sql = "SELECT DataTypeParentID FROM ".$wpdb->prefix."agency_data_type LIMIT 1";
					$r = $wpdb->get_results($sql);
					if(count($r) == 0){
						//create column
						$queryAlter = "ALTER TABLE " . $wpdb->prefix ."agency_data_type ADD DataTypeParentID varchar(20) default 0";
						$resultsDataAlter = $wpdb->query($queryAlter,ARRAY_A);
					}

					
		echo "		<div id='ProfileType-div'>";
		
		echo "			Please select your gender first";
			
		echo "		</div>";
		echo "</div><!-- #profile-type -->\n";
		# End : Display Profile Type



		if($ptype == 'Locations' || $ptype == 'Make-Up_Artists'){
			# display here bulk upload
			echo "<div id=\"upload-casting-photo\" class=\"rbfield\">\n";
			echo "		<label>". __("Select Photos", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
			echo "<div>";
			for($idx=0;$idx<=4;$idx++){
				echo "		<input type='file' name='profile_photo_registration_".$idx."'>";
			}			
			echo "</div>";
			echo "</div>";
		}

		# will show only if model
		echo "	<div id=\"profile-stagename\" class=\"rbfield rbtext rbsingle\" style=\"display:none;\">\n";
		echo "		<label>". __("Stage name", RBAGENCY_interact_TEXTDOMAIN) .":</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileDisplay\" name=\"ProfileDisplay\"/></div>\n";
		echo "	</div>\n";

		

		
		# Display custom fields
		echo "<div class=\"custom-fields-registration\">";
		echo "</div>";
		# End : Display custom fields	

		# Consent processing
		echo "	<div id=\"consent-processing\" class=\"rbfield\" style=\"display:none;\">\n";
		echo "		<h3 style=\"font-size:22px;text-align:center;\">". __("Consent to the processing of personal data", RBAGENCY_interact_TEXTDOMAIN) ."</h3><br/>\n";
		echo "<p style=\"text-align:justify;font-size:12px;\">".__("Please note that Nikkis Models is an agency recruiting models for the production of erotic and sexual themes. The cooperation between the model and the agency take place only after the model was properly instructed about the content of such cooperation and only after granting unconditional consent to such type of cooperation.",RBAGENCY_interact_TEXTDOMAIN)."</p><br />";

		# Yes, I Agree
		echo "<input type=\"submit\" name=\"agree_btn\" class=\"agree-btn\" value=\"".__('Yes, I agree', RBAGENCY_interact_TEXTDOMAIN)."\" style=\"padding: 15px 30px;margin-left:165px;\"/>";
		echo "	</div>\n";
		# End : Consent processing

		# Default submit button
		echo "	<div id=\"registration-submit-btn\" class=\"rbfield\">\n";	
		echo "<input type=\"submit\" name=\"agree_btn\" class=\"agree-btn\" value=\"".__('Submit', RBAGENCY_interact_TEXTDOMAIN)."\" style=\"padding: 15px 30px;margin-left:165px;\"/>";
		echo "	</div>\n";

		echo "</form>";
		echo "</div>";


	}else{ # End : st is not set

		# Put in here what will happen after registration

		printf( "<p style=\"text-align:center;\">".__("Thank you for registering, %s!", RBAGENCY_interact_TEXTDOMAIN)."<p>", $_SESSION['ProfileUserName'] );		
		echo "<p style=\"text-align:center;\">".__("Please check your email for the next step to complete registration.", RBAGENCY_interact_TEXTDOMAIN)."</p>";		
		echo "<p style=\"text-align:center;\">".__("<i>(It might go to your spam folder )</i>", RBAGENCY_interact_TEXTDOMAIN)."</p>";
		//echo $_SESSION["PW"];
		//echo "<input type=\"hidden\" value=\"".$_SESSION["PW"]."\" />";

	} # End
	
}

function rbGetCurrentUserProfileTypeTitle(){
	global $wpdb;

	$query = "SELECT ProfileType FROM ". table_agency_profile ." WHERE ProfileUserLinked = ". rb_agency_get_current_userid();
	$results = $wpdb->get_results($query,ARRAY_A);
	$profileType = "";
	foreach($results as $result){
		$profileType = $result['ProfileType'];
	}

	$dataTypeTitleArr = array();
	if(strpos($profileType,',') !== false){
		$profileTypeArr = explode(',',$profileType);
		foreach($profileTypeArr as $profileTypeID){
			$q = "SELECT DataTypeTitle FROM ".table_agency_data_type." WHERE DataTypeID = $profileTypeID";
			$results = $wpdb->get_results($q,ARRAY_A);
			foreach($results as $result){
				$dataTypeTitleArr[] = $result['DataTypeTitle'];
			}
		}
	}else{
		$q = "SELECT DataTypeTitle FROM ".table_agency_data_type." WHERE DataTypeID = $profileType";
		$results = $wpdb->get_results($q,ARRAY_A);
		foreach($results as $result){
			$dataTypeTitleArr[] = $result['DataTypeTitle'];
		}
	}
	$profileDataTypeTitle = implode(',',$dataTypeTitleArr);
	return $profileDataTypeTitle;
}

?>