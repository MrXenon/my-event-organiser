<?php
/**
 * Created by PhpStorm.
 * User: Kevin Schuit
 * Date: 01/02/2018
 * Time: 08:53
 */
// Set base url and add page specific vars
$base_url = get_permalink();

//Include the Event class from the VIEW.
require_once MY_EVENT_ORGANISER_PLUGIN_INCLUDES_VIEWS_DIR.'/EventView.php';

$event_view = new EventView();

//Get the getters
$get_inputs = $event_view->getGetValues();

//Get form vars
$post_inputs = $event_view->getPostValues();

//If provided set current file based on the provided link
$current_file = (!empty($get_inputs['link']) ?
MY_EVENT_ORGANISER_PLUGIN_INCLUDES_VIEWS_DIR. '/'. $get_inputs['link'].'.php' :
'');

//Add the current page link get param.
if (!empty($get_inputs['link'])){
    $params = array( 'link' => $get_inputs['link']);
    $file_base_url = add_query_arg( $params, $base_url );
} else {
    //Or stick to base url
    $file_base_url = $base_url;
}

$form_result = new WP_Error();
//Check add form event :
if ( $event_view->is_submit_event_add_form($post_inputs) ){

    //Check add form submission :
    $form_result = $event_view->check_event_save_form( $post_inputs );
    if ( !is_bool($form_result) && get_class($form_result) == 'WP_Error'){
        //Reshow the form again
    } else {
        echo /*__FILE__.__LINE__.*/"Save the event form!!<br />";

        // Check on error
         if ( !($form_result instanceof WP_Error )){

             $form_result = new WP_Error();
         }

         // Create instance of Event class
         $event = new Event();

         // Save the event
         $return = $event->save($post_inputs['title'],
             $post_inputs['cat'],
             $post_inputs['type'],
             $post_inputs['info'],
             $post_inputs['event_date'],
             $post_inputs['end_date'],
             $post_inputs['due_date']);

         if ( ! ($return instanceof WP_Error)) {
             // Do not show the event input file again : Show main page
             $current_file = '';
         }
    }
}

// Check add form submission :
if ( $event_view->is_submit_event_add_form($post_inputs) ){
}

if (!empty($current_file) && file_exists( $current_file)){

    // Include the link file and show that page.
    include $current_file;

} //*
else if (!empty($current_file) && !file_exists($current_file)){
    // Linked file not found!
    // @todo: Change error
    echo '<span style="color:red">Main view error: FILE NOT FOUND ['.
        $current_file .']</span>';

} //*/
else {

    echo '<span style="color:blue">Test Main view</span>';
    ?>
    <div>This is the main view content</div>

    <?php
    if (current_user_can('ivs_meo_event_read')) {

        if (current_user_can('ivs_meo_event_create')) {
            // Create add link
            $params = array('link' => 'event_add');
            // Add params to base url update link
            $link = add_query_arg($params, $base_url);
            ?>
            <a href="<?php echo $link; ?>">Evenementen toevoegen </a><p/>

            <?php
        }
        // Create event_list link
        $params = array('link' => 'event_list');
        // Add params to base url link
        $link = add_query_arg($params, $base_url);
        ?>

        <a href="<?php echo $link; ?>">Evenementen lijst </a><p/>

        <?php
        //Create sign on link
        $params = array('link' => 'event_apply');
        // Add params to base url update link
        $link = add_query_arg($params, $base_url);
        ?>
        <a href="<?php echo $link; ?>">Inschrijven op evenement </a><p/>
        <?php
    } // current_user_can()
}
    ?>

