<?php
/**
 * Created by PhpStorm.
 * User: wissilesogoyou
 * Date: 4/13/15
 * Time: 5:57 PM
 *
 * Plugin Name: HTTP User Sync
 *
 * Plugin URI: http://bayeast.org
 * Description:  HTTP Member synchronisation between a different membership database (on server 1) and this WordPress database (on server 2)
 * Version: 1.4.2
 * Author: Wissile Sogoyou
 * Author URI: http://bayeast.org
 * Network: true - Whether the plugin can only be activated network wide.
 * License: Bay East
 *
 * Add a link to our plugin in the admin menu
 * under 'Settings > User Sync'
 *
 */




function user_sync_options_page () {

    if( !current_user_can( 'manage_options' ) ) {
        wp_die( 'You do not have sufficient permissions to access this page: <strong>FULL ADMIN ACCESS IS REQUIRED
                <p>Please contact <a href="mailto:wissiles@bayeast.org">Wissile Sogoyou</a></strong></p>' );
    }

    require('inc/options-page-wrapper.php');
}


function user_sync_menu () {
    /*
     * Use the add_options_page function
     * add_option_page( $page_title, $menu_title, $capability, $menu-slug, $function )
     *
     */

    add_menu_page (
        'HTTP User Sync',
        'HTTP User Sync',
        'manage_options',
        'http-user-sync',
        'user_sync_options_page',
        'dashicons-update'
    );

}

add_action( 'admin_menu', 'user_sync_menu' );



function wp_user_sync_styles () {
    wp_enqueue_style( 'wp_user_sync_styles', plugins_url( 'HTTP-User-Sync/wp-user-sync.css' ) );
}
add_action( 'admin_head', 'wp_user_sync_styles');







// Get HTTP request content 
$json = file_get_contents('php://input');
// decode json content as objects
$object = json_decode($json);
// access action value 
$action = $object->{'action'};


// if value of action is add-user them init user_sync function
if ( $action == "add-user") {
    add_action('init','user_sync');
}



// function to iterate through array of users and process them accordingly
function user_sync() {

    // Get HTTP request content
    $json = file_get_contents('php://input');
    // decode json content as objects
    $object = json_decode($json);
    // access array of users and store it in variable (to be reviewed)
    $users = $object->{'users'};

    //Initialize to 0 count for each member processed
    $updatedNumber = 0;
    $deletedNumber = 0;
    $insertedNumber = 0;

    // iterate through array of users to process
    foreach ($object->users as $user) {

        // store users data in var
        $username = $user->username;
        $password = $user->password;
        $email = $user->email;
        $firstName = $user->firstname;
        $lastName = $user->lastname;
        $status = $user->status;
        $firmName = $user->firmname;
        $type = $user->type;
        $address1 = $user->address1;
        $address2 = $user->address2;
        $city = $user->city;
        $state = $user->state;
        $zip = $user->zip;
        $phone = $user->phone;




        // check if username already exist in our db so we wether update or delete
        if (username_exists($username) && $status != 'delete') {
            // get the userid of that username to manipulate it
            $user = get_user_by('login', $username);
            $user_id = $user->ID;


            $userdata = array(
                'ID' => $user_id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_pass' => $password,
                'user_email' => $email
            );
            $user_id = wp_update_user($userdata);

            // Update or add extra field to this user database from HTTP request
            update_user_meta($user_id, 'firm_name', $firmName);
            update_user_meta($user_id, 'type', $type);
            update_user_meta($user_id, 'address1', $address1);
            update_user_meta($user_id, 'address2', $address2);
            update_user_meta($user_id, 'city', $city);
            update_user_meta($user_id, 'state', $state);
            update_user_meta($user_id, 'zip', $zip);
            update_user_meta($user_id, 'phone', $phone);
            update_user_meta($user_id, 'status', $status);

            $membersStatusArray = array('Member' => $username, 'Status' => 'Updated');
            $membersStatus[] = $membersStatusArray;
            $updatedNumber++;
        }

        if (username_exists($username) && $status == 'delete') {
            // get the userid of that username to manipulate it
            $user = get_user_by('login', $username);
            $user_id = $user->ID;

            $membersStatusArray = array('Member' => $username, 'Status' => 'Deleted');
            $membersStatus[] = $membersStatusArray;
            $deletedNumber++;

            $user_id = wp_delete_user($user_id);

            // Update or add extra field to this user database from HTTP request
            delete_user_meta($user_id, 'firm_name', $firmName);
            delete_user_meta($user_id, 'type', $type);
            delete_user_meta($user_id, 'address1', $address1);
            delete_user_meta($user_id, 'address2', $address2);
            delete_user_meta($user_id, 'city', $city);
            delete_user_meta($user_id, 'state', $state);
            delete_user_meta($user_id, 'zip', $zip);
            delete_user_meta($user_id, 'phone', $phone);
            delete_user_meta($user_id, 'status', $status);
        }

        // if there is no record of that user then insert it as a new user
        if (!username_exists($username) && $status != 'delete') {

            $userdata = array(
                'user_login' => $username,
                'user_pass' => $password,
                'user_email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName
            );
            $user_id = wp_insert_user($userdata);



            // Update or add extra field to this user database from HTTP request
            update_user_meta($user_id, 'firm_name', $firmName);
            update_user_meta($user_id, 'type', $type);
            update_user_meta($user_id, 'address1', $address1);
            update_user_meta($user_id, 'address2', $address2);
            update_user_meta($user_id, 'city', $city);
            update_user_meta($user_id, 'state', $state);
            update_user_meta($user_id, 'zip', $zip);
            update_user_meta($user_id, 'phone', $phone);
            update_user_meta($user_id, 'status', $status);

            $membersStatusArray = array('Member' => $username, 'Status' => 'Inserted');
            $membersStatus[] = $membersStatusArray;
            $insertedNumber++;
        }

    }




// IMPORTANT: server side validation return response with status

    //On success
    if( !is_wp_error($user_id) ) {
        $membersStatus;
        update_option( 'usersync', $membersStatus );
        date_default_timezone_set('America/Los_Angeles');
        $userSyncTime = date('l jS \of F Y h:i:s A');
        update_option( 'usersynctime', $userSyncTime );
        update_option( 'usersyncupdated', $updatedNumber );
        update_option( 'usersyncdeleted', $deletedNumber );
        update_option( 'usersyncinserted', $insertedNumber );
    } else {
        $membersStatus = $user_id->get_error_message();
        update_option( 'usersync', $membersStatus );
        date_default_timezone_set('America/Los_Angeles');
        $userSyncTime = date('l jS \of F Y h:i:s A');
        update_option( 'usersynctime', $userSyncTime );
    }

    // kill all tasks while returning users status
    wp_die( json_encode( $membersStatus ) );

}





