<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CommonConfigData */

$this->title = 'Create Common Config Data';
$this->params['breadcrumbs'][] = ['label' => 'Common Config Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="common-config-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
