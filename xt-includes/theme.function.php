<?php
/**
 * WP Xintaoke theme functions
 *
 * These are the functions for the wp-Xintaoke theme engine
 *
 */

/**
 * Displays the database update notice
 * @access public
 *
 * @since 3.8
 * @param null
 * @return null
 */
function xt_admin_notice_installing() {
    ?>

    <div class="error fade">
        <p><?php
    printf('<strong>需要到新淘客WordPress插件平台激活初始化您的网站</strong>. <br>
	            <ul>
	            <li>1.注册并登录<a href="%1s" target="_blank">新淘客WordPress插件平台</a>(已注册的会员可直接登录)，添加网站</li>
	            <li>2.验证并添加该网站（<a href="' . admin_url('admin.php?page=xt_menu_sys&xt-action=tool') . '">填写验证代码</a>）</li>', XT_API_URL)
    ?></p>
    </div>

    <?php
}

function xt_admin_notice_app() {
    ?>

    <div class="error fade">
        <p><?php
    printf('<strong>尚未配置新淘客WordPress插件平台的AppKey,AppSecret,无法正常使用装修功能</strong>. <br>
	            <ul>
	            <li>1.登录<a href="%1s" target="_blank">新淘客WordPress插件平台</a></li>
	            <li>2.管理中心---找到该网站---管理---复制AppKey,AppSecret</li>
	            <li>3.回到您的WordPress后台---新淘客---<a href="' . admin_url('admin.php?page=xt_menu_sys&xt-action=platform') . '">平台设置</a>---粘贴AppKey,AppSecret---保存</li>
	            </ul>', XT_API_URL)
    ?></p>
    </div>

    <?php
}

function xt_admin_notice_app_taobao() {
    ?>

    <div class="error fade">
        <p>
            <strong>尚未配置淘宝开放平台的AppKey,AppSecret,无法正常调用淘宝数据</strong>. <br>
        <ul><li>WordPress管理后台---新淘客---<a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=platform'); ?>">平台设置</a>---淘宝</li></ul>
    </p>
    </div>

    <?php
}

function xt_admin_notice_app_taobao_tkpid() {
    ?>

    <div class="error fade">
        <p>
            <strong>尚未配置阿里妈妈淘点金PID,您站内的普通淘宝链接将无法转换为推广链接</strong>. <br>
        <ul><li>WordPress管理后台---新淘客---<a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=platform'); ?>">平台设置</a>---淘宝</li></ul>
    </p>
    </div>

    <?php
}

function xt_admin_notice_app_taobao_s8pid() {
    ?>

    <div class="error fade">
        <p>
            <strong>尚未配置阿里妈妈完整PID,您站内的淘宝搜索推广链接有可能无法正常获得佣金</strong>. <br>
        <ul><li>WordPress管理后台---新淘客---<a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=platform'); ?>">平台设置</a>---淘宝</li></ul>
    </p>
    </div>

    <?php
}

function xt_admin_notice_app_paipai() {
    ?>

    <div class="error fade">
        <p>
            <strong>尚未配置拍拍开放平台的开发者QQ号,appOAuthID,secretOAuthKey,无法正常调用拍拍数据</strong>. <br>
        <ul><li>WordPress管理后台---新淘客---<a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=platform'); ?>">平台设置</a>---拍拍</li></ul>
    </p>
    </div>

    <?php
}

function xt_admin_notice_app_paipai_session() {
    ?>

    <div class="error fade">
        <p>
            <strong>尚未配置拍拍accessToken,推广ID,无法正常获取拍拍客推广订单记录</strong>. <br>
        <ul><li>WordPress管理后台---新淘客---<a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=platform'); ?>">平台设置</a>---拍拍</li></ul>
    </p>
    </div>

    <?php
}

function xt_admin_notice_template() {
    ?>

    <div class="error fade">
        <p>
            <strong>请把文件"<?php echo XT_THEME_PATH; ?>/xt-page.php"手动复制到"<?php echo get_template_directory(); ?>"下</strong>. <br>
        </p>
    </div>
    <?php
}

function xt_admin_notice_permalink_structure() {
    ?>

    <div class="error fade">
        <p>
            <strong>固定链接格式不能为默认,请进入<a href="<?php echo admin_url('options-permalink.php') ?>">固定链接</a>设置中,设置为其他形式,建议使用文章名</strong>. <br>
        </p>
    </div>
    <?php
}

function xt_admin_notices() {
    global $wpdb;
    $option_env = get_option(XT_OPTION_ENV);
    $env = IS_BAE ? 'BAE' : (IS_SAE ? 'SAE' : 'VPS');
    if (empty($option_env)) {
        update_option(XT_OPTION_ENV, $env);
    } else {
        if ($env != $option_env) {
            update_option(XT_OPTION_ENV, $env);
        }
    }
    $permalink_structure = get_option('permalink_structure');
    if (empty($permalink_structure)) {
        add_action('admin_notices', 'xt_admin_notice_permalink_structure');
        return true;
    }
    //HOME
    $base = xt_base();
    if (!empty($base)) {
        $home_id = $wpdb->get_var("SELECT id FROM `" . $wpdb->posts . "` WHERE `post_name` = '" . $base . "'	AND `post_type` != 'revision'");
        if (empty($home_id)) {
            require_once XT_PLUGIN_DIR . '/xt-core/xt-installer-functions.php';
            $home_id = xt_install_home();
        }
    }
    //MENUS
    $global = get_option(XT_OPTION_GLOBAL);
    if (!$global['isMenu']) {
        require_once XT_PLUGIN_DIR . '/xt-core/xt-installer-functions.php';
        xt_install_menu($global);
    }
    $isInstalled = get_option(XT_OPTION_INSTALLED);
    if (!$isInstalled) {
        add_action('admin_notices', 'xt_admin_notice_installing');
    } else {

        if (!file_exists(get_template_directory() . '/xt-page.php')) {
            if (!@ copy(XT_THEME_PATH . '/xt-page.php', get_template_directory() . '/xt-page.php')) {
                add_action('admin_notices', 'xt_admin_notice_template');
                return true;
            }
        }
        $app = xt_is_ready();
        if (empty($app)) {
            add_action('admin_notices', 'xt_admin_notice_app');
            return true;
        }
        $app = xt_taobao_is_ready();
        if (empty($app)) {
            add_action('admin_notices', 'xt_admin_notice_app_taobao');
            return true;
        }
        if (!isset($app['tkpid']) || empty($app['tkpid'])) {
            add_action('admin_notices', 'xt_admin_notice_app_taobao_tkpid');
            return true;
        }
        if (!isset($app['s8pid']) || empty($app['s8pid'])) {
            add_action('admin_notices', 'xt_admin_notice_app_taobao_s8pid');
            return true;
        }
//        $app = xt_taobao_is_session_ready();
//        if (empty($app)) {
//            add_action('admin_notices', 'xt_admin_notice_app_taobao_session');
//            return true;
//        }
        $app = xt_paipai_is_ready();
        if (empty($app)) {
            add_action('admin_notices', 'xt_admin_notice_app_paipai');
            return true;
        }
        $app = xt_paipai_is_session_ready();
        if (empty($app)) {
            add_action('admin_notices', 'xt_admin_notice_app_paipai_session');
            return true;
        }
    }
}

add_action('admin_init', 'xt_admin_notices');

function xt_print_footer_scripts() {
    
}

function xt_print_login_scripts() {
    
}

function xt_print_login_styles() {
    $theme_settings = get_option(XT_OPTION_THEME_SETTING);
    $linkColor = '';
    if (!empty($theme_settings) && isset($theme_settings['linkColor']) && !empty($theme_settings['linkColor'])) {
        $linkColor = $theme_settings['linkColor'];
    }
    $http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
    ?>
    <link rel='stylesheet' href='<?php echo XT_THEME_URL; ?>/bootstrap.min.css?v=<?php echo XT_STATIC_VERSION; ?>' type='text/css' media='all' />
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" href="<?php echo XT_THEME_URL; ?>/bootstrap-ie6.min.css">
    <![endif]-->
    <!--[if lte IE 7]>
    <link rel="stylesheet" type="text/css" href="<?php echo XT_THEME_URL; ?>/ie.css">
    <![endif]-->
    <link rel='stylesheet' href='<?php echo XT_THEME_URL; ?>/xintaoke.min.css?v=<?php echo XT_STATIC_VERSION; ?>' type='text/css' media='all' />
    <style type="text/css">
        body.login{margin:0 auto;width:680px;background:none;position:relative;}#login {padding: 10px 0 0;width:312px;margin-left: 0px;float:left;margin-top:20%;}#login h1{display:none;}
        #login_error, .login .message{margin-top:-61px;margin-left: 0px;width:600px;}
        <?php
        if (isset($_GET['action']) && $_GET['action'] == 'resetpass' && !isset($_GET['error'])) {
            echo '#X_Login-Right{display:none;}';
        } elseif ($http_post || isset($_GET['error'])) {
            echo '.login .message{display:none;}';
        }
        ?>
        #login form{margin:0 auto;}
        .login form .input, .login input[type="text"] {height:32px;}
        #X_Login-Right{float:left;padding: 10px 0 0;margin-top:20%;margin-left: 20px;width:332px;padding-top:58px;}
        .xt-third-login{margin-top:0px;}
        .xt-third-login ul.inline li{margin: 0 16px 8px 0;float: left;padding: 0;_display: inline;}
        .xt-third-login .btn{padding: 6px 12px;width: 110px;-webkit-border-radius: 0;-moz-border-radius: 0;border-radius: 0;}
        <?php
        if (!empty($linkColor)) {
            echo '.login #nav a, .login #backtoblog a{color:' . $linkColor . '!important;}';
        }
        ?>

        <?php echo str_replace(array('.btn-primary{'), array('.btn-primary,#wp-submit{'), xt_get_theme()); ?>

    </style>
    <?php
}

add_action('login_head', 'xt_print_login_styles');
add_action('login_head', 'xt_print_login_scripts');

function xt_login_right() {
    $_loginurl = xt_platform_authorize_url('[PLATFORM]', urlencode(!empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : home_url()), (!empty($_REQUEST['mode']) ? htmlspecialchars($_REQUEST['mode']) : ''));
    ?>
    <div id="X_Login-Right">
        <div class="xt-third-login">
            <ul class="inline clearfix">
                <li><a class="btn btn-primary" rel="nofollow" href="<?php echo str_replace('[PLATFORM]', 'taobao', $_loginurl); ?>"><i class="xt-icon-taobao"></i>&nbsp;&nbsp;淘宝帐号登录</a></li>
                <li><a class="btn btn-primary" rel="nofollow" href="<?php echo str_replace('[PLATFORM]', 'weibo', $_loginurl); ?>"><i class="xt-icon-weibo"></i>&nbsp;&nbsp;微博帐号登录</a></li>
                <li><a class="btn btn-primary" rel="nofollow" href="<?php echo str_replace('[PLATFORM]', 'qq', $_loginurl); ?>"><i class="xt-icon-qq"></i>&nbsp;&nbsp;Q&nbsp;Q&nbsp;帐号登录</a></li>
            </ul>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php
}

add_action('login_footer', 'xt_login_right');

function xt_register_form_fields() {
    ?>
    <style type="text/css">
        #reg_passmail{display:none}
    </style>
    <p><label for="user_pass">登录密码<br><input type="password" name="user_pass" id="user_pass" class="input" value="" size="20" tabindex="31"></label></p>
    <p><label for="user_pass1">确认密码<br><input type="password" name="user_pass1" id="user_pass1" class="input" value="" size="20" tabindex="32"></label></p>
    <?php
}

function xt_register_form_fields_check($login, $email, $errors) {
    if (strlen($_POST['user_pass']) < 6)
        $errors->add('password_length', "<strong>错误</strong>：密码不能少于6位");
    elseif ($_POST['user_pass'] != $_POST['user_pass1'])
        $errors->add('password_error', "<strong>错误</strong>：两次输入的密码不一致");
    do_action('xt_register_form_fields_check');
}

function xt_register_from_fields_save($user_id, $password = "", $meta = array()) {
    global $xt_during_user_creation;
    if (!$xt_during_user_creation) {

        $user = new WP_User($user_id);
        $user_login = $user->user_login;

        xt_fanxian_invite($user_id); //invite

        if (xt_is_fanxian()) { //cash ,jifen
            $registe_cash = xt_fanxian_registe_cash();
            $registe_jifen = xt_fanxian_registe_jifen();
            if (intval($registe_cash) > 0 || intval($registe_jifen) > 0) {
                xt_new_fanxian(array(
                    'platform' => 'xt',
                    'trade_id' => $user_id,
                    'type' => 'REGISTE',
                    'user_id' => $user_id,
                    'user_name' => $user_login,
                    'fanxian' => intval($registe_cash),
                    'jifen' => intval($registe_jifen),
                    'create_time' => current_time('mysql'),
                    'order_time' => current_time('mysql')
                ));
            }
        }
        do_action('xt_register_from_fields_save');
        if (isset($_POST['user_pass']) && !empty($_POST['user_pass'])) {
            $userdata = array();
            $userdata['ID'] = $user_id;
            $userdata['user_pass'] = $_POST['user_pass'];

            wp_new_user_notification($user_id, $_POST['user_pass'], 1);
            wp_update_user($userdata);

            //auto login
            wp_set_auth_cookie($user_id, true, false);
            wp_set_current_user($user_login);
            do_action('wp_login', $user_login);
            wp_redirect(home_url());
            exit;
        }
    }
}

function xt_register_remove_default_password_nag() {
    global $user_ID;
    delete_user_setting('default_password_nag', $user_ID);
    update_user_option($user_ID, 'default_password_nag', false, true);
}

add_action('admin_init', 'xt_register_remove_default_password_nag');
add_action('register_form', 'xt_register_form_fields');
add_action('register_post', 'xt_register_form_fields_check', 10, 3);
add_action('user_register', 'xt_register_from_fields_save');