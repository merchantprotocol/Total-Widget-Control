<?php 
/**
 * @Author	Jonathon byrd
 * @link http://www.jonathonbyrd.com
 * @Package Wordpress
 * @SubPackage Total Widget Control
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @Since 1.0.0
 * 
 * 
 * Plugin Name: Total Widget Control
 * Plugin URI: http://www.5twentystudios.com
 * Description: <a href="http://www.totalwidgetcontrol.com" target="_blank">Total Widget Control</a> is a plugin for WordPress that allows administrators to display widgets when, where and to whom they want. Additionally, TWC makes theme development a breeze by providing template codes for managing and creating widgets and sidebars. Visit the dedicated community at <a href="http://www.totalwidgetcontrol.com" target="_blank">www.totalwidgetcontrol.com</a>
 * Version: 1.6.16
 * Author: 5Twenty Studios
 * Author URI: http://www.5twentystudios.com
 * 
 * 
 */

defined('ABSPATH') or die("Cannot access pages directly.");

/**
 * Initializing 
 * 
 * The directory separator is different between linux and microsoft servers.
 * Thankfully php sets the DIRECTORY_SEPARATOR constant so that we know what
 * to use.
 */
defined("TWC_AFFILIATE_ID") or define("TWC_AFFILIATE_ID", '1');

/**
 * Initializing 
 * 
 * The directory separator is different between linux and microsoft servers.
 * Thankfully php sets the DIRECTORY_SEPARATOR constant so that we know what
 * to use.
 */
defined("DS") or define("DS", DIRECTORY_SEPARATOR);

/**
 * Initializing 
 * 
 * The directory separator is different between linux and microsoft servers.
 * Thankfully php sets the DIRECTORY_SEPARATOR constant so that we know what
 * to use.
 */
defined("TWC_VERSION") or define("TWC_VERSION", '1.6.16');

/**
 * Initialize Localization
 * 
 * @tutorial http://codex.wordpress.org/I18n_for_WordPress_Developers
 * function call loads the localization files from the current folder
 */
if (function_exists('load_theme_textdomain')) load_theme_textdomain('twc');

/**
 * Set Dates Default Timezone
 * 
 * The server has a timezone, mysql has a timezone, php has a timezone and wordpress 
 * it's own timezone. The following setting will synchronize the wordpress timezone
 * with the php timezone. This program uses the php timezone for publishing settings.
 */
if (function_exists('date_default_timezone_set')) date_default_timezone_set(get_site_option('timezone_string', 'UTC'));
if (function_exists('ini_set')) ini_set('date.timezone', get_site_option('timezone_string', 'UTC'));

/**
 * Lite License
 * 
 * This is the lite license that can be used on any domain.
 */
