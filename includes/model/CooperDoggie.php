<?php
/**
 * Description of EventCategories
 * 
 * @author Kevin Schuit
 */



 class CooperDoggie{

     /**
  * getPostValues : 
  * Filter input and retrieve POST input params
  *
  *@return array containing known POST input fields
  */
  public function getPostValues(){
      
    //Define the check for params
    $post_check_array = array (
        //Submit action
        'add' => array('filter' => FILTER_SANITIZE_STRING ),
        'update' => array('filter' => FILTER_SANITIZE_STRING ),

        //Event type name
        'dogName' => array('filter' => FILTER_SANITIZE_STRING ),

        //img URL
        'dogImg' => array('filter' => FILTER_SANITIZE_STRING),

        //img URL
        'dogRace' => array('filter' => FILTER_SANITIZE_STRING),
        
        //Id of current row
        'id' => array('filter' => FILTER_VALIDATE_INT )
    );

    //Get filtered input: 
    $inputs = filter_input_array( INPUT_POST, $post_check_array );

    //RTS
    return $inputs;
  }


public function save($input_array) {
    try {
        if (!isset($input_array['dogName']) OR
            !isset($input_array['dogImg']) OR
            !isset($input_array['dogRace'])) {
            //Mondatory fields are missing
            throw new Exception(__("Empty mandatory fields") );
        }

        if ((strlen($input_array['dogName']) <1 ) OR
            (strlen($input_array['dogImg']) <1 ) OR
            (strlen($input_array['dogRace']) <1 ) ){
            //Mandatory fields are empty
            throw new Exception(__("Empty mandatory fields") );
            }

        global $wpdb;

        //Insert query
        $wpdb->query($wpdb->prepare("INSERT INTO `" .$this->getTableName()."` ( `dog_name`, `dog_img`, `dog_race`)".
        " VALUES ( '%s', '%s', '%s');",$input_array['dogName'], $input_array['dogImg'], $input_array['dogRace']) );

        //Error ? It's in there:
        if ( !empty($wpdb->last_error) ){
            $this->last_error = $wpdb->last_error;
            return FALSE;
        }

    } catch (Exception $exc) {
        // @todo: Add error handling
        echo '<pre>'. $exc->getTraceAsString() . '</pre>';
    }
        return TRUE;
}

    /**
     * 
     * @return int number of Event categories stored in db
     */
    public function getNrOfCoopers(){
        global $wpdb;

        $query = "SELECT COUNT(*) AS nr FROM `".$this->getTableName()."`";
        $result = $wpdb->get_results( $query, ARRAY_A);

        return $result[0] ['nr'];
    }

    public function getCooperList() {

        global $wpdb;
        $return_array = array();

        $result_array = $wpdb->get_results( "SELECT * FROM `".$this->getTableName(). "` ORDER BY `id_doggie`", ARRAY_A);

        //For all database results:
        foreach ( $result_array as $idx => $array) {
            //New object
        $cooper = new CooperDoggie();
        //Set all info
        $cooper->setName($array['dog_name']);
        $cooper->setId($array['id_doggie']);
        $cooper->setImg($array['dog_img']);
        $cooper->setRace($array['dog_race']);

        //Add new object to return array
        $return_array[] = $cooperDoggie;
        }
        return $return_array;
    }

    /**
     * @param type $id Id of the event category
     */
    public function setId( $id ) {
        if ( is_int(intval($id) ) ){
            $this->id = $id;
        }
    }

    public function setName( $name ) {
        if ( is_string($name )){
            $this->name = trim($name);
        }
    }

    public function setImg ($img) {
        if ( is_string($img)){
            $this->img = trim($img);
        }
    }

    public function setRace ($race) {
        if ( is_string($race)){
            $this->race = trim($race);
        }
    }

    /**
     * 
     * @return int The db id of this event
     */
    public function getId() {
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getImg(){
        return $this->img;    
    }

    public function getRace(){
        return $this->race;    
    }

    /**
     * getGetValues: 
     * Filter input and retrieve GET input params
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
     * Check the action and perform action on : 
     * - delete
     * 
     * @param type @get_array all get vars and values
     * @return string the action provided by the $_GET array
     */
    public function handleGetAction( $get_array ){
        $action = '';

        switch($get_array['action']) { 
            case 'update':
                // Indicate curren action is update if id provided 
                if ( !is_null($get_array['id']) ){
                    $action = $get_array['action'];
                }
                break;
            
            case 'delete':
                //Delete current id if provided
                if ( !is_null($get_array['id']) ){
                    $this->delete($get_array);
                }
                $action = 'delete';
                break;
            default:
            //Oops
            break;
        }
        return $action;
    }


    /**
     * 
     * @global type $wpdb Wordpress database
     * @param type $input_array post_array
     * @return boolean TRUE on Succes else FALSE
     * @throws Exception
     */
    public function update($input_array){
        try {
            $array_fields = array('id', 'dogName', 'dogImg', 'dogRace');
            $table_fields = array( 'id_doggie', 'dog_name', 'dog_img', 'dog_race');
            $data_array = array(); 

            //Check fields
            foreach( $array_fields as $field){
                //Check fields


                if (!isset($input_array[$field])){
                    throw new Exception(__("$field is mandatory for update."));
                }
                //Add data_array (without hash idx)
                // (input_array is POST data -> COuld have more fields)
                $data_array[] = $input_array[$field];
            }
        
            global $wpdb;

            //Update query
            
            $wpdb->query($wpdb->prepare("UPDATE ".$this->getTableName().
            " SET  `dog_name` = '%s', `dog_img` = '%s', `dog_race` = '%s' ".
            "WHERE`".$this->getTableName()."`.`id_doggie` =%d;",
            $input_array['dogName'],
            $input_array['dogImg'],
            $input_array['dogRace'],
            $input_array['id']) );

        } catch (Exception $exc) {
            // @todo: Fix error handlin
            echo $exc->getTraceAsString();
            $this->last_error = $exc->getMessage();

            return FALSE;
        }
        return TRUE;
    }

     /**
     * @global type $wpdb
     * @return type string table name with wordpress (and app prefix)
     */
    private function getTableName(){

        global $wpdb;
        return $table = $wpdb->prefix . "doggie"; 
    }

    /**
     * The function takes the input data array and changes the indexes to the coumn names
     * In case of update or insert action
     * 
     * @param type $input_data_array data array(id, name, description)
     * @param type $action          update | insert
     * @return type array with column index and values OR FALSE 
     */
    private function getTableDataArray($input_data_array, $action=''){

        //Get the Table Column Names
        $keys = $this->getTableColumnNames($this->getTableName());

        //Get data array with table columns
        //NULL if columns and data does not match in count
        //
        //Note: The order of the fields shall be the same for both!
        $table_data = array_combine($keys, $input_data_array);

        switch ( $action ){
            case 'update': //Intended fall-through
            case 'insert':
                //Remove the index -> is primary key and can
                //therefore not be changed!
                if (!empty($table_data)){
                    unset($table_data['id_doggie']);
                }
                break;
                //Remove
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
            $result_array = $wpdb->get_results("SELECT `COLUMN_NAME` ".
            "FROM INFORMATION_SCHEMA.COLUMNS".
            "WHERE `TABLE_SCHEMA`='".DB_NAME.
            "' AND TABLE_NAME = '".$this->getTableName() ."'", ARRAY_A);

            $keys = array();
            foreach ( $result_array as $idx => $row ){
                $keys[$idx] = $row['COLUMN_NAME'];
            }
            return $keys;
        } catch (Exception $exc) {
            //@todo: Fix error handlin
            echo $exc->getTraceAsString();
            $this->last_error = $exc->getMessage();
            return FALSE;
        }
    }

    /**
     * 
     * @global type $wpdb The Wordpress database class
     * @param type $input_array containing delete id
     * @return boolean TRUE on succes OR FALSE
     */
    public function delete($input_array){

        try{
            //Check input id
            if (!isset($input_array['id']) )
            throw new Exception(__("Missing mandatory fields") );

            global $wpdb;

            //Delete row by provided id (Wordpress style)
            $wpdb->delete( $this->getTableName(),
                    array( 'id_doggie' => $input_array['id'] ),
                    array( '%d' ) ); //Where format
            //*/

            //Error ? It's in there:
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