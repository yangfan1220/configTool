<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CommonConfigDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJs('$(function () {
        $.ajax({
            url: "/api/project-info/get-project-over-view-info",
            data: {app_id:$("#urlAppIdParam").val()},
            type: "get",
            success: function (data) {
                if (data.code==0){
                    $("#app_id").text(data.data.app_id);
                    $("#app_name").text(data.data.app_name);
                    $("#principal_name").text(data.data.principal_name);
                    $("#principal_email").text(data.data.principal_email);
                }else {
                    alert("获取项目信息失败 code 不等于0"+data.msg);
                }
            },
            error: function (err) {
                alert("直接error了"+data.msg);
            }
        });
    });');
?>
<input type="hidden" id="urlAppIdParam" value="<?= \Yii::$app->request->get('app_id') ?>"/>
<script>

</script>
<div class="common-config-data-index">
    <div class="row" style="margin-bottom: 5%">
        <div class="col-md-2 " style="height: 210px;width:250px;border: 1px solid;background-color: #ffffff;border-radius: 10px">
            <div style="height: 15%;">
                <div style="height: 100%;width: 60%;display: inline">
                    项目信息
                </div>
                <div style="display: inline;margin-left: 60%">
                    <a style="border: none;" href="#">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true" style=""></span>
                    </a>
                </div>
            </div>

            <div style="height: 85%;line-height: 3em">
                应用id:&nbsp;&nbsp;&nbsp;<div style="display: inline" id="app_id"></div>
                <br/>
                应用名:&nbsp;&nbsp;&nbsp;<div style="display: inline" id="app_name"></div>
                <br/>
                负责人:&nbsp;&nbsp;&nbsp;<div style="display: inline" id="principal_name"></div>
                <br/>
                邮箱:&nbsp;&nbsp;&nbsp;<div style="display: inline" id="principal_email"></div>
                <br/>
            </div>


        </div>
    </div>

    <p>
        <?= Html::a('添加配置', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'key',
            'value:ntext',
            'comment',
            'create_time',
            //'update_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
