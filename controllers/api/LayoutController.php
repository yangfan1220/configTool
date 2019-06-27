<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-27
 * Time: 15:29
 */

namespace app\controllers\api;

use yii\web\Response;
use yii\web\Controller;
use app\models\FormatDataStruct;
use app\models\tables\ProjectInfo;

class LayoutController extends Controller
{
    public function actionGetProjectInfo()
    {
        $data=[];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $projectInfos = ProjectInfo::find()->asArray()->all();
        foreach ($projectInfos as $index => $projectInfo) {
            $data[]=[
                'value'=>$projectInfo['app_id'],
                'text'=>$projectInfo['app_id'].' | '.$projectInfo['app_name'],
            ];
        }
        return FormatDataStruct::success($data);
    }
}