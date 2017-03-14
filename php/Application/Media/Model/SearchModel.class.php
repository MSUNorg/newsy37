<?php
namespace Media\Model;
use Think\Model;
use Front\Logic\InfoLogic;

/**
 * 文档基础模型
 */
class SearchModel extends Model{

    
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

    public function search(){
        echo 1;
    }
}