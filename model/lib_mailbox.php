<?php

class lib_mailbox extends spModel {

    var $pk = "username"; // 数据表的主键
    var $table = "mailbox"; // 数据表的名称
    
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
                'maxlength' => 255,
            ),
            'name' => array(
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'quota' => array(
                'notnull' => TRUE,
                'maxlength' => 16,
            ),
            'expiredate' => array(
                'notnull' => TRUE,
                'istime1' => TRUE,
            ),
            'password' => array(
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'verfiypass' => array(
                'notnull' => TRUE,
                'equalto' => 'password',
            ),
            'uidnumber' => array(
                'notnull' => TRUE,
                'maxlength' => 6,
            ),
            'gidnumber' => array(
                'notnull' => TRUE,
                'maxlength' => 6,
            ),
        ),
        "messages" => array(// 规则
            'username' => array(
                //'is_english' => TRUE,
                'notnull' => 'notnull_username',
                'maxlength' => 'max_255',
            ),
            'name' => array(
                'notnull' => 'notnull_truename',
                'maxlength' => 'max_255',
            ),
            'quota' => array(
                'notnull' => 'notnull_diskquota',
                'maxlength' => 'max_16',
            ),
            'expiredate' => array(
                'notnull' => 'notnull_expire',
                'istime1' => 'is_time',
            ),
            'password' => array(
                'notnull' => 'notnull_password',
                'maxlength' => 'max_255',
            ),
            'verfiypass' => array(
                'notnull' => 'notnull_repassword',
                'equalto' => 'noteq',
            ),
            'uidnumber' => array(
                'notnull' => 'notnull_userid',
                'maxlength' => 'max_6',
            ),
            'gidnumber' => array(
                'notnull' => 'notnull_groupid',
                'maxlength' => 'max_6',
            ),
        ),
    );
    var $verifier_update = array(
        "rules" => array(// 规则
            'username' => array(
                //'is_english' => TRUE,
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'name' => array(
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'quota' => array(
                'notnull' => TRUE,
                'maxlength' => 16,
            ),
            'expiredate' => array(
                'notnull' => TRUE,
                'istime1' => TRUE,
            ),
            'password' => array(
                'maxlength' => 255,
            ),
            'verfiypass' => array(
                'equalto' => 'password',
            ),
            'uidnumber' => array(
                'notnull' => TRUE,
                'maxlength' => 6,
            ),
            'gidnumber' => array(
                'notnull' => TRUE,
                'maxlength' => 6,
            ),
        ),
        "messages" => array(// 规则
            'username' => array(
                //'is_english' => TRUE,
                'notnull' => 'notnull_username',
                'maxlength' => 'max_255',
            ),
            'name' => array(
                'notnull' => 'notnull_truename',
                'maxlength' => 'max_255',
            ),
            'quota' => array(
                'notnull' => 'notnull_diskquota',
                'maxlength' => 'max_16',
            ),
            'expiredate' => array(
                'notnull' => 'notnull_expire',
                'istime1' => 'is_time',
            ),
            'password' => array(
                'maxlength' => 'max_255',
            ),
            'verfiypass' => array(
                'equalto' => 'noteq',
            ),
            'uidnumber' => array(
                'notnull' => 'notnull_userid',
                'maxlength' => 'max_6',
            ),
            'gidnumber' => array(
                'notnull' => 'notnull_groupid',
                'maxlength' => 'max_6',
            ),
        ),
    );
    var $verifier_login = array(
        "rules" => array(// 规则
            'username' => array(
                //'is_english' => TRUE,
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'password' => array(
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'repass' => array(
                'notnull' => TRUE,
                'equalto' => 'password',
            ),
        ),
        "messages" => array(// 规则
            'username' => array(
                //'is_english' => TRUE,
                'notnull' => 'notnull_username',
                'maxlength' => 'max_255',
            ),
            'password' => array(
                'notnull' => 'notnull_password',
                'maxlength' => 'max_255',
            ),
            'repass' => array(
                'notnull' => 'notnull_repassword',
                'equalto' => 'noteq',
            ),
        ),
    );
    var $verifier_chgpwd = array(
        "rules" => array(// 规则
            'password' => array(
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'newpassword' => array(
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'renewpassword' => array(
                'notnull' => TRUE,
                'equalto' => 'newpassword',
            ),
        ),
        "messages" => array(// 规则
            'password' => array(
                'notnull' => 'notnull_password',
                'maxlength' => 'max_255',
            ),
            'newpassword' => array(
                'notnull' => 'notnull_password',
                'maxlength' => 'max_255',
            ),
            'renewpassword' => array(
                'notnull' => 'notnull_repassword',
                'equalto' => 'noteq',
            ),
        ),
    );
}

?>