<?php namespace houdunwang\session\build;

use houdunwang\config\Config;
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

	public function bootstrap() {
		$this->session_name = Config::get( 'session.name' );
		$this->session_id   = $this->getSessionId();
		$this->expire       = Config::get( 'session.expire' ) ?: 3600;
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
		Cookie::set( $this->session_name, $id, $this->expire, '/', Config::get( 'session.domain' ) );

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
	 * @param string $value
	 *
	 * @return null
	 */
	public function get( $name = '', $value = null ) {
		$tmp = $this->items;
		foreach ( explode( '.', $name ) as $d ) {
			if ( isset( $tmp[ $d ] ) ) {
				$tmp = $tmp[ $d ];
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
	 * 清除所有数据
	 * @return bool
	 */
	public function flush() {
		$this->items = [ ];

		return true;
	}

	/**
	 * 闪存
	 *
	 * @param $name
	 * @param string $value
	 *
	 * @return bool|mixed|void
	 */
	public function flash( $name = null, $value = '[get]' ) {
		if ( is_null( $name ) ) {
			return $this->get( '_FLASH_' ) ?: [ ];
		}
		//删除所有闪存
		if ( $name == '[del]' ) {
			return $this->del( '_FLASH_' );
		}
		if ( $value == '[get]' ) {
			if ( $data = $this->get( '_FLASH_.' . $name ) ) {
				return $data[0];
			}

			return;
		}
		if ( $value == [ 'del' ] ) {
			return $this->del( '_FLASH_.' . $name );
		}

		return $this->set( '_FLASH_.' . $name, [ $value, __URL__ ] );
	}

	//析构函数
	public function __destruct() {
		//删除无效闪存
		$flash = (array) $this->flash();
		foreach ( $flash as $k => $v ) {
			if ( $v[1] != __URL__ ) {
				$this->flash( $k, '[del]' );
			}
		}

		//储存数据
		$this->write();
		if ( mt_rand( 1, 5 ) == 5 ) {
			$this->gc();
		}
	}
}