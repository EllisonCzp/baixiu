<?php

require_once '../../functions.php';
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
$row = bx_execute('DELETE FROM comments WHERE id in (' . $id . ');');

header('Content-Type: appliaction/json');
// 返回信号
echo json_encode($row > 0);
