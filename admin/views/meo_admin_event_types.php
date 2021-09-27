<?php
// Include model:
include MY_EVENT_ORGANISER_PLUGIN_MODEL_DIR . "/EventType.php";

// Declare class variable:
$event_type = new EventType();

// Set base url to current file and add page specific vars
$base_url = get_admin_url() . 'admin.php';
$params = array('page' => basename(__FILE__, ".php"));

// Add params to base url
$base_url = add_query_arg($params, $base_url);

// Get the GET data in filtered array
$get_array = $event_type->getGetValues();

/*
echo '<pre>';
echo __FILE__.__LINE__.'<br />';
var_dump($get_array);
echo '</pre>';
//*/

// Keep track of current action.
$action = FALSE;
if (!empty($get_array)) {

    // Check actions
    if (isset($get_array['action'])) {
        $action = $event_type->handleGetAction($get_array);
    }
}

/* Na checken     */
// Get the POST data in filtered array
$post_array = $event_type->getPostValues();

// Collect Errors
$error = FALSE;
// Check the POST data
if (!empty($post_array['add'])) {

    // Check the add form:
    $add = FALSE;
    // Save event types
    $result = $event_type->save($post_array);
    if ($result) {
        // Save was succesfull
        $add = TRUE;
    } else {
        // Indicate error
        $error = TRUE;
    }
}

// Check the update form:
if (isset($post_array['update'])) {
    // Save event types
    $event_type->update($post_array);
}

/*/
echo '<pre>';
echo __FILE__.__LINE__.'<br />';
var_dump($post_array);
echo '</pre>';
//*/

?>


<div class="wrap">
    Admin event types CRUD. <br/>
    (Open inschrijving, alleen IVS Leden, etc)

    <?php
    if (isset($add)) {
        echo($add ? "<p>Added a new type</p>" : "");
    }
    // Check if action == update : then start update form
    echo(($action == 'update') ? '<form action="' . $base_url . '" method="post">' : '');
    ?>
    <table>
        <caption>Event types</caption>
        <thead>
        <tr>
            <th width="10">Id</th>
            <th width="150">Name</th>
            <th width="200">Description</th>
        </tr>
        </thead>
        <!-- <tr><td colspan="3">Event types rij 1</td></tr> -->
        <?php
        //*
        if ($event_type->getNrOfEventTypes() < 1) {
            ?>
            <tr>
                <td colspan="3">Start adding Event Types
            </tr>
        <?php } else {
            $type_list = $event_type->getEventTypeList();

            //** Show all event types in the tabel
            foreach ($type_list as $event_type_obj) {

                // Create update link
                $params = array('action' => 'update', 'id' => $event_type_obj->getId());

                // Add params to base url update link
                $upd_link = add_query_arg($params, $base_url);

                // Create delete link
                $params = array('action' => 'delete', 'id' => $event_type_obj->getId());

                // Add params to base url delete link
                $del_link = add_query_arg($params, $base_url);
                ?>

                <tr>
                    <td width="10"><?php echo $event_type_obj->getId();
                        ?></td>
                    <?php
                    // If update and id match show update form
                    // Add hidden field id for id transfer
                    if (($action == 'update') && ($event_type_obj->getId() == $get_array['id'])) {
                        ?>
                        <td width="180"><input type="hidden" name="id" value="<?php echo $event_type_obj->getId(); ?>">
                            <input type="text" name="name" value="<?php echo $event_type_obj->getName(); ?>"></td>
                        <td width="200"><input type="text" name="description"
                                               value="<?php echo $event_type_obj->getDescription(); ?>"></td>
                        <td colspan="2"><input type="submit" name="update" value="Updaten"/></td>
                    <?php } else { ?>
                        <td width="180"><?php echo $event_type_obj->getName(); ?></td>
                        <td width="200"><?php echo $event_type_obj->getDescription(); ?></td>
                        <?php if ($action !== 'update') {
                            // If action is update don’t show the action button
                            ?>
                            <td><a href="<?php echo $upd_link; ?>">Update</a></td>
                            <td><a href="<?php echo $del_link; ?>">Delete</a></td>
                            <?php
                        } // if action !== update
                        ?>
                    <?php } // if acton !== update ?>
                </tr>
                <?php
            }
            ?>


        <?php }
        ?>
    </table>
    <?php
    // Check if action = update : then end update form
    echo(($action == 'update') ? '</form>' : '');
    /** Finally add the new entry line only if no update action **/
    if ($action !== 'update') {
        ?>
        <form action="<?php echo $base_url; ?>" method="post">
            <tr>
                <table>
                    <tr>
                        <td colspan="2"><input type="text" name="name"></td>
                        <td><input type="text" name="description"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" name="add" value="Toevoegen"/>
                        </td>
                    </tr>
                </table>
        </form>
        <?php
    } // if action !== update
    ?>
</div>