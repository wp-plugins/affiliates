/**
 * affiliates.js
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
jQuery(document).ready(function(){
	
	/* design */
	jQuery(".affiliate").corner("5px");
	jQuery(".filters").corner("5px");
	
	/* effects & handling */
	jQuery('.view-toggle').each( function() {
		jQuery(this).click(function() {
			var description = jQuery(this).children(".view");
			var expander = jQuery(this).children(".expander");
			//console.log(description);
			if ( description.is(":hidden") ) {
				description.slideDown("fast");
				expander.contents().remove();
				expander.append("[-] ");
			} else {
				description.slideUp("fast");
				expander.contents().remove();
				expander.append("[+] ");
			}
		});
	});
	
	/* functionality */
	var i = jQuery('#affiliatefields').children('div.affiliate').length;
	jQuery("a.removefromaffiliate").button();
	jQuery("a.addtoaffiliate").button();
	jQuery('#addtoaffiliate').click(function() {
		jQuery(
			'<div id="affiliate-' + i + '" class="affiliate new">' +
			
				'<p>' +
				'<label for="name-field-' + i + '" class="field-label first">' + nameFieldLabel + '</label>' +
				'<input id="name-field-' + i + '" name="name-field-' + i + '" class="namefield" type="text"/>' +
				'<label for="email-field-' + i + '" class="field-label">' + emailFieldLabel + '</label>' +
				'<input id="email-field-' + i + '" name="email-field-' + i + '" class="emailfield" type="text"/>' +
				'</p>' +
				'<p>' +
				'<label for="from-date-field-' + i + '" class="field-label first">' + fromDateFieldLabel + '</label>' +
				'<input id="from-date-field-' + i + '" name="from-date-field-' + i + '" class="datefield" type="text"/>' +
				'<label for="thru-date-field-' + i + '" class="field-label">' + thruDateFieldLabel + '</label>' +
				'<input id="thru-date-field-' + i + '" name="thru-date-field-' + i + '" class="datefield" type="text"/>' +
				'</p>' +
				'<p id="affiliatelink-' + i + '">' + submitForAffiliateLinkText + '</p>' +
				
				'<a class="removefromaffiliate" id="removefromaffiliate-' + i + '" onClick="removeFromAffiliates(' + i + ')">' + removeButtonText + '</a>' +
				
				
			'</div>'
		).appendTo('#affiliatefields');
		
		jQuery('#from-date-field-'+i).datepicker({dateFormat:'yy-mm-dd'});
		jQuery('#thru-date-field-'+i).datepicker({dateFormat:'yy-mm-dd'});
		jQuery("#removefromaffiliate-"+i).button();
		i++;
		jQuery('#affiliatecount').attr('value', i);
	});
});

function removeFromAffiliates(i) {
	var id = jQuery('#affiliate-id-'+i).attr('value');
	if ( id != null ) {
		jQuery('<input type="hidden" name="delete-affiliate-id-'+i+'" value="'+id+'"/>').appendTo('#deleteaffiliates');
	}
	jQuery('#affiliate-'+i).slideUp(1000, function() { jQuery(this).remove(); } );
}
