<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16-3-18
 * Time: 13:40
 */

namespace controller\stats;

class Login extends Base {
    function get() {
        global $smarty;
        $smarty->assign('title', '系统登录');
        $smarty->display('stats/login.tpl');
    }

    function post() {
        global $f3;
        $check = false;
        $username = $_POST['username'];
        $password = $_POST['password'];
        $authorizedKeys = $f3->get('AUTHORIZED_KEYS');
        if ((is_array($authorizedKeys) && in_array(md5($username.$password), $authorizedKeys)) || (is_string($authorizedKeys) && md5($username.$password) == $authorizedKeys)) {
            setcookie('USERNAME', $username);
            $check = true;
        }
        echo $check ? 'success' : 'failed';
    }

    function login() {
        // do nothing, replace the parent::login to avoid loop forever
    }
} 
