<?php

function xt_share_fetch($url, $user_id = 0, $user_name = '') {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if ($user_id == 0) {
        $user = wp_get_current_user();
        if ($user->exists()) {
            global $wpdb;
            $user_id = $user->ID;
            if (empty($user->display_name))
                $user->display_name = $user->user_login;
            $user_name = $wpdb->escape($user->display_name);
        } else {
            $result['code'] = 500;
            $result['msg'] = '未登录或未指定要分享的会员';
            return $result;
        }
    }
    $rs = preg_match("/^(http:\/\/|https:\/\/)/", $url, $match);
    if (intval($rs) == 0) {
        $url = "http://" . $url;
    }
    $rs = parse_url($url);

    $scheme = isset($rs['scheme']) ? $rs['scheme'] . "://" : "http://";
    $host = isset($rs['host']) ? $rs['host'] : "none";
    $host = explode('.', $host);
    $host = array_slice($host, -2, 2);
    $domain = implode('.', $host);
    switch ($domain) {
        case 'taobao.com' :
        case 'tmall.com' :
            return (xt_share_fetch_taobao($url, $user_id, $user_name, $result));
            break;
        case 'paipai.com' :
            return (xt_share_fetch_paipai($url, $user_id, $user_name, $result));
            break;
        default:
            $result['code'] = 500;
            $result['msg'] = '暂不支持该商家的商品分享';
            return $result;
            break;
    }
}

function xt_share_fetch_taobao($url, $user_id, $user_name, $result) {
    $app = xt_get_app_taobao();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        $result['code'] = 501;
        $result['msg'] = '暂不支持淘宝';
        return $result;
    }
    $id = _xt_share_fetch_taobao_id($url);
    if ($id == 0) {
        $result['code'] = 501;
        $result['msg'] = '宝贝的地址不正确';
        return $result;
    }

    $key = 'tb_' . $id;
    //检查是否已分享该商品
    if ($user_id > 0) {
        if (xt_check_share($key, $user_id)) {
            $result['code'] = 502;
            $result['msg'] = '您已分享该宝贝';
            return $result;
        }
    }

    $result = array();
    $goods = xt_taobao_item($id);
    if (is_wp_error($goods)) {
        $result['code'] = 500;
        $result['msg'] = $goods->get_error_message();
        return $result;
    }
    $goods = (array) $goods;
    if (empty($goods['detail_url']) || empty($goods['pic_url'])) {
        $result['code'] = 501;
        $result['msg'] = '获取淘宝商品信息失败，请重试';
        return $result;
    }
    //$goods['commission'] = 0;
    $result['code'] = 0;
    $result['msg'] = '';
    $result['result']['share_key'] = $key;
    $result['result']['user_id'] = $user_id;
    $result['result']['user_name'] = $user_name;
    $result['result']['cid'] = $goods['cid'];
    $result['result']['title'] = $goods['title'];
    $result['result']['price'] = $goods['price'];
    $result['result']['pic_url'] = $goods['pic_url'];
    $result['result']['url'] = $goods['detail_url'];
    $result['result']['location'] = isset($goods['location']) ? ($goods['location']->state . ' ' . $goods['location']->city) : '';
    $result['result']['nick'] = $goods['nick'];
    $result['result']['cat'] = $goods['cid'];

    $result['result']['from_type'] = 'taobao';
    $result['result']['data_type'] = 1;

    $result['result']['cache_data'] = serialize(array(
        'item' => $goods,
        'comment' => array(
            'total' => 0,
            'comments' => array()
        )
            ));
    return $result;
}

function _xt_share_fetch_taobao_id($url) {
    $id = 0;
    $parse = parse_url($url);
    if (isset($parse['query'])) {
        parse_str($parse['query'], $params);
        if (isset($params['id']))
            $id = $params['id'];
        elseif (isset($params['item_id']))
            $id = $params['item_id'];
        elseif (isset($params['default_item_id']))
            $id = $params['default_item_id'];
    }
    return $id;
}

function xt_share_fetch_paipai($url, $user_id, $user_name, $result) {
    $app = xt_get_app_paipai();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        $result['code'] = 501;
        $result['msg'] = '暂不支持拍拍';
        return $result;
    }
    $id = _xt_share_fetch_paipai_id($url);
    if (!$id) {
        $result['code'] = 501;
        $result['msg'] = '宝贝的地址不正确';
        return $result;
    }
    $key = 'pp_' . $id;
    //检查是否已分享该商品
    if ($user_id > 0) {
        if (xt_check_share($key, $user_id)) {
            $result['code'] = 502;
            $result['msg'] = '您已分享该宝贝';
            return $result;
        }
    }
    $goods = xt_paipai_item($id);
    if (is_wp_error($goods)) {
        $result['code'] = 500;
        $result['msg'] = $goods->get_error_message();
        return $result;
    }
    $goods = (array) $goods;
    $result = array();
    //$goods['commission'] = 0;
    $result['code'] = 0;
    $result['msg'] = '';
    $result['result']['share_key'] = $key;
    $result['result']['user_id'] = $user_id;
    $result['result']['user_name'] = $user_name;
    $result['result']['cid'] = $goods['classId'];
    $result['result']['title'] = $goods['itemName'];
    $result['result']['price'] = number_format($goods['itemPrice'] / 100, 2);
    $result['result']['pic_url'] = $goods['picLink'];
    $result['result']['location'] = $goods['regionInfo'];
    $result['result']['nick'] = $goods['sellerName'];
    $result['result']['cat'] = $goods['classId'];

//    $resp = xt_paipaike_item($id);
//    if (!is_wp_error($resp)) {
//        $taoke = (array) $resp->cpsSearchCommData;
//        if ($taoke['dwIsCpsFlag'] && $taoke['dwActiveFlag']) {
//            if ($taoke['dwPrimaryCmm']) {
//                $goods['commission'] = number_format($taoke['dwPrice'] * $taoke['dwPrimaryRate'] / (10000 * 100), 2);
//            } else {
//                $goods['commission'] = number_format($taoke['dwPrice'] * $taoke['dwClassRate'] / (10000 * 100), 2);
//            }
//        }
//    }

    $result['result']['from_type'] = 'paipai';
    $result['result']['data_type'] = 1;

    $result['result']['cache_data'] = serialize(array(
        'item' => _xt_share_fetch_paipai_convert($goods),
        'comment' => array(
            'total' => 0,
            'comments' => array()
        )
            ));
    return $result;
}

function _xt_share_fetch_paipai_id($url) {
    $id = 0;
    $parse = parse_url($url);
    if (isset($parse['path'])) {
        $parse = explode('/', $parse['path']);
        $parse = end($parse);
        $parse = explode('-', $parse);
        $parse = current($parse);
        $parse = explode('.', $parse);
        $id = current($parse);
    }
    return $id;
}

function _xt_share_fetch_paipai_convert($paipai) {
    return array(
        'num_iid' => $paipai['itemCode'],
        'detail_url' => 'http://auction1.paipai.com/' . $paipai['itemCode'],
        'title' => $paipai['itemName'],
        'nick' => $paipai['sellerName'],
        'pic_url' => $paipai['picLink'],
        'price' => number_format($paipai['itemPrice'] / 100, 2),
        'qq' => $paipai['sellerUin'],
        'cid' => $paipai['classId']
    );
}