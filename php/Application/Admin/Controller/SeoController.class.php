<?php

namespace Admin\Controller;
/**
 * lwx
 */
class SeoController extends ThinkController {
    
    public function save() {
        
        $name = $map['name'] = $_POST['name'];
        
        $data = $_POST;unset($data['name']);unset($data['module']);
        
        $flag = M('Seo','tab_')->where($map)->setField($data);
        
        if($flag !== false){
            
            $this->set_config($name,$data);
            
            $this->success('保存成功');
            
        }else{
            
            $this->error('保存失败');
            
        }
        
    }
    
    protected function allValueInfo($name='') {
        
        $map['name'] = array('like','%'.$name.'%');
        
        $seo = M('Seo','tab_')->where($map)->order("id asc")->select();
        
        if (empty($seo)) {$this->error('没有此设置');}
        
        $this->assign('lists',$seo);    
        
    }
    
    public function media() {
        
        $this->allValueInfo('media');
        $this->meta_title = 'SEO搜索';
        
        $this->display();
        
    }
    
    public function channel() {
        
        $this->allValueInfo('channel');
        $this->meta_title = 'SEO搜索';
        $this->display();
        
    }
    
    
    private function set_config($name="",$config=""){
        
        $module = I('request.module');
        
        $config_file ="./Application/Common/Conf/seo_".$module."_config.php";
        
        if(file_exists($config_file)){
            
            $configs=include $config_file;
            
        }else {
            
            $configs=array();
            
        }
        
        #定义一个数组
        
        $data = array();
        
        #给数组赋值
        
        $data[$name] = $config;
        
        $configs=array_merge($configs,$data);
        
        $result = file_put_contents($config_file, "<?php\treturn " . var_export($configs, true) . ";");
   
    }
   
}
