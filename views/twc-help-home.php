<?php 
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

defined('ABSPATH') or die("Cannot access pages directly.");
if ( TWC_CURRENT_USER_CANNOT ) wp_die();

?>
<div>
	<h4>Total Widget Control - Help Section</h4>
	<p>Welcome to the 5Twenty Studios Total Widget Control Plugin, for Wordpress 3.0+. 
	This help section is designed to be a quick resource for new administrators. If you
	have questions and your answers are not found in this window, please visit our 
	community links below.</p>
	<p><a href="http://community.5twentystudios.com/?kb" target="_blank">TWC Documentation</a></p>
	<p><a href="http://community.5twentystudios.com/community/" target="_blank">Support Forums</a></p>
	<p><strong style="color:#555;"><?php echo __('Frequently Asked Questions:'); ?></strong></p>
	<p>As you hover over the following items, we will display information next to the 
	item in the page.</p>
	<ul class="hot-to-twc">
	<li id="qtipNewWidget" tooltip="Click here to add a new widget">How do I add a new widget?</li>
	<li id="qtipInactiveWidgets" tooltip="Inactive widgets are stored in the Trash, which is just another name for inactive widget.">Where are my inactive widgets?</li>
	<li id="qtipSwitchView" tooltip="Click here to switch back to the Wordpress Widget Screen">Ho do I switch back to the old widget screen?</li>
	<li id="qtipEditWidget" tooltip="This is the name of the Widget, click here to edit this instance">How do I edit a widget?</li>
	<li id="qtipChooseSidebar" tooltip="As soon as you change the sidebar value, it will update itself.">How do I change where on the page my widget will display?</li>
	</ul>
</div>
<script>
jQuery(document).ready(function(){
	jQuery('.twc_sidebar_select_box:first').qtip({
		content: { text: jQuery('#qtipChooseSidebar').attr('tooltip') },
		show: { when: { target: jQuery('#qtipChooseSidebar') } },
	    hide: { when: { target: jQuery('#qtipChooseSidebar'), event: 'mouseout' } },
	    position: {
	        corner: {
	           target: 'topMiddle',
	           tooltip: 'bottomMiddle'
	        }
	     },
	     style: {
	        name: 'dark',
	        padding: '7px 13px',
	        width: {
	           max: 210,
	           min: 0
	        },
	        tip: true
	     }
	});
	
	
	jQuery('.row-title:first').qtip({
		content: { text: jQuery('#qtipEditWidget').attr('tooltip') },
		show: { when: { target: jQuery('#qtipEditWidget') } },
	    hide: { when: { target: jQuery('#qtipEditWidget'), event: 'mouseout' } },
	    position: {
	        corner: {
	           target: 'rightMiddle',
	           tooltip: 'leftMiddle'
	        }
	     },
	     style: {
	        name: 'dark',
	        padding: '7px 13px',
	        width: {
	           max: 210,
	           min: 0
	        },
	        tip: true
	     }
	});

	
	jQuery('#view-switch-excerpt').qtip({
		content: { text: jQuery('#qtipSwitchView').attr('tooltip') },
		show: { when: { target: jQuery('#qtipSwitchView') } },
	    hide: { when: { target: jQuery('#qtipSwitchView'), event: 'mouseout' } },
	    position: {
	        corner: {
	           target: 'topMiddle',
	           tooltip: 'bottomRight'
	        }
	     },
	     style: {
	        name: 'dark',
	        padding: '7px 13px',
	        width: {
	           max: 210,
	           min: 0
	        },
	        tip: true
	     }
	});

	
	jQuery('.add-new-h2:first').qtip({
		content: { text: jQuery('#qtipNewWidget').attr('tooltip') },
		show: { when: { target: jQuery('#qtipNewWidget') } },
	    hide: { when: { target: jQuery('#qtipNewWidget'), event: 'mouseout' } },
	    position: {
	        corner: {
	           target: 'topMiddle',
	           tooltip: 'bottomLeft'
	        }
	     },
	     style: {
	        name: 'dark',
	        padding: '7px 13px',
	        width: {
	           max: 210,
	           min: 0
	        },
	        tip: true
	     }
	});

	jQuery('#twcInactiveWidgetsArea').qtip({
		content: { text: jQuery('#qtipInactiveWidgets').attr('tooltip') },
		show: { when: { target: jQuery('#qtipInactiveWidgets') } },
	    hide: { when: { target: jQuery('#qtipInactiveWidgets'), event: 'mouseout' } },
	    position: {
	        corner: {
	           target: 'topMiddle',
	           tooltip: 'bottomMiddle'
	        }
	     },
	     style: {
	        name: 'dark',
	        padding: '7px 13px',
	        width: {
	           max: 210,
	           min: 0
	        },
	        tip: true
	     }
	});
});


</script>
