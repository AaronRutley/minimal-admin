<?php
/*
Plugin Name: Minimal Admin 
Plugin URI: http://elevenmedia.com.au
Description: No frills & experimental plugin to hide non essential wp-admin functionality.
Version: 1.0.
Author: Aaron Rutley
Author URI: http://elevenmedia.com.au/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*  Copyright 2012 Eleven Media (email : info@elevenmedia.com.au)

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
	
class Eleven_Minimal_Admin {

	function __construct(){
		// hide admin bar when viwing site
		add_filter( 'show_admin_bar', '__return_false' );

		add_action( 'admin_menu', array( &$this, 'minimal_dashboard_remove_menu_items' ) );

		add_filter( 'manage_pages_columns', array( &$this, 'minimal_dashboard_custom_columns' ) );
		add_filter( 'manage_posts_columns', array( &$this, 'minimal_dashboard_custom_columns' ) );

		add_action( 'admin_menu', array( &$this, 'minimal_dashboard_hide_dashboard' ) );

		add_action( 'admin_init', array( &$this, 'minimal_dashboard_add_grav_forms' ) );
	}

	// clean up WordPress dashboard with this CSS 
	function minimal_dashboard_custom_admin_styles() { ?>
<style type="text/css">	
	.wp-menu-separator	{ display:none; }
	#wp-admin-bar-comments	{ display:none; } 
	#wp-admin-bar-new-content	{ display:none; } 
	#wp-admin-bar-wpseo-menu	{ display:none; }
	#footer	{ display:none; }  
	.tablenav.top	{ display:none; }
	#screen-options-link-wrap	{ display:none; }
	#contextual-help-link-wrap	{ display:none; }
	.column-wpseo-score	{ display:none; }
	.column-wpseo-title	{ display:none; }
	.column-wpseo-metadesc	{ display:none; }
	.column-wpseo-focuskw	{ display:none; }
	#collapse-menu	{ display:none; }
</style>
<?php
	}


	// hide menu items from all users 
	function minimal_dashboard_remove_menu_items() {
		global $menu;

		$restricted = array(__('Links'), __('Comments'), __('Media'), __('Dashboard'), __('Tools'), __('Profile'));

		end ($menu);

		while (prev($menu)){
			$value = explode(' ',$menu[key($menu)][0]);
			if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){
				unset($menu[key($menu)]);
			}
		}
	}


	// tidy up edit page screen to leave just the title 
	function minimal_dashboard_custom_columns($defaults) {
		unset($defaults['comments']);
		unset($defaults['author']);
		unset($defaults['date']);
		unset($defaults['categories']);
		unset($defaults['tags']);
		unset($defaults['wpseo-score']); 
		return $defaults;
	}


	// hide dashboard by redirecting user to 'all pages' 
	function minimal_dashboard_hide_dashboard () {
		if ( preg_match( '#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI'] )) {
			wp_redirect( get_option( 'siteurl' ) . '/wp-admin/edit.php?post_type=page');
		}
	}


	// grant editor access to gravity forms 
	function minimal_dashboard_add_grav_forms(){
		$role = get_role('editor');
		$role->add_cap('gform_full_access');
	}
}

$eleven_minimal_admin = new Eleven_Minimal_Admin();