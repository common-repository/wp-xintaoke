<?php
wp_clear_auth_cookie();

if (isset($_REQUEST['redirect_to']) && !empty($_REQUEST['redirect_to'])) {
    $redirect_to = urldecode($_REQUEST['redirect_to']);
} else {
    $redirect_to = home_url();
}
$_fx = isset($_REQUEST['fx']) ? $_REQUEST['fx'] : 0;
$_loginurl = xt_platform_authorize_url('[PLATFORM]', urlencode(!empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : home_url()), (!empty($_REQUEST['mode']) ? htmlspecialchars($_REQUEST['mode']) : ''));
$_url = '';
if (isset($_REQUEST['fx'])) {
    $_url = base64_decode($_REQUEST['url']);
}
if (isset($_REQUEST['type']) && ($_REQUEST['type'] == 'tuan' || $_REQUEST['type'] == 'bijia')) {
    $_url = urldecode($_url);
}
?>
<div class="row-fluid">
    <div class="span12" style="min-height: 0px;">

        <?php
        if ($_fx > 0 && xt_is_fanxian()) {
            ?>
            <div class="alert" style="width:630px;margin:0 auto;margin-bottom: 15px;">
                <span>登录后购买最高可返还&nbsp;<strong class="text-default"><?php echo $_REQUEST['fx'] ?></strong>&nbsp;<span><?php echo (isset($_REQUEST['from_type']) && xt_fanxian_is_jifenbao($_REQUEST['from_type'])) ? xt_jifenbao_text() : '元' ?></span>。</span>
                <a target="_top" href="<?php echo $_url ?>">先购物，再返利</a>
            </div>
            <?php
        } elseif (isset($_REQUEST['fx'])) {
            ?>
            <div class="alert" style="width:630px;margin:0 auto;margin-bottom: 15px;">
                <a target="_top" href="<?php echo $_url ?>">不登录,直接访问</a>
            </div>
            <?php
        }
        ?>
        <div id="X_Login-Error" class="alert alert-error hide" style="width:630px;margin:0 auto;">
        </div>
    </div>    
</div>
<div class="row-fluid">
    <div class="span6 xt-first">
        <div class="form-horizontal">
            <div class="control-group">
                <label class="control-label" for="user_login">用户名</label>
                <div class="controls">
                    <input type="text" name="log" id="user_login">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="user_pass">密码</label>
                <div class="controls">
                    <input type="password" name="pwd" id="user_pass">
                </div>
            </div>
            <div class="control-group">
                <div class="controls xt-last">
                    <label class="checkbox hide">
                        <input type="checkbox" name="rememberme" id="rememberme" value="forever" checked> 记住我的登录信息
                    </label>
                    <button name="wp-submit" id="wp-submit" class="btn btn-primary" data-loading-text="登录中...">登录</button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-gray">忘记密码</a>
                    <input type="hidden" name="redirect_to" id="redirect_to" value="<?php echo esc_url($redirect_to); ?>">
                    <input type="hidden" name="testcookie" value="1">
                </div>
            </div>
        </div>
    </div>
    <div class="span6">
        <ul class="inline clearfix" style="margin-top: -8px;">
            <li><a rel="nofollow" class="btn btn-primary" href="<?php echo str_replace('[PLATFORM]', 'taobao', $_loginurl); ?>"><i class="xt-icon-taobao"></i>&nbsp;&nbsp;淘宝帐号登录</a></li>
            <li><a rel="nofollow" class="btn btn-primary" href="<?php echo str_replace('[PLATFORM]', 'weibo', $_loginurl); ?>"><i class="xt-icon-weibo"></i>&nbsp;&nbsp;微博帐号登录</a></li>
            <li><a rel="nofollow" class="btn btn-primary" href="<?php echo str_replace('[PLATFORM]', 'qq', $_loginurl); ?>"><i class="xt-icon-qq"></i>&nbsp;&nbsp;Q&nbsp;Q&nbsp;帐号登录</a></li>
            <li></li>
        </ul>
        <?php if (get_option('users_can_register')) { ?>
            <div class="clearfix" style="margin-top: 26px;">
                还没有账号，<a rel="nofollow" href="<?php echo esc_url(site_url('wp-login.php?action=register&redirect_to=' . urlencode($redirect_to), 'login')); ?>" target="_top"">立刻注册</a>
            </div>
        <?php } ?>
    </div>
</div>