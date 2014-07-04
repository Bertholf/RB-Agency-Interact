<?php 
/*
Plugin Name: RB Agency Interact
Text Domain: rb-agency-intract
Plugin URI: http://rbplugin.com/wordpress/model-talent-agency-software/
Description: Enhancement to the RB Agency software allowing models to interact directly.
Author: Rob Bertholf
Author URI: http://rob.bertholf.com/
Version: 2.1
*/
$rb_agency_interact_VERSION = "2.1"; 
/*
License: CF Commercial-to-GPL License
Copyright 2007-2013 Rob Bertholf
This License is a legal agreement between You and the Developer for the use of the Software. 
By installing, copying, or otherwise using the Software, You agree to be bound by the terms of this License. 
If You do not agree to the terms of this License, do not install or use the Software.
See license.txt for full details.
*/

// *************************************************************************************************** //

/*
 * Security
 */

	// Avoid direct calls to this file, because now WP core and framework has been used
	if ( !function_exists('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
	}


// *************************************************************************************************** //

/*
 * Declare Global Constants
 */

	// Version
	define("rb_agency_interact_VERSION", $rb_agency_interact_VERSION); // e.g. 1.0
	// Paths
	define("rb_agency_interact_BASENAME", plugin_basename(__FILE__) );  // rb-agency/rb-agency.php
	$rb_agency_interact_WPURL = get_bloginfo("wpurl"); // http://domain.com/wordpress
	$rb_agency_interact_WPUPLOADARRAY = wp_upload_dir(); // Array  $rb_agency_interact_WPUPLOADARRAY['baseurl'] $rb_agency_interact_WPUPLOADARRAY['basedir']
	define("rb_agency_interact_BASEDIR", get_bloginfo("wpurl") ."/". PLUGINDIR ."/". dirname( plugin_basename(__FILE__) ) ."/" );  // http://domain.com/wordpress/wp-content/plugins/rb-agency-interact/
	define("rb_agency_interact_UPLOADDIR", $rb_agency_interact_WPUPLOADARRAY['baseurl'] ."/profile-media/" );  // http://domain.com/wordpress/wp-content/uploads/profile-media/
	define("rb_agency_interact_UPLOADPATH", $rb_agency_interact_WPUPLOADARRAY['basedir'] ."/profile-media/" ); // /home/content/99/6048999/html/domain.com/wordpress/wp-content/uploads/profile-media/
	define("rb_agency_interact_TEXTDOMAIN", basename(dirname( __FILE__ )) ); //   rb-agency
	define("rb_agency_interact_BASEREL", plugin_dir_path( __FILE__ ) );
	


// *************************************************************************************************** //

/*
 * Declare Global WordPress Database Access
 */

	global $wpdb;


/*
 * Set Table Names
 */

	if (!defined("table_agency_interact_temp"))
		define("table_agency_interact_temp", "{$wpdb->prefix}agency_interact_temp");
	if (!defined("table_agencyinteract_subscription_rates"))
		define("table_agencyinteract_subscription_rates", "{$wpdb->prefix}agencyinteract_subscription_rates");


// *************************************************************************************************** //


/*
 * Initialize
 */
	// Call the initialization function
	add_action('init',  array('RBAgencyInteract', 'init'));
	// Check if version number changed and upgrade required
	add_action('init',  array('RBAgencyInteract', 'check_update_needed'));


// *************************************************************************************************** //


/*
 * Call Function and Language
 */

	require_once(WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)) . "/functions.php");



// *************************************************************************************************** //

/*
 * RB Agency Interact Class
 */


class RBAgencyInteract {

	/*
	 * Initialization
	 */

		public static function init(){

			/*
			 * Internationalization
			 */

				// Identify Folder for PO files
				load_plugin_textdomain( rb_agency_interact_TEXTDOMAIN, false, basename( dirname( __FILE__ ) ) . '/translation/' ); 


			/*
			 * Admin Related
			 */
			if ( is_admin() ){

				// TODO:


				// Load Menus
				//add_action('admin_menu', array('RBAgency', 'menu_admin'));

				// Register Settings
				add_action('admin_init', array('RBAgencyInteract', 'do_register_settings') );
			}

		}


	/*
	 * Plugin Activation
	 * Run when the plugin is installed.
	 */

