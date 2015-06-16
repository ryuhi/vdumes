<?php

class cron extends spController {

    public function apply() {
        //Switch the directory to 'tmp'
        chdir(APP_PATH . '/tmp/');

        //Get the files whose name begins from 'vdumes_', these files are VDUMES should be updated. If they begins from 'wwoffice_', they mean used by WWOffice.
        $array = glob("vdumes_*.txt");
        $count = count($array);

        if ($count == 0) {
            exit();
        }

        //cycle each file for update
        foreach ($array as $val) {
            //The files's content seperated by semicolon, from left to right is domain,address,quota.
            $str = file_get_contents(APP_PATH . '/tmp/' . $val);
            $arr = explode(';', $str);

            //The update data
            $arr[] = array(
                'quota' => $arr[2]
            );

            //The condition for update
            $conditions = array(
                'username' => $arr[1],
            );
            $mx = spClass('lib_mailbox');
            $bl = $mx->update($conditions, $arr);

            //Get the affected rows.
            $rows = $mx->affectedRows();

            //If the affected rows equal -1, it means update failure, otherwise it means success.
            if ($rows == -1) {
                $this->spArgs('msg') = 'Update quota failure.';
                $this->writelog();
            } else {
                //If update success, then delete the txt file. If it failed, jump the error message then jump to the index page of mailbox controller.
                if (!unlink($val)) {
                    $this->spArgs('msg') = 'The file is failed by deleting.';
                    $this->writelog();
                }
                return TRUE;
            }
        }
    }

    //Generate the scheduled task for synchronizing the quota.
    public function addcron() {
        $os = php_uname('s');

        $file = APP_PATH . '/index.php?c=cron&a=apply';


        switch ($os) {
            case substr($os, 0, 7) == 'Windows':
                exec("schtasks /create /sc minute /mo 5 /tn 'update_quota' /tr . $file . /ru 'System'");
                break;

            case substr($os, 0, 5) == 'Linux':
                switch ($_SERVER['SERVER_PORT']) {
                    case 80:
                        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                        break;

                    case 443:
                        $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                        break;

                    default:
                        $url = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
                }

                $cmdline = '*/5 * * * * /usr/bin/curl' . $url;

                exec('crontab -e <' . $cmdline);
                break;
        }
    }

    //If error occured, the write to log
    private function writelog() {

        $dir = APP_PATH . 'tmp/log';
        //IF 'tmp/log' is file, delete it.
        if (is_file($dir)) {
            unlink(APP_PATH . 'tmp/log');
        }
        //IF 'tmp/log' is not exists, create it as folder.
        if (!dir_exists($dir)) {
            __mkdirs(APP_PATH . 'tmp/log');
        }
        //IF 'tmp/log' is exists as folder, the create logs under it.
        if (dir_exists($dir) && is_dir($dir)) {
            $date = date("Y-m-d H:i:s", time());
            $arr = array('-', ':');
            $date = trim(str_replace($arr, '', $date));
            $cnt = $this->spArgs('msg');
            $str = $date . '\t' . $cnt;
            file_put_contents(APP_PATH . 'tmp/log/log_' . $date . '.log', $str, FILE_APPEND);
        }
        return TRUE;
    }

}

?>
