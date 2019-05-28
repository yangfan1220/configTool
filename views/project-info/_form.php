<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\common\GetUserModel;

/* @var $this yii\web\View */
/* @var $model app\models\ProjectInfo */
/* @var $form yii\widgets\ActiveForm */
$appPrincipalInfo=GetUserModel::getAppPrincipalInfo();
?>

<div class="project-info-form" style="width: 40%;margin-left:25%">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'app_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'app_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'app_principal_id')->dropDownList($appPrincipalInfo, ['prompt'=>'请选择']) ?>

    <div class="control-label"><b>项目管理员</b></div>
    <?php
    foreach ($appPrincipalInfo as $userId=>$userNameUserMail){
        echo '<div class="input-group"><span class="input-group-addon">';
        echo Html::checkbox('app_manage_ids[]',false,['value'=>$userId,'label'=>$userNameUserMail,'']);
        echo '</span></div>';
    }
    ?>
    <?php // echo  $form->field($model, 'app_manage_ids')->dropDownList(GetUserModel::getAppPrincipalInfo(), ['prompt'=>'请选择']) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success','data' => [
            'confirm' => '确认提交?',
        ]]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
