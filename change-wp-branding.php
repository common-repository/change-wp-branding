<?php 
/*
Plugin Name: Change WP Branding

Plugin URI: https://plugins.wphelpline.com

Description: Modify WP Admin Branding and Color Scheme

Version: 1.0.0

Author: WPHelpline

Author URI: https://wphelpline.com

License: GPLv2 or later

Text Domain: change-wp-branding

*/
add_action("admin_menu", "change_wp_branding_options_submenu");
function change_wp_branding_options_submenu() {

	// Add Submenu Page Under Tools
	add_submenu_page(
		'tools.php',
		'Change WP Branding Settings',
		'Change WP Branding',
		'manage_options',
		'change-wp-branding-options',
		'change_wp_branding_settings_page' );
}

// Submenu Page Callback
function change_wp_branding_settings_page() {
	// Get Option Array 
	$change_wp_branding_options = get_option('change_wp_branding'); ?>
	<div class="cwb-wrap">

		<!-- Form Styling -->
		<style>
			.cwb-wrap { padding: 10px; color: #2271b1; }
			.cwb-form .cwb-form-group { padding-top: 15px; }
			.cwb-form label { display: block; font-size: 14px; color: black; font-weight: 500; padding-bottom: 10px; padding-left: 2px; }
			.cwb-form .cwb-form-field { width: 50%; height: 40px; }
			.cwb-form .cwb-small-field { width: 5%; cursor: pointer; }
			.cwb-form #submit { padding: 2px; width: 8%; font-size: 16px; }
		</style>

		<h1><?php _e('Change WP Branding Settings', 'change-wp-branding'); ?></h1>
		<p><?php _e('Manage WP Admin Branding & Colors', 'change-wp-branding'); ?></p>

		<form action="" class="cwb-form" method="post">
			<div class="cwb-form-group">
				<label for="login_logo"><?php _e('Set Login Logo URL', 'change-wp-branding'); ?></label>
				<input type="url" name="change_wp_branding[login_logo]" value="<?php echo isset($change_wp_branding_options['login_logo']) ? esc_url($change_wp_branding_options['login_logo']) : ''; ?>" class="cwb-form-field">
			</div>

			<div class="cwb-form-group">
				<label for="login_header_url"><?php _e('Set Login Header URL', 'change-wp-branding'); ?></label>
				<input type="url" name="change_wp_branding[login_header_url]" value="<?php echo isset($change_wp_branding_options['login_header_url']) ? esc_url($change_wp_branding_options['login_header_url']) : ''; ?>" class="cwb-form-field">
			</div>

			<div class="cwb-form-group">
				<label for="login_header_title"><?php _e('Set Login Header Title', 'change-wp-branding'); ?></label>
				<input type="text" name="change_wp_branding[login_header_title]" value="<?php echo isset($change_wp_branding_options['login_header_title']) ? esc_attr($change_wp_branding_options['login_header_title']) : ''; ?>" class="cwb-form-field">
			</div>

			<div class="cwb-form-group">
				<label for="login_button_color"><?php _e('Select Login Button Color', 'change-wp-branding'); ?></label>
				<input type="color" name="change_wp_branding[login_button_color]" value="<?php echo isset($change_wp_branding_options['login_button_color']) ? esc_attr($change_wp_branding_options['login_button_color']) : '#398fdf'; ?>" class="cwb-form-field cwb-small-field">
			</div>

			<div class="cwb-form-group">
				<label for="login_button_text_color"><?php _e('Select Login Button Text Color', 'change-wp-branding'); ?></label>
				<input type="color" name="change_wp_branding[login_button_text_color]" value="<?php echo isset($change_wp_branding_options['login_button_text_color']) ? esc_attr($change_wp_branding_options['login_button_text_color']) : '#ffffff'; ?>" class="cwb-form-field cwb-small-field">
			</div>

			<?php submit_button( 'Save' ); ?>
			<?php wp_nonce_field( 'change-wp-branding-options-save', 'change-wp-branding-options-save-nonce' ); ?>

		</form>

	</div>
<?php }

// Check Nonce
function change_wp_branding_user_can_save( $action, $nonce ) {

	$is_nonce_set   = isset( $_POST[ $nonce ] );
	$is_valid_nonce = false;
	
	if ( $is_nonce_set ) {
		$is_valid_nonce = wp_verify_nonce( $_POST[ $nonce ], $action );
	}

	return ( $is_nonce_set && $is_valid_nonce );

}

// Save Form Fields
add_action( 'load-tools_page_change-wp-branding-options', 'change_wp_branding_save_options' );
function change_wp_branding_save_options() { 

	$action       = 'change-wp-branding-options-save';
	$nonce        = 'change-wp-branding-options-save-nonce';

	// If the user doesn't have permission to save, then display an error message
	if ( ! change_wp_branding_user_can_save( $action, $nonce ) ) {
		return;
	}

	if ( isset( $_POST['change_wp_branding'] ) ) {
		// Update Option 

		$change_wp_branding_input = array();

		if( !empty($_POST['change_wp_branding']) ){
			
			foreach ( $_POST['change_wp_branding'] as $key => $value ) { 
				$change_wp_branding_input[$key] = sanitize_text_field( $value ); 
			}
		}

		update_option( 'change_wp_branding', $change_wp_branding_input );
	} else {			
		// Delete Option 
		delete_option( 'change_wp_branding' );			
	} 
}

// Change WP Login Logo
add_action( 'login_enqueue_scripts', 'change_wp_branding_login_logo' );
function change_wp_branding_login_logo() {
	$change_wp_branding_options = get_option('change_wp_branding');

	$login_logo = isset($change_wp_branding_options['login_logo']) ? $change_wp_branding_options['login_logo'] : '';
	$login_button_color = isset($change_wp_branding_options['login_button_color']) ? $change_wp_branding_options['login_button_color'] : '#398fdf';
	$login_button_text_color = isset($change_wp_branding_options['login_button_text_color']) ? $change_wp_branding_options['login_button_text_color'] : '#171717'; ?>

	<style>
		<?php if( !empty($login_logo) ){ ?>
			#login h1 a, .login h1 a {
				background-image: url(<?php echo esc_url($login_logo); ?>);
				height:65px; width:320px; background-size: 320px 65px; background-repeat: no-repeat; padding-bottom: 30px;
			}
		<?php } ?>
		#login #wp-submit { background: <?php echo esc_attr($login_button_color); ?>; color: <?php echo esc_attr($login_button_text_color); ?>; border-color: <?php echo esc_attr($login_button_color); ?>; }
		#login .message { border-left: 4px solid <?php echo esc_attr($login_button_color); ?>; }
		#login #user_login:focus { border-color: <?php echo esc_attr($login_button_color); ?>; box-shadow: 0 0 0 1px <?php echo esc_attr($login_button_color); ?>; }
		#login #user_pass:focus { border-color: <?php echo esc_attr($login_button_color); ?>; box-shadow: 0 0 0 1px <?php echo esc_attr($login_button_color); ?>; }
		#login #wp-submit:focus { box-shadow: 0 0 0 1px #ffffff, 0 0 0 3px <?php echo esc_attr($login_button_color); ?>; }
	</style>
	
<?php } 

// Change WP Login URL
add_filter( 'login_headerurl', 'change_wp_branding_login_url' );
function change_wp_branding_login_url() {
	$change_wp_branding_options = get_option('change_wp_branding');

	$login_header_url = isset($change_wp_branding_options['login_header_url']) ? $change_wp_branding_options['login_header_url'] : home_url();
	return $login_header_url;
}

// Change WP Login Logo alt text
add_filter( 'login_headertitle', 'change_wp_branding_login_title' );
function change_wp_branding_login_title() {
	$change_wp_branding_options = get_option('change_wp_branding');

	$login_header_title = isset($change_wp_branding_options['login_header_title']) ? $change_wp_branding_options['login_header_title'] : get_option( 'blogname' );
	return $login_header_title;
}