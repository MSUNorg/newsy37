<?php
// +----------------------------------------------------------------------
// | 徐州梦创信息科技有限公司—专业的游戏运营，推广解决方案.
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.vlcms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: kefu@vlcms.com QQ：97471547
// +----------------------------------------------------------------------
namespace Admin\Event;

use Think\Controller;

/**
 * 后台首页控制器
 * 
 * @author zxc <zuojiazi@vip.qq.com>
 */
class StatEvent extends Controller
{

    /**
     * 充值统计
     */
    public function spend_statistics()
    {
        $model_name = "spend";
        $serach_field = array("pay_time",'pay_status');
        $field = "sum(pay_amount) as amount,pay_way";
        $group = "pay_way";
        $order = "pay_way ASC";
        $last_month_amount = $this->last_month_data($model_name, $serach_field, $field, $group, $order);
        $last_month_total = array_sum(array(
            $last_month_amount[0]["amount"],
            $last_month_amount[1]["amount"],
            $last_month_amount[2]["amount"],
			$last_month_amount[3]["amount"]
        ));
        foreach ($last_month_amount as $key => $value) {
            switch ($value['pay_way']) {
                case 0:
                    $ptb =$ptb + $value["amount"];
                    break;
                case 1: // 支付宝
                    $alipay = $alipay + $value["amount"];
                    break;
                case 2: // 微信
                    $weixin = $weixin + $value["amount"];
				case 3: // 聚宝云
                    $jubaopay = $jubaopay + $value["amount"];
                    break;
            }
        }
        $last_data = array(
            $ptb,
            $alipay,
            $weixin,
			$jubaopay,
            $last_month_total == "" ? 0 : $last_month_total
        );
        $this_month_amount = $this->this_month_data($model_name, $serach_field, $field, $group, $order);
        $this_month_total = array_sum(array(
            $this_month_amount[0]["amount"],
            $this_month_amount[1]["amount"],
            $this_month_amount[2]["amount"],
			$this_month_amount[3]["amount"]
        ));
        foreach ($this_month_amount as $key => $value) {
            switch ($value['pay_way']) {
                case 0:
                    $ptb2 = $ptb2 + $value["amount"];
                    break;
                case 1:
                    $alipay2 = $alipay2 + $value["amount"];
                    break;
                case 2:
                    $weixin2 = $weixin2 + $value["amount"];
                    break;
				case 3:
                    $jubaopay2 = $jubaopay2 + $value["amount"];
                    break;
            }
        }
        $this_data = array(
            $ptb2,
            $alipay2,
            $weixin2,
			$jubaopay2,
            $this_month_total == "" ? 0 : $this_month_total
        );
        // var_dump($last_data);exit;
        $this->assign("spend_last_data", $last_data);
        $this->assign("spend_this_data", $this_data);
    }

    /**
     * 注册统计
     */
    public function register_statistics()
    {
        $model_name = "User";
        $serach_field = array("register_time",'lock_status');
        $field = "count(id) as counts,register_way";
        $group = "register_way";
        $order = "register_way ASC";
        $last_month1 = $this->last_month_data($model_name, $serach_field, $field, $group, $order);
        foreach($last_month1 as $k=>$val){
            $value[]=$val['register_way'];
        }
        if(!in_array(0,$value)){
            $last_month[0]=array('counts'=>0,'register_way'=>0);
        }else{
            $last_month[0]=$last_month1[0];
        }
        if(!in_array(1,$value)){
            $last_month[1]=array('counts'=>0,'register_way'=>1);
        }else{
            if(!in_array(0,$value)){
                $last_month[1]=$last_month1[0];
            }else{
                $last_month[1]=$last_month1[1];
            }
        }
        if(!in_array(2,$value)){
            $last_month[2]=array('counts'=>0,'register_way'=>2);
        }else{
            $last_month[2]=end($last_month1);
        }
        $last_month_total = array_sum(array(
            $last_month[0]["counts"],
            $last_month[1]["counts"],
            $last_month[2]["counts"],
        ));
        $last_data = array(
            $last_month[2]["counts"],
            $last_month[1]["counts"],
            $last_month[0]["counts"],
            $last_month_total
        );
        $this_month1 = $this->this_month_data($model_name, $serach_field, $field, $group, $order);
        foreach($this_month1 as $k=>$val){
            $value[]=$val['register_way'];
        }
        if(!in_array(0,$value)){
            $this_month[0]=array('counts'=>0,'register_way'=>0);
        }else{
            $this_month[0]=$this_month1[0];
        }
        if(!in_array(1,$value)){
            $this_month[1]=array('counts'=>0,'register_way'=>1);
        }else{
            if(!in_array(0,$value)){
                $this_month[1]=$this_month1[0];
            }else{
                $this_month[1]=$this_month1[1];
            }
        }
        if(!in_array(2,$value)){
            $this_month[2]=array('counts'=>0,'register_way'=>2);
        }else{
            $this_month[2]=end($this_month1);
        }
        $this_month_total = array_sum(array(
            $this_month[0]["counts"],
            $this_month[1]["counts"],
            $this_month[2]["counts"]
        ));
        $this_data = array(
            $this_month[2]["counts"],
            $this_month[1]["counts"],
            $this_month[0]["counts"],
            $this_month_total
        );
        
        $this->assign("reg_last_data", $last_data);
        $this->assign("reg_this_data", $this_data);
    }

