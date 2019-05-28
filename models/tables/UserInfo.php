<?php

namespace app\models\tables;

use Yii;

/**
 * This is the model class for table "user_info".
 *
 * @property int $id
 * @property string $user_id 用户ID
 * @property string $user_name 用户中文名
 * @property string $user_passwd 用户密码
 * @property string $user_mail 用户邮箱
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class UserInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'user_name', 'user_passwd', 'user_mail'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['user_id'], 'string', 'max' => 15],
            [['user_name', 'user_passwd', 'user_mail'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'user_name' => '用户中文名',
            'user_passwd' => '用户密码',
            'user_mail' => '用户邮箱',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
