<?php

/**
 * 
 * 初始化数据库连接
 */
include './config.php';
$conn = mysqli_connect(
    $config['mysql']['host'],
    $config['mysql']['username'],
    $config['mysql']['password'],
    $config['mysql']['database']
);

// 空间大小以KB为单位，用户不分等级，容量越买越多
$sql_createDB = "CREATE TABLE IF NOT EXISTS `copyrighted_music_user` (" .
    "`id` int(10) NOT NULL AUTO_INCREMENT," .
    "`username` TEXT comment \"用户名\"," .
    "`password` TEXT comment \"密码\"," .
    "`email` TEXT comment \"邮箱\"," .
    "`register_time` CHAR(10) comment \"注册时间\"," .
    "PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($conn, $sql_createDB);


$sql_createDB = "CREATE TABLE IF NOT EXISTS `copyrighted_music_verCode` (" .
    "`id` int(10) NOT NULL AUTO_INCREMENT," .
    "`verCode` CHAR(6) comment \"验证码\"," .
    "`email` TEXT comment \"邮箱\"," .
    "`create_time` CHAR(10) comment \"创建时间\"," .
    "PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($conn, $sql_createDB);
