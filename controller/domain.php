<?php

class domain extends spController {

    function index() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $au = spClass('lib_domain');
        $conditions = '';
        $res = $au->spPager($this->spArgs('page', 1), 25)->findAll($conditions, 'createdate DESC');
        $this->pager = $au->spPager()->getPager();
        $res1 = $au->findAll();
        $this->count = count($res1);
        foreach ($res as $key => $val) {
            $rsall[$key]['domain'] = $val['domain'];
            $rsall[$key]['maxusers'] = $val['maxusers'];
            $conditions = array('domain' => $val['domain']);
            $dm = spClass('lib_mailbox');
            $curusr = $dm->findCount($conditions);
            $rsall[$key]['curusers'] = $curusr;
            $al = spClass('lib_alias');
            $curalias = $al->findCount($conditions);
            $rsall[$key]['maxalias'] = $val['maxalias'];
            $rsall[$key]['curalias'] = $curalias;
            $rsall[$key]['maxquota'] = $val['maxquota'];
        }
        $this->crtfirst = $au->find(null, 'createdate ASC', 'domain');
        $this->rs = $rsall;
        $this->getView()->registerPlugin("modifier", "size", "sizecount");
        $this->langindex = $lang;
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("domain/index.html");
    }

    function add() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("domain/add.html");
    }

    function insert() {
        if (isset($_POST['save'])) {
            if (strtolower(SYS_ISP_MODE) <> 'yes') {
                $hashpath = 'A0/B0';
            }
            $array = array(
                domain => trim($_POST['domain']),
                description => trim($_POST['description']),
                hashdirpath => $hashpath,
                maxalias => $_POST['maxalias'],
                maxusers => $_POST['maxusers'],
                maxquota => $_POST['maxquota'] * 1024 * 1024,
                transport => $_POST['transport'],
                can_signup => isset($_POST['can_signup']) ? $_POST['can_signup'] : 0,
                default_quota => $_POST['default_quota'] * 1024 * 1024,
                default_expire => $_POST['default_expire'],
                disablesmtpd => $_POST['SERVICES_smtpd'],
                disablesmtp => $_POST['SERVICES_smtp'],
                disablepop3 => $_POST['SERVICES_pop3'],
                createdate => date("Y-m-d H:i:s", time()),
                expiredate => $_POST['expiredate'],
                active => $_POST['active'],
            );
            $domain = spClass('lib_domain');
            $domain->verifier = $domain->verifier_add; // 切换验证规则
            $results = $domain->spVerifier($array);
            if (false == $results) {
                $domain->create($array);
                $newdm = trim($_POST['domain']);
                if (strtolower(SYS_ISP_MODE) <> 'yes') {
                    mkdir(SYS_MAILDIR_BASE . '/' . "$newdm", 0700);
                }
                if (0 <= $domain->affectedRows()) {
                    $setsuccess = T('setsuccess');
                    $this->success($setsuccess, spUrl('domain', 'index'));
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
        }
    }

    function edit() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $conditions = array('domain' => $_GET['did']);
        $dmdt = spClass('lib_domain');
        $this->detail = $dmdt->find($conditions);
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("domain/edit.html");
    }

    function update() {
        if (isset($_POST['save'])) {
            $conditions = array('domain' => $_GET['did']);
            $array = array(
                description => $_POST['description'],
                maxalias => $_POST['maxalias'],
                maxusers => $_POST['maxusers'],
                maxquota => $_POST['maxquota'] * 1024 * 1024,
                transport => $_POST['transport'],
                can_signup => $_POST['cansignup'],
                default_quota => $_POST['defaultquota'] * 1024 * 1024,
                default_expire => $_POST['defaultexpire'],
                disablesmtpd => $_POST['SERVICES_smtpd'],
                disablesmtp => $_POST['SERVICES_smtp'],
                disablepop3 => $_POST['SERVICES_pop3'],
                expiredate => $_POST['expire'],
                active => $_POST['active'],
            );
            $domain = spClass('lib_domain');
            $domain->verifier = $domain->verifier_update; // 切换验证规则
            $results = $domain->spVerifier($array);
            if (false == $results) {
                $bl = $domain->update($conditions, $array);
                if ($bl) {
                    $setsuccess = T('setsuccess');
                    $this->success($setsuccess, spUrl('domain', 'index'));
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
        }
    }

    function rm() {
        $conditions = array('domain' => $_GET['did']);
        $mx = spClass('lib_mailbox');
        $curusr = $mx->findCount($conditions);
        if ($curusr > 0) {
            $stillremain = T('stillremain');
            $this->error($stillremain, spUrl('mailbox', 'index'));
        } else {
            $dm = spClass('lib_domain');
            $bl = $dm->delete($conditions);
            $homedir = SYS_MAILDIR_BASE . '/' . $_GET['did'];
            if (php_uname('s') == 'Windows NT') {
                $cmd = 'rd "' . $homedir . '" /s /q';
            } elseif (php_uname('s') == 'Linux') {
                $cmd = 'rm -rf ' . $homedir;
            }
            exec($cmd);
            if ($bl) {
                $rmsuccess = T('rmsuccess');
                $this->success($rmsuccess, spUrl('domain', 'index'));
            }
        }
    }

    // 检查是否有重名
    function chkclone() {
        $this->getLang();
        $conditions = array('domain' => $_REQUEST['domain']);
        $dm = spClass('lib_domain');
        $count = $dm->findCount($conditions);
        if ($count == 1) {
            $result = array(
                'status' => 0, // 失败标志
                'message' => T('existdomain'),
            ); // 提示信息             
        } elseif ($count == 0) {
            $result = array(
                'status' => 1, // 成功标志
                'message' => T('noexistdomain'),
            ); // 提示信息
        }
        echo json_encode($result); // 返回（显示）JSON结果
    }

}
?>