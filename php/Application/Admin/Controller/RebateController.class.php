<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class RebateController extends ThinkController {
    //private $table_name="Game";
    const model_name = 'rebate';

    /**
    *返利设置列表
    */
    public function lists(){
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $extend['game_name'] = $_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
        if(isset($_REQUEST['game_appid'])){
            $extend['game_appid'] = array('like','%'.$_REQUEST['game_appid'].'%');
            unset($_REQUEST['game_appid']);
        }
        parent::lists(self::model_name,$_GET["p"],$extend);
    }

     public function add(){
    	if(IS_POST){
            $rebate=M("rebate","tab_");
            $add=$rebate->create();
            $map['game_id']=$add['game_id'];
            $is_set=$rebate->where($map)->find();
           if($is_set!==null){
                 $this->error("此游戏已经设置过返利",U('lists'));
            }else{
                $add['game_name']=get_game_name($add['game_id']);
            if(!is_numeric($add['ratio'])||$add['ratio']<0||$add['ratio']>100){
                $this->error("金额输入错误",U('lists'));
            }else{
                $rebate->add($add);                            
                $this->success("添加成功",U("lists"));
               } 

              }
          }else{
            $this->meta_title = '新增游戏返利';
         $this->display();
        }
    }

        public function edit() {
            $rebate=M("rebate","tab_");  
            $id=$_REQUEST['id'];
            if(IS_POST){
                if($rebate->create()&&$rebate->save()){
                 $this->success("编辑成功",U("lists"));
                }else{
                $this->error("编辑失败",U("lists"));
                }
            }else{
            $map['id']=$id;
            $lists=$rebate->where($map)->find();
            $this->assign("data",$lists);
            $this->meta_title = '编辑游戏返利';
            $this->display();
            }
        }
        
    public function del($model = null, $ids=null) {
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }

}
