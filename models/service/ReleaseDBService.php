<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-08
 * Time: 11:56
 */

namespace app\models\service;


use app\models\Emum\ConfigDataReleaseHistoryEmum;
use app\models\tables\CommonConfigData;
use app\models\tables\ConfigDataReleaseHistory;
use app\models\tables\ConfigDataReleaseHistoryAllLog;
use app\models\tables\ConfigDataReleaseHistoryModifyLog;

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
}