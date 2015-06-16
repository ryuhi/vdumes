<?php

class manager extends spController {

    public function index() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $mb = spClass('lib_manager');
        if (!isset($_POST['type']) && !isset($_POST['name'])) {
            $conditions = null;
        } elseif (empty($_POST['name'])) {
            $conditions = null;
        } else {
            $conditions = "{$_POST['type']} like '{$_POST['name']}%'";
        }
        $this->rs = $mb->spPager($this->spArgs('page', 1), 25)->findAll($conditions);
        $this->pager = $mb->spPager()->getPager();
        $this->count = $mb->findCount();
        $this->getView()->registerPlugin("modifier", "size", "sizecount");
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display('manager/index.html');
    }

    public function add() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $conditions = array('active' => 1);
        $dm = spClass('lib_domain');
        $this->dms = $dm->findAll($conditions, 'createdate DESC', 'domain');
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("manager/add.html");
    }

    public function insert() {
        if (isset($_POST['save'])) {
            $array = array(
                username => trim($_POST['username']),
                password => md5crypt(trim($_POST['password'])),
                type => 'postmaster',
                uid => '',
                name => $_POST['name'],
                question => $_POST['safeask'],
                answer => $_POST['safeanswer'],
                disablepwdchange => $_POST['disablepwdchange'],
                createdate => date("Y-m-d H:i:s", time()),
                expiredate => $_POST['expiredate'],
                active => $_POST['active'],
            );
            foreach ($_POST['domain'] as $val) {
                $dmarray[] = array(
                    username => trim($_POST['username']),
                    domain => $val,
                    createdate => date("Y-m-d H:i:s", time()),
                    active => $_POST['active'],
                );
            }
            $admin = spClass('lib_manager1');
            $admin->verifier = $admin->verifier_add; // 切换验证规则
            $results = $admin->spVerifier($this->spArgs());
            if (false <> $results) {
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
            if (!is_array($_POST['domain']) || (is_array($_POST['domain']) && count($_POST['domain']) == 0)) {
                $this->error(T('notnull_domain'), $_SERVER['HTTP_REFERER']);
            }
            $admin->create($array);
            $dadm = spClass('lib_domain_manager');
            $dadm->createAll($dmarray);
            if (0 <= $admin->affectedRows() && 0 <= $dadm->affectedRows()) {
                $setsuccess = T('setsuccess');
                $this->success($setsuccess, spUrl('manager', 'index'));
            }
        }
    }

    public function edit() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $conditions = array('active' => 1);
        $dm = spClass('lib_domain');
        $dms = $dm->findAll($conditions, 'createdate DESC', 'domain');
        $conditions = array('username' => $_GET['adm']);
        $dmdt = spClass('lib_manager');
        $detail = $dmdt->spLinker()->find($conditions);
        foreach ($detail['dmm'] as $key => $value) {
            $vdm = $value['domain'];
            foreach ($dms as $k => $val) {
                if ($val['domain'] == $vdm) {
                    $dms[$k]['chose'] = 1;
                }
            }
        }
        $this->detail = $detail;
        $this->dms = $dms;
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("manager/edit.html");
    }

    public function update() {
        if (isset($_POST['save'])) {
            $conditions = array('username' => $_POST['username']);
            $array = array(
                name => trim($_POST['name']),
                expiredate => $_POST['expiredate'],
                question => $_POST['safeask'],
                answer => $_POST['safeanswer'],
                active => $_POST['active'],
            );
            if (trim($_POST['password']) == trim($_POST['verfiypass']) && !empty($_POST['password'])) {
                $array['password'] = md5crypt(trim($_POST['password']));
            }
            $manager = spClass('lib_manager1');

            $manager->verifier = $manager->verifier_update; // 切换验证规则
            $results = $manager->spVerifier($this->spArgs());
            if (false <> $results) {
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
            if (!is_array($_POST['domain']) || (is_array($_POST['domain']) && count($_POST['domain']) == 0)) {
                $this->error(T('notnull_domain'), $_SERVER['HTTP_REFERER']);
            }
            $manager->update($conditions, $array);
            $dm = spClass('lib_domain_manager');
            $dm->delete($conditions);
            foreach ($_POST['domain'] as $val) {
                $dmarray[] = array(
                    username => trim($_POST['username']),
                    domain => $val,
                    createdate => date("Y-m-d H:i:s", time()),
                    active => $_POST['active'],
                );
            }
            $dm->createAll($dmarray);
            if ($manager->affectedRows() >= 0 && $dm->affectedRows() >= 0) {
                $setsuccess = T('setsuccess');
                $this->success($setsuccess, spUrl('manager', 'index'));
            }
        }
    }

    public function rm() {
        $this->getLang();
        $conditions = array('username' => $_GET['adm']);
        $admin = spClass('lib_manager');
        $bl = $admin->delete($conditions);
        $dadm = spClass('lib_domain_manager');
        $bl2 = $dadm->delete($conditions);
        if ($bl) {
            $rmsuccess = T('rmsuccess');
            $this->success($rmsuccess, spUrl('manager', 'index'));
        }
    }

    // 检查是否有重名
    public function chkclone() {
        $this->getLang();
        $conditions = array('username' => $_GET['username']);
        $mx = spClass('lib_manager');
        $count = $mx->findCount($conditions);
        if ($count == 1) {
            $result = array(
                'status' => 0, // 失败标志
                'message' => T('existadmin'),
            ); // 提示信息             
        } elseif ($count == 0) {
            $result = array(
                'status' => 1, // 成功标志
                'message' => T('noexistadmin'),
            ); // 提示信息
        }
        echo json_encode($result); // 返回（显示）JSON结果
    }

}

?>