<?php
/**
 * Created by PhpStorm.
 * User: wissilesogoyou
 * Date: 6/16/15
 * Time: 3:20 PM
 */
?>



<p><?php echo( 'Welcome to our BAY EAST plugin page if you experience any issue with this plugin,
<strong>please contact <a href="mailto:wissiles@bayeast.org">Wissile Sogoyou</a></strong>' ); ?></p>

<div class="wrap">

    <div id="icon-options-general" class="icon32"></div>
    <h2><?php esc_attr_e( 'HTTP User Sync', 'wp_admin_style' ); ?></h2>
    <p><?php esc_attr_e( 'HTTP Member synchronisation between a different membership database (on server 1) and this WordPress database (on server 2)' ); ?></p>

<?php
function ilc_admin_tabs($current = 'status') {
    $tabs = array('status' => 'Status', 'test' => 'Test');
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $class = ($tab == $current) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=http-user-sync&tab=$tab'>$name</a>";

    }
    echo '</h2>';
}



if (isset ($_GET['tab'])) ilc_admin_tabs($_GET['tab']); else ilc_admin_tabs('status');

$memStatus = get_option( 'usersync' );
$memStatusTime = get_option( 'usersynctime' );
$memStatusUpdated = get_option( 'usersyncupdated' );
$memStatusDeleted = get_option( 'usersyncdeleted' );
$memStatusInserted = get_option( 'usersyncinserted' );

