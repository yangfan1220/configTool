<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CommonConfigDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '项目配置数据';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="common-config-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('添加配置', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'key',
            'value:ntext',
            'comment',
            'create_time',
            //'update_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
