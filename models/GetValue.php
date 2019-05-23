<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-22
 * Time: 17:20
 */

namespace app\models;


use yii\base\Model;
use yii\base\DynamicModel;

class GetValue extends Model
{
    public function rules()
    {
        return [
            [['app_id', 'key'], 'required'],
            [['app_id', 'key'], 'trim'],
            [['app_id'], 'string', 'max' => 50],
            [['key'], 'string', 'max' => 128],
            [['app_id'], 'match', 'pattern' => '/^[0-9a-zA-Z_]{1,}$/', 'message' => 'App Id仅支持英文数字下划线'],
            [['app_id'], 'in', 'range' => ProjectInfo::find()->select('project_key')->asArray()->column()],
        ];
    }

    public function validateData($data)
    {
        return DynamicModel::validateData($data, $this->rules());
    }
}