<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
use Admin\Logic\SetLogic;

/**
 * 文档基础模型
 */
class LinksModel extends Model{
    /* 自动验证规则 */
    protected $_validate = array(
        array('title',  'require', '友链标题不能为空',         self::MUST_VALIDATE,  'regex',  self::MODEL_BOTH),
        array('title',  '1,20',    '友链标题不能超过20个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
        array('link_url', 'require', '友链URL不能为空',        self::MUST_VALIDATE,  'regex',  self::MODEL_BOTH),
        array('type','require','请选择友链类型'),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('create_time',       'getCreateTime',         self::MODEL_INSERT,  'callback'),
    );
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
     * 创建时间不写则取当前时间
     * @return int 时间戳
     * @author huajie <banhuajie@163.com>
     */
    protected function getCreateTime(){
        $create_time    =   I('post.create_time');
        return $create_time?strtotime($create_time):NOW_TIME;
    }
}