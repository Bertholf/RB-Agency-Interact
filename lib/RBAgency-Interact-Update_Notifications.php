<?php

class RBAgency_Interact_Update_Notifications {

	protected $_updated_primaryinfos = [];
	protected $_updated_customfields = [];
	protected $_update_upload_photos = false;

	public function checkPrimaryInfoChanges($request){

		global $wpdb;

		$ProfileID = $request['ProfileID'];

		$query = "SELECT * FROM ".$wpdb->prefix."agency_profile WHERE ProfileID = %d";
		$profileInfos = $wpdb->get_results($wpdb->prepare($query,$ProfileID),ARRAY_A);

		$ProfileUserLinked = $this->getProfileUserLinked($ProfileID);
		$temp = [];
		foreach($profileInfos as $profile){
			foreach($profile as $k=>$v){
				$temp[$k] = $v;
			}
		}


		foreach($request as $k=>$v){
			if(strpos($k, 'ProfileCustomID') === false){
				if($request[$k] != $temp[$k]){
					$this->_updated_primaryinfos[$k] = $v;				
				}
			}			
		}
		if(!empty($this->_updated_primaryinfos)){
			$this->_updated_primaryinfos['ProfileUserLinked'] = $ProfileUserLinked;
		}		
		return !empty($this->_updated_primaryinfos) ? $this->_updated_primaryinfos : "";
	}


	public function checkCustomFieldChanges($request){
		global $wpdb;

		$ProfileID = $request['ProfileID'];
		$ProfileUserLinked = $this->getProfileUserLinked($ProfileID);
		
		//$profileCustomFields = $wpdb->get_results($wpdb->prepare($query,$ProfileID),ARRAY_A);

		$temp = [];
		foreach($request as $k=>$v){
			if(strpos($k, 'ProfileCustomID_')>-1){		

				
				
				if(is_array($v)){
					$v = array_unique($v);
					$v = implode(",",$v);
					$lastCharacter = substr($v,-1);
					if($lastCharacter == ','){
						if(empty($v)){
							$v = 'empty';
						}else{
							$v = substr($v,0,-1);
						}
						
					}
				}else{
					$v = $v;
					$lastCharacter = substr($v,-1);
					if($lastCharacter == ','){
						if(empty($v)){
							$v = 'empty';
						}else{
							$v = substr($v,0,-1);
						}
					}
				}
				#check if value is date or not
				$explodedKey = explode('_',$k);
				if(count($explodedKey) == 3){
					$ProfileCustomID = str_replace('ProfileCustomID_Date_', "", $k);
					$query = "SELECT ProfileCustomDateValue FROM ".$wpdb->prefix."agency_customfield_mux WHERE ProfileID = %d AND ProfileCustomID = %d";
					$profileCustomField = $wpdb->get_row($wpdb->prepare($query,$ProfileID,$ProfileCustomID),ARRAY_A);
					if($profileCustomField['ProfileCustomDateValue'] != $v){
						;
						$this->_updated_customfields[$ProfileCustomID] = !empty($v) ? $v : 'empty'; 
					}
				}else{
					$ProfileCustomID = str_replace('ProfileCustomID_', "", $k);
					$query = "SELECT ProfileCustomValue FROM ".$wpdb->prefix."agency_customfield_mux WHERE ProfileID = %d AND ProfileCustomID = %d";
					$profileCustomField = $wpdb->get_row($wpdb->prepare($query,$ProfileID,$ProfileCustomID),ARRAY_A);
					
					if($profileCustomField['ProfileCustomValue'] != $v){
						$this->_updated_customfields["ProfileCustomID".$ProfileCustomID] = !empty($v) ? $v : 'empty'; 
					}		
				}
			}
		}

		if(!empty($this->_updated_customfields)){
			$this->_updated_customfields['ProfileUserLinked'] = $ProfileUserLinked;
		}

		return !empty($this->_updated_customfields) ? $this->_updated_customfields : "";
		
	}

	public function getProfileUserLinked($ProfileID){
		global $wpdb;
		$ProfileUserLinked = '';
		$query = "SELECT ProfileUserLinked FROM ".$wpdb->prefix."agency_profile WHERE ProfileID = %d";
		$result = $wpdb->get_row($wpdb->prepare($query,$ProfileID),ARRAY_A);
		return $result['ProfileUserLinked'];
	}

	public function generateRequestCustomFields(){
		foreach($_POST as $k=>$v){ 
			if(strpos($k,'ProfileCustomID')>-1){
				$isDate = explode("_",$k);
				if(count($isDate) == 2){
					$k1 = str_replace('ProfileCustomID_', '', $k);
					$k = str_replace('_date', '', $k);
					$request["ProfileCustomID_Date_".$k] = $v;
				}else{
					$k = str_replace('ProfileCustomID', '', $k);
					$request["ProfileCustomID_".$k] = $v;
				}					
						
			}
		}

		return $request;
	}

	public function checkUploadPhotoChanges($ProfileID,$ProfileMediaFileName,$type){
		global $wpdb;
		$user = $wpdb->get_row("SELECT ProfileUserLinked FROM ".$wpdb->prefix."agency_profile WHERE ProfileID = ".$ProfileID,ARRAY_A);
		update_user_meta($user['ProfileUserLinked'],"MediaFileName_".$type."_".$ProfileMediaFileName,$ProfileMediaFileName);
	}

	public function checkUploadVideoChanges($ProfileID,$ProfileMediaFileName,$type){
		global $wpdb;
		$user = $wpdb->get_row("SELECT ProfileUserLinked FROM ".$wpdb->prefix."agency_profile WHERE ProfileID = ".$ProfileID,ARRAY_A);
		update_user_meta($user['ProfileUserLinked'],"VideoFileName_".$type."_".$ProfileMediaFileName,$ProfileMediaFileName);
	}







}