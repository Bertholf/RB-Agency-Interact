<?php

/*
 * RBAgency_Extends Class
 *
 * These are shortcode and widget specific functions
 */

class RBAgency_Interact_Extends {

	// *************************************************************************************************** //

	/*
	 * Initialize
	 */
		public static function init(){

			// Assign shortcodes
			add_shortcode( 'profile_register', array("RBAgency_Interact_Extends","profile_register_shortcode") );

		}



	// *************************************************************************************************** //
	// * SHORTCODES 
	// *************************************************************************************************** //


	/*
	 * Profile List
	 * Shortcode: [profile_register]
	 */
		public static function profile_register_shortcode($atts, $content = null){

			ob_start();

			// Get Shortcode Attributes
			extract(shortcode_atts(array(
					"mode" => null
				), $atts));

			// Get Options
			$shortcode_register = true;
			
			$rb_agency_options_arr = get_option('rb_agency_options');
			/*if($mode == "client"){
				include(RBAGENCY_INTERACT_PLUGIN_DIR ."theme/client-register.php");
			}else*/if($mode == "talent"){
				include(RBAGENCY_INTERACT_PLUGIN_DIR ."theme/member-register.php");
			}elseif($mode == "casting" && class_exists("RBAgencyCasting")){
				include(RBAGENCY_casting_PLUGIN_DIR."view/casting-register.php");
			}else{
				echo "Registration mode is not set.";
			}

			$output_string=ob_get_contents();;
			ob_end_clean();
			return $output_string;

		}

}

?>