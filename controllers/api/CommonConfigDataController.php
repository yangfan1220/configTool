<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-04
 * Time: 12:50
 */

namespace app\controllers\api;

use app\models\tables\ConfigDataModifyLog;
use yii\web\Controller;
use yii\web\Response;
use app\models\FormatDataStruct;
use app\models\Emum\ConfigDataModifyLogEmum;


class CommonConfigDataController extends Controller
{
    public function actionGetConfigDataModifyLog()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = ConfigDataModifyLog::find()->where(['app_id' => \Yii::$app->session['app_id']])->orderBy(['create_time' => SORT_DESC])->asArray()->all();

        array_walk($data, function (&$value) {
            if (!empty($value['modify_type'])) {
                $value['modify_type'] = ConfigDataModifyLogEmum::$modifyType[$value['modify_type']];
            }
        });
        return FormatDataStruct::success($data);
    }
}