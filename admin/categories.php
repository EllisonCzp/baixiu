<?php

require_once '../functions.php';
bx_get_current_user();

// 判断是否为编辑状态
if (!empty($_GET['id'])) {
  // 客户端 通过URL传递一个ID
  // =>客户端是要来拿一个修改数据的表单
  // => 需要拿到用户想要的数据
  $current_edit_category = bx_query_one('select * from categories where id=' . $_GET['id']);
}


// 添加函数
function add_category()
{
  if (empty($_POST['name']) || empty($_POST['slug'])) {
    $GLOBALS['success'] = false;
    $GLOBALS['message'] = '请完整填写表单';
    return;
  }
  // 接收数据
  $name = $_POST['name'];
  $slug = $_POST['slug'];

  // 保存数据
  $affected_rows = bx_execute("INSERT INTO categories VALUES(null,'{$slug}','{$name}');");
  // echo $affected_rows;
  $GLOBALS['success'] = $affected_rows > 0;
  $GLOBALS['message'] = $affected_rows <= 0 ? '添加失败！' : '添加成功！';
}

// 修改函数
function edit_category()
{
  global $current_edit_category;

  // if (empty($_POST['name']) || empty($_POST['slug'])) {
  //   $GLOBALS['success'] = false;
  //   $GLOBALS['message'] = '请完整填写表单';
  //   return;
  // }
  // 接收数据
  $id = $current_edit_category['id'];
  $name = $_POST['name'] ? $_POST['name'] : $current_edit_category['name'];
  $current_edit_category['name'] = $name;
  $slug = $_POST['slug'] ? $_POST['slug'] : $current_edit_category['slug'];
  $current_edit_category['slug'] = $slug;

  // 保存数据
  $affected_rows = bx_execute("UPDATE categories SET slug = '{$slug}', `name` ='{$name}' WHERE id = '{$id}';");
  echo $affected_rows;
  $GLOBALS['success'] = $affected_rows > 0;
  $GLOBALS['message'] = $affected_rows <= 0 ? '修改失败！' : '修改成功！';
}

// 如果修改操作与查询操作在一起，一定是先做修改 再查询 （增强数据的时效性）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


  if (empty($_GET['id'])) {
    // 一但表单提交请求 并且 没有通过 URL 提交 ID 就意味着是要添加数据
    add_category();
  } else {
    // 一但表单提交请求 并且 通过 URL 提交 ID 就意味着是要修改数据
    edit_category();
  }
}

// 查询数据 展示到页面上
$categories = bx_query_all('SELECT * FROM categories');
// var_dump($categories);




?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)) : ?>
        <div class="alert<?php echo $success ? ' alert-success' : ' alert-danger'; ?>">
          <strong><?php echo $success ? '成功' : '失败'; ?>
          </strong><?php echo $message; ?>
        </div>
      <?php endif ?>

      <div class="row">
        <div class="col-md-4">
          <?php if (isset($current_edit_category)) : ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?><?php echo '?id=' . $current_edit_category['id']; ?>" method="POST" autocomplete="off">
              <h2>修改《 <?php echo $current_edit_category['name']; ?> 》</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_category['name']; ?>">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_category['slug']; ?>">
                <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">保存</button>
              </div>
            </form>
          <?php else : ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" autocomplete="off">
              <h2>添加新分类目录</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
                <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">添加</button>
              </div>
            </form>
          <?php endif ?>

        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/category-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php if (isset($categories)) : ?>
                <?php foreach ($categories as $line) : ?>
                  <tr>
                    <td class="text-center"><input id="checkbox" type="checkbox" data-id="<?php echo $line['id']; ?>"></td>
                    <td><?php echo $line['name']; ?></td>
                    <td><?php echo $line['slug']; ?></td>
                    <td class="text-center">
                      <a href="/admin/categories.php?id=<?php echo $line['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                      <a href="/admin/category-delete.php?id=<?php echo $line['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                    </td>
                  </tr>
                <?php endforeach ?>
              <?php endif ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    // 不要重复使用无意义的选择操作，应该采用变量去本地化
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







      // ===================================================================================
      // $tbodyCheckbox.on('change', function() {
      //   var flag = false;
      //   // 有任意一个 checkbox 选中就显示 反之隐藏
      //   $tbodyCheckbox.each(function(i, item) {
      //     // attr 和 prop 区别
      //     // attr 访问的是元素属性
      //     // prop 访问的是元素对应的DOM对象的属性
      //     if ($(item).prop('checked')) {
      //       flag = true;
      //     }

      //   })
      //   flag ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
      // })

    });
  </script>
  <script>
    NProgress.done()
  </script>
</body>

</html>