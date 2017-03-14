<?php
namespace Sdk\Controller;
use Think\Controller\RestController;
use JPush\src\JPush;
use JPush\src\core\JPushException;
class JpushController extends RestController{

    public function ceshi(){
        Vendor("Jpush.src.JPush");
        $app_key = 'b244ff43b9579721776e2ab6';
        $master_secret = 'a0af641f06f7e7321d17e591';
        $client = new \JPush($app_key, $master_secret);
      $payload = $client->push()
    ->setPlatform("all")
    ->addAllAudience()
    ->setNotificationAlert("Hi, 这是一条定时发送的消息")
    ->build();

// 创建一个2016-12-22 13:45:00触发的定时任务
$response = $client->schedule()->createSingleSchedule("每天14点发送的定时任务", $payload, array("time"=>"2016-06-27 15:50:10"));
echo 'Result=' . json_encode($response) . $br;
    }
}

