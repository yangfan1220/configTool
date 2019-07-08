<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-21
 * Time: 11:29
 */

namespace app\models;

use app\models\tables\ProjectRedisInfo;
use app\models\tables\ProjectInfo;
use app\models\tables\CommonDataStorage;
use app\models\Mail\MailMessageStruct;
use app\models\Mail\SendMail;

class SetValue
{
    public static $redisConnection;

    /**
     * 当前项目信息
     * @var
     */
    public static $currentProject;

    public static function getAllProject()
    {
        $allProjectInfo = ProjectInfo::find()->asArray()->all();
        self::$currentProject = $allProjectInfo;
        return array_column($allProjectInfo, 'app_id', 'id');
    }

    public static function getRedisInfoByProjectKey($projectAppId)
    {
        $redisInfo = ProjectRedisInfo::find()->where(['project_app_id' => $projectAppId])->asArray()->one();
        //未来拆表的话，修改这里的获取redis信息逻辑
        return $redisInfo;
    }

    public static function getConfDataByProjectKey($projectKey)
    {
        CommonDataStorage::setTableName($projectKey);
        $confData = CommonDataStorage::find()->asArray()->all();
        return array_column($confData, 'value', 'key');
    }

    /**
     * 设置redis连接信息  在用
     * @param $redisInfo
     */
    public static function setConfDataRedisInfo($redisInfo)
    {
        self::$redisConnection = \Yii::$app->redis;
        self::$redisConnection->hostname = $redisInfo['redis_host'];
        self::$redisConnection->port = $redisInfo['redis_port'];
        self::$redisConnection->database = $redisInfo['redis_database_id'];

        !empty($redisInfo['redis_password']) && self::$redisConnection->password = $redisInfo['redis_password'];
    }

    /**
     * 测试连接   在用
     * @return bool
     */
    public static function testConnect()
    {
        $testSetRe = self::$redisConnection->set('test', 'testConnection');
        if ($testSetRe) {
            $testDelRe = self::$redisConnection->del('test');
            return $testDelRe ? true : false;
        }
        return false;
    }

    public static function setRedisValue($data, $projectKey)
    {
        foreach ($data as $key => $value) {
            try{
                $setRe=self::$redisConnection->set(static::getKeysRule($projectKey, $key), $value, 'ex', '3600');
                if ($setRe == false) {
                    throw new \Exception('SetValue::setRedisValue() 推送数据到redis失败');
                }
            }catch (\Exception $e){
                MailMessageStruct::unshiftMailMessage($e->getMessage());
                MailMessageStruct::pushMailMessage('当前key为'.$key.'   '.'value为'.$value);
                continue;
            }
        }

        if(!empty(MailMessageStruct::$mailMessages)){
            MailMessageStruct::unshiftMailMessage('当前app_id: '.\Yii::$app->session['app_id']);
            SendMail::send();
        }
    }

    /**
     * 未来修改设置key的规则 在这里修改
     * @param $projectKey
     * @param $key
     * @return string
     */
    public static function getKeysRule($projectKey, $key)
    {
        return $projectKey . '_set_' . $key;
    }
}