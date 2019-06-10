<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-06-10
 * Time: 17:10
 */

namespace app\models\common;

use app\models\tables\ConfigDataReleaseHistoryAllLog;
use function foo\func;
use Yii;

class ReleaseStatusModel
{
    private static function getReleaseStatus()
    {
        return ConfigDataReleaseHistoryAllLog::findBySql('SELECT `key`,`value` from  `config_data_release_history_all_log` WHERE `app_id`=:app_id and `release_name`=(SELECT `release_name` FROM `config_data_release_history` WHERE `app_id`=:app_id ORDER BY `id` DESC LIMIT 1)', [':app_id' => Yii::$app->session['app_id']])->asArray()->all();
    }

    public static function formatReleaseStatus()
    {
        $data = static::getReleaseStatus();
        array_walk($data,function (&$val,&$key){
             $key=$val['key'];
             $val=md5($val['key'].$val['value']);
        });
       return $data;
    }

}