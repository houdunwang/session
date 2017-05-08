<?php namespace houdunwang\session\build;

use houdunwang\config\Config;
use houdunwang\cookie\Cookie;

trait Base
{
    //session_id
    protected $session_id;
    //session_name
    protected $session_name;
    //过期时间
    protected $expire;
    //session 数据
    protected $items = [];
    //开始时间
    protected $startTime;

    public function bootstrap()
    {
        $this->session_name = Config::get('session.name');
        $this->expire       = intval(Config::get('session.expire'));
        $this->session_id   = $this->getSessionId();
        $this->connect();
        $this->items     = $this->read() ?: [];
        $this->startTime = time();

        return $this;
    }

    /**
     * 设置SESSION_ID
     *
     * @return string
     */
    final protected function getSessionId()
    {
        $id = Cookie::get($this->session_name);
        if ( ! $id) {
            $id = 'hdphp'.md5(microtime(true).mt_rand(1, 6));
        }
        Cookie::set(
            $this->session_name,
            $id,
            $this->expire,
            '/',
            Config::get('session.domain')
        );

        return $id;
    }

    /**
     * 检测数据是否存在
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->items[$name]);
    }

    /**
     * 批量设置
     *
     * @param $data
     */
    public function batch($data)
    {
        foreach ($data as $k => $v) {
            $this->set($k, $v);
        }
    }

    /**
     * 设置数据
     *
     * @param string $name  名称
     * @param mixed  $value 值
     *
     * @return mixed
     */
    public function set($name, $value)
    {
        $tmp =& $this->items;
        foreach (explode('.', $name) as $d) {
            if ( ! isset($tmp[$d])) {
                $tmp[$d] = [];
            }
            $tmp = &$tmp[$d];
        }

        $tmp = $value;

        return true;
    }

    /**
     * 获取指定的session数据
     *
     * @param string $name
     * @param string $value
     *
     * @return null
     */
    public function get($name = '', $value = null)
    {
        $tmp = $this->items;
        foreach (explode('.', $name) as $d) {
            if (isset($tmp[$d])) {
                $tmp = $tmp[$d];
            } else {
                return $value;
            }
        }

        return $tmp;
    }

    /**
     * 按名子删除
     *
     * @param $name
     *
     * @return bool
     */
    public function del($name)
    {
        if (isset($this->items[$name])) {
            unset($this->items[$name]);
        }

        return true;
    }

    /**
     * 获取所有数据
     *
     * @return mixed
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * 清除所有数据
     *
     * @return bool
     */
    public function flush()
    {
        $this->items = [];

        return true;
    }

    /**
     * 闪存
     *
     * @param        $name
     * @param string $value
     *
     * @return bool|mixed|void
     */
    public function flash($name = null, $value = '[get]')
    {
        if (is_null($name)) {
            return $this->get('_FLASH_') ?: [];
        }
        //删除所有闪存
        if ($name == '[del]') {
            return $this->del('_FLASH_');
        }
        if ($value == '[get]') {
            if ($data = $this->get('_FLASH_.'.$name)) {
                return $data[0];
            }

            return;
        }
        if ($value == ['del']) {
            return $this->del('_FLASH_.'.$name);
        }

        return $this->set('_FLASH_.'.$name, [$value, $this->startTime]);
    }

    //析构函数
    public function __destruct()
    {
        //删除无效闪存
        foreach ($this->flash() as $k => $v) {
            if ($v[1] != $this->startTime) {
                $this->flash($k, '[del]');
            }
        }
        //储存数据
        $this->write();
        if (mt_rand(1, 5) == 5) {
            $this->gc();
        }
    }
}