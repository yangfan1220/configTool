<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_info".
 *
 * @property int $id
 * @property string $project_name 项目名称
 * @property string $project_key 项目唯一key：用于业务端获取配置信息
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class ProjectInfo extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db2;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_name', 'project_key'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['project_name', 'project_key'], 'string', 'max' => 50],
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
            'id'           => 'ID',
            'project_name' => '项目的名称',
            'project_key'  => '项目的key',
            'create_time'  => '创建时间',
            'update_time'  => '更新时间',
        ];
    }
}
