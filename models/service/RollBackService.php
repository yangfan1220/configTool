<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-10
 * Time: 20:26
 */

namespace app\models\service;

use app\models\tables\ConfigDataReleaseHistory;
use app\models\Emum\ConfigDataReleaseHistoryEmum;
use yii\web\NotFoundHttpException;


class RollBackService
{
    private static function verifyIsCurrentVersion($targetRollbackVersion)
    {
        $currentReleaseData = static::getCurrentReleaseData();

        if ($currentReleaseData['release_name'] == $targetRollbackVersion) {
            throw new NotFoundHttpException('不允许回滚到当前版本');
        }
    }

    private static function getCurrentReleaseData()
    {
        return ConfigDataReleaseHistory::findBySql('SELECT `current_record_style`,`release_name` from `' . ConfigDataReleaseHistory::tableName() . '` WHERE `app_id`=:app_id ORDER BY `id` DESC LIMIT 1 FOR UPDATE;', [':app_id' => \Yii::$app->session['app_id']])->asArray()->one();
    }

    private static function insertConfigDataReleaseHistory($releaseName)
    {
        $model = new  ConfigDataReleaseHistory();
        $model->app_id = \Yii::$app->session['app_id'];
        $model->current_record_style = ConfigDataReleaseHistoryEmum::$currentRecordStyleRollback;
        $model->release_name = $releaseName;
        $model->create_name = \Yii::$app->session['userMail'];
        $model->save();
        if ($model->hasErrors()) {
            throw new \Exception(current($model->getFirstErrors()));
        }
    }

    public static function RollBack($targetRollbackVersion)
    {
        $transaction = ConfigDataReleaseHistory::getDb()->beginTransaction();
        try {
            static::verifyIsCurrentVersion($targetRollbackVersion);
            //添加发布的回滚记录
            static::insertConfigDataReleaseHistory($targetRollbackVersion);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new NotFoundHttpException($e->getMessage());
        }

    }
}