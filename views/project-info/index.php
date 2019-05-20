<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Infos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-info-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Project Info', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <!--    --><?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'project_name',
            'project_key',
            'redis_host',
            'redis_port',
            'redis_database_id',
            'redis_password',
            'create_time',
            'update_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
    <!--   用于显示弹出框的基本元素-->
    <div class="modal bs-example-modal-lg" id="projectKey">
        <div class="modal-dialog">
            <div class="modal-content width_reset" id="tmpl-modal-output-render"></div>
        </div>
    </div>

    <!--    --><?php //\yii\widgets\Pjax::begin(['enablePushState' => false,'timeout' =>5000]); ?>
    <!---->
    <!--    --><?php //echo Html::a("历史地址", ['project-info/first?projectKey=11'], ['class' => 'btn btn-lg btn-primary']) ?>
    <?php //if (!empty($tableIsExistRe)) echo $tableIsExistRe; ?>
    <!--    --><?php //\yii\widgets\Pjax::end(); ?>
</div>
