<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "config_data_modify_log".
 *
 * @property int $id
 * @property int $modify_type 修改类型： 1：新增；2：修改；3：删除
 * @property string $app_id 项目(应用)唯一key
 * @property string $key 配置名称
 * @property string $old_value 配置的旧内容
 * @property string $new_value 配置的新内容
 * @property string $comment 配置注释
 * @property string $create_name 创建该记录的邮箱，也就是修改人
 * @property string $create_time 创建时间
 */
class ConfigDataModifyLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config_data_modify_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['modify_type', 'app_id', 'key', 'comment', 'create_name'], 'required'],
            [['modify_type'], 'integer'],
            [['old_value', 'new_value'], 'string'],
            [['create_time'], 'safe'],
            [['app_id', 'create_name'], 'string', 'max' => 50],
            [['key'], 'string', 'max' => 128],
            [['comment'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'modify_type' => '修改类型： 1：新增；2：修改；3：删除',
            'app_id' => '项目(应用)唯一key',
            'key' => '配置名称',
            'old_value' => '配置的旧内容',
            'new_value' => '配置的新内容',
            'comment' => '配置注释',
            'create_name' => '创建该记录的邮箱，也就是修改人',
            'create_time' => '创建时间',
        ];
    }
}
