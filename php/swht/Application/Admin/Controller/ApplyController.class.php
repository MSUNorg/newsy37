<?php
// +----------------------------------------------------------------------
// | 手游平台
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.msun.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zxc
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
use OSS\OssClient;
use OSS\Core\OSsException;
/**
 * 后台首页控制器
 * @author zxc <zuojiazi@vip.qq.com>
 */
class ApplyController extends ThinkController {
    //private $table_name="Game";
    const model_name = 'Apply';

    public function lists(){
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_id']=get_game_id($_REQUEST['game_name']);
                unset($_REQUEST['game_name']);
            }
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['promote_id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['apply_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['apply_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        parent::lists(self::model_name,$_GET["p"],$map);
    }

    public function edit($id=null){
        $id || $this->error('请选择要编辑的用户！');
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::edit($model['id'],$id);
    }

    public function set_status($model='Apply'){
        parent::set_status($model);
    }

    public function del($model = null, $ids=null){
        $source = D(self::model_name);
        $id = array_unique((array)$ids);
        $map = array('id' => array('in', $id) );
        $list = $source->where($map)->select();
        foreach ($list as $key => $value) {
            $file_url = APP_ROOT.$value['pack_url'];
            unlink($file_url);
        }
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids,"tab_");
    }


    public function package($ids=null)
    {
        try{
            $ids || $this->error("打包数据不存在");
            $apply_data = D('Apply')->find($ids);
            //验证数据正确性
            if(empty($apply_data) || $apply_data["status"] != 1){$this->error("未审核或数据错误"); exit();}
            #获取原包数据
            $source_file = $this->game_source($apply_data["game_id"],1);
            if(substr($source_file['file_url'] , 0 , 2)==".."){
                $source_file['file_url']=substr($source_file['file_url'],'1',strlen($source_file['file_url']));
            }
            //验证原包是否存在
            if(empty($source_file) || !file_exists(".".$source_file['file_url'])){$this->error("游戏原包不存在"); exit();}
            
            $newname = "game_package" .$apply_data["game_id"]."-".$apply_data['promote_id'].".apk";
            $to = "./Uploads/GamePack/".$newname;
            copy(".".$source_file['file_url'],".".$to);                  
            $zip = new \ZipArchive;
            $res = $zip->open("../Uploads/GamePack/".$newname, \ZipArchive::CREATE);
              if ($res==TRUE) {
                    $pack_data = array(
                    "game_id"    => $source_file["game_id"],
                    "game_name"  => $source_file['game_name'],
                    "game_appid" => get_game_appid($source_file["game_id"],"id"),
                    "promote_id" => $apply_data['promote_id'],
                    "promote_account" => $apply_data["promote_account"],
                );
                $zip->addFromString('META-INF/mch.properties', json_encode($pack_data));
                $zip->close();                
                if(get_tool_status("oss_storage")==1){
                    $newname = "game_package" .$apply_data["game_id"]."-".$apply_data['promote_id'].".apk";
                    $to = "http://".C("oss_storage.bucket").".".C("oss_storage.domain")."/GamePak/".$newname;
                    $to = str_replace('-internal', '', $to);
                    $new_to="../Uploads/GamePack/".$newname;
                    $updata['savename'] = $newname;
                    $updata['path'] = $new_to;
                    $this->upload_game_pak_oss($updata);
                    @unlink ($new_to);
                }
                $promote = array('game_id'=>$apply_data['game_id'],'promote_id'=>$apply_data['promote_id']);
                $jieguo = $this->updateinfo($ids,$to,$apply_data);
                if($jieguo){
                    $this->success("成功",U('lists'));
                }
                else{
                    $this->error("操作失败");
                }
            } else {
                throw new \Exception('分包失败');
            }
        }
        catch(\Exception $e){
            $this->error($e->getMessage());
        }
    }

    /**
    *上传到OSS
    */
    public function upload_game_pak_oss($return_data=null){
        /**
        * 根据Config配置，得到一个OssClient实例
        */
        try {
            Vendor('OSS.autoload');
            $ossClient = new \OSS\OssClient(C("oss_storage.accesskeyid"), C("oss_storage.accesskeysecr"), C("oss_storage.domain"));
        } catch (OssException $e) {
            $this->error($e->getMessage());
        }

        $bucket = C('oss_storage.bucket');
        $oss_file_path ="GamePak/". $return_data["savename"];

        $avatar = $return_data["path"];
        try {

         $this->multiuploadFile($ossClient,$bucket,$oss_file_path,$avatar);        
        return true;
        } catch (OssException $e) {
            /* 返回JSON数据 */
           $this->error($e->getMessage());
        }
    }

    /**
    *修改申请信息
    */
    public function updateinfo($id,$pack_url,$promote){
        $model = M('Apply',"tab_");
        $data['id'] = $id;
        $data['pack_url'] = $pack_url;
        $data['dow_url']  = '/index.php?s=/Home/Down/down_file/game_id/'.$promote['game_id'].'/promote_id/'.$promote['promote_id'];
        $data['dispose_id'] = UID;
        $data['dispose_time'] = NOW_TIME;
        $res = $model->save($data);
        return $res;
    }

    public function game_source($game_id,$type){
        $model = D('Source');
        $map['game_id'] = $game_id;
        $map['type'] = $type;
        $data = $model->where($map)->find();
        return $data;
    }

    public function multiuploadFile($ossClient, $bucket,$url,$file){
        //$file = __FILE__;
        $options = array();
        try{
            #初始化分片上传文件
            $uploadId = $ossClient->initiateMultipartUpload($bucket, $url);
            //$ossClient->multiuploadFile($bucket, $url, $file, $options);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": initiateMultipartUpload FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        /*
         * step 2. 上传分片
         */
        $partSize = 5 * 1000 * 1024;
        $uploadFile = $file;
        $uploadFileSize = filesize($uploadFile);
        $pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
        $responseUploadPart = array();
        $uploadPosition = 0;
        $isCheckMd5 = true;
        foreach ($pieces as $i => $piece) {
            $fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
            $toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
            $upOptions = array(
                $ossClient::OSS_FILE_UPLOAD => $uploadFile,
                $ossClient::OSS_PART_NUM => ($i + 1),
                $ossClient::OSS_SEEK_TO => $fromPos,
                $ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
                $ossClient::OSS_CHECK_MD5 => $isCheckMd5,
            );
            if ($isCheckMd5) {
                $contentMd5 = \OSS\Core\OssUtil::getMd5SumForFile($uploadFile, $fromPos, $toPos);
                $upOptions[$ossClient::OSS_CONTENT_MD5] = $contentMd5;
            }
            //2. 将每一分片上传到OSS
            try {
                $responseUploadPart[] = $ossClient->uploadPart($bucket, $url, $uploadId, $upOptions);
            } catch(OssException $e) {
                printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} FAILED\n");
                printf($e->getMessage() . "\n");
                return;
            }
            //printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} OK\n");
        }
        $uploadParts = array();
        foreach ($responseUploadPart as $i => $eTag) {
            $uploadParts[] = array(
                'PartNumber' => ($i + 1),
                'ETag' => $eTag,
            );
        }
        /**
         * step 3. 完成上传
         */
        try {
            $ossClient->completeMultipartUpload($bucket, $url, $uploadId, $uploadParts);
        }  catch(OssException $e) {
            printf(__FUNCTION__ . ": completeMultipartUpload FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
    }               

}
