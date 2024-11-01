<?php

/**
 * Taobao Chongzhi widget class
 *
 */
class XT_Widget_Taobao_Chongzhi extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-taobao-chongzhi',
            'description' => '淘宝充值框'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('taobaochongzhi', '淘宝充值框', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => XT_WIDGET_EDIT_PAGE_COMMON,
            'layout' => '3,12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $pid = $instance['pid'];
        echo $before_widget;
        if (empty($pid)) {
            echo '<h4>尚未配置充值框PID</h4>';
        } else {
            if ($layout == 'span3') {
                ?>
                <iframe name="alimamaifrm" frameborder="0" marginheight="0" marginwidth="0" border="0" scrolling="no" width="233" height="200" style="margin-bottom: -5px;" data-src="http://www.taobao.com/go/app/tbk_app/chongzhi_210_200.php?pid=<?php echo $pid ?>&page=chongzhi_210_200.php&size_w=210&size_h=200&stru_phone=1&stru_game=1&stru_travel=1&size_cat=std" ></iframe>
                <?php
            } elseif ($layout == 'span12') {
                ?>
                <iframe name="alimamaifrm" frameborder="0" marginheight="0" marginwidth="0" border="0" scrolling="no" width="1000" height="30" style="margin-bottom: -5px;" data-src="http://www.taobao.com/go/app/tbk_app/chongzhi_950_30.php?pid=<?php echo $pid ?>&page=chongzhi_950_30.php&size_w=950&size_h=30&stru_phone=1&stru_game=1&stru_travel=1&size_cat=std" ></iframe>
                <?php
            }
        }
        echo $after_widget;
    }

}

/**
 * Daogou Categories widget class
 *
 */
class XT_Widget_Daogou_Category extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-daogou-category',
            'description' => '导购文章分类'
        );
        $control_ops = array(
            'width' => 800,
            'height' => 350
        );
        parent :: __construct('daogoucategory', '导购文章分类', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => XT_WIDGET_EDIT_PAGE_COMMON,
            'layout' => '3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        if (empty($title)) {
            $title = '导购分类';
        }
        global $xt, $xt_daogou_itemcat;
        $dataType = $instance['dataType'];
        $cids = $instance['cids'];
        $terms = array();
        if ($dataType == 'all') {
            $terms = get_terms('daogou_category', array('get' => 'all'));
        } else {
            if (!empty($cids))
                $terms = get_terms('daogou_category', array('include' => $cids));
        }
        if (!empty($terms)) {
            echo $before_widget;
            echo '<div class="hd"><h4 class="xt-bd-l"><a href="' . xt_get_daogou_search_url() . '" class="text-gray">' . $title . '</a></h4></div>';
            echo '<div class="bd clearfix"><ul class="xt-bd-list unstyled">';
            foreach ($terms as $term) {
                $active = '';
                if (!empty($xt_daogou_itemcat)) {
                    if ($xt->is_daogous) {
                        if ($xt_daogou_itemcat->term_id == $term->term_id) {
                            $active = ' class="xt-bg-l active" ';
                        }
                    }
                }
                echo '<li' . $active . '><a href="' . xt_get_daogou_search_url(array('cid' => $term->term_id)) . '">' . $term->name . '</a></li>';
            }
            echo '</ul></div>';
            echo $after_widget;
        }
    }

}

/**
 * MeilishuoSearchbox widget class
 *
 */
class XT_Widget_MeilishuoSearchbox extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-meilishuosearchbox',
            'description' => '左搜索,中LOGO,右登录'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('meilishuosearchbox', '左搜索,中LOGO,右登录', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'header',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $search_items = $instance['search_items'];
        $logo = $instance['logo'];
        echo $before_widget;
        ?>
        <div class="clearfix<?php echo empty($search_items) ? ' xt-nosearch' : '' ?>">
            <?php
            if (!empty($search_items)) {
                echo '<div class="pull-left xt-first">';
                $searchs = xt_search();
                $_search_items = array();
                global $xt, $wp_query;
                $s = '';
                if ($xt->is_shares || $xt->is_albums || $xt->is_paipais || $xt->is_users) {
                    $xt_share_param = $wp_query->query_vars['xt_param'];
                    if (isset($xt_share_param['s'])) {
                        $s = $xt_share_param['s'];
                    } elseif (isset($xt_share_param['keyWord'])) {
                        $s = $xt_share_param['keyWord'];
                    }
                }
                foreach ($search_items as $key) {
                    if (isset($searchs[$key])) {
                        $_search_items[] = '在<em>' . $searchs[$key]['title'] . '</em>里找"' . strtoupper($key) . '"';
                    }
                }
                $_search_items_length = count($_search_items);
                $_search_items = json_encode($_search_items);
                echo '<form class="X_Search-Form-Dropdown" method="get" target="_blank"><div class="input-append"><input type="text" placeholder="搜索" data-items="' . $_search_items_length . '" data-source=\'' . $_search_items . '\' value="' . $s . '"/><button type="submit" class="btn">搜索</button></div></form>';
                echo ' </div>';
            }
            ?>

            <div class="pull-left xt-last">
                <a href="<?php echo home_url(); ?>" class="<?php echo!empty($logo) ? ('xt-logo') : 'xt-nologo'; ?>"><?php echo!empty($logo) ? ('<img src="' . $logo . '">') : get_bloginfo('name'); ?></a>
            </div>
            <div class="pull-right">
                <?php
                $user = wp_get_current_user();
                if ($user->exists()) {
                    if (empty($user->display_name)) {
                        $user->display_name = $user->user_login;
                    }
                    global $wpdb;
                    $user_name = $wpdb->escape($user->display_name);
                    ?>
                    <div class="btn-group">
                        <a class="X_User-Share-Publish btn text-default" style="margin-right: -1px;" href="javascript:;">+ 分享我喜欢的</a>
                        <div class="btn-group">
                            <a class="btn dropdown-toggle" data-toggle="dropdown" href="javascript:;"><?php echo wp_trim_words($user_name, 8); ?>&nbsp;&nbsp;&nbsp;<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php xt_the_user_url($user->ID); ?>#share" class="X_Menu-Share-A">我的分享</a></li>
                                <li><a href="<?php xt_the_user_url($user->ID); ?>#like" class="X_Menu-Fav-A">我喜欢的</a></li>
                                <li><a href="<?php xt_the_user_url($user->ID); ?>#album" class="X_Menu-Album-A">我的专辑</a></li>
                                <li><a href="<?php echo xt_site_url('account#profile') ?>" class="X_Menu-Account-A">账号设置</a></li>
                                <?php if (current_user_can('edit_pages')): ?>
                                    <li class="xt-last"><a href="<?php echo admin_url() ?>">管理中心</a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo wp_logout_url() . '&redirect_to=' . $_SERVER['REQUEST_URI'] ?>">退出登录</a></li>
                            </ul>
                        </div>
                        <?php if (xt_is_fanxian()) { ?>
                            <a href="<?php echo xt_site_url('account') ?>" class="btn" style="margin-left: -1px;">我的返现</a>
                        <?php } ?>
                    </div>
                    <?php
                } else {
                    $_loginurl = xt_platform_authorize_url('[PLATFORM]', $_SERVER['REQUEST_URI']);
                    ?>
                    <div class="btn-group">
                        <a rel="nofollow" href="<?php echo str_replace('[PLATFORM]', 'taobao', $_loginurl); ?>" class="btn"><i class="xt-icon-taobao"></i>&nbsp;淘宝</a>
                        <a rel="nofollow" href="<?php echo str_replace('[PLATFORM]', 'weibo', $_loginurl); ?>" class="btn"><i class="xt-icon-weibo"></i>&nbsp;微博</a>
                        <a rel="nofollow" href="<?php echo str_replace('[PLATFORM]', 'qq', $_loginurl); ?>" class="btn"><i class="xt-icon-qq"></i>&nbsp;QQ</a>
                        <a class="X_User-Login btn text-default" href="javascript:;">登录</a>
                        <?php if (get_option('users_can_register')) : ?>
                            <a rel="nofollow" href="<?php echo esc_url(site_url('wp-login.php?action=register&redirect_to=' . urlencode($_SERVER['REQUEST_URI']), 'login')); ?>" class="btn text-default">注册</a>
                        <?php endif; ?>
                    </div>
                    <?php
                }
                ?>

            </div>
        </div>
        <?php
        echo $after_widget;
    }

}

/**
 * LogoSearchbox widget class
 *
 */
class XT_Widget_LogoSearchbox extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-searchbox xt-widget-logosearchbox',
            'description' => '带LOGO搜索框'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('logosearchbox', '带LOGO搜索框', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'header',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $search_items = $instance['search_items'];
        $type = isset($instance['type']) ? ($instance['type']) : 'top';
        $logo = $instance['logo'];
        $share = isset($instance['share']) && $instance['share'] ? 1 : 0;
        $sharecolor = isset($instance['sharecolor']) && $instance['sharecolor'] ? $instance['sharecolor'] : 'primary';
        echo $before_widget;
        $_marginTop = '';
        $_padddingTop = 'padding-bottom: 5px;';
        $_padddingBottom = 'padding-bottom: 5px;';
        if ($type == 'top') {
            $_padddingBottom = 'padding-bottom: 10px;';
        }
        if (count($search_items) == 1 && $type == 'top') {
            $_marginTop = 'margin-top:26px;';
        } elseif (count($search_items) > 1 && $type == 'top') {
            $_padddingTop = 'padding-top: 15px;';
        }
        ?>
        <div class="clearfix xt-<?php echo $type ?> <?php echo $share ? 'xt-share' : ' xt-noshare' ?> <?php echo count($search_items) == 1 ? 'xt-one' : '' ?>" style="<?php echo $_padddingTop . $_padddingBottom; ?>">
            <div class="pull-left xt-first">
                <a href="<?php echo home_url(); ?>"><?php echo!empty($logo) ? ('<img src="' . $logo . '">') : get_bloginfo('name'); ?></a>
            </div>
            <div class="pull-left xt-last" <?php echo $share && $type == 'left' ? '' : ('style="margin-left:50px;' . $_marginTop . '"') ?>>
                <?php
                _xt_widget_searchbox($type, $search_items);
                ?>
            </div>
            <?php
            if ($share) {
                ?>
                <div class="pull-right">
                    <button class="X_User-Share-Publish btn btn-<?php echo esc_attr($sharecolor) ?>">我要分享</button>
                </div>
            <?php } ?>
        </div>
        <?php
        echo $after_widget;
    }

}

/**
 * Toolbar widget class
 *
 */
