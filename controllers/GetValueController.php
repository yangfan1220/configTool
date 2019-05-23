<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-22
 * Time: 17:11
 */

namespace app\controllers;


use app\models\GetValue;
use yii\web\Controller;
use app\models\FormatDataStruct;
use yii\web\Response;


class GetValueController extends Controller
{
    public function actionIndex()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data['app_id']=\Yii::$app->request->get('app_id','');
        $data['key']=\Yii::$app->request->get('key','');
        $getValueModel = new GetValue();
        $dynamicModel = $getValueModel->validateData($data);
        if ($dynamicModel->hasErrors()) {
            $error = $dynamicModel->getFirstErrors();
            return FormatDataStruct::failed(current($error));
        }

        $value=$getValueModel->getValue($data);

    }
}