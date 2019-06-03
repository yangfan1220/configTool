<?php

return [
    'table_suffix'=>'_config_data',
    'createConfigDataStorageTableDDL'=> <<<EOT
CREATE TABLE `%s` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key_value_mictime_md5` varchar(50) NOT NULL COMMENT '生成的MD5值,用于确保一个唯一，主要是用来生成唯一码从而不使用id，日志使用',
  `publish_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '发布状态：1：未发布;2:已发布',
  `current_config_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '当前配置状态：1：新增；2：修改；3：删除；',
  `config_level` tinyint(3) unsigned NOT NULL COMMENT '配置等级：1：私有的（只能被自己接收到）。2：公有的（设定的appid能接收到）',
  `key` varchar(128) NOT NULL COMMENT '配置名称',
  `value` text NOT NULL COMMENT '配置内容',
  `comment` varchar(512) NOT NULL COMMENT '配置注释',
  `value_type` tinyint(3) UNSIGNED NOT NULL COMMENT '配置内容的类型 1：string ；2：json',
  `create_name` varchar(50) NOT NULL COMMENT '创建该配置的姓名',
  `modify_name` varchar(50) NOT NULL DEFAULT '' COMMENT '修改该配置的姓名',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_value_mictime_md5` (`key_value_mictime_md5`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='基础配置表';
EOT,
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
];
