<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------

/**
 * UCenter客户端配置文件
 * 注意：该配置文件请使用常量方式定义
 */

define('UC_APP_ID', 1); //应用ID
define('UC_API_TYPE', 'Model'); //可选值 Model / Service
define('UC_AUTH_KEY', 'oq0d^*AcXB$-2[]PkFaKY}eR(Hv+<?g~CImW>xyV'); //加密KEY
define('UC_DB_DSN', 'mysql://root:root@127.0.0.1:3306/37');  // 数据库连接，使用Model方式调用API必须配置此项
// define('UC_DB_DSN', 'mysql://a21122qa:a21122qaa@127.0.0.1:3306/a21122qa'); // 数据库连接，使用Model方式调用API必须配置此项

define('UC_TABLE_PREFIX', 'sys_'); // 数据表前缀，使用Model方式调用API必须配置此项
