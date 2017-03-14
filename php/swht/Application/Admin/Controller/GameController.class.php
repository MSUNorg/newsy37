<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台首页控制器
 * @author zxc
 */
class GameController extends ThinkController {
    //private $table_name="Game";
    const model_name = 'game';

    /**
    *游戏信息列表
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

    
}
