<?php

namespace Media\Controller;
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
        ->field('tab_game_source.*,game_name,tab_game.game_address')
        ->join("left join tab_game_source on tab_game.id = tab_game_source.game_id")->where($map)->find();
        if(!varify_url($data['game_address'])){
            $this->down($data['file_url']);
        }
		else{
            Header("HTTP/1.1 303 See Other");
            Header("Location: ".$data['game_address']); 
        }
        M('Game','tab_')->where('id='.$game_id)->setInc('dow_num');
        $this->add_down_stat($game_id);
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

	public function down($file, $isLarge = false, $rename = NULL)
	{
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
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: binary");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($_SERVER['DOCUMENT_ROOT'].$file));
        //ob_clean();
        flush();
        if($isLarge)
            self::readfileChunked($file);
        else
            readfile($_SERVER['DOCUMENT_ROOT'].$file);
    }

    /**
    *游戏下载统计
    */
    public function add_down_stat($game_id=null){
        $model = M('down_stat','tab_');
        $data['promote_id'] = 0;
        $data['game_id'] = $game_id;
        $data['number'] = 1;
        $data['type'] = 0;
        $data['create_time'] = NOW_TIME;
        $model->add($data);
    }
}
