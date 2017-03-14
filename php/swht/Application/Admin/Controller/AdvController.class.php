<?php
// +----------------------------------------------------------------------
// | 手游平台
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.msun.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc
// +----------------------------------------------------------------------

namespace Admin\Controller;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class AdvController extends ThinkController {
    
    /**
    *媒体页面广告位
    */
    public function media_adv_pos_lists(){
        $model = M('Model')->getByName("AdvPos");
        $id = $model['id'];
        $extend = array(
            'id'=>$id,
            'title'=>'媒体广告位管理',
            'tem_lists' => "media_adv_pos_lists",
        );
        $BaseAdv = A("AdvPos","Event");
        $BaseAdv->BaseAdv("media",$extend);
    }

    /**
    *APP广告位
    */
    public function app_adv_pos_lists(){
        $model = M('Model')->getByName("AdvPos");
        $id = $model['id'];
        $extend = array(
            'id'=>$id,
            'title'=>'媒体广告位管理',
            'tem_lists' => "app_adv_pos_lists",
        );
        $BaseAdv = A("AdvPos","Event");
        $BaseAdv->BaseAdv("app",$extend);
    }

    /**
    *编辑广告位
    */
    protected function baes_edit($model="",$id=0,$page_url=""){
        //获取模型信息
        $model = D('Model')->find($model);
        $model || $this->error('模型不存在！');

        if(IS_POST){
            $Model  =   D(parse_name(get_table_name($model['id']),1));
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            if($Model->create() && $Model->save()){
                $this->success('保存'.$model['title'].'成功！', U($page_url));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields     = get_model_attribute($model['id']);
            //获取数据
            $data       = D(get_table_name($model['id']))->find($id);
            $data || $this->error('数据不存在！');
            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑'.$model['title'];
            $this->display($model['template_edit']?$model['template_edit']:'');
        }
    }

    /**
    *删除广告
    */
    public function base_del($model = null, $ids=null){
        $model = M('Model')->find($model);
        $model || $this->error('模型不存在！');

        $ids = array_unique((array)I('ids',0));

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        $Model = D(get_table_name($model['id']));
        $map = array('id' => array('in', $ids) );
        if($Model->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
    *添加广告
    */
    protected function base_add_adv($model = null,$page_url=""){
        //获取模型信息
        $model = M('Model')->where(array('status' => 1))->find($model);
        $model || $this->error('模型不存在！');
        if(IS_POST){
            $Model  =   D(parse_name(get_table_name($model['id']),1));
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            if($Model->create() && $Model->add()){
                $this->success('添加'.$model['title'].'成功！', U("$page_url",array('model'=>$model['name'])));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields = get_model_attribute($model['id']);
            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->meta_title = '新增'.$model['title'];
            $this->display($model['template_add']?$model['template_add']:'');
        }
    }

    /**
    *编辑媒体广告位
    */
    public function edit_media_adv_pos($model='',$id=0){
        $this->baes_edit($model,$id,"media_adv_pos");
    }

    /**
    *编辑APP广告位
    */
    public function edit_app_adv_pos($model='',$id=0){
        $this->baes_edit($model,$id,"app_adv_pos");
    }

    /**
    *广告列表
    */
    public function adv_lists(){
        parent::lists("adv",$_GET["p"]);
    }

    /**
    *删除媒体广告
    */
    public function del_adv($model="",$ids=0){
        $model = M('Model')->getByName("adv");
        $this->base_del($model['id'],$ids);
    }

    /**
    *编辑广告
    */
    public function edit_adv($model="",$id=0){
        $this->baes_edit($model,$ids,"adv_lists");
    }

    /**
    *添加媒体广告
    */
    public function add_media_adv($model=""){
        $this->base_add_adv(15,"adv_lists");
    }

    /**
    *添加媒体广告
    */
    public function add_app_adv(){
        $this->base_add_adv(15,"adv_lists");
    }
}
