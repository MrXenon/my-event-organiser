<?php
//Include model:

include COOPER_MODEL_DIR. "/CooperDoggie.php";

//Declare class variable:
$cooper_doggie = new CooperDoggie();

//Set base url to current file and add page specific vars
$base_url = get_admin_url().'admin.php';
$params = array( 'page' => basename(__FILE__,".php"));

//Add params to base url
$base_url = add_query_arg( $params, $base_url );

//Get the GET data in filtered array
$get_array = $cooper_doggie->getGetValues();

//Keep track of current action
$action = FALSE;

if (!empty($get_array)) {

    //Check actions
    if (isset($get_array['action'])) {
        $action = $cooper_doggie->handleGetAction($get_array);
    }
}

//Get the POST data in filtered array
$post_array = $cooper_doggie->getPostValues();

//Collect Errors
$error = FALSE;

//Check the POST data
if (!empty($post_array)){

    //Check the add form:
    $add = FALSE;
    if (isset($post_array['add']) ){
        // Save event categorie
        $result = $cooper_doggie->save($post_array);
        if ($result){
            //Save was succesfull
            $add = TRUE;
        } else {
            //Indicate error
            $error = TRUE;
        }
    }

    //Check the update form:
    if (isset($post_array['update']) ){
        //Save event categorie
        $cooper_doggie->update($post_array);
    }
}
?>

<div class="wrap">
        Admin event categorie CRUD.<br />
        (Uitje, excursie, etc)

<?php

if(isset($add)){
    echo ($add ? "<p>Added a new event</p>" : "");
}


    //Check if action == update: then start update form
    echo (($action == 'update') ? '<form action="'.$base_url.'"method="post">' : '');
    ?>

    <table>
        <caption>Event type categories</caption>
            <thead>
                <tr>
                    <th width="10">Id</th>
                    <th width="200">Name</th>
                    <th width="200">Img</th>
                    <th width="200">Race</th>
                </tr>
            </thead>
    <!--  <tr><td colspan="3">Event types rij 1</td></tr> -->
    <?php
//*
 if( $cooper_doggie->getNrOfCoopers() < 1){
?>
 <tr><td colspan="3">Start adding Doggies</tr>
<?php 
 } else {
     $cooper_list = $cooper_doggie->getCooperList(); 

     //** Show all event categories in the tabel */
     foreach( $cooper_list as $cooper_obj){
         
        //Create update link
        $params = array( 'action' => 'update', 'id' => $cooper_obj->getId());
        //Add params to base url update link
        $upd_link = add_query_arg( $params,  $base_url );

        //Create delete link
        $params = array( 'action' => 'delete', 'id' => $cooper_obj->getId());

        //Add params to base url delete link
        $del_link = add_query_arg( $params, $base_url);

         ?>
     <tr><td width="10"><?= $cooper_obj->getId(); ?></td>
<?php 
     //If update and id match show update form
     //Add hidden field id for id transfer
     if (   ($action == 'update') && 
            ($cooper_obj->getId() == $get_array['id']) ){ 
?>
    <td width="180"><input type="hidden" name="id" value="<?= $cooper_obj->getId(); ?>">
        <input type="text" name="dogName" value="<?= $cooper_obj->getName(); ?>"></td>
    <td width="200"><input type="text" name="dogImg" value ="<?= $cooper_obj->getImg();?>"></td>
    <td width="200"><input type="text" name="dogRace" value ="<?= $cooper_obj->getRace();?>"></td>
    <td colspan="2"><input type="submit" name="update" value="Updaten"/></td>

    <?php } else {  ?>
        <td width="180"><?= $cooper_obj->getId(); ?></td>
        <td width="180"><?= $cooper_obj->getName(); ?></td>
        <td width="200"><img style="width:auto; Height:250px;" src="<?= $cooper_obj->getImg();?>"></td>
        <td width="200"><?= $cooper_obj->getRace();?></td>
        <?php if ($action !=='update') {
            //If action is update don't show the action button
        ?>
        <td><a href="<?= $upd_link; ?>">Update</a></td>
        <td><a href="<?= $del_link; ?>">Delete</a></td>
    <?php
            } //if action !== update
    ?>
    <?php } // if action !== update ?>
     </tr>
    <?php   }   }
    ?>

    </table>
    <?php

    // var_dump($action);
    //Check if action == update : then end update form
    echo (($action == 'update') ? '</form>' : '');
    /** Finally add the new entery line only if no update action  **/
    if ($action !== 'update'){
    ?>

    <form action="<?= $base_url; ?>" method="post"><tr>
        <table>
            <tr><td colspan="2">
                    <input type="text" name="dogName"></td>
                <td><input type="text" name="dogImg"></td>
                <td><input type="text" name="dogRace"></td></tr>
            <tr><td colspan="2"><input type="submit" name="add" value="Toevoegen"/></td>
            </tr>
        </table>
    </form>
    <?php
    } //if action !== update
    ?>
</div>

<!-- Verder met 2.4. Update werkend maken GUI Formulier Pagina 4/5 
Update het formulier niet -->