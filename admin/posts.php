<?php
session_start();
require_once '../functions.php';
bx_get_current_user();


$where = '1 = 1';
$search = '';
// 接收筛选参数
if (isset($_GET['category']) && preg_match('/\d/', $_GET['category'])) {
  $where .= ' and posts.category_id = ' . $_GET['category'];
  $search .= '&category=' . $_GET['category'];
}
if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= " and posts.`status` ='{$_GET['status']}'";
  $search .= '&status=' . $_GET['status'];
}



// 处理分页参数
// ====================================================================

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
if ($page < 1) {
  // 不可能有小于1的情况
  header('Location: /admin/posts.php?page=1' . $search);
}
// 每页的数量
$size = 20;
// 跳过的数据
$offset = ($page - 1) * $size;
// 计算总页数     总页数 = 总条数 / 每页条数
$total_count = (int)bx_query_one("SELECT COUNT(1) as num FROM posts INNER JOIN categories ON posts.category_id = categories.id
INNER JOIN  users ON posts.user_id = users.id where {$where} ;")['num'];
$total_page = (int)ceil($total_count / $size);
if ($page > $total_page) {
  header('location: /admin/posts.php?page=' . $total_page . $search);
}

// 获取数据
// ======================================================================
$posts =  bx_query_all("SELECT
                        posts.id,
                        posts.title,
                        categories.`name`as category_name,
                        users.nickname as user_nickname,
                        posts.created,
                        posts.`status`
                        FROM posts
                        INNER JOIN categories ON posts.category_id = categories.id
                        INNER JOIN  users ON posts.user_id = users.id
                        where {$where}
                        ORDER BY posts.id DESC
                        LIMIT {$offset}, {$size};");

// 获取所有分类
$categories = bx_query_all('SELECT * FROM categories');

// 处理分页页码
// ====================================================================

// 计算页码开始
$visiables = 5;
$interval = ($visiables - 1) / 2; //区间
$begin = $page - $interval; //开始页码
$end = $begin + $visiables - 1; //结束页码

// 可能出现 $begin 和 $end 不合理的情况
// $begin 必须 >0
// 确保$begin 最小为一
$begin = $begin < 1 ? 1 : $begin;
$end = $begin + $visiables - 1;

$end = $end > $total_page ? $total_page : $end;
$begin = $end - $visiables + 1;
$begin = $begin < 1 ? 1 : $begin;

// if ($begin < 1) {
//   // $begin修改就意味$end也要一起修改
//   $begin = 1;
//   $end = $begin + $visiables;
// }
// // $end 必须 <= 最大页数
// if ($end > $total_page + 1) {
//   // 超出页数范围
//   $end = $total_page + 1;
//   // $end 修改就意味 $begin 也要一起修改
//   $begin = $end - $visiables; // 数据过少可能产生负数
//   // 如果数据过少
//   if ($begin < 1) {
//     $begin = 1;
//   }
// }

// 处理数据转换逻辑
// ==================================================================
/**
 * 转换状态显示
 *
 * @param [string] $status 英文状态
 * @return [string]        中文状态
 */
function convert_status($status)
{
  $dict = array(
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站'
  );

  return isset($dict[$status]) ? $dict[$status] : '未知状态';
}
/**
 * 转换时间格式
 *
 * @param [string] $created
 * @return [string]
 */
function convert_date($created)
{
  $timestamp = strtotime($created);
  return date('Y年m月d日 <b\r> H: i: s', $timestamp);
}

// function get_category($categroy_id)
// {
//   return bx_query_one("select name from categories where id = '{$categroy_id}';")['name'];
// }

// function get_nickname($user_id)
// {
//   return bx_query_one("select nickname from users where id = '{$user_id}';")['nickname'];
// }





?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>

<body>
  <script>
    NProgress.start()
  </script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
          <strong>错误！</strong>发生XXX错误
        </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/post-delete.php" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $value) : ?>
              <option <?php echo isset($_GET['category']) && $_GET['category'] === $value['id'] ? 'selected' : ''; ?> value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option <?php echo isset($_GET['status']) && $_GET['status'] === 'drafted' ? 'selected' : ''; ?> value="drafted">草稿</option>
            <option <?php echo isset($_GET['status']) && $_GET['status'] === 'trashed' ? 'selected' : ''; ?> value="trashed">回收</option>
            <option <?php echo isset($_GET['status']) && $_GET['status'] === 'published' ? 'selected' : ''; ?> value="published">已发布</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <?php if ($page > 1) : ?>
            <li><a href="?page=<?php echo '1' . $search; ?>">首页</a></li>
          <?php endif ?>
          <?php for ($i = $begin; $i <= $end; $i++) : ?>
            <li <?php echo $i == $page ? 'class=active' : ''; ?>><a href="?page=<?php echo $i . $search; ?>"><?php echo $i; ?></a></li>
          <?php endfor ?>
          <?php if ($page < $total_page) : ?>
            <li><a href="?page=<?php echo $total_page . $search; ?>">尾页</a></li>
          <?php endif ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $line) : ?>
            <tr>
              <td class="text-center"><input type="checkbox" data-id="<?php echo $line['id']; ?>"></td>
              <td><?php echo $line['title']; ?></td>
              <!-- <td><?php /* echo get_nickname($line['user_id']);*/ ?></td> -->
              <!-- <td><?php /*echo get_category($line['category_id']);*/ ?></td> -->
              <td><?php echo $line['user_nickname']; ?></td>
              <td><?php echo $line['category_name']; ?></td>
              <td class="text-center"><?php echo convert_date($line['created']); ?></td>
              <!-- 一旦当输出的判断或者转换逻辑过于复杂，不建议直接写在混编位置 -->
              <td class="text-center"><?php echo convert_status($line['status']); ?></td>
              <td class="text-center">
                <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                <a href="/admin/post-delete.php?id=<?php echo $line['id']; ?>" class="btn btn-danger btn-xs">删除</a>
              </td>
            </tr>
          <?php endforeach ?>


        </tbody>
      </table>
    </div>
  </div>
  <?php $current_page = 'posts' ?>

  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function($) {
      var $theadCheckbox = $('thead input');
      var $tbodyCheckbox = $('tbody input');
      var $btnDelete = $('#btn_delete');
      // 定义一个数组记录被选中的复选框
      var allCheckds = [];

      $tbodyCheckbox.on('change', function() {
        var flag = true;
        var id = $(this).data('id');
        // 根据有没有选中当前这个 checkbox 决定是添加还是删除
        if ($(this).prop('checked')) {
          // allCheckds.indexOf(id) !== -1 || allCheckds.push(id);
          allCheckds.includes(id) || allCheckds.push(id);
        } else {
          allCheckds.splice(allCheckds.indexOf(id), 1);
        }
        flag = allCheckds.length == $tbodyCheckbox.length ? true : false;
        // 根据剩下的选中的个数决定显示还是隐藏
        allCheckds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
        $theadCheckbox.prop('checked', flag);
        $btnDelete.prop('search', '?id=' + allCheckds);
      })

      // 全选和全不选
      $theadCheckbox.on('change', function() {
        $tbodyCheckbox.prop('checked', $(this).prop('checked')).trigger('change');
      })
    });
  </script>
  <script>
    NProgress.done()
  </script>
</body>

</html>