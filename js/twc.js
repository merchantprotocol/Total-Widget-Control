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
 * Total Widget Control
 * 
 * Javascript class to contain all of the elements needed for managing widgets.
 * 
 */
jQuery.noConflict();
var twc;

(function($) {
	var api = twc = {
			
		url : userSettings.url + '?view=',
		template_url : '',
		widget_id : '',
		debug: false,
		
		//Method is fired upon document.ready
		init : function()
		{
			if (typeof(twcOptions) != 'undefined')
			{
				api.template_url = twcOptions.template_url;
				api.url = twcOptions.url;
			}
			else
			{
				api.error_log( 'Could not locate twcOptions variable.', 'twc.init', '38' );
			}
			
			//loading
			$('.submit-add-to-menu').val('Add Widget To Pages');

			$('.twc_sidebar_select_box').change(sidebar_select_box);
			$('#twc-widget-wrap').submit(twc.save_widget_edit);
		},
		
		
		//little debugger error display at the bottom of your files
		debug_log : function( message )
		{
			if (!api.debug) return false;
			var debug = jQuery('#twc_debug_javascript').html();
			jQuery('#twc_debug_javascript').html(debug + '<br/>' + message);
		},
		
		
		//builds an array out of inputs
		inputs : function( inputs )
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
				twc.debug_log( '<li><em>'+this.name+'</em> = '+values[this.name]+'</li>' );
			});
			
			return values;
		},
		
		
		//Returns the entire set of edit form input:value pairs
		edit_form : function()
		{
			//initializing variables
			twc.debug_log( '<ul>' );
			var values = {};
			var inputs = jQuery('#menu-management-liquid').find(':input');
			values = api.inputs( inputs );
			
			var radio = jQuery('#menu-management-liquid').find('input[type=radio]:checked');
			radio.each(function() {
				values[this.name] = jQuery(this).val();
				twc.debug_log( '<li><em>'+this.name+'</em> = '+values[this.name]+'</li>' );
			});
			
			var inputs = jQuery('.twcpi').find(':input');
			inputs.each(function(){
				values[this.name] = jQuery(this).val();
				twc.debug_log( '<li><em>'+this.name+'</em> = '+values[this.name]+'</li>' );
			});
			values['twcp_visible_parent'] = (jQuery('#twcp_visible_parent').attr('checked'))? jQuery('#twcp_visible_parent').val():''; 
			twc.debug_log( '<li><em>twcp_visible_parent</em> = '+values['twcp_visible_parent']+'</li>' );
			twc.debug_log( '</ul>' );
			
			return values;
		},
		
		
		//Removes the menu item from display
		delete_menu_item : function( widget_id, object_id )
		{
			params = {};
			params['widget-id'] = widget_id;
			params['object-id'] = object_id;
				
			$.post( api.url + '?view=twc-menu-item-delete', params, function(r) {
				jQuery( '#object-'+ object_id ).fadeOut("fast", function() { jQuery(this).remove(); } );
			});
		},
		
		
		//function updates the positions select box with the new position list
		update_positions : function( sidebar_select )
		{
			//initializing variables
			var sidebar_id = sidebar_select.val();
			var positions;
			sidebar_select.find('option[value='+ sidebar_id +']').each(function(k,v){
				positions = jQuery(v).attr('positions');
			});
			var positions_select = jQuery('#sidebar_position');
			
			//reasons to fail
			if ( !positions_select ) return false;
			
			jQuery('option', positions_select).remove();
			
			for (i=0; i < positions; i++)
			{
				positions_select.append( new Option(i+1, i) );
			}
			positions_select.append( new Option('Last', i+1) );
		},
		
		
		//saves the widget on the edit screen
		save_widget_edit : function( event )
		{
			event.preventDefault();
			jQuery('#ajax-feedback').css('visibility','visible');
			
			//initializing variables
			twc.debug_log( '<h3>Get Edit Form Values</h3>' );
			dataValues = api.edit_form();
			var need_prompt = (jQuery('.twc_sidebar_select_box:first').val() == 'wp_inactive_widgets');
			
			twc.debug_log( '<em>' + api.url + '?view=twc-save-edit</em>' );
			jQuery.ajax({
				type : 'POST',
				url: api.url + '?view=twc-save-edit',
				data: dataValues,
				error : function(jqXHR, textStatus)
				{
					api.error_log( jqXHR.status +' '+jqXHR.statusText +' : '+ textStatus, 'save_widget_edit', '95' );
					jQuery('#ajax-feedback').css('visibility','hidden');
				},
				success: function( result ){
					twc.debug_log( '<blockquote>'+ result +'</blockquote>' );
					if (jQuery('#twc-redirect').val() && (!need_prompt || (need_prompt && confirm("You're saving this to the inactive widgets sidebar, continue?"))) )
					{
						window.location.href = jQuery('#twc-redirect').val();
					}
					else
					{
						jQuery('#twc-redirect').val('');
						jQuery('#ajax-feedback').css('visibility','hidden');
					}
					twc.debug_log( '<b>Successful</b>' );
				}
			});
			return false;
		},
		
		
		//Save the quick edit form
		save_quick_edit: function( widget_id )
		{
			api.widget_id = widget_id;
			
			//initializing variables
		 	var list_type = jQuery('#list_type-'+widget_id).val();
		 	var sidebar_id = jQuery('#twc_sidebar_select_box_'+widget_id).val();
		 	var inputs = jQuery('#edit-'+widget_id).find(':input');
		 	var values = api.inputs( inputs );
		 	
		 	//load the spinner
		 	jQuery('#edit-'+widget_id).find('.ajax-feedback:first').css('visibility','visible');
		 	
		 	jQuery.ajax({
		 		type : 'POST',
		 		url: api.url+'?view=twc-save-qedit&widget_id='+widget_id,
		 		data: values,
				error : function(jqXHR, textStatus)
				{
					api.error_log( jqXHR.status +' '+jqXHR.statusText +' : '+ textStatus, 'save_quick_edit', '144' );
					jQuery('#ajax-feedback').css('visibility','hidden');
				},
		 		success: function( result ){
		 			// hide the widget if its inactive.
		 			if (list_type == 'inactive' && sidebar_id != 'wp_inactive_widgets')
		 			{
		 				//jQuery('#tr_row_widget-'+widget_id).fadeOut("fast", function() { jQuery(this).remove(); } );
		 				
		 				//twc-table-qedit-activated
		 				jQuery.ajax({
		 					url: api.url+'?view=twc-table-qedit-activated&widget_id='+widget_id,
		 					error : function(jqXHR, textStatus)
		 					{
		 						api.error_log( jqXHR.status +' '+jqXHR.statusText +' : '+ textStatus, 'twc-table-qedit-activated', '157' );
		 					},
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
		},
		
		
		//prints the error to the screen
		//@TODO need to save this to the database via ajax
		error_log : function( message, func, line )
		{
			//initializing variables
			if (typeof(func) != 'undefined' || typeof(line) != 'undefined')
				message = func +' '+ message +' on line '+ line;
			message = 'jQuery.twc : ' + message;
			
			//error reporting
			twc.debug_log( message );
			console.log( message );
		}
	
	};

	//firing the initialization class
	$(document).ready(function($){ twc.init(); });

})(jQuery);


/**
 * 
 * @return
 */
function sidebar_select_box(){
	var widget_id = jQuery(this).attr('id').replace("twc_sidebar_select_box_","");
	twc_update_positions_select( jQuery(this) );
	twc_save_sidebar(widget_id);
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
			url: userSettings.url+"?view=twc-save-sidebar",
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
						url: userSettings.url+'?view=twc-table-qedit-activated&widget_id='+widget_id,
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
		url: userSettings.url+'?view=twc-table-qedit&widget_id='+widget_id,
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
		url: userSettings.url+'?view=twc-trash-instance&old_sidebar_id='+old_sidebar_id+'&widget_id='+widget_id,
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
		url: userSettings.url+'?view=twc-trash-instance&delete_confirmation=1&widget_id='+widget_id,
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
 * 
 * @return
 */
function twc_hide_messages( messageID )
{
	jQuery.ajax({
		url: userSettings.url+'?view=twc-hide-message&hide_ID='+messageID,
		success: function( result ){
			jQuery('.message'+messageID).fadeOut("fast", function() { jQuery(this).remove(); } );
	 	}
	});
}

 if(!Array.indexOf){
	    Array.prototype.indexOf = function(obj){
	        for(var i=0; i<this.length; i++){
	            if(this[i]==obj){
	                return i;
	            }
	        }
	        return -1;
	    }
	}
 
 
//Deprecated functions as of 1.6.11
function twc_update_positions_select( sidebar_select ){return twc.update_positions(sidebar_select);}
function twc_ongoing_selection( widget_id, object_id ){return;}
function twc_save_widget_edit(){return twc.save_widget_edit();}
function twc_save_widget_qedit( widget_id ){return twc.save_quick_edit(widget_id);}
function twc_get_form_vars( inputs ){return twc.inputs( inputs ); }

