<?php

namespace code;

use utils\StringUtils;

class ProductMeta
{
    static $color = ['black', 'white', 'gray', 'brown', 'blue', 'green', 'yellow', 'red', 'purple', 'multi', 'metal', 'leopard', 'transparent'];
    static $heelHeight = ['0--1', '1--2', '2--3', '3--4.5', '>4.5'];
    static $heelType = ['stiletto', 'chunky', 'wedge', 'kitten', 'flat'];
    static $occasion = ['holiday', 'daily', 'fashion', 'party'];
    static $structure = ['open toe', 'sling back', 'close', 'd\'orsay', 'sandals', 'anklehigh', 'kneehigh', 'overkneehigh', 'midcalf'];
    static $toe = ['square toe', 'round toe', 'pointed toe'];

    static function validate($name, $value)
    {
        $name = StringUtils::snakeToCamel($name);
        if (isset($$name)) {
            return in_array($value, self::$$name);
        } else {
            return false;
        }
    }
}