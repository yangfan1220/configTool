<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CommonConfigData */
/* @var $form yii\widgets\ActiveForm */

$js = <<<JS
if ($(".config_level_public").prop("checked")==true){
     var str = $('.willBeFull').html().replace(/\s+/g,"");
    if (str.length=="" || str==undefined){
  $('.willBeFull').append($('.tmpHidden').html());
  }
}
console.log($(".config_level_public").prop("checked"));

$(".config_level_public").click(function() {
    $(".config_level_public").prop("checked","true");
 str = $('.willBeFull').html().replace(/\s+/g,"");
    if (str.length==""){
  $('.willBeFull').append($('.tmpHidden').html());
  }
});


$(".config_level_private").click(function() {
  $('.willBeFull').empty();
});
JS;
$this->registerJs($js);
//TODO 默认必须选择 当前的appid 且 不可取消
?>


<div style="display: none" class="tmpHidden">
    <?= Html::tag('label', '选择哪个appId可以获取到该配置') . "<br/>" ?>
    <?= Html::checkboxList('allowAppIds', ['id'], $projectInfo) ?>
</div>
<div class="common-config-data-form">
    <?= Html::beginForm(['/common-config-data/create'], 'post') ?>

    <?= Html::tag('label', Html::encode("配置等级")) . "<br/>" ?>
    <?= Html::radio('config_level', true, ['label' => 'private', 'value' => '1', 'class' => 'config_level_private']); ?>
    <?= Html::radio('config_level', false, ['label' => 'public', 'value' => '2', 'class' => 'config_level_public']) . "<br/><br/>"; ?>



    <?= Html::tag('label', '配置名称')."<br/>" ?>
    <?= Html::input('text', 'key', '', ['class' => 'form-control']) ?>

    <?= Html::tag('label', '配置内容')."<br/>" ?>
    <?= Html::textarea('value', '', ['class' => 'form-control']) ?>



    <?= "<br/>" . Html::tag('label', Html::encode("配置值的类型")) . "<br/>" ?>
    <?= Html::radio('value_type', true, ['label' => 'string', 'value' => '1']); ?>
    <?= Html::radio('value_type', false, ['label' => 'Json', 'value' => '2']) . "<br/><br/>"; ?>

    <?php // $form->field($model, 'comment')->textInput(['maxlength' => true, 'class' => ['form-control']]) ?>
    <?= Html::tag('label', '配置注释')."<br/>" ?>
    <?= Html::input('text', 'comment', '', ['class' => 'form-control'])."<br/>" ?>



    <div class="willBeFull">

    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?= Html::endForm() ?>

</div>
