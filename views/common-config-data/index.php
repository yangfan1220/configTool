<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use app\models\Emum\CommonConfigDataEmum;
use app\assets\CommonConfigDataIndexAsset;
use app\models\common\ReleaseStatusModel;
use app\models\Emum\ConfigDataModifyLogEmum;
use app\models\service\ReleaseService;

CommonConfigDataIndexAsset::register($this);
$releaseStatusData = ReleaseStatusModel::formatReleaseStatus();
$currentKeyStatus=ReleaseService::getReleaseChanges();

/* @var $this yii\web\View */
/* @var $searchModelValueType2 app\models\CommonConfigDataSearch */
/* @var $dataProviderValueType2 yii\data\ActiveDataProvider */
$dataProviderValueType2->pagination->pageParam = 'data2-page';
$dataProviderValueType2->sort->sortParam = 'data2-sort';

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
                alert("直接error了"+err[\'responseJSON\'][\'message\']);
            }
        });
    });');

Modal::begin([
    'id'          => 'create-modal',
    'header'      => '<h4 class="modal-title">项目redis信息</h4>',
    'footer'      => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
    'bodyOptions' => ['class' => 'projectRedisInfo'],
]);
$csrf = Yii::$app->request->csrfToken;
$js = <<<JS
    $(function() {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
      $('.projectRedisInfo').html($(".willDisplay").html());
      $(".willDisplaySubmit").click(function() {
        $.ajax({
            url: "/api/project-info/set-project-redis-info",
            data: {
                _csrf:csrfToken,
                project_app_id:$("#urlAppIdParam").val(),
                redis_host:$(".willDisplayHostAddr").val(),
                redis_port:$(".willDisplayHostPort").val(),
                redis_database_id:$(".willDisplayDBNo").val(),
                redis_password:$(".willDisplayDBPasswd").val(),
                },
            type: "post",
            success: function (data) {
                if (data.code==0){
                   $('.close').trigger("click");
                   window.location.href='/common-config-data/index';
                }else {
                    alert("获取项目信息失败 code 不等于0"+data.message);
                }
            },
            error: function (err,data,aaa) {
                alert("直接error了"+err['responseJSON']['message']);
            }
        });
      });
    });

JS;
$this->registerJs($js);
Modal::end();

$js = <<<JS
 $.ajax({
            url: "/api/project-info/get-project-redis-info",
            data: {
                // _csrf:csrfToken,
                project_app_id:$("#urlAppIdParam").val(),
                },
            type: "get",
            success: function (data) {
                if (data.code==0){
                    if(data.data!=""){
                        $("#host_addr").text(data.data.redis_host)
                        $("#host_port").text(data.data.redis_port)
                        $("#db_no").text(data.data.redis_database_id)
                        $("#db_passwd").text(data.data.redis_password)
                    }
                }else {
                    alert("获取项目信息失败 code 不等于0"+data.message);
                }
            },
            error: function (err,data,aaa) {
                alert("直接error了"+err['responseJSON']['message']);
            }
        });
JS;
$this->registerJs($js);
?>
<!--下面是弹出框的内容   开始-->
<div style="display: none" class="willDisplay">
    <form>
        <div class="form-group">
            <label>主机地址</label>
            <input type="text" class="form-control willDisplayHostAddr" autocomplete="off">
            <input type="hidden" name="_csrf" id="csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
        </div>

        <div class="form-group">
            <label>主机端口</label>
            <input type="text" class="form-control willDisplayHostPort" autocomplete="off">
        </div>

        <div class="form-group">
            <label>数据库编号</label>
            <input type="text" class="form-control willDisplayDBNo" autocomplete="off">
        </div>

        <div class="form-group">
            <label>主机密码</label>
            <input type="password" class="form-control willDisplayDBPasswd" autocomplete="off">
        </div>

        <button type="button" class="btn btn-default willDisplaySubmit" style="margin-left: 40%">提交</button>
    </form>
</div>
<!--上面是弹出框的内容  结束-->

<input type="hidden" id="urlAppIdParam"
       value="<?php !empty(\Yii::$app->request->get('app_id')) ? \Yii::$app->session['app_id'] = \Yii::$app->request->get('app_id') : [];
       echo \Yii::$app->session['app_id']; ?>"/>

