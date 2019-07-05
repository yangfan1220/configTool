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
use yii\base\DynamicModel;
use yii\web\NotFoundHttpException;
use app\models\tables\CommonDataStorage;

class ReleaseService
{
    private static function getCurrentBaseConfigDataByAppId($appId)
    {
        return CommonConfigData::find()->select(['key', 'value','create_name','modify_name','update_time'])->where(['app_id' => $appId])->asArray()->all();
    }

    private static function getCurrentAlreadyReleaseConfigDataByAppId($appId)
    {
        $data= ConfigDataReleaseHistory::findBySql('SELECT `key`,`value` FROM `config_data_release_history_all_log` WHERE `app_id`=:app_id AND `release_name`=(SELECT `release_name` FROM `config_data_release_history` WHERE `app_id`=:app_id ORDER BY `id`
 DESC LIMIT 1)',[':app_id'=>$appId])->asArray()->all();
        return array_column($data, 'value', 'key');
    }

    private static function compare($baseArr, $alreadyArr)
    {
        $compareResultArr = [];
        foreach ($baseArr as  $val) {
            if (isset($alreadyArr[$val['key']])) {
                if ($val['value'] == $alreadyArr[$val['key']]) {
                    unset($alreadyArr[$val['key']]);
                } else {
                    $compareResultArr[$val['key']]['status'] = ConfigDataModifyLogEmum::$modifyTypeModify;
                    $compareResultArr[$val['key']]['alreadyRelease'] = $alreadyArr[$val['key']];
                    $compareResultArr[$val['key']]['notRelease'] = $val['value'];
                    $compareResultArr[$val['key']]['modifyName'] = !empty($val['modify_name'])?$val['modify_name']:$val['create_name'];
                    $compareResultArr[$val['key']]['updateTime'] = $val['update_time'];
                    unset($alreadyArr[$val['key']]);
                }
            } else {
                $compareResultArr[$val['key']]['status'] = ConfigDataModifyLogEmum::$modifyTypeAdd;
                $compareResultArr[$val['key']]['alreadyRelease'] ='';
                $compareResultArr[$val['key']]['notRelease'] =$val['value'];
                $compareResultArr[$val['key']]['modifyName'] =!empty($val['modify_name'])?$val['modify_name']:$val['create_name'];
                $compareResultArr[$val['key']]['updateTime'] =$val['update_time'];
            }
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
        $model=DynamicModel::validateData($data,[
            [['releaseName','releaseComment'],'required'],
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
        $modifyDataLog=static::getReleaseChanges();
        $transaction=ConfigDataReleaseHistory::getDb()->beginTransaction();
        try{
            ReleaseDBService::insertConfigDataReleaseHistory($data);
            ReleaseDBService::insertConfigDataReleaseHistoryModifyLog($modifyDataLog,$data['releaseName']);
            //获取全部的配置信息
            $configDataAll=ReleaseDBService::selectAllConfigData();
            ReleaseDBService::insertConfigDataReleaseHistoryAllLog($configDataAll,$data['releaseName']);

            $commonDataStorageTransaction=CommonDataStorage::getDb()->beginTransaction();

            try{
                ReleaseDBService::insertDataStorage($configDataAll);


                $commonDataStorageTransaction->commit();
            }catch (\Exception $e){
                $commonDataStorageTransaction->rollBack();
                throw $e;
            }

            $transaction->commit();
        }catch (\Exception $e){
            $transaction->rollBack();
            throw new NotFoundHttpException($e->getMessage());
        }
        ReleaseDBService::saveToRedis($configDataAll);
    }
}