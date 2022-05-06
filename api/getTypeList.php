<?php

/**
 * 输出音乐分类列表
 * 可以通过 bin/获取分类名称列表.py 更新
 */
$data = [
    "Other",
    "Electronic",
    "Misc",
    "Romantic",
    "Percussion / Underscoring",
    "epic",
    "upbeat",
    "Horror",
    "Comedy",
    "World Music"
];
echo json_encode($data);