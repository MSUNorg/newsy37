<?php

namespace Sdk\Controller;
use Common\Api\GameApi;
/**
 * 支付游戏回调控制器
 * @author 小纯洁 
 */
class GameNotifyController extends BaseController {

    /**
    *游戏支付通知地址
    */
    public function game_pay_notify()
    {
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);

        $param['out_trade_no'] = $request['out_trade_no'];
        $param['price']   = $request['price'];
        $param['extend']  = $request['extend'];
        $param['game_id'] = $request['game_id'];
        $game = new GameApi();
        #游戏支付通知
        $result = $game->game_pay_notify($param);
        if($result == "success1"){
            $result = $this->update_game_pay_status($request['out_trade_no'],$request['code'],$request['extend']);
            $this->set_message(1,'success','游戏支付成功');
        }else{
            $this->set_message(0,'fail','游戏支付失败');
        }
    }

    /**
    *修改游戏支付状态
    */
    private function update_game_pay_status($out_trade_no="",$code=1,$extend=""){
        $result = false;
        $map['pay_order_number'] = $out_trade_no;
        $data = array("pay_game_status"=>1,"extend"=>$extend);
        switch ($code) {
            case 1:
                $result = M('spend',"tab_")->where($map)->setField($data);
                break;
            default:
                $result = M('BindSpend',"tab_")->where($map)->setField($data);
                break;
        }

        return $result;

    }
}
