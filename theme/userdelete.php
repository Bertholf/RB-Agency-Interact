<?php

/*
 * Delete Current User Passed
 * @parm User Id
 */

require_once('../../../../wp-load.php');
require_once('../../../../wp-admin/includes/user.php');
 
    global $wpdb;
 
	$id = (int) $_POST['ID'];
	$option = (int) $_POST['OPT'];
	$user = new WP_User( $id );
	$reactivate = (int) $_POST["REACTIVATE"];
	$userID = (int) $_POST["USERID"];
    
	if($option == 3){

		if($reactivate > 0){ //reactivate
			$update = "UPDATE ". table_agency_profile ." SET ProfileIsActive = 1 WHERE ProfileID = " . $id;
			$results = $wpdb->query($update);
		}else{
			$update = "UPDATE ". table_agency_profile ." SET ProfileIsActive = 2 WHERE ProfileID = " . $id;
			$results = $wpdb->query($update);
			wp_logout();
		}
        
		
		
		

	} elseif($option == 2) {

			// allow for transaction statement
			wp_delete_user( $userID);
			do_action('delete_user', $userID);
			$sql = "DELETE FROM ".table_agency_profile." WHERE ProfileID = ".$id;
			$wpdb->query($sql);

			$sql = "DELETE FROM ".table_agency_profile_media." WHERE ProfileID = ".$id;
			$wpdb->query($sql);

			$sql = "DELETE FROM ".table_agency_customfield_mux." WHERE ProfileID = ".$id;
			$wpdb->query($sql);


			
	}
 	echo $option;
  
?>
