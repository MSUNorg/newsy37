<?php
// +----------------------------------------------------------------------
// | 徐州梦创信息科技有限公司—专业的游戏运营，推广解决方案.
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.vlcms.com  All rights reserved.
// +----------------------------------------------------------------------
// | Author: kefu@vlcms.com QQ：97471547
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 文档基础模型
 */
class PromoteModel extends Model{

    /**
     * 构造函数
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        /* 设置默认的表前缀 */
        $this->tablePrefix ='tab_';
        /* 执行构造方法 */
        parent::__construct($name, $tablePrefix, $connection);
    }
        /**
    *  检测
    */
    public function isLogin() {
        $user = session('promote_auth');
        if (!empty($user)) {
            return $user;
        }
        return null;
    }
        /**
    * 更新时间
    */
    protected function getTime() {
        return date("Y-m-d H:i:s",time());
    }
    

}