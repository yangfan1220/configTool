<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "config_data_release_history_all_log".
 *
 * @property int $id
 * @property string $unique_id 当前版本的唯一id
 * @property string $app_id 项目(应用)唯一key
 * @property string $release_name 发布名称
 * @property string $key 配置名称
 * @property string $value 配置内容
 * @property int $config_level_log 配置等级：1：私有的（只能被自己接收到）。2：公有的（设定的appid能接收到）
 * @property string $comment_log 配置注释
 * @property int $value_type_log 配置内容的类型 1：string ；2：json
 * @property string $create_name_log 创建该配置的姓名
 * @property string $modify_name_log 修改该配置的姓名
 * @property string $create_time_log 创建时间
 * @property string $update_time_log 更新时间
 */
class ConfigDataReleaseHistoryAllLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config_data_release_history_all_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['unique_id', 'app_id', 'release_name', 'key', 'value', 'config_level_log', 'comment_log', 'value_type_log', 'create_time_log', 'update_time_log'], 'required'],
            [['value'], 'string'],
            [['config_level_log', 'value_type_log'], 'integer'],
            [['create_time_log', 'update_time_log'], 'safe'],
            [['unique_id', 'release_name', 'key'], 'string', 'max' => 128],
            [['app_id', 'create_name_log', 'modify_name_log'], 'string', 'max' => 50],
            [['comment_log'], 'string', 'max' => 512],
            [['unique_id', 'key'], 'unique', 'targetAttribute' => ['unique_id', 'key']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'unique_id' => '当前版本的唯一id',
            'app_id' => '项目(应用)唯一key',
            'release_name' => '发布名称',
            'key' => '配置名称',
            'value' => '配置内容',
            'config_level_log' => '配置等级：1：私有的（只能被自己接收到）。2：公有的（设定的appid能接收到）',
            'comment_log' => '配置注释',
            'value_type_log' => '配置内容的类型 1：string ；2：json',
            'create_name_log' => '创建该配置的姓名',
            'modify_name_log' => '修改该配置的姓名',
            'create_time_log' => '创建时间',
            'update_time_log' => '更新时间',
        ];
    }
}