defined("TWC_LITE_LICENSE") or define("TWC_LITE_LICENSE", 'aWYgKGZ1bmN0aW9uX2V4aXN0cygidHdjX2Vycm9yX2xvZyIpICYmIGdldF9vcHRpb24oInR3Y19yZWNlaXZlZF9hX2xpY2Vuc2UiLCBmYWxzZSkpCnsKCXVwZGF0ZV9vcHRpb24oJ3R3Y19yZWNlaXZlZF9hX2xpY2Vuc2UnLCBmYWxzZSk7Cgl0d2NfZXJyb3JfbG9nKCJUaGUgbGljZW5zZSBoYXMgYmVlbiBsb2FkZWQgZmluZS48aHI+Iik7Cn0KJEdMT0JBTFNbIlRXQ0FVVEgiXSA9IGZhbHNlOwpyZW1vdmVfYWxsX2FjdGlvbnMoInR3Y19kaXNwbGF5X2FkbWluIik7CmFkZF9hY3Rpb24oImluaXQiLCAidHdjX3dpZGdldF9nbG9iYWwiKTsKYWRkX2FjdGlvbigndHdjX2luaXQnLCAndHdjX2J1bGtfYWN0aW9ucycsIDQpOwphZGRfYWN0aW9uKCJ0d2NfaW5pdCIsICJ0d2NfY2xlYXIiLCA1KTsKYWRkX2FjdGlvbigidHdjX2luaXQiLCAidHdjX3JlZ2lzdGVyX3BsYWNlaG9sZGVyX3NpZGViYXIiLCAxMCk7CmFkZF9hY3Rpb24oInR3Y19pbml0IiwgInR3Y19hZGRfaGVscF90ZXh0IiwgMTgpOwphZGRfYWN0aW9uKCJ0d2NfZGlzcGxheV9hZG1pbiIsICJ0d2NfY29udHJvbGxlciIsIDIwKTsKYWRkX2FjdGlvbigidHdjX3dpZGdldF93cmFwX2gyIiwgInR3Y19oMl9hZGRfbmV3IiwgOCk7CmFkZF9hY3Rpb24oInR3Yy10YWJsZSIsICJ0d2Nfdmlld190YWJsZSIsIDIwKTsKYWRkX2FjdGlvbigidHdjLW5vLXNpZGViYXJzIiwgInR3Y192aWV3X25vX3NpZGViYXJzIiwgMjApOwphZGRfYWN0aW9uKCJ0d2MtaW5hY3RpdmUiLCAidHdjX3ZpZXdfaW5hY3RpdmUiLCAyMCk7CmFkZF9hY3Rpb24oInR3Yy1hZGQiLCAidHdjX3ZpZXdfYWRkIiwgMjApOwphZGRfYWN0aW9uKCJ0d2MtZWRpdCIsICJ0d2Nfdmlld19lZGl0IiwgMjApOwphZGRfYWN0aW9uKCJ0d2Nfd2lkZ2V0X21ldGFib3giLCAidHdjX3dpZGdldF9lZGl0X2Zvcm0iLCAyMCk7CmFkZF9hY3Rpb24oInR3Y193aWRnZXRfbWV0YWJveCIsICJ0d2Nfd2lkZ2V0X21ldGFfd3JhcHBlciIsIDI1KTsKYWRkX2FjdGlvbigidHdjX25hdl9tZW51X2xpc3QiLCAidHdjX3dpZGdldF9tZXRhX25hdm1lbnUiKTsKYWRkX2FjdGlvbigid3AiLCAidHdjX3NldF9vYmplY3RfaWQiLCAxLCAxKTsKYWRkX2FjdGlvbigid2lkZ2V0X2Rpc3BsYXlfY2FsbGJhY2siLCAidHdjX2Rpc3BsYXlfdGhlX3dpZGdldCIsIDEwMDAsIDMpOwphZGRfYWN0aW9uKCJkeW5hbWljX3NpZGViYXIiLCAidHdjX3RyaWdnZXJfc2lkZWJhciIsIDIwLCAxKTsKYWRkX2FjdGlvbigidHdjX2R5bmFtaWNfc2lkZWJhciIsICJ0d2NfZHluYW1pY19zaWRlYmFyIiwgMjAsIDEpOwphZGRfYWN0aW9uKCJ0d2NfZHluYW1pY19zaWRlYmFyIiwgInR3Y19kZWZhdWx0X3NpZGViYXIiLCAyNSwgMSk7CmFkZF9hY3Rpb24oJ3R3Y19uYXZfbWVudV9saXN0JywgJ3R3Y192aWV3X2VkaXRfc3VibWl0JywgMSk7CmFkZF9hY3Rpb24oJ3R3Y193aWRnZXRfbWV0YWJveCcsICd0d2Nfdmlld19wcm9fZWRpdCcsIDMwKTsKCmFkZF9maWx0ZXIoIndpZGdldF91cGRhdGVfY2FsbGJhY2siLCAidHdjX3NhdmVfbWVudV9pdGVtcyIsIDIwLCAxKTsKYWRkX2ZpbHRlcigid2lkZ2V0X3VwZGF0ZV9jYWxsYmFjayIsICJ0d2Nfc2F2ZV9kZWZhdWx0X3NpZGViYXIiLCAyMCwgNCk7CmFkZF9maWx0ZXIoImR5bmFtaWNfc2lkZWJhcl9wYXJhbXMiLCAidHdjX2Rpc3BsYXlfdGhlX3NpZGViYXIiKTsKCmZ1bmN0aW9uIHR3Y192aWV3X3Byb19lZGl0KCl7IHR3Y19zaG93X3ZpZXcoJ3R3Yy1lZGl0LXBybycpOyB9CmZ1bmN0aW9uIHR3Y192aWV3X2VkaXRfc3VibWl0KCl7IHR3Y19zaG93X3ZpZXcoJ3R3Yy1lZGl0LXN1Ym1pdCcpOyB9CmZ1bmN0aW9uIHR3Y192aWV3X3RhYmxlKCl7IHR3Y19zaG93X3ZpZXcoInR3Yy10YWJsZSIpOyB9CmZ1bmN0aW9uIHR3Y192aWV3X25vX3NpZGViYXJzKCl7IHR3Y19zaG93X3ZpZXcoInR3Yy1uby1zaWRlYmFycyIpOyB9CmZ1bmN0aW9uIHR3Y192aWV3X2luYWN0aXZlKCl7IHR3Y19zaG93X3ZpZXcoInR3Yy1pbmFjdGl2ZSIpOyB9CmZ1bmN0aW9uIHR3Y192aWV3X2FkZCgpeyB0d2Nfc2hvd192aWV3KCJ0d2MtYWRkIik7IH0KZnVuY3Rpb24gdHdjX3ZpZXdfZWRpdCgpeyB0d2Nfc2hvd192aWV3KCJ0d2MtZWRpdCIpOyB9CmZ1bmN0aW9uIHR3Y193aWRnZXRfZWRpdF9mb3JtKCl7IHR3Y19zaG93X3ZpZXcoInR3Yy1lZGl0LWZvcm0iKTsgfQpmdW5jdGlvbiB0d2Nfd2lkZ2V0X21ldGFfd3JhcHBlcigpeyB0d2Nfc2hvd192aWV3KCJ0d2MtbWV0YS13cmFwcGVyIik7IH0KZnVuY3Rpb24gdHdjX3dpZGdldF9tZXRhX25hdm1lbnUoKXsgdHdjX3Nob3dfdmlldygidHdjLW1ldGEtbmF2LW1lbnVzIik7IH0KCmFkZF9maWx0ZXIoJ3R3Y193aWRnZXRfZGlzcGxheScsICd0d2NwX3dpZGdldF93cmFwcGVyJywgMTAwLCAyKTsKYWRkX2ZpbHRlcigidHdjX2Rpc3BsYXlfd2lkZ2V0IiwgInR3Y19kaXNwbGF5X2lmX2V4Y2x1ZGVkIiwgMjAsIDIpOwphZGRfZmlsdGVyKCd0d2NfZGlzcGxheV93aWRnZXQnLCAndHdjX2Rpc3BsYXlfaWZfZGVmYXVsdCcsIDEwMCwgMik7CmFkZF9maWx0ZXIoJ3R3Y19kaXNwbGF5X3dpZGdldCcsICd0d2NfZGlzcGxheV9pZl92aXNpYmxpdHknLCAxMzAsIDIpOwphZGRfZmlsdGVyKCd0d2NfZGlzcGxheV93aWRnZXQnLCAndHdjX2Rpc3BsYXlfaWZfdGltZXN0YW1wJywgMjAwLCAyKTsKYWRkX2ZpbHRlcigndHdjX2Rpc3BsYXlfd2lkZ2V0JywgJ3R3Y19kaXNwbGF5X2lmX3N0YXR1cycsIDUwMCwgMik7CmFkZF9maWx0ZXIoInR3Y193aWRnZXRfdGl0bGUiLCAidHdjX3dpZGdldF9wcm90aXRsZSIsIDIwLCAyKTsKCmZ1bmN0aW9uIHR3Y193aWRnZXRfcHJvdGl0bGUoJHRpdGxlLCAkd2lkZ2V0KQp7CgkkaXNwcm8gPSAoaXNzZXQoJHdpZGdldFsncCddWyd0d2NwX3dpZGdldF90aXRsZSddKSAmJiBzdHJsZW4odHJpbSgkd2lkZ2V0WydwJ11bJ3R3Y3Bfd2lkZ2V0X3RpdGxlJ10pKSk7CglpZiAoJGlzcHJvKSByZXR1cm4gJHdpZGdldFsncCddWyd0d2NwX3dpZGdldF90aXRsZSddOwoJcmV0dXJuICR0aXRsZTsKfQo=');

