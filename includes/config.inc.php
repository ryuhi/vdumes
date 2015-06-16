<?php

# maildir_base, the base dir of user maildir, use absolute path
# if not set. 
define('SYS_MAILDIR_BASE', '/home/domains');
# define('SYS_MAILDIR_BASE','E:/mailbox');
# sys_sess_dir, the session dir
define('SYS_SESS_DIR', '/var/www/extsuite/mmg/tmp');

# sys_captcha_on 1|0 - to enable captcha feature or not
define('SYS_CAPTCHA_ON', 1);

# sys_captcha_key
define('SYS_CAPTCHA_KEY', '434038');

# sys_captcha_len
define('SYS_CAPTCHA_LEN', 6);

# sys_sess_timeout, session timeout in seccond, default 6 hours
# SYS_SESS_TIMEOUT = 21600
# sys_user_lang, user default language
# SYS_LANG = en_US
# sys_app_type, the app type: WebMail or ExtMan? It must be the same
# as prefix part of language package name, eg: WebMail::en_US
define('SYS_APP_TYPE', 'VDUMES - Virtual Domains Unicode Management for Extmail Solutions');

# web management related restritions
# sys_default_expire, valid value: ?y ?m ?d
define('SYS_DEFAULT_EXPIRE', '1y');

# sys_groupmail_sender - sender for groupmail, this account must
# exist or postfix or other mta will complain error
//SYS_GROUPMAIL_SENDER = postmaster@extmail.org
# sys_default_services, valid value: smtpd, smtp, webmail, netdisk,
# imap and pop3, concatenate with "," as multiple values, eg: webmail,smtpauth
define('SYS_DEFAULT_SERVICES', 'smtpd,smtp,pop3');

# sys_isp_mode, yes|no - if yes, use our HashDir to spread
# storage to multiple directories
define('SYS_ISP_MODE', 'no');

# sys_domain_hashdir = yes|no, if yes we will enable domain hashdir
# depend on sys_isp_mode = yes
define('SYS_DOMAIN_HASHDIR', 'yes');

# sys_domain_hashdir_depth, the hash length and depth, format:
# length x depth, eg: 2x1 => length =2, depth =1
# depend on sys_isp_mode = yes
define('SYS_DOMAIN_HASHDIR_DEPTH', '2x2');

# sys_user_hashdir = yes|no, if yes we will enable user hashdir
# depend on sys_isp_mode = yes
define('SYS_USER_HASHDIR', 'yes');

# sys_user_hashdir_depth, similar to sys_hashdir_domain_depth
define('SYS_USER_HASHDIR_DEPTH', '2x2');

# XXX FIXME
# experimental feature, per domain tranport/routing capability
# same config style as SYS_USER_ROUTING_LIST
# SYS_DOMAIN_ROUTING_LIST = lmtp:mx1.extmail.org,lmtp:mx2.extmail.org
# XXX FIXME
# experimental feature, per user routing capability
# please specify routing info, concatenate with "," as multiple list
# members, eg: smtp:mx1.abc.com,smtp:mx2.abc.com
# SYS_USER_ROUTING_LIST = smtp:[192.168.2.130],smtp:[192.168.2.128]
# sys_min_uid, the minimal uid
//SYS_MIN_UID = 500
# sys_min_gid, the minimal gid
//SYS_MIN_GID = 100
# sys_default_uid, if not set, webman will ignore it
define('SYS_DEFAULT_UID', 1000);

# sys_default_gid, if not set, webman will ignore it
define('SYS_DEFAULT_GID', 1000);

# sys_quota_multiplier, in bytes, default to 1 MB
define('SYS_QUOTA_MULTIPLIER', 1048576);

# sys_quota_type, valid type: vda|courier
define('SYS_QUOTA_TYPE', 'courier');

# maxquota, alias, users and netdisk quota for domain
define('SYS_DEFAULT_MAXQUOTA', 500);
define('SYS_DEFAULT_MAXALIAS', 100);
define('SYS_DEFAULT_MAXUSERS', 100);
define('SYS_DEFAULT_MAXNDQUOTA', 500);

# per user default quota, netdisk quota and expire
define('SYS_USER_DEFAULT_QUOTA', 5);
define('SYS_USER_DEFAULT_NDQUOTA', 5);
define('SYS_USER_DEFAULT_EXPIRE', '1y');

# sys_crypt_type: crypt|cleartext|plain|md5|md5crypt|plain-md5|ldap-md5|sha|sha1
define('SYS_CRYPT_TYPE', 'md5crypt');

# comment it if you only want to save crypted password
# we highly recommend that you disable the following line :)
# SYS_MYSQL_ATTR_CLEARPW = clearpwd
# comment it if you only want to save crypted password
# we highly recommend that you disable the following line :)
# SYS_LDAP_ATTR_CLEARPW = clearPassword
# sys_rrd_datadir, the full path of rrd data
//SYS_RRD_DATADIR = /var/lib
# sys_rrd_tmpdir, the temp dir for graph
//SYS_RRD_TMPDIR = /tmp/viewlog
# sys_rrd_queue_on, yes|no, show queue or not
//SYS_RRD_QUEUE_ON = yes
# sys_cmdserver_sock
//SYS_CMDSERVER_SOCK = /tmp/cmdserver.sock 
# sys_cmdserver_maxconn
//SYS_CMDSERVER_MAXCONN = 5
# sys_cmdserver_pid
//SYS_CMDSERVER_PID = /var/run/cmdserver.pid
# sys_cmdserver_log
//SYS_CMDSERVER_LOG = /var/log/cmdserver.log
# sys_cmdserver_authcode
//SYS_CMDSERVER_AUTHCODE = your_auth_code_here
# sys_disable_server_list
//SYS_IGNORE_SERVER_LIST = web
?>
