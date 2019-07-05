<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-07-03
 * Time: 17:23
 */

namespace app\models\common;

use Yii;

class SetValueOfCommonModel
{

    public static function joinDataStorageTableName($tableName)
    {
        return $tableName.'_data_storage';
    }

    public static function TheTableExist($tableName)
    {
        $sql=sprintf(Yii::$app->params['createConfigDataStorageTableDDL'],$tableName);
        Yii::$app->db2->createCommand($sql)->execute();
    }
}