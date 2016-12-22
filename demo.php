<?php
require 'vendor/autoload.php';
$config = [
	//session_name
	'name'      => 'hdcmsid',
	//cookie加密密钥
	'secureKey' => 'houdunwang88',
	//有效域名
	'domain'    => '',
	//过期时间 0 会话时间 3600 为一小时
	'expire'    => 0,
	#File
	'file'      => [
		'path' => 'storage/session',
	],
	#Mysql
	'mysql'     => [
		//缓存表
		'table'    => 'session'
	],
	#Memcache
	'memcache'  => [
		'host' => 'localhost',
		'port' => 11211,
	],
	#Redis
	'redis'     => [
		'host'     => 'localhost',
		'port'     => 11211,
		'password' => '',
		'database' => 0,
	]
];
\houdunwang\config\Config::set( 'session', $config );
$obj = new \houdunwang\session\Session();
$obj->driver( 'mysql' );
$obj->bootstrap();
//$obj->set('a',33);
echo $obj->get('a');
