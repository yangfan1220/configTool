<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-21
 * Time: 11:41
 */

namespace app\commands;


use app\models\SetValue;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Mail\MailMessageStruct;
use app\models\Mail\SendMail;


class SetValueController extends Controller
{
    public function actionIndex()
    {
        //获取所有的项目信息的key
        $AllProject = SetValue::getAllProject();
        foreach ($AllProject as $projectKey=>$releaseStatus) {
            if ($releaseStatus!=2){
                continue;
            }
//            if(SetValue::theTableIsExist($projectKey)===false){
//                continue;
//            }
            //获取当前项目的redis信息
            $redisInfo = SetValue::getRedisInfoByProjectKey($projectKey);
            //设置当前项目的redis信息
            SetValue::setConfDataRedisInfo($redisInfo);
            //测试连接是否正常
            try{
                $testConnectRe = SetValue::testConnect();
                if ($testConnectRe === false) {
                   throw new \Exception('SetValue::testConnect() 设置OR连接失败');
                }
            }catch (\Exception $e){
                MailMessageStruct::unshiftMailMessage($e->getMessage());
                MailMessageStruct::unshiftMailMessage('当前app_id: '.\Yii::$app->session['app_id']);
                SendMail::send();
                MailMessageStruct::$mailMessages=[];
                continue;
            }
            //获取项目的配置数据
            $ConfData = SetValue::getConfDataByProjectKey($projectKey);
            //将项目的配置数据推送到redis

            SetValue::setRedisValue($ConfData,$projectKey);
        }
        return ExitCode::OK;
    }
}