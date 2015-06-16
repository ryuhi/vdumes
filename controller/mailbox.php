<?php

class mailbox extends spController {

    public function index() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $dms = $this->dmlist();

        $mb = spClass('lib_mailbox');
        foreach ($dms as $key => $val) {
            if (!isset($_POST['type']) && !isset($_POST['name'])) {
                $this->conditions = null;
            } elseif (empty($_POST['name'])) {
                $this->conditions = null;
            } else {
                $this->conditions = "{$_POST['type']} like '{$_POST['name']}%'";
            }
            $this->conditions = array(
                domain => $val['domain'],
            );

            $val['result'] = $mb->spPager($this->spArgs('page', 1), 30)->findAll($this->conditions);
            $val['pager'] = $mb->spPager()->getPager();
            $mx[] = $val;
        }
        $this->rs = $mx;
        $this->count = $mb->findCount();
        $this->getView()->registerPlugin("modifier", "size", "sizecount");
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display('mailbox/index.html');
    }

    public function add() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $this->dmlist();
        $this->size = $this->chksize();
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("mailbox/add.html");
    }

    public function insert() {
        if (isset($_POST['save'])) {
            $count = $this->chkcount();
            if ($count == 0) {
                $array = array(
                    username => trim($_POST['username']) . '@' . trim($_POST['domain']),
                    uid => trim($_POST['username']),
                    password => md5crypt($_POST['password']),
                    clearpwd => '',
                    name => $_POST['name'],
                    mailhost => '',
                    maildir => trim($_POST['domain']) . '/' . trim($_POST['username']) . '/Maildir',
                    homedir => trim($_POST['domain']) . '/' . trim($_POST['username']),
                    quota => $_POST['quota'] * 1024 * 1024 . 'S',
                    netdiskquota => 0,
                    domain => trim($_POST['domain']),
                    uidnumber => $_POST['uidnumber'],
                    gidnumber => $_POST['gidnumber'],
                    createdate => date("Y-m-d H:i:s", time()),
                    expiredate => $_POST['expiredate'],
                    active => $_POST['active'],
                    disablepwdchange => $_POST['disablepwdchange'],
                    disablesmtpd => $_POST['SERVICES_smtpd'],
                    disablesmtp => $_POST['SERVICES_smtp'],
                    disablewebmail => 0,
                    disablenetdisk => 1,
                    disableimap => 1,
                    disablepop3 => $_POST['SERVICES_pop3'],
                    question => $_POST['safeask'],
                    answer => $_POST['safeanswer'],
                );
                $mx = spClass('lib_mailbox');
                $mx->verifier = $mx->verifier_add; // 切换验证规则
                $results = $mx->spVerifier($this->spArgs());
                if (false == $results) {
                    $mx->create($array);
                    if (strtolower(SYS_ISP_MODE) == 'no') {
                        $newdm = trim($_POST['domain']);
                        $newname = trim($_POST['username']);
                        $cur = SYS_MAILDIR_BASE . '/' . "$newdm" . '/' . "$newname" . '/Maildir/cur';
                        $new = SYS_MAILDIR_BASE . '/' . "$newdm" . '/' . "$newname" . '/Maildir/new';
                        $tmp = SYS_MAILDIR_BASE . '/' . "$newdm" . '/' . "$newname" . '/Maildir/tmp';
                        __mkdirs($cur, 0700);
                        __mkdirs($new, 0700);
                        __mkdirs($tmp, 0700);
                    }
                    if (0 <= $mx->affectedRows() && is_dir($cur) && is_dir($new) && is_dir($tmp)) {
                        $setsuccess = T('setsuccess');
                        $this->success($setsuccess, spUrl('mailbox', 'index'));
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
                $moreuser = T('moreuser');
                $this->error($moreuser, spUrl('mailbox', 'add'));
            }
        }
    }

    public function edit() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $conditions = array('username' => $_GET['mid']);
        $dmdt = spClass('lib_mailbox');
        $this->detail = $dmdt->find($conditions);
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("mailbox/edit.html");
    }

    public function update() {
        if (isset($_POST['save'])) {
            $conditions = array('username' => $_POST['dm']);
            $tmp = explode('@', $_POST['dm']);
            $domain = $tmp[1];
            $array = array(
                username => trim($_POST['username']) . '@' . $domain,
                name => trim($_POST['name']),
                quota => $_POST['quota'] * 1024 * 1024 . 'S',
                expiredate => $_POST['expire'],
                active => $_POST['active'],
                question => $_POST['safeask'],
                answer => $_POST['safeanswer'],
                uidnumber => $_POST['userid'],
                gidnumber => $_POST['groupid'],
                disablesmtpd => $_POST['SERVICES_smtpd'],
                disablesmtp => $_POST['SERVICES_smtp'],
                disablepop3 => $_POST['SERVICES_pop3'],
            );

            if (trim($_POST['password']) == trim($_POST['verfiypass']) && !empty($_POST['password'])) {
                $array['password'] = md5crypt(trim($_POST['password']));
                //检查密码强度，获得强度状态
                $status = passwordStrength(trim($_POST['password']), trim($_POST['username']));
                //如果状态值为1或者2,1为密码强度不足，2为包含用户名，均返回修改界面
                if (in_array($status, array(1, 2))) {
                    switch ($status) {
                        case 1:
                            $setfail = T('badPass');
                            $this->error($setfail, spUrl('mailbox', 'edit', array(mid => $array['username'])));
                            break;

                        case 2:
                            $setfail = T('notusr');
                            $this->error($setfail, spUrl('mailbox', 'edit', array(mid => $array['username'])));
                            break;
                    }
                }
            }

            $mx = spClass('lib_mailbox');
            $mx->verifier = $mx->verifier_update; // 切换验证规则
            $results = $mx->spVerifier($this->spArgs());
            if (false == $results) {
                $bl = $mx->update($conditions, $array);
                if ($bl) {
                    $setsuccess = T('setsuccess');
                    $this->success($setsuccess, spUrl('mailbox', 'index'));
                }
            } else {
                foreach ($results as $item) { // 开始循环错误信息的规则，这里只有用户名
                    // 每一个规则，都有可能返回多个错误信息，所以这里我们也循环$item来获取多个信息
                    foreach ($item as $msg) {
                        // 虽然我们使用了循环，但是这里我们只需要第一条出错信息就行。
                        // 所以取到了第一条错误信息的时候，我们使用$this->error来提示并跳转
                        $this->error($msg, $_SERVER['HTTP_REFERER']);
                    }
                }
            }
        }
    }

    public function rm() {
        $this->getLang();
        $conditions = array('username' => $_GET['mid']);
        $mx = spClass('lib_mailbox');
        $home = $mx->find($conditions, null, 'homedir');
        $bl = $mx->delete($conditions);
        if ($_GET['delall'] == 1) {
            $homedir = SYS_MAILDIR_BASE . '/' . $home['homedir'];
            if (php_uname('s') == 'Windows NT') {
                $cmd = 'rd "' . $homedir . '" /s /q';
            } elseif (php_uname('s') == 'Linux') {
                $cmd = 'rm -rf ' . $homedir;
            }
            exec($cmd);
        }
        if ($bl) {
            $rmsuccess = T('rmsuccess');
            $this->success($rmsuccess, spUrl('mailbox', 'index'));
        }
    }

    // 检查是否有重名
    public function chkclone() {
        $this->getLang();
        $conditions = array('username' => $_GET['username'] . '@' . $_GET['domain']);
        $mx = spClass('lib_mailbox');
        $count = $mx->findCount($conditions);
        if ($count == 1) {
            $result = array(
                'status' => 0, // 失败标志
                'message' => T('existuser'),
            ); // 提示信息             
        } elseif ($count == 0) {
            $result = array(
                'status' => 1, // 成功标志
                'message' => T('noexistuser'),
            ); // 提示信息
        }
        echo json_encode($result); // 返回（显示）JSON结果
    }

    // 检查添加的邮箱是否已到上限
    private function chkcount() {
        $this->getLang();
        $conditions = array('domain' => trim($_POST['domain']));
        $mb = spClass('lib_mailbox');
        $count = $mb->findCount($conditions);
        $dm = spClass('lib_domain');
        $maxdm = $dm->find($conditions, null, 'maxusers');
        if ($count + 1 > $maxdm['maxusers']) {
            return 1;
        } else {
            return 0;
        }
    }

    // 获取当前选中域名的默认磁盘限额 JSON版
    public function checksize() {
        $dm = spClass('lib_domain');
        $sql = "SELECT `default_quota` FROM `domain` WHERE `domain` = '{$_GET['domain']}'";
        $dmsize = $dm->findsql($sql);
        echo json_encode($dmsize[0]['default_quota'] / 1024 / 1024);
    }

    // 获取当前选中域名的默认磁盘限额
    private function chksize() {
        $dm = spClass('lib_domain');
        $sql = "SELECT `default_quota` FROM `domain` WHERE `domain` = '{$_GET['domain']}'";
        $dmsize = $dm->findsql($sql);
        return $dmsize[0]['default_quota'] / 1024 / 1024;
    }

    private function dmlist() {
        if ($this->role == 'postmaster') {
            $cond = array('username' => $_COOKIE['user'], 'active' => 1);
            $mng = spClass('lib_domain_manager');
            $dms = $mng->findAll($cond, null, 'domain');
            $this->dms = $dms;
        } elseif ($this->role == 'admin') {
            $conditions = array('active' => 1);
            $dm = spClass('lib_domain');
            $this->dms = $dm->findAll($conditions, 'createdate DESC', 'domain');
        }
        return $this->dms;
    }

}

?>
