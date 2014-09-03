<?php
	/* Load registration file. */
	//require_once( ABSPATH . WPINC . '/registration.php' );
	
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

echo "     <div id=\"rbsignin-register\" class=\"rbinteract\">\n";
			$ref = get_query_var("ref");

			if (isset($error)){
			echo "<p class=\"error\">". $error ."</p>\n";
			}
			if (isset($ref) && $ref == "pending-approval") {
			echo "<p id=\"message\" class=\"updated\">". __("Your account is pending for approval.", rb_agency_interact_TEXTDOMAIN). "</p>\n";
			}
			if (isset($ref) && $ref == "casting") {
			echo "<p id=\"message\" class=\"updated\">". __("Your account is not registered as Talent/Model.", rb_agency_interact_TEXTDOMAIN).  __(" Click", rb_agency_interact_TEXTDOMAIN)." <a href=\"".get_bloginfo("url")."/casting-login/\">".__("here", rb_agency_interact_TEXTDOMAIN)."</a> ".__("to login as Casting.", rb_agency_interact_TEXTDOMAIN)."</p>\n";
			}
			
			
			if(isset($ref) && $ref == "reset_password"){
				echo "<p  id=\"message\" class=\"updated\">".__("Check your e-mail for the reset link to create a new password.", rb_agency_interact_TEXTDOMAIN)."</p>\n";
			}

echo "        <div id=\"rbsign-in\" class=\"inline-block\">\n";
echo "          <h1>". __("Members Sign in", rb_agency_interact_TEXTDOMAIN). "</h1>\n";
echo "          <form name=\"loginform\" id=\"login\" action=\"". network_site_url("/"). "profile-login/\" method=\"post\">\n";
echo "            <div class=\"field-row\">\n";
echo "              <label for=\"user-name\">". __("Username", rb_agency_interact_TEXTDOMAIN). "</label><input type=\"text\" name=\"user-name\" value=\"". (isset($_POST['user-name']) ? esc_html($_POST['user-name']):"") ."\" id=\"user-name\" />\n";
echo "            </div>\n";
echo "            <div class=\"field-row\">\n";
echo "              <label for=\"password\">". __("Password", rb_agency_interact_TEXTDOMAIN). "</label><input type=\"password\" name=\"password\" value=\"\" id=\"password\" /> <a href=\"". get_bloginfo('wpurl') ."/wp-login.php?action=lostpassword&redirect_to=/profile-login/?ref=reset_password\">". __("forgot password", rb_agency_interact_TEXTDOMAIN). "?</a>\n";
echo "            </div>\n";
echo "            <div class=\"field-row\">\n";
echo "              <label><input type=\"checkbox\" name=\"remember-me\" value=\"forever\" /> ". __("Keep me signed in", rb_agency_interact_TEXTDOMAIN). "</label>\n";
echo "            </div>\n";
echo "            <div class=\"field-row submit-row\">\n";
echo "              <input type=\"hidden\" name=\"action\" value=\"log-in\" />\n";
echo "              <input type=\"submit\" value=\"". __("Sign In", rb_agency_interact_TEXTDOMAIN). "\" /><br />\n";
echo "            </div>\n";
echo "          </form>\n";
echo "        </div> <!-- rbsign-in -->\n";

if(isset($rb_agencyinteract_option_switch_sidebar) && $rb_agencyinteract_option_switch_sidebar == 1){
			echo "        <div id=\"rbsign-up\" class=\"inline-block\">\n";
			if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow == 1)) {

				echo "          <div id=\"talent-register\" class=\"register\">\n";
				echo "            <h1>". __("Not a member", rb_agency_interact_TEXTDOMAIN). "?</h1>\n";
				echo "            <h3>". __("Talent", rb_agency_interact_TEXTDOMAIN). " - ". __("Register here", rb_agency_interact_TEXTDOMAIN). "</h3>\n";
				echo "            <ul>\n";
				echo "              <li>". __("Create your free profile page", rb_agency_interact_TEXTDOMAIN). "</li>\n";
				echo "              <li>". __("Apply to Auditions & Jobs", rb_agency_interact_TEXTDOMAIN). "</li>\n";
				echo "            </ul>\n";
				echo "              <a href=\"". get_bloginfo("wpurl") ."/profile-register/talent\" class=\"rb_button\">". __("Register as Talent / Model", rb_agency_interact_TEXTDOMAIN). "</a>\n";
				echo "          </div> <!-- talent-register -->\n";
				echo "          <div class=\"clear line\"></div>\n";

						/*
						 * Casting Integratino
						 */
						/*
						if (function_exists('rb_agency_casting_menu')) {
								echo "          <div id=\"agent-register\" class=\"register\">\n";
								echo "            <h3>". __("Casting Agents & Producers", rb_agency_interact_TEXTDOMAIN). "</h3>\n";
								echo "            <ul>\n";
								echo "              <li>". __("List Auditions & Jobs free", rb_agency_interact_TEXTDOMAIN). "</li>\n";
								echo "              <li>". __("Contact People in the Talent Directory", rb_agency_interact_TEXTDOMAIN). "</li>\n";
								echo "              <li><a href=\"". get_bloginfo("wpurl") ."/casting-register/\" class=\"rb_button\">". __("Register as Agent / Producer", rb_agency_interact_TEXTDOMAIN). "</a></li>\n";
								echo "            </ul>\n";
								echo "          </div> <!-- talent-register -->\n";
						}*/
				
				}
			echo "        </div> <!-- rbsign-up -->\n";
}
else{
	echo "        <div id=\"rbsign-up\" class=\"inline-block\">\n";
	echo "          <div id=\"talent-register\" class=\"register\">\n";
	 if ( dynamic_sidebar('rb-agency-interact-login-sidebar') ) :endif; 
	echo "          </div> <!-- talent-register -->\n";
	echo "          <div class=\"clear line\"></div>\n";
	echo "        </div> <!-- rbsign-up -->\n";

}

echo "      <div class=\"clear line\"></div>\n";
echo "      </div>\n";
?>