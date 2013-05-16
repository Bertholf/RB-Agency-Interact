<?php
			$ProfileCustomTitle = $data1['ProfileCustomTitle'];
			$ProfileCustomType = $data1['ProfileCustomType'];
			$qProfileCustomValue = mysql_query("SELECT * FROM ".table_agency_customfield_mux." WHERE ProfileID = '".$ProfileID."' AND ProfileCustomID = '".$data1['ProfileCustomID']."'") or die(mysql_error());
			$fProfileCustomValue = mysql_fetch_assoc($qProfileCustomValue);
			$ProfileCustomValue = $fProfileCustomValue["ProfileCustomValue"];
			 // SET Label for Measurements
			 // Imperial(in/lb), Metrics(ft/kg)
			$rb_agency_options_arr = get_option('rb_agency_options');
			  $rb_agency_option_unittype  = $rb_agency_options_arr['rb_agency_option_unittype'];
			  $measurements_label = "";
			 if ($ProfileCustomType == 7) { //measurements field type
			           if($rb_agency_option_unittype ==0){ // 0 = Metrics(ft/kg)
						if($data1['ProfileCustomOptions'] == 1){
						     $measurements_label  ="<em>(cm)</em>";
						}elseif($data1['ProfileCustomOptions'] == 2){
						     $measurements_label  ="<em>(kg)</em>";
						}elseif($data1['ProfileCustomOptions'] == 3){
						  $measurements_label  ="<em>(In Inches/Feet)</em>";
						}
					}elseif($rb_agency_option_unittype ==1){ //1 = Imperial(in/lb)
						if($data1['ProfileCustomOptions'] == 1){
						     $measurements_label  ="<em>(In Inches)</em>";
						}elseif($data1['ProfileCustomOptions'] == 2){
						  $measurements_label  ="<em>(In Pounds)</em>";
						}elseif($data1['ProfileCustomOptions'] == 3){
						  $measurements_label  ="<em>(In Inches/Feet)</em>";
						}
					}
			 }
			if ($ProfileCustomType == 1) { //TEXT
			 echo "    <tr valign=\"top\">\n";
			 echo "		<td scope=\"row\">";
			 echo "				        <label for=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">".__($data1['ProfileCustomTitle'].$measurements_label, rb_agency_TEXTDOMAIN)."</label>\n";
				echo "		</td>\n";
				echo "	<td>";

						echo "<input type=\"text\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" value=\"". $ProfileCustomValue ."\" /><br />\n";
				echo "		</td>\n";
				;
			} elseif ($ProfileCustomType == 2) { // Min Max
			 echo "    <tr valign=\"top\">\n";
			 echo "		<td scope=\"row\">";
			 echo "				        <label for=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">".__($data1['ProfileCustomTitle'].$measurements_label, rb_agency_TEXTDOMAIN)."</label>\n";
			 echo "		</td>\n";
			 echo "	<td>";

				$ProfileCustomOptions_String = str_replace(",",":",strtok(strtok($data1['ProfileCustomOptions'],"}"),"{"));
				list($ProfileCustomOptions_Min_label,$ProfileCustomOptions_Min_value,$ProfileCustomOptions_Max_label,$ProfileCustomOptions_Max_value) = explode(":",$ProfileCustomOptions_String);

				if(!empty($ProfileCustomOptions_Min_value) && !empty($ProfileCustomOptions_Max_value)){
					    echo "<br /><br /> <label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Min", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" value=\"". $ProfileCustomOptions_Min_value ."\" />\n";
						echo "<br /><br /><br /><label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Max", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" value=\"". $ProfileCustomOptions_Max_value ."\" /><br />\n";
					
				}else{
					    echo "<br /><br />  <label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Min", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" value=\"".$_SESSION["ProfileCustomID". $data1['ProfileCustomID']]."\" />\n";
					    echo "<br /><br /><br /><label for=\"ProfileCustomLabel_min\" style=\"text-align:right;\">". __("Max", rb_agency_TEXTDOMAIN) . "&nbsp;&nbsp;</label>\n";
						echo "<input type=\"text\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" value=\"".$_SESSION["ProfileCustomID". $data1['ProfileCustomID']]."\" /><br />\n";

				}
				echo "		</td>\n";
			    echo "	</tr>";
			} elseif ($ProfileCustomType == 3) {
			 echo "    <tr valign=\"top\">\n";
			 echo "		<td scope=\"row\">";
			 echo "				        <label for=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">".__($data1['ProfileCustomTitle'].$measurements_label, rb_agency_TEXTDOMAIN)."</label>\n";
			 echo "		</td>\n";
			 echo "	<td>";

			  list($option1,$option2) = explode(":",$data1['ProfileCustomOptions']);	
					$data = explode("|",$option1);
					$data2 = explode("|",$option2);
		            echo "<label>".$data[0]."</label>";
					echo "<select name=\"ProfileCustomID". $data1['ProfileCustomID'] ."[]\">\n";
					echo "<option value=\"\">--</option>";
					    $pos = 0;
						foreach($data as $val1){
							if($val1 != end($data) && $val1 != $data[0]){
								 if($val1 == $ProfileCustomValue ){
										$isSelected = "selected=\"selected\"";
										echo "<option value=\"".$val1."\" ".$isSelected .">".$val1."</option>";
								 }else{
											echo "<option value=\"".$val1."\" >".$val1."</option>";
								 }
							}
						}
					  	echo "</select>\n";
					if(!empty($data2) && !empty($option2)){
						echo "<label>".$data2[0]."</label>";
					 		$pos2 = 0;
							echo "<select name=\"ProfileCustomID". $data1['ProfileCustomID'] ."[]\">\n";
							echo "<option value=\"\">--</option>";
							foreach($data2 as $val2){
									if($val2 != end($data2) && $val2 !=  $data2[0]){
										 if($val2 == $ProfileCustomValue ){
											$isSelected = "selected=\"selected\"";
											echo "<option value=\"".$val2."\" ".$isSelected .">".$val2."</option>";
										 }else{
											echo "<option value=\"".$val2."\" >".$val2."</option>";
										 }
									}
								}
							echo "</select>\n";
					
					}
						 echo "		</td>\n";
						  echo "	</tr>";   
				
				
			} 
			elseif ($ProfileCustomType == 4) 
			{

				if(is_admin()){
					echo "    <tr valign=\"top\">\n";
					 echo "		<td scope=\"row\">";
					 echo "				        <label for=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">".__($data1['ProfileCustomTitle'].$measurements_label, rb_agency_TEXTDOMAIN)."</label>\n";
					 echo "		</td>\n";
					 echo "	<td>";

					echo "<textarea style=\"width: 100%; min-height: 300px;\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">". $ProfileCustomValue ."</textarea>";
					echo "		</td>\n";
					echo "	</tr>";
				}
			}
			 elseif ($ProfileCustomType == 5)
			  {
				  echo "    <tr valign=\"top\">\n";
				 echo "		<td scope=\"row\">";
				 echo "				        <label for=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">".__($data1['ProfileCustomTitle'].$measurements_label, rb_agency_TEXTDOMAIN)."</label>\n";
				 echo "		</td>\n";
				 echo "	<td>";

				   $array_customOptions_values = explode("|",$data1['ProfileCustomOptions']);
				          echo "<div style=\"width:300px;float:left;\">";
						  foreach($array_customOptions_values as $val){
							     if(in_array($val,explode(",",$ProfileCustomValue))){
								 echo "<label><input type=\"checkbox\" checked=\"checked\" value=\"". $val."\"  name=\"ProfileCustomID". $data1['ProfileCustomID'] ."[]\" />";
								 echo "". $val."</label>";
							     }else{
								  echo "<label><input type=\"checkbox\" value=\"". $val."\"  name=\"ProfileCustomID". $data1['ProfileCustomID'] ."[]\" />";
								 echo "". $val."</label>";
							     }
						  }
						    echo "<input type=\"hidden\" value=\"\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."[]\"/>";
						  echo "</div>";
						  echo "		</td>\n";
						  echo "	</tr>";
			       
			}
			elseif ($ProfileCustomType == 6) {
					 echo "    <tr valign=\"top\">\n";
					 echo "		<td scope=\"row\">";
					 echo "				        <label for=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">".__($data1['ProfileCustomTitle'].$measurements_label, rb_agency_TEXTDOMAIN)."</label>\n";
					 echo "		</td>\n";
					 echo "	<td>";

				  $array_customOptions_values = explode("|",$data1['ProfileCustomOptions']);
				   echo "<div style=\"width:300px;float:left;\">";
						  foreach($array_customOptions_values as $val){
							     if(in_array($val,explode(",",$ProfileCustomValue))){
								 echo "<label><input type=\"radio\" checked=\"checked\" value=\"". $val."\"  name=\"ProfileCustomID". $data1['ProfileCustomID'] ."[]\" />";
								 echo "". $val."</label>";
							     }else{
								 echo "<label><input type=\"radio\" value=\"". $val."\"  name=\"ProfileCustomID". $data1['ProfileCustomID'] ."[]\" />";
								 echo "". $val."</label>";
							     }
						  }
						  echo "<input type=\"hidden\" value=\"\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."[]\"/>";
						  echo "</div>";
						  echo "		</td>\n";
						  echo "	</tr>";
		
			       
			}
			
			elseif ($ProfileCustomType == 7) { // Imperial(in/lb), Metrics(ft/kg)
				/*   if($data1['ProfileCustomOptions']==1){
							    if($rb_agency_option_unittype == 1){
									echo "<select name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">\n";
										if (empty($ProfileCustomValue)) {
									echo " 				<option value=\"\">--</option>\n";
										}
										
										$i=36;
										$heightraw = 0;
										$heightfeet = 0;
										$heightinch = 0;
										while($i<=90)  { 
										  $heightraw = $i;
										  $heightfeet = floor($heightraw/12);
										  $heightinch = $heightraw - floor($heightfeet*12);
									echo " <option value=\"". $i ."\" ". selected($ProfileCustomValue, $i) .">". $heightfeet ." ft ". $heightinch ." in</option>\n";
										  $i++;
										}
									echo " </select>\n";
							    }else{
								    echo "	 <input type=\"text\" id=\"ProfileStatHeight\" name=\"ProfileStatHeight\" value=\"". $ProfileCustomValue ."\" />\n";
							    }
				   }else{
*/
					   echo "    <tr valign=\"top\">\n";
						 echo "		<td scope=\"row\">";
						 echo "				        <label for=\"ProfileCustomID". $data1['ProfileCustomID'] ."\">".__($data1['ProfileCustomTitle'].$measurements_label, rb_agency_TEXTDOMAIN)."</label>\n";
						 echo "		</td>\n";
						 echo "	<td>";

					  echo "<input type=\"text\" name=\"ProfileCustomID". $data1['ProfileCustomID'] ."\" value=\"". $ProfileCustomValue ."\" /><br />\n";
					
				 //  }
				  echo "		</td>\n";
				  echo "	</tr>";
		
				
			}
				
		?>