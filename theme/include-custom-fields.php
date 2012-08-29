<?php

			$query1 = "SELECT ProfileCustomID, ProfileCustomTitle, ProfileCustomType, ProfileCustomOptions, ProfileCustomOrder, ProfileCustomView, ProfileCustomShowGender, ProfileCustomShowProfile, ProfileCustomShowSearch, ProfileCustomShowLogged, ProfileCustomShowAdmin,ProfileCustomShowRegistration FROM ". table_agency_customfields ." WHERE ProfileCustomView = ".($ProfileInformation+0)." ORDER BY ProfileCustomOrder ASC";

								$results1 = mysql_query($query1);

								$count1 = mysql_num_rows($results1);

								$pos = 0;

			

			$query2 = "SELECT ProfileGender,ProfileUserLinked  FROM ".table_agency_profile." WHERE ProfileUserLinked = '".rb_agency_get_current_userid()."' ";

								$results2 = mysql_query($query2);

							      $dataList2 = mysql_fetch_assoc($results2); 

								$count2 = mysql_num_rows($results2);

								

		

		while ($data1 = mysql_fetch_array($results1)) { 

		       

							

							if($data1["ProfileCustomShowGender"] == $dataList2["ProfileGender"] && $count2 >=1 && !empty($data1["ProfileCustomShowRegistration"]) || !empty($data1["ProfileCustomShowAdmin"]) || !empty($data1["ProfileCustomShowLogged"]) || !empty($data1["ProfileCustomShowProfile"]) || !empty($data1["ProfileCustomShowSearch"])){ // Depends on Current LoggedIn User's Gender

								
                                                     
									 include("view-custom-fields.php");

								

							}elseif(empty($data1["ProfileCustomShowGender"]) && !empty($data1["ProfileCustomShowRegistration"]) || !empty($data1["ProfileCustomShowAdmin"]) || !empty($data1["ProfileCustomShowLogged"]) || !empty($data1["ProfileCustomShowProfile"]) || !empty($data1["ProfileCustomShowSearch"])){


									include("view-custom-fields.php");

							}

						

		 }

			

				

   

?>