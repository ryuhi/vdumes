<?php
define("APP_PATH",dirname(__FILE__));
define("SP_PATH",dirname(__FILE__).'/SpeedPHP');
$spConfig = array();
require(SP_PATH."/SpeedPHP.php");;
import(APP_PATH.'/includes/global.func.php');
import(APP_PATH.'/includes/config.inc.php');
date_default_timezone_set('Asia/Shanghai');
$headers = apache_request_headers(); 
$client_time = (isset($headers['If-Modified-Since']) ? strtotime($headers['If-Modified-Since']) : 0); 
$now=gmmktime(); 
$now_list=gmmktime()-60*5; 
if ($client_time<$now and $client_time > $now_list){ 
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', $client_time).' GMT', true, 304);
    exit(0);
}else{ 
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', $now).' GMT', true, 200);
}
ini_set('default_charset','utf-8');
filter_input_array(INPUT_POST,$_POST);
filter_input_array(INPUT_GET,$_GET);
filter_input_array(INPUT_COOKIE,$_COOKIE);
filter_input_array(INPUT_SESSION,$_SESSION);
spRun();
?>
