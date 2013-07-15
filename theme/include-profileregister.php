<?php
    // profile type
    $ptype = (int)get_user_meta($current_user->id, "rb_agency_interact_profiletype", true);
    $ptype = retrieve_title($ptype);
    $ProfileGender = get_user_meta($current_user->id, "rb_agency_interact_pgender", true);
    echo '<input name="ProfileGender" type="hidden" value="'.$ProfileGender.'">'; 

    echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/account/\" style=\"width: 400px;\">\n";
	echo "<input type=\"hidden\" id=\"ProfileContactEmail\" name=\"ProfileContactEmail\" value=\"". $current_user->user_email ."\" />\n";
	echo "<input type=\"hidden\" id=\"ProfileUserLinked\" name=\"ProfileUserLinked\" value=\"". $current_user->id ."\" />\n";
    echo "<input type=\"hidden\" id=\"ProfileGender\" name=\"ProfileGender\" value=\"".$ProfileGender ."\" />\n";
	echo "<input type=\"hidden\" id=\"ProfileType\" name=\"ProfileType\" value=\"".get_user_meta($current_user->id, "rb_agency_interact_profiletype", true) ."\" />\n";

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
	echo "			<label style=\"width: 50px;float:left;line-height: 24px;\">Home:</label> <input type=\"text\" style=\"width: 144px;\" id=\"ProfileContactPhoneHome\" name=\"ProfileContactPhoneHome\" value=\"". $ProfileContactPhoneHome ."\" /><br />\n";
	echo "			<label style=\"width: 50px;float:left;line-height: 24px;\">Cell:</label> <input type=\"text\" style=\"width: 144px;\" id=\"ProfileContactPhoneCell\" name=\"ProfileContactPhoneCell\" value=\"". $ProfileContactPhoneCell ."\" /><br />\n";
	echo "			<label style=\"width: 50px;float:left;line-height: 24px;\">Work:</label> <input type=\"text\" style=\"width: 144px;\" id=\"ProfileContactPhoneWork\" name=\"ProfileContactPhoneWork\" value=\"". $ProfileContactPhoneWork ."\" /><br />\n";
	echo "		</td>\n";
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\">". __("Website", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
	echo "		<td>\n";
	echo "			<input type=\"text\" id=\"ProfileContactWebsite\" name=\"ProfileContactWebsite\" value=\"". $ProfileContactWebsite ."\" />\n";
	echo "		</td>\n";
	echo "	  </tr>\n";
	// Public Information
	echo "    <tr valign=\"top\">\n";
	echo "		<td scope=\"row\" colspan=\"2\"><h3>". __("Public Information", rb_agencyinteract_TEXTDOMAIN) ."</h3>The following information may appear in profile pages.</th>\n";
	echo "	  </tr>\n";
	echo "    <tr valign=\"top\">\n";
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

	/*
	 * Get Private custom Fields Here
	 *
	 */
		    $ProfileInformation = "1"; // Private fields only

			$query1 = "SELECT ProfileCustomID, ProfileCustomTitle, ProfileCustomType, ProfileCustomOptions, ProfileCustomOrder, ProfileCustomView, ProfileCustomShowGender, ProfileCustomShowProfile, ProfileCustomShowSearch, 
			                  ProfileCustomShowLogged, ProfileCustomShowAdmin,ProfileCustomShowRegistration FROM "
			         . table_agency_customfields ." WHERE ProfileCustomView = ". $ProfileInformation ." ORDER BY ProfileCustomOrder ASC";
			
			$results1 = mysql_query($query1);
			$count1 = mysql_num_rows($results1);
			$pos = 0;
			while ($data1 = mysql_fetch_array($results1)) { 
                               /*
                                * Get Profile Types to
                                * filter models from clients
                                */
                                $permit_type = false;

                                $PID = $data1['ProfileCustomID'];

                                $get_types = "SELECT ProfileCustomTypes FROM ". table_agency_customfields_types .
                                            " WHERE ProfileCustomID = " . $PID;

                                $result = mysql_query($get_types);

                                while ( $p = mysql_fetch_array($result)){
                                        $types = $p['ProfileCustomTypes'];			    
                                }

                                $types = explode(",",$types); 

                                if(in_array($ptype,$types)){ $permit_type=true; }
                                
				if ( ($data1["ProfileCustomShowGender"] == $ProfileGender) || ($data1["ProfileCustomShowGender"] == 0) 
                                      && $permit_type == true )  {

					include("view-custom-fields.php");

				}
			}
        

	
	$rb_agencyinteract_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_registerallow = (int)$rb_agencyinteract_options_arr['rb_agencyinteract_option_registerallow'];

	
	  if ($rb_agencyinteract_option_registerallow  == 1) {
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Username(cannot be changed.)", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		if(isset($current_user->user_login)){
		echo "			<input type=\"text\" id=\"ProfileUsername\"  disabled=\"disabled\" value=\"".$current_user->user_login."\" />\n";
		echo "                  <input type=\"hidden\" name=\"ProfileUsername\" value=\"".$current_user->user_login."\"  />";
		}else{
		echo "			<input type=\"text\" id=\"ProfileUsername\"  name=\"ProfileUsername\" value=\"\" />\n";	
		}
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
	echo "  </tbody>\n";
	echo "</table>\n";
	echo "<p class=\"submit\">\n";
	echo "     <input type=\"hidden\" name=\"action\" value=\"addRecord\" />\n";
	echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_restaurant_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
	echo "</p>\n";
	echo "</form>\n";
?>