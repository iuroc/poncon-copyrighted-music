<?php

/**
 * 
 * 把云盘列表数据和原站采集的数据合并后插入到数据表中
 */

$config = json_decode(file_get_contents('./config.json'), true);
$mysql = $config['mysql'];
$conn = mysqli_connect($mysql['host'], $mysql['username'], $mysql['password'], $mysql['database']);
$fromData = json_decode(file_get_contents('./data.good.min.json'), true);

$createTB_sql = "CREATE TABLE IF NOT EXISTS `copyrighted_music` (" .
    "`id` int(10) NOT NULL AUTO_INCREMENT," .
    "`fileName` TEXT comment \"文件名称\"," .
    "`size` TEXT comment \"文件大小\"," .
    "`fileId` TEXT comment \"文件ID\"," .
    "`etag` TEXT comment \"文件标识\"," .
    "`s3keyFlag` TEXT," .
    "`type` TEXT," .
    "`msg` TEXT comment \"音乐介绍\"," .
    "`time` TEXT comment \"音频时长\"," .
    "`downloadUrl` TEXT comment \"下载链接\"," .
    "`musicType` TEXT comment \"音乐类型\"," .
    "PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($conn, $createTB_sql);


for ($x = 0; $x < count($fromData); $x++) {
    $item = $fromData[$x];
    $insert_sql = "INSERT INTO `copyrighted_music` (`fileName`, `size`, `fileId`, `etag`, `s3keyFlag`, `type`, `msg`, `time`, `downloadUrl`, `musicType`)" .
        "VALUES ('"  . addslashes($item['fileName']) .  "', '"  . addslashes($item['size']) .  "', '"  . addslashes($item['fileId']) .  "', '"  . addslashes($item['etag']) .  "', '"  . addslashes($item['s3keyFlag']) .  "', '"  . addslashes($item['type']) .  "', '"  . addslashes($item['msg']) .  "', '"  . addslashes($item['time']) .  "', '"  . addslashes($item['downloadUrl']) .  "', '"  . addslashes($item['musicType']) .  "')";
    mysqli_query($conn, $insert_sql);
}
