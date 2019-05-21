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


class SetValueController extends Controller
{
    public function actionIndex()
    {
        //获取所有的项目信息的key
        $AllProject = SetValue::getAllProject();
        foreach ($AllProject as $projectID => $projectKey) {
            //获取当前项目的redis信息
            $redisInfo = SetValue::getRedisInfoByProjectKey($projectKey);
            //设置当前项目的redis信息
            SetValue::setConfDataRedisInfo($redisInfo);
            //测试连接是否正常
            $testConnectRe = SetValue::testConnect();
            if ($testConnectRe === false) {
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