?>

        <?php


        if ( $_GET['page'] == 'http-user-sync') {

            if (isset ($_GET['tab'])) {
                $tab = $_GET['tab'];
            } else {
                $tab = 'status';
            }


            switch ($tab) {


                    case 'status' :
                        ?>

                        <!-- SECOND Page -->

                        <h2><?php esc_attr_e( 'Request Status', 'wp_admin_style' ); ?></h2>

                        <div class="updated">
                        <h3>Users Report <em style="font-size:13px;color: #808080;margin-left: 10px;">(Last sync: <span style="color:red;"><?php echo $memStatusTime ?></span>)</em></h3>
                        <div class="line"></div>
                            <p> <strong>Total Users:</strong>
                                <?php
                                    global $wpdb;
                                    $user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
                                    echo $user_count;
                                ?>
                            </p>
                            <span>----------------------------------------------------------------</span>
                            <p> <strong>Staff Users:</strong>
                                <?php echo (count( get_users( array( 'role' => 'editor' ) ) )) + (count( get_users( array( 'role' => 'administrator', 'editor', 'author', 'contributor' ) ) ))?>
                            </p>
                            <p> <strong>Members:</strong>
                                <?php echo count( get_users( array( 'role' => 'subscriber' ) ) )?>
                            </p>
                            <p> <strong>Realtors:</strong>
                                <?php echo count( get_users( array(
                                        'meta_key'     => 'type',
                                        'meta_value'   => 'realtor'
                                ) ) ); ?>
                            </p>
                        </div>


                        <div class="error">
                            <h3>Errors Report</h3>
                            <div class="line"></div>
                            <p><strong>Recorded Errors:</strong> <?php echo 'Synced without errors'; ?></p>
                        </div>


                        <div class="updated">
                        <h3>System Status</h3>
                        <div class="line"></div>
                            <p><strong>Home Url: </strong><?php echo home_url(); ?><p/>
                            <p><strong>Site Url: </strong><?php echo site_url(); ?><p/>
                            <p><strong>Admin Email: </strong><?php echo '<a href="mailto:'. get_bloginfo('admin_email') .'">'.get_bloginfo('admin_email').'</a>' ?><p/>
                            <p><strong>Host Name: </strong><?php echo gethostname(); ?><p/>
                            <p><strong>Server Address: </strong><?php echo $_SERVER['HTTP_HOST'] ?><p/>
                            <p><strong>IP Address: </strong><?php echo $_SERVER['REMOTE_ADDR'] ?><p/>
                            <p><strong>WordPress Version: </strong><?php echo get_bloginfo('version') ?><p/>
                            <p><strong>PHP Version: </strong><?php echo phpversion() ?><p/>
                            <p><strong>MySQL Client: </strong><?php echo mysql_get_client_info() ?><p/>
                            <p><strong>MySQL Host: </strong><?php echo mysql_get_host_info() ?><p/>
                            <p><strong>MySQL Protocol: </strong><?php echo mysql_get_proto_info() ?><p/>
                            <p><strong>PHP OS: </strong><?php echo PHP_OS; ?><p/>
                        </div>




                        <?php
                        break;
                    case 'test' :
                        ?>

                        <!-- THIRD Page -->

                        <h2><?php esc_attr_e( 'Request Response', 'wp_admin_style' ); ?></h2>

                        <div class="wrap">

                            <div id="icon-options-general" class="icon32"></div>


                            <div id="poststuff">

                                <div id="post-body" class="metabox-holder columns-2">

                                    <!-- main content -->
                                    <div id="post-body-content">

                                        <div class="meta-box-sortables ui-sortable">

                                            <div class="postbox">

                                                <h3><?php esc_attr_e( 'Server Request Response', 'wp_admin_style' ); ?></h3>
                                                <div class="line"></div>
                                                <div class="inside">



                                                <?php
                                                    if( $memStatus != '' ) {
                                                        echo '<h4>User Synchronization Time: <span style="color:red;">'. $memStatusTime .'</span> </h4>
                                                        <h4>Total of Updated User: <span style="color:red;">'. $memStatusUpdated .'</span></h4>
                                                        <h4>Total of Deleted User: <span style="color:red;">'. $memStatusDeleted .'</span></h4>
                                                        <h4>Total of Inserted User: <span style="color:red;">'. $memStatusInserted .'</span></h4>
                                                        <p><div class="line"></div>
                                                        <h4 style="color:red;">JSON Users Report</h4>' . json_encode($memStatus) . '</p> ';
                                                    }else {
                                                        echo 'There is no recent server request / user synchronisation';
                                                    }

                                                ?>

                                                </div>
                                                <!-- .inside -->

                                            </div>
                                            <!-- .postbox -->





                                        </div>
                                        <!-- .meta-box-sortables .ui-sortable -->

                                    </div>
                                    <!-- post-body-content -->

                                    <!-- sidebar -->
                                    <div id="postbox-container-1" class="postbox-container">

                                        <div class="meta-box-sortables">

                                            <div class="postbox">

                                                <h3><span><?php esc_attr_e(
                                                            'About your computer', 'wp_admin_style'
                                                        ); ?></span></h3>

                                                <div class="inside">
                                                    <strong>Home Url: </strong><?php echo home_url(); ?><br/>
                                                    <strong>Site Url: </strong><?php echo site_url(); ?><br/>
                                                    <strong>Admin Email: </strong><?php echo '<a href="mailto:'. get_bloginfo('admin_email') .'">'.get_bloginfo('admin_email').'</a>' ?><br/>
                                                    <strong>Host Name: </strong><?php echo gethostname(); ?><br/>
                                                    <strong>Server Address: </strong><?php echo $_SERVER['HTTP_HOST'] ?><br/>
                                                    <strong>IP Address: </strong><?php echo $_SERVER['REMOTE_ADDR'] ?><br/>
                                                    <strong>WordPress Version: </strong><?php echo get_bloginfo('version') ?><br/>
                                                    <strong>PHP Version: </strong><?php echo phpversion() ?><br/>
                                                    <strong>MySQL Client: </strong><?php echo mysql_get_client_info() ?><br/>
                                                    <strong>MySQL Host: </strong><?php echo mysql_get_host_info() ?><br/>
                                                    <strong>MySQL Protocol: </strong><?php echo mysql_get_proto_info() ?><br/>
                                                    <strong>PHP OS: </strong><?php echo PHP_OS; ?><br/>
                                                </div>
                                                <!-- .inside -->

                                            </div>
                                            <!-- .postbox -->

                                            <div class="postbox">

                                                <h3><span><?php esc_attr_e(
                                                            'About the plugin', 'wp_admin_style'
                                                        ); ?></span></h3>

                                                <div class="inside">
                                                    <p>Welcome to our BAY EAST plugin page if you experience any issue with this plugin, please contact <a href="mailto:wissiles@bayeast.org">Wissile Sogoyou</a></p>
                                                    <p>Please read more about the plugin on <a href="">github</a> or in the <a href="http://bayeast.org">website</a>.</p>
                                                        <p>© Copyright 2015 - 2015	BayEast</p>
                                                </div>
                                                <!-- .inside -->

                                            </div>
                                            <!-- .postbox -->

                                        </div>
                                        <!-- .meta-box-sortables -->

                                    </div>
                                    <!-- #postbox-container-1 .postbox-container -->

                                </div>
                                <!-- #post-body .metabox-holder .columns-2 -->

                                <br class="clear">
                            </div>
                            <!-- #poststuff -->

                        </div> <!-- .wrap -->

                        <?php
                        break;
            }

        }


?>











</div> <!-- .wrap -->
