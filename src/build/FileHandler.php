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

/**
 * 文件处理
 * Class FileHandler
 * @package hdphp\session
 * @author 向军
 */
class FileHandler implements AbSession {
	use Base;
	protected $dir;
	protected $file;

	//连接
	public function connect() {
		$dir = $this->config( 'file.path' );
		//创建目录
		if ( ! is_dir( $dir ) ) {
			mkdir( $dir, 0755, true );
			file_put_contents( $dir . '/index.html', '' );
		}
		$this->dir = realpath( $dir );

		$this->file = $this->dir . '/' . $this->session_id . '.php';
	}

	//读取数据
	public function read() {
		if ( ! is_file( $this->file ) ) {
			return [ ];
		}

		return include $this->file;
	}

	//保存数据
	public function write() {
		$data = "<?php \nreturn " . var_export( $this->items, true ) . ";\n?>";
		file_put_contents( $this->file, $data );
	}

	//删除所有数据
	public function flush() {
		return is_file( $this->file ) or unlink( $this->file );
	}

	//垃圾回收
	public function gc() {
		foreach ( glob( $this->dir . '/*.php' ) as $f ) {
			if ( basename( $f ) != basename( $this->file ) && ( filemtime( $this->file ) + $this->expire ) < time() ) {
				unlink( $this->file );
			}
		}
	}
}