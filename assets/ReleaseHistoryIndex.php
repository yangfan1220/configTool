<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-10
 * Time: 10:11
 */

namespace app\assets;
use yii\web\AssetBundle;


class ReleaseHistoryIndex extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        'js/release-history-index.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}