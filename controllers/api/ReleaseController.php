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

    public function test()
    {
        $rs = ['code' => 0, 'msg' => 'ok', 'data' => true];
        ob_end_clean();
//        ob_start();
        echo json_encode($rs);
        $size = ob_get_length();
        header("Content-Length: $size");
        header('Connection: close');
        header("HTTP/1.1 200 OK");
        header("Content-Type: application/json;charset=utf-8");
        ob_flush();
//        ob_end_flush();
//        if(ob_get_length()){
//
//        }
//
//        flush();

        ignore_user_abort(true);
        set_time_limit(0);


        $i = 0;
        for ($a = 0; $a <= 9999999; $a++) {
            $b = $a / 300;
            if ($b == intval($b)) {
                echo '<div>' . $i . '</div>';
                flush();
                $i++;
            }
        }
        echo 'ecd';
    }

    /**
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRelease()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data=\Yii::$app->request->post();
        ReleaseService::releaseValidate($data);
        ReleaseService::Release($data);
    }
}