<?php
/*
Template Name: 	Member Details
 * @name		Member Details
 * @type		PHP page
 * @desc		Member Details
*/

if (!headers_sent()) {
header("Cache-control: private"); //IE 6 Fix
}
global $wpdb;

/* Get User Info ******************************************/ 
global $current_user;
get_currentuserinfo();

// Get Settings
$rb_agency_options_arr 							= get_option('rb_agency_options');
$rb_agency_option_profilenaming 				= isset($rb_agency_options_arr['rb_agency_option_profilenaming']) ?(int)$rb_agency_options_arr['rb_agency_option_profilenaming']:"";
$rb_agency_interact_options_arr 					= get_option('rb_agencyinteract_options');
$rb_agencyinteract_option_registerallow 		= isset($rb_agency_interact_options_arr['rb_agencyinteract_option_registerallow']) ?(int)$rb_agency_interact_options_arr['rb_agencyinteract_option_registerallow']:"";
$rb_agencyinteract_option_overviewpagedetails 	= isset($rb_agency_interact_options_arr['rb_agencyinteract_option_overviewpagedetails']) ? (int)$rb_agency_interact_options_arr['rb_agencyinteract_option_overviewpagedetails']:"";

// Check Sidebar
$rb_agency_interact_options_arr = get_option('rb_agencyinteract_options');
$rb_agencyinteract_option_profilemanage_sidebar = isset($rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar']) ?$rb_agency_interact_options_arr['rb_agencyinteract_option_profilemanage_sidebar']:"";
$rb_subscription = isset($rb_agency_options_arr['rb_agencyinteract_option_profilelist_subscription']) ?$rb_agency_options_arr['rb_agencyinteract_option_profilelist_subscription']:"";

// Were they users or agents?
$profiletype = (int)get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);
if(get_user_meta($current_user->ID, 'rb_agency_interact_clientdata', true)){$profiletypetext = __("Agent/Producer", RBAGENCY_interact_TEXTDOMAIN); } else {$profiletypetext = __("Model/Talent", RBAGENCY_interact_TEXTDOMAIN); }



// Change Title
add_filter('wp_title', 'rb_agencyinteractive_override_title', 10, 2);
function rb_agencyinteractive_override_title(){
	return "Member Overview";
}

/* Display Page ******************************************/ 


