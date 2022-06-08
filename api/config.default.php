<?php

/**
 * 
 * 配置文件
 */
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods:POST,GET,OPTIONS');
header('Access-Control-Allow-Headers:Authorization,lpy');
$config = array(
    'mysql' => array(
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'ponconsoft'
    ),
    'download_url_update_duration' => 5 * 60 * 60, // 下载链接有效期 (s),
    '123pan' => array(
        "username" => "", // 123云盘账号
        "password" => "" // 123云盘密码
    ),
    'smtp' => array(
        'smtp_host' => 'smtp.163.com', // SMTP服务器
        'smtp_username' => '', // SMTP账号
        'smtp_password' => '', // SMTP密码
        'smtp_secure' => 'ssl', // 连接加密方法
        'smtp_port' => 994, // SMTP端口
        'sendFrom' => '', // 发件人邮箱
        'sendFromName' => '无忧音乐网' // 发件人名称
    )
);


$config['123pan']['authorization'] = get_authorization($config);

function defaultGetData($key, $value)
{
    if (array_key_exists($key, $_POST)) {
        return addslashes($_POST[$key] == null ? $value : $_POST[$key]);
    } else {
        return addslashes($value);
    }
}

function get_authorization($config)
{
    $data = json_decode(file_get_contents('authorization.json'), true);
    $duration_hour = $data['duration_hour'];
    $update_time = $data['update_time'];
    if (time() - $update_time > $duration_hour * 60 * 60) {
        // 令牌过期 更新令牌
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => "content-type: application/json",
                'content' => json_encode(array(
                    "passport" => $config['123pan']['username'],
                    "password" => $config['123pan']['password']
                )),
                'timeout' => 900
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents('https://www.123pan.com/b/api/user/sign_in', false, $context);
        $authorization = 'Bearer ' . json_decode($result, true)['data']['token'];
        if ($authorization) {
            $data['authorization'] = $authorization;
            $data['update_time'] = time();
            file_put_contents('authorization.json', json_encode($data));
        }
    }
    return $data['authorization'];
}

