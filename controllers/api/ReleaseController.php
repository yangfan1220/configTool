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
//        $messages = [];
//        $users = [
//            'fan.yang@mfashion.com.cn',
//        ];
//        $errorMessages=[
//            '当前app_id: '.\Yii::$app->session['app_id']
//        ];
//        foreach ($users as $user) {
//            $messages[] = \Yii::$app->mailer->compose('mailTemplate',['imageFileName'=>'./img/logo.png','errorMessages'=>$errorMessages])
//                ->setFrom(['alert@mfashion.com.cn' => '配置工具报警账号'])
//                ->setTo($user)
//                ->setSubject('配置工具发布至redis出现异常');
//        }
//        \Yii::$app->mailer->sendMultiple($messages);
        $beginTransaction=\Yii::$app->db->beginTransaction();
        try{
            $a=\Yii::$app->db->createCommand('delete from project_info where id=3')->execute();
            $b=\Yii::$app->db->createCommand('select * from project_info for update ')->queryAll();
            throw new \Exception('111');

            $beginTransaction->commit();
        }catch (\Exception $e){
            $beginTransaction->rollBack();
            var_dump($e->getMessage());
        }
        var_dump(isset($a)?$a:'a');
        var_dump(isset($b)?$b:'b');
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