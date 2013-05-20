<?php
	global $user_ID; 
	global $current_user;
	get_currentuserinfo();
	$ProfileUserLinked = $current_user->id;

	$query1 = "SELECT * FROM " . table_agency_profile . " WHERE ProfileUserLinked='$ProfileUserLinked'";
	$results1 = mysql_query($query1) or die ( __("Error, query failed", rb_agencyinteract_TEXTDOMAIN ));
	$count1 = mysql_num_rows($results1);
	if($count1 > 1);
	while ($data = mysql_fetch_array($results1)) {
		$ProfileID					=$data['ProfileID'];
		$ProfileUserLinked			=$data['ProfileUserLinked'];
		$ProfileGallery				=stripslashes($data['ProfileGallery']);
		            echo "<form id=\"deletePost\"  name=\"deletePost\" action=\"". get_bloginfo("wpurl") ."/profile-member/media/\" method=\"post\">";
				echo " <input type=\"hidden\" name=\"ProfileID\" value=\"".$ProfileID."\" />";
				echo " <input type=\"hidden\" name=\"targetid\" id=\"targetid\" value=\"\" />";
				echo " <input type=\"hidden\" name=\"actionsub\" value=\"photodelete\" />";
				echo "</form>";
                      

		echo "<form method=\"post\" enctype=\"multipart/form-data\" action=\"". get_bloginfo("wpurl") ."/profile-member/media/\">\n";
	
			if ( !empty($ProfileID) && ($ProfileID > 0) ) { // Editing Record
		echo "	<div class=\"manage-section gallery\">\n";
		if(!empty($UploadMedia))
		{
			echo "<div id=\"message\" class=\"uploaded\">";
			foreach ($UploadMedia as $mediaFile) {
			
			echo "<p>File <strong>".$mediaFile. "</strong> successfully uploaded!</p>";
			
			}
			echo "<p>You may continue uploading more files. If you are done, please click the EXIT link below to go back to homepage.</p>";
			$back = $rb_agencyinteract_WPURL ."/profile-member/";
			echo '<p><a class="rb_button" href='.$back.'>EXIT</a></p>';
			echo "</div>";
		}
		
		echo "	<h3>". __("Gallery", rb_agencyinteract_TEXTDOMAIN) ."</h3>\n";
				
				echo "<script type=\"text/javascript\">\n";
				echo "function confirmDelete(delMedia,mediaType) {\n";
				echo "  if (confirm(\"".__("Are you sure you want to delete this", rb_agencyinteract_TEXTDOMAIN) ." \"+mediaType+\"?\")) {\n";
				//echo "         document.getElementById('deletePost').submit();   \n";
				echo "         document.getElementById('targetid').value=delMedia;";
				echo "         document.deletePost.submit();   \n";
				//echo "	document.location = \"?&action=editRecord&ProfileID=". $ProfileID ."&actionsub=photodelete&targetid=\"+delMedia;\n";
				echo "  }\n";
				echo "}\n";
				echo "</script>\n";
				
				// Are we deleting?
				if ($_POST["actionsub"] == "photodelete") {
					$deleteTargetID = $_POST["targetid"];
					
					// Verify Record
					$queryImgConfirm = "SELECT * FROM ". table_agency_profile_media ." WHERE ProfileID =  \"". $ProfileID ."\" AND ProfileMediaID =  \"". $deleteTargetID ."\"";
					$resultsImgConfirm = mysql_query($queryImgConfirm);
					$countImgConfirm = mysql_num_rows($resultsImgConfirm);
					while ($dataImgConfirm = mysql_fetch_array($resultsImgConfirm)) {
						$ProfileMediaID = $dataImgConfirm['ProfileMediaID'];
						$ProfileMediaType = $dataImgConfirm['ProfileMediaType'];
						$ProfileMediaURL = $dataImgConfirm['ProfileMediaURL'];
						
					
						
						if ($ProfileMediaType == "Demo Reel" || $ProfileMediaType == "Video Monologue" || $ProfileMediaType == "Video Slate") {
								  echo ("<div id=\"message\" class=\"updated\"><p>File <strong>'. $ProfileMediaURL .'</strong> ". __("successfully removed", rb_agencyinteract_TEXTDOMAIN) .".</p></div>");
						} else {
							// Remove File
							$dirURL = rb_agency_UPLOADPATH . $ProfileGallery; 
							if (!unlink($dirURL ."/". $ProfileMediaURL)) {
							  echo ("<div id=\"message\" class=\"error\"><p>". __("Error removing", rb_agencyinteract_TEXTDOMAIN) ." <strong>". $ProfileMediaURL ."</strong>. ". __("Please try again", rb_agencyinteract_TEXTDOMAIN) .".</p></div>");
							} else {
							  echo ("<div id=\"message\" class=\"updated\"><p>File <strong>'. $ProfileMediaURL .'</strong> ". __("successfully removed", rb_agencyinteract_TEXTDOMAIN) .".</p></div>");
							}
						}
							// Remove Record
						$delete = "DELETE FROM " . table_agency_profile_media . " WHERE ProfileID =  \"". $ProfileID ."\" AND ProfileMediaID=$ProfileMediaID";
						$results = $wpdb->query($delete);
					} // is there record?
				}
				// Go about our biz-nazz
					$queryImg = "SELECT * FROM ". table_agency_profile_media ." WHERE ProfileID =  \"". $ProfileID ."\" AND ProfileMediaType = \"Image\" ORDER BY ProfileMediaPrimary DESC, ProfileMediaID DESC";
					$resultsImg = mysql_query($queryImg);
					$countImg = mysql_num_rows($resultsImg);
					while ($dataImg = mysql_fetch_array($resultsImg)) {
					  if ($dataImg['ProfileMediaPrimary']) {
						  $styleClass = "primary-picture";
						  $isChecked = " checked";
						  $isCheckedText = " Primary";
						  $toDelete = "";
					  } else {
						  $styleBackground = "#000000";
						  $isChecked = "";
						  $isCheckedText = " Select";
						  $toDelete = "  <div class=\"delete\"><a href=\"javascript:;\" class=\"btn-small-red\" onclick=\"confirmDelete('". $dataImg['ProfileMediaID'] ."','".$dataImg['ProfileMediaType']."');\"><span>Delete</span> &raquo;</a></div>\n";
					  }
						echo "<div class=\"profileimage\" class=\"". $styleClass ."\">\n". $toDelete ."";
                        
						echo '<input type="hidden" name="pgallery" value="'.$ProfileGallery.'">';
						
						echo '<input type="hidden" name="pmedia_url" value="'.$dataImg['ProfileMediaURL'].'">';					

						echo "  <img src=\"". rb_agency_UPLOADDIR . $ProfileGallery ."/". $dataImg['ProfileMediaURL'] ."\" style=\"width: 100px; z-index: 1; \" />\n";
						echo "  <div class=\"". $styleClass ." primary rb_button\"><label><input type=\"radio\" name=\"ProfileMediaPrimary\" value=\"". $dataImg['ProfileMediaID'] ."\" class=\"button-primary\"". $isChecked ." /> ". $isCheckedText ."</label></div>\n";

						echo "</div>\n";
					}
					if ($countImg < 1) {
						echo "<p>". __("There are no images loaded for this profile yet.", rb_agencyinteract_TEXTDOMAIN) ."</p>\n";
					}
					
		echo "		</div>\n";
		echo "	<div class=\"manage-section media\">\n";
		echo "		<h3>". __("Media", rb_agencyinteract_TEXTDOMAIN) ."</h3>\n";
		echo "		<p>". __("The following files (pdf, audio file, etc.) are associated with this record", rb_agencyinteract_TEXTDOMAIN) .".</p>\n";
	
					$queryMedia = "SELECT * FROM ". table_agency_profile_media ." WHERE ProfileID =  \"". $ProfileID ."\" AND ProfileMediaType <> \"Image\"";
					$resultsMedia = mysql_query($queryMedia);
					$countMedia = mysql_num_rows($resultsMedia);
					while ($dataMedia = mysql_fetch_array($resultsMedia)) {
						if ($dataMedia['ProfileMediaType'] == "Demo Reel" || $dataMedia['ProfileMediaType'] == "Video Monologue" || $dataMedia['ProfileMediaType'] == "Video Slate") {
							$outVideoMedia .= "<div class=\"media-video\">". $dataMedia['ProfileMediaType'] ."<br />". rb_agency_get_videothumbnail($dataMedia['ProfileMediaURL']) ."<br /><a href=\"http://www.youtube.com/watch?v=". $dataMedia['ProfileMediaURL'] ."\" target=\"_blank\">Link to Video</a><br />[<a href=\"javascript:confirmDelete('". $dataMedia['ProfileMediaID'] ."','".$dataMedia['ProfileMediaType']."')\">DELETE</a>]</div>\n";
						} elseif ($dataMedia['ProfileMediaType'] == "Voice Demo") {
							$outLinkVoiceDemo .= "<div>". $dataMedia['ProfileMediaType'] .": <a href=\"". rb_agency_UPLOADDIR . $ProfileGallery ."/". $dataMedia['ProfileMediaURL'] ."\" target=\"_blank\">". $dataMedia['ProfileMediaTitle'] ."</a> [<a href=\"javascript:confirmDelete('". $dataMedia['ProfileMediaID'] ."','".$dataMedia['ProfileMediaType']."')\">DELETE</a>]</div>\n";
						}
						 elseif ($dataMedia['ProfileMediaType'] == "Resume") {
							$outLinkResume .= "<div>". $dataMedia['ProfileMediaType'] .": <a href=\"". rb_agency_UPLOADDIR . $ProfileGallery ."/". $dataMedia['ProfileMediaURL'] ."\" target=\"_blank\">". $dataMedia['ProfileMediaTitle'] ."</a> [<a href=\"javascript:confirmDelete('". $dataMedia['ProfileMediaID'] ."','".$dataMedia['ProfileMediaType']."')\">DELETE</a>]</div>\n";
						}
						 elseif ($dataMedia['ProfileMediaType'] == "Headshot") {
							$outLinkHeadShot .= "<div>". $dataMedia['ProfileMediaType'] .": <a href=\"". rb_agency_UPLOADDIR . $ProfileGallery ."/". $dataMedia['ProfileMediaURL'] ."\" target=\"_blank\">". $dataMedia['ProfileMediaTitle'] ."</a> [<a href=\"javascript:confirmDelete('". $dataMedia['ProfileMediaID'] ."','".$dataMedia['ProfileMediaType']."')\">DELETE</a>]</div>\n";
						}elseif ($dataMedia['ProfileMediaType'] == "CompCard") {
							$outLinkComCard .= "<div>". $dataMedia['ProfileMediaType'] .": <a href=\"". rb_agency_UPLOADDIR . $ProfileGallery ."/". $dataMedia['ProfileMediaURL'] ."\" target=\"_blank\">". $dataMedia['ProfileMediaTitle'] ."</a> [<a href=\"javascript:confirmDelete('". $dataMedia['ProfileMediaID'] ."','".$dataMedia['ProfileMediaType']."')\">DELETE</a>]</div>\n";
						}else{
							$outCustomMediaLink .= "<div>". $dataMedia['ProfileMediaType'] .": <a href=\"". rb_agency_UPLOADDIR . $ProfileGallery ."/". $dataMedia['ProfileMediaURL'] ."\" target=\"_blank\">". $dataMedia['ProfileMediaTitle'] ."</a> [<a href=\"javascript:confirmDelete('". $dataMedia['ProfileMediaID'] ."','".$dataMedia['ProfileMediaType']."')\">DELETE</a>]</div>\n";
						}
					}
					echo '<p>';
					echo $outLinkVoiceDemo;
					echo '</p>';
					echo '<p>';
					echo $outLinkResume;
					echo '</p>';
					echo '<p>';
					echo $outLinkHeadShot;
					echo '</p>';
					echo '<p>';
					echo $outLinkComCard;
					echo '</p>';
					echo '<p>';
					echo $outCustomMediaLink;
					echo '</p>';
					echo $outVideoMedia;
					if ($countMedia < 1) {
						echo "<p><em>". __("There are no additional media linked", rb_agencyinteract_TEXTDOMAIN) ."</em></p>\n";
					}
		echo "		</div>\n";
		echo "	<div class=\"manage-section upload\">\n";
		echo "		<h3>". __("Upload Media Files", rb_agencyinteract_TEXTDOMAIN) ."</h3>\n";
		echo "		<p>". __("Upload new media using the forms below. The following formats are available: jpg, png, mp3, and pdf. If uploading an mp3 for a voice monolouge, use the  \"Voice Demo\" option. For Resumes, make sure the file is a PDF ", rb_agencyinteract_TEXTDOMAIN) .".</p>\n";
	
				for( $i=1; $i<10; $i++ ) {
				echo "<div><label>Type: </label><select name=\"profileMedia". $i ."Type\"><option value=\"Image\">Image</option><option value=\"Headshot\">Headshot</option><option value=\"CompCard\">Comp Card</option><option>Resume</option><option>Voice Demo</option>"; rb_agency_getMediaCategories($data['ProfileGender']); echo"</select><input type='file' id='profileMedia". $i ."' name='profileMedia". $i ."' /></div>\n";
				}
		echo "		<p>". __("Paste the video URL below", rb_agencyinteract_TEXTDOMAIN) .".</p>\n";
	
				echo "<div><label>Type: </label><select name=\"profileMediaV1Type\"><option selected>". __("Video Slate", rb_agencyinteract_TEXTDOMAIN) ."</option><option>". __("Video Monologue", rb_agencyinteract_TEXTDOMAIN) ."</option><option>". __("Demo Reel", rb_agencyinteract_TEXTDOMAIN) ."</option></select><textarea id='profileMediaV1' name='profileMediaV1'></textarea></div>\n";
				echo "<div><label>Type: </label><select name=\"profileMediaV2Type\"><option>". __("Video Slate", rb_agencyinteract_TEXTDOMAIN) ."</option><option selected>". __("Video Monologue", rb_agencyinteract_TEXTDOMAIN) ."</option><option>". __("Demo Reel", rb_agencyinteract_TEXTDOMAIN) ."</option></select><textarea id='profileMediaV2' name='profileMediaV2'></textarea></div>\n";
				echo "<div><label>Type: </label><select name=\"profileMediaV3Type\"><option>". __("Video Slate", rb_agencyinteract_TEXTDOMAIN) ."</option><option>". __("Video Monologue", rb_agencyinteract_TEXTDOMAIN) ."</option><option selected>". __("Demo Reel", rb_agencyinteract_TEXTDOMAIN) ."</option></select><textarea id='profileMediaV3' name='profileMediaV3'></textarea></div>\n";
		
			}
		echo "<p><strong>Press the \"Save and Continue\" button only once</strong>. Depending on the number of files and or your connection speed, it may take a few moments to fully upload your new files/changes. When the page refreshes, you should see your new media.</p>\n";
		echo "		</div>\n";
		echo "<p class=\"submit\">\n";
		echo "     <input type=\"hidden\" name=\"ProfileID\" value=\"". $ProfileID ."\" />\n";
		echo "     <input type=\"hidden\" name=\"ProfileGallery\" value=\"". $ProfileGallery ."\" />\n";
		echo "     <input type=\"hidden\" name=\"action\" value=\"editRecord\" />\n";
		echo "     <input type=\"submit\" name=\"submit\" value=\"". __("Save and Continue", rb_restaurant_TEXTDOMAIN) ."\" class=\"button-primary\" onClick=\"this.value = 'Please Wait...'\"/>\n";
		echo "</p>\n";
		echo "</form>\n";
	}
?>