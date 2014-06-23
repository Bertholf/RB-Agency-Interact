<?php
	global $user_ID; 
	global $current_user;
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
      
	$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
	$rb_agencyinteract_option_registerallow = (int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerallow'];

	// Get Data
	$query = "SELECT * FROM " . table_agency_profile . " WHERE ProfileUserLinked='$ProfileUserLinked' LIMIT 1";
	$results = mysql_query($query) or die ( __("Error, query failed", rb_agency_interact_TEXTDOMAIN ));
	$count = mysql_num_rows($results);
	while ($data = mysql_fetch_array($results)) {
		
		// $ProfileGender =$data['ProfileGender'];
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
		$ProfileContactLinkYouTube	=stripslashes($data['ProfileContactLinkYoutube']);
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

		$query= "SELECT GenderID, GenderTitle FROM " .  table_agency_data_gender . " GROUP BY GenderTitle ";
		$queryShowGender = mysql_query($query);
		$registered_as = array();
		while($dataShowGender = mysql_fetch_assoc($queryShowGender)){															
						array_push($registered_as, $dataShowGender["GenderTitle"]);															
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
					while($dataShowGender = mysql_fetch_assoc($queryShowGender)){															
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
		echo "		<div><input type=\"text\" id=\"ProfileDateBirth\" name=\"ProfileDateBirth\" value=\"". $ProfileDateBirth ."\" /></div>\n";
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
		echo "	<div id=\"profile-state\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("State", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileLocationState\" name=\"ProfileLocationState\" value=\"". $ProfileLocationState ."\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-zip\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Zip", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileLocationZip\" name=\"ProfileLocationZip\" value=\"". $ProfileLocationZip ."\" /></div>\n";
		echo "	</div>\n";
		echo "	<div id=\"profile-country\" class=\"rbfield rbtext rbsingle\">\n";
		echo "		<label>". __("Country", rb_agency_interact_TEXTDOMAIN) ."</label>\n";
		echo "		<div><input type=\"text\" id=\"ProfileLocationCountry\" name=\"ProfileLocationCountry\" value=\"". $ProfileLocationCountry ."\" /></div>\n";
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
		
		// Include Profile Customfields
		        $ProfileInformation = "1"; // Private fields only

			$query1 = "SELECT ProfileCustomID, ProfileCustomTitle, ProfileCustomType, ProfileCustomOptions, ProfileCustomOrder, ProfileCustomView, ProfileCustomShowGender, ProfileCustomShowProfile, ProfileCustomShowSearch, ProfileCustomShowLogged, ProfileCustomShowAdmin,ProfileCustomShowRegistration FROM ". table_agency_customfields ." WHERE ProfileCustomView = ". $ProfileInformation ." ORDER BY ProfileCustomOrder ASC";
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
                                $types = "";
                                while ( $p = mysql_fetch_array($result)){
                                        $types = $p['ProfileCustomTypes'];			    
                                }

                                if($types != "" || $types != NULL){
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
                                } 
                                
				if ( ($data1["ProfileCustomShowGender"] == $ProfileGender) || ($data1["ProfileCustomShowGender"] == 0) 
                                      && $permit_type == true )  {

					include("view-custom-fields.php");

				}
			 }


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
		echo "		<div><input type=\"text\" id=\"ProfileContactLinkYouTube\" name=\"ProfileContactLinkYouTube\" value=\"". $ProfileContactLinkYouTube ."\" /></div>\n";
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