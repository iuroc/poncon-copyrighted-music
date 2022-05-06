<?php

/**
 * 
 * 用户登录
 */

include './init_db.php';
$username = defaultGetData('username', '');
$password = defaultGetData('password', '');

if (!$username || !$password) {
    die(json_encode(array(
        'code' => 999,
        'msg' => '参数缺失'
    )));
}

$result = mysqli_query($conn, "SELECT * FROM `copyrighted_music_user` WHERE (`username` = '$username' OR `email` = '$username') AND `password` = '$password' LIMIT 1");
if (!mysqli_num_rows($result)) {
    die(json_encode(array(
        'code' => 1000,
        'msg' => '账号或密码错误'
    )));
}


$data = array(
    'code' => 200,
    'msg' => '登录成功',
    'result' => array()
);

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $data['result'] = $row;
    break;
}

echo json_encode($data);
