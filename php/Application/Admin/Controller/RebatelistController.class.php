<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class RebatelistController extends ThinkController {
    const model_name = 'RebateList';

    /**
    *返利设置列表
    */
    public function lists(){
        parent::lists(self::model_name,$_GET["p"],$extend);
    }
    
    public function del($model = null, $ids=null) {
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }
}
