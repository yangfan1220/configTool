<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-05
 * Time: 18:55
 */

namespace app\models\service;

use app\models\tables\CommonConfigData;
use Yii;
use app\models\tables\ConfigDataModifyLog;


class CommonConfigDataDBService
{
    public static function insertCommonConfigDataModel($data,$addId)
    {
        $CommonConfigDataModel = new CommonConfigData;
        $CommonConfigDataModel->app_id = $addId;
        $CommonConfigDataModel->config_level = $data['config_level'];
        $CommonConfigDataModel->key = $data['key'];
        $CommonConfigDataModel->value = $data['value'];
        $CommonConfigDataModel->comment = $data['comment'];
        $CommonConfigDataModel->value_type = $data['value_type'];
        $CommonConfigDataModel->create_name = Yii::$app->session['userMail'];
        $CommonConfigDataModel->save();
        $errorMsg = current($CommonConfigDataModel->getFirstErrors());
        if (!empty($errorMsg)) {
            throw new \Exception($errorMsg);
        }
    }

    public static function insertConfigDataModifyLogModel($data,$addId,$modifyType,$oldValue='')
    {
        $configDataModifyLog = new ConfigDataModifyLog();
        $configDataModifyLog->modify_type = $modifyType;
        $configDataModifyLog->app_id = $addId;
        $configDataModifyLog->key = $data['key'];
        $configDataModifyLog->old_value = $oldValue;
        $configDataModifyLog->new_value = $data['value'];
        $configDataModifyLog->comment = $data['comment'];
        $configDataModifyLog->create_name = Yii::$app->session['userMail'];
        $configDataModifyLog->save();
        $errorMsg = current($configDataModifyLog->getFirstErrors());
        if (!empty($errorMsg)) {
            throw new \Exception($errorMsg);
        }
    }

    public static function updateCommonConfigDataModel($data,$id)
    {
        $commonConfigDataModel = CommonConfigData::findOne(['id' => $id]);
        $commonConfigDataModel->load($data);
        $commonConfigDataModel->modify_name = Yii::$app->session['userMail'];
        $oldAttributeOfValue = $commonConfigDataModel->getOldAttribute('value');
        $commonConfigDataModel->save();
        $errorMsg = current($commonConfigDataModel->getFirstErrors());
        if (!empty($errorMsg)) {
            throw new \Exception($errorMsg);
        }
        return $oldAttributeOfValue;
    }
}