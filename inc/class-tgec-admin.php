<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 31/01/18
 * Time: 09:54 PM
 */

class Tgec_admin{


    /**Page IDs generated by add_menu functions. Used internally for CSS purposes*/
    public $main_page_id;
    public $add_new_event_id;

    /**URLS of different admin pages */
    public $admin_url;
    public $admin_add_new_url;

    /**Constructor */
    public function __construct(){
        add_action( 'admin_menu', array( $this, 'tgec_admin_menus' ) );
    }

    /**Registers Menus */
    public function tgec_admin_menus(){

        $main_slug = 'wp-tgec';
        $add_new_slug = 'tgec-add-new';

        //Adds Main Menu to wp-admin
        $this->main_page_id =
            add_menu_page(
              'TGE Calendar',
              'TGE Calendar',
              'administrator',
              $main_slug, //menu-slug
              array( $this, 'tgec_admin_page_main' ) //callback function
            );

        //Adds sub-menu for Add-new event
        $this->add_new_event_id =
            add_submenu_page(
                'wp-tgec',
                'Add new',
                'Add new',
                'administrator',
                $add_new_slug,
                array($this, 'tgec_admin_page_add_new')
            );

        //URLS of different admin pages
        $this->admin_url = admin_url( 'admin.php?page=' . $main_slug );
        $this->admin_add_new_url = admin_url( 'admin.php?page=' . $add_new_slug );
    }

    /** Renders the whole admin page */
    public function tgec_admin_page_main(){
        $event_list = new Tgec_admin_list();

        $this->tgec_admin_page_header( 'events-list');
        $event_list->prepare_items();
        $event_list->display();

    }

    /** Renders the Admin page Header */
    private function tgec_admin_page_header( $active_page = 'events-list' ){
        ?>
        <div id="tge-admin-page" class="wrap">
            <header>
                <h1 id="tge-title">The great events Calendar</h1>
                <h2 class="nav-tab-wrapper">
                    <?php
                    if( current_user_can( 'administrator' ) || ( 'editor' ) ){
                        ?>
                        <a href="<?php echo $this->admin_url;?>" class="nav-tab<?php echo ( 'events-list' == $active_page )? ' nav-tab-active' : '';?>">
                            <?php echo _e('All events'); ?>
                        </a>
                        <a href="<?php echo $this->admin_add_new_url;?>" class="nav-tab<?php echo ( 'new-event' == $active_page )? ' nav-tab-active' : '';?>">
                            <?php _ex( 'Add New', 'submenu item text', 'wp-tgec' ); ?>
                        </a>
                        <?php
                    }
                    else{
                        echo 'Sorry, you do not have access';
                    }
                    ?>
                </h2>
            </header>
        </div>
        <?php
    }

    /** Renders Add New Event Admin Page */
    public function tgec_admin_page_add_new() {

        global $tgec_db;

        $this->tgec_admin_page_header( 'new-event' );

        $this->pseudo_meta_box(
                'add-new-event',
                'Add New Event',
                $this->tgec_edit_form()
        );

        $tgec_db->put_event();

    }

    public function tgec_edit_form(){
        $display =<<< EDITFORM
        <form action="#event_form" method="post" id="event_form">
            <h3>You can enter your new Event here:</h3>
            <ul>
            <li><input type="text" name="event_title" id="event_title" placeholder="Event Title"/></li>
            <li><textarea name="event_details" placeholder="Event Details" cols="50" rows="5"></textarea></li>
            <li><input type="submit" name="submit" value="Save event" /></li>
            </ul>
        </form>
EDITFORM;
        return $display;
    }

    /**Renders the pseudo-metabox to display the content or add/edit events **/
    private function pseudo_meta_box($id, $title = "", $content = ""){
        ?>
        <div id="poststuff" class="wrap meta-box-holder">
            <div id="normal-sortables" class="meta-box-sortables">
                <div id="<?php echo $id; ?>" class="postbox " >
                    <h3 class="hndle" style="cursor:default;"><span><?php echo $title; ?></span></h3>
                    <div class="inside">
                        <?php echo $content; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
