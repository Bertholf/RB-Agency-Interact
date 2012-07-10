<?php

		echo " <div class=\"profile-manage-menu\">\n";
		echo "   <div id=\"subMenuTab\">\n";

		// Agents Menu Options
		if ($profiletype == 1) {

		// Welcome 
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/") { $tabclass = "active"; } else { $tabclass = "inactive"; }
		echo " 		<div class=\"tab-left tab-". $tabclass ."\">\n";
		echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">Welcome</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";

		// Account Manage
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/account/") { $tabclass = "active"; } else { $tabclass = "inactive"; }
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a  href=\"". get_bloginfo("wpurl") ."/profile-member/account/\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">Account & Contact Information</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";

		// Classification Manage Link
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/manage/") { $tabclass = "active"; } else { $tabclass = "inactive"; }
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a  href=\"". get_bloginfo("wpurl") ."/profile-member/manage/\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">Classification & Details</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";

		// Search 
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-category/") { $tabclass = "active"; } else { $tabclass = "inactive"; }
		echo " 		<div class=\"tab-left tab-". $tabclass ."\">\n";
		echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-category/\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">Search Talents</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";



		// View Favorites
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/favorites/") { $tabclass = "active"; } else { $tabclass = "inactive"; }
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a  href=\"". get_bloginfo("wpurl") ."/profile-favorites/\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">View Marked Favorites</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";



		// Log OUt
		echo " 		<div class=\"tab-right\">\n";
		echo " 			<a title=\"Logout\" href=\"". wp_logout_url('index.php') ."\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">Log Out</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";


		// END Agency Menu Options
		

		//Models Talent Menu Options
		} else {



		// Account Overview
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/") { $tabclass = "active"; } else { $tabclass = "inactive"; }
		echo " 		<div class=\"tab-left tab-". $tabclass ."\">\n";
		echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">Overview</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";


		// Profile Manage Link
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/manage/") { $tabclass = "active"; } else { $tabclass = "inactive"; }
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a  href=\"". get_bloginfo("wpurl") ."/profile-member/manage/\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">My Profile</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";


		// Account Manage
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/account/") { $tabclass = "active"; } else { $tabclass = "inactive"; }
		echo " 		<div class=\"tab-left tab-". $tabclass ."\">\n";
		echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/account/\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">Account</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";




		// Media Link
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/media/") { $tabclass = "active"; } else { $tabclass = "inactive"; }
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/media/\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">My Media</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";
		
					
		
		
		
		// Subscription Link
		if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/subscription/") { $tabclass = "active"; } else { $tabclass = "inactive"; }

		$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
			$rb_agencyinteract_option_subscribeupsell = (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_subscribeupsell'];

		if ($rb_agencyinteract_option_subscribeupsell) {
			// Is there a subscription?
			$sql = "SELECT SubscriberDateExpire FROM ". table_agencyinteract_subscription ." WHERE SubscriberDateExpire >= NOW() AND ProfileID =  ". $current_user->ID ." ORDER BY SubscriberDateExpire DESC LIMIT 1";
			$results = mysql_query($sql);
			$count = mysql_num_rows($results);
			if ($count > 0) {
			  while ($data = mysql_fetch_array($results)) {
				$SubscriberDateExpire = $data["SubscriberDateExpire"];
				echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
				echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/subscription/\">\n";
				echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">Available Subscriptions</div></div></div>\n";
				echo " 			</a>\n";
				echo " 		</div>\n";
			  } // is there record?
			} else {
				$SubscriberDateExpire = NULL;
				echo " 		<div class=\"tab-right tab-". $tabclass ."\">\n";
				echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/subscription/\">\n";
				echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">My Subscriptions</div></div></div>\n";
				echo " 			</a>\n";
				echo " 		</div>\n";
			}
		}

		
		// VIEW PROFILE LINK
		echo " 		<div class=\"tab-inner\">\n";
			
			global $wpdb;
			global $current_user;
			get_currentuserinfo();
			if (is_user_logged_in()) {
    			/* Check if the user is regsitered *****************************************/
    			$sql = "SELECT ProfileGallery FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
    			$results = mysql_query($sql);
    			$count = mysql_num_rows($results);
    			if ($count > 0) {
      			while ($data = mysql_fetch_array($results)) {
        			echo "<a href=\"". rb_agency_PROFILEDIR . $data["ProfileGallery"] ."/\" title=\"View Your Profile\" target=\"_blank\">View Your Online Profile</a>\n";
      			} // is there record?
    			} else {
        			echo "<a href=\"/profile-member/\">Setup your profile</a>\n";
    			}
			} else {
    			echo "You are not logged in.  <a href=\"/profile-login/\">Log In</a>\n";
			}
		
		echo " 		</div>\n";
		
		
		// LOG OUT LINK
		echo " 		<div class=\"tab-right\">\n";
		echo " 			<a title=\"Logout\" href=\"". wp_logout_url('index.php') ."\">\n";
		echo " 			  <div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">Log Out</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";
		
		
		} // End Talent Menu Options
		
		
		
		
		
		
		
		echo "   </div>\n";
		echo " </div>\n";
?>