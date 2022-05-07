<?php

/**
 * 
 * 获取用户收藏列表
 */
include './init_db.php';
$page = defaultGetData('page', 0);
$pageSize = defaultGetData('pageSize', 30);
$username = defaultGetData('username', 30);
$password = defaultGetData('password', '');
$offset = $page * $pageSize;

if (!$username || !$password) {
    die(json_encode(array(
        'code' => 900,
        'msg' => '参数缺失'
    )));
}

if ($username && $password) {
    $result = mysqli_query($conn, "SELECT * FROM `copyrighted_music_user` WHERE (`username` = '$username' OR `email` = '$username') AND `password` = '$password' LIMIT 1");
    if (!mysqli_num_rows($result)) {
        die(json_encode(array(
            'code' => 907,
            'msg' => '账号或密码错误'
        )));
    }
}

$sql = "SELECT * FROM `copyrighted_music`;";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die(json_encode(array(
        'code' => 903,
        'msg' => '数据库出错'
    )));
}

$data = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $collect_userList = $row['collect_userList'];
    $collect_userList_data = explode(',', $collect_userList);
    // 判断用户是否已经收藏
    if (in_array($username, $collect_userList_data)) {
        array_push($data, $row);
    }
}

$cou = $offset + $pageSize;

if ($cou > count($data)) {
    $cou = count($data);
}

$newData = array();
for ($x = $offset; $x < $cou; $x++) {
    array_push($newData, $data[$x]);
}

echo json_encode(array(
    'code' => 200,
    'mag' => '获取成功',
    'result' => $newData
));
