<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\common\GetUserModel;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\ProjectInfo */
/* @var $form yii\widgets\ActiveForm */
$appPrincipalInfo = GetUserModel::getAppPrincipalInfo();

//$js = <<<JS
//$(function() {
//  $('form select#projectinfo-app_principal_id').change(function() {
//   val= $(this).val();
//   console.log(val);
//   $('#select2-w1-result-7tps-aaaaaaa').click();
//   var a=$('#select2-w1-result-7tps-aaaaaaa').trigger("click");
//      console.log(a);
//
//  });
//});
//JS;
//$this->registerJs($js);
?>

<div class="project-info-form" style="width: 40%;margin-left:25%">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'app_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'app_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'app_principal_id')->dropDownList($appPrincipalInfo, ['prompt' => '请选择']) ?>

    <div class="control-label"><b>项目管理员</b></div>

    <?=Select2::widget([ 'name' => 'app_manage_ids[]',
                         'data' => $appPrincipalInfo,
//                         'value'=>'aaaaaaa',
                         'options' => ['multiple' => true,'placeholder' => '请选择...']
    ]);?>

    <div class="form-group">
        <?= Html::submitButton('保存', [
            'class' => 'btn btn-success', 'data' => [
                'confirm' => '确认提交?',
            ]
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
