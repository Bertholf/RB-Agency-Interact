<?php
	global $user_ID; 
	global $current_user;
	get_currentuserinfo();
	$ProfileUserLinked = $current_user->id;

	$query = "SELECT * FROM " . table_agency_profile . " WHERE ProfileUserLinked='$ProfileUserLinked'";
	$results = mysql_query($query) or die ( __("Error, query failed", rb_agencyinteract_TEXTDOMAIN ));
	$count = mysql_num_rows($results);
	while ($data = mysql_fetch_array($results)) {
		$ProfileID					=$data['ProfileID'];
		$ProfileUserLinked			=$data['ProfileUserLinked'];
		$ProfileGallery				=stripslashes($data['ProfileGallery']);


		echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/media/\">\n";
	
			if ( !empty($ProfileID) && ($ProfileID > 0) ) { // Editing Record
		echo "		<h3>". __("Gallery", rb_agencyinteract_TEXTDOMAIN) ."</h3>\n";
				
				echo "<script>\n";
				echo "function confirmDelete(delPhoto) {\n";
				echo "  if (confirm(\"Are you sure you want to delete this photo?\")) {\n";
				echo "	document.location = \"". admin_url("admin.php?page=". $_GET['page']) ."&action=editRecord&ProfileID=". $ProfileID ."&actionsub=photodelete&targetid=\"+delPhoto;\n";
				echo "  }\n";
				echo "}\n";
				echo "</script>\n";
				
				// Are we deleting?
				if ($_GET["actionsub"] == "photodelete") {
					$deleteTargetID = $_GET["targetid"];
					
					// Verify Record
					$queryImgConfirm = "SELECT * FROM ". table_agency_profile_media ." WHERE ProfileID =  \"". $ProfileID ."\" AND ProfileMediaID =  \"". $deleteTargetID ."\"";
					$resultsImgConfirm = mysql_query($queryImgConfirm);
					$countImgConfirm = mysql_num_rows($resultsImgConfirm);
					while ($dataImgConfirm = mysql_fetch_array($resultsImgConfirm)) {
						$ProfileMediaID = $dataImgConfirm['ProfileMediaID'];
						$ProfileMediaType = $dataImgConfirm['ProfileMediaType'];
						$ProfileMediaURL = $dataImgConfirm['ProfileMediaURL'];
						
						// Remove Record
						$delete = "DELETE FROM " . table_agency_profile_media . " WHERE ProfileID =  \"". $ProfileID ."\" AND ProfileMediaID=$ProfileMediaID";
						$results = $wpdb->query($delete);
						
						if ($ProfileMediaType == "Demo Reel" || $ProfileMediaType == "Video Monologue" || $ProfileMediaType == "Video Slate") {
							// Nothing to Remove
						} else {
							// Remove File
							$dirURL = rb_agency_UPLOADPATH . $ProfileGallery;
							if (!unlink($dirURL ."/". $ProfileMediaURL)) {
							  echo ("<div id=\"message\" class=\"error\"><p>". __("Error removing", rb_agencyinteract_TEXTDOMAIN) ." <strong>". $ProfileMediaURL ."</strong>. ". __("Please try again", rb_agencyinteract_TEXTDOMAIN) .".</p></div>");
							} else {
							  echo ("<div id=\"message\" class=\"updated\"><p>File <strong>'. $ProfileMediaURL .'</strong> ". __("successfully removed", rb_agencyinteract_TEXTDOMAIN) .".</p></div>");
							}
						}
					} // is there record?
				}
				// Go about our biz-nazz
					$queryImg = "SELECT * FROM ". table_agency_profile_media ." WHERE ProfileID =  \"". $ProfileID ."\" AND ProfileMediaType = \"Image\" ORDER BY ProfileMediaPrimary DESC, ProfileMediaID DESC";
					$resultsImg = mysql_query($queryImg);
					$countImg = mysql_num_rows($resultsImg);
					while ($dataImg = mysql_fetch_array($resultsImg)) {
					  if ($dataImg['ProfileMediaPrimary']) {
						  $styleBackground = "#900000";
						  $isChecked = " checked";
						  $isCheckedText = " Primary";
						  $toDelete = "";
					  } else {
						  $styleBackground = "#000000";
						  $isChecked = "";
						  $isCheckedText = " Select";
						  $toDelete = "  <div class=\"delete\"><a href=\"javascript:confirmDelete('". $dataImg['ProfileMediaID'] ."')\"><span>Delete</span> &raquo;</a></div>\n";
					  }
						echo "<div class=\"profileimage\" style=\"background: ". $styleBackground ."; \">\n". $toDelete ."";
						echo "  <img src=\"". rb_agency_UPLOADDIR . $ProfileGallery ."/". $dataImg['ProfileMediaURL'] ."\" style=\"width: 100px; z-index: 1; \" />\n";
						echo "  <div class=\"primary\" style=\"background: ". $styleBackground ."; \"><input type=\"radio\" name=\"ProfileMediaPrimary\" value=\"". $dataImg['ProfileMediaID'] ."\" class=\"button-primary\"". $isChecked ." /> ". $isCheckedText ."</div>\n";
						echo "</div>\n";
					}
					if ($countImg < 1) {
						echo "<div>". __("There are no images loaded for this profile yet.", rb_agencyinteract_TEXTDOMAIN) ."</div>\n";
					}
					
		echo "		<div style=\"clear: both;\"></div>\n";
		echo "		<h3>". __("Media", rb_agencyinteract_TEXTDOMAIN) ."</h3>\n";
		echo "		<p>". __("The following downloadable files (pdf, audio file, etc.) are associated with this record", rb_agencyinteract_TEXTDOMAIN) .".</p>\n";
	
					$queryMedia = "SELECT * FROM ". table_agency_profile_media ." WHERE ProfileID =  \"". $ProfileID ."\" AND ProfileMediaType <> \"Image\"";
					$resultsMedia = mysql_query($queryMedia);
					$countMedia = mysql_num_rows($resultsMedia);
					while ($dataMedia = mysql_fetch_array($resultsMedia)) {
						if ($dataMedia['ProfileMediaType'] == "Demo Reel" || $dataMedia['ProfileMediaType'] == "Video Monologue" || $dataMedia['ProfileMediaType'] == "Video Slate") {
							echo "<div style=\"float: left; width: 120px; text-align: center; padding: 10px; \">". $dataMedia['ProfileMediaType'] ."<br />". rb_agency_get_videothumbnail($dataMedia['ProfileMediaURL']) ."<br /><a href=\"http://www.youtube.com/watch?v=". $dataMedia['ProfileMediaURL'] ."\" target=\"_blank\">Link to Video</a><br />[<a href=\"javascript:confirmDelete('". $dataMedia['ProfileMediaID'] ."')\">DELETE</a>]</div>\n";
						} else { //if ($dataMedia['ProfileMediaType'] == "") 
							echo "<div>". $dataMedia['ProfileMediaType'] .": <a href=\"". rb_agency_UPLOADDIR . $ProfileGallery ."/". $dataMedia['ProfileMediaURL'] ."\" target=\"_blank\">". $dataMedia['ProfileMediaTitle'] ."</a> [<a href=\"javascript:confirmDelete('". $dataMedia['ProfileMediaID'] ."')\">DELETE</a>]</div>\n";
						}
					}
					if ($countMedia < 1) {
						echo "<div><em>". __("There is not any additional media linked", rb_agencyinteract_TEXTDOMAIN) ."</em></div>\n";
					}
		echo "		<div style=\"clear: both;\"></div>\n";
		echo "		<h3>". __("Upload", rb_agencyinteract_TEXTDOMAIN) ."</h3>\n";
		echo "		<p>". __("Upload new media using the forms below", rb_agencyinteract_TEXTDOMAIN) .".</p>\n";
	
				for( $i=1; $i<10; $i++ ) {
				echo "<div>Type: <select name=\"profileMedia". $i ."Type\"><option>Image</option><option>Headshot</option><option>Comp Card</option><option>Resume</option><option>Voice Demo</option></select><input type='file' id='profileMedia". $i ."' name='profileMedia". $i ."' /></div>\n";
				}
		echo "		<p>". __("Paste the YouTube video URL below", rb_agencyinteract_TEXTDOMAIN) .".</p>\n";
	
				echo "<div>Type: <select name=\"profileMediaV1Type\"><option selected>". __("Video Slate", rb_agencyinteract_TEXTDOMAIN) ."</option><option>". __("Video Monologue", rb_agencyinteract_TEXTDOMAIN) ."</option><option>". __("Demo Reel", rb_agencyinteract_TEXTDOMAIN) ."</option></select><textarea id='profileMediaV1' name='profileMediaV1'></textarea></div>\n";
				echo "<div>Type: <select name=\"profileMediaV2Type\"><option>". __("Video Slate", rb_agencyinteract_TEXTDOMAIN) ."</option><option selected>". __("Video Monologue", rb_agencyinteract_TEXTDOMAIN) ."</option><option>". __("Demo Reel", rb_agencyinteract_TEXTDOMAIN) ."</option></select><textarea id='profileMediaV2' name='profileMediaV2'></textarea></div>\n";
				echo "<div>Type: <select name=\"profileMediaV3Type\"><option>". __("Video Slate", rb_agencyinteract_TEXTDOMAIN) ."</option><option>". __("Video Monologue", rb_agencyinteract_TEXTDOMAIN) ."</option><option selected>". __("Demo Reel", rb_agencyinteract_TEXTDOMAIN) ."</option></select><textarea id='profileMediaV3' name='profileMediaV3'></textarea></div>\n";
		
			}
	
		echo "<p class=\"submit\">\n";
		echo "     <input type=\"hidden\" name=\"ProfileID\" value=\"". $ProfileID ."\" />\n";
		echo "     <input type=\"hidden\" name=\"ProfileGallery\" value=\"". $ProfileGallery ."\" />\n";
		echo "     <input type=\"hidden\" name=\"action\" value=\"editRecord\" />\n";
		echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_restaurant_TEXTDOMAIN) ."\" class=\"button-primary\" />\n";
		echo "</p>\n";
		echo "</form>\n";
	}
?>