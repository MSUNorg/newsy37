<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------
namespace Home\Event;
use Think\Controller;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class BaseEvent extends Controller {

    public function baseinfo() {

    }

    public function lists($model,$p){
    	$model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =   $model['map'];
        $key    =   $model['key'];
        //账号查询
        foreach ($key as $k=> $value) {
            if(isset($_REQUEST[$value])&&$_REQUEST[$value]!=''){
                $map[$value]  =   array('like','%'.$_REQUEST[$value].'%');
                unset($_REQUEST[$value]);
            }
        }
        //高级查询
        if(!empty($_REQUEST['time-start']) && !empty($_REQUEST['time-end'])){
                $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $data = M($name,'tab_')
             /* 查询指定字段，不指定则查询所有字段 */
            ->field(empty($fields) ? true : $fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($order)
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();
            // var_dump($map);
            // exit;

        /* 查询记录总数 */
        $count = M($name,'tab_')->where($map)->count();
         /*统计充值 */
        if(in_array('real_amount',$fields)){
        $total_amount=M($name,'tab_')->where($map)->sum('real_amount');
        }
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->assign('count',$count);
        $this->assign('total_amount',$total_amount);
        $this->display($model['template_list']);
    }
   
}
