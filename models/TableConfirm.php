<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-17
 * Time: 19:16
 */

namespace app\models;


class TableConfirm extends \yii\db\ActiveRecord
{
    /**
     * @return object|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDb()
    {
        return \Yii::$app->get('db2');
    }

    public static function tableIsExist($projectKey)
    {
        $tableName = static::willCreateTableName($projectKey);
        $commandRe = \Yii::$app->db2->createCommand("show table status like '$tableName'")->queryAll();
        return empty($commandRe) ? false : true;
    }

    /**
     * 修改表名生成规则可以修改这里
     * @param $projectKey
     * @return string
     */
    public static function willCreateTableName($projectKey)
    {
        $tableSuffix = \Yii::$app->params['table_suffix'];
        return $projectKey . $tableSuffix;
    }

    public static function createTable($projectKey)
    {
        $createTableDDL = sprintf(\Yii::$app->params['createConfigDataStorageTableDDL'], static::willCreateTableName($projectKey));
        return \Yii::$app->db2->createCommand($createTableDDL)->execute();
    }
}