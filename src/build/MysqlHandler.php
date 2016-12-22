<?php
/** .-------------------------------------------------------------------
 * |  Software: [HDCMS framework]
 * |      Site: www.hdcms.com
 * |-------------------------------------------------------------------
 * |    Author: 向军 <2300071698@qq.com>
 * |    WeChat: aihoudun
 * | Copyright (c) 2012-2019, www.houdunwang.com. All Rights Reserved.
 * '-------------------------------------------------------------------*/
namespace houdunwang\session\build;

use houdunwang\config\Config;
use houdunwang\db\Db;

class MysqlHandler implements AbSession {
	use Base;
	//数据库连接
	private $link;
	//数据表
	private $table;

	//初始
	public function connect() {
		if ( ! Config::get( 'database' ) ) {
			$config = array_merge( [
				//读库列表
				'read'     => [ ],
				//写库列表
				'write'    => [ ],
				//表字段缓存目录
				'cacheDir' => 'storage/field',
				//开启读写分离
				'proxy'    => false,
				//开启调度模式
				'debug'    => true,
				//主机
				'host'     => 'localhost',
				//类型
				'driver'   => 'mysql',
				//帐号
				'user'     => 'root',
				//密码
				'password' => 'admin888',
				//数据库
				'database' => 'demo',
				//表前缀
				'prefix'   => ''
			], Config::get( 'session.mysql' ) );
			Config::set( 'database', $config );
		}
		$this->link  = ( new Db() )->table( Config::get( 'session.mysql.table' ) );
		$this->table = $this->link->getTable();
	}

	//读取
	public function read() {
		$data = $this->link->where( 'session_id', $this->session_id )->where( 'atime', '>', time() - $this->expire )->pluck( 'data' );

		return $data ? unserialize( $data ) : [ ];
	}

	//写入
	public function write() {
		$data = serialize( $this->items );
		$sql  = "REPLACE INTO " . $this->table . "(session_id,data,atime) ";
		$sql .= "VALUES('{$this->session_id}','$data'," . time() . ')';

		return $this->link->execute( $sql );
	}

	//卸载
	public function flush() {
		$sql = "DELETE FROM " . $this->table . " WHERE session_id='{$this->session_id}'";

		return $this->link->execute( $sql );
	}

	/**
	 * SESSION垃圾处理
	 * @return boolean
	 */
	public function gc() {
		$sql = "DELETE FROM " . $this->table . " WHERE atime<" . ( time() - $this->expire ) . " AND session_id<>'" . session_id() . "'";

		return $this->link->execute( $sql );
	}
}
