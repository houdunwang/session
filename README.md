#SESSION组件

##介绍

组件提供高效的SESSION管理手段, 提供多种处理引擎包括File、Mysql、Memcache、Redis等,支持统一调用接口使用方便。

[TOC]

#开始使用

####安装组件
使用 composer 命令进行安装或下载源代码使用。

```
composer require houdunwang/session
```
> HDPHP 框架已经内置此组件，无需要安装

####配置
```
$config = [
	//session_name
	'name'      => 'hdcmsid',
	//cookie加密密钥
	'secureKey' => 'houdunwang88',
	//有效域名
	'domain'    => '',
	//过期时间 0 会话时间 3600 为一小时
	'expire'    => 0,
	#File引擎配置
	'file'      => [
		'path' => 'storage/session',
	],
	#Mysql引擎配置
	'mysql'     => [
		//缓存表
		'table'    => 'session'
	],
	#Memcache引擎配置
	'memcache'  => [
		'host' => 'localhost',
		'port' => 11211,
	],
	#Redis引擎配置
	'redis'     => [
		'host'     => 'localhost',
		'port'     => 11211,
		'password' => '',
		'database' => 0,
	]
];
\houdunwang\config\Config::set( 'session', $config );
```

####设置
```
\houdunwang\session\Session::set('name','houdunwang.com');
```

####闪存
通过 flash 指令设置的数据会在下次请求结束时自动删除, 这类动作我们称为闪存数据。

```
\houdunwang\session\Session::flash('name','houdunren.com');
```

####获取
```
\houdunwang\session\Session::get('name');
```

####获取所有
```
\houdunwang\session\Session::all();
```

####判断
```
\houdunwang\session\Session::has('name');
```

####删除
```
\houdunwang\session\Session::del('name');
```

####清空
删除所有数据
```
\houdunwang\session\Session::flush();
```

###数据库引擎
组件提供了Mysql处理引擎。

####创建数据表
执行以下SQL语句创建数据表
```
CREATE TABLE `session` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` char(100) DEFAULT NULL,
  `data` text,
  `atime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
```

####设置数据库连接配置
如果SESSION处理使用数据库引擎, 首先要对数据库连接参数进行配置。可以参考 [Db数据库组件](https://github.com/houdunwang/db) 文档。

```
\houdunwang\session\Session::set( 'database', [
    //读库列表
	'read'     => [ ],
	//写库列表
	'write'    => [ ],
	//表字段缓存目录
	'cacheDir' => 'storage/field',
	//开启读写分离
	'proxy'    => false,
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
] );
```
