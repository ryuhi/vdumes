<?php

class lib_alias extends spModel {

    var $pk = "address"; // 数据表的主键
    var $table = "alias"; // 数据表的名称
    var $addrules = array(
        'isdomain' => 'chkdm',
    );
    var $verifier_add = array(
        "rules" => array(// 规则
            'username' => array(
                //'is_english' => TRUE,
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'domain' => array(
                'notnull' => TRUE,
                'maxlength' => 255,
                'isdomain' => TRUE,
            ),
            'goto' => array(
                'notnull' => TRUE,
            ),
        ),
        "messages" => array(// 规则
            'username' => array(
                //'is_english' => TRUE,
                'notnull' => 'notnull_username',
                'maxlength' => 'max_255',
            ),
            'domain' => array(
                'notnull' => 'notnull_domain',
                'maxlength' => 'max_255',
                'isdomain' => 'isdomain',
            ),
            'goto' => array(
                'notnull' => 'notnull_goto',
            ),
        ),
    );
    var $verifier_update = array(
        "rules" => array(// 规则
            'goto' => array(
                'notnull' => TRUE,
            ),
        ),
        "messages" => array(// 规则
            'goto' => array(
                'notnull' => 'notnull_goto',
            ),
        ),
    );

}

?>