<?php
session_start();
require_once '../functions.php';
bx_get_current_user();

function add_user()
{
  //1. 接收并校验数据
  if (empty($_POST['email'])) {
    $GLOBALS['success'] = false;
    $GLOBALS['message'] = '请输入邮箱';
    return;
  }
  if (!preg_match('/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/', $_POST['email'])) {
    $GLOBALS['success'] = false;
    $GLOBALS['message'] = '请有效的邮箱';
    return;
  }
  if (empty($_POST['slug'])) {
    $GLOBALS['success'] = false;
    $GLOBALS['message'] = '请输入别名';
    return;
  }
  if (empty($_POST['nickname'])) {
    $GLOBALS['success'] = false;
    $GLOBALS['message'] = '请输入昵称';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['success'] = false;
    $GLOBALS['message'] = '请输入密码';
    return;
  }
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $pass = $_POST['password'];
  // 2.把数据保存到数据库中
  $row = bx_execute("INSERT INTO users VALUES (null,'{$slug}','{$email}','{$pass}','{$nickname}',null,null,'activated');");
  $GLOBALS['success'] = $row > 0 ? true : false;
  $GLOBALS['message'] = $row <= 0 ? '添加失败！' : '添加成功';
}
// 判断是否为post提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  add_user();
}

// 查询数据展示到页面上
$users = bx_query_all('select * from users');

?>


<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)) : ?>
        <div class="alert<?php echo $success ? ' alert-success' : ' alert-danger'; ?> ">
          <strong><?php echo $success ? '成功' : '失败'; ?></strong><?php echo $message; ?>
        </div>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" novalidate>
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/user-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody id="current_checked">
              <?php foreach ($users as $line) : ?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $line['id']; ?>"></td>
                  <td class="text-center"><img class="avatar" src="<?php echo $line['avatar']; ?>"></td>
                  <td><?php echo $line['email']; ?></td>
                  <td><?php echo $line['slug']; ?></td>
                  <td><?php echo $line['nickname']; ?></td>
                  <td><?php echo $line['status']; ?></td>
                  <td class="text-center">
                    <a href="post-add.php" class="btn btn-default btn-xs">编辑</a>
                    <a href="/admin/user-delete.php?id=<?php echo $line['id']; ?> " class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function($) {

      $checkbox = $('#current_checked input');

      $btnDelete = $('#btn_delete');
      var allCheckds = [];
      $checkbox.on('change', function() {
        var id = $(this).data('id');
        // 根据有没有选中当前这个 checkbox 决定是添加还是删除
        if ($(this).prop('checked')) {
          allCheckds.push(id);
        } else {
          allCheckds.splice(allCheckds.indexOf(id), 1);
        }
        // 根据剩下的选中的个数决定显示还是隐藏
        allCheckds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
        $btnDelete.prop('search', '?id=' + allCheckds);
      })


    });
  </script>
  <script>
    NProgress.done()
  </script>
</body>

</html>