// Call Header
echo $rb_header = RBAgency_Common::rb_header();

	echo "	<div id=\"primary\" class=\"rb-agency-interact member-overview\">\n";
	echo "  	<div id=\"rbcontent\">\n";

		// get profile Custom fields value
		$rb_agency_new_registeredUser = get_user_meta($current_user->ID,'rb_agency_new_registeredUser',true);


	// ****************************************************************************************** //
	// Check if User is Logged in or not
	if (is_user_logged_in()) {

		/*
		 * Set Media to not show to
		 * client/s, agents, producers,
		 */
		$ptype = (int)get_user_meta($current_user->ID, "rb_agency_interact_profiletype", true);
		$ptype = retrieve_title($ptype);
		$restrict = array('client','clients','agents','agent','producer','producers');

		if(empty($rb_agency_new_registeredUser) && rb_get_user_profilstatus() == 3){
			echo "<p class=\"rbalert success\">Thank you for joining ".get_bloginfo("name")."! Your account is pending for approval. We will send you an email once your account is approved.";
			$profile_gallery = $wpdb->get_row($wpdb->prepare("SELECT ProfileGallery FROM ".table_agency_profile." WHERE ProfileUserLinked = %d",$current_user->ID));
			echo "<a href=\"". get_bloginfo("wpurl") ."/profile/".$profile_gallery->ProfileGallery."\">View My Profile</a>";
			echo "<a href=\"". get_bloginfo("wpurl") ."/profile-member/account/\">Manage Account</a></p>";

		} else {

			if(!empty($rb_agency_new_registeredUser)){
				if(in_array(strtolower($ptype),$restrict)){
					echo "<div id=\"profile-steps\">Profile Setup: Step 1 of 2</div>\n";
				} else {
					echo "<div id=\"profile-steps\">Profile Setup: Step 1 of 3</div>\n";
				}
			}

			echo "	<div id=\"profile-manage\" class=\"profile-overview\">\n";

			// Menu
			include("include-menu.php");

			echo " <div class=\"manage-overview manage-content\">\n";
			
			/* Check if the user is regsitered *****************************************/ 
			$sql = "SELECT ProfileID FROM ". table_agency_profile ." WHERE ProfileUserLinked =  ". $current_user->ID ."";
			$results = $wpdb->get_row($sql,ARRAY_A);
			$count = $wpdb->num_rows;

			if ($count > 0) {

				$data = $results;// is there record?

				echo "	<div class=\"manage-section welcome\">\n";

				//loop all auditions with mp3 audio files

				$ProfileID = isset($_REQUEST['ProfileID'])?$_REQUEST['ProfileID'] : $data['ProfileID'];
					
					$q = "SELECT cs_job.*, avail.* FROM  ".table_agency_casting_job." AS cs_job INNER JOIN ".table_agency_castingcart_availability."
					AS avail ON cs_job.Job_ID = avail.CastingJobID WHERE avail.CastingAvailabilityProfileID = ".$ProfileID."
					";
					$job_data = $wpdb->get_results($q);

				echo '<table cellpadding="10">
						<tbody>
							<tr>
								<th>Job ID</th>
								<th>Job Title</th>
								<th>Date Confirmed</th>
								<th>MP3 Audition Files</th>
								<th>Availability</th>
							</tr>';

							?>
							<?php
							
							//audio files
							$dir = RBAGENCY_UPLOADPATH ."_casting-jobs/";
							$files = "";
                            if(is_dir($dir)){
                                $files = scandir($dir, 0);
                            }
							
							$medialink_option = $rb_agency_options_arr['rb_agency_option_profilemedia_links'];


							?>
							<?php
							if(count($job_data) > 0)
							{
								foreach($job_data as $job)
								{
									
								?><tr>
									<td><?php echo $job->Job_ID ; ?> </td>
									<td><a href="<?php echo site_url(); ?>/job-detail/<?php echo $job->Job_ID ?>"><?php echo $job->Job_Title ; ?> </a></td>
									<td><?php echo $job->CastingAvailabilityDateCreated ; ?> </td>
									<td>
										<?php 
                                        if(is_array($files)){
										for($i = 0; $i < count($files); $i++){
										$parsedFile = explode('-',$files[$i]);

											if($parsedFile[0] == $job->Job_ID && $ProfileID == $parsedFile[1]){
												$mp3_file = str_replace(array($parsedFile[0].'-',$parsedFile[1].'-'),'',$files[$i]);
												if($medialink_option == 2){
													//open in new window and play
													echo '<a href="'.site_url().'/wp-content/uploads/profile-media/_casting-jobs/'.$files[$i].'" target="_blank">'.$mp3_file.'</a><br>';
												}elseif($medialink_option == 3){
													//open in new window and download
													$force_download_url = RBAGENCY_PLUGIN_URL."ext/forcedownload.php?file=".'_casting-jobs/'.$files[$i];
													echo '<a href="'.$force_download_url.'" target="_blank">'.$mp3_file.'</a><br>';
												}
												
											}
										}
                                        }
										?>

									</td>
									<td>
										<?php

											$query = "SELECT CastingAvailabilityStatus as status FROM ".table_agency_castingcart_availability." WHERE CastingAvailabilityProfileID = %d AND CastingJobID = %d";
											$prepared = $wpdb->prepare($query,$ProfileID,$job->Job_ID);
											$availability = current($wpdb->get_results($prepared));
											if($availability->status == 'notavailable'){
												echo 'Not Available';
											}else{
												echo ucfirst($availability->status);
											}
											
										?>

									</td>
								</tr>
							<?php
									
								}
							}
							else {
								?>
								<tr>
									<td colspan="3"> No Record Found !</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
						<?php

			echo " <div class=\"section-content section-account\">\n"; // .account
			echo " </div>\n";

			echo " </div>\n"; // .welcome

			// No Record Exists, register them
			} else {
				
			}
			echo " </div>\n"; // .manage-content
			echo "</div><!-- #profile-manage -->\n";
		}

		// if pending for approval
	} else {

		// Show Login Form
		include("include-login.php");
	}

	echo "  </div><!-- #rbcontent -->\n";
	echo "</div><!-- #primary -->\n";

// Call Footer
echo $rb_footer = RBAgency_Common::rb_footer();
?>