<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-04
 * Time: 12:50
 */

namespace app\controllers\api;

use app\models\tables\ConfigDataModifyLog;
use app\models\tables\ConfigDataReleaseHistory;
use yii\web\Controller;
use yii\web\Response;
use app\models\FormatDataStruct;
use app\models\Emum\ConfigDataModifyLogEmum;
use app\models\Emum\ConfigDataReleaseHistoryEmum;


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

    public function actionGetConfigDataReleaseHistory()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $configDataReleaseHistoryData = ConfigDataReleaseHistory::find()->select(['release_name','unique_id'])->where('app_id=:app_id',[':app_id'=>\Yii::$app->session['app_id']])
            ->andWhere('current_record_style=:current_record_style',[':current_record_style'=>ConfigDataReleaseHistoryEmum::$currentRecordStyleRelease])
            ->orderBy(['id'=>SORT_DESC])
            ->asArray()->all();

        /**
         * ,'current_record_style=:current_record_style'
         * ,':current_record_style'=>ConfigDataReleaseHistoryEmum::$currentRecordStyleRelease
         */
        return FormatDataStruct::success($configDataReleaseHistoryData);
    }
}