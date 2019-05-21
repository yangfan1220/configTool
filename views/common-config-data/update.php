<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CommonConfigData */

$this->title = 'Update Common Config Data: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Common Config Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="common-config-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
