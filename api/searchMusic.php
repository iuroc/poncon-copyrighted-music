<?php

/**
 * 
 * 搜索音乐
 */
include './init_db.php';
$keyword = defaultGetData('keyword', '');
$page = defaultGetData('page', 0);
$pageSize = defaultGetData('pageSize', 30);
$username = defaultGetData('username', '');
$password = defaultGetData('password', '');
$offset = $page * $pageSize;

$keyword = addslashes($keyword);

if (!$keyword) {
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

$sql = "SELECT * FROM `copyrighted_music` WHERE `fileName` LIKE '%$keyword%' OR `fileId` LIKE '%$keyword%' ORDER BY `like_num` DESC LIMIT $pageSize OFFSET $offset;;";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die(json_encode(array(
        'code' => 903,
        'msg' => '数据库出错'
    )));
}



$data = array('code' => 200, 'msg' => '查询成功', 'result' => array());
$x = 0;
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $collect_userList = $row['collect_userList'];
    $collect_userList_data = explode(',', $collect_userList);
    if (in_array($username, $collect_userList_data)) {
        $row['hasLike'] = 1; // 用户已收藏
    } else {
        $row['hasLike'] = 0; // 用户未收藏
    }
    unset($row['collect_userList']);
    $row['like_num'] = count($collect_userList_data) - 1;
    $data['result'][$x++] = $row;
}
echo json_encode($data);
