<?php
	/* Load registration file. */
	require_once( ABSPATH . WPINC . '/registration.php' );
	
	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );
	if (( current_user_can("create_users") || $registration ) && !$hideregister) {
		$widthClass = "half";
	} else {
		$widthClass = "full";
	}

echo "     <div id=\"profile-interact\">\n";

			if ( $error ) {
			echo "<p class=\"error\">". $error ."</p>\n";
			}

echo "        <div id=\"member-sign-in\" class=\"fl ". $widthClass ."\">\n";
echo "          <h1>". __("Members Sign in", rb_agencyinteract_TEXTDOMAIN). "</h1>\n";
echo "          <form name=\"loginform\" id=\"login\" action=\"". get_bloginfo("wpurl") ."/profile-login/\" method=\"post\">\n";
echo "            <div class=\"box\">\n";
echo "              <label for=\"user-name\">". __("Username", rb_agencyinteract_TEXTDOMAIN). "</label><input type=\"text\" name=\"user-name\" value=\"". wp_specialchars( $_POST['user-name'], 1 ) ."\" id=\"user-name\" />\n";
echo "            </div>\n";
echo "            <div class=\"box\">\n";
echo "              <label for=\"password\">". __("Password", rb_agencyinteract_TEXTDOMAIN). "</label><input type=\"password\" name=\"password\" value=\"\" id=\"password\" /> <a href=\"". get_bloginfo('wpurl') ."/wp-login.php?action=lostpassword\">". __("forgot password", rb_agencyinteract_TEXTDOMAIN). "?</a>\n";
echo "            </div>\n";
echo "            <div class=\"box\">\n";
echo "              <input type=\"checkbox\" name=\"remember-me\" value=\"forever\" /> ". __("Keep me signed in", rb_agencyinteract_TEXTDOMAIN). "\n";
echo "            </div>\n";
echo "            <div class=\"submit-box\">\n";
echo "              <input type=\"hidden\" name=\"action\" value=\"log-in\" />\n";
echo "              <input type=\"submit\" value=\"". __("Sign In", rb_agencyinteract_TEXTDOMAIN). "\" /><br />\n";
echo "            </div>\n";
echo "          <form>\n";
echo "        </div> <!-- member-sign-in -->\n";

			if (( current_user_can("create_users") || $registration ) && !$hideregister) {

echo "        <div id=\"not-a-member\" class=\"fr\">\n";
echo "          <div id=\"talent-register\">\n";
echo "            <h1>". __("Not a member", rb_agencyinteract_TEXTDOMAIN). "?</h1>\n";
echo "            <h2>". __("Talent", rb_agencyinteract_TEXTDOMAIN). " - ". __("Register here", rb_agencyinteract_TEXTDOMAIN). "</h2>\n";
echo "            <ul>\n";
echo "              <li>". __("Create your free profile page", rb_agencyinteract_TEXTDOMAIN). "</li>\n";
echo "              <li>". __("Apply to Auditions & Jobs", rb_agencyinteract_TEXTDOMAIN). "</li>\n";
echo "            </ul>\n";
echo "            <a href=\"". get_bloginfo("wpurl") ."/profile-register/\" id=\"register-talent\">". __("Register as Talent / Model", rb_agencyinteract_TEXTDOMAIN). "</a>\n";
echo "          </div> <!-- talent-register -->\n";
echo "          <div class=\"clear line\"></div>\n";
echo "          <div id=\"agent-register\" >\n";
echo "            <h2>". __("Casting Agents & Producers", rb_agencyinteract_TEXTDOMAIN). "</h2>\n";
echo "            <ul>\n";
echo "              <li>". __("List Auditions & Jobs free", rb_agencyinteract_TEXTDOMAIN). "</li>\n";
echo "              <li>". __("Contact People in the Talent Directory", rb_agencyinteract_TEXTDOMAIN). "</li>\n";
echo "            </ul>\n";
echo "            <a href=\"". get_bloginfo("wpurl") ."/profile-register/\" id=\"register-agent\">". __("Register as Agent / Producer", rb_agencyinteract_TEXTDOMAIN). "</a>\n";
echo "          </div> <!-- talent-register -->\n";
echo "        </div> <!-- not-a-member -->\n";
			}
			
echo "      <div class=\"clear line\"></div>\n";
echo "      </div>\n";
?>