class XT_Widget_Toolbar extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-toolbar',
            'description' => '顶部工具条'
        );
        $control_ops = array(
            'width' => 698,
            'height' => 350
        );
        parent :: __construct('toolbar', '顶部工具条', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'header',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $lefts = $instance['left'];
        $rights = $instance['right'];
        $bgClass = '';
        if ($instance['color'] == 'black') {
            echo str_replace('clearfix', 'clearfix xt-widefat xt-black', $before_widget);
        } elseif ($instance['color'] == 'grey') {
            echo str_replace('clearfix', 'clearfix xt-widefat xt-grey', $before_widget);
        } else {
            $bgClass = ' xt-bg-l';
            echo str_replace('clearfix', 'clearfix xt-widefat xt-bg-l', $before_widget);
        }
        ?>
        <div class="clearfix xt-first">
            <ul class="pull-left unstyled">
                <?php
                $isLeft = false;
                if (!empty($lefts)) {
                    foreach ($lefts as $left) {
                        if (!empty($left['title'])) {
                            $isLeft = true;
                            echo '<li><a href="' . ($left['link'] ? $left['link'] : 'javascript:;') . '">' . $left['title'] . '</a></li>';
                        }
                    }
                }
                ?>
            </ul>
            <ul class="pull-right unstyled">
                <?php
                if (!empty($rights)) {
                    if (isset($rights['login']) && $rights['login']) {
                        $user = wp_get_current_user();
                        if ($user->exists()) {
                            if (empty($user->display_name)) {
                                $user->display_name = $user->user_login;
                            }
                            global $wpdb;
                            $user_name = $wpdb->escape($user->display_name);
                            ?>
                            <li>欢迎您，<span><?php echo $user_name; ?></span></li>
                            <li class="dropdown dropdown-hover">
                                <a href="<?php xt_the_user_url($user->ID); ?>" class="dropdown-toggle" data-toggle="dropdown">个人中心</a>
                                <ul class="dropdown-menu<?php echo $bgClass; ?>">
                                    <li><a href="<?php xt_the_user_url($user->ID); ?>#share" class="X_Menu-Share-A">我的分享</a></li>
                                    <li><a href="<?php xt_the_user_url($user->ID); ?>#like" class="X_Menu-Fav-A">我喜欢的</a></li>
                                    <li><a href="<?php xt_the_user_url($user->ID); ?>#album" class="X_Menu-Album-A">我的专辑</a></li>
                                    <li><a href="<?php echo xt_site_url('account#profile') ?>" class="X_Menu-Account-A">账号设置</a></li>
                                    <?php if (current_user_can('edit_pages')): ?>
                                        <li class="xt-last"><a href="<?php echo admin_url() ?>">管理中心</a></li>
                                    <?php endif; ?>
                                    <li><a href="<?php echo wp_logout_url() . '&redirect_to=' . $_SERVER['REQUEST_URI'] ?>">退出登录</a></li>
                                </ul>
                            </li>
                            <?php if (xt_is_fanxian()) { ?>
                                <li><a href="<?php echo xt_site_url('account') ?>">我的返现</a></li>
                            <?php } ?>
                            <?php
                        } else {
                            echo '<li><a href="javascript:;" class="X_User-Login">登录</a></li>';
                            if (get_option('users_can_register')) {
                                echo '<li><a href="' . esc_url(site_url('wp-login.php?action=register&redirect_to=' . urlencode($_SERVER['REQUEST_URI']), 'login')) . '">注册</a></li>';
                            }
                            if (xt_is_fanxian()) {
                                echo '<li><a href="javascript:;" class="X_User-Need-Login">我的返现</a></li>';
                            }
                        }
                    }
                    if (isset($rights['favorite']) && $rights['favorite']) {
                        echo '<li><a href="javascript:;" class="X_User-addBrowserFavorite" data-title="' . esc_attr(get_bloginfo('name')) . '" data-url="' . esc_attr(home_url()) . '">加入收藏</a></li>';
                    }
                    if (isset($rights['follow']) && $rights['follow']) {
                        $lis = array();
                        if (!empty($rights['follow']['sina'])) {
                            $lis[] = '<li><a href="' . $rights['follow']['sina'] . '">新浪微博</a></li>';
                        }
                        if (!empty($rights['follow']['qq'])) {
                            $lis[] = '<li><a href="' . $rights['follow']['qq'] . '">腾讯微博</a></li>';
                        }
                        if (!empty($rights['follow']['qzone'])) {
                            $lis[] = '<li><a href="' . $rights['follow']['qzone'] . '">Q&nbsp;&nbsp;Q空间</a></li>';
                        }
                        if (!empty($rights['follow']['weixin'])) {
                            $lis[] = '<li><a href="' . $rights['follow']['weixin'] . '">微　　信</a></li>';
                        }
                        if (!empty($lis)) {
                            ?>
                            <li class="dropdown dropdown-hover">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">关注我们</a>
                                <ul class="dropdown-menu<?php echo $bgClass; ?>">
                                    <?php
                                    echo implode('', $lis);
                                    ?>
                                </ul>
                            </li>
                            <?php
                        }
                    }
                    if (isset($rights['help']) && $rights['help']) {
                        echo '<li><a href="' . xt_get_help_search_url() . '">帮助</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
        <?php
        echo $after_widget;
    }

}

/**
 * TaobaoGuide widget class
 *
 */
class XT_Widget_TaobaoGuide extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-taobaoguide',
            'description' => '带图分类文章模块'
        );
        $control_ops = array(
            'width' => 698,
            'height' => 350
        );
        parent :: __construct('taobaoguide', '带图分类文章模块', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom,invite',
            'layout' => '12,9'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $cids = isset($instance['cids']) ? $instance['cids'] : array(
            array(
                'title' => '',
                'cid' => -1
            ),
            array(
                'title' => '',
                'cid' => -1
            ),
            array(
                'title' => '',
                'cid' => -1
                ));
        echo $before_widget;
        echo '<div class="xt-custom-bd clearfix">';
        if (!empty($cids)) {
            foreach ($cids as $cid) {
                echo '<div class="xt-block">';
                echo '<h4><a class="text-gray" href="' . xt_get_daogou_search_url(array('cid' => $cid['cid'])) . '" target="_blank">' . $cid['title'] . '</a></h4>';
                $_params = array(
                    'post_type' => 'daogou',
                    'posts_per_page' => 9
                );
                if (!empty($cid['cid']) && $cid['cid'] > 0) {
                    $_params['tax_query'] = array(
                        array(
                            "taxonomy" => "daogou_category",
                            "field" => "id",
                            "terms" => $cid['cid']
                        )
                    );
                }
                query_posts($_params);
                $_post_count = 0;
                $a = '';
                $list = array(array('', ''), array('', '', ''), array('', '', ''), array('', '', ''));
                $_isA = false;
                $_tags = array();
                while (have_posts()) {
                    the_post();
                    $_permalink = get_permalink();
                    $_title = get_the_title();
                    $_excerpt = wp_trim_words($_title, 7, '');
                    if (!$_isA && has_post_thumbnail()) {
                        $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
                        $image = wp_get_attachment_image_src($post_thumbnail_id, 'post-thumbnail', false);
                        list($src, $width, $height) = $image;
                        $a = '<a class="xt-first" href="' . $_permalink . '" target="_blank"><img src="' . $src . '" alt="' . $_title . '"><span class="xt-text xt-bg-l">' . $_excerpt . '</span></a>';
                        $_isA = true;
                    } else {
                        switch ($_post_count) {
                            case 0:
                                $list[0][0] = '<a href="' . $_permalink . '" target="_blank" title="' . $_title . '">' . wp_trim_words($_title, 8, '') . '</a>';
                                break;
                            case 1:
                                $list[0][1] = '<a href="' . $_permalink . '" target="_blank" title="' . $_title . '">' . wp_trim_words($_title, 8, '') . '</a>';
                                break;
                            case 2:
                                $list[1][0] = _xt_widget_taobaoguide_tag($_tags);
                                $list[1][1] = '<a class="text-gray" href="' . $_permalink . '" target="_blank" title="' . $_title . '">' . $_excerpt . '</a>';
                                break;
                            case 3:
                                if (empty($list[1][0])) {
                                    $list[1][0] = _xt_widget_taobaoguide_tag($_tags);
                                }
                                $list[1][2] = '<a class="text-gray" href="' . $_permalink . '" target="_blank" title="' . $_title . '">' . $_excerpt . '</a>';
                                break;
                            case 4:
                                $list[2][0] = _xt_widget_taobaoguide_tag($_tags);
                                $list[2][1] = '<a class="text-gray" href="' . $_permalink . '" target="_blank" title="' . $_title . '">' . $_excerpt . '</a>';
                                break;
                            case 5:
                                if (empty($list[2][0])) {
                                    $list[2][0] = _xt_widget_taobaoguide_tag($_tags);
                                }
                                $list[2][2] = '<a class="text-gray" href="' . $_permalink . '" target="_blank" title="' . $_title . '">' . $_excerpt . '</a>';
                                break;
                            case 6:
                                $list[3][0] = _xt_widget_taobaoguide_tag($_tags);
                                $list[3][1] = '<a class="text-gray" href="' . $_permalink . '" target="_blank" title="' . $_title . '">' . $_excerpt . '</a>';
                                break;
                            case 7:
                                if (empty($list[3][0])) {
                                    $list[3][0] = _xt_widget_taobaoguide_tag($_tags);
                                }
                                $list[3][2] = '<a class="text-gray" href="' . $_permalink . '" target="_blank" title="' . $_title . '">' . $_excerpt . '</a>';
                                break;
                            case 8:
                                $a = '<a class="xt-first" href="' . $_permalink . '" target="_blank"><img alt="' . $_title . '"><span class="xt-text xt-bg-l">' . $_excerpt . '</span></a>';
                                break;
                        }
                        $_post_count++;
                    }
                }
                echo $a;
                echo '<ul class="unstyled">';
                foreach ($list as $li) {
                    echo '<li>' . implode('', $li) . '</li>';
                }
                if (!empty($_tags)) {
                    $rands = array_rand($_tags, 4);
                    echo '<li>';
                    $_count = 0;
                    foreach ($rands as $rand) {
                        $tag = $_tags[$rand];
                        $class = ' class="text-gray" ';
                        if ($_count == 1) {
                            $class = ' ';
                        }
                        echo '<span>[<a' . $class . 'href="' . get_tag_link($tag) . '" target="_blank" title="' . $tag->name . '">' . wp_trim_words($tag->name, 3, '') . '</a>]</span>';
                        $_count++;
                    }
                    echo '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
        }
        echo '</div>';
        echo $after_widget;
    }

}

function _xt_widget_taobaoguide_tag(&$_tags = array()) {
    $span = '';
    $tags = get_the_tags();
    if (!empty($tags)) {
        $__tags = array_slice($tags, 0, 1);
        $tag = $__tags[0];
        $span = '<span>[<a class="text-gray" href="' . get_tag_link($tag) . '" target="_blank" title="' . $tag->name . '">' . wp_trim_words($tag->name, 2, '') . '</a>]</span>';
        $_tags = array_merge($_tags, array_slice($tags, 1, count($tags) - 1));
    }
    return $span;
}

/**
 * Custom widget class
 *
 */
class XT_Widget_Custom extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-text',
            'description' => '自定义模块,多种模块模板,自由编辑'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('custom', '自定义模块', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => XT_WIDGET_EDIT_PAGE_COMMON,
            'layout' => '12,9,3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $text = apply_filters('widget_text', empty($instance['text']) ? '' : $instance['text'], $instance);
        if (isset($instance['widefat']) && !empty($instance['widefat'])) {
            echo str_replace('clearfix', 'clearfix xt-widefat', $before_widget);
        } else {
            echo $before_widget;
        }
        echo $text;
        echo $after_widget;
    }

}

