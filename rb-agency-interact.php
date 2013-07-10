<?php 
/*
  Plugin Name: RB Agency Interact
  Text Domain: rb-agencyinteract-interact
  Plugin URI: http://rbplugin.com/
  Description: Enhancement to the RB Agency software allowing models ot manage their own information.
  Author: Rob Bertholf
  Author URI: http://rob.bertholf.com/
  Version: 0.1
*/
$rb_agencyinteract_VERSION = "2.0.0"; 
if (!session_id())
session_start();
if ( ! isset($GLOBALS['wp_version']) || version_compare($GLOBALS['wp_version'], '2.8', '<') ) { // if less than 2.8 
	echo "<div class=\"error\"><p>". __("This plugin requires WordPress version 2.8 or newer", rb_agencyinteract_TEXTDOMAIN) .".</p></div>\n";
	return;
}
// *************************************************************************************************** //
// Avoid direct calls to this file, because now WP core and framework has been used
	if ( !function_exists('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
	}
	
	
// Plugin Definitions
	define("rb_agencyinteract_VERSION", $rb_agencyinteract_VERSION); // e.g. 1.0
	define("rb_agencyinteract_BASENAME", plugin_basename(__FILE__) );  // rb-agency/rb-agency.php
	$rb_agencyinteract_WPURL = get_bloginfo("wpurl"); // http://domain.com/wordpress
	$rb_agencyinteract_WPUPLOADARRAY = wp_upload_dir(); // Array  $rb_agencyinteract_WPUPLOADARRAY['baseurl'] $rb_agencyinteract_WPUPLOADARRAY['basedir']
	define("rb_agencyinteract_BASEDIR", get_bloginfo("wpurl") ."/". PLUGINDIR ."/". dirname( plugin_basename(__FILE__) ) ."/" );  // http://domain.com/wordpress/wp-content/plugins/rb-agency-interact/
	define("rb_agencyinteract_UPLOADDIR", $rb_agencyinteract_WPUPLOADARRAY['baseurl'] ."/profile-media/" );  // http://domain.com/wordpress/wp-content/uploads/profile-media/
	define("rb_agencyinteract_UPLOADPATH", $rb_agencyinteract_WPUPLOADARRAY['basedir'] ."/profile-media/" ); // /home/content/99/6048999/html/domain.com/wordpress/wp-content/uploads/profile-media/
	define("rb_agencyinteract_TEXTDOMAIN", basename(dirname( __FILE__ )) ); //   rb-agency
// Call Language Options
	add_action('init', 'rb_agencyinteract_loadtranslation');
		function rb_agencyinteract_loadtranslation(){
			load_plugin_textdomain( rb_agencyinteract_TEXTDOMAIN, false, basename( dirname( __FILE__ ) ) . '/translation/' ); 
		}
	
// *************************************************************************************************** //
// Set Table Names
	if (!defined("table_agencyinteract_agent"))
		define("table_agencyinteract_agent", "rb_agencyinteract_agent");
	if (!defined("table_agencyinteract_subscription"))
		define("table_agencyinteract_subscription", "rb_agencyinteract_subscription");
	if (!defined("table_agencyinteract_subscription_rates"))
		define("table_agencyinteract_subscription_rates", "rb_agencyinteract_subscription_rates");
// Call default functions
	include_once(dirname(__FILE__).'/functions.php');

// Does it need a diaper change?
	include_once(dirname(__FILE__).'/upgrade.php');

// *************************************************************************************************** //
// Creating tables on plugin activation
	function rb_agencyinteract_install() {
		// Required for all WordPress database manipulations
		global $wpdb, $rb_agencyinteract_options_arr;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		// Update the options in the database
		update_option("rb_agencyinteract_options", $rb_agencyinteract_options_arr);
		// Hold the version in a seprate opgion
		add_option("rb_agencyinteract_version", $rb_agencyinteract_VERSION);
		// Subscriptions
		$sql = "CREATE TABLE ". table_agencyinteract_subscription ." (
			SubscriberID BIGINT(20) NOT NULL AUTO_INCREMENT,
			ProfileID BIGINT(20) NOT NULL DEFAULT '0',
			SubscriberDateStart TIMESTAMP DEFAULT NOW(),
			SubscriberDateExpire DATE,
			SubscriberPurchasePrice DECIMAL(12,2),
			SubscriberPurchaseDetails TEXT,
			PRIMARY KEY (SubscriberID)
			);";
		dbDelta($sql);
		// Subscriptions
		$sql = "CREATE TABLE ". table_agencyinteract_subscription_rates ." (
			SubscriptionRateID BIGINT(20) NOT NULL AUTO_INCREMENT,
			SubscriptionRateTitle VARCHAR(255),
			SubscriptionRateType VARCHAR(255),
			SubscriptionRateText TEXT,
			SubscriptionRateTerm INT(10) NOT NULL DEFAULT '1',
			SubscriptionRatePrice DECIMAL(12,2),
			PRIMARY KEY (SubscriptionRateID)
			);";
		dbDelta($sql);
		
		
	}
