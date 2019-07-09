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
use app\models\common\SetValueOfCommonModel;
use yii\db\Connection;

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
        $allProjectInfo = ProjectInfo::find()->select(['app_id', 'release_status'])->asArray()->all();
        self::$currentProject = $allProjectInfo;
        return array_column($allProjectInfo, 'release_status','app_id');
    }

    public static function getRedisInfoByProjectKey($projectAppId)
    {
        $redisInfo = ProjectRedisInfo::find()->where(['project_app_id' => $projectAppId])->asArray()->one();
        //未来拆表的话，修改这里的获取redis信息逻辑
        return $redisInfo;
    }

    public static function getConfDataByProjectKey($projectKey)
    {
        $tableName = SetValueOfCommonModel::joinDataStorageTableName($projectKey);
        CommonDataStorage::setTableName($tableName);
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
            try {
                $setRe = self::$redisConnection->set(static::getKeysRule($projectKey, $key), $value, 'ex', '3600');
                if ($setRe == false) {
                    throw new \Exception('SetValue::setRedisValue() 推送数据到redis失败');
                }
            } catch (\Exception $e) {
                MailMessageStruct::unshiftMailMessage($e->getMessage());
                MailMessageStruct::pushMailMessage('当前key为' . $key . '   ' . 'value为' . $value);
                continue;
            }
        }

        if (!empty(MailMessageStruct::$mailMessages)) {
            MailMessageStruct::unshiftMailMessage('当前app_id: ' . \Yii::$app->session['app_id']);
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

//    public static function theTableIsExist($projectKey)
//    {
//        $tableName = SetValueOfCommonModel::joinDataStorageTableName($projectKey);
//        $dbName = static::getCurrentConnectionDataBaseName(\Yii::$app->db2);
//        if (empty($dbName)) {
//            return false;
//        }
//        $tableCount = \Yii::$app->db2
//            ->createCommand('SELECT count(*) as c  FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA`=:TABLE_SCHEMA AND `TABLE_NAME`=:TABLE_NAME', [
//                ':TABLE_SCHEMA' => $dbName,
//                ':TABLE_NAME'   => $tableName,
//            ])
//            ->queryScalar();
//        return $tableCount == 1 ? true : false;
//    }
//
//    private static function getCurrentConnectionDataBaseName(Connection $db)
//    {
//        $isMatch = preg_match_all('/(dbname=)([\S]*)(\$||;)/', $db->dsn, $matches);
//        if ($isMatch) {
//            return empty($matches[2]) ? false : $matches[2][0];
//        }
//        return false;
//    }
}