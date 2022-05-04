<?php

/**
 * 
 * 获取音乐列表
 * 可根据分类筛选，按照页码和每页数量输出数据
 */


include './init_db.php';
$type = defaultGetData('type', '');
$page = defaultGetData('page', 0);
$pageSize = defaultGetData('pageSize', 30);
$offset = $page * $pageSize;

if ($type) {
    $sql = "SELECT * FROM `copyrighted_music` WHERE `musicType` LIKE '%$type%' ORDER BY `like_num` DESC LIMIT $pageSize OFFSET $offset;";
} else {
    $sql = "SELECT * FROM `copyrighted_music` ORDER BY `like_num` DESC LIMIT $pageSize OFFSET $offset;";
}
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
    $data['result'][$x++] = $row;
}
echo json_encode($data);
