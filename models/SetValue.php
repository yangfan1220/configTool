<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-21
 * Time: 11:29
 */

namespace app\models;


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
        return array_column($allProjectInfo, 'project_key', 'id');
    }

    public static function getRedisInfoByProjectKey($projectKey)
    {
        $redisInfo = ProjectInfo::find()->where(['project_key' => $projectKey])->asArray()->one();
        /**
         *
         * $redisInfo =>>>>>>>>
         * array(9) {
         *'id' =>
         *string(1) "4"
         *'project_name' =>
         *string(9) "测试一"
         *'project_key' =>
         *string(32) "b59c67bf196a4758191e42f76670ceba"
         *'redis_host' =>
         *string(9) "localhost"
         *'redis_port' =>
         *string(4) "6379"
         *'redis_database_id' =>
         *string(1) "0"
         *'redis_password' =>
         *string(8) "foobared"
         *'create_time' =>
         *string(19) "2019-05-17 15:35:42"
         *'update_time' =>
         *string(19) "2019-05-17 18:22:18"
         * }
         */
        //未来拆表的话，修改这里的获取redis信息逻辑
        return $redisInfo;
    }

    public static function getConfDataByProjectKey($projectKey)
    {
        //TODO 在考虑是否加上验证表名是否存在  暂时不加
        CommonConfigData::setTableName($projectKey);
        $confData = CommonConfigData::find()->asArray()->all();
        return array_column($confData, 'value', 'key');
    }

    public static function setConfDataRedisInfo($redisInfo)
    {
        self::$redisConnection = \Yii::$app->redis;
        self::$redisConnection->hostname = $redisInfo['redis_host'];
        self::$redisConnection->port = $redisInfo['redis_port'];
        self::$redisConnection->database = $redisInfo['redis_database_id'];
        self::$redisConnection->password = $redisInfo['redis_password'];
    }

    public static function testConnect()
    {
        $testSetRe = self::$redisConnection->set('test', 'testConnection');
        if ($testSetRe) {
            $testDelRe = self::$redisConnection->del('test');
            return $testDelRe ? true : false;
        } else {
            //TODO 日志、邮件、等等通知
            return false;
        }
    }

    public static function setRedisValue($data, $projectKey)
    {
        foreach ($data as $key => $value) {
            $setRe = self::$redisConnection->set(static::getKeysRule($projectKey, $key), $value);
            if ($setRe == false) {
                //TODO 失败日志、邮件、等等通知
            }
            //TODO 成功日志、邮件、等等通知
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