<?php
// 也可以使用 $_SERVER['PHP_SELF']代替$current_page
$current_page = isset($current_page) ? $current_page : '';

// 因为这个sidebar.php是被其它页面载入执行的 所以这里的相对路径是 相对于其他页面的
// 如果希望根治这个问题可以使用物理路径解决dirname(__FILE__)
require_once  '../functions.php';
// 拿到当前登录用户的信息
$current_user = bx_get_current_user();


?>
<div class="aside">
    <div class="profile">
        <img class="avatar" src="<?php echo $current_user['avatar']; ?>">
        <h3 class="name"><?php echo $current_user['nickname']; ?></h3>
    </div>
    <ul class="nav">
        <!-- 仪表盘 -->
        <li <?php echo $current_page === 'index' ? 'class = active' : ''; ?>>
            <a href="/admin/index.php"><i class="fa fa-dashboard"></i>仪表盘</a>
        </li>

        <!-- 定义一个数组保存文章各个页面的标识 -->
        <?php $menu_posts = array('posts', 'post-add', 'categories'); ?>
        <!-- 文章 -->
        <li <?php echo in_array($current_page, $menu_posts) ? 'class = active' : ''; ?>>
            <a href="#menu-posts" <?php echo in_array($current_page, $menu_posts) ? '' : 'class = collapsed'; ?> data-toggle="collapse">
                <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
            </a>
            <ul id="menu-posts" class="collapse<?php echo in_array($current_page, $menu_posts) ? ' in' : ''; ?>">
                <li <?php echo $current_page === 'posts' ? 'class = active' : ''; ?>><a href="/admin/posts.php">所有文章</a></li>
                <li <?php echo $current_page === 'post-add' ? 'class = active' : ''; ?>><a href="/admin/post-add.php">写文章</a></li>
                <li <?php echo $current_page === 'categories' ? 'class = active' : ''; ?>><a href="/admin/categories.php">分类目录</a></li>
            </ul>
        </li>
        <!-- 评论 -->
        <li <?php echo $current_page === 'comments' ? "class = active" : ''; ?>>
            <a href="/admin/comments.php"><i class="fa fa-comments"></i>评论</a>
        </li>
        <!-- 用户 -->
        <li <?php echo $current_page === 'users' ? "class = active" : ''; ?>>
            <a href="/admin/users.php"><i class="fa fa-users"></i>用户</a>
        </li>

        <!-- 设置 -->
        <!-- 定义一个数组保存设置各个页面的标识 -->
        <?php $menu_settings = array('nav-menus', 'slides', 'settings') ?>
        <li <?php echo in_array($current_page, $menu_settings) ? 'class = active' : ''; ?>>
            <a href="#menu-settings" <?php echo in_array($current_page, $menu_settings) ? '' : 'class = collapsed'; ?> data-toggle="collapse">
                <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
            </a>
            <ul id="menu-settings" class="collapse <?php echo in_array($current_page, $menu_settings) ? ' in' : ''; ?>">
                <li <?php echo $current_page === 'nav-menus' ? 'class = active' : ''; ?>><a href="/admin/nav-menus.php">导航菜单</a></li>
                <li <?php echo $current_page === 'slides' ? 'class = active' : ''; ?>><a href="/admin/slides.php">图片轮播</a></li>
                <li <?php echo $current_page === 'settings' ? 'class = active' : ''; ?>><a href="/admin/settings.php">网站设置</a></li>
            </ul>
        </li>
    </ul>
</div>