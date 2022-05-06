<?php

/**
 * 
 * 获取验证码
 */


// 验证码用时间戳取md5后，打乱顺序，取前6个
// 一旦验证成功，删除数据库数据
// 提交邮箱生成验证码，如果该邮箱已经存在，判断创建时间到当前时间有没有超过60秒，有的话，更新数据，提示发送成功，没有的话，提示太频繁
// 验证时，提交邮箱和验证码，数据库判断两者，如果找到匹配，再判断创建时间，有没有超过5分钟
// 如果匹配失败，则提示验证码错误，如果过期，则提示验证码过期，如果正确，则提示正确并删除验证码

include './init_db.php';
include './sendCode.php';

$email = defaultGetData('email', '');
if (!$email) {
    die(json_encode(array(
        'code' => 999,
        'msg' => '参数缺失'
    )));
}


// 生成验证码
$verCode = substr(str_shuffle(md5(time())), 0, 6);
$content = '<div style="background-color: rgba(230, 230, 230, 0.5); border-radius: 20px; text-align: center; max-width: 500px; padding: 40px 20px; margin: auto; border: 1px solid rgba(140, 140, 140, 0.2); box-shadow: 2px 2px 5px rgba(140, 140, 140, 0.5);">
                <h1 style="margin: 0 auto 15px;">无忧音乐网</h1>
                <div style="font-size: 20px;">
                    <div style="margin-bottom: 15px;">您的验证码是：<span style="color: blue; font-weight: bold;">' . $verCode . '</span></div>
                    <div>有效期5分钟</div>
                </div>
            </div>';

// 判断用户有没有被注册过

$result = mysqli_query($conn, "SELECT * FROM `copyrighted_music_user` WHERE `email` = '$email' LIMIT 1");

if (mysqli_num_rows($result)) {
    // 已经被注册了
    die(json_encode(array(
        'code' => 996,
        'msg' => '邮箱已被注册'
    )));
}


// 查找数据库中是否已经有该邮箱验证码，并判断有没有超过60秒
$result = mysqli_query($conn, "SELECT * FROM `copyrighted_music_verCode` WHERE `email` = '$email' LIMIT 1");


if (!mysqli_num_rows($result)) {
    // 数据库不存在该邮箱验证码数据，可以发送验证码
    $status = sendCode($config, $email, '无忧音乐网的验证码', $content);
    if (!$status) {
        die(json_encode(array(
            'code' => 998,
            'msg' => '验证码发送失败'
        )));
    }
    $create_time = time();
    $r = mysqli_query($conn, "INSERT INTO `copyrighted_music_verCode` (`email`, `verCode`, `create_time`) VALUES ('$email', '$verCode', '$create_time')");
    if ($r) {
        echo json_encode(array(
            'code' => 200,
            'msg' => '发送成功'
        ));
    }
} else {
    // 服务器存在验证码，判断有没有超过60秒
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $create_time = (int)$row['create_time'];
        if ((time() - $create_time) > 60) {
            // 时间超过60秒，可以再次发送了
            $status = sendCode($config, $email, '无忧音乐网的验证码', $content);
            if (!$status) {
                die(json_encode(array(
                    'code' => 998,
                    'msg' => '验证码发送失败'
                )));
            }
            $create_time = time();
            // 更新验证码和创建时间
            $r = mysqli_query($conn, "UPDATE `copyrighted_music_verCode` SET `create_time` = '$create_time', `verCode` = '$verCode' WHERE `email` = '$email' LIMIT 1");
            if ($r) {
                echo json_encode(array(
                    'code' => 200,
                    'msg' => '发送成功'
                ));
            }
        } else {
            echo json_encode(array(
                'code' => 997,
                'msg' => '发送频率过快，请' . (60 - (time() - $create_time)) . '秒后重试'
            ));
        }
        break;
    }
}
