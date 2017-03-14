<?php

namespace Addons\UploadImages;
use Common\Controller\Addon;

/**
 * 图片批量上传插件
 * @author 原作者:tjr&jj
 * @author 木梁大囧
 */

    class UploadImagesAddon extends Addon{
        public $info = array(
            'name' => 'UploadImages',
            'title' => '多图上传',
            'description' => '多图上传',
            'status' => 1,
            'author' => '木梁大囧',
            'version' => '1.2'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的UploadImages钩子方法
        public function UploadImages($param){
            $name = $param['name'] ?: 'pics';
            $valArr = $param['value'] ? explode(',', $param['value']) : array();
            $this->assign('name',$name);
            $this->assign('valStr',$param['value']);
            $this->assign('valArr',$valArr);
            $this->display('upload');
        }
    }