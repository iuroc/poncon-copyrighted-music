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

// 判断数据库的下载连接是否过期
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $url_update_time = (int)($row['url_update_time']);
    if (time() - $url_update_time < $config['download_url_update_duration']) {
        // 链接还在有效期内
        echo json_encode(array(
            'code' => 200,
            'msg' => '查询成功(缓存)',
            'result' => $row
        ));
    } else {
        // 链接过期，重新获取
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => "content-type: application/json\nauthorization: " . $config['123pan']['authorization'],
                'content' => json_encode(array(
                    "driveId" => 0,
                    "etag" => $row['etag'],
                    "fileId" => $row['fileId'],
                    "s3keyFlag" => $row['s3keyFlag'],
                    "type" => 0,
                    "fileName" => $row['fileName'],
                    "size" => $row['size']
                )),
                'timeout' => 900
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );
        $context = stream_context_create($options);
        $result2 = file_get_contents('https://www.123pan.com/a/api/file/download_info', false, $context);
        $download_url = json_decode($result2, true)['data']['DownloadUrl'];
        if (!$download_url) {
            die(json_encode(array(
                'code' => 902,
                'msg' => '服务器出错'
            )));
        }
        // 更新数据库下载链接
        $download_url = addslashes($download_url);
        $result3 = mysqli_query($conn, "UPDATE `copyrighted_music` SET `downloadUrl` = '$download_url', `url_update_time` = " . time() . " WHERE `fileId` = '$fileId' LIMIT 1;");
        if (!$result3) {
            die(json_encode(array(
                'code' => 903,
                'msg' => '数据库出错，' . mysqli_error($conn)
            )));
        }
        $row['downloadUrl'] = $download_url;
        echo json_encode(array(
            'code' => 200,
            'msg' => '查询成功(非缓存)',
            'result' => $row
        ));
    }
}
