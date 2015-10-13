<?php 
global $wpdb;
define("LabelPlural", "Pending Profiles");
define("LabelSingular", "Pending Profiles");
$rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_unittype  			= $rb_agency_options_arr['rb_agency_option_unittype'];
	$rb_agency_option_showsocial 			= $rb_agency_options_arr['rb_agency_option_showsocial'];
	$rb_agency_option_agencyimagemaxheight 	= $rb_agency_options_arr['rb_agency_option_agencyimagemaxheight'];
		if (empty($rb_agency_option_agencyimagemaxheight) || $rb_agency_option_agencyimagemaxheight < 500) {$rb_agency_option_agencyimagemaxheight = 800; }
	$rb_agency_option_profilenaming 		= (int)$rb_agency_options_arr['rb_agency_option_profilenaming'];
	$rb_agency_option_locationtimezone 		= (int)$rb_agency_options_arr['rb_agency_option_locationtimezone'];
// *************************************************************************************************** //
// Handle Post Actions
if (isset($_POST['action'])) {
	// Get Post State
	$action = $_POST['action'];
	switch($action) {
	// *************************************************************************************************** //
	// Delete bulk
	case 'deleteRecord':
		foreach($_POST as $ProfileID) {
			// Verify Record
			$queryDelete = "SELECT * FROM ". table_agency_profile ." WHERE ProfileID =  ". $ProfileID;
			$resultsDelete =$wpdb->get_results($queryDelete,ARRAY_A);
			foreach($resultsDelete as $dataDelete) {
				$ProfileGallery = $dataDelete['ProfileGallery'];

				// Remove Profile
				$delete = "DELETE FROM " . table_agency_profile . " WHERE ProfileID = ". $ProfileID;
				$results = $wpdb->query($delete);
				// Remove Media
				$delete = "DELETE FROM " . table_agency_profile_media . " WHERE ProfileID = ". $ProfileID;
				$results = $wpdb->query($delete);

				if (isset($ProfileGallery)) {
					// Remove Folder
					$dir = RBAGENCY_UPLOADPATH . $ProfileGallery ."/";
					$mydir = opendir($dir);
					while(false !== ($file = readdir($mydir))) {
						if($file != "." && $file != "..") {
							unlink($dir.$file) or DIE("couldn't delete $dir$file<br />");
						}
					}
					// remove dir
					if(is_dir($dir)) {
						rmdir($dir) or DIE("couldn't delete $dir$file<br />");
					}
					closedir($mydir);

				} else {
					echo __("No valid record found.", RBAGENCY_interact_TEXTDOMAIN);
				}

			echo ('<div id="message" class="updated"><p>'. __("Profile deleted successfully!", RBAGENCY_interact_TEXTDOMAIN) .'</p></div>');
			}// is there record?

		}
		rb_display_list();
		exit;
	break;

	}
}
else {
// *************************************************************************************************** //
// Show List
	rb_display_list();
}

