<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-08
 * Time: 13:58
 */

namespace app\controllers;


use yii\web\Controller;

class ReleaseHistoryController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}