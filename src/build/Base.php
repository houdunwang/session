<?php namespace houdunwang\session\build;

use houdunwang\cookie\Cookie;

trait Base {
	//session_id
	protected $session_id;
	//session_name
	protected $session_name;
	//过期时间
	protected $expire;
	//session 数据
	protected static $items = [ ];
	//外观
	protected $facade;

	public function __construct( $facade ) {
		$this->facade = $facade;
	}

	public function bootstrap() {
		$this->session_name = $this->facade->config( 'name' );
		$this->session_id   = $this->getSessionId();
		$this->expire       = $this->facade->config( 'expire' ) ?: 3600;
		$this->connect();
		self::$items = $this->read() ?: [ ];

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
		Cookie::set( $this->session_name, $id, $this->expire, '/', $this->facade->config( 'domain' ) );

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
		return isset( self::$items[ $name ] );
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
		$tmp =& self::$items;
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
		$tmp = self::$items;
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
		if ( isset( self::$items[ $name ] ) ) {
			unset( self::$items[ $name ] );
		}

		return true;
	}

	/**
	 * 获取所有数据
	 * @return mixed
	 */
	public function all() {
		return self::$items;
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