// *************************************************************************************************** //
// Manage Record
function rb_display_list() {
  global $wpdb;
  $rb_agency_options_arr = get_option('rb_agency_options');
	$rb_agency_option_locationtimezone 		= (int)$rb_agency_options_arr['rb_agency_option_locationtimezone'];
  echo "<div class=\"wrap\">\n";
  echo "  <div id=\"rb-overview-icon\" class=\"icon32\"></div>\n";
  echo "  <h2>". __("List", RBAGENCY_interact_TEXTDOMAIN) ." ". LabelPlural ."</h2>\n";

  echo "  <h3 class=\"title\">". __("All Records", RBAGENCY_interact_TEXTDOMAIN) ."</h3>\n";

		// Sort By
        $sort = "";
        if (isset($_GET['sort']) && !empty($_GET['sort'])){
            $sort = $_GET['sort'];
        }
        else {
            $sort = "profile.ProfileContactNameFirst";
        }

		// Sort Order
        $dir = "";
        if (isset($_GET['dir']) && !empty($_GET['dir'])){
            $dir = $_GET['dir'];
            if ($dir == "desc" || !isset($dir) || empty($dir)){
               $sortDirection = "asc";
               } else {
               $sortDirection = "desc";
            }
		} else {
				$sortDirection = "desc";
				$dir = "asc";
		}
  
		// Filter
		$filter = "WHERE profile.ProfileIsActive = 3 ";
        if ((isset($_GET['ProfileContactNameFirst']) && !empty($_GET['ProfileContactNameFirst'])) || isset($_GET['ProfileContactNameLast']) && !empty($_GET['ProfileContactNameLast'])){
    		if (isset($_GET['ProfileContactNameFirst']) && !empty($_GET['ProfileContactNameFirst'])){
			$selectedNameFirst = $_GET['ProfileContactNameFirst'];
			$query .= "&ProfileContactNameFirst=". $selectedNameFirst ."";
			$filter .= " AND profile.ProfileContactNameFirst LIKE '". $selectedNameFirst ."%'";
			  }
    		if (isset($_GET['ProfileContactNameLast']) && !empty($_GET['ProfileContactNameLast'])){
			$selectedNameLast = $_GET['ProfileContactNameLast'];
			$query .= "&ProfileContactNameLast=". $selectedNameLast ."";
			$filter .= " AND profile.ProfileContactNameLast LIKE '". $selectedNameLast ."%'";
			  }
		}
		if (isset($_GET['ProfileUsername']) && !empty($_GET['ProfileUsername'])){
			$selectedUsername = $_GET['ProfileUsername'];
			$query .= "&ProfileUsername=". $selectedUsername ."";
			$filter .= " AND users.user_login='". $selectedUsername ."'";
		}
		if (isset($_GET['ProfileContactEmail']) && !empty($_GET['ProfileContactEmail'])){
			$selectedEmail = $_GET['ProfileContactEmail'];
			$query .= "&ProfileContactEmail=". $selectedEmail ."";
			$filter .= " AND profile.ProfileContactEmail='". $selectedEmail ."'";
		}
		
		if (isset($_GET['ProfileLocationCity']) && !empty($_GET['ProfileLocationCity'])){
			$selectedCity = $_GET['ProfileLocationCity'];
			$query .= "&ProfileLocationCity=". $selectedCity ."";
			$filter .= " AND profile.ProfileLocationCity='". $selectedCity ."'";
		}
		if (isset($_GET['ProfileType']) && !empty($_GET['ProfileType'])){
			$selectedType = $_GET['ProfileType'];
			$query .= "&ProfileType=". $selectedType ."";
			$filter .= " AND profiletype.DataTypeID='". $selectedType ."'";
		}

		// Bulk Action

		if(isset($_POST['BulkAction_ProfileApproval']) || isset($_POST['BulkAction_ProfileApproval2'])){

			//**** BULK DELETE
			if($_POST['BulkAction_ProfileApproval']=="Delete" || $_POST['BulkAction_ProfileApproval2']=="Delete"){

				if(isset($_POST['profileID'])){
					foreach($_POST['profileID'] as $key){

									$ProfileID = $key;
									// Verify Record
									$queryDelete = "SELECT * FROM ". table_agency_profile ." WHERE ProfileID =  ". $ProfileID;
									$resultsDelete = $wpdb->get_results($queryDelete,ARRAY_A);
									foreach ($resultsDelete as $dataDelete) {
										$ProfileGallery = $dataDelete['ProfileGallery'];

										// Remove Profile
										$delete = "DELETE FROM " . table_agency_profile . " WHERE ProfileID = ". $ProfileID;
										$results = $wpdb->query($delete);
										// Remove Media
										$delete = "DELETE FROM " . table_agency_profile_media . " WHERE ProfileID = ". $ProfileID;
										$results = $wpdb->query($delete);

										if (isset($ProfileGallery)) {
											// Remove Folder
											$dir = RBAGENCY_UPLOADPATH . $ProfileGallery ."/";
											$mydir = opendir($dir);
											while(false !== ($file = readdir($mydir))) {
												if($file != "." && $file != "..") {
													$isUnlinked = @unlink($dir.$file);
													if($isUnlinked){
	
													} else {
														echo "Couldn't delete $dir$file<br />";
													}
												}
											}
											// remove dir
											if(is_dir($dir)) {
												$isRemoved = @rmdir($dir);
												if($isRemoved){
	
												} else {
														echo "Couldn't delete $dir$file<br />";
												}
											}
											closedir($mydir);

										} else {
											echo __("No valid record found.", RBAGENCY_interact_TEXTDOMAIN);
										}

									echo ('<div id="message" class="updated"><p>'. __("Profile deleted successfully!", RBAGENCY_interact_TEXTDOMAIN) .'</p></div>');
									}// is there record?

					}

				}

			}
			// Bulk Approve
			else if($_POST['BulkAction_ProfileApproval']=="Approve" || $_POST['BulkAction_ProfileApproval2']=="Approve"){

					if(isset($_POST['profileID'])){
						$countProfile = 0;
						foreach($_POST['profileID'] as $key){

							$countProfile++;
							$ProfileID = $key;
							// Verify Record
							$queryApprove = "UPDATE ". table_agency_profile ." SET ProfileIsActive = 1 WHERE ProfileID =  ". $ProfileID;
							$resultsApprove = $wpdb->query($queryApprove);

							$ProfileUserLinked = rb_get_user_linkedID($ProfileID);

							wp_new_user_notification_approve($ProfileUserLinked);

						}

						$profileLabel = '';
						$countProfile > 1 ? $profileLabel = "$countProfile Profiles" : $profileLabel = "Profile" ;
					echo ('<div id="message" class="updated"><p>'. __("$profileLabel Approved successfully!", RBAGENCY_interact_TEXTDOMAIN) .'</p></div>');


					}

			}
		}

		if(isset($_GET["action"])=="approveRecord"){
			$ProfileID = $_GET["ProfileID"];
			$queryApprove = "UPDATE ". table_agency_profile ." SET ProfileIsActive = 1 WHERE ProfileID =  ". $ProfileID;
			$resultsApprove = $wpdb->query($queryApprove);
			$ProfileUserLinked = rb_get_user_linkedID($ProfileID);
			wp_new_user_notification_approve($ProfileUserLinked);
			if($resultsApprove){
				echo ('<div id="message" class="updated"><p>'. __(" ".(isset($profileLabel)?$profileLabel:"")." Approved successfully!", RBAGENCY_interact_TEXTDOMAIN) .'</p></div>');
			}
		}

		
		$results = $wpdb->get_results("SELECT *,users.user_login as ProfileUsername FROM ". table_agency_profile ." profile 
			LEFT JOIN ". table_agency_data_type ." profiletype ON profile.ProfileType = profiletype.DataTypeID 
			LEFT JOIN ".$wpdb->prefix."users users ON users.ID = profile.ProfileUserLinked 
			". $filter  .""); // number of total rows in the database
		//Paginate

		$items = $wpdb->num_rows;
		if($items > 0) {
			$p = new RBAGENCY_Pagination;
			$p->items($items);
			$p->limit(50); // Limit entries per page
			$p->target("admin.php?page=". (isset($_GET['page'])?$_GET['page']:"") .(isset($query)?$query:""));
			$p->currentPage((isset($_GET[(isset($p->paging)?$p->paging:"")])?$_GET[$p->paging]:"")); // Gets and validates the current page
			$p->calculate(); // Calculates what to show
			$p->parameterName('paging');
			$p->adjacents(1); //No. of page away from the current page

			if(!isset($_GET['paging'])) {
				$p->page = 1;
			} else {
				$p->page = $_GET['paging'];
			}

			//Query for limit paging
			$limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;
		} else {
			$limit = "";
		}


        echo "<div class=\"tablenav\">\n";
 		$queryGenderResult = $wpdb->get_results("SELECT GenderID, GenderTitle FROM ".table_agency_data_gender." ",ARRAY_A);
			$queryGenderCount = $wpdb->num_rows;
			foreach($queryGenderResult as $fetchGender){
			echo "	<div style=\"float: left; \"><a class=\"button-primary\" href=\"". admin_url("admin.php?page=rb_agency_menu_profiles") ."&action=add&ProfileGender=".$fetchGender["GenderID"]."\">". __("Create New ".ucfirst($fetchGender["GenderTitle"])."", RBAGENCY_TEXTDOMAIN) ."</a></div>\n";
			}
		echo "  <div class=\"tablenav-pages\">\n";
				if($items > 0) {
					echo $p->show();// Echo out the list of paging. 
				}
        echo "  </div>\n";
        echo "</div>\n";
		echo "<table cellspacing=\"0\" class=\"widefat fixed\">\n";
		echo "  <thead>\n";
		echo "    <tr>\n";
		echo "        <td style=\"width: 90%;\" nowrap=\"nowrap\">    \n";
       


		echo "    		<form method=\"GET\" action=\"". admin_url("admin.php?page=". (isset($_GET['page'])?$_GET["page"]:"")) ."\">\n";
		echo "    			<input type=\"hidden\" name=\"page_index\" id=\"page_index\" value=\"". (isset($_GET['page_index'])?$_GET["page_index"]:"") ."\" />  \n";
		echo "    			<input type=\"hidden\" name=\"page\" id=\"page\" value=\"". $_GET['page'] ."\" />\n";
		echo "    			<input type=\"hidden\" name=\"type\" value=\"name\" />\n";
		echo "    			". __("Search By", RBAGENCY_interact_TEXTDOMAIN) .": \n";
		echo "    			". __("First Name", RBAGENCY_interact_TEXTDOMAIN) .": <input type=\"text\" name=\"ProfileContactNameFirst\" value=\"". (isset($selectedNameFirst)?$selectedNameFirst:"") ."\" style=\"width: 100px;\" />\n";
		echo "    			". __("Last Name", RBAGENCY_interact_TEXTDOMAIN) .": <input type=\"text\" name=\"ProfileContactNameLast\" value=\"". (isset($selectedNameLast)?$selectedNameLast:"") ."\" style=\"width: 100px;\" />\n";
		echo "    			". __("Username", RBAGENCY_interact_TEXTDOMAIN) .": <input type=\"text\" name=\"ProfileUsername\" value=\"". (isset($selectedUsername)?$selectedUsername:"") ."\" style=\"width: 100px;\" />\n";
		echo "    			". __("Email Address", RBAGENCY_interact_TEXTDOMAIN) .": <input type=\"text\" name=\"ProfileContactEmail\" value=\"". (isset($selectedEmail)?$selectedEmail:"") ."\" style=\"width: 100px;\" />\n";
		echo "    			". __("Location", RBAGENCY_interact_TEXTDOMAIN) .": \n";
		echo "    			<select name=\"ProfileLocationCity\">\n";
		echo "					<option value=\"\">". __("Any Location", RBAGENCY_interact_TEXTDOMAIN) ."</option>";
								$query = "SELECT DISTINCT ProfileLocationCity, ProfileLocationState FROM ". table_agency_profile ." ORDER BY ProfileLocationState, ProfileLocationCity ASC";
								$results = $wpdb->get_results($query,ARRAY_A);
								$count = $wpdb->num_rows;
								foreach($results as $data){
										if (isset($data['ProfileLocationCity']) && !empty($data['ProfileLocationCity'])) {
									echo "<option value=\"". $data['ProfileLocationCity'] ."\" ". selected((isset($selectedCity)?$selectedCity:""), $data["ProfileLocationCity"]) ."\">". $data['ProfileLocationCity'] .", ". strtoupper(isset($dataLocation["ProfileLocationState"])?$dataLocation["ProfileLocationState"]:"") ."</option>\n";
									}
								}
		echo "    			</select>\n";
		echo "    			". __("Category", RBAGENCY_interact_TEXTDOMAIN) .":\n";
		echo "    			<select name=\"ProfileType\">\n";
		echo "					<option value=\"\">". __("Any Category", RBAGENCY_interact_TEXTDOMAIN) ."</option>";
								$query = "SELECT DataTypeID, DataTypeTitle FROM ". table_agency_data_type ." ORDER BY DataTypeTitle ASC";
								$results = $wpdb->get_results($query,ARRAY_A);
								$count = $wpdb->num_rows;
								foreach($results as $data) {
									echo "<option value=\"". $data['DataTypeID'] ."\" ". selected((isset($selectedCity)?$selectedCity:""), $data["DataTypeTitle"]) ."\">". $data['DataTypeTitle'] ."</option>\n";
								}
		echo "    			</select>\n";
		echo "    			<input type=\"submit\" value=\"". __("Filter", RBAGENCY_interact_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
		echo "          </form>\n";
		echo "        </td>\n";
		echo "        <td style=\"width: 10%;\" nowrap=\"nowrap\">\n";
		echo "    		<form method=\"GET\" action=\"". admin_url("admin.php?page=". $_GET['page']) ."\">\n";
		echo "    			<input type=\"hidden\" name=\"page_index\" id=\"page_index\" value=\"". (isset($_GET['page_index'])?$_GET['page_index']:"") ."\" />  \n";
		echo "    			<input type=\"hidden\" name=\"page\" id=\"page\" value=\"". $_GET['page'] ."\" />\n";
		echo "    			<input type=\"submit\" value=\"". __("Clear Filters", RBAGENCY_interact_TEXTDOMAIN) ."\" class=\"button-secondary\" />\n";
		echo "    		</form>\n";
		echo "        </td>\n";
		echo "        <td>&nbsp;</td>\n";

		
		echo "    </tr>\n";
		echo "  </thead>\n";
		echo "</table>\n";
     
     
		echo "<form method=\"post\" action=\"". admin_url("admin.php?page=". $_GET['page']) ."\" id=\"formMainBulk\">\n";
		echo "    			<select name=\"BulkAction_ProfileApproval\">\n";
		echo "              <option value=\"\"> ". __("Bulk Action", RBAGENCY_interact_TEXTDOMAIN) ."<option\>\n";
		echo "              <option value=\"Approve\"> ". __("Approve", RBAGENCY_interact_TEXTDOMAIN) ."<option\>\n";
		echo "              <option value=\"Delete\"> ". __("Delete", RBAGENCY_interact_TEXTDOMAIN) ."<option\>\n";
		echo "              </select>"; 
		echo "    <input type=\"submit\" value=\"". __("Apply", RBAGENCY_interact_TEXTDOMAIN) ."\" name=\"ProfileBulkAction\" class=\"button-secondary\"  />\n";
		echo "<table cellspacing=\"0\" class=\"widefat fixed\">\n";
		echo " <thead>\n";
		echo "    <tr class=\"thead\">\n";
		echo "        <th class=\"manage-column column-cb check-column\" id=\"cb\" scope=\"col\"><input type=\"checkbox\"/></th>\n";
		echo "        <th class=\"column-ProfileID\" id=\"ProfileID\" scope=\"col\" style=\"width:50px;\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&sort=ProfileID&dir=". $sortDirection) ."\">ID</a></th>\n";
		
		echo "        <th class=\"column-ProfileContactNameFirst\" id=\"ProfileContactNameFirst\" scope=\"col\" style=\"width:130px;\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&sort=ProfileContactNameFirst&dir=". $sortDirection) ."\">First Name</a></th>\n";
		echo "        <th class=\"column-ProfileContactNameLast\" id=\"ProfileContactNameLast\" scope=\"col\" style=\"width:130px;\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&sort=ProfileContactNameLast&dir=". $sortDirection) ."\">Last Name</a></th>\n";
		echo "        <th class=\"column-ProfileUsername\" id=\"ProfileUsername\" scope=\"col\" style=\"width:130px;\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&sort=ProfileUsername&dir=". $sortDirection) ."\">Username</a></th>\n";
		
		echo "        <th class=\"column-ProfileGender\" id=\"ProfileGender\" scope=\"col\" style=\"width:65px;\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&sort=ProfileGender&dir=". $sortDirection) ."\">Gender</a></th>\n";
		echo "        <th class=\"column-ProfilesProfileDate\" id=\"ProfilesProfileDate\" scope=\"col\" style=\"width:50px;\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&sort=ProfileDateBirth&dir=". $sortDirection) ."\">Age</a></th>\n";
		echo "        <th class=\"column-ProfileContactEmail\" id=\"ProfileContactEmail\" scope=\"col\" style=\"width:150px;\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&sort=ProfileContactEmail&dir=". $sortDirection) ."\">Email Address</a></th>\n";
		echo "        <th class=\"column-ProfileLocationCity\" id=\"ProfileLocationCity\" scope=\"col\" style=\"width:100px;\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&sort=ProfileLocationCity&dir=". $sortDirection) ."\">City</a></th>\n";
		echo "        <th class=\"column-ProfileLocationState\" id=\"ProfileLocationState\" scope=\"col\" style=\"width:50px;\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&sort=ProfileLocationState&dir=". $sortDirection) ."\">State</a></th>\n";
		echo "        <th class=\"column-ProfileDetails\" id=\"ProfileDetails\" scope=\"col\" style=\"width:100px;\">Category</th>\n";
		echo "        <th class=\"column-ProfileDetails\" id=\"ProfileDetails\" scope=\"col\" style=\"width:65px;\">Images</th>\n";
		echo "        <th class=\"column-ProfileStatHits\" id=\"ProfileStatHits\" scope=\"col\" style=\"width:60px;\">Views</th>\n";
		echo "        <th class=\"column-ProfileDateViewLast\" id=\"ProfileDateViewLast\" scope=\"col\">Last Viewed Date</th>\n";
		echo "    </tr>\n";
		echo " </thead>\n";
		echo " <tfoot>\n";
		echo "    <tr class=\"thead\">\n";
		echo "        <th class=\"manage-column column-cb check-column\" id=\"cb\" scope=\"col\"><input type=\"checkbox\"/></th>\n";
		echo "        <th class=\"column\" scope=\"col\">ID</th>\n";
		
		echo "        <th class=\"column\" scope=\"col\">First Name</th>\n";
		echo "        <th class=\"column\" scope=\"col\">Last Name</th>\n";
		echo "        <th class=\"column\" scope=\"col\">Username</th>\n";
		echo "        <th class=\"column\" scope=\"col\">Gender</th>\n";
		echo "        <th class=\"column\" scope=\"col\">Age</th>\n";
		echo "        <th class=\"column\" scope=\"col\">Email Address</th>\n";
		echo "        <th class=\"column\" scope=\"col\">City</th>\n";
		echo "        <th class=\"column\" scope=\"col\">State</th>\n";
		echo "        <th class=\"column\" scope=\"col\">Category</th>\n";
		echo "        <th class=\"column\" scope=\"col\">Images</th>\n";
		echo "        <th class=\"column\" scope=\"col\">Views</th>\n";
		echo "        <th class=\"column\" scope=\"col\">Last Viewed</th>\n";
		echo "    </tr>\n";
		echo " </tfoot>\n";
		echo " <tbody>\n";
        $query = "SELECT *,users.user_login ProfileUsername FROM ". table_agency_profile ." profile 
            LEFT JOIN ". table_agency_data_type ." profiletype ON profile.ProfileType = profiletype.DataTypeID 
            LEFT JOIN ".$wpdb->prefix."users users ON users.ID = profile.ProfileUserLinked 
            ". $filter  ." ORDER BY $sort $dir $limit";
        
        //echo $query;
        $results2 = $wpdb->get_results($query,ARRAY_A);
        echo $wpdb->last_error;
        
        $count_clients = $wpdb->num_rows;
        foreach ($results2 as $data) {
            
            $ProfileID = $data['ProfileID'];
            $ProfileGallery = stripslashes($data['ProfileGallery']);
            $ProfileUsername = stripslashes($data['ProfileUsername']);
            $ProfileContactNameFirst = stripslashes($data['ProfileContactNameFirst']);
            $ProfileContactNameLast = stripslashes($data['ProfileContactNameLast']);
            $ProfileContactEmail = stripslashes($data['ProfileContactEmail']);
            $ProfileLocationCity = RBAgency_Common::format_propercase(stripslashes($data['ProfileLocationCity']));
            $ProfileLocationState = stripslashes($data['ProfileLocationState']);
            $ProfileGender = stripslashes($data['ProfileGender']);
            $ProfileDateBirth = stripslashes($data['ProfileDateBirth']);
            $ProfileStatHits = stripslashes($data['ProfileStatHits']);
            $ProfileDateCreated = stripslashes($data['ProfileDateCreated']);
            
			$DataTypeTitle = stripslashes($data['ProfileType']);

			if(strpos($data['ProfileType'], ",") > 0){
            $title = explode(",",$data['ProfileType']);
            $new_title = "";
            foreach($title as $t){
                $id = (int)$t;
                $get_title = "SELECT DataTypeTitle FROM " . table_agency_data_type .  
                             " WHERE DataTypeID = " . $id; 
                $resource = $wpdb->get_row($get_title,ARRAY_A); 
                $count = $wpdb->num_rows;  
                if ($count > 0 ){
                    $new_title .= "," . $resource['DataTypeTitle']; 
                }
            }
            $new_title = substr($new_title,1);
        } else {
                $new_title = "";
                $id = (int)$data['ProfileType'];
                $get_title = "SELECT DataTypeTitle FROM " . table_agency_data_type .  
                             " WHERE DataTypeID = " . $id; 
                $resource = $wpdb->get_row($get_title,ARRAY_A);     
                $count = $wpdb->num_rows;
                if ($count > 0 ){
                    $new_title = $resource['DataTypeTitle']; 
                }
        }
         
        
            $DataTypeTitle = stripslashes($new_title);
			$resultImageCount = $wpdb->get_results("SELECT * FROM " . table_agency_profile_media . " WHERE ProfileID='". $ProfileID ."' AND ProfileMediaType = 'Image'",ARRAY_A);
			$profileImageCount = $wpdb->num_rows;

			$fetchProfileGender = $wpdb->get_row("SELECT * FROM ".table_agency_data_gender." WHERE GenderID = '".$ProfileGender."' ",ARRAY_A);

			$ProfileGender  = $fetchProfileGender["GenderTitle"];
		echo "    <tr". (isset($rowColor)?$rowColor:"") .">\n";
		echo "        <th class=\"check-column\" scope=\"row\">\n";
		echo "          <input type=\"checkbox\" value=\"". $ProfileID ."\" class=\"administrator\" id=\"". $ProfileID ."\" name=\"profileID[". $ProfileID ."]\"/>\n";
		echo "        </th>\n";
		echo "        <td class=\"ProfileID column-ProfileID\">". $ProfileID ."</td>\n";
		
		echo "        <td class=\"ProfileContactNameFirst column-ProfileContactNameFirst\">\n";
		echo "          ". $ProfileContactNameFirst ."\n";
		echo "          <div class=\"row-actions\">\n";
		echo "            <span class=\"allow\"><a href=\"". admin_url("admin.php?page=". $_GET['page'] ."&amp;action=approveRecord&amp;ProfileID=". $ProfileID) ."\" title=\"". __("Approve this Record", RBAGENCY_interact_TEXTDOMAIN) . "\">". __("Approve", RBAGENCY_interact_TEXTDOMAIN) . "</a> | </span>\n";
		echo "            <span class=\"edit\"><a href=\"". admin_url("admin.php?page=rb_agency_profiles&amp;action=editRecord&amp;ProfileID=". $ProfileID) ."\" title=\"". __("Edit this Record", RBAGENCY_interact_TEXTDOMAIN) . "\">". __("Edit", RBAGENCY_interact_TEXTDOMAIN) . "</a> | </span>\n";
		echo "            <span class=\"view\"><a href=\"../profile/" . $ProfileGallery ."/\" title=\"". __("View", RBAGENCY_interact_TEXTDOMAIN) . "\" target=\"_blank\">". __("View", RBAGENCY_interact_TEXTDOMAIN) . "</a> | </span>\n";
		//echo "            <span class=\"delete\"><a class=\"submitdelete\" href=\"". admin_url("admin.php?page=". $_GET['page']) ."&amp;action=deleteRecord&amp;ProfileID=". $ProfileID ."\"  onclick=\"if ( confirm('". __("You are about to delete the profile for ", RBAGENCY_interact_TEXTDOMAIN) ." ". $ProfileContactNameFirst ." ". $ProfileContactNameLast ."'". __("Cancel", RBAGENCY_interact_TEXTDOMAIN) . "\' ". __("to stop", RBAGENCY_interact_TEXTDOMAIN) . ", \'". __("OK", RBAGENCY_interact_TEXTDOMAIN) . "\' ". __("to delete", RBAGENCY_interact_TEXTDOMAIN) . ".') ) {return true;}return false;\" title=\"". __("Delete this Record", RBAGENCY_interact_TEXTDOMAIN) . "\">". __("Delete", RBAGENCY_interact_TEXTDOMAIN) . "</a> </span>\n";
		echo "          </div>\n";
		echo "        </td>\n";
		echo "        <td class=\"ProfileContactNameLast column-ProfileContactNameLast\">". $ProfileContactNameLast ."</td>\n";
		echo "        <td class=\"ProfileUsername column-ProfileUsername\">". $ProfileUsername ."</td>\n";
		echo "        <td class=\"ProfileGender column-ProfileGender\">". $ProfileGender ."</td>\n";
		echo "        <td class=\"ProfilesProfileDate column-ProfilesProfileDate\">". rb_agency_get_age($ProfileDateBirth) ."</td>\n";
		echo "        <td class=\"ProfileContactEmail column-ProfileContactEmail\">". $ProfileContactEmail ."</td>\n";
		
		echo "        <td class=\"ProfileLocationCity column-ProfileLocationCity\">". $ProfileLocationCity ."</td>\n";
		echo "        <td class=\"ProfileLocationCity column-ProfileLocationState\">". $ProfileLocationState ."</td>\n";
		echo "        <td class=\"ProfileDetails column-ProfileDetails\">". $DataTypeTitle ."</td>\n";
		echo "        <td class=\"ProfileDetails column-ProfileDetails\">". $profileImageCount ."</td>\n";
		echo "        <td class=\"ProfileStatHits column-ProfileStatHits\">". $ProfileStatHits ."</td>\n";
		echo "        <td class=\"ProfileDateViewLast column-ProfileDateViewLast\">\n";
		echo "           ". rb_agency_makeago(rb_agency_convertdatetime($ProfileDateCreated), $rb_agency_option_locationtimezone);
		echo "        </td>\n";
		echo "    </tr>\n";

        }
            if ($count_clients < 1) {
				if (isset($filter)) {
		echo "    <tr>\n";
		echo "        <th class=\"check-column\" scope=\"row\"></th>\n";
		echo "        <td class=\"name column-name\" colspan=\"5\">\n";
		echo "           <p>No profiles found with this criteria.</p>\n";
		echo "        </td>\n";
		echo "    </tr>\n";
				} else {
		echo "    <tr>\n";
		echo "        <th class=\"check-column\" scope=\"row\"></th>\n";
		echo "        <td class=\"name column-name\" colspan=\"5\">\n";
		echo "            <p>There aren't any profiles loaded yet!</p>\n";
		echo "        </td>\n";
		echo "    </tr>\n";
				}
        }
		echo " </tbody>\n";
		echo "</table>\n";

		echo "    			<select name=\"BulkAction_ProfileApproval2\">\n";
		echo "              <option value=\"\"> ". __("Bulk Action", RBAGENCY_interact_TEXTDOMAIN) ."<option\>\n";
		echo "              <option value=\"Approve\"> ". __("Approve", RBAGENCY_interact_TEXTDOMAIN) ."<option\>\n";
		echo "              <option value=\"Delete\"> ". __("Delete", RBAGENCY_interact_TEXTDOMAIN) ."<option\>\n";
		echo "              </select>"; 
		echo "    <input type=\"submit\" value=\"". __("Apply", RBAGENCY_interact_TEXTDOMAIN) ."\" name=\"ProfileBulkAction\" class=\"button-secondary\"  />\n";

		echo "<div class=\"tablenav\">\n";
		echo "  <div class='tablenav-pages'>\n";
			if($items > 0) {
				echo $p->show();// Echo out the list of paging. 
			}
		echo "  </div>\n";
		echo "</div>\n";
    
		echo "<p class=\"submit\">\n";
		//echo "  <input type=\"hidden\" value=\"deleteRecord\" name=\"action\" />\n";
		//echo "  <input type=\"submit\" value=\"". __('Delete') ."\" class=\"button-primary\" name=\"submit\" />	\n";
		echo "</p>\n";


		echo "</form>\n";
}
?>