<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-10
 * Time: 20:26
 */

namespace app\models\service;

use app\models\tables\CommonConfigData;
use app\models\tables\ConfigDataReleaseHistory;
use app\models\tables\ConfigDataReleaseHistoryAllLog;
use app\models\tables\ProjectInfo;
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

    public static function RollBack($targetRollbackVersion)
    {
        $transaction = ConfigDataReleaseHistory::getDb()->beginTransaction();
        try {
            static::verifyIsCurrentVersion($targetRollbackVersion);
            /**添加发布的回滚记录**/
            static::updateCurrentReleaseStatusOfProjectInfo($targetRollbackVersion);
            //从日志记录表里获取老的数据
            $oldData=static::getOldDataFromRecordTable($targetRollbackVersion);
            //删除当前appid下的发布的数据，随后将老数据插入到新数据中
            static::deleteCurrentReleaseBaseConfigData();
            //将老的数据插入到当前的表中
            static::insertCommonConfigData($oldData);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new NotFoundHttpException($e->getMessage().$e->getFile().$e->getLine());
        }

    }

    private static function getOldDataFromRecordTable($targetRollbackVersion)
    {
        return ConfigDataReleaseHistoryAllLog::findBySql('select * from '.ConfigDataReleaseHistoryAllLog::tableName().' where `app_id`=:app_id and `release_name`=:release_name for update',[
            ':app_id'=>\Yii::$app->session['app_id'],
            ':release_name'=>$targetRollbackVersion,
        ])->asArray()->all();
    }

    private static function  deleteCurrentReleaseBaseConfigData()
    {
        CommonConfigData::deleteAll('app_id=:app_id',[':app_id'=>\Yii::$app->session['app_id']]);
    }

    private static function insertCommonConfigData($oldData)
    {
        $columns=[
            'app_id','config_level','key','value','comment','value_type','create_name','modify_name','create_time','update_time'
        ];
        $data=[];
        foreach ($oldData as $oldDatum) {
            $data[]=[
                \Yii::$app->session['app_id'],
                $oldDatum['config_level_log'],
                $oldDatum['key'],
                $oldDatum['value'],
                $oldDatum['comment_log'],
                $oldDatum['value_type_log'],
                $oldDatum['create_name_log'],
                $oldDatum['modify_name_log'],
                $oldDatum['create_time_log'],
                $oldDatum['update_time_log'],
            ];
        }
        CommonConfigData::getDb()->createCommand()->batchInsert(CommonConfigData::tableName(),$columns,$data)->execute();
    }

    private static function updateCurrentReleaseStatusOfProjectInfo($targetRollbackVersion)
    {
        ProjectInfo::updateAll(['release_status'=>1,'will_rollback_release_name'=>$targetRollbackVersion],'app_id=:app_id',[':app_id'=>\Yii::$app->session['app_id']]);
    }
}