<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-27
 * Time: 16:55
 */

use yii\helpers\Html;
?>

<?= Html::tag('label', '请选择你要回滚的版本',['style'=>['font-size'=>'20px']])."<br/>" ?>
<style>
    .btn{
        border: #0f0f0f 1px ridge;
    }
</style>
<div class="btn-group" style="width: 50%">
    <button type="button" class="btn " style="width: 40%">请选择</button>
    <div style="display: none"></div>
    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu common-config-data_index_rollback_dropdown-menu">
        <li><a href="#">Action</a></li>
        <li><a href="#">Another action</a></li>
        <li><a href="#">Something else here</a></li>
        <li><a href="#">Separated link</a></li>
    </ul>
</div>


<?= Html::button('提交',['class'=>['btn','btn-success','index-toggle-rollback'],'style'=>['margin-left'=>'40%','display'=>'block']]);?>


