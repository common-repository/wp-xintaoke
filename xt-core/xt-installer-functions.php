<?php

function xt_install_home() {
    return wp_insert_post(array(
                'post_title' => '新淘客首页(默认)',
                'post_type' => 'page',
                'post_name' => xt_base(),
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_content' => '<h1>新淘客默认首页,请勿编辑该页面</h1>',
                'post_status' => 'publish',
                'post_author' => 0,
                'menu_order' => 0
            ));
}

function xt_install_menu($global) {
    $menu_id = wp_create_nav_menu('新淘客菜单');
    if ($menu_id) {
        //home
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => get_home_url('/'),
            'menu-item-title' => '首页',
            'menu-item-status' => 'publish',
            'menu-item-position' => 1
        ));
        //shares
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_get_shares_search_url(),
            'menu-item-title' => '逛街啦',
            'menu-item-status' => 'publish',
            'menu-item-position' => 2
        ));
        //albums
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_get_albums_search_url(),
            'menu-item-title' => '专辑',
            'menu-item-status' => 'publish',
            'menu-item-position' => 3));
        //taobaos
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_get_taobao_search_url(),
            'menu-item-title' => '淘宝',
            'menu-item-status' => 'publish',
            'menu-item-position' => 6
        ));
        //paipais
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_get_paipai_search_url(),
            'menu-item-title' => '拍拍',
            'menu-item-status' => 'publish',
            'menu-item-position' => 7
        ));
        //bijias
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_get_bijia_search_url(),
            'menu-item-title' => '比价',
            'menu-item-status' => 'publish',
            'menu-item-position' => 8
        ));
        //tuans
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_get_tuan_search_url(),
            'menu-item-title' => '团购',
            'menu-item-status' => 'publish',
            'menu-item-position' => 9
        ));
        //coupons
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_get_coupon_search_url(),
            'menu-item-title' => '折扣',
            'menu-item-status' => 'publish',
            'menu-item-position' => 10
        ));
        //temais
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_get_temai_search_url(),
            'menu-item-title' => '特卖',
            'menu-item-status' => 'publish',
            'menu-item-position' => 11
        ));

        //stars
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_site_url('stars'),
            'menu-item-title' => '明星',
            'menu-item-status' => 'publish',
            'menu-item-position' => 12
        ));
        //brands
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-type' => 'custom',
            'menu-item-url' => xt_site_url('brands'),
            'menu-item-title' => '品牌',
            'menu-item-status' => 'publish',
            'menu-item-position' => 13
        ));
        set_theme_mod('nav_menu_locations', array('primary' => $menu_id));
        $global['isMenu'] = 1;
        update_option(XT_OPTION_GLOBAL, $global);
    }
}