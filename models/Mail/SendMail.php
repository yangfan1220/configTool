<?php
/**
 * Created by PhpStorm.
 * User: 杨帆
 * Date: 2019-07-08
 * Time: 16:17
 */

namespace app\models\Mail;


class SendMail
{
    public static function send()
    {
        $mailMessages = MailMessageStruct::$mailMessages;
        if (!empty($mailMessages)) {
            $messages = [];
            $users = [
                \Yii::$app->params['adminEmail']
            ];
            if (\Yii::$app->params['adminEmail'] != \Yii::$app->session['userMail']) {
                array_push($users, \Yii::$app->session['userMail']);
            }
            foreach ($users as $user) {
                $messages[] = \Yii::$app->mailer->compose('mailTemplate', ['imageFileName' => './img/logo.png', 'errorMessages' => $mailMessages])
                    ->setFrom(['alert@mfashion.com.cn' => '配置工具报警账号'])
                    ->setTo($user)
                    ->setSubject('配置工具发布至redis出现异常');
            }
            \Yii::$app->mailer->sendMultiple($messages);
        }
    }
}