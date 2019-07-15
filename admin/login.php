<?php

// 载入配置文件
require_once '../config.php';
require_once '../functions.php';

// 给用户找一个箱子 （ 如果你之前有 就用之前的 没有就给一个新的）
// session_start();

function login()
{
  // 1.接收数据并校验
  // 验证是否输入用户名
  if (empty($_POST['email'])) {
    $GLOBALS['error_message'] = '请输入邮箱';
    return;
  }
  // 验证是否输入密码
  if (empty($_POST['password'])) {
    $GLOBALS['error_message'] = '请输入密码';
    return;
  }
  $user = $_POST['email'];
  $pass = $_POST['password'];


  // 取数据
  $data = bx_query_one("SELECT * FROM users WHERE email = '{$user}' limit 1");
  // var_dump($data);

  if (!$data) {
    // 用户名不存在
    $GLOBALS['error_message'] = '用户名或密码错误';
    return;
  }
  // 密码一般都是加密储存的
  if ($data['password'] !== $pass) {
    // 密码不匹配
    $GLOBALS['error_message'] = '用户名或密码错误';
    return;
  }

  // 存储一个登陆标识（session）
  $_SESSION['current_login_user'] = $data;

  // 2.持久化
  // 3.响应
  header('location:/admin/');
}



// 判断是否为post提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  login();
}
?>


<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>

<body>
  <div class="login">
    <form class="login-wrap<?php echo isset($error_message) ? ' tada animated' : ''; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" autocomplete="off" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger">
          <strong>错误！</strong> <?php echo $error_message; ?>
        </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" value="<?php echo empty($_POST['email']) ? '' : $_POST['email']; ?>" name="email" type="email" class="form-control" placeholder="邮箱" autofocus>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>


  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    $(function($) {
      // 1.单独作用域
      // 2.确保页面加载过后执行

      //目标： 在用户输入自己的邮箱过后 在页面上展示对应的头像
      // 1.邮箱文本框失去焦点
      var emailFormat = /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/;
      $('#email').on('blur', function() {
        // 忽略掉文本框为空或者不是一个邮箱
        if (!this.value || !emailFormat.test(this.value)) return;

        // 用户输入的是一个邮箱
        $.get('/admin/api/avater.php', {
          email: this.value
        }, function(res) {
          if (!res) return;
          $('.avatar').fadeOut(function() {
            $(this).on('load', function() {
              $(this).fadeIn();
            }).attr('src', res);
          });



        });
      });



    });
  </script>
</body>

</html>