<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "jurisdiction_info".
 *
 * @property int $id
 * @property string $app_id 项目(应用)唯一key
 * @property int $user_id 用户ID
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class JurisdictionInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jurisdiction_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['app_id', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['app_id'], 'string', 'max' => 50],
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
            'user_id' => '用户ID',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
