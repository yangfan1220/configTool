<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-29
 * Time: 18:10
 */

namespace app\models\service;

use app\models\Emum\CommonConfigDataEmum;
use app\models\Emum\ConfigDataModifyLogEmum;
use app\models\tables\CommonConfigData;
use app\models\tables\ProjectInfo;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\base\DynamicModel;
use Yii;

class CommonConfigDataService
{
    private function rules()
    {
        return [
            [['config_level', 'value_type', 'key', 'value', 'comment'], 'required'],
            ['config_level', 'in', 'range' => CommonConfigDataEmum::$configLevel],
            ['value_type', 'in', 'range' => CommonConfigDataEmum::$valueType],
        ];
    }

    /**添加时验证
     *
     * @param $data
     * @return mixed|string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function validate($data)
    {
        $model = DynamicModel::validateData($data, $this->rules());
        if ($model->hasErrors()) {
            throw new NotFoundHttpException(current($model->getFirstErrors()));
        }
        if ($data['config_level'] == CommonConfigDataEmum::$configLevel['public']) {
            if (empty($data['allowAppIds'])) {
                throw new NotFoundHttpException('接受配置的APPID 不能为空');
            }
        }
        static::verifyValueTypeIsJson($data['value_type'], $data['value']);
    }

    /**
     * 更新时验证
     * @param $data
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function validateForUpdate($data)
    {
        $model = DynamicModel::validateData($data, $this->rules());
        if ($model->hasErrors()) {
            throw new NotFoundHttpException(current($model->getFirstErrors()));
        }
        static::verifyValueTypeIsJson($data['value_type'], $data['value']);
    }

    /**
     * @param $valueType
     * @param $configData
     * @throws NotFoundHttpException
     */
    private static function verifyValueTypeIsJson($valueType, $configData)
    {
        if ($valueType == CommonConfigDataEmum::$valueType['Json']) {
            try {
                Json::decode($configData);
            } catch (\Exception $e) {
                throw new NotFoundHttpException($e->getMessage() . '   Json数据格式错误');
            }
        }
    }


    /********************************************上面方法为验证****************************************************************/

    /**
     * 正常渲染页面时
     * @return array
     */
    public function getProjectInfo()
    {
        $projectInfo = ProjectInfo::find()->select(['app_id', 'app_name'])->asArray()->all();
        $projectInfoArray = array_column($projectInfo, 'app_name', 'app_id');
        array_walk($projectInfoArray, function (&$value, $key) {
            $value = $key . ' | ' . $value;
        });
        return $projectInfoArray;
    }

    /**
     * 插入时的逻辑
     * @param $data
     * @throws NotFoundHttpException
     */
    public function insertOperation($data)
    {
        try {
            if ($data['config_level'] == CommonConfigDataEmum::$configLevel['public']) {
                static::saveForPublic($data);
            } else {
                static::saveForPrivate($data);
            }
        } catch (\Exception $e) {
            throw new NotFoundHttpException($e->getMessage() . $e->getTraceAsString());
        }
    }

    private static function saveForPublic($data)
    {
        $configDataStorageTransaction = CommonConfigData::getDb()->beginTransaction();
        try {
            foreach ($data['allowAppIds'] as $allowAppId) {
                CommonConfigDataDBService::insertCommonConfigDataModel($data, $allowAppId);
                CommonConfigDataDBService::insertConfigDataModifyLogModel($data, $allowAppId, ConfigDataModifyLogEmum::$modifyTypeAdd);
            }
            $configDataStorageTransaction->commit();
        } catch (\Exception $e) {
            $configDataStorageTransaction->rollBack();
            throw $e;
        }
    }

    private static function saveForPrivate($data)
    {
        $configDataStorageTransaction = CommonConfigData::getDb()->beginTransaction();
        try {
            CommonConfigDataDBService::insertCommonConfigDataModel($data, Yii::$app->session['app_id']);
            CommonConfigDataDBService::insertConfigDataModifyLogModel($data, Yii::$app->session['app_id'], ConfigDataModifyLogEmum::$modifyTypeAdd);
            $configDataStorageTransaction->commit();
        } catch (\Exception $e) {
            $configDataStorageTransaction->rollBack();
            throw $e;
        }
    }

    /**
     * 更新时逻辑
     * @param $id
     * @param $data
     * @throws \Throwable
     */
    public static function updateOperation($id, $data)
    {
        $willInsertData = $data['CommonConfigData'];
        if ($data['CommonConfigData']['config_level'] == CommonConfigDataEmum::$configLevel['private']) {
            $transaction = CommonConfigData::getDb()->beginTransaction();
            try {
                $oldAttributeOfValue = CommonConfigDataDBService::updateCommonConfigDataModel($data, $id);
                if ($oldAttributeOfValue != $willInsertData['value']) {
                    CommonConfigDataDBService::insertConfigDataModifyLogModel($willInsertData, Yii::$app->session['app_id'], ConfigDataModifyLogEmum::$modifyTypeModify, $oldAttributeOfValue);
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            return;
        } elseif ($data['CommonConfigData']['config_level'] == CommonConfigDataEmum::$configLevel['public']) {
            $transaction = CommonConfigData::getDb()->beginTransaction();
            try {
                $oldAttributeOfValue='';

                $commonConfigDataModels = CommonConfigData::find()->where(['key' => $willInsertData['key']])->all();
                foreach ($commonConfigDataModels as $commonConfigDataModel) {
                    $commonConfigDataModel->value = $willInsertData['value'];
                    $commonConfigDataModel->comment = $willInsertData['comment'];
                    $commonConfigDataModel->value_type = $willInsertData['value_type'];
                    empty($oldAttributeOfValue) && $oldAttributeOfValue=$commonConfigDataModel->getOldAttribute('value');
                    $commonConfigDataModel->update();
                    if ($commonConfigDataModel->hasErrors()){
                        throw new \Exception(current($commonConfigDataModel->getFirstErrors()));
                    }

                    if ($oldAttributeOfValue != $willInsertData['value']) {
                        CommonConfigDataDBService::insertConfigDataModifyLogModel($willInsertData, $commonConfigDataModel->app_id, ConfigDataModifyLogEmum::$modifyTypeModify, $oldAttributeOfValue);
                    }
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            return ;
        }
        throw new NotFoundHttpException('config_level is wrong');
    }
}