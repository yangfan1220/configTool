<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "config_data_release_history".
 *
 * @property int $id
 * @property string $unique_id 当前版本的唯一id
 * @property string $app_id 项目(应用)唯一key
 * @property int $current_record_style 当前发布记录的方式:1:普通发布；2：回滚
 * @property string $release_name 发布名称
 * @property string $comment 发布备注
 * @property string $create_name 创建该发布的姓名
 * @property string $create_time 创建该发布的时间
 */
class ConfigDataReleaseHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config_data_release_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['unique_id', 'app_id', 'current_record_style', 'release_name', 'create_name'], 'required'],
            [['current_record_style'], 'integer'],
            [['create_time'], 'safe'],
            [['unique_id', 'release_name'], 'string', 'max' => 128],
            [['app_id', 'create_name'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 512],
            [['unique_id'], 'unique'],
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
            'current_record_style' => '当前发布记录的方式:1:普通发布；2：回滚',
            'release_name' => '发布名称',
            'comment' => '发布备注',
            'create_name' => '创建该发布的姓名',
            'create_time' => '创建该发布的时间',
        ];
    }
}
