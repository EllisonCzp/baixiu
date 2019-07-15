<?php

require_once '../functions.php';
/**
 * 根据客户端传递过来的ID 删除对应的数据
 */

if (empty($_GET['id'])) {
    exit('缺少必要参数');
}
// 获取客户端传入的参数
// $id = (int)$_GET['id'];
$id = $_GET['id'];
// => 1 or 1 = 1
// sql注入
sql_in($id);
// 删除对应的数据
bx_execute('DELETE FROM posts WHERE id in (' . $id . ');');

// http 中的 referer 用来标识当前请求的来源
// 跳转页面
header('location:' . $_SERVER['HTTP_REFERER']);
