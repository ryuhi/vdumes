<?php

class user extends spController {
    public function index() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        if (isset($_POST['save'])) {
            $mx = spClass('lib_mailbox');
            $mx->verifier = $mx->verifier_chgpwd; // 切换验证规则
            $results = $mx->spVerifier($this->spArgs());
            if (false == $results) {
                $status = passwordStrength(trim($_POST['newpassword']),trim($_POST['user']));
                if (in_array($status, array(1,2))) {
                    switch($status) {
                        case 1:
                        $setfail = T('badPass');
                        $this->error($setfail, spUrl('user', 'index'));
                        break;
                    
                        case 2:
                        $setfail = T('notusr');
                        $this->error($setfail, spUrl('user', 'index'));
                        break;   
                    }
                }
                $cond = array('username' => trim($_COOKIE['user']));
                $res = $mx->find($cond);
                $password = crypt(trim($_POST['password']), $res['password']);
                if ($password <> $res['password']) {
                    $this->error(T('loginerror'), spUrl('user', 'index'));
                } else {
                    $array = array(
                        password => md5crypt(trim($_POST['newpassword'])),
                    );
                    $bl = $mx->update($cond, $array);
                    if ($bl) {
                        $setsuccess = T('setsuccess');
                        $this->success($setsuccess, spUrl('user', 'index'));
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
            $this->display('user/chgpwd.html');
        }
    }
}
?>
