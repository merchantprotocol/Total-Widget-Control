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
 * Plugin Name: Total Widget Control
 * Plugin URI: http://www.5twentystudios.com
 * Description: This plugin is designed to revolutionize the widget control system within Wordpress 3.0+. The goal here is to learn from the Joomla module control system and implement their design into WordPress. <a href="http://www.jonathonbyrd.com" target="_blank">Author Website</a>
 * Version: 1.5.17
 * Author: 5Twenty Studios
 * Author URI: http://www.5twentystudios.com
 * 
 * 
 * 
 * @TODO I need to clean up the twc-nav-menu javascript to only include the js that I need
 * @TODO I need to build in my own set of default widgets and default wrappers
 * @TODO I need to activate shortcodes for widgets
 * @TODO I need to activate shortcodes for sidebars
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
defined("DS") or define("DS", DIRECTORY_SEPARATOR);

/**
 * Initializing 
 * 
 * The directory separator is different between linux and microsoft servers.
 * Thankfully php sets the DIRECTORY_SEPARATOR constant so that we know what
 * to use.
 */
defined("TWC_VERSION") or define("TWC_VERSION", '1.5.17');

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
if (function_exists('date_default_timezone_set')) date_default_timezone_set(get_site_option('timezone_string'));
if (function_exists('ini_set')) ini_set('date.timezone', get_site_option('timezone_string'));

/**
 * Lite License
 * 
 * This is the lite license that can be used on any domain.
 */
