<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-10
 * Time: 20:26
 */

namespace app\models\service;

use app\models\tables\CommonDataStorage;
use app\models\tables\ConfigDataReleaseHistory;
use app\models\tables\ConfigDataReleaseHistoryAllLog;
use app\models\tables\ProjectInfo;
use yii\web\NotFoundHttpException;
use app\models\Helper;
use app\models\common\SetValueOfCommonModel;


class RollBackService
{
    private static function getTargetRollbackConfigData($targetRollbackVersionUniqueId)
    {
        $sql = 'select * from ' . ConfigDataReleaseHistoryAllLog::tableName() . ' where unique_id=:unique_id';
        return ConfigDataReleaseHistoryAllLog::findBySql($sql, [':unique_id' => $targetRollbackVersionUniqueId])->asArray()->all();
    }

    private static function getRollBackChanges($targetRollbackVersionUniqueId)
    {
        $appId = \Yii::$app->session['app_id'];
        $currentBaseConfigData = static::getTargetRollbackConfigData($targetRollbackVersionUniqueId);
        $currentAlreadyReleaseConfigData = ReleaseService::getCurrentAlreadyReleaseConfigDataByAppId($appId);
        $compareResult = ReleaseService::compare($currentBaseConfigData, $currentAlreadyReleaseConfigData, 2);
        return [$currentBaseConfigData, $compareResult];
    }

    private static function verifyIsCurrentVersion($targetRollbackVersion)
    {
        $projectInfo = ProjectInfo::findOne(['app_id' => \Yii::$app->session['app_id']]);
        if (empty($projectInfo->current_released_unique_id)) {
            throw new NotFoundHttpException('能不能回滚自己心里没数么？你刚发布一个版本，想要回滚，我给你把数据清空了么？');
        }
        if ($projectInfo->current_released_unique_id == $targetRollbackVersion) {
            throw new NotFoundHttpException('不允许回滚到当前版本');
        }
    }

    public static function RollBack($targetRollbackVersionUniqueId, $targetRollbackVersionReleaseName)
    {
        $transaction = ConfigDataReleaseHistory::getDb()->beginTransaction();
        try {
            static::verifyIsCurrentVersion($targetRollbackVersionUniqueId);
            $uniqueId = Helper::getCode();
            /**添加发布的回滚记录**/
            //从日志记录表里获取老的数据
//            $oldData = static::getOldDataFromRecordTable($targetRollbackVersionUniqueId);
            [$configDataAll, $modifyDataLog] = static::getRollBackChanges($targetRollbackVersionUniqueId);

            //1、插入配置数据发布历史表
            ReleaseDBService::insertConfigDataReleaseHistory(['releaseName' => $targetRollbackVersionReleaseName, 'releaseComment' => '回滚'], $uniqueId, 2);
            //2、插入发布的配置数据变更的表
            ReleaseDBService::insertConfigDataReleaseHistoryModifyLog($modifyDataLog, $targetRollbackVersionReleaseName, $uniqueId);
            //3、插入当前回滚的全部记录
            ReleaseDBService::insertConfigDataReleaseHistoryAllLog($configDataAll, $targetRollbackVersionReleaseName, $uniqueId);
            //4、更新当前的回滚的唯一id
            ReleaseDBService::updateProjectInfo($uniqueId);

            $commonDataStorageTransaction = CommonDataStorage::getDb()->beginTransaction();
            try {
                //获取表名 没有就生成
                $tableName = SetValueOfCommonModel::joinDataStorageTableName(\Yii::$app->session['app_id']);
                ReleaseDBService::deleteDataStorage($tableName);
                ReleaseDBService::insertDataStorage($configDataAll, $tableName);
                $commonDataStorageTransaction->commit();
            } catch (\Exception $e) {
                $commonDataStorageTransaction->rollBack();
                throw $e;
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new NotFoundHttpException($e->getMessage() . $e->getFile() . $e->getLine());
        }

    }

    private static function getOldDataFromRecordTable($targetRollbackVersionUniqueId)
    {
        return ConfigDataReleaseHistoryAllLog::findBySql('select * from ' . ConfigDataReleaseHistoryAllLog::tableName() . ' where `app_id`=:app_id and `unique_id`=:unique_id for update', [
            ':app_id'    => \Yii::$app->session['app_id'],
            ':unique_id' => $targetRollbackVersionUniqueId,
        ])->asArray()->all();
    }

//    private static function insertCommonConfigData($oldData)
//    {
//        $columns = [
//            'app_id', 'config_level', 'key', 'value', 'comment', 'value_type', 'create_name', 'modify_name', 'create_time', 'update_time'
//        ];
//        $data = [];
//        foreach ($oldData as $oldDatum) {
//            $data[] = [
//                \Yii::$app->session['app_id'],
//                $oldDatum['config_level_log'],
//                $oldDatum['key'],
//                $oldDatum['value'],
//                $oldDatum['comment_log'],
//                $oldDatum['value_type_log'],
//                $oldDatum['create_name_log'],
//                $oldDatum['modify_name_log'],
//                $oldDatum['create_time_log'],
//                $oldDatum['update_time_log'],
//            ];
//        }
//        CommonConfigData::getDb()->createCommand()->batchInsert(CommonConfigData::tableName(), $columns, $data)->execute();
//    }
}