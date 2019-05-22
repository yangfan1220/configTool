<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CommonConfigData */

$this->title = '添加配置';
$this->params['breadcrumbs'][] = ['label' => '项目配置数据', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="common-config-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formCreate', [
        'model' => $model,
    ]) ?>

</div>
