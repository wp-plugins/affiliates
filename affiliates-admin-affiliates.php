<?php
/**
 * affiliates-admin-affiliates.php
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

function affiliates_admin_affiliates() {
		
	global $wpdb, $wp_rewrite;
	
	if ( !current_user_can( AFFILIATES_ADMINISTER_AFFILIATES ) ) {
		wp_die( __( 'Access denied.', AFFILIATES_PLUGIN_DOMAIN ) );
	}
	
	echo
		'<div>' .
			'<h2>' .
				__( 'Affiliates', AFFILIATES_PLUGIN_DOMAIN ) .
			'</h2>' .
		'</div>';
				
	if ( !$wp_rewrite->using_permalinks() ) {			
		echo '<p class="warning">' .
			sprintf( __( 'Your site is not using <a href="%s">permalinks</a>. You will only be able to use <span class="affiliate-link">affiliate links</span> but not <span class="affiliate-permalink">affiliate permalinks</span>, unless you change your permalink settings.', AFFILIATES_PLUGIN_DOMAIN ), get_admin_url( null, 'options-permalink.php') ) .
			'</p>';
	}
	
	//
	// handle affiliates form submission
	//		
	$affiliates_table = _affiliates_get_tablename( 'affiliates' );
	
	if ( wp_verify_nonce( $_POST[AFFILIATES_ADMIN_AFFILIATES_NONCE], plugin_basename( __FILE__ ) ) ) {
		
		// mark deleted affiliates as deleted
		$i = 0;
		$affiliatecount = intval( $_POST['affiliatecount'] );
		while ( ( $_POST['delete-affiliate-id-' . $i] != NULL ) || ( $i <= $affiliatecount ) ) {
			$affiliate_id = $_POST['delete-affiliate-id-' . $i];
			$affiliate_exists = FALSE;
			// do not mark the pseudo-affiliate as deleted: type != ...
			$check = $wpdb->prepare(
				"SELECT affiliate_id FROM $affiliates_table WHERE affiliate_id = %d AND (type IS NULL OR type != '" . AFFILIATES_DIRECT_TYPE . "')",
				intval( $affiliate_id ) );
			if ( $wpdb->query( $check ) ) {
				$affiliate_exists = TRUE;
			}
			
			if ( $affiliate_exists ) {
				$wpdb->query(
					$query = $wpdb->prepare(
						"UPDATE $affiliates_table SET status = 'deleted' WHERE affiliate_id = %d",
						intval( $affiliate_id )
					)
				);
			}
			
			$i++;
		}
		
		// add or modify affiliate entries
		$i = 0;
		$affiliatecount = intval( $_POST['affiliatecount'] );
		while ( ( $_POST['name-field-' . $i] != NULL ) || ( $i <= $affiliatecount ) ) {
			
			$affiliate_id = $_POST['affiliate-id-' . $i];
			$affiliate_exists = false;
			$is_direct = false;
			if ( $the_affiliate = $wpdb->get_row( $wpdb->prepare(
				"SELECT affiliate_id FROM $affiliates_table WHERE affiliate_id = %d",
				intval( $affiliate_id ) ) ) ) {
				$affiliate_exists = true;
				$is_direct = $the_affiliate->type == AFFILIATES_DIRECT_TYPE;
			}
			
			$name = $_POST['name-field-' . $i];
			// don't change the name of the pseudo-affiliate
			if ( $is_direct ) {
				$name = AFFILIATES_DIRECT_NAME;
			}
			if ( !empty( $name ) ) {
				
				// Note the trickery (*) that has to be used because wpdb::prepare() is not
				// able to handle null values.
				// @see http://core.trac.wordpress.org/ticket/11622
				// @see http://core.trac.wordpress.org/ticket/12819
				
				$data = array(
					'name' => $name
				);
				$formats = array( '%s' );
				
				$email = trim( $_POST['email-field-' . $i] );
				if ( is_email( $email ) ) {
					$data['email'] = $email;
					$formats[] = '%s';
				} else {
					$data['email'] = null; // (*)
					$formats[] = 'NULL'; // (*)
				}
				
				$from_date = $_POST['from-date-field-' . $i];
				if ( empty( $from_date ) ) {
					$from_date = date( 'Y-m-d', time() );
				} else {
					$from_date = date( 'Y-m-d', strtotime( $from_date ) );
				}
				$data['from_date'] = $from_date;
				$formats[] = '%s';
				
				$thru_date = $_POST['thru-date-field-' . $i];
				if ( !empty( $thru_date ) && strtotime( $thru_date ) < strtotime( $from_date ) ) {
					// thru_date is before from_date => set to null
					$thru_date = null;							
				}
				if ( !empty( $thru_date ) ) {
					$thru_date = date( 'Y-m-d', strtotime( $thru_date ) );
					$data['thru_date'] = $thru_date;
					$formats[] = '%s';
				} else {
					$data['thru_date'] = null; // (*)
					$formats[] = 'NULL'; // (*)
				}
				
				if ( $affiliate_exists ) {
					
					$sets = array();
					$values = array();
					$j = 0;
					foreach( $data as $key => $value ) {
						$sets[] = $key . ' = ' . $formats[$j];
						if ( $value ) { // (*)
							$values[] = $value;
						}
						$j++;
					}

					if ( !empty( $sets ) ) {
						$sets = implode( ', ', $sets );
						$values[] = intval( $affiliate_id );
						$query = $wpdb->prepare(
							"UPDATE $affiliates_table SET $sets WHERE affiliate_id = %d",
							$values
						);
						$wpdb->query( $query );
					}
				} else {
					$data_ = array();
					$formats_ = array();
					foreach( $data as $key => $value ) { // (*)
						if ( $value ) {
							$data_[$key] = $value;
						}
					}
					foreach( $formats as $format ) { // (*)
						if ( $format != "NULL" ) {
							$formats_[] = $format;
						}
					}
					$wpdb->insert(
						$affiliates_table,
						$data_,
						$formats_
					);
				}
			} // if
			$i++;
		} // while
	
	} // if (nonce)
	
	//
	// print the Affiliates form
	//
	echo '<h3>' . __( 'Your current affiliates', AFFILIATES_PLUGIN_DOMAIN ) . '</h3>';
	
	echo '<p>' .
		__( 'There are two types of links your affiliates may use to link to your site:', AFFILIATES_PLUGIN_DOMAIN ) .
		'</p>' .
		'<ul>' .
		'<li>' .
			'<p class="affiliate-link">' .
			__( 'Affiliate link', AFFILIATES_PLUGIN_DOMAIN ) .
			'<p/>' .
			'<p>' .
			__( 'This link uses a parameter in the URL to record vists you receive through your affiliates.', AFFILIATES_PLUGIN_DOMAIN ) .
			__( 'The affiliate information is removed once a visitor has landed on your site.', AFFILIATES_PLUGIN_DOMAIN ) .
			__( 'You may also append the ?affiliates=... part to links to your posts.', AFFILIATES_PLUGIN_DOMAIN ) .
			'</p>' .
		'</li>' .
		'<li>' .
			'<p class="affiliate-permalink">' .
			__( 'Affiliate permalink', AFFILIATES_PLUGIN_DOMAIN ) .
			'</p>' .
			'<p>' .
			__( 'This link uses a nicer URL to record vists you receive through your affiliates.', AFFILIATES_PLUGIN_DOMAIN ) .
			__( 'The affiliate information is removed once a visitor has landed on your site.', AFFILIATES_PLUGIN_DOMAIN ) .
			'</p>' .
			'</li>' .
		'</ul>' .
		'<p>' .
			__( 'Once a visitor has landed on your site through an affiliate link, referrals may be recorded and attributed to the affiliate.', AFFILIATES_PLUGIN_DOMAIN ) .
		'</p>';
				
	echo '<form action="" name="affiliates" method="post">';
	
	echo '<div id="affiliatefields">';
	$affiliates_table = _affiliates_get_tablename( 'affiliates' );
	$query = "SELECT * FROM " . $affiliates_table . " WHERE status = 'active' ORDER BY name";
	$affiliates = $wpdb->get_results( $query, OBJECT );
	$i = 0;
	foreach ( $affiliates as $affiliate ) {
		
		$is_direct = $affiliate->type == AFFILIATES_DIRECT_TYPE;
		
		echo '<div id="affiliate-' . $i . '" class="affiliate ' . ( $i % 2 == 0 ? 'even' : 'odd' ) . '">';
		
		$affiliate_id = $affiliate->affiliate_id;
		echo '<input id="affiliate-id-' . $i . '" name="affiliate-id-' . $i . '" type="hidden" value="' . esc_attr( $affiliate_id ) . '"/>';
		
		echo '<p>';
		
		if ( !$is_direct ) {
			$name = $affiliate->name;
			echo '<label for="name-field-' . $i . '" class="field-label first">' . __( 'Name', AFFILIATES_PLUGIN_DOMAIN ) . '</label>';
			echo '<input id="name-field-' . $i . '" name="name-field-' . $i . '" class="namefield" type="text" value="' . esc_attr( stripslashes( $name ) ) . '"/>';
		} else {
			echo '<label for="name-field-' . $i . '" class="field-label first">' . __( 'Name', AFFILIATES_PLUGIN_DOMAIN ) . '</label>';
			echo '<input id="name-field-' . $i . '" name="name-field-' . $i . '" type="hidden" value="' . esc_attr( AFFILIATES_DIRECT_NAME ) . '"/>';
			echo '<input class="namefield" type="text" disabled="disabled" value="' . esc_attr( _x( 'Direct', 'pseudo-affiliate name', AFFILIATES_PLUGIN_DOMAIN ) ) . '"/>';
		}
		
		$email = $affiliate->email;
		echo '<label for="email-field-' . $i . '" class="field-label">' . __( 'Email', AFFILIATES_PLUGIN_DOMAIN ) . '</label>';
		echo '<input id="email-field-' . $i . '" name="email-field-' . $i . '" class="emailfield" type="text" value="' . esc_attr( $email ) . '"/>';
		
		echo '</p>';
		
		echo '<p>';
		
		$from_date = $affiliate->from_date;
		if ( !empty( $from_date ) ) {
			$from_date = date( 'Y-m-d', strtotime( $from_date ) );
		}
		$thru_date = $affiliate->thru_date;
		if ( !empty( $thru_date ) ) {
			$thru_date = date( 'Y-m-d', strtotime( $thru_date ) );
		}
		echo '<label for="from-date-field-' . $i . '" class="field-label first">' . __( 'From', AFFILIATES_PLUGIN_DOMAIN ) . '</label>';
		if ( !empty( $from_date ) ) {
			echo '<input id="from-date-field-' . $i . '" name="from-date-field-' . $i . '" class="datefield" type="text" value="'.esc_attr( $from_date ).'"/>';
		} else {
			echo '<input id="from-date-field-' . $i . '" name="from-date-field-' . $i . '" class="datefield" type="text">/';
		}
		echo '<label for="thru-date-field-' . $i . '" class="field-label">' . __( 'Until', AFFILIATES_PLUGIN_DOMAIN ) . '</label>';
		if ( !empty( $thru_date ) ) {
			echo '<input id="thru-date-field-' . $i . '" name="thru-date-field-' . $i . '" class="datefield" type="text" value="'.esc_attr( $thru_date ).'"/>';
		} else {
			echo '<input id="thru-date-field-' . $i . '" name="thru-date-field-' . $i . '" class="datefield" type="text"/>';
		}
		
		echo '<br/>';
		echo '<span class="description">' . __( 'Affiliates are inoperative outside these dates. Hits, visits and referrals will only be stored for operative affiliates.', AFFILIATES_PLUGIN_DOMAIN ) . '</span>';
		
		echo '</p>';
		
		$encoded_id = affiliates_encode_affiliate_id( $affiliate->affiliate_id ); 
		echo '<p id="affiliatelink-' . $i . '">' .
			__( 'Affiliate link', AFFILIATES_PLUGIN_DOMAIN ) . ' : <span class="affiliate-link">' . get_bloginfo('url') . '?affiliates=' . $encoded_id . '</span>' .
			'&nbsp;' .
			sprintf( __( 'You may also append %s to links to your posts.', AFFILIATES_PLUGIN_DOMAIN ), '<span class="affiliate-link">' . '?affiliates=' . $encoded_id . '</span>' ) .
			'</p>';
		echo '<p id="affiliatepermalink-' . $i . '">' .
			__( 'Affiliate permalink', AFFILIATES_PLUGIN_DOMAIN ) . ' : <span class="affiliate-permalink">' . get_bloginfo('url') . '/affiliates/' . $encoded_id . '</span>' .
			( $wp_rewrite->using_permalinks() ? '' :
				'<span class="warning">' .
				'&nbsp;' .
				sprintf( __( 'You cannot use this option unless you adjust your <a href="%s">permalink settings</a>.', AFFILIATES_PLUGIN_DOMAIN ), get_admin_url( null, 'options-permalink.php') ) .
				'</span>'
				) . 
			'</p>';				

		if ( $affiliate->type != AFFILIATES_DIRECT_TYPE ) {
			echo '<a class="removefromaffiliate" id="removefromaffiliate-' . $i . '" onClick="removeFromAffiliates(' . $i . ')">' . __( 'Remove', AFFILIATES_PLUGIN_DOMAIN ) . '</a>';
		} else {
			echo
				'<p>' .
				__( 'This is the pseudo-affiliate that represents you or your organization. Referrals that are not attributable to affiliates will be attributed to this one.', AFFILIATES_PLUGIN_DOMAIN ) .
				'</p>' .
				'<p>' .
				__( 'This is useful to keep track of all your transactions, including those that have not been initiated via an affiliate.', AFFILIATES_PLUGIN_DOMAIN ) .
				'</p>'
				;
		}
		
		echo '</div>'; // affiliate-$i
		$i++;
	}
	echo '</div>';
	
	echo '<div id="deleteaffiliates">';
	echo '</div>';
	
	echo '<div id="affiliatecontrols">';
	echo '<input id="affiliatecount" name="affiliatecount" type="hidden" value="'.$i.'"/>';
	echo '<p>';
	echo __('To create a new affiliate press', AFFILIATES_PLUGIN_DOMAIN );
	echo '<a class="addtoaffiliate" id="addtoaffiliate">' . __( 'Add', AFFILIATES_PLUGIN_DOMAIN ) . '</a>';
	echo '</p>';
	echo '<p>';
	echo __('Use the <i>Remove</i> button to eliminate an affiliate. Your changes will be applied after you press <i>Submit changes</i>.', AFFILIATES_PLUGIN_DOMAIN );
	echo '</p>';
	wp_nonce_field( plugin_basename( __FILE__ ), AFFILIATES_ADMIN_AFFILIATES_NONCE );
	echo '<input type="submit" value="' . __( 'Submit changes', AFFILIATES_PLUGIN_DOMAIN ) . '"/>';
	echo '</div>';
	
	echo '</form>';
	
	affiliates_footer();
}
?>