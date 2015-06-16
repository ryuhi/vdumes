<?php

class lib_domain_manager extends spModel {

    var $pk = "username,domain"; // 数据表的主键
    var $table = "domain_manager"; // 数据表的名称
    var $addrules = array(
        'isdomain' => 'chkdm',
        //'is_english' => 'is_english',
        'istime1' => 'istime1',
    );
    var $verifier_add = array(
        "rules" => array(// 规则
            'username' => array(
                //'is_english' => TRUE,
                'notnull' => TRUE,
                'maxlength' => 150,
            ),
            'domain' => array(
                'notnull' => TRUE,
                'maxlength' => 150,
            ),
        ),
        "messages" => array(// 规则
            'username' => array(
                //'is_english' => TRUE,
                'notnull' => 'notnull_username',
                'maxlength' => 'max_150',
            ),
            'domain' => array(
                'notnull' => 'notnull_truename',
                'maxlength' => 'max_150',
            ),
        ),
    );

}

?>