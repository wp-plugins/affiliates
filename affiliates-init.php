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

global $affiliates_options, $affiliates_version, $affiliates_admin_messages;

if ( !isset( $affiliates_admin_messages ) ) {
	$affiliates_admin_messages = array();
}

if ( !isset( $affiliates_version ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$affiliates_version = AFFILIATES_DEFAULT_VERSION;
	if ( function_exists( 'get_plugin_data' ) ) {
		$plugin_data = get_plugin_data( dirname( __FILE__ ) . '/affiliates.php' );
		if ( !empty( $plugin_data ) ) {
			$affiliates_version = $plugin_data['Version'];
		}			
	}
}

// options
include_once( dirname( __FILE__ ) . '/class-affiliates-options.php' );
if ( $affiliates_options == null ) {
	$affiliates_options = new Affiliates_Options();
}

// utilities
include_once( dirname( __FILE__ ) . '/class-affiliates-utility.php' );

// forms, shortcodes, widgets
include_once( dirname( __FILE__ ) . '/class-affiliates-contact.php' );
include_once( dirname( __FILE__ ) . '/class-affiliates-registration.php' );
include_once( dirname( __FILE__ ) . '/class-affiliates-registration-widget.php' );

add_action( 'widgets_init', 'affiliates_widgets_init' );

/**
 * Register widgets
 */
function affiliates_widgets_init() {
	register_widget( 'Affiliates_Contact' );
	register_widget( 'Affiliates_Registration_Widget' );
}

add_action( 'admin_init', 'affiliates_admin_init' );

/**
 * Hook into admin_init. Used to get our styles at the right place.
 * @see affiliates_admin_menu()
 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Load_scripts_only_on_plugin_pages
 */
function affiliates_admin_init() {
	global $affiliates_version;
	wp_register_style( 'smoothness', AFFILIATES_PLUGIN_URL . 'css/smoothness/jquery-ui-1.8.16.custom.css', array(), $affiliates_version );
	wp_register_style( 'affiliates_admin', AFFILIATES_PLUGIN_URL . 'css/affiliates_admin.css', array(), $affiliates_version );
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
	global $post_type, $affiliates_version;
	
	// load datepicker scripts for all
	wp_enqueue_script( 'datepicker', AFFILIATES_PLUGIN_URL . 'js/jquery.ui.datepicker.min.js', array( 'jquery', 'jquery-ui-core' ), $affiliates_version );
	wp_enqueue_script( 'datepickers', AFFILIATES_PLUGIN_URL . 'js/datepickers.js', array( 'jquery', 'jquery-ui-core', 'datepicker' ), $affiliates_version );
	// corners
	wp_enqueue_script( 'jquery-corner', AFFILIATES_PLUGIN_URL . 'js/jquery.corner.js', array( 'jquery', 'jquery-ui-core' ), $affiliates_version );
	// add more dates used for trips and events
	wp_enqueue_script( 'affiliates', AFFILIATES_PLUGIN_URL . 'js/affiliates.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-button', 'jquery-corner' ), $affiliates_version );
	// and thus are the translations of the buttons used
	
//	echo '
//		<script type="text/javascript">
//			var fooText = "' . __( 'Foo', AFFILIATES_PLUGIN_DOMAIN ) . '";
//		</script>
//		';
}
?>