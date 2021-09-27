<?php
/**
 * Created by PhpStorm.
 * User: Kevin Schuit
 * Date: 26/01/2018
 * Time: 08:53
 */

class EventType {

    /**
     * getPostValues :
     * Filter input and retrieve POST input params
     *
     * @return array containing known POST input fields
     */
    public function getPostValues(){

        // Define the check for params
        $post_check_array = array (
            // submit action
            'add' => array('filter' => FILTER_SANITIZE_STRING ),
            'update'   => array('filter' =>FILTER_SANITIZE_STRING ),
            // List all update form fields !!!
            // event type name.
            'name'   => array('filter' => FILTER_SANITIZE_STRING ),
            // Help text
            'description'   => array('filter' => FILTER_SANITIZE_STRING ),
            // Id of current row
            'id'    => array( 'filter'    => FILTER_VALIDATE_INT ),
            // event type name.
            'name' => array('filter' => FILTER_SANITIZE_STRING ),
            // Help text
            'description' => array('filter' => FILTER_SANITIZE_STRING ),
        );
        // Get filtered input:
        $inputs = filter_input_array( INPUT_POST, $post_check_array );
        // RTS
        return $inputs;
    }

    /**
     *
     * @global type $wpdb The Wordpress database class
     * @param type $input_array containing insert data
     * @return boolean TRUE on succes OR FALSE
     */
    public function save($input_array){
            try {
                if (!isset($input_array['name']) OR
                    !isset($input_array['description'])){
                    // Mandatory fields are missing
                    throw new Exception(__("Missing mandatory fields"));
                }
                if ( (strlen($input_array['name']) < 1) OR
                    (strlen($input_array['description']) < 1) ){
                    // Mandatory fields are empty
                    throw new Exception( __("Empty mandatory fields") );
                }

                global $wpdb;

                // Insert query
                $wpdb->query($wpdb->prepare("INSERT INTO `". $wpdb->prefix
                    ."meo_event_type` ( `name`, `description`)".
                    " VALUES ( '%s', '%s');",$input_array['name'],
                    $input_array['description']) );
                // Error ? It's in there:
                if ( !empty($wpdb->last_error) ){
                    $this->last_error = $wpdb->last_error;
                    return FALSE;
                }

                /*
                 echo '<pre>';
                 echo __FILE__.__LINE__.'<br />';
                 var_dump($wpdb);
                 echo '</pre>';
                 //*/

                //echo 'Insert name and description for this
                //Type:"'.$input_array['name'].
                // '"-"'. $input_array['description'].'"<br />';

            } catch (Exception $exc) {
                // @todo: Add error handling
                echo '<pre>'. $exc->getTraceAsString() .'</pre>';
            }

        return TRUE;
    }

    /**
     *
     * @return int number of Event types stored in db
     */
    public function getNrOfEventTypes(){
        global $wpdb;

        $query = "SELECT COUNT(*) AS nr FROM `". $wpdb->prefix
            ."meo_event_type`";
        $result = $wpdb->get_results( $query, ARRAY_A );

        return $result[0]['nr'];
    }

    /**
     *
     * @return type
     */
    public function getEventTypeList(){

        global $wpdb;
        $return_array = array();

        $result_array = $wpdb->get_results( "SELECT * FROM `". $wpdb->prefix .
            "meo_event_type` ORDER BY `id_event_type`", ARRAY_A);

         /*
         echo '<pre>'
         echo __FILE__.__LINE__.'<br />';
         var_dump($result_array);
         echo '</pre>';
         //*/

        // For all database results:
        foreach ( $result_array as $idx => $array){
        // New object
            $type = new EventType();
            // Set all info
            $type->setName($array['name']);
            $type->setId($array['id_event_type']);
            $type->setDescription($array['description']);

            // Add new object toe return array.
            $return_array[] = $type;
        }
        return $return_array;
    }

    /**
     *
     * @param type $id Id of the event type
     */
    public function setId( $id ){
        if ( is_int(intval($id) ) ){
            $this->id = $id;
        }
    }

    /**
     *
     * @param type $name name of the event type
     */
    public function setName( $name ){
        if ( is_string( $name )){
            $this->name = trim($name);
        }
    }

    /**
     *
     * @param type $desc The help text of the event type
     */
    public function setDescription ($desc){
        if ( is_string($desc)){
            $this->description = trim($desc);
        }
    }

    /**
     *
     * @return int The db id of this event
     */
    public function getId(){
        return $this->id;
    }

    /**
     *
     * @return string The name of the Event type
     */
    public function getName(){
        return $this->name;
    }

    /**
     *
     * @return string The help text of the description
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * getGetValues :
     *  Filter input and retrieve GET input params
     *
     * @return array containing known GET input fields
     */
    public function getGetValues(){
        //Define the check for params
        $get_check_array = array (
            //Action
            'action' => array('filter' => FILTER_SANITIZE_STRING ),

            //Id of current row
            'id' => array('filter' => FILTER_VALIDATE_INT ));

        //Get filtered input:
        $inputs = filter_input_array( INPUT_GET, $get_check_array );

        // RTS
        return $inputs;

    }

