<?php

namespace Mobile\Controller;
use Think\Controller;

/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class DownController extends Controller {
	
	public function down_file($game_id=0,$type=1){
		$model = M('Game','tab_');
		$map['tab_game.id'] = $game_id;
		//$map['file_type'] = $type;
		$data = $model
        ->field('tab_game_source.*,tab_game.game_name,tab_game.game_address')
        ->join("left join tab_game_source on tab_game.id = tab_game_source.game_id")->where($map)->find();
        if(!empty($data['file_url'])){
            $this->down($data['file_url']);
        }
		else{
            Header("HTTP/1.1 303 See Other");
            Header("Location: ".$data['game_address']); 
        }
         M('Game','tab_')->where(array('id'=>$game_id))->setInc('dow_num',1);
	}

    function access_url($url) {    
        if ($url=='') return false;    
        $fp = fopen($url, 'r') or exit('Open url faild!');    
        if($fp){  
        while(!feof($fp)) {    
            $file.=fgets($fp)."";  
        }  
        fclose($fp);    
        }  
        return $file;  
    }  

        public function down($file, $isLarge = false, $rename = NULL) {
            if(headers_sent())return false;
            if(!$file) {
                $this->error('文件不存在哦 亲!');
                //exit('Error 404:The file not found!');
            }
            if($rename==NULL){
                if(strpos($file, '/')===false && strpos($file, '\\')===false)
                    $filename = $file;
                else{
                    $filename = basename($file);
                }
            }else{
                $filename = $rename;
            }
            header('Content-Description: File Transfer');
            header("Content-Type: application/force-download;");
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: binary");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));//$_SERVER['DOCUMENT_ROOT'].
            header("Pragma: no-cache"); //不缓存页面
            //ob_clean();
            flush();
            if($isLarge)
                self::readfileChunked($file);
            else
                readfile($file);
            }

}
