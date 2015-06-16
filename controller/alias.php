<?php
class alias extends spController {

    public function index() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $mb = spClass('lib_alias');
        if (!isset($_POST['cond'])) {
            $conditions = null;
        } elseif (empty($_POST['cond'])) {
            $conditions = null;
        } else {
            $conditions = "address like '{$_POST['cond']}%' or goto like '%{$_POST['cond']}%'";
        }
        $this->rs = $mb->spPager($this->spArgs('page', 1), 25)->findAll($conditions);
        $this->pager = $mb->spPager()->getPager();
        $this->count = $mb->findCount();
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display('alias/index.html');
    }

    public function add() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
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
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("alias/add.html");
    }

    public function insert() {
        if (isset($_POST['save'])) {
            $count = $this->chkcount();
            if ($count == 0) {
                $search = array("\n", "\r\n", "\r", ",,");
                $array = array(
                    address => trim($_POST['username']) . '@' . trim($_POST['domain']),
                    "goto" => str_replace($search, ',', trim($_POST['goto'])),
                    domain => trim($_POST['domain']),
                    createdate => date("Y-m-d H:i:s", time()),
                    active => $_POST['active'],
                );
                $als = spClass('lib_alias');
                $als->verifier = $als->verifier_add; // 切换验证规则
                $results = $als->spVerifier($this->spArgs());
                if (false == $results) {
                    $als->create($array);
                    if (0 <= $als->affectedRows()) {
                        $setsuccess = T('setsuccess');
                        $this->success($setsuccess, spUrl('alias', 'index'));
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
                $morealias = T('morealias');
                $this->error($morealias, spUrl('alias', 'add'));
            }
        }
    }

    public function edit() {
        $this->getLang();
        $this->role = spClass('spAcl')->get();
        $conditions = array('address' => $_GET['aid']);
        $dmdt = spClass('lib_alias');
        $this->detail = $dmdt->find($conditions);
        $this->getView()->registerPlugin("modifier", "showgoto", "showgoto");
        $this->title = gettitie($_GET['c'], $_GET['a']);
        $this->display("alias/edit.html");
    }

    public function update() {
        if (isset($_POST['save'])) {
            $conditions = array('address' => $_POST['hidalias']);
            $search = array("\n", "\r\n", "\r", ",,");
            $array = array(
                "goto" => str_replace($search, ',', trim($_POST['directurl'])),
                active => $_POST['active'],
            );
            $als = spClass('lib_alias');
            $als->verifier = $als->verifier_update; // 切换验证规则
            $results = $als->spVerifier($array);
            if (false == $results) {
                $bl = $als->update($conditions, $array);
                if ($bl) {
                    $setsuccess = T('setsuccess');
                    $this->success($setsuccess, spUrl('alias', 'index'));
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

    public function rm() {
        $this->getLang();
        $conditions = array('address' => $_GET['aid']);
        $al = spClass('lib_alias');
        $bl = $al->delete($conditions);
        if ($bl) {
            $rmsuccess = T('rmsuccess');
            $this->success($rmsuccess, spUrl('alias', 'index'));
        }
    }

    // 检查是否有重名
    public function chkclone() {
        $this->getLang();
        $conditions = array('address' => $_GET['username'] . '@' . $_GET['domain']);
        $al = spClass('lib_alias');
        $count = $al->findCount($conditions);
        $cndmx = array('username' => $_GET['username'] . '@' . $_GET['domain']);
        $mx = spClass('lib_mailbox');
        $cntmx = $mx->findCount($cndmx);
        if ($count >= 1 || $cntmx >= 1) {
            $result = array(
                'status' => 0, // 失败标志
                'message' => T('existuser'),
            ); // 提示信息             
        } elseif ($count == 0 && $cntmx == 0) {
            $result = array(
                'status' => 1, // 成功标志
                'message' => T('noexistuser'),
            ); // 提示信息
        }
        echo json_encode($result); // 返回（显示）JSON结果
    }

    // 检查添加的别名是否已到上限
    private function chkcount() {
        $this->getLang();
        $conditions = array('domain' => trim($_POST['domain']));
        $al = spClass('lib_alias');
        $count = $al->findCount($conditions);
        $dm = spClass('lib_domain');
        $maxal = $dm->find($conditions, null, 'maxalias');
        if ($count + 1 > $maxal['maxalias']) {
            return 1;
        } else {
            return 0;
        }
    }

}
?>