		public static function install(){

			// Required for all WordPress database manipulations
			global $wpdb;
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			/*
			 * Check Permissions
			 */

				// Does the user have permission to activate the plugin
				if ( !current_user_can('activate_plugins') )
					return;
				// Check Admin Referer
				$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
				check_admin_referer( "activate-plugin_{$plugin}" );

			/*
			 * Initialize Options
			 */

				// Update the options in the database
				if(!get_option("rb_agencyinteract_options")) {

					// Set Default Options
					$rb_agency_interact_options_arr = array(
						"rb_agencyinteract_option_registerapproval" => 1,
						"rb_agencyinteract_option_registerallow" => 1
						);
					// Add Options
					update_option("rb_agencyinteract_options",$rb_agency_interact_options_arr);
				}

			/*
			 * Install Schema
			 */

				// agencyinteract Subscription 
				$sql = "CREATE TABLE IF NOT EXISTS ".table_agencyinteract_subscription_rates." (
						SubscriptionRateID int(11) NOT NULL AUTO_INCREMENT,
						SubscriptionRateTitle varchar(200) NOT NULL,
						SubscriptionRateType int(11) NOT NULL,
						SubscriptionRateText text NOT NULL,
						SubscriptionRatePrice int(11) NOT NULL,
						SubscriptionRateTerm int(11) NOT NULL,
						  PRIMARY KEY (SubscriptionRateID)
					);";
				dbDelta($sql);


			/*
			 * Flush rewrite rules
			 */

				// Flush rewrite rules
				//RBAgency::flush_rules();
		}


	/*
	 * Plugin Deactivation
	 * Cleanup when complete
	 */

		public static function unistall(){

			// Permission Granted... Remove
			global $wpdb; // Required for all WordPress database manipulations
			// Drop the tables
			$wpdb->query("DROP TABLE " . table_agencyinteract_subscription_rates);
		}


	/*
	 * Plugin Uninstall
	 * Cleanup when complete
	 */

		public static function remove(){

			// Does user have permission?
			if ( ! current_user_can( 'activate_plugins' ) )
				return;
			check_admin_referer( 'bulk-plugins' );

			// Important: Check if the file is the one that was registered during the uninstall hook.
			if ( __FILE__ != WP_UNINSTALL_PLUGIN )
				return;

			// Permission Granted... Remove
			global $wpdb; // Required for all WordPress database manipulations

			// Drop the tables
			//$wpdb->query("DROP TABLE " . table_agency_interact_temp);

			// Delete Saved Settings
			delete_option('rb_agencyinteract_options');

			$thepluginfile = "rb-agency-interact/rb-agency-interact.php";
			$current = get_settings('active_plugins');
			array_splice($current, array_search( $thepluginfile, $current), 1 );
			update_option('active_plugins', $current);
			do_action('deactivate_' . $thepluginfile );

			echo "<div style=\"padding:50px;font-weight:bold;\"><p>". __("Almost done...", rb_agency_interact_TEXTDOMAIN) ."</p><h1>". __("One More Step", rb_agency_interact_TEXTDOMAIN) ."</h1><a href=\"plugins.php?deactivate=true\">". __("Please click here to complete the uninstallation process", rb_agency_interact_TEXTDOMAIN) ."</a></h1></div>";
			die;

		}


	/*
	 * Update Needed
	 * Is this an updated version of the software and needs database upgrade?
	 */

		public static function check_update_needed(){

			// Hold the version in a seprate option
			if(!get_option("rb_agency_interact_version")) {
				update_option("rb_agency_interact_version", rb_agency_interact_VERSION);
			} else {
				// Version Exists, but is it out of date?
				if(get_option("rb_agency_interact_version") <> rb_agency_interact_VERSION){
					require_once(WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)) . "/upgrade.php");
				} else {
					// Namaste, version is number is correct
				}
			}
		}



	/*
	 * Register Settings
	 * Register Settings group
	 */

		public static function do_register_settings() {
			register_setting('rb-agencyinteract-settings-group', 'rb_agencyinteract_options'); //, 'rb_agency_interact_options_validate'
		}

}


	/*
	 * Administrative Menu
	 * Create the admin menu items
	 */

		// Dont Delete this...
		function rb_agency_interact_menu() {
			return true;
		}

		//Pages
		function rb_agency_interact_approvemembers(){
			include_once('admin/profile-approve.php');
		}



// *************************************************************************************************** //

/*
 * Plugin Actions
 */

	// Activate Plugin
	register_activation_hook(__FILE__, array('RBAgencyInteract', 'install'));

	// Deactivate Plugin
	register_deactivation_hook(__FILE__, array('RBAgencyInteract', 'uninstall'));

	// Uninstall Plugin
	register_uninstall_hook(__FILE__, array('RBAgencyInteract', 'remove'));

// *************************************************************************************************** //






