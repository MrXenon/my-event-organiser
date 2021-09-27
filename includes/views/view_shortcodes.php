<?php

//	Add	the	main view shortcode
add_shortcode('cooper_main_view','load_main_view_cooper');

function load_main_view_cooper( $atts, $content = NULL){
    //Include the main view
        include COOPER_INCLUDES_VIEWS_DIR.
            '/cooper_main_view.php';
}

