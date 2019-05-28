<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<div class="site-login col-lg-3 col-lg-offset-4">

    <form action="/site/login" method="post" name="LoginForm">
        <div class="form-group">
            <label for="exampleInputEmail1">用户名</label>
            <input type="text" name="username" class="form-control" id="exampleInputEmail1" placeholder="">
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken(true)?>">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">密码</label>
            <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>
</div>