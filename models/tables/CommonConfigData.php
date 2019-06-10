<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "common_config_data".
 *
 * @property int $id
 * @property string $app_id 项目(应用)唯一key
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
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'common_config_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['app_id', 'config_level', 'key', 'value', 'comment', 'value_type', 'create_name'], 'required'],
            [['config_level', 'value_type'], 'integer'],
            [['value'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['app_id', 'create_name', 'modify_name'], 'string', 'max' => 50],
            [['key'], 'string', 'max' => 128],
            [['comment'], 'string', 'max' => 512],
            [['app_id', 'key'], 'unique', 'targetAttribute' => ['app_id', 'key']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_id' => '项目(应用)唯一key',
            'config_level' => '配置等级',
            'key' => '配置名称',
            'value' => '配置内容',
            'comment' => '配置注释',
            'value_type' => '配置内容的类型',
            'create_name' => '创建人',
            'modify_name' => '修改人',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
