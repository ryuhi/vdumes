<?php
/**
 * Smarty plugin
 * 
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty human readable size converter modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     size<br>
 * Purpose:  convert the size to the human readable size.
 * 
 * @link 
 * @author Zhang Hui<tobutatu at gmail.com>
 * @param string $ 
 * @return string 
 */
function smarty_modifier_size($size, $dec=2)
{ 
	require(APP_PATH.'/includes/global.func.php');
    return sizecount($size, $dec=2);
} 

?>