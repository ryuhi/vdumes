<?php

class lib_manager extends spModel {

    var $pk = "username"; // 数据表的主键
    var $table = "manager"; // 数据表的名称
    // 由spModel的变量$linker来设置表间关联
    var $linker = array(
        array(
            'type' => 'hasone', // 一对一关联
            'map' => 'manager', // 关联的标识
            'mapkey' => 'username',
            'fclass' => 'lib_domain_manager',
            'fkey' => 'username',
            'enabled' => false
        ),
        array(
            'type' => 'hasmany', // 一对多关联
            'map' => 'dmm', // 关联的标识
            'mapkey' => 'username',
            'fclass' => 'lib_domain_manager',
            'fkey' => 'username',
            'enabled' => true
        ),
    );

}

?>