<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "app_id_key_relation".
 *
 * @property int $id
 * @property string $key_value_mictime_md5 *_config_data表的MD5值,用于关联此表 预留
 * @property string $key 配置名称
 * @property string $app_id 项目(应用)唯一key
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class AppIdKeyRelation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'app_id_key_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key_value_mictime_md5', 'key', 'app_id'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['key_value_mictime_md5', 'app_id'], 'string', 'max' => 50],
            [['key'], 'string', 'max' => 128],
            [['key', 'app_id'], 'unique', 'targetAttribute' => ['key', 'app_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key_value_mictime_md5' => '*_config_data表的MD5值,用于关联此表 预留',
            'key' => '配置名称',
            'app_id' => '项目(应用)唯一key',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
