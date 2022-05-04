<?php

/**
 * 
 * 配置文件
 */
header('Content-type: application/json');
$config = array(
    'mysql' => array(
        'host' => '',
        'username' => '',
        'password' => '',
        'database' => ''
    ),
    'download_url_update_duration' => 5 * 60 * 60, // 下载链接有效期 (s)
    '123pan' => array(
        'authorization' => '' // 123云盘的身份令牌
    )
);

function defaultGetData($key, $value)
{
    if (array_key_exists($key, $_POST)) {
        return addslashes($_POST[$key] == null ? $value : $_POST[$key]);
    } else {
        return addslashes($value);
    }
}
