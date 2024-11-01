<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;
add_action('admin_menu', 'xt_admin_menu');

function xt_admin_menu() {
    global $xt_admin_sys, $xt_admin_share, $xt_admin_fanxian, $xt_admin_member;
    $xt_admin_sys = add_menu_page('新淘客', '新淘客', 'administrator', 'xt_menu_sys', 'xt_menu_home');
    $xt_admin_share = add_submenu_page('xt_menu_sys', '分享', '分享', 'administrator', 'xt_menu_share', 'xt_menu_share');
    $xt_admin_fanxian = add_submenu_page('xt_menu_sys', '返现', '返现', 'administrator', 'xt_menu_fanxian', 'xt_menu_fanxian');
    $xt_admin_member = add_submenu_page('xt_menu_sys', '会员', '会员', 'administrator', 'xt_menu_member', 'xt_menu_member');
    do_action('xt_admin_menu');
}

function _xt_submenu_action_sys() {
    $action = isset($_GET['xt-action']) && !empty($_GET['xt-action']) ? $_GET['xt-action'] : "";
    if (!in_array($action, array(
                'profile',
                'setting',
                'platform',
                'mail',
                'tool'
            ))) {
        $action = 'profile';
    }
    return $action;
}

function _xt_submenu_action_share() {
    $action = isset($_GET['xt-action']) && $_GET['xt-action'] ? $_GET['xt-action'] : "";
    if (!in_array($action, array(
                'catalog',
                'albumcatalog',
                'tag',
                'share',
                'album',
                'comment'
            ))) {
        $action = 'catalog';
    }
    return $action;
}

function _xt_submenu_action_fanxian() {
    $action = isset($_GET['xt-action']) && $_GET['xt-action'] ? $_GET['xt-action'] : "";
    if (!in_array($action, array(
                'taobao',
                'paipai',
                'yiqifa',
                'cash',
                'buy',
                'ads',
                'jifen'
            ))) {
        $action = 'taobao';
    }
    return $action;
}

function _xt_submenu_action_member() {
    $action = isset($_GET['xt-action']) && $_GET['xt-action'] ? $_GET['xt-action'] : "";
    if (!in_array($action, array(
                'member',
                'role'
            ))) {
        $action = 'member';
    }
    return $action;
}

function xt_menu_home() {
    $action = _xt_submenu_action_sys();
    ?>
    <style type="text/css">
        .widefat td{vertical-align: middle;}
        .xt-help-link{margin:0 2px 0 0;display: block;float:left;width:16px;height:16px;background: url("<?php echo XT_CORE_IMAGES_URL ?>/help.png") no-repeat;}
    </style>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=profile') ?>" class="nav-tab<?php echo $action == 'profile' ? ' nav-tab-active' : '' ?>">概况</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=setting') ?>" class="nav-tab<?php echo $action == 'setting' ? ' nav-tab-active' : '' ?>">基本设置</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=platform') ?>" class="nav-tab<?php echo $action == 'platform' ? ' nav-tab-active' : '' ?>">平台设置</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=mail') ?>" class="nav-tab<?php echo $action == 'mail' ? ' nav-tab-active' : '' ?>">邮箱设置</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=tool') ?>" class="nav-tab<?php echo $action == 'tool' ? ' nav-tab-active' : '' ?>">工具</a>
        </h2>
        <?php
        require_once (XT_PLUGIN_DIR . '/xt-admin/sys_' . $action . '.php');
        ?>
    </div>
    <?php
}

