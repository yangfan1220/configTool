<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CommonConfigData */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="common-config-data-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => true,'readonly'=>true]) ?>

    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

<!--    --><?php // echo $form->field($model, 'create_time')->textInput(); ?>
<!---->
<!--    --><?php // echo $form->field($model, 'update_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
