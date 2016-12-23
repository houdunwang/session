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

/**URL处理类
 * Class Session
 * @package hdphp\session
 * @author 向军 <2300071698@qq.com>
 */
class Session {
	//操作驱动
	protected $link;

	//设置驱动
	protected static function driver( $driver = null ) {
		$driver = $driver ?: Config::get( 'session.driver' );
		$driver = '\houdunwang\session\\build\\' . ucfirst( $driver ) . 'Handler';
		static $links = [ ];
		if ( ! isset( $links[ $driver ] ) ) {
			$obj       = new Session();
			$obj->link = new $driver;
			$obj->link->bootstrap();

			$links[ $driver ] = $obj;
		}

		return $links[ $driver ];
	}

	public function __call( $method, $params ) {
		if ( is_null( $this->link ) ) {
			$this->driver();
		}
		if ( method_exists( $this->link, $method ) ) {
			return call_user_func_array( [ $this->link, $method ], $params );
		}
	}

	public static function __callStatic( $name, $arguments ) {
		return call_user_func_array( [ static::driver(), $name ], $arguments );
	}
}