<?php
/** .-------------------------------------------------------------------
 * |  Software: [HDCMS framework]
 * |      Site: www.hdcms.com
 * |-------------------------------------------------------------------
 * |    Author: 向军 <2300071698@qq.com>
 * |    WeChat: aihoudun
 * | Copyright (c) 2012-2019, www.houdunwang.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/

namespace houdunwang\session;

use houdunwang\config\Config;

/**
 * SESSION处理类
 * Class Session
 *
 * @package houdunwang\session
 */
class Session
{
    //操作驱动
    protected static $link;

    //设置驱动
    public function driver($driver = '')
    {
        $driver     = $driver ?: Config::get('session.driver');
        $driver     = $driver ?: 'file';
        $driver     = '\houdunwang\session\\build\\'.ucfirst($driver).'Handler';
        self::$link = new $driver();
        self::$link->bootstrap();

        return $this;
    }

    public function __call($method, $params)
    {
        if (is_null(self::$link)) {
            $this->driver();
        }

        return call_user_func_array([self::$link, $method], $params);
    }

    public static function single()
    {
        static $link = null;
        if (is_null($link)) {
            $link = new static();
        }

        return $link;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::single(), $name], $arguments);
    }
}