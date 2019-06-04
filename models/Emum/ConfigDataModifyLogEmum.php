<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-03
 * Time: 18:55
 */

namespace app\models\Emum;


class ConfigDataModifyLogEmum
{
    public static $modifyTypeAdd = 1;
    public static $modifyTypeModify = 2;
    public static $modifyTypeDel = 3;
    public static $modifyType = [
        1 => '新增',
        2 => '修改',
        3 => '删除',
    ];
}