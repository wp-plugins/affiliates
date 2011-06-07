<?php
/**
 * affiliates-admin.php
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
/**
 * Affiliates overview and summarized statistics.
 */
function affiliates_admin() {
	
	global $wpdb;

	if ( !current_user_can( AFFILIATES_ACCESS_AFFILIATES ) ) {
		wp_die( __( 'Access denied.', AFFILIATES_PLUGIN_DOMAIN ) );
	}
	
	echo '<h2>' . __( 'Affiliates Overview', AFFILIATES_PLUGIN_DOMAIN ) . '</h2>';
	
	echo '<h3>' . __( 'Statistics Summary', AFFILIATES_PLUGIN_DOMAIN ) . '</h3>';
	for ( $i = 0; $i < 3; $i++ ) {
		switch ( $i ) {
			case 0:
				$affiliates = affiliates_get_affiliates( true, true );
				$title = __( 'From operative affiliates:', AFFILIATES_PLUGIN_DOMAIN );
				$info = sprintf( _n( 'There is 1 operative affiliate', 'There are %d operative affiliates', count( $affiliates ), AFFILIATES_PLUGIN_DOMAIN ), count( $affiliates ) );
				break;
			case 1:
				$affiliates = affiliates_get_affiliates( true, false );
				$title = __( 'From operative and non-operative affiliates:', AFFILIATES_PLUGIN_DOMAIN );
				$info = sprintf( _n( 'There is 1 affiliate in this set', 'There are %d affiliates in this set', count( $affiliates ), AFFILIATES_PLUGIN_DOMAIN ), count( $affiliates ) );
				break;
			case 2:
				$affiliates = affiliates_get_affiliates( false, false );
				$title = __( 'All time (includes data from deleted affiliates):', AFFILIATES_PLUGIN_DOMAIN );
				$info = sprintf( _n( 'There is 1 affiliate in this set', 'There are %d affiliates in this set', count( $affiliates ), AFFILIATES_PLUGIN_DOMAIN ), count( $affiliates ) );
				break;
		}
		$hits = 0;
		$visits = 0;
		$referrals = 0;
		foreach ( $affiliates as $affiliate ) {
			$affiliate_id = $affiliate['affiliate_id'];
			$hits      += affiliates_get_affiliate_hits( $affiliate_id );
			$visits    += affiliates_get_affiliate_visits( $affiliate_id );
			$referrals += affiliates_get_affiliate_referrals( $affiliate_id );
		}
		
		echo '<h4>' . $title . '</h4>';
		echo '<p>' . $info . '</p>';
		echo '<ul>';
		echo '<li>' . sprintf( __( '%10d Hits', AFFILIATES_PLUGIN_DOMAIN ), $hits ) . '</li>';
		echo '<li>' . sprintf( __( '%10d Visits', AFFILIATES_PLUGIN_DOMAIN ), $visits ) . '</li>';
		echo '<li>' . sprintf( __( '%10d Referrals', AFFILIATES_PLUGIN_DOMAIN ), $visits ) . '</li>';
		echo '</ul>';
		
	}
	affiliates_footer();
}
?>