<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "sada".
 *
 * @property int $id
 * @property string $key 配置名称
 * @property string $value 配置内容
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class CommonDataStorage extends \yii\db\ActiveRecord
{
    private static $tempTableName;

    public static function setTableName($tableName)
    {
        static::$tempTableName=$tableName;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return static::$tempTableName;
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
            [['key', 'value'], 'required'],
            [['value'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['key'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => '配置名称',
            'value' => '配置内容',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
