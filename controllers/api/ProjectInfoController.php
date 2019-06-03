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
use app\models\tables\ProjectRedisInfo;
use yii\web\Controller;
use yii\web\Response;
use app\models\FormatDataStruct;
use app\models\SetValue;


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

    public function actionSetProjectRedisInfo()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = \Yii::$app->request->post();
        unset($data['_csrf']);
        $projectRedisInfoObj = new ProjectRedisInfo();
        $projectRedisInfoObj->attributes = $data;
        SetValue::setConfDataRedisInfo($data);
        //测试连接是否正常
        $testConnectRe = SetValue::testConnect();
        if ($testConnectRe === false) {
            return FormatDataStruct::failed('redis连接失败');

        }
        if ($projectRedisInfoObj->validate() && $projectRedisInfoObj->save()) {
            return FormatDataStruct::success();
        }
        return FormatDataStruct::failed(current($projectRedisInfoObj->getFirstErrors()));
    }

    public function actionGetProjectRedisInfo()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = \Yii::$app->request->get('project_app_id');
        $projectRedisInfoObj = new ProjectRedisInfo();
        if(!empty($projectRedisInfoObj=$projectRedisInfoObj::findOne(['project_app_id' => $data]))){
            $projectRedisInfo=$projectRedisInfoObj->toArray();
            return FormatDataStruct::success($projectRedisInfo);
        }
        return FormatDataStruct::success();
    }
}