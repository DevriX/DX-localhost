<?php
/**
 * Plugin Name: DX localhost
 * Plugin URI: https://wordpress.org/plugins/dx-localhost/
 * Description: Display a notice box when you're working on localhost or a staging server
 * Version: 1.3
 * Author: DevriX
 * Author URI: http://devrix.com/
 * Text Domain: dx_loc
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/mpeshev/DX-localhost
 * License: GPL2
 */
 
/**
 Copyright 2016 DevriX (email : contact AT devrix DOT com)
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as
 published by the Free Software Foundation.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Some defines
 */

if ( ! defined( 'DX_LOCALHOST_VERSION' ) ) {
	define( 'DX_LOCALHOST_VERSION', '1.3' );
}

if ( ! defined( 'DX_LOCALHOST_URL' ) ) {
	define( 'DX_LOCALHOST_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'DX_LOCALHOST_ASSETS_URL' ) ) {
	define( 'DX_LOCALHOST_ASSETS_URL', plugins_url( '/assets/', __FILE__ ) );
}

if ( ! class_exists( 'DX_Localhost' ) ) :
class DX_Localhost {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'dx_localhost_menu' ) );
		add_action( 'admin_init', array( $this, 'dx_localhost_admin_init' ) );
		add_action( 'plugins_loaded', array( $this, 'dx_localhost_load_textdomain' ) );
		add_action( 'admin_bar_menu', array( $this, 'dx_localhost_admin_bar_menu' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'dx_localhost_display_notice_line' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'dx_localhost_display_notice_line' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'dx_enqueue_color_picker' ) );
	}
	 
	/**
	 * Verify login activities and load script if on localhost
	 */
	function dx_localhost_display_notice_line() {
		$dx_localhost_settings = get_option( 'dx-localhost-settings' );
		
		$is_display_notice_line = ! empty( $dx_localhost_settings['notice-checkbox'] ) ? $dx_localhost_settings['notice-checkbox'] : "";
		
		if ( empty( $is_display_notice_line ) ) {
			wp_enqueue_style( 'dx-localhost', DX_LOCALHOST_ASSETS_URL . '/css/dx-localhost.css' );
			
			$style = '';
			$notice_color_val       = isset( $dx_localhost_settings['notice-color'] ) ? $dx_localhost_settings['notice-color'] : "";
			$notice_text_color_val  = isset( $dx_localhost_settings['notice-text-color'] ) ? $dx_localhost_settings['notice-text-color'] : "";
			$is_logged_in = is_user_logged_in();
			
			if ( ! empty( $notice_color_val ) ) {
				$style .= 'background-color: ' . $notice_color_val . ';';
			}
			
			if ( ! empty( $notice_text_color_val ) ) {
				$style .= 'color: ' . $notice_text_color_val . ';';
			}
			
			if ( ! empty( $is_logged_in ) && $is_logged_in == true ) {
				$is_admin_bar_showing = is_admin_bar_showing();
				if ( ! empty( $is_admin_bar_showing ) && $is_admin_bar_showing == true ) {
					$style .= 'top: 32px;';
				}
			}
			
			$notice_msg = __( 'You are working on ' . self::get_env_name() , 'dx_loc' );
			
			echo '<div id="dx-localhost-notice" style = "'. $style . '">'. $notice_msg .'</div>';
		}
	}

	/**
	 * Load plugin text domain
	 */
	function dx_localhost_load_textdomain() {
		load_plugin_textdomain( 'dx_loc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	/**
	 * Check if the current server is localhost
	 */
	function dx_is_localhost() {
		$dx_localhost = get_option( 'dx-localhost-settings' );
		$dx_localhost_settings = !empty( $dx_localhost ) && is_array( $dx_localhost ) ? $dx_localhost : "";
		
		$dx_env_name = isset( $dx_localhost_settings['env-name'] ) ? $dx_localhost_settings['env-name'] : "";
		$dx_ip_addr  = isset( $dx_localhost_settings['ip-addr'] ) ? $dx_localhost_settings['ip-addr'] : "";
		
		$dx_localhost_name = apply_filters( 'dx_localhost_name', array( 'localhost', $dx_env_name ) );
		$dx_localhost_addr = apply_filters( 'dx_localhost_addr', array( '127.0.0.1', $dx_ip_addr ) );
		
		if ( in_array( $_SERVER['SERVER_NAME'], $dx_localhost_name ) || in_array( $_SERVER['SERVER_ADDR'], $dx_localhost_addr ) ) {
			return true;
		}
		
		return false;
	 }


	function dx_localhost_menu() {
		add_options_page('DX localhost Options', __( 'DX localhost', 'dx_loc' ), 'manage_options', 'dx_localhost_options', array( $this, 'dx_localhost_options_cb' ) );
	}

	function dx_localhost_admin_init() {
		register_setting( 'dx-localhost-settings', 'dx-localhost-settings', array( $this, 'dx_validate_settings' ) );
	}

	function dx_localhost_options_cb() {
		$dx_localhost = get_option( 'dx-localhost-settings' );		
		$dx_localhost_settings = !empty( $dx_localhost ) && is_array( $dx_localhost ) ? $dx_localhost : "";
		
		$toolbar_checkbox_val   = isset( $dx_localhost_settings['toolbar-checkbox'] ) ? $dx_localhost_settings['toolbar-checkbox'] : "";
		$notice_checkbox_val    = isset( $dx_localhost_settings['notice-checkbox'] ) ? $dx_localhost_settings['notice-checkbox'] : "";
		$toolbar_color_val      = isset( $dx_localhost_settings['toolbar-color'] ) ? $dx_localhost_settings['toolbar-color'] : "";
		$notice_color_val       = isset( $dx_localhost_settings['notice-color'] ) ? $dx_localhost_settings['notice-color'] : "";
		$toolbar_text_color_val = isset( $dx_localhost_settings['toolbar-text-color'] ) ? $dx_localhost_settings['toolbar-text-color'] : "";
		$notice_text_color_val  = isset( $dx_localhost_settings['notice-text-color'] ) ? $dx_localhost_settings['notice-text-color'] : "";
		$dx_env_name            = isset( $dx_localhost_settings['env-name'] ) ? $dx_localhost_settings['env-name'] : "";
		$dx_ip_addr             = isset( $dx_localhost_settings['ip-addr'] ) ? $dx_localhost_settings['ip-addr'] : "";
		?>
		<div class="wrap">
			<h2><?php _e( 'DX localhost Options', 'dx_loc' ); ?></h2>
		
			<form action="options.php" method="post"> 
				<?php settings_fields( 'dx-localhost-settings' ); ?>
					<table class="form-table">
						<tr>
							<th scope="row"><?php _e( 'Toolbar Visibility', 'dx_loc' ); ?></th>
							<td>
								<div><input type="checkbox" id="toolbar-checkbox" name="dx-localhost-settings[toolbar-checkbox]" value="1" <?php checked('1', $toolbar_checkbox_val); ?> />
								<label for="toolbar-checkbox"><?php _e( 'Disable Localhost Toolbar Button', 'dx_loc' ); ?></label></div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Notice Line Visibility', 'dx_loc' ); ?></th>
							<td>
								<div><input type="checkbox" id="notice-checkbox" name="dx-localhost-settings[notice-checkbox]" value="1"<?php checked('1', $notice_checkbox_val);?> />
								<label for="notice-checkbox"><?php _e( 'Disable Notice Line', 'dx_loc' ); ?></label></div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Toolbar Button Color', 'dx_loc' ); ?></th>
							<td><input type="text" id="toolbar-color" name="dx-localhost-settings[toolbar-color]" value="<?php echo $toolbar_color_val;?>" class="toolbar-color-field"  /></td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Toolbar Text Color', 'dx_loc' ); ?></th>
							<td><input type="text" id="toolbar-text-color" name="dx-localhost-settings[toolbar-text-color]" value="<?php echo $toolbar_text_color_val;?>" class="toolbar-text-color-field"  /></td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Notice Line Color', 'dx_loc' ); ?></th>
							<td><input type="text" id="notice-color" name="dx-localhost-settings[notice-color]" value="<?php echo $notice_color_val;?>" class="notice-color-field"  /></td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Notice Line Text Color', 'dx_loc' ); ?></th>
							<td><input type="text" id="notice-text-color" name="dx-localhost-settings[notice-text-color]" value="<?php echo $notice_text_color_val;?>" class="notice-text-color-field"  /></td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Dev Environment Name:', 'dx_loc' ); ?></th>
							<td>
							   <div><input class="dx-localhost-settings-env-name" type="text" id="dx-env-name-id" name="dx-localhost-settings[env-name]" value="<?php echo $dx_env_name ?>" /></div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Dev Site IP Address:', 'dx_loc' ); ?></th>
							<td>
								<div><input class="dx-localhost-settings-ip-addr" type="text" id="dx-ip-addr-id" name="dx-localhost-settings[ip-addr]" value="<?php echo $_SERVER['SERVER_ADDR'] ?>" /></div>
							</td>
						</tr>
					</table>				
				<div><?php submit_button( __( 'Save Changes', 'dx_loc', 'primary', 'dx-localhost' ) );?></div>
			</form>
		</div>
		<?php
	}
	
	function dx_localhost_admin_bar_menu( $wp_admin_bar ) {
		$dx_localhost = get_option( 'dx-localhost-settings' );
		$dx_localhost_settings = !empty( $dx_localhost ) && is_array( $dx_localhost ) ? $dx_localhost : "";
		
		$toolbar_checkbox_val     = isset( $dx_localhost_settings['toolbar-checkbox'] ) ? $dx_localhost_settings['toolbar-checkbox'] : "";
		$notice_line_checkbox_val = isset( $dx_localhost_settings['notice-checkbox'] ) ? $dx_localhost_settings['notice-checkbox'] : "";
			
		//if toolbar is not disabled display the style
		if( empty( $toolbar_checkbox_val ) || $toolbar_checkbox_val == 0 ) {
			self::dx_localhost_dispay_toolbar( $wp_admin_bar );
		}
		//if notice line is not disabled display the style
		if( empty( $notice_line_checkbox_val ) || $notice_line_checkbox_val == 0 ) {
			self::dx_notice_line_style();
		}
	}
	
	public static function dx_toolbar_button_style () {
		$dx_localhost = get_option( 'dx-localhost-settings' );
		$dx_localhost_settings = !empty( $dx_localhost ) && is_array( $dx_localhost ) ? $dx_localhost : "";
		
		$toolbar_color_val      = isset( $dx_localhost_settings['toolbar-color'] ) ? $dx_localhost_settings['toolbar-color'] : "";		
		$toolbar_text_color_val = isset( $dx_localhost_settings['toolbar-text-color'] ) ? $dx_localhost_settings['toolbar-text-color'] : "";
		$style = "";
		?>
		<style type="text/css">
		<?php if( !empty( $toolbar_color_val ) ) : ?>
			.dx-localhost-btn {
				<?php $style .= 'background-color: '. $toolbar_color_val .';'?>
			}
		<?php endif; ?>		
		<?php if( !empty( $toolbar_text_color_val ) ) : ?>
			.dx-localhost-btn {
				<?php $style .= ' color: '. $toolbar_text_color_val .';' ?>
			}
		<?php endif; ?>
		</style>
		<?php
		return $style;
	}
	
	public static function dx_notice_line_style() {
		$dx_localhost = get_option( 'dx-localhost-settings' );
		$dx_localhost_settings = !empty( $dx_localhost ) && is_array( $dx_localhost ) ? $dx_localhost : "";

		$notice_color_val       = isset( $dx_localhost_settings['notice-color'] ) ? $dx_localhost_settings['notice-color'] : "";
		$notice_text_color_val  = isset( $dx_localhost_settings['notice-text-color'] ) ? $dx_localhost_settings['notice-text-color'] : "";
		?>
		<style type="text/css">
		<?php if( !empty( $notice_color_val ) ) : ?>
			#dx-localhost-notice {
				background-color:<?php echo $notice_color_val; ?>; 
			}
		<?php endif; ?>		
		<?php if( !empty( $notice_text_color_val ) ) : ?>
			#dx-localhost-notice {
				color:<?php echo $notice_text_color_val; ?>; 
			}
		<?php endif; ?>
		
		</style>
		<?php
	}
	
	public static function get_env_name( ) {
		$dx_localhost = get_option( 'dx-localhost-settings' );
		$dx_localhost_settings = !empty( $dx_localhost ) && is_array( $dx_localhost ) ? $dx_localhost : "";
		
		$dx_env_name = isset( $dx_localhost_settings['env-name'] ) ? $dx_localhost_settings['env-name'] : "";
		
		//working on localhost and environment name is not yet specified
		if ( ( $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '::1' ) && $dx_env_name == '' ) {
			$dx_env_name = __( 'Localhost', 'dx_loc' );
		}
		
		//working on staging domain and environment name is not yet specified
		if ( ( $_SERVER['SERVER_ADDR'] != '127.0.0.1' || $_SERVER['SERVER_ADDR'] != '::1' ) && $dx_env_name == '') { 
			$dx_env_name = $_SERVER['SERVER_ADDR'];
		}
		return $dx_env_name;
	}

	public static function dx_localhost_dispay_toolbar( $wp_admin_bar ) {
		$dx_toolbar_btn_style = self::dx_toolbar_button_style();
		$dx_env_name = self::get_env_name();
		
		$args = array(
			'id'    => 'dx-localhost',
			'title' => '<input class ="dx-localhost-btn" id="dx-localhost-btn" style=" '.$dx_toolbar_btn_style.' " type="submit" value="'. $dx_env_name .'"/>',
			'href'  => admin_url( 'options-general.php?page=dx_localhost_options', 'http'),
			'meta'  => array( 'class' => 'my-toolbar-page' )
			);
		$wp_admin_bar->add_node( $args );
	}

	function dx_enqueue_color_picker( $hook ) {
		if ( $hook == 'settings_page_dx_localhost_options' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'my-script-handle', DX_LOCALHOST_ASSETS_URL . '/scripts/dx-colorpicker.js', array( 'wp-color-picker' ), false, true );	
		}
	}

	/**
	 * Validate Settings
	 * 
	 * Filter the submitted data as per your request and return the array
	 * 
	 * @param array $input
	 */
	function dx_validate_settings( $input ) {
		if ( ! isset( $input['toolbar-checkbox'] ) ) {
			$input['toolbar-checkbox'] = 0;
		}
		
		if ( ! isset( $input['notice-checkbox'] ) ) {
			$input['notice-checkbox'] = 0;
		}
		
		if ( empty ( $input['env-name'] ) ) {
			$input['env-name'] = self::get_env_name();
		}
		
		//check if ip address input field is empty or ip address entered by the user is not the ip address of the site.
		if ( empty ( $input['ip-addr'] ) || $input['ip-addr'] != $_SERVER['SERVER_ADDR'] ) {
			$input['ip-addr'] = $_SERVER['SERVER_ADDR'];
		}
			
		return $input;
	}
}

$dx_localhost = new DX_Localhost();
endif;
