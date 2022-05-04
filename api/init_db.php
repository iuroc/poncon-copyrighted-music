<?php

/**
 * 
 * 初始化数据库连接
 */
include './config.php';
$conn = mysqli_connect($config['mysql']['host'], $config['mysql']['username'], $config['mysql']['password'], $config['mysql']['database']);
