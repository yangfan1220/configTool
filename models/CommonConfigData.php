<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "b59c67bf196a4758191e42f76670ceba_config_data".
 *
 * @property int $id
 * @property string $key 配置名称
 * @property string $value 配置内容
 * @property string $comment 配置注释
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class CommonConfigData extends \yii\db\ActiveRecord
{
    private static $tableName;

    public static function setTableName($projectKey)
    {
        self::$tableName = TableConfirm::willCreateTableName($projectKey);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::$tableName;
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key'], 'match', 'pattern' => '/^[a-zA-Z0-9]{1,}$/'],
            [['key', 'value', 'comment'], 'required'],
            [['value'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['key'], 'string', 'max' => 128],
            [['comment'], 'string', 'max' => 512],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'key'         => '配置名称',
            'value'       => '配置内容',
            'comment'     => '配置注释',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
