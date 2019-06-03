<?php

namespace app\models\tables;

use app\models\common\TableConfirm;
use Yii;

/**
 * This is the model class for table "test_app_id_config_data".
 *
 * @property int $id
 * @property string $key_value_mictime_md5 生成的MD5值,用于确保一个唯一，主要是用来生成唯一码从而不使用id，日志使用
 * @property int $config_level 配置等级：1：私有的（只能被自己接收到）。2：公有的（设定的appid能接收到）
 * @property string $key 配置名称
 * @property string $value 配置内容
 * @property string $comment 配置注释
 * @property int $value_type 配置内容的类型 1：string ；2：json
 * @property string $create_name 创建该配置的姓名
 * @property string $modify_name 修改该配置的姓名
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class CommonConfigData extends \yii\db\ActiveRecord
{
    public static $tableName;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return static::$tableName ?: TableConfirm::willCreateTableName(Yii::$app->session['app_id']);
    }

    public static function setTableName($tableName)
    {
        static::$tableName = $tableName;
    }


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
            [['key_value_mictime_md5', 'config_level', 'key', 'value', 'comment', 'value_type', 'create_name'], 'required'],
            [['config_level', 'value_type'], 'integer'],
            [['value'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['key_value_mictime_md5', 'create_name', 'modify_name'], 'string', 'max' => 50],
            [['key'], 'string', 'max' => 128],
            [['comment'], 'string', 'max' => 512],
            [['key_value_mictime_md5'], 'unique'],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'key_value_mictime_md5' => 'MD5',
            'config_level'          => '配置等级',
            'key'                   => '配置名称',
            'value'                 => '配置内容',
            'comment'               => '配置注释',
            'value_type'            => '配置内容的类型',
            'create_name'           => '创建人',
            'modify_name'           => '修改人',
            'create_time'           => '创建时间',
            'update_time'           => '更新时间',
        ];
    }
}
