<?php
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
(new yii\web\Application($config))->run();


/**
 * 1.new yii\web\Application 实例化不存在的类调用8行文件的22行方法（该方法主要是根据类名寻找文件位置，并include过来，然后实例化
 */
