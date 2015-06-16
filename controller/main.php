<?php

class main extends spController {

    function index() {
        $langs = explode(',', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        switch (strtolower($langs[0])) {
            case 'zh_cn':
                $lang = 'zh_CN';
                break;

            case 'zh-cn':
                $lang = 'zh_CN';
                break;

            case 'ja':
                $lang = 'ja';
                break;
        }
        spController::setLang($lang);
        $_SESSION['lang'] = $lang;
        $this->display("main/login.html");
    }

    function check() {
        $this->_vcode_check();
        $adm = spClass('lib_manager');
        $conditions = array(
            'username' => trim($_POST['username']),
        );
        $res = $adm->findcount($conditions);
        if ($res > 0) {
            $this->checkadm();
        } else {
            $this->checkuser();
        }
    }

    function show() {
        $mng = spClass('lib_manager1');
        $cond = array('username' => trim($_COOKIE['user']));
        $this->res = $mng->find($cond, null, 'type');
        $this->dbtype = $GLOBALS['G_SP']['db']['driver'];
        $this->listenv = array(
            'os' => php_uname('s'),
            'host' => php_uname('n'),
            'version' => php_uname('r'),
            'vername' => php_uname('v'),
            'plat' => php_uname('m'),
        );
        $this->role = spClass('spAcl')->get();
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("main/index.html");
    }

    function logout() {
        $this->getLang();
        setcookie(user, '', time() - 1);
        setcookie(_SpLangCookies, '', time() - 1);
        setcookie('lang', '', time() - 1);
        unset($_COOKIE['user']);
        unset($_COOKIE["_SpLangCookies"]);
        unset($_COOKIE['lang']);
        unset($_SESSION['lang']);
        $this->success(T('backlogin'), spUrl('main', 'index'));
    }

    function _vcode() {
        $vcode = spClass('spVerifyCode');
        $vcode->display();
    }

    private function _vcode_check() {
        $vcode = spClass('spVerifyCode');
        if (!$vcode->verify($this->spArgs('verifycode'))) {
            $this->error(T('captchaerror'), spUrl('main', 'index'));
        }
    }

    public function chgpwd() {
        $this->getLang();
        if (isset($_POST['save'])) {
            $admin = spClass('lib_manager1');
            $admin->verifier = $admin->verifier_chgpwd; // 切换验证规则
            $results = $admin->spVerifier($this->spArgs());
            if (false == $results) {
                $cond = array('username' => trim($_COOKIE['user']));
                $res = $admin->find($cond);
                $password = crypt(trim($_POST['password']), $res['password']);
                if ($password <> $res['password']) {
                    $this->error(T('loginerror'), spUrl('main', 'chgpwd'));
                } else {
                    $array = array(
                        password => md5crypt(trim($_POST['newpassword'])),
                    );
                    $bl = $admin->update($cond, $array);
                    if ($bl) {
                        $setsuccess = T('setsuccess');
                        $this->success($setsuccess, spUrl('main', 'chgpwd'));
                    }
                }
            } else {
                foreach ($results as $item) { // 开始循环错误信息的规则，这里只有用户名
                    // 每一个规则，都有可能返回多个错误信息，所以这里我们也循环$item来获取多个信息
                    foreach ($item as $msg) {
                        // 虽然我们使用了循环，但是这里我们只需要第一条出错信息就行。
                        // 所以取到了第一条错误信息的时候，我们使用$this->error来提示并跳转
                        //echo ($msg);
                        $this->error($msg, $_SERVER['HTTP_REFERER']);
                    }
                }
            }
        } else {
            $this->title = gettitie($_GET['c'], $_GET['a']);
            $this->display('main/chgpwd.html');
        }
    }
    
    private function checkuser() {
        $user = spClass('lib_mailbox');
        $user->verifier = $user->verifier_login; // 切换验证规则
        $results = $user->spVerifier($this->spArgs());
        if (false == $results) {
            $cond = array('username' => trim($_POST['username']));
            $res = $user->find($cond);
            $password = crypt(trim($_POST['password']), $res['password']);
            if ($password <> $res['password']) {
                $this->error(T('loginerror'), spUrl('main', 'index'));
            }
            $recex = $res['expiredate'];
            $curr = date("Y-m-d", time());
            if ($recex <> '0000-00-00') {
                if ($recex <= $curr) {
                    $this->error(T('loginexpire'), spUrl('main', 'index'));
                }
            }
            spClass('spAcl')->set("user");
            setcookie(lang, $_POST['lang'], time() + 86400 * 30);
            $_SESSION['lang'] = $_POST['lang'];
            setcookie(user, $_POST['username'], time() + 86400 * 30);
            $this->setLang($_SESSION['lang']);
            $this->jump(spUrl('user', 'index'));
        } else {
            foreach ($results as $item) { 
                foreach ($item as $msg) {
                    $this->error($msg, spUrl("main", "index"));
                }
            }
        }
    }
    
    private function checkadm() {
        $mng = spClass('lib_manager1');
        $mng->verifier = $mng->verifier_login; // 切换验证规则
        $results = $mng->spVerifier($this->spArgs());
        if (false == $results) {
            $cond = array('username' => trim($_POST['username']));
            $res = $mng->find($cond);
            $password = crypt(trim($_POST['password']), $res['password']);
            if ($password <> $res['password']) {
                $this->error(T('loginerror'), spUrl('main', 'index'));
            }
            $recex = $res['expiredate'];
            $curr = date("Y-m-d", time());
            if ($recex <> '0000-00-00') {
                if ($recex <= $curr) {
                    $this->error(T('loginexpire'), spUrl('main', 'index'));
                }
            }
            switch ($res['type']) {
                case 'admin':
                    spClass('spAcl')->set("admin");
                    break;

                case 'postmaster':
                    spClass('spAcl')->set("postmaster");
                    break;
            }
            setcookie(lang, $_POST['lang'], time() + 86400 * 30);
            $_SESSION['lang'] = $_POST['lang'];
            setcookie(user, $_POST['username'], time() + 86400 * 30);
            $this->setLang($_SESSION['lang']);
            $this->jump(spUrl('main', 'show'));
        } else {
            foreach ($results as $item) { // 开始循环错误信息的规则，这里只有用户名
                // 每一个规则，都有可能返回多个错误信息，所以这里我们也循环$item来获取多个信息
                foreach ($item as $msg) {
                    // 虽然我们使用了循环，但是这里我们只需要第一条出错信息就行。
                    // 所以取到了第一条错误信息的时候，我们使用$this->error来提示并跳转
                    //echo ($msg);
                    $this->error($msg, spUrl("main", "index"));
                }
            }
        }
    }

}
