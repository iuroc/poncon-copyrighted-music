<?php

/**
 * 
 * 用户注册
 */

include './init_db.php';

$username = defaultGetData('username', '');
$password = defaultGetData('password', '');
$email = defaultGetData('email', '');
$verCode = defaultGetData('verCode', '');
$register_time = time();

if (!$username || !$password || !$email || !$verCode) {
    die(json_encode(array(
        'code' => 900,
        'msg' => '参数缺失'
    )));
}

// 判断用户有没有被注册过

$result = mysqli_query($conn, "SELECT * FROM `copyrighted_music_user` WHERE (`username` = '$username' OR `email` = '$email') LIMIT 1");

if (mysqli_num_rows($result)) {
    // 已经被注册了
    die(json_encode(array(
        'code' => 904,
        'msg' => '用户名或邮箱已被注册'
    )));
}


// 判断验证码是否正确

$result = mysqli_query($conn, "SELECT * FROM `copyrighted_music_verCode` WHERE (`email` = '$email' AND `verCode` = '$verCode') LIMIT 1");
if (!mysqli_num_rows($result)) {
    // 验证码错误
    die(json_encode(array(
        'code' => 905,
        'msg' => '验证码错误'
    )));
}
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $create_time = (int)$row['create_time'];
    $limit_time = 10 * 60; // 验证码过期时间(s)
    if ((time() - $create_time) > $limit_time) {
        die(json_encode(array(
            'code' => 906,
            'msg' => '验证码过期'
        )));
    }
    break;
}

$result = mysqli_query($conn, "INSERT INTO `copyrighted_music_user` (`username`, `password`, `email`, `register_time`) VALUES ('$username', '$password', '$email', '$register_time')");
if (!$result) {
    die(json_encode(array(
        'code' => 903,
        'msg' => '数据库出错'
    )));
}

// 注册成功，删除验证码
mysqli_query($conn, "DELETE FROM `copyrighted_music_verCode` WHERE `email` = '$email' LIMIT 1");

echo json_encode(array(
    'code' => 200,
    'msg' => '注册成功'
));
