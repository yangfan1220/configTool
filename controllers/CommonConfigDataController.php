<?php

namespace app\controllers;

use app\models\TableConfirm;
use app\models\UpdateValue;
use Yii;
use app\models\CommonConfigData;
use app\models\CommonConfigDataSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommonConfigDataController implements the CRUD actions for CommonConfigData model.
 */
class CommonConfigDataController extends Controller
{
    public function init()
    {
        $queryParams = Yii::$app->request->queryParams;
        if (isset($queryParams['pk']) && !empty($queryParams['pk'])) {
            CommonConfigData::setTableName($queryParams['pk']);
            Yii::$app->session['pk'] = $queryParams['pk'];
            //判断是否存在该表
            static::tableIsExist($queryParams['pk']);
            return;
        }
        if (Yii::$app->session->has('pk')) {
            CommonConfigData::setTableName(Yii::$app->session['pk']);
            //判断是否存在该表
            static::tableIsExist(Yii::$app->session['pk']);
            return;
        }
        if (empty($queryParams['pk']) && Yii::$app->session->has('pk') === false) {
            throw new NotFoundHttpException('会话过期，请回到主页重新登录');
        }
    }

    private static function tableIsExist($projectkey)
    {
        if (TableConfirm::tableIsExist($projectkey) === false) {
            throw new NotFoundHttpException('该表不存在，请不要修改url地址');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CommonConfigData models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommonConfigDataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CommonConfigData model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CommonConfigData model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CommonConfigData();

        if ($model->load(Yii::$app->request->post()) && $model->save() && UpdateValue::setRedisValue(Yii::$app->request->post())) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CommonConfigData model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save() && UpdateValue::setRedisValue(Yii::$app->request->post())) {

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $commonConfigDataModel = $this->findModel($id);
        $commonConfigDataModel->delete();
        UpdateValue::delRedisValue($commonConfigDataModel->getAttribute('key'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the CommonConfigData model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommonConfigData the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CommonConfigData::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