    /**
     * 本年总充值
     */
    public function spend_statistics_year()
    {
        $model_name = "spend";
        $serach_field = "pay_time";
        $field = "FROM_UNIXTIME(pay_time, '%c') as month,sum(pay_amount) as amount";
        $group = "FROM_UNIXTIME(pay_time,'%Y%m%d')";
        $order = "pay_time ASC";
        $map["pay_status"] = 1;
        $year_total = $this->data_year($model_name, $map, $serach_field, $field, $group, $order);
        $map["promote_id"] = array(
            "neq",
            "0"
        );
        $map2["promote_id"] = array(
            "eq",
            "0"
        );
        $map2["pay_status"] = 1;
        $year_promote = $this->data_year($model_name, $map, $serach_field, $field, $group, $order, $where);
        $ziran_promote = $this->data_year($model_name, $map2, $serach_field, $field, $group, $order, $where);
        $this->assign("ziran_promote", $ziran_promote);
        $this->assign("year_total", $year_total);
        $this->assign("year_promote", $year_promote);
    }

    /**
     * 本年总注册
     */
    public function register_statistics_year()
    {
        $model_name = "User";
        $serach_field = "register_time";
        $field = "FROM_UNIXTIME(register_time, '%c') as month,count(id) as counts";
        $group = "FROM_UNIXTIME(register_time,'%c')";
        $order = "register_time ASC";
        $map["lock_status"] = 1;
        $last_data = $this->user_data_year($model_name, $map, $serach_field, $field, $group, $order);
        $map["promote_id"] = array(
            "neq",
            "0"
        );
        $map2["promote_id"] = array(
            "eq",
            "0"
        );
        $this_data = $this->user_data_year($model_name, $map, $serach_field, $field, $group, $order);
        $ziran_data = $this->user_data_year($model_name, $map2, $serach_field, $field, $group, $order);
        $this->assign("reg_data_year", $last_data);
        $this->assign("prom_data_year", $this_data);
        $this->assign("ziran_data_year", $ziran_data);
    }

    /**
     * 上月数据
     */
    public function last_month_data($model_name, $serach_field, $field = true, $group = "", $order = "")
    {
        $last_month_start = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))));
        $last_month_end = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), 0, date("Y"))));
        $map[reset($serach_field)] = array(
            "BETWEEN",
            array(
                $last_month_start,
                $last_month_end
            )
        );
        $map[end($serach_field)]=1;
        // var_dump($map);exit;
        $model = D($model_name);
        $data = $model->field($field)
            ->where($map)
            ->group($group)
            ->order($order)
            ->select();
        return $data;
    }

    /**
     * 本月数据
     */
    protected function this_month_data($model_name, $serach_field, $field = true, $group = "", $order = "")
    {
        $this_month_start = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y"))));
        $this_month_end = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("t"), date("Y"))));
        $map[reset($serach_field)] = array(
            "BETWEEN",
            array(
                $this_month_start,
                $this_month_end
            )
        );
        $map[end($serach_field)]=1;
        // var_dump($map);exit;
        $model = D($model_name);
        $data = $model->field($field)
            ->where($map)
            ->group($group)
            ->order($order)
            ->select();
        return $data;
    }

    /**
     * 本年数据 根据月份分组
     */
    protected function data_year($model_name, $map, $serach_field, $field = true, $group = "", $order = "")
    {
        $this_year_start = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, date("Y"))));
        $this_year_end = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, 12, 31, date("Y"))));
        $map[$serach_field] = array(
            "BETWEEN",
            array(
                $this_year_start,
                $this_year_end
            )
        );
        $model = D($model_name);
        $data = $model->field($field)
            ->where($map)
            ->group($group)
            ->order($order)
            ->select();
        $data = i_array_column($data, 'amount', 'month');
        return $data;
    }

    protected function user_data_year($model_name, $map, $serach_field, $field = true, $group = "", $order = "", $where = "")
    {
        $this_year_start = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, date("Y"))));
        $this_year_end = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, 12, 31, date("Y"))));
        $map[$serach_field] = array(
            "BETWEEN",
            array(
                $this_year_start,
                $this_year_end
            )
        );
        $model = D($model_name);
        $data = $model->field($field)
            ->where($map)
            ->group($group)
            ->order($order)
            ->select();
         $data = i_array_column($data, 'counts', 'month');
        return $data;
    }
}
