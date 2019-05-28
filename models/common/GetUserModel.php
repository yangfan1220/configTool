<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-05-27
 * Time: 16:54
 */

namespace app\models\common;
use app\models\tables\ProjectInfo;
use app\models\tables\UserInfo;


class GetUserModel
{
    //未来的应用负责人信息返回   修改这里
    public static function getAppPrincipalInfo(){
        $userInfos=UserInfo::find()->asArray()->all();
        $returnData=[];
        foreach ($userInfos as $key=>$userInfo){
            $returnData[$userInfo['id']]=$userInfo['user_name'].' | '.$userInfo['user_mail'];
        }
        return $returnData;
    }

    public static function getUserInfoByID($userID){
        return UserInfo::find()->select(['id','user_name','user_mail'])->addParams(['id'=>$userID])->asArray()->one();
    }

    public static function getProjectInfoByAppId($appId){
        $projectInfo=ProjectInfo::find()->select(['app_id','app_name','app_principal_id'])->where(['app_id'=>$appId])->asArray()->one();
        return $projectInfo;
    }
}