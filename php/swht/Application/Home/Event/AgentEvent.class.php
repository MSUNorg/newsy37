<?php
namespace Home\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author 王贺 
 */
class AgentEvent extends BaseEvent {

    public function lists($model=null){
        parent::lists($model,$p);
    }

    public function add_agent_recrd($moeny=null){
    	$model = M('Agent',"tab_");
    	$data['game_id'] = 0;
    	$data['promote_id'] = PROMOTE_ID;
    	$data['amount'] = $moeny;
    	$data['real_amount'] = $moeny;
    	$data['status'] = 1;
    	$data['pay_type'] = 1;
    	$data['create_time'] = NOW_TIME;
        return	$model->add($data);
    }
    
}
