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

/**
 * 文档基础模型
 */
class RebateListModel extends Model{

    

    /* 自动验证规则 */
    protected $_validate = array(
        // array('game_id', 'require', '游戏不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //  array('ratio',         '/^[0-100](\.\d+)?$/',           '返利比例输入错误',             self::VALUE_VALIDATE,  'regex',  self::MODEL_BOTH),

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

}