<?php
global $wpdb,$current_user;
get_currentuserinfo();
		echo " <div class=\"profile-manage-menu\">\n";
		echo "   <div id=\"subMenuTab\">\n";
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/") {$tabclass = "active"; } else {$tabclass = "inactive"; }
		echo " 		<div class=\"tab-left tab-". $tabclass ."\">\n";
		echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/\">\n";
		echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("Overview", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/account/") {$tabclass = "active"; } else {$tabclass = "inactive"; }
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a  href=\"". get_bloginfo("wpurl") ."/profile-member/account/\">\n";
		echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("My Account", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";
		if(get_user_meta($current_user->ID, 'rb_agency_interact_clientdata', true)) {
		// Agents

					if ( ($_SERVER["REQUEST_URI"]) == "/profile-favorite/") {$tabclass = "active"; } else {$tabclass = "inactive"; }
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a href=\"\">\n";
		echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("Favorites", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";
		} else {
		//Models Talent
					if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/manage/") {$tabclass = "active"; } else {$tabclass = "inactive"; }
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a  href=\"". get_bloginfo("wpurl") ."/profile-member/manage/\">\n";
		echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("My Profile", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";

		/*
		 * Set Media to not show to
		 * client/s, agents, producers,
		 */
		$ptype = (int)get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);
			  $ptype = retrieve_title($ptype);
		$restrict = array('client','clients','agents','producers');
		if(in_array(strtolower($ptype),$restrict)){
			$d = 'display:none;';
		} else {
			$d = '';
		}

		if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/media/")
		 {
		 	$tabclass = "active"; 
		} else 
		{
			$tabclass = "inactive"; 
		}
		echo ' 		<div class="tab-inner tab-'. $tabclass .'" style="'.$d.'">';
		echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/media/\">\n";
		echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("My Media", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";
		}

		

		if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/accounts") {$tabclass = "active"; } else {$tabclass = "inactive"; }		
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a  href=\"". get_bloginfo("wpurl") ."/profile-member/accounts/"."\" >\n";
		echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("Other Accounts", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";


		if(is_plugin_active( 'rb-agency-casting/rb-agency-casting.php' )){
			if ( ($_SERVER["REQUEST_URI"]) == "/profile-member/auditions/") {$tabclass = "active"; } else {$tabclass = "inactive"; }
			echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
			echo " 			<a  href=\"". get_bloginfo("wpurl") ."/profile-member/auditions/\">\n";
			echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("Auditions", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
			echo " 			</a>\n";
			echo " 		</div>\n";
		}

		if ( ($_SERVER["REQUEST_URI"]) == "/profile/") {$tabclass = "active"; } else {$tabclass = "inactive"; }
		$profile_gallery = $wpdb->get_row($wpdb->prepare("SELECT ProfileGallery FROM ".table_agency_profile." WHERE ProfileContactEmail = '%s'",$current_user->user_email));
		if(!empty($profile_gallery->ProfileGallery)){
		echo " 		<div class=\"tab-inner tab-". $tabclass ."\">\n";
		echo " 			<a  href=\"". get_bloginfo("wpurl") ."/profile/".$profile_gallery->ProfileGallery."\">\n";
		echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("View My Profile", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
		echo " 			</a>\n";
		echo " 		</div>\n";
		}
                
		$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
			$rb_agencyinteract_option_subscribeupsell = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_subscribeupsell']) ?(int)$rb_agency_interact_options_arr['rb_agencyinteract_option_subscribeupsell']:0;

		if ($rb_agencyinteract_option_subscribeupsell) {
			// Is there a subscription?
			$sql = "SELECT SubscriberDateExpire FROM ". table_agencyinteract_subscription ." WHERE SubscriberDateExpire >= NOW() AND ProfileID =  ". $current_user->ID ." ORDER BY SubscriberDateExpire DESC LIMIT 1";
			$results = $wpdb->get_results($sql,ARRAY_A);
			$count = $wpdb->num_rows;
			if ($count > 0) {
				foreach($results as $data) {
				$SubscriberDateExpire = $data["SubscriberDateExpire"];
				echo " 		<div class=\"tab-right tab-". $tabclass ."\">\n";
				echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/subscription/\">\n";
				echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("My Subscription", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
				echo " 			</a>\n";
				echo " 		</div>\n";
				}// is there record?
			} else {
				$SubscriberDateExpire = NULL;
				echo " 		<div class=\"tab-right tab-". $tabclass ."\">\n";
				echo " 			<a href=\"". get_bloginfo("wpurl") ."/profile-member/subscription/\">\n";
				echo " 				<div class=\"subMenuTabBG\"><div class=\"subMenuTabBorders\"><div class=\"subMenuTabText\">".__("My Subscription", RBAGENCY_interact_TEXTDOMAIN) ."</div></div></div>\n";
				echo " 			</a>\n";
				echo " 		</div>\n";
			}
		}
		echo "   </div>\n";
		echo " </div>\n";
?>