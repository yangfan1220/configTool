<?php

return [
    'table_suffix'=>'_config_data',
    'createConfigDataStorageTableDDL'=> <<<EOT
  CREATE TABLE IF NOT EXISTS `%s` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(128) NOT NULL COMMENT '配置名称',
  `value` text NOT NULL COMMENT '配置内容',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='发布表';
EOT,
    'adminEmail' => 'fan.yang@mfashion.com.cn',
//    'senderEmail' => 'alert@mfashion.com.cn',
//    'senderName' => 'alert@mfashion.com.cn',
];
