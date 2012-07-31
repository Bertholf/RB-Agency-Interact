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
	      $ProfileStatHeight			=stripslashes($data['ProfileStatHeight']);
		$ProfileStatWeight			=stripslashes($data['ProfileStatWeight']);
		$ProfileDateUpdated			=stripslashes($data['ProfileDateUpdated']);
		$ProfileType				=stripslashes($data['ProfileType']);

		echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/manage/\">\n";
		echo "     <input type=\"hidden\" name=\"ProfileID\" value=\"". $ProfileID ."\" />\n";
		echo " <table class=\"form-table\">\n";
		echo "  <tbody>\n";
		// Account Information	
		echo "    <tr valign=\"top\">\n";
		echo "		<td scope=\"row\">". __("Classification", rb_agencyinteract_TEXTDOMAIN) ."</th>\n";
		echo "		<td>\n";
			
					$ProfileTypeArray = explode(",", $ProfileType);
					
					$queryType = "SELECT * FROM ". table_agency_data_type ." ORDER BY DataTypeTitle";
					$resultsType = mysql_query($queryType);
					$countType = mysql_num_rows($resultsType);
					while ($dataType = mysql_fetch_array($resultsType)) {
						echo "<input type=\"checkbox\" name=\"ProfileType[]\" id=\"ProfileType\" value=\"". $dataType['DataTypeID'] ."\""; if ( in_array($dataType['DataTypeID'], $ProfileTypeArray)) { echo " checked=\"checked\""; } echo "> ". $dataType['DataTypeTitle'] ."<br />\n";
					}
	
		echo "		</td>\n";
		echo "	  </tr>\n";
            
		// Include Profile Customfields
		     $ProfileInformation = "0"; // Public fields only
			include("include-custom-fields.php");
								
						
		echo "  </tbody>\n";
		echo "</table>\n";
		echo "". __("Last updated ", rb_agencyinteract_TEXTDOMAIN) ." ". rb_agency_makeago(rb_agency_convertdatetime($ProfileDateUpdated), $rb_agency_option_locationtimezone) ."\n";
	
		echo "<p class=\"submit\">\n";
		
		echo "     <input type=\"hidden\" name=\"action\" value=\"editRecord\" />\n";
		echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_restaurant_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
		echo "</p>\n";
		echo "</form>\n";
	}
		

?>