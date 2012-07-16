<?php
	global $user_ID; 
	global $current_user;
	get_currentuserinfo();
	$ProfileUserLinked = $current_user->id;

	// Get Settings
	$rb_agency_options_arr = get_option('rb_agency_options');
		$rb_agency_option_unittype  			= $rb_agency_options_arr['rb_agency_option_unittype'];
		$rb_agency_option_locationtimezone 		= (int)$rb_agency_options_arr['rb_agency_option_locationtimezone'];

	// Get Values
	$query = "SELECT * FROM " . table_agency_profile . " WHERE ProfileUserLinked='$ProfileUserLinked'";
	$results = mysql_query($query) or die ( __("Error, query failed", rb_agencyinteract_TEXTDOMAIN ));
	$count = mysql_num_rows($results);
	while ($data = mysql_fetch_array($results)) {
		$ProfileID					=$data['ProfileID'];
		$ProfileUserLinked			=$data['ProfileUserLinked'];
		$ProfileGender    			=stripslashes($data['ProfileGender']);
		$ProfileStatEthnicity		=stripslashes($data['ProfileStatEthnicity']);
		$ProfileStatSkinColor		=stripslashes($data['ProfileStatSkinColor']);
		$ProfileStatEyeColor		=stripslashes($data['ProfileStatEyeColor']);
		$ProfileStatHairColor		=stripslashes($data['ProfileStatHairColor']);
		$ProfileStatHeight			=stripslashes($data['ProfileStatHeight']);
		$ProfileStatWeight			=stripslashes($data['ProfileStatWeight']);
		$ProfileStatBust	        =stripslashes($data['ProfileStatBust']);
		$ProfileStatWaist	    	=stripslashes($data['ProfileStatWaist']);
		$ProfileStatHip	        	=stripslashes($data['ProfileStatHip']);
		$ProfileStatShoe		    =stripslashes($data['ProfileStatShoe']);
		$ProfileStatDress			=stripslashes($data['ProfileStatDress']);
		$ProfileUnion				=stripslashes($data['ProfileUnion']);
		$ProfileExperience			=stripslashes($data['ProfileExperience']);
		$ProfileDateUpdated			=stripslashes($data['ProfileDateUpdated']);
		$ProfileType				=stripslashes($data['ProfileType']);

		echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/manage/\">\n";
		
		echo " <table class=\"form-table\">\n";
		echo "  <tbody>\n";
		// Account Information	
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\" colspan=\"2\"><h3>". __("Classification", rb_agencyinteract_TEXTDOMAIN) ."</h3></th>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Classification", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
			
					$ProfileTypeArray = explode(",", $ProfileType);
		
					$query3 = "SELECT * FROM ". table_agency_data_type ." ORDER BY DataTypeTitle";
					$results3 = mysql_query($query3);
					$count3 = mysql_num_rows($results3);
					while ($data3 = mysql_fetch_array($results3)) {
						echo "<input type=\"checkbox\" name=\"ProfileType[]\" id=\"ProfileType[]\" value=\"". $data3['DataTypeID'] ."\""; if ( in_array($data3['DataTypeID'], $ProfileTypeArray)) { echo " checked=\"checked\""; } echo "> ". $data3['DataTypeTitle'] ."<br />\n";
					}
	
		echo "		</td>\n";
		echo "	  </tr>\n";
	
				  // Metric or Imperial?
				  if ($rb_agency_option_unittype == 1) {
		echo "    <tr valign=\"top\">\n";
		echo "        <td scope=\"row\">". __("Height", rb_agencyinteract_TEXTDOMAIN) ." <em>(". __("In Inches", rb_agencyinteract_TEXTDOMAIN) .")</em></th>\n";
		echo "        <td><select name=\"ProfileStatHeight\" id=\"ProfileStatHeight\">\n";
						if (empty($ProfileStatHeight)) {
		echo " 				<option value=\"\" selected>--</option>\n";
						}
						
						$i=36;
						$heightraw = 0;
						$heightfeet = 0;
						$heightinch = 0;
						while($i<=90)  { 
						  $heightraw = $i;
						  $heightfeet = floor($heightraw/12);
						  $heightinch = $heightraw - floor($heightfeet*12);
		echo " 				<option value=\"". $i ."\" ". selected($ProfileStatHeight, $i) .">". $heightfeet ." ft ". $heightinch ." in</option>\n";
						  $i++;
						}
		echo " 			</select>\n";
		echo "        </td>\n";
		echo "    </tr>\n";
				  } else {
		echo "    <tr valign=\"top\">\n";
		echo "        <td scope=\"row\">". __("Height", rb_agencyinteract_TEXTDOMAIN) ." <em>(". __("cm", rb_agencyinteract_TEXTDOMAIN) .")</em></th>\n";
		echo "        <td>\n";
		echo "			<input type=\"text\" id=\"ProfileStatHeight\" name=\"ProfileStatHeight\" value=\"". $ProfileStatHeight ."\" />\n";
		echo "        </td>\n";
		echo "    </tr>\n";
				  }
		echo "    <tr valign=\"top\">\n";
		echo "        <td scope=\"row\">". __("Weight", rb_agencyinteract_TEXTDOMAIN) ." \n";
					  if ($rb_agency_option_unittype == 1) { echo "<em>(". __("In Pounds", rb_agencyinteract_TEXTDOMAIN) .")</em>"; } else { echo "<em>(". __("In Kilo", rb_agencyinteract_TEXTDOMAIN) .")</em></th>\n"; }
		echo "        </th>\n";
		echo "        <td>\n";
		echo "			<input type=\"text\" id=\"ProfileStatWeight\" name=\"ProfileStatWeight\" value=\"". $ProfileStatWeight ."\" />\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "        <td scope=\"row\">". __("Measurements", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "        <td>\n";
						if ($ProfileGender == "Male") { _e("Chest", rb_agencyinteract_TEXTDOMAIN); } elseif ($ProfileGender == "Female"){ _e("Bust", rb_agencyinteract_TEXTDOMAIN); } else { echo "". __("Bust", rb_agencyinteract_TEXTDOMAIN) ."/". __("Bust", rb_agencyinteract_TEXTDOMAIN); } 
							echo "<input type=\"text\" style=\"width: 80px;\" id=\"ProfileStatBust\" name=\"ProfileStatBust\" value=\"". $ProfileStatBust ."\" /><br />\n";
						echo "". __("Waist", rb_agencyinteract_TEXTDOMAIN) .": \n";
							echo "<input type=\"text\" style=\"width: 80px;\" id=\"ProfileStatWaist\" name=\"ProfileStatWaist\" value=\"". $ProfileStatWaist ."\" /><br />\n";
						if ($ProfileGender == "Male") { _e("Inseam", rb_agencyinteract_TEXTDOMAIN); } elseif ($ProfileGender == "Female"){ _e("Hips", rb_agencyinteract_TEXTDOMAIN); } else { echo "". __("Hips", rb_agencyinteract_TEXTDOMAIN) ."/". __("Inseam", rb_agencyinteract_TEXTDOMAIN); } 
							echo "<input type=\"text\" style=\"width: 80px;\" id=\"ProfileStatHip\" name=\"ProfileStatHip\" value=\"". $ProfileStatHip ."\" /><br />\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Shoe Size", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileStatShoe\" name=\"ProfileStatShoe\" value=\"". $ProfileStatShoe ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">";
						if($ProfileGender == "Male"){ echo __("Suit Size", rb_agencyinteract_TEXTDOMAIN); } elseif ($ProfileGender == "Female"){ echo __("Dress Size", rb_agencyinteract_TEXTDOMAIN); } else { echo __("Suit", rb_agencyinteract_TEXTDOMAIN) ."/". __("Dress Size", rb_agencyinteract_TEXTDOMAIN); } 
		echo "      </th>\n";
		echo "		<td>\n";
		echo "			<input type=\"text\" id=\"ProfileStatDress\" name=\"ProfileStatDress\" value=\"". $ProfileStatDress ."\" />\n";
		echo "		</td>\n";
		echo "	  </tr>\n";
	
		$query1 = "SELECT ProfileCustomID, ProfileCustomTitle, ProfileCustomType, ProfileCustomOptions,  ProfileCustomOrder, ProfileCustomView,  ProfileCustomShowGender	, ProfileCustomShowProfile, ProfileCustomShowSearch, ProfileCustomShowLogged, ProfileCustomShowAdmin FROM ". table_agency_customfields ." WHERE ProfileCustomView IN (0,2) AND ProfileCustomType < 7 ORDER BY ProfileCustomOrder desc";
		$results1 = mysql_query($query1);
		$count1 = mysql_num_rows($results1);
		while ($data1 = mysql_fetch_array($results1)) {
		
		echo "  <tr valign=\"top\">\n";
		echo "    <td scope=\"row\">". $data1['ProfileCustomTitle'] ."</th>\n";
		echo "    <td>\n";
			  if ( !empty($ProfileID) && ($ProfileID > 0) ) {

				$subresult = mysql_query("SELECT ProfileCustomValue FROM ". table_agency_customfield_mux ." WHERE ProfileCustomID = ". $data1['ProfileCustomID'] ." AND ProfileID = ". $ProfileID." ");
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
			  
			  	//Custom fields display options
	$rb_agency_option_customfields_profilepage = $rb_agency_options_arr['rb_agency_option_customfield_profilepage'];
	$rb_agency_option_customfields_searchpage = $rb_agency_options_arr['rb_agency_option_customfield_searchpage'];
	$rb_agency_option_customfields_loggedin_all = $rb_agency_options_arr['rb_agency_option_customfield_loggedin_all'];
	$rb_agency_option_customfields_loggedin_admin = $rb_agency_options_arr['rb_agency_option_customfield_loggedin_admin'];
	  
	  $ProfileCustomID  = $data1['ProfileCustomID'];
	  $ProfileCustomType = $data1['ProfileCustomType'];
			  
			   if($rb_agency_option_customfields_searchpage == 1 || $rb_agency_option_customfield_profilepage == 1 ){ // Show on Search Page
		    
						 if(($rb_agency_option_customfields_loggedin_all ==1 && is_user_logged_in()))
						 {
							 // Show custom fields for admins only.
							if($rb_agency_option_customfields_loggedin_admin == 1 && current_user_can("level_10") && is_user_logged_in()){ 
								include("view-customfields-profilemanager.php");
								//echo "1";
							}
							// Show custom fields for logged in users.
							if($rb_agency_option_customfields_loggedin_admin == 0 && !current_user_can("level_10")){
								include("view-customfields-profilemanager.php");
							 // echo "2";
							}
							
						 }
						 
						 // Show custom fields to all user level.
						 if(($rb_agency_option_customfields_loggedin_all == 0 && !is_user_logged_in())){
							
							include("view-customfields-profilemanager.php");
							// echo "3";
						}
						if((!current_user_can("level_10") && $rb_agency_option_customfields_loggedin_admin ==0  && $rb_agency_option_customfields_loggedin_all == 0)){
								include("view-customfields-profilemanager.php");
							// echo "4";
							
						}
			
				 }
					              
					
	
			
				// END Query2
		echo "    </td>\n";
		echo "  </tr>\n";
		}

		echo "  </tbody>\n";
		echo "</table>\n";
		echo "". __("Last updated ", rb_agencyinteract_TEXTDOMAIN) ." ". rb_agency_makeago(rb_agency_convertdatetime($ProfileDateUpdated), $rb_agency_option_locationtimezone) ."\n";
	
		echo "<p class=\"submit\">\n";
		echo "     <input type=\"hidden\" name=\"ProfileID\" value=\"". $ProfileID ."\" />\n";
		echo "     <input type=\"hidden\" name=\"action\" value=\"editRecord\" />\n";
		echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_restaurant_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
		echo "</p>\n";
		echo "</form>\n";
	}
		

?>