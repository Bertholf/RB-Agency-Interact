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
	$userID = (int) $_POST['USERID']
    
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
			do_action('delete_user', $userID);

			if ( 'novalue' === $reassign || null === $reassign ) {
				$post_types_to_delete = array();
				foreach ( get_post_types( array(), 'objects' ) as $post_type ) {
					if ( $post_type->delete_with_user ) {
						$post_types_to_delete[] = $post_type->name;
					} elseif ( null === $post_type->delete_with_user && post_type_supports( $post_type->name, 'author' ) ) {
						$post_types_to_delete[] = $post_type->name;
					}
				}

				$post_types_to_delete = apply_filters( 'post_types_to_delete_with_user', $post_types_to_delete, $userID );
				$post_types_to_delete = implode( "', '", $post_types_to_delete );
				$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d AND post_type IN ('$post_types_to_delete')", $userID ) );
				if ( $post_ids ) {
					foreach ( $post_ids as $post_id )
						wp_delete_post( $post_id );
				}

				// Clean links
				$link_ids = $wpdb->get_col( $wpdb->prepare("SELECT link_id FROM $wpdb->links WHERE link_owner = %d", $userID) );

				if ( $link_ids ) {
					foreach ( $link_ids as $link_id )
						wp_delete_link($link_id);
				}
			} else {
				$reassign = (int) $reassign;
				$wpdb->update( $wpdb->posts, array('post_author' => $reassign), array('post_author' => $userID) );
				$wpdb->update( $wpdb->links, array('link_owner' => $reassign), array('link_owner' => $userID) );
			}

			// delete user
			$meta = $wpdb->get_col( $wpdb->prepare( "SELECT umeta_id FROM $wpdb->usermeta WHERE user_id = %d", $userID ) );
			foreach ( $meta as $mid )
				delete_metadata_by_mid( 'user', $mid );

			$wpdb->delete( $wpdb->users, array( 'ID' => $userID ) );

			clean_user_cache( $user );

			// allow for commit transaction
			do_action('deleted_user', $userID);
	}
 	echo $option;
  
?>
