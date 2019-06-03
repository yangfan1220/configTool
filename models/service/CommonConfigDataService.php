<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-29
 * Time: 18:10
 */

namespace app\models\service;


use app\models\common\TableConfirm;
use app\models\Emum\CommonConfigDataEmum;
use app\models\tables\AppIdKeyRelation;
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

    public function validate($data)
    {
        $model = DynamicModel::validateData($data, $this->rules());
        if ($model->hasErrors()) {
            return current($model->getFirstErrors());
        }
        if ($data['config_level'] == CommonConfigDataEmum::$configLevel['public']) {
            if (empty($data['allowAppIds'])) {
                return '接受配置的APPID 不能为空';
            }
        }
        static::verifyValueTypeIsJson($data['value_type'], $data['value']);
    }

    public function validateForUpdate($data)
    {
        $model = DynamicModel::validateData($data, $this->rules());
        if ($model->hasErrors()) {
            return current($model->getFirstErrors());
        }
        static::verifyValueTypeIsJson($data['value_type'], $data['value']);
    }


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

    public static function generateUniqueMD5($configKey, $configData)
    {
        return md5($configKey . $configData . microtime());
    }

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

    public function saveOperation($data)
    {
        $md5 = static::generateUniqueMD5($data['key'], $data['value']);
        if ($data['config_level'] == CommonConfigDataEmum::$configLevel['public']) {
            try {
                static::saveForPublic($data, $md5);
            } catch (\Exception $e) {
                throw new NotFoundHttpException($e->getTraceAsString());
            }
        } else {
            try{
            static::saveForPrivate($data,$md5);
            }catch (\Exception $e){
                throw new NotFoundHttpException($e->getTraceAsString());
            }
        }
    }

    private static function getConfigDataStorageData($data, $md5)
    {
        return [
            'key_value_mictime_md5' => $md5,
            'config_level'          => $data['config_level'],
            'key'                   => $data['key'],
            'value'                 => $data['value'],
            'comment'               => $data['comment'],
            'value_type'            => $data['value_type'],
            'create_name'           => Yii::$app->session['userMail'],
        ];
    }

    private static function getInsertAppIdKeyRelationData($data, $md5)
    {
        $willInsertData = [];
        foreach ($data['allowAppIds'] as $allowAppId) {
            $willInsertData[] = [$md5, $data['key'], $allowAppId];
        }
        return $willInsertData;
    }


    private static function saveForPublic($data, $md5)
    {
        $configDataStorageDb = CommonConfigData::getDb();
        $configDataStorageTransaction = $configDataStorageDb->beginTransaction();
        try {
            //configDataStorage insert data
            $configDataStorageData = static::getConfigDataStorageData($data, $md5);
            foreach ($data['allowAppIds'] as $allowAppId) {
                $tableName = TableConfirm::willCreateTableName($allowAppId);
                $configDataStorageDb->createCommand()->insert($tableName, $configDataStorageData)->execute();
            }

            $appIdKeyRelationDb = AppIdKeyRelation::getDb();
            $appIdKeyRelationDbTransaction = $appIdKeyRelationDb->beginTransaction();

            try {
                $willInsertAppIdKeyRelationData = static::getInsertAppIdKeyRelationData($data, $md5);
                $appIdKeyRelationDb->createCommand()->batchInsert(AppIdKeyRelation::tableName(), ['key_value_mictime_md5', 'key', 'app_id'], $willInsertAppIdKeyRelationData)->execute();


                $appIdKeyRelationDbTransaction->commit();
            } catch (\Exception $e) {
                $appIdKeyRelationDbTransaction->rollBack();
                throw $e;
            }
            $configDataStorageTransaction->commit();
        } catch (\Exception $e) {
            $configDataStorageTransaction->rollBack();
            throw $e;
        }
    }

    private static function saveForPrivate($data, $md5)
    {
        $configDataStorageDb = CommonConfigData::getDb();
        $configDataStorageTransaction = $configDataStorageDb->beginTransaction();
        try{
            $tableName = TableConfirm::willCreateTableName(Yii::$app->session['app_id']);
            $configDataStorageData = static::getConfigDataStorageData($data, $md5);
            $configDataStorageDb->createCommand()->insert($tableName, $configDataStorageData)->execute();
            $configDataStorageTransaction->commit();
        }catch (\Exception $e){
            $configDataStorageTransaction->rollBack();
            throw $e;
        }
    }

    public static function saveForUpdate(CommonConfigData $model,$data)
    {
        switch ($data['CommonConfigData']['config_level']){
            case CommonConfigDataEmum::$configLevel['private']:
                $model->modify_name=Yii::$app->session['userMail'];
                if($model->save()){
                    return true;
                }
                throw new NotFoundHttpException(current($model->getFirstErrors()));
                break;
            case CommonConfigDataEmum::$configLevel['public']:
                $data=$data['CommonConfigData'];
                $appIdsArray=static::getAppIdsByPublicKey($data['key']);
                static::realSave($appIdsArray,$data);
                return true;
                break;
        }

        throw new NotFoundHttpException('配置等级出现异常');
    }

    public static function getAppIdsByPublicKey($publicKey)
    {
        $appIdsArray=AppIdKeyRelation::find()->select(['app_id'])->where(['key'=>$publicKey])->asArray()->all();
        return array_column($appIdsArray,'app_id');
    }

    private static function realSave($appIdsArray,$data)
    {
        $configDataStorageDb = CommonConfigData::getDb();
        $configDataStorageTransaction = $configDataStorageDb->beginTransaction();
        try{
            foreach ($appIdsArray as $allowAppId) {
                $tableName = TableConfirm::willCreateTableName($allowAppId);
                CommonConfigData::setTableName($tableName);

                $commonConfigData=CommonConfigData::findOne(['key'=>$data['key'],'config_level'=>CommonConfigDataEmum::$configLevel['public']]);
                $commonConfigData->value_type=$data['value_type'];
                $commonConfigData->value=$data['value'];
                $commonConfigData->comment=$data['comment'];
                $commonConfigData->modify_name=Yii::$app->session['userMail'];
                $commonConfigData->save();
                //如果出问题了  抛异常 下面捕获 回滚
                $firstErrors=current($commonConfigData->getFirstErrors());
                if(!empty($firstErrors)){
                    throw new \Exception($firstErrors);
                }
                //否则  日志 修改了什么 从哪到哪

            }
            $configDataStorageTransaction->commit();
        }catch (\Exception $e){
            $configDataStorageTransaction->rollBack();
            throw new NotFoundHttpException($e->getTraceAsString());
        }
    }
}