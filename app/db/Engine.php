<?php
/**
 * @author QiangYu
 *
 * 数据库连接引擎
 *
 * Customized by Syncxplus from bzfshop:src/protected/Core/Modal/DbEngine.php
 *
 * */

namespace db;

class Engine extends \DB\SQL
{
    function __construct($dsn, $user = null, $pw = null, array $options = null)
    {
        parent::__construct($dsn, $user, $pw, $options);
    }

    function exec($cmds, $args = null, $ttl = 0, $log = true, $stamp = false)
    {
        return parent::exec($cmds, $args, $ttl, $log, $stamp);
    }

}