<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_info".
 *
 * @property int $id
 * @property string $project_name 项目名称
 * @property string $project_key 项目唯一key：用于业务端获取配置信息
 * @property string $redis_host redis主机地址
 * @property int $redis_port redis主机端口
 * @property int $redis_database_id redis数据库
 * @property string $redis_password redis密码
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class ProjectInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_info';
    }

    /**
     * @return object|\yii\db\Connection|null
     * @throws \yii\base\InvalidConfigException
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
            [['project_name', 'project_key', 'redis_host'], 'required'],
            [['redis_port', 'redis_database_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['project_name', 'project_key', 'redis_host', 'redis_password'], 'string', 'max' => 50],
            [['project_name'], 'unique'],
            [['project_key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_name' => '项目名称',
            'project_key' => '项目唯一key：用于业务端获取配置信息',
            'redis_host' => 'redis主机地址',
            'redis_port' => 'redis主机端口',
            'redis_database_id' => 'redis数据库',
            'redis_password' => 'redis密码',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
