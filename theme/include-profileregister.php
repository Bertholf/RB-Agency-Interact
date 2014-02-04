<?php
    // profile type
    $ptype = get_user_meta($current_user->id, "rb_agency_interact_profiletype", true);
    
    echo "<div class=\"rbform\">";
	//check if array
    if($ptype != ''){
		if(strpos($ptype, ",") > -1){
			$ptyp = explode(",",$ptype);
			foreach($ptyp as $p){
				$ptype_arr[] = str_replace(" ","_",retrieve_title($p));	 		
			}
			$ptype = array();
			$ptype = $ptype_arr;
		} else {
				$ptype = str_replace(" ","_",retrieve_title($ptype));
		}
    }     

    $ProfileGender = get_user_meta($current_user->id, "rb_agency_interact_pgender", true);
    echo '<input name="ProfileGender" type="hidden" value="'.$ProfileGender.'">'; 

    echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/account/\">\n";
	echo "<input type=\"hidden\" id=\"ProfileContactEmail\" name=\"ProfileContactEmail\" value=\"". $current_user->user_email ."\" />\n";
	echo "<input type=\"hidden\" id=\"ProfileUserLinked\" name=\"ProfileUserLinked\" value=\"". $current_user->id ."\" />\n";
    echo "<input type=\"hidden\" id=\"ProfileGender\" name=\"ProfileGender\" value=\"".$ProfileGender ."\" />\n";
	echo "<input type=\"hidden\" id=\"ProfileType\" name=\"ProfileType\" value=\"".get_user_meta($current_user->id, "rb_agency_interact_profiletype", true) ."\" />\n";
	
	echo "	<h3>". __("Contact Information", rb_agency_TEXTDOMAIN) ."</h3>\n";	
	echo "	<div id=\"profile-firstname\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("First Name", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileContactNameFirst\" name=\"ProfileContactNameFirst\" value=\"". $current_user->first_name ."\" /></div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-lastname\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Last Name", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileContactNameLast\" name=\"ProfileContactNameLast\" value=\"". $current_user->last_name ."\" /></div>\n";	
	echo "	</div>\n";
	echo "	<div id=\"profile-phone\" class=\"rbfield rbtext rbmulti rbblock\">\n";
	echo "		<label>". __("Phone", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div>\n";
	echo "			<div><label>Home:</label><div><input type=\"text\" id=\"ProfileContactPhoneHome\" name=\"ProfileContactPhoneHome\" value=\"". $ProfileContactPhoneHome ."\" /></div></div>\n";
	echo "			<div><label>Cell:</label><div><input type=\"text\" id=\"ProfileContactPhoneCell\" name=\"ProfileContactPhoneCell\" value=\"". $ProfileContactPhoneCell ."\" /></div></div>\n";
	echo "			<div><label>Work:</label><div><input type=\"text\" id=\"ProfileContactPhoneWork\" name=\"ProfileContactPhoneWork\" value=\"". $ProfileContactPhoneWork ."\" /></div></div>\n";
	echo "		</div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-website\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Website", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileContactWebsite\" name=\"ProfileContactWebsite\" value=\"". $ProfileContactWebsite ."\" /></div>\n";
	echo "	  </div>\n";

	// Public Information
	echo "	<h3>". __("Public Information", rb_agency_interact_TEXTDOMAIN) ."</h3>\n";
	echo "	<p>The following information may appear in profile pages.</p>\n";
	echo "	<div id=\"profile-birthdate\" class=\"rbfield rbselect rbmulti rbblock\">\n";
	echo "		<label>". __("Birthdate", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div>\n";
				  /* Month */ 
	echo "		<div>\n";				  
				  $monthName = array(1=> "January", "February", "March","April", "May", "June", "July", "August","September", "October", "November", "December"); 
	echo "		  <select name=\"ProfileDateBirth_Month\" id=\"ProfileDateBirth_Month\">\n";
	echo "			<option value=\"\"> -- Select Month -- </option>\n";
        for ($currentMonth = 1; $currentMonth <= 12; $currentMonth++ ) { 	
            echo "			<option value=\"". $currentMonth ."\">". $monthName[$currentMonth] ."</option>\n";
	}
	echo "		  </select>\n";
	echo "		</div>\n";	
				  /* Day */ 
	echo "		<div>\n";				  
	echo "		  <select name=\"ProfileDateBirth_Day\" id=\"ProfileDateBirth_Day\">\n";
	echo "			<option value=\"\"> -- Select Day -- </option>\n";
        for ($currentDay = 1; $currentDay <= 31; $currentDay++ ) { 	
            echo "			<option value=\"". $currentDay ."\">". $currentDay ."</option>\n";
        }
	echo "		  </select>\n";
	echo "		</div>\n";

				  /* Year */ 
	echo "		<div>\n";
	echo "		  <select name=\"ProfileDateBirth_Year\" id=\"ProfileDateBirth_Year\">\n";
	echo "			<option value=\"\"> -- Select Year -- </option>\n";
        for ($currentYear = 1940; $currentYear <= 2010; $currentYear++ ) { 	
            echo "			<option value=\"". $currentYear ."\">". $currentYear ."</option>\n";
	}
	echo "		  </select>\n";
	echo "		</div>\n";
	echo "		</div>\n";
	echo "	  </div>\n";

	// Private Information	
	echo "	<h3>". __("Private Information", rb_agency_interact_TEXTDOMAIN) ."</h3>";
	echo "	<p>". __("The following information will NOT appear in public areas and is for administrative use only.", rb_agency_interact_TEXTDOMAIN) ."</p>\n";

	echo "	<div id=\"profile-street\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Street", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileLocationStreet\" name=\"ProfileLocationStreet\" /></div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-city\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("City", rb_agency_interact_TEXTDOMAIN) ."</label>\n";	
	echo "		<div><input type=\"text\" id=\"ProfileLocationCity\" name=\"ProfileLocationCity\" /></div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-state\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("State", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileLocationState\" name=\"ProfileLocationState\" /></div>\n";
	echo "	  </div>\n";
	echo "	<div id=\"profile-zip\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Zip", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileLocationZip\" name=\"ProfileLocationZip\" /></div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-country\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Country", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileLocationCountry\" name=\"ProfileLocationCountry\" /></div>\n";
	echo "	</div>\n";

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

								// check ptype if array
								if(is_array($ptype)){
									$result = array_diff($ptype, $types);
									if(count($result) != count($ptype)){
										$permit_type = true;
									} 	
								} else {
									if(in_array($ptype,$types)){ $permit_type = true; }
								}
                                
				if ( ($data1["ProfileCustomShowGender"] == $ProfileGender) || ($data1["ProfileCustomShowGender"] == 0) 
                                      && $permit_type == true )  {

					include("view-custom-fields.php");

				}
			}
        

	
	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
	$rb_agency_interact_option_registerallow = (int)$rb_agency_interact_options_arr['rb_agency_interact_option_registerallow'];

	
	  if ($rb_agency_interact_option_registerallow  == 1) {
		echo "	<div id=\"profile-username\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Username", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div>\n";
		if(isset($current_user->user_login)){
		echo "			<input type=\"text\" id=\"ProfileUsername\"  disabled=\"disabled\" value=\"".$current_user->user_login."\" />\n";
		echo "          <input type=\"hidden\" name=\"ProfileUsername\" value=\"".$current_user->user_login."\"  />";
		} else {
		echo "			<input type=\"text\" id=\"ProfileUsername\"  name=\"ProfileUsername\" value=\"\" />\n";	
		}
		echo "			<small class=\"rbfield-note\">Cannot be changed</small>";
		echo "		</div>\n";
		echo "	  </div>\n";
	 }
	
	echo "	<div id=\"profile-password\" class=\"rbfield rbpassword rbsingle\">\n";
	echo "		<label>". __("Password", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div>\n";
	echo "			<input type=\"password\" id=\"ProfilePassword\" name=\"ProfilePassword\" />\n";
	echo "			<small class=\"rbfield-note\">Leave blank to keep same password</small>";
	echo "		</div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-password\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Password", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div>\n";
	echo "			<input type=\"password\" id=\"ProfilePasswordConfirm\" name=\"ProfilePasswordConfirm\" />\n";
	echo "			<small class=\"rbfield-note\">Retype to Confirm</small>";	
	echo "		</div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-submit\" class=\"rbfield rbsubmit rbsingle\">\n";
	echo "     <input type=\"hidden\" name=\"action\" value=\"addRecord\" />\n";
	echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_restaurant_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
	echo "	</div>\n";
	echo "</form>\n";
	echo "</div>\n";
?>
