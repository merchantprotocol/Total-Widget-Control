/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.0
 * 
 * 
 */

/**
 * Fire some actions
 * 
 */
jQuery(document).ready(function(){
	jQuery('.twc_sidebar_select_box').change(sidebar_select_box);
	jQuery('#twc-widget-wrap').submit(twc_save_widget_edit);
	twc_ongoing_selection();
});

/**
 * Make the proper selections
 * @return
 */
function twc_ongoing_selection()
{
	jQuery('#side-sortables').find('[type=checkbox][name^=menu-item]').each(function(){
		if (twcSelectedIds.indexOf( jQuery(this).val() ) >= 0)
		{
			jQuery(this).attr('checked', true);
		}
		else
		{
			jQuery(this).attr('checked', false);
		}
	});
}

/**
 * This function is appended to the select box
 * @return false
 */
function sidebar_select_box(){
	var widget_id = jQuery(this).attr('id').replace("twc_sidebar_select_box_","");
	twc_save_sidebar(widget_id);
}

/**
 * saves the full edit area
 * @return false
 */
function twc_save_widget_edit()
{
	jQuery('#ajax-feedback').css('visibility','visible');
	
	//initializing variables
	var values = {};
	var inputs = jQuery('#menu-management-liquid').find(':input');
	values = twc_get_form_vars( inputs );
	var checkboxes = jQuery('#menu-settings-column').find('input[type=checkbox]:checked');
	re = new RegExp('menu-item\\[(\[^\\]\]*)');
	checkboxes.each(function() {
		values[this.name] = jQuery(this).val();
		var menu_item_id = re.exec(this.name);
		var menu_item_id = menu_item_id[1];
		values['menu-item['+menu_item_id+'][menu-item-url]'] = jQuery(".menu-item-url[name*="+menu_item_id+"]").val();
	});
	var radio = jQuery('#menu-management-liquid').find('input[type=radio]:checked');
	radio.each(function() {values[this.name] = jQuery(this).val();});
	var inputs = jQuery('.twcpi').find(':input');
	inputs.each(function(){values[this.name] = jQuery(this).val();});
	values['twcp_visible_parent'] = (jQuery('#twcp_visible_parent').attr('checked'))? jQuery('#twcp_visible_parent').val():''; 
	
	jQuery.ajax({
		type : 'POST',
		url: '?view=twc-save-edit',
		data: values,
		success: function( result ){
			//alert(result);
			if (jQuery('#twc-redirect').val())
			{
				window.location.href = jQuery('#twc-redirect').val();
			}
			else
			{
				jQuery('#ajax-feedback').css('visibility','hidden');
			}
		}
	});
	return false;
}

 /**
  * Save the quick edit form
  * 
  * @param widget_id
  * @return false
  */
 function twc_save_widget_qedit( widget_id )
 {
 	//initializing variables
 	var list_type = jQuery('#list_type-'+widget_id).val();
 	var sidebar_id = jQuery('#twc_sidebar_select_box_'+widget_id).val();
 	var inputs = jQuery('#edit-'+widget_id).find(':input');
 	var values = twc_get_form_vars( inputs );
 	
 	//load the spinner
 	jQuery('#edit-'+widget_id).find('.ajax-feedback:first').css('visibility','visible');
 	
 	jQuery.ajax({
 		type : 'POST',
 		url: '?view=twc-save-qedit&widget_id='+widget_id,
 		data: values,
 		success: function( result ){
 			//alert(result);
 			
 			// hide the widget if its inactive.
 			if (list_type == 'inactive' && sidebar_id != 'wp_inactive_widgets')
 			{
 				//jQuery('#tr_row_widget-'+widget_id).fadeOut("fast", function() { jQuery(this).remove(); } );
 				
 				//twc-table-qedit-activated
 				jQuery.ajax({
 					url: '?view=twc-table-qedit-activated&widget_id='+widget_id,
 					success: function( result ){
 						jQuery('#edit-'+widget_id).fadeOut("fast", function() { jQuery(this).remove(); } );
 						jQuery('#tr_row_widget-'+widget_id).html(result);
 					}
 				});
 			}
 			else
 			{
 				var title = jQuery('#widget-'+widget_id+'-title').val();
 				jQuery('#the_title-'+widget_id).html(title);
 				jQuery('#edit-'+widget_id).find('.ajax-feedback:first').css('visibility','hidden');
 			}
 		}
 	});
 	return false;	
 }
 
function twc_update_timestamp(){jQuery('#timestamp').html( 'Publish on: <b>'+jQuery('#mm').find(":selected").text()+' '+jQuery('#jj').val()+','+jQuery('#aa').val()+' @ '+jQuery('#hh').val()+':'+jQuery('#mn').val()+'</b>' );}
function twc_cancel_timestamp(){
	jQuery('#mm').val( jQuery('#twcp_mm_default').val() );
	jQuery('#jj').val( jQuery('#twcp_jj_default').val() );
	jQuery('#aa').val( jQuery('#twcp_YY_default').val() );
	jQuery('#hh').val( jQuery('#twcp_hh_default').val() );
	jQuery('#ii').val( jQuery('#twcp_ii_default').val() );
}
function twc_update_status(){jQuery('#post-status-display').html( jQuery('#twcp_status').find(":selected").text() );}
function twc_cancel_status(){jQuery('#twcp_status').val( jQuery('#twcp_status_default').val() );}
function twc_update_visiblity(){jQuery('#post-visibility-display').html( jQuery('#twcp_visibility').find(":selected").text() );}
function twc_cancel_visiblity(){jQuery('#twcp_visibility').val( jQuery('#twcp_visibility_default').val() );}

