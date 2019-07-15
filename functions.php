<?php

/**
 * 封装大家公用的函数
 */
require_once 'config.php';
@session_start();

/**
 * 获取当前用户的登录信息 如果获取不到则跳转到登录页面
 * 定义函数一定要注意函数名与内置函数冲突的问题
 * @return[session]
 */
function bx_get_current_user()
{

  if (empty($_SESSION['current_login_user'])) {
    // 没有登录信息
    // 跳转到登录页面
    header('location:/admin/login.php');
    // 跳转之后就不需要执行下面的代码
    exit();
  }
  return $_SESSION['current_login_user'];
}


/**
 * 查询数据库
 */
function bx_query($sql)
{
  $conn = mysqli_connect(BX_DB_HOST, BX_DB_USER, BX_DB_PASS, BX_DB_NAME);
  if (!$conn) {
    exit('数据库连接失败');
  }
  $query = mysqli_query($conn, $sql);
  if (!$query) {
    // 查询失败
    return false;
  }
  return $query;
}


/**
 * 获取多条数据
 * @return[array] 索引数组[ 关联数组[] ]
 */
function bx_query_all($sql)
{
  $query = bx_query($sql);
  $result = [];
  while ($row = mysqli_fetch_assoc($query)) {
    $result[] = $row;
  }
  return @$result;
}

/**
 * 获取一条数据
 * @return[array] 关联数组
 */
function bx_query_one($sql)
{
  $res = bx_query_all($sql)[0];
  return isset($res) ? $res : null;
}


/**
 * 增 删 改数据
 */
function bx_execute($sql)
{
  $conn = mysqli_connect(BX_DB_HOST, BX_DB_USER, BX_DB_PASS, BX_DB_NAME);
  if (!$conn) {
    exit('数据库连接失败');
  }
  $query = mysqli_query($conn, $sql);
  if (!$query) {
    // 查询失败
    return false;
  }
  $affected_rows = mysqli_affected_rows($conn);

  mysqli_close($conn);

  return $affected_rows;
}

/**
 * sql注入
 */
function sql_in($str)
{
  $res = explode(',', $str);
  foreach ($res as  $value) {
    if (!is_numeric($value)) {
      exit('请正确传入参数');
    }
  }
}