//Activate Install Hook
register_activation_hook(__FILE__,'rb_agencyinteract_install');

// *************************************************************************************************** //
// Register Administrative Settings
if ( is_admin() ){
	/****************  Add Options Page Settings Group ***************/
	add_action('admin_init', 'rb_agencyinteract_register_settings');
		// Register our Array of settings
		function rb_agencyinteract_register_settings() {
			register_setting('rb-agencyinteract-settings-group', 'rb_agencyinteract_options'); //, 'rb_agencyinteract_options_validate'
		}
		
		// Validate/Sanitize Data
		function rb_agencyinteract_options_validate($input) {
			// Sanitize Data
		}	
	
	add_action('admin_menu','set_rb_agencyinteract_menu');
		//Create Admin Menu
		function set_rb_agencyinteract_menu(){
			// Add Approve Users
			add_submenu_page('users.php', __('Approve Users', rb_agencyinteract_TEXTDOMAIN), __('Approve Users', rb_agencyinteract_TEXTDOMAIN), 'edit_users', basename(__FILE__), 'rb_agencyinteract_approvemembers');
			//add_filter('plugin_action_links', 'rb_agencyinteract_filter_plugin_actions', 10, 2);
			
		}
		/*
		// Adds the Settings link to the plugin activate/deactivate page
		function rb_agencyinteract_filter_plugin_actions($links, $file) {
			static $this_plugin;
			
			if (!$this_plugin) {
				$this_plugin = plugin_basename(__FILE__);	
			}
			if ($file == $this_plugin){
				$settings_link = '<a href="users.php?page=' . rb_agencyinteract_BASENAME . '">' . __('Settings', rb_agencyinteract_TEXTDOMAIN) . '</a>';
				array_unshift($links, $settings_link); // before other links
			}
			return $links;
		}
		*/
		
	/****************  Activate Admin Menu Hook ***********************/
	
		//Pages
		function rb_agencyinteract_settings(){
			include_once('admin/settings.php');
		}
		function rb_agencyinteract_approvemembers(){
			include_once('admin/profile-approve.php');
		}
	
	
		
}
// *************************************************************************************************** //
// Add Widgets
	// Login / Actions Widget
	add_action('widgets_init', create_function('', 'return register_widget("rb_agencyinteract_widget_loginactions");'));
		class rb_agencyinteract_widget_loginactions extends WP_Widget {
			
			// Setup
			function rb_agencyinteract_widget_loginactions() {
				$widget_ops = array('classname' => 'rb_agencyinteract_widget_profileaction', 'description' => __("Displays profile actions such as login and links to edit", rb_agencyinteract_TEXTDOMAIN) );
				$this->WP_Widget('rb_agencyinteract_widget_profileaction', __("Agency Interact Login", rb_agencyinteract_TEXTDOMAIN), $widget_ops);
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
							echo " <form></form>";    
							echo "          <form name=\"loginform\" id=\"login\" action=\"". network_site_url("/") ."profile-login/\" method=\"post\">\n";
							echo "            <div class=\"box\">\n";
							echo "              <label for=\"user-name\">". __("Username", rb_agencyinteract_TEXTDOMAIN). "</label><input type=\"text\" name=\"user-name\" value=\"". wp_specialchars( $_POST['user-name'], 1 ) ."\" id=\"user-name\" />\n";
							echo "            </div>\n";
							echo "            <div class=\"box\">\n";
							echo "              <label for=\"password\">". __("Password", rb_agencyinteract_TEXTDOMAIN). "</label><input type=\"password\" name=\"password\" value=\"\" id=\"password\" /> <a href=\"". get_bloginfo('wpurl') ."/wp-login.php?action=lostpassword\">". __("forgot password", rb_agencyinteract_TEXTDOMAIN). "?</a>\n";
							echo "            </div>\n";
							echo "            <div class=\"box\">\n";
							echo "              <input type=\"checkbox\" name=\"remember-me\" value=\"forever\" /> ". __("Keep me signed in", rb_agencyinteract_TEXTDOMAIN). "\n";
							echo "            </div>\n";
							echo "            <div class=\"submit-box\">\n";
							echo "              <input type=\"hidden\" name=\"action\" value=\"log-in\" />\n";
							echo "              <input type=\"submit\" value=\"". __("Sign In", rb_agencyinteract_TEXTDOMAIN). "\" /><br />\n";
							echo "            </div>\n";
							echo "          </form>\n";     
						 
					 } else {
						   if(current_user_can('level_10')){
							  if ( !empty( $title ) ) { echo $before_title . "RB Agency Settings" . $after_title; };
							  echo "<ul>";
							  echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu")."\">Overview</a></li>";
							  echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_profiles")."\">Manage Profiles</a></li>";
							  echo "<li><a href=\"".admin_url("admin.php?page=rb_agencyinteract_menu_approvemembers")."\">Approve Profiles</a></li>";
							  echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_search")."\">Search Profiles</a></li>";
							  echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_searchsaved")."\">Saved Searches</a></li>";
							  echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_reports")."\">Tools &amp; Reports</a></li>";
							  echo "<li><a href=\"".admin_url("admin.php?page=rb_agency_menu_settings")."\">Settings</a></li>";
							  echo "<li><a href=\"/wp-login.php?action=logout&_wpnonce=3bb3c87a3d\">Logout</a></li>";	    
								  echo "</ul>";
							
							 } else{
							  
								rb_agency_profilesearch(array("profilesearch_layout" =>"simple"));   
								   
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
	add_shortcode("agency_register","rb_agencyinteract_shortcode_agencyregister");
		function rb_agencyinteract_shortcode_agencyregister($atts, $content = null){
			ob_start();
			wp_register_form($atts);
			$output_string=ob_get_contents();
			ob_end_clean();
			return $output_string;
		}

	add_shortcode("profile_register","rb_agencyinteract_shortcode_profileregister");
		function rb_agencyinteract_shortcode_profileregister($atts, $content = null){
			ob_start();
			wp_register_form($atts);
			$output_string=ob_get_contents();
			ob_end_clean();
			return $output_string;
		}

/****************************************************************/
//Uninstall
	function rb_agencyinteract_uninstall() {
		
		register_uninstall_hook(__FILE__, 'rb_agencyinteract_uninstall_action');
		function rb_agencyinteract_uninstall_action() {
			//delete_option('create_my_taxonomies');
		}
	
		// Final Cleanup
		delete_option('rb_agencyinteract_options');
		
		$thepluginfile = "rb-agency/rb-agency.php";
		$current = get_settings('active_plugins');
		array_splice($current, array_search( $thepluginfile, $current), 1 );
		update_option('active_plugins', $current);
		do_action('deactivate_' . $thepluginfile );
	
		echo "<div style=\"padding:50px;font-weight:bold;\"><p>". __("Almost done...", rb_agencyinteract_TEXTDOMAIN) ."</p><h1>". __("One More Step", rb_agencyinteract_TEXTDOMAIN) ."</h1><a href=\"plugins.php?deactivate=true\">". __("Please click here to complete the uninstallation process", rb_agencyinteract_TEXTDOMAIN) ."</a></h1></div>";
		die;
	}
?>