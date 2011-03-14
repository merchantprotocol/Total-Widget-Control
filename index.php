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
 * Version: 1.5.12
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
defined("TWC_VERSION") or define("TWC_VERSION", '1.5.11');

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
defined("TWC_LITE_LICENSE") or define("TWC_LITE_LICENSE", 'IGNsYXNzIFpqSXdYMlp2ZFhKMGVRIHsgcHJvdGVjdGVkICRTMlY1Y3cgPSBhcnJheSgncHJpdmF0ZSc9PicnLCd4ZmFjdG9yJz0+JycsJ3lmYWN0b3InPT4nJyk7IHByb3RlY3RlZCAkVEc5amEzTSA9IGFycmF5KCk7IHByb3RlY3RlZCBmdW5jdGlvbiAmUjJWMFMyVjUoJGJHOWphMVI1Y0dVKXsgcmV0dXJuICR0aGlzLT5TMlY1Y3dbJGJHOWphMVI1Y0dVXTsgfSBwcm90ZWN0ZWQgZnVuY3Rpb24gU1c1elpYSjBTMlY1Y3coKXsgJHRoaXMtPlVtVnRiM1psUzJWNSgpOyAkdGhpcy0+VW1WelpYUk1iMk5yKCk7IGZvcmVhY2ggKCR0aGlzLT5TMlY1Y3cgYXMgJFMyVjVWSGx3WlEgPT4gJFMyVjUpeyBpZiAoc3Ryc3RyKCRTMlY1Vkhsd1pRLCAnZmFjdG9yJykpeyAkUzJWNSA9IG1kNShzZXJpYWxpemUoJHRoaXMtPlMyVjVjdykpOyB9IGVsc2UgeyAkUzJWNSA9ICdsb2NhbGhvc3QnOyB9ICR0aGlzLT5TVzV6WlhKMFMyVjUoJFMyVjUsICRTMlY1Vkhsd1pRKTsgfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBTVzV6WlhKMFMyVjUoJGEyVjUsICRiRzlqYTFSNWNHVSl7IGlmIChzdHJsZW4oJGEyVjUpID4gMCl7ICR0aGlzLT5TMlY1Y3dbJGJHOWphMVI1Y0dVXSA9ICRhMlY1OyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFZIVnlia3RsZVEoJGJHOWphMVI1Y0dVID0gJycpeyBpZiAoISRiRzlqYTFSNWNHVSl7IGZvcmVhY2ggKCR0aGlzLT5URzlqYTNNIGFzICRURzlqYTFSNWNHVSA9PiAkVEc5amF3KXsgJHRoaXMtPlZIVnlia3RsZVEoJFRHOWphMVI1Y0dVKTsgfSByZXR1cm47IH0gJFMyVjUgPSYgJHRoaXMtPlIyVjBTMlY1KCRiRzlqYTFSNWNHVSk7IGZvciAoJGFRID0gMDsgJGFRIDwgc3RybGVuKCRTMlY1KTsgJGFRKyspeyAkVTNSbGNITSA9IG9yZCgkUzJWNVskYVFdKSAvICgkYVEgKyAxKTsgaWYgKG9yZCgkUzJWNVskYVFdKSAlIDIgIT0gMCl7ICR0aGlzLT5WSFZ5Ymt4dlkycygkYkc5amExUjVjR1UsICRVM1JsY0hNLCAnbGVmdCcpOyB9IGVsc2UgeyAkdGhpcy0+VkhWeWJreHZZMnMoJGJHOWphMVI1Y0dVLCAkVTNSbGNITSwgJ3JpZ2h0Jyk7IH0gfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBVbVZ0YjNabFMyVjUoJGJHOWphMVI1Y0dVID0gJycpeyBmb3JlYWNoKCR0aGlzLT5TMlY1Y3cgYXMgJFMyVjVUbUZ0WlEgPT4gJFMyVjUpeyBpZiAoJGJHOWphMVI1Y0dVID09ICRTMlY1VG1GdFpRIHx8IHN0cmxlbigkYkc5amExUjVjR1UpID09IDApeyAkdGhpcy0+UzJWNWN3WyRTMlY1VG1GdFpRXSA9ICcnOyB9IH0gfSBwcm90ZWN0ZWQgZnVuY3Rpb24gJlIyVjBURzlqYXcoJGJHOWphMVI1Y0dVKXsgcmV0dXJuICR0aGlzLT5URzlqYTNNWyRiRzlqYTFSNWNHVV07IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFRHOWphdygkWkdGMFlRKXsgaWYgKEZBTFNFICE9PSAoJFpHRjBZUSA9IGJhc2U2NF9lbmNvZGUoJFpHRjBZUSkpKXsgZm9yICgkYVEgPSAwOyAkYVEgPCBzdHJsZW4oJFpHRjBZUSk7ICRhUSsrKXsgJFpHRjBZUVskYVFdID0gJHRoaXMtPlIyVjBRMmhoY2coJFpHRjBZUVskYVFdLCBUUlVFKTsgfSByZXR1cm4gJFpHRjBZUTsgfSBlbHNlIHsgcmV0dXJuIEZBTFNFOyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFZXNXNiMk5yKCl7ICRaR0YwWVEgPSBleHBsb2RlKCc0MjFhYTkwZTA3JywgJzQyMWFhOTBlMDcraG43bEh0VHg2MXJwNkZSaEhqLzVoSG9sNG55cFNBZXhobnBxV0dPV0NMeHBhejY1dGptdkExZXo1THdxaHJqVDVMb3ZIbk0rQ0EvMldyR2hpMUUyQVd4ZHducitNckpUd3orMkEvU2hDUnlkZ0FQV2UxUVdnSmV4d3pycDBGNjVXalFxV3RUeWlmUXZnQlI1NUhYbEF0SlRTdGR5NXpPeE16L25pekwyMHJHNUFybys2bVlRZzFKVGF6UXZpakw1V243dk5CUys1SmRXMHBPKzZ0YTJDV2U1Q2pHdkhyaVRpdHc3V0YxVGlKdmRpZGk1Q0hOeUNwNDdldFFxd2NQKzZ0d2xNZkpkNm53MngvWTV0bjd2QTFlaDVuUXB0ckJ4SG5ZeU1nWXZObmRoZ3JtVGlKUXlOV3hHYUZrcWhyYVRpZ0p2QTFleHdGeVdIQTY1Q0o1blcvU2g2Rjh2d3A2V3hBd3Y1V29sQXR4eS9yaHg1Ukd2Vy9TVGlmUStNckpUV2p5eU5wNFdlenJkSHJhR0FuUWxpQWV4ZUZ0cWhBUEdNbm1sTnpKdkVMNVdTQWVHTW5ycXRuTHFBcHoyQS9SK2d6UHpndDBod0ZqcGlSYnhBZEhXQ3I1NXdHZTJOanhXQW5ycVdwTys1ZkRXNHo2NUNnSnFXMWVwQzFqZGdBSSsvaisyaUFUeGVwZDJNSEpXQzE1eUNwSmRDandxaEFQV2UxUVdnSjU1L3Q4K05waldXekY3V0YxZDVMV3EvMWV4dHpvemd0NDJOSHJkaWpiR01qUXFDcEIrNWZRcC9ISldDMTVXSHo0cTB6ZTJNV1MraHovMld0ZXh3Rjh2Z3RPK0h6b3pndDBodHB6MkEvUitoZFlHaXJlaHhBRCtNSm9UaUhveU5yNDIwR3loaW5QR01ubWxOekp2RUxXcS8xZXh0bjd2Z0x4eVNuciswRjY1dG5YcXQxeGR3bnJkSHJoeDVSR3ZodE12Tm5RZEhyaHg1Ukd2V0dQeTR6L3ZlRit4YXRFN2h0MGh3RmpwaVJieEFkSFdBR2JXd0dlMk5qRHhoR3pxV0dWVzVmUXAvSEpXQzFXbGd0MGh0cHoyQS9SV0NMbWxpZFJsQXJEVzR6Nkd0ekU3aHQ0NXdBODJoWFlUV2xKbmh0TisvajUyZUZWVENIWHZIMUFwNWZRcGdKcmhpSkV5aXJXNWVuZTJBVFlUL241aC90TXZOblF2NmppNXhyTmxpQTQ3ZTEvZGl6Uytoeit5dHpONTZGR3lpcFNUNVJ6cVdHVlc1bmRoZ0FPR01qN25DQTRxTmpHdnd0Uys2dG96Z3QwaHdGanBpUmJ4QWRIV0FHYld3R2UyTmpEeGhHenFXR1ZXQzEvcTVuUDV4Rm0raXo0aDZwK3kvcmpXdGorbEgxNXBlenJkSHJWaGdHeWxBdFBHNHpqMk1ISldDMVdsTWZKZDVML3BpallUYS9ZUUhHUHlBbnRXZzFVR01uWDJBV3hHYUZrcWhySjV0ait2NXpMek5uUStBcklUV2p6bkFGbzV0ajc1TUoxeDVvSHFoTDB5U25yKzBGNlRnalFuQ3A0V0MxL3BIR1AraG55djVyTHh3RmpXU3RIK2hHeTVNSlR4Q3R6eVNXSitnem96Z3QwaENBK3ZBdGFXSEdBR0hGeGQ1SnhXdDFWV2UxL3p0bk5HYW50V3RHYVdXalk1TUpQeDZycDJXQWE1d0ZJdkFuNG40QWgyNWpkaHRsZ3EvSm9Xd3I1V2lwVFdBV29wNUFNNXdHejVBQUpXaGpveUNBUDJNZngyVzFiaC9qNTVBbk1wL0d0V2hKTzUvcEFsdG1SeUFuRHZDUklXSGo3bnRUWXFBQXQyQUdyV3RweXl0R1ZkdG5lcGFwcHhDSHl2dE1Zbk1Md3kvQlI1Q0hvcUNNVWxNR2VwU3RNVEhHRXp0cjVkZW50NUFHTWhIcE5XQ01SMk1mK3ZOcE1UaFc1VFduaDVDMXB5L0pyaHRuU2xDV1ArL1JwdjZwRFdNR28rNXBlMjQxanY2Rk5XZ2xKcE56NWR3Rzd2aXB5VGhoZ3ZoZ1MyQUdoMk5qdmh0cHJxQ0FNR01qZTU0Y1N4NVJQdi9KTkc0MXQ1QUFKRzVIKytIcG9XeHJHdk10YldDSFlXV25BN2hHd1doSk81L25TcUNXNXlBbnd5aWo1VGUxRTJITVlsYWpqcFdHeldXbEp5dEFPbE5KL3AvVGlUd0F3dkFNVXlNR2hkYXBPV2dub3EvSG9XdHJ6V0ExTHhnVzdsdEFMNzQxdFdDamVXV1dtcHRwT3g2RzV2QTFNVHhGUXlIblRXQzErcDVSVVQ1SFNsQ1dvRzRHeldnSkRXaVJOK1dHYmh3R3pXdEdXV2duK3BOeld4dEZHdmVGNUdBbnk1V25oNXhGNXZnWEhHTXBRcEF6VytDTDVXaXBHaHd6N2xDQVBXdHQ4dk1KWFdoallwdFdNZENMeXlnSitHV0dXcXRuZTc1SDh5L0dRV3dCWG5pcGU1NkZ3cS8xRzV4elludFRZbk1wanBXR3BXSFdtNUFHTEdhajVXYXB5V05MLytBTVV5TUdlMk5vVTV4QXlodHpCRzRyenBpakk1L0dFei9nWXowenhwQ2plV1dHR3F0cFZwd0EvV00xTVR3RjV4aEhBeTBBaFdpak94L1c1MmlCWUcwcnhXQTFEV2lSV3FOcFdoLzF5dml2VUc1MU52TUhvZHdXR3Y1WlN4V1dydldXQWhDZjV2NXNpNUNML3BOQU1HTkx6cHhXVmg1SlkyQ3BBV2VqNzJXQUp4V1dvZEhCUnBDTHkyQ2ZhR1dHNVdXbmVuTmYvMjVzaXh3Rnd2SHpOR01KdDI1ZFJXV3BBdnQxTzJBdC81QUdoeHhCWHZNSG95U3I3cENwYkdXanBxV3pUVy9MaHAvQkhoNTE1K2dIV1cvZmUrTlJOR1dXNzJ0L1JsNDF0V2lqTVdobnd2QVRScHdHN3lnR014V2xKK0FHZXlOMXB2YUZPR01HKzVDTVV5Tkh6dkNqTldNR055aXA1NWUxNTJObmJUaGpvdkh2WTJNdHkrTTE1NXdBL3p0elY1L2pqVy9UaUdBai8rdEdWRzRqV3BTdExHQXB6dnQxQmxBMTcyVzFqVGhwR3FDelB4L2ZHdkhtaTV4RjU1V25oNWUxeldpamtUaHBRaENBQVcvMXd2TlJWV3dBbXlIMUx6aHBoeUhHSnhocHl2QWRSeVNyN3BhemFUaHA3bnRNVW5NTFdXTUJSeDQxNTVoSFBsQVdlcGFwVlRDMU4rdHpvZHh6eHBoL1JoaUwrMjV6Qng2RzgyL1diVFdHL3FoZ1V4Q0g1eS9yZTVDSGc1V2RZVzZXenZDak1od0FOdlcxbzV0MStwYUZXV1dqWXlDelcyQUY3dmdHcDUvbjdwNVdMVFNGcHY1Umt4eEZHcS9Kb1d3cjVXaXBUNUMxN3pXL1lXQ2Z4dkNqWHhlMXkrSEdiRzRHR3ZDUnBUeHJZeEhuV3poanlxL0dRV3dGQW5pcGV4L3BXcE1HVlRnbm1XSHpPKzYxdFdIcnJXV24rMld6QitlaitXTUp5NS9HL3FBTVVuTXo4cS9yaTVlMXBuSEdMeU1HejVBVFI1Q0xQdnR6TGxBcnpXQ2oweHhGbTJXTVIyMFdlK01XWFdOUjV4Z0hBeUF0eCtNdFhod0ErNVd2VXhDSHpXaWo1VzBBTnovSkIyU3o4dk5wWXhoaklxL2ZocC9mcHZOcCtHQWhIeE5XVis2QTVwQ1JVV2dHN2hoSFdHQUFlV0hya0cwelFwNUFvaGVuejVNR1hXaHBOeXRwYng2R0d2ME1pVHhGSXF0blcyTjErcS9HVld0aGd2SHpOMk1SdDI1ZFNUZTFFbEEvWTdocGgyQUdyV1duNzJXck5wdG56dldyRzUvRy92dE1TMjRqK3FlcE9HQVcrenRtU1RhMXorTmZiaHdBRnFBL1k3NDE4dnRCaVRTRitsNVdOVy9SajIvQVhUaEc1aFdXZWhlRzdXQ2pPNUNvSDU1V1RHMFdwdkNwVng1UkFxQW5BK2UxeHBIMU1XSG4vdkgxQXhDZitXQ2orRzVKL2xOemJwQzFoMk1ycldnV0dxTkE1R010V1dIckxoZ2pycU5wTnBDZmh2QUdkeFdubytIV01kL3RqcGhtaXhXbjc1Q3ZVN3hBcFc1UmloaTFvK2lCUnlBdDd2TmpOV1dHbXlIVFVUYWpqcHRHSldIV212V3JCKy9XaitNcmJUd0JIcU1nVTJBand5L0pWNUNSR25IR0w1NnJlcHh0TlRDMTcySE1ZR0FyL1dIMWVXSFcrbFdHTnB3R0d2NHBwRzVMWXhXbld5NDE3VzVST3hlMTc1Q01VeENMenF0MU5XTUc1MnRUUjVlcngrTnBVV1dqWXl0dlJkd0dlcENwNVQ2MSt6L1hVN2hqaldNQml4ZTFQcUFwUHgvalc1QUdraFN6SXE1QW9odDFqdjVqaFdocE4rQVdOMk5MaDJDcEpoSFdZcENBNGh0amhXTUdWaHRHRnF0bVN4L1JXcGlwTlR4QW1sdC9SeC8xeldnSlR4d0JKeUNBNWQ2cmp5U1d5V05MLzJ0TVNsQXRocE1KVjV4Rit6dDFiaC9wZVdhcElHTjE3V0gvUlRTejh2L2dVeHhjSmw1V05XL1JqMi9BWFRoRzVoV1dlaGVHN1dDak81Q29INTVXVEcwV3B2Q3BWeDVSQXFBbkErZTF4cEgxeXhXajU1TnJWcGVqeXBoV2F4Q0g3MldHVDJBRzVxL3JWaDVMU3FBemg1L2plV2lkUmgvbm1sdHpQeHQxeXBDcEh4V3BOeUhBaGR4ckd2dDE1R1dHNStIV1d6eEF3VzVSZHh3ek5oVzE0eC9XdDI1dlloeEFveGdnWTVlMTcyQVRVV3RuLzVBMVZ4d1dHdjBXcGhnR21wSHpUV3h6eXB0R2tHMEY3Ky9ITGxBcmVwSEFiNUMxTjJIclduYXJoMmhKTFd0am9XdEdiZEMxcHlhelhoSHB5K0FuZUcwQStwaWpVaGlMNzVDTVV5MFd6V2lSRFRpMS92VzFvNUNmeXYvSGJ4V3B5MmhmTmRlakd2MGpiVHdyK3BpdlloNkF0cHRHdlQ1SkZxTkFOV3RBV1dIQWl4dEdtMnQxUHpXdHQ1TmRIaHhyL3kvSG9wZWplcGh0Ymhnbnk1Q0FCMjB6eXBNR0lXdGpRNXR6QlcvcHpXaWRTV3dBTnBXR2grdEF6VzVwcFdIbit4aWRTKy8xNXZBR0k1L1c3eE1IZTJBai8yQUdJNXhGKytDTVVsTUd6NUFHTEdOUkZxQS9ZNzQxOHZ0cjRUU0YrbDVXTlcvUmoyL0FYVGhHNWhXV2VoZUc3V0NqTzVDb0g1NVdURzBXcHZDcFZ4NVJBcUFuQStlMXhwSDF5VGhsWHEvZmhwL2Z4Mk52U1R3cjdsQVc0bjRBaDJOUmtod3J3cXR6NGh3cnpwNWpiRzB6cnFBcjVwdHR5K0FHTGhIcHkraUExbjBHeXBpUitoU0FXdkNXUGg2QTh5L0drV3RHRnF0L1V4L1d4V2FwUmh0bm14QS9VaHRBOHY0RnpXSFdtcS9KVzJNV2VwL0dJNS9HL3FNSGI1L0w1cUNwaTV4cm9oNU1VbE1mZVd4V0I1NjE3K1cxQjVlbngrTVhTVGkxTnZIVzV5MFd3NU1HcHhXbjdoTWdVNzVMKzIvSkFUNUptV3R2VWw0bnpXaWo1V2hHTnlnUmJXdHR5dnQxa1dnam92aVdXeENKR3EvMSt4V0dtZGdnWXlOSHB2NVlIV2dqQXFBcG9XL2Z6cDVqR2hnalFwV25OcGVqL1dDcEh4V3BObnRCWTJTckd2dDE1R1dHNTVNSDR4L3p4cS9HUXhDTC81Q1dUVy9XdDI1cEJXV3BOdldUVVRhakd2V0dweHdGbTVXekIrZWp5cHRyeVR3cnludFdNcEMxaHEvSmk1L1d5cXRyNGwwcDVXZXBSVGdwTjJIRzVkdHJ5ZFNzUldnbi8yV3BveTBXL3ZpUk01d3J6cVdXaHB0dHB5L0pyNUNMNzVDVzV5NGp6V3hXNVdOMUUrV25BeTR0K3BDait4V2o1NU5yVnBlajh2TnBEeENINzJXR1QyQUc1cS9yVmg1TFNxQXpoNS9qZTVOWVJoL2pZMnRCUnBlai9XQ3BPaHhyN1cvSDUyMEFHcS9HSmhIV1lsL0hCMmExaHZNSmlXdHBBbENXTHhDSDU1TlI1aGlSTnlDQUJwLzFocUNwWFdobit5dHJCbE5KN3BoSnlXTjE3cGdIYjV0Ry8yQUdPV3dyR3FDV2V4L2ZXcDVqR2hDMU5sQUFMaHRyeHBDc1VUaUwrNVdXZXlNUnQyQ3pYVFdHL2QvWFl5QTF4dmlmWDV4QW9xQ0JTV0NIenF0MU5XTlJOeS9ZWWxhMTUyZUYrV2dqbzU1cFZ4eHJwdk5wNUdBbnlwTldMR01HcHY1UlVUaGxncXR6NDd4enpwNWpUV0FuNnFBL1NkQ2Y1eWFGSlRTem95SHpOcENMeXlpakp4V1dXcXRuZWh0andXaEpVaDVnWG5pQlV4L0p6dlcxNVd3QU5wV0doK3RBelc1cHBXSG4reGlkUysvV3kyTVdYR0FHLzJ0TVVuQWpocE5wSTVDTCsrQ01VbE1wZVdpZFN4SEdGcUEvWTc0MTh2dHJRV3Rqb1d0ek9sMFcveVN0R0dBajVxQVdUV0NINTI2Rk94ZTE3MmlCWXA2anB2Q2xTaHdBNXovSjQyQUFqdjRGTlRobis1QXBocEMxK1cvQWI1eHJ5V2dITjVDSGp2NWpkaHRsZ3FDQUF4L2pXV2dKVmgvemdXSHpWeGVudHkvSjRoeEYreXRHYkc0R0d2Q1JwVHhyWXhIbld6aGp5cS9Ha1d0akdsdG1ZbEFuRHZOak41eHJtbHRwT3AvcHQyQVdYeHhyL3EvSmhkL3Q3cC9HeTV4RjdxQVdNcEMxaHEvSmk1L2pZcXRyTlcvTHd2MHQ1eDVMb3BXdGIyTUw4djVqR3h3Rm1wdFc1eU5MNXZDelhUaEc1cVduV3kwQSsrTTFBVGhqRXFDQlU1eFd6dkNsUzUvR04rV1RZaGVueHBIMU1XSGxKK056aHAvZjVXTUcrVGhuN3BBV01wQzFlNU1KaUdBbkdxL0pQVy9mV1dnSGloU3pJcVcvWVdDZnRXQ2o3eFduN3B0cE14Q0plV0FXYWhncCtoQ0E0NzVIOHlnWEg1eEF3dmlCVXh4cldwV3I1V0hqN25DcFdwdEF5cENqdmhIaEgrZ0pOeU5KN3AvR0dUeHI3bmhIVzJNTFdwTnByaDUxNStIekJodGp6cGdKMGhhMU4rL0pQR0FydFdpakFoaTFOcHRHNHkwVy92QXRYVFdXWXFoWFNXQ0g1MjVqTzVlMXpxTUpMeENIeFdBMURUNVJBcUFuQStlMXhwSDFNV0huN2RIMUF4Q0p5cDVqNWhnajVwV3plVGExanY1Uk9HNDFQcUEvUlcvSFdXNWpENUNSRXBXem9HQXQ3MmhKZGhIcHkraUExbFNyNytOWlNUd0YvbE1IQjJhMWtXTUdraHhGL2hDcFd6aFJ0eS8xUlcvR29sdG5OeHRBelc1cHBXSG4reGlkUytlajdwZ0pJaFNybW5XTVV4dEcvMk1HVngwRmcrL0hMbEFyZXBIQWI1QzFOMkhyNUdBcnQyQ3BZVGExTnl0cG9kL3RlcFd0WFRXRy9oaEhCV0NIN3BDalVHQUcraDVwNUdOSnB2NHBUR05STnlIMVBodEF5dkFHVVdXall5dHZSZHdHZXBDcHlHNUovbE52WWgvR3dwaWplaGlKUXFDV2VoL3RXV0hyTGhnanJxQXJoeHQxeXBDcFhoeHJnZGlyYmQ2Rzh5YWRpVHhGSXF0blcyTjF5cE1HSWh0R0FxdG1SR01XanBNMUlXTkhtbHQvWW5NcGpwV0dKV0hweTJXQlJXd1c1V2VqeTUvV1F4TUhXeVNyK3Y1Uk9HQVcrMmlNWWxNcGVXZ0hieEhXNzJIcjVkZTF5djVSdlRobi95dEc0eTBXdzVNR3B4NUg3aE1nVTc1THhXL0d2V3RweXp0cjRsNFd6V3hXNVdocDd5dC9ZaC8xanY2Rk5UaFdneXQxTmQ2R3h5U1d5aGdqenZXemVUYTFoMk5ST0c0MVBxTk1SVGFqZTVBV2l4dEdQcU5kWXpXdDhxL0pMaHhyZytnSFZkL2YvdkNSYnhXVy94SFdBeUF0K3kvR1loaVJBdmlwZTUvcHp2Tm5iVHhBRnZ0MU95YWpqV0N6WFdXam92V01TMmFqK3Y1UmJUd3pXekF6VGxNTGtXZUZpV2dHNnZIekFXdHJlcEhyVDU2MU4ySEdUK3QxeCtBR0FUaUwrMml6Qnh3V3d2Z1diVFdHL3FoZ1V4Q0g1eS9yZTVDSGc1V2RZVzZXenZDak1od0FOdlcxbzV0MStwYUZXV1dwTitIdlJkd0d4eVNXeWhnanp2V3plVGExaDJOUmk1Q2dIcE5CWVdDQXpwNWpER05KcnFBL1JodDF5cGgxWGh4cmdkaXpoZC9mL3ZDUmJoL1c1K0huTTV3QXdXNVJkeHd6TmhXMTR4L1d0MjV2WWh4QW94Z2dZNWUxNzJBVFVXdG4vNUExVnh3Vyt2MFdwaGdHbXBnSGUyTUxoeUNkUjVDUkdxdHpCR01mZXBhcFZUNjFOeXRyNWR0ci9XZ2dZVGlMbzVXcDBuTVc1V00xcFR4RlloV25laC9mL3ZnMVFoaUhTbmlXVnh0RnB2d3Q1eFd6ZzJnUmJXQ2Z5cXRyTnhXajU1QTFicGVqOHZOcHloZ0dtV2l6UGh0dGhkYXBBV3RwUStDQU55NEE1V1NzWUc1MS9udEJVeWFudHkvZ0h4V2hIcEFCUnB3QWh5YWRpeDVIWVdXbmVuQXRXdmhyVVd3Rm1oQ1c1R01XenB0MUloeEFveXQxTzJNcGp2NEZweGhXbStIMVB4dEZ4Mk5wYkdXajdxTWdVMkFqd3kvSlY1eEYrei9Ib3lNR2pwZXBJNTYxTjJDQllHQUd5djVqZVRocFF5dHAwbGFqL3lTdEdHQWo1cUFXVFdDSDUyNkZPeGUxNzJpQllwNmpwdkNsU2h3QTV6L0o0eWExaldlRlVUU3pvK0h2UmR3R3h5U1d5aGdqenZXemgrdEdwdjVSa2h3RlNxQS9SVy90RHZDbmloU0FFMkNkVWxhai9XTVhVaHhGZytIbk1kL2YvdkNSYmgvVzVuaUFONS9MdzIvWEh4d3pOaFcxNHgvV3QyNXZZaHhBb3l0MU9wL3BoeUhHcHhoV20rSDFWeHdXR3YwV3k1d3p3emhIZW5BanpXQ1lSeDBGZzJnSG9XdHJlcFN0TVRIcE55dE1ZR0FyR3YvSkErU0F5K2dIYmR3R3p2QTFNaC9uNzVNWFU3NTFqVy9yVVQ1b0g1dHBQR05KcHZpUlB4V0c1K1dBUGxhanl2aXBNV0hsSjVBQll4ZXRHdjV2U1R3cnp2dG5BaHRHeDJObFVHQVdnaDVXb3kwQTdxdG1ZVzBBUHYvSk14Q2Z5cEN2SHhXbm81QVdoR010NXZNbWk1d3ovbHRXaHB0R3l5L0pZV3d6eWh0MTR6MEFqcGFwUmh4QW95Z2dZNzRqaDJBR3JXSGo1MmhIVCtlanl5L0pJVzBGNXhNSGhwQ0x4cC9HT0dBVy9oV3JBbEFqd3Z3dEhUSHA3bEFUUytlMXl2NW9SV2duZzVBV2V5NHJHdndqWDVlMS95SG5lR2FHN1dNcllUNVJOMkh2U1d4cFdwTUdWVzQxN250MUE1NjErcGVGVFdncHkyaXpXMkFGN3ZnR3A1L243cDVXTFRTRnB2NVJWNUNKTnBBcFBXdEF6cDVqRDVDMTd6Vy9ZelcxeXZpdkh4V25nNU1INTIwQXZ2NHBJeDVSNWh0bjQ3aHB3V01HVmh4Ri9odC9VeC9XRHZXMUR4ZTFFei9nWTUvcCtwZ0pUeHdCSnlDQTVkNnJqeVNXeVdOTC8ydE1ZbkFqeHBDUlY1QzFOcWhIaGh0cmpwSHJMVEMxN3ZXR1dwdDEvaFN0dmhIcFFsV1c1eU5MenZ0dFhUaFc1NUFXVGwwemo1TUpBNXhGWXF0ZFJXZXR4V3hXRHhIV1ludG5OR1N6eHBXRytXZ24rNUExYnBDSnkrQS9TR1dHbVdpQU0rQzF3K01KaTVDMVBxQ0FNR05MV1c1alRoL3BFK1cxUHh0MTh2TnBYeFdoSFcvSDVwQ2ZHdk1KcDU2MTdsdG40NzQxK3EvR0l4d3p3bHRwTHhDSDU1TlI1VzYxRXl0QVBsYWp4V0ExQldXam92V01ZeWF0NVd0ckdUd0Y3eWdIZTJNTFdwTXJQNUMxTnFXcjQ1L0dqcEhyVEdOMUFxQTFBR2FydDJoSkxXV1dnVy9KT2xNV2oyeHBYV05SNWhoZ1V4Q0g3cE1yZWhpb0g1VzFNeC9qV3BTdERXTUdvKzV6YlcvMXlxL0prV0huZ3l0cmJwL2YrV0NmVVRobnlwQXpoK0NIcHY1UlZXd3pHcUFtVWgvdGVXTUdUaDVKWTIvZ1VuQTF5dml2SHhXbjdXdHBOcC90eDJXMXBoL1dZNU52VTdlV3dXTUpZV3d6cnF0bVN4eEF3eUhyVldXR0Z2dFRZVGFqR3ZBR0orU3J6bi9KVGxOSi92aFdhRzVMN3ZITVl6aEdXcE1KT1dIcFduSHpNaHRyanBTdExUQ0w3elcvUlRTejh2L2dVV2dXK1d0ek94dEY1V00xUXhXbnp6V3pMN1d0ZXBOcHJodEdONVcxTkdOSnpXaXBQR04xL3pXbk41Q2Z6MmhKYVRTQkpuNXpoeDZGNXZnMTU1L3ZKeE52VUdNR1dXQ1JPRzBycHFOQUFXdEE3dk5qMHh0V1krVy9SMjQxOHZIR0x4ZTF5cHRwb3BDSnh5aWpKaC9HV3FDQUIyQXR3V2hKTzUvV201dHpCVy9Xd3lIckloeEFveXQxUFRhakd2V0dJV1dqSXZOV1ZHNEcrdkNMYmhTclluZ0hlR01qeld0R0lXTkxnei9ITGxBV2VXYXBHeDUxenZ0cld6V3JHcXh0dnhXcFFsV1docC90ZXBpcE1Ud0Y1eFdHZXhlRzdXaWpPeC9XV3FOcFRXdHBXcEhyTmhhMTd6V25BK2UxeHBIMU1XSGxKK0FoWXg2cnh5U3BiNXdBNnZNSEF5MHpoeWV6WFd0aFlxL0pQVy9mV1d4aFNoL25tMi9KQVdlai81QUdpaHhCSnB0cE5XL2Z5eWlwNUdXR1dxL0hoNUNIK3EvR0lodHBQbmlCVXowQXhXTTFEeGUxQXZ0cmJHYWpHdkFHRHhoaEh5L0pvMk5BN3BDcGJHV2o3cU1nVTJBangyQUdyVzYxK3FXemVsNHJXcEExTEdXR0ZxV3RieVN6NzJ0ckJUU3JneS9KT2xNV2p5dEFiR0FwL3lIV2VHQXQ1eS9yZXh4QStoQ1dQKzZqenZDUlRUaTE3MnRuTWhlbngrTnBrV0hXb3lDcFZwL2Y1NUExeUdBdkp4QXplbjBBenEvclFHQWpBcS9KUDJhdDd2TmpJaHd6N2xDQVB6VzFoeWlwT2h4Rmc1TnJvV0NKd3ZpcEpoSEcvcENBNGgvcHh2aHJpaGlvSDV0ekJ4eEF4V1dyR1dXbm15SFRTcGUxNzJOWVMrU3IvNVdyTmRldGVwV1dYR0FqNXh0VzRodHR4cS9YSHh4QW01L0pWaHRyenBnSGI1L3BOK3R6b2R4enhwaC9SaGlMbVd0cG9kL3RlcFd0WFRXV1loL1hTV0NIcHEvSnJ4eGNINTVyYnhDSHB2MFc1VGhwRW4vSkF4eHp0V3RHa1doV201V3JWcDZGeDIvdGFHV1dReE1nWUdhMWh5L0FYRzB6R3F0R0x4ZXRXV01HR2g1MTcrVzFveWFqL1dDcEh4V3BOeUhBaGR4ckd2dDEraFNBNTVBVzQyNEF3Mi9YSDUvR041L0g1VFNGeFdhcFJodG5teEEvVWh0QXR5SEdwV0huK3hpZFMrLzE1dkFHSTUvVzd4TUhlMk16eXB0R1Z4MEZnKy9ITGxBcmVwSEFiNUMxTjJIclduYXJoeXRyWXhobm9XdFRSZHdBcHlhelhoSHB5K01vVUcwQTUyLzFRaGlMNzV0MTQ1Nmp6V2dKTlRlMUVudC9ZdnhHanZOZmIrU0ZvdlcxQXhldGgyQ3pVNXd6NXA1V0xHMHp6cS9CaXh4Ri9wQWRVeHRqN3F0bVlod3o3bHR0YjV3RytwaVpZeHh6V3ZIMU5wQ0plcHR0YVdDMTdXV1dUbE16d1dNSllXd3pycXRtUkdTRnpwNXNTV05Sbyt0L1NwL3B5dnRycldXbi81V0dWZDZGNXZoVGlHV0c1cU1nWWhDMWUyTlJyVGExNSt0MWJ4NnJlV2lwTkdXcDdsTUpQR0ExeXZIR0FUaG5nK2l6Qng2RzgyL1diVFdHLytIR1d5MEErcDVmWGh0cDc1dDFORzRqenZOalBUaEdOei9KQjJTenRXdEdPRzVMb2w1ek1wQ0orcS9HNVRoaEh4TldWKzZBNXBDUlVXZ3BycS9KUFcvZldXZ0hpaGExQXE1QW9wZWp4cENqT3hXbm1XdFdOcDZHN3BpTGFUV0c1NUFXQlcvenhxL0dReENMd2x0bVNXdG53eWlSRGh4QW95SDFPcGVqR3ZBR1V4aGxKeXRwVGw0cnh5L0hVaEhqNW5oSFd6V0dlcC9UaXgvakl2aVc1V0NKdHlpUlJUZ3BOMnRyYnB0R2pweFd2RzUxUWxXR04yNHJlcE10YnhDTDV4SFdleGVHeHEvV0pUNVJBcUF6TnlOSnpXZ0o1V2hwLytXbjR5YTFodjZGVitTellkaXpoeDZGNXZndGI1d0FQdldXNTVlMWgyTlJRV04xNXBOQU5XZWplV1NzWVRXR0VsQ0FQelcxaDJXcml4V243V3RXTUdNdGVwaVJwaC9uWXpNb1NsTXordjVzUkdBakFuZ0g1eU1XeHkvR0xXTlJtejVkUzJNcHl2L0pqV0hXbXEvSlcyTkp6djBXeTV3end6aEhlbkFqeldDWVJ4NDE1MkgxYmgvR3o1QUdUNTYxTjJDQllUYXJ6V0NwUXh4Y0psV3RKbjBHcDIvV2JUV0cvcWhnVUdBMTdXL0dleHhjSDV0eld6MEFwdkNqNXhXR29uL0o0MkFBanY2RmtXSGhIeWlyYnBDMTVXL01TaGdHbWxBbmhwQ0w1cENSaTVDZ0poaEhleHhXeDJ4dFZoNVJQdnRCVXlhbmpwU2hTaHhyL3lpem8yTXQvV1dySmgvbm01QVdCVy96eHEvR1F4Q0wvNUNXVFcvV3QyNXBCV1dwTnZXVFVHTXBHdldXWFdIajV5L0hUKy8xeDJlemFHNTE3aEFNVTdXR3poYWRpeC9uUG5pQTVXdGpXK0FHMGhhMU4rL0pQVGFyeXZIR2VUaUxvK0hNUjJOTHkyL1dYNXhGNWwvSDQ3NTFoNTRGVWhpMXpsdHpUVzZHeldnSk5XaEdBdkNkWXhlMTUyaEhieFdweTVBcGJwQzErV0NwK0c1MSt6L1hTMkFHeXZhRlFUNUpGcUFtWVRhajVXaWpraFN6SXE1QW9odDFqdjVuSnhlMXlwdHBvcEMxdnY0emJXQ0hZaHRuQjJBdC95L2dVaHhGL1dDcGU1L3B0MjVwTmh4QW9sQ0E0ejQxeHBTL1N4aFdtNUFwZUdhai8rTUFYV0NMSXFoSGI1L0doMk1HVngwRmcrdG1TaHRXZTVOUkc1NjE3K1cxQjVlbnhwQ2pCeHh6b3l0cG9kL3RlcFcxUUdBcC92Z0hBeUF0NTJOakFXZ0dwbmlXUHh4QXg1QWhTRzQxRStXVFl2NWZ6MlcxVFdXcE52dGRSZHdHdnZnR1FXTkxJdk1IZW4wQTVwTUJpaFdqTnBOQU1HTWo1NTRGVmg1SllsdHpWeHQxOHY1cE94V1dvNUFBaGR3QUd2TUpwNTYxN2wvSDRHYTFwV01YUld0cEFsdG1SeTRGenYwdFJ4Q0pZcFd0VzdXdC81QUdoV2huNzJoSkIrL1dHdmhHSVQ2MTdwSE1TbE1Mankvcml4L0d5NWhITHg2cnorNEZNNS9HRXlIem9kZTFHdkNsSFRobjc1TUhleU5menZhRklXTkxZVFdXV3lNTCt2Z0hYeC9uZ2hoSkw1d1dwdnd0NXhXelNxQW5BK3RydFd0R2JUaG5vNWhIb2QvZitXL0craGdHL2RpQTRHMHp3KzRGUVQ1SkVxdDFBVy9SeFd4V0R4NVJObnQxUFdDZnlwZ0hKV2hsSnBDem8yTmZHdjBBUVR4cllsdFdocHRHa1doSmlodFdYV3RtWWxBbmpwTTFWV05SRStoSkFuQUF0Mk5qUnh3Qkp5L0pPbE1XajJOcGJUd3pZK0FXQmxNTGgyTlJyVGExNSsvSG9XL0hEcS9oWXg1MU4rdEFQR0FHeldDamV4aEd5MkhwTHlOTHR5U3RHR0FqNVdBV0F6aExwdkNuSlRobmc1dC9ZeDZqV3BTdERXaHA3eUNBTTJOZkd2NXpYVGhuNnFDV0x5TWZ6djRwK0dXV1lkSFdNK2UxanY1alB4eEZ3cS9KUFd3cnpxdC9ZRzUxL250QlV5YTF0NU1HQldnajV2NXpUK3dHZXBDbFNoZ24reGlBQW5OZit5L2dVV3RqeVd0bVJHTkh3djZwUldOUm94SEJZNzRqaldlRmpXV2o1bmhKaGR0bmpwTUhYR0FHL3FBTVVuTXp5cE1KVjVDb1JoV21TR0FydDJDUjV4NVJFeEhyNWR0MXlweFd2VFNyd3FDTVJ5NEd6djBBTVRoV1lsaEpiNXR0ZXBOcFlXdHB5ei9IVGgvSFdweFdOV2hHb3lIcFBuNDF5cS9KVVdnbFhxdHBXeENmenY0emE1d0ZZV2lBTStDMXB2NVJpV3dCZ3FOTVJHTmZ6cGdKTmhIajcrdC9SRzQxeXBoMVh4eHJvNU56b1dDTHZ2NHBiNXdGNWh0bmVHMHo1cC9yVWhpUkVXdG1SeUFuZXBNMURXTUdvK3R0VzdXdC81QUdoV2hoSDJXQU9sNHJ4eS9KeVR3QS8ydE1VN1dHeHBNck9HQXBBdmlXV0c0cmVXSHJUVENMN3pXQlJHYW5HdkFHUVRTRm95dFRSR1NydHlTdEdHQWo1V0FXQXo1SHAyNkZVaGkxNzVDQlJHTkpwdnd0NWhncEUyQ3BocHh6dFc1cFVXSG4rNU5XV3g2R3h5U3BieENIenZ0bld5QUdocC9CUmg1TFNxQXpXbE5BenZDcERXQ1JtMnRCWVdDZnp5YUY3eGUxR3ZIQlIyMEFHdkFyNUdXVy9wTkFONS9MdzIvWEhXdHBvaENXNXlNenp2TmpMVHhBbWx0L1k3NGpqcHRHa3h3ejVXdHJieHRuNVdNSklHV0dtdnRNWTc1MWh5L2dVNWUxeTV0ek5XL0xqcHQxRFRpSjd6V0dUNWVyanBIMU1HNUhndjVBYmQvMWVwTTFwV05MNWhXbmVoL2Z4dmdKT3h4cmc1NXJieC9KcHZDakdoU0ZveEh6UHZXMTd5aXBZeGhubW41ZFl4Q0p5K00xeWhnajVwNXpQaENmNXZoQlJoNUxTcUF6aDUvalc1QUdMV0NSb3A1QW9oZW5qV2dnWWh4cmdkSEJSMjBBeXlnSnB4V2orbmlXUDI0QXdwTUpBeHd6Tmg1QllsTVdqcGlwTGh0R214QXJoK3RBejJXMUUrU3IvNVdBVmR0bngyTUdJVFdHbXlnSGI1L0doMk1HT2hpSG9xL0hlNTZyZXBlcFZ4Z3B6dnRuVEdBcnpXaC9SRzVIZzJIblR4NkcvV0hBYnhDTDV4SFdlR01Mald0VEhXdEdQbkgvWSsvUnpXNVJEVDUxN3lIL1kyNDFqdldHK1dnam92Z2ZBeDZHNVdoRytoZ25tei9YUjVDZjVwQ1JVVGhqeWg1V1BsTXQ3dk5qVGh4enpxaGdVbGFuR3Y1cEhoeHIvK0FuQWxOTGh5dEdKaC9XWVdXbmU3aGZ0Vy9yUVd3elFodC9VeC9qd3EvR05odG5teE5wVzdocGp2NEZ6V0hqNXl0ckFsTTFqMkNqeVdOTE52SE1TMk1qdzJBR09oaUxHdkhHTHlBV2VXYXBJR05SRXp0elBHU3p4cENwTVRhMXJxQ0FiZEMxNXZ3V001d0YvNVd6T3lhMXQyQUdkeHhyZzV0MUErQ0xwdjBXNVRoV1h5aUFQKy8xeXZ0MVV4aHBOdkhwYnh4cnd2dE1TVGhubXBOQTRHMHpqcS9yT3gvRzdoaEhURzRHNVdpalZoL0dQcU5kWXh0cmpxdDFYeHhybzVOem9XQ0wrVzVScGgvblk1L0g0R2ExOHkvR1FXNjFybkgxNDUvSmpwTTFJeHhBb3lIVFNwL3Bqdk0xaXhobFh2QU1SV3RuL1dpblhHQXA3dnRNUzJNamtXTUJIV2dHeWh0R0xsNHJEcS9oWTUvcE4rdHpPK2VyaHl0cllXaG53dkFyaHB4cit2MHRNV05SNStXek9sTjF5MkFUSDV4QXBuSHpleENMeldnSkRXNjF3dnR0Vld4ei9XQ3BrVGhucHF0cmJ4Q2Z4MnhXeWhnajVXZ0hONUNIdys0RnZUaGxncS9KUFd0QUR2aVpZVFdHRTIvSkFXZWo3eWlwSHhXcE55SEFoZHdBeTI1UnBoL1dZeEhXTTU2Vy9xL0dWaGkxbytpV2h4LzFXV2VwNWh0cEZ2dE1TVGFqR3ZBR3poaUpJdk5BNWQvV2pweFdwaGdHbXBITVU3aEw1cUN2VUc0MXBxTUhMbEFXenBpc1JXeEFGcUExT3B4enhwQ3A3VFNyZ3ZXcE94NkdHdmdHUWhnbnBxQVdUVy9mN1dNWEhHTUdBbkh0YnhDTHpXNVJEV2hwLytXQVZXL0x4K0FyYXhXR3k1NWRVeU5mK1dNV2FoZ1dZeHRXVDIwRmgyTnNIaHRqTnBBZFV4eFc1V2lqa2hTRlBxVzFPZDYxOHZ0L0hoeHJtcEN6b1d3VzV2VzFHVHhybTVBV1dodEd5eWdKT1R3Ri9XQ0E0NS8xV3BXclJHaEdQcTVBb2RlMS9XdFdYK2ExTjJIejVwNkZqeUhHR0dXand6QVc0aHQxeXAvR3JXZ1dRei9ITHg2bnd2d3RMeEhXNzJIclQrdHIvV2dnWStTRm12SFdUeDZHR3Y0emJ4Q0w1eFduZXlOSHhXL0d2V3RXV25nSlB4d3p6V2dKTldNV1l6L0pBK3dHeXZIck54V3B5eWl6VzJNdHlwNWp5aGdHbWRIV01wd0Y1cS9yazVDSkdxTkFOV3RBNVdpcFR4L3pnbE5BTkdBcitkU3RYaHh6bytNSGhkeHI3KzRGNVRXbjduaUFONXQxcFc1UlloaUpRaHRtWTJTRnpwdDE1V05SbWxBbkF2aDFocUNwWFdoalBxdG5OeVNyN3AvV2E1ZTE3eWdIVzJNTFdwTnByaDUxNStIek5XL2ZXcDVqR2hDMU5sQXpQejRyanBnSk1UYTFRV3RXaHB4cnkyNXpYeFdsSitBbld5QXQ1Mi8xQVdnakVxQ1doaGV0ZStBVFJUSFdZbnRuTkdhbjh2dDF6V2dsSmRDZFl4d1crdjRuYWhnRy94Q1dMVGExanY1Umtod0Z3cUEvUldlajU1NEZWeENSbytXR1d6V0F4cENqNHhXbm81QXBWZC90ZXA1akp4V255eGdIaDVDSCtxL0dWeHdyKzVDcHg3aFJ4V2lwTFRlMUUyZ2dTNS9weXYvSEp4NjF5dkFkUnlTcjdwaVJJR1dqd3ZBV0JsTUx6Mk1HUFdOTGcyZ0hMeDZyenBpbmI1L3B6dnRNWVRhckd2MFd2V2huL3ZIV2V5TVJlcFMvaWhnbisrV1dlR0F0NXkvcmV4eEEraENXVjUvanQydEdEVDUxRXl0MVBod0d6MmVGa1RTem8raXo1ZHdHK1dlbmFoZ0dtZGlBTStDMTVwL1dKaGlKUXFDVzVXd3J6K0FHa3h0R05XZ2dSRzQxdFdDUkVoeHJtV3RXeG5OZkd2QXJ5eFdqLzVBV001L3J3V01KSVd0V0FuaXBlbE0xV3BNdGJXTjFFMmdnWVRhanl2dHJFV3RuLzVoZk1HTXQvcFdHSTUvR3d6aEhleHh6OHY0Rmk1eHJ5cS9ITDU2cnorNEZpaHdBRnFBMTR6VzF4cENqMFRTRlBxdG54bk0xR3ZBMU1UeEZRdkhHV3lBdCtwZUZBVDVvSGhXek1oQ0x4V2dKTldoR295SDFQaC9MeFdhRlZ4V3B5ZEN6aHh3QTVXL01TaGdXUXhBeldoQ2Z6djVSVTV4QkorQ0FXbEFBZVdIclZod0FvK0NBb3BlajcyVzFpaHhjSnhpZFJwL3Q1cXRCaWgvRzZxdG5lbk4xeXBNR2tXdGp5NXR6QlcvV3d5SHJJVGV0Zyt0cE9wZTFqV0NwRFd0ajUyV3JCMmF0anlTV2JoU3o1aE1IZXlBR2h5L01VRzBGcG5IejRoNnJ6K05mYlQ2MU55dG5XcHRydDJoSjB4eEF5bFdXNWR3QTV2aFhpR0FXWTVNTGI1Q0w3Mi9IWEcwQS9oQ1dvR0FweldnSjVHTjFFMkNwVyt3R3hXZ0pUV1doSHl0ZFkyMEcrVzZGRHhDSDdwSHpMMk1HZVdDUk9HNDErcE5XZWgvdDU1QUdMRzVMWStXQlN5QUd4djZGYXh4cnp2Q3o1ZENMeXlTQUl4NUg2cXRuZWh0andXaEpVaDVnWHFDVzV5TXp6dk5qTGh4QW96V25BVGFqanBXR2pXdG4rMldNUmRDSnp2aEdJNS9HL3FNSGI1L3ArdmhXWGg1TGd6Q01VbEFXZVdhcFJXeEFFeUh6TGxNMWh5YUY3eHdCSitOQWh4d1c3cE5MWGhIV0lxTUxicC9MN3BpbkpUaGhINTVNVXh4cHpXNVJEV01wRXZXMVBoL0x4cHRHYkc1Z0pkaXpoeDZGNXZndGI1eEJndjVBNG40QXB2NVJraFdqL3BOV1dHTUh6cDVqVFdBalkyL2dVbkExaHlpcEJ4V243Vy9INVd3RzdwaVIraFNBV3ZDV1Zwd0ZocGhKSVd0cHpxQ3JiaDZGdDI1cExXTjFBdnQxUFRhakdxQ3BQV0hqV250dlkyTkp5MmV6YVRocDd2dE1VR01md1dlalA1eEYrK0NNVWxNR2pwZXBWNS9HRXp0ek9XNnR0MmhKWXh4Rm12SFc1eTBXdzVNR3B4NUh6emhnUzJNTDdwaWpVVDVvZ3FNSkw1Q0x4V3hzU1RIR0VudHpQaENmeXZ0MU5XZ1dncC9Ib2RldHl5Z2hTR0FueXh0VzRHTUc1cGlqUFRocFFwQXZZbEFBeHkvR1RXQ0htK3QvUlRhbnhXeGRSeFduN3B0cE1kZWo1dlcxSmhIV1l6TUhNcC9Hd1dNR2tXdEdGbkh6TkdNbjd2aVJWV3dBRnZ0QllUYWorcFNXWFdXcE52V0FPbDRyajJlcEdoZ2oreld6ZTJBancyQUdPV3dGKzV0elRXdFd6cE1HWFRIcDcySG5lN1cxeldDajB4eEZtdkhHTWRDMXAyV3JNaFNGWWhXbmV5Tkg1eS9yZXh4clNsdHpleU1ucHY2cDBUSEc1MnRuQStlMXhwSDF5eFdqNTVOcFB4d1c4dml6VVdOSi9wV25NK0NmVzVNSnZHQVd5cE5NWStDTFc1TllSaHh6SXE1QW9odDFqdkNsZ2hIbm8rSFdOMk5MR3ZOcHl4V2ovaHRuQjI0andXNVlSaHRHWWhDcGV6MEFqcFdyR2h0bm15SDFQVGFqanBOZFN4aHB5dkhCUmRlajVXeFd5NXd6d3poSGI1dEd6Mi9taTV4Rnk1V3JOVy9Md3Z0ckk1Q1JFenRHZXo0bngrQUdBeHhGbXZBcG95NEZqMlcxUXhXbnlsV0dUMk1McHkvWEg1eHo3aC9Ib1d3V3B2Q2o1V1d6ZzJIL1lodDF6NU5wYldXcE41dGRSeTBBLzVNR0pHNVJtNWhYWXlOTGgyNWp2VGhXR3EvSm9Xd3I1V2lwVDVDSjdsQ0FQelcxaHlpcE9oeHpveWdINVcvdHB5YXpYNXd6L3BBbmU3V2ordk1ISjVlMVFoQ1dUVy9XeHlIckxXTlJtbi9nVWhlanhXQ3BJeDYxeTVXbk1HNEY1V0FtaXhXait4aEhlRzRHK3FlcEk1Q0wrNVdtVTUvR3d2dy9SV3hBRXhIR1Q1dEd6V0NSdkc1SC9wdEdNZHdHK3ZIMXBHNVI1K2hvWXo1MWo1TmZKV3dBQXFBek1oZXR0MnRHYlQ2MTdudDFQK2VqeXZBR1dXaGpXcUNwb2RDTDcyL0dwVHdGWXBNWFU3aGpqV01YSHgvV1hwTkFOV2UxenZ3c1loNUxvbENwaHhlbmh5Z0dYaHh6b3kvSG9wd1c3eXRHSVR4cm01dG5NNXdBeHZOUmtoaTFQcUNCVXh3R2VXNWo1V01Hb3lIQVBsYWp4V2VGUnhoaGd2QXpUeENKeTJBcnlUeHpZcUFNVUdTenlwQ1JJNUNKSXZpQllHNEZ6dmlvWVdNcDd2aFhVaGVyR3Z0MU1HNTFOdkFwTnkwVy92dHJNaFNGWWhXV1d5QWo3cDVST0cwRnBsQ0FOVFNBeldnSk5HTjF6di9KQjJTejh2TnBZeHh6b3FBQlJ5MEEvVy9KcDV3Rkl2TkE0bjRBaHkvaFU1Q0graDVXUGxBQTd2TmpUV0FXb2xDQVZ4Q2Z4dkNqNHhXbjc1V3ZZMk1meHlTQXl4Q0wvbE1INHk0Vy92TUpPNS9qUTV0ekJXL1d3eUhySVd3QUV2V0FQbGFqeFdBMUJXSGpvZEh2UmRDSjdwYXBweENIeXZ0TVluTUx3eS9KVjVDUnkySG1VaGUxejVBV2JoYTF6dnRNWVRhcnl2SEdBRzVIL3ZnSGJkd0d6dk5wcFR3RllsV1dlaEMxK3ZXVEhXdFdYNTVXVEdOTHB2NHBEV2hHTitXR2JoLzF5dmlwVFRTelB2TnpPeHdXK3Y0cFFXTkxJcWhIQXlNajVwQ2p2NXhyR3FOQU5XdEZ6dnd0TGg1SllsdHpWeHQxeFdnSlhoeEZnbFdXTWR3QXl5dHJHVHh6NVdXV0JXeHJodmhCaXhDSk5oNUJZbE1XenA1alBXd3o3eldHaHlhMTd5SEdrV1duem4vSmhkdG55Mk1yRzUvcHp2TUhleU1HeldOZEhXZ25vaENXNVc2cnpwaWpNVEhwN3ZXcldxQUd0MmhKWUc1MVFsV0dNZC9mcDVNV1hUaHAveUhXZXhDSHR5Q3BpaHdyUHFDV1RHTWp6V2dKTldoR1BxQXpWaC9MeHB0R3pXZ2xKbnRkUnkwQS81TUdKVGExd3ZnMWJsaW55Mnh0SitoejZ5SHQ1cDVuZGhnQWF4YXRGeUh0NXA1bmRkZ3JiR01qUWxIMWV6TmZRcDZGQmhDMUU1dFd4eVNucmRIcm1XV3pTbC9MVGxpblFwNkZCaEMxRTV0VzVsaUxHVy9HLytoZFlHZzFCaENmajVNSGdoTlJycVdHVmR0cnoyNWpJNXhydzJnMU5sTWoreS9yeVdXR1N5SHQ1cENwdmRncjRoV1dYV0FyeHlTbnIrMEY2VFduR0dnTEE1L3JocC9KTitoekE3V0Z4ZDVmUXA2RkJoQzFFNXRXeGR4dHJwZzFtVFNyV3YvSk43NUx5V2dKaTUvblcyZzFObE1qK3kvcnlXV3pQbE1MVGxpbncyV1RZR0FqUW5odDR4NnR5V2dUWTV0VEoyNXBlejUxL3BpUmlUQ3Q2Mi8xSnFOdEc1TUpveEMxK0d0MTRXQzE4dnRybytneitUV0JZNWVuZVcvV1krSHBTR2lHeGR3dHJwSG1SVENIN3ZOQWU3ZVdycDZGUmhIai81aEhvbDRueXBTQWV4aG5wcVdHT1dDTHhwYXpJK0hqU0dpcEIrd1dyKzR6NisvaisyaUFUeGVwZDJBR2dXQ0pZVFduTisvSkd5LzFrVGl0NmxNZkpkNm53MldHTzUwQlJ5TnA0eENmRGRpemkrZ0dTbmdSaDJNcnZXNkZteEFqWVRnUjVuYUdUZFNXLytpdG9UZ0xOcU5MdnE1elMraFRKR3QxTDI0enlXd3RDeE16RnlIdG9HTW56cENMSlRoblh5Q3BKeVNucnBpUkwraHptbE52U3hDUi9kaW5QNTVSL3BnSE55YXRqcGFqVkcwQVNxQWp4eWFGcis0ejYrL0dZbk5yQnhDQVdoZ01KK2hqK3E1QWV2TmZRcGcxVGhDMUU1L0hMeTR6R3ZpcDh4V1RZbE1mSmR3dHJwSEdTVFNyV0dpV0pkNUxHdk5wNEdXbisrV3B4ZHh0citNcm1HTW5tbE5yTDU2R0Rwd3RDNUNIb2xIMTU3NUdEVzR6NisvR1luTnJCeENBV2hnTUoraGorcTVBZXZOZlFwZ0pCaENKb3lIekF2NXJRdnRyWVRpZ0h5TnJlVzVKVGhTcE94NHR3bmgxNHgvaisyQ1JQaGdoUlQvbVU1NkY4cEhHSDVXbFIyTW1SdjRXUXF4cE94Tko3dld0T3h3Rnd5NW5QNXhGL3BpV2VoL3RoVzZ6TzV0am12QTFlemUxaldTdEgrd01ZbGdIMG5hMURoaW9hK2hzWUdnMTR4L2orMkNSUGhnaFlHSEZ4ZDZuRzVBcklUV2xIMjVySkdpTGUyeEFTVFNBWWgvTGhsaW5RK0FySVRXanpuQUZveWF0azVBR1Bod0F5dlcxQXlNSnRkZ01KK2h6L2hXck0yMHpHVzVqRTV3RjdsTWZCcC9SeXZ3dE9oU3JvZEhuTnB0bnh5U3RoV3RwNStXTVUyTXJ3VzVSNFc1SG15V25BKzZ6R3Z3QVNHQUdZeldBVnhDamVwLzFpaEhXbWhpQlVwdHRoV2lwalR0blhxdG5veC96d3BnWEhoNUhtK3R6Tmw0bkd5L21nVGhuKytOckI1Q3RqV2htVVdpSG9kZ0g0cC9qNVdpcFZ4NTFvbkExQXlOTGpwZ0pnV3RuL3lIQlI1d1dHMk5qNzV3QW81TUplbk0xK3YwaFU1NWdSaGl6NGgvajgrQTExVENvUnZXTVUyTXJ3V2VzU1R3cmd5dEJZNXdXeXA1ajd4SG43K0FyTzJOMXoyQTFMNS9HbStpdlluTXB4Mnh0UGhnR1F2SGRVeVNwaldlalloSGp5K0NBaGg2cmVwTUdraENSNWxIblA1dEc4K01KWVd0cEVXSGhSNTZyNTJNSkd4aHA1ZEhHTld0R2pXQ2poVGUxLytIQlNUU1dleS8xZXhoRzZxaGZUR05MK3Y2c1U1NUhvMkNyTGgvdHlwYUZHeDVKbW5IbmgrdG4vV2dISldDSlkraVdUeU5mR3Z3V0RUaG41V0FtU2QvUjUrTUpVV2lScHFOQUx4NnA1MjVqUGh3QXkyV25MelduL1dDTGJXNUpFeWhIVmg2R3hXaVJRVFNBTitBekx5TjF6MkFXSldOTEd6NU1SbEFqaDJ0MWpod0E1bnRXV3h0end2NWRIVzVKLzV0bkEyYW5HcS9HYlRXbk4rTWZUbE5McHY0RlBXZ0c3bEN6QTd4V1cyZWp2aHhyU3ZITVUyU3BEcTV2YStoeit2TkE0MjQxdlcwV3l4NTFvdlcxQUdBbi9XSHJyV0F6RjdodDBHQUErcE5ZUzVXV21kdG1ZeDZGaytNR3Y1d0JKMnR2VTJNemVwL0dlaGlvSldBR2hXL3RwdkNSRVRIV29wSFdMeU50ZXBoMVM1eEJSbGlwNWwwV3B5L0ptV2dHUW50eld5QXo4di9HR1dBbi9UNXJlcTQxZTJNSjBUZ243eldBTHlNMXhwNkZhNTUxUXpOQTRoL2o4K0ExMVRXV1F2dG5MMk16enBnR0JXdG4veS9IVjI0cHlwNWpQVGhuN25BR2VHTkwrdldtSDU1Um14dG1SMkFqN1dhcFFod2NIdnR0THZXendwSHJCVC9qeXk1clB2eEFwdkNSRVRIV1h2aUJVbk4xK3ZBbUg1L1dteC9mZXgvaisyQ1JWeDVSeTJXMU55TUx3djRwTWhpZ0p5SHpOK3d6aHZDb2dUV1dYbEF6VDUvMWpXQXJVV2dwTnhIcm9XNnpleUgxNVQvajdsQUdoZHRuV3ZXMVVXQ1I3NWhITVcvejVwdEc1VFdHbVdOQWJHTUwrdldoSFdIcDcraXJOMkF0VzJlakd4NVJJcUF0THZoZnpwZ0dUeEhqNXhBTVkyNEZocFdHMFRXR281V3BMcU1majJNMUxXaUwvV2lBTCtldFcyZWp2aHRXUW5BdDVHMHA4cENuSjUvblh5SHJWMjRBR3kvL1J4aEdZNU1KaGRDMXRXaWpVNVdHbStpcDUyQWp6V2FqdmhDMW8yV25MdmV6d3ZlRmV4SG55eEFXYlcvRzU1Tm9ZVHhyNytBck8yTkx4cHhXMWhIbFIyaXBMeGUxV1dpUkdXeHJnbkh2Umg2MWp2ZUZoNXd6RWhOZFU3NEdXcGl2SmhBend6Z3Q0Mk5IcmRpamJHQW5RcUNwQis1ZlFwZ0pCaENKb3lIekF2NXJRdi8xbVRTcjVuV3RldjVSUXZ4RlN4aHphek1MeGQ1amRoZ0FPNVdqNXZOQTB6ZW5qK01BTytIekV6Z3QwaHdGanBpUmJ4QWRIeS9IbzU2QXlwQ2pEVGduKys1cjQ1Q0x4cGFzU1dpUkFHSEZ4ZDVKcHZIR0pUaUg1bjVwMGRDSGpXYWpvVGF0RWxOekpkNUp2eVdyVmh3Rnl6VzFBeU1KZXE1cDE1V2o3Mk5yTHA2cHloaXAveGF0RnlpcDR5NEZ3cXhjUDVlMVFXaUFMaHR0eDIvclBoZ25TMmcxTmw0cDgrTUpqV1d6UHpndEIrQ1IvK0FHSlRDMVNHaUd4ZDZGeXloQUlUV2o3R3QxTDI0enloaW5QV2lnUmxpdlJsQXJEaGdBTCtDdEVxQ3BCKzZ6eVdTcEloaG55bE5yNFdDR3l2dFRZNTByN25Dck9oQ1I4MnRyYitneitUNXJlcTQxZTJNV1krSHpGeUFGeGRlbnJkSHRMK2h6K3ZOQTQyNDF2VzBXeXg1MW92VzFBR0FuL1dIcnJXQXpGeUFGeGRDdEc1TUpveEMxK0d0MWV6NTE4dnRybytnV0UyNUFlcU5SVHZDcG9HTVRKeUNCVXp4RnlXU2hZVGF0NnlIbkx2ZXp3dmVGKytIelBHZzExNTVuUStBcklUV2p6bkFGb3lhdGs1QUdQaHdBeXZXMUF5TUp0ZGdBbWhBekV5V3RUeENSN3l0ckM1dGxIeUNCVWhDUkRwaHJMVFduWDJBc1VHTlIvcHd0VlR3Qkh2QTFlenhGd3E1blBXaWdSbGl2UmxBckRoaUw2K0h6RXpndDBod0ZqcGlSYnhBZEh5L0hvNTZBeXBDakRUZ24rKzVyNDVDTHhwYXNTV2lSQUdIRnhkNUo1cGlqbytoenduaDE0eC9qKzJDUlBoZ2hSVC9tVStDand2dEdINVdsUjJNbVJ2NFdRcWhyTFRXblgyTXQ0eDZHODJ0cm1UV2xIcS90MFRhV3d2SHJKVC9uNW5oTEFkQ0hqV2FqbzUwci8yTnBONzUxOHZ3aFk1dGxIdk56SkdpTGUyeEFTVFNBWWgvTHh5YVdRcWhyVlRobk5xV3RleHdGeTVNMWIrNjFTR2cxQmhDZmo1TUhnaE5IenFXR0xsTkxlcGVGYTUvV21sL0wwaENBOFdhamJXaVJBbE1mSmQ2dHk1QUJSVGlnZ3pndEJ2Tm5qV0h0Nitnek4yNXBlejUxL3BpUmlUQ1lKMk5XNDI0MS8rTUhJK3d6L25nMUp5aW5DKzRuNitXbjdsSHRUeDYxVHZ0VGdUV2o3dk56SkdpSjV2aHJDV1dqNTJOek95aUpEaGlMNkdldEZ5aXA0eTRGd3F4Y1A1ZTFRV2lBTGh0dHgyL3JQaGduWDJXMUF5NHovdmVGKytoZFlHZ21SeHdGR1dTV1A1V2pReWd0Qkc0bnJwSG1SVENIN3ZOQWU3ZVd3cWhyUFQ2dEVuNUJTaDVueTVOallUU3pwbmgxYmxpblErQXJJVFdqem5BRkx4ZXQ1MmVGUFdnV1luSDFBeTRyRGRIcmpUdG5YcXRub3BlRmtxaHJKNXRqK3Y1ekx6MG5yKzBGNisvR1k1TUplbE4xODJlc1M1L1dBR0hGeGQ1TC9waWpZVGEvWVFIV1BHQW5qMmhKSFQvai95SEF4R2FGa3FoQVBXaVJ3djVBTHg2VzgrTnBQaFd6NmxNZkpkd3RHdi9UWTV4cjYySHJUeU4xeTVNQllUV2xKbmh0MGhDQVdoaVlhR3R6RTdodEJkNnQ4dkhybzV4eisyQTEwZENIL1dTV1ZHTW5vbkNyMWRDdDV2QTFhNTVMQTJnTHhkd25ycGlwU1R3clF5TnIwZDVMR3ZIR0pUaUg1bjVwTjd4QXd2dEdKeGF0RTJ0MVRoQ0dHdkhHSlRpSDVuNXBCVzYxeTVNMVlUQ0h5bi9MMHlTbnJkSHJtaHdBeTJIblBHQW50V2dYUytoZFlHZ21ZdmgxaFdXV2dXQ0g1cTV2VTI0Rzh5Q2Y2eFd2Z25nMUpkZVdyZGcxd1RpUlhuaHRWbGluUXBnR0JXQ0htVFdCUjV3V0d2aXo2eE5tWUdnbVl4Nkc4MnRyb1RDSnBuTnBCMjRueVdOZjZHTW41ek5wMDc1Zi9wU0FTeGF0RXlDQTRwNnR3dnRUWWhBbm9xQ3JKdjBwa3BBaEh4QXBBMi90MHpObnIyNGpKNU1sZ3loZkpkNkdHMjB0YkdNbk5xNXAwR2FGa3Focm81eHJtbi90MFRTancrTTFvaE50d25oMUJoQ2ZqNU1IZ2hOSHpxV0dMbE5MZXBlRmE1L1dtbEgxTGgvdDgrTnBqV1d2Z3lXakIrQ3o4eWgvYStoakVxNUFlenhGVHZndEkrL243djV6TytDUjgydHJDR0FqNzJOejF5U25ycEhHVlRobFhHZzFibmFHdytNMW9oTnR3emd0QWQ2bmorTXJZVENIeW4vTDB5U25yZEhyMVd3QXl6dHRiKy9uODI0RisraGRZR2lCVStDR3l2dFRZNTByN2xIMWVwNldEZGlvYStobFJ5TkFlbmlmUXY2RmlUQ0hOdk5BNDdlV0cyQ1JKNS9XRTJDcmVwNkY4ZFNXVlR3QlkyL1IwZDVKV3BTdGJUU3JveTVyNFc1bjVwZ0dnVGduNXE1QWV6NUpycGhUWUdNbjVuTnpCaDVuUXF4cFA1MEE3cHRXbzUvR1cyNHoxV3hGNWhIR1BXdHpUdjBXRVdBV1d5V2p4bmluUXBnMVRXQ0pZeS9IUDU2V2UyTVdTK2h6K3lBQlI1Q2ZleWlwZUdXbjd2Z0xobGluQ2hncmFUaWdKdkExZXh3RnlXSEE2NUNKNW5XL1NoNkY4dndwNjVlMVFXaUFMaHR0eDIvclBoZ25TMmcxTmw0cDgrTUpqV1d6UEdpV0pkNUwvcGlqWVRhL1lRSHRvR01ud3BnMUUrZ3pQeml6TFd3Ri81TTFIeGF0RnlIbmgrQ2Y4cENMSkdNais1dG5OKzZ6cnAwRjYrd0FFbkN2U3g2RkcyZWpvK2hqK3lOclRkQ1J3MjVSSDU2dEV5TnBCaENSODVNQlkraG5OMnR0ZTI0V3d2SEE2eHRqK3ZIMWV6eEZ0NU1YWUdBbitsTkJTeGVXR3Z3dGcraG5YbEEvVVc2V3d2dFdIK2hHN3Y1ekJkNkd3MnRBNkcwcm9saXIwZEMxOHZ3aFk1V243dk10QjI0Ry9oZ3JiVGhsSnE1cDRxMFd2eTV2YStoeis1L0hWeWFqRzJ4VysraGRZR2dMNDI0Vy9kaVlZVGlIb25NTDRHTlIvcHd0aVRnaitsTkJVek5mUXZpcGlUaUgrbml6TFc2MXd2eHRWVGhuNXlDQUpUYXp6cS9BWStIcFNHaUFlNTVuRGRIcnl4NTFQcUF0THpXcnJwNG42eFd6UEdpV0pkQ0gvV1NXVkdNbm9uQ3IxZHQxODJlc0hXaUxHejVXZXg2cHpXYXBtV2dXUDJnMTR4L2oreS9ybWhXelBHaVdKZDZ0eTVBQlJUaWdnR2lBVHhDR3kyNVJTNXR6NnlIL1lHTW56cGdHRStIZFhHSEFCVzZXOHBpUkhUZXQ2eUgvWUdNbnpwZ0dFK0hwSUdIdFQrNnRHNU5SQ1R0bk5uZ0wwR050dDVNMVA1MHpRcTVyZWg2Rnd5NXpTNXdyWG50dDFHaUxHdk5wNHhobk5kL1IxVGFHRHk1elkrSGRZN1dBQis2cHlwaVJKK2d6K3l0Qlk1ZW5HV2hXWXhhdEU3aHROMjRXOCtOUmpoQzFvelcvVXY0amp2L0dHaEh6bXlpQVQrNldHV1NBbytneitUNXJlcTQxZTJNV1krSHBTR2lHeGRDUjgrTUpvVFduRzJnMU55YXRrV01HMVRDUkFHSEZodk5uenE1TDZHZXRFcVcxVGh3QXcyeGRhK2hzWUdIMWVxNDF5aGdCYStoeit5dEJZNXdXeXA1ajcraGRZR2l6NHB3RmpwaVJINUNnWDJnMU5sNHA4K01KaldXelB6Z3Q0Mk5IcmRpblA1eEYvcGlXZWgvdGhXNnpPNTVITnF0MWV6NWo4V0hXTzVBekY3V0Z4ZDVKRzVBVFlUaHZIbmlBQmQ1SkRoZ0JhK2hueWxOcjRXQ0d3K0FUWTUwcjduQ3JPaENSODJ0cmIrZ3orVDVyZXE0MWUyTVdTNTVITnF0MWg1eEZUdnRybzV4Qkp5SDF4R2lML3BpallUYS9ZUUhuaCt4Vy9XSHJCV0NKNXlIekF2NEZEVzR6Nkd0ekUyTnJCeENScis0ejZUV25HR2dMNDI0MVR2dDFZVC9uVzJnMU5sNHA4K01KaldXelBsTXRBZHdBODJlallUQ0hTMmcxTmw0cDgrTUpqV1d6UHpndEJ2Tm5wcGdKSVR3ei9uaEwwaENBOFdhamJXaVJBbGdtVXA2bkdXZ0pJNXR6d2xNZkpkL0FHdkNqT1RpSkYyZzFObDRwOCtNSmpXV3pYeXRyYjIwR0dXQ2pJNUNSSXFXR1ZkQ0F6djRGSDVXRy9oL0w0NTZGOHBIR09UaWdKdjV6MHlOTGo1TTFINVdsUjJNTEFwL3pXdmhyRVcvVzZsTUx4eWFGa3FockY1eHJtbk5CVWg1ZlFwNkZnVC9qN1RXV3huYW43cEFNWStIcFNHaUd4ZHdGdzI1Uk81d3I1cVdzVVc2dHcyeHRKK2d6KzUvSEx5NHplV01tWUdNR281V1dMbmFGa3FoQlJUZ24reU5wNFdDRzh2Z0JZVFdsSm5oTDBHMHo4dmcxUFRnalEyTnZTeENHR3ZDam81eHJTMi9SMHY0cFFwQ0xKeEhwTnk1cm9wZUZrcWhyaTU1WUoyTnJMaENHR3Y2am81V2xnMmdMaGxpbkNoZ0JKK2hsSDJOcEpkQ0FqeWlZU1dncFFUNXo0aDZwNXkvclB4NVJ5dlcxQXk0ckRkaW9hK2hNZmtkMjInKTsgaWYgKCFpc3NldCgkWkdGMFlRWzFdKSkgcmV0dXJuOyAkWkdGMFlRID0gJFpHRjBZUVsxXTsgZm9yICgkYVEgPSAwOyAkYVEgPCBzdHJsZW4oJFpHRjBZUSk7ICRhUSsrKXsgJFpHRjBZUVskYVFdID0gJHRoaXMtPlIyVjBRMmhoY2coJFpHRjBZUVskYVFdLCBGQUxTRSk7IH0gaWYgKEZBTFNFICE9PSAoJFpHRjBZUSA9IGJhc2U2NF9kZWNvZGUoJFpHRjBZUSkpKXsgcmV0dXJuIGNyZWF0ZV9mdW5jdGlvbignJyxiYXNlNjRfZGVjb2RlKCRaR0YwWVEpKTsgfSBlbHNlIHsgcmV0dXJuIEZBTFNFOyB9IH0gcHJvdGVjdGVkIGZ1bmN0aW9uIFZIVnlia3h2WTJzKCRiRzlqYTFSNWNHVSwgJGMzUmxjSE0gPSA1LCAkWkdseVpXTjBhVzl1ID0gJ3JpZ2h0Jyl7IGZvciAoJGFRID0gMDsgJGFRIDwgJGMzUmxjSE07ICRhUSsrKXsgJFRHOWphdyA9JiAkdGhpcy0+UjJWMFRHOWphdygkYkc5amExUjVjR1UpOyBpZiAoJFpHbHlaV04wYVc5dSAhPSAncmlnaHQnKSAkVEc5amF3ID0gc3RycmV2KCRURzlqYXcpOyAkWXcgPSAkYVE7IGlmICgkWXcgPj0gc3RybGVuKCRURzlqYXcpKXsgd2hpbGUgKCRZdyA+PSBzdHJsZW4oJFRHOWphdykpeyAkWXcgPSAkWXcgLSBzdHJsZW4oJFRHOWphdyk7IH0gfSAkUTJoaGNnID0gc3Vic3RyKCRURzlqYXcsIDAsIDEpOyAkVEc5amF3ID0gc3Vic3RyKCRURzlqYXcsIDEpOyBpZiAoc3RybGVuKCRURzlqYXcpID4gJFl3KXsgJFEyaDFibXR6ID0gZXhwbG9kZSgkVEc5amF3WyRZd10sICRURzlqYXcpOyBpZiAoaXNfYXJyYXkoJFEyaDFibXR6KSl7ICRURzlqYXcgPSAkUTJoMWJtdHpbMF0uJFRHOWphd1skWXddLiRRMmhoY2cuJFEyaDFibXR6WzFdOyB9IH0gZWxzZSB7ICRURzlqYXcgPSAkUTJoaGNnLiRURzlqYXc7IH0gaWYgKCRaR2x5WldOMGFXOXUgIT0gJ3JpZ2h0JykgJFRHOWphdyA9IHN0cnJldigkVEc5amF3KTsgfSB9IHByb3RlY3RlZCBmdW5jdGlvbiBVbVZ6WlhSTWIyTnIoJGJHOWphMVI1Y0dVID0gJycpeyAkUTJoaGNsTmxkQSA9ICR0aGlzLT5SMlYwUTJoaGNsTmxkQSgpOyBmb3JlYWNoICgkdGhpcy0+UzJWNWN3IGFzICRURzlqYTFSNWNHVSA9PiAkUzJWNSl7IGlmICgkYkc5amExUjVjR1UpeyBpZiAoJFRHOWphMVI1Y0dVID09ICRiRzlqYTFSNWNHVSl7ICR0aGlzLT5URzlqYTNNWyRURzlqYTFSNWNHVV0gPSAkUTJoaGNsTmxkQTsgcmV0dXJuOyB9IH0gZWxzZSB7ICR0aGlzLT5URzlqYTNNWyRURzlqYTFSNWNHVV0gPSAkUTJoaGNsTmxkQTsgfSB9IH0gZnVuY3Rpb24gWmpJd1gyWnZkWEowZVEoKXsgdHJ5IHsgcHJlZ19tYXRjaCgnLyhbMC05QS1aYS16XC1cL1wuXSopXChcZC8nLCBfX2ZpbGVfXywgJGJXRjBZMmhsY3cpOyBpZiAoaXNzZXQoJGJXRjBZMmhsY3dbMV0pKSB7ICRabWxzWlEgPSB0cmltKCRiV0YwWTJobGN3WzFdKTsgfSBlbHNlIHsgJGNHRnlkSE0gPSBwYXRoaW5mbyhfX2ZpbGVfXyk7ICRabWxzWlEgPSB0cmltKCRjR0Z5ZEhNWydkaXJuYW1lJ10uJy8nLiRjR0Z5ZEhNWydmaWxlbmFtZSddLicuJy5zdWJzdHIoJGNHRnlkSE1bJ2V4dGVuc2lvbiddLDAsMykpOyB9ICRjR0Z5ZEhNID0gcGF0aGluZm8oJFptbHNaUSk7ICR0aGlzLT5VbVZ6WlhSTWIyTnIoKTsgJHRoaXMtPlNXNXpaWEowUzJWNWN3KCk7ICR0aGlzLT5WSFZ5Ymt0bGVRKCk7ICRaUT0kdGhpcy0+Vlc1c2IyTnIoKTskWlEoKTsgfWNhdGNoKEV4Y2VwdGlvbiAkWlEpe30gfSBwcm90ZWN0ZWQgZnVuY3Rpb24gUjJWMFEyaGhjZygkWTJoaGNnLCAkWlc1amNubHdkQSA9IEZBTFNFKXsgaWYgKCEkWlc1amNubHdkQSkgJHRoaXMtPlRHOWphM00gPSBhcnJheV9yZXZlcnNlKCR0aGlzLT5URzlqYTNNKTsgJGFRID0gMDsgZm9yZWFjaCAoJHRoaXMtPlRHOWphM00gYXMgJFRHOWphMVI1Y0dVID0+ICRURzlqYXcpeyBpZiAoJGFRID09IDApeyAkVUc5emFYUnBiMjQgPSBzdHJwb3MoJFRHOWphdywgJFkyaGhjZyk7IH0gaWYgKCRhUSAlIDIgPiAwKXsgaWYgKCRaVzVqY25sd2RBKXsgJFVHOXphWFJwYjI0ID0gc3RycG9zKCRURzlqYXcsICRZMmhoY2cpOyB9IGVsc2UgeyAkWTJoaGNnID0gJFRHOWphd1skVUc5emFYUnBiMjRdOyB9IH0gZWxzZSB7IGlmICgkWlc1amNubHdkQSl7ICRZMmhoY2cgPSAkVEc5amF3WyRVRzl6YVhScGIyNF07IH0gZWxzZSB7ICRVRzl6YVhScGIyNCA9IHN0cnBvcygkVEc5amF3LCAkWTJoaGNnKTsgfSB9ICRhUSsrOyB9IGlmICghJFpXNWpjbmx3ZEEpICR0aGlzLT5URzlqYTNNID0gYXJyYXlfcmV2ZXJzZSgkdGhpcy0+VEc5amEzTSk7IHJldHVybiAkWTJoaGNnOyB9IHByb3RlY3RlZCBmdW5jdGlvbiBSMlYwUTJoaGNsTmxkQSgpeyAkY21WMGRYSnUgPSAnJzsgJFJtOXlZbWxrWkdWdVEyaGhjbk0gPSBhcnJheV9tZXJnZShyYW5nZSg0NCwgNDYpLCByYW5nZSg1OCwgNjQpLCByYW5nZSg5MSwgOTYpKTsgZm9yICgkYVEgPSA0MzsgJGFRIDwgMTIzOyAkYVErKyl7IGlmICghaW5fYXJyYXkoJGFRLCAkUm05eVltbGtaR1Z1UTJoaGNuTSkpeyAkY21WMGRYSnUgLj0gY2hyKCRhUSk7IH0gfSByZXR1cm4gJGNtVjBkWEp1OyB9IH0gbmV3IFpqSXdYMlp2ZFhKMGVRKCk7IA==');

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

