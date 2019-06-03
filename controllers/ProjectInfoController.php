<?php

namespace app\controllers;

use app\models\FormatDataStruct;
use app\models\common\TableConfirm;
use app\models\tables\JurisdictionInfo;
use Yii;
use app\models\tables\ProjectInfo;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProjectInfoController implements the CRUD actions for ProjectInfo model.
 */
class ProjectInfoController extends Controller
{
    public function init()
    {
        if(empty(Yii::$app->session['username'])){
            $this->redirect('/site/login');
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
     * Lists all ProjectInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
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
     * 创建项目基本信息
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        $data=Yii::$app->request->post();
        $model = new ProjectInfo();
        if ($model->load($data)){
            //创建表 在db2
            try {
                TableConfirm::createTable($data['ProjectInfo']['app_id']);
            }catch (\Exception $e){
                throw new NotFoundHttpException($e);
            }
            //存储信息以及权限
            $insertData=[];
            foreach ($data['app_manage_ids'] as $userId){
                $insertData[]=[
                    $data['ProjectInfo']['app_id'],
                    $userId
                ];
            }
            $transaction=$model::getDb()->beginTransaction();
            try{
                if ($model->save() && $model::getDb()->createCommand()->batchInsert(JurisdictionInfo::tableName(), ['app_id','user_id'], $insertData)->execute()){
                    $transaction->commit();
                }else{
                    $transaction->rollBack();
                    throw new NotFoundHttpException('保存失败:'.current($model->getFirstErrors()));
                }
            }catch (\Exception $e){
                $transaction->rollBack();
                throw new NotFoundHttpException($e);
            }
            return $this->redirect(['/common-config-data/index', 'app_id' => $model->app_id]);
        }
        return $this->render('create',['model'=>$model]);
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
