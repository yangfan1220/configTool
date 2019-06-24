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
    private static function getReleaseNameBeforeThis()
    {
        $data=ConfigDataReleaseHistory::findBySql('select `release_name`,`comment` from  `'.ConfigDataReleaseHistory::tableName().'` where `app_id`=:app_id order by `id` desc for update',[':app_id'=>\Yii::$app->session['app_id']])->limit(2)->asArray()->all();
        if(count($data)<2){
            throw new \Exception('无法回滚，不到2条发布记录');
        }
        return $data[1];
    }

    private static function insertConfigDataReleaseHistory($releaseName,$releaseComment)
    {
        $model = new  ConfigDataReleaseHistory();
        $model->app_id = \Yii::$app->session['app_id'];
        $model->current_record_style = ConfigDataReleaseHistoryEmum::$currentRecordStyleRollback;
        $model->release_name = $releaseName;
        $model->comment = $releaseComment;
        $model->create_name = \Yii::$app->session['userMail'];
        $model->save();
        if($model->hasErrors()){
            throw new \Exception(current($model->getFirstErrors()));
        }
    }

    public static function RollBack()
    {
        $transaction=ConfigDataReleaseHistory::getDb()->beginTransaction();
        try{
            //添加发布的回滚记录
            $beforeReleaseData=static::getReleaseNameBeforeThis();
            static::insertConfigDataReleaseHistory($beforeReleaseData['release_name'],$beforeReleaseData['comment']);

            //TODO  更新发布后的表  暂时不写
            $transaction->commit();
        }catch (\Exception $e){
            $transaction->rollBack();
            throw new NotFoundHttpException($e->getMessage());
        }

    }
}