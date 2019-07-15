<?php

/**
 * 根据用户邮箱获取头像
 */
// 1.获取客户端传递的数据
if (empty($_GET['email'])) {
    exit('缺少必要参数');
}

$email = $_GET['email'];

// 查询对应的头像地址
require_once '../../functions.php';

$avatar = bx_query_one("SELECT avatar FROM `users` WHERE email = '{$email}' limit 1;");
echo $avatar['avatar'];
