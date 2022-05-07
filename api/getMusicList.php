<?php

/**
 * 
 * 获取音乐列表
 * 可根据分类筛选，按照页码和每页数量输出数据
 * 按照收听人数排序
 * 
 * 待优化：传入用户名，遍历音乐列表时，同时输出该歌曲用户是否收藏，前端发现用户已经收藏，则显示为星标
 */


include './init_db.php';
$type = defaultGetData('type', '');
$page = defaultGetData('page', 0);
$pageSize = defaultGetData('pageSize', 30);
$offset = $page * $pageSize;

if ($type == 'all') {
    $sql = "SELECT * FROM `copyrighted_music` ORDER BY `listen_num` DESC LIMIT $pageSize OFFSET $offset;";
} else {
    $sql = "SELECT * FROM `copyrighted_music` WHERE `musicType` LIKE '%$type%' ORDER BY `listen_num` DESC LIMIT $pageSize OFFSET $offset;";
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
