<?php

/**
 * 
 * 配置文件
 */

$config = array(
    'mysql' => array(
        'host' => '',
        'username' => '',
        'password' => '',
        'database' => ''
    ),
    'download_url_update_duration' => 5 * 60 * 60 // 下载链接有效期 (s)
);

function defaultGetData($key, $value)
{
    if (array_key_exists($key, $_POST)) {
        return addslashes($_POST[$key] == null ? $value : $_POST[$key]);
    } else {
        return addslashes($value);
    }
}
