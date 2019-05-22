<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-20
 * Time: 11:07
 */

use yii\helpers\Html;

?>
    <script src="/assets/80ce0cea/jquery.js"></script>
    <script src="/assets/b37ae8fb/yii.js"></script>
    <script src="/assets/b37ae8fb/yii.gridView.js"></script>
    <script src="/assets/beb28805/js/bootstrap.js"></script>
    <div style="display: none" class="willSendProjectKey"><?=$projectKey ?></div>
<?php
if ($tableIsExistRe == false) {
    ?>

    <div class="alert alert-warning alert-dismissible" role="alert">
        <strong>警告！</strong> 当前项目的配置表不存在，是否确认创建？
    </div>
    <div style="width:100%;height:100%;word-wrap: break-word">
        <?php highlight_string($createTableDDL); ?>
    </div>
    <div align="center">
        <?php
        echo Html::a("取消", '/project-info/index', ['class' => 'btn  btn-primary']);
        echo Html::button('确认', ['class' => 'btn  btn-danger submitTableDDL']);
        ?>
    </div>
    <script type="text/javascript">
        $(function () {
            var willSendProjectKey=$('.willSendProjectKey').text();
            $(".submitTableDDL").click(function () {
                $.ajax({
                    url: '/project-info/create-table',
                    data: {
                        projectKey: willSendProjectKey,
                    },
                    type: 'POST',
                    success: function (data) {
                        if (data.code==0){
                            window.location.href="/common-config-data/index?pk="+willSendProjectKey;
                        }else {
                            alert("创建失败"+data.msg);
                        }
                    },
                    error: function (err) {
                        window.location.href="/project-info/index";
                    }
                });
            });

        });
    </script>
    <?php
} else {
    ?>
    <script type="text/javascript">
        var willSendProjectKey=$('.willSendProjectKey').text();
        window.location.href="/common-config-data/index?pk="+willSendProjectKey;
    </script>
<?php
}
?>