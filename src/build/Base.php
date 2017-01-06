<?php namespace houdunwang\session\build;

use houdunwang\arr\Arr;
use houdunwang\cookie\Cookie;

trait Base {
	//session_id
	protected $session_id;
	//session_name
	protected $session_name;
	//过期时间
	protected $expire;
	//session 数据
	protected $items = [ ];
	//配置
	protected $config;

	//设置配置项
	public function config( $config, $value = null ) {
		if ( is_array( $config ) ) {
			$this->config = $config;

			return $this;
		} else if ( is_null( $value ) ) {
			return Arr::get( $this->config, $config );
		} else {
			$this->config = Arr::set( $this->config, $config, $value );

			return $this;
		}
	}

	public function bootstrap() {
		$this->session_name = $this->config( 'name' );
		$this->session_id   = $this->getSessionId();
		$this->expire       = $this->config( 'expire' ) ?: 3600;
		$this->connect();
		$this->items = $this->read() ?: [ ];

		return $this;
	}

	/**
	 * 获取客户端IP
	 * @return string
	 */
	final private function getSessionId() {
		$id = Cookie::get( $this->session_name );
		if ( ! $id || substr( $id, 0, 5 ) != 'hdphp' ) {
			$id = 'hdphp' . md5( microtime( true ) ) . mt_rand( 1, 99999 );
		}
		Cookie::set( $this->session_name, $id, $this->expire, '/', $this->config( 'domain' ) );

		return $id;
	}

	/**
	 * 检测数据是否存在
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function has( $name ) {
		return isset( $this->items[ $name ] );
	}

	/**
	 * 设置数据
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return mixed
	 */
	public function set( $name, $value ) {
		$tmp =& $this->items;
		foreach ( explode( '.', $name ) as $d ) {
			if ( ! isset( $tmp[ $d ] ) ) {
				$tmp[ $d ] = [ ];
			}
			$tmp = &$tmp[ $d ];
		}

		return $tmp = $value;
	}

	/**
	 * 获取指定的session数据
	 *
	 * @param string $name
	 *
	 * @return null
	 */
	public function get( $name = '' ) {
		$tmp = $this->items;
		foreach ( explode( '.', $name ) as $d ) {
			if ( isset( $tmp[ $d ] ) ) {
				$tmp = $tmp[ $d ];
			} else {
				return null;
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
	public function del( $name ) {
		if ( isset( $this->items[ $name ] ) ) {
			unset( $this->items[ $name ] );
		}

		return true;
	}

	/**
	 * 获取所有数据
	 * @return mixed
	 */
	public function all() {
		return $this->items;
	}

	/**
	 * 闪存
	 *
	 * @param $name
	 * @param string $value
	 *
	 * @return bool|mixed|void
	 */
	public function flash( $name, $value = '[get]' ) {
		if ( $name == '[del]' ) {
			return $this->del( '_FLASH_' );
		}
		if ( $value == '[get]' ) {
			return $this->get( '_FLASH_.' . $name );
		}

		return $this->set( '_FLASH_.' . $name, $value );
	}

	//析构函数
	public function __destruct() {
		//删除闪存
		$this->flash( '[del]' );
		$this->write();
		if ( mt_rand( 1, 5 ) ) {
			$this->gc();
		}
	}
}