<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16-3-18
 * Time: 13:40
 */

namespace controller\profit;

class Base extends \controller\Base {
    protected $user;

    function beforeRoute() {
        global $f3, $smarty;
        $smarty->assign('context', $f3->get('BASE'));
        $this->user = $this->login();
        $smarty->assign('user', $this->user);
    }

    function get() {
        global $f3;
        $f3->reroute($this->url().'Index');
    }
    
    function login() {
        global $f3;
        if(isset($_COOKIE['USERNAME']) && $_COOKIE['USERNAME']) return $_COOKIE['USERNAME'];
        $f3->reroute($this->url().'Login');
    }

    function url() {
        global $f3;
        return $f3->get('SCHEME').'://'.$f3->get('HOST').':'.$f3->get('PORT').$f3->get('BASE').'/profit/';
    }
} 