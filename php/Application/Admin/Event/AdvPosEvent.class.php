<?php
namespace Admin\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author 王贺 
 */
class AdvPosEvent extends BaseEvent {
    
    /**
    *广告位显示
    */
    public function BaseAdv($module="",$extend=array()){
        $map=$extend['map'];
        $map['module'] = $module;
        // $map['status'] = 1;
        $adv  = D("AdvPos");
        $list = $adv->where($map)->select();
        $this->assign("list_data",$list);
        $this->assign("model",$extend);
        $this->display($extend["tem_lists"]);
    }
    
}
