<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台首页控制器
 * @author zxc
 */
class SiteController extends ThinkController {
    
     // 获取某个标签的配置参数
    public function group($cate_id=0,$group_id=0) {
        $type   =   C('CONFIG_GROUP_LIST');
        $map['status'] = 1;
        $map['category'] = $cate_id;
        $map['group'] = $group_id;
        $list   =   M("Config")->where($map)->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        $this->assign('id',$group_id);
        $this->meta_title = $type[$id].'设置';
        $this->display();
    }


    /**
     * 批量保存配置
     * @author zxc <zuojiazi@vip.qq.com>
     */
    public function save($config){
        if($config && is_array($config)){
            $Config = M('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA',null);
        $this->success('保存成功！');
    }

    public function media($cate_id=0,$group_id=0){
        $cate_id  = I('cate_id',1);
        $group_id = I('group_id',1);
        $this->group($cate_id,$group_id);
    }

    public function channel(){
        $cate_id = I('cate_id',2);
        $group_id = I('group_id',1);
        $this->group($cate_id,$group_id);
    }

    public function app(){
        $cate_id = I('cate_id',3);;
        $group_id = I('group_id',1);
        $this->group($cate_id,$group_id);
    }
    //友情链接
    /**
     * 编辑配置
     * @author zxc <zuojiazi@vip.qq.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $Config = D('Config');
            $data = $Config->create();
            if($data){
                if($Config->save()){
                    S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_config','config',$data['id'],UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Config')->field(true)->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }
}