    /**
     *  Check the action and perform action on :
     *  -delete
     *
     * @param type $get_array all get vars en values
     * @return string the action provided by the $_GET array.
     */
    public function handleGetAction( $get_array ){
        $action = '';

        switch($get_array['action']){
            case 'update':
                // Indicate current action is update if id provided
                if ( !is_null($get_array['id']) ){
                    $action = $get_array['action'];
                }
                break;

            case 'delete':
                // Delete current id if provided
                if ( !is_null($get_array['id']) ){
                    $this->delete($get_array);
                }
                $action = 'delete';
                break;

            default:
                // Oops
                    break;
        }
        return $action;
    }

    /**
     *
     * @global type $wpdb
     * @return type string table name with wordpress (and app prefix)
     */
    private function getTableName(){
        global $wpdb;
        return $table = $wpdb->prefix . "meo_event_type";
    }

/**
*
* @global type $wpdb WordPress database
* @param type $input_array post_array
* @return boolean TRUE on Succes else FALSE
* @throws Exception
*/
    public function update($input_array){
        try {
            $array_fields = array('id', 'name', 'description');
            $table_fields = array( 'id_event_type', 'name' , 'description');
            $data_array = array();

            // Check fields
            foreach( $array_fields as $field){

                // Check fields
                if (!isset($input_array[$field])){
                    throw new Exception(__("$field is mandatory for update."));
                }

                // Add data_array (without hash idx)
                // (input_array is POST data -> Could have more fields)
                $data_array[] = $input_array[$field];
            }
            global $wpdb;
            // Update query
            //*
            $wpdb->query($wpdb->prepare("UPDATE ".$this->getTableName()."
            SET `name` = '%s', `description` = '%s' ".
                "WHERE `wp_meo_event_type`.`id_event_type` =%d;",$input_array['name'],
                $input_array['description'], $input_array['id']) );
            /*/
            // Replace form field id index by table field id name
            $wpdb->update($this->getTableName(), $this->getTableDataArray($data_array),
            array( 'id_event_type' => $input_array['id']), // Where
            array( '%s', '%s' ),    // Data format
            array( '%d' ));         // Where format
            //*/
        } catch (Exception $exc) {
            // @todo: Fix error handlin
            echo $exc->getTraceAsString();
            $this->last_error = $exc->getMessage();
            return FALSE;
        }
        return TRUE;
    }

    /**
     * The function takes the input data array and changes the
     * indexes to the column names
     * In case of update or insert action
     *
     * @param type $input_data_array  data array(id, name, descpription)
     * @
    param type $action            update | insert
     * @return type array with collumn index and values OR FALSE
     */
    private function getTableDataArray($input_data_array, $action=''){
        // Get the Table Column Names.
        $keys = $this->getTableColumnNames($this->getTableName());

        // Get data array with table collumns
        // NULL if collumns and data does not match in count
        //
        // Note: The order of the fields shall be the same for both!
        $table_data = array_combine($keys, $input_data_array);

        switch ( $action ){
            case 'update':  // Intended fall-through

            case 'insert':
                // Remove the index -> is primary key and can
                // therefore not be changed!

                if (!empty($table_data)){
                    unset($table_data['id_event_type']);
                }
                break;
                    // Remove
        }

        return $table_data;
    }

    /**
     * Get the column names of the specified table
     * @global type $wpdb
     * @param type $table
     * @return type
     */
    private function getTableColumnNames($table){
        global $wpdb;
        try {
            $result_array = $wpdb->get_results("SELECT `COLUMN_NAME`"."FROM INFORMATION_SCHEMA.COLUMNS".
                " WHERE `TABLE_SCHEMA`='".DB_NAME."' AND TABLE_NAME = '".$this->getTableName() ."'", ARRAY_A);
            $keys = array();
            foreach ( $result_array as $idx => $row ){
                $keys[$idx] = $row['COLUMN_NAME'];
            }
            return $keys;
        } catch (Exception $exc) {
        // @todo: Fix error handlin
            echo $exc->getTraceAsString();
            $this->last_error = $exc->getMessage();
            return FALSE;
        }
    }

    /**
     *
     * @global type $wpdb The WordPress database class
     * @param type $input_array containing delete id
     * @return boolean TRUE on succes OR FALSE
     */
    public function delete($input_array){
        try {
            // Check input id
            if (!isset($input_array['id']) ) throw new Exception(__("Missing mandatory fields") );
            global $wpdb;

            // Delete query
            /*
            $query = $wpdb->prepare("Delete FROM `". $this->getTableName(). "` WHERE `id_event_type` = %d", $input_array['id']);

            // Execute query:
            $wpdb->query( $query );
            /*/
            // Delete row by provided id (WordPress style)
            $wpdb->delete( $this->getTableName(),
                array( 'id_event_type' => $input_array['id'] ),
                array( '%d' ) );

            // Where format
            //*/
            // Error ? It's in there:
            if ( !empty($wpdb->last_error) ){
                throw new Exception( $wpdb->last_error);
            }
        } catch (Exception $exc) {

            //@todo: Add error handling
            echo '<pre>';
            $this->last_error = $exc->getMessage();
            echo $exc->getTraceAsString();
            echo $exc->getMessage();
            echo '</pre>';
        }

        return TRUE;
    }

}



?>