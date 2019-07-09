<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-06
 * Time: 15:33
 */

namespace app\models\service;


use app\models\tables\CommonConfigData;
use app\models\tables\ConfigDataReleaseHistory;
use app\models\Emum\ConfigDataModifyLogEmum;
use app\models\tables\ProjectInfo;
use yii\base\DynamicModel;
use yii\web\NotFoundHttpException;
use app\models\tables\CommonDataStorage;
use app\models\common\SetValueOfCommonModel;
use app\models\Emum\ConfigDataReleaseHistoryEmum;

class ReleaseService
{
    private static function getCurrentBaseConfigDataByAppId($appId)
    {
        return CommonConfigData::find()->select(['key', 'value', 'create_name', 'modify_name', 'update_time'])->where(['app_id' => $appId])->asArray()->all();
    }

    private static function getCurrentAlreadyReleaseConfigDataByAppId($appId)
    {
        $data = ConfigDataReleaseHistory::findBySql('SELECT * FROM `config_data_release_history_all_log` WHERE `app_id`=:app_id AND `release_name`=(SELECT `release_name` FROM `config_data_release_history` WHERE `app_id`=:app_id ORDER BY `id`
 DESC LIMIT 1)', [':app_id' => $appId])->asArray()->all();
        return array_column($data, null, 'key');
    }

    private static function compare($baseArr, $alreadyArr)
    {
        $compareResultArr = [];
        foreach ($baseArr as $val) {
            if (isset($alreadyArr[$val['key']]['value'])) {
                if ($val['value'] == $alreadyArr[$val['key']]['value']) {
                    //key  一样  value 一样    证明没有改变
                    unset($alreadyArr[$val['key']]);
                } else {
                    //key  一样  value 不一样    证明修改
                    $compareResultArr[$val['key']]['status'] = ConfigDataModifyLogEmum::$modifyTypeModify;
                    $compareResultArr[$val['key']]['alreadyRelease'] = $alreadyArr[$val['key']]['value'];
                    $compareResultArr[$val['key']]['notRelease'] = $val['value'];
                    $compareResultArr[$val['key']]['modifyName'] = !empty($val['modify_name']) ? $val['modify_name'] : $val['create_name'];
                    $compareResultArr[$val['key']]['updateTime'] = $val['update_time'];
                    unset($alreadyArr[$val['key']]);
                }
            } else {
                //如果原来的数组里没有   但是基本的有    证明是新增的
                $compareResultArr[$val['key']]['status'] = ConfigDataModifyLogEmum::$modifyTypeAdd;
                $compareResultArr[$val['key']]['alreadyRelease'] = '';
                $compareResultArr[$val['key']]['notRelease'] = $val['value'];
                $compareResultArr[$val['key']]['modifyName'] = !empty($val['modify_name']) ? $val['modify_name'] : $val['create_name'];
                $compareResultArr[$val['key']]['updateTime'] = $val['update_time'];
            }
        }

        foreach ($alreadyArr as $k=>$v){
            $compareResultArr[$k]['status'] = ConfigDataModifyLogEmum::$modifyTypeDel;
            $compareResultArr[$k]['alreadyRelease'] = $v['value'];
            $compareResultArr[$k]['notRelease'] = '';
            $compareResultArr[$k]['modifyName'] = $v['modify_name_log'];
            $compareResultArr[$k]['updateTime'] = $v['update_time_log'];
        }

        //组装将要删除的键名  暂时没有   设计
//        $displayDeleteKeys = array_keys($alreadyArr);
//        $value = array_fill(0, count($displayDeleteKeys), ConfigDataModifyLogEmum::$modifyTypeDel);
//        $willDel=array_combine($displayDeleteKeys, $value);
        return $compareResultArr;
    }

    public static function getReleaseChanges()
    {
        $appId = \Yii::$app->session['app_id'];
        $currentBaseConfigData = static::getCurrentBaseConfigDataByAppId($appId);
        $currentAlreadyReleaseConfigData = static::getCurrentAlreadyReleaseConfigDataByAppId($appId);
        return static::compare($currentBaseConfigData, $currentAlreadyReleaseConfigData);
    }

    public static function releaseValidate($data)
    {
        $model = DynamicModel::validateData($data, [
            [['releaseName', 'releaseComment'], 'required'],
        ]);
        if ($model->hasErrors()) {
            throw new NotFoundHttpException(current($model->getFirstErrors()));
        }
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public static function Release($data)
    {
        $projectInfoObj = ProjectInfo::findOne(['app_id' => \Yii::$app->session['app_id']]);
        if ($projectInfoObj->release_status == 1) {
            $configDataAll = static::releaseForRollBack($projectInfoObj->will_rollback_release_name);
        } else {
            $modifyDataLog = static::getReleaseChanges();
            $transaction = ConfigDataReleaseHistory::getDb()->beginTransaction();
            try {
                ReleaseDBService::insertConfigDataReleaseHistory($data);
                ReleaseDBService::insertConfigDataReleaseHistoryModifyLog($modifyDataLog, $data['releaseName']);
                //获取全部的配置信息
                $configDataAll = ReleaseDBService::selectAllConfigData();
                ReleaseDBService::insertConfigDataReleaseHistoryAllLog($configDataAll, $data['releaseName']);
                ReleaseDBService::updateProjectInfo();

                $commonDataStorageTransaction = CommonDataStorage::getDb()->beginTransaction();

                try {
                    //获取表名 没有就生成
                    $tableName = SetValueOfCommonModel::joinDataStorageTableName(\Yii::$app->session['app_id']);
                    SetValueOfCommonModel::TheTableExist($tableName);
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
                $message = $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine();
                throw new NotFoundHttpException($message);
            }
        }
        ReleaseDBService::saveToRedis($configDataAll);
    }

    private static function releaseForNormal()
    {

    }

    private static function releaseForRollBack($willRollbackReleaseName)
    {
        $transaction = ConfigDataReleaseHistory::getDb()->beginTransaction();
        try {
            ReleaseDBService::insertConfigDataReleaseHistory(['releaseName' => $willRollbackReleaseName, 'releaseComment' => '回滚'], ConfigDataReleaseHistoryEmum::$currentRecordStyleRollback);
            $configDataAll = ReleaseDBService::selectAllConfigData();
            ReleaseDBService::updateProjectInfo();

            /***嵌套事务开启***/
            $commonDataStorageTransaction = CommonDataStorage::getDb()->beginTransaction();
            try {
                //获取表名 没有就生成
                $tableName = SetValueOfCommonModel::joinDataStorageTableName(\Yii::$app->session['app_id']);
                SetValueOfCommonModel::TheTableExist($tableName);
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
            $message = $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine();
            throw new NotFoundHttpException($message);
        }
        return $configDataAll;
    }
}