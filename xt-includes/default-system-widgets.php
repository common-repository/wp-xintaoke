<?php

/**
 * Malls widget class
 *
 */
class XT_Widget_Malls extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-malls',
            'description' => '全部商城'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('sysmalls', '全部商城', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'malls',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $type = isset($instance['type']) ? $instance['type'] : 'direct';
        echo $before_widget;
        $tabs = xt_yiqifa_website_category();
        if (!empty($tabs)) {
            ?>
            <div class="tabbable tabs-left">
                <ul class="nav nav-tabs nav-pills xt-bd-l">
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
                                            if ($type == 'jump') {
                                                $_url = xt_jump_url(array('type' => 'mall', 'id' => $site['id'], 'title' => $site['name']));
                                            }
                                            if (empty($site['commission'])) {
                                                $site['commission'] = '50%';
                                            }
                                            ?>
                                            <li>
                                                <a rel="nofollow" target="_blank" href="<?php echo $_url; ?>" class="thumbnail" title="<?php echo $site['name']; ?>">
                                                    <img src="<?php echo $site['logo']; ?>" alt="<?php echo $site['name']; ?>" width="120px" height="60px">
                                                    <?php echo ($site['commission'] ? ('<span class="xt-desc">最高返<em>' . $site['commission'] . '</em></span>') : ''); ?>
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
        echo $after_widget;
    }

}

/**
 * Taoquan widget class
 *
 */
class XT_Widget_Taoquan extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-taoquan',
            'description' => '淘宝优惠券'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('systaoquan', '淘宝优惠券', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'taoquan',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        echo $before_widget;
        ?>
        <div class="clearfix" style="overflow:hidden;">
            <iframe style="margin-top: -160px;margin-bottom: -110px;overflow: hidden" frameborder="0" marginheight="0" marginwidth="0" border="0" id="X_Taobao-Quan" scrolling="no" height="1652px;" width="100%" src="http://taoquan.taobao.com/coupon/coupon_list.htm" ></iframe>
        </div>

        <?php
        echo $after_widget;
    }

}

/**
 * Activities widget class
 *
 */
class XT_Widget_Activities extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-activities',
            'description' => '特卖活动'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('sysactivities', '特卖活动', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'activities',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        echo $before_widget;
        $websites = xt_yiqifa_hotactivity_website();
        $activities = xt_yiqifa_hotactivity();
        ?>
        <ul class="nav nav-pills">
            <?php
            if (!empty($websites)) {
                foreach ($websites as $website) {
                    ?>
                    <li><a href="javascript:;" data-value="<?php echo $website['web_id'] ?>"><?php echo $website['web_name'] ?></a></li>
                    <?php
                }
            }
            ?>

        </ul>
        <?php
        if (!empty($activities)) {
            foreach ($activities as $key => $acts) {
                if (!empty($acts)) {
                    echo '<ul class="thumbnails hide" data-value="' . $key . '" style="text-align:center;">';
                    foreach ($acts as $act) {
                        ?>
                        <li class="span6">
                            <a href="<?php echo $act['hot_o_url'] ?>" target="_blank" class="thumbnail text-gray">
                                <img src="<?php echo $act['pic_url'] ?>" alt="<?php echo $act['hot_name'] ?>">
                                <h4>
                                    <?php
                                    if (!empty($act['discount'])) {
                                        echo '<span class="text-default">' . str_replace('折', '', $act['discount']) . '折</span>&nbsp;&nbsp;&nbsp;';
                                    }
                                    echo $act['hot_name'];
                                    ?>
                                </h4>
                                <p>
                                    <?php
                                    if (!empty($act['begin_date']) && !empty($act['end_date'])) {
                                        echo $act['begin_date'] . '&nbsp;&nbsp;-&nbsp;&nbsp;' . $act['end_date'];
                                    } elseif (!empty($act['begin_date']) && empty($act['end_date'])) {
                                        echo $act['begin_date'] . '开始';
                                    } elseif (empty($act['begin_date']) && !empty($act['end_date'])) {
                                        echo $act['end_date'] . '结束';
                                    }
                                    ?>
                                </p>
                            </a>
                        </li>
                        <?php
                    }
                    echo '</ul>';
                }
            }
        }
        echo $after_widget;
        ?>
        <script>
            jQuery(function($){
                $('#<?php echo $widget_id ?>').find('.nav a').click(function(){
                    var id = $(this).attr('data-value');
                    $(this).parent().addClass('active').siblings().removeClass('active');
                    $('#<?php echo $widget_id ?> .thumbnails').each(function(){
                        if($(this).attr('data-value')==id){
                            $(this).removeClass('hide');
                        }else{
                            $(this).addClass('hide');
                        }
                    });
                });
                $('#<?php echo $widget_id ?>').find('.nav a:first').click();
            })
        </script>
        <?php
    }

}

/**
 * Stars widget class
 *
 */
class XT_Widget_Stars extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-stars',
            'description' => '明星店'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('sysstars', '明星店', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'stars',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        echo $before_widget;
        $brands = include ('data-tmall-star.php');
        ?>
        <ul class="unstyled clearfix xt-first xt-bd-l">
            <li class="xt-selected xt-bg-l xt-first">
                <a href="" data-value="" title="全部品牌">全部</a>
            </li>
            <li><a href="#X_Brand-A" data-value="A" title="以A开头所有品牌">A</a></li>
            <li><a href="#X_Brand-B" data-value="B" title="以B开头所有品牌">B</a></li>
            <li><a href="#X_Brand-C" data-value="C" title="以C开头所有品牌">C</a></li>
            <li><a href="#X_Brand-D" data-value="D" title="以D开头所有品牌">D</a></li>
            <li><a href="#X_Brand-E" data-value="E" title="以E开头所有品牌">E</a></li>
            <li><a href="#X_Brand-F" data-value="F" title="以F开头所有品牌">F</a></li>
            <li><a href="#X_Brand-G" data-value="G" title="以G开头所有品牌">G</a></li>
            <li><a href="#X_Brand-H" data-value="H" title="以H开头所有品牌">H</a></li>
            <li><a href="#X_Brand-I" data-value="I" title="以I开头所有品牌">I</a></li>
            <li><a href="#X_Brand-J" data-value="J" title="以J开头所有品牌">J</a></li>
            <li><a href="#X_Brand-K" data-value="K" title="以K开头所有品牌">K</a></li>
            <li><a href="#X_Brand-L" data-value="L" title="以L开头所有品牌">L</a></li>
            <li><a href="#X_Brand-M" data-value="M" title="以M开头所有品牌">M</a></li>
            <li><a href="#X_Brand-N" data-value="N" title="以N开头所有品牌">N</a></li>
            <li><a href="#X_Brand-O" data-value="O" title="以O开头所有品牌">O</a></li>
            <li><a href="#X_Brand-P" data-value="P" title="以P开头所有品牌">P</a></li>
            <li><a href="#X_Brand-Q" data-value="Q" title="以Q开头所有品牌">Q</a></li>
            <li><a href="#X_Brand-R" data-value="R" title="以R开头所有品牌">R</a></li>
            <li><a href="#X_Brand-S" data-value="S" title="以S开头所有品牌">S</a></li>
            <li><a href="#X_Brand-T" data-value="T" title="以T开头所有品牌">T</a></li>
            <li><a href="#X_Brand-U" data-value="U" title="以U开头所有品牌">U</a></li>
            <li><a href="#X_Brand-V" data-value="V" title="以V开头所有品牌">V</a></li>
            <li><a href="#X_Brand-W" data-value="W" title="以W开头所有品牌">W</a></li>
            <li><a href="#X_Brand-X" data-value="X" title="以X开头所有品牌">X</a></li>
            <li><a href="#X_Brand-Y" data-value="Y" title="以Y开头所有品牌">Y</a></li>
            <li><a href="#X_Brand-Z" data-value="Z" title="以Z开头所有品牌">Z</a></li>
        </ul>
        <div class="clearfix">
            <?php
            foreach ($brands as $h => $bs) {
                echo '<div id="X_Brand-' . $h . '" class="xt-brands clearfix" data-value="' . $h . '">';
                echo '<h2>' . $h . '</h2>';
                echo '<div><ul class="unstyled clearfix">';
                foreach ($bs as $brand) {
                    echo '<li><a class="text-gray" href="' . $brand['url'] . '" target="_blank"><img src="' . $brand['pic'] . '"><span><em class="xt-bold">' . $brand['star'] . '</em><em>' . $brand['name'] . '</em></span></a></li>';
                }
                echo '</ul></div>';
                echo '</div>';
            }
            ?>

        </div>
        <script>
            jQuery(function($){
                $('.xt-widget-stars ul.xt-first a').click(function(){
                    var head = $(this).attr('data-value');
                    var li = $(this).parent();
                    li.addClass('xt-selected xt-bg-l').siblings().removeClass('xt-selected xt-bg-l');
                    if(head==''){
                        $('.xt-widget-stars .xt-brands').each(function(){
                            $(this).fadeIn();
                        });
                    }else{
                        $('.xt-widget-stars .xt-brands').hide();
                        $('.xt-widget-stars .xt-brands[data-value="'+head+'"]').fadeIn();
                    }
                    return false;
                });
            });
        </script>
        <?php
        echo $after_widget;
    }

}

