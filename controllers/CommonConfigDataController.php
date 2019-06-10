<?php

namespace app\controllers;

use app\models\service\CommonConfigDataService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\tables\CommonConfigData;
use app\models\tables\CommonConfigDataSearch;


class CommonConfigDataController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new CommonConfigDataSearch();
        !empty(\Yii::$app->request->get('app_id')) ? \Yii::$app->session['app_id'] = \Yii::$app->request->get('app_id') : [];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModelValueType2'  => $searchModel,
            'dataProviderValueType2' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $commonConfigDataService = new CommonConfigDataService();
        /**
         * 保存数据时
         */
        if (!empty($data = Yii::$app->request->post())) {

            $commonConfigDataService->validate($data);
            $commonConfigDataService->insertOperation($data);
            return $this->redirect('/common-config-data/index');
        }
        /**
         * 正常渲染页面时
         */
        $projectInfo = $commonConfigDataService->getProjectInfo();
        return $this->render('create', [
            'projectInfo' => $projectInfo,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $data = Yii::$app->request->post();
        if(!empty($data)){
            $commonConfigDataService = new CommonConfigDataService();
            $commonConfigDataService->validateForUpdate($data['CommonConfigData']);
            $commonConfigDataService->updateOperation($id, $data);
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = CommonConfigData::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
