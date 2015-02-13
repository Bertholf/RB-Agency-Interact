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
			$rb_agency_options_arr = get_option('rb_agency_options');

			include(RBAGENCY_INTERACT_PLUGIN_DIR ."theme/include-profileregister.php");

			$output_string=ob_get_contents();;
			ob_end_clean();
			return $output_string;

		}

}

?>