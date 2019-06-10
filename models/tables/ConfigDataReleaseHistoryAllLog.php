<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "config_data_release_history_all_log".
 *
 * @property int $id
 * @property string $app_id 项目(应用)唯一key
 * @property string $release_name 发布名称
 * @property string $key 配置名称
 * @property string $value 配置内容
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
            [['app_id', 'release_name', 'key', 'value'], 'required'],
            [['value'], 'string'],
            [['app_id'], 'string', 'max' => 50],
            [['release_name', 'key'], 'string', 'max' => 128],
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
            'release_name' => '发布名称',
            'key' => '配置名称',
            'value' => '配置内容',
        ];
    }
}
