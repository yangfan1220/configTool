<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ProjectInfo */

$this->title = '创建项目';
$this->params['breadcrumbs'][] = ['label' => '项目信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
