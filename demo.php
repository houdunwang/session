<?php
require 'vendor/autoload.php';
$config = [
	//session_name
	'name'     => 'hdcmsid',
	//有效域名
	'domain'   => '',
	//过期时间 0 会话时间 3600 为一小时
	'expire'   => 0,
	#File
	'file'     => [
		'path' => 'storage/session',
	],
	#Mysql
	'mysql'    => [
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
		//缓存表
		'table'    => 'session'
	],
	#Memcache
	'memcache' => [
		'host' => 'localhost',
		'port' => 11211,
	],
	#Redis
	'redis'    => [
		'host'     => 'localhost',
		'port'     => 11211,
		'password' => '',
		'database' => 0,
	]
];