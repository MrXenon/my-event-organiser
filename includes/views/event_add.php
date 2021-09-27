<?php

if (function_exists('current_user_can') &&
    !current_user_can('ivs_meo_event_create'))
    die(__('Cheatin&#8217; uh?', 'my_event_organiser'));

// Include the Event class from the model.
require_once MY_EVENT_ORGANISER_PLUGIN_MODEL_DIR . '/Event.php';

$event = new Event();

//Get the list with the event categories
$event_cat_list = $event->getEventCategoryList();
$event_type_list = $event->getEventTypeList();

//Set timezone default:
date_default_timezone_set('Europe/Amsterdam');

/*Include this section */

require_once(MY_EVENT_ORGANISER_PLUGIN_INCLUDES_DIR . '/calendar/classes/tc_calendar.php');

?>
<h2><?php echo __('Evenement aanmaken') ?></h2>
<p/>
<h3><?php echo __('Menu') ?></h3>
<form action="<?php echo $file_base_url; ?>" method="post">
    <table>
        <tr>
            <td><?php echo __('Titel:'); ?></td>
            <td><input type="text" name="title" value="<?php echo $post_inputs['title'] ?>"/></td>
        </tr>
        <tr>
            <td><?php echo __('Selecteer evenement categorie:'); ?></td>
            <td><select name="cat">

                    <?php
                    // Create the category drop down
                    foreach ($event_cat_list as $event_cat_obj) {
                        ?>
                        <option value="<?php echo $event_cat_obj->getId(); ?>"><?php
                            echo $event_cat_obj->getName(); ?></option>
                    <?php }
                    ?>

                </select></td>
        </tr>
        <tr>
            <td><?php echo __('Selecteer inschrijvingstype:'); ?></td>
            <td><select name="type">

                    <?php

                    // Create the category drop down
                    foreach ($event_type_list as $event_type_obj) {
                        ?>
                        <option value="<?php echo $event_type_obj->getId(); ?>"><?php
                            echo $event_type_obj->getName(); ?></option>
                    <?php }
                    ?>

                </select></td>
        </tr>
        <tr>
            <td><?php echo __('Evenement datum:<br />'); ?></td>
            <td>
                <?php
                $calendar_dir = site_url() . "/wp-content/plugins/my-event-organiser/includes/calendar/";

                $event_date_default = is_null($post_inputs['event_date']) ? Date("Y-m-d") : $post_inputs['event_date'];
                $end_date_default = is_null($post_inputs['end_date']) ? '' :
                    $post_inputs['end_date'];
                $due_date_default = is_null($post_inputs['due_date']) ? Date("Y-m-d") :
                    $post_inputs['due_date'];

                $myCalendar = new tc_calendar("event_date", true /*, false */);
                $myCalendar->setIcon($calendar_dir . "/images/iconCalendar.gif");
                $myCalendar->setDate(date('d', strtotime($event_date_default))
                    , date('m', strtotime($event_date_default))
                    , date('Y', strtotime($event_date_default)));
                $myCalendar->setPath($calendar_dir);
                $myCalendar->setYearInterval(Date("Y"), intval(Date("Y")) + 5);
                $myCalendar->setAlignment('left', 'bottom');
                $myCalendar->setDatePair('event_date', 'end_date', $end_date_default);
                $myCalendar->writeScript();

                $myCalendar = new tc_calendar("end_date", true /*, false */);
                $myCalendar->setIcon($calendar_dir . "images/iconCalendar.gif");
                $myCalendar->setDate(date('d', strtotime($end_date_default))
                    , date('m', strtotime($end_date_default))
                    , date('Y', strtotime($end_date_default)));
                $myCalendar->setPath($calendar_dir);
                $myCalendar->setYearInterval(Date("Y"), intval(Date("Y")) + 5);
                $myCalendar->setAlignment('left', 'bottom');
                $myCalendar->setDatePair('event_date', 'end_date', $event_date_default);

                ?><span class="error"><?php echo $form_result->get_error_message('event_date'); ?></span></td>
        </tr>

        <!-- <input type="text" name="event_date" /></td></tr> -->
        <tr>
            <td><?php echo __('Eind datum: <br />'); ?></td>
            <td>

                <?php
                $myCalendar->writeScript();
                ?><span class="error">
                <?php echo $form_result->get_error_message('end_date');
                ?></span></td>

        </tr>
        <tr>
            <td><?php echo __('Uiterlijke inschrijfdatum'); ?></td>
            <td>

                <?php
                $myCalendar = new tc_calendar("due_date", true);
                $myCalendar->setIcon($calendar_dir . "images/iconCalendar.gif");
                $myCalendar->setDate(date('d', strtotime($due_date_default))
                    , date('m', strtotime($due_date_default))
                    , date('Y', strtotime($due_date_default)));
                $myCalendar->setPath($calendar_dir);
                $myCalendar->setYearInterval(Date("Y"), intval(Date("Y")) + 10);
                $myCalendar->dateAllow(Date("Y") . '-01-01', (intval(Date("Y")) + 2) . '-01-01');
                // Disable some days:
                //$myCalendar->setSpecificDate(array("2016-04-06", "2016-04-07", "2016-04-10"), 0, 'month');
                //$myCalendar->setOnChange("myChanged('test')");
                $myCalendar->writeScript(); ?><span
                        class="error"><?php echo $form_result->get_error_message('due_date'); ?></span></td>
        </tr>


        <!-- <input type="text" name="due_date"/></td></tr> -->
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td><?php echo __('Extra informatie:'); ?></td>
            <td><textarea
                        name="info" rows="4" cols="5"><?php echo
                    $post_inputs['info']; ?></textarea>
                <span class="error"><?php echo $form_result->get_error_message('info'); ?></span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="add_event"
                       value="<?php echo __('Aanmaken'); ?>"/></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>
</form>