/**
 * Searchbox widget class
 *
 */
class XT_Widget_Searchbox extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-searchbox',
            'description' => '搜索框'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('searchbox', '搜索框', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom,header,shares,albums,users,daogous,taobaos,paipais,invite,error404',
            'layout' => '12,9'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $search_items = $instance['search_items'];
        $type = isset($instance['type']) ? ($instance['type']) : 'top';
        echo $before_widget;
        _xt_widget_searchbox($type, $search_items);
        echo $after_widget;
    }

}

function _xt_widget_searchbox($type, $search_items) {
    $s = '';
    $searchs = xt_search();
    echo '<form class="xt-widget-searchbox-bd xt-' . $type . '">';
    $lis = '';
    if (!empty($search_items)) {
        global $xt;
        $isSearchPage = 1;
        $search_selected = array('share' => 0, 'album' => 0, 'taobao' => 0, 'shop' => 0, 'paipai' => 0, 'bijia' => 0, 'tuan' => 0);
        if ($xt->is_shares) {
            $search_selected['share'] = 1;
        } elseif ($xt->is_albums) {
            $search_selected['album'] = 1;
        } elseif ($xt->is_taobaos) {
            $search_selected['taobao'] = 1;
        } elseif ($xt->is_shops) {
            $search_selected['shop'] = 1;
        } elseif ($xt->is_paipais) {
            $search_selected['paipai'] = 1;
        } elseif ($xt->is_bijias) {
            $search_selected['bijia'] = 1;
        } elseif ($xt->is_tuans) {
            $search_selected['tuan'] = 1;
        } else {
            $isSearchPage = 0;
        }
        if ($isSearchPage) {
            $s = xt_search_keyword();
        }

        $_count = 0;
        $caret = $type == 'top' ? '<span class="caret xt-bd-l"></span>' : '';
        foreach ($search_items as $key) {
            if (isset($searchs[$key])) {
                $lis .= '<li class="' . ($search_selected[$key] ? 'active' : (!$isSearchPage && $_count == 0 ? 'active' : '')) . '" data-value="' . $key . '" data-placeholder="' . esc_attr($searchs[$key]['placeholder']) . '"><a href="javascript:;">' . $searchs[$key]['title'] . '</a>' . $caret . '</li>';
                $_count++;
            }
        }
    }
    if ($type == 'left') {
        ?>
        <div class="input-append">
            <div class="btn-group <?php echo count($search_items) == 1 ? 'hide' : ''; ?>">
                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <?php echo $lis; ?>
                </ul>
            </div>
            <input class="span4 xt-bd-l" type="text" x-webkit-speech="" autocomplete="off" value="<?php echo esc_attr($s); ?>"/>
            <button class="btn btn-primary xt-search" type="button" data-loading-text="搜索中...">搜索</button>
        </div>
        <?php
    } else {
        echo '<ul class="nav nav-pills ' . (count($search_items) == 1 ? 'hide' : '') . '">';
        echo $lis;
        echo '</ul>';
        ?>
        <div class="input-append">
            <input class="span4 xt-bd-l" type="text" x-webkit-speech="" autocomplete="off" value="<?php echo esc_attr($s); ?>"/>
            <button class="btn btn-primary xt-search" type="button" data-loading-text="搜索中...">搜索</button>
        </div>
        <?php
    }
    ?>

    <?php
    echo '</form>';
}

/**
 * Fanxian Side widget class
 *
 */
class XT_Widget_Fanxian_Tab extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-fanxian-tab xt-bd-l',
            'description' => '显示所有返现商城'
        );
        $control_ops = array(
            'width' => 700,
            'height' => 350
        );
        parent :: __construct('fanxiantab', '全部返现商城', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom',
            'layout' => '9,12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $cids = $instance['cids'];
        echo $before_widget;
        echo '<div class="hd"><h4 class="xt-bd-l" ' . (!empty($title) ? '' : ' style="display:none;"') . '><span>' . $title . '</span></h4></div><div class="bd">';
        if (!empty($cids)) {
            $cats = xt_yiqifa_website_category();
            $tabs = array();
            foreach ($cids as $cid) {
                if (isset($cats[$cid])) {
                    $tabs[] = $cats[$cid];
                }
            }
            ?>
            <div class="tabbable tabs-left">
                <ul class="nav nav-tabs nav-pills">
                    <?php
                    if (!empty($tabs)) {
                        $isFirst = true;
                        foreach ($tabs as $tab) {
                            echo '<li class="' . ($isFirst ? 'active' : '') . '"><a href="#X_Merchant-Category-' . $tab['id'] . '" data-toggle="tab">' . $tab['name'] . '(' . $tab['amount'] . ')</a></li>';
                            $isFirst = false;
                        }
                    }
                    ?>
                </ul>
                <div class="tab-content">
                    <?php
                    if (!empty($tabs)) {
                        $isFirst = true;
                        foreach ($tabs as $tab) {
                            $sites = $tab['sites'];
                            ?>
                            <div class="tab-pane <?php echo $isFirst ? 'active' : ""; ?>" id="X_Merchant-Category-<?php echo $tab['id']; ?>">
                                <ul class="thumbnails">
                                    <?php
                                    $isFirst = false;
                                    if (!empty($sites)) {
                                        foreach ($sites as $site) {
                                            $_url = $site['url'];
                                            ?>
                                            <li>
                                                <a rel="nofollow" target="_blank" href="<?php echo $_url; ?>" class="thumbnail" title="<?php echo $site['name']; ?>">
                                                    <img src="<?php echo $site['logo']; ?>" alt="<?php echo $site['name']; ?>" width="120px" height="60px">
                                                    <?php echo ($site['commission'] ? ('<h5>返' . $site['commission'] . '</h5>') : ''); ?>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>

                                </ul>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        echo '</div>';
        echo $after_widget;
    }

}

/**
 * Catalog Share widget class
 *
 */
class XT_Widget_Catalog_Share extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-catalog-share',
            'description' => '分享的分类,标签显示'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('catalogshare', '分享分类', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom,shares,invite',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $cids = apply_filters('xt_filter_ids', ($instance['cids']));
        $style = $instance['style'];
        global $xt, $xt_catalog, $wp_query;
        $s = '';
        echo $before_widget;
        if (!empty($cids)) {
            $cids = explode(',', $cids);
            $roots = xt_catalogs_share_sub(0);
            $catalogs = array();
            if (!empty($roots)) {
                foreach ($roots as $root) {
                    if (in_array($root->id, $cids)) {
                        $catalogs[] = $root;
                    }
                }
            }
            $fCid = $cids[0];
            $sCid = 0;
            if ($xt->is_shares && !empty($xt_catalog)) {
                $xt_share_param = $wp_query->query_vars['xt_param'];
                $s = isset($xt_share_param['s']) ? $xt_share_param['s'] : '';
                $parentCid = $xt_catalog->id;
                if ($xt_catalog->parent) {
                    $parentCid = $xt_catalog->parent;
                    $sCid = $xt_catalog->id;
                }
                if (in_array($parentCid, $cids)) {
                    $fCid = $parentCid;
                }
            }
            $childs = xt_catalogs_share_sub($fCid);
            switch ($style) {
                case 'mogujie' :
                    _xt_widget_catalog_share_mogujie($fCid, $sCid, $s, $catalogs, $childs);
                    break;
                case 'fandongxi' :
                    _xt_widget_catalog_share_fandongxi($fCid, $sCid, $s, $catalogs, $childs);
                    break;
            }
        }
        if ($xt->is_shares) {
            $xt_share_param = $wp_query->query_vars['xt_param'];
            $s = isset($xt_share_param['s']) ? $xt_share_param['s'] : '';
            $h3 = !empty($s) ? $s : (!empty($xt_catalog) ? $xt_catalog->title : '');
            $filterCid = '';
            $filterPrice = $xt_share_param['price'];
            $filterSortOrder = $xt_share_param['sortOrder'];
            if (!empty($xt_catalog)) {
                $filterCid = $xt_catalog->id;
            }
            $prices = xt_prices();
            ?>	
            <div class="row-fluid clearfix" style="margin-top:10px;">
                <h3 class="pull-left text-default" style="margin:0px;"><?php echo $h3 ?></h3>&nbsp;&nbsp;
                <div class="pull-left" style="padding:8px 0px 3px 20px;">
                    排序：
                    <div class="btn-group">
                        <a class="btn btn-small <?php echo $filterSortOrder == 'popular' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_shares_search_url(array('cid' => $filterCid, 's' => $s, 'sortOrder' => 'popular', 'price' => $filterPrice)); ?>" data-value="popular">潮流</a>
                        <a class="btn btn-small <?php echo $filterSortOrder == 'newest' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_shares_search_url(array('cid' => $filterCid, 's' => $s, 'sortOrder' => 'newest', 'price' => $filterPrice)); ?>" data-value="newest">最新</a>
                        <a class="btn btn-small <?php echo $filterSortOrder == 'hot' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_shares_search_url(array('cid' => $filterCid, 's' => $s, 'sortOrder' => 'hot', 'price' => $filterPrice)); ?>" data-value="hot">最热</a>
                    </div>
                    &nbsp;
                    &nbsp;
                    &nbsp;
                    价格：
                    <div class="btn-group">
                        <a class="btn btn-small <?php echo $filterPrice == '' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_shares_search_url(array('cid' => $filterCid, 's' => $s, 'sortOrder' => $filterSortOrder, 'price' => '')); ?>" data-value="">全部</a>
                        <a class="btn btn-small <?php echo $filterPrice == 'low' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_shares_search_url(array('cid' => $filterCid, 's' => $s, 'sortOrder' => $filterSortOrder, 'price' => 'low')); ?>" data-value="low"><?php echo $prices['low']['end'] ?>元</a>
                        <a class="btn btn-small <?php echo $filterPrice == 'medium' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_shares_search_url(array('cid' => $filterCid, 's' => $s, 'sortOrder' => $filterSortOrder, 'price' => 'medium')); ?>" data-value="medium"><?php echo $prices['medium']['end'] ?>元</a>
                        <a class="btn btn-small <?php echo $filterPrice == 'high' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_shares_search_url(array('cid' => $filterCid, 's' => $s, 'sortOrder' => $filterSortOrder, 'price' => 'high')); ?>" data-value="high"><?php echo $prices['high']['end'] ?>元</a>
                        <a class="btn btn-small <?php echo $filterPrice == 'higher' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_shares_search_url(array('cid' => $filterCid, 's' => $s, 'sortOrder' => $filterSortOrder, 'price' => 'higher')); ?>" data-value="higher">更高</a>
                    </div>
                </div>
            </div>
            <?php
        }
        echo $after_widget;
    }

}

function _xt_widget_catalog_share_mogujie($fCid, $sCid, $s, $catalogs, $childs) {
    echo '<div class="hd"><h4  class="xt-bd-l" style="' . (empty($title) ? 'display:none' : '') . '"><span>' . $title . '</span></h4></div><div class="bd"><div class="row-fluid">';
    ?>	
    <div class="span2">
        <ul class="dropdown-menu" style="position:static;margin:6px 12px 12px;display:block;">
            <?php
            if (!empty($catalogs)) {
                foreach ($catalogs as $cat) {
                    echo '<li ' . ($cat->id == $fCid ? 'class="active"' : '') . ' style="width:120px;"><a href="' . xt_get_shares_search_url(array(
                        'cid' => $cat->id
                    )) . '">' . $cat->title . '</a></li>';
                }
            }
            ?>
        </ul>
    </div>
    <div class="span10">
        <?php
        $css = array_chunk($childs, 3);
        $_count = 0;
        foreach ($css as $cs) {
            echo '<div class="row-fluid">';
            foreach ($cs as $child) {
                $img = '';
                if (!empty($child->pic)) {
                    $img = '<img src="' . $child->pic . '"/>';
                }
                echo '<div class="span4"><dl class="dl-horizontal">';
                echo '<dt><a href="' . xt_get_shares_search_url(array(
                    'cid' => $child->id
                )) . '"><label class="label label-default">' . $child->title . '</label><br><span>' . $img . '</span></a></dt>';
                echo '<dd><ul class="inline">';
                if (!empty($child->tags)) {
                    $tags = $child->tags;
                    foreach ($tags as $tag) {
                        echo '<li><a class="' . (rand(0, 2) == 2 ? 'text-default' : 'text-gray') . '" href="' . xt_get_shares_search_url(array(
                            'cid' => $child->id,
                            's' => $tag->title
                        )) . '">' . $tag->title . '</a></li>';
                    }
                }
                echo '</ul></dd>';
                echo '</dl></div>';
            }
            echo '</div>';
            $_count++;
            if ($_count == 2)
                break;
        }
        ?>
    </div>
    <?php
    echo '</div></div>';
}

function _xt_widget_catalog_share_fandongxi($fCid, $sCid, $s, $catalogs, $childs) {
    ?>	
    <div class="row-fluid">
        <div class="pull-left" style="width:100px;background-color: white;height:200px;_display:inline;">
            <ul class="nav nav-list">
                <?php
                if (!empty($catalogs)) {
                    $isFirst = true;
                    foreach ($catalogs as $cat) {
                        echo '<li ' . ($cat->id == $fCid ? 'class="active"' : '') . '><a ' . ($isFirst ? 'style="padding:8px 15px 4px 15px;"' : '') . ' href="' . xt_get_shares_search_url(array(
                            'cid' => $cat->id
                        )) . '">' . $cat->title . '</a></li>';
                        $isFirst = false;
                    }
                }
                ?>
            </ul>
        </div>
        <div class="pull-right" style="width:900px;_display:inline;">
            <?php
            $_count = 0;
            foreach ($childs as $child) {
                echo '<div class="pull-left">';
                $img = '<img  data-src="holder.js/160x80/text:无图无真相"/>';
                if (!empty($child->pic)) {
                    $img = '<img src="' . $child->pic . '"/>';
                }
                echo '<div>' . $img . '</div>';
                echo '<h5><a ' . ($sCid == $child->id ? 'class="label-default"' : '') . ' href="' . xt_get_shares_search_url(array(
                    'cid' => $child->id
                )) . '">' . $child->title . '</a></h5>';
                echo '<ul class="inline">';
                if (!empty($child->tags)) {
                    $tags = $child->tags;
                    $__count = 0;
                    foreach ($tags as $tag) {
                        echo '<li><a class="' . ($sCid == $child->id && $s == $tag->title ? 'label-default' : ((rand(0, 2) == 2 ? ' text-default ' : ' text-gray '))) . '" href="' . xt_get_shares_search_url(array(
                            'cid' => $child->id,
                            's' => $tag->title
                        )) . '">' . $tag->title . '</a></li>';
                        $__count++;
                        if ($__count == 9) {
                            break;
                        }
                    }
                }
                echo '</ul>';
                echo '</div>';
                $_count++;
                if ($_count == 5) {
                    break;
                }
            }
            ?>
        </div>
    </div>	
    <?php
}

/**
 * Album widget class
 *
 */
class XT_Widget_Grid_Album extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-grid-album',
            'description' => '专辑列表'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('gridalbum', '专辑列表', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom,album,user,daogous,invite',
            'layout' => '12,9,3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $dataType = ($instance['dataType']);
        $user_id = intval($instance['user_id']);
        $count = intval($instance['count']);
        $cid = isset($instance['cid']) ? intval($instance['cid']) : 0;
        $ids = apply_filters('xt_filter_ids', ($instance['ids']));
        if ($dataType == 'cid' && !empty($cid)) {
            query_albums(array(
                'page' => 1,
                'album_per_page' => $count,
                'cid' => $cid
            ));
        } elseif ($dataType == 'ids' && !empty($ids)) {
            query_albums(array(
                'no_found_rows' => 1,
                'album__in' => explode(',', $ids)
            ));
        } elseif ($dataType == 'user_id' && $user_id > 0) {
            query_albums(array(
                'page' => 1,
                'album_per_page' => $count,
                'user_id' => $user_id
            ));
        }
        echo $before_widget;
        echo '<div class="hd"><h4  class="xt-bd-l" style="' . (empty($title) ? 'display:none' : '') . '"><span>' . $title . '</span></h4></div><div class="bd">';
        echo '<div class="thumbnails thumbnails-span3 clearfix">';
        if (xt_have_albums()) {
            while (xt_have_albums()) {
                xt_the_album();
                get_the_album_template_small(0, '', true);
            }
        }
        echo '</div>';
        echo '<div class="clearfix"></div></div>';
        echo $after_widget;
    }

}

/**
 * Alert widget class
 *
 */
class XT_Widget_Alert extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-alert',
            'description' => '可手动关闭的通知条,适合公告,通知'
        );
        $control_ops = array(
            'width' => 698,
            'height' => 350
        );
        parent :: __construct('alert', '提醒', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => XT_WIDGET_EDIT_PAGE_COMMON,
            'layout' => '12,9,3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $text = apply_filters('widget_text', empty($instance['text']) ? '' : $instance['text'], $instance);
        $cookie = intval($instance['cookie']);
        $width = '950px';
        if (!empty($instance['widefat'])) {
            echo str_replace('clearfix', 'clearfix xt-widefat', $before_widget);
            $width = '980px';
        } else {
            echo $before_widget;
        }
        echo '<div class="' . ($cookie ? 'hide' : '') . ' alert ' . (isset($instance['color']) ? $instance['color'] : '') . '"><div style="width:' . $width . ';margin:0 auto;">';
        if ($cookie) {
            echo '<a href="javascript:;" class="close" data-cookie="true" data-cookie-key="' . $widget_id . '"><span>&times;不再显示</span></a>';
        } else {
            echo '<a href="javascript:;" class="close">&times;</a>';
        }
        echo $text;
        echo '</div></div>';
        echo $after_widget;
    }

}