/**
 * Save the sidebar when the sidebar select box changes to a new form
 * 
 * @param widget_id
 * @return null
 */
function twc_save_sidebar( widget_id )
{
	var sidebar_id = jQuery('#twc_sidebar_select_box_'+widget_id).val();
	var position = jQuery('#position-'+widget_id).val();
	var list_type = jQuery('#list_type-'+widget_id).val();
	
	//show spinner
	jQuery('#tr_row_widget-'+widget_id).find('.ajax-feedback:first').css('visibility','visible');
	
	if (sidebar_id != 'wp_inactive_widgets')
	{
		jQuery.ajax({
			url: "/?view=twc-save-sidebar",
			data: {
				'twc_data[widget_id]' : widget_id,
				'twc_data[position]' : position,
				'twc_data[sidebar_slug]' : jQuery('#twc_sidebar_select_box_'+widget_id).val(),
			},
			success: function( result ){
				//alert(result);
	
				// hide the widget if its inactive.
				if (list_type == 'inactive' && sidebar_id != 'wp_inactive_widgets')
				{
					//jQuery('#tr_row_widget-'+widget_id).fadeOut("fast", function() { jQuery(this).remove(); } );
					
					//twc-table-qedit-activated
					jQuery.ajax({
						url: '?view=twc-table-qedit-activated&widget_id='+widget_id,
						success: function( result ){
							jQuery('#edit-'+widget_id).fadeOut("fast", function() { jQuery(this).remove(); } );
							jQuery('#tr_row_widget-'+widget_id).html(result);
						}
					});
				}
				else
				{
					jQuery('#tr_row_widget-'+widget_id).find('.ajax-feedback:first').css('visibility','hidden');
				}
			}
		});	
	}
	else
	{
		twc_trash_widget( widget_id );
	}
}
 
/**
 * loads the quick edit area
 * 
 * @param widget_id
 * @return false
 */
function twc_load_qedit( widget_id )
{
	jQuery.ajax({
		url: '?view=twc-table-qedit&widget_id='+widget_id,
		success: function( result ){
			var next = jQuery('#tr_row_widget-'+widget_id).next();
			if (next.attr('id') == 'edit-'+widget_id) return;
			
			jQuery('#tr_row_widget-'+widget_id).after(result);
			jQuery('.widget-top a.widget-action').css('display','none');
			jQuery('.widget-inside').css('display','block');
			jQuery('.widget-inside').css('padding','0 50px 50px');
			jQuery('.widget-inside input[text]').css('width','100%');
			jQuery('.widget-inside input[textarea]').css('width','100%');
		}
	});
	return false;
}
 
/**
 * function deletes the widget and loads the undo view
 *  
 * @param widget_id
 * @return false
 */
function twc_trash_widget( widget_id )
{
	jQuery('#tr_row_widget-'+widget_id).find('.ajax-feedback:first').css('visibility','visible');
	jQuery('#edit-'+widget_id).fadeOut("fast", function() { jQuery(this).remove(); } );
	 
	//initializing variables
	var old_sidebar_id = jQuery('#twc_sidebar_select_box_'+widget_id).val();
	 
	jQuery.ajax({
		url: '?view=twc-trash-instance&old_sidebar_id='+old_sidebar_id+'&widget_id='+widget_id,
		success: function( result ){
			jQuery('#tr_row_widget-'+widget_id).html(result);
	 	}
	});
	return false;
}

/**
 * Deletes the widget permanently
 * 
 * @param widget_id
 * @return
 */
function twc_delete_permanently( widget_id )
{
	jQuery('#tr_row_widget-'+widget_id).find('.ajax-feedback:first').css('visibility','visible');
	 
	jQuery.ajax({
		url: '?view=twc-trash-instance&delete_confirmation=1&widget_id='+widget_id,
		success: function( result ){
			jQuery('#tr_row_widget-'+widget_id).fadeOut("fast", function() { jQuery(this).remove(); } );
		
	 	}
	});
	return false;
}

/**
 * Function is responsible for the UNDO option when deactivating a widget
 *
 * @param widget_id
 * @return
 */
function twc_activate( widget_id, old_sidebar_id )
{
	alert(widget_id);
}

/**
 * closes the quick edit area
 *
 * @param widget_id
 * @return false
 */
function twc_close_qedit( widget_id )
{
	jQuery('#edit-'+widget_id).fadeOut("fast", function() { jQuery(this).remove(); } );
	return false; 
}

/**
 * builds an array out of inputs
 * 
 * @param inputs
 * @return false
 */
function twc_get_form_vars( inputs )
{
	var values = {};
	inputs.each(function() {
		if(this.type == 'checkbox')
		{
			if (jQuery(this).attr('checked'))
			{
				values[this.name] = (jQuery(this).attr('checked'))? 
					((jQuery(this).val()) ?jQuery(this).val() :'on')
					:''; 
			}
		}
		else
		{
			values[this.name] = jQuery(this).val();
		}
	});
	
	return values;
}
 
/**
 * 
 * @return
 */
function twc_hide_messages( messageID )
{
	jQuery.ajax({
		url: '?view=twc-hide-message&hide_ID='+messageID,
		success: function( result ){
			jQuery('.message'+messageID).fadeOut("fast", function() { jQuery(this).remove(); } );
	 	}
	});
}
 

