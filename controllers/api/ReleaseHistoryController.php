<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-10
 * Time: 10:31
 */

namespace app\controllers\api;
use app\models\tables\ConfigDataReleaseHistory;
use app\models\tables\ConfigDataReleaseHistoryAllLog;
use app\models\tables\ConfigDataReleaseHistoryModifyLog;
use yii\web\Controller;
use yii\web\Response;
use app\models\FormatDataStruct;
use Yii;



class ReleaseHistoryController extends Controller
{

    public function actionGetReleaseHistory()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $data=ConfigDataReleaseHistory::find()->where(['app_id'=>\Yii::$app->session['app_id']])->asArray()->all();
        return FormatDataStruct::success($data);
    }

    public function actionGetReleaseHistoryConfig()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $configType=Yii::$app->request->get('configType');
        $releaseName=Yii::$app->request->get('releaseName');
        if (empty($releaseName)){
            throw new \Exception('发布名称参数错误');
        }
        if($configType==1){
            $data=ConfigDataReleaseHistoryModifyLog::find()->select(['modify_type','key','old_value','new_value'])->where(['app_id'=>\Yii::$app->session['app_id'],'release_name'=>$releaseName])->asArray()->all();
        }elseif($configType==2){
            $data=ConfigDataReleaseHistoryAllLog::find()->select(['key','value'])->where(['app_id'=>\Yii::$app->session['app_id'],'release_name'=>$releaseName])->asArray()->all();
        }else{
            throw new \Exception('传输类型失败');
        }
        return FormatDataStruct::success($data);
    }
}