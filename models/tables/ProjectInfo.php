<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "project_info".
 *
 * @property int $id
 * @property string $app_name 项目(应用)名称
 * @property string $app_id 项目(应用)唯一key
 * @property string $app_principal_id 应用负责人id
 * @property int $release_status 发布状态: 0、未发布；1、回滚；2、已发布
 * @property string $will_rollback_release_name 如果发布状态为回滚，那么该字段是将要回滚的发布名称
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['app_name', 'app_id', 'app_principal_id'], 'required'],
            [['release_status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['app_name', 'app_id'], 'string', 'max' => 50],
            [['app_principal_id'], 'string', 'max' => 15],
            [['will_rollback_release_name'], 'string', 'max' => 128],
            [['app_name'], 'unique'],
            [['app_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_name' => '项目(应用)名称',
            'app_id' => '项目(应用)唯一key',
            'app_principal_id' => '应用负责人id',
            'release_status' => '发布状态: 0、未发布；1、回滚；2、已发布',
            'will_rollback_release_name' => '如果发布状态为回滚，那么该字段是将要回滚的发布名称',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
