<?php 

echo $rb_header = RBAgency_Common::rb_header();
session_start();
?>


<?php



if(!isset($_GET['status'])){

	
	$status = '';
	$amt = '';
	$cc = '';
	foreach($_GET as $k=>$v){
		if($k == 'st'){
			$status = urldecode($v);
		}
		if($k == 'amt'){
			$amt = urldecode($v);
		}
		if($k == 'cc'){
			$cc = urldecode($v);
		}
	}
	

	
	$user_id = $current_user->ID;
	global $wpdb;

	if(!empty($_SESSION['S2MEMBER_user_payment_session'])){
		$user_role = urldecode($_GET['cm']);
		wp_update_user( array ('ID' => $user_id, 'role' => $user_role ) ) ;
		unset($_SESSION['S2MEMBER_user_payment_session']);

		$rbagency_use_s2member_option = get_option('rbagency_use_s2member');
		$rbagency_message_after_payment = get_option('rbagency_message_after_payment');

		if($rbagency_use_s2member_option == true){
			if(!empty($rbagency_message_after_payment)){
				echo nl2br($rbagency_message_after_payment);
			}else{
					echo "<h2>".__("Registration Complete!",RBAGENCY_interact_TEXTDOMAIN)."</h2>";
					echo "".__("Your account is currently pending for approval.",RBAGENCY_interact_TEXTDOMAIN)."";
					echo "".__("We will email you once your account is approved.",RBAGENCY_interact_TEXTDOMAIN)."";
			}
		}
	}else{
		echo '<h2>Unable to complete your registration.</h2>';
	}

	$sql = "SELECT * FROM wp_usermeta WHERE user_id = $user_id";
	$r = $wpdb->get_results($sql);
	//echo "<pre>";
	//print_r($r);
	//echo "</pre>";
	//echo "<a href='".get_bloginfo("url")."/registration-success/?status=pending' style='background-color: rgb(0, 0, 0); color: rgb(255, 255, 255); padding: 10px;'>View My Profile</a>";
	//echo "<a href='".get_bloginfo("url")."/registration-success/?status=pending' style='margin-left:10px;background-color: rgb(0, 0, 0); color: rgb(255, 255, 255); padding: 10px;'>Manage Account</a>";
	//echo "<a href='".get_bloginfo("url")."' style='margin-left:10px;background-color: rgb(0, 0, 0); color: rgb(255, 255, 255); padding: 10px;'>Logout</a>";
}else{
	echo '<h2>Your account is pending for approval. We will send you an email once your account is approved.</h2><br/>';
}


?>

<?php

echo $rb_footer = RBAgency_Common::rb_footer();

?>