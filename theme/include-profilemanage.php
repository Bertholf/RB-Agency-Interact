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

	
		echo "  </tbody>\n";
		echo "</table>\n";

	/*
	 *   added this new custom field display 
	 */
	$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
		
	$query3 = "SELECT * FROM ". table_agency_customfields ." 
			   WHERE ProfileCustomView = 0 AND ProfileCustomShowRegistration = 1 ORDER BY ProfileCustomOrder";

	$results3 = mysql_query($query3) or die(mysql_error());
	$count3 = mysql_num_rows($results3);
	
	while ($data3 = mysql_fetch_assoc($results3)) {
	 	
		if (($data3["ProfileCustomShowGender"] == $ProfileGender) || ($data3["ProfileCustomShowGender"] == 0) ) {
		
		$ProfileCustomTitle = $data3['ProfileCustomTitle'];
		$ProfileCustomType  = $data3['ProfileCustomType'];
	       
			 //  SET Label for Measurements
			 //  Imperial(in/lb), Metrics(ft/kg)
			 $rb_agency_options_arr = get_option('rb_agency_options');
			 $rb_agency_option_unittype  = $rb_agency_options_arr['rb_agency_option_unittype'];
			 $measurements_label = "";

			 if ($ProfileCustomType == 7) { //measurements field type

			            if($rb_agency_option_unittype ==0){ // 0 = Metrics(ft/kg)
						
								if($data3['ProfileCustomOptions'] == 1){
									 $measurements_label  ="<em>(cm)</em>";
								}elseif($data3['ProfileCustomOptions'] == 2){
									 $measurements_label  ="<em>(kg)</em>";
								}elseif($data3['ProfileCustomOptions'] == 3){
								  $measurements_label  ="<em>(In Inches/Feet)</em>";
								}
					    }elseif($rb_agency_option_unittype ==1){ //1 = Imperial(in/lb)
								if($data3['ProfileCustomOptions'] == 1){
									 $measurements_label  ="<em>(In Inches)</em>";
								}elseif($data3['ProfileCustomOptions'] == 2){
								  $measurements_label  ="<em>(In Pounds)</em>";
								}elseif($data3['ProfileCustomOptions'] == 3){
								  $measurements_label  ="<em>(In Inches/Feet)</em>";
								}
						}

					
			 }  
		 
		 	 
		 echo "<p class=\"form-".strtolower(trim($data3['ProfileCustomTitle']))." "
		       .gender_filter($data3['ProfileCustomShowGender'])."\">\n"; 
		 
		 echo "<label style='width:200px; float:left;' for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
		       . __( $data3['ProfileCustomTitle'].$measurements_label, rb_agencyinteract_TEXTDOMAIN) 
			   ."</label>\n";		  
		 
		 if ($ProfileCustomType == 1) { //TEXT
			
			echo '<input type="text" name="ProfileCustomID'. $data3['ProfileCustomID'] 
			     .'" value="'. retrieve_datavalue($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']],
				 									$data3['ProfileCustomID'],$ProfileID,"textbox") 
				 .'" /><br />';
			}
			
		elseif ($ProfileCustomType == 2) { // Min Max
			
				$ProfileCustomOptions_String = str_replace(",",":",
				                               strtok(strtok($data3['ProfileCustomOptions'],"}"),"{"));
				
				list($ProfileCustomOptions_Min_label,$ProfileCustomOptions_Min_value,
				$ProfileCustomOptions_Max_label,$ProfileCustomOptions_Max_value) 
				= explode(":", $ProfileCustomOptions_String);
			 
				if (!empty($ProfileCustomOptions_Min_value) && !empty($ProfileCustomOptions_Max_value)) {
						
					   echo "<br /><br /> <label style='width:200px; float:left;' for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">"
						     . __("Min", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
					   echo "<input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] 
						     ."\" value=\"". 
							 retrieve_datavalue($ProfileCustomOptions_Min_value,
				 								$data3['ProfileCustomID'],$ProfileID,"textbox")
							  ."\" />\n";
					   echo "<br /><br /><br /><label style='width:200px; float:left;' for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">"
						    . __("Max", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
					   echo "<input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\""
					        .  retrieve_datavalue($ProfileCustomOptions_Max_value,
				 								  $data3['ProfileCustomID'],$ProfileID,"textbox") ."\" /><br />\n";
				
				} else {
						echo "<br /><br />  <label style='width:200px; float:left;' for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">"
						     . __("Min", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\""
						     .retrieve_datavalue($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']],
				 									$data3['ProfileCustomID'],$ProfileID,"textbox") ."\" />\n";
						echo "<br /><br /><br /><label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">"
						     . __("Max", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\""
						     .retrieve_datavalue($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']],
				 									$data3['ProfileCustomID'],$ProfileID,"textbox") ."\" /><br />\n";
				}
			 
		} 
			
		elseif ($ProfileCustomType == 3) {  // Drop Down
		
				list($option1,$option2) = explode(":",$data3['ProfileCustomOptions']);	
		
				$data = explode("|",$option1);
				$data2 = explode("|",$option2);
		
				echo "<label style='width:200px; float:left;'>".$data[0]."</label>";
				
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
					echo "<label style='width:200px; float:left;'>".$data2[0]."</label>";
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
				
			} elseif ($ProfileCustomType == 4) {
				echo "<textarea style=\"width: 100%; min-height: 100px;\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\">"
				     . $_REQUEST["ProfileCustomID". $data3['ProfileCustomID']] ."</textarea>";

			} elseif ($ProfileCustomType == 5) {

				$array_customOptions_values = explode("|",$data3['ProfileCustomOptions']);
				echo "<div style=\"width:300px;float:left;\">";
				foreach($array_customOptions_values as $val){
					 $xplode = explode(",",$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]);
					 echo "<label><input type=\"checkbox\" value=\"". $val."\"   "; 
					 
					 if(in_array($val,$xplode)){ echo "checked=\"checked\""; } 
					 
					 echo" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\" />";
					 
					 echo "". $val."</label>";
				}    
				
				echo "<br/>";
				echo "</div>";
				   
			} elseif ($ProfileCustomType == 6) {
				
				$array_customOptions_values = explode("|",$data3['ProfileCustomOptions']);
				
				foreach($array_customOptions_values as $val){
					
					 echo "<input type=\"radio\" value=\"". $val."\" "; checked($val, $_REQUEST["ProfileCustomID"
					       . $data3['ProfileCustomID']]); 
					 echo" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\" />";
					 echo "<span>". $val."</span><br/>";
				}
				
			}elseif ($ProfileCustomType == 7) { //Imperial/Metrics
			

			    if($data3['ProfileCustomTitle']=="Height" AND $rb_agency_option_unittype==1){
			        
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
		   
		       }else{
			   
			 echo '<input type="text" name="ProfileCustomID'. $data3['ProfileCustomID'] 
			     .'" value="'. retrieve_datavalue($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']],
				 									$data3['ProfileCustomID'],$ProfileID, 'textbox') 
				 .'" /><br />';
			   }
			}
									
	    echo "</p>\n";
		   
		   } // end if
		   	
        }// End while


		echo "". __("Last updated ", rb_agencyinteract_TEXTDOMAIN) ." "
			   . rb_agency_makeago(rb_agency_convertdatetime($ProfileDateUpdated), $rb_agency_option_locationtimezone) ."\n";
	
		echo "<p class=\"submit\">\n";
		echo "     <input type=\"hidden\" name=\"action\" value=\"editRecord\" />\n";
		echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_restaurant_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
		echo "</p>\n";
		echo "</form>\n";
	}
?>