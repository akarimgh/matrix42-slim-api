<?php
/**
 * Created by PhpStorm.
 * User: fabianhenzler
 * Date: 30/05/15
 * Time: 20:18
 */

namespace matrix42\slim_api;


class Matrix42_API_v1_Admin {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    function admin_menu () {
        add_options_page( 'Matrix42 Slim API','Matrix42 Slim API','manage_options','matrix42_api', array( $this, 'settings_page' ) );
    }

    function  settings_page () {
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
            global $wp_rewrite;
            update_option('slim_base_path',$_REQUEST['slim_base_path']);
            $wp_rewrite->flush_rules(true);
        }
        ?>
        <div class="wrap">
            <h1>Matrix42 Slim API Configuration</h1>
            <form action="" method="post">
                <label>Base Path <input type="text" name="slim_base_path" value="<?php echo get_option('slim_base_path','slim/api/v1/')?>"></label>
                <input type="submit" value="Update" class="button-primary">
            </form>
        </div>
    <?php
    }
}