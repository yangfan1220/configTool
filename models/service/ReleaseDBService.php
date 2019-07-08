<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-08
 * Time: 11:56
 */

namespace app\models\service;


use app\models\Emum\ConfigDataReleaseHistoryEmum;
use app\models\Mail\MailMessageStruct;
use app\models\Mail\SendMail;
use app\models\tables\CommonConfigData;
use app\models\tables\ConfigDataReleaseHistory;
use app\models\tables\ConfigDataReleaseHistoryAllLog;
use app\models\tables\ConfigDataReleaseHistoryModifyLog;
use app\models\SetValue;
use yii\db\Exception;

class ReleaseDBService
{
    public static function insertConfigDataReleaseHistory($data)
    {
        $model = new  ConfigDataReleaseHistory();
        $model->app_id = \Yii::$app->session['app_id'];
        $model->current_record_style = ConfigDataReleaseHistoryEmum::$currentRecordStyleRelease;
        $model->release_name = $data['releaseName'];
        $model->comment = $data['releaseComment'];
        $model->create_name = \Yii::$app->session['userMail'];
        $model->save();
        if($model->hasErrors()){
            throw new \Exception(current($model->getFirstErrors()));
        }
    }

    public static function insertConfigDataReleaseHistoryModifyLog($modifyDataLog,$releaseName)
    {

        foreach ($modifyDataLog as $key=>$keyAttr){
            $model=new ConfigDataReleaseHistoryModifyLog();
            $model->app_id=\Yii::$app->session['app_id'];
            $model->release_name=$releaseName;
            $model->modify_type=$keyAttr['status'];
            $model->key=(string)$key;
            $model->old_value=$keyAttr['alreadyRelease'];
            $model->new_value=$keyAttr['notRelease'];
            $model->save();
            if($model->hasErrors()){
                throw new \Exception(current($model->getFirstErrors()));
            }
        }
    }

    public static function selectAllConfigData()
    {
        $sql='select `key`,`value` from '.CommonConfigData::tableName().' where app_id=:app_id for update ';
        $configDataAll=CommonConfigData::getDb()->createCommand($sql,[':app_id'=>\Yii::$app->session['app_id']])->queryAll();
        return $configDataAll;
    }

    public static function insertConfigDataReleaseHistoryAllLog($configDataAll,$releaseName)
    {
        foreach ($configDataAll as $keysInfo){
            $model=new ConfigDataReleaseHistoryAllLog();
            $model->app_id=\Yii::$app->session['app_id'];
            $model->release_name=$releaseName;
            $model->key=$keysInfo['key'];
            $model->value=$keysInfo['value'];
            $model->save();
            if($model->hasErrors()){
                throw new \Exception(current($model->getFirstErrors()));
            }
        }

    }

    public static function insertDataStorage($configDataAll,$tableName)
    {
        $rows=[];
        foreach ($configDataAll as $keysInfo) {
            $rows[]=[
                $keysInfo['key'],
                $keysInfo['value'],
            ];
        }
        return \Yii::$app->db2->createCommand()->batchInsert($tableName,['key','value'],$rows)->execute();
    }

    public static function deleteDataStorage($tableName)
    {
        return \Yii::$app->db2->createCommand('DELETE FROM '.$tableName)->execute();
    }

    public static function saveToRedis($configDataAll)
    {
        try{
            $appId=\Yii::$app->session['app_id'];
            //获取redis信息
            $redisInfo=SetValue::getRedisInfoByProjectKey($appId);
            SetValue::setConfDataRedisInfo($redisInfo);
            //连接测试
            $testConnectRe = SetValue::testConnect();
            if ($testConnectRe==false) {
                throw new Exception('SetValue::testConnect() 设置OR连接失败');
            }

            foreach ($configDataAll as $keysInfo ) {
                $setRe = SetValue::$redisConnection->set(SetValue::getKeysRule($appId, $keysInfo['key']), $keysInfo['value'], 'ex', '3600');
                if ($setRe == false) {
                    MailMessageStruct::pushMailMessage($keysInfo['key'].'的值'.$keysInfo['value'].'设置失败');
                }
            }
        }catch (\Exception $e){
            MailMessageStruct::unshiftMailMessage($e->getMessage());
            MailMessageStruct::unshiftMailMessage('当前app_id: '.\Yii::$app->session['app_id']);
            SendMail::send();
        }
    }
}