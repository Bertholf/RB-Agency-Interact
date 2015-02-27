<?php
// *************************************************************************************************** //
// Respond to Login Request
$error = "";
$have_error = false;
	$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_redirect_first_time = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time']:1;
	$rb_agencyinteract_option_redirect_first_time_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_first_time_url']:"/profile-member/account/";
	$rb_agencyinteract_option_redirect_custom_login = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_custom_login']:0;
	$rb_agencyinteract_option_redirect_afterlogin = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin']:1;
	$rb_agencyinteract_option_redirect_afterlogin_url = isset($rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url'])?$rb_agencyinteract_options_arr['rb_agencyinteract_option_redirect_afterlogin_url']:"/profile-member/account/";


if ( $_SERVER['REQUEST_METHOD'] == "POST" && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) {

	global $error;
    $login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => isset($_POST['remember-me'])?$_POST['remember-me']:false ), false );
	
    get_currentuserinfo();

		
	if(!is_wp_error($login)) {
    	wp_set_current_user($login->ID);  // populate
	   	get_user_login_info();
	}else{
			$error .= __( $login->get_error_message(), RBAGENCY_interact_TEXTDOMAIN);
			$have_error = true;
			
		
	}
}

function get_user_login_info(){
	// get options
  
    global $user_ID, $wpdb;
	$redirect = isset($_POST["lastviewed"])?$_POST["lastviewed"]:"";
	get_currentuserinfo();
	$user_info = get_userdata( $user_ID );

    // Check if user is registered as Model/Talent
    $profile_is_active = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_profile." WHERE ProfileUserLinked = %d  ",$user_ID));
    $is_model_or_talent  = $wpdb->num_rows;
   
    if(isset($user_ID) && ($is_model_or_talent > 0) || current_user_can("edit_posts")){
		
			// If user_registered date/time is less than 48hrs from now
			if(!empty($redirect)){
				header("Location: ". $redirect);
				exit;
			} else {

				// If Admin, redirect to plugin
				if(current_user_can("edit_posts")) {
					header("Location: ". admin_url("admin.php?page=rb_agency_menu"));
					exit;
				}

				// Message will show for 48hrs after registration
				/*elseif( strtotime( $user_info->user_registered ) > ( time() - 172800 ) ) {
					if(get_user_meta($user_ID, 'rb_agency_interact_clientdata', true)){
							header("Location: ". get_bloginfo("wpurl"). "/casting-dashboard/");
					} else {

							header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
					}
				} */
				else {
						$rb_agency_new_registeredUser = get_user_meta($user_ID,'rb_agency_new_registeredUser',true);
						if(!empty($rb_agency_new_registeredUser)){
							  if($rb_agencyinteract_option_redirect_first_time == 1){
							  	    header("Location: ". get_bloginfo("wpurl"). "/profile-member/account/");
							  	    exit;
							  }else{
									header("Location: ". $rb_agencyinteract_option_redirect_first_time_url);
									exit;
							  }
						}else{
							if(get_user_meta($user_ID, 'rb_agency_interact_clientdata', true)){
									header("Location: ". get_bloginfo("wpurl"). "/casting-dashboard/");
									exit;
							} else {
								if($rb_agencyinteract_option_redirect_afterlogin == 1){
								     header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
								     exit;
								}else{
								     header("Location: ". $rb_agencyinteract_option_redirect_afterlogin_url);
								     exit;
								}
							}
						}

				}
		  	}
	} elseif($profile_is_active->ProfileIsActive == 3){
					header("Location: ". get_bloginfo("wpurl"). "/profile-member/pending/");
					exit;
	} else {
			 $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
	   	     $is_casting  = $wpdb->num_rows;
	   		    if( $is_casting > 0){
				 	wp_logout();
					header("Location: ". get_bloginfo("wpurl"). "/profile-login/?ref=casting");	
					exit;
				}else{ // user is a model/talent but wp user_id is not linked to any rb profile.
					if($rb_agencyinteract_option_redirect_afterlogin == 1){
								     header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
								     exit;
					}else{
								     header("Location: ". $rb_agencyinteract_option_redirect_afterlogin_url);
								     exit;
					}
				}
	}
}

//add_filter('login_redirect', 'rb_agency_interact_login_redirect', 10, 3);
	
// ****************************************************************************************** //
// Already logged in 
	if (is_user_logged_in()) {
		global $user_ID;
		
		if(current_user_can("edit_posts")) {
			wp_redirect(admin_url("admin.php?page=rb_agency_menu"));
			exit;
		}

		$wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_casting." WHERE CastingUserLinked = %d  ",$user_ID));
	   	$is_casting  = $wpdb->num_rows;

	   	if( $is_casting > 0){
				 					wp_redirect(get_bloginfo("wpurl"). "/casting-dashboard/");	
									exit;
		}else{ // user is a model/talent but wp user_id is not linked to any rb profile.
					if($rb_agencyinteract_option_redirect_afterlogin == 1){
								     wp_redirect(get_bloginfo("wpurl"). "/profile-member/");
								     exit;
					}else{
								     wp_redirect($rb_agencyinteract_option_redirect_afterlogin_url);
								     exit;
					}
		}
			
		// Call Header
		echo $rb_header = RBAgency_Common::rb_header();
		echo "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";
		
		global $user_ID; 
		$login = get_userdata( $user_ID );
				 get_user_login_info();	 

			
			echo "    <p class=\"alert\">\n";
						printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', RBAGENCY_interact_TEXTDOMAIN), "/profile-member/", $login->display_name );
			echo "		 <a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __('Log out of this account', RBAGENCY_interact_TEXTDOMAIN) ."\">". __('Log out &raquo;', RBAGENCY_interact_TEXTDOMAIN) ."</a>\n";
			echo "    </p><!-- .alert -->\n";
		echo "</div><!-- #rbcontent -->\n";

		// Call Footer
		echo $rb_footer = RBAgency_Common::rb_footer();
	
	
// ****************************************************************************************** //
// Not logged in
	} else {
// *************************************************************************************************** //
		// Prepare Page
		
		// Call Header
		echo $rb_header = RBAgency_Common::rb_header();

			echo "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";
			
				// Show Login Form
				$hideregister = true;
				include("include-login.php");

			echo "</div><!-- #rbcontent -->\n";

		// Call Footer
		echo $rb_footer = RBAgency_Common::rb_footer();
	
	} // Done
	

	
?>