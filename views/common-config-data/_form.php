<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Emum\CommonConfigDataEmum;

/* @var $this yii\web\View */
/* @var $model app\models\CommonConfigData */
/* @var $form yii\widgets\ActiveForm */

$ConfigValueType=array_flip(CommonConfigDataEmum::$valueType);
?>

<div class="common-config-data-form">
    <?php $form = ActiveForm::begin(); ?>

    <?php
    $configLevelKey=array_search($model->config_level,CommonConfigDataEmum::$configLevel);
    ?>
    <?= $form->field($model, 'config_level')->radioList([$model->config_level=>$configLevelKey]) ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => true,'readonly'=>true]) ?>

    <?= $form->field($model, 'value_type')->radioList($ConfigValueType) ?>

    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

<!--    --><?php // echo $form->field($model, 'value_type')->radio(); ?>
<!---->
<!--    --><?php // echo $form->field($model, 'update_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