// *************************************************************************************************** //
// Add Widgets
	// Login / Actions Widget
	add_action('widgets_init', create_function('', 'return register_widget("rb_agency_interact_widget_loginactions");'));
		class rb_agency_interact_widget_loginactions extends WP_Widget {
			
			// Setup
			function rb_agency_interact_widget_loginactions() {
				$widget_ops = array('classname' => 'rb_agency_interact_widget_profileaction', 'description' => __("Displays profile actions such as login and links to edit", rb_agency_interact_TEXTDOMAIN) );
				$this->WP_Widget('rb_agency_interact_widget_profileaction', __("Agency Interact Login", rb_agency_interact_TEXTDOMAIN), $widget_ops);
			}

			// What Displays
			function widget($args, $instance) {
				extract($args, EXTR_SKIP);
				echo $before_widget;
				$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
				$count = $instance['trendShowCount'];
				# $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
				# $entry_title = empty($instance['entry_title']) ? ' ' : apply_filters('widget_entry_title', $instance['entry_title']);
				# $comments_title = empty($instance['comments_title']) ? ' ' : apply_filters('widget_comments_title', $instance['comments_title']);

				$atts = array('count' => $count);

				if(!is_user_logged_in()){

						if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
						echo "	<div class=\"rbform\">\n";
						echo "  	<form name=\"loginform\" id=\"rbform-login\" action=\"". network_site_url("/") ."profile-login/\" method=\"post\">\n";
						echo "      	<div class=\"rbfield rbtext rbsingle\">\n";
						echo "          	<label for=\"user-name\">". __("Username", rb_agency_interact_TEXTDOMAIN). "</label>";
						echo "				<div><input type=\"text\" name=\"user-name\" value=\"". wp_specialchars( $_POST['user-name'], 1 ) ."\" id=\"user-name\" /></div>\n";
						echo "          </div>\n";
						echo "          <div class=\"rbfield rbpassrword rbsingle\">\n";
						echo "             	<label for=\"password\">". __("Password", rb_agency_interact_TEXTDOMAIN). "</label>";
						echo "				<div>";
						echo "					<input type=\"password\" name=\"password\" value=\"\" id=\"password\" />";
						echo "					<small class=\"rbfield-note\"><a href=\"". get_bloginfo('wpurl') ."/wp-login.php?action=lostpassword\">". __("forgot password", rb_agency_interact_TEXTDOMAIN). "?</a></small>\n";
						echo "          	</div>\n";
						echo "          </div>\n";
						echo "          <div class=\"rbfield rbcheckbox rbsingle\">\n";
						echo "          	<label></label>\n";
						echo "              <div><label><input type=\"checkbox\" name=\"remember-me\" value=\"forever\" /> ". __("Keep me signed in", rb_agency_interact_TEXTDOMAIN). "</label></div>\n";
						echo "          </div>\n";
						echo "          <div class=\"rbfield rbsubmit rbsingle\">\n";
						echo "         		<input type=\"hidden\" name=\"action\" value=\"log-in\" />\n";
						echo "             	<input type=\"submit\" value=\"". __("Sign In", rb_agency_interact_TEXTDOMAIN). "\" />\n";
						echo "          </div>\n";
						echo "  	</form>\n";
						echo "	</div>\n";

				} else {
					if(current_user_can('level_10')){
						if ( !empty( $title ) ) { echo $before_title . "RB Agency Settings" . $after_title; };
						echo "<ul>";
						echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu")."\">Overview</a></li>";
						echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_profiles")."\">Manage Profiles</a></li>";
						echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_interact_menu_approvemembers")."\">Approve Profiles</a></li>";
						echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_search")."\">Search Profiles</a></li>";
						echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_searchsaved")."\">Saved Searches</a></li>";
						echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_reports")."\">Tools &amp; Reports</a></li>";
						echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_settings")."\">Settings</a></li>";
						echo "<li><a href=\"/wp-login.php?action=logout&_wpnonce=3bb3c87a3d\">Logout</a></li>";	    
						echo "</ul>";

					} else{

						rb_agency_profilesearch(array("layout" =>"simple"));

					}

				}

				echo $after_widget;
			}

			// Update
			function update($new_instance, $old_instance) {
				$instance = $old_instance;
				$instance['title'] = strip_tags($new_instance['title']);
				$instance['trendShowCount'] = strip_tags($new_instance['trendShowCount']);
				return $instance;
			}
		
			// Form
			function form($instance) {				
				$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
				$title = esc_attr($instance['title']);
				$trendShowCount = esc_attr($instance['trendShowCount']);
				?>
					<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
					<p><label for="<?php echo $this->get_field_id('trendShowCount'); ?>"><?php _e('Show Count:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('trendShowCount'); ?>" name="<?php echo $this->get_field_name('trendShowCount'); ?>" type="text" value="<?php echo $trendShowCount; ?>" /></label></p>
				<?php 
			}

		} // class



// *************************************************************************************************** //
// Add Short Codes
	add_shortcode("agency_register","rb_agency_interact_shortcode_agencyregister");
		function rb_agency_interact_shortcode_agencyregister($atts, $content = null){
			ob_start();
			wp_register_form($atts);
			$output_string=ob_get_contents();
			ob_end_clean();
			return $output_string;
		}

	add_shortcode("profile_register","rb_agency_interact_shortcode_profileregister");
		function rb_agency_interact_shortcode_profileregister($atts, $content = null){
			ob_start();
			wp_register_form($atts);
			$output_string=ob_get_contents();
			ob_end_clean();
			return $output_string;
		}

?>