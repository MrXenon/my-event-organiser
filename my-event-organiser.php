<?php

defined( 'ABSPATH' ) OR exit;

/**
 * Plugin Name: My event organisator plugin
 * Plugin URI: < your plugin url > 
 * Description: This plugin will help organise an event with your website
 * Author: Kevin Schuit
 * Author URI: < your uri > 
 * Version: 0.0.1
 * Text Domain: my-event-organiser
 * Domain Path: languages
 * 
 * This is distributed in hte hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even teh implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details.
 * 
 * You should have received a cpoy of the GNU General Publilc License 
 * along with your plugin. If not, see <http://www.gnu.org/licenses/>.
 */

 //Define the plugin name:
 //Activeren en deactiveren
 define ( 'MY_EVENT_ORGANISER_PLUGIN', __FILE__ );

 //Inculde the general defenition file:
 require_once plugin_dir_path ( __FILE__ ) . 'includes/defs.php';

/* Register the hooks */
    register_activation_hook( __FILE__, array( 'MyEventOrganiser', 'on_activation' ) );
    register_deactivation_hook( __FILE__, array( 'MyEventOrganiser', 'on_deactivation' ) );
 
 class MyEventOrganiser
 {
     public function __construct()
     {

         //Fire a hook before the class is setup.
         do_action('my_event_organiser_pre_init');

         //Load the plugin
         add_action('init', array($this, 'init'), 1);
     }

     public static function on_activation()
     {
         if ( ! current_user_can( 'activate_plugins' ) )
             return;
         $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
         check_admin_referer( "activate-plugin_{$plugin}" );

         // Add the theme capabilities
         MyEventOrganiser::add_plugin_caps();
         MyEventOrganiser::createDb();
         MyEventOrganiser::create_page();

         # Uncomment the following line to see the function in action
         # exit( var_dump( $_GET ) );
     }
     public static function on_deactivation()
     {
         if ( ! current_user_can( 'activate_plugins' ) )
             return;
         $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
         check_admin_referer( "deactivate-plugin_{$plugin}" );

         // Remove the theme specific capabilities
         MyEventOrganiser::remove_plugin_caps();

         # Uncomment the following line to see the function in action
         # exit( var_dump( $_GET ) );
     }

     /**
      * Loads the plugin into Wordpress
      *
      * @since 1.0.0
      */
     public function init()
     {

         // Run hook once Plugin has been initialized
         do_action('my_event_organiser_init');

         // Load admin only components.
         if (is_admin()) {

             //Load all admin specific includes
             $this->requireAdmin();

             //Setup admin page
             $this->createAdmin();
         } else {
             // Load the calendar javascript
             wp_enqueue_script('calendar-script', '/wp-content/plugins/my-event-organiser/includes' . '/calendar/calendar.js');
         }

         // Load the view shortcodes
         $this->loadViews();
     }

     /**
      * Loads all admin related files into scope
      *
      * @since 1.0.0
      */
     public function requireAdmin()
     {

         //Admin controller file
         require_once MY_EVENT_ORGANISER_PLUGIN_ADMIN_DIR . '/MyEventOrganiser_AdminController.php';
     }

     /**
      * Admin controller functionality
      */
     public function createAdmin()
     {
         MyEventOrganiser_AdminController::prepare();
     }

     /**
      * Load the view shortcodes:
      */
     public function loadViews()
     {
         include MY_EVENT_ORGANISER_PLUGIN_INCLUDES_VIEWS_DIR . '/view_shortcodes.php';
     }

     /*
* Define the array with plugin specific capabilities per role.
*
*/
     public static function get_plugin_roles_and_caps(){

         // Define the desired roles for this plugin:
         return array (
             /* Is always available - Should be on first line */
             array('administrator',
                 'Admin',
                 array( 'ivs_meo_event_read',
                     'ivs_meo_event_create',
                     'ivs_meo_event_update',
                     'ivs_meo_event_delete') ),

             array('ivs_manager',
                 'IVS_Manager',
                 array( 'ivs_meo_event_read',
                     'ivs_meo_event_create',
                     'ivs_meo_event_update',
                     'ivs_meo_event_delete') ),

             array('project_lid',
                 'Project lid',
                 array( 'ivs_meo_event_read') )
         );

     }

     /**
      * Add plugin specific capabilities
      * Check firsth for the specific roles
      * If they not exists add specific roles
      * Add plugin specific caps per role
      *
      */
     public static function add_plugin_caps() {

         // Include the roles and capabilities definition file:
         require_once plugin_dir_path( __FILE__ ) .
             'includes/roles_and_caps_defs.php';

         $role_array = MyEventOrganiser::get_plugin_roles_and_caps();

         // Check for the roles:
         foreach ($role_array as $key => $role_name) {
             // Check specific role
             if( !( $GLOBALS['wp_roles']->is_role(
                 $role_name[MEO_EVENT_ROLE_NAME] )) ){

            // Create role
                 $role = add_role($role_name[MEO_EVENT_ROLE_NAME],
                     $role_name[MEO_EVENT_ROLE_ALIAS], array('read' => true, 'level_0' => true));
             }
             // else : role exists
         }

         #exit( var_dump( $_GET ). var_dump($role) );

         // Add the capabilities per role
         foreach ($role_array as $key => $role_name) {
             // Create the caps for this role
             foreach ($role_name[MEO_EVENT_ROLE_CAP_ARRAY] as $cap_key =>
                      $cap_name) {
                 // gets the author role
                 $role = get_role( $role_name[MEO_EVENT_ROLE_NAME] );
                 // This only works, because it accesses the class instance.
                 // would allow the author to edit others' posts for current theme only
                 $role->add_cap( $cap_name );
            }
         }
     }

     /**
      *
      * Remove all the specific capabilities for this plugin.
      *
      *
      *
      */
     public static function remove_plugin_caps(){

         // Include the roles and capabilities definition file:
         require_once plugin_dir_path( __FILE__ ) .
             'includes/roles_and_caps_defs.php';

         // Get the plugin specific capabilities per role
         $role_array = MyEventOrganiser::get_plugin_roles_and_caps();

         // Add the capabilities per role
         foreach ($role_array as $key => $role_name) {
             // Create the caps for this role
             foreach ($role_name[MEO_EVENT_ROLE_CAP_ARRAY] as $cap_key => $cap_name) {
                    // gets the specific role
                             $role = get_role( $role_name[MEO_EVENT_ROLE_NAME] );
                             // This only works, because it accesses the class instance.
                                // would allow the author to edit others' posts for current theme only
                                 $role->remove_cap( $cap_name );
             }
         }
     }

     public static function createDb()
     {

         require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

         //Calling $wpdb;
         global $wpdb;

         $charset_collate = $wpdb->get_charset_collate();

         //Name of the table that will be added to the db
         $event         = $wpdb->prefix . "meo_event";
         $category      =    $wpdb->prefix . "meo_event_category";
         $types         =    $wpdb->prefix . "meo_event_type";

        /*Create Database*/
         //Create the download table
         $sql = "CREATE TABLE IF NOT EXISTS $event (
            id_event BIGINT(10) NOT NULL AUTO_INCREMENT,
            event_title VARCHAR(64) NOT NULL,
            fk_event_category BIGINT(11) NOT NULL,
            fk_event_type bigint(10) NOT NULL,
            event_info varchar(1024) NOT NULL,
            event_date date NOT NULL,
            event_due_date date NOT NULL,
            event_end_date date DEFAULT NULL,
            PRIMARY KEY  (id_event))
            ENGINE = InnoDB $charset_collate";
         dbDelta($sql);

         //Create the submissions table
         $sql = "CREATE TABLE IF NOT EXISTS $category (
            id_event_category BIGINT(10) NOT NULL AUTO_INCREMENT,
            name varchar(32) NOT NULL,
            description varchar(1024) NOT NULL,
            PRIMARY KEY  (id_event_category))
            ENGINE = InnoDB $charset_collate";
         dbDelta($sql);

         //Create the submissions table
         $sql = "CREATE TABLE IF NOT EXISTS $types (
            id_event_type BIGINT(10) NOT NULL AUTO_INCREMENT,
            name varchar(32) NOT NULL,
            description varchar(1024) NOT NULL,
            PRIMARY KEY  (id_event_type))
            ENGINE = InnoDB $charset_collate";
         dbDelta($sql);

     }

      // Create a page with the shortcode when the plug-in gets activated
    public static function create_page()
    {
        if (!current_user_can('activate_plugins')) return;

        global $wpdb;
        // check if the page name exists, if not exists, create new page and add title,
        // content, set status to publish, add author related to the person that activated the plug-in & set type to page
        if (null === $wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'my-event-organiser'", 'ARRAY_A')) {

            $current_user = wp_get_current_user();
            // Create post object
            $page = array(
                'post_title' => __('My event organiser'),
                'post_content' => '[my_event_organiser_main_view]',
                'post_status' => 'publish',
                'post_author' => $current_user->ID,
                'post_type' => 'page',
            );

            // insert the post into the database
            wp_insert_post($page);
        }
    }


 }

 // Instantiate the class
 $event_organiser = new MyEventOrganiser();
 ?>