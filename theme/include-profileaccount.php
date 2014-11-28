<?php
	global $user_ID; 
	global $current_user;
	global $wpdb;
	get_currentuserinfo();
	$ProfileUserLinked = $current_user->ID;
        $ptype = get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);
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

	$ProfileGender  = get_user_meta($current_user->ID, "rb_agency_interact_pgender", true);

	// Get Settings
	$rb_agency_options_arr = get_option('rb_agency_options');
	
		$rb_agency_option_showsocial 			= $rb_agency_options_arr['rb_agency_option_showsocial'];
		$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
		$rb_agency_option_locationtimezone 		= (int)$rb_agency_options_arr['rb_agency_option_locationtimezone'];
		$rb_agency_option_formshow_displayname = isset($rb_agency_options_arr['rb_agency_option_formshow_displayname'])?$rb_agency_options_arr['rb_agency_option_formshow_displayname']:0;

      
	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_registerallow = (int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerallow'];

	// Get Data
	$query = "SELECT * FROM " . table_agency_profile . " WHERE ProfileUserLinked='$ProfileUserLinked' LIMIT 1";
	$results = $wpdb->get_results($query,ARRAY_A) or die ( __("Error, query failed", rb_agency_interact_TEXTDOMAIN ));
	$count = $wpdb->num_rows;
	foreach($results as $data) {
		
		$ProfileGender = $data['ProfileGender'];
		$ProfileID					=$data['ProfileID'];
		$ProfileUserLinked			=$data['ProfileUserLinked'];
		$ProfileGallery				=stripslashes($data['ProfileGallery']);
		$ProfileContactDisplay		=stripslashes($data['ProfileContactDisplay']);
		$ProfileContactNameFirst	=stripslashes($data['ProfileContactNameFirst']);
		$ProfileContactNameLast		=stripslashes($data['ProfileContactNameLast']);
		$ProfileContactEmail		=stripslashes($data['ProfileContactEmail']);
		$ProfileContactWebsite		=stripslashes($data['ProfileContactWebsite']);
		$ProfileContactLinkFacebook	=stripslashes($data['ProfileContactLinkFacebook']);
		$ProfileContactLinkTwitter	=stripslashes($data['ProfileContactLinkTwitter']);
		$ProfileContactLinkYoutube	=stripslashes($data['ProfileContactLinkYoutube']);
		$ProfileContactLinkFlickr	=stripslashes($data['ProfileContactLinkFlickr']);
		$ProfileContactPhoneHome	=stripslashes($data['ProfileContactPhoneHome']);
		$ProfileContactPhoneCell	=stripslashes($data['ProfileContactPhoneCell']);
		$ProfileContactPhoneWork	=stripslashes($data['ProfileContactPhoneWork']);

		$ProfileDateBirth	    	=stripslashes($data['ProfileDateBirth']);
		$ProfileLocationStreet		=stripslashes($data['ProfileLocationStreet']);
		$ProfileLocationCity		=stripslashes($data['ProfileLocationCity']);
		$ProfileLocationState		=stripslashes($data['ProfileLocationState']);
		$ProfileLocationZip			=stripslashes($data['ProfileLocationZip']);
		$ProfileLocationCountry		=stripslashes($data['ProfileLocationCountry']);
		$ProfileDateUpdated			=$data['ProfileDateUpdated'];
		$ProfileCustomType          =$data["ProfileType"];

		$query= "SELECT DataTypeID, DataTypeTitle FROM " .  table_agency_data_type . " WHERE DataTypeID IN(".$ProfileCustomType  .") GROUP BY DataTypeTitle ";
		$queryShowDataType = $wpdb->get_results($query,ARRAY_A);
		$registered_as = array();
		foreach($queryShowDataType as $dataShowDataType){															
						array_push($registered_as, $dataShowDataType["DataTypeTitle"]);															
		}
        
       echo "<div id=\"profile-account\" class=\"rbform\">\n";
		echo "<h3>Hi ".$ProfileContactDisplay."! You are registered as ".implode(",",$registered_as)."</h3>";
		echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/account/\">\n";
		echo "	<input type=\"hidden\" name=\"ProfileID\" value=\"". $ProfileID ."\" />\n";
		echo "	<h3>". __("Contact Information", rb_agency_interact_TEXTDOMAIN) ."</h3>\n";
		echo "	<div id=\"gallery-folder\" class=\"rbfield rblink rbsingle\">\n";
		echo "		<label>". __("Gallery Folder", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div class=\"rbmessage\">\n";
					if (!empty($ProfileGallery) && is_dir(rb_agency_UPLOADPATH .$ProfileGallery)) { 
						echo "<span class=\"updated\"><a href=\"".network_site_url("/")."profile/". $ProfileGallery ."/\" target=\"_blank\">/profile/". $ProfileGallery ."/</a></span>\n";
						echo "<input type=\"hidden\" id=\"ProfileGallery\" name=\"ProfileGallery\" value=\"". $ProfileGallery ."\" />\n";
					} else {
						echo "<input type=\"text\" id=\"ProfileGallery\" name=\"ProfileGallery\" value=\"". $ProfileGallery ."\" />\n";
						echo "<small class=\"rbfield-note error\">". __("Folder Pending Creation", rb_agency_interact_TEXTDOMAIN) ."</small>\n";
					}		
		echo "		</div>\n";
		echo "	</div>\n";
		if($rb_agency_option_formshow_displayname > 0){
			echo "	<div id=\"profile-displayname\" class=\"rbfield rbtext rbsingle\">\n";
			echo "		<label>". __("Display Name", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
			echo "			<div><input type=\"text\" id=\"ProfileContactDisplay\" name=\"ProfileContactDisplay\" value=\"". $ProfileContactDisplay ."\" /></div>\n";
			echo "	</div>\n";
		}
		echo "	<div id=\"profile-firstname\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("First Name", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "			<div><input type=\"text\" id=\"ProfileContactNameFirst\" name=\"ProfileContactNameFirst\" value=\"". $ProfileContactNameFirst ."\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-lastname\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Last Name", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "			<div><input type=\"text\" id=\"ProfileContactNameLast\" name=\"ProfileContactNameLast\" value=\"". $ProfileContactNameLast ."\" /></div>\n";
		echo "	  </div>\n";
		echo "	<div id=\"profile-gender\" class=\"rbfield rbselect rbsingle\">\n";
		echo "		<label>". __("Gender", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div>";		

					echo "<select name=\"ProfileGender\">";
					echo "<option value=\"\">All Gender</option>";
					$query= "SELECT GenderID, GenderTitle FROM " .  table_agency_data_gender . " GROUP BY GenderTitle ";
					$queryShowGender = $wpdb->get_results($query,ARRAY_A);
		
					foreach($queryShowGender as $dataShowGender){															
						echo "<option value=\"".$dataShowGender["GenderID"]."\" ". selected($ProfileGender ,$dataShowGender["GenderID"],false).">".$dataShowGender["GenderTitle"]."</option>";															
					}
					echo "</select>";
		echo "		</div>\n";
		echo "	  </div>\n";

		// Private Information
		echo "	<h3>". __("Private Information", rb_agency_interact_TEXTDOMAIN) ."</h3>";
		echo "  <p>The following information will appear only in administrative areas.</p>\n";
		echo "	<div id=\"profile-email\" class=\"rbfield rbemail rbsingle\">\n";
		echo "		<label>". __("Email Address", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactEmail\" name=\"ProfileContactEmail\" value=\"". $ProfileContactEmail ."\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-birthdate\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Birthdate", rb_agency_interact_TEXTDOMAIN) ." <em>YYYY-MM-DD</em></label>\n";
		echo "		<div><input class=\"rb-datepicker\" type=\"text\" id=\"ProfileDateBirth\" name=\"ProfileDateBirth\" value=\"". $ProfileDateBirth ."\" /></div>\n";
		echo "	</div>\n";

		// Address
		echo "	<div id=\"profile-street\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Street", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileLocationStreet\" name=\"ProfileLocationStreet\" value=\"". $ProfileLocationStreet ."\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-city\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("City", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileLocationCity\" name=\"ProfileLocationCity\" value=\"". $ProfileLocationCity ."\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-country\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Country", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div>";
		//<input type=\"text\" id=\"ProfileLocationCountry\" name=\"ProfileLocationCountry\" value=\"". $ProfileLocationCountry ."\" />
								$query_get ="SELECT * FROM `". table_agency_data_country ."`" ;
								$result_query_get = $wpdb->get_results($query_get);
								$location= site_url();

								echo '<input type="hidden" id="url" value="'.$location.'">';
								echo "<select name=\"ProfileLocationCountry\" id=\"ProfileLocationCountry\"  onchange='javascript:populateStates(\"ProfileLocationCountry\",\"ProfileLocationState\");'>";
								echo '<option value="">'. __("Select country", rb_agency_TEXTDOMAIN) .'</option>';
								 foreach($result_query_get as $r){
									  $selected = isset($ProfileLocationCountry) && $ProfileLocationCountry==$r->CountryID?"selected=selected":"";
									echo '<option '.$selected.' value='.$r->CountryID.' >'.$r->CountryTitle.'</option>';
								 }
								echo '</select>';
		echo "      </div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-state\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("State", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		//echo "		<div><input type=\"text\" id=\"ProfileLocationState\" name=\"ProfileLocationState\" value=\"". $ProfileLocationState ."\" /></div>\n";
		echo "			<div>";
								$query_get ="SELECT * FROM `".table_agency_data_state."` WHERE CountryID='".(isset($ProfileLocationCountry)?$ProfileLocationCountry:"")."'" ;
								$result_query_get = $wpdb->get_results($query_get);
								echo '<select name="ProfileLocationState" id="ProfileLocationState">';
								echo '<option value="">'. __("Select state", rb_agency_TEXTDOMAIN) .'</option>';
								 foreach($result_query_get as $r){
									 $selected = isset($ProfileLocationState) && $ProfileLocationState==$r->StateID?"selected=selected":"";
									echo '<option '.$selected.' value='.$r->StateID.' >'.$r->StateTitle.'</option>';
								 }
								echo '</select>';
		echo "			</div>";
		echo "	</div>\n";
		echo "	<div id=\"profile-zip\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Zip", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileLocationZip\" name=\"ProfileLocationZip\" value=\"". $ProfileLocationZip ."\" /></div>\n";
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
		echo "	</div>\n";
		

	/*
	 *   added this new custom field display 
	 */
	$rb_agency_option_profilenaming 		= isset($rb_agency_options_arr['rb_agency_option_profilenaming']) ?(int)$rb_agency_options_arr['rb_agency_option_profilenaming']:0;
		
	$query3 = "SELECT * FROM ". table_agency_customfields ."  WHERE ProfileCustomView = 1 ORDER BY ProfileCustomOrder";

	$results3 = $wpdb->get_results($query3,ARRAY_A);
	  $count3 = $wpdb->num_rows;
	
	foreach($results3 as $data3) {
		/*
		 * Get Profile Types to
		 * filter models from clients
		 */
		$permit_type = false;

		$PID = $data3['ProfileCustomID'];

		$get_types = "SELECT ProfileCustomTypes FROM ". table_agency_customfields_types .
					 " WHERE ProfileCustomID = " . $PID;
		$result = $wpdb->get_results($get_types,ARRAY_A);
		$types = "";				
		foreach($result as $p){
		// 	$types = $p['ProfileCustomTypes'];		
		  //$types = str_replace("_", " ", $p['ProfileCustomTypes']);
			$types = str_replace(" ", "_", trim(strtolower($p['ProfileCustomTypes'])));
		}
		
		/*if($types != "" || $types != NULL){
		   $types = explode(",",$types); 
		   if(in_array($ptype,$types)){ $permit_type=true; }
		} */

		if($types != "" || $types != NULL){
		   $types = explode(",",trim($types)); 
		   if(count(array_intersect($ptype,$types))>0){ 
		   		$permit_type=true; 
		   } 
		} 
		
			$ProfileCustomTitle = $data3['ProfileCustomTitle'];
			$ProfileCustomType  = $data3['ProfileCustomType'];
			echo'<input type="hidden" name="aps12" value="'.$data3["ProfileCustomShowGender"].'" >';
			echo'<input type="hidden" name="ctype" value="'.($permit_type).'" >';
		
	
  	if (($data3["ProfileCustomShowGender"] == $ProfileGender) || ($data3["ProfileCustomShowGender"] == 0)  && $permit_type == true ) {

			

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
			
		elseif ($ProfileCustomType == 2 ) { // Min Max

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
			
		elseif ($ProfileCustomType == 3 || $ProfileCustomType == 9) {  // Drop Down

			echo "<div id=\"rbfield-".strtolower(trim($data3['ProfileCustomTitle']))." ".gender_filter($data3['ProfileCustomShowGender'])."\" class=\"rbfield rbselect rbsingle\">";
			echo "<label for=\"".strtolower(trim($data3['ProfileCustomTitle']))."\">"
			   . __( $data3['ProfileCustomTitle'].$measurements_label, rb_agency_interact_TEXTDOMAIN) 
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
									  }else{
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
				$myresults = $wpdb->get_results($myquery,ARRAY_A);
				foreach($myresults as $mydata) {
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
				}
				echo "</div>";
			}
		   
		   } // end if
			
		}// End while

		// Show Social Media Links
		if ($rb_agency_option_showsocial == "1") { 
		echo "	<h3>". __("Social Media Profiles", rb_agency_interact_TEXTDOMAIN) ."</h3>\n";
		echo "	<div id=\"profile-facebook\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Facebook", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactLinkFacebook\" name=\"ProfileContactLinkFacebook\" value=\"". $ProfileContactLinkFacebook ."\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-twitter\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Twitter", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactLinkTwitter\" name=\"ProfileContactLinkTwitter\" value=\"". $ProfileContactLinkTwitter ."\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-youtube\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("YouTube", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactLinkYoutube\" name=\"ProfileContactLinkYoutube\" value=\"". $ProfileContactLinkYoutube ."\" /></div>\n";
		echo "  </div>\n";
		echo "	<div id=\"profile-flickr\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Flickr", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileContactLinkFlickr\" name=\"ProfileContactLinkFlickr\" value=\"". $ProfileContactLinkFlickr ."\" /></div>\n";
		echo "	</div>\n";
		} 
		if ($rb_agencyinteract_option_registerallow  == 1) {
			echo "	<div id=\"profile-username\" class=\"rbfield rbtext rbsingle\">\n";
			echo "		<label>". __("Username", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
			echo "		<div>\n";
			if(isset($current_user->user_login)){
			echo "			<input type=\"text\" id=\"ProfileUsername\"  name=\"ProfileUsername\" disabled=\"disabled\" value=\"".$current_user->user_login."\" />\n";
			} else {
			echo "			<input type=\"text\" id=\"ProfileUsername\"  name=\"ProfileUsername\" value=\"\" />\n";	
			}
			echo "			<small class=\"rbfield-note\">Cannot be changed</small>";
			echo "		</div>\n";
			echo "  </div>\n";
	 	}
		echo "	<div id=\"rbprofile-password\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Password", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div>";
		echo "			<input type=\"password\" id=\"ProfilePassword\" name=\"ProfilePassword\" />\n";
		echo "			<small class=\"rbfield-note\">Leave blank to keep same password</small>";	
		echo "	 	</div>\n";
		echo "	</div>\n";
		echo "	<div id=\"rbprofile-retype-password\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Retype Password", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div>";
		echo "			<input type=\"password\" id=\"ProfilePasswordConfirm\" name=\"ProfilePasswordConfirm\" />";
		echo "			<small class=\"rbfield-note\">Retype to Confirm</small>";	
		echo "		</div>\n";
		echo "	</div>\n";
		echo "	<div id=\"rbform-last-updated\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Last updated ", rb_agency_interact_TEXTDOMAIN) ."</label>";
		echo "		<div>". rb_agency_makeago(rb_agency_convertdatetime($ProfileDateUpdated), $rb_agency_option_locationtimezone) ."</div>\n";
		echo "	</div>\n";
		echo "	<div id=\"rbform-submit\" class=\"rbfield rbsubmit rbsingle\">\n";
		echo "		<input type=\"hidden\" name=\"action\" value=\"editRecord\" />\n";
		echo "		<input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_agency_interact_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
		echo "	</div>\n";
		echo "</form>\n";
		echo "</div>\n";
	}
?>