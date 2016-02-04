<?php session_start();

//echo $rb_footer = RBAgency_Common::rb_header();

//$parseV = explode("---",$_GET["v"]);
$password = urldecode($_GET["v"]);
$user_id = $_GET["u"];

$_SESSION["uid"] = $user_id;
$_SESSION["password"] = $password;

wp_redirect(site_url()."/user-membership-page");
exit();

?>
