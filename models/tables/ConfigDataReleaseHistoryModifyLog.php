<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "config_data_release_history_modify_log".
 *
 * @property int $id
 * @property string $unique_id 当前版本的唯一id
 * @property string $app_id 项目(应用)唯一key
 * @property string $release_name 发布名称
 * @property int $modify_type 修改类型： 1：新增；2：修改；3：删除
 * @property string $key 配置名称
 * @property string $old_value 配置的旧内容
 * @property string $new_value 配置的新内容
 */
class ConfigDataReleaseHistoryModifyLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config_data_release_history_modify_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['unique_id', 'app_id', 'release_name', 'modify_type', 'key',], 'required'],
            [['modify_type'], 'integer'],
            [['old_value', 'new_value'], 'string'],
            [['unique_id', 'release_name', 'key'], 'string', 'max' => 128],
            [['app_id'], 'string', 'max' => 50],
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
            'modify_type' => '修改类型： 1：新增；2：修改；3：删除',
            'key' => '配置名称',
            'old_value' => '配置的旧内容',
            'new_value' => '配置的新内容',
        ];
    }
}
