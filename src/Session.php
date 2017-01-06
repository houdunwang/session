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

use houdunwang\arr\Arr;
use houdunwang\config\Config;

/**URL处理类
 * Class Session
 * @package hdphp\session
 * @author 向军 <2300071698@qq.com>
 */
class Session {
	//操作驱动
	protected $link;
	protected $config;

	public function __construct() {
		$this->config( Config::get( 'session' ) );
	}

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

	//设置驱动
	protected function driver( $driver = null ) {
		$driver     = $driver ?: Config::get( 'session.driver' );
		$driver     = '\houdunwang\session\\build\\' . ucfirst( $driver ) . 'Handler';
		$this->link = new $driver();
		$this->link->config( Config::get( 'session' ) );
		$this->link->connect();

		return $this;
	}

	public function __call( $method, $params ) {
		if ( is_null( $this->link ) ) {
			$this->driver();
		}

		return call_user_func_array( [ $this->link, $method ], $params );
	}

	public static function single() {
		static $link;
		if ( is_null( $link ) ) {
			$link = new static();
		}

		return $link;
	}

	public static function __callStatic( $name, $arguments ) {
		return call_user_func_array( [ static::single(), $name ], $arguments );
	}
}