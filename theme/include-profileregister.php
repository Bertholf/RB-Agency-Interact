<?php
global $wpdb;
	// profile type
	$ptype = get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);

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

	$ProfileGender = get_user_meta($current_user->ID, "rb_agency_interact_pgender", true);
	echo '<input name="ProfileGender" type="hidden" value="'.$ProfileGender.'">'; 

	echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/account/\">\n";
	echo "<input type=\"hidden\" id=\"ProfileContactEmail\" name=\"ProfileContactEmail\" value=\"". $current_user->user_email ."\" />\n";
	echo "<input type=\"hidden\" id=\"ProfileUserLinked\" name=\"ProfileUserLinked\" value=\"". $current_user->ID ."\" />\n";
	echo "<input type=\"hidden\" id=\"ProfileGender\" name=\"ProfileGender\" value=\"".$ProfileGender ."\" />\n";
	echo "<input type=\"hidden\" id=\"ProfileType\" name=\"ProfileType\" value=\"".get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true) ."\" />\n";

	echo "	<h3>". __("Contact Information", RBAGENCY_TEXTDOMAIN) ."</h3>\n";
	echo "	<div id=\"profile-firstname\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("First Name", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileContactNameFirst\" name=\"ProfileContactNameFirst\" value=\"". $current_user->first_name ."\" /></div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-lastname\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Last Name", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileContactNameLast\" name=\"ProfileContactNameLast\" value=\"". $current_user->last_name ."\" /></div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-phone\" class=\"rbfield rbtext rbmulti rbblock\">\n";
	echo "		<label>". __("Phone", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div>\n";
	echo "			<div><label>Home:</label><div><input type=\"text\" id=\"ProfileContactPhoneHome\" name=\"ProfileContactPhoneHome\" value=\"". (isset($ProfileContactPhoneHome)?$ProfileContactPhoneHome:"") ."\" /></div></div>\n";
	echo "			<div><label>Cell:</label><div><input type=\"text\" id=\"ProfileContactPhoneCell\" name=\"ProfileContactPhoneCell\" value=\"". (isset($ProfileContactPhoneCell)?$ProfileContactPhoneCell:"") ."\" /></div></div>\n";
	echo "			<div><label>Work:</label><div><input type=\"text\" id=\"ProfileContactPhoneWork\" name=\"ProfileContactPhoneWork\" value=\"". (isset($ProfileContactPhoneWork)?$ProfileContactPhoneWork:"") ."\" /></div></div>\n";
	echo "		</div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-website\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Website", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileContactWebsite\" name=\"ProfileContactWebsite\" value=\"". (isset($ProfileContactWebsite)? $ProfileContactWebsite:"") ."\" /></div>\n";
	echo "		</div>\n";

	// Public Information
	echo "	<h3>". __("Public Information", RBAGENCY_interact_TEXTDOMAIN) ."</h3>\n";
	echo "	<p>The following information may appear in profile pages.</p>\n";

	/*
	 * Get Public custom Fields Here
	 *
	 */
			$ProfileInformation = "0"; // Private fields only

			$query1 = "SELECT ProfileCustomID, ProfileCustomTitle, ProfileCustomType, ProfileCustomOptions, ProfileCustomOrder, ProfileCustomView, ProfileCustomShowGender, ProfileCustomShowProfile, ProfileCustomShowSearch, 
								ProfileCustomShowLogged, ProfileCustomShowAdmin,ProfileCustomShowRegistration FROM "
					. table_agency_customfields ." WHERE ProfileCustomView = ". $ProfileInformation ." ORDER BY ProfileCustomOrder ASC";

			$results1 = $wpdb->get_results($query1,ARRAY_A);
			$count1 =  $wpdb->num_rows;
			$pos = 0;
			foreach($results1 as $data1) {
								/*
								 * Get Profile Types to
								 * filter models from clients
								 */
								$permit_type = false;

								$PID = $data1['ProfileCustomID'];

								$get_types = "SELECT ProfileCustomTypes FROM ". table_agency_customfields_types .
											" WHERE ProfileCustomID = " . $PID;

								$result =  $wpdb->get_results($get_types,ARRAY_A);

								foreach ( $result as $p ){
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
									if(in_array($ptype,$types)){$permit_type = true; }
								}

				if ( ($data1["ProfileCustomShowGender"] == $ProfileGender) || ($data1["ProfileCustomShowGender"] == 0)   && $permit_type == true )  {

					include("view-custom-fields.php");

				}
			}


	// Private Information
	echo "	<h3>". __("Private Information", RBAGENCY_interact_TEXTDOMAIN) ."</h3>";
	echo "	<p>". __("The following information will NOT appear in public areas and is for administrative use only.", RBAGENCY_interact_TEXTDOMAIN) ."</p>\n";
	echo "	<div id=\"profile-birthdate\" class=\"rbfield rbselect rbmulti rbblock\">\n";
		echo "		<label>". __("Birthdate", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div>\n";
							/* Month */ 
			echo "		<div>\n";
							$monthName = array(1=> "January", "February", "March","April", "May", "June", "July", "August","September", "October", "November", "December"); 
			echo "			<select name=\"ProfileDateBirth_Month\" id=\"ProfileDateBirth_Month\">\n";
			echo "			<option value=\"\"> -- Select Month -- </option>\n";
				for ($currentMonth = 1; $currentMonth <= 12; $currentMonth++ ) {
					echo "			<option value=\"". $currentMonth ."\">". $monthName[$currentMonth] ."</option>\n";
			}
			echo "			</select>\n";
			echo "		</div>\n";
							/* Day */ 
			echo "		<div>\n";
			echo "			<select name=\"ProfileDateBirth_Day\" id=\"ProfileDateBirth_Day\">\n";
			echo "			<option value=\"\"> -- Select Day -- </option>\n";
				for ($currentDay = 1; $currentDay <= 31; $currentDay++ ) {
					echo "			<option value=\"". $currentDay ."\">". $currentDay ."</option>\n";
				}
			echo "			</select>\n";
			echo "		</div>\n";

							/* Year */ 
			echo "		<div>\n";
			echo "			<select name=\"ProfileDateBirth_Year\" id=\"ProfileDateBirth_Year\">\n";
			echo "			<option value=\"\"> -- Select Year -- </option>\n";
				for ($currentYear = 1940; $currentYear <= date("Y")+6; $currentYear++ ) {
					echo "			<option value=\"". $currentYear ."\">". $currentYear ."</option>\n";
			}
			echo "			</select>\n";
			echo "		</div>\n";
		echo "		</div>\n";
	echo "		</div>\n";
	echo "	<div id=\"profile-country\" class=\"rbfield rbselect rbsingle\">\n";
	echo "      <label>" . __("Country", RBAGENCY_TEXTDOMAIN) . "</label>\n";
	echo "      <div>\n";

	$query_get ="SELECT * FROM `". table_agency_data_country ."` ORDER BY CountryTitle ASC" ;
	$result_query_get = $wpdb->get_results($query_get);
	$location= site_url();

	echo '<input type="hidden" id="url" value="'.$location.'">';
	echo "<select name=\"ProfileLocationCountry\" id=\"ProfileLocationCountry\"  onchange='javascript:populateStates(\"ProfileLocationCountry\",\"ProfileLocationState\");'>";
	echo '<option value="">'. __("Select country", RBAGENCY_TEXTDOMAIN) .'</option>';
	foreach($result_query_get as $r){
			$selected =$ProfileLocationCountry==$r->CountryID?"selected=selected":"";
		echo '<option '.$selected.' value='.$r->CountryID.' >'.$r->CountryTitle.'</option>';
	}
	echo '</select>';
	echo "      </div>\n";
	echo "    </div>\n";


	echo "	<div id=\"profile-state\" class=\"rbfield rbtext rbsingle\">\n";
	echo "      <label>" . __("State", RBAGENCY_TEXTDOMAIN) . "</label>\n";
	echo "      <div>\n";
	$query_get ="SELECT * FROM `".table_agency_data_state."` ORDER BY StateTitle ASC" ;
	$result_query_get = $wpdb->get_results($query_get);
	echo '<select name="ProfileLocationState" id="ProfileLocationState">';
	echo '<option value="">'. __("Select state", RBAGENCY_TEXTDOMAIN) .'</option>';
	foreach($result_query_get as $r){
		$selected =$ProfileLocationState==$r->StateID?"selected=selected":"";
		echo '<option '.$selected.' value='.$r->StateID.' >'.$r->StateTitle.'</option>';
	}
	echo '</select>';

	echo "      </div>\n";
	echo "    </div>\n";

	echo "	<div id=\"profile-street\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Street", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileLocationStreet\" name=\"ProfileLocationStreet\" /></div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-city\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("City", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileLocationCity\" name=\"ProfileLocationCity\" /></div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-zip\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Zip", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div><input type=\"text\" id=\"ProfileLocationZip\" name=\"ProfileLocationZip\" /></div>\n";
	echo "	</div>\n";

	/*
	 * Get Private custom Fields Here
	 *
	 */
			$ProfileInformation = "1"; // Private fields only

			$query1 = "SELECT ProfileCustomID, ProfileCustomTitle, ProfileCustomType, ProfileCustomOptions, ProfileCustomOrder, ProfileCustomView, ProfileCustomShowGender, ProfileCustomShowProfile, ProfileCustomShowSearch, 
								ProfileCustomShowLogged, ProfileCustomShowAdmin,ProfileCustomShowRegistration FROM "
					. table_agency_customfields ." WHERE ProfileCustomView = ". $ProfileInformation ." ORDER BY ProfileCustomOrder ASC";

			$results1 = $wpdb->get_results($query1,ARRAY_A);
			$count1 =  $wpdb->num_rows;
			$pos = 0;
			foreach($results1 as $data1) {
								/*
								 * Get Profile Types to
								 * filter models from clients
								 */
								$permit_type = false;

								$PID = $data1['ProfileCustomID'];

								$get_types = "SELECT ProfileCustomTypes FROM ". table_agency_customfields_types .
											" WHERE ProfileCustomID = " . $PID;

								$result =  $wpdb->get_results($get_types,ARRAY_A);

								foreach ( $result as $p ){
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
									if(in_array($ptype,$types)){$permit_type = true; }
								}

				if ( ($data1["ProfileCustomShowGender"] == $ProfileGender) || ($data1["ProfileCustomShowGender"] == 0)   && $permit_type == true )  {

					include("view-custom-fields.php");

				}
			}



	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_registerallow = (int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerallow'];

	if ($rb_agencyinteract_option_registerallow  == 1) {
	echo "	<div id=\"profile-username\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Username", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div>\n";
	if(isset($current_user->user_login)){
	echo "			<input type=\"text\" id=\"ProfileUsername\"  disabled=\"disabled\" value=\"".$current_user->user_login."\" />\n";
	echo "          <input type=\"hidden\" name=\"ProfileUsername\" value=\"".$current_user->user_login."\"  />";
	} else {
	echo "			<input type=\"text\" id=\"ProfileUsername\"  name=\"ProfileUsername\" value=\"\" />\n";
	}
	echo "			<small class=\"rbfield-note\">Cannot be changed</small>";
	echo "		</div>\n";
	echo "	</div>\n";
	}

	echo "	<div id=\"profile-password\" class=\"rbfield rbpassword rbsingle\">\n";
	echo "		<label>". __("Password", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div>\n";
	echo "			<input type=\"password\" id=\"ProfilePassword\" name=\"ProfilePassword\" />\n";
	echo "			<small class=\"rbfield-note\">Leave blank to keep same password</small>";
	echo "		</div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-password\" class=\"rbfield rbtext rbsingle\">\n";
	echo "		<label>". __("Password", RBAGENCY_interact_TEXTDOMAIN) ."</label>\n";
	echo "		<div>\n";
	echo "			<input type=\"password\" id=\"ProfilePasswordConfirm\" name=\"ProfilePasswordConfirm\" />\n";
	echo "			<small class=\"rbfield-note\">Retype to Confirm</small>";
	echo "		</div>\n";
	echo "	</div>\n";
	echo "	<div id=\"profile-submit\" class=\"rbfield rbsubmit rbsingle\">\n";
	echo "     <input type=\"hidden\" name=\"action\" value=\"addRecord\" />\n";
	echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", RBAGENCY_interact_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
	echo "	</div>\n";

	echo "</form>\n";

?>
