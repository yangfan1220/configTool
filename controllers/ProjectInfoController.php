<?php

namespace app\controllers;

use app\models\FormatDataStruct;
use app\models\SetValue;
use app\models\TableConfirm;
use Yii;
use app\models\ProjectInfo;
use app\models\ProjectInfoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProjectInfoController implements the CRUD actions for ProjectInfo model.
 */
class ProjectInfoController extends Controller
{
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
     * Lists all ProjectInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

//        var_dump($_GET);
//        $tableIsExistRe=TableConfirm::tableIsExist($projectKey);
//        if (!empty($tableIsExistRe)) echo $tableIsExistRe;

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProjectInfo model.
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
     * Creates a new ProjectInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectInfo();
        if ($model->load(Yii::$app->request->post())) {
            try {
                SetValue::setConfDataRedisInfo(Yii::$app->request->post('ProjectInfo'));
                SetValue::testConnect();
            } catch (\Exception $e) {
                throw new NotFoundHttpException($e->getMessage());
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProjectInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProjectInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProjectInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectInfo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionFirst($projectKey)
    {
        $this->layout = false;
        $tableIsExistRe = TableConfirm::tableIsExist($projectKey);
        return $this->render('first', [
            'tableIsExistRe' => $tableIsExistRe,
            'createTableDDL' => sprintf(Yii::$app->params['createConfigDataStorageTableDDL'], TableConfirm::willCreateTableName($projectKey)),
            'projectKey'     => $projectKey,
        ]);
    }

    public function actionCreateTable()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->layout = false;
        $projectKey = Yii::$app->request->post('projectKey');
        if (TableConfirm::tableIsExist($projectKey)) {
            $this->redirect('/project-info/index');
        }
        try {
            $createTableRe = TableConfirm::createTable($projectKey);
        } catch (\Exception $e) {
            $createTableRe = $e->getMessage();
        }
        if ($createTableRe === 0) {
            return FormatDataStruct::success($createTableRe);
        } else {
            return FormatDataStruct::failed($createTableRe);
        }
    }
}
