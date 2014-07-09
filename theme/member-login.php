<?php
// *************************************************************************************************** //
// Respond to Login Request
$error = "";
$have_error = false;

if ( $_SERVER['REQUEST_METHOD'] == "POST" && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) {

	global $error;
    $login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => isset($_POST['remember-me'])?$_POST['remember-me']:false ), false );
	
    get_currentuserinfo();

		
	if(!is_wp_error($login)) {
    	wp_set_current_user($login->ID);  // populate
	   	get_user_login_info();
	}else{
			$error .= __( $login->get_error_message(), rb_agency_interact_TEXTDOMAIN);
			$have_error = true;
			
		
	}
}

function get_user_login_info(){

    global $user_ID, $wpdb;
	$redirect = isset($_POST["lastviewed"])?$_POST["lastviewed"]:"";
	get_currentuserinfo();
	$user_info = get_userdata( $user_ID );

    // Check if user is registered as Model/Talent
    $profile_is_active = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".table_agency_profile." WHERE ProfileUserLinked = %d  ",$user_ID));
    $is_model_or_talent  = $wpdb->num_rows;
    echo  $is_model_or_talent;
   if(isset($user_ID) && ($is_model_or_talent > 0) || current_user_can("publish_pages")){
		
		// If user_registered date/time is less than 48hrs from now
			
		if(!empty($redirect)){
			header("Location: ". $redirect);
		} else {

			// If Admin, redirect to plugin
			if(current_user_can("publish_pages")) {
				header("Location: ". admin_url("admin.php?page=rb_agency_menu"));
			}

			// Message will show for 48hrs after registration
			/*elseif( strtotime( $user_info->user_registered ) > ( time() - 172800 ) ) {
				if(get_user_meta($user_ID, 'rb_agency_interact_clientdata', true)){
						header("Location: ". get_bloginfo("wpurl"). "/casting-dashboard/");
				} else {

						header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
				}
			} */else {
					$rb_agency_new_registeredUser = get_user_meta($user_ID,'rb_agency_new_registeredUser',true);
					if(!empty($rb_agency_new_registeredUser)){
							header("Location: ". get_bloginfo("wpurl"). "/profile-member/account/");
					}else
					if(get_user_meta($user_ID, 'rb_agency_interact_clientdata', true)){
							header("Location: ". get_bloginfo("wpurl"). "/casting-dashboard/");
					} else {
						     header("Location: ". get_bloginfo("wpurl"). "/profile-member/");
					}

			}
	  	}
	}/* elseif($profile_is_active->ProfileIsActive == 3){
				wp_logout();
				header("Location: ". get_bloginfo("wpurl"). "/profile-login/?ref=pending-approval");
					
	}*/ else {
			 	wp_logout();
				header("Location: ". get_bloginfo("wpurl"). "/profile-login/?ref=casting");	
		}
}

add_filter('login_redirect', 'rb_agency_interact_login_redirect', 10, 3);
	
// ****************************************************************************************** //
// Already logged in 
	if (is_user_logged_in()) {
	
		global $user_ID; 
		$login = get_userdata( $user_ID );
				 get_user_login_info();	 

			
			echo "    <p class=\"alert\">\n";
						printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', rb_agency_interact_TEXTDOMAIN), "/profile-member/", $login->display_name );
			echo "		 <a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __('Log out of this account', rb_agency_interact_TEXTDOMAIN) ."\">". __('Log out &raquo;', rb_agency_interact_TEXTDOMAIN) ."</a>\n";
			echo "    </p><!-- .alert -->\n";
			
	
// ****************************************************************************************** //
// Not logged in
	} else {
// *************************************************************************************************** //
		// Prepare Page
		get_header();

			echo "<div id=\"rbcontent\" class=\"rb-interact rb-interact-login\">\n";
			
				// Show Login Form
				$hideregister = true;
				include("include-login.php");

			echo "</div><!-- #rbcontent -->\n";

		// Get Footer
		get_footer();
	
	} // Done
	

	
?>