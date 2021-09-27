<?php
/**
 * Description of EventView class
 */

require_once MY_EVENT_ORGANISER_PLUGIN_MODEL_DIR .'/Event.php';

class EventView{

    private $event;

    public function __construct() {
        $this->event = new Event();
    }

    public function getGetValues(){
        //Define the check for params
        $get_check_array = array (
            //Submit action
            'link' => array('filter' => FILTER_SANITIZE_STRING )
        );

        //Get filtered input:
        return filter_input_array( INPUT_GET, $get_check_array );
    }

    public function getPostValues(){
        // Define the check for params
        $post_check_array = array (

            // Add event form
            // submit action
            'add_event' => array('filter' => FILTER_SANITIZE_STRING ),

            // Title
            'title' => array('filter' => FILTER_SANITIZE_STRING ),

            // Event Category
            'cat' => array('filter' => FILTER_SANITIZE_NUMBER_INT ),
            // Event Type
            'type' => array('filter' => FILTER_SANITIZE_NUMBER_INT ),

            // Additional info
            'info' => array('filter' => FILTER_SANITIZE_STRING ),

            // Calendar info
            'event_date' => array('filter' => FILTER_SANITIZE_STRING ),
            'end_date' => array('filter' => FILTER_SANITIZE_STRING ),
            'due_date' => array('filter' => FILTER_SANITIZE_STRING )
        );
        // Get filtered input:
        $post_inputs = filter_input_array( INPUT_POST, $post_check_array );

        return $post_inputs;
    }


    public function is_submit_event_add_form( $post_inputs ){
        if (!is_null($post_inputs['add_event'])) return TRUE;

        return FALSE;
    }

    public function check_event_save_form ( &$post_inputs )
    {
        // Special wordpress error class
        $errors = new WP_Error();

        // Title
        try {
            $this->event->checkEventTitle($post_inputs['title']);
        } catch (Exception $exc) {
            $errors->add('title', $exc->getMessage());
        }

        // Categorie ID
        try {
            $this->event->checkCat($post_inputs['cat']);
        } catch (Exception $exc) {
            $errors->add('cat', $exc->getMessage());
        }
        // Type ID
        try {
            $this->event->checkType($post_inputs['type']);
        } catch (Exception $exc) {
            $errors->add('type', $exc->getMessage());
        }

        // Info
        try {
            $this->event->checkInfo($post_inputs['info']);
        } catch (Exception $exc) {
            $errors->add('info', $exc->getMessage());
        }

        // Check all date's
        $dates = array( 'event_date',
            'end_date',
            'due_date');
        foreach( $dates as $date_field ){
            try {
                // End date might be empty
                $date_empty = !($date_field == 'end_date');
                $this->event->checkDate($post_inputs[$date_field],
                    $date_empty);
                // If empty date equals 0000-00-00 change to ''
                if (!$date_empty && (strcmp($post_inputs[$date_field] ,'0000-00-00') == 0 )){
                    $post_inputs[$date_field] = '';
                }
            } catch (Exception $exc) {
                $errors->add($date_field, $exc->getMessage());
            }
        }

        // Check for errors before saving the date
        if ($errors->get_error_code()) return $errors;
        return TRUE; // return the real result
    }

}
?>