/**
 * Daogou widget class
 *
 */
class XT_Widget_DaogouList extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-daogou',
            'description' => '显示导购文章列表'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('daogou', '导购文章', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom',
            'layout' => '12,9'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $dataType = $instance['dataType'];
        $cid = $instance['cid'];
        $ids = apply_filters('xt_filter_ids', ($instance['ids']));
        $page_size = intval($instance['count']);
        if ($dataType == 'cid') {
            if (intval($cid) == 0 || $cid == -1) {
                query_posts(array(
                    'post_type' => 'daogou',
                    'posts_per_page' => $page_size
                ));
            } else {
                $_params = array(
                    'post_type' => 'daogou',
                    'posts_per_page' => $page_size
                );
                if (!empty($cid) && $cid > 0) {
                    $_params['tax_query'] = array(
                        array(
                            "taxonomy" => "daogou_category",
                            "field" => "id",
                            "terms" => $cid
                        )
                    );
                }
                query_posts($_params);
            }
        } elseif ($dataType == 'ids') {
            query_posts(array(
                'post__in' => explode(',', $ids),
                'post_type' => 'daogou'
            ));
        }
        echo $before_widget;
        echo '<div class="hd"><h4 class="xt-bd-l" ' . (!empty($title) ? '' : ' style="display:none;"') . '><span>' . $title . '</span></h4></div><div class="bd">';
        _xt_widget_blog_daogou($layout);
        echo '</div>';
        echo $after_widget;
    }

}

/**
 * Topic widget class
 *
 */
class XT_Widget_Topic extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-topic',
            'description' => '显示多个系统分类的分享,适合制作专题'
        );
        $control_ops = array(
            'width' => 600,
            'height' => 350
        );
        parent :: __construct('topic', '专题', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $cids = $instance['cids'];
        $color = isset($instance['color']) && !empty($instance['color']) ? $instance['color'] : 'yellow';
        echo $before_widget;
        echo '<div id="X_Topic_Menu"><ul class="nav nav-tabs">';
        if (!empty($cids))
            : global $wpdb;
            $cats = $wpdb->get_results('SELECT * FROM ' . XT_TABLE_CATALOG . ' WHERE id in (' . implode(',', $cids) . ') AND type=\'share\'');
            $cats_array = array();
            $default_cat = 0;
            foreach ($cids as $cid) {
                $cats_array[$cid] = '';
            }
            if (!empty($cats)) {
                foreach ($cats as $cat) {
                    $cats_array[$cat->id] = esc_html($cat->title);
                }
            }
            $_count = 0;
            foreach ($cats_array as $cid => $cname)
                : if (!empty($cname))
                    : if ($_count == 0)
                        $default_cat = $cid;
                    ?>
                    <li <?php echo $_count == 0 ? 'class="active"' : ''; ?>>
                        <a href="javascript:;" data-id="<?php echo $cid ?>"><?php echo $cname; ?></a>
                    </li>

                    <?php
                    $_count++;
                endif;
            endforeach;
        endif;
        echo '</ul><i class="xt-right-bottom-left"></i> <i class="xt-right-bottom-right"></i></div>';
        if ($default_cat > 0) {
            query_shares(array(
                'page' => 1,
                'share_per_page' => 40,
                'cid' => $default_cat,
                'no_found_rows' => true
            ));
            get_the_share_container(array(
                'nopage' => true
            ));
        }
        echo $after_widget;
    }

}

/**
 * SideCat widget class
 *
 */
class XT_Widget_SideCat extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-sidecat',
            'description' => '侧边栏分类'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('sidecat', '侧边栏分类', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'taobaos,paipais',
            'layout' => '3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $cids = $instance['cids'];
        $cnames = $instance['cnames'];
        echo $before_widget;
        echo '<div class="hd"><h4 class="xt-bd-l" ' . (!empty($title) ? '' : 'style="display:none"') . '><span>' . $title . '</span></h4></div><div class="bd"><ul class="xt-bd-list unstyled clearfix">';
        if (!empty($cids) && !empty($cnames)) {
            $cids = explode(',', $cids);
            $cnames = explode(',', $cnames);
            if (count($cids) == count($cnames)) {
                global $xt_current_page;
                for ($i = 0; $i < count($cids); $i++) {
                    $_url = '';
                    if ($xt_current_page == 'taobaos') {
                        $_url = xt_get_taobao_search_url(array(
                            'cid' => $cids[$i]
                                ));
                    } elseif ($xt_current_page == 'paipais') {
                        $_url = xt_get_paipai_search_url(array(
                            'classId' => $cids[$i]
                                ));
                    }
                    echo '<li><a href="' . $_url . '">' . $cnames[$i] . '</a></li>';
                }
            }
        }
        echo '</ul></div>';
        echo $after_widget;
    }

}