defined("TWC_LITE_LICENSE") or define("TWC_LITE_LICENSE", 'IGNsYXNzIFpqSXdYMlp2ZFhKMGVRIHsgcHJvdGVjdGVkICRTMlY1Y3cgPSBhcnJheSgncHJpdmF0ZSc9PicnLCd4ZmFjdG9yJz0+JycsJ3lmYWN0b3InPT4nJyk7IHByb3RlY3RlZCAkVEc5amEzTSA9IGFycmF5KCk7IHByb3RlY3RlZCBmdW5jdGlvbiAmUjJWMFMyVjUoJGJHOWphMVI1Y0dVKXsgcmV0dXJuICR0aGlzLT5TMlY1Y3dbJGJHOWphMVI1Y0dVXTsgfSBwcm90ZWN0ZWQgZnVuY3Rpb24gU1c1elpYSjBTMlY1Y3coKXsgJHRoaXMtPlVtVnRiM1psUzJWNSgpOyAkdGhpcy0+VW1WelpYUk1iMk5yKCk7IGZvcmVhY2ggKCR0aGlzLT5TMlY1Y3cgYXMgJFMyVjVWSGx3WlEgPT4gJFMyVjUpeyBpZiAoc3Ryc3RyKCRTMlY1Vkhsd1pRLCAnZmFjdG9yJykpeyAkUzJWNSA9IG1kNShzZXJpYWxpemUoJHRoaXMtPlMyVjVjdykpOyB9IGVsc2UgeyAkUzJWNSA9ICdkZXYuNXR3ZW50eXN0dWRpb3MuY29tJzsgfSAkdGhpcy0+U1c1elpYSjBTMlY1KCRTMlY1LCAkUzJWNVZIbHdaUSk7IH0gfSBwcm90ZWN0ZWQgZnVuY3Rpb24gU1c1elpYSjBTMlY1KCRhMlY1LCAkYkc5amExUjVjR1UpeyBpZiAoc3RybGVuKCRhMlY1KSA+IDApeyAkdGhpcy0+UzJWNWN3WyRiRzlqYTFSNWNHVV0gPSAkYTJWNTsgfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBWSFZ5Ymt0bGVRKCRiRzlqYTFSNWNHVSA9ICcnKXsgaWYgKCEkYkc5amExUjVjR1UpeyBmb3JlYWNoICgkdGhpcy0+VEc5amEzTSBhcyAkVEc5amExUjVjR1UgPT4gJFRHOWphdyl7ICR0aGlzLT5WSFZ5Ymt0bGVRKCRURzlqYTFSNWNHVSk7IH0gcmV0dXJuOyB9ICRTMlY1ID0mICR0aGlzLT5SMlYwUzJWNSgkYkc5amExUjVjR1UpOyBmb3IgKCRhUSA9IDA7ICRhUSA8IHN0cmxlbigkUzJWNSk7ICRhUSsrKXsgJFUzUmxjSE0gPSBvcmQoJFMyVjVbJGFRXSkgLyAoJGFRICsgMSk7IGlmIChvcmQoJFMyVjVbJGFRXSkgJSAyICE9IDApeyAkdGhpcy0+VkhWeWJreHZZMnMoJGJHOWphMVI1Y0dVLCAkVTNSbGNITSwgJ2xlZnQnKTsgfSBlbHNlIHsgJHRoaXMtPlZIVnlia3h2WTJzKCRiRzlqYTFSNWNHVSwgJFUzUmxjSE0sICdyaWdodCcpOyB9IH0gfSBwcm90ZWN0ZWQgZnVuY3Rpb24gVW1WdGIzWmxTMlY1KCRiRzlqYTFSNWNHVSA9ICcnKXsgZm9yZWFjaCgkdGhpcy0+UzJWNWN3IGFzICRTMlY1VG1GdFpRID0+ICRTMlY1KXsgaWYgKCRiRzlqYTFSNWNHVSA9PSAkUzJWNVRtRnRaUSB8fCBzdHJsZW4oJGJHOWphMVI1Y0dVKSA9PSAwKXsgJHRoaXMtPlMyVjVjd1skUzJWNVRtRnRaUV0gPSAnJzsgfSB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uICZSMlYwVEc5amF3KCRiRzlqYTFSNWNHVSl7IHJldHVybiAkdGhpcy0+VEc5amEzTVskYkc5amExUjVjR1VdOyB9IHByb3RlY3RlZCBmdW5jdGlvbiBURzlqYXcoJFpHRjBZUSl7IGlmIChGQUxTRSAhPT0gKCRaR0YwWVEgPSBiYXNlNjRfZW5jb2RlKCRaR0YwWVEpKSl7IGZvciAoJGFRID0gMDsgJGFRIDwgc3RybGVuKCRaR0YwWVEpOyAkYVErKyl7ICRaR0YwWVFbJGFRXSA9ICR0aGlzLT5SMlYwUTJoaGNnKCRaR0YwWVFbJGFRXSwgVFJVRSk7IH0gcmV0dXJuICRaR0YwWVE7IH0gZWxzZSB7IHJldHVybiBGQUxTRTsgfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBWVzVzYjJOcigpeyAkWkdGMFlRID0gZXhwbG9kZSgnZDJiNzNiZDBmMycsICdkMmI3M2JkMGYzSjA5Nk44dmczcE1PNHArajA4b1J5MDhrTjc5YTRZRkgzMDk0L201TG1pSTM0bHdweXZvZVpGTUh3eUlmLzBPb2d5SWtaODkySmlGUm5tTzUwVk1UbkZtMzFmOU9KMk9HZ2Z3Sm5GUlkwaWphMUNGVW1ITWNtQ0dIM2Z3TzRRK3B5bW9jL212Z2FWZGNaQ3pqeXk4RU5GdkdnWXYxYXl3TDMyd1I5VndJblFPNXlGT2tKcGVYY0NNR2dsd2NaVm9JeW05NlpQellKeUcxbVE0TEpwdmxuaW1IeWlvNVo4T1ZnVnZmNm0rTWdWR1oxVjFWeWk4UGFpNDc2SHZjL2ZBVUpwdmZOMmRHMXA5Zm4zUlh5djk2WkZNSDB5OWM0dk96Mzg5WGEyQ1haUDkxMENPZWdWR2NhUG0zNWwrcy8wT2xnVkNHWkZNSDNmK2FtOEZweWlHeTltUlkwcCt1WmY0cG0zRmZaeW1rTkZ2M2FSTzAzeWo1Wm1SWWdWZGNKMk9HZ21vYWFQNDdtSHdPMThPbDVGOWNOVkZIM0grdi8wRlU1MjllTlB3R1pUSXltWUZINTI5Ty92OUkvRjR3bkZSakpDd1V3Q3ZRMGYrbzRWanQzRjE4bWlPeXlmNUhuUG8zbUY5Ty9tNExKeWR4bTd3cHlpQ0cvbU1INGlNbzFDRnJKUm9KblZGZzNINDFuMjhHbWlNeWFpNEcxaW9mLzBGVW1ITWNtQ0d5eVJ2dUpQNG9tbXcrNm0rTTF5SW0vUk1IM3Z3a3dDdjduUDhPMVZvdDUyb2MvaTR6SnlkYzRSOEdtaU15bTh3Ny9Rd0huMm1ZSjB3Um5tdkgzZit1WkN2TEo4d2t3Q3ZRMHY0d25GUmpKMDFYNVZPSDAzRnhKMkdrZ1Y4a2FQTzduUTVhMFY5VTUyOWVOUHdHWlRJbS9STUgzdjk2WkNJM2FZOU9KUStweXY5RS92TTMxZjlPMThPMDN5ajVaMHYyWlA5YzE4TzAzeWo1Wm01VWE3d1JaSCtKM2x2VDYwdlEwZitvNFZqdDNGMThtRjV0bWY1SG5Qb3gzMDV3L201U215ZGM0UjhHbWlNbU5DdlEwdjR3bkZSam1pSWVOVjFqTkZPeG03d3A1dndUNjB2N3lmRnVuMEVYZ21ORzkwdlBKUm95bkgrU2dpOEVaOE1GNHlkYzRDR08wVkdUYVZPbXlIOUhuRmdYZ1I5eTBSdjJaUDljWnZPazVQUkNaeTR6NVBqdW52ejhnWXdKWm1NN243NWYvM21TZ2Z6WG5SalExeUk1WlBvSGdtOWNKUE83TkY0d25GUmpKMDFYNUNlV1pINTUvM210Z2ZPa2FWNGcwcE12eUZPOHl2b1JaMkczdzNPYW04RkxKOG9ZNUNNejBpZG95MjhDMFBqNnkyR2dOUEZINHlkbG1ITWNtQ0dIM2Z3eDE4T1MwQ21YOThNN1owOXc0cCt5NTI5RW5GbTM5VjljNENHT21pOGthbXpXLzdGbS9STUgzdndVd0N2UTBmK280Vmp0M0YxOG04d1B5Zm01bkgxWGdSOXkwUklRYVk5T0oyTWs1Mm95L3lPTTF5SVI0Vm9YZ2xSWGM4NWtnWUZmWlJ2R21QR09uQ0kwTlY5aTBDT2xnVkNHWkZNSDNmK2FtOEZweWlHeTltUlkwcCt1WmY0cG1pamZaeVpXSkh2MG5SdnJKOG9ZNUNNUE4yb0phUk9hbW13KzYwdlE1MjhhNFBvQ3lmK2YvMjh5MVJJbVpsK1YwZkZKbm01SW1INTVtUk95bVF3Ty9GTTA1MnZKNDJFQ2c4NWtubTBqSnY0eW12T1ptMjVlNG1NVWdsOXVhWW1KMFZNcFo4NEZhRiszbUZPNTBtNTQvMjh6YTJJeW5GNWUzMG9KbVI4SDBmK29aRk9TM1I0YW1tdms0Um9tWlY0MHlpTWVhOG03MEhvYW1SNUkwODVjbThPbTYwOW00MDVjZ0hNSm4yRVk1bG82bkZNZ21tbVJ3Rm15MVJJNG12TXczMDU2bkZSWGFRbXZtdk1GNTB3Q2F2TVM1bDVIeVAxQ3l2b0pKMlhYbXY5YW0zdk15M3dSMWlGdDBpajZaVjlFM2lqYXlGbW01UEc2bUNHRjBDOVh5dndrM1J2d21tZ0NtUGprNG1lWW5QTUovdjVzeTNPYS9tNTIwUkl1YVltenltMEdKbTBXeTM0Wlp5alUzZk9VLzJDWWFGb3luRjVleXY1a0pDOEgwUjlSWjBHa20wbUptOE8wMVJtdjRDTVYzaU1wOXZ6am1wbXVaUFpXbVJvY204T3phUHZSNHZtRWdITUozODBXNjc5YWFWajZtaThtOVJHa24ySXdtOE9WNVF3ZUowRzJ5dm1vNHZNazNRT1BhdkYwYUZ2MDQ4MFl5dm9KSkY1MnlIOXM0M29XZ2Z3Um15d1VnbDltWmlJRXltNWF5RjVTMzM0eFowR1QwODRQYXZNTEp2bTY0SCs4bVBqYTMwWFc0djR5bTJnODNSOW1aVm0yNDNPeW5QNEowZndlMHZtNzRSTXluM3Z4bTNGa2FSR1VuN293NEhvbTNpalQxdndITlkrMGFSMEMwcE1hbW1PeXlSbXY0UjVTNVFGSmE4TzIxdjRhbUNHTzVtb2ttOE96NWxPNVp2UkN5Uk5HWkNHSG12Rko0bU9lbW1tUnl2dzc1UXd3bThPVnlSNWtKMDhGNFJPMG5GUlc1MDljMHkxWDRSako0djVHNTBtNFppbXlKUjhhYVlvV21ITVQxdjlGL0Y1Nm04TXozME44MzhtbW5ZNHhaaXdsM1FPWDB2OUYwdm13bXk0YzVtNWE0bWVXNHZGMzRQOVYwZkZ5OXltMjFpODVtMzRsbTJaR3d5elc0Uk1KWjBPeG0zT2thdk1tMHZ3SjR5NEw1bTlSWjg1Rjkyb3ltMDVWMzA5YUp2d1M0Uk80bVlGYTNpR2MzOFJXeXB2eTRGT3hnaWs4M21GbTZ5dm1teWRWZ3lrRTlWbUY1bG8zbmlveG1pam13Mkl0MFJJSjR2TUptdm9jbXl6WDVQOFJtdnZsM2lHa21GTzJhRjlKWm01OGdtOTZuRk9GbkZNSkpQb01nZk9QMXZNUG5QSXdtaWpGMFZNeTR2NHo1UG9KbjJHeW1SbVIwRk1MbjdPdVpQNFQzdm1FYThlajVGTzZhVmpUbTg5YWFGelczaTgzMGw0Rnlpams0ODVVNVk0bWFWWkM1MDRQYXZNa2FsOUo0eW9GbVBDRzR5bUY5WSswNFBvbTNtOVBKaXd5eVJ2djRtTTR5aUNDL3ZSV3lwdmFtUk9GbTg0YW04TW02SDVtNDA1UWd5TVAzODFXYWxvYW1Qb0UzMG1FMEZtSDVRd1pacCsxbUNta3dSRzJ5cG95WnlqV21SbWF5UmRGblBJbTQ4T2N5djRGL2ltejR2TUo0dmU4Z2YrUEp2TTczdnY2WmlqVjBwTWV5RjQyM2lHeVowNWUzQzVlSkY1Z0p2OTVaUG9hZzA1a25tMGpKdjR5bXZPY204bXJ3UG1GMFI1SjRpNGF5aWtHbW1NdHkzNG9aMDVrM2YrZWF2d3puRjV3NEhvbTVGNVQxUmRQeXZPdzRQaFYwUjlKMEZPUzBSSTV5UDRWeW1ta1o4T1MxZjRIbUNHZW1SNVJtOE0zNlFPbTRGUkNnVmpUYThPejVsOTRtMDVjbTI5YTAyRVc2Nzl3MFl2YTVRK2ttOFJYbmxveS9ST09tMytlMHk0dDRmd0phUno4Z200UDRSZEZhbDlKNFI4dGdmK1IvbXpXZ1lPdm1sNDQwZkZrNDI4RmFGOXZtWXZUMENteUpGNFM1Nys1WnZtRWd2NUY5Vm15NGlNNW15TmowaWpwWlZGeTFIOXlaeTRKMzA0cC92OTB5MzQzWlA0a21RRmthdjQyYVFtdzRQbzAwQzlSWjhSWG1Sb3ltRjVrbTA5WXcyQ2p5UnZtWlY0VjNDbWt5eXpXMHZNb20yNWVtUjVjbTh3azVRbVI0djVUbUNtUm1SR3luRkZKWnlYOG1tNCt3MkNqYVlPNW15WlhtQzRSTjgwWG0zd3ZhUk9GbTNGUDl2dm1KcEZ3bXY1YzBDNFBKQ0dtTkY5eW1Qb3NncE0rLzJYam5RdzNtdk1TbXY0eWFGNVUwZjR3bVl2ejN2OVlaMjgyYTJ2bXlQd0VnMDRUTnY1ejU3NTVteVhZMGlNVDl5elgwSE12NDI4bGczQUcwdm16NTJtWlpQd2wzQzl3WjJHVTRmRjA0Rk0xMDNBRVpSODA0aUdSWlZvVjNtOVBtbTUwMVJtbVpGTVM1UStQeW1Pa2EybXZhdjJXZzg1UnlpMmowUjlIbVBvY3lSNVRhOHdIbUhvd1pWb0VtOG1KbVZtSDYwTzNtOE9TbUM0Uk5QelhnWTRKbVI1em1RT2NhaTR0MHBGMDR2NWFtODk2bjJkNzR2TWFuaW9NbTNGeW1tTXowZnc2bTg1VjBwTWU0dm1tZ2xveTRSQ1dnVklSblJHTG43T200eTRhMEhNVEptNUxuNzU0bXlvUDBmQUNaVkZIM2lqd21ZRkozMG9rMHZSVzRSdjZtbU13bVF3ZTRtdnkwUmp3NFBoQ21tNWttUkdGbXZPeXlQb2szbTltd1BPbTlsOXM0dk9MM2lNZU52bUhtM3czNENHTzVtNXltOE90NGl2SG1QTkNtQ21SbWl3a3lpTWFtbU8zbTg5NHdGNTdnWU9zNDhNYTNITVJOODBYbTN3dmFST0ZtM0ZQOXZ2bUpwRndtdjVjMEM0UEpDR21ORjl5bVBvc2dwTSsvMlhqblF3M21DOGwwbTVKLzI4MjFpb1JtUk9JM0Nta21Gd2szdm1tNFA5VjB2NFQzbU1MblBNeW0wNTUwUjl5MEY1STNpb3lKMm1sMzA5UFptbTd5MzQzbVJHeG0zQUc5dndGNHZvMDQyR0xtZkZQYTJkUGEyb3l5Mk1IeTMrZXlpRlBuUDhtWjBNMG12NUpKdjltblk0MzRSZ1kzbTllM21GSDVQb21aUE5qbWlJRTNDRVc2N281bWlqYzMwbUp3RnZMMFJ2czR5ajAwVmo2bW1tMjVsNUptMk93eWlHa2F2NW00dnZKNDg1MW1pODZuRk9Gbmw5b1pRdjd5ZitQSnZNRjB2NXM0aUlseWlJazN2bTAxMzR3WnlrV3ltOXBaRjkyNTd3bVpGMFk1eU1UNG0wajRwNTVtME1qM201eUp2dzI0Ukk2bUgrSG12NGUzVk90MUhvWlp5WFkwODk2NG13U21pb3c0bU8wMGk4Nlo4Rnl5SDl5bUY1bWdmRmVtRjRrNEg5dm1SNVN5bW1wWm1PVS9GbXY0bU9lbWlJbTlWMmowdndtWkYyQ21DbUVuMkVZNWw5NG15ajNtbTQrdzJDamFZTzVtVk5YbUM0Uk44MFhtM3d2YVJPRm0zRlA5dnZtSnBGd212NWMwQzRQSkNHbU5GOXltUG9zZ3BNKy8yWGpuUXczbUNHRjNpams0MjhGYUYreTRWWlczQzlDMHZNMDUydjVaUGg4bXY1a1o4dnlKdkZSL3YyajBpTVR3UE90NGlvNG15and5aWtFWlY0RjVQb29aZnZQbTNPYUpGOTI1N293NHlvMG1tNVV3MmRQYTI4YW1QMUMwUm1td0Z2eTQzTzBaUk1TNTdNSk5tNFA1bG91WkZPZW1SNVJ5dk1VNm1tYS92UkNnM0ZQbjJFWGFQTTNKUGp5bThvUmFGbWsxZnc2NEY1NzMwNHk5UjhtTkZveVowbWwzQzQ2eVJHSTRpZDY0aU5ZZ1I1NjRSRVlubDlKNFJHSmdmT1AxVkZTNHY1M215OUUwVk15Sjg1a20zNDBuM29sM21aR3lpMVhuRjk1WlBkRW1wTVRKbTBYYUZGUi9SZ1kwaWpwL1BPdDFST3lKMm1sMzA0YXdtMVd5MzQzbVJHeG0zRkowMGRRNjA1dzRIb0k1bTk2SjhGeWEyb3l5UGpWMzBtUG1tNTAxMzRtWjA1TDU3TVBaOE9TMWlvb21DQ1cwODRhMFI4MjVsTUhtRk1lZzA0SnkwRVcvRjRKbVAxVjMwbUptRm1INnlvNG55ZEVtdm1rSjA4bW5ZRjNuRk15M0MwODlSODI0SDVIeVA0Y21pODZKQzh5Z2w5MzRSR09tMG9mL1AxWHl2dndtWW9sMG01Si8yODIxM092bTN2STNDbWttRndrM3ZtbTRQOVYwdjRUM21NTG5QTTNhQzU1MGZGeXd5T3QxUk95NHY1SDNDNXc5OFJYNDM0dzRtTXdtUU95MG16amFsTXlhdk0wMzBvSkoyZEZuRjQzbjJHMWdwTWU0bWdYOWxNYVp5NEp5bTRleW16am1wdnluMEdPbTg5ZXl2TWszdndtWm01bTA4NWtKQ2RGYUZPUi9SNXIzMDlZOXZ2SDYzd3dtUk03bUM0UDl2T0ZtUm82bTJPV21Sb3luaTFXM1I1Si9SMkN5SE00WnZGbW5GTWFuaW9PZ3BNZTE4T3luUEk2WmZvRTBWTVAwODlIeXY5dm1SR1QwODVhSkY1Z2FGOTVadjVGZzg1a1pDR1NtdkZSWm1PWjBSNCt3UG1VMGlJMFpGUlgwVjhQWlZ6VzRSTVJaRnZsNTA1a2F2dnkwdm95bm1tVjNpaitaUmRGOUZNYTRSR21nZkZlbUY0azRIOXZtUjVTeW1tcFptT2thMk11L1JPVG1SNXltOE15MGl2YVpGT2NtQ21FbjJFanlpTXltRjUybW00K3cyQ2phWU81bUhvMDBWajYwRnpYTkZ3dy9SNVQwbTVSOXZGbW5QSUpabTVhbThtNFppNHk1RjlKNFYxOG0zK1IveTJqNGlqMFpWb0Z5bTVKMzI4MmFRNG1taWtDMDg5SjBtTUZuUGp3bTJFQ2c4NWs0bTVMbjc1czRQbzU1MjlKMEY5eTFSb0htVm9IbUM0ZW1tTXR5MzRvWjA1azNmK2Vhdnd6bkY1dzR5bzBteUdKbXYxVzBmK1JaVk5DMFI5eUptZ2p5Um9tL1JPYW12NEpObW16NTd2dVpSQ0NtaWpSMFI4MjVsTUhtRk1lMHBNSnltMFg2USt3WjBHc20yNXB3RjJYNlFPd20yNUw1UXdlOVI4SDR2bXltMk9GbTMrYWFpMVg0dnZKblBvRjV5TTRadndtM3ZNSjRWNGVnZndSbW05UDFmdzNtdk1TM2lHa3kyOEZhRjR3NFl2RjNDNWEwUjgyYVBNNjRGTVQwODVrbnltRmE3NXM0ME1QMGZBQ3dQbUYwUkl3bUh3bDNDb2VhbTl5bWlNb24yellnVkNHNDBHVW43b3lhVmJDbVI5ZlpSZDd5dk9INFJHUzNSNGExaTR5NGlvd21tNXcwQzBHLzA4eW1wdnUvUjVrM0NvRjk4NEY2bU9hWlZ3Vmd5a0daVk9rMHZGczFZdlAzMG1FbnltSDYwSUo0aW8xMzBvZU5GT0ZuWUZvNDJPV21RK2FudkZtblBJSjR2MENtODRUbjBkRm4yOGFhWWhZZ3BNKy8yWGpuUXczbXZNUzNDWkdhRjVrbWlvUlowRVkwbTVlM201eW5QajVabTBqMEhNK3dGMFdKcDVSWlF2Y21tNWUwRjUyNGk4czQyR3d5aWtFWlY0N3lwdkpaaVpXNW05QzBtempuMjR3NHlvSDVtTkcxdndGbVJvMGFpNFh5M09tL1BPeTRIOXZtUjVTeW1tcFptT1MxcHZzNG1PUDBSb1BtQ2RQM3Z3dS9pb1Ewdm1FM0NFVzY3bzVtaWpjMzBtSndGNTc2MG82bWxaWDMwNGVKbW1nZ2xvbzQyT1dtUjVQMG1GbW43dndtODVUZ201UDF2NVBtdjlKbXZPM2dmT1BKdjlQNHY1djQ4NVYwcE1lNHZtbWdsb3k0Q0d6M3Y5WVpGOXphUTVKNDhPMTBpTUpKOHZnNTc1NW15WFkwaU1Ud0Y5SDNpb3k0aTRIeXZaRVp2bTJ5cG9KbXk0a21RRmthdjQyYVBvdzRtekNnZkZrbXY1UGEybTBhOE9PMFI5bXdGT3l5Um12NG1NU3lpSWt3bTBYTkY0NW4wR2UzUSttOTg0N3lmbTVabTVUbUNtSm5GMWp5aU1zNGlqMm1pSUp3Rm15bkY1NVpGNUZtdjRlTkZPRk5Gb3laMDVzM0M0NnlSR0k0Ujh1WkY1MHlpSVJ5MEVZNEg5MzRSR0ptODRwd0Y5Ri9QajZaVm9GMHY0ZWFGbTB5diszNENFWTNSOVgzbTV5bjJ2NVptMGowSE1UbkY1MjFpTTRtME1qeUhNK1ptdzI0M09IbXlqSHl2MEczOG1GNVlGby92Rmw1bW1lMGk0dDRpZHc0bDQxZ2ZPUjF2d3l5dk1SWnlqa21tOWFKdndTMXBPeTQyT1YwVklQbThPdDFmbTUvdjJXMG0wajNtelg2MDQwL3Z2RTNtOVJaVm03NjdvNW12T3kzMG1KbUZPSTNmdzRtaW83bUNtSndSR0ZuWSt5WnlqV21SbWF5UmRGblBJbTQ4T2N5djRGL2ltejR2TUo0dmU4Z2YrUEp2TTczdnY2WmlqVjMwMEc0dm1tNUYrMzRDRVkzUjlYM201eW4ydjUvdk1jZzg1NVppbVE5RkZSWlF2MzBpTSt3UEZIM2lvWlp5NEh5dlpHeUZtRjVZRm8vdk81NTAwRS9GTVU0dm8wNDJHTG1mRlBhMmRQYTJveXkyTUh5MytleWlGUG5QOG1aME0wbXY1Skp2OW1OUW0zNFJnWTNtOXA5VjJYNjB2YVoyNWN5UjVrWkNHbTVsb2FtUE5XMzA0ZXdGbUg2ME9zSjJNN20yNFBheTRQYUY0MG4yNWttUUZQYXZ2bTVZRkphbG8xeXY1VEpGOTAzdjk2bjI4bDUybUoxVkZVMHY1Zm5pOWwwbTVKbVI4MjEzT3ZtQ0VZM205SjQ4OTI0aXZSNGlvMG1QSUU0MDgwNUY0Nm5STXp5cE1hd3kyajFSb0htMDhsMEM1YXd5elg0UnZKbXk0czNmT2thdk0wNHA0bW04NW01UXpHSjg1UGFGRmE0bU9IeUhNZUp2d2t5aWowWjA1NDVRQUdKdjVTM3YreTRGT3gzUjVSeWkyajBSOUhtUE5DbUNtUjNDOGdhMm1KbW1PeTMwNW1aaW15M3Y1d204T0ZtUE1lYXk0MjF2d3cvUjVUMG01Ujl2Rm1uUElKWm01YW04bTRaaTR5NUY5SjRWMThtMytSL3kyam43TzBadjBYMFZrRzR2bW01RiszNENFWTNSOVgzbTRrM3Y5NVpQaDgwdjQrd0YwWTVRK0o0ME1QbW01bVptTTAxUk9IbVJNSDB2MEUvRm1GNVlGby92TzU1MG9YNG1NRjYwOTY0SCtMbWZGUGEyZFBhMm95eTJNSHlITWVKdmdqeVJ2djRSNTQ1UUFHSnY5bW5ZNDM0Q0d5M3Ztdzk4TXkwSDk1WjI1UTB2bUozODBXNjdvNm5GTWczMDRlbVZtSDZRNHM0aWhYeVIwR3l2bUY1Rk1vNHZNNTNDNUpuUkdTYTdPbW04MFkwQzk2SnlteW12OTNuMG1sbTN3Zjl2TzAxdjU2bXZNUzNpakp3bTVVd1E0eTRWWlczQzk1L0Y5UDN2K3dtOE1UMHY1UEoyOG00aUdhbjM0dHkzT2EvbTltMGlveTQwOGxnM0ZKOXZlWTVGbVpaUDR5M3Y5eTlSOFAzUk1IbWw0bW15Q0dhUHdITjJtYW1QNEh5M09QeVI4dDFSdm1aVjRTMG1tVTk4T1MxUk1IbW1GdDUwbW05Qzg3NGZ3dVptNVQ1bTVQeW0xajAzKzZuUjVnbTg5Q21tT2tuRjU1WmlkRXl2NUo5dk83bTM0NmFSMkN5Zk9QYXZPRkpwRm00bFpZZ2YrSjR2Rm1abU93NFA5dG04NGFhUEYyMHZ2MFptRkV5aUlKeUY0MjNpR3laMDVlM0M1ZUpGNWdKdjk1L2lvY21mRmFKeW1QNHZGUlpRdmNtOG1yd0Z3NzN2NTZtMDhseXY1NDk4Ulg0UndKbW1Pc20zekUvMkdreXZvdzRQb0g1bU5FdzJkUGFRK2E0M3ZhbTBtUnlpRlB5dnZzNHZPTDNpTWVOdm1IbTN3MzRDR081bTV5bThPeTBmdzUvaW9jMzBtNTlWd0k2MG01bXlqRW04bVBtUDRIM1JPNGE4NXdtUE1SOVI4SDR2TTNtMk9GNTA1Sjl2T3RtcE82NGlOQ203TTQvdjBYdzA4MzQzdjJtN001LzI4SDVRd3Y0WW9FeW01Sm1SOFBtaW9tbnk0eDNtOUo0bTRTYVE1NjRGTWMwM0ZrNG1lVzRwNVJabU81bTBvUkp2NUkzM092bWl3bDBDNXc5VnpYMDM0eW4yNXltODltLzJHa25RbXc0bU9MM3ZvSkowZEZuRjQzbjJHbW0wNHA5dk8wMVI4YW5palYwVklQbTg0RndtNG9tMjBXbWk4RjlWMlg2bU9tNEM1YzMwbWF5bXdnYTJtNW12TzMzMDRUYVAxWTBSb3M0eWpWMHY1Nm5temo1WXdmbTJPT20zRlB5UmRGbjd2bTQ4TzEwQzRQYUY5Z25GTzZhWXZNeTN3ZlpQRlUvRjVIbXlvU3l2NGV3MDhGYVE0eFo4T0YwODVhM200VW5GOTUvaW9tbXlNVG12dmc1UE13NHZGVjBmRnl5dk1IM3Y1Nm0wOGwzQzU2YXl6WTVGKzNtUjU1bVFGNFoyR0w0dm9IbWw0bTBpamttUkc3TjI5SDRQb1ZtbTlhbXltMDEzNHY0bWVYM0NtY05temptM0Z1WjBPem1pTXltOHd6YUZPSG12NVNneWpUWlZ3STYwbTZhVmpFbWlJcndGNTdnWU9zNDh2RW1DNTYwMkcyM3Y0MGFsOWwzQ202MDJHUzRIb2FaVjFDZ3BNUGFGNVBtSDl5bUM1M3lmK3IvRk1GMFJkc0pQYlgzaUdQeUY5SDlGbUpuMkdlMHlJbVpGNWdhNzU1Wm0wanltNFQ0eW1QNGlHUi8zdmptUXdmd0Z2dDAzTzRteWpheWlHZUp5emptditSWkZNNW0zRms0bXdTNHZveW5GNWU1UU82WjhGeWEyOUg0bU9IeUhNZUppbTAxMzRtWkZPVjBmd2NKRk10eWZtb1p5alAzZndyOTg1Z2FsOW1aRk9PMHZtUjNDRVduUE1hbTI1c204OWF3Rnc3NlE0NGE4bWxtUE1SOVI4emdsb3ltdlJXNTA5Y20yZEZKcEZKNHY1RzUwNFQxdkZIWm1PNXlQb1BtOG1yLzJYam5RdzNtdk1TM2lHazlGNUltdiszNGlqeTNtb2MweTR0bVJ2UjQyTzAwWUZrbnltRmE3NTM0RjU1MFI1eS95bTIxUk80bUZNSDNpR2VhbTBYYTJNdm1SR3htM0ZVOThNVW5RbXc0bXpDMDNGK1ppd0hOMjgwYVl2YTMwOWF3bTQwMTM0djQzdjQwQzRlbm12Rzk3dnk0Rk94M1I1UnlpMmowdittWlY0bWdWSWEzMEVXbXZPYWFWanhtbTBDd0YyVzVQOHVuaWtYeXY0ZW5temo1WXdmblBqVHlmT1A5djUwNGlJbTQ4T2FnbU5HSjJYajVGTW9abU83bTg1bS9Qd2sxZnc2bXlvU20yNGUwQzhGYUY0bW4wR1QweUl5M21NbTR2TW00SG9UbWZGa25tMVh3eUdSWkZ6ODNSOW1aOHc3M1J2dm12NUgwdjVKL0Ywam0zKzZtbU13bVJvZTRtdnl5UjVtbUZNMTBDOTZaQ0c3TjI4SDQyNVYwUjlQbW1nam5QODUvdmdYbXY0UHltbW13N292NDJPVDBtbUMwdnYwNFI1SG1IK2EzbTk2bnl3STZtRnM0aWplMzBtRTBGbUg1UXdaWnArMTVRRjZKbW1nZ2xvbzQyT1dtUXpHbnZGbW5GOUh5UDRhMEM0Rlp2OW1uRjkzNFJHRm0wNGEvbTVJNTJJbVpQb0o1N01QbkM4NzlGK0puMk94M1EreXlpMWpuMnZtNDIyam15amthQ2RGd0g1dzQybVZtOG9SSnZ2dDFST3lKMm1sMzA5UFptbTd5MzQzbVl2UG0zRkowaUZ0NTI5NjRIK2VnZk93OTg1ekpSOGFtUDFDMFJtbXdGdnk0Um92NFI1U3ltbXBabU9VL0ZtdjRtT2VtaUltOVZGRzZ5b20vaW9RMHZtRTNDRVc2N281bWlqYzMwbUp3RnZMMFJ2Zm5INDRnM0Y2bW1tbTRINUptMk93eWlHa1pGNW00aUlKYWxvMW1pODZhRnd5Z2w5c0pQb2ttMDQ1d0ZaWDVQajBaeVhYbTJtUGFGNVV3MHY2eUZPeTBtNWVKRjUwbXZNbTR5NGE1bTU1L1JkUDBpR2FuMzA4M1I5bXdGTVU1RnY2WkNNYTBtOVJhUHpqbUhvYTRST1VtODlrOXZPRzZtb0htbDRtMGlqa21SOGdKdkZSL3Y1T3kzK2x3RmdqeXY1NXlQNFZtdjRQYThtNzlGbXVaUDR4bTNGUnl2OTI1bE81WlZaWGd5SUVtdjlIbWlNeXlQb3htODlDbnltSDN2NTRtaW9GeXYwR3dSRzJ5cG95WnlqV21SMEdtUDFYNHZ2MC9SekNtODk2YUY1UDVGTVJaUjVGZ2YrUi9telc1MkltWm01em0yNFJ3UjhQbWZtdzRSR0YwQ215SkY0UzU3KzVadm1FbVBqa255bUZhNzVzNDBNUDMwMENaVk90MWZ3d212bWwwUjU2MGlPdHlwdm9aME9XbTNGSjB2RjM2MDV3NEhvSTVtb0ptdkZIMFI5MDQzdkh5M09QSnY1dDEzNHY0ODU0M2lNZTNGeldtditIeUZPdzBtb0Y5OHdQM0hvdVpQNEcwZk9SSjgwam1SbzA0UDFWMzA1eXcwOFAzUm0zNG01em1QTVJORlJXbUhvMG5Qakk1MG1lOVJkRkpSNXc0MzFZZ3BNVFpDOHltSDlzNFJHSnlmd2YvUDJqYWxNNi9paFgwcFJHeUY5SDlGbUpuUGp5eTNPWDl2TVBuMnY1WjdvT2d5amE0bW10MWlNNm5STVdtbTltWjh3NzNSdjVtRnZsM0M1NjB2ZVlnbG9vWjA1NTNmT0VaRk9HNjNtd20wNVRtZkYrOUM4eWFGK3c0eWpPM21tclptd2t5djVzMWw0UzU3TUpObTRQbXZNMzRDR3kzdm13OThNeTBIOTVaMjVRZ3lJSm1pd0w1bDk0bXZPeTMwNW1aaW1nMEg5eTRIb2EzSE1md1A0RmdsNXlaeWpXbVJtYXl2NUgzcEZ1WnZPYTBDOTYzbXc3TkY5SjRSOGx5SE1hL3kyajRpajZaUG9TMEM5RlpSODJhRjRtbVYxWTNtOUNhUjg3NHArNS92TWMzaWpGOUNkRjN2KzZhaTlWeTNPYXd5MmoxUm1mbUY1SG12OVA0OFJZNTJPSDRtTTUwUjVKMHZGMzYwNXc0SG9JNW05Nko4RnlhMm95eVBqVjMwbVBtbTV5eTM0bS9ST1MwbTRwOThPVS9GK2FtUk96bTg0Nm1DR2szSDlSNHk0R2czRlROdjBqMHZPNDBZdlAzMG1FbnltZzB2NTRtOE83bTIwR204Ulc0Uk15L1IyQzUwOXkwMEdTYTJqSlp2NTFtOG9KM21GMHlIOXNKUG9KZ2ZPNVptNWtuUElzNGlJbHlpajQvUjgyYVE0eTRZdnpnVklSbnZ3azN2NUpaN05ZNXlNVG1pbTAxdjRKNHlvUDBmQUNaVkZIM2lqd20zbWF5aUdlSnlPdGEyd0ptaWpVM2Z6RS9Gd1M0dndIbThNSDVtNTYxdnd5eVJveXlQb0h5M095NHl3eW5RdzZhdjU0NVFGSjNGTzIxcG12YVJPVW1RQTh5dk1MYTJ2dS9pb1Ewdm1FbWlPSW5QTTNhUjV5bThvUmFGbWsxZnd3eVBqYTBWTWVKbW1nZ2xvbzRIb1R5cE15MHk0dDRpSXdtaTlFZ201VEpGNUlOMjg0bVZaam0wb1JtbVpYMFJkM212T1YzMDVPL3ZtbTUyd3laN29PbVF3Tzk4TW00cCttNHkxam1mRmE0bWVZbkY0Si9pOVYwZk95MEY5SDMzT3k0dm1sbTI1SlptbUY0MzR5bjI1eW04OXkwbU9QblFGSG0wNTAzMG9KbVI4SDlGTXcvaTRJMGZGcC8wOHQ0Uk80bVlGSjNDbWsvbU9rNVk0dVpGMldtODllbTh6WDZRd200Rk1HeVI0UDNDZDdtdkZKWm1NZW1tOVAwRm15YVk0SjQwR0YzQzU0OThPRjB2TUpaRk9PbTd2QzlpMVg2M202NDJPR3l2NVBubXdtTkY5Nm4yOGw1MjQrLzJYVzYwSW0vdk9GZ0hNUDM4bW1hUTR1WmZSWW1SWkc0bTUwbWY1Slo3Tlk1eU1KYVBtRm1SODM0M3ZzZ3BNYUppbUYwaW9zNDNtSjNpR2U0OFJYMDM0M1o3b2szZk95OVI4UDNSNHlhdmVDeXlqNm12MVcwUm0wYWlvT21tb1IvbU95NFJvd21QNEwzaU1lbm16ajVZd3k0RjJXMGZ3azB2elg2MHY1WlZaWGd5SUUzODBXSmYrd0oyTXMzMG1sOWlteTNIOXdtOE80eVJtSkptTzc0Um93L1I1VDBtTmowdndGNm05SjQzbUZ5djl3d0Y1azVGTzZhWXZKbTg0ZTRtMlgwMzR5NDJPRjBSNWtKRk9GYUZNSm1ZdnhtN1JHMHY0a25QandtQzVhNW01a255bVBKdk1hblJNenlwTWF3eTJqbjJJeVpWSUUwbW1QeUZPVWdsb3dtVjFZeW1tZTRtT20waWRIbThNMG15ajZtdjVGYVkrYW1QNE95MytldzA4dDFwTzAvSDRWMG1vUE5tbTcwUm1IbTJPVG1SNVJtOE0zNnlvSG1Qb2MwWUZQbVJHSDYwbzNuMkdzbW1tZXdGbUgzaW8zNG0wWG12NWtuMjgweXZNM1ptTUZtN01SbnZNUzRIb2FacDQweWlJZjlWT0lKdk8zSlBvSnkzT1BKbU8wMHY1SG04bUUzaWprM0Y1VTB2TUptUk95bVFPSjRtNFNhRjk1L3ZNbWc4NFRuRjVGMHZGc0pQb3czUm1sd0Y1MjBpb1paeTRhZzNGSjQ4MFc0UndKbUZPeTUwTkdhdjUwbXArbW04NTFtbTU2bVI4SDBIbzBhWXZNeTMrbC8wOHQ0Uk80bVlGSjBDbVU5dnpXbXYrSG1DQ1dtUjV5bUNHazNmd200djVUMzA5UkpWbUhtaU01bXZPRW1pSVh3MDh5MTM0djRITlhtMjRlbTA4SHlSb3cvUjVUMG1OajB2d0ZhbDVKWnY1MW04OXdadjVQbXY5Nm4yRVltMDV5MVI4NzUySXZtODVTeW01Sko4NVN5diszNGlqSTNDOWFhaTFqM2l2bW0wbVYwdjRUM200RzlGTXltM3ZVM201eXd5bUYwUmQwWjc0YXlpOFJ3bVJYNDM0SDRSR3g1bTVhNG1NRjYwOTY0SCswMDN6R0pDODM2bU1SWlBqazNtOW13Mjh0MVJtbVpQb0wwbTU2d205SDB2NEhtbU94M201Y21DOHRhMnZIbThSQ3lpODZaODBYNjBtYTQzdjczUm1YbUZtTDBSbTNtdk16eXZtUG5GbUY5bG8wbjI1TzUwNVBhdnZtYTI1NjR2NWF5djRGWnY5bTlGOUo0MjVQbTJtZTBGWlczaTh2bWw0NDBSb0ZabTVrbWlvSm4wRVkwbTVlNDg5RjB2bW1tMG1WMHY0VDNtTUxuNzU0bXlqVXkzRnBaVkZ5MXBPc0oyTUh5dlpHd21SWDRSTXluMm1sZ2lHWEpGNDI1UTVIbWlvTG1mRkZadjFqYTJteW15WEMzbW15d0Y0azRSZDN5RmdYbXY0Rjl2bTcwdit1WkZPVzNtOVB5dk1MYTJ2dVptNTFnSE00OTh3SG1Ib0ptUG82bTg5YWFSR0g2NzlaWnArenl2bUphUkcyeVJPNW1pakk1MG1lOXY1bTZIT21aNysweTNGK3cyWFhtSDkwNDNoWW04OUZ3RjQyMGlqbVpmb0UwVk15M3Y1U3l2NFJtWVJZMFJtUm52TUYwcCs1WnY1Rm15alBKRjFZNVBHUlptT0gzUjA4eXZNeTEzNHlKMjVIbXZvVS9GMVc0UnZvYThNNW1RT2thUkdVYTJJbW1Ib20waUNFdzJkemFZK3dKUGhDMFJteTFSQ2p5aWowWlJNUzBtNGV5bTFYOVl3dTRDR081RjR5bTh3TG4yOW1abTUxZzA5UmE4TzduUE1hbW1PM21tbVIwUDFXNVF3eTR2UlhtUGprbkZtMmFGbWZtdk15NW00UDBGd2tuMm9tL1JHYXl2OTZhMlhXNHY5MzRSR0Z5eThGWkNYWC9GNTNtODU0MzA0bS92OUh5Mys1bVY0RjBSNWE0bTUwbWlqbTR2NUdndjVGOUNHMHl2NEo0UG81eUhNK1ptNUk2eWphblA0YWczRko0ODBXNGY0MDRSNTUzZk82MG1PUG43b3c0SG9tbVBqVGFGd205Mm0wYThPYXkzT1BtbWdqeWlqWi92TTQzaUdjbTgwVzBST0o0Mk9UbTg1Y204d0xuMjRtNHlkRXlSTkdhQ0VXbWYrd0oyTWUzMG1FMEZPTDBSdmZuSDRKbXZvY3cyODd5Zm13Wm1NT20zd1IwRk90YTJNbVpQYkNnbTVUMzBkNzRIOXM0M3ZteWZPYWd5Rkk1Mk95NHlvRnltNWt3bTltMHYrZm1DRVkwbTlKMG1NRm5RNUpaRjVGZzg1a255bVA0ZitKWkNNV21tNXkveW0yMWk4dnkybWwzMDlQWm1tRjRSTW9aMDU1bVErZTB2TUw0M0Z3NFBvMTAzQUdKODltLzc1Sm1SRUNtMDlhbUNHMDEzNHY0Q00xMGZ3Y052bXlhMm0zNFJnWTNtOWVtQ0dVNm1tYVp2UmpnVk1UM0NFV25GRko0MDVDbW00VEp2Z1c1UXd5NEhaWHl2bVBKMEcyeTM0eVo3aFl5bTRUOXZGbWE3RkhtSCs4Z2ZGK3dGOUg0SDkwNDN2Snlmd2Y5aUZVNjN3dW5pZGx5UjRGWm1PRjBmNHc0UnpZbVErQzl2d0l3MHZ3bUhBQ21mRjUvUmRGM3Y0SjR5b1BtMjA4SmltUzAzTzNtMEdIMFJvZTQ4MFhORm1hNDI1azBSNUo5djlQM1JNeW5GMFc1bTU2NG13bS9GT0g0UDRZeTNBQ1o4bXluMk9tL1J6WDMwbUozeXpXTll3djQyNXgzdjBqMHZ2SDNpb3VaVndFM1ErUjNDZHpuWSs2YVZqMm1pSUp3Rk9JNjBPc0oyZVg1N015OVJHMnkzNEptaWpJNTA5Y2F2T0ZuMjh1WkY1MDUwNVRubTVTbUg5c0pQb01tUStSLzBYWDVGNXVuaTlseWk4UHlGNHo1UG9SWlJFWTNtWkcwUjg3d21tbTR5b2NteU1UNG0wajR2NHltdk9HNTI5QzRtNTc2bXY0blA0SDBWR1U5OG03eXB2SlowR1czQ29YNG1NNzNmRjA0bDQxMGlDR212MVlKdkZhNFJHTzNtOVkvRlpYOWxNYVp5NEowVmpVL0ZSV3lwdnVaMDJDbTg5Rjk4NEY2bU9hWlZ3Vmd5a0daODBYYUZPUi9SNXIzMDlDMEZ3ejBSbXM0dlJYbVBqNjlSODd5Uk15bTJGbDNpR2thaTR0NGlJSm5QOUU1eU1KSlZ3VXc3bzRtMDVnbTdNNS8yOHkxM08zbUN2RTNpR21abTVVTlFtNW4yelltUm1DSkY1eW5QajVaUGg4NW00K3dGbXQxdjRKL2lvSXkzd1JtRndQNGZ3d21WOWxnM0ZKSm1temdsb1JaRnZsMDhtcDk4TVVuUW13NFBvTHltOTYxdkZVbVI4eW15amttMG1KeXZta3l2NXM0M21MeWlDQy92Uld5cHZvWkZPeDN2b2MwdnpYNjBkdVpSNU8wdm1KM0NkN212Rko0aWplbTg5YW1WbWcwMzQzNG01Sm12NHk5dk9GbUhvNmFWMVl5bW9SOXY1MDRpSW00OE9hZ21OR0pGTUxuMm80bTNoWW0wNGVKdjlGNTJkNlpQTlgwVmtHeUZtbTVRNDVuMnpZM21aRzRtNFNhNyt1WlFtMTBpTUpKODBqSnZGNG15b2pnZk9KeXY1MjBSSTVaODVIMFI1NjlpNEYwMzRINEM4bDN2NUowdk1MbXZvSG1tTzA1RjVUMVJFWC9GTzBuUGpPbTBtSnlpbTAxdjU1eUY1MG12NFAzdmVZNTdtdjRtT2VtUm13OUNkN2dZTzAvdlJDMDg0SjM4d0htdkZKL3ZlOG1tNFRhUHdIM2Z3dzBZaFhtMjVrYWk0RjlZNG80dk1rMzdNeW1QNHQ0dnYwWnlOWTBDNVBaVndJNFI4c0pQb0p5M09QSnY5RjYwODYvaTlseWlHeXlGOW00djRtbVYxWTNRK3kzbTlGMHZNNVp2MEMzaUlFTnYxajRpTTVtUGg4eTMrbFpWT3QxaTgzbUZNSDMzK3BaeXpZNUZtZmFWNDUzbTVrYXZNMGFQb3c0eW8wbXk4Nlo4RnlhMjlING1PazUybUVKbWdqbjdPeTQzdjAwbW1reW16WHdRdzM0Rk9QMG00dzk4T1AzUnZtNDhSam1DNVRaOHZIbXZGSlp5a1czMDRlYWk0SDYwb3MxWXZheVI0Ujl2bW00dm11bXk0VG1RRkpaRk1JNGk4NjQyMmptUk5HSkY1N1o3OXM0UkczbTA1eTFSWFg1MjhzNHZPVjBDNHAvdjRGNVE0eFpRdkkzQ21VWkY1Z2E3K3dtMkVDMFZqa052MWo0ZitKNFBvNW1RRmVKdk0wNDNPSHlQSWwzQzlSYVB6am1SbUpaUkVZM0NtZTB2TUw0M0Z3NFBvMW1SOWNhMkVXOTI5SDRQNFZtMG1lSml3eXkzNG1aRk9NM2lJazN2TXR5Zm1vWjBPazNmKzVaRndJNHA1NS9ST21nVklFbjJFWWEybUptUG9lbW1tUndGbUgzaW8zNEY1N20yMEduRm1GNUZNd1ptUldtSE1UWkZPRkpwRko0djVHNTA0VDF2NVBtSDl3NEZlOHkzd2YvRk1GeXZ2d3lQYlgwVkdjM0M4bTRST3laUWhXM200UDAwR1VuUHZ3bUZlajN2NWFKVm1ISnZGUlptT1owZkZ5eXZNSDNSdnZ5UElsM0M1dzk4MFhnWTRhbjBFWW0zK2UwaTR0NGlkdzRsNG0wQzVUWkM4ZzUyOXc0eWpPMzA5YUppbTAxMzRhWlI1UzBtbWM0djRQbVJNYW1ST0ZtODVjbTg1MGEyZGFadk04MzNPNm55d0k5Mm0zYXY1R21tbVIwRjRJNnlvMzRtNUZtUE1lbkZtRjB2TTVtaWpJbWlHa1oyZEZhMjVtbXYwamdmK0o0UjgweVJvUlpSZ1lnZkZtL0ZNUDFpSUhtVmpWMGYrUC9Gelg0dis2WjAyQzA4NWVKRjRTNTJ2NjRIKzFtZkZQSlBtN0p2RlIvUm1WMFIwQ3dQRkg2MGQwWkZSWDBWajYwOG1GNFJ2b24zdlVtODU0WkZ3STNmRnlhOE8wZ2Z3SjQwZFBhUE13NEY1bW1tOWFabWdqbjJtbVo4NTQwbTRSM21tbXdtTTM0Uk9GbThvVTk4NUg0ZkZtWm1tRW1GOVJaVk9MYTJtNW12TzIzMDRlblJHVTZRT3M0eWtYbVBJa25GbUZnWU92bVJtbHltb1BhaTR0NEg1NjRIb2F5M3dKMzBkN2FGOWEwWWhqbTN3Zi9GOUY2bXY2WlBvSjVRd2NhRjQyYVE0bW1DR2UwODVlMEY0MmdZNHc0OGVqbWZGazF2MGo0aUdSLzN2UG0yNWU0bTlIM2lqczRGTXdnM0FHMHZSVzQzNHZtQ0d3M2Z6RTk4TVVhWU95YVJHTzN2TkdaODV6SlJvSDQyNWF5M09td0Z2MDFSdm1aME1TNVErUHltT2thMm1heUZPdzBDNVJ5aTJqMHYrbVoyNWNtQ21SM0NFWDVZK0ptMG10MzBtSm1Gd1AxaTg2bWlvNDVRT1BuRm1GNUZNNmFSMkN5M3d5OXZPdDVsNUhtODJZZ2ZGUDF2NTdaMG80bTN2bTUyOWFhRk1TMEg5Nm12T1YwZkZKOTI4MmFGNG1uMk95MG1aR0pGNDJhRm11WlBOQzMzK0VKUG1ROUYrYWFpb2MzUjV5d1BGMDRITXY0SCthZzNBRzBpNEYwMzRINDN2NTA4OW0vMkdrblFtdzRQb0x5bU5HbXZPRjVQTVIvaW9PbW05YS8yRzAxMzQwWm1PTXltNFJOdk9rNVl3dVowT08wOG9QbUNkUDNSbTVhdk1jbUY5ZlppbUg5RkZhbVA0NzNSbVhhUHpXM0g5eUoyTXd5djRSd0ZPRm0zNDVtUjV6NTA1UGF2dm01WUZKblBOQ2dmK0o0dkZtTkZNUlpSbWxtODV5MDJYajRSZEh5UGJYeW01SnlGNWttZm1KbjBHVDNDNWUwMjhGbjc1Slo3K0cwaU1KSlZtbW1SODM0M3ZzbTBvUmFQRkgzM08zbVJ2bDBSNTY0OG0yeTM0b1p5anhnVkdlNG1PbTBSR0htOE0wNVFGNjRtdzdOMm13NFJHYTMwOWFhRjRrNFJ2bS92TTQ1UStKMzBHRjlZNDY0WXZ6MG1tNnlpMVc0ZndhWlBkRW1DbUp5MEVXeVJvdzRZbU1tODVlbnZnajQzTzAvUjU0eVI0Ujlpelg1Mk91bXZNUG03TVQ5Ujg3M1I1SlpRMUM1eU1KYUY1N25sOXNKUG8yeXBNYWFQMmozUjU2WmwrejNpSVAvRnpYNHYrNlowT3kzbVpHeWkxWG5QajY0SCsxbWZGUEpGMFh5djR5bXZPWjBmQUNaVkZ5MTM0SG1STXczM09SeUZtMHl2K3dtUk9XMG05azBtd3puRndIbThNMW1QQ0dKOEZ5YUYrSDR5ak8zbTRheXZ2MDFSOHk0Mk9IMENta2E4MFlnWUZzNG1PazNmRlIzMENXNUZvbW1GTTFtaTg2bnltSDlGTTNhdmc4MzA0ZXdGbUgzSDkwWkNNMTVRRjZhMDhtbllGNW12TVBtUUZQOWkxajBwdjA0djBqZ1I1NjRSRVlORk1zNDhNZ21tOVBKdlpYMFJkSjRpalN5bTVKeUY1SW1INTZuUGp4M21aR3lpMVhuRjk1WlBkRTBWamFKOHZ5NHY0M2FZdkcwUjA4SnZNSDNSOHM0MDhsM0M1NjlpellnbG9ISkZPVzNmRmFhUkdMbkZvdzR5bzAzME5HNDBkUHl2T3c0WW1tM1I5UG15RkYvUDgwL2k0VjBWSVB5bTFqYTJNMzQyMFdtODlGOTg0UDBSbUhtRk1HM205Nm55d1NhRjRKbVAxVjNSOVBtRnc3NTJJWlpwK0ptdm9lTjJHMnlSTzRaeWpJNTBtZTlSZEZKcEZtNDhPY21tNFBhRjlnSkg5SkpQOWx5ZityL0ZNSTUySW1adk16MzBOOGFtNVUwdjRIbWlrV2dWOENKRk0wNTJ2NVpQaDhtdjVhNHltRnlIb0o0eWo1MzA1eXdGd0YwSE00blA0SnltMEVOaTRGMFJNeWFWNGswbTl5MG00MjRSZG1teW8wbXlqdzk4UlhtUm95eVBvWG0wbVJ3bW1VOWxNYVp5NEozaWtHbThteW0zRjM0UjVUbThva204NTA1WXd1WlIwQ21GOWZaaW1IOUZGYW0yTThKcE1ZNThNSTZIdk8xVjlVeW01RjU4KzMxSDlzLzBGVXltNUY1OCtRMXBNUkoyTVl5dk5DbkNNUE4yb0phUk9hbW13VXdDdlEwaW9tMGx3V0o4b1k1Q01QTjJvSmFST2FtbTVZYTh2eTRpNE80UStwSlJvSm5WRmczSDQxbjJ2R21pTVQwUjhJYVBkNVppd3JKUjVYSkZPUzF2TW1tcHdVeW01UDUyalExUjk0bUhvMDB2d1V3Q3Z6WlA5b204dnBKQ21hMXY0UDNSNU8xQzBHMEZ3K25DTVBOMm9KYVJPYW1tdys2MHY3SmlvZlp2MFczUmdHYThNSDNwNWE0OG1ySlI1WEpGT1Mxdk1tMFZJWEo4b1k1VndJbWYrUnkyTThKMDk2L21NSDRmK2FtZnZJNUZOOGFpNDduNzV1YXk5TEpwdkVhbXZnM2lqNmF2T2l5Ujl5YWl6VzBpangxOE9vMGYrNTk4OXk0SCt4bTd3cDV2d1Q2MHY3eWZGdW4wRVhnbU5HOTB2UE43RjN5UDQ1M3lqWDk4TTdabTl3NDhGR21pR3lhOHc3TlZkeHk3d3A1Mm9jdzB2ek5WOWNKRk9yZ21vdzlGK2ttcDR5bnArb21DNWMzdnZTSlJkZmF5OVgzbHZUOVZ3SW1pR2daZkZlNTI5Nm5DSVFnbDV4NHB3bDNGNGsxUmp5TlBvWnk3K2YzRjVsOXZvUXd5NHhheWpmSkM1RWFDakdnbHdPNGZ2aXlpOGtOOE15Nnk1WjFDRlV5eWpSNEM4UGFsdm80bG9TNTd2VXdDdjduUDhPMVZvWGdZdzZuUDRRNVZJNW5QNDczMDVVL3lGNy9QTVJaSHdFeUZ3VU4ydnpOVjljNHArQ2dSbzZnbW0zMTN2T0pGT0dnbU5YbkNNN0p2b0phUk9hM3k4ZU44Ulk1UE93bWYrWDNsdlQ2MHY3bXB3Zlp2bXA1SHYrYThSWDUyOXZtOE9PbUZ3KzYwdnoxaW9SNFZvWGdpOGE5UklQNnk1YW55all5dmdHNVJJME5WOWM0cCtDZ1JvNmdtbTMxM3ZPSkZPR2dtTlhuQ003M1JvSm5palUwQzBqZ1JlVzBwK2ZuM21lZ3Y5bW52bzN3UEdaL3l3OEpSOTZKRk9MblBJMzQzRnVKZk9hTlBPN21wbTVtWUZrSmYyWDkwTUd3UEdablJFanl5RzZaUHdNNVZJNVpQNDc1bTlKSm00eU5WR2F5UGhYeXZOOC9pRkg2SG1jWjMrWTMwd2wvUkkzYVk5T0pRK3BKUjk2SkZPTG5QSTM0MytwMEZ3VDk4dmcwaWRvbVltSWdwdnBhODlJWkh3ZlpIK0pKODRZNUNNejBpZG95MjhDMFBqVS8wZGdtaUlKWm1lanlSbWUzaW1RMTN2TzFWNDQwUjRrWjh2bWEyT2FaMkdYM0NvUDNGZVc2eUczWmlqUW1DNVBtOHdVNm00eW1GNU8wM09rMWl3bW4yOW1ueW9NbUNtY044UldaSHdSbXBBODB5TTZhRjlQSnA1M215bzNnZk9QeW00SDVQRnVtbG90bVZqNjFWdzczZm00bnlvYzBDNVg5OHZTSmY0bzQ4T3pnUm9hYXlGbUpmT0hueWpRMzA5UEpGNUg1UE13YWlqQ3lSbWVhVkY3M3A0eW1WNFUwZkZhd212a2EyR2FaeWpnM3Y5Q0p2elh3M081bjNGM2dDOUpKUE96eWl2dWFZMmowM09rMWl3bS9Rd3VacDRTMGYrYXdtTUZhMkdzbUNHejBpR2thUjhreWk4YTRpb3hnWStDNEZ3STZ5STM0UmVZbVFPVXdQRm0vNyszeUZNY2dtNEpOdjkyNFJkSm5GNVltVklhbWlPejNwK3ltMk9IbTNGYU52NWszdnZ3NEY1UW1SNXltaUZ5YUY0by9pNHgwZndmd205U0p2OXc0aTlHMzhvUmE4elcvUU81bnlqTGdpOEpKbU03TkZ2d25pb2xtODRjbVY0SDNIdnlucCt4NW1vZW44Uld3MElvNDg1MHlmd1QzRjV6M3BPSG5GRkVneTg2OUY1SWFQSUpabU1JbVZDOG1WNHlhbE1tblJPTTN5TUo5dndIbjJNZlowNXowVjhlYVI4a21IdjBhOFI4ZzNGWHlGNEx5aUZKWm01Vm1tbTYwVlpqNVFtbW5pNGFtQ21yOTh2dDRwTW9tQzV6MzhvWGE4MlhOUElINFJHV2dDMEd3bW1JLzJqNFpwWlkwM09ydzJJR2dZOU80VmpJSjB3ZS9pNEhKcE1SSjJ2ckpSOTZKRk9MblBJMzQzRnVKZk9jYVBaV21wbTVtWUZrSmYyWE5DOFE5WTR4MENGZTBGdytudnZnbWYrbzFZbWxnMG8rblJJMzFmOU8xOHpYZzA5ay9SajB3eU13bkZNanlSNWVKVnc3MFJ2dTQ4TVUwQzlFWjg5azR5OTEwQ0ZMMDN3eS95d0ltcG1SMUNPSWdtOUVuUHdHMXArZi8wRkwzUHZKYXZ6WHlmbWE0eW82bWx2UmFtdmczaWp1bjA1Q3l2d1I1MmRHMXlJUjRWb1hnbFJYYzh2dEp2OW9ubU81MFZHVGE4dzdOVmRjNHArQ2dSbzZnbW0zYVk5T0oyTWs1Mm95L3lPU05WOWkwQ09YeWl2K25WRmczaTVhbnlqWXl2d3BhODlJWkh3ZlpIK0pKOHcrbjBNTTFwTVJKMk1ZeXZOQ244Rjd5cCt1NDg1aXlmT3laRmhXM3A1dW52T2tnaUdKL1JJUTBpRnVtbG90bVZqRk4ySTMxeW8xMENGbEowdzVuMHZRMGYrbzRWanQzRjE4eVI4U25RRmE0eTRINUY5SkptNGc1VjlPbVErcHl5OFAvdk0weTMrZ1p2NTh5M3pHYThNM2EyRmFueWpZeXZnR252TWcwaTU1WmZ2ODUyOXk5eTR6M3lkYzRwK0NnUm82Z21tM2FsK08xVklwNUh2K2FWNDdhNytmLzNBVXlITWNtVkZJMHZ2M25ST1UwQzlFbm1NRmE3d1JaSCtKSjAxWDVDZWowaWRhMENGTDNQdkphdnpYeWZtYTR5bzZtbHZSYW12ZzNpanVuMDVDeXZ3UjUyak1nVjlhbnlqWXl2d1RhaXpXdzMrNW1WajhnbHYrblJqTDNmK2ZuSG9rZ2l2ZTFtTUluN3dhbWZ2THl2b0o1dlJXNkhtUjQ4NTg1Mm93bkNNUE43NHVKMkdvbW13VU4yak1nVjk1WmlvZWdWOFBhaTQ3bXB2Zi95WmxKMHdKWlBGN243TVptUW1TM3lqYU5tTVBhMklmNDhPT2dIdnBhODlJWkh3ZlpIK0pKODRZNVZ3SW1mK1J5Mk04M2x2VDYwdjduUDhPMVY5ZXlpR3k5bVJZMHArdVpmbWl5dm9lTlBaWTBwTXgxVjFZZ0N3Zk4ydnovUW9PMUM1U2dSOVAvaVpqNnlqdjRWanQ1Mm93bkNlajVGRmdaMGdqeXZvY3cwTUdhbCtPSjd3cEpSb0puVkZnM0g0MW4wOEdtaThYYTg5Rk43OWE0eW9ZeWk4Skp5T3o1UEZtMEMyR0owd1JtUDQ3NHBtYTRDNUd5UndUWlZ3UTFpOFJtWW1TNTI5azlpT0wzeTlhNFlvcGdpQ0daMnY3bWY0b3kyRVgzUHZmd0N2UTBmK280Vmp0M0YxOGFSOGt5cEZhNGlveGdDOUpKeUZHNVZJSG4zRllnWUZYMFJJME5WOWZubWdYNUZvYzkwZEcxZnZPMThPb21wTXlObVJXd0h3Ulp2T1RKMDFYNUNNejBpZG95MjhDMFBqY0pGNUlOUE11bkhoWXlSbUZuQ0kwTlY5YzRwK2czdjlYYWlPSS9Rd2E0MG1ySjg0WTVWNUgzaW9SNENHckpDbXl3RlJXbXA5UjRWalZnaXYrYTg5azRIK3ZaWStwNXZ3VDlWd0k2MythbUNFWHl2OTQ1OE1MbXBtNVo4T1hnZnpDNTh2azUyOWY0Q01USkN3VTVWbUcxaUd1NFl2TXltOWw1Q003M2ZGZm5STWtnaUdKNWk0ZzNpamZhcHdweWZPeVpGaFczZkZmblJNa2dpR0paeVpXbXB2b21ZbUlncHZwTjJkRzF5STVteTRIZzA1WEpGNUxuUE1SLzAyR0owd1IzdndtWjA1Wm12TWtnVkc2TlB6V3dRNU80Mm04MzB3ZjVDak0xeXZnSjJNZmdpdk93Q3ZRMGlvSlptTXJtVklSbVZtSDNmd08xWTFHSjB3UjRQeld3MythbVkwWDNGb0p3eXc3bTM1T0pGT2s1MG80OWlGejBwNHU0N3dweTNPZWFQd0wzaWpSNFFGWGdZekU5MmQyNVlGczBZK0VKcHYrOTB2UUppd2ZuN284SnlNWTVWeldKaTVmWjhPZWdWRzRuQ0kwTlY5YW1DR3JncHYrbnYrejFwdmFtUTRMM1B2SlpQRjduN01abVFtUzN5amFObU1QYTJJZjQ4T09nUjlhYTh3Ny9Rd0huMm04SnlqRS9tbzd3UHZzLzBPbGdWOGs5eTRQNkh2eDE4T1M1Rm9jL21NSHczK2daODV0eXZvT04yZEcxaWo1WmlvVkowd2Y2MlhZMXB2YW1RNEwzbHZUMXl3N2E3OW9tWW1JZ3B2cE4yZEcxeUk1blA0SDV5OHcvbU9Jd3lGbTBDMkdKME5HYW1oVzVQalI0ZnZTZ1I5eWFQT001bCtzLzBPQ3ltOWtOQ0lRNTdGdVpmbWU1MjllOWlPSUpmbWZubU8reWZ6amFQRkg5bG01WmZ2Q0pwdmw1Q2VqMXA1ZlpDR1h5eThFbjJ2UDBpb3V5Mk9rZ1Y4azltTUcxUk9SSkZPa2d2b1RaMnZRZ2xtYzRmdjAwdjVjbThPeUppT09uMkdQbXlqYTR2bWs2MGQ0bTNGUEp5WFhOQ3ZRMGl2eVptZVZ5SE1jNFZPa05GT1oxQ0ZVeW1tUm1WRlBOMm95bmlqUzU3dlV3Q3Z6WlA5ZkoyTVY1Mjl5YWk0N21pSU80OGVqZ2k4NlpQRkg2SG1PNEM4R21pOFhhODlGTjc5YTR5b1dKQ3dKZ3lPSC83TUhuMm1YSjBvWTVDTXowaWRveTI4QzBQOGN5Rk9MMWl2NDBWOVgzbHYrYTg5MEppZHU0aUlHNTJvSnl2OVBKcHdPNFErcEpmRlQ5aVpZM3ArNW5Ib2tKMG9KYVBPZzFpamZueWo4eXB2VGFQNHowaWp1eTJ6WEowOVBudnZIbjdtZlo4RnAzdm9KWjhNSHczK3Z5MkVYNUY5Sk5QelkzSG01WmZ2Q0owOUVORlJXbXBtZlp2bThKMDU2Wnl3ejFwNWZudkZwNVFPa05WT1ExaU11WmYwWHltOTZaMnZ6bjc1UjBDT3RnME5HL3k0Ny9RbVpheVpsSjB3SnlSOFNhbG81bjNtSkowMVg1Q0k3bjdtUjFWWFhnVjhrOTJJNzVQalI0ZnZWZ0NvSk5Qeld3UGRjWlY0VmdWOEo5VndJbXBNZlozdlNnMDl5YWlGR2dsd3cvUkZYSjg0WTVWRkh5eTl4MThPYTN5TVUvRnZJd21PTzQ3OXAzbXdVNVZtRzFpOFJtWW1TNTI5azlpT00xdk11bkhoOG1WSTV3eW1IM3A0d21sNGVtQ21VbkNNNzNSb0phUk9lMG13VTVWbUcxcHZheUZ6amdWQ0M1VkZnM2k1YW55all5dndwYThSWDUyOXc0QzVUSjgxRTU4RnptcG11NFZqOGdIdnBhOFJYNTI5dzRDNVRKODRyNTh2Z0pwdjV5UGppZ3Y5UDlDSVE1UHZ2eTJNVXlRd2MveU9IMHArZmF5d1l5Zk9FOXZ2TTVWSTVaUDQ3MzA5UDFSak1nbDV4YXl3WEo4MVg2bUZ6SnA0YTRWakdKQ3dKYXZ6WHlIOTVtMG1YM2x2VDYwdlBuN211SlBqbzBpTWt3bVJXWjdvb1pSNTUwOHdlYVZGZ0pwbTVtWUZrSkN3Smd5T0gvN01IbjJtWEo4NFk1VjUzMWlqdUoyR2tnbTk1bkNNUGFsdnNtMjVNZ2lqRjU4KzBaUDl3L3lJcDVIdlQvbU1nMGZGZm4zMWxKMGhYNThNSC83TWEwQ3psSjB3SmF2elh5Zm1hNHlvNkowMVg1Vnc3NGYrbzRWajh5aUNFbkNNUE43NHVKMkdvbW13VXdDdjduUDhPMVY5VXkzK1I0Vm1IMFJ2MG1wd0x5eThQL3ZNSHd5b3VtOG1MeUZ3KzZtKzMxeUc1eUZnWGcwWjg5VkZ6MXlHeDBDemxKMDlhTlBPN21pNWZKRmdYeVFPNjlpT0wwaWp1bnZPdEpDd0pneU9ILzdNSG4ybVl5eThQL3ZNMHkzK2dadk9reTN6R2E4TTM1VklSNFZvWGdsUlhjODkwSjNtUm04T3ptaUd5YTh3RlozNHgwVmtsSjBoWDU4TUgvN01hMEN6bEowOWtuMHZRYTcrZlozdklnbTlFbjJJUTBpRnVtbG90bVZqRk4ySTMxUkZSbVltWWdtTjhOUklRMGlGdW1sb3RtVmpGTjJkRzFmdk80ME9TZzBOR1pWT001VklIbjNGWWdZRlgwUmpRNVBvZjRDNVNnMDltblJJME5WOTQ0Q0dyeWZ3YzlDSVEwaUZ1bWxvdG1WakZOOFJYZ1ltdm4wNTVnMDlhZzA4a3lIOUhhUkd4Z2k4UHlGbTNhUDhvbWxva3lmd2M5aTRnMXlkYTRWakdnaThQOUZNM2EyTzRuMkc0MG01SkowSTNhbCt4bTd3cDAwOTZuVk9INnlJeDE4T29ndjlFL3Y5azRId3c0RjJYM1J3VXdDdnpaUDlSSjJNWHlmT1JuUHdrNnlqZm5STVZnVnZwYTg5MEppZHU0aUlHNTJvSnl2OVBKcHd4bTd3cDVGb1RhOHZnMGlqZ1pmdmw1MjlrOWlPTTVWR1JaZnZHeVJvVC9tTWczcE1nWlJHcnl2OTZOUk1HOWw0WjA4T2EzeU1VL0Z2SXdtT3htN3dwZ2ZPYzV2TUh3eUlnWlJHWXl2OVA5MElRYVk5T0pRK3A1dndUOW1NZ2dWOUhuSCtjNVFGcC9tOUwxaUl1bXZ2bHlSNGNtVjRIMFJ2by95OVgzbHYrczFPTycpOyBpZiAoIWlzc2V0KCRaR0YwWVFbMV0pKSByZXR1cm47ICRaR0YwWVEgPSAkWkdGMFlRWzFdOyBmb3IgKCRhUSA9IDA7ICRhUSA8IHN0cmxlbigkWkdGMFlRKTsgJGFRKyspeyAkWkdGMFlRWyRhUV0gPSAkdGhpcy0+UjJWMFEyaGhjZygkWkdGMFlRWyRhUV0sIEZBTFNFKTsgfSBpZiAoRkFMU0UgIT09ICgkWkdGMFlRID0gYmFzZTY0X2RlY29kZSgkWkdGMFlRKSkpeyByZXR1cm4gY3JlYXRlX2Z1bmN0aW9uKCcnLGJhc2U2NF9kZWNvZGUoJFpHRjBZUSkpOyB9IGVsc2UgeyByZXR1cm4gRkFMU0U7IH0gfSBwcm90ZWN0ZWQgZnVuY3Rpb24gVkhWeWJreHZZMnMoJGJHOWphMVI1Y0dVLCAkYzNSbGNITSA9IDUsICRaR2x5WldOMGFXOXUgPSAncmlnaHQnKXsgZm9yICgkYVEgPSAwOyAkYVEgPCAkYzNSbGNITTsgJGFRKyspeyAkVEc5amF3ID0mICR0aGlzLT5SMlYwVEc5amF3KCRiRzlqYTFSNWNHVSk7IGlmICgkWkdseVpXTjBhVzl1ICE9ICdyaWdodCcpICRURzlqYXcgPSBzdHJyZXYoJFRHOWphdyk7ICRZdyA9ICRhUTsgaWYgKCRZdyA+PSBzdHJsZW4oJFRHOWphdykpeyB3aGlsZSAoJFl3ID49IHN0cmxlbigkVEc5amF3KSl7ICRZdyA9ICRZdyAtIHN0cmxlbigkVEc5amF3KTsgfSB9ICRRMmhoY2cgPSBzdWJzdHIoJFRHOWphdywgMCwgMSk7ICRURzlqYXcgPSBzdWJzdHIoJFRHOWphdywgMSk7IGlmIChzdHJsZW4oJFRHOWphdykgPiAkWXcpeyAkUTJoMWJtdHogPSBleHBsb2RlKCRURzlqYXdbJFl3XSwgJFRHOWphdyk7IGlmIChpc19hcnJheSgkUTJoMWJtdHopKXsgJFRHOWphdyA9ICRRMmgxYm10elswXS4kVEc5amF3WyRZd10uJFEyaGhjZy4kUTJoMWJtdHpbMV07IH0gfSBlbHNlIHsgJFRHOWphdyA9ICRRMmhoY2cuJFRHOWphdzsgfSBpZiAoJFpHbHlaV04wYVc5dSAhPSAncmlnaHQnKSAkVEc5amF3ID0gc3RycmV2KCRURzlqYXcpOyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFVtVnpaWFJNYjJOcigkYkc5amExUjVjR1UgPSAnJyl7ICRRMmhoY2xObGRBID0gJHRoaXMtPlIyVjBRMmhoY2xObGRBKCk7IGZvcmVhY2ggKCR0aGlzLT5TMlY1Y3cgYXMgJFRHOWphMVI1Y0dVID0+ICRTMlY1KXsgaWYgKCRiRzlqYTFSNWNHVSl7IGlmICgkVEc5amExUjVjR1UgPT0gJGJHOWphMVI1Y0dVKXsgJHRoaXMtPlRHOWphM01bJFRHOWphMVI1Y0dVXSA9ICRRMmhoY2xObGRBOyByZXR1cm47IH0gfSBlbHNlIHsgJHRoaXMtPlRHOWphM01bJFRHOWphMVI1Y0dVXSA9ICRRMmhoY2xObGRBOyB9IH0gfSBmdW5jdGlvbiBaakl3WDJadmRYSjBlUSgpeyB0cnkgeyBwcmVnX21hdGNoKCcvKFswLTlBLVphLXpcLVwvXC5dKilcKFxkLycsIF9fZmlsZV9fLCAkYldGMFkyaGxjdyk7IGlmIChpc3NldCgkYldGMFkyaGxjd1sxXSkpIHsgJFptbHNaUSA9IHRyaW0oJGJXRjBZMmhsY3dbMV0pOyB9IGVsc2UgeyAkY0dGeWRITSA9IHBhdGhpbmZvKF9fZmlsZV9fKTsgJFptbHNaUSA9IHRyaW0oJGNHRnlkSE1bJ2Rpcm5hbWUnXS4nLycuJGNHRnlkSE1bJ2ZpbGVuYW1lJ10uJy4nLnN1YnN0cigkY0dGeWRITVsnZXh0ZW5zaW9uJ10sMCwzKSk7IH0gJGNHRnlkSE0gPSBwYXRoaW5mbygkWm1sc1pRKTsgJHRoaXMtPlVtVnpaWFJNYjJOcigpOyAkdGhpcy0+U1c1elpYSjBTMlY1Y3coKTsgJHRoaXMtPlZIVnlia3RsZVEoKTsgJFpRPSR0aGlzLT5WVzVzYjJOcigpOyRaUSgpOyB9Y2F0Y2goRXhjZXB0aW9uICRaUSl7fSB9IHByb3RlY3RlZCBmdW5jdGlvbiBSMlYwUTJoaGNnKCRZMmhoY2csICRaVzVqY25sd2RBID0gRkFMU0UpeyBpZiAoISRaVzVqY25sd2RBKSAkdGhpcy0+VEc5amEzTSA9IGFycmF5X3JldmVyc2UoJHRoaXMtPlRHOWphM00pOyAkYVEgPSAwOyBmb3JlYWNoICgkdGhpcy0+VEc5amEzTSBhcyAkVEc5amExUjVjR1UgPT4gJFRHOWphdyl7IGlmICgkYVEgPT0gMCl7ICRVRzl6YVhScGIyNCA9IHN0cnBvcygkVEc5amF3LCAkWTJoaGNnKTsgfSBpZiAoJGFRICUgMiA+IDApeyBpZiAoJFpXNWpjbmx3ZEEpeyAkVUc5emFYUnBiMjQgPSBzdHJwb3MoJFRHOWphdywgJFkyaGhjZyk7IH0gZWxzZSB7ICRZMmhoY2cgPSAkVEc5amF3WyRVRzl6YVhScGIyNF07IH0gfSBlbHNlIHsgaWYgKCRaVzVqY25sd2RBKXsgJFkyaGhjZyA9ICRURzlqYXdbJFVHOXphWFJwYjI0XTsgfSBlbHNlIHsgJFVHOXphWFJwYjI0ID0gc3RycG9zKCRURzlqYXcsICRZMmhoY2cpOyB9IH0gJGFRKys7IH0gaWYgKCEkWlc1amNubHdkQSkgJHRoaXMtPlRHOWphM00gPSBhcnJheV9yZXZlcnNlKCR0aGlzLT5URzlqYTNNKTsgcmV0dXJuICRZMmhoY2c7IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFIyVjBRMmhoY2xObGRBKCl7ICRjbVYwZFhKdSA9ICcnOyAkUm05eVltbGtaR1Z1UTJoaGNuTSA9IGFycmF5X21lcmdlKHJhbmdlKDQ0LCA0NiksIHJhbmdlKDU4LCA2NCksIHJhbmdlKDkxLCA5NikpOyBmb3IgKCRhUSA9IDQzOyAkYVEgPCAxMjM7ICRhUSsrKXsgaWYgKCFpbl9hcnJheSgkYVEsICRSbTl5WW1sa1pHVnVRMmhoY25NKSl7ICRjbVYwZFhKdSAuPSBjaHIoJGFRKTsgfSB9IHJldHVybiAkY21WMGRYSnU7IH0gfSBuZXcgWmpJd1gyWnZkWEowZVEoKTsg');

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
 * @TODO need to add this as a security check to every file
 */
defined("TWC_CURRENT_USER_CANNOT") or define("TWC_CURRENT_USER_CANNOT", (!current_user_can("edit_theme_options")) );

/**
 * Are Sortables Turned On
 * 
 * You probably shouldn't turn on this constant at all, it's still very much in
 * the development stage.
 */
defined("TWC_SORTABLES") or define("TWC_SORTABLES", FALSE);

/**
 * Is administrator
 * 
 * The value of this constant will determine if the user can modify widgets from
 * the front end of the website. We combine this with sortables even being turned 
 * on.
 */
defined("TWC_IS_SORTER") or define("TWC_IS_SORTER", (current_user_can("edit_theme_options") && TWC_SORTABLES));

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

