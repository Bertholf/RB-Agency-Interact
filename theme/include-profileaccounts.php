<?php

if(isset($_POST['submit'])){

	$current = get_user_meta($data["ProfileID"],"otherAccountURLs_".$data["ProfileID"],true);

	if(!empty($current)){
		$otherURLS = [];
		for($idx=0;$idx<count($_POST["otherAccountURLs"]);$idx++){
			if(!empty($_POST["otherAccountURLs"][$idx])){
				$otherURLS[] = $_POST["otherAccountURLs"][$idx];
			}						
		}
		$accounts = $current."|".implode("|",$otherURLS);
	}else{
		$otherURLS = [];
		for($idx=0;$idx<count($_POST["otherAccountURLs"]);$idx++){
			if(!empty($_POST["otherAccountURLs"][$idx])){
				$otherURLS[] = $_POST["otherAccountURLs"][$idx];
			}						
		}
		$accounts = implode("|",$otherURLS);
	}
	

	update_user_meta($data["ProfileID"],"otherAccountURLs_".$data["ProfileID"],$accounts);
}

if(isset($_GET['del'])){
	$otherAccounts = get_user_meta($data["ProfileID"],"otherAccountURLs_".$data["ProfileID"],true);
	$otherAccountsArr = explode("|",$otherAccounts);
	$newOtherAccountsArr = [];
	$deletedAccount = "";
	foreach($otherAccountsArr as $otherAccount){
		if($_GET['del'] != $otherAccount){
			$newOtherAccountsArr[] = $otherAccount;
		}else{
			if(strpos($otherAccounts, $_GET['del']) >-1){
				$deletedAccount = $_GET['del'];
			}			
		}
	}
	//update new accounts
	update_user_meta($data["ProfileID"],"otherAccountURLs_".$data["ProfileID"],implode("|",$newOtherAccountsArr));
	
	if(!empty($deletedAccount)){
		echo "Account: ".$_GET['del']." successfully deleted!";
	}else{
		wp_redirect(site_url()."/profile-member/accounts/");
	}
	$deletedAccount = "";
	
}


?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".add-account-url-btn").click(function(event){
			event.preventDefault;
			jQuery("#other-account-url-wrapper").append("<input type='text' class='add-other-account-url-txt' name='otherAccountURLs[]' placeholder='Add URL Here' /><br/> ");
		});
	});
</script>
<form method="POST">
<div id="other-account-url-wrapper">
<input type="text" class="add-other-account-url-txt" name="otherAccountURLs[]" placeholder="Add URL Here"/><br/>
	
</div>	

<input type="button" class="add-account-url-btn" value="Add URL">
<input type="submit" name="submit" value="<?php echo __("Save URL",RBAGENCY_interact_TEXTDOMAIN); ?>">
</form>

<?php
rbGetOtherAccountProfileLinks($data["ProfileID"]);
?>