<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-06
 * Time: 15:33
 */

namespace app\models\service;


use app\models\Helper;
use app\models\tables\CommonConfigData;
use app\models\tables\ConfigDataReleaseHistory;
use app\models\Emum\ConfigDataModifyLogEmum;
use yii\base\DynamicModel;
use yii\web\NotFoundHttpException;
use app\models\tables\CommonDataStorage;
use app\models\common\SetValueOfCommonModel;

class ReleaseService
{
    private static function getModifyName($compareType, $data)
    {
        if ($compareType == 1) {
            $modifyName = !empty($data['modify_name']) ? $data['modify_name'] : $data['create_name'];
        } elseif ($compareType == 2) {
            $modifyName = !empty($data['modify_name_log']) ? $data['modify_name_log'] : $data['create_name_log'];
        } else {
            throw new NotFoundHttpException('参数错误');
        }
        return $modifyName;
    }

    private static function getCurrentBaseConfigDataByAppId($appId)
    {
        $sql = 'SELECT `key`, `value`, `create_name`, `modify_name`, `update_time` FROM ' . CommonConfigData::tableName() . ' where `app_id`=:app_id for update';
        return CommonConfigData::findBySql($sql, [':app_id' => $appId])->asArray()->all();
    }

    public static function getCurrentAlreadyReleaseConfigDataByAppId($appId)
    {
        $sql = 'SELECT b.* FROM project_info a
        LEFT JOIN  config_data_release_history_all_log b
        ON a.current_released_unique_id=b.unique_id
        WHERE a.app_id=:app_id FOR UPDATE';
        $data = ConfigDataReleaseHistory::findBySql($sql, [':app_id' => $appId])->asArray()->all();
        return array_column($data, null, 'key');
    }

    public static function compare($baseArr, $alreadyArr, $compareType = 1)
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
                    $compareResultArr[$val['key']]['modifyName'] = static::getModifyName($compareType, $val);
                    $compareResultArr[$val['key']]['updateTime'] = $compareType == 1 ? $val['update_time'] : $val['update_time_log'];
                    unset($alreadyArr[$val['key']]);
                }
            } else {
                //如果原来的数组里没有   但是基本的有    证明是新增的
                $compareResultArr[$val['key']]['status'] = ConfigDataModifyLogEmum::$modifyTypeAdd;
                $compareResultArr[$val['key']]['alreadyRelease'] = '';
                $compareResultArr[$val['key']]['notRelease'] = $val['value'];
                $compareResultArr[$val['key']]['modifyName'] = static::getModifyName($compareType, $val);
                $compareResultArr[$val['key']]['updateTime'] = $compareType == 1 ? $val['update_time'] : $val['update_time_log'];
            }
        }

        //两者unset后剩下的就是删除的
        foreach ($alreadyArr as $k => $v) {
            $compareResultArr[$k]['status'] = ConfigDataModifyLogEmum::$modifyTypeDel;
            $compareResultArr[$k]['alreadyRelease'] = $v['value'];
            $compareResultArr[$k]['notRelease'] = '';
            $compareResultArr[$k]['modifyName'] = $v['modify_name_log'];
            $compareResultArr[$k]['updateTime'] = $v['update_time_log'];
        }

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
        $transaction = ConfigDataReleaseHistory::getDb()->beginTransaction();
        $modifyDataLog = static::getReleaseChanges();//获取当前的与已经发布的配置的变化
        try {
            $uniqueId = Helper::getCode();
            ReleaseDBService::updateProjectInfo($uniqueId); //更新当前的发布版本
            ReleaseDBService::insertConfigDataReleaseHistory($data, $uniqueId);//插入发布的历史记录
            ReleaseDBService::insertConfigDataReleaseHistoryModifyLog($modifyDataLog, $data['releaseName'], $uniqueId);//发布的历史记录数据的修改记录(与已经发布的配置的变化)
            //获取全部的配置信息
            $configDataAll = ReleaseDBService::selectAllConfigData();
            ReleaseDBService::insertConfigDataReleaseHistoryAllLog($configDataAll, $data['releaseName'], $uniqueId);


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
        ReleaseDBService::saveToRedis($configDataAll);
    }
}