<?php

//将以字节为单位的文件大小转换成可读的MB、GB之类的格式
function sizecount($size, $dec = 2) {
    $size = str_replace('S', '', $size);
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, $dec) . " " . $a[$pos];
}

//--------------------md5crypt加密函数系列 开始-------------------
function pacrypt($pw, $pw_db = "") {
    $password = "";
    $salt = "";
    $split_salt = preg_split('/\$/', $pw_db);
    if (isset($split_salt[2])) {
        $salt = $split_salt[2];
    }
    $password = md5crypt($pw, $salt);
    return $password;
}

//  
// md5crypt  
// Action: Creates MD5 encrypted password  
// Call: md5crypt (string cleartextpassword)  
//    
function md5crypt($pw, $salt = "", $magic = "") {
    $MAGIC = "$1$";

    if ($magic == "")
        $magic = $MAGIC;
    if ($salt == "")
        $salt = e_salt();
    $slist = explode("$", $salt);
    if ($slist[0] == "1")
        $salt = $slist[1];

    $salt = substr($salt, 0, 8);
    $ctx = $pw . $magic . $salt;
    $final = hex2bin(md5($pw . $salt . $pw));

    for ($i = strlen($pw); $i > 0; $i-=16) {
        if ($i > 16) {
            $ctx .= substr($final, 0, 16);
        } else {
            $ctx .= substr($final, 0, $i);
        }
    }
    $i = strlen($pw);

    while ($i > 0) {
        if ($i & 1)
            $ctx .= chr(0);
        else
            $ctx .= $pw[0];
        $i = $i >> 1;
    }
    $final = hex2bin(md5($ctx));

    for ($i = 0; $i < 1000; $i++) {
        $ctx1 = "";
        if ($i & 1) {
            $ctx1 .= $pw;
        } else {
            $ctx1 .= substr($final, 0, 16);
        }
        if ($i % 3)
            $ctx1 .= $salt;
        if ($i % 7)
            $ctx1 .= $pw;
        if ($i & 1) {
            $ctx1 .= substr($final, 0, 16);
        } else {
            $ctx1 .= $pw;
        }
        $final = hex2bin(md5($ctx1));
    }
    $passwd = "";
    $passwd .= to64(((ord($final[0]) << 16) | (ord($final[6]) << 8) | (ord($final[12]))), 4);
    $passwd .= to64(((ord($final[1]) << 16) | (ord($final[7]) << 8) | (ord($final[13]))), 4);
    $passwd .= to64(((ord($final[2]) << 16) | (ord($final[8]) << 8) | (ord($final[14]))), 4);
    $passwd .= to64(((ord($final[3]) << 16) | (ord($final[9]) << 8) | (ord($final[15]))), 4);
    $passwd .= to64(((ord($final[4]) << 16) | (ord($final[10]) << 8) | (ord($final[5]))), 4);
    $passwd .= to64(ord($final[11]), 2);
    return "$magic$salt\$$passwd";
}

function e_salt() {
    srand((double) microtime() * 1000000);
    $salt = substr(md5(rand(0, 9999999)), 0, 8);
    return $salt;
}

/**/ if (!function_exists('hex2bin')) { # PHP around 5.3.8 includes hex2bin as native function - http://php.net/hex2bin  

    function hex2bin($str) {
        $len = strlen($str);
        $nstr = "";
        for ($i = 0; $i < $len; $i+=2) {
            $num = sscanf(substr($str, $i, 2), "%x");
            $nstr.=chr($num[0]);
        }
        return $nstr;
    }

    /**/
}

function to64($v, $n) {
    $ITOA64 = "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $ret = "";
    while (($n - 1) >= 0) {
        $n--;
        $ret .= $ITOA64[$v & 0x3f];
        $v = $v >> 6;
    }
    return $ret;
}

//--------------------md5crypt加密函数系列 结束-------------------
function showgoto($string) {
    $arr = explode(',', $string);
    return $str = implode("\n", $arr);
}

function chkdm($val, $right) {
    return $right === (preg_match('/^([A-Za-z0-9-]+\.)+[A-Za-z0-9]+$/', $val) != 0 && strlen($val) < 253);
    /* if ($times >= 0) {
      return true;
      } else {
      return false;
      } */
}

function is_english($val, $right) {
    return $right == ( preg_match('/^[A-Z@a-z0-9-_]$/', $val) != 0 );
}

function istime1($val, $right) {
    $test = @strtotime($val);
    return $right == ( $test !== -1);
}

//根据Ctroller的名字和action的名字获得页面标题
function gettitie($controller, $action) {
    $als = spClass('lib_acl');
    $conditions = array(
        controller => $controller,
        action => $action,
    );
    $res = $als->findAll($conditions, null, 'name');
    foreach ($res as $key => $val) {
        $arr[] = $val['name'];
    }
    return $record = implode('', array_unique($arr));
}

//检查输入密码的强度
function passwordStrength($password, $username) {
    $badPass = 1;
    $notusr = 2;
    $goodPass = 3;
    $strongPass = 4;
    $symbolSize = 0;

    //Get the username in email address(before the last '@');
    $pos = strpos(strrev($username), '@', 1);
    $usr = strrev(substr(strrev($username), $pos + 1));
    $curpass = strtolower($password);
    $curusr = strtolower($usr);
    // If the password includes the username, return a message 
    if (strpos($curpass,$curusr) !== false ) {
        return $message = $notusr;
    }
    if (preg_match("/[0-9]/", $password)) {
        $symbolSize += 10;
    }

    if (preg_match("/[a-z]/", $password)) {
        $symbolSize += 26;
    }

    if (preg_match("/[A-Z]/", $password)) {
        $symbolSize += 26;
    }

    if (preg_match("/[\W]/", $password)) {
        $symbolSize += 31;
    }

    $natLog = log(pow($symbolSize, strlen($password)));

    $score = $natLog / log(2);

    if ($score < 40) {
        return $message = $badPass;
    }
    if ($score < 56) {
        return $message = $goodPass;
    }
    return $message = $strongPass;
    
}


?>