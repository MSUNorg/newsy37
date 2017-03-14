<?php
namespace Admin\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author 王贺 
 */
class BaseEvent extends Controller {

    public function search_game($model,$p){
        $this->search_lists($model,$p);
    }

    public function search_gift($model,$p){
        
    }

    protected function search_lists($model,$p){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
		$game  = M($model['m_name'],$model['prefix']);
		$map = $model['map'];
		$row = 10;
		$data  = $game->where($map)->order($model['order'])->select();
		$count = $game->where($map)->count();
		//分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign("count",$cuont);
        $this->assign('search_data', $data);
        $this->display($model['tmeplate_list']);
    }

    protected function search_join(){
    	$page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
		$game  = M($model['m_name'],$model['prefix']);
		$map = $model['map'];
		$data  = $game
		->field($model['field'])
		->join($model['join'])
		->where($map)->order($model['order'])->select();
		$count = $game->join($model['join'])->where($map)->count();
		//分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign("count",$cuont);
        $this->assign('list_data', $data);
        $this->display($model['tmeplate_list']);
    }

    
}