/**
 * User Control Level
 * 
 * Allows the developer to hook into this system and set the access level for this plugin.
 * If the user does not have the capability to view this plguin, they may still be
 * able to view the default widget area. This will not cause problems with the script,
 * however the editing user will not be able to add or delete viewable pages to the 
 * widget.
 * 
 * @TODO need to set this to call get_option from the db
 */
defined("TWC_ACCESS_CAPABILITY") or define("TWC_ACCESS_CAPABILITY", "edit_theme_options" );

/**
 * Are Sortables Turned On
 * 
 * You probably shouldn't turn on this constant at all, it's still very much in
 * the development stage.
 */
defined("TWC_SORTABLES") or define("TWC_SORTABLES", FALSE);

/**
 * Startup
 * 
 * This block of functions is only preloading a set of functions that I've prebuilt
 * and that I use throughout my websites.
 * 
 * @TODO Need to test this system while it's using the bootstrap file, currently it's being 
 * overridden by the 520 plugin
 * 
 * @copyright Proprietary Software, Copyright Byrd Incorporated. All Rights Reserved
 * @since 1.0
 */
require_once ABSPATH.WPINC.DS."pluggable.php";
require_once dirname(__file__).DS."bootstrap.php";
require_once dirname(__file__).DS."total-widget-control.php";
require_once dirname(__file__).DS."template-codes.php";
require_once dirname(__file__).DS."widgets.php";

/**
 * Initialize the Framework
 * 
 */
set_controller_path( dirname( __FILE__ ) );
require_once dirname(__file__).DS."auth.php";
twc_initialize();

/**
 * Initializing 
 * 
 * The directory separator is different between linux and microsoft servers.
 * Thankfully php sets the DIRECTORY_SEPARATOR constant so that we know what
 * to use.
 */
defined("TWC_LICENSE") or define("TWC_LICENSE", 'lite');

