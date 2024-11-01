<?php

/**
 * WP Xintaoke AJAX and Init functions
 *
 * These are the XT AJAX and Init functions
 *
 * @package wp-xintaoke
 */
function xt_ajax_login_box() {
    xt_load_template('xt-login_box.php');
    exit();
}

add_action('wp_ajax_nopriv_xt_ajax_login_box', 'xt_ajax_login_box');

function xt_ajax_login() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    $user = wp_signon('', false);
    if (is_wp_error($user)) {
        $result['code'] = 500;
        $result['msg'] = $user->get_error_message();
    }
    exit(json_encode($result));
}

add_action('wp_ajax_nopriv_xt_ajax_login', 'xt_ajax_login');

function xt_ajax_item_get() {
    xt_load_template('xt-item_box.php');
    exit();
}

add_action('wp_ajax_xt_ajax_item_get', 'xt_ajax_item_get');
add_action('wp_ajax_nopriv_xt_ajax_item_get', 'xt_ajax_item_get');

function xt_ajax_item_get_paipaike() {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $result = array(
        'commission' => -1,
        'dwTotalPayNum' => -1
    );
    if (empty($id)) {
        exit(json_encode($result));
    }
    $item = xt_paipaike_item($id);
    if (is_wp_error($item)) {
        exit(json_encode($result));
    }
    if (isset($item->cpsSearchCommData) && !empty($item->cpsSearchCommData)) {
        $data = $item->cpsSearchCommData;
        $price = (absint($data->dwPrice)) / 100;
        $rate = 0;
        if ($data->dwPrimaryCmm) {//主推
            $rate = (absint($data->dwPrimaryRate)) / 10000;
        } else {//类目
            $rate = (absint($data->dwClassRate)) / 10000;
        }
        $result['commission'] = round($price * $rate, 2);
        $result['dwTotalPayNum'] = $data->dwTotalPayNum;
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_item_get_paipaike', 'xt_ajax_item_get_paipaike');
add_action('wp_ajax_nopriv_xt_ajax_item_get_paipaike', 'xt_ajax_item_get_paipaike');

function xt_ajax_user_card_get() {
    xt_load_template('xt-user_card.php');
    exit();
}

add_action('wp_ajax_xt_ajax_user_card_get', 'xt_ajax_user_card_get');
add_action('wp_ajax_nopriv_xt_ajax_user_card_get', 'xt_ajax_user_card_get');

function xt_ajax_share_catalog_box() {
    xt_load_template('xt-share_catalog.php');
    exit();
}

add_action('wp_ajax_xt_ajax_share_catalog_box', 'xt_ajax_share_catalog_box');

function xt_ajax_admin_share_catalog() {
    $share_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $cids = isset($_POST['cids']) ? $_POST['cids'] : array();
    if ($share_id > 0) {
        xt_delete_share_catalog(0, $share_id); //share AND catalog
        if (!empty($cids)) {
            foreach ($cids as $cid) {
                xt_new_share_catalog(array(
                    'id' => $share_id,
                    'cid' => $cid
                ));
            }
        }
    }
}

add_action('wp_ajax_xt_ajax_admin_share_catalog', 'xt_ajax_admin_share_catalog');

function xt_ajax_share_box() {
    xt_load_template('xt-share_box.php');
    exit();
}

add_action('wp_ajax_xt_ajax_share_box', 'xt_ajax_share_box');

function xt_ajax_share_fetch() {
    $url = $_POST['url'];
    exit(json_encode(xt_share_fetch($url)));
}

add_action('wp_ajax_xt_ajax_share_fetch', 'xt_ajax_share_fetch');

function xt_ajax_share_add() {
    //第一步:新增分享
    $xt_share_param = $_POST;
    //第二步:加入专辑
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['share_key'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要分享的对象';
    }
    if (!isset($_POST['user_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定评论人';
    }

    if (!isset($_POST['album_title'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定专辑';
    }
    if (!isset($_POST['title'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定宝贝名称';
    }
    if (!isset($_POST['from_type'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定分享宝贝来源';
    }
    if (!isset($_POST['data_type'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定分享宝贝类型';
    }

    $user = wp_get_current_user();
    if ($user->exists()) {
        global $wpdb;
        if ($user->ID != absint($_POST['user_id'])) {
            $result['code'] = 500;
            $result['msg'] = '用户不一致';
        }
        if (empty($user->display_name))
            $user->display_name = $user->user_login;
        $user_name = $wpdb->escape($user->display_name);
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }

    if ($result['code'] == 0) {
        $result['result'] = xt_share_share($_POST);
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_share_add', 'xt_ajax_share_add');

function xt_ajax_search_albums() {
    global $xt;
    $xt->is_albums = false;
    $xt_album_param = $_GET;
    if (isset($xt_album_param['s_index'])) {
        $xt->is_albums = true;
        if ($xt_album_param['page'] > $xt_album_param['s_index'] + 4) {
            exit('no more');
        }
    }
    unset($xt_album_param['action']);
    query_albums($xt_album_param);
    $_isScroll = isset($xt_album_param['isScroll']) && "false" == $xt_album_param['isScroll'] ? false : true;
    $_isCatalog = isset($xt_album_param['isCatalog']) && "false" == $xt_album_param['isCatalog'] ? false : true;
    get_the_album_container($xt_album_param, $_isCatalog, true, $_isScroll);
    exit();
}

add_action('wp_ajax_xt_ajax_search_albums', 'xt_ajax_search_albums');
add_action('wp_ajax_nopriv_xt_ajax_search_albums', 'xt_ajax_search_albums');

function xt_ajax_search_shares() {
    global $xt;
    $xt->is_shares = false;
    $xt_share_param = $_GET;
    if (isset($xt_share_param['s_index'])) {
        $xt->is_shares = true;
        if ($xt_share_param['page'] > ($xt_share_param['s_index'] + 4)) {
            exit('no more');
        }
    }
    unset($xt_share_param['action']);
    if (isset($xt_share_param['isHome']) && $xt_share_param['isHome']) {
        global $xt_pageuser_follows;
        $xt_pageuser_follows = get_user_meta(intval($xt_share_param['user_id']), XT_USER_FOLLOW, true);
        if (empty($xt_pageuser_follows)) {
            query_shares(array_merge(array('no_found_rows' => 1), $xt_share_param));
        } else {
            query_shares($xt_share_param);
        }
    } else {
        query_shares($xt_share_param);
    }

    $_isScroll = isset($xt_share_param['isScroll']) && "false" == $xt_share_param['isScroll'] ? false : true;
    $_isCatalog = isset($xt_share_param['isCatalog']) && "false" == $xt_share_param['isCatalog'] ? false : true;

    get_the_share_container($xt_share_param, false, true, $_isScroll);
    exit();
}

add_action('wp_ajax_xt_ajax_search_shares', 'xt_ajax_search_shares');
add_action('wp_ajax_nopriv_xt_ajax_search_shares', 'xt_ajax_search_shares');

function xt_ajax_share_comments() {
    xt_load_template('xt-share_comments');
    exit();
}

add_action('wp_ajax_xt_ajax_share_comments', 'xt_ajax_share_comments');
add_action('wp_ajax_nopriv_xt_ajax_share_comments', 'xt_ajax_share_comments');

function xt_ajax_share_add_comment() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['share_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要评论的对象';
    }
    if (!isset($_POST['user_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定评论人';
    }
    // If the user is logged in
    $user = wp_get_current_user();
    if ($user->exists()) {
        global $wpdb;
        if ($user->ID != absint($_POST['user_id'])) {
            $result['code'] = 500;
            $result['msg'] = '用户不一致';
        }
        if (empty($user->display_name))
            $user->display_name = $user->user_login;
        $user_name = $wpdb->escape($user->display_name);
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    if ($result['code'] == 0) {
        $share_id = absint($_POST['share_id']);
        $user_id = absint($_POST['user_id']);
        $content = (isset($_POST['content'])) ? trim(strip_tags($_POST['content'])) : null;

        $commentdata = compact('share_id', 'user_name', 'user_id', 'content');
        $comment_id = xt_new_comment($commentdata);
        $result['result'] = $comment_id;
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_share_add_comment', 'xt_ajax_share_add_comment');

function xt_ajax_favorite_add() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要喜欢的对象';
    }
    if (!isset($_POST['user_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定用户';
    }
    if (!isset($_POST['type'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定喜欢的类型';
    }
    // If the user is logged in
    $user = wp_get_current_user();
    if ($user->exists()) {
        global $wpdb;
        if ($user->ID != absint($_POST['user_id'])) {
            $result['code'] = 500;
            $result['msg'] = '用户不一致';
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    if ($result['code'] == 0) {
        $id = absint($_POST['id']);
        $user_id = absint($_POST['user_id']);
        $type = absint($_POST['type']);
        $fav = xt_get_favorite($id, $user_id, $type);
        if (empty($fav)) {
            $favoritedata = compact('id', 'user_id', 'type');
            $count = xt_new_favorite($favoritedata);
            $result['result'] = $count;
        } else {
            $result['code'] = 1000;
            $result['msg'] = '喜欢过了';
        }
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_favorite_add', 'xt_ajax_favorite_add');

function xt_ajax_favorite_delete() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要删除喜欢的对象';
    }
    if (!isset($_POST['user_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定用户';
    }
    if (!isset($_POST['type'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要删除喜欢的类型';
    }
    // If the user is logged in
    $user = wp_get_current_user();
    if ($user->exists()) {
        global $wpdb;
        if ($user->ID != absint($_POST['user_id'])) {
            $result['code'] = 500;
            $result['msg'] = '用户不一致';
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    if ($result['code'] == 0) {
        $id = absint($_POST['id']);
        $user_id = absint($_POST['user_id']);
        $type = absint($_POST['type']);
        $favoritedata = compact('id', 'user_id', 'type');
        $count = xt_delete_favorite($id, $user_id, $type);
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_favorite_delete', 'xt_ajax_favorite_delete');

function xt_ajax_share_album_delete() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要删除喜欢的对象';
    }
    if (!isset($_POST['user_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定用户';
    }
    if (!isset($_POST['album_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要删除宝贝所属的专辑';
    }
    // If the user is logged in
    $user = wp_get_current_user();
    if ($user->exists()) {
        if ($user->ID != absint($_POST['user_id'])) {
            $result['code'] = 500;
            $result['msg'] = '用户不一致';
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    if ($result['code'] == 0) {
        $id = absint($_POST['id']);
        $user_id = absint($_POST['user_id']);
        $album_id = absint($_POST['album_id']);
        $count = xt_delete_share_album($id, $album_id, $user_id);
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_share_album_delete', 'xt_ajax_share_album_delete');

function xt_ajax_album_popup() {
    // If the user is logged in
    $user = wp_get_current_user();
    if ($user->exists()) {
        global $wpdb;
        if ($user->ID != absint($_POST['user_id'])) {
            exit('用户不一致');
        }
    } else {
        exit('未登录');
    }
    $result['result'] = exit(xt_get_album_popup($user->ID, $_POST['id'], $_POST['pic'], $_POST['title']));
}

add_action('wp_ajax_xt_ajax_album_popup', 'xt_ajax_album_popup');

function xt_ajax_album_add() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['title'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定专辑名称';
    }
    if (!isset($_POST['user_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定用户';
    }
    // If the user is logged in
    $user = wp_get_current_user();
    if ($user->exists()) {
        global $wpdb;
        if ($user->ID != absint($_POST['user_id'])) {
            $result['code'] = 500;
            $result['msg'] = '用户不一致';
        }
        if (empty($user->display_name))
            $user->display_name = $user->user_login;
        $user_name = $wpdb->escape($user->display_name);
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    if ($result['code'] == 0) {
        $title = strip_tags($_POST['title']);
        $user_id = absint($_POST['user_id']);
        $content = strip_tags($_POST['content']);
        $album = xt_get_album(0, $title);
        if (empty($album)) {
            $albumdata = compact('title', 'user_id', 'user_name', 'content');
            $album = xt_new_album($albumdata);
            $result['result'] = $album;
        } else {
            $result['code'] = 2000;
            $result['msg'] = '专辑名称已存在';
        }
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_album_add', 'xt_ajax_album_add');

function xt_ajax_album_join() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['album_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定专辑';
    }
    if (!isset($_POST['user_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定用户';
    }
    if (!isset($_POST['share_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定分享';
    }
    // If the user is logged in
    $user = wp_get_current_user();
    if ($user->exists()) {
        global $wpdb;
        if ($user->ID != absint($_POST['user_id'])) {
            $result['code'] = 500;
            $result['msg'] = '用户不一致';
        }
        if (empty($user->display_name))
            $user->display_name = $user->user_login;
        $user_name = $wpdb->escape($user->display_name);
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    if ($result['code'] == 0) {
        $album_id = absint($_POST['album_id']);
        $share_id = $id = absint($_POST['share_id']);
        $user_id = absint($_POST['user_id']);
        $content = ($_POST['content']);
        $album = array();
        if ($album_id == 0) {
            $title = $user_name . '的分享';
            $albumdata = compact('title', 'user_id', 'user_name');
            $album_id = xt_new_album($albumdata);
        } else {
            $album = xt_get_share_album($id, $album_id, $user_id);
        }
        if (empty($album)) {
            $albumdata = compact('id', 'album_id', 'user_id', 'user_name');
            $album = xt_new_share_album($albumdata);
            $result['result'] = $album;
            if (!empty($content)) {
                $commentdata = compact('share_id', 'user_name', 'user_id', 'content');
                $comment_id = xt_new_comment($commentdata);
            }
        } else {
            $result['code'] = 2001;
            $result['msg'] = '已经被该专辑收藏过啦!';
        }
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_album_join', 'xt_ajax_album_join');

function xt_ajax_album_update() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['album_id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定专辑';
    }
    if (!isset($_POST['title'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定专辑名称';
    }
    $album_id = absint($_POST['album_id']);
    $title = strip_tags($_POST['title']);
    $content = strip_tags($_POST['content']);

    $user = wp_get_current_user();
    if ($user->exists()) {
        $album = xt_get_album($album_id);
        if (empty($album)) {
            $result['code'] = 500;
            $result['msg'] = '当前指定的专辑不存在';
        } else {
            if ($album->user_id != $user->ID) {
                $result['code'] = 500;
                $result['msg'] = '您无权修改此专辑';
            }
            if ($album->title != $title) {
                $title_album = xt_get_album(0, $title);
                if (!empty($title_album)) {
                    $result['code'] = 2000;
                    $result['msg'] = '专辑名称已存在';
                }
            }
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    if ($result['code'] == 0) {
        global $wpdb;
        $wpdb->update(XT_TABLE_ALBUM, array(
            'title' => $title,
            'content' => $content
                ), array(
            'id' => $album_id
        ));
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_album_update', 'xt_ajax_album_update');

function xt_ajax_account_right() {
    $user = wp_get_current_user();
    if ($user->exists()) {
        if (isset($_GET['module']) && !empty($_GET['module'])) {
            xt_load_template('account/' . $_GET['module'] . '.php');
        }
    } else {
        exit('您尚未登录');
    }
    exit();
}

add_action('wp_ajax_xt_ajax_account_right', 'xt_ajax_account_right');

function xt_ajax_account_profile_update() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    $user = wp_get_current_user();
    if ($user->exists()) {
        global $wpdb;
        $user_id = $user->ID;
        check_admin_referer('update-user_' . $user_id);
        do_action('edit_user_profile_update', $user_id);
        $errors = edit_user($user_id);

        if (!is_wp_error($errors)) {
            exit(json_encode($result));
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_account_profile_update', 'xt_ajax_account_profile_update');

function xt_ajax_account_orders() {
    $user = wp_get_current_user();
    if ($user->exists()) {
        if (!isset($_POST['platform']) || empty($_POST['platform'])) {
            exit('未指定订单类型');
        }
        xt_load_template('account/xt-orders-' . $_POST['platform'] . '.php');
    } else {
        exit('您尚未登录');
    }
    exit();
}

add_action('wp_ajax_xt_ajax_account_orders', 'xt_ajax_account_orders');

function xt_ajax_account_tuiguang() {
    $user = wp_get_current_user();
    if ($user->exists()) {
        if (!isset($_POST['type']) || empty($_POST['type'])) {
            exit('未指定推广类型');
        }
        xt_load_template('account/xt-tuiguang-' . $_POST['type'] . '.php');
    } else {
        exit('您尚未登录');
    }
    exit();
}

add_action('wp_ajax_xt_ajax_account_tuiguang', 'xt_ajax_account_tuiguang');

function xt_ajax_account_unbind() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    $user = wp_get_current_user();
    if ($user->exists()) {
        if (isset($_POST['type']) && in_array($_POST['type'], array(
                    'sina',
                    'taobao',
                    'qq'
                ))) {
            switch ($_POST['type']) {
                case 'sina' :
                    delete_user_meta($user->ID, XT_WEIBO_KEY);
                    delete_user_meta($user->ID, XT_WEIBO_TOKEN);
                    delete_user_meta($user->ID, XT_WEIBO);
                    delete_user_meta($user->ID, XT_WEIBO_SETTING);
                    break;
                case 'taobao' :
                    delete_user_meta($user->ID, XT_TAOBAO_KEY);
                    delete_user_meta($user->ID, XT_TAOBAO_TOKEN);
                    delete_user_meta($user->ID, XT_TAOBAO);
                    delete_user_meta($user->ID, XT_TAOBAO_SETTING);
                    break;
                case 'qq' :
                    delete_user_meta($user->ID, XT_QQ_KEY);
                    delete_user_meta($user->ID, XT_QQ_TOKEN);
                    delete_user_meta($user->ID, XT_QQ);
                    delete_user_meta($user->ID, XT_QQ_SETTING);
                    break;
            }
        } else {
            $result['code'] = 500;
            $result['msg'] = '未指定要解绑的平台';
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_account_unbind', 'xt_ajax_account_unbind');

function xt_ajax_account_bind_setting() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    $user = wp_get_current_user();
    if ($user->exists()) {
        if (isset($_POST['type']) && in_array($_POST['type'], array(
                    'sina',
                    'taobao',
                    'qq'
                ))) {
            if (isset($_POST['setting']) && !empty($_POST['setting'])) {
                $settings = json_decode(stripcslashes($_POST['setting']), true);
                if (!empty($settings)) {
                    switch ($_POST['type']) {
                        case 'sina' :
                            update_user_meta($user->ID, XT_WEIBO_SETTING, $settings);
                            break;
                        case 'taobao' :
                            update_user_meta($user->ID, XT_TAOBAO_SETTING, $settings);
                            break;
                        case 'qq' :
                            update_user_meta($user->ID, XT_QQ_SETTING, $settings);
                            break;
                    }
                }
            }
        } else {
            $result['code'] = 500;
            $result['msg'] = '未指定要解绑的平台';
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_account_bind_setting', 'xt_ajax_account_bind_setting');

function xt_ajax_follow() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    $user = wp_get_current_user();
    if ($user->exists()) {
        if (isset($_POST['user_id']) && isset($_POST['f_user_id'])) {
            if ($user->ID == $_POST['f_user_id']) {
                global $wpdb;
                $follow_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . XT_TABLE_USER_FOLLOW . " WHERE f_user_id=%d", $user->ID));
                if ($follow_count >= xt_followlimit()) {
                    $result['code'] = 500;
                    $result['msg'] = '您已达到关注限额(' . xt_followlimit() . ')';
                } else {
                    $result['result'] = xt_follow_by_id($_POST['user_id'], $_POST['f_user_id']);
                }
            } else {
                $result['code'] = 500;
                $result['msg'] = '您无权限操作此用户';
            }
        } else {
            $result['code'] = 500;
            $result['msg'] = '未指定要关注的对象';
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_follow', 'xt_ajax_follow');

function xt_ajax_unfollow() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    $user = wp_get_current_user();
    if ($user->exists()) {
        if (isset($_POST['user_id']) && isset($_POST['f_user_id'])) {
            if ($user->ID == $_POST['f_user_id']) {
                xt_unfollow_by_id($_POST['user_id'], $_POST['f_user_id']);
            } else {
                $result['code'] = 500;
                $result['msg'] = '您无权限操作此用户';
            }
        } else {
            $result['code'] = 500;
            $result['msg'] = '未指定要关注的对象';
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_unfollow', 'xt_ajax_unfollow');

function xt_ajax_search_users() {
    global $xt_user_follow;
    $xt_user_param = $_GET;
    if (isset($xt_user_param['s_index']) && $xt_user_param['page'] > $xt_user_param['s_index'] + 4) {
        exit('no more');
    }
    unset($xt_user_param['action']);
    query_users($xt_user_param);
    $_isScroll = isset($xt_user_param['isScroll']) && "false" == $xt_user_param['isScroll'] ? false : true;
    $_isCatalog = isset($xt_user_param['isCatalog']) && "false" == $xt_user_param['isCatalog'] ? false : true;

    get_the_user_container($xt_user_param, $_isCatalog, true, $_isScroll);
    exit();
}

add_action('wp_ajax_xt_ajax_search_users', 'xt_ajax_search_users');
add_action('wp_ajax_nopriv_xt_ajax_search_users', 'xt_ajax_search_users');

function xt_ajax_tixian() {
    xt_load_template('account/xt-tixian.php');
    exit();
}

add_action('wp_ajax_xt_ajax_tixian', 'xt_ajax_tixian');

function xt_ajax_tixian_save() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!xt_is_fanxian()) {
        $result['code'] = 500;
        $result['msg'] = '已关闭返利通道';
        exit(json_encode($result));
    }
    $_amount = $_POST['amount'];
    if (!isset($_POST['type']) || empty($_POST['type'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要提现的类型';
        exit(json_encode($result));
    }
    $_type = $_POST['type'];
    if (!in_array($_type, array(
                'cash',
                'jifenbao'
            ))) {
        $result['code'] = 500;
        $result['msg'] = '指定的提现类型不正确';
        exit(json_encode($result));
    }
    if (empty($_amount)) {
        $result['code'] = 500;
        $result['msg'] = '未填写提现数额';
    }
    if (!is_numeric($_amount)) {
        $result['code'] = 500;
        $result['msg'] = '提现数额格式不正确';
    } else {
        $cashback = (int) xt_fanxian_cashback();
        if ($_type == 'jifenbao') {
            $cashback = $cashback * 100;
        }
        if ($_amount < $cashback) {
            $result['code'] = 500;
            $result['msg'] = '提现数额必须大于:' . $cashback;
        }
    }
    if ($result['code'] == 0) {
        $user = wp_get_current_user();
        if ($user->exists()) {
            $_fanxian = 0;
            $_tixian = 0;
            $_cash = 0;
            if ($_type == 'cash') {
                $_fanxian = xt_user_total_fanxian($user->ID);
                $_tixians = xt_total_tixian($user->ID);
                $_tixian = $_tixians[0] + $_tixians[1]; //未审核,已完成
                $_cash = $_fanxian - $_tixian; //余额                
            } else {
                $_fanxian = xt_user_total_jifen($user->ID);
                $_tixians = xt_total_tixian_jifen($user->ID);
                $_tixian = $_tixians[0] + $_tixians[1]; //未审核,已完成
                $_cash = $_fanxian - $_tixian; //余额                
            }

            if ($_amount > $_cash) {
                $result['code'] = 500;
                $result['msg'] = '提现数额超过余额';
            } else {
                $__cash = $__jifen = 0;
                if ($_type == 'jifenbao') {
                    $__jifen = $_amount;
                } else {
                    $__cash = $_amount;
                }
                xt_new_tixian(array(
                    'user_id' => $user->ID,
                    'cash' => $__cash,
                    'jifen' => $__jifen
                ));
            }
        } else {
            $result['code'] = 500;
            $result['msg'] = '未登录';
        }
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_tixian_save', 'xt_ajax_tixian_save');

function xt_ajax_account_jifen() {
    $user = wp_get_current_user();
    if ($user->exists()) {
        xt_load_template('account/xt-jifen-list.php');
    } else {
        exit('您尚未登录');
    }
    exit();
}

add_action('wp_ajax_xt_ajax_account_jifen', 'xt_ajax_account_jifen');

function xt_ajax_account_jifen_exchange() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要兑换的商品';
        exit(json_encode($result));
    }
    $user = wp_get_current_user();
    if ($user->exists()) {
        $jifen = xt_user_total_jifen($user->ID);
        $jifenOrder = $jifen > 0 ? xt_user_total_jifen_order($user->ID) : array(
            0,
            0,
            0
                );
        global $wpdb;
        $item = $wpdb->get_row('SELECT * FROM ' . XT_TABLE_USER_JIFEN_ITEM . ' WHERE id=' . (int) $_POST['id']);
        if (empty($item)) {
            $result['code'] = 500;
            $result['msg'] = '您要兑换的商品不存在';
        }
        if ($item->stock - $item->buy_count <= 0) {
            $result['code'] = 500;
            $result['msg'] = '库存不足,无法兑换';
        }
        if ($item->jifen > ($jifen - $jifenOrder[0] - $jifenOrder[1])) {
            $result['code'] = 500;
            $result['msg'] = xt_jifenbao_text() . '不足,无法兑换';
        }
        $count = $wpdb->get_var('SELECT COUNT(*) FROM ' . XT_TABLE_USER_JIFEN_ORDER . ' WHERE item_id=' . $item->id . ' AND user_id=' . $user->ID);
        if ($count >= $item->user_count) {
            $result['code'] = 500;
            $result['msg'] = '超出每人限兑数量';
        }
        if ($result['code'] == 0) {
            if ($wpdb->insert(XT_TABLE_USER_JIFEN_ORDER, array(
                        'item_id' => $item->id,
                        'num' => 1,
                        'jifen' => $item->jifen,
                        'status' => 0,
                        'create_time' => current_time('mysql'),
                        'user_id' => $user->ID,
                        'user_name' => $user->user_login
                    ))) {
                $wpdb->update(XT_TABLE_USER_JIFEN_ITEM, array(
                    'buy_count' => $item->buy_count + 1
                        ), array(
                    'id' => $item->id
                ));
            }
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_account_jifen_exchange', 'xt_ajax_account_jifen_exchange');

function xt_ajax_unorder() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要找回的订单';
        exit(json_encode($result));
    }
    if (!isset($_POST['platform']) || empty($_POST['platform'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要找回的订单的类型';
        exit(json_encode($result));
    }
    if (!isset($_POST['tradeId']) || empty($_POST['tradeId'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定要找回的订单的订单号';
        exit(json_encode($result));
    }
    $table = '';
    $field_tradeId = '';
    $field_commission = '';
    $field_payTime = '';
    $field_outer = 'outInfo';
    switch ($_POST['platform']) {
        case 'taobao' :
            $table = XT_TABLE_TAOBAO_REPORT;
            $field_tradeId = 'trade_id';
            $field_commission = 'commission';
            $field_payTime = 'pay_time';
            $field_outer = 'outer_code';
            break;
        case 'paipai' :
            $table = XT_TABLE_PAIPAI_REPORT;
            $field_tradeId = 'dealId';
            $field_commission = 'brokeragePrice';
            $field_payTime = 'chargeTime';
            $field_outer = 'outInfo';
            break;
        case 'yiqifa' :
            $table = XT_TABLE_YIQIFA_REPORT;
            $field_tradeId = 'orderNo';
            $field_commission = 'commission';
            $field_payTime = 'orderTime';
            $field_outer = 'outerCode';
            break;
    }
    if (empty($table)) {
        $result['code'] = 500;
        $result['msg'] = '订单类型不正确';
        exit(json_encode($result));
    }
    $user = wp_get_current_user();
    if ($user->exists()) {
        global $wpdb;
        $order = $wpdb->get_row('SELECT * FROM ' . $table . ' WHERE id=' . intval($_POST['id']));
        if (empty($order)) {
            $result['code'] = 500;
            $result['msg'] = '未找到指定的订单';
            exit(json_encode($result));
        }
        if ($order->user_id > 0) {
            $result['code'] = 500;
            $result['msg'] = '该订单已被[' . $order->user_name . ']找回';
            exit(json_encode($result));
        }
        if ($order->$field_tradeId == $_POST['tradeId']) {
            if ($wpdb->update($table, array(
                        'user_id' => $user->ID,
                        'user_name' => $user->user_login
                            ), array(
                        'id' => intval($_POST['id'])
                    ))) {
                $needFanxian = false;
                $commission = $order->$field_commission;

                if ($_POST['platform'] == 'paipai') {
                    $commission = round($commission / 100, 2);
                    $needFanxian = $order->bargainState == 0 && xt_is_fanxian();
                } elseif ($_POST['platform'] == 'yiqifa') {
                    $needFanxian = $order->orderStatus == 'A' && xt_is_fanxian();
                } elseif ($_POST['platform'] == 'taobao') {
                    $needFanxian = true;
                }
                if ($needFanxian) {
                    if (!empty($order->$field_outer) && preg_match("/^([a-zA-Z]{4})8$/", str_replace(XT_FANXIAN_PRE, '', $order->$field_outer), $guids)) {
                        $users = xt_report_fanxian_member($_POST['platform'], $order->$field_outer . xt_user_guid($user->ID));
                        $buyer = $users['buyer'];
                        $sharer = $users['sharer'];
                        $adser = $users['adser'];
                        xt_report_fanxian_save($_POST['platform'], $buyer, $sharer, $adser, $_POST['tradeId'], $commission, $order->$field_payTime);
                    } else {
                        $users = xt_report_fanxian_member($_POST['platform'], XT_FANXIAN_PRE . $user->ID);
                        $buyer = $users['buyer'];
                        $sharer = $users['sharer'];
                        $adser = $users['adser'];
                        xt_report_fanxian_save($_POST['platform'], $buyer, $sharer, $adser, $_POST['tradeId'], $commission, $order->$field_payTime);
                    }
                }
            }
        } else {
            $result['code'] = 500;
            $result['msg'] = '订单号不正确';
        }
    } else {
        $result['code'] = 500;
        $result['msg'] = '未登录';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_unorder', 'xt_ajax_unorder');