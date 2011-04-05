/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.5.5
 * 
 * @tutorial http://jqueryui.com/demos/sortable/
 * 
 */
jQuery.noConflict();
var twcSortables;
(function($) {

twcSortables = {

	init : function() {
		var sidebars = $('.twc_sortable_sidebar');
		
		sidebars.sortable();
	}

};

$(document).ready(function($){ twcSortables.init(); });

})(jQuery);