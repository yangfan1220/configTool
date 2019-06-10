<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-04
 * Time: 12:31
 */

namespace app\assets;

use yii\web\AssetBundle;


class CommonConfigDataIndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        'js/common-config-data-index.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}