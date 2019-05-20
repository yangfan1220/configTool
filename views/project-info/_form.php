<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProjectInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'project_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'redis_host')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'redis_port')->textInput() ?>

    <?= $form->field($model, 'redis_database_id')->textInput() ?>

    <?= $form->field($model, 'redis_password')->textInput(['maxlength' => true]) ?>

<!--    --><?php //= $form->field($model, 'create_time')->textInput() ?>
<!---->
<!--    --><?php //= $form->field($model, 'update_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success','data' => [
            'confirm' => 'Do you confirm?',
        ]]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
