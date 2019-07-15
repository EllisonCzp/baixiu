<?php

/**
 * 根据用户邮箱获取头像
 */
// 1.获取客户端传递的数据
if (empty($_FILES['avatar'])) {
    exit('缺少必要参数');
}

$avatar = $_FILES['avatar'];

if ($avatar['error'] !== UPLOAD_ERR_OK) {
    exit('上传失败');
}
if (strpos($avatar['type'], 'image/') !== 0) {
    exit('文件格式错误');
}
if ($avatar['size'] < 0 || $avatar['size'] > 4 * 1024 * 1024) {
    exit('文件过大/过小');
}
$ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
$target = '../../static/uploads/' . uniqid() . '.' . $ext;

if (!move_uploaded_file($avatar['tmp_name'], $target)) {
    exit('上传失败');
}

echo substr($target, 5);
