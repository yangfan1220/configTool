<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-05
 * Time: 17:02
 */

namespace app\controllers\api;

use yii\web\Controller;
use app\models\service\ReleaseService;
use yii\web\Response;
use app\models\FormatDataStruct;


class ReleaseController extends Controller
{
    public function actionGetReleaseChanges()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = ReleaseService::getReleaseChanges();
        return FormatDataStruct::success($data);
    }

    public function actionTest()
    {
        $messages = [];
        $users = [
            'fan.yang@mfashion.com.cn',
        ];
        $errorMessages=[
            '当前app_id: '.\Yii::$app->session['app_id']
        ];
        foreach ($users as $user) {
            $messages[] = \Yii::$app->mailer->compose('mailTemplate',['imageFileName'=>'./img/logo.png','errorMessages'=>$errorMessages])
                ->setFrom(['alert@mfashion.com.cn' => '配置工具报警账号'])
                ->setTo($user)
                ->setSubject('配置工具发布至redis出现异常');
        }
        \Yii::$app->mailer->sendMultiple($messages);
    }

    /**
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRelease()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data = \Yii::$app->request->post();
        ReleaseService::releaseValidate($data);
        ReleaseService::Release($data);
        return FormatDataStruct::success();
    }
}