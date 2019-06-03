<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-31
 * Time: 10:46
 */

namespace app\models\service;

use yii\web\NotFoundHttpException;

class ProjectInfoService
{
    public static function validate($data)
    {
        if(empty($data['ProjectInfo']['app_principal_id']) || empty($data['app_manage_ids'])){
            throw new NotFoundHttpException('有空值存在');
        }
        if(!in_array($data['ProjectInfo']['app_principal_id'],$data['app_manage_ids'])){
            throw new NotFoundHttpException('项目管理员必须包含应用负责人 也就是应用负责人默认拥有项目管理员权限');
        }
    }
}