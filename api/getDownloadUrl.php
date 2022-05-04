<?php

/**
 * 
 * 获取文件下载链接
 */

include './init_db.php';

$fileId = defaultGetData('fileId', '');

// 判断参数是否缺失
if (!$fileId) {
    die(json_encode(array(
        'code' => 900,
        'msg' => '参数缺失'
    )));
}

// 判断文件是否存在
$result = mysqli_query($conn, "SELECT `fileId` FROM `copyrighted_music` WHERE `fileId` = '$fileId' LIMIT 1;");
if (!mysqli_num_rows($result)) {
    die(json_encode(array(
        'code' => 901,
        'msg' => '暂无查询结果'
    )));
}

// 判断数据库的下载连接是否过期
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $url_update_time = (int)($row['url_update_time']);
    echo $url_update_time;
}
