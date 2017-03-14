<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class ChannelDataController extends ThinkController {
    
    /**
    *渠道注册用户
    */
    public function channel_regist_user($p=1){
        parnet::lists("User",$p);
    }

    /**
    *渠道充值
    */
    public function channel_recharge($p=1){
        parnet::lists("User",$p);
    }
}
