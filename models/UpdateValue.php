<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-21
 * Time: 17:19
 */

namespace app\models;


use yii\web\NotFoundHttpException;

class UpdateValue
{
    private static $willSetRedisKey;

    public static function setRedisValue($postParams)
    {
        static::testConnection($postParams['CommonConfigData']['key']);
        $setRe = SetValue::$redisConnection->set(static::$willSetRedisKey, $postParams['CommonConfigData']['value']);
        if ($setRe == false) {
            //TODO 失败日志、邮件、等等通知
            return false;
        }
        //TODO 成功日志、邮件、等等通知
        return true;
    }

    private static function testConnection($key)
    {
        $projectKey = \Yii::$app->session['pk'];
        static::$willSetRedisKey = SetValue::getKeysRule($projectKey, $key);
        $redisInfo = SetValue::getRedisInfoByProjectKey($projectKey);
        //设置当前项目的redis信息
        SetValue::setConfDataRedisInfo($redisInfo);
        //测试连接是否正常
        $testConnectRe = SetValue::testConnect();
        if ($testConnectRe === false) {
            throw new NotFoundHttpException('redis连接失败');
        }
    }

    public static function delRedisValue($key)
    {
        static::testConnection($key);
        $setRe = SetValue::$redisConnection->del(static::$willSetRedisKey);
        if ($setRe == false) {
            //TODO 失败日志、邮件、等等通知
            return false;
        }
        //TODO 成功日志、邮件、等等通知
        return true;
    }
}