<div class="common-config-data-index" style="border-bottom: 1px solid #000000">
    <div class="row" style="margin-bottom: 5%">
        <div class="col-md-2 "
             style="height: 210px;width:250px;border: 1px solid;background-color: #ffffff;border-radius: 10px">
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

        <div class="col-md-2 col-md-offset-1"
             style="height: 210px;width:250px;border: 1px solid;background-color: #ffffff;border-radius: 10px">
            <div style="height: 15%;">
                <div style="height: 100%;width: 60%;display: inline;color: red">
                    项目redis信息
                </div>
                <div style="display: inline;margin-left: 50%">
                    <a style="border: none;" href="#" data-target="#create-modal" data-toggle="modal" ,>
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true" style=""></span>
                    </a>
                </div>
            </div>

            <div style="height: 85%;line-height: 3em">
                主机地址:&nbsp;&nbsp;&nbsp;<div style="display: inline" id="host_addr"></div>
                <br/>
                主机端口:&nbsp;&nbsp;&nbsp;<div style="display: inline" id="host_port"></div>
                <br/>
                数据库编号:&nbsp;&nbsp;&nbsp;<div style="display: inline" id="db_no"></div>
                <br/>
                密码:&nbsp;&nbsp;&nbsp;<div style="display: inline" id="db_passwd"></div>
                <br/>
            </div>
        </div>

    </div>

    <p>
        <?= Html::button('表格', ['class' => 'btn btn-success', 'style' => [], 'id' => 'table-button']) ?>
        <?= Html::button('更改历史', ['class' => 'btn btn-success', 'style' => [], 'id' => 'modify-history-button']) ?>
        <?= Html::a('添加配置', ['create'], ['class' => 'btn btn-success', 'style' => ['float' => 'right']]) ?>
        <?= Html::a('发布历史', ['/release-history/index'], ['class' => 'btn btn-success', 'style' => ['float' => 'right', 'margin-right' => '4px']]) ?>
        <?= Html::button('发布', ['class' => 'btn btn-success index-release', 'style' => ['float' => 'right', 'margin-right' => '4px'], 'id' => 'publish-button', 'data-target' => '#publish-modal', 'data-toggle' => 'modal']) ?>
        <?= Html::button('回滚', ['class' => 'btn btn-success rollback-button', 'style' => ['float' => 'right', 'margin-right' => '4px'],'id' => 'rollback-button', 'data-target' => '#rollback-modal', 'data-toggle' => 'modal']) ?>
        <?php
        //发布的弹窗
        Modal::begin([
            'id'          => 'publish-modal',
            'header'      => '<h4 class="modal-title">发布</h4>',
            'footer'      => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
            'bodyOptions' => ['class' => 'publish'],
            'size'        => 'modal-lg',
        ]);
        echo $this->render('publish_toggle');
        Modal::end();

        //回滚的弹窗
        Modal::begin([
            'id'          => 'rollback-modal',
            'header'      => '<h4 class="modal-title">回滚</h4>',
            'footer'      => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
            'bodyOptions' => ['class' => 'rollback'],
            'size'        => 'modal-lg',
        ]);
        echo $this->render('rollback_toggle');
        Modal::end();

        ?>

    </p>


</div>
<div class="project-config-info-display">
    <?= Html::tag('div', '项目配置', ['class' => ['h3'], 'style' => ['text-align' => 'center']]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProviderValueType2,
        'filterModel'  => $searchModelValueType2,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'config_level', 'content' => function ($model, $key, $index, $column) {
                return array_search($model->config_level, CommonConfigDataEmum::$configLevel);
            }
            ],
            [
                'label' => '发布状态', 'content' => function ($model, $key, $index, $column) use ($releaseStatusData) {
                $publishStatus = 1;
                if (in_array(md5($model->getAttribute('key') . $model->getAttribute('value')), $releaseStatusData)) {
                    $publishStatus = 2;
                }
                $class = ['class' => ['btn-success']];
                if ($publishStatus == ConfigDataModifyLogEmum::$dontPublishStatus) {
                    $class = ['class' => ['btn-warning']];
                }
                return Html::tag('div', ConfigDataModifyLogEmum::$publishStatus[$publishStatus], $class);
            }
            ],
            [
                'attribute' => 'key', 'content' => function ($model, $key, $index, $column) use ($currentKeyStatus) {
                $class = [];
                if(!isset($currentKeyStatus[$model->key])){
                    return $model->key;
                }
                switch ($currentKeyStatus[$model->key]['status']) {
                    case CommonConfigDataEmum::$currentConfigStatusAdd:
                        $class = ['class' => ['btn-success'], 'style' => 'display:inline'];
                        break;
                    case CommonConfigDataEmum::$currentConfigStatusModify:
                        $class = ['class' => ['btn-info'], 'style' => 'display:inline'];
                        break;
                    case CommonConfigDataEmum::$currentConfigStatusDel:
                        $class = ['class' => ['btn-danger'], 'style' => 'display:inline'];
                        break;
                }
                $content = $model->key . Html::tag('div', CommonConfigDataEmum::$current_config_status[$currentKeyStatus[$model->key]['status']], $class);
                return Html::tag('div', $content, ['style' => 'display:inline']);
            }
            ],


            'value',
            'comment',
            [
                'attribute' => 'value_type', 'content' => function ($model, $key, $index, $column) {
                return array_search($model->value_type, CommonConfigDataEmum::$valueType);
            },
            ],
            'create_name',
            'modify_name',
            ['class' => 'yii\grid\ActionColumn', 'header' => '操作', 'visibleButtons' => ['delete' => false, 'view' => false, 'update' => true]],
        ],
    ]); ?>

</div>

<!--  切换标签数据的容器-->
<div class="modify-history-container">


</div>