/**
 * Brands widget class
 *
 */
class XT_Widget_Brands extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-brands',
            'description' => '天猫品牌街'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('sysbrands', '天猫品牌街', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'brands',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        echo $before_widget;
        $brands = include ('data-tmall-brand.php');
        ?>
        <ul class="unstyled clearfix xt-first xt-bd-l">
            <li class="xt-selected xt-bg-l xt-first">
                <a href="" data-value="" title="全部品牌">全部</a>
            </li>
            <li><a href="#X_Brand-A" data-value="A" title="以A开头所有品牌">A</a></li>
            <li><a href="#X_Brand-B" data-value="B" title="以B开头所有品牌">B</a></li>
            <li><a href="#X_Brand-C" data-value="C" title="以C开头所有品牌">C</a></li>
            <li><a href="#X_Brand-D" data-value="D" title="以D开头所有品牌">D</a></li>
            <li><a href="#X_Brand-E" data-value="E" title="以E开头所有品牌">E</a></li>
            <li><a href="#X_Brand-F" data-value="F" title="以F开头所有品牌">F</a></li>
            <li><a href="#X_Brand-G" data-value="G" title="以G开头所有品牌">G</a></li>
            <li><a href="#X_Brand-H" data-value="H" title="以H开头所有品牌">H</a></li>
            <li><a href="#X_Brand-I" data-value="I" title="以I开头所有品牌">I</a></li>
            <li><a href="#X_Brand-J" data-value="J" title="以J开头所有品牌">J</a></li>
            <li><a href="#X_Brand-K" data-value="K" title="以K开头所有品牌">K</a></li>
            <li><a href="#X_Brand-L" data-value="L" title="以L开头所有品牌">L</a></li>
            <li><a href="#X_Brand-M" data-value="M" title="以M开头所有品牌">M</a></li>
            <li><a href="#X_Brand-N" data-value="N" title="以N开头所有品牌">N</a></li>
            <li><a href="#X_Brand-O" data-value="O" title="以O开头所有品牌">O</a></li>
            <li><a href="#X_Brand-P" data-value="P" title="以P开头所有品牌">P</a></li>
            <li><a href="#X_Brand-Q" data-value="Q" title="以Q开头所有品牌">Q</a></li>
            <li><a href="#X_Brand-R" data-value="R" title="以R开头所有品牌">R</a></li>
            <li><a href="#X_Brand-S" data-value="S" title="以S开头所有品牌">S</a></li>
            <li><a href="#X_Brand-T" data-value="T" title="以T开头所有品牌">T</a></li>
            <li><a href="#X_Brand-U" data-value="U" title="以U开头所有品牌">U</a></li>
            <li><a href="#X_Brand-V" data-value="V" title="以V开头所有品牌">V</a></li>
            <li><a href="#X_Brand-W" data-value="W" title="以W开头所有品牌">W</a></li>
            <li><a href="#X_Brand-X" data-value="X" title="以X开头所有品牌">X</a></li>
            <li><a href="#X_Brand-Y" data-value="Y" title="以Y开头所有品牌">Y</a></li>
            <li><a href="#X_Brand-Z" data-value="Z" title="以Z开头所有品牌">Z</a></li>
        </ul>
        <div class="clearfix">
            <?php
            foreach ($brands as $h => $bs) {
                echo '<div id="X_Brand-' . $h . '" class="xt-brands clearfix" data-value="' . $h . '">';
                echo '<h2>' . $h . '</h2>';
                echo '<div><ul class="unstyled clearfix">';
                $_count = 0;
                foreach ($bs as $brand) {
                    $class = '';
                    if ($_count > 10) {
                        $class = ' class="hide" ';
                    }
                    echo '<li' . $class . '><a class="text-gray" href="' . $brand['url'] . '" target="_blank">' . $brand['name'] . '</a></li>';
                    if ($_count == 10) {
                        echo '<li><a class="label-default" href="javascript:;" data-value="' . $h . '">更多<i class="icon-chevron-right icon-white"></i></a></li>';
                    }
                    $_count++;
                }
                echo '</ul></div>';
                echo '</div>';
            }
            ?>

        </div>
        <script>
            jQuery(function($){
                $('.xt-widget-brands ul.xt-first a').click(function(){
                    var head = $(this).attr('data-value');
                    var li = $(this).parent();
                    li.addClass('xt-selected xt-bg-l').siblings().removeClass('xt-selected xt-bg-l');
                    if(head==''){
                        $('.xt-widget-brands .xt-brands').each(function(){
                            $(this).find('.label-default').parent().removeClass('hide');
                            $(this).find('li:gt(11)').addClass('hide');
                            $(this).fadeIn();
                        });
                    }else{
                        $('.xt-widget-brands .xt-brands').hide();
                        var selected = $('.xt-widget-brands .xt-brands[data-value="'+head+'"]');
                        selected.find('li').removeClass('hide');
                        selected.find('.label-default').parent().addClass('hide');
                        selected.fadeIn();    
                    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                    return false;
                });
                $('.xt-widget-brands .label-default').click(function(){
                    var head = $(this).attr('data-value');
                    $('.xt-widget-brands ul.xt-first a[data-value="'+head+'"]').click();
                });
            });
        </script>
        <?php
        echo $after_widget;
    }

}

/**
 * Daogous widget class
 *
 */
class XT_Widget_Daogous extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-daogou',
            'description' => '导购文章列表'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('sysdaogous', '导购文章列表', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'daogous',
            'layout' => '9'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $count = $instance['count'];
        global $wp;
        $params = $wp->query_vars['xt_param'];
        $page_no = $params['page'];
        $page_size = $count;
        $cid = $params['cid'];
        $s = $params['s'];
        $_params = array(
            'post_type' => 'daogou',
            'paged' => $page_no,
            'posts_per_page' => $page_size,
            's' => $s
        );
        if (!empty($cid)) {
            $_params['tax_query'] = array(
                array(
                    "taxonomy" => "daogou_category",
                    "field" => "id",
                    "terms" => $cid
                )
            );
        }
        query_posts($_params);
        echo $before_widget;
        echo '<div class="hd"><h4 class="xt-bd-l" ' . (!empty($title) ? '' : ' style="display:none;"') . '><span>' . $title . '</span></h4></div><div class="bd">';
        _xt_widget_blog_daogou($layout);
        echo '</div>';
        echo $after_widget;
        echo '<div id="X_Pagination-Bottom" class="clearfix">';
        echo '<div class="pagination xt-pagination-links" style="padding:0;margin:0 auto;">';
        global $wp_query;
        $big = 100; // need an unlikely integer
        echo paginate_links(array(
            'base' => xt_get_daogou_search_url(array_merge($params, array(
                        'page' => '%#%'
                    ))),
            'current' => max(1, get_query_var('paged')),
            'total' => $wp_query->max_num_pages,
            'type' => 'list'
        ));
        echo '</div></div>';
    }

}

