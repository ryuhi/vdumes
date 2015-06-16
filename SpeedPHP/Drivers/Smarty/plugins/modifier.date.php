<?php
//require_once $smarty->_get_plugin_filepath('shared', 'make_timestamp');
    require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');
function smarty_modifier_date($string, $format = 'Y-m-d H:i:s')
{
    $timestamp = smarty_make_timestamp($string);
    return date($format, $timestamp);
}
?>