<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi@vip.qq.com> <http://www.msun.com>
// +----------------------------------------------------------------------
namespace Admin\Event;
use Think\Controller;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class BaseEvent extends Controller {

    public function custom_list($model = null, $p = 0,$extend_map = array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //获取模型信息
        $model = M('Model')->getByName($model);
        $model || $this->error('模型不存在！');

        //解析列表规则
        $fields = array();
        
        // 关键字搜索
        $map    =   $extend_map;
        $key    =   $model['search_key']?$model['search_key']:'title';
        if(isset($_REQUEST[$key])){
            $map[$key]  =   array('like','%'.$_GET[$key].'%');
            unset($_REQUEST[$key]);
        }
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name] =   $val;
            }
        }
        $row    = empty($model['list_row']) ? 15 : $model['list_row'];

        if($model['need_pk']){
            in_array('id', $fields) || array_push($fields, 'id');
        }
        
        $name = parse_name(get_table_name($model['id']), true);
        $data = D($name)
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(empty($fields) ? true : $fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['need_pk']?'id DESC':'')
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();

            /* 查询记录总数 */
            $count = D($name)->where($map)->count();

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        //$data   =   $this->parseDocumentList($data,$model['id']);
        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }
}
