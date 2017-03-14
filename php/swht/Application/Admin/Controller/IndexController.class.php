<?php
// +----------------------------------------------------------------------
// | 手游平台
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.msun.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController {

    /**
     * 后台首页
     * @author zxc <zuojiazi@vip.qq.com>
     */
    public function index(){
        $this->meta_title = '管理首页';
        $this->display();
    }

}
