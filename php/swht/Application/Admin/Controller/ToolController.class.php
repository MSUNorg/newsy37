<?php

namespace Admin\Controller;
/**
 * 后台首页控制器
 * @author zxc
 */
class ToolController extends ThinkController {
    
    public function saveTool($value='')
    {
        $name   = $_POST['name'];
        $config = I('config');
        $data   = array('config'=>json_encode($config),'template'=>$_POST['template'],'status'=>$_POST['status']);
        $flag   = M('Tool','tab_')->where("name='{$name}'")->setField($data);
        if($flag !== false){
            $this->success('保存成功');
        }else{
            $this->error('保存失败');
        }
    }

    protected function BaseConfig($name='')
    {   
        $map['name'] = array('in',$name);
        $tool = M('tool',"tab_")->where($map)->select();
        if(empty($tool)){$this->error('没有此设置');}
        foreach ($tool as $key => $val) {
            $this->assign($tool[$key]['name'],json_decode($tool[$key]['config'],true));
            unset($tool[$key]['config']);
            $this->assign($tool[$key]['name']."_data",$tool[$key]);
        }
        
    }
    /**
    *短信设置
    */
    public function smsset($value='')
    {
        $this->BaseConfig("sms_set");
        $this->display();
    }

    /**
    *文件存储
    */
    public function storage($value='')
    {
        $str = "oss_storage,qiniu_storage";
        $this->BaseConfig($str);
        $this->display();
    }

    /**
    *支付设置
    */
    public function payset($value='')
    {
        $str = "oss_storage,qiniu_storage";
        $this->BaseConfig($str);
        $this->display();
    }

    /**
    *邮件设置
    */
    public function email($value='')
    {
        $str = "email_set";
        $this->BaseConfig($str);
        $this->display();
    }

    /**
    *第三方登陆设置
    */
    public function thirdparty($value='')
    {
        $str = "qq_login,wx_login";
        $this->BaseConfig($str);
        $this->display();
    }
}
