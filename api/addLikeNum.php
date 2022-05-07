<?php

/**
 * 
 * 增加或删除收藏，向歌曲数据中添加用户名
 */

include './init_db.php';

$fileId = defaultGetData('fileId', '');
$username = defaultGetData('username', '');
$password = defaultGetData('password', '');

if (!$username || !$password || !$fileId) {
    die(json_encode(array(
        'code' => 900,
        'msg' => '参数缺失'
    )));
}



$result = mysqli_query($conn, "SELECT * FROM `copyrighted_music_user` WHERE (`username` = '$username' OR `email` = '$username') AND `password` = '$password' LIMIT 1");
if (!mysqli_num_rows($result)) {
    die(json_encode(array(
        'code' => 907,
        'msg' => '账号或密码错误'
    )));
}

// 判断文件是否存在
$result = mysqli_query($conn, "SELECT * FROM `copyrighted_music` WHERE `fileId` = '$fileId' LIMIT 1;");
if (!$result) {
    die(json_encode(array(
        'code' => 903,
        'msg' => '数据库出错'
    )));
}
if (!mysqli_num_rows($result)) {
    die(json_encode(array(
        'code' => 901,
        'msg' => '暂无查询结果'
    )));
}

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $collect_userList = $row['collect_userList'];
    $collect_userList_data = explode(',', $collect_userList);
    $like_num = (int)$row['like_num'];
    // 判断用户是否已经收藏
    if (in_array($username, $collect_userList_data)) {
        // 用户已经收藏，取消收藏
        $collect_userList_data = array_diff($collect_userList_data, [$username]);
        $collect_userList = '';
        foreach ($collect_userList_data as $value) {
            if ($value) {
                $collect_userList .= ',' . $value;
            }
        }
        $order_type = 1;
        $like_num--;
    } else {
        // 用户未收藏，增加收藏
        $collect_userList .= ',' . $username;
        $order_type = 0;
        $like_num++;
    }
    $sql = "UPDATE `copyrighted_music` SET `collect_userList` = '$collect_userList', `like_num` = $like_num WHERE `fileId` = '$fileId';";
    $result2 = mysqli_query($conn, $sql);
    if (!$result2) {
        die(json_encode(array(
            'code' => 903,
            'msg' => '数据库出错'
        )));
    }
    if ($order_type) {
        echo json_encode(array(
            'code' => 200,
            'msg' => '取消收藏成功',
            'type' => $order_type
        ));
    } else {
        echo json_encode(array(
            'code' => 200,
            'msg' => '收藏成功',
            'type' => $order_type
        ));
    }
}
