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
		$hits               = 0;
		$visits             = 0;
		$referrals_accepted = 0;
		$referrals_closed   = 0;
		$referrals_pending  = 0;
		$referrals_rejected = 0;		
		foreach ( $affiliates as $affiliate ) {
			$affiliate_id = $affiliate['affiliate_id'];
			$hits      += affiliates_get_affiliate_hits( $affiliate_id );
			$visits    += affiliates_get_affiliate_visits( $affiliate_id );
			$referrals_accepted += affiliates_get_affiliate_referrals( $affiliate_id, null, null, AFFILIATES_REFERRAL_STATUS_ACCEPTED );
			$referrals_closed   += affiliates_get_affiliate_referrals( $affiliate_id, null, null, AFFILIATES_REFERRAL_STATUS_CLOSED );
			$referrals_pending  += affiliates_get_affiliate_referrals( $affiliate_id, null, null, AFFILIATES_REFERRAL_STATUS_PENDING );
			$referrals_rejected += affiliates_get_affiliate_referrals( $affiliate_id, null, null, AFFILIATES_REFERRAL_STATUS_REJECTED );
		}
		
		$accepted_icon = "<img class='icon' alt='" . __( 'Accepted', AFFILIATES_PLUGIN_DOMAIN) . "' src='" . AFFILIATES_PLUGIN_URL . "images/accepted.png'/>";
		$closed_icon = "<img class='icon' alt='" . __( 'Closed', AFFILIATES_PLUGIN_DOMAIN) . "' src='" . AFFILIATES_PLUGIN_URL . "images/closed.png'/>";
		$pending_icon = "<img class='icon' alt='" . __( 'Pending', AFFILIATES_PLUGIN_DOMAIN) . "' src='" . AFFILIATES_PLUGIN_URL . "images/pending.png'/>";
		$rejected_icon = "<img class='icon' alt='" . __( 'Rejected', AFFILIATES_PLUGIN_DOMAIN) . "' src='" . AFFILIATES_PLUGIN_URL . "images/rejected.png'/>";
		
		echo '<div class="manage" style="margin-right:1em">';
		echo '<p>';
		echo '<strong>' . $title . '</strong>&nbsp;' . $info;
		echo '</p>';
		echo '<ul>';
		echo '<li>' . __( '<strong>Referrals:</strong>', AFFILIATES_PLUGIN_DOMAIN ) . '</li>';
		echo '<li><ul>';
		echo '<li>' . $accepted_icon . '&nbsp;' . sprintf( __( '%10d Accepted', AFFILIATES_PLUGIN_DOMAIN ), $referrals_accepted ) . '</li>';
		echo '<li>' . $closed_icon . '&nbsp;' . sprintf( __( '%10d Closed', AFFILIATES_PLUGIN_DOMAIN ), $referrals_closed ) . '</li>';
		echo '<li>' . $pending_icon . '&nbsp;' . sprintf( __( '%10d Pending', AFFILIATES_PLUGIN_DOMAIN ), $referrals_pending ) . '</li>';
		echo '<li>' . $rejected_icon . '&nbsp;' . sprintf( __( '%10d Rejected', AFFILIATES_PLUGIN_DOMAIN ), $referrals_rejected ) . '</li>';
		echo '</li></ul>';
		echo '<li>' . sprintf( __( '%10d Hits', AFFILIATES_PLUGIN_DOMAIN ), $hits ) . '</li>';
		echo '<li>' . sprintf( __( '%10d Visits', AFFILIATES_PLUGIN_DOMAIN ), $visits ) . '</li>';
		echo '</ul>';
		echo '</div>';
		
	}
	affiliates_footer();
}
?>