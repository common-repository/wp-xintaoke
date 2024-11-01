<?php
global $xt, $wp_query;
if ($xt->is_error404) {
    xt_set_404();
    status_header(404);
    nocache_headers();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <?php
        do_action('xt_header_start');
        ?>
        <?php
        if ($xt->is_brands || $xt->is_stars || $xt->is_activities || $xt->is_taoquan || $xt->is_error404) {
            ?>
            <meta name="robots" content="nofollow">
                <?php
            }
            ?>
            <meta charset="<?php bloginfo('charset'); ?>" />
            <?php
            $verification = get_option(XT_OPTION_VERIFICATION);
            if (!empty($verification)) {
                echo stripslashes($verification);
            }
            ?>
            <!--<meta name="viewport" content="width=device-width, initial-scale=1.0" />-->
            <title><?php
            wp_title('-', true, 'right');
            ?></title>
            <?php xt_meta(); ?>
            <link rel="profile" href="http://gmpg.org/xfn/11" />
            <link rel='stylesheet' href='<?php echo XT_THEME_URL; ?>/bootstrap.min.css?v=<?php echo XT_STATIC_VERSION; ?>' type='text/css' media='all' />
            <!--[if lte IE 6]>
            <link rel="stylesheet" type="text/css" href="<?php echo XT_THEME_URL; ?>/bootstrap-ie6.min.css">
            <![endif]-->
            <!--[if lte IE 7]>
            <link rel="stylesheet" type="text/css" href="<?php echo XT_THEME_URL; ?>/ie.css">
            <![endif]-->
            <link rel='stylesheet' href='<?php echo XT_THEME_URL; ?>/xintaoke.min.css?v=<?php echo XT_STATIC_VERSION; ?>' type='text/css' media='all' />
            <!--[if lt IE 9]>
            <script src="<?php echo XT_CORE_JS_URL; ?>/html5.js" type="text/javascript"></script>
            <![endif]-->
            <?php
            xt_header_script();
            wp_enqueue_script('jquery');
            print_head_scripts();
            ?>
            <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/xintaoke-utils.min.js?v=' . XT_STATIC_VERSION; ?>"></script>
            <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/bootstrap.min.js?v=' . XT_STATIC_VERSION; ?>"></script>
            <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/xintaoke.min.js?v=' . XT_STATIC_VERSION; ?>"></script>
            <?php if ($xt->is_account): ?>
                <link rel='stylesheet' href='<?php echo XT_CORE_CSS_URL . '/jquery-ui-1.8.24.custom.css'; ?>' type='text/css' media='all' />	
                <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/jqueryui/jquery-ui-1.9.1.custom.min.js'; ?>"></script>
                <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/clipboard/ZeroClipboard.min.js'; ?>"></script>
                <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/jquery.validate.min.js'; ?>"></script>
                <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/jqueryui/jquery.ui.datepicker.min.js'; ?>"></script>	
                <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/xt-account.min.js?v=' . XT_STATIC_VERSION; ?>"></script>
            <?php endif; ?>
            <?php
            $app = xt_get_app_taobao();
            if (!empty($app) && !empty($app['appKey']) && !empty($app['appSecret'])) {
                ?>
                <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=<?php echo $app['appKey']; ?>"></script>
                <?php
            }
            ?>
            <style type="text/css">
<?php
$theme_setting = xt_get_theme_setting();
if (!empty($theme_setting)) {
    if (isset($theme_setting['grayScale']) && absint($theme_setting['grayScale']) > 0) {
        if (isset($theme_setting['grayScaleHome']) && absint($theme_setting['grayScaleHome']) === 0) {
            echo 'html{-webkit-filter: grayscale(' . $theme_setting['grayScale'] . '%);filter:gray;filter:progidXImageTransform.Microsoft.BasicImage(grayscale=' . ($theme_setting['grayScale'] / 100) . ');} ';
        } else {
            if ($xt->is_index) {
                echo 'html{-webkit-filter: grayscale(' . $theme_setting['grayScale'] . '%);filter:gray;filter:progidXImageTransform.Microsoft.BasicImage(grayscale=' . ($theme_setting['grayScale'] / 100) . ');} ';
            }
        }
    }
}
?>
<?php echo xt_get_theme(); ?>
<?php echo xt_is_fanxian() ? '' : '.xt-fanxian,.xt-fanxian-tip,.X_Fanxian,.X_Fanxian-Tip{display:none;}' ?>
            </style>
            <?php
            do_action('xt_header_end');
            ?>
    </head>
    <?php
    $body_class = isset($wp_query->query_vars['xt_action']) ? ('xt-body-' . $wp_query->query_vars['xt_action']) : '';
    if (empty($body_class)) {
        if (($xt->is_page || $xt->is_daogou) && isset($wp_query->post) && isset($wp_query->post->ID)) {
            $body_class = 'xt-body-' . $wp_query->post->ID;
        } elseif ($xt->is_error404) {
            $body_class = 'xt-body-error404';
        }
    }
    if ($xt->is_taobaos || $xt->is_paipais || $xt->is_bijias) {
        $body_class.=' xt-body-widefat';
    }
    ?>
    <body class="<?php echo $body_class ?>">
