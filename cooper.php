<?php

defined( 'ABSPATH' ) OR exit;

/**
 * Plugin Name: Cooper
 * Plugin URI: https://www.digital-sense.nl
 * Description: This plug-in will show us our favorite dog COOPER.
 * Author: Nick Kroezen, Johanna Dieleman & Kevin Schuit
 * Author URI: https://digital-sense.nl/cooper
 * Version: 1.0.0
 * Text Domain: cooper
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
 define ( 'COOPER', __FILE__ );

 //Inculde the general defenition file:
 require_once plugin_dir_path ( __FILE__ ) . 'includes/defs.php';

/* Register the hooks */
    register_activation_hook( __FILE__, array( 'Cooper', 'on_activation' ) );
    register_deactivation_hook( __FILE__, array( 'Cooper', 'on_deactivation' ) );
 
 class Cooper
 {
     public function __construct()
     {

         //Fire a hook before the class is setup.
         do_action('cooper_pre_init');

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
         Cooper::createDb();
         Cooper::create_page();

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
          do_action('cooper_init');
 
          // Load admin only components.
          if (is_admin()) {
 
              //Load all admin specific includes
              $this->requireAdmin();
 
              //Setup admin page
              $this->createAdmin();
          }else {}
 
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
         require_once COOPER_ADMIN_DIR . '/Cooper_AdminController.php';
     }

     /**
      * Admin controller functionality
      */
     public function createAdmin()
     {
        Cooper_AdminController::prepare();
     }

     /**
      * Load the view shortcodes:
      */
     public function loadViews()
     {
         include COOPER_INCLUDES_VIEWS_DIR . '/view_shortcodes.php';
     }

        /**
         * Function underneath creates database tables for the cooper plug-in.
         */
     public static function createDb()
     {

         require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

         //Calling $wpdb;
         global $wpdb;

         $charset_collate = $wpdb->get_charset_collate();

         //Name of the table that will be added to the db
         $doggie         =       $wpdb->prefix . "doggie";


        /*Create Database*/
         //Create the doggie table
         $sql = "CREATE TABLE IF NOT EXISTS $doggie (
            id_doggie INT(11) NOT NULL AUTO_INCREMENT,
            dog_name VARCHAR(125) NOT NULL,
            dog_img VARCHAR(125) NOT NULL,
            dog_race VARCHAR(125) NOT NULL,
            PRIMARY KEY  (id_doggie))
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
        if (null === $wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'cooperDoggie'", 'ARRAY_A')) {

            $current_user = wp_get_current_user();
            // Create post object
            $page = array(
                'post_title' => __('Cooper Doggie'),
                'post_content' => '[cooper_main_view]',
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
 $cooper = new Cooper();
 ?>