<?php

class lib_manager1 extends spModel {

    var $pk = "username"; // 数据表的主键
    var $table = "manager"; // 数据表的名称

    public function acljump() {
        // 这里直接"借用"了spController.php的代码来进行无权限提示
        $url = spUrl("main", "index");
        spController::getLang();
        $msg = T('nopermit');
        echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><link type=\'text\/css\' rel=\'stylesheet\ href=\'./public/css/{$_SESSION['lang']}/global.css\'><script>function sptips(){alert(\"$msg\");location.href=\"{$url}\";}</script></head><body onload=\"sptips()\"></body></html>";
        exit;
    }

    var $addrules = array(
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
        ),
    );
    var $verifier_update = array(
        "rules" => array(// 规则
            'name' => array(
                'notnull' => TRUE,
                'maxlength' => 255,
            ),
            'expiredate' => array(
                'notnull' => TRUE,
                'istime1' => TRUE,
            ),
            'password' => array(
                'notnull' => FALSE,
                'maxlength' => 255,
            ),
            'verfiypass' => array(
                'notnull' => FALSE,
                'equalto' => 'password',
            ),
        ),
        "messages" => array(// 规则
            'name' => array(
                'notnull' => 'notnull_truename',
                'maxlength' => 'max_255',
            ),
            'expiredate' => array(
                'notnull' => 'notnull_expire',
                'istime1' => 'is_time',
            ),
            'password' => array(
                'notnull' => 'null_password',
                'maxlength' => 'max_255',
            ),
            'verfiypass' => array(
                'notnull' => 'notnull_repassword',
                'equalto' => 'noteq',
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