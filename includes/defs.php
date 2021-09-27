<?php

/**
 * Defenitions needed in the plugin
 * 
 * @author
 * @version 0.1
 * 
 * Version history
 * 0.1      Initial version
 */
// De versie moet gleijk zij met het versie nummer in de my-event-organiser.php header
define ( 'cooper_VERSION', '1.0.0' );

// Minimum required Wordpress version for this plugin
define ( 'cooper_REQUIRED_WP_VERSION', '4.0' );

define ( 'COOPER_BASENAME', plugin_basename( COOPER ) );

define ( 'COOPER_NAME', trim( dirname ( COOPER_BASENAME ), '/' ) );

// Folder Structure
define ( 'COOPER_DIR', untrailingslashit( dirname ( COOPER ) ) );

define ( 'COOPER_INCLUDES_DIR', COOPER_DIR . '/includes' );

define ( 'COOPER_INCLUDES_VIEWS_DIR', COOPER_INCLUDES_DIR	. '/views'	);

define ( 'COOPER_MODEL_DIR', COOPER_INCLUDES_DIR . '/model' );

define ( 'COOPER_ADMIN_DIR', COOPER_DIR . '/admin' );

define ( 'COOPER_ADMIN_VIEWS_DIR', COOPER_ADMIN_DIR . '/views' );

?>