/**
 * Header widget class
 *
 */
class XT_Widget_HeaderLove extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-headerlove',
            'description' => '含LOGO,用户中心菜单导航'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('headerlove', 'LOGO,用户菜单', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'header',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $logo = $instance['logo'];
        $isLogo = false;
        if (!empty($logo)) {
            $_pic = strrchr($logo, '.');
            if (in_array($_pic, array(
                        '.png',
                        '.gif',
                        '.jpg'
                    ))) {
                $isLogo = true;
            }
        }
        $_site = esc_html(get_bloginfo('name', 'display'));
        echo $before_widget;
        ?>
        <div id="X_Header" class="xt-headerlove-box row-fluid">
            <div class="span6">
                <h1 <?php echo $isLogo ? 'class="xt-logo"' : 'class="xt-nologo"' ?>>
                    <a href="<?php echo home_url('/'); ?>" target="_top">
                        <?php echo!empty($logo) ? '<img src="' . $logo . '" alt="' . $_site . '">' : $_site; ?>
                    </a>
                </h1>
            </div>
            <div class="span6">
                <div class="xt-headerlove-box-right">
                    <?php
                    global $wpdb;
                    $user = wp_get_current_user();
                    if ($user->exists()) {
                        if (empty($user->display_name))
                            $user->display_name = $user->user_login;
                        $user_name = $wpdb->escape($user->display_name);
                        ?>
                        <div class="xt-headerlove-userbox clearfix">
                            <a href="<?php xt_the_user_url($user->ID); ?>">
                                <span title="<?php echo $user_name; ?>"><?php echo wp_trim_words($user_name, 8); ?></span>
                                <span><img src="<?php xt_the_user_pic('', $user->ID) ?>" alt="<?php echo $user_name ?>"></span>
                            </a>
                        </div>
                    <?php } ?>
                    <ul class="xt-headerlove-ul">
                        <?php
                        $user = wp_get_current_user();
                        if (!$user->exists()) {
                            ?>
                            <li class="xt-headerlove-ul-li xt-headerlove-ul-login"><a href="javascript:;" class="X_User-Login" id="X_User-Login">
                                    <span></span> 登录/注册
                                </a></li>
                            <li class="xt-headerlove-ul-li xt-headerlove-ul-publish"><a href="javascript:;" class="X_User-Share-Publish" id="X_User-Share-Publish">
                                    <span></span> 我要分享
                                </a></li>
                            <?php if (xt_is_fanxian()): ?>
                                <li class="xt-headerlove-ul-li xt-headerlove-ul-fanxian"><a href="javascript:;" class="X_User-Need-Login"><span></span>
                                        我的返现
                                    </a></li>
                            <?php endif; ?>
                            <li class="xt-headerlove-ul-li xt-headerlove-ul-msg hide"><a href="javascript:;" class="X_User-Need-Login"> <span></span>
                                    消息
                                </a></li>
                        <?php }else { ?>
                            <li class="xt-headerlove-ul-li xt-headerlove-ul-publish"><a href="javascript:;" class="X_User-Share-Publish" id="X_User-Share-Publish">
                                    <span></span> 我要发表
                                </a></li>
                            <?php if (xt_is_fanxian()): ?>
                                <li class="xt-headerlove-ul-li xt-headerlove-ul-fanxian"><a href="<?php echo xt_site_url('account') ?>"><span></span>
                                        我的返现
                                    </a></li>
                            <?php endif; ?>
                            <li class="xt-headerlove-ul-li xt-headerlove-ul-my">
                                <a href="<?php xt_the_user_url($user->ID); ?>">
                                    <span></span> 个人中心
                                    <i class="xt-headerlove-line-topleft"></i>
                                    <i class="xt-headerlove-line-topright"></i>
                                </a>
                                <div class="xt-headerlove-ul-my-sub">
                                    <ul>
                                        <li class="xt-first"><a href="<?php xt_the_user_url($user->ID); ?>#share" class="X_Menu-Share-A">我的分享</a></li>
                                        <li><a href="<?php xt_the_user_url($user->ID); ?>#like" class="X_Menu-Fav-A">我喜欢的</a></li>
                                        <li class="xt-last"><a href="<?php xt_the_user_url($user->ID); ?>#album" class="X_Menu-Album-A">我的专辑</a></li>
                                        <li class="xt-last"><a href="<?php echo xt_site_url('account#profile') ?>" class="X_Menu-Account-A">账号设置</a></li>
                                        <li class="xt-last hide"><a href="<?php echo xt_site_url('account#tuiguang') ?>"> class="X_Menu-Invite-A"邀请好友</a></li>
                                        <?php if (current_user_can('edit_pages')): ?>
                                            <li class="xt-last"><a href="<?php echo admin_url() ?>">管理中心</a></li>
                                        <?php endif; ?>
                                        <li><a href="<?php echo wp_logout_url() . '&redirect_to=' . $_SERVER['REQUEST_URI'] ?>">退出</a></li>

                                    </ul>
                                    <i class="xt-headerlove-line-bottom"></i> <i class="xt-headerlove-line-topleft"></i>
                                    <i class="xt-headerlove-line-bottomleft"></i> <i
                                        class="xt-headerlove-line-bottomright"></i>
                                </div>
                            </li>
                            <li class="xt-headerlove-ul-li xt-headerlove-ul-msg hide"><a href="javascript:alert('即将开放');"> <span></span>
                                    消息
                                </a></li>
                        <?php } ?>

                    </ul>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(function($) {
                $('#X_Header .xt-headerlove-ul-li').hover(function(){$('#X_Header .xt-headerlove-ul-li').removeClass('xt-hover');$(this).addClass('xt-hover')},function(){$(this).removeClass('xt-hover')});
            });
        </script>
        <?php
        echo $after_widget;
    }

}

/**
 * Nav widget class
 *
 */
class XT_Widget_Nav extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-nav',
            'description' => '导航条'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('nav', '导航条', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'header',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

        if (isset($instance['fixed']) && $instance['fixed'] == 'navbar-fixed-default') {
            $before_widget = str_replace('clearfix"', 'xt-widget-affix-top clearfix" style="top:0px;z-index:1030" ', $before_widget);
        }
        if (isset($instance['widefat']) && empty($instance['widefat'])) {
            
        } else {
            $before_widget = str_replace('clearfix', 'clearfix xt-widefat', $before_widget);
        }
        echo $before_widget;
        if (isset($instance['fixed'])) {
            if ($instance['fixed'] == 'navbar-fixed-top') {
                echo '<style type="text/css">html { margin-top: 40px !important; }* html body { margin-top: 40px !important; }</style>';
            } elseif ($instance['fixed'] == 'navbar-fixed-bottom') {
                echo '<style type="text/css">html { margin-bottom: 40px !important; }* html body { margin-bottom: 40px !important; }</style>';
            }
        }
        echo '<div class="shop-header navbar ' . (isset($instance['fixed']) ? $instance['fixed'] : '') . '"><div class="navbar-inner"><div class="container">';
        wp_nav_menu(array(
            'menu' => $instance['menu'],
            'container' => 'nav-collapse',
            'menu_class' => 'nav',
            'fallback_cb' => 'xt_default_menu',
            'walker' => new XT_Walker_Nav_Menu
        ));
        if (isset($instance['search']) && $instance['search']) {
            $search_items = $instance['search_items'];
            $searchs = xt_search();
            $_search_items = array();
            if (!empty($search_items)) {
                $s = xt_search_keyword();
                foreach ($search_items as $key) {
                    if (isset($searchs[$key])) {
                        $_search_items[] = '在<em>' . $searchs[$key]['title'] . '</em>里找"' . strtoupper($key) . '"';
                    }
                }
                $_search_items_length = count($_search_items);
                $_search_items = json_encode($_search_items);


                echo '<form class="X_Search-Form-Dropdown navbar-search pull-right" method="get" target="_blank"><div class="input-append"><input type="text" placeholder="搜索" data-items="' . $_search_items_length . '" data-source=\'' . $_search_items . '\' value="' . $s . '"/><button type="submit" class="btn">搜索</button></div></form>';
            }
        }
        echo '</div></div></div>';
        echo $after_widget;
    }

}

/**
 * TaobaoGrid widget class
 *
 */
class XT_Widget_Grid extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-grid',
            'description' => '支持:淘宝,拍拍,分享,折扣,天猫,全网,团购'
        );
        $control_ops = array(
            'width' => 800,
            'height' => 350
        );
        parent :: __construct('grid', '宝贝推广', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom',
            'layout' => '12,9,3'
        );
    }

    function widget($args, $instance) {
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        switch ($instance['dataType']) {
            case 'taobao' :
                _xt_widget_grid_taobao($args, $instance);
                break;
            case 'paipai' :
                _xt_widget_grid_paipai($args, $instance);
                break;
            case 'share' :
                _xt_widget_grid_share($args, $instance);
            case 'coupon' :
                _xt_widget_grid_coupon($args, $instance);
                break;
            case 'temai' :
                _xt_widget_grid_temai($args, $instance);
                break;
            case 'bijia':
                _xt_widget_grid_bijia($args, $instance);
            default :
                break;
        }
    }

}

/**
 * Blog widget class
 *
 */
class XT_Widget_Blog extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-blog',
            'description' => '文章模块,三列显示'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('blog', '文章', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom',
            'layout' => '12,9,3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $cids = isset($instance['cids']) ? $instance['cids'] : array(
            array(
                'title' => '',
                'cid' => -1
            ),
            array(
                'title' => '',
                'cid' => -1
            ),
            array(
                'title' => '',
                'cid' => -1
            )
                );
        $show = intval($instance['show']);
        $count = intval($instance['count']);

        echo $before_widget;
        $_count = 0;
        echo '<div class="row-fluid">';
        $span = $layout == 'span12' ? 'span4' : ($layout == 'span9' ? 'span6' : 'span12');
        $_length = $layout == 'span12' ? 3 : ($layout == 'span9' ? 2 : 1);
        foreach ($cids as $cid) {
            echo '<div class="' . $span . '">';
            if (empty($cid['title'])) {
                $cid['title'] = '文章列表';
            }
            echo '<div class="hd"><h4 class="xt-bd-l" ' . (!empty($cid['title']) ? '' : 'style="display:none"') . '><a href="' . ($cid['cid'] > 0 ? get_category_link($cid) : '') . '" target="_blank">' . $cid['title'] . '</a></h4></div><div class="bd">';
            if ($show == 0) {
                echo '<ul class="xt-bd-list unstyled clearfix">';
            } else {
                echo '<ul class="media-list clearfix">';
            }
            $_params = array(
                'post_type' => 'daogou',
                'posts_per_page' => $count
            );
            if (!empty($cid['cid']) && $cid['cid'] > 0) {
                $_params['tax_query'] = array(
                    array(
                        "taxonomy" => "daogou_category",
                        "field" => "id",
                        "terms" => $cid['cid']
                    )
                );
            }
            query_posts($_params);
            $_post_count = 0;
            while (have_posts()) {
                the_post();
                _xt_widget_blog_li($show, $_post_count);
                $_post_count++;
            }
            echo '</ul></div></div>';
            $_count++;
            if ($_count == $_length) {
                break;
            }
        }
        echo '</div>';
        echo $after_widget;
    }

}

