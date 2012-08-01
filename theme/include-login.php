<?php
	/* Load registration file. */
	require_once( ABSPATH . WPINC . '/registration.php' );
	
	/* Check if users can register. */
	$registration = get_option( 'rb_agencyinteract_options' );
	$rb_agencyinteract_option_registerallow = $registration["rb_agencyinteract_option_registerallow"];
	// Facebook Login Integration
	$rb_agencyinteract_option_fb_registerallow = $registration['rb_agencyinteract_option_fb_registerallow'];
	$rb_agencyinteract_option_fb_app_id = $registration['rb_agencyinteract_option_fb_app_id'];
	$rb_agencyinteract_option_fb_app_secret = $registration['rb_agencyinteract_option_fb_app_secret'];
	$rb_agencyinteract_option_fb_app_uri = $registration['rb_agencyinteract_option_fb_app_uri'];
   
	if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow )) {
		$widthClass = "half";
	} else {
		$widthClass = "full";
	}

echo "     <div id=\"profile-interact\">\n";

			if ( $error ) {
			echo "<p class=\"error\">". $error ."</p>\n";
			echo $_POST['user-name']."-".$_POST['password'];
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
		if($rb_agencyinteract_option_fb_registerallow == 1){
				echo " <div class=\"fb-login-button\" scope=\"email\" data-show-faces=\"false\" data-width=\"200\" data-max-rows=\"1\"></div>";
						echo "  <div id=\"fb-root\"></div>
						
							<script>
							window.fbAsyncInit = function() {
							    FB.init({
								appId      : '".$rb_agencyinteract_option_fb_app_id."',  ";
						  if(empty($rb_agencyinteract_option_fb_app_uri)){  // set default
							   echo "\n channelUrl : '".network_site_url("/")."profile-member/', \n";
						   }else{
							  echo "channelUrl : '".$rb_agencyinteract_option_fb_app_uri."',\n"; 
						   }
						 echo "	status     : true, // check login status
								cookie     : true, // enable cookies to allow the server to access the session
								xfbml      : true  // parse XFBML
							    });
							  };
					  		// Load the SDK Asynchronously
							(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = '//connect.facebook.net/en_US/all.js#xfbml=1&appId=".$rb_agencyinteract_option_fb_app_id."'
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));</script>";
		}
echo "            </div>\n";
echo "          <form>\n";
echo "        </div> <!-- member-sign-in -->\n";

			if (( current_user_can("create_users") || $rb_agencyinteract_option_registerallow == 1)) {

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