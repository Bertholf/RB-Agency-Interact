<?php 
session_start();
echo $rb_header = RBAgency_Common::rb_header();


echo "<div id=\"main-content\" class=\"main-content col-sm-8\">";
echo "<div id=\"primary\" class=\"content-area\">";
echo "<div id=\"content\" class=\"site-content\" role=\"main\">";




echo "<h2 style=\" margin-top: 0px; margin-bottom: 10px; \"><h2>".__("Choose Membership Plan.",RBAGENCY_interact_TEXTDOMAIN) ."</h2>";




if(is_user_logged_in()){
	
	$token = md5(time() . rand());
	$_SESSION['S2MEMBER_user_payment_session'] = $token;
	
	echo "<ul style=\" -webkit-padding-start: 0px; \">";

		for($idx=1;$idx<4;$idx++){

			echo "<li style='float:left;margin-right:40px;list-style-type:none;'>";

				$paypal_code = get_option("subscription_paypal_btn_$idx");
				$subscription_type = get_option("subscription_type_$idx");
				$change = array(
					site_url()."/?s2member_paypal_return=1"
				);

				$change2 = array(
					'<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0; ?>', 
					'<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0; ?>',
					'<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1; ?>',
					'<?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1; ?>');

				
				$return_url = site_url()."/registration-success";
				$custom_field = '<input type="hidden" name="custom" value="s2member_level'.$idx.'">';
				$pcode = str_replace($change2, "", $paypal_code);		
				$pcode_pre = str_replace($change,$return_url,$pcode);
				$pcode_final = str_replace('</form>',$custom_field.'</form>',$pcode_pre);

				echo "<table>";
				echo "<tr>";
				echo "<td>".nl2br(get_option("subscription_title_$idx"))."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td style=\" margin-bottom: 15px; \">".nl2br(get_option("subscription_description_$idx"))."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>".$pcode_final."</td>";
				echo "</tr>";
				echo "</table>";

			echo "</li>";
		}
echo "</ul>";

	
}




echo "</div></div></div>";


echo $rb_footer = RBAgency_Common::rb_footer();

?>

