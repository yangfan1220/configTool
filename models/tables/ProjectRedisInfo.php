<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "project_redis_info".
 *
 * @property int $id ID
 * @property string $project_app_id 项目(应用)唯一key
 * @property string $redis_host redis主机地址
 * @property int $redis_port redis主机端口
 * @property int $redis_database_id redis数据库
 * @property string $redis_password redis密码
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class ProjectRedisInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_redis_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_app_id', 'redis_host'], 'required'],
            [['redis_port', 'redis_database_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['project_app_id', 'redis_host', 'redis_password'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_app_id' => '项目(应用)唯一key',
            'redis_host' => 'redis主机地址',
            'redis_port' => 'redis主机端口',
            'redis_database_id' => 'redis数据库',
            'redis_password' => 'redis密码',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
