<?php
/**
 * Plugin Name: Ajax Simple Auth Request Handling.
 * Description: Handles simple authentication requests.
 * Author: Arafat
 */
class Simple_Auth_ajax_Request_Handling {
    function __construct() {
        add_shortcode( 'simple-auth', [$this, 'render_shortcode']);
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
        add_action( 'wp_ajax_simple_auth_profile_form', [ $this, 'update_profile'] );
    }
    function render_shortcode() {
        if ( is_user_logged_in() ) {
            return $this->render_profile_page();
        }else{
            $this->render_auth_page();
        }
    }
    private function render_auth_page() {

    }
    private function render_profile_page() {
        $user = wp_get_current_user();
        ob_clean();
        ?>
        <div id="simple-auth-profile">
            <h2>Update Profile</h2>
            <form action="" method="POST" id="profile-form">
                <input type="text" name="display_name" value="<?= $user->display_name; ?>">
                <input type="email" name="email" value="<?= $user->user_email; ?>">
                <input type="hidden" name="action" value="simple_auth_profile_form">
                <button name="submit" type="submit">Update Profile</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    function enqueue_scripts() {
        wp_enqueue_style( 'profile-auth-css', plugin_dir_url(__FILE__) . "assets/css/profile-auth.css");
        wp_enqueue_script( 'profile-auth-js', plugin_dir_url(__FILE__) . "assets/js/profile-auth.js",['jquery'] );
        $data = [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce( 'simple-auth-profile-form' )
        ];
        wp_localize_script('profile-auth-js','dataBank',$data );
    }
    function update_profile() {
        check_ajax_referer( 'simple-auth-profile-form' );
        $display_name = sanitize_text_field( $_POST['display_name'] );
        $email = sanitize_email( $_POST['email'] );
        $user = wp_get_current_user();
        $user_data = [
            'ID' => $user->ID,
            'display_name' => $display_name,
            'user_email' => $email
        ];
        $user_id = wp_update_user( $user_data );
        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error([
                'massage' => $user_id->get_error_message()
            ]);
        }
            wp_send_json_success([
                'massage' => 'Profile updated successfully.',
            ]);
        exit;
        
    }
    
}
new Simple_Auth_ajax_Request_Handling();