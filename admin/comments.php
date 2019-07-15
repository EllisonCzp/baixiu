<?php
require_once '../functions.php';
bx_get_current_user();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
    .loading {
      display: flex;
      position: fixed;
      align-items: center;
      justify-content: center;
      top: 0;
      bottom: 0;
      right: 0;
      left: 0;
      background-color: rgba(0, 0, 0, .5);
      z-index: 999;
    }

    .lds-spinner {
      color: official;
      display: inline-block;
      position: relative;
      width: 64px;
      height: 64px;
    }

    .lds-spinner div {
      transform-origin: 32px 32px;
      animation: lds-spinner 1.2s linear infinite;
    }

    .lds-spinner div:after {
      content: " ";
      display: block;
      position: absolute;
      top: 3px;
      left: 29px;
      width: 5px;
      height: 14px;
      border-radius: 20%;
      background: #fff;
    }

    .lds-spinner div:nth-child(1) {
      transform: rotate(0deg);
      animation-delay: -1.1s;
    }

    .lds-spinner div:nth-child(2) {
      transform: rotate(30deg);
      animation-delay: -1s;
    }

    .lds-spinner div:nth-child(3) {
      transform: rotate(60deg);
      animation-delay: -0.9s;
    }

    .lds-spinner div:nth-child(4) {
      transform: rotate(90deg);
      animation-delay: -0.8s;
    }

    .lds-spinner div:nth-child(5) {
      transform: rotate(120deg);
      animation-delay: -0.7s;
    }

    .lds-spinner div:nth-child(6) {
      transform: rotate(150deg);
      animation-delay: -0.6s;
    }

    .lds-spinner div:nth-child(7) {
      transform: rotate(180deg);
      animation-delay: -0.5s;
    }

    .lds-spinner div:nth-child(8) {
      transform: rotate(210deg);
      animation-delay: -0.4s;
    }

    .lds-spinner div:nth-child(9) {
      transform: rotate(240deg);
      animation-delay: -0.3s;
    }

    .lds-spinner div:nth-child(10) {
      transform: rotate(270deg);
      animation-delay: -0.2s;
    }

    .lds-spinner div:nth-child(11) {
      transform: rotate(300deg);
      animation-delay: -0.1s;
    }

    .lds-spinner div:nth-child(12) {
      transform: rotate(330deg);
      animation-delay: 0s;
    }

    @keyframes lds-spinner {
      0% {
        opacity: 1;
      }

      100% {
        opacity: 0;
      }
    }
  </style>
</head>

<body>
  <script>
    NProgress.start()
  </script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul id="pagination" class="pagination pagination-sm pull-right">
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>

          <!-- <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>已批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-warning btn-xs">驳回</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr> -->
        </tbody>
      </table>
    </div>
  </div>


  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php'; ?>
  <div class="loading" style="display: none">
    <div class="lds-spinner">
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
      <div></div>
    </div>

  </div>

  <script id="comment_tmpl" type="text/x-jsrender">
    {{for comments}}
    <tr {{if status == 'held'}} class="warning" {{else status == 'rejected'}} class="danger" {{/if}} data-id="{{:id}}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{:author}}</td>
      <td>{{:content}}</td>
      <td>《{{:post_title}}》</td>
      <td>{{:created}}</td>
      <td>{{:status}}</td>
      <td class="text-center">
        {{if status == 'held'}}
        <a href="post-add.html" class="btn btn-info btn-xs">审核</a>
        <a href="post-add.html" class="btn btn-warning btn-xs">拒绝</a>
        {{else status == 'rejected' }}
          <a href="post-add.html" class="btn btn-info btn-xs">审核</a>
          {{/if}}
          <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
      </td>
    </tr>
    {{/for}}
  </script>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.min.js"></script>


  <script>
    // nprogress
    $(document)
      .ajaxStart(function() {
        NProgress.start();
        // 显示
        $('.loading').fadeIn();
        $('body').addClass('nprogr')
      })
      .ajaxStop(function() {
        NProgress.done();
        $('.loading').fadeOut();
        $('body').removeClass('nprogr');


      })
    // 记录当前页
    var currentPage = 1;

    $(function($) {
      function loadPage(page) {
        $('tbody').fadeOut();
        $.getJSON('/admin/api/comments.php', {
            page: page
          },
          function(res) {
            if (page > res['total_pages']) {
              loadPage(res.total_pages);
              return
            }
            // twbsPagination
            $('#pagination').twbsPagination('destroy');
            $('#pagination').twbsPagination({
              totalPages: res['total_pages'],
              visiblePages: 5,
              initiateStartPageClick: false,
              first: '&laquo;',
              last: '&raquo;',
              prev: '上一页',
              next: '下一页',
              startPage: page,
              onPageClick: function(e, page) {
                // 初始化之前就会执行一次
                loadPage(page);
              }

            });
            // 渲染数据
            var html = $('#comment_tmpl').render({
              comments: res['comments']
            });
            $('tbody').html(html).fadeIn();
            // 记录当前页码
            currentPage = page;
          });
      }
      loadPage(currentPage);
      // 删除
      // ================================================================
      // 由于删除按钮是动态添加的 而且执行动态添加代码是在此之后执行的，过早注册不了
      $('tbody').on('click', '.btn-delete', function() {
        var $tr = $(this).parent().parent();
        // 删除单条数据的时机
        // 1.拿到当前数据的id
        var id = $tr.data('id');
        // 2.发送一个ajax请求 告诉服务端要删除那一条数据
        $.get('/admin/api/comments-delete.php', {
          id: id
        }, function(res) {
          // 3.根据服务端返回的删除是否成功 决定是否在界面上移除这个元素
          if (!res) return;
          // 删除成功
          // 4.重新再去载入当前这一页的数据
          loadPage(currentPage);
        });


      });

    })
  </script>
  <script>
    NProgress.done()
  </script>
</body>

</html>