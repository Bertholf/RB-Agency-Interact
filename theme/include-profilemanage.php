<?php
	global $user_ID; 
	global $current_user;
	get_currentuserinfo();
	$ProfileUserLinked = $current_user->ID;
	// Get Settings
	$rb_agency_options_arr = get_option('rb_agency_options');
		$rb_agency_option_unittype  			= isset($rb_agency_options_arr['rb_agency_option_unittype'])?$rb_agency_options_arr['rb_agency_option_unittype']:0;
		$rb_agency_option_locationtimezone 		= isset($rb_agency_options_arr['rb_agency_option_locationtimezone'])? (int)$rb_agency_options_arr['rb_agency_option_locationtimezone']:0;
	// Get Values
	$query = "SELECT * FROM " . table_agency_profile . " WHERE ProfileUserLinked='$ProfileUserLinked'";
	$results = mysql_query($query) or die ( __("Error, query failed", rb_agency_interact_TEXTDOMAIN ));
	$count = mysql_num_rows($results);
		/*
		 * Get profile type and Gender
		 */
		$ptype = (int)get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);
		$ptype = retrieve_title($ptype);
		$ProfileGender = get_user_meta($current_user->ID, "rb_agency_interact_pgender", true);
		$ProfileTypeArray = array();
		$profileType = ""; 
		$ptype1 = get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);
		$ProfileTypeArray = explode(",", $ptype1);
		$query3 = "SELECT * FROM " . table_agency_data_type . " ORDER BY DataTypeTitle";
		$results3 = mysql_query($query3);
		$count3 = mysql_num_rows($results3);
		$i=1; 
		while ($data3 = mysql_fetch_array($results3)) {

			if (in_array($data3['DataTypeID'], $ProfileTypeArray)){
				$profileType .=  $data3['DataTypeTitle'] ;

				if($i<$count3){
					$profileType .=  "&nbsp;,&nbsp;";
				}
			}
			$i++;
		}
				
	while ($data = mysql_fetch_array($results)) {
		$ProfileID					=$data['ProfileID'];
		$ProfileUserLinked			=$data['ProfileUserLinked'];
		$ProfileDateUpdated			=stripslashes($data['ProfileDateUpdated']);
		$ProfileType				=stripslashes($data['ProfileType']);
		echo "<div class=\"rbform\">";
		echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/manage/\">\n";
		echo "     <input type=\"hidden\" name=\"ProfileID\" value=\"". $ProfileID ."\" />\n";
		echo "     <input type=\"hidden\" name=\"ProfileType\" value=\"". $ptype1 ."\" />\n";

		echo "<div id=\"rb-field-classification\" class=\"rbfield rbtext rbsingle\">";
		echo "	<label for=\"classification\">". __("Classification:", rb_agency_interact_TEXTDOMAIN) ."</label>";
		echo "	<div>".$profileType ."</div>";
		echo "</div>";

	/*
	 *   added this new custom field display 
	 */
	$rb_agency_option_profilenaming 		= isset($rb_agency_options_arr['rb_agency_option_profilenaming']) ?(int)$rb_agency_options_arr['rb_agency_option_profilenaming']:0;
		
	$query3 = "SELECT * FROM ". table_agency_customfields ." 
			   WHERE ProfileCustomView = 0 AND ProfileCustomShowRegistration = 1 ORDER BY ProfileCustomOrder";

	$results3 = mysql_query($query3) or die(mysql_error());
	$count3 = mysql_num_rows($results3);
	
	while ($data3 = mysql_fetch_assoc($results3)) {
		/*
		 * Get Profile Types to
		 * filter models from clients
		 */
		$permit_type = false;

		$PID = $data3['ProfileCustomID'];

		$get_types = "SELECT ProfileCustomTypes FROM ". table_agency_customfields_types .
					 " WHERE ProfileCustomID = " . $PID;
		$result = mysql_query($get_types);
		$types = "";				
		while ( $p = mysql_fetch_array($result)){
		// 	$types = $p['ProfileCustomTypes'];		
		$types = str_replace("_", " ", $p['ProfileCustomTypes']);
		}
		
		if($types != "" || $types != NULL){
		   $types = explode(",",$types); 
		   if(in_array($ptype,$types)){ $permit_type=true; }
		} 
		
		echo'<input type="hidden" name="aps12" value="'.$data3["ProfileCustomShowGender"].'" >';
		
		if (($data3["ProfileCustomShowGender"] == $ProfileGender) || ($data3["ProfileCustomShowGender"] == 0) 
			&& $permit_type == true ) {

			$ProfileCustomTitle = $data3['ProfileCustomTitle'];
			$ProfileCustomType  = $data3['ProfileCustomType'];

			//  SET Label for Measurements
			//  Imperial(in/lb), Metrics(ft/kg)
			$rb_agency_options_arr = get_option('rb_agency_options');
			$rb_agency_option_unittype  = $rb_agency_options_arr['rb_agency_option_unittype'];
			$measurements_label = "";

			if ($ProfileCustomType == 7) { //measurements field type

				if($rb_agency_option_unittype ==0) { // 0 = Metrics(ft/kg)
					if($data3['ProfileCustomOptions'] == 1){
						$measurements_label  ="<em> (cm)</em>";
					}elseif($data3['ProfileCustomOptions'] == 2){
						$measurements_label  ="<em> (kg)</em>";
					}elseif($data3['ProfileCustomOptions'] == 3){
						$measurements_label  ="<em> (Inches/Feet)</em>";
					}
				} elseif($rb_agency_option_unittype ==1){ //1 = Imperial(in/lb)
					if($data3['ProfileCustomOptions'] == 1){
						$measurements_label  ="<em> (Inches)</em>";
					}elseif($data3['ProfileCustomOptions'] == 2){
						$measurements_label  ="<em> (Pounds)</em>";
					}elseif($data3['ProfileCustomOptions'] == 3){
						$measurements_label  ="<em> (Inches/Feet)</em>";
					}
				}
			}

		 if ($ProfileCustomType == 1) { //TEXT

			echo "<div id=\"rbfield-".strtolower(trim($data3['ProfileCustomTitle']))." ".gender_filter($data3['ProfileCustomShowGender'])."\" class=\"rbfield rbtext rbsingle\">";
			echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
			   . __( $data3['ProfileCustomTitle'].$measurements_label, rb_agency_interact_TEXTDOMAIN) 
			   ."</label>\n";
			echo '<div><input type="text" name="ProfileCustomID'. $data3['ProfileCustomID'] 
				 .'" value="'. retrieve_datavalue(isset($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']])?$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]:"",
													$data3['ProfileCustomID'],$ProfileID,"textbox") 
				 .'" /></div>';
			echo "</div>";
			}
			
		elseif ($ProfileCustomType == 2) { // Min Max

			echo "<div id=\"rbfield-".strtolower(trim($data3['ProfileCustomTitle']))." ".gender_filter($data3['ProfileCustomShowGender'])."\" class=\"rbfield rbtext rbmulti\">";
			echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
			   . __( $data3['ProfileCustomTitle'].$measurements_label, rb_agency_interact_TEXTDOMAIN) 
			   ."</label>\n";
			echo "<div>";
				$ProfileCustomOptions_String = str_replace(",",":",
											   strtok(strtok($data3['ProfileCustomOptions'],"}"),"{"));
				
				list($ProfileCustomOptions_Min_label,$ProfileCustomOptions_Min_value,
				$ProfileCustomOptions_Max_label,$ProfileCustomOptions_Max_value) 
				= explode(":", $ProfileCustomOptions_String);
			 
				if (!empty($ProfileCustomOptions_Min_value) && !empty($ProfileCustomOptions_Max_value)) {
						
					   echo "<div><label for=\"ProfileCustomLabel_min\">"
							 . __("Min", rb_agency_TEXTDOMAIN) . " </label>\n";
					   echo "<div><input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] 
							 ."\" value=\"". 
							 retrieve_datavalue($ProfileCustomOptions_Min_value,
												$data3['ProfileCustomID'],$ProfileID,"textbox")
							  ."\" /></div></div>\n";
					   echo "<div><label for=\"ProfileCustomLabel_min\">"
							. __("Max", rb_agency_TEXTDOMAIN) . " </label>\n";
					   echo "<div><input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\""
							.  retrieve_datavalue($ProfileCustomOptions_Max_value,
												  $data3['ProfileCustomID'],$ProfileID,"textbox") ."\" /></div></div>\n";
				
				} else {
						echo "<div><label for=\"ProfileCustomLabel_min\">"
							 . __("Min", rb_agency_TEXTDOMAIN) . " </label>\n";
						echo "<div><input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\""
							 .retrieve_datavalue($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']],
													$data3['ProfileCustomID'],$ProfileID,"textbox") ."\" /></div></div>\n";
						echo "<div><label for=\"ProfileCustomLabel_min\">"
							 . __("Max", rb_agency_TEXTDOMAIN) . " </label>\n";
						echo "<div><input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\""
							 .retrieve_datavalue($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']],
													$data3['ProfileCustomID'],$ProfileID,"textbox") ."\" /></div></div>\n";
				}
			echo "</div>";
			 
		} 
			
		elseif ($ProfileCustomType == 3) {  // Drop Down

			echo "<div id=\"rbfield-".strtolower(trim($data3['ProfileCustomTitle']))." ".gender_filter($data3['ProfileCustomShowGender'])."\" class=\"rbfield rbselect rbsingle\">";
			echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
			   . __( $data3['ProfileCustomTitle'].$measurements_label, rb_agency_interact_TEXTDOMAIN) 
			   ."</label>\n";
		          
				@list($option1,$option2) = @explode(":",$data3['ProfileCustomOptions']);	
		
				$data = explode("|",$option1);
				$data2 = explode("|",$option2);
				echo "<div>";
				echo "<select name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\">\n";
		
						echo "<option value=\"\">--</option>";
				
							$pos = 0;
							foreach($data as $val1){
								if(!empty($val1)){
												echo "<option value=\"".$val1."\" ".
											retrieve_datavalue("",$data3['ProfileCustomID'],$ProfileID,"dropdown",$val1)
												." >".$val1."</option>";
								}
							}
					
				echo "</select>\n";
				
				if (!empty($data2) && !empty($option2)) {

						$pos2 = 0;
						echo "<select name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\">\n";
						echo "<option value=\"\">--</option>";
						foreach($data2 as $val2){
								if($val2 != end($data2) && $val2 !=  $data2[0]){
									echo "<option value=\"".$val2."\" ". selected($val2, $_REQUEST["ProfileCustomID"
										 . $data3['ProfileCustomID']]) 
										 ." >".$val2."</option>";
								}
							}
						echo "</select>\n";
				}
				echo "</div>";
				echo "</div>";
				
			} elseif ($ProfileCustomType == 4) {
				echo "<div id=\"rbfield-".strtolower(trim($data3['ProfileCustomTitle']))." ".gender_filter($data3['ProfileCustomShowGender'])."\" class=\"rbfield rbtextarea rbsingle\">";
				echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
			   . __( $data3['ProfileCustomTitle'].$measurements_label, rb_agency_interact_TEXTDOMAIN) 
			   ."</label>\n";
				echo "<div><textarea name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\">"
					 . retrieve_datavalue(isset($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']])?$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]:"",
													$data3['ProfileCustomID'],$ProfileID,"textbox") ."</textarea></div>";
				echo "</div>";
			} elseif ($ProfileCustomType == 5) {
				echo "<div id=\"rbfield-".strtolower(trim($data3['ProfileCustomTitle']))." ".gender_filter($data3['ProfileCustomShowGender'])."\" class=\"rbfield rbcheckbox rbsingle\">";
				echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
			   . __( $data3['ProfileCustomTitle'].$measurements_label, rb_agency_interact_TEXTDOMAIN) 
			   ."</label>\n";
				$xplode =array(); 
				$myquery = "SELECT ProfileCustomValue FROM " . table_agency_customfield_mux . " WHERE ProfileID=".$ProfileID." and ProfileCustomID=".$data3['ProfileCustomID']." ";
				$myresults = mysql_query($myquery) or die ( __("Error, query failed", rb_agencyinteract_TEXTDOMAIN ));
				while ($mydata = mysql_fetch_array($myresults)) {
					$xplode = explode(",",$mydata['ProfileCustomValue']);
				}

				$array_customOptions_values = explode("|",$data3['ProfileCustomOptions']);
				echo "<div>";
				foreach($array_customOptions_values as $val){
					if(isset($val) && $val!=""){
					 echo "<div><label><input type=\"checkbox\" value=\"". $val."\"   "; 
					 
					 if(in_array($val,$xplode)){ echo "checked=\"checked\""; } 
					 
					 echo" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\" />";
					 
					 echo "<span> ". $val."</span></label></div>";
					 }
				}    
				
				echo "</div>";
				echo "</div>";
				   
			} elseif ($ProfileCustomType == 6) {
				
				echo "<fieldset id=\"rbfield-".strtolower(trim($data3['ProfileCustomTitle']))." ".gender_filter($data3['ProfileCustomShowGender'])."\" class=\"rbfield rbcheckbox rbsingle\">";
				echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
			   . __( $data3['ProfileCustomTitle'].$measurements_label, rb_agency_interact_TEXTDOMAIN) 
			   ."</label>\n";
			   echo "<div>";
				$array_customOptions_values = explode("|",$data3['ProfileCustomOptions']);
				
				foreach($array_customOptions_values as $val){
					
					$selected = "";
					 $check = "";
					 $selected = retrieve_datavalue("",$data3['ProfileCustomID'],$ProfileID,"dropdown",$val);
					 
					 if($selected == "selected"){
						$check = "checked";	
					 }
					
					 echo "<div><label><input type=\"radio\" value=\"". $val."\" " . $check .
						  " name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\" /> ";
					 echo "<span> ".$val."</span></label></div>";
				}
				echo "</div>";
				echo "</fieldset>";
				
			}elseif ($ProfileCustomType == 7) { //Imperial/Metrics
			
				echo "<div id=\"rbfield-".strtolower(trim($data3['ProfileCustomTitle']))." ".gender_filter($data3['ProfileCustomShowGender'])."\" class=\"rbfield rbselect rbsingle\">";
				echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
			   . __( $data3['ProfileCustomTitle'].$measurements_label, rb_agency_interact_TEXTDOMAIN) 
			   ."</label>\n";
				if($data3['ProfileCustomTitle']=="Height" AND $rb_agency_option_unittype==1){
					echo "<div>";
					echo "<select name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\">\n";
					echo "<option value=\"\">--</option>\n";
		
					$i=36;
						$heightraw = 0;
						$heightfeet = 0;
						$heightinch = 0;
						while($i<=90)  { 
							  $heightraw = $i;
							  $heightfeet = floor($heightraw/12);
							  $heightinch = $heightraw - floor($heightfeet*12);
								echo " <option value=\"". $i ."\" ".
								retrieve_datavalue("",$data3['ProfileCustomID'],$ProfileID,"dropdown",$i)  .">"
									 . $heightfeet ." ft ". $heightinch ." in</option>\n";
								$i++;
						}
						
				   echo " </select>\n";
				   echo "</div>";
		   
			   }	else {		       	
			   
			 echo '<div><input type="text" name="ProfileCustomID'. $data3['ProfileCustomID'] 
				 .'" value="'. retrieve_datavalue(isset($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']])?$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]:"",
													$data3['ProfileCustomID'],$ProfileID, 'textbox') 
				 .'" /></div>';
				 ;					
			   }
				echo "</div>";
			}
		   
		   } // end if
			
		}// End while

		echo " <div id=\"rbfield-last-update\" class=\"rbfield rbtext rbsingle\">";
		echo "		<label>". __("Last updated ", rb_agency_interact_TEXTDOMAIN)."</label>";
		echo "		<div>". rb_agency_makeago(rb_agency_convertdatetime($ProfileDateUpdated), $rb_agency_option_locationtimezone) ."</div>\n";
		echo "	</div>\n";
		echo "	<div id=\"rbfield-submit\" class=\"rbfield rbsubmit rbsingle\">";
		echo "		<input type=\"hidden\" name=\"action\" value=\"editRecord\" />\n";
		echo "		<input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_agency_interact_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
		echo "	</div>\n";
		echo "</form>\n";
		echo "</div>\n";
	}
?>