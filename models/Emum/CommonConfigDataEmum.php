<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-30
 * Time: 12:01
 */

namespace app\models\Emum;


class CommonConfigDataEmum
{
    public static $configLevel = [
        'private' => 1,
        'public'  => 2
    ];

    public static $valueType = [
        'string' => 1,
        'Json'   => 2
    ];


    public static $currentConfigStatusAdd = 1;
    public static $currentConfigStatusModify = 2;
    public static $currentConfigStatusDel = 3;
    public static $currentConfigStatusPublished = 4;
    public static $current_config_status = [
        1 => '新',
        2 => '改',
        3 => '删',
        4 => '',//当发布后 更新此状态
    ];
}