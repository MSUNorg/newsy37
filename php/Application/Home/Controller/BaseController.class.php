<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends HomeController{
    
    protected function _initialize(){
        parent::_initialize();
        $this->login();
        $map['id']=get_pid();
       $pro=M("promote","tab_")->where($map)->find();
       define('PLEVEL',$pro['parent_id']);
       define('PID',is_login_promote());
       $this->assign("parent_id",$pro['parent_id']);
    }


    /* 用户登录检测 */
    protected function login(){
        /* 用户登录检测 */
        is_login_promote() || $this->error('您还没有登录，请先登录！', U('Index/index'));
    }


    /**
     * 显示指定模型列表数据
     * @param  String $model 模型标识
     * @author zxc <zuojiazi@vip.qq.com>
     */
    public function lists($model = null, $p = 0,$extend_map = array()){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //获取模型信息
        $model = M('Model')->getByName($model);
        $model || $this->error('模型不存在！');

        //解析列表规则
        $fields = array();
        // 关键字搜索
        $map    =   $extend_map;
        $key    =   $model['search_key']?$model['search_key']:'title';
        if(isset($_REQUEST[$key])){
            $map[$key]  =   array('like','%'.$_GET[$key].'%');
            unset($_REQUEST[$key]);
        }
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name] =   $val;
            }
        }
        $row    = empty($model['list_row']) ? 15 : $model['list_row'];
        $name = parse_name(get_table_name($model['id']), true);
        $data = M($name,"tab_")
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(empty($fields) ? true : $fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['need_pk']?'id DESC':'')
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();

        /* 查询记录总数 */
        $count = M($name,"tab_")->where($map)->count();

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            //分页跳转的时候保证查询条件
            // var_dump($_REQUEST);exit;
            foreach($_REQUEST as $key=>$val) {
                $page->parameter[$key]   =  $val;
            }
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        //$data   =   $this->parseDocumentList($data,$model['id']);
        $this->assign("count",$count);
        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }

    
}
