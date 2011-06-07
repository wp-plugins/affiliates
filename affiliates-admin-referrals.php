<?php
/**
 * affiliates-admin-referrals.php
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
	// Shows referrals by date

include_once( dirname( __FILE__ ) . '/class-affiliates-date-helper.php');

function affiliates_admin_referrals() {
	
	global $wpdb, $affiliates_options;
	
	if ( !current_user_can( AFFILIATES_ACCESS_AFFILIATES ) ) {
		wp_die( __( 'Access denied.', AFFILIATES_PLUGIN_DOMAIN ) );
	}
	
	if (
		isset( $_POST['from_date'] ) ||
		isset( $_POST['thru_date'] ) ||
		isset( $_POST['clear_filters'] ) ||
		isset( $_POST['affiliate_id'] ) ||
		isset( $_POST['expanded'] ) ||
		isset( $_POST['expanded_data'] ) ||
		isset( $_POST['expanded_description'] )
	) {
		if ( !wp_verify_nonce( $_POST[AFFILIATES_ADMIN_HITS_FILTER_NONCE], plugin_basename( __FILE__ ) ) ) {
			wp_die( __( 'Access denied.', AFFILIATES_PLUGIN_DOMAIN ) );
		}
	}
	
	// filters
	$from_date            = $affiliates_options->get_option( 'referrals_from_date', null );
	$thru_date            = $affiliates_options->get_option( 'referrals_thru_date', null );
	$affiliate_id         = $affiliates_options->get_option( 'referrals_affiliate_id', null );
	$expanded             = $affiliates_options->get_option( 'referrals_expanded', null );
	$expanded_description = $affiliates_options->get_option( 'referrals_expanded_description', null );
	$expanded_data        = $affiliates_options->get_option( 'referrals_expanded_data', null );
	$show_inoperative     = $affiliates_options->get_option( 'referrañs_show_inoperative', null );
	
	if ( isset( $_POST['clear_filters'] ) ) {
		$affiliates_options->delete_option( 'referrals_from_date' );
		$affiliates_options->delete_option( 'referrals_thru_date' );
		$affiliates_options->delete_option( 'referrals_affiliate_id' );
		$affiliates_options->delete_option( 'referrals_expanded' );
		$affiliates_options->delete_option( 'referrals_expanded_description' );
		$affiliates_options->delete_option( 'referrals_expanded_data' );
		$affiliates_options->delete_option( 'referrals_show_inoperative' );
		$from_date = null;
		$thru_date = null;
		$affiliate_id = null;
		$expanded = null;
		$expanded_data = null;
		$expanded_description = null;
		$show_inoperative = null;
	} else {
		// filter by date(s)
		if ( !empty( $_POST['from_date'] ) ) {
			$from_date = date( 'Y-m-d', strtotime( $_POST['from_date'] ) );
			$affiliates_options->update_option( 'referrals_from_date', $from_date );
		}
		if ( !empty( $_POST['thru_date'] ) ) {
			$thru_date = date( 'Y-m-d', strtotime( $_POST['thru_date'] ) );
			$affiliates_options->update_option( 'referrals_thru_date', $thru_date );
		}
		if ( $from_date && $thru_date ) {
			if ( strtotime( $from_date ) > strtotime( $thru_date ) ) {
				$thru_date = null;
				$affiliates_options->delete_option( 'referrals_thru_date' );
			}
		}
		// We now have the desired dates from the user's point of view, i.e. in her timezone.
		// If supported, adjust the dates for the site's timezone:
		if ( $from_date ) {
			$from_datetime = DateHelper::u2s( $from_date );
		}
		if ( $thru_date ) {
			$thru_datetime = DateHelper::u2s( $thru_date, 24*3600 );
		}

		// filter by affiliate id
		if ( !empty( $_POST['affiliate_id'] ) ) {
			$affiliate_id = affiliates_check_affiliate_id( $_POST['affiliate_id'] );
			if ( $affiliate_id ) {
				$affiliates_options->update_option( 'referrals_affiliate_id', $affiliate_id );
			}
		} else if ( isset( $_POST['affiliate_id'] ) ) { // empty && isset => '' => all
			$affiliate_id = null;
			$affiliates_options->delete_option( 'referrals_affiliate_id' );	
		}
		
		// expanded details?
		if ( !empty( $_POST['submitted'] ) ) {
			if ( !empty( $_POST['expanded'] ) ) {
				$expanded = true;
				$affiliates_options->update_option( 'referrals_expanded', true );
			} else {
				$expanded = false;
				$affiliates_options->delete_option( 'referrals_expanded' );
			}
			if ( !empty( $_POST['expanded_data'] ) ) {
				$expanded_data = true;
				$affiliates_options->update_option( 'referrals_expanded_data', true );
			} else {
				$expanded_data = false;
				$affiliates_options->delete_option( 'referrals_expanded_data' );
			}
			if ( !empty( $_POST['expanded_description'] ) ) {
				$expanded_description = true;
				$affiliates_options->update_option( 'referrals_expanded_description', true );
			} else {
				$expanded_description = false;
				$affiliates_options->delete_option( 'referrals_expanded_description' );
			}
			if ( !empty( $_POST['show_inoperative'] ) ) {
				$show_inoperative = true;
				$affiliates_options->update_option( 'referrals_show_inoperative', true );
			} else {
				$show_inoperative = false;
				$affiliates_options->delete_option( 'referrals_show_inoperative' );
			}
		}
	}
	
	if ( isset( $_POST['row_count'] ) ) {
		if ( !wp_verify_nonce( $_POST[AFFILIATES_ADMIN_HITS_NONCE_1], plugin_basename( __FILE__ ) ) ) {
			wp_die( __( 'Access denied.', AFFILIATES_PLUGIN_DOMAIN ) );
		}
	}
	
	if ( isset( $_POST['paged'] ) ) {
		if ( !wp_verify_nonce( $_POST[AFFILIATES_ADMIN_HITS_NONCE_2], plugin_basename( __FILE__ ) ) ) {
			wp_die( __( 'Access denied.', AFFILIATES_PLUGIN_DOMAIN ) );
		}
	}
	
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = remove_query_arg( 'paged', $current_url );
	
	$affiliates_table = _affiliates_get_tablename( 'affiliates' );
	$referrals_table = _affiliates_get_tablename( 'referrals' );
	$hits_table = _affiliates_get_tablename( 'hits' );
	$posts_table = $wpdb->prefix . 'posts';
	
	echo
		'<div>' .
			'<h2>' .
				__( 'Referrals', AFFILIATES_PLUGIN_DOMAIN ) .
			'</h2>' .
		'</div>';

	$row_count = intval( $_POST['row_count'] );
	
	if ($row_count <= 0) {
		$row_count = $affiliates_options->get_option( 'referrals_per_page', AFFILIATES_HITS_PER_PAGE );
	} else {
		$affiliates_options->update_option('referrals_per_page', $row_count );
	}
	$offset = intval( $_GET['offset'] );
	if ( $offset < 0 ) {
		$offset = 0;
	}
	$paged = intval( $_GET['paged'] );
	if ( $paged < 0 ) {
		$paged = 0;
	} 
	
	$orderby = $_GET['orderby'];
	switch ( $orderby ) {
		case 'datetime' :
		case 'name' :
		case 'post_title' :
			break;
		default :
			$orderby = 'datetime';
	}
	
	$order = $_GET['order'];
	switch ( $order ) {
		case 'asc' :
		case 'ASC' :
			$switch_order = 'DESC';
			break;
		case 'desc' :
		case 'DESC' :
			$switch_order = 'ASC';
			break;
		default:
			$order = 'DESC';
			$switch_order = 'ASC';
	}
	
	if ( $from_date || $thru_date || $affiliate_id ) {
		$filters = " WHERE ";
	} else {
		$filters = '';			
	}
	$filter_params = array();
	if ( $from_date && $thru_date ) {
		$filters .= " datetime >= %s AND datetime < %s ";
		$filter_params[] = $from_datetime;
		$filter_params[] = $thru_datetime;
	} else if ( $from_date ) {
		$filters .= " datetime >= %s ";
		$filter_params[] = $from_datetime;
	} else if ( $thru_date ) {
		$filters .= " datetime < %s ";
		$filter_params[] = $thru_datetime;
	}
	if ( $affiliate_id ) {
		if ( $from_date || $thru_date ) {
			$filters .= " AND ";
		}
		$filters .= " r.affiliate_id = %d ";
		$filter_params[] = $affiliate_id;
	}
	
	// how many are there ?
	$count_query = $wpdb->prepare(
		"SELECT count(*) FROM $referrals_table r
		$filters
		",
		$filter_params
	);
	$count = $wpdb->get_var( $count_query );
	
	if ( $count > $row_count ) {
		$paginate = true;
	} else {
		$paginate = false;
	}
	$pages = ceil ( $count / $row_count );
	if ( $paged > $pages ) {
		$paged = $pages;
	}
	if ( $paged != 0 ) {
		$offset = ( $paged - 1 ) * $row_count;
	}
			
	$query = $wpdb->prepare("
		SELECT *
		FROM $referrals_table r
		LEFT JOIN $affiliates_table a ON r.affiliate_id = a.affiliate_id
		LEFT JOIN $posts_table p ON r.post_id = p.ID
		$filters
		ORDER BY $orderby $order
		LIMIT $row_count OFFSET $offset
		",
		$filter_params + $filter_params
	);
	
	$results = $wpdb->get_results( $query, OBJECT );		

	$column_display_names = array(
		'datetime'   => __( 'Date', AFFILIATES_PLUGIN_DOMAIN ),
		'post_title' => __( 'Post', AFFILIATES_PLUGIN_DOMAIN ),
		'name'       => __( 'Affiliate', AFFILIATES_PLUGIN_DOMAIN )
	);
	
	$column_count = count( $column_display_names );
	
	$output .= '<div id="referrals-overview" class="referrals-overview">';
		
	$affiliates = affiliates_get_affiliates( true, !$show_inoperative );
	if ( !empty( $affiliates ) ) {
		$affiliates_select .= '<label class="affiliate-id-filter" for="affiliate_id">' . __('Affiliate', AFFILIATES_PLUGIN_DOMAIN ) . '</label>';
		$affiliates_select .= '<select class="affiliate-id-filter" name="affiliate_id">';
		$affiliates_select .= '<option value="">--</option>';
		foreach ( $affiliates as $affiliate ) {
			if ( $affiliate_id == $affiliate['affiliate_id']) {
				$selected = ' selected="selected" ';
			} else {
				$selected = '';
			}
			$affiliates_select .= '<option ' . $selected . ' value="' . esc_attr( $affiliate['affiliate_id'] ) . '">' . esc_attr( stripslashes( $affiliate['name'] ) ) . '</option>';
		}
		$affiliates_select .= '</select>';
	}
	
	$output .=
		'<div class="filters">' .
			'<label class="description" for="setfilters">' . __( 'Filters', AFFILIATES_PLUGIN_DOMAIN ) . '</label>' .
			'<form id="setfilters" action="" method="post">' .
				'<p>' .
				$affiliates_select .
				'</p>
				<p>' .
				'<label class="from-date-filter" for="from_date">' . __('From', AFFILIATES_PLUGIN_DOMAIN ) . '</label>' .
				'<input class="datefield from-date-filter" name="from_date" type="text" value="' . esc_attr( $from_date ) . '"/>'.
				'<label class="thru-date-filter" for="thru_date">' . __('Until', AFFILIATES_PLUGIN_DOMAIN ) . '</label>' .
				'<input class="datefield thru-date-filter" name="thru_date" type="text" class="datefield" value="' . esc_attr( $thru_date ) . '"/>'.
				'</p>
				<p>' .
				wp_nonce_field( plugin_basename( __FILE__ ), AFFILIATES_ADMIN_HITS_FILTER_NONCE, true, false ) .
				'<input type="submit" value="' . __( 'Apply', AFFILIATES_PLUGIN_DOMAIN ) . '"/>' .
				'<label class="expanded-filter" for="expanded">' . __( 'Expand details', AFFILIATES_PLUGIN_DOMAIN ) . '</label>' .
				'<input class="expanded-filter" name="expanded" type="checkbox" ' . ( $expanded ? 'checked="checked"' : '' ) . '/>' .
				'<label class="expanded-filter" for="expanded_description">' . __( 'Expand descriptions', AFFILIATES_PLUGIN_DOMAIN ) . '</label>' .
				'<input class="expanded-filter" name="expanded_description" type="checkbox" ' . ( $expanded_description ? 'checked="checked"' : '' ) . '/>' .
				'<label class="expanded-filter" for="expanded_data">' . __( 'Expand data', AFFILIATES_PLUGIN_DOMAIN ) . '</label>' .
				'<input class="expanded-filter" name="expanded_data" type="checkbox" ' . ( $expanded_data ? 'checked="checked"' : '' ) . '/>' .
				'<label class="show-inoperative-filter" for="show_inoperative">' . __( 'Include inoperative affiliates', AFFILIATES_PLUGIN_DOMAIN ) . '</label>' .
				'<input class="show-inoperative-filter" name="show_inoperative" type="checkbox" ' . ( $show_inoperative ? 'checked="checked"' : '' ) . '/>' .
				'<input type="submit" name="clear_filters" value="' . __( 'Clear', AFFILIATES_PLUGIN_DOMAIN ) . '"/>' .
				'<input type="hidden" value="submitted" name="submitted"/>' .
				'</p>' .
			'</form>' .
		'</div>';
						
	$output .= '
		<div class="page-options">
			<form id="setrowcount" action="" method="post">
				<div>
					<label for="row_count">' . __('Results per page', AFFILIATES_PLUGIN_DOMAIN ) . '</label>' .
					'<input name="row_count" type="text" size="2" value="' . esc_attr( $row_count ) .'" />
					' . wp_nonce_field( plugin_basename( __FILE__ ), AFFILIATES_ADMIN_HITS_NONCE_1, true, false ) . '
					<input type="submit" value="' . __( 'Apply', AFFILIATES_PLUGIN_DOMAIN ) . '"/>
				</div>
			</form>
		</div>
		';
		
	if ( $paginate ) {
	  require_once(dirname( __FILE__ ) . '/class-affiliates-pagination.php' );
		$pagination = new Affiliates_Pagination($count, $paged, $row_count);
		$output .= '<form id="posts-filter" method="post" action="">';
		$output .= '<div>';
		$output .= wp_nonce_field( plugin_basename( __FILE__ ), AFFILIATES_ADMIN_HITS_NONCE_2, true, false );
		$output .= '</div>';
		$output .= '<div class="tablenav top">';
		$output .= $pagination->pagination( 'top' );
		$output .= '</div>';
		$output .= '</form>';
	}
					
	$output .= '
		<table id="referrals" class="referrals wp-list-table widefat fixed" cellspacing="0">
		<thead>
			<tr>
			';
	
	foreach ( $column_display_names as $key => $column_display_name ) {
		$options = array(
			'orderby' => $key,
			'order' => $switch_order
		);
		$class = "";
		if ( strcmp( $key, $orderby ) == 0 ) {
			$lorder = strtolower( $order );
			$class = "$key manage-column sorted $lorder";
		} else {
			$class = "$key manage-column sortable";
		}
		$column_display_name = '<a href="' . esc_url( add_query_arg( $options, $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
		$output .= "<th scope='col' class='$class'>$column_display_name</th>";
	}
	
	$output .= '</tr>
		</thead>
		<tbody>
		';
		
	if ( count( $results ) > 0 ) {

		for ( $i = 0; $i < count( $results ); $i++ ) {
			
			$result = $results[$i];
							
			$output .= '<tr class="details-referrals ' . ( $i % 2 == 0 ? 'even' : 'odd' ) . '">';
			$output .= '<td class="datetime">' . DateHelper::s2u( $result->datetime ) . '</td>';
			$link = get_permalink( $result->post_id );
			$title = get_the_title( $result->post_id );
			$output .= '<td class="post_title"><a href="' . esc_attr( $link ) . '" target="_blank">' . wp_filter_nohtml_kses( $title ) . '</a></td>';
			$output .= "<td class='name'>" . stripslashes( wp_filter_nohtml_kses( $result->name ) ) . "</td>";
			$output .= '</tr>';
			
			$data = $result->data;
			if ( !empty( $data )  && $expanded ) {
				if ( $expanded_data ) {
					$data_view_style = '';
					$expander = AFFILIATES_EXPANDER_RETRACT;
				} else {
					$data_view_style = ' style="display:none;" ';
					$expander = AFFILIATES_EXPANDER_EXPAND;
				}
				$data = unserialize( $data );
				if ( $data ) {
					$output .= '<tr>';
					$output .= "<td colspan='$column_count'>";
					$output .= '<div class="view-toggle">';
					$output .= "<div class='expander'>$expander</div>";
					$output .= '<div class="view-toggle-label">' . __( 'Data', AFFILIATES_PLUGIN_DOMAIN ) . '</div>';
					$output .= "<div class='view' $data_view_style>";
					$output .= '<table class="referral-data wp-list-table widefat fixed" cellspacing="0">';
					if ( is_array( $data ) ) {
						foreach ( $data as $key => $info ) {
							$title = __( $info['title'], $info['domain'] );
							$value = $info['value'];
							$output .= "<tr id='referral-data-$i'>";
							$output .= '<td class="referral-data-title">';
							$output .= wp_filter_nohtml_kses( $title );
							$output .= '</td>';
							$output .= '<td class="referral-data-value">';
							$output .= wp_filter_nohtml_kses( $value );
							$output .= '</td>';
							$output .= '</tr>';
						}
					} else {
						$output .= "<tr id='referral-data-$i'>";
						$output .= '<td class="referral-data-title">';
						$output .= __( 'Data', AFFILIATES_PLUGIN_DOMAIN );
						$output .= '</td>';
						$output .= '<td class="referral-data-value">';
						$output .= wp_filter_nohtml_kses( $data );
						$output .= '</td>';
						$output .= '</tr>';
					}
					$output .= '</table>';
					$output .= '</div>'; // .view
					$output .= '</div>'; // .view-toggle
					$output .= '</td>';
					$output .= '</tr>';
				}
			}
			
			if ( !empty( $result->description ) && $expanded ) {
				if ( $expanded_description ) {
					$description_view_style = '';
					$expander = AFFILIATES_EXPANDER_RETRACT;
				} else {
					$description_view_style = ' style="display:none;" ';
					$expander = AFFILIATES_EXPANDER_EXPAND;
				}
				$output .= "<tr id='referral-description-$i'>" .
					'<td colspan="3">' .
						'<div class="view-toggle">' .
							"<div class='expander'>$expander</div>" .
							'<div class="view-toggle-label">' . __('Description', AFFILIATES_PLUGIN_DOMAIN ) . '</div>' .
							"<div class='view' $description_view_style>" .
								wp_filter_kses( addslashes( $result->description ) ) .
							'</div>' .
						'</div>' .
					'</td>' .
				'</tr>';
			}
		}
	} else {
		$output .= '<tr><td colspan="' . $column_count . '">' . __('There are no results.', AFFILIATES_PLUGIN_DOMAIN ) . '</td></tr>';
	}
		
	$output .= '</tbody>';
	$output .= '</table>';
					
	if ( $paginate ) {
	  require_once( dirname( __FILE__ ) . '/class-affiliates-pagination.php' );
		$pagination = new Affiliates_Pagination( $count, null, $row_count );
		$output .= '<div class="tablenav bottom">';
		$output .= $pagination->pagination( 'bottom' );
		$output .= '</div>';			
	}

	$output .= '</div>'; // .visits-overview
	echo $output;
	affiliates_footer();
} // function affiliates_admin_hits()
?>