/**
 * Carousel widget class
 *
 */
class XT_Widget_Carousel extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-carousel',
            'description' => '图片轮换'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('carousel', '图片轮换', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => XT_WIDGET_EDIT_PAGE_COMMON,
            'layout' => '12,9,3'
        );
    }

    function widget($args, $instance) {
        extract($args);

        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        echo $before_widget;
        $_id = $widget_id . '-carousel';
        ?>
        <div id="<?php echo $_id ?>" class="carousel slide">
            <div class="carousel-inner">
                <?php
                $scrolls = isset($instance['scrolls']) ? $instance['scrolls'] : array();
                $_count = 0;
                if (!empty($scrolls)) {
                    foreach ($scrolls as $scroll) {
                        $src = $scroll['image'];
                        $href = $scroll['link'];
                        $title = $scroll['title'];
                        $desc = $scroll['desc'];
                        if (!empty($src)) {
                            echo '<div class="' . ($_count == 0 ? 'active' : '') . ' item"><a href="' . (!empty($href) ? $href : 'javascript:;') . '" target="_blank"><img style="width:100%" alt="' . esc_html($title) . '" src="' . $src . '"></a>' . ((!empty($title) || !empty($desc)) ? ('<div class="carousel-caption"><h4>' . $title . '</h4><p>' . $desc . '</p></div>') : '') . '</div>';
                            $_count++;
                        }
                    }
                }
                ?>
            </div>
            <ol class="carousel-indicators">
                <?php
                if ($_count > 0) {
                    for ($i = 0; $i < $_count; $i++) {
                        ?>
                        <li data-target="#<?php echo $_id ?>" data-slide-to="<?php echo $i ?>" <?php echo $i == 0 ? 'class="active"' : '' ?>></li>
                        <?php
                    }
                }
                ?>
            </ol>
            <a class="carousel-control left" href="#<?php echo $widget_id ?>-carousel" data-slide="prev">&lsaquo;</a>
            <a class="carousel-control right" href="#<?php echo $widget_id ?>-carousel" data-slide="next">&rsaquo;</a>
        </div>
        <?php
        echo $after_widget;
    }

}

/**
 * Text widget class
 *
 */
class XT_Widget_Text extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-text',
            'description' => '任意文本或 HTML'
        );
        $control_ops = array(
            'width' => 698,
            'height' => 350
        );
        parent :: __construct('text', '文本', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => XT_WIDGET_EDIT_PAGE_COMMON,
            'layout' => '12,9,3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $text = apply_filters('widget_text', empty($instance['text']) ? '' : $instance['text'], $instance);
        if (isset($instance['widefat']) && !empty($instance['widefat'])) {
            echo str_replace('clearfix', 'clearfix xt-widefat', $before_widget);
        } else {
            echo $before_widget;
        }
        if (isset($instance['widget']) && $instance['widget']) {
            echo '<div class="hd"><h4  class="xt-bd-l" style="' . (empty($title) ? 'display:none' : '') . '"><span>' . $title . '</span></h4></div><div class="bd">';
        }
        ?>
        <?php echo!empty($instance['filter']) ? wpautop($text) : $text; ?>
        <?php
        if (isset($instance['widget']) && $instance['widget']) {
            echo '</div>';
        }
        echo $after_widget;
    }

}

