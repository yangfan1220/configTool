<?php
use  yii\helpers\Html;
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-04
 * Time: 17:35
 */
?>
<div class="change" style="display: inline">
    <label style="font-size: 20px">Changes</label>

</div>

<?= Html::tag('span', '',['class'=>['glyphicon','glyphicon-asterisk'],'style'=>['color'=>'red','display'=>'inline']]) ?>
<?= Html::tag('label', 'Release Name',['style'=>['font-size'=>'20px']])."<br/>" ?>

<?= Html::input('text','release_name','',['class'=>['form-control','release-release-name'],'style'=>['width'=>'70%','margin-left'=>'5%']]);?>


<?= Html::tag('label', 'Comment',['style'=>['font-size'=>'20px']])."<br/>" ?>

<?= Html::textarea('textarea','',['rows'=>5,'class'=>['form-control','release-release-comment'],'style'=>['width'=>'70%','margin-left'=>'5%']]);?>
<?= Html::button('提交',['class'=>['btn','btn-success','index-toggle-release'],'style'=>['margin-left'=>'40%']]);?>


