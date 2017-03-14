<?php
// +----------------------------------------------------------------------
// | 徐州梦创信息科技有限公司—专业的游戏运营，推广解决方案.
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.vlcms.com  All rights reserved.
// +----------------------------------------------------------------------
// | Author: kefu@vlcms.com QQ：97471547
// +----------------------------------------------------------------------
namespace Admin\Event;
use Think\Controller;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class SourceEvent extends Controller {

    public function add_source(){
        $model = D('Game_source');
        $data = $model->create();
        $data['file_size'] = round($data['file_size']/pow(1024,2),2)."MB";
        $data['file_url']  = $data['file_url']."/".$data['file_name'];
        $data['op_id'] = UID;
        $data['op_account'] = session("user_auth.username");
        $data['create_time'] = NOW_TIME;
        $res = $model->add($data);
        if($res){
            $this->update_game_size($data);
            $this->soure_pack($data['game_id'],$data['file_url']);
            $this->success('添加成功',U('GameSource/lists'));
        }
        else{
            $this->error('添加失败');
        }
    }

    /**
    *修改游戏原包
    */
    public function update_source($id = null,$file_name){
        $id || $this->error('id不能为空');
        $model = D('Game_source');
        $data = $model->create();
        $url=$data['file_url'];
        $data['file_size'] = round($data['file_size']/pow(1024,2),2)."MB";
        $data['file_url']  = $data['file_url']."/".$data['file_name'];
        $data['id'] = $id;
        $data['op_id'] = UID;
        $data['op_account'] = session("user_auth.username");
        $data['create_time'] = NOW_TIME;
        if($data['file_name']==$file_name){
            $this->error('修改失败',U('GameSource/lists'));
        }else{
        $res = $model->save($data);
        if($res){
            @unlink($url."/".$file_name);
            $this->update_game_size($data);
            $this->soure_pack($data['game_id'],$data['file_url']);
            $this->success('修改成功',U('GameSource/lists'));
        }
        else{
            $this->error('修改失败',U('GameSource/lists'));
        }

        }
    }

    protected function update_game_size($param=null){
        $model = D('Game');
        $map['id'] = $param['game_id'];
        $data['game_size'] = $param['file_size'];
        $data['version'] = $param['version'];
        if($param['file_type']==1){
            $data['and_dow_address'] = $param['file_url'];
            $data['game_address'] = $param['file_url'];
        }
        else{
            $data['ios_dow_address'] = $param['file_url'];
        }
        $model->where($map)->save($data);
    }

    /**
    *原包打包
    */
    protected function soure_pack($game_id=0,$file_url=""){
        //$file_url = "./Uploads/SourcePack/20160715125638_847.apk";
        $game_info = M("game","tab_")->find($game_id);
        $zip = new \ZipArchive;
        $res = $zip->open($file_url, \ZipArchive::CREATE);
        $data = array(
            "game_id"     => $game_info['id'],
            "game_name"   => $game_info['game_name'],
            "game_appid"  => $game_info['game_appid'],
            "promote_id"  => 0,
            "promote_account"=> "自然注册",
        );
        $zip->addFromString('META-INF/mch.properties', json_encode($data));
        $zip->close();
    }
   
}
