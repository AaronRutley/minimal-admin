<?php
/*
Plugin Name: Minimal Admin 
Plugin URI: http://elevenmedia.com.au
Description: No frills & experimental plugin to hide non essential wp-admin functionality.
Version: 1.1.
Author: Aaron Rutley
Author URI: http://elevenmedia.com.au/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*  Copyright 2012 Eleven Media ( email : info@elevenmedia.com.au )

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

	function __construct( ){
		// hide admin bar when viwing site
		add_filter( 'show_admin_bar', '__return_false' );

		add_action( 'admin_menu', array( &$this, 'remove_menu_items' ) );
		add_action( 'admin_head', array( &$this, 'custom_admin_styles' ) );

		add_filter( 'manage_pages_columns', array( &$this, 'custom_columns' ) );
		add_filter( 'manage_posts_columns', array( &$this, 'custom_columns' ) );

		add_action( 'admin_menu', array( &$this, 'hide_dashboard' ) );
		add_action( 'admin_init', array( &$this, 'add_grav_forms' ) );
		
	}

	// clean up WordPress dashboard with this CSS 
	function custom_admin_styles() { ?>
<style type="text/css">	
	.wp-menu-separator	{ display:none; }
	#wp-admin-bar-comments	{ display:none; } 
	#wp-admin-bar-new-content	{ display:none; } 
	#wp-admin-bar-wpseo-menu	{ display:none; }
	#footer	{ display:none; }  
	.tablenav.top	{ display:none; }
	#contextual-help-link-wrap	{ display:none; }
	.column-wpseo-score	{ display:none; }
	.column-wpseo-title	{ display:none; }
	.column-wpseo-metadesc	{ display:none; }
	.column-wpseo-focuskw	{ display:none; }
	#collapse-menu	{ display:none; }

	<?php 
			$minimail_options = get_option('sample_theme_options');
			$option_show_screenoptions = $minimail_options['option2'];
			if ($option_show_screenoptions == '1') { 
					echo '#screen-options-link-wrap	{ display:none; }'; 
			} ?>
 
</style>
<?php
	}





	// hide menu items from all users 
	function remove_menu_items() {
		remove_menu_page('tools.php');  
		remove_menu_page('link-manager.php');  
		remove_menu_page('upload.php'); 
		remove_menu_page('index.php'); 
		remove_menu_page('edit-comments.php'); 
		$minimail_options = get_option('sample_theme_options');
		$option_show_posts = $minimail_options['option1'];
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

$eleven_minimal_admin = new Eleven_Minimal_Admin();




/* simple options page 		
add_action('admin_init', 'minimal_admin_register_menu');
add_action('admin_menu', 'minimal_admin_options_page');


function minimal_admin_register_menu() {
	add_options_page('Minimal Admin', 'Minimal Admin', 'administrator', 'gridly_admin', 'minimal_adminoptions_page');
}
*/



add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'sample_options', 'sample_theme_options', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
 
function theme_options_add_page() {
		add_options_page('Minimal Admin', 'Minimal Admin', 'administrator', 'gridly_admin', 'minimal_admin_options_page');
}

/**
 * Create arrays for our select and radio options
 */
$select_options = array(
	'0' => array(
		'value' =>	'0',
		'label' => __( 'Zero', 'sampletheme' )
	),
	'1' => array(
		'value' =>	'1',
		'label' => __( 'One', 'sampletheme' )
	),
	'2' => array(
		'value' => '2',
		'label' => __( 'Two', 'sampletheme' )
	),
	'3' => array(
		'value' => '3',
		'label' => __( 'Three', 'sampletheme' )
	),
	'4' => array(
		'value' => '4',
		'label' => __( 'Four', 'sampletheme' )
	),
	'5' => array(
		'value' => '3',
		'label' => __( 'Five', 'sampletheme' )
	)
);



/**
 * Create the options page
 */
function minimal_admin_options_page() {
	global $select_options, $radio_options;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
		<h2>Minimal Admin Options</h2>

		<form method="post" action="options.php">
			<?php settings_fields( 'sample_options' ); ?>
			<?php $options = get_option( 'sample_theme_options' ); ?>

			<table class="form-table">
			
				<tr valign="top"><th scope="row">Posts</th>
					<td>
						<input id="sample_theme_options[option1]" name="sample_theme_options[option1]" type="checkbox" value="1" <?php checked( '1', $options['option1'] ); ?> />
						<label class="description" for="sample_theme_options[option1]"><?php _e( 'Hide Posts', 'sampletheme' ); ?></label>
					</td>
				</tr>
				
				<tr valign="top"><th scope="row">Screen Options</th>
					<td>
						<input id="sample_theme_options[option2]" name="sample_theme_options[option2]" type="checkbox" value="1" <?php checked( '1', $options['option2'] ); ?> />
						<label class="description" for="sample_theme_options[option2]"><?php _e( 'Hide Screen Options', 'sampletheme' ); ?></label>
					</td>
				</tr>
				
				
				
				<tr valign="top"><th scope="row"><?php _e( 'Select input', 'sampletheme' ); ?></th>
					<td>
						<select name="sample_theme_options[selectinput]">
							<?php
								$selected = $options['selectinput'];
								$p = '';
								$r = '';

								foreach ( $select_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="sample_theme_options[selectinput]"><?php _e( 'Sample select input', 'sampletheme' ); ?></label>
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

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function theme_options_validate( $input ) {
	global $select_options, $radio_options;

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['option1'] ) )
		$input['option1'] = null;
	$input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );
	
	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['option2'] ) )
	$input['option2'] = null;
	$input['option2'] = ( $input['option2'] == 1 ? 1 : 0 );

	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['selectinput'], $select_options ) )
		$input['selectinput'] = null;
		
	return $input;
}

// ref http://themeshaper.com/2010/06/03/sample-theme-options/

