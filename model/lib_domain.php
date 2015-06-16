<?php

class lib_domain extends spModel {

    var $pk = "domain"; // 数据表的主键
    var $table = "domain"; // 数据表的名称
// 定义验证规则
    var $addrules = array(
        'isdomain' => 'chkdm',
        'istime1' => 'istime1',
    );
    var $verifier_add = array(
        "rules" => array(// 规则
            'domain' => array(
                'notnull' => TRUE,
                'maxlength' => 253,
                'isdomain' => TRUE,
            ),
            'maxusers' => array(
                'notnull' => TRUE,
                'maxlength' => 10,
            ),
            'maxalias' => array(
                'notnull' => TRUE,
                'maxlength' => 10,
            ),
            'maxquota' => array(
                'notnull' => TRUE,
                'maxlength' => 16,
            ),
            'expiredate' => array(
                'notnull' => TRUE,
                'istime1' => TRUE,
            ),
            'default_quota' => array(
                'notnull' => TRUE,
            ),
            'default_expire' => array(
                'notnull' => TRUE,
            )
        ),
        "messages" => array(// 规则
            'domain' => array(
                'notnull' => 'notnull_domain',
                'maxlength' => 'max_253',
                'isdomain' => 'isdomain',
            ),
            'maxusers' => array(
                'notnull' => 'notnull_maxusers',
                'maxlength' => 'max_10',
            ),
            'maxalias' => array(
                'notnull' => 'notnull_maxalias',
                'maxlength' => 'max_10',
            ),
            'maxquota' => array(
                'notnull' => 'notnull_maxquota',
                'maxlength' => 'max_16',
            ),
            'expiredate' => array(
                'notnull' => 'notnull_expire',
                'istime1' => 'is_time',
            ),
            'default_quota' => array(
                'notnull' => 'notnull_defaultquota',
            ),
            'default_expire' => array(
                'notnull' => 'notnull_defaultexpire',
            )
        ),
    );
    var $verifier_update = array(
        "rules" => array(// 规则
            'maxusers' => array(
                'notnull' => TRUE,
                'maxlength' => 10,
            ),
            'maxalias' => array(
                'notnull' => TRUE,
                'maxlength' => 10,
            ),
            'maxquota' => array(
                'notnull' => TRUE,
                'maxlength' => 16,
            ),
            'expiredate' => array(
                'notnull' => TRUE,
                'istime1' => TRUE,
            ),
            'default_quota' => array(
                'notnull' => TRUE,
            ),
            'default_expire' => array(
                'notnull' => TRUE,
            )
        ),
        "messages" => array(// 规则
            'maxusers' => array(
                'notnull' => 'notnull_maxusers',
                'maxlength' => 'max_10',
            ),
            'maxalias' => array(
                'notnull' => 'notnull_maxalias',
                'maxlength' => 'max_10',
            ),
            'maxquota' => array(
                'notnull' => 'notnull_maxquota',
                'maxlength' => 'max_16',
            ),
            'expiredate' => array(
                'notnull' => 'notnull_expire',
                'istime1' => 'is_time',
            ),
            'default_quota' => array(
                'notnull' => 'notnull_defaultquota',
            ),
            'default_expire' => array(
                'notnull' => 'notnull_defaultexpire',
            )
        ),
    );

}

?>