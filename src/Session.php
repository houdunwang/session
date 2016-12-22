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
	protected static $driver;

	//开启
	public function __construct() {
	}

	//魔术方法
	public function __call( $method, $params ) {
		if ( is_null( self::$driver ) ) {
			$driver       = '\houdunwang\session\\build\\' . ucfirst( Config::get( 'session.driver' ) ) . 'Handler';
			self::$driver = new $driver();
			self::$driver->bootstrap();
		}
		if ( ! method_exists( $this->driver, $method ) ) {
			throw new \Exception( '请求方法不存在' );
		}

		return call_user_func_array( [ $this->driver, $method ], $params );
	}
}