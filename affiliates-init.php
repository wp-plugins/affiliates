<?php
/**
 * affiliates-init.php
 * 
 * Copyright (c) 2010, 2011 "kento" Karim Rahimpur www.itthinx.com
 * 
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 * 
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This header and all notices must be kept intact.
 * 
 * @author Karim Rahimpur
 * @package affiliates
 * @since affiliates 1.0.0
 */

global $affiliates_options;
	
// options
include_once( dirname( __FILE__ ) . '/class-affiliates-options.php');
if ( $affiliates_options == null ) {
	$affiliates_options = new Affiliates_Options();
}
	
// widgets
include_once( dirname( __FILE__ ) . '/class-affiliates-contact.php');  
add_action( 'widgets_init', 'affiliates_widgets_init' );

/**
 * Register widgets
 */
function affiliates_widgets_init() {
	register_widget( 'Affiliates_Contact' );
}

add_action( 'admin_init', 'affiliates_admin_init' );

/**
 * Hook into admin_init. Used to get our styles at the right place.
 * @see affiliates_admin_menu()
 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Load_scripts_only_on_plugin_pages
 */
function affiliates_admin_init() {
	wp_register_style( 'smoothness', AFFILIATES_PLUGIN_URL . 'css/smoothness/jquery-ui-1.8.11.custom.css' );
	wp_register_style( 'affiliates_admin', AFFILIATES_PLUGIN_URL . 'css/affiliates_admin.css' );
}

/**
 * Load styles.
 * @see affiliates_admin_menu()
 */
function affiliates_admin_print_styles() {
	wp_enqueue_style( 'smoothness' );
	wp_enqueue_style( 'affiliates_admin' );
}
	
/**
 * Load scripts.
 */
function affiliates_admin_print_scripts() {
	global $post_type;
	
	// load datepicker scripts for all
	wp_enqueue_script( 'datepicker', AFFILIATES_PLUGIN_URL . 'js/jquery.ui.datepicker.js', array( 'jquery', 'jquery-ui-core' ), '1.0.0' );
	wp_enqueue_script( 'datepickers', AFFILIATES_PLUGIN_URL . 'js/datepickers.js', array( 'jquery', 'jquery-ui-core', 'datepicker' ), '1.0.0' );
	// corners
	wp_enqueue_script( 'jquery-corner', AFFILIATES_PLUGIN_URL . 'js/jquery.corner.js', array( 'jquery', 'jquery-ui-core' ), '1.0.0' );		
	// add more dates used for trips and events
	wp_enqueue_script( 'affiliates', AFFILIATES_PLUGIN_URL . 'js/affiliates.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-button', 'jquery-corner' ), '1.0.0' );
	// and thus are the translations of the buttons used
	echo '
		<script type="text/javascript">
			var addButtonText = "' . __( 'Add', AFFILIATES_PLUGIN_DOMAIN ) . '";
			var removeButtonText = "' . __( 'Remove', AFFILIATES_PLUGIN_DOMAIN ) . '";
			var submitForAffiliateLinkText = "' . __( 'Affiliate link', AFFILIATES_PLUGIN_DOMAIN ) . ' : ' . __('Submit changes to obtain new affiliate links.', AFFILIATES_PLUGIN_DOMAIN ) . '";
			var nameFieldLabel = "' . __( 'Name', AFFILIATES_PLUGIN_DOMAIN ) . '";
			var emailFieldLabel = "' . __( 'Email', AFFILIATES_PLUGIN_DOMAIN ) . '";
			var fromDateFieldLabel = "' . __( 'From', AFFILIATES_PLUGIN_DOMAIN ) . '";
			var thruDateFieldLabel = "' . __( 'Until', AFFILIATES_PLUGIN_DOMAIN ) . '";
		</script>
		';
}
?>