/**
 * Helps widget class
 *
 */
class XT_Widget_Helps extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-help',
            'description' => '帮助文章列表'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('syshelps', '帮助文章列表', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'helps',
            'layout' => '9'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('xt_widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        global $wp, $xt_help_itemcat;
        $params = $wp->query_vars['xt_param'];
        $cid = $params['cid'];
        $s = $params['s'];
        $_params = array(
            'post_type' => 'help',
            'nopaging' => 1,
            'orderby' => 'ID',
            'order' => 'DESC'
        );
        if (empty($cid)) {
            $_params['meta_key'] = 'xt_help_hot';
            $_params['meta_value'] = 1;
        } else {
            $_params['tax_query'] = array(
                array(
                    "taxonomy" => "help_category",
                    "field" => "id",
                    "terms" => $cid
                )
            );
        }
        query_posts($_params);
        echo $before_widget;
        $contents = array();
        ?>
        <div class="xt-help-links clearfix">
            <div class="xt-help-links-hd xt-hd-title clearfix">
                <h4 class="xt-bd-l"><?php echo $xt_help_itemcat ? $xt_help_itemcat->name : '常见问题' ?></h4>
            </div>
            <div class="xt-help-links-bd clearfix">
                <ul class="xt-first clearfix text-default">
                    <?php
                    if (have_posts()) {
                        while (have_posts()) {
                            the_post();
                            $_title = get_the_title();
                            echo '<li><a href="#X_Help-' . get_the_ID() . '" title="' . $_title . '">' . wp_trim_words($_title, 20) . '</a></li>';
                            $contents[] = array(
                                'id' => get_the_ID(),
                                'title' => $_title,
                                'content' => get_the_content()
                            );
                        }
                    }
                    ?> 
                </ul>
            </div>
        </div>
        <div class="xt-help-contents">
            <?php
            if (!empty($contents)) {
                foreach ($contents as $content) {
                    ?>
                    <div id="X_Help-<?php echo $content['id'] ?>" class="xt-help-content">
                        <div class="xt-help-content-hd">
                            <h4><i class="icon-question-sign"></i><?php echo $content['title'] ?></h4>
                        </div>
                        <div class="xt-help-content-bd">
                            <?php echo $content['content'] ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>

        </div>
        <?php
        echo $after_widget;
    }

}

/**
 * User widget class
 *
 */
class XT_Widget_User extends XT_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'xt-widget-system-user',
            'description' => '会员详情页'
        );
        $control_ops = array(
            'width' => 500,
            'height' => 350
        );
        parent :: __construct('sysuser', '会员详情', $widget_ops, $control_ops);
    }

    function support() {
        return array(
            'page' => 'user',
            'layout' => '12'
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $covers = isset($instance['covers']) ? $instance['covers'] : array();
        $_covers = array();
        if (!empty($covers)) {
            foreach ($covers as $cover) {
                if (!empty($cover)) {
                    $_covers[] = $cover;
                }
            }
        }
        if (empty($_covers)) {
            $_covers = array(XT_CORE_IMAGES_URL . '/475b3d56jw1dwq73nfyfrj.jpg');
        }
        echo $before_widget;
        global $xt_user, $xt_user_follow, $xt_pageuser_follows, $wp_query;

        $xt_pageuser_follows = get_user_meta(intval($xt_user->ID), XT_USER_FOLLOW, true);

        $_user = wp_get_current_user();
        if ($_user->exists()) {
            if (empty($xt_user_follow)) {
                $xt_user_follow = get_user_meta($_user->ID, XT_USER_FOLLOW, true);
            }
            if (empty($xt_user_follow)) {
                $xt_user_follow = array(
                    $_user->ID
                );
            }
        }

        $_user_id = $wp_query->query_vars['xt_param'];
        $xt_share_param = array(
            'page' => 1,
            'share_per_page' => 40,
            'isHome' => 1,
            'isFavorite' => 0,
            'isShare' => 0,
            'user_id' => $_user_id
        );
        $xt_album_param = array(
            'page' => 1,
            'album_per_page' => 40,
            'isFavorite' => 1,
            'isShare' => 0,
            'user_id' => $_user_id
        );
        $xt_user_param = array(
            'page' => 1,
            'user_per_page' => 21,
            's' => '',
            'follow' => $_user_id,
            'fans' => $_user_id
        );
        ?>
        <div id="X_User-Content" style="background-color:#FAFAFA;">
            <div class="clearfix">
                <div class="xt-user-profile-cover" style="background-image:url(<?php echo $_covers[array_rand($_covers)]; ?>);"></div>
                <div class="xt-user-profile-header">
                    <div id="X_User-Profile-Header" class="" style="position:relative;">
                        <div class="xt-user-profile-info" style="padding-left:225px;">
                            <div class="xt-user-profile-name"><h2><?php xt_the_user_title(); ?></h2></div>
                            <div class="xt-user-profile-desc"><span>简介：<?php echo wp_trim_words(xt_get_the_user_description(), 50); ?></span></div>
                            <div class="xt-user-profile-action">
                                <?php if (!xt_is_self(xt_get_the_user_id())): ?>
                                    <?php if (!empty($xt_user_follow) && in_array((int) xt_get_the_user_id(), $xt_user_follow)): ?>
                                        <div><div class="xt-user-profile-btn"><span><em class="xt-user-profile-icon xt-user-profile-icon-one"></em>已关注<em class="xt-line">|</em><a class="xt-unfollow" data-userid="<?php xt_the_user_id(); ?>" href="javascript:void(0);">取消</a></span></div></div>
                                    <?php else: ?>
                                        <div><a class="xt-user-profile-btn xt-follow" data-userid="<?php xt_the_user_id(); ?>" title="加关注" href="javascript:void(0);"><span><em class="xt-user-profile-btn-icon">+</em>关注</span></a></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="xt-user-profile-photo">
                            <div class="xt-user-profile-pic"><img src="<?php xt_the_user_pic('', 0, 180); ?>" alt="<?php xt_the_user_title(); ?>"></div>
                            <ul class="clearfix">
                                <li><a id="X_User-Follow-A" href="#follow" class="xt-nav"><strong><?php echo (xt_the_user_followcount()) ?></strong><span>关注</span></a></li>
                                <li><a id="X_User-Fans-A" href="#fans" class="xt-nav"><strong><?php echo (xt_the_user_fanscount()) ?></strong><span>粉丝</span></a></li>
                                <li class="xt-last"><a id="X_User-Favorite-A" href="#like" class="xt-nav"><strong><?php echo (xt_the_user_fav_sharecount()) ?></strong><span>喜欢</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="X_Share-UserNav" class="">
                    <ul class="nav nav-tabs" style="padding:0 15px;">
                        <li class="xt-user-center xt-first  active"><a id="X_User-Home-A" href="#home" class="xt-nav"> <i></i> <span>个人首页</span></a></li>
                        <li class="xt-user-share"><a id="X_User-Share-A" href="#share" class="xt-nav"> <i></i> <span>分享</span></a></li>
                        <li class="xt-user-album"><a id="X_User-Share-Album-A" href="#album" class="xt-nav"> <i></i> <span>专辑</span></a></li>
                        <li class="xt-user-like xt-last"><a id="X_User-Favorite-A" href="#like" class="xt-nav"> <i></i> <span>喜欢</span></a></li>
                    </ul>
                </div>
            </div>
            <?php
            if (empty($xt_pageuser_follows)) {
                query_shares(array_merge(array('no_found_rows' => 1), $xt_share_param));
            } else {
                query_shares($xt_share_param);
            }

            echo "<script type='text/javascript'>var XT_SHARE_PARAMS=" . json_encode($xt_share_param) . ";</script>";
            echo "<script type='text/javascript'>var XT_ALBUM_PARAMS=" . json_encode($xt_album_param) . ";</script>";
            echo "<script type='text/javascript'>var XT_USER_PARAMS=" . json_encode($xt_user_param) . ";</script>";
            get_the_share_container($xt_share_param, false, false, false);
            echo '</div>';
            echo $after_widget;
        }

    }

    /**
     * Share widget class
     *
     */
    class XT_Widget_Share extends XT_Widget {

        function __construct() {
            $widget_ops = array(
                'classname' => 'xt-widget-system-share',
                'description' => '分享详情'
            );
            $control_ops = array(
                'width' => 500,
                'height' => 350
            );
            parent :: __construct('sysshare', '分享详情', $widget_ops, $control_ops);
        }

        function support() {
            return array(
                'page' => 'share',
                'layout' => '9'
            );
        }

        function widget($args, $instance) {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            echo $before_widget;
            ?>
            <div id="X_Share-Detail" class="xt-share-detail">
                <?php
                get_the_share_detail_template();
                ?>
                <div class="well well-small">
                    <?php xt_load_template('xt-share_comments'); ?>
                    <textarea id="comment" class="input-xxlarge" style="width:705px;"></textarea>
                    <div class="clearfix">
                        <?php
                        if (function_exists('cs_print_smilies')) {
                            cs_print_smilies();
                        };
                        ?>
                        <a href="javascript:;" class="btn btn-small btn-primary" id="X_Share-comment-submit" data-id="<?php the_share_id() ?>" data-url="<?php echo esc_url(get_the_share_url()); ?>">评 论</a>
                    </div>
                </div>
            </div>
            <?php
            echo $after_widget;
        }

    }

    /**
     * Albums widget class
     *
     */
    class XT_Widget_Albums extends XT_Widget {

        function __construct() {
            $widget_ops = array(
                'classname' => 'xt-widget-system-shares',
                'description' => '专辑列表页'
            );
            $control_ops = array(
                'width' => 500,
                'height' => 350
            );
            parent :: __construct('sysalbums', '专辑列表', $widget_ops, $control_ops);
        }

        function support() {
            return array(
                'page' => 'albums',
                'layout' => '12'
            );
        }

        function widget($args, $instance) {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            echo $before_widget;
            global $wp_query, $xt;
            if ($xt->is_albums) {
                $xt_album_param = $wp_query->query_vars['xt_param'];
                $s = isset($xt_album_param['s']) ? $xt_album_param['s'] : '';
                $h3 = !empty($s) ? $s : '';
                if (!isset($instance['isSort']) || ($instance['isSort'])) {
                    $filterSortOrder = $xt_album_param['sortOrder'];
                    ?>	
                    <div class="row-fluid clearfix" style="margin-bottom:10px;">
                        <h3 class="pull-left text-default" style="margin:0px;"><?php echo $h3 ?></h3>&nbsp;&nbsp;
                        <div class="pull-left" style="padding:8px 0px 3px 20px;">
                            排序：
                            <div class="btn-group">
                                <a class="btn btn-small <?php echo $filterSortOrder == 'popular' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_albums_search_url(array('s' => $s, 'sortOrder' => 'popular')); ?>" data-value="popular">潮流</a>
                                <a class="btn btn-small <?php echo $filterSortOrder == 'newest' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_albums_search_url(array('s' => $s, 'sortOrder' => 'newest')); ?>" data-value="newest">最新</a>
                                <a class="btn btn-small <?php echo $filterSortOrder == 'hot' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_albums_search_url(array('s' => $s, 'sortOrder' => 'hot')); ?>" data-value="hot">最热</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            $xt_album_param = $wp_query->query_vars['xt_param'];
            echo "<script type='text/javascript'>var XT_ALBUM_PARAMS=" . json_encode($xt_album_param) . ";</script>";
            get_the_album_container($xt_album_param, 'no albums', false, false, false);
            echo $after_widget;
        }

    }

    /**
     * Album widget class
     *
     */
    class XT_Widget_Album extends XT_Widget {

        function __construct() {
            $widget_ops = array(
                'classname' => 'xt-widget-system-album',
                'description' => '专辑详情页'
            );
            $control_ops = array(
                'width' => 500,
                'height' => 350
            );
            parent :: __construct('sysalbum', '专辑详情', $widget_ops, $control_ops);
        }

        function support() {
            return array(
                'page' => 'album',
                'layout' => '12'
            );
        }

        function widget($args, $instance) {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            echo $before_widget;
            global $wp_query, $xt_album, $xt_user;
            $xt_share_param = array(
                'album_id' => absint($wp_query->query_vars['xt_param']),
                'page' => 1,
                'share_per_page' => 40,
                'user_id' => $xt_user->ID
            );
            echo "<script type='text/javascript'>var XT_SHARE_PARAMS=" . json_encode($xt_share_param) . ";</script>";
            $user = wp_get_current_user();
            $isSelf = ($user->exists() && ($user->ID == $xt_album->user_id));
            $covers = isset($instance['covers']) ? $instance['covers'] : array();
            $_covers = array();
            if (!empty($covers)) {
                foreach ($covers as $cover) {
                    if (!empty($cover)) {
                        $_covers[] = $cover;
                    }
                }
            }
            if (empty($_covers)) {
                $_covers = array(XT_CORE_IMAGES_URL . '/default_header.jpg');
            }
            ?>
            <?php if ($isSelf): ?>
                <div id="X_Album-Modal-Update-Box" class="modal hide">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>编辑专辑</h3>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="control-group">
                                <label class="control-label" for="X_Album-Update-Title" style="width:100px;">名称</label>
                                <div class="controls" style="margin-left:120px;">
                                    <input type="text" class="input-xlarge" id="X_Album-Update-Title" value="<?php echo $xt_album->title; ?>">
                                    <input type="hidden" id="X_Album-Update-Id" value="<?php echo $xt_album->id; ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="X_Album-Update-Content" style="width:100px;">描述</label>
                                <div class="controls" style="margin-left:120px;">
                                    <textarea id="X_Album-Update-Content" class="input-xlarge" rows="3"><?php echo esc_html($xt_album->content); ?></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary">确定</button>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row-fluid" style="background-color:white;margin-bottom:15px;">
                <div class="span5">
                    <div class="clearfix" style="padding:0 30px;">
                        <h1 style="white-space:nowrap;overflow:hidden;font-size:20px;"><?php if ($isSelf): ?>[<a href="javascript:;" data-toggle="modal" data-target="#X_Album-Modal-Update-Box" style="text-decoration: none;">编辑</a>]&nbsp;<?php endif; ?><?php echo ($xt_album->title) ?></h1>
                        <div class="pull-left muted">宝贝数量 <b class="text-default"><?php echo $xt_album->share_count ?></b></div> <div class="pull-right muted" style="margin-right:60px;">喜欢指数 <b class="text-default"><?php echo $xt_album->fav_count ?></b></div>
                    </div>
                    <div class="clearfix muted" style="padding:5px 30px 0px;height:76px;font-size:12px;font-weight:normal;overflow:hidden;">
                        <?php echo esc_html($xt_album->content); ?>
                    </div>
                    <div class="clearfix" style="padding:5px 30px;">
                        <div class="media">
                            <a class="pull-left" target="_blank" href="<?php xt_the_user_url(); ?>"><img class="media-object" style="width:32px;height:32px;" src="<?php xt_the_user_pic(); ?>"></a>
                            <div class="media-body">
                                <h4 class="media-heading" style="font-size:12px;line-height:12px;"><a target="_blank" href="<?php xt_the_user_url(); ?>"><?php xt_the_user_title(); ?></a></h4>
                                <div class="media" style="margin-top:0px;">
                                    <small>
                                        <span>关注</span><span><?php echo (xt_the_user_followcount()) ?></span>&nbsp;&nbsp;
                                        <span>粉丝</span><span><?php echo (xt_the_user_fanscount()) ?></span>&nbsp;&nbsp;
                                        <span>宝贝</span><span><?php echo (xt_get_the_user_sharecount() + xt_get_the_user_fav_sharecount()) ?></span>&nbsp;&nbsp;
                                        <span>喜欢</span><span><?php echo (xt_get_the_user_fav_sharecount() + xt_get_the_user_fav_albumcount()) ?></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>     
                </div>
                <div class="span7" style="height:240px;overflow:hidden;">
                    <img src="<?php echo $_covers[array_rand($_covers)]; ?>" width="100%">
                </div>
            </div>
            <?php
            get_the_share_container($xt_share_param, false, false, false);
            ?>
            <?php
            echo $after_widget;
        }

    }

    /**
     * Shares widget class
     *
     */
    class XT_Widget_Shares extends XT_Widget {

        function __construct() {
            $widget_ops = array(
                'classname' => 'xt-widget-system-shares',
                'description' => '分享搜索'
            );
            $control_ops = array(
                'width' => 500,
                'height' => 350
            );
            parent :: __construct('sysshares', '分享搜索', $widget_ops, $control_ops);
        }

        function support() {
            return array(
                'page' => 'shares',
                'layout' => '12'
            );
        }

        function widget($args, $instance) {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            echo $before_widget;
            global $wp_query;
            $xt_share_param = $wp_query->query_vars['xt_param'];
            echo "<script type='text/javascript'>var XT_SHARE_PARAMS=" . json_encode($xt_share_param) . ";</script>";
            get_the_share_container($xt_share_param, isset($instance['isCat']) && $instance['isCat']);
            echo $after_widget;
        }

    }

    /**
     * Taobaos widget class
     *
     */
    class XT_Widget_Taobaos extends XT_Widget {

        function __construct() {
            $widget_ops = array(
                'classname' => 'xt-widget-grid xt-widget-system-taobaos',
                'description' => '淘宝搜索页-列表'
            );
            $control_ops = array(
                'width' => 500,
                'height' => 350
            );
            parent :: __construct('systaobaos', '淘宝搜索列表', $widget_ops, $control_ops);
        }

        function support() {
            return array(
                'page' => 'taobaos',
                'layout' => '9,12'
            );
        }

        function widget($args, $instance) {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            global $wp_query;
            $xt_taobaos_param = $wp_query->query_vars['xt_param'];
            $xt_taobaos_param['start_commissionRate'] = isset($instance['start_commissionRate']) ? intval($instance['start_commissionRate']) : '';
            $xt_taobaos_param['end_commissionRate'] = isset($instance['end_commissionRate']) ? intval($instance['end_commissionRate']) : '';
            _xt_widget_grid_taobao($args, array(
                'dataType' => 'taobao',
                'page_no' => $xt_taobaos_param['page_no'],
                'page_size' => $instance['count'],
                'size' => 'big',
                'taobao' => $xt_taobaos_param,
                'urlType' => isset($instance['urlType']) ? $instance['urlType'] : 'jump'
            ));
        }

    }

    /**
     * Taobao widget class
     *
     */
    class XT_Widget_Taobao extends XT_Widget {

        function __construct() {
            $widget_ops = array(
                'classname' => 'xt-widget-system-taobao',
                'description' => '淘宝商品详情页'
            );
            $control_ops = array(
                'width' => 500,
                'height' => 350
            );
            parent :: __construct('systaobao', '淘宝商品详情页', $widget_ops, $control_ops);
        }

        function support() {
            return array(
                'page' => 'taobao',
                'layout' => '12'
            );
        }

        function widget($args, $instance) {
            extract($args);
            global $xt, $xt_taobao_item;
            if ($xt->is_taobao && !empty($xt_taobao_item)) {
                echo $before_widget;
                $_pic = $xt_taobao_item->pic_url . '_310x310.jpg';
                $_title = $xt_taobao_item->title;
                $_seller = $xt_taobao_item->nick;
                $_url = xt_jump_url(array('id' => $xt_taobao_item->num_iid));
                ?>
                <div class="row-fluid">
                    <div class="span9">
                        <div class="media">
                            <a href="<?php echo $_url; ?>" class="pull-left" target="_blank">
                                <?php xt_write_pic(base64_encode($_pic), $_title) ?>
                                <span class="X_Fanxian-Tip"><i>返现金额与实际成交价相关</i></span>
                            </a>
                            <div class="media-body">
                                <h1 class="media-heading"><a style="width:310px;" id="X_Detail-Item-Get-Title" data-id="<?php echo $xt_taobao_item->num_iid; ?>" href="<?php echo $_url; ?>" target="_blank" class="text-gray"><?php echo $_title ?></a></h1>
                                <p class="clearfix">
                                <table class="table">
                                    <tbody>
                                        <tr><th>店&nbsp;掌&nbsp;柜：</th><td><a id="X_Detail-Item-Get-Seller" href="javascript:;" target="_blank"><?php echo $xt_taobao_item->nick; ?></a></td></tr>
                                        <tr><th>所&nbsp;在&nbsp;地：</th><td><?php echo $xt_taobao_item->location->state . '&nbsp;&nbsp;' . $xt_taobao_item->location->city; ?></td></tr>
                                        <tr id="X_Detail-Item-Get-Volume-Title" class="hide"><th>销&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：</th><td id="X_Detail-Item-Get-Volume"></td></tr>
                                        <tr class="xt-price"><th>价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格：</th><td><b>￥</b><strong id="X_Detail-Item-Get-Price"><?php echo $xt_taobao_item->price; ?></strong></td></tr>
                                        <tr class="xt-fanxian"><th>返&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;利：</th><td id="X_Detail-Item-Get-Fanxian" data-type="<?php echo xt_fanxian_is_jifenbao('taobao') ? 'jifenbao' : 'cash' ?>"></td></tr>
                                        <tr class="xt-btns">
                                            <td colspan="2"><a href="<?php echo $_url ?>" target="_blank" class="btn btn-primary btn-large">立刻购买</a>&nbsp;&nbsp;&nbsp;&nbsp;<button id="X_Detail-Item-Get-Publish-Btn" data-url="<?php echo $xt_taobao_item->detail_url; ?>" type="button" class="btn btn-success btn-large X_Publish">我要分享</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="span3">
                        <div id="X_Detail-Item-Get-Recommend" class="xt-widget-recommend-side clearfix" data-platform="taobao" data-cid="<?php echo $xt_taobao_item->cid ?>" data-id="<?php echo $xt_taobao_item->num_iid; ?>">
                            <div class="hd"><h4 class="xt-bd-l"><span>猜你喜欢</span></h4></div>
                            <div class="bd" style="padding:10px 0 5px;">
                                <ul class="media-list">
                                </ul>               
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                echo $after_widget;
            }
        }

    }

    /**
     * TaobaoRecommend widget class
     *
     */
    class XT_Widget_TaobaoRecommend extends XT_Widget {

        function __construct() {
            $widget_ops = array(
                'classname' => 'xt-widget-grid xt-widget-system-taobaorecommend',
                'description' => '淘宝商品详情页推荐列表'
            );
            $control_ops = array(
                'width' => 500,
                'height' => 350
            );
            parent :: __construct('systaobaorecommend', '淘宝商品详情页推荐列表', $widget_ops, $control_ops);
        }

        function support() {
            return array(
                'page' => 'taobao',
                'layout' => '12'
            );
        }

        function widget($args, $instance) {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            global $xt, $xt_taobao_item;
            if ($xt->is_taobao && !empty($xt_taobao_item)) {
                echo $before_widget;
                ?>
                <div id="X_Detail-Item-Get-Recommend-Grid" class="shop-display">
                    <div class="hd">
                        <h4 class="xt-bd-l" <?php echo!empty($title) ? '' : 'style="display:none"' ?>><span><?php echo $title ?></span></h4>
                    </div>
                    <div class="bd">
                        <ul class="thumbnails thumbnails-span3 clearfix">
                        </ul>
                    </div>
                </div>
                <?php
                echo $after_widget;
            }
        }

    }

    /**
     * Shops widget class
     *
     */
    class XT_Widget_Shops extends XT_Widget {

        function __construct() {
            $widget_ops = array(
                'classname' => 'xt-widget-system-shops',
                'description' => '淘宝店铺搜索页-列表'
            );
            $control_ops = array(
                'width' => 500,
                'height' => 350
            );
            parent :: __construct('sysshops', '淘宝店铺搜索列表', $widget_ops, $control_ops);
        }

        function support() {
            return array(
                'page' => 'shops',
                'layout' => '9'
            );
        }

        function widget($args, $instance) {
            extract($args);
            global $wp_query, $xt;
            if ($xt->is_shops) {
                echo $before_widget;
                $xt_shops_param = $wp_query->query_vars['xt_param'];
                $xt_shops_param['page_size'] = absint($instance['count']);
                $results = xt_taobaoke_shops_search($xt_shops_param);
                if (is_wp_error($results)) {
                    xt_api_error($results);
                } else {
                    $shops = $results['shops'];
                    $total = $results['total'];
                    $filterType = 0;
                    if ($xt_shops_param['only_mall']) {
                        $filterType = 1;
                    } elseif ($xt_shops_param['start_credit'] == 16 && $xt_shops_param['end_credit'] == 20) {
                        $filterType = 2;
                    } elseif ($xt_shops_param['start_credit'] == 11 && $xt_shops_param['end_credit'] == 15) {
                        $filterType = 3;
                    }
                    $page = $xt_shops_param['page_no'];
                    $prev_url = $next_url = '';
                    if ($page > 1) {
                        $prev_url = xt_get_shop_search_url(array_merge($xt_shops_param, array('page_no' => $page - 1)));
                    }
                    if ($page < $total) {
                        $next_url = xt_get_shop_search_url(array_merge($xt_shops_param, array('page_no' => $page + 1)));
                    }
                    $base = xt_get_shop_search_url(array_merge($xt_shops_param, array('page_no' => '%#%')));
                    $pager_top = xt_search_pager_top($prev_url, $next_url, $page, $xt_shops_param['page_size'], $total);
                    $pager_bottom = xt_search_pager_bottom($base, $page, $xt_shops_param['page_size'], $total);
                    echo '<div class="hd">';
                    xt_output_breadcrumbs(true, array('name' => $total . '个店铺', 'url' => ''), false, $pager_top);
                    echo '</div>';
                    $isFanxian = xt_is_fanxian();
                    $urlType = isset($instance['urlType']) ? $instance['urlType'] : 'direct';
                    ?>
                    <div class="bd" style="padding-top: 10px;">
                        <div class="xt-filter-box xt-shop-filter form-inline">
                            <label>关键词：</label>
                            <input id="X_Shop-Filter-Keyword" type="text" class="input-small" value="<?php echo esc_attr($xt_shops_param['keyword']) ?>">
                            &nbsp;&nbsp;
                            <select id="X_Shop-Filter-Start-Credit" style="width:80px">
                                <option value="">等级</option>
                                <option value="1">一心</option>
                                <option value="2">两心</option>
                                <option value="3">三心</option>
                                <option value="4">四心</option>
                                <option value="5">五心</option>
                                <option value="6">一钻</option>
                                <option value="7">两钻</option>
                                <option value="8">三钻</option>
                                <option value="9">四钻</option>
                                <option value="10">五钻</option>
                                <option value="11">一皇冠</option>
                                <option value="12">两皇冠</option>
                                <option value="13">三皇冠</option>
                                <option value="14">四皇冠</option>
                                <option value="15">五皇冠</option>
                                <option value="16">一金冠</option>
                                <option value="17">两金冠</option>
                                <option value="18">三金冠</option>
                                <option value="19">四金冠</option>
                                <option value="20">五金冠</option>
                            </select>
                            &nbsp;&nbsp;--&nbsp;&nbsp;
                            <select id="X_Shop-Filter-End-Credit" style="width:80px">
                                <option value="">等级</option>
                                <option value="1">一心</option>
                                <option value="2">两心</option>
                                <option value="3">三心</option>
                                <option value="4">四心</option>
                                <option value="5">五心</option>
                                <option value="6">一钻</option>
                                <option value="7">两钻</option>
                                <option value="8">三钻</option>
                                <option value="9">四钻</option>
                                <option value="10">五钻</option>
                                <option value="11">一皇冠</option>
                                <option value="12">两皇冠</option>
                                <option value="13">三皇冠</option>
                                <option value="14">四皇冠</option>
                                <option value="15">五皇冠</option>
                                <option value="16">一金冠</option>
                                <option value="17">两金冠</option>
                                <option value="18">三金冠</option>
                                <option value="19">四金冠</option>
                                <option value="20">五金冠</option>
                            </select>
                            &nbsp;&nbsp;
                            <button id="X_Shop-Filter-Btn" class="btn btn-small btn-primary" data-url="<?php echo xt_get_shop_search_url(array('keyword' => 'SEARCH', 'start_credit' => 'START', 'end_credit' => 'END')); ?>" data-loading-text="搜索中...">搜索</button>
                        </div>
                        <div class="xt-filter-tab clearfix">
                            <ul>
                                <li class="<?php echo $filterType == 0 ? 'active' : '' ?>"><a href="<?php echo xt_get_shop_search_url() ?>">所有店铺</a></li>
                                <li class="<?php echo $filterType == 1 ? 'active' : '' ?>"><a href="<?php echo xt_get_shop_search_url(array('cid' => $xt_shops_param['cid'], 'only_mall' => 1, 'keyword' => $xt_shops_param['keyword'])) ?>">天猫商城</a></li>
                                <li class="<?php echo $filterType == 2 ? 'active' : '' ?>"><a href="<?php echo xt_get_shop_search_url(array('cid' => $xt_shops_param['cid'], 'start_credit' => 16, 'end_credit' => 20, 'keyword' => $xt_shops_param['keyword'])) ?>">至尊店铺</a></li>
                                <li class="xt-last <?php echo $filterType == 3 ? 'active' : '' ?>"><a href="<?php echo xt_get_shop_search_url(array('cid' => $xt_shops_param['cid'], 'start_credit' => 11, 'end_credit' => 15, 'keyword' => $xt_shops_param['keyword'])) ?>">皇冠店铺</a></li>
                            </ul>
                        </div>
                        <div class="row-fluid clearfix">
                            <?php
                            foreach ($shops as $shop) {
                                $click_url = $shop->click_url;
                                if ($urlType == 'jump') {
                                    $click_url = xt_jump_url(array('title' => $shop->shop_title, 'url' => $click_url));
                                }
                                ?>
                                <div class="span6">
                                    <div class="media">
                                        <div class="pull-left">
                                            <a rel="nofollow" data-type="1" data-sellerid="<?php echo $shop->user_id; ?>" data-rd="1" data-style="2" data-tmpl="140x190"></a>                                                    
                                        </div>
                                        <div class="media-body">
                                            <h5 class="media-heading"><a rel="nofollow" href="<?php echo $click_url; ?>" target="_blank"><?php echo $shop->shop_title ?></a></h5>
                                            <ul class="unstyled">
                                                <li>店铺掌柜：<?php echo $shop->seller_nick; ?></li>
                                                <li>信用等级：<i class="rank seller-rank-<?php echo $shop->seller_credit ?>"></i></li>
                                                <li>累计销量：<?php echo $shop->total_auction; ?></li>
                                                <li>宝贝数量：<?php echo $shop->auction_count; ?></li>
                                                <?php if ($isFanxian) { ?><li>平均返现：<strong class="text-default"><?php echo $shop->commission_rate; ?>%</strong></li><?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        if (!empty($pager_bottom)) {
                            echo '<div id="X_Pagination-Bottom" class="clearfix">';
                            echo '<div class="pagination pagination-large xt-pagination-links">';
                            echo $pager_bottom;
                            echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    }


                    echo $after_widget;
                    ?>
                    <script>
                        jQuery(function($){
                            $('#X_Shop-Filter-Start-Credit').val(<?php echo $xt_shops_param['start_credit']; ?>);
                            $('#X_Shop-Filter-End-Credit').val(<?php echo $xt_shops_param['end_credit']; ?>)
                            $('.xt-widget-system-shops .span6').hover(function(){
                                $(this).addClass('hover').siblings().removeClass('hover');
                            },function(){
                                $(this).removeClass('hover');
                            });
                            $('#X_Shop-Filter-Btn').click(function(){
                                var url = ($(this).attr('data-url'));
                                var self=$(this);
                                if(url){
                                    try{
                                        var keyword = $('#X_Shop-Filter-Keyword').val();
                                        var start_credit = $('#X_Shop-Filter-Start-Credit').val();
                                        var end_credit = $('#X_Shop-Filter-End-Credit').val();
                                        if(start_credit&&end_credit){
                                            if(parseInt(end_credit)<parseInt(start_credit)){
                                                alert('结束信用等级不能小于开始信用等级');
                                                return false;
                                            }
                                        }
                                        self.button('loading');
                                        url = url.replace('SEARCH',encodeURIComponent(keyword)).replace('START',start_credit).replace('END',end_credit);
                                        document.location.href = url;
                                    }catch(e){
                                        $(this).button('reset');
                                    }
                                }
                            });
                        })
                    </script>
                    <?php
                }
            }

        }

        /**
         * Paipais widget class
         *
         */
        class XT_Widget_Paipais extends XT_Widget {

            function __construct() {
                $widget_ops = array(
                    'classname' => 'xt-widget-grid xt-widget-system-paipais',
                    'description' => '拍拍搜索页-列表'
                );
                $control_ops = array(
                    'width' => 500,
                    'height' => 350
                );
                parent :: __construct('syspaipais', '拍拍搜索列表', $widget_ops, $control_ops);
            }

            function support() {
                return array(
                    'page' => 'paipais',
                    'layout' => '9,12'
                );
            }

            function widget($args, $instance) {
                extract($args);
                $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                global $wp_query;
                $xt_paipais_param = $wp_query->query_vars['xt_param'];
                $xt_paipais_param['crMin'] = isset($instance['crMin']) ? intval($instance['crMin']) : '';
                $xt_paipais_param['crMax'] = isset($instance['crMax']) ? intval($instance['crMax']) : '';
                _xt_widget_grid_paipai($args, array(
                    'dataType' => 'paipai',
                    'pageIndex' => $xt_paipais_param['pageIndex'],
                    'pageSize' => $instance['count'],
                    'size' => 'big',
                    'paipai' => $xt_paipais_param
                ));
            }

        }

        /**
         * Bijias widget class
         *
         */
        class XT_Widget_Bijias extends XT_Widget {

            function __construct() {
                $widget_ops = array(
                    'classname' => 'xt-widget-grid xt-widget-system-bijias',
                    'description' => '全网搜索页-列表'
                );
                $control_ops = array(
                    'width' => 500,
                    'height' => 350
                );
                parent :: __construct('sysbijias', '全网搜索列表', $widget_ops, $control_ops);
            }

            function support() {
                return array(
                    'page' => 'bijias',
                    'layout' => '9,12'
                );
            }

            function widget($args, $instance) {
                extract($args);
                $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                global $wp_query;
                $xt_bijias_param = $wp_query->query_vars['xt_param'];
                _xt_widget_grid_bijia($args, array(
                    'dataType' => 'bijia',
                    'page' => $xt_bijias_param['page_no'],
                    'rowCount' => $instance['count'],
                    'size' => 'big',
                    'bijia' => $xt_bijias_param,
                    'urlType' => isset($instance['type']) ? $instance['type'] : 'direct'
                ));
            }

        }

        /**
         * Tuans widget class
         *
         */
        class XT_Widget_Tuans extends XT_Widget {

            function __construct() {
                $widget_ops = array(
                    'classname' => 'xt-widget-grid xt-widget-system-tuans',
                    'description' => '团购搜索页-列表'
                );
                $control_ops = array(
                    'width' => 500,
                    'height' => 350
                );
                parent :: __construct('systuans', '团购搜索列表', $widget_ops, $control_ops);
            }

            function support() {
                return array(
                    'page' => 'tuans',
                    'layout' => '12'
                );
            }

            function widget($args, $instance) {
                extract($args);
                $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                global $wp_query;
                $xt_tuans_param = $wp_query->query_vars['xt_param'];
                _xt_widget_grid_tuan($args, array(
                    'dataType' => 'tuan',
                    'page' => $xt_tuans_param['page_no'],
                    'pagesize' => $instance['count'],
                    'size' => 'big',
                    'tuan' => $xt_tuans_param,
                    'urlType' => isset($instance['type']) ? $instance['type'] : 'direct'
                ));
            }

        }

        /**
         * Temais widget class
         *
         */
        class XT_Widget_Temais extends XT_Widget {

            function __construct() {
                $widget_ops = array(
                    'classname' => 'xt-widget-grid xt-widget-system-temais',
                    'description' => '淘宝特卖搜索页-列表'
                );
                $control_ops = array(
                    'width' => 500,
                    'height' => 350
                );
                parent :: __construct('systemais', '淘宝特卖搜索列表', $widget_ops, $control_ops);
            }

            function support() {
                return array(
                    'page' => 'temais',
                    'layout' => '9,12'
                );
            }

            function widget($args, $instance) {
                extract($args);
                $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                global $wp_query;
                $xt_temais_param = $wp_query->query_vars['xt_param'];
                _xt_widget_grid_temai($args, array(
                    'dataType' => 'temai',
                    'page_no' => $xt_temais_param['page_no'],
                    'page_size' => 48,
                    'size' => 'big',
                    'temai' => $xt_temais_param,
                    'urlType' => isset($instance['urlType']) ? $instance['urlType'] : 'jump'
                ));
            }

        }

        /**
         * Coupons widget class
         *
         */
        class XT_Widget_Coupons extends XT_Widget {

            function __construct() {
                $widget_ops = array(
                    'classname' => 'xt-widget-grid xt-widget-system-coupons',
                    'description' => '淘宝折扣搜索页-列表'
                );
                $control_ops = array(
                    'width' => 500,
                    'height' => 350
                );
                parent :: __construct('syscoupons', '淘宝折扣搜索列表', $widget_ops, $control_ops);
            }

            function support() {
                return array(
                    'page' => 'coupons',
                    'layout' => '9,12'
                );
            }

            function widget($args, $instance) {
                extract($args);
                $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                global $wp_query;
                $xt_coupons_param = $wp_query->query_vars['xt_param'];
                $xt_coupons_param['start_commissionRate'] = isset($instance['start_commissionRate']) ? intval($instance['start_commissionRate']) : '';
                $xt_coupons_param['end_commissionRate'] = isset($instance['end_commissionRate']) ? intval($instance['end_commissionRate']) : '';
                _xt_widget_grid_coupon($args, array(
                    'dataType' => 'coupon',
                    'page_no' => $xt_coupons_param['page_no'],
                    'page_size' => $instance['count'],
                    'size' => 'big',
                    'coupon' => $xt_coupons_param,
                    'urlType' => isset($instance['urlType']) ? $instance['urlType'] : 'jump'
                ));
            }

        }

        /**
         * Invite widget class
         *
         */
        class XT_Widget_Invite extends XT_Widget {

            function __construct() {
                $widget_ops = array(
                    'classname' => 'xt-widget-system-invite',
                    'description' => '邀请'
                );
                $control_ops = array(
                    'width' => 500,
                    'height' => 350
                );
                parent :: __construct('sysinvite', '会员邀请', $widget_ops, $control_ops);
            }

            function support() {
                return array(
                    'page' => 'invite',
                    'layout' => '12'
                );
            }

            function widget($args, $instance) {
                extract($args);
                $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                echo $before_widget;
                global $xt_user, $wpdb;
                $user = wp_get_current_user();
                ?>
                <div class="xt-invite">
                    <img src="<?php echo XT_CORE_IMAGES_URL ?>/T1_C22XfxgXXbY4ZLi-945-50.png" width="945" height="50" alt="" style="vertical-align: top;">
                    <img src="<?php echo XT_CORE_IMAGES_URL ?>/T10pL4XfVXXXcUTwcX-945-185.png" width="945" height="185" alt="" style="vertical-align: top;">
                    <img src="<?php echo XT_CORE_IMAGES_URL ?>/T16VY4Xf4bXXa.M2ZX-945-163.png" width="945" height="163" alt="" style="vertical-align: top;">
                    <div class="xt-invite-info">
                        <p style="text-indent: 0;">亲，</p>
                        <p style="margin: 5px 0 0 15px;">
                            <?php
                            if (empty($xt_user->display_name))
                                $xt_user->display_name = $xt_user->user_login;
                            $user_name = $wpdb->escape($xt_user->display_name);
                            $jifen = intval(xt_fanxian_registe_jifen());
                            if ($jifen > 100) {
                                $jifen = intval($jifen / 100);
                            } else {
                                $jifen = 0;
                            }
                            $amount = intval(xt_fanxian_registe_cash()) + $jifen;
                            echo str_replace(array(
                                '#user#',
                                '#site#',
                                '#amount#'
                                    ), array(
                                '<strong>' . $user_name . '</strong>',
                                get_bloginfo('name'),
                                '<strong class="text-default">' . $amount . '</strong>'
                                    ), esc_textarea($instance['desc']));
                            ?>
                        </p>
                    </div>
                    <div class="xt-invite-join"><a class="btn btn-large btn-primary" <?php
                    if (!$user->exists()) {
                        echo ' href="' . esc_url(site_url('wp-login.php?action=register&redirect_to' . urlencode(home_url()), 'login')) . '" ';
                    } else {
                        echo ' href="javascript:;"  onclick="alert(\'该红包为新人专享,您无法领取!\')" ';
                    }
                            ?>>接受邀请拿红包</a></div>
                    <div class="xt-invite-step"><a href="<?php echo home_url(); ?>" target="_blank"> <img src="<?php echo XT_CORE_IMAGES_URL ?>/T13Fj3XfXiXXc1XAPD-925-127.png" width="925" height="127"></a></div>
                </div>
                <?php
                echo $after_widget;
            }

        }

        /**
         * Daogou widget class
         *
         */
        class XT_Widget_Daogou extends XT_Widget {

            function __construct() {
                $widget_ops = array(
                    'classname' => 'xt-widget-system-daogou',
                    'description' => '导购详情页'
                );
                $control_ops = array(
                    'width' => 500,
                    'height' => 350
                );
                parent :: __construct('sysdaogou', '导购详情页', $widget_ops, $control_ops);
            }

            function support() {
                return array(
                    'page' => 'daogou',
                    'layout' => '12'
                );
            }

            function widget($args, $instance) {
                extract($args);
                $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                echo $before_widget;
                global $post;
                if (have_posts())
                    : while (have_posts())
                        : the_post();
                        echo '<div class="hd"><h4 class="xt-bd-l"><span><a href="' . get_permalink() . '">' . get_the_title() . '</a>&nbsp;&nbsp;&nbsp;&nbsp;<small>' . get_the_date() . '</small></span></h4></div><div class="bd">';
                        echo '<div class="row-fluid">';
                        the_content();

                        $items = get_post_meta($post->ID, 'xt_items', true);
                        if (!empty($items)) {
                            $itemsHtml = '';
                            foreach ($items as $item) {
                                if (!empty($item['pic'])) {
                                    $_url = xt_jump_url(array(
                                        'id' => get_the_share_key($item['key']),
                                        'type' => $item['type'],
                                        'share' => $item['guid']
                                            ));
                                    $itemsHtml .= '<li>
                                                <a class="thumbnail" rel="nofollow" target="_blank" href="' . $_url . '" title="' . $item['title'] . '">'
                                            . xt_write_pic(base64_encode(xt_pic_url($item['pic'], 160, $item['type'])), $item['title'], 0, 0, '', '', false) .
                                            '</a>
                                                 <div class="caption">
                                                    <div class="desc"><a rel="nofollow" target="_blank" href="' . $_url . '" class="permalink">' . $item['title'] . '</a></div>
                                                </div>
                                              </li>';
                                }
                            }
                            if (!empty($itemsHtml))
                                echo '<div class="span12"><h4>推荐的宝贝</h4><ul class="thumbnails">' . $itemsHtml . '</ul></div>';
                        }
                        comments_template('xt-daogou_comments.php', true);
                        echo '</div></div>';
                    endwhile;
                endif;
                echo $after_widget;
            }

        }

        function xt_daogou_comment($comment, $args, $depth) {
            $GLOBALS['comment'] = $comment;
            ?>	
            <li class="media" style="border-bottom: 1px dashed #DDD;padding-bottom:5px;" id="comment-<?php comment_ID(); ?>">
                <a class="pull-left" href="<?php xt_the_user_url($comment->user_id) ?>"><?php echo get_avatar($comment, 32); ?></a>
                <div class="media-body">
                    <h5 class="media-heading clearfix" style="margin-bottom:0px;">
                        <span class="pull-right muted" style="font-weight:normal;"><?php echo sprintf('%1$s %2$s', get_comment_date(), get_comment_time()); ?></span>
                        <a href="<?php xt_the_user_url($comment->user_id) ?>" target="_blank"><?php echo get_comment_author(); ?></a>
                        <span class="muted" style="font-weight:normal;">(<?php comment_reply_link(array_merge($args, array('reply_text' => '回复', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>)</span>
                    </h5>
                    <div class="media" style="margin-top:0px;">
                        <?php comment_text(); ?>
                    </div>
                </div>
            </li>
            <?php
        }

        /**
         * Help widget class
         *
         */
        class XT_Widget_Help extends XT_Widget {

            function __construct() {
                $widget_ops = array(
                    'classname' => 'xt-widget-system-help',
                    'description' => '帮助详情页'
                );
                $control_ops = array(
                    'width' => 500,
                    'height' => 350
                );
                parent :: __construct('syshelp', '帮助详情页', $widget_ops, $control_ops);
            }

            function support() {
                return array(
                    'page' => 'help',
                    'layout' => '12'
                );
            }

            function widget($args, $instance) {
                extract($args);
                $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                echo $before_widget;
                global $post;
                if (have_posts())
                    : while (have_posts())
                        : the_post();
                        echo '<div class="hd"><h4 class="xt-bd-l"><span><a href="' . get_permalink() . '">' . get_the_title() . '</a>&nbsp;&nbsp;&nbsp;&nbsp;<small>' . get_the_date() . '</small></span></h4></div><div class="bd">';
                        echo '<div class="row-fluid">';
                        the_content();
                        comments_template('xt-help_comments.php', true);
                        echo '</div></div>';
                    endwhile;
                endif;
                echo $after_widget;
            }

        }

        function xt_help_comment($comment, $args, $depth) {
            $GLOBALS['comment'] = $comment;
            ?>	
            <li class="media" style="border-bottom: 1px dashed #DDD;padding-bottom:5px;" id="comment-<?php comment_ID(); ?>">
                <a class="pull-left" href="<?php xt_the_user_url($comment->user_id) ?>"><?php echo get_avatar($comment, 32); ?></a>
                <div class="media-body">
                    <h5 class="media-heading clearfix" style="margin-bottom:0px;">
                        <span class="pull-right muted" style="font-weight:normal;"><?php echo sprintf('%1$s %2$s', get_comment_date(), get_comment_time()); ?></span>
                        <a href="<?php xt_the_user_url($comment->user_id) ?>" target="_blank"><?php echo get_comment_author(); ?></a>
                        <span class="muted" style="font-weight:normal;">(<?php comment_reply_link(array_merge($args, array('reply_text' => '回复', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>)</span>
                    </h5>
                    <div class="media" style="margin-top:0px;">
                        <?php comment_text(); ?>
                    </div>
                </div>
            </li>
            <?php
        }