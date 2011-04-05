/**
 * @Author	5Twenty Studios
 * @link http://www.5twentystudios.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.0
 * 
 * This files is a modification of the WordPress Administration Navigation Menu
 * Interface JS functions
 */

var wpNavMenu;

(function($) {

	var api = wpNavMenu = {

		options : {
			menuItemDepthPerLevel : 30, // Do not use directly. Use depthToPx and pxToDepth instead.
			globalMaxDepth : 11
		},

		menuList : undefined,	// Set in init.
		targetList : undefined, // Set in init.
		menusChanged : false,
		isRTL: !! ( 'undefined' != typeof isRtl && isRtl ),
		negateIfRTL: ( 'undefined' != typeof isRtl && isRtl ) ? -1 : 1,

		// Functions that run on init.
		init : function() {
			api.menuList = $('#menu-to-edit');
			api.targetList = api.menuList;

			this.jQueryExtensions();

			this.attachMenuEditListeners();

			this.setupInputWithDefaultTitle();
			this.attachQuickSearchListeners();
			this.attachThemeLocationsListeners();

			this.attachTabsPanelListeners();

			this.attachUnsavedChangesListener();

			this.initToggles();

			this.initTabManager();
		},

		jQueryExtensions : function() {
			// jQuery extensions
			$.fn.extend({
				menuItemDepth : function() {
					var margin = api.isRTL ? this.eq(0).css('margin-right') : this.eq(0).css('margin-left');
					return api.pxToDepth( margin && -1 != margin.indexOf('px') ? margin.slice(0, -2) : 0 );
				},
				updateDepthClass : function(current, prev) {
					return this.each(function(){
						var t = $(this);
						prev = prev || t.menuItemDepth();
						$(this).removeClass('menu-item-depth-'+ prev )
							.addClass('menu-item-depth-'+ current );
					});
				},
				shiftDepthClass : function(change) {
					return this.each(function(){
						var t = $(this),
							depth = t.menuItemDepth();
						$(this).removeClass('menu-item-depth-'+ depth )
							.addClass('menu-item-depth-'+ (depth + change) );
					});
				},
				childMenuItems : function() {
					var result = $();
					this.each(function(){
						var t = $(this), depth = t.menuItemDepth(), next = t.next();
						while( next.length && next.menuItemDepth() > depth ) {
							result = result.add( next );
							next = next.next();
						}
					});
					return result;
				},
				updateParentMenuItemDBId : function() {
					return this.each(function(){
						var item = $(this),
							input = item.find('.menu-item-data-parent-id'),
							depth = item.menuItemDepth(),
							parent = item.prev();

						if( depth == 0 ) { // Item is on the top level, has no parent
							input.val(0);
						} else { // Find the parent item, and retrieve its object id.
							while( ! parent[0] || ! parent[0].className || -1 == parent[0].className.indexOf('menu-item') || ( parent.menuItemDepth() != depth - 1 ) )
								parent = parent.prev();
							input.val( parent.find('.menu-item-data-db-id').val() );
						}
					});
				},
				/**
				 * Adds selected menu items to the menu.
				 *
				 * @param jQuery metabox The metabox jQuery object.
				 */
				addSelectedToMenu : function(processMethod) {
					if ( 0 == $('#menu-to-edit').length ) {
						return false;
					}

					return this.each(function() {
						var t = $(this), menuItems = {},
							checkboxes = t.find('.tabs-panel-active .categorychecklist li input:checked'),
							re = new RegExp('menu-item\\[(\[^\\]\]*)');

						processMethod = processMethod || api.addMenuItemToBottom;

						// If no items are checked, bail.
						if ( !checkboxes.length )
							return false;

						// Show the ajax spinner
						t.find('img.waiting').show();

						// Retrieve menu item data
						$(checkboxes).each(function(){
							var t = $(this),
								listItemDBIDMatch = re.exec( t.attr('name') ),
								listItemDBID = 'undefined' == typeof listItemDBIDMatch[1] ? 0 : parseInt(listItemDBIDMatch[1], 10);
							if ( this.className && -1 != this.className.indexOf('add-to-top') )
							{
								processMethod = api.addMenuItemToTop;
							}
							menuItems[listItemDBID] = t.closest('li').getItemData( 'add-menu-item', listItemDBID );
						});

						// Add the items
						api.addItemToMenu(menuItems, processMethod, function(){
							// Deselect the items and hide the ajax spinner
							checkboxes.removeAttr('checked');
							t.find('img.waiting').hide();
						});
					});
				},
				getItemData : function( itemType, id ) {
					itemType = itemType || 'menu-item';

					var itemData = {}, i,
					fields = [
						'menu-item-db-id',
						'menu-item-object-id',
						'menu-item-object',
						'menu-item-parent-id',
						'menu-item-position',
						'menu-item-type',
						'menu-item-title',
						'menu-item-url',
						'menu-item-description',
						'menu-item-attr-title',
						'menu-item-target',
						'menu-item-classes',
						'menu-item-xfn'
					];

					if( !id && itemType == 'menu-item' ) {
						id = this.find('.menu-item-data-db-id').val();
					}

					if( !id ) return itemData;

					this.find('input').each(function() {
						var field;
						i = fields.length;
						while ( i-- ) {
							if( itemType == 'menu-item' )
							{
								field = fields[i] + '[' + id + ']';
							}
							else if( itemType == 'add-menu-item' )
							{
								field = 'menu-item[' + id + '][' + fields[i] + ']';
							}

							if (
								this.name &&
								field == this.name
							) {
								itemData[fields[i]] = this.value;
							}
						}
					});

					return itemData;
				},
				setItemData : function( itemData, itemType, id ) { // Can take a type, such as 'menu-item', or an id.
					itemType = itemType || 'menu-item';

					if( !id && itemType == 'menu-item' ) {
						id = $('.menu-item-data-db-id', this).val();
					}

					if( !id ) return this;

					this.find('input').each(function() {
						var t = $(this), field;
						$.each( itemData, function( attr, val ) {
							if( itemType == 'menu-item' )
								field = attr + '[' + id + ']';
							else if( itemType == 'add-menu-item' )
								field = 'menu-item[' + id + '][' + attr + ']';

							if ( field == t.attr('name') ) {
								t.val( val );
							}
						});
					});
					return this;
				}
			});
		},

		initToggles : function() {return;
			// init postboxes
			postboxes.add_postbox_toggles('nav-menus');

			// adjust columns functions for menus UI
			columns.useCheckboxesForHidden();
			columns.checked = function(field) {
				$('.field-' + field).removeClass('hidden-field');
			}
			columns.unchecked = function(field) {
				$('.field-' + field).addClass('hidden-field');
			}
		},

		attachMenuEditListeners : function() {
			var that = this;
			$('#update-nav-menu').bind('click', function(e) {
				if ( e.target && e.target.className ) {
					if ( -1 != e.target.className.indexOf('item-edit') ) {
						return that.eventOnClickEditLink(e.target);
					} else if ( -1 != e.target.className.indexOf('menu-save') ) {
						return that.eventOnClickMenuSave(e.target);
					} else if ( -1 != e.target.className.indexOf('menu-delete') ) {
						return that.eventOnClickMenuDelete(e.target);
					} else if ( -1 != e.target.className.indexOf('item-delete') ) {
						return that.eventOnClickMenuItemDelete(e.target);
					} else if ( -1 != e.target.className.indexOf('item-cancel') ) {
						return that.eventOnClickCancelLink(e.target);
					}
				}
			});
		},

		/**
		 * An interface for managing default values for input elements
		 * that is both JS and accessibility-friendly.
		 *
		 * Input elements that add the class 'input-with-default-title'
		 * will have their values set to the provided HTML title when empty.
		 */
		setupInputWithDefaultTitle : function() {
			var name = 'input-with-default-title';

			$('.' + name).each( function(){
				var $t = $(this), title = $t.attr('title'), val = $t.val();
				$t.data( name, title );

				if( '' == val ) $t.val( title );
				else if ( title == val ) return;
				else $t.removeClass( name );
			}).focus( function(){
				var $t = $(this);
				if( $t.val() == $t.data(name) )
					$t.val('').removeClass( name );
			}).blur( function(){
				var $t = $(this);
				if( '' == $t.val() )
					$t.addClass( name ).val( $t.data(name) );
			});
		},

		attachThemeLocationsListeners : function() {
			var loc = $('#nav-menu-theme-locations'), params = {};
			params['action'] = 'menu-locations-save';
			params['menu-settings-column-nonce'] = $('#menu-settings-column-nonce').val();
			loc.find('input[type=submit]').css('display','none');
			
			loc.find('input[type=submit]').click(function() {
				loc.find('select').each(function() {
					params[this.name] = $(this).val();
				});
				loc.find('.waiting').show();
				$.post( ajaxurl, params, function(r) {
					loc.find('.waiting').hide();
				});
				return false;
			});
			
		},

		attachQuickSearchListeners : function() {
			var searchTimer;

			$('.quick-search').keypress(function(e){
				var t = $(this);

				if( 13 == e.which ) {
					api.updateQuickSearchResults( t );
					return false;
				}

				if( searchTimer ) clearTimeout(searchTimer);

				searchTimer = setTimeout(function(){
					api.updateQuickSearchResults( t );
				}, 400);
			}).attr('autocomplete','off');
		},

		updateQuickSearchResults : function(input) {
			var panel, params,
			minSearchLength = 2,
			q = input.val();

			if( q.length < minSearchLength ) return;

			panel = input.parents('.tabs-panel');
			params = {
				'action': 'menu-quick-search',
				'response-format': 'markup',
				'menu': $('#menu').val(),
				'menu-settings-column-nonce': $('#menu-settings-column-nonce').val(),
				'q': q,
				'type': input.attr('name')
			};

			$('img.waiting', panel).show();

			$.post( ajaxurl, params, function(menuMarkup) {
				api.processQuickSearchQueryResponse(menuMarkup, params, panel);
			});
		},

		addCustomLink : function( processMethod ) {
			var url = $('#custom-menu-item-url').val(),
				label = $('#custom-menu-item-name').val();

			processMethod = processMethod || api.addMenuItemToBottom;

			if ( '' == url || 'http://' == url )
				return false;

			// Show the ajax spinner
			$('.customlinkdiv img.waiting').show();
			this.addLinkToMenu( url, label, processMethod, function() {
				// Remove the ajax spinner
				$('.customlinkdiv img.waiting').hide();
				// Set custom link form back to defaults
				$('#custom-menu-item-name').val('').blur();
				$('#custom-menu-item-url').val('http://');
			});
		},

		addLinkToMenu : function(url, label, processMethod, callback) {
			processMethod = processMethod || api.addMenuItemToBottom;
			callback = callback || function(){};

			api.addItemToMenu({
				'-1': {
					'menu-item-type': 'custom',
					'menu-item-url': url,
					'menu-item-title': label
				}
			}, processMethod, callback);
		},

		addItemToMenu : function(menuItem, processMethod, callback) {
			var menu = $('#menu').val();
			var inputs = jQuery('#menu-management-liquid').find(':input');
			
			processMethod = processMethod || function(){};
			callback = callback || function(){};
			
			params = {
				'menu': menu,
				'menu-item': menuItem,
				'widget-id': $('[name=widget-id]').val()
			};
			
			ajaxurl = userSettings.url+'?view=twc-menu-item';
			
			$.post( ajaxurl, params, function(menuMarkup) {
				var ins = $('#menu-instructions');
				processMethod(menuMarkup, params);
				if( ! ins.hasClass('menu-instructions-inactive') && ins.siblings().length )
					ins.addClass('menu-instructions-inactive');
				callback();
			});
		},

		/**
		 * Process the add menu item request response into menu list item.
		 *
		 * @param string menuMarkup The text server response of menu item markup.
		 * @param object req The request arguments.
		 */
		addMenuItemToBottom : function( menuMarkup, req ) {
			$(menuMarkup).appendTo( api.targetList );
		},

		addMenuItemToTop : function( menuMarkup, req ) {
			$(menuMarkup).prependTo( api.targetList );
		},

		attachUnsavedChangesListener : function() {
			$('#menu-management input, #menu-management select, #menu-management, #menu-management textarea').change(function(){
				api.registerChange();
			});
		},

		registerChange : function() {
			api.menusChanged = true;
		},

		attachTabsPanelListeners : function() {
			$('#menu-settings-column').bind('click', function(e) {
				var selectAreaMatch, panelId, wrapper, items,
					target = $(e.target);

				if ( target.hasClass('nav-tab-link') ) {
					panelId = /#(.*)$/.exec(e.target.href);
					if ( panelId && panelId[1] )
						panelId = panelId[1]
					else
						return false;

					wrapper = target.parents('.inside');
					
					// upon changing tabs, we want to uncheck all checkboxes
					//$('input', wrapper).removeAttr('checked');

					$('.tabs-panel-active', wrapper).removeClass('tabs-panel-active').addClass('tabs-panel-inactive');
					$('#' + panelId, wrapper).removeClass('tabs-panel-inactive').addClass('tabs-panel-active');

					$('.tabs', wrapper).removeClass('tabs');
					target.parent().addClass('tabs');

					// select the search bar
					$('.quick-search', wrapper).focus();

					return false;
				} else if ( target.hasClass('select-all') ) {
					selectAreaMatch = /#(.*)$/.exec(e.target.href);
					if ( selectAreaMatch && selectAreaMatch[1] ) {
						items = $('#' + selectAreaMatch[1] + ' .tabs-panel-active .menu-item-title input');
						if( items.length === items.filter(':checked').length )
						{
							//items.removeAttr('checked');
						}
						else
							items.attr('checked', 'checked');
						return false;
					}
				} else if ( target.hasClass('submit-add-to-menu') ) {
					api.registerChange();

					if ( e.target.id && 'submit-customlinkdiv' == e.target.id )
						api.addCustomLink( api.addMenuItemToBottom );
					else if ( e.target.id && -1 != e.target.id.indexOf('submit-') )
						$('#' + e.target.id.replace(/submit-/, '')).addSelectedToMenu( api.addMenuItemToBottom );
					return false;
				} else if ( target.hasClass('page-numbers') ) {
					$.post( ajaxurl, e.target.href.replace(/.*\?/, '').replace(/action=([^&]*)/, '') + '&action=menu-get-metabox',
						function( resp ) {
							if ( -1 == resp.indexOf('replace-id') )
								return;

							var metaBoxData = $.parseJSON(resp),
							toReplace = document.getElementById(metaBoxData['replace-id']),
							placeholder = document.createElement('div'),
							wrap = document.createElement('div');

							if ( ! metaBoxData['markup'] || ! toReplace )
								return;

							wrap.innerHTML = metaBoxData['markup'] ? metaBoxData['markup'] : '';

							toReplace.parentNode.insertBefore( placeholder, toReplace );
							placeholder.parentNode.removeChild( toReplace );

							placeholder.parentNode.insertBefore( wrap, placeholder );

							placeholder.parentNode.removeChild( placeholder );

						}
					);

					return false;
				}
			});
		},

		initTabManager : function() {
			var fixed = $('.nav-tabs-wrapper'),
				fluid = fixed.children('.nav-tabs'),
				active = fluid.children('.nav-tab-active'),
				tabs = fluid.children('.nav-tab'),
				tabsWidth = 0,
				fixedRight, fixedLeft,
				arrowLeft, arrowRight, resizeTimer, css = {},
				marginFluid = api.isRTL ? 'margin-right' : 'margin-left',
				marginFixed = api.isRTL ? 'margin-left' : 'margin-right',
				msPerPx = 2;

			/**
			 * Refreshes the menu tabs.
			 * Will show and hide arrows where necessary.
			 * Scrolls to the active tab by default.
			 *
			 * @param savePosition {boolean} Optional. Prevents scrolling so
			 * 		  that the current position is maintained. Default false.
			 **/
			api.refreshMenuTabs = function( savePosition ) {
				return;
				var fixedWidth = fixed.width(),
					margin = 0, css = {};
				fixedLeft = fixed.offset().left;
				fixedRight = fixedLeft + fixedWidth;

				if( !savePosition )
					active.makeTabVisible();

				// Prevent space from building up next to the last tab if there's more to show
				if( tabs.last().isTabVisible() ) {
					margin = fixed.width() - tabsWidth;
					margin = margin > 0 ? 0 : margin;
					css[marginFluid] = margin + 'px';
					fluid.animate( css, 100, "linear" );
				}

				// Show the arrows only when necessary
				if( fixedWidth > tabsWidth )
					arrowLeft.add( arrowRight ).hide();
				else
					arrowLeft.add( arrowRight ).show();
			}

			$.fn.extend({
				makeTabVisible : function() {
					var t = this.eq(0), left, right, css = {}, shift = 0;

					if( ! t.length ) return this;

					left = t.offset().left;
					right = left + t.outerWidth();

					if( right > fixedRight )
						shift = fixedRight - right;
					else if ( left < fixedLeft )
						shift = fixedLeft - left;

					if( ! shift ) return this;

					css[marginFluid] = "+=" + api.negateIfRTL * shift + 'px';
					fluid.animate( css, Math.abs( shift ) * msPerPx, "linear" );
					return this;
				},
				isTabVisible : function() {
					var t = this.eq(0),
						left = t.offset().left,
						right = left + t.outerWidth();
					return ( right <= fixedRight && left >= fixedLeft ) ? true : false;
				}
			});

			// Find the width of all tabs
			tabs.each(function(){
				tabsWidth += $(this).outerWidth(true);
			});

			// Set up fixed margin for overflow, unset padding
			css['padding'] = 0;
			css[marginFixed] = (-1 * tabsWidth) + 'px';
			fluid.css( css );

			// Build tab navigation
			arrowLeft = $('<div class="nav-tabs-arrow nav-tabs-arrow-left"><a>&laquo;</a></div>');
			arrowRight = $('<div class="nav-tabs-arrow nav-tabs-arrow-right"><a>&raquo;</a></div>');
			// Attach to the document
			fixed.wrap('<div class="nav-tabs-nav"/>').parent().prepend( arrowLeft ).append( arrowRight );

			// Set the menu tabs
			api.refreshMenuTabs();
			// Make sure the tabs reset on resize
			$(window).resize(function() {
				if( resizeTimer ) clearTimeout(resizeTimer);
				resizeTimer = setTimeout( api.refreshMenuTabs, 200);
			});

			// Build arrow functions
			$.each([{
					arrow : arrowLeft,
					next : "next",
					last : "first",
					operator : "+="
				},{
					arrow : arrowRight,
					next : "prev",
					last : "last",
					operator : "-="
				}], function(){
				var that = this;
				this.arrow.mousedown(function(){
					var marginFluidVal = Math.abs( parseInt( fluid.css(marginFluid) ) ),
						shift = marginFluidVal,
						css = {};

					if( "-=" == that.operator )
						shift = Math.abs( tabsWidth - fixed.width() ) - marginFluidVal;

					if( ! shift ) return;

					css[marginFluid] = that.operator + shift + 'px';
					fluid.animate( css, shift * msPerPx, "linear" );
				}).mouseup(function(){
					var tab, next;
					fluid.stop(true);
					tab = tabs[that.last]();
					while( (next = tab[that.next]()) && next.length && ! next.isTabVisible() ) {
						tab = next;
					}
					tab.makeTabVisible();
				});
			});
		},

		eventOnClickEditLink : function(clickedEl) {
			var settings, item,
			matchedSection = /#(.*)$/.exec(clickedEl.href);
			if ( matchedSection && matchedSection[1] ) {
				settings = $('#'+matchedSection[1]);
				item = settings.parent();
				if( 0 != item.length ) {
					if( item.hasClass('menu-item-edit-inactive') ) {
						if( ! settings.data('menu-item-data') ) {
							settings.data( 'menu-item-data', settings.getItemData() );
						}
						settings.slideDown('fast');
						item.removeClass('menu-item-edit-inactive')
							.addClass('menu-item-edit-active');
					} else {
						settings.slideUp('fast');
						item.removeClass('menu-item-edit-active')
							.addClass('menu-item-edit-inactive');
					}
					return false;
				}
			}
		},

		eventOnClickCancelLink : function(clickedEl) {
			var settings = $(clickedEl).closest('.menu-item-settings');
			settings.setItemData( settings.data('menu-item-data') );
			return false;
		},

		eventOnClickMenuSave : function(clickedEl) {
			var locs = '',
			menuName = $('#menu-name'),
			menuNameVal = menuName.val();
			// Cancel and warn if invalid menu name
			if( !menuNameVal || menuNameVal == menuName.attr('title') || !menuNameVal.replace(/\s+/, '') ) {
				menuName.parent().addClass('form-invalid');
				return false;
			}
			// Copy menu theme locations
			$('#nav-menu-theme-locations select').each(function() {
				locs += '<input type="hidden" name="' + this.name + '" value="' + $(this).val() + '" />';
			});
			$('#update-nav-menu').append( locs );
			// Update menu item position data
			api.menuList.find('.menu-item-data-position').val( function(index) { return index + 1; } );
			window.onbeforeunload = null;

			return true;
		},

		eventOnClickMenuDelete : function(clickedEl) {
			// Delete warning AYS
			if ( confirm( navMenuL10n.warnDeleteMenu ) ) {
				window.onbeforeunload = null;
				return true;
			}
			return false;
		},

		eventOnClickMenuItemDelete : function(clickedEl) {
			var itemID = parseInt(clickedEl.id.replace('delete-', ''), 10);
			api.removeMenuItem( $('#menu-item-' + itemID) );
			api.registerChange();
			return false;
		},

		/**
		 * Process the quick search response into a search result
		 *
		 * @param string resp The server response to the query.
		 * @param object req The request arguments.
		 * @param jQuery panel The tabs panel we're searching in.
		 */
		processQuickSearchQueryResponse : function(resp, req, panel) {
			var i, matched, newID,
			takenIDs = {},
			form = document.getElementById('twc-widget-wrap'),
			pattern = new RegExp('menu-item\\[(\[^\\]\]*)', 'g'),
			items = resp.match(/<li>.*<\/li>/g);

			if( ! items ) {
				$('.categorychecklist', panel).html( '<li><p>No Results Found.</p></li>' );
				$('img.waiting', panel).hide();
				return;
			}

			i = items.length;
			while( i-- ) {
				// make a unique DB ID number
				matched = pattern.exec(items[i]);
				if ( matched && matched[1] ) {
					newID = matched[1];
					while( form.elements['menu-item[' + newID + '][menu-item-type]'] || takenIDs[ newID ] ) {
						newID--;
					}

					takenIDs[newID] = true;
					if ( newID != matched[1] ) {
						items[i] = items[i].replace(new RegExp('menu-item\\[' + matched[1] + '\\]', 'g'), 'menu-item[' + newID + ']');
					}
				}
			}

			$('.categorychecklist', panel).html( items.join('') );
			$('img.waiting', panel).hide();
			
			that = this;
			$('.categorychecklist').find('[type=checkbox]').each(function(){
				jQuery(this).click(function(){
					that.twcOnCheckBoxClick(this);
				});
			});
			twc_ongoing_selection();
		},

		removeMenuItem : function(el) {
			var children = el.childMenuItems();

			el.addClass('deleting').animate({
					opacity : 0,
					height: 0
				}, 350, function() {
					var ins = $('#menu-instructions');
					el.remove();
					children.shiftDepthClass(-1).updateParentMenuItemDBId();
					if( ! ins.siblings().length )
						ins.removeClass('menu-instructions-inactive');
				});
		},

		depthToPx : function(depth) {
			return depth * api.options.menuItemDepthPerLevel;
		},

		pxToDepth : function(px) {
			return Math.floor(px / api.options.menuItemDepthPerLevel);
		}

	};

	$(document).ready(function(){ wpNavMenu.init(); });

})(jQuery);
