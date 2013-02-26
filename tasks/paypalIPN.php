<?php
include_once('../../../../wp-config.php');
$conn = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
mysql_select_db(DB_NAME,$conn);
global $wpdb;
// Revision Notes
// 11/04/11 - changed post back url from https://www.paypal.com/cgi-bin/webscr to https://ipnpb.paypal.com/cgi-bin/webscr
// For more info see below:
// https://www.x.com/content/bulletin-ip-address-expansion-paypal-services
// "ACTION REQUIRED: if you are using IPN (Instant Payment Notification) for Order Management and your IPN listener script is behind a firewall that uses ACL (Access Control List) rules which restrict outbound traffic to a limited number of IP addresses, then you may need to do one of the following: 
// To continue posting back to https://www.paypal.com  to perform IPN validation you will need to update your firewall ACL to allow outbound access to *any* IP address for the servers that host your IPN script
// OR Alternatively, you will need to modify  your IPN script to post back IPNs to the newly created URL https://ipnpb.paypal.com using HTTPS (port 443) and update firewall ACL rules to allow outbound access to the ipnpb.paypal.com IP ranges (see end of message)."

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Host: ipnpb.paypal.com:443\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://ipnpb.paypal.com', 443, $errno, $errstr, 30);
// assign posted variables to local variables
$SubscriptionRateTitle 	= $_POST['item_name'];
$SubscriptionRateID 	= $_POST['item_number'];
$ProfileID			 	= $_POST['custom'];
$SubscriberPurchasePrice= $_POST['mc_gross'];
$payment_status 		= $_POST['payment_status'];
$payment_currency 		= $_POST['mc_currency'];
$txn_id 				= $_POST['txn_id'];
$receiver_email 		= $_POST['receiver_email'];
$payer_email 			= $_POST['payer_email']; // ProfileContactEmail
if (!$fp) {
	// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email

			// Does the user already have an account?
			$lookup = "SELECT * FROM ". table_agencyinteract_subscription ." WHERE SubscriptionRateID = ". $SubscriptionRateID ." LIMIT 1";
			$results = $wpdb->get_results($lookup);
			if ($results) {
			  foreach ($results as $result) {
				$SubscriberDateStart = $result->SubscriberDateExpire;
			  }
			} else {
				// No Account, Today is the start day
				$SubscriberDateStart = date("Y-m-d"); // current date
			}
			// check that payment_amount/payment_currency are correct
			// Find the SubscriptionRateID
			$lookup = "SELECT SubscriptionRateTerm, SubscriptionRatePrice FROM ". table_agencyinteract_subscription_rates ." WHERE SubscriptionRateID = ". $SubscriptionRateID ." LIMIT 1";
			$results = $wpdb->get_results($lookup);
			if ($results) {
				foreach ($results as $result) {
					$SubscriptionRateTerm = $result->SubscriptionRateTerm;
					$SubscriberDateExpire = date("Y-m-d", strtotime(date("Y-m-d", strtotime($SubscriberDateStart)) . " +". $SubscriptionRateTerm ." month"));
					$SubscriberPurchasePrice = $result->SubscriberPurchasePrice;
				}
			}
			
			$mail_From = "From: rob@bertholf.com";
			$mail_To = $receiver_email;
			$mail_Subject = "VERIFIED IPN";
			$mail_Body = $req;
			
			foreach ($_POST as $key => $value){
				$emailtext .= $key . " = " .$value ."\n\n";
			}
			
			mail($mail_To, $mail_Subject, $emailtext . "\n\n" . $mail_Body, $mail_From);
			// I like pretty things
			$SubscriberPurchaseDetails = "";
			// Create Record of Payment
			$insert = "INSERT INTO " . table_agencyinteract_subscription . " (ProfileID,SubscriberDateExpire,SubscriptionRateID,SubscriberPurchasePrice,SubscriberPurchaseDetails) 
				VALUES ('" . $wpdb->escape($ProfileID) . "','" . $wpdb->escape($SubscriberDateExpire) . "','" . $wpdb->escape($SubscriptionRateID) . "','" . $wpdb->escape($SubscriberPurchasePrice) . "','" . $wpdb->escape($SubscriberPurchaseDetails) . "')";
			$results = $wpdb->query($insert);
			$SubscriberID = $wpdb->insert_id;
			
	mail($mail_To, $mail_Subject, $lookup ."<br />". $insert, $mail_From);
		
		
		} else if (strcmp ($res, "INVALID") == 0) {
		// log for manual investigation
		
			$mail_From = "From: rob@bertholf.com";
			$mail_To = $receiver_email;
			$mail_Subject = "INVALID IPN";
			$mail_Body = $req;
			
			foreach ($_POST as $key => $value){
				$emailtext .= $key . " = " .$value ."\n\n";
			}
			
			mail($mail_To, $mail_Subject, $emailtext . "\n\n" . $mail_Body, $mail_From);
		
		}
	} //while
	fclose ($fp);
}
?>
