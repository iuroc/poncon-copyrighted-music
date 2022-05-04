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
