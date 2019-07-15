<?php

// 接收客户端的ajax请求 返回响应数据


header('Content-Type: appliaction/json');

// 载入封装好的函数
require_once '../../functions.php';

// 计算页码
// =======================================================================

// 获取客户端传递过来的页码
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
// 每页条数
$length = 20;
// 跳过的条数 = （当前页数 - 1） * 每页多少条
$offset = ($page - 1) * $length;
// 获取总条数
$total_length = bx_query_one('SELECT COUNT(1) as num FROM `comments`INNER JOIN posts WHERE comments.post_id = posts.id;')['num'];
// 总页数 = 总条数 / 每页条数
$total_page = ceil($total_length / $length);

$sql = sprintf('SELECT
                comments.*,
                posts.title as post_title
                FROM `comments`
                INNER JOIN posts WHERE comments.post_id = posts.id
                ORDER BY comments.created DESC
                LIMIT %d, %d', $offset, $length);

// 获取所有评论数据
$comments = bx_query_all($sql);

// 因为网络之间传递的只能是字符串
// 所以先将数据转换成字符串（序列化）
$json = json_encode(array(
    'comments' => $comments,
    'total_pages' => $total_page
));

// 响应给客户端
echo $json;