class XT_Widget_ShareAndTags extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-shareandtags',
            'description' => '通过配置分类,排序显示指定分享与标签'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('shareandtags', '分享与标签', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'custom',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '分享' : $instance['title'], $instance, $this->id_base);

        $style = isset($instance['style']) ? ($instance['style']) : 'MZ';

        $sortby = empty($instance['sortby']) ? 'newest' : $instance['sortby'];
        $cid = empty($instance['cid']) ? '' : $instance['cid'];
        $row = isset($instance['row']) ? intval($instance['row']) : 1;
        $tagPicCount = ($style == 'MZ' ? 10 : $row * 6);
        $tagCount = ($style == 'MZ' ? 20 : 28);
        $query_tags = query_tags(array(
            'cid' => $cid,
            'tag_per_page' => $tagPicCount + $tagCount
                ));
        $tags = $query_tags['tags'];
        $shares = xt_shareandtags($cid, array_slice($tags, 0, $tagPicCount), $sortby);
        $count = count($shares);
        if ($style == 'MZ') {
            echo str_replace('clearfix', 'xt-widget-shareandtags-mz clearfix', $before_widget);
            ?>
            <div class="hd"><h4 <?php echo!empty($title) ? '' : 'style="display:none;"' ?>><span><?php echo $title ?></span></h4></div>
            <div class="bd">
                <div class="clearfix">
                    <div class="xt-big xt-img-box">
                        <?php
                        if ($count > 0) {
                            $_share = $shares[0]['share'];
                            $_tag = $shares[0]['tag'];
                            if (!empty($_share)) {
                                ?>
                                <a href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank" title="<?php echo $_share->title ?>"><?php xt_write_pic(base64_encode(get_the_share_picurl(200, $_share)), $_share->title) ?><span><?php echo $_tag ?></span></a>
                                <?php
                            } else {
                                ?>
                                <a href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank"><span><?php echo $_tag ?></span></a>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="xt-small">
                        <?php
                        if ($count > 1) {
                            $length = $count > 7 ? 7 : $count;
                            for ($i = 1; $i < $length; $i++) {
                                $_share = $shares[$i]['share'];
                                $_tag = $shares[$i]['tag'];
                                if (!empty($_share)) {
                                    ?>
                                    <div class="xt-img-box"><a href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank" title="<?php echo $_share->title ?>"><?php xt_write_pic(base64_encode(get_the_share_picurl(160, $_share)), $_share->title) ?><span><?php echo $_tag ?></span></a></div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="xt-img-box"><a href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank"><span><?php echo $_tag ?></span></a></div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="xt-big xt-img-box">
                        <?php
                        if ($count > 7) {
                            $_share = $shares[7]['share'];
                            $_tag = $shares[7]['tag'];
                            if (!empty($_share)) {
                                ?>
                                <a href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank" title="<?php echo $_share->title ?>"><?php xt_write_pic(base64_encode(get_the_share_picurl(200, $_share)), $_share->title) ?><span><?php echo $_tag ?></span></a>
                                <?php
                            } else {
                                ?>
                                <a href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank"><span><?php echo $_tag ?></span></a>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="xt-small xt-last">
                        <?php
                        if ($count > 8) {
                            for ($i = 8; $i < $count; $i++) {
                                $_share = $shares[$i]['share'];
                                $_tag = $shares[$i]['tag'];
                                if (!empty($_share)) {
                                    ?>
                                    <div class="xt-img-box"><a href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank" title="<?php echo $_share->title ?>"><?php xt_write_pic(base64_encode(get_the_share_picurl(160, $_share)), $_share->title) ?><span><?php echo $_tag ?></span></a></div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="xt-img-box"><a href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank"><span><?php echo $_tag ?></span></a></div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
                if (count($tags) > $tagPicCount) {
                    $tags = array_slice($tags, $tagPicCount, $tagCount);
                    $tagsList = array_chunk($tags, 5);
                    ?>
                    <div class="xt-tags clearfix">
                        <?php
                        foreach ($tagsList as $_tags) {
                            if (!empty($_tags)) {
                                $_count = 0;
                                foreach ($_tags as $_tag) {
                                    echo '<a ' . ($_count == 0 ? 'class="xt-first"' : 'class="text-gray"') . ' href="' . xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag->title)) . '" target="_blank">' . $_tag->title . '</a>';
                                    $_count++;
                                }
                            }
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>

            </div>
            <?php
        } else {
            echo $before_widget;
            ?>
            <div class="hd xt-bd-radius-top"><h4 class="xt-bd-l" <?php echo!empty($title) ? '' : 'style="display:none;"' ?>><span><?php echo $title ?></span></h4></div>
            <div class="bd xt-bd-radius-bottom">
                <?php if (!empty($shares)):$_count = 0; ?>
                    <ul class="thumbnails">
                        <?php
                        if (!empty($shares)) {

                            foreach ($shares as $__share) {
                                $_share = $__share['share'];
                                $_tag = $__share['tag'];
                                if (!empty($_share)) {
                                    ?>
                                    <li><a class="thumbnail" href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank" title="<?php echo $_share->title ?>"><?php xt_write_pic(base64_encode(get_the_share_picurl(200, $_share)), $_share->title) ?><span><?php echo $_tag ?></span></a></li>
                                    <?php
                                } else {
                                    ?>
                                    <li><a class="thumbnail" href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag)) ?>" target="_blank"><span><?php echo $_tag ?></span></a></li>
                                    <?php
                                }
                            }
                        }
                        ?>	
                    </ul>
                <?php endif; ?>
                <?php
                if (count($tags) > $tagPicCount) {
                    $tags = array_slice($tags, $tagPicCount - 1, $tagCount);
                    ?>
                    <div class="clearfix" style="position:relative;">
                        <?php
                        $chunks = array_chunk($tags, 7);
                        foreach ($chunks as $chunk):$_tag0 = (array) $chunk[0];
                            ?>
                            <dl class="dl-horizontal pull-left">
                                <dt>
                                <a target="_blank" class="text-success" href="<?php echo xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag0['title'])) ?>"><?php echo $_tag0['title']; ?></a>
                                </dt>
                                <dd>
                                    <ul class="inline">
                                        <?php
                                        for ($i = 1; $i < count($chunk); $i++) {
                                            $_random = rand(1, 3);
                                            $_tag = (array) $chunk[$i];
                                            echo '<li><a ' . ($_random == 1 ? ' class="text-default" ' : ' class="text-gray"') . 'href="' . xt_get_shares_search_url(array('cid' => $cid, 's' => $_tag['title'])) . '" target="_blank">' . $_tag['title'] . '</a></li>';
                                        }
                                        ?>
                                    </ul>
                                </dd>
                            </dl>
                        <?php endforeach; ?>
                        <?php if ($cid > 0): ?><a class="xt-widget-tag-more label-default" href="<?php echo xt_get_shares_search_url(array('cid' => $cid)); ?>" target="_blank">全部<span>...</span></a><?php endif; ?>
                    </div>
                <?php } ?>		
            </div>	
            <?php
        }
        echo $after_widget;
    }

}

function _xt_widget_grid_paipai($args, $instance) {
    extract($args);
    $title = !isset($instance['title']) || empty($instance['title']) ? '' : $instance['title'];
    $size = isset($instance['size']) ? $instance['size'] : '';
    $pageIndex = isset($instance['pageIndex']) ? intval($instance['pageIndex']) : 1;
    $pageSize = isset($instance['row']) ? intval($instance['row']) * xt_size($layout, $size) : (isset($instance['pageSize']) ? intval($instance['pageSize']) : 10);
    $paipai = $instance['paipai'];

    $classId = intval($paipai['classId']);
    $keyWord = isset($paipai['keyWord']) ? $paipai['keyWord'] : '';
    $begPrice = isset($paipai['begPrice']) ? $paipai['begPrice'] : '';
    $endPrice = isset($paipai['endPrice']) ? $paipai['endPrice'] : '';
    $orderStyle = isset($paipai['orderStyle']) ? $paipai['orderStyle'] : '';
    $crMin = isset($paipai['crMin']) ? $paipai['crMin'] : '';
    $crMax = isset($paipai['crMax']) ? $paipai['crMax'] : '';
    $payType = isset($paipai['payType']) ? $paipai['payType'] : '';
    $property = isset($paipai['property']) ? $paipai['property'] : '';
    $level = isset($paipai['level']) ? $paipai['level'] : '';
    $address = isset($paipai['address']) ? $paipai['address'] : '';
    $display = isset($paipai['display']) ? $paipai['display'] : array(
        'title',
        'price',
        'volume',
        'seller'
            );
    $params = array(
        'classId' => $classId,
        'keyWord' => $keyWord,
        'begPrice' => $begPrice,
        'endPrice' => $endPrice,
        'orderStyle' => $orderStyle,
        'crMin' => $crMin,
        'crMax' => $crMax,
        'payType' => $payType,
        'property' => $property,
        'level' => $level,
        'address' => $address,
        'pageIndex' => $pageIndex,
        'pageSize' => $pageSize
    );
    $resp = xt_paipaike_items_search($params);
    if ($layout == 'span3' && $size == 'small') {
        echo str_replace('clearfix', ' xt-widget-recommend-side clearfix', $before_widget);
    } else {
        echo $before_widget;
    }
    if (is_wp_error($resp)) {
        echo '<h2>' . $resp->get_error_code() . ':' . $resp->get_error_message() . '</h2>';
    } else {
        $__params = array(
            'total' => $resp['total'],
            'size' => $size,
            'count' => $pageSize,
            'title' => $title,
            'items' => $resp['items'],
            'type' => 'paipai',
            'display' => $display,
            'params' => $params
        );
        if ($layout == 'span3' && $size == 'small') {
            xt_widget_template_grid_sidesmall($__params);
        } else {
            xt_widget_template_grid($__params);
        }
    }
    echo $after_widget;
}

function _xt_widget_grid_taobao($args, $instance) {
    extract($args);
    $title = !isset($instance['title']) || empty($instance['title']) ? '' : $instance['title'];
    $size = isset($instance['size']) ? $instance['size'] : '';
    $page_no = isset($instance['page_no']) ? intval($instance['page_no']) : 1;
    $page_size = isset($instance['row']) ? intval($instance['row']) * xt_size($layout, $size) : (isset($instance['page_size']) ? intval($instance['page_size']) : 10);
    $taobao = $instance['taobao'];
    $cid = intval($taobao['cid']);
    $keyword = isset($taobao['keyword']) ? $taobao['keyword'] : '';
    $start_price = isset($taobao['start_price']) ? $taobao['start_price'] : '';
    $end_price = isset($taobao['end_price']) ? $taobao['end_price'] : '';
    $start_credit = isset($taobao['start_credit']) ? $taobao['start_credit'] : '';
    $end_credit = isset($taobao['end_credit']) ? $taobao['end_credit'] : '';
    $sort = isset($taobao['sort']) ? $taobao['sort'] : '';
    $start_commissionRate = isset($taobao['start_commissionRate']) ? $taobao['start_commissionRate'] : '';
    $end_commissionRate = isset($taobao['end_commissionRate']) ? $taobao['end_commissionRate'] : '';
    $cash_ondelivery = isset($taobao['cash_ondelivery']) ? $taobao['cash_ondelivery'] : '';
    $mall_item = isset($taobao['mall_item']) ? $taobao['mall_item'] : '';
    $display = isset($taobao['display']) ? $taobao['display'] : array(
        'title',
        'price',
        'volume',
        'seller'
            );

    if ($cid > 0 || !empty($keyword)) {
        $_items = xt_taobaoke_items_search(array(
            'cid' => $cid,
            'keyword' => $keyword,
            'start_price' => $start_price,
            'end_price' => $end_price,
            'start_credit' => $start_credit,
            'end_credit' => $end_credit,
            'sort' => $sort,
            'start_commissionRate' => $start_commissionRate,
            'end_commissionRate' => $end_commissionRate,
            'cash_ondelivery' => $cash_ondelivery,
            'mall_item' => $mall_item,
            'page_no' => $page_no,
            'page_size' => $page_size
                ));
        if ($layout == 'span3' && $size == 'small') {
            echo str_replace('clearfix', ' xt-widget-recommend-side clearfix', $before_widget);
        } else {
            echo $before_widget;
        }
        if (is_wp_error($_items)) {
            xt_api_error($_items);
        } else {
            $__params = array(
                'total' => $_items['total'],
                'size' => $size,
                'count' => $page_size,
                'title' => $title,
                'items' => $_items['items'],
                'type' => 'taobao',
                'display' => $display,
                'urlType' => isset($instance['urlType']) ? $instance['urlType'] : 'jump'
            );
            if ($layout == 'span3' && $size == 'small') {
                xt_widget_template_grid_sidesmall($__params);
            } else {
                xt_widget_template_grid($__params);
            }
        }
        echo $after_widget;
    }
}

function _xt_widget_grid_share($args, $instance) {
    extract($args);
    $title = !isset($instance['title']) || empty($instance['title']) ? '' : $instance['title'];
    $size = isset($instance['size']) ? $instance['size'] : '';
    $page_no = isset($instance['page_no']) ? intval($instance['page_no']) : 1;
    $page_size = isset($instance['row']) ? intval($instance['row']) * xt_size($layout, $size) : (isset($instance['page_size']) ? intval($instance['page_size']) : 10);
    $share = $instance['share'];

    $sortby = empty($share['sortby']) ? 'newest' : $share['sortby'];
    $cid = empty($share['cid']) ? '' : $share['cid'];
    $display = isset($share['display']) ? $share['display'] : array(
        'title',
        'price',
        'volume',
        'seller'
            );
    if ($cid > 0) {
        $query_shares = query_shares(array(
            'cid' => absint($cid),
            'page' => $page_no,
            'share_per_page' => $page_size,
            'sortOrder' => $sortby
                ));
        if ($layout == 'span3' && $size == 'small') {
            echo str_replace('clearfix', ' xt-widget-recommend-side clearfix', $before_widget);
        } else {
            echo $before_widget;
        }
        $__params = array(
            'size' => $size,
            'count' => $page_size,
            'title' => $title,
            'items' => $query_shares,
            'type' => 'share',
            'display' => $display
        );
        if ($layout == 'span3' && $size == 'small') {
            xt_widget_template_grid_sidesmall($__params);
        } else {
            xt_widget_template_grid($__params);
        }

        echo $after_widget;
    }
}

function _xt_widget_grid_coupon($args, $instance) {
    extract($args);
    $title = !isset($instance['title']) || empty($instance['title']) ? '' : $instance['title'];
    $size = isset($instance['size']) ? $instance['size'] : '';
    $page_no = isset($instance['page_no']) ? intval($instance['page_no']) : 1;
    $page_size = isset($instance['row']) ? intval($instance['row']) * xt_size($layout, $size) : (isset($instance['page_size']) ? intval($instance['page_size']) : 10);
    $coupon = $instance['coupon'];

    $cid = intval($coupon['cid']);
    $cid = $cid > 0 ? $cid : '';
    $keyword = isset($coupon['keyword']) ? $coupon['keyword'] : '';
    $start_coupon_rate = isset($coupon['start_coupon_rate']) ? $coupon['start_coupon_rate'] : '';
    $end_coupon_rate = isset($coupon['end_coupon_rate']) ? $coupon['end_coupon_rate'] : '';
    $start_credit = isset($coupon['start_credit']) ? $coupon['start_credit'] : '';
    $end_credit = isset($coupon['end_credit']) ? $coupon['end_credit'] : '';
    $sort = isset($coupon['sort']) ? $coupon['sort'] : '';
    $start_commissionRate = isset($coupon['start_commissionRate']) ? $coupon['start_commissionRate'] : '';
    $end_commissionRate = isset($coupon['end_commissionRate']) ? $coupon['end_commissionRate'] : '';
    $shop_type = isset($coupon['shop_type']) ? $coupon['shop_type'] : 'all';

    $display = isset($coupon['display']) ? $coupon['display'] : array(
        'title',
        'price',
        'volume',
        'seller'
            );

    if ($cid > 0 || !empty($keyword)) {
        $_items = xt_taobaoke_items_coupon_search(array(
            'cid' => $cid,
            'keyword' => $keyword,
            'start_coupon_rate' => $start_coupon_rate,
            'end_coupon_rate' => $end_coupon_rate,
            'start_credit' => $start_credit,
            'end_credit' => $end_credit,
            'sort' => $sort,
            'start_commissionRate' => $start_commissionRate,
            'end_commissionRate' => $end_commissionRate,
            'shop_type' => $shop_type,
            'page_no' => $page_no,
            'page_size' => $page_size
                ));
        if ($layout == 'span3' && $size == 'small') {
            echo str_replace('clearfix', ' xt-widget-recommend-side clearfix', $before_widget);
        } else {
            echo $before_widget;
        }

        if (is_wp_error($_items)) {
            xt_api_error($_items);
        } else {
            $__params = array(
                'total' => $_items->total_results,
                'size' => $size,
                'count' => $page_size,
                'title' => $title,
                'items' => $_items->taobaoke_items->taobaoke_item,
                'type' => 'coupon',
                'display' => $display,
                'urlType' => isset($instance['urlType']) ? $instance['urlType'] : 'jump'
            );
            if ($layout == 'span3' && $size == 'small') {//side
                xt_widget_template_grid_sidesmall($__params);
            } else {
                xt_widget_template_grid($__params);
            }
        }
        echo $after_widget;
    }
}

function _xt_widget_grid_temai($args, $instance) {
    extract($args);
    $title = !isset($instance['title']) || empty($instance['title']) ? '' : $instance['title'];
    $size = isset($instance['size']) ? $instance['size'] : '';
    $page_no = isset($instance['page_no']) ? intval($instance['page_no']) : 1;
    $page_size = isset($instance['row']) ? intval($instance['row']) * xt_size($layout, $size) : (isset($instance['page_size']) ? intval($instance['page_size']) : 10);
    $temai = $instance['temai'];

    $cat = intval($temai['cat']);
    $start = ($page_no - 1) * 48; //contant
    $sort = isset($temai['sort']) ? $temai['sort'] : 's';

    $display = isset($temai['display']) ? $temai['display'] : array(
        'title',
        'price',
        'volume',
        'seller'
            );

    if ($cat > 0) {
        $_items = xt_taobaoke_items_temai($cat, $start, $sort);
        if ($layout == 'span3' && $size == 'small') {
            echo str_replace('clearfix', ' xt-widget-recommend-side clearfix', $before_widget);
        } else {
            echo $before_widget;
        }
        if (is_wp_error($_items)) {
            xt_api_error($_items);
        } else {
            $__params = array(
                'total' => $_items->total_results,
                'size' => $size,
                'count' => $page_size,
                'title' => $title,
                'items' => $_items->item_list->tmall_search_tm_item,
                'type' => 'temai',
                'display' => $display,
                'params' => array('cat' => $cat, 'page_no' => $page_no, 'sort' => $sort),
                'urlType' => isset($instance['urlType']) ? $instance['urlType'] : 'jump'
            );
            if ($layout == 'span3' && $size == 'small') {
                xt_widget_template_grid_sidesmall($__params);
            } else {
                xt_widget_template_grid($__params);
            }
        }
        echo $after_widget;
    }
}

function _xt_widget_grid_bijia($args, $instance) {
    extract($args);
    $title = !isset($instance['title']) || empty($instance['title']) ? '' : $instance['title'];
    $size = isset($instance['size']) ? $instance['size'] : '';
    $page_no = isset($instance['page']) ? intval($instance['page']) : 1;
    $page_size = isset($instance['row']) ? intval($instance['row']) * xt_size($layout, $size) : (isset($instance['rowCount']) ? intval($instance['rowCount']) : 10);
    $bijia = $instance['bijia'];
    $bj_keyword = $bijia['keyword'];
    $bj_category = intval($bijia['catid']);
    $bj_webid = isset($bijia['webid']) ? $bijia['webid'] : '';
    $bj_minprice = absint($bijia['minprice']);
    $bj_maxprice = absint($bijia['maxprice']);
    $bj_orderby = absint($bijia['orderby']);

    $bj_display = isset($bijia['display']) ? $bijia['display'] : array(
        'title',
        'price',
        'volume',
        'seller'
            );
    $params = array('page_no' => $page_no, 'page_size' => $page_size, 'keyword' => $bj_keyword, 'catid' => $bj_category, 'webid' => $bj_webid, 'minprice' => $bj_minprice, 'maxprice' => $bj_maxprice, 'orderby' => $bj_orderby);
//    if (empty($bj_keyword)) {
//        $_items = xt_yiqifa_api_product_list_get($params);
//    } else {
    $_items = xt_yiqifa_api_product_search($params);
//    }
    if ($layout == 'span3' && $size == 'small') {
        echo str_replace('clearfix', ' xt-widget-recommend-side clearfix', $before_widget);
    } else {
        echo $before_widget;
    }
    if (is_wp_error($_items)) {
        xt_api_error($_items);
    } else {
        $__params = array(
            'total' => $_items['total'],
            'size' => $size,
            'count' => $page_size,
            'title' => $title,
            'items' => $_items['pdt_list']['pdt'],
            'type' => 'bijia',
            'display' => $bj_display,
            'params' => $params,
            'urlType' => isset($instance['urlType']) ? $instance['urlType'] : 'direct'
        );
        if ($layout == 'span3' && $size == 'small') {
            xt_widget_template_grid_sidesmall($__params);
        } else {
            xt_widget_template_grid($__params);
        }
    }
    echo $after_widget;
}

function _xt_widget_grid_tuan($args, $instance) {
    extract($args);
    $title = !isset($instance['title']) || empty($instance['title']) ? '' : $instance['title'];
    $size = isset($instance['size']) ? $instance['size'] : '';
    $page_no = isset($instance['page']) ? intval($instance['page']) : 1;
    $page_size = isset($instance['row']) ? intval($instance['row']) * xt_size($layout, $size) : (isset($instance['pagesize']) ? intval($instance['pagesize']) : 10);
    $tuan = $instance['tuan'];
    $tuan_keyword = $tuan['keyword'];
    $tuan_category_id = intval($tuan['catid']);
    $tuan_city_id = intval($tuan['city_id']);
    $tuan_price = ($tuan['price']);
    $tuan_order = ($tuan['orderby']);

    $tuan_display = isset($tuan['display']) ? $tuan['display'] : array(
        'title',
        'price',
        'volume',
        'seller'
            );
    $params = array(
        'page_no' => $page_no,
        'page_size' => $page_size,
        'keyword' => $tuan_keyword,
        'catid' => $tuan_category_id,
        'city_id' => $tuan_city_id,
        'price' => $tuan_price,
        'orderby' => $tuan_order
    );
    $_items = xt_yiqifa_api_tuan_search($params);
    if ($layout == 'span3' && $size == 'small') {
        echo str_replace('clearfix', ' xt-widget-recommend-side clearfix', $before_widget);
    } else {
        echo $before_widget;
    }
    if (is_wp_error($_items)) {
        xt_api_error($_items);
    } else {
        $__params = array(
            'total' => $_items['total'],
            'size' => $size,
            'count' => $page_size,
            'title' => $title,
            'items' => $_items['tuan_list']['tuan'],
            'type' => 'tuan',
            'display' => $tuan_display,
            'params' => $params,
            'urlType' => isset($instance['urlType']) ? $instance['urlType'] : 'direct'
        );
        if ($layout == 'span3' && $size == 'small') {
            xt_widget_template_grid_sidesmall($__params);
        } else {
            xt_widget_template_grid($__params);
        }
    }
    echo $after_widget;
}

function _xt_widget_blog_li($show, $count) {
    global $post;
    $_thumb = '';
    if (has_post_thumbnail()) {
        $_thumb = get_the_post_thumbnail($post->ID, array(
            100,
            100
                ));
    }
    if (empty($_thumb)) {
        $_thumb = '<img class="media-object" data-src="holder.js/100x100/text:无图无真相">';
    }
    switch ($show) {
        case 0 ://标题
            echo '<li><a href="' . get_permalink() . '" target="_blank">' . get_the_title() . '</a></li>';
            break;
        case 1 ://标题+摘要
            echo '<li class="media"><div class="media-body"><h5 class="media-heading"><a href="' . get_permalink() . '" target="_blank">' . get_the_title() . '</a></h5><div class="media">' . get_the_excerpt() . '</div></div></li>';
            break;
        case 2 ://标题+摘要+缩略图
            echo '<li class="media"><a class="pull-left" href="' . get_permalink() . '" target="_blank">' . $_thumb . '</a><div class="media-body"><h5 class="media-heading"><a href="' . get_permalink() . '" target="_blank">' . get_the_title() . '</a></h5><div class="media">' . get_the_excerpt() . '</div></div></li>';
            break;
    }
}

function _xt_widget_blog_daogou($layout) {
    echo '<ul class="media-list">';
    if (have_posts())
        : global $post;
        while (have_posts())
            : the_post();
            ?>

            <li class="media">
                <?php
                if (has_post_thumbnail()) {
                    echo '<a class="pull-left" href="' . get_permalink() . '" target="_blank">' . get_the_post_thumbnail(null, 'xt-daogou-post-thumbnail') . '</a>';
                } else {
                    echo '<a class="pull-left" href="' . get_permalink() . '" target="_blank"><img class="media-object" data-src="holder.js/180x180/text:无图无真相"></a>';
                }
                ?>
                <div class="media-body">
                    <h4 class="media-heading"><a href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a></h4>
                    <div class="media">
                        <?php
                        echo wp_trim_words(strip_tags(get_the_content()), ($layout == 'span12' ? 280 : 150));
                        ?>
                        <a href="<?php the_permalink(); ?>" target="_blank" rel="nofollow">阅读全文</a>
                    </div>
                </div>
                <div class="pull-right">
                    <ul class="thumbnails">
                        <?php
                        $items = get_post_meta($post->ID, 'xt_items', true);
                        if (!empty($items))
                            : foreach ($items as $item)
                                : if (!empty($item['pic']))
                                    :
                                    ?>
                                    <li><a class="thumbnail" href="<?php echo xt_jump_url(array('id' => get_the_share_key($item['key']), 'type' => $item['type'], 'share' => $item['guid'])) ?>" target="_blank"> <img src="<?php echo xt_pic_url($item['pic'], 160, $item['type']); ?>"></a></li>
                                    <?php
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
            </li>
            <?php
        endwhile;
    endif;
    echo '</ul>';
}

function _xt_widget_blog_help($layout) {
    echo '<ul class="media-list">';
    if (have_posts())
        : global $post;
        while (have_posts())
            : the_post();
            ?>

            <li class="media">
                <div class="media-body">
                    <h4 class="media-heading"><a href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a></h4>
                    <div class="media">
                        <?php
                        echo wp_trim_words(strip_tags(get_the_content()), ($layout == 'span12' ? 280 : 150));
                        ?>
                        <a href="<?php the_permalink(); ?>" target="_blank" rel="nofollow">阅读全文</a>
                    </div>
                </div>
            </li>
            <?php
        endwhile;
    endif;
    echo '</ul>';
}

function xt_default_menu($args = array()) {
    ?>
    <ul class="nav">
        <li class="menu-item"><a href="<?php echo home_url('/') ?>">首页</a></li>
        <li class="menu-item"><a href="<?php echo xt_get_shares_search_url() ?>">逛街啦</a></li>
        <li class="menu-item"><a href="<?php echo xt_get_albums_search_url() ?>">专辑</a></li>
        <li class="menu-item"><a href="<?php echo xt_get_taobao_search_url() ?>">淘宝</a></li>
        <li class="menu-item"><a href="<?php echo xt_get_tuan_search_url() ?>">团购</a></li>
        <li class="menu-item"><a href="<?php echo xt_get_bijia_search_url() ?>">比价</a></li>
        <li class="menu-item"><a href="<?php echo xt_get_paipai_search_url() ?>">拍拍</a></li>
        <li class="menu-item"><a href="<?php echo xt_get_temai_search_url() ?>">特卖</a></li>
        <li class="menu-item"><a href="<?php echo xt_get_coupon_search_url() ?>">折扣</a></li>
        <li class="menu-item"><a href="<?php echo xt_site_url('brands') ?>">品牌</a></li>
        <li class="menu-item"><a href="<?php echo xt_site_url('stars') ?>">明星</a></li>
    </ul>
    <?php
}

/**
 * Create HTML list of nav menu items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker
 */
class XT_Walker_Nav_Menu extends Walker {

    /**
     * @see Walker::$tree_type
     * @since 3.0.0
     * @var string
     */
    var $tree_type = array(
        'post_type',
        'taxonomy',
        'custom'
    );

    /**
     * @see Walker::$db_fields
     * @since 3.0.0
     * @todo Decouple this.
     * @var array
     */
    var $db_fields = array(
        'parent' => 'menu_item_parent',
        'id' => 'db_id'
    );

    /**
     * @see Walker::start_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of page. Used for padding.
     */
    function start_lvl(& $output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
    }

    /**
     * @see Walker::end_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of page. Used for padding.
     */
    function end_lvl(& $output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param int $current_page Menu item ID.
     * @param object $args
     */
    function start_el(& $output, $item, $depth = 0, $args = array(), $id = 0) {
        global $wp_query;
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        if (is_front_page()) {
            if (strstr($class_names, 'current_page_item') === false && strstr($class_names, 'menu-item-home') === false) {
                $class_names = str_replace('current-menu-item', 'current-menu-item active', $class_names);
            }
        } else {
            if (strstr($class_names, 'menu-item-home') === false) {
                $class_names = str_replace('current-menu-item', 'current-menu-item active', $class_names);
            }
        }


        $output .= $indent . '<li' . $id . $value . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .=!empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .=!empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .=!empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * @see Walker::end_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Page data object. Not used.
     * @param int $depth Depth of page. Not Used.
     */
    function end_el(& $output, $item, $depth = 0, $args = array()) {
        $output .= "</li>\n";
    }

}