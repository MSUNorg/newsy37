<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class MessageController extends ThinkController {
    
    /**
	*纠错列表
    */
    public function wrong($p=0){
        parent::lists("Message",$p);
    }


    /**
	*留言列表
    */
    public function message($p=0){
        parent::lists("Message",$p);
    }


    public function set_status($model='Message',$ids=null){
        
        if(!empty($ids)){
            $this->set_mess($ids);
        }
        
        parent::set_status($model);
    }

    public function set_mess($ids=null){
        $model = D('Message');
        $map['id']  = array("in",$ids);
        $data['op_id'] = UID;
        $data['op_account'] = session("user_auth.username");
        $model->where($map)->save($data);
    }

}
