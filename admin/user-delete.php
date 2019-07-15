<?php

// 根据客户端传入的id值 删除对应的数据

// 载入配置文件
require_once '../functions.php';

// 判断是否传值
if (empty($_GET['id'])) {
    exit('缺少必要参数');
}
// sql注入
sql_in($_GET['id']);
// 接收参数
$user_id = $_GET['id'];
// 删除对应的数据
bx_execute('delete from users where id in (' . $user_id . ') ');
// 响应
header('location: /admin/users.php');
