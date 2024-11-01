<?php
xt_taobao_jssdk_cookie(); //TOP JSSDK COOKIE
global $wp_query, $xt_share_guid;
$xt_jump_param = $wp_query->query_vars['xt_param'];
$_type = $xt_jump_param['type'];
$_id = $xt_jump_param['id'];
$_url = $xt_jump_param['url'];
$_fx = $xt_jump_param['fx'];
$_title = $xt_jump_param['title'];
$_q = '';
$xt_share_guid = $xt_jump_param['share'];
$IS_URL_CONVERT = $IS_CONVERT = false;
$IS_URL_LOGIN_SHOW = true;
$IS_JUEJINLIAN = false;
$IS_JUEJINLIAN_GO = false;
if (!empty($_title)) {
    $_title = urldecode($_title);
}
if (!empty($_url)) {
    $_url = base64_decode($_url);
} else {
    $_url = '';
}
$iframe_url = $_url;
switch ($_type) {
    case 'mall':
        if (!empty($_id)) {
            $campaign = xt_get_website($_id);
            if (!empty($campaign)) {
                $_title = $campaign['name'];
                $_url = $iframe_url = $campaign['url']; //add_query_arg(array('t' => $campaign['topUrl']), $campaign['billLinkUrl']);
            }
        }
        $IS_JUEJINLIAN = true;
        break;
    case 'activity':
    case 'tuan':
    case 'bijia':
        parse_str($_url, $_bijia);
        if (isset($_bijia['t']) && !empty($_bijia['t'])) {
            $_url = $iframe_url = $_bijia['t'];
        }
        $IS_JUEJINLIAN = true;
        break;
    case 'taobao' :
        if (!empty($_id)) {
            $iframe_url = 'http://item.taobao.com/item.htm?id=' . $_id;
            if (!empty($_title))
                $_title = '淘宝 - ' . $_title;
            else
                $_title = '淘宝';
        }elseif (!empty($_url) && !xt_is_clickurl($_url)) {
            $iframe_url = $_url;
            if (!empty($_title))
                $_title = $_title . ' - 淘宝';
        } else {
            $_q = $_title;
            $_title = '淘宝';
        }
        break;
    case 'paipai' :
        $iframe_url = 'http://auction1.paipai.com/' . $_id;
        if (!empty($_title))
            $_title = '拍拍 - ' . $_title;
        else
            $_title = '拍拍';
        break;
    case 'coupon' :
        $iframe_url = 'http://item.taobao.com/item.htm?id=' . $_id;
        $_title = '淘宝折扣 - ' . $_title;
        break;
    case 'temai' :
        $iframe_url = $_url;
        $_title = '淘宝特卖 - ' . $_title;
}
if ($_type == 'temai' && !empty($_url)) {
    $_items = xt_taobaoke_item('', $_id, 'click_url');
    if (is_wp_error($_items) || empty($_items)) {
        
    } else {
        $_url = $_items[0]->click_url;
    }
} elseif ($_type == 'taobao' && empty($_url)) {
    if (!empty($_id)) {
        $_items = xt_taobaoke_item($_id, '', 'title,click_url');
        if (is_wp_error($_items) || empty($_items)) {
            $_url = $iframe_url;
        } else {
            $_url = $_items[0]->click_url;
            $_title = '淘宝 - ' . $_items[0]->item->title;
            if (!xt_fanxian_is_sharebuy() && !empty($xt_share_guid)) {
                $IS_CONVERT = false;
            } else {
                $IS_CONVERT = true;
            }
        }
    } elseif (!empty($_q)) {
        $listurl = xt_taobaoke_listurl($_q);
        if (is_wp_error($listurl)) {
            wp_die($listurl->get_error_message());
        } else {
            $_url = $listurl;
        }
    }
} elseif ($_type == 'taobao' && !empty($_url) && empty($_id) && empty($_q)) {
    $IS_URL_CONVERT = true;
} elseif ($_type == 'paipai' && empty($_url)) {
    $resp = xt_paipaike_item($_id);
    if (!is_wp_error($resp)) {
        if (isset($resp->cpsSearchCommData)) {
            $taoke = (array) $resp->cpsSearchCommData;
            $_url = $taoke['sClickUrl'];
            $_title = '拍拍 - ' . $taoke['sTitle'];
            $RATE = xt_get_rate();
            if ($RATE > 0) {
                if ($taoke['dwIsCpsFlag'] && $taoke['dwActiveFlag']) {
                    if ($taoke['dwPrimaryCmm']) {
                        $_fx = number_format($taoke['dwPrice'] * $taoke['dwPrimaryRate'] / (10000 * 100), 2);
                    } else {
                        $_fx = number_format($taoke['dwPrice'] * $taoke['dwClassRate'] / (10000 * 100), 2);
                    }
                }
            }
        }
    }
    if (empty($_url)) {
        $_url = $iframe_url;
    }
}
$_url = xt_refresh_url($_url);
if (empty($_url)) {
    wp_redirect(home_url());
    exit();
}
$user = wp_get_current_user();
if ($IS_JUEJINLIAN) {
    if (!xt_is_forcelogin() || $user->exists()) {
        $IS_JUEJINLIAN_GO = true;
    }
} else {
    if (!empty($_url) && (!xt_is_forcelogin() || $user->exists())) {
        $IS_URL_LOGIN_SHOW = false;
        if ($_type == 'taobao' && empty($_id) && empty($_q)) {
            if (xt_is_clickurl($_url)) {
                wp_redirect($_url);
                exit();
            }
        } else {
            wp_redirect($_url);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <meta name="robots" content="noindex,nofollow">
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width" />
        <title><?php
echo $_title . ' - ';
// Add the blog name.
bloginfo('name');
?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel='stylesheet' href='<?php echo XT_THEME_URL; ?>/bootstrap.min.css?v=<?php echo XT_STATIC_VERSION; ?>' type='text/css' media='all' />
        <!--[if lte IE 6]>
        <link rel="stylesheet" type="text/css" href="<?php echo XT_THEME_URL; ?>/bootstrap-ie6.min.css">
        <![endif]-->
        <!--[if lte IE 7]>
        <link rel="stylesheet" type="text/css" href="<?php echo XT_THEME_URL; ?>/ie.css">
        <![endif]-->
        <link rel='stylesheet' href='<?php echo XT_THEME_URL; ?>/xintaoke.min.css?v=<?php echo XT_STATIC_VERSION; ?>' type='text/css' media='all' />
        <style type="text/css">
<?php echo xt_get_theme(); ?>
<?php echo xt_get_theme_custom(); ?>
<?php echo xt_is_fanxian() ? '' : '.xt-fanxian,.X_Fanxian{display:none;}' ?>
        </style>
        <?php
        xt_header_script();
        ?>
        <script type="text/javascript" src="<?php echo includes_url('/js/jquery/jquery.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/xintaoke-utils.min.js?v=' . XT_STATIC_VERSION; ?>"></script>
        <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/bootstrap.min.js?v=' . XT_STATIC_VERSION; ?>"></script>
        <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/xintaoke.min.js?v=' . XT_STATIC_VERSION; ?>"></script>
        <style type="text/css">
            body{background:none;}
        </style>
    </head>
    <body <?php body_class(); ?>>
        <?php if (!$IS_JUEJINLIAN_GO) { ?>
            <iframe frameborder="0" scrolling="no" src="<?php echo $iframe_url ?>" marginwidth="0" marginheight="0" width="100%" class="ifrnameurl" id="X_Iframe"></iframe>
            <?php
        }
        $codeAnalytics = xt_code_analytics();
        if (!empty($codeAnalytics)) {
            echo $codeAnalytics;
        }
        ?>
        <!--[if IE 6]>
                <script type="text/javascript" src="<?php echo XT_THEME_URL; ?>/bootstrap-ie.js"></script>
        <![endif]-->
    </body>
    <?php
    $TB_SHOW = false;
    if ($_type == 'taobao' && $IS_CONVERT && $_id > 0) {
        $app = xt_get_app_taobao();
        if (!empty($app) && !empty($app['appKey']) && !empty($app['appSecret'])) {
            if (xt_get_rate() > 0) {
                $TB_SHOW = true;
                ?>
                <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=<?php echo $app['appKey']; ?>"></script>
                <script type="text/javascript">
                    jQuery(function($){
                        var RATE = <?php echo xt_get_rate(); ?>;
                        var fx = '';
                        TOP.api({ 
                            method:'taobao.taobaoke.widget.items.convert', 
                            fields:'commission',
                            num_iids:<?php echo $_id ?>
                        },function(resp){
                            try{
                                if(resp.taobaoke_items.taobaoke_item){
                                    fx = Math.round((resp.taobaoke_items.taobaoke_item[0].commission*RATE*100))/10000;
            <?php
            if (xt_fanxian_is_jifenbao('taobao')) {
                echo "fx = Math.round(fx*100);";
            } else {
                echo "fx = Math.round(fx*100)/100;";
            }
            ?>
                            } 	
                        }catch(e){
                        }
                        var params={
                            page:'jump',
                            url:'<?php echo base64_encode($_url); ?>',
                            type:'<?php echo $_type ?>',
                            fx:fx,
                            from_type:'taobao',
                            mode:'popup'
                        }
                        XT_openLogin('<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>', params);
                    });
                });
                </script>
                <?php
            }
        }
    } elseif ($IS_URL_CONVERT) {
        $app = xt_get_app_taobao();
        if (!empty($app) && !empty($app['appKey']) && !empty($app['appSecret'])) {
            if (xt_get_rate() > 0) {
                $TB_SHOW = true;
                ?>
                <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=<?php echo $app['appKey'];
                ?>"></script>
                <script type="text/javascript">
                    jQuery(function($){
                        var RATE = <?php echo xt_get_rate(); ?>;
                        var url = '<?php echo $_url ?>';
                        var redirect_to = encodeURIComponent('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>');
                        TOP.api({ 
                            method:'taobao.taobaoke.widget.url.convert', 
                            url:'<?php echo $_url ?>',
                            outer_code:'<?php echo xt_outercode() ?>'
                        },function(resp){
                            try{
                                if(resp.taobaoke_item){
                                    url = resp.taobaoke_item.click_url;
                                    redirect_to = '<?php echo xt_jump_url(array('type' => 'url', 'url' => '_URL_')); ?>';
                                    redirect_to = encodeURIComponent(redirect_to.replace(encode64('_URL_'), encode64(url)));
                                } 	
                            }catch(e){
                            }
            <?php if ($IS_URL_LOGIN_SHOW) {
                ?>
                                var params={
                                    page:'jump',
                                    url:encode64(url),
                                    type:'<?php echo $_type ?>',
                                    fx:0,
                                    from_type:'taobao',
                                    mode:'popup'
                                }
                                XT_openLogin(redirect_to, params);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
            <?php } else {
                ?>
                                top.location.href=url;
                <?php
            }
            ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                    });
                });
                </script>
                <?php
            }
        }
    } elseif ($IS_JUEJINLIAN) {
        $app = xt_get_app_yiqifa();
        if (isset($app['wid']) && !empty($app['wid']) && is_numeric($app['wid'])) {
            ?>
            <script type='text/javascript'>
                var _jjl = new Date().toDateString().replace(/\s/g, '') + new Date().toTimeString().replace(/:\d{2}:\d{2}\sUTC[+]\d{4}$/g, '');
                document.write(unescape("%3Cscript src='http://p.yiqifa.com/js/juejinlian.js' type='text/javascript'%3E%3C/script%3E"));
                document.write(unescape("%3Cscript src='http://p.yiqifa.com/jj?_jjl.js' type='text/javascript'%3E%3C/script%3E"));
                document.write(unescape("%3Cscript src='http://p.yiqifa.com/js/md.js' type='text/javascript'%3E%3C/script%3E"));
            </script> 
            <script type='text/javascript'>
                try{ 
                    var siteId = <?php echo $app['wid'] ?>;
                    document.write(unescape("%3Cscript src='http://p.yiqifa.com/jj?sid=" + siteId + "&_jjl.js' type='text/javascript'%3E%3C/script%3E"));
                    var jjl = JueJinLian._init(); 
                    jjl._addWid(siteId);
                    jjl._addE('<?php echo xt_outercode() ?>');
                    jjl._addScope(1);
                    jjl._run(); 
                }catch(e){}
            </script>
            <?php
        }
    }
    ?>
    <script type="text/javascript">
        function XT_location(){
            var url = '<?php echo $_url ?>';
<?php
if ($IS_JUEJINLIAN) {
    ?>
                var j = JueJinLian;
                var m = url;
                if(j._isChange(url)){
                    j._connAdd(j);
                    if(j._tao_channel == 0 && j._is_tao == 0){
                        m = j._url + "fit=t" + j.adds + encodeURIComponent(m);
                    }
                    if(j._is_yiqifa == 0 && !(j._tao_channel == 0 && j._is_tao == 0) && j._si_tao != 0){
                        m = j._url + "fit=b" + j.adds + encodeURIComponent(m);
                    }
                    if(j._is_yiqifa == 1 && !(j._tao_channel == 0 && j._is_tao == 0) && j._si_tao != 0 && j._basic_channel == 1){
                        m = j._url + "fit=a" + j.adds + encodeURIComponent(m);
                    }
                }
                url = m;   
                j._reset();
    <?php
}
?>
        return url;
    }
    function XT_openLogin(redirect,params) {
        if (typeof(redirect) == 'undefined' || redirect == '') {
            redirect = location.href;
        }
        if(typeof(params)=='undefined'||!params){
            params = {};
        }
        params.action='xt_ajax_login_box';
        params.redirect_to = redirect;
        params.mode = 'popup';
        XT.ajaxModalPost('登录',XT.ajaxurl,params,function(){
            jQuery('#user_login').focus();
            jQuery('#X_Modal').on('hidden', function () {
                document.location.href=XT_location();
            });
            jQuery('#user_pass').keydown(function() {
                var code;
                if (!e)
                    var e = window.event;
                if (e.keyCode) {
                    code = e.keyCode;
                } else if (e.which) {
                    code = e.which;
                }
                if (code == 13) {
                    jQuery('#wp-submit').click();
                    return false;
                }
                return true;
            });
            jQuery('#wp-submit').unbind('click').click(function(){
                jQuery('#X_Login-Error').html('').hide();
                var self = jQuery(this);
                self.button('loading');
                XT.login(jQuery('#user_login').val(),jQuery('#user_pass').val(),1,function(response){
                    if(response.code>0){
                        jQuery('#X_Login-Error').html(response.msg).fadeIn();
                    }else{
                        document.location.href = decodeURIComponent(jQuery('#redirect_to').val());
                    }
                    self.button('reset');                    
                },function(request, error, status){
                    alert(request.responseText);
                    self.button('reset');
                });
            });
        },'xt-login-popup',250);
    }
    jQuery(document).ready(function($){
        $('#X_Iframe').attr('height', $(window).height() + 'px');
        $(window).resize(function() {
            $('#X_Iframe').attr('height', $(window).height() + 'px');
        });
<?php if ($IS_JUEJINLIAN_GO) {
    ?>
                document.location.href=XT_location();
    <?php
} elseif (!$TB_SHOW) {
    ?>
                var params={
                    page:'jump',
                    url:'<?php echo base64_encode($_url); ?>',
                    type:'<?php echo $_type ?>',
                    fx:'<?php echo $_fx ?>',
                    from_type:'taobao',
                    mode:'popup'
                }
                XT_openLogin('<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>', params);
<?php } ?>
    });
    </script>
</html>