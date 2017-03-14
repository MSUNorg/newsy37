<?php
// +----------------------------------------------------------------------
// | 手游平台
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.msun.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc
// +----------------------------------------------------------------------


namespace Addons\DevTeam;
use Common\Controller\Addon;

/**
 * 开发团队信息插件
 * @author thinkphp
 */

    class DevTeamAddon extends Addon{

        public $info = array(
            'name'=>'DevTeam',
            'title'=>'开发团队信息',
            'description'=>'开发团队成员信息',
            'status'=>1,
            'author'=>'thinkphp',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的AdminIndex钩子方法
        public function AdminIndex($param){
            $config = $this->getConfig();
            $this->assign('addons_config', $config);
            if($config['display'])
                $this->display('widget');
        }
    }