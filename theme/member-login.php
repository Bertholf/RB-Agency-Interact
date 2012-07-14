<?php

// *************************************************************************************************** //
// Respond to Login Request

	if ( $_SERVER['REQUEST_METHOD'] == "POST" && !empty( $_POST['action'] ) && $_POST['action'] == 'log-in' ) {
	
		global $error;
		$login = wp_login( $_POST['user-name'], $_POST['password'] );
		$login = wp_signon( array( 'user_login' => $_POST['user-name'], 'user_password' => $_POST['password'], 'remember' => $_POST['remember-me'] ), false );
          get_currentuserinfo();

			if( $login) {

			   get_user_login_info();

			}
			
	}
function  get_user_login_info(){

	      global $user_ID;  

	      get_currentuserinfo();

		        $user_info = get_userdata( $user_ID ); 

				

				if($user_ID){

					// If user_registered date/time is less than 48hrs from now

					// Message will show for 48hrs after registration

					if( $user_info->user_level > 7) {

						header("Location: ". get_bloginfo("wpurl"). "/wp-admin/");

					} 

					else if ( strtotime( $user_info->user_registered ) > ( time() - 172800 ) ) {

						header("Location: ". get_bloginfo("wpurl"). "/profile-member/account/");

					} else {

						header("Location: ". get_bloginfo("wpurl"). "/profile-member/");

					}
				

				}
				elseif(empty($_POST['user-name']) || empty($_POST['password']) ){
					
				}
				else{

					 // Reload

				    header("Location: ". get_bloginfo("wpurl")."/profile-login/&callback=".md5(rand(1000,9999)));	

				}

}



// ****************************************************************************************** //
// Already logged in 
	if (is_user_logged_in()) { 
	
	
		global $user_ID; 
		$login = get_userdata( $user_ID );
				 get_user_login_info();	 
			/*

			echo "    <p class=\"alert\">\n";

						printf( __('You have successfully logged in as <a href="%1$s" title="%2$s">%2$s</a>.', rb_agencyinteract_TEXTDOMAIN), "/profile-member/", $login->display_name );

			echo "		 <a href=\"". wp_logout_url( get_permalink() ) ."\" title=\"". __('Log out of this account', rb_agencyinteract_TEXTDOMAIN) ."\">". __('Log out &raquo;', rb_agencyinteract_TEXTDOMAIN) ."</a>\n";

			echo "    </p><!-- .alert -->\n";

			*/

	

// ****************************************************************************************** //

// Not logged in

	} else { 



		// *************************************************************************************************** //

		// Prepare Page

		get_header();



		echo "<div id=\"container\" class=\"one-column rb-agency-interact-account\">\n";

		echo "  <div id=\"content\">\n";

		

			// Show Login Form

			$hideregister = true;

			include("include-login.php"); 	



		echo "  </div><!-- #content -->\n";

		echo "</div><!-- #container -->\n";

		

		// Get Sidebar 

		$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');

			$rb_agencyinteract_option_profilemanage_sidebar = $rb_agencyinteract_options_arr['rb_agencyinteract_option_profilemanage_sidebar'];

			$LayoutType = "";

			if ($rb_agencyinteract_option_profilemanage_sidebar) {

				echo "	<div id=\"profile-sidebar\" class=\"manage\">\n";

					$LayoutType = "profile";

					get_sidebar(); 

				echo "	</div>\n";

			}

		// Get Footer

		get_footer();

	

	} // Done

?>