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
	$minimail_options = get_option('minimal_admin_plugin_options');
	$option_show_screenoptions = $minimail_options['option_hide_screen_options'];
	if ($option_show_screenoptions == '1') { 
		echo '#screen-options-link-wrap	{ display:none; }'; 
		echo '#contextual-help-link-wrap	{ display:none; }';
		echo '.tablenav.top								{ display:none; }';
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
		$minimail_options = get_option('minimal_admin_plugin_options');
		$option_show_posts = $minimail_options['option_hide_posts'];
		if ($option_show_posts == '1') { remove_menu_page('edit.php'); }
		
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
}

$minimal_admin = new Minimal_Admin();




// start simple options page 		
add_action( 'admin_init', 'minimail_admin_options_init' );
add_action( 'admin_menu', 'minimail_admin_options_add_page');


// Init plugin options to white list our options
function minimail_admin_options_init(){
	register_setting( 'minimal_admin_options', 'minimal_admin_plugin_options', 'plugin_options_validate' );
}


// load up the menu page
function minimail_admin_options_add_page() {
	add_options_page('Minimal Admin', 'Minimal Admin', 'administrator', 'minimal_admin', 'minimal_admin_options_page');
}


// create the options page
function minimal_admin_options_page() {
	?>
	<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
		<h2>Minimal Admin Options</h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'minimal_admin_options' ); ?>
			<?php $options = get_option( 'minimal_admin_plugin_options' ); ?>

			<table class="form-table">
				<tr valign="top"><th scope="row">Admin Side Menu Options</th>
					<td>
						<input id="minimal_admin_plugin_options[option_hide_posts]" name="minimal_admin_plugin_options[option_hide_posts]" type="checkbox" value="1" <?php checked( '1', $options['option_hide_posts'] ); ?> />
						<label class="description" for="minimal_admin_plugin_options[option_hide_posts]"><?php _e( 'Hide the posts menu item', 'minimailadminplugin' ); ?></label>
					</td>
				</tr>
				
				<tr valign="top"><th scope="row">Admin Edit Screens</th>
					<td>
						<input id="minimal_admin_plugin_options[option_hide_screen_options]" name="minimal_admin_plugin_options[option_hide_screen_options]" type="checkbox" value="1" <?php checked( '1', $options['option_hide_screen_options'] ); ?> />
						<label class="description" for="minimal_admin_plugin_options[option_hide_screen_options]"><?php _e( 'Hide screen options tab, help tab and the post filtering bar', 'minimailadminplugin' ); ?></label>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="Save Options" />
			</p>
		</form>
	</div>
	<?php
}

// sanitize and validate input
function plugin_options_validate( $input ) {

	// check option hide posts checkbox value is either 0 or 1
	if ( ! isset( $input['option_hide_posts'] ) )
	$input['option_hide_posts'] = null;
	$input['option_hide_posts'] = ( $input['option_hide_posts'] == 1 ? 1 : 0 );
	
	// check option hide screen optionscheckbox value is either 0 or 1
	if ( ! isset( $input['option_hide_screen_options'] ) )
	$input['option_hide_screen_options'] = null;
	$input['option_hide_screen_options'] = ( $input['option_hide_screen_options'] == 1 ? 1 : 0 );

	return $input;
}
