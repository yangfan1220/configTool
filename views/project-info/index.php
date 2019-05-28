<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJs('$(function () {
       var myProjectParentHeight=$(".myProject").parent().height();
       if(myProjectParentHeight>400){
           $(".myProject").css("height",myProjectParentHeight);
       }
       $(".myProjectChild").css("margin-top",myProjectParentHeight/2);
       //获取项目信息
       $.ajax({
        url: \'/api/project-info/get-project-info\',
        type: \'GET\',
        success: function (data) {
            if (data.code==0){
                $.each(data.data,function (key,value) {
                if(value.app_id!=undefined){
                 $(".willPushItems").append(generateWillPushItem(value.app_id,value.app_name));
                }
                   
                });
            }else {
                alert("获取项目信息失败"+data.msg);
            }
        },
        error: function (err) {
            alert("获取项目信息失败"+data.msg);
        }
    });
    $(".createProject").click(function () {
        window.location.href="/project-info/create";
    });
       });');
$this->registerJs('
       $(".willPushItems").on("click",".projectInfo",function(){
        window.location.href="common-config-data/index?app_id="+$(this).attr("appId");
  });
');
?>
<script type="text/javascript">
    function generateWillPushItem(appId,appName) {
        return "<div class=\"col-md-2 overview btn projectInfo\" appId=\""+appId+"\">"+appId+"<br/>"+appName+"</div>";
    }

</script>
<style rel="stylesheet">
    .overview {
        background-color: #a9d86c;
        margin: 0.5% 0.5%;
        color: #ffffff;
        height: 117px;
    }
    .vertical_center{
        padding-top: 10%;
    }
</style>
<div class="row" style="text-align: center">
    <div class="col-md-2 overview myProject vertical_center" style="height: 258px">我的项目</div>
    <div class="row willPushItems">
        <div class="col-md-2 overview btn createProject"><h1><span class="glyphicon  glyphicon-plus btn-lg" aria-hidden="true"></span>
            </h1>创建项目
        </div>
<!--js添加的元素在这里-->
    </div>

</div>
