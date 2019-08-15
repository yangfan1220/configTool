<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-10
 * Time: 20:14
 */

namespace app\controllers\api;


use app\models\service\RollBackService;
use yii\web\Controller;
use app\models\FormatDataStruct;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RollBackController extends  Controller
{
    public function actionRollBack()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $targetRollbackVersionUniqueId=\Yii::$app->request->post('target_rollback_version_uniqueId');
        $targetRollbackVersionReleaseName=\Yii::$app->request->post('target_rollback_version_release_name');
        if(empty($targetRollbackVersionUniqueId)){
            throw new NotFoundHttpException('target_rollback_version_uniqueId不能为空');
        }
        RollBackService::RollBack($targetRollbackVersionUniqueId,$targetRollbackVersionReleaseName);
        return FormatDataStruct::success();
    }
}