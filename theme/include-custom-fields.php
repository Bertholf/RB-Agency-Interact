<?php
		$query2 = "SELECT ProfileGender,ProfileUserLinked  FROM ".table_agency_profile." WHERE ProfileUserLinked = '".rb_agency_get_current_userid()."' ";
			$results2 = mysql_query($query2);
		    $dataList2 = mysql_fetch_assoc($results2); 
			$count2 = mysql_num_rows($results2);

			$ProfileGender = $dataList2["ProfileGender"]

		$query1 = "SELECT ProfileCustomID, ProfileCustomTitle, ProfileCustomType, ProfileCustomOptions, ProfileCustomOrder, ProfileCustomView, ProfileCustomShowGender, ProfileCustomShowProfile, ProfileCustomShowSearch, ProfileCustomShowLogged, ProfileCustomShowAdmin,ProfileCustomShowRegistration FROM ". table_agency_customfields ." WHERE ProfileCustomView = ". $ProfileInformation ." ORDER BY ProfileCustomOrder ASC";
			$results1 = mysql_query($query1);
			$count1 = mysql_num_rows($results1);
			$pos = 0;

		while ($data1 = mysql_fetch_array($results1)) { 
			if ( ($data1["ProfileCustomShowGender"] == $ProfileGender) || ($data1["ProfileCustomShowGender"] = 0) ) {
				// Yes, its the same gender, show it:
				include("view-custom-fields.php");
			//if($data1["ProfileCustomShowGender"] == $dataList2["ProfileGender"] && $count2 >=1 && !empty($data1["ProfileCustomShowRegistration"]) || !empty($data1["ProfileCustomShowAdmin"]) || !empty($data1["ProfileCustomShowLogged"]) || !empty($data1["ProfileCustomShowProfile"]) || !empty($data1["ProfileCustomShowSearch"])){ // Depends on Current LoggedIn User's Gender
			//} elseif(empty($data1["ProfileCustomShowGender"]) && !empty($data1["ProfileCustomShowRegistration"]) || !empty($data1["ProfileCustomShowAdmin"]) || !empty($data1["ProfileCustomShowLogged"]) || !empty($data1["ProfileCustomShowProfile"]) || !empty($data1["ProfileCustomShowSearch"])){
			//	include("view-custom-fields.php");
			}
		 }

?>