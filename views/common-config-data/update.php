<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CommonConfigData */

$this->title = '更新项目配置数据: ';
?>
<div class="common-config-data-update" style="width:70% ;margin-left: 10%">
    <div class="alert alert-danger" role="alert">不支持public与private相互转换</div>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
