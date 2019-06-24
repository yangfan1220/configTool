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
use yii\web\Response;

class RollBackController extends  Controller
{
    public function actionRollBack()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        RollBackService::RollBack();
        return FormatDataStruct::success();
    }
}