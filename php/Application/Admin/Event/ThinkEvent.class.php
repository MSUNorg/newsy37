<?php
namespace Admin\Event;
use Think\Controller;
/**
 * 后台事件控制器
 * @author 王贺 
 */
class ThinkEvent extends Controller {

    public function lists($model = null, $p = 0){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =   $model['map'];
        $key    =   $model['key'];

        if(!empty($model['field_time'])){
            if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                $map[$model['field_time']]  = array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
                unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
            }
        }
        foreach ($key as $key => $value) {
            if(isset($_REQUEST[$value])){
                $map[$value]  =   array('like','%'.$_GET[$value].'%');
                unset($_REQUEST[$value]);
            }
        }
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name] =   $val;
            }
        }

        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $data = D($name)
             /* 查询指定字段，不指定则查询所有字段 */
            ->field(empty($fields) ? true : $fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['order'])
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();

        /* 查询记录总数 */
        $count = D($name)->where($map)->count();

         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }


    public function join_lists($model = null, $p = 0){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //解析列表规则
        $fields = $model['fields'];
        // 关键字搜索
        $map    =   $model['map'];
        $key    =   $model['key'];

        if(!empty($model['field_time'])){
            if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                $map[$model['field_time']]  = array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
                unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
            }
        }
        foreach ($key as $key => $value) {
            if(isset($_REQUEST[$value])){
                $map[$value]  =   array('like','%'.$_GET[$value].'%');
                unset($_REQUEST[$value]);
            }
        }
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name] =   $val;
            }
        }

        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        $name = $model['m_name'];
        $data = D($name)
             /* 查询指定字段，不指定则查询所有字段 */
            ->field(empty($fields) ? true : $fields)
            ->join($model['join'])
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['order'])
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();

        /* 查询记录总数 */
        $count = D($name)->where($map)->join($model['join'])->count();

         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('model', $model);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
        
    }

    public function extend_edit($model = null, $id = 0,$data){

        if(IS_POST){
            $Model  =   D($model['m_name']);
            if($Model->save($data)){
                $this->success('保存'.$model['title'].'成功！', U($model['jump_url']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            //获取数据
            $data       = D($model['m_name'])->find($id);
            $data || $this->error('数据不存在！');

            $this->assign('model', $model);
            $this->assign('data', $data);
            $this->meta_title = '编辑'.$model['title'];
            $this->display($model['template_edit']?$model['template_edit']:'');
        }
    }

    public function model_entity($id,$model){
        $entity = D($model['m_name']);
        $map[$model['field']] = $id;
        $data = $entity->where($map)->find();
        return $data;
    }
}
