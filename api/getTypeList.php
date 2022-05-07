<?php

/**
 * 输出音乐分类列表
 * 可以通过 bin/获取分类名称列表.py 更新
 */

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
$data = [
    ["all", "全部"],
    ["World Music", "世界"],
    ["Comedy", "喜剧"],
    ["Horror", "恐怖"],
    ["upbeat", "乐观"],
    ["epic", "史诗"],
    ["Percussion / Underscoring", "冲击/强调"],
    ["Romantic", "浪漫"],
    ["Misc", "杂项"],
    ["Electronic", "电子"],
    ["Other", "其他"]
];
echo json_encode($data);