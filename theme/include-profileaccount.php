<?php
	global $user_ID; 
	global $current_user;
	get_currentuserinfo();
	$ProfileUserLinked = $current_user->id;

	// Get Settings
	$rb_agency_options_arr = get_option('rb_agency_options');
		$rb_agency_option_showsocial 			= $rb_agency_options_arr['rb_agency_option_showsocial'];
		$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
		$rb_agency_option_locationtimezone 		= (int)$rb_agency_options_arr['rb_agency_option_locationtimezone'];

	// Get Data
	$query = "SELECT * FROM " . table_agency_profile . " WHERE ProfileUserLinked='$ProfileUserLinked'";
	$results = mysql_query($query) or die ( __("Error, query failed", rb_agencyinteract_TEXTDOMAIN ));
	$count = mysql_num_rows($results);
	while ($data = mysql_fetch_array($results)) {
		$ProfileID					=$data['ProfileID'];
		$ProfileUserLinked			=$data['ProfileUserLinked'];
		$ProfileGallery				=stripslashes($data['ProfileGallery']);
		$ProfileContactDisplay		=stripslashes($data['ProfileContactDisplay']);
		$ProfileContactNameFirst	=stripslashes($data['ProfileContactNameFirst']);
		$ProfileContactNameLast		=stripslashes($data['ProfileContactNameLast']);
		$ProfileContactEmail		=stripslashes($data['ProfileContactEmail']);
		$ProfileContactWebsite		=stripslashes($data['ProfileContactWebsite']);
		$ProfileContactLinkFacebook	=stripslashes($data['ProfileContactLinkFacebook']);
		$ProfileContactLinkTwitter	=stripslashes($data['ProfileContactLinkTwitter']);
		$ProfileContactLinkYouTube	=stripslashes($data['ProfileContactLinkYouTube']);
		$ProfileContactLinkFlickr	=stripslashes($data['ProfileContactLinkFlickr']);
		$ProfileContactPhoneHome	=stripslashes($data['ProfileContactPhoneHome']);
		$ProfileContactPhoneCell	=stripslashes($data['ProfileContactPhoneCell']);
		$ProfileContactPhoneWork	=stripslashes($data['ProfileContactPhoneWork']);
		$ProfileContactParent		=stripslashes($data['ProfileContactParent']);
		$ProfileGender    			=stripslashes($data['ProfileGender']);
		$ProfileDateBirth	    	=stripslashes($data['ProfileDateBirth']);
		$ProfileLocationStreet		=stripslashes($data['ProfileLocationStreet']);
		$ProfileLocationCity		=stripslashes($data['ProfileLocationCity']);
		$ProfileLocationState		=stripslashes($data['ProfileLocationState']);
		$ProfileLocationZip			=stripslashes($data['ProfileLocationZip']);
		$ProfileLocationCountry		=stripslashes($data['ProfileLocationCountry']);
		$ProfileDateUpdated			=$data['ProfileDateUpdated'];

		echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/account/\">\n";
		
		echo " <table class=\"form-table\">\n";
		echo "  <tbody>\n";
		echo "    <tr colspan=\"2\">\n";
		echo "		<td scope=\"row\"><h3>". __("Contact Information", rb_agencyinteract_TEXTDOMAIN) ."</h3></th>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Gallery Folder", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
					if (!empty($ProfileGallery) && is_dir(rb_agency_UPLOADPATH .$ProfileGallery)) { 
						echo "<div id=\"message\"><span class=\"updated\"><a href=\"/profile/". $ProfileGallery ."/\" target=\"_blank\">/profile/". $ProfileGallery ."/</a></span></div>\n";
						echo "<input type=\"hidden\" id=\"ProfileGallery\" name=\"ProfileGallery\" value=\"". $ProfileGallery ."\" />\n";
					} else {
						echo "<input type=\"text\" id=\"ProfileGallery\" name=\"ProfileGallery\" value=\"". $ProfileGallery ."\" />\n";
						echo "<div id=\"message\"><span class=\"error\">". __("Folder Pending Creation", rb_agencyinteract_TEXTDOMAIN) ."</span>\n";
					}
		echo "             	</div>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("First Name", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileContactNameFirst\" name=\"ProfileContactNameFirst\" value=\"". $ProfileContactNameFirst ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Last Name", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileContactNameLast\" name=\"ProfileContactNameLast\" value=\"". $ProfileContactNameLast ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Gender", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td><select name=\"ProfileGender\" id=\"ProfileGender\">\n";
		echo "			<option value=\"\" ". selected($ProfileGender, "") .">". __("Not Specified", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";
		echo "			<option value=\"Male\" ". selected($ProfileGender, "Male") .">". __("Male", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";
		echo "			<option value=\"Female\" ". selected($ProfileGender, "Female") .">". __("Female", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";
		echo "		  </select>\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		// Private Information
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\" colspan=\"2\"><h3>". __("Private Information", rb_agencyinteract_TEXTDOMAIN) ."</h3>The following information will appear only in administrative areas.</th>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Parent (if minor)", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileContactParent\" name=\"ProfileContactParent\" value=\"". $ProfileContactParent ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Email Address", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileContactEmail\" name=\"ProfileContactEmail\" value=\"". $ProfileContactEmail ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Birthdate", rb_agencyinteract_TEXTDOMAIN) ." <em>YYYY-MM-DD</em></th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileDateBirth\" name=\"ProfileDateBirth\" value=\"". $ProfileDateBirth ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		// Address
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Street", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileLocationStreet\" name=\"ProfileLocationStreet\" value=\"". $ProfileLocationStreet ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("City", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileLocationCity\" name=\"ProfileLocationCity\" value=\"". $ProfileLocationCity ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("State", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileLocationState\" name=\"ProfileLocationState\" value=\"". $ProfileLocationState ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Zip", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileLocationZip\" name=\"ProfileLocationZip\" value=\"". $ProfileLocationZip ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Country", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileLocationCountry\" name=\"ProfileLocationCountry\" value=\"". $ProfileLocationCountry ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		// Custom Admin Fields
	
		$query1 = "SELECT ProfileCustomID, ProfileCustomTitle, ProfileCustomType, ProfileCustomOptions FROM ". table_agency_customfields ." WHERE ProfileCustomView = 1 ORDER BY ProfileCustomView, ProfileCustomTitle";
		$results1 = mysql_query($query1);
		$count1 = mysql_num_rows($results1);
		while ($data1 = mysql_fetch_array($results1)) {
		
		echo "  <tr valign=\"top\">\n";
		echo "    <td scope=\"row\">". $data1['ProfileCustomTitle'] ."</th>\n";
		echo "    <td>\n";
			  if ( !empty($ProfileID) && ($ProfileID > 0) ) {
	
				$subresult = mysql_query("SELECT ProfileCustomValue FROM ". table_agency_customfield_mux ." WHERE ProfileCustomID = ". $data1['ProfileCustomID'] ." AND ProfileID = ". $ProfileID);
				$subcount = mysql_num_rows($subresult);
				if ($subcount > 0) { 
				  while ($row = mysql_fetch_object($subresult)) {
					$ProfileCustomValue = $row->ProfileCustomValue;
				  }
				} else {
					$ProfileCustomValue = "";
				}
				mysql_free_result($subresult);
				
			  } /// End 
			  
			  
				$ProfileCustomType = $data1['ProfileCustomType'];
				if ($ProfileCustomType == 1) {
					$ProfileCustomOptions_Array = explode( "|", $data1['ProfileCustomOptions']);
					foreach ($ProfileCustomOptions_Array as &$value) {
					//echo "	<input type=\"checkbox\"  name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" value=\"". $value ."\" ". checked($ProfileCustomValue, $value) ." /> ". $value ."\n";
					} 
				} elseif ($ProfileCustomType == 2) {
					$ProfileCustomOptions_Array = explode( "|", $data1['ProfileCustomOptions']);
					foreach ($ProfileCustomOptions_Array as &$value) {
					//echo "	<input type=\"radio\"  name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" value=\"". $value ."\" ". checked($ProfileCustomValue, $value) ." /> ". $value ."\n";
					} 
				} elseif ($ProfileCustomType == 3) {
					$ProfileCustomOptions_Array = explode( "|", $data1['ProfileCustomOptions']);
					echo "<select name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">\n";
					foreach ($ProfileCustomOptions_Array as &$value) {
					echo "	<option value=\"". $value ."\" ". selected($ProfileCustomValue, $value) ."> ". $value ." </option>\n";
					} 
					echo "</select>\n";
				} else {
					echo "<input type=\"text\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" value=\"". $ProfileCustomValue ."\" /><br />\n";
				}
				
				// END Query2
		echo "    </td>\n";
		echo "  </tr>\n";
		}
		
		// Links	
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Phone", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			Home: <input type=\"text\" style=\"width: 100px;\" id=\"ProfileContactPhoneHome\" name=\"ProfileContactPhoneHome\" value=\"". $ProfileContactPhoneHome ."\" /><br />\n";
		echo "			Cell: <input type=\"text\" style=\"width: 100px;\" id=\"ProfileContactPhoneCell\" name=\"ProfileContactPhoneCell\" value=\"". $ProfileContactPhoneCell ."\" /><br />\n";
		echo "			Work: <input type=\"text\" style=\"width: 100px;\" id=\"ProfileContactPhoneWork\" name=\"ProfileContactPhoneWork\" value=\"". $ProfileContactPhoneWork ."\" /><br />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Website", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileContactWebsite\" name=\"ProfileContactWebsite\" value=\"". $ProfileContactWebsite ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		// Show Social Media Links
		if ($rb_agency_option_showsocial == "1") { 
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\" colspan=\"2\"><h3>". __("Social Media Profiles", rb_agencyinteract_TEXTDOMAIN) ."</h3></th>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Facebook", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileContactLinkFacebook\" name=\"ProfileContactLinkFacebook\" value=\"". $ProfileContactLinkFacebook ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Twitter", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileContactLinkTwitter\" name=\"ProfileContactLinkTwitter\" value=\"". $ProfileContactLinkTwitter ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("YouTube", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileContactLinkYouTube\" name=\"ProfileContactLinkYouTube\" value=\"". $ProfileContactLinkYouTube ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Flickr", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileContactLinkFlickr\" name=\"ProfileContactLinkFlickr\" value=\"". $ProfileContactLinkFlickr ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		} 
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Password (Leave blank to keep same password)", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"password\" id=\"ProfilePassword\" name=\"ProfilePassword\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Password (Retype to Confirm)", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"password\" id=\"ProfilePasswordConfirm\" name=\"ProfilePasswordConfirm\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "	</tbody>\n";
		echo " </table>\n";

		echo "". __("Last updated ", rb_agencyinteract_TEXTDOMAIN) ." ". rb_agency_makeago(rb_agency_convertdatetime($ProfileDateUpdated), $rb_agency_option_locationtimezone) ."\n";
		echo "<p class=\"submit\">\n";
		echo "     <input type=\"hidden\" name=\"ProfileID\" value=\"". $ProfileID ."\" />\n";
		echo "     <input type=\"hidden\" name=\"action\" value=\"editRecord\" />\n";
		echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_restaurant_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
		echo "</p>\n";
		echo "</form>\n";
	}
?>