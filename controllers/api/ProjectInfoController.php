<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-27
 * Time: 15:14
 */

namespace app\controllers\api;

use app\models\common\GetUserModel;
use app\models\tables\ProjectInfo;
use yii\web\Controller;
use yii\web\Response;
use app\models\FormatDataStruct;


class ProjectInfoController extends Controller
{
    /**
     * 首页的卡片信息
     * @return array
     */
    public function actionGetProjectInfo()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $projectInfoObj = ProjectInfo::find();
        $appId = \Yii::$app->request->get('app_id');
        if (!empty($appId)) {
            $projectInfoObj->where(['app_id' => $appId]);
        }
        $projectInfoObj->asArray()->all();
        return FormatDataStruct::success($projectInfoObj->asArray()->all());
    }

    public function actionGetProjectOverViewInfo()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $appId = \Yii::$app->request->get('app_id');
        if (empty($appId)) {
            throw new  \yii\web\NotFoundHttpException('app_id出现错误');
        }
        $projectInfo = GetUserModel::getProjectInfoByAppId($appId);
        $userInfo = GetUserModel::getUserInfoByID($projectInfo['app_principal_id']);
        return FormatDataStruct::success([
            'app_id'          => $projectInfo['app_id'],
            'app_name'        => $projectInfo['app_name'],
            'principal_name'  => $userInfo['user_name'],
            'principal_email' => $userInfo['user_mail'],
        ]);
    }
}