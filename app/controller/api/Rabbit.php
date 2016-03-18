<?php
/**
 * Created by PhpStorm.
 * User: jibo
 * Date: 16/3/12
 * Time: 10:32
 */

namespace controller\api;

use utils\RabbitHandler;

class Rabbit extends Base {

    function get() {
        global $f3;
        $rabbit = new RabbitHandler(
            $f3->get('RABBIT_HOST'),
            $f3->get('RABBIT_PORT'),
            $f3->get('RABBIT_USER'),
            $f3->get('RABBIT_PWD'),
            $f3->get('RABBIT_EXCHANGE_OMS'),
            $f3->get('RABBIT_ROUTE_KEY_OMS_MAIL')
        );
        $rabbit->send('This is a test message');
        trace('Send out a test message');
    }
}