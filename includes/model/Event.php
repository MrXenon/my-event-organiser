<?php
require_once COOPER_MODEL_DIR . '/EventCategory.php';
require_once COOPER_MODEL_DIR . '/EventType.php';

class Event
{

    /**
     *
     * @var type EventCategory
     */
    private $event_category = null;
    private $event_type = null;

    public function __construct()
    {

        // Init the category and type
        $this->event_category = new EventCategory();
        $this->event_type = new EventType();
    }

    /**
     *
     * @return type array of EventCategorie
     */
    public function getEventCategoryList()
    {
        return $this->event_category->getEventCategoryList();
    }

    public function getEventTypeList()
    {
        return $this->event_type->getEventTypeList();
    }

    public function checkEventTitle($title)
    {
        if (!is_string($title)) throw new Exception (__('Tekst invullen'));

        if (empty($title)) throw new Exception (__('Verplicht veld!'));
    }

    public function checkCat($cat)
    {
        if (!is_numeric($cat)) throw new Exception (__('Categorie link incorrect'));

        if (strlen($cat) < 1) throw new Exception (__('Verplicht veld!'));
    }

    public function checkType($type)
    {
        if (!is_numeric($type)) throw new Exception (__('Type link incorrect'));

        if (strlen($type) < 1) throw new Exception (__('Verplicht veld!'));
    }

    public function checkDate($date, $empty = FALSE)
    {
        if (!$empty && strlen($date) < 1) throw new Exception (__('Verplicht veld'));

        if (!is_string($date)) throw new Exception (__('Datum tekst formaat yyyy-mm-dd'));
        //@todo check date format
    }

    public function checkInfo($info)
    {
        if (!is_string($info)) throw new Exception(__('Tekst invullen'));
    }

    /**
     *
     * @global WPDB $wpdb Wordpress database class
     * @param string $title
     * @param int $cat -> id
     * @param int $type -> id
     * @param string $info
     * @param date $event_start_date
     * @param date $event_end_date
     * @param date $event_due_date
     * @return boolean
     */
    function save($title, $cat, $type, $info, $event_start_date,
                  $event_end_date, $event_due_date)
    {

        global $wpdb;
        $error = new WP_Error();

        try {
            $this->checkEventTitle($title);
            $this->checkCat($cat);
            $this->checkType($type);
            $this->checkInfo($info);
            $this->checkDate($event_start_date);
            $this->checkDate($event_end_date, TRUE);
            $this->checkDate($event_due_date, TRUE);
        } catch (Exception $exc) {
            $error->add('save', $exc->getMessage());
        }

        // Check on found errors if none save data
        if (count($error->get_error_messages()) < 1) {

            $sql = $wpdb->prepare("INSERT INTO `" . $wpdb->prefix . "meo_event`" .
                "( `event_title`, `fk_event_category`, `fk_event_type`, " .
                "`event_info`, `event_date`, `event_due_date`,`event_end_date`)" .
                " VALUES ( '%s', '%d', '%d', '%s', '%s', " .
                (strlen($event_due_date) ? "'%s'" : 'null') . ", " . // Could be NULL
                (strlen($event_end_date) ? "'%s'" : 'null') . // Could be NULL
                ");",
                $title, $cat, $type, $info, $event_start_date,
                (strlen($event_due_date) ? $event_due_date : 'null'), // Could be NULL
                (strlen($event_end_date) ? $event_end_date : 'null' )// Could be NULL
            );


            //*
            // Check your SQL by adding an additional slash before the ‘/*’
//            echo '<pre>';
//            echo __FILE__ . __LINE__ . '<br />';
//            var_dump($sql);
//            echo '</pre>';
            //*/

            $wpdb->query($sql);


            // Error on save ? It's in there:
            if (!empty($wpdb->last_error)) {
                $this->last_error = $wpdb->last_error;
                $error->get_error_message($this->last_error);

                return $error;
            }

        } else {

            // Some WP_ERROR on input vars
            var_dump($error);
            return $error;
        }

        // Return the last inserted id (Id from the newly created event)
        return $wpdb->insert_id;
    }
}

?>