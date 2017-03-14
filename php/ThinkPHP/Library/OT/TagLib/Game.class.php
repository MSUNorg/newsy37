<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc <zuojiazi.cn@gmail.com> <http://www.msun.com>
// +----------------------------------------------------------------------
namespace OT\TagLib;
use Think\Template\TagLib;
/**
 * 文档模型标签库
 */
class Game extends TagLib{
    /**
     * 定义标签列表
     * @var array
     */
    protected $tags   =  array(
        'page'     => array('attr' => 'cate,listrow', 'close' => 0), //列表分页
        'position' => array('attr' => 'pos,cate,limit,filed,name', 'close' => 1), //
        'recommend'=> array('attr' =>'name,recommend_status,limit,field,','close'=>1), //游戏推荐
        'recommends'=> array('attr' =>'name,recommend_status,limit,field,','close'=>1), //游戏推荐 
        'list'     => array('attr' => 'name,category,child,identify,page,row,field', 'close' => 1), //获取指定分类列表
        'limits'   => array('attr' => 'name,category,sort,limit,field', 'close' => 1), //获取指定分类列表
    );

    /**
    *首页显示限制条数
    */
    public function _limits($tag, $content){

        $name     = $tag['name'];
        $cate     = $tag['category'];
        $sort     = empty($tag['sort']) ? 'false' : $tag['sort'];
        $limit    = empty($tag['limit']) ? '10'    : $tag['limit'];
        $field    = empty($tag['field']) ? 'true'  : $tag['field'];
        $parse  = '<?php ';
        $parse .= '$__CATE__  = '.$cate.";";
        $parse .= '$__LIST__ = D(\'Game\')->game_list_limt(';
        $parse .= '$__CATE__,"'.$sort.'", 1,';
        $parse .= $field .','.$limit.');';
        $parse .= ' ?>';
        $parse .= '<volist name="__LIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /* 推荐位列表 */
    public function _position($tag, $content){
        $pos    = $tag['pos'];
        $cate   = $tag['cate'];
        $limit  = empty($tag['limit']) ? 'null' : $tag['limit'];
        $field  = empty($tag['field']) ? 'true' : $tag['field'];
        $name   = $tag['name'];
        $parse  = '<?php ';
        $parse .= '$__POSLIST__ = D(\'Document\')->position(';
        $parse .= $pos . ',';
        $parse .= $cate . ',';
        $parse .= $limit . ',';
        $parse .= $field . ');';
        $parse .= ' ?>';
        $parse .= '<volist name="__POSLIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    public function _recommend($tag,$content){
        $name     = $tag['name'];
        $cate     = $tag['recommend_status'];
        $child    = empty($tag['child']) ? 'false' : $tag['child'];
        $limit    = empty($tag['limit']) ? '10'    : $tag['limit'];
        $field    = empty($tag['field']) ? 'true'  : $tag['field'];
        $identify = empty($tag['identify']) ? '':$tag['identify'];
        $parse  = '<?php ';
        $parse .= '$__CATE__  = '.$cate.";";
        $parse .= '$__LIST__ = D(\'Game\')->game_recommend_limt(';
        $parse .= '$__CATE__, \'`id` DESC\', 1,';
        $parse .= $field .','.$limit.');';
        $parse .= ' ?>';
        $parse .= '<volist name="__LIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }    
    public function _recommends($tag,$content){
        $name     = $tag['name'];
        $cate     = $tag['recommend_status'];
        $child    = empty($tag['child']) ? 'false' : $tag['child'];
        $limit    = empty($tag['limit']) ? '10'    : $tag['limit'];
        $field    = empty($tag['field']) ? 'true'  : $tag['field'];
        $identify = empty($tag['identify']) ? '':$tag['identify'];
        $parse  = '<?php ';
        $parse .= '$__CATE__  = '.$cate.";";
        $parse .= '$__LIST__ = D(\'Game\')->game_change(';
        $parse .= '$__CATE__, \'`id` DESC\', 1,';
        $parse .= $field .','.$limit.');';
        $parse .= ' ?>';
        $parse .= '<volist name="__LIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /* 列表数据分页 */
    public function _page($tag){
        $cate    = $tag['cate'];
        $listrow = $tag['listrow'];
        $parse   = '<?php ';
        $parse  .= '$__PAGE__ = new \Think\Page(get_list_count(' . $cate . '), ' . $listrow . ');';
        $parse  .= 'echo $__PAGE__->show();';
        $parse  .= ' ?>';
        return $parse;
    }

    /* 段落数据分页 */
    public function _partpage($tag){
        $id      = $tag['id'];
        if ( isset($tag['listrow']) ) {
            $listrow = $tag['listrow'];
        }else{
            $listrow = 10;
        }
        $parse   = '<?php ';
        $parse  .= '$__PAGE__ = new \Think\Page(get_part_count(' . $id . '), ' . $listrow . ');';
        $parse  .= 'echo $__PAGE__->show();';
        $parse  .= ' ?>';
        return $parse;
    }

    /* 段落列表 */
    public function _partlist($tag, $content){
        $id     = $tag['id'];
        $field  = $tag['field'];
        $name   = $tag['name'];
        if ( isset($tag['listrow']) ) {
            $listrow = $tag['listrow'];
        }else{
            $listrow = 10;
        }
        $parse  = '<?php ';
        $parse .= '$__PARTLIST__ = D(\'Document\')->part(' . $id . ',  !empty($_GET["p"])?$_GET["p"]:1, \'' . $field . '\','. $listrow .');';
        $parse .= ' ?>';
        $parse .= '<?php $page=(!empty($_GET["p"])?$_GET["p"]:1)-1; ?>';
        $parse .= '<volist name="__PARTLIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }
}