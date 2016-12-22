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

####生成实例
```
$obj = new \houdunwang\session\Session();
```

####设置引擎
```
$obj->driver( 'file' );
```

####启动组件
```
$obj->bootstrap();
```

##指令

####设置
```
$obj->set('name','houdunwang.com');
```

####获取
```
$obj->get('name');
```

####判断
```
$obj->has('name');
```

####删除
```
$obj->del('name');
```

####清空
删除所有数据
```
$obj->flush();
```