function xt_menu_share() {
    $action = _xt_submenu_action_share();
    ?>
    <style type="text/css">
        .widefat td{vertical-align: middle;}
        .xt-help-link{margin:0 2px 0 0;display: block;float:left;width:16px;height:16px;background: url("<?php echo XT_CORE_IMAGES_URL ?>/help.png") no-repeat;}
    </style>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <a href="<?php echo admin_url('admin.php?page=xt_menu_share&xt-action=catalog') ?>" class="nav-tab<?php echo $action == 'catalog' ? ' nav-tab-active' : '' ?>">分享分类</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_share&xt-action=albumcatalog') ?>" class="nav-tab<?php echo $action == 'albumcatalog' ? ' nav-tab-active' : '' ?>">专辑分类</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_share&xt-action=tag') ?>" class="nav-tab<?php echo $action == 'tag' ? ' nav-tab-active' : '' ?>">标签</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_share&xt-action=share') ?>" class="nav-tab<?php echo $action == 'share' ? ' nav-tab-active' : '' ?>">分享</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_share&xt-action=album') ?>" class="nav-tab<?php echo $action == 'album' ? ' nav-tab-active' : '' ?>">专辑</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_share&xt-action=comment') ?>" class="nav-tab<?php echo $action == 'comment' ? ' nav-tab-active' : '' ?>">评论</a>
        </h2>
        <?php
        require_once (XT_PLUGIN_DIR . '/xt-admin/share_' . $action . '.php');
        ?>
    </div>
    <?php
}

function xt_menu_fanxian() {
    $action = _xt_submenu_action_fanxian();
    ?>
    <style type="text/css">
        .widefat td{vertical-align: middle;}.widefat td p{margin-bottom:2px;}
        .xt-help-link{margin:0 2px 0 0;display: block;float:left;width:16px;height:16px;background: url("<?php echo XT_CORE_IMAGES_URL ?>/help.png") no-repeat;}
    </style>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <a href="<?php echo admin_url('admin.php?page=xt_menu_fanxian&xt-action=taobao') ?>" class="nav-tab<?php echo $action == 'taobao' ? ' nav-tab-active' : '' ?>">淘宝</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_fanxian&xt-action=paipai') ?>" class="nav-tab<?php echo $action == 'paipai' ? ' nav-tab-active' : '' ?>">拍拍</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_fanxian&xt-action=yiqifa') ?>" class="nav-tab<?php echo $action == 'yiqifa' ? ' nav-tab-active' : '' ?>">商城</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_fanxian&xt-action=cash') ?>" class="nav-tab<?php echo $action == 'cash' ? ' nav-tab-active' : '' ?>">提现记录</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_fanxian&xt-action=buy') ?>" class="nav-tab<?php echo $action == 'buy' ? ' nav-tab-active' : '' ?>">购买返现</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_fanxian&xt-action=ads') ?>" class="nav-tab<?php echo $action == 'ads' ? ' nav-tab-active' : '' ?>">推广返现</a>
            <!--<a href="<?php echo admin_url('admin.php?page=xt_menu_fanxian&xt-action=jifen') ?>" class="nav-tab<?php echo $action == 'jifen' ? ' nav-tab-active' : '' ?>"><?php echo xt_jifenbao_text(); ?></a>-->
        </h2>
        <?php
        require_once (XT_PLUGIN_DIR . '/xt-admin/fanxian_' . $action . '.php');
        ?>
    </div>
    <?php
}

function xt_menu_member() {
    $action = _xt_submenu_action_member();
    ?>
    <style type="text/css">
        .widefat td{vertical-align: middle;}.widefat td p{margin-bottom:5px;}
        .xt-help-link{margin:0 2px 0 0;display: block;float:left;width:16px;height:16px;background: url("<?php echo XT_CORE_IMAGES_URL ?>/help.png") no-repeat;}
    </style>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <a href="<?php echo admin_url('admin.php?page=xt_menu_member&xt-action=member') ?>" class="nav-tab<?php echo $action == 'member' ? ' nav-tab-active' : '' ?>">会员</a>
            <a href="<?php echo admin_url('admin.php?page=xt_menu_member&xt-action=role') ?>" class="nav-tab<?php echo $action == 'role' ? ' nav-tab-active' : '' ?>">角色</a>
        </h2>
        <?php
        require_once (XT_PLUGIN_DIR . '/xt-admin/member_' . $action . '.php');
        ?>
    </div>
    <?php
}
?>
