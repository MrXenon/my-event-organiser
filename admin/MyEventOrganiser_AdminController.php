<?php

/**
 * This Admin controller file provide functionality for the Admin section of the 
 * My event organiser.
 * 
 * @author Kevin Schuit
 * @version 0.1
 * 
 * Version history
 * 0.1 Kevin Schuit Initial version
 */

 class MyEventOrganiser_AdminController {

    /**
     * This function will prepare all Admin functionality for the plugin
     */
    static function prepare() {
        
        // Check that we are in the admin area
        if ( is_admin() ) :

            // Add the sidebar Menu structure
            add_action( 'admin_menu', array( 'MyEventOrganiser_AdminController', 'addMenus' ) );

        endif;
    }

    /**
     * Add the Menu structure to the Admin sidebar
     */
    static function addMenus() {

        add_menu_page(
            //string $page_title The text to be displayed in the title tags
            // of the page when the menu is selected
            __( 'My Event Organiser Admin', 'my-event-organiser'),
            // string $menu_title The text to be used for the menu
            __( 'My Event Organiser', 'my-event-organiser' ),
            // string $capability The capability required for this menu to be
            //displayed to the user.
            '',
            //string $menu_slug THe slug name to refer to this menu by (should
            //be unique for this menu)
            'my-event-organiser-admin',
            
            // callback $function The function to be called to output the content for this page
            array( 'MyEventOrganiser_AdminController', 'adminMenuPage'),

            // string $icon_url The url to the icon to be used for this menu
            // * Pass a base64-encoded SVG using a data URI, which will be colored to match the color shceme
            //This should begin with 'data: image/svg+xml;base64,'.
            // * Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'.
            // * Pass 'none' to leave div.wp-menu=image empty so an icon can be added via CSS.
            'dashicons-chart-pie'
            
            // int $position The position in the menu order this one should appear
        );

    //Opdracht 3        
        add_submenu_page (
            'my-event-organiser-admin',

            __( 'meo_admin_event_types', 'my-event-organiser' ),

            __( 'Event Types', 'my-event-organiser'),

            'manage_options',

            'meo_admin_event_types', 

            array( 'MyEventOrganiser_AdminController', 'adminSubMenuEventTypes')

        );



        add_submenu_page (
            // string $parent_slug The slug name for the parent menu
            // (or the file name of a standard Wordpress admin page)
            'my-event-organiser-admin',

            // string $page_title The text to be displayed in the title tags of
            // the page when the menu is selected
            __( 'event_categorie', 'my-event-organiser' ),

            // string $menu_title The text to be used for the menu
            __( 'Event categorieën', 'my-event-organiser'),

            // string $capability The capability required for this menu to be
            // displayed to the user
            'manage_options',

            // string $menu_slug The slug name to refer to this menu by (should be 
            // unique for this menu)
            'meo_admin_event_category',

            // callback $function The function to be called to output the content for this page
            array( 'MyEventOrganiser_AdminController', 'adminSubMenuEventCategory')
        );
    }

        /**
        * The main menu page
         */
            static function adminMenuPage() {

                //Include the view for this menu page.
                include MY_EVENT_ORGANISER_PLUGIN_ADMIN_VIEWS_DIR . '/admin_main.php';
            }


            //The Submenu page for the event types Opdr3
            static function adminSubMenuEventTypes (){
            include MY_EVENT_ORGANISER_PLUGIN_ADMIN_VIEWS_DIR . '/meo_admin_event_types.php';
            }

        /**
        * the submenu page for the event categories
        */
            static function adminSubMenuEventCategory()
        {
            //include the view for this submenu page.
            include MY_EVENT_ORGANISER_PLUGIN_ADMIN_VIEWS_DIR . '/meo_admin_event_category.php';
        }
    }
?>