<?php

//	Add	the	main view shortcode
add_shortcode('my_event_organiser_main_view','load_main_view');

function load_main_view( $atts, $content = NULL){
    //Include the main view
        include MY_EVENT_ORGANISER_PLUGIN_INCLUDES_VIEWS_DIR.
            '/my_event_organiser_main_view.php';
}

