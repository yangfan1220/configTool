<?php

return [
    //配置工具基础数据的表
    'db'=>[
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=config_tool_storage',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',

        // Schema cache options (for production environment)
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 60,
        'schemaCache' => 'cache',
    ],
    //发布的数据配置表  定时任务会取值并推送的表
    'db2'=>[
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=config_data_storage',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',

        // Schema cache options (for production environment)
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 60,
        'schemaCache' => 'cache',
    ],
    'redis' => [
        'class' => 'yii\redis\Connection',
        /*********************************设置为空的原因在于需要根据配置动态的设置redis连接**********************************************************/
        'hostname' => 'default value !!  if  you  see this that  you should check current app_id config of redis info  is right???',
//        'port' => 6379,
//        'database' => 0,
//        'password'=>'foobared',
        'connectionTimeout'=>'3',
        'dataTimeout'=>'3',
    ],
       ];
