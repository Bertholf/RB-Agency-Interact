<?php 

echo $rb_header = RBAgency_Common::rb_header();
session_start();

$paypal_code = get_option('rbagency_paypal_button_code');

$change = array("http://79.170.40.242/petlondonmodels.com/?s2member_paypal_return=1");

$pcode = str_replace($change,site_url().'/registration-success',$paypal_code);
echo $pcode;


echo $rb_footer = RBAgency_Common::rb_footer();

?>

