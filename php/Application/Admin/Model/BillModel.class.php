<?php
namespace Admin\Model;
use Think\Model;

/**
 * 
 */
class BillModel extends Model{

    

    /* 自动验证规则 */
    protected $_validate = array(
    
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('create_time',       'getCreateTime',         self::MODEL_INSERT,  'callback'),
        array('bill_number',       'getBillNumber',         self::MODEL_INSERT,  'callback'),
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
    
    public function getBillNumber() {
        $bill_number = I('bill_number');
        return $bill_number?$bill_number:'dz_'.date('YmdHis',time()).rand(100,999);
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