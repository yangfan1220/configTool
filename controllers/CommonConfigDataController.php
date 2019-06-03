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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['config_level' => 2]);

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

            $validateRe = $commonConfigDataService->validate($data);
            if (!empty($validateRe)) {
                throw new NotFoundHttpException($validateRe);
            }

            $commonConfigDataService->saveOperation($data);
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
        if ($model->load($data)) {
            $commonConfigDataService = new CommonConfigDataService();
            $validateRe = $commonConfigDataService->validateForUpdate($data['CommonConfigData']);
            if (!empty($validateRe)) {
                throw new NotFoundHttpException($validateRe);
            }

            if ($commonConfigDataService->saveForUpdate($model, $data)) {
                return $this->redirect(['index']);
            }
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
