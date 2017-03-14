<?php
	namespace Upload\Driver\OSS;

	class OSS {

		public $accessKeyId = '';
		public $accessKeySecret = '';
		public $endpoint = '';

		public function __construct($accessKeyId,$accessKeySecret,$endpoint){
				$this->accessKeyId=$accessKeyId;
				$this->accessKeySecret=$accessKeySecret;
				$this->endpoint=$endpoint;
		}

			/**
			 * 创建一存储空间
			 * acl 指的是bucket的访问控制权限，有三种，私有读写，公共读私有写，公共读写。
			 * 私有读写就是只有bucket的拥有者或授权用户才有权限操作
			 * 三种权限分别对应OSSClient::OSS_ACL_TYPE_PRIVATE，
			 *               OssClient::OSS_ACL_TYPE_PUBLIC_READ,
			 *               OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE
			 *
			 * @param OssClient $ossClient OSSClient实例
			 * @param string    $bucket 要创建的bucket名字
			 * @return null
			 */
			public function createBucket($ossClient, $bucket){
			    try {
			        $ossClient->createBucket($bucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
			    } catch (OssException $e) {
			        printf(__FUNCTION__ . ": FAILED\n");
			        printf($e->getMessage() . "\n");
			        return;
			    }
			    print(__FUNCTION__ . ": OK" . "\n");
			}


			/**
			 * 列出用户所有的Bucket
			 *
			 * @param OssClient $ossClient OssClient实例
			 * @return null
			 */
			public function listBuckets($ossClient){
			    $bucketList = null;
			    try{
			        $bucketListInfo = $ossClient->listBuckets();
			    } catch(OssException $e) {
			        printf(__FUNCTION__ . ": FAILED\n");
			        printf($e->getMessage() . "\n");
			        return;
			    }
			    $bucketList = $bucketListInfo->getBucketList();
			    foreach($bucketList as $bucket) {
			        print($bucket->getLocation() . "\t" . $bucket->getName() . "\t" . $bucket->getCreatedate() . "\n");
			    }
			}

				/**
				 * 获取bucket的acl配置
				 *
				 * @param OssClient $ossClient OssClient实例
				 * @param string $bucket 存储空间名称
				 * @return null
				 */
				public function getBucketAcl($ossClient, $bucket){
				    try {
				        $res = $ossClient->getBucketAcl($bucket);
				    } catch (OssException $e) {
				        printf(__FUNCTION__ . ": FAILED\n");
				        printf($e->getMessage() . "\n");
				        return;
				    }
				    print(__FUNCTION__ . ": OK" . "\n");
				    print('acl: ' . $res);
				}

					/**
					 * 删除存储空间
					 *
					 * @param OssClient $ossClient OSSClient实例
					 * @param string    $bucket 待删除的存储空间名称
					 * @return null
					 */
					public function deleteBucket($ossClient, $bucket){
					    try{
					        $ossClient->deleteBucket($bucket);
					    } catch(OssException $e) {
					        printf(__FUNCTION__ . ": FAILED\n");
					        printf($e->getMessage() . "\n");
					        return;
					    }
					    print(__FUNCTION__ . ": OK" . "\n");
					}
						
			    /**
                 * 通过multipart上传文件
                 *
                 * @param OssClient $ossClient OSSClient实例
                 * @param string $bucket 存储空间名称
                 * @param string $url 路径
                 * @param string $file 文件
                 * @return null
                 */
                public function multiuploadFile($ossClient, $bucket,$url,$file){
                    $file = __FILE__;
                    $options = array();
                    try{
                        $ossClient->multiuploadFile($bucket, $url, $file, $options);
                    } catch(OssException $e) {
                        printf(__FUNCTION__ . ": FAILED\n");
                        printf($e->getMessage() . "\n");
                        return;
                    }
                    print(__FUNCTION__ . ":  OK" . "\n");
                }
	}
