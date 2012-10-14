<?php
/*
Plugin Name: Minimal Admin 
Plugin URI: http://www.minimaladmin.com/
Description: Very simple plugin to hide non essential wp-admin functionality.
Version: 0.3.
Author: Aaron Rutley
Author URI: http://www.aaronrutley.com.au/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*  Copyright 2012 Aaron Rutley

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
	
class Minimal_Admin {

	function __construct( ){
		add_filter( 'show_admin_bar', '__return_false' );

		add_action( 'admin_menu', array( &$this, 'hide_menu_items' ) );
		add_action( 'admin_head', array( &$this, 'custom_admin_styles' ) );

		add_filter( 'manage_pages_columns', array( &$this, 'custom_columns' ) );
		add_filter( 'manage_posts_columns', array( &$this, 'custom_columns' ) );

		add_action( 'admin_menu', array( &$this, 'hide_dashboard' ) );
		add_action( 'admin_init', array( &$this, 'add_grav_forms' ) );
		
		add_filter( 'plugin_action_links', array( &$this, 'add_settings_link'), 10, 2 ); 
		
		$options = get_option('minimal-admin');
		if ( empty( $options ) ) {
			$options = array(
									'hide_posts' => false,
									'hide_screen_options' => false
			);
			update_option( 'minimal-admin', $options );
		}
	}
	


	// clean up WordPress dashboard with this CSS 
	function custom_admin_styles() { ?>
<style type="text/css">	
	#wp-admin-bar-comments { display:none; } 
	#wp-admin-bar-new-content { display:none; } 
	#wp-admin-bar-wpseo-menu { display:none; }
	#footer { display:none; }  
	#collapse-menu { display:none; }
	.column-wpseo-score { display:none; }
	.column-wpseo-title { display:none; }
	.column-wpseo-metadesc { display:none; }
	.column-wpseo-focuskw { display:none; }
	.menu-icon-dashboard { display:none; }
  .wp-menu-separator { display:none; }
	li#wp-admin-bar-site-name.menupop .ab-sub-wrapper	{ display:none; }
	
<?php 
	$options = get_option('minimal-admin');
	$option_hide_screen_options = $options['hide_screen_options'];
	if ($option_hide_screen_options == '1') { 
		echo '#screen-options-link-wrap { display:none; }'; 
		echo '#contextual-help-link-wrap { display:none; }';
		echo '.tablenav.top { display:none; }';
	} 
?>
 
</style>
<?php
	}


	// hide menu items from all users 
	function hide_menu_items() {
		remove_menu_page('tools.php');  
		remove_menu_page('link-manager.php');
		remove_menu_page('upload.php'); 
		remove_menu_page('edit-comments.php'); 
		remove_menu_page('profile.php'); 

		$options = get_option('minimal-admin');
		$option_hide_posts = $options['hide_posts'];
		if ($option_hide_posts == '1') { 
		remove_menu_page('edit.php'); 
		}
	}
	

	// tidy up edit page screen to leave just the title 
	function custom_columns( $defaults ) {
		unset( $defaults['comments'] );
		unset( $defaults['author'] );
		unset( $defaults['date'] );
		unset( $defaults['categories'] );
		unset( $defaults['tags'] );
		return $defaults;
	}


	// hide dashboard by redirecting user to 'all pages' 
	function hide_dashboard ( ) {
		if ( preg_match( '#wp-admin/?( index.php )?$#', $_SERVER['REQUEST_URI'] ) ) {
			wp_redirect( get_option( 'siteurl' ) . '/wp-admin/edit.php?post_type=page' );
		}
	}


	// grant editor access to gravity forms 
	function add_grav_forms( ){
		$role = get_role( 'editor' );
		$role->add_cap( 'gform_full_access' );
	}


	// add settings link to plugin summary page	
	function add_settings_link($links, $file) {
		static $this_plugin;
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
		 
		if ($file == $this_plugin){
		$settings_link = '<a href="options-general.php?page=minimal-admin">'.__("Settings", "minimal-admin").'</a>';
		 array_unshift($links, $settings_link);
		}
		return $links;
	}
 
 
}
$minimal_admin = new Minimal_Admin();




// options page 
	function min_admin_plugin_menu() {
	 add_options_page( __( 'Minimal Admin', 'minimal-admin' ), __( 'Minimal Admin', 'minimal-admin' ), 'update_core', 'minimal-admin', 'min_admin_settings' );
	}
	add_action( 'admin_menu', 'min_admin_plugin_menu' );
	
	
	function min_admin_settings() {
		if ( ! current_user_can( 'update_core' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'minimal-admin' ) );
	
		$message = '';
		if ( ! empty( $_REQUEST['submit'] ) ) {
			check_admin_referer( 'minimal-admin-settings' );
	
			min_admin_save_settings();
			$message = __( 'Settings updated', 'minimal-admin' );
		}
		$options = get_option( 'minimal-admin' );
		$messages = array(
						'hide_posts' => __( 'Hide Posts from the wp-admin menu', 'minimal-admin' ),
						'hide_screen_options' => __( 'Hide screen options tab, help tab and the post filtering bar', 'minimal-admin' )
					);
	?>
		<div class="wrap">
			<?php screen_icon('options-general'); ?>
			<h2><?php _e( 'Minimal Admin', 'minimal-admin' ); ?></h2>
	<?php
		if ( ! empty( $message ) ) {
	?>
			<div class="updated">
				<p><?php echo $message; ?></p>
			</div>
	<?php
		}
	?>
			<form method="post">
			<?php wp_nonce_field( 'minimal-admin-settings' ); ?>
	<?php
		foreach ( $options as $type => $enabled ) {
			$checked = '';
			if ( $enabled )
				$checked = ' checked="checked"';
	
			echo "<p><input type='checkbox' id='$type' name='$type' value='1'$checked> <label for='$type'>{$messages[$type]}</label></p>";
		}
	?>
			<br/><br/>
		
			<p><input class="button button-primary" type="submit" name="submit" id="submit" value="<?php esc_attr_e( 'Save Changes', 'minimal-admin' ); ?>" /></p>
			</form>
		</div>
	<?php
	}
	
	
	function min_admin_save_settings() {
		$types = array( 'hide_posts', 'hide_screen_options' );
		$options = get_option( 'minimal-admin' );
	
		foreach ( $types as $type ) {
			if ( ! empty( $_REQUEST[$type] ) )
				$options[$type] = true;
			else
				$options[$type] = false;
		}
	update_option( 'minimal-admin', $options );
	}
