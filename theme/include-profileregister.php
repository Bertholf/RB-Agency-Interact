<?php
	/* Get Options */
	$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
		$rb_agencyinteract_option_registerconfirm = (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_registerconfirm'];

	echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/account/\" style=\"width: 400px;\">\n";
	echo "<input type=\"hidden\" id=\"ProfileContactEmail\" name=\"ProfileContactEmail\" value=\"". $current_user->user_email ."\" />\n";
	echo "<input type=\"hidden\" id=\"ProfileUserLinked\" name=\"ProfileUserLinked\" value=\"". $current_user->id ."\" />\n";
	
	echo " <table class=\"form-table\">\n";
	echo "  <tbody>\n";
	echo "    <tr>\n";
	echo "		<td scope=\"row\" colspan=\"2\"><h3>". __("Contact Information", rb_agency_TEXTDOMAIN) ."</h3></th>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("First Name", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileContactNameFirst\" name=\"ProfileContactNameFirst\" value=\"". $current_user->first_name ."\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("Last Name", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileContactNameLast\" name=\"ProfileContactNameLast\" value=\"". $current_user->last_name ."\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("Phone", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileContactEmail\" name=\"ProfileContactPhoneHome\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	// Public Information
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\" colspan=\"2\"><h3>". __("Public Information", rb_agencyinteract_TEXTDOMAIN) ."</h3>The following information may appear in profile pages.</th>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("Gender", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td><select name=\"ProfileGender\" id=\"ProfileGender\">\n";
	echo "			<option value=\"\">". __("Not Specified", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";
	echo "			<option value=\"Male\">". __("Male", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";
	echo "			<option value=\"Female\">". __("Female", rb_agencyinteract_TEXTDOMAIN) ."</option>\n";
	echo "		  </select>\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("Birthdate", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
				  /* Month */ 
				  $monthName = array(1=> "January", "February", "March","April", "May", "June", "July", "August","September", "October", "November", "December"); 
	echo "		  <select name=\"ProfileDateBirth_Month\" id=\"ProfileDateBirth_Month\">\n";
					for ($currentMonth = 1; $currentMonth <= 12; $currentMonth++ ) { 	
	echo "			<option value=\"". $currentMonth ."\">". $monthName[$currentMonth] ."</option>\n";
					}
	echo "		  </select>\n";

				  /* Day */ 
	echo "		  <select name=\"ProfileDateBirth_Day\" id=\"ProfileDateBirth_Day\">\n";
					for ($currentDay = 1; $currentDay <= 31; $currentDay++ ) { 	
	echo "			<option value=\"". $currentDay ."\">". $currentDay ."</option>\n";
					}
	echo "		  </select>\n";

				  /* Year */ 
	echo "		  <select name=\"ProfileDateBirth_Year\" id=\"ProfileDateBirth_Year\">\n";
					for ($currentYear = 1940; $currentYear <= 2010; $currentYear++ ) { 	
	echo "			<option value=\"". $currentYear ."\">". $currentYear ."</option>\n";
					}
	echo "		  </select>\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	// Private Information
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\" colspan=\"2\"><h3>". __("Private Information", rb_agencyinteract_TEXTDOMAIN) ."</h3>". __("The following information will NOT appear in public areas and is for administrative use only.", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("Parent (if minor)", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileContactParent\" name=\"ProfileContactParent\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("Street", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileLocationStreet\" name=\"ProfileLocationStreet\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("City", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileLocationCity\" name=\"ProfileLocationCity\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("State", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileLocationState\" name=\"ProfileLocationState\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("Zip", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileLocationZip\" name=\"ProfileLocationZip\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("Country", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileLocationCountry\" name=\"ProfileLocationCountry\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	// Address
	// Custom Admin Fields

	$query1 = "SELECT ProfileCustomID, ProfileCustomTitle, ProfileCustomType, ProfileCustomOptions FROM ". table_agency_customfields ." WHERE ProfileCustomView = 1 ORDER BY ProfileCustomView, ProfileCustomTitle";
	$results1 = mysql_query($query1);
	$count1 = mysql_num_rows($results1);
	while ($data1 = mysql_fetch_array($results1)) {
	
	echo "  <tr valign=\"top\">\n";
	echo "    <td scope=\"row\">". $data1['ProfileCustomTitle'] ."</th>\n";
	echo "    <td>\n";
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
				echo "	<option value=\"". $value ."\"> ". $value ." </option>\n";
				} 
				echo "</select>\n";
			} else {
				echo "<input type=\"text\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" /><br />\n";
			}
	}
	// END Query
	echo "    </td>\n";
	echo "  </tr>\n";
	if ($rb_agencyinteract_option_registerconfirm == 0) {
	echo "  <tr valign=\"top\">\n";
	echo "    <td scope=\"row\">". __("Password (Leave blank to keep same password)", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "    <td>\n";
	echo "			<input type=\"password\" id=\"ProfilePassword\" name=\"ProfilePassword\" />\n";
	echo "    </td>\n";
	echo "  </tr>\n";
	echo "  <tr valign=\"top\">\n";
	echo "    <td scope=\"row\">". __("Password (Retype to Confirm)", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "    <td>\n";
	echo "			<input type=\"password\" id=\"ProfilePasswordConfirm\" name=\"ProfilePasswordConfirm\" />\n";
	echo "    </td>\n";
	echo "  </tr>\n";
	}
	echo "  </tbody>\n";
	echo "</table>\n";
	echo "<p class=\"submit\">\n";
	echo "     <input type=\"hidden\" name=\"action\" value=\"addRecord\" />\n";
	echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_restaurant_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
	echo "</p>\n";
	echo "</form>\n";
?>