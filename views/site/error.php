<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Web服务器处理您的请求时出现错误，请阅读出错信息。
    </p>
    <p>
        如果您认为这是服务器错误，请与我们联系。
    </p>

</div>
