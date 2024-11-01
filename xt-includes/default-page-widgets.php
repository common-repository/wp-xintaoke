<?php

/**
 * Recommend Daogou Blog widget class
 *
 */
class XT_Widget_Recommend_Daogou extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-blog',
            'description' => '同类导购文章推荐'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('pagerecommendblog', '同类导购文章推荐', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'daogou',
            'layout' => '9,3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        global $xt;
        if ($xt->is_daogou) {
            $cats = get_the_terms(null, 'daogou_category');
            if (!$cats || is_wp_error($cats))
                $cats = array();
            $cats = array_values($cats);
            foreach (array_keys($cats) as $key) {
                _make_cat_compat($cats[$key]);
            }
            $cids = array();
            if (!empty($cats)) {
                foreach ($cats as $cat) {
                    $cids[] = $cat->cat_ID;
                }
            }
            $cids = implode(',', $cids);
            $show = intval($instance['show']);
            $count = intval($instance['count']);
            $sortOrder = $instance['sortOrder'];
            $isDaogouList = ($layout == 'span9' && $show == 2);
            if ($isDaogouList) {
                echo str_replace('xt-widget-blog', 'xt-widget-daogou', $before_widget);
            } else {
                echo $before_widget;
            }
            echo '<div class="hd"><h4 class="xt-bd-l" style="' . (empty($title) ? 'display:none' : '') . '"><span>' . $title . '</span></h4></div><div class="bd">';

            $_params = array(
                'post_type' => 'daogou',
                'posts_per_page' => $count,
                'orderby' => $sortOrder
            );
            if (!empty($cids)) {
                $_params['tax_query'] = array(
                    array(
                        "taxonomy" => "daogou_category",
                        "field" => "id",
                        "terms" => $cids
                    )
                );
            }
            query_posts($_params);
            if ($isDaogouList) {
                _xt_widget_blog_daogou($layout);
            } else {
                if ($show == 0) {
                    echo '<ul class="xt-bd-list unstyled clearfix">';
                } else {
                    echo '<ul class="media-list clearfix">';
                }
                $_post_count = 0;
                while (have_posts()) {
                    the_post();
                    _xt_widget_blog_li($show, $_post_count);
                    $_post_count++;
                }
                echo '</ul>';
            }
            echo '</div>';
            echo $after_widget;
        }
    }

}

/**
 * Help Categories widget class
 *
 */
class XT_Widget_Help_Category extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-help-category',
            'description' => '帮助分类'
        );
        $control_ops = array(
            'width' => 800,
            'height' => 350
        );
        parent :: __construct('pagehelpcategory', '帮助分类', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'helps',
            'layout' => '3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        if (empty($title)) {
            $title = '帮助中心';
        }
        global $xt, $xt_help_itemcat;
        if (isset($xt->is_helps) && $xt->is_helps) {
            $dataType = $instance['dataType'];
            $cids = $instance['cids'];
            $terms = array();
            if ($dataType == 'all') {
                $terms = get_terms('help_category', array('get' => 'all'));
            } else {
                if (!empty($cids))
                    $terms = get_terms('help_category', array('include' => $cids));
            }
            if (!empty($terms)) {
                echo $before_widget;
                echo '<div class="hd"><h4 class="xt-bd-l" ' . (!empty($title) ? '' : 'style="display:none;"') . '><a href="' . xt_get_help_search_url() . '" class="text-gray">' . $title . '</a></h4></div>';
                echo '<div class="bd clearfix"><ul class="xt-bd-list unstyled">';
                foreach ($terms as $term) {
                    $active = '';
                    if (!empty($xt_help_itemcat)) {
                        if ($xt_help_itemcat->term_id == $term->term_id) {
                            $active = ' class="xt-bg-l active" ';
                        }
                    }
                    echo '<li' . $active . '><a href="' . xt_get_help_search_url(array('cid' => $term->term_id)) . '">' . $term->name . '</a></li>';
                }
                echo '</ul></div>';
                echo $after_widget;
            }
        }
    }

}

/**
 * Album UserAlbum widget class
 *
 */
class XT_Widget_Album_UserAlbum extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-grid-album xt-widget-grid-pagealbum xt-widget-grid-pageuseralbum',
            'description' => '用户专辑'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('pagegriduseralbum', '用户专辑', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'share',
            'layout' => '3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        if (empty($title)) {
            $title = '精彩推荐';
        }
        $count = intval($instance['count']);
        $smallCount = intval(isset($instance['smallCount']) ? $instance['smallCount'] : 3);
        global $xt, $xt_user;
        if (isset($xt->is_share) && $xt->is_share && !empty($xt_user)) {
            query_albums(array(
                'page' => 1,
                'album_per_page' => $count,
                'user_id' => $xt_user->ID
            ));
            if (xt_have_albums()) {
                echo $before_widget;
                echo '<div class="hd"><h4 class="xt-bd-l" ' . (!empty($title) ? '' : 'style="display:none;"') . '><span>' . $title . '</span></h4></div>';
                echo '<div class="thumbnails thumbnails-span3 clearfix">';
                while (xt_have_albums()) {
                    xt_the_album();
                    get_the_album_template_small(0, '', false, $smallCount ? $smallCount : 3);
                }
                echo '<div class="clearfix"></div></div>';
                echo $after_widget;
            }
        }
    }

}

/**
 * Album widget class
 *
 */
class XT_Widget_Album_Album extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-grid-album xt-widget-grid-pagealbum',
            'description' => '所属专辑'
        );
        $control_ops = array(
            'width' => 400,
            'height' => 350
        );
        parent :: __construct('pagegridalbum', '所属专辑', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'share',
            'layout' => '3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        if (empty($title)) {
            $title = '所属专辑';
        }
        global $xt, $xt_user, $xt_album;
        if (isset($xt->is_share) && $xt->is_share && !empty($xt_user)) {
            $xt_album = get_share_album();
            if (!empty($xt_album)) {
                xt_setup_albumdata($xt_album);
                echo $before_widget;
                echo '<div class="hd"><h4 class="xt-bd-l" ' . (!empty($title) ? '' : 'style="display:none;"') . '><span>' . $title . '</span></h4></div>';
                echo '<div class="thumbnails thumbnails-span3 clearfix">';
                get_the_album_template_small();
                echo '<div class="clearfix"></div></div>';
                echo $after_widget;
            }
        }
    }

}

/**
 * Recommend Taobaos widget class
 *
 */
class XT_Widget_Recommend_Taobaos extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-grid xt-widget-recommend',
            'description' => '淘宝搜索相关折扣推荐'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('pagerecommendtaobaos', '淘宝相关折扣推荐', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'taobaos',
            'layout' => '3'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        global $wp;
        $params = $wp->query_vars['xt_param'];
        $page_no = intval($params['page_no']);
        $cid = $params['cid'];
        $keyword = $params['keyword'];

        $shop_type = $instance['shop_type'];
        $sort = $instance['sort'];
        $row = intval($instance['row']);
        $size = isset($instance['size']) ? $instance['size'] : 'big';
        _xt_widget_grid_coupon($args, array(
            'title' => $title,
            'size' => $size,
            'page_no' => $page_no,
            'row' => $row,
            'coupon' => array(
                'cid' => $cid,
                'keyword' => $keyword,
                'shop_type' => $shop_type,
                'sort' => $sort
            )
        ));
    }

}