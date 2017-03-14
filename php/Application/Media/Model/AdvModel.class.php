<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Media\Model;
use Think\Model;

/**
 * 文档基础模型
 */
class AdvModel extends Model{

    

    /* 自动验证规则 */
    protected $_validate = array(
        
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('start_time',  'strtotime', self::MODEL_INSERT, 'function'),
        array('end_time',  'strtotime', self::MODEL_INSERT, 'function'),
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
    *获取广告信息
    */
     public function adv_lists($name,$sort,$limit){
		
        $map['tab_adv.status'] = 1;
        $map['tab_adv_pos.name'] = $name;
        $data = $this->field("tab_adv.*,tab_adv_pos.name")

                     ->join("tab_adv_pos on tab_adv.pos_id = tab_adv_pos.id")

                     ->where($map)

                     ->limit($limit)

                     ->select();

        return $data;

    }  

    public function adv_lists2($name,$sort,$limit){
        $map['tab_adv.status'] = 1;
        $map['tab_adv_pos.name'] = $name;/*array('in',array('7','8'))*/
        $data = $this->field("tab_adv.*,tab_adv_pos.name")
                     ->join("tab_adv_pos on tab_adv.pos_id = tab_adv_pos.id")
                     ->where($map)
                     ->limit($limit)
                     ->order('tab_adv.sort desc')
                     ->select();
        return $data;

/*$array[] = array('id'=>1,'price'=>50);
$array[] = array('id'=>2,'price'=>70);
$array[] = array('id'=>3,'price'=>30);
$array[] = array('id'=>4,'price'=>20);
 
foreach ($data as $key=>$value){
    $id[$key] = $value['id'];
    $price[$key] = $value['price'];
}
 
array_multisort($price,SORT_NUMERIC,SORT_DESC,$id,SORT_STRING,SORT_ASC,$array);  */        
    }
}