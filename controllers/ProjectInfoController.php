<?php

namespace app\controllers;

use app\models\service\ProjectInfoService;
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
        //TODO  优化
        $data=Yii::$app->request->post();
        $model = new ProjectInfo();
        if ($model->load($data)){
            ProjectInfoService::validate($data);
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
}
