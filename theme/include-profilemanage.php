<?php
	global $user_ID;
	global $current_user;
	global $wpdb;
	get_currentuserinfo();
	$ProfileUserLinked = $current_user->ID;
	// Get Settings
	$rb_agency_options_arr = get_option('rb_agency_options');
		$rb_agency_option_unittype  			= isset($rb_agency_options_arr['rb_agency_option_unittype'])?$rb_agency_options_arr['rb_agency_option_unittype']:0;
		$rb_agency_option_locationtimezone 		= isset($rb_agency_options_arr['rb_agency_option_locationtimezone'])? (int)$rb_agency_options_arr['rb_agency_option_locationtimezone']:0;
	// Get Values
		$query = "SELECT * FROM " . table_agency_profile . " WHERE ProfileUserLinked='$ProfileUserLinked' LIMIT 1";
	$results = $wpdb->get_results($query,ARRAY_A);
		$count = $wpdb->num_rows;
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
		$query3 = "SELECT * FROM " . table_agency_data_type . " WHERE DataTypeLevel = 0 ORDER BY DataTypeOrder ASC";
		$results3 = $wpdb->get_results($query3,ARRAY_A);
		$count3 = $wpdb->num_rows;
		$ProfileTypeNotArray = "";
		// print_r($results3);
	foreach($results as $data) {
		$ProfileID					=$data['ProfileID'];
		$ProfileGender = $data['ProfileGender'];
		$ProfileUserLinked			=$data['ProfileUserLinked'];
		$ProfileDateUpdated			=stripslashes($data['ProfileDateUpdated']);
		$ProfileType				=stripslashes($data['ProfileType']);
		$ProfileType 				=explode(",",$ProfileType);
		$ProfileTypeNotArray		=stripslashes($data['ProfileType']);
		$ProfileResume		=stripslashes($data['ProfileResume']);
		$i=1;
		foreach($results3 as $data3) {

				$dataID = $data3['DataTypeID'];

				$profileType .=  "<input type=\"checkbox\" name=\"ProfileType[]\" value=\"".$data3['DataTypeID']."\" ".(in_array($data3['DataTypeID'], $ProfileType)?"checked=\"checked\"":"")."/> ".$data3['DataTypeTitle'] ;

				$query3 = "SELECT * FROM " . table_agency_data_type . " WHERE DataTypeParentID = ".$dataID." ORDER BY DataTypeOrder ASC";
				$results_child = $wpdb->get_results($query3,ARRAY_A);
				foreach ($results_child as $child) {
					$profileType .=  "<br />&nbsp;&nbsp;<input type=\"checkbox\" name=\"ProfileType[]\" value=\"".$child['DataTypeID']."\" ".(in_array($child['DataTypeID'], $ProfileType)?"checked=\"checked\"":"")."/> ".$child['DataTypeTitle'] ;
				}

				if($i<$count3){
					$profileType .=  "</br>";
				}
			$i++;
		}

		$styleclass = 'rbfield';

		echo "<div class=\"rbform\">";
		echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/manage/\">\n";
		echo "     <input type=\"hidden\" name=\"ProfileID\" value=\"". $ProfileID ."\" />\n";
		echo "     <input type=\"hidden\" name=\"ProfileType\" value=\"". $ptype1 ."\" />\n";

		echo "<div id=\"rb-field-classification\" class=\"". $styleclass ." rbtext rbsingle\">";
		echo "	<label for=\"classification\">". __("Classification:", RBAGENCY_interact_TEXTDOMAIN) ."</label>";
		echo "	<div>".$profileType ."</div>";
		echo "</div>";

	/*
	 *   added this new custom field display
	 */
	$rb_agency_option_profilenaming 		= isset($rb_agency_options_arr['rb_agency_option_profilenaming']) ?(int)$rb_agency_options_arr['rb_agency_option_profilenaming']:0;

	$query3 = "SELECT * FROM ". table_agency_customfields ."
				WHERE ProfileCustomView = 0 AND ProfileCustomShowRegistration = 1 ORDER BY ProfileCustomOrder";

	$results3 = $wpdb->get_results($query3,ARRAY_A);
		$count3 = $wpdb->num_rows;
	//echo $ProfileTypeNotArray;

	foreach($results3 as $data3) {
		/*
		 * Get Profile Types to
		 * filter models from clients
		 */
		$permit_type = false;

		$PID = $data3['ProfileCustomID'];

		$get_types = "SELECT ProfileCustomTypes,ProfileCustomDataTypeID FROM ". table_agency_customfields_types .
					" WHERE ProfileCustomID = " . $PID;
		$results = $wpdb->get_results($get_types,ARRAY_A);

		$typesID = "";
		foreach($results as $result){
			$typesID = $result['ProfileCustomDataTypeID'];
		}
		$ProfileTypeToArray = explode(",",$ProfileTypeNotArray);
		if($typesID != "" || $typesID != NULL){
			$typesID = explode(",",trim($typesID));
			if(count(array_intersect($ProfileTypeToArray,$typesID))>0){
					$permit_type=true;
			}
		}

		echo'<input type="hidden" name="aps12" value="'.$data3["ProfileCustomShowGender"].'" >';
		echo'<input type="hidden" name="ctype" value="'.(isset($ProfileCustomType)?$ProfileCustomType:"").'" >';
			$ProfileCustomTitle = $data3['ProfileCustomTitle'];
			$ProfileCustomType  = $data3['ProfileCustomType'];

			$genderTitle = rb_agency_getGenderTitle($ProfileGender);						
			$customFieldGenders = get_option("ProfileCustomShowGenderArr_".$data3['ProfileCustomID']);
			$showCustomfieldsGendersArr = explode(",",$customFieldGenders);

			//echo $genderTitle."=".$data3['ProfileCustomTitle']."=".$data3['ProfileCustomID']."=".get_option("ProfileCustomShowGenderArr_".$data3["ProfileCustomID"])."<br>";
			if (  (in_array($genderTitle, $showCustomfieldsGendersArr) || in_array('All Gender', $showCustomfieldsGendersArr)) && $permit_type == true) {
			//  SET Label for Measurements
			//  Imperial(in/lb), Metrics(ft/kg)
			$rb_agency_options_arr = get_option('rb_agency_options');
			$rb_agency_option_unittype  = $rb_agency_options_arr['rb_agency_option_unittype'];
			$measurements_label = "";

			if ($ProfileCustomType == 7) { //measurements field type

				if($rb_agency_option_unittype ==0) { // 0 = Metrics(ft/kg)
					if($data3['ProfileCustomOptions'] == 1){
						$measurements_label  ="<em>(".__("cm", RBAGENCY_interact_TEXTDOMAIN).")</em>";
					} elseif($data3['ProfileCustomOptions'] == 2){
						$measurements_label  ="<em>(".__("kg", RBAGENCY_interact_TEXTDOMAIN).")</em>";
					} elseif($data3['ProfileCustomOptions'] == 3){
						$measurements_label  ="<em>(".__("Inches/Feet", RBAGENCY_interact_TEXTDOMAIN).")</em>";
					}
				} elseif($rb_agency_option_unittype ==1){ //1 = Imperial(in/lb)
					if($data3['ProfileCustomOptions'] == 1){
						$measurements_label  ="<em> (".__("In Inches", RBAGENCY_interact_TEXTDOMAIN).")</em>";
					} elseif($data3['ProfileCustomOptions'] == 2){
						$measurements_label  ="<em> (".__("In Pound", RBAGENCY_interact_TEXTDOMAIN).")</em>";
					} elseif($data3['ProfileCustomOptions'] == 3){
						$measurements_label  ="<em>(".__("In Inches/Feet", RBAGENCY_interact_TEXTDOMAIN).")</em>";
					}
				}
			}

			$styleid = 'rbfield-'. str_replace(' ', '-', strtolower(trim($data3['ProfileCustomTitle'])));

			$gender_filter = gender_filter($data3['ProfileCustomShowGender']);
			if ( isset( $gender_filter ) ) {
				$styleclass = $styleclass .' '. gender_filter($data3['ProfileCustomShowGender']);
			}

		if ($ProfileCustomType == 1) { //TEXT

			echo "<div id=\"". $styleid ."\" class=\"". $styleclass ." rbtext rbsingle\">";
			echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
				. __( $data3['ProfileCustomTitle'].$measurements_label, RBAGENCY_interact_TEXTDOMAIN)
				."</label>\n";
			echo '<div><input type="text" name="ProfileCustomID'. $data3['ProfileCustomID']
				.'" value="'. retrieve_datavalue(isset($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']])?$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]:"",
					$data3['ProfileCustomID'],$ProfileID,"textbox")
				.'" /></div>';
			echo "</div>";
			}

		elseif ($ProfileCustomType == 2 ) { // Min Max

			echo "<div id=\"". $styleid ."\" class=\"". $styleclass ." rbtext rbmulti\">";
			echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
				. __( $data3['ProfileCustomTitle'].$measurements_label, RBAGENCY_interact_TEXTDOMAIN)
				."</label>\n";
			echo "<div>";
				$ProfileCustomOptions_String = str_replace(",",":",
												strtok(strtok($data3['ProfileCustomOptions'],"}"),"{"));

				list($ProfileCustomOptions_Min_label,$ProfileCustomOptions_Min_value,
				$ProfileCustomOptions_Max_label,$ProfileCustomOptions_Max_value)
				= explode(":", $ProfileCustomOptions_String);

				if (!empty($ProfileCustomOptions_Min_value) && !empty($ProfileCustomOptions_Max_value)) {

						echo "<div><label for=\"ProfileCustomLabel_min\">"
							. __("Min", RBAGENCY_TEXTDOMAIN) . " </label>\n";
						echo "<div><input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID']
							."\" value=\"".
							retrieve_datavalue($ProfileCustomOptions_Min_value,
												$data3['ProfileCustomID'],$ProfileID,"textbox")
								."\" /></div></div>\n";
						echo "<div><label for=\"ProfileCustomLabel_min\">"
							. __("Max", RBAGENCY_TEXTDOMAIN) . " </label>\n";
						echo "<div><input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\""
							.  retrieve_datavalue($ProfileCustomOptions_Max_value,
													$data3['ProfileCustomID'],$ProfileID,"textbox") ."\" /></div></div>\n";

				} else {
						echo "<div><label for=\"ProfileCustomLabel_min\">"
							. __("Min", RBAGENCY_TEXTDOMAIN) . " </label>\n";
						echo "<div><input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\""
							.retrieve_datavalue($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']],
													$data3['ProfileCustomID'],$ProfileID,"textbox") ."\" /></div></div>\n";
						echo "<div><label for=\"ProfileCustomLabel_min\">"
							. __("Max", RBAGENCY_TEXTDOMAIN) . " </label>\n";
						echo "<div><input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\""
							.retrieve_datavalue($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']],
													$data3['ProfileCustomID'],$ProfileID,"textbox") ."\" /></div></div>\n";
				}
			echo "</div>";

		}

		elseif ($ProfileCustomType == 3 || $ProfileCustomType == 9) { // Drop Down

			echo "<div id=\"". $styleid ."\" class=\"". $styleclass ." rbselect rbsingle\">";
			echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
				. __( $data3['ProfileCustomTitle'].$measurements_label, RBAGENCY_interact_TEXTDOMAIN)
				."</label>\n";

				@list($option1,$option2) = @explode(":",$data3['ProfileCustomOptions']);

				$data = explode("|",$option1);
				$data2 = explode("|",$option2);
				echo "<div>";
				echo "<select name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\" ".($ProfileCustomType == 9?"multiple=\"multiple\"":"").">\n";

						echo "<option value=\"\">--</option>";

							$pos = 0;
							foreach($data as $val1){
								if(!empty($val1)){
										if($ProfileCustomType == 9){
												echo "<option value=\"".$val1."\" ".
											retrieve_datavalue("",$data3['ProfileCustomID'],$ProfileID,"multiple",$val1)
												." >".$val1."</option>";
										} else {
													echo "<option value=\"".$val1."\" ".
											retrieve_datavalue("",$data3['ProfileCustomID'],$ProfileID,"dropdown",$val1)
												." >".$val1."</option>";
										}
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
				echo "<div id=\"". $styleid ."\" class=\"". $styleclass ." rbtextarea rbsingle\">";
				echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
				. __( $data3['ProfileCustomTitle'].$measurements_label, RBAGENCY_interact_TEXTDOMAIN)
				."</label>\n";
				echo "<div><textarea name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\">"
					. retrieve_datavalue(isset($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']])?$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]:"",
													$data3['ProfileCustomID'],$ProfileID,"textbox") ."</textarea></div>";
				echo "</div>";
			} elseif ($ProfileCustomType == 5) {
				echo "<div id=\"". $styleid ."\" class=\"". $styleclass ." rbcheckbox rbsingle\">";
				echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
				. __( $data3['ProfileCustomTitle'].$measurements_label, RBAGENCY_interact_TEXTDOMAIN)
				."</label>\n";
				$xplode =array();
				$myquery = "SELECT ProfileCustomValue FROM " . table_agency_customfield_mux . " WHERE ProfileID=".$ProfileID." and ProfileCustomID=".$data3['ProfileCustomID']." ";
				$myresults = $wpdb->get_results($myquery,ARRAY_A);
				foreach($myresults as $mydata) {
					$xplode = explode(",",$mydata['ProfileCustomValue']);
				}

				$array_customOptions_values = explode("|",$data3['ProfileCustomOptions']);
				echo "<div>";
				foreach($array_customOptions_values as $val){
					if(isset($val) && $val!=""){
					echo "<div><label><input type=\"checkbox\" value=\"". $val."\"   ";

					if(in_array($val,$xplode)){echo "checked=\"checked\""; }

					echo" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\" />";

					echo "<span> ". $val."</span></label></div>";
					}
				}

				echo "</div>";
				echo "</div>";

			} elseif ($ProfileCustomType == 6) {

				echo "<fieldset id=\"". $styleid ."\" class=\"". $styleclass ." rbcheckbox rbsingle\">";
				echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
				. __( $data3['ProfileCustomTitle'].$measurements_label, RBAGENCY_interact_TEXTDOMAIN)
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

					if(!empty($val)){
						echo "<div><label><input type=\"radio\" value=\"". $val."\" " . $check .
							" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."[]\" /> ";
						echo "<span> ".$val."</span></label></div>";
					}


				}
				echo "</div>";
				echo "</fieldset>";

			} elseif ($ProfileCustomType == 10) { //Date
				echo "<div id=\"". $styleid ."\" class=\"". $styleclass ." rbselect rbsingle\">";
				echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
				. __( $data3['ProfileCustomTitle'].$measurements_label, RBAGENCY_interact_TEXTDOMAIN)
				."</label>\n";
							echo "<div>";

						echo "<input type=\"text\" id=\"rb_datepicker". $data3['ProfileCustomID']."\" class=\"rb-datepicker\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."_date\" value=\"". 	retrieve_datavalue("",$data3['ProfileCustomID'],$ProfileID,"date") ."\" /><br />\n";
						echo "<script type=\"text/javascript\">\n\n";
						echo "jQuery(function(){\n\n";
						echo "jQuery(\"input[name=ProfileCustomID". $data3['ProfileCustomID'] ."_date]\").val('". 	retrieve_datavalue("",$data3['ProfileCustomID'],$ProfileID,"date")."');\n\n";
							echo "});\n\n";
						echo "</script>\n\n";
				echo "</div>";
					echo "</div>";
			} elseif ($ProfileCustomType == 7) { //Imperial/Metrics

				if($data3['ProfileCustomOptions']==3){
					echo "<div id=\"". $styleid ."\" class=\"". $styleclass ." rbselect rbsingle\">";
					echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
					. __( $data3['ProfileCustomTitle'].$measurements_label, RBAGENCY_interact_TEXTDOMAIN)
					."</label>\n";
					if($rb_agency_option_unittype==1){
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

					}
						else {

					echo '<div><input type="text" name="ProfileCustomID'. $data3['ProfileCustomID']
					.'" value="'. retrieve_datavalue(isset($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']])?$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]:"",
														$data3['ProfileCustomID'],$ProfileID, 'textbox')
					.'" /></div>';
					;
					}
					echo "</div>";
				}else{
					echo "	<div id=\"rbfield-". $data3['ProfileCustomID'] ."\" class=\"rbfield rbtextarea rbsingle\">\n";
					echo "		<label for=\"ProfileCustomID". $data3['ProfileCustomID'] ."\">".__($data3['ProfileCustomTitle'].$measurements_label, RBAGENCY_TEXTDOMAIN).":</label>\n";
					echo "		<div><input type=\"text\" name=\"ProfileCustomID". $data3['ProfileCustomID'] ."\" value=\"". retrieve_datavalue(isset($_REQUEST["ProfileCustomID". $data3['ProfileCustomID']])?$_REQUEST["ProfileCustomID". $data3['ProfileCustomID']]:"",
														$data3['ProfileCustomID'],$ProfileID, 'textbox') ."\" /></div>\n";
					echo "	</div>";
				}
				
			}

			}// end if

		}// End while

		echo "<div class=\"rbfield\">";
		echo "		<label>". __("Resume Editor", RBAGENCY_interact_TEXTDOMAIN)."</label>";
		echo " <div id=\"\" class=\"\" style=\"width: 700px !important;overflow: hidden;margin-left:250px;margin-top:-35px;\">";		
			$profile_resume = $ProfileResume;
			$content = !empty($profile_resume) ? $profile_resume : "";
			$editor_id = 'profile_resume'; 
		
			wp_editor( $content, $editor_id,array("wpautop"=>false,"tinymce"=>true) ); 
		
		echo "	</div>\n";
		echo " </div>\n";

		echo " <div id=\"rbfield-last-update\" class=\"". $styleclass ." rbtext rbsingle\">";
		echo "		<label>". __("Last updated", RBAGENCY_interact_TEXTDOMAIN)."</label>";
		echo "		<div>". rb_agency_makeago(rb_agency_convertdatetime($ProfileDateUpdated), $rb_agency_option_locationtimezone) ."</div>\n";
		echo "	</div>\n";
		echo "	<div id=\"rbfield-submit\" class=\"". $styleclass ." rbsubmit rbsingle\">";
		echo "		<input type=\"hidden\" name=\"action\" value=\"editRecord\" />\n";
		echo "		<input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", RBAGENCY_interact_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
		echo "	</div>\n";
		echo "</form>\n";
		echo "</div>\n";

		echo '

		<script type="text/javascript">
				jQuery(function(){
					jQuery( "input[id=rb_datepicker22]").datepicker({
						dateFormat: "yy-mm-dd"
					});
				});
				</script>
		';


	}
?>
