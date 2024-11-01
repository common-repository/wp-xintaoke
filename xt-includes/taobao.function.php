<?php

function xt_is_clickurl($url) {
    $rs = parse_url($url);
    $host = isset($rs['host']) ? $rs['host'] : "none";
    if ($host == 's.click.taobao.com') {
        return true;
    }
    return false;
}

function xt_taobao_detail($num_iid) {
    return xt_site_url('taobao-' . $num_iid);
}

function xt_taobao_refreshtoken() {
    $app = xt_taobao_is_session_ready();
    if ($app) {
        if (isset($app['token']['refresh_token']) && !empty($app['token']['refresh_token'])) {
            //请求参数
            $postfields = array(
                'grant_type' => 'refresh_token',
                'client_id' => $app['appKey'],
                'client_secret' => $app['appSecret'],
                'refresh_token' => $app['token']['refresh_token']
            );
            include_once XT_PLUGIN_DIR . ('/xt-core/sdks/taobao/TopClient.php');
            $client = new TopClient;
            try {
                $token = json_decode($client->curl(XT_TAOBAO_TOKEN_URL, $postfields), true);
                $token['expires_in_date'] = date('Y-m-d H:i:s', (current_time('timestamp') + ($token['expires_in'])));
            } catch (Exception $e) {
                wp_die($e->getMessage());
            }
            if (isset($token['expires_in']) && !empty($token['expires_in']) && isset($token['re_expires_in']) && !empty($token['re_expires_in'])) {
                $app['token'] = $token;
                $option_platform = get_option(XT_OPTION_PLATFORM);
                $option_platform['taobao'] = $app;
                update_option(XT_OPTION_PLATFORM, $option_platform);
            }
        }
    }
}

function xt_taobao_is_ready() {
    $app = xt_get_app_taobao();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return false;
    }
    return $app;
}

function xt_taobao_is_session_ready() {
    $app = xt_get_app_taobao();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return false;
    }
    if (empty($app['token']) || !isset($app['token']['access_token']) || empty($app['token']['access_token'])) {
        return false;
    }
    return $app;
}

function xt_taobao_s8pid() {
    $app = xt_get_app_taobao();
    if (!empty($app) && isset($app['s8pid']) && !empty($app['s8pid'])) {
        return $app['s8pid'];
    }
    return '';
}

function xt_taobao_jssdk_cookie() {
    if (!is_admin()) {
        $app = xt_taobao_is_ready();
        if ($app) {
            $timestamp = time() . "000";
            $message = $app['appSecret'] . 'app_key' . $app['appKey'] . 'timestamp' . $timestamp . $app['appSecret'];
            $mysign = strtoupper(hash_hmac("md5", $message, $app['appSecret']));
            setcookie("timestamp", $timestamp);
            setcookie("sign", $mysign);
        }
    }
}

function xt_taobao_area() {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/AreasGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new AreasGetRequest;
    $req->setFields('id,type,name,parent_id,zip');
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }

    if (isset($resp->areas) && isset($resp->areas->area)) {
        return $resp->areas->area;
    }
    return array();
}

function xt_taobao_temai_item_cats($parentCid = XT_TAOBAO_CAT_TEMAI) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TmallTemaiSubcatsSearchRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TmallTemaiSubcatsSearchRequest;
    $req->setCat(absint($parentCid));
    $resp = $client->execute($req);

    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }

    if (isset($resp->cat_list) && isset($resp->cat_list->tmall_tm_cat)) {
        return $resp->cat_list->tmall_tm_cat;
    }
    return array();
}

function xt_taobao_item_cats($parentCid = 0, $cids = array()) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/ItemcatsGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new ItemcatsGetRequest;
    $req->setFields("cid,parent_cid,name,is_parent,status,sort_order");
    if (!empty($cids) && $parentCid === 0) {
        $req->setCids(implode(',', array_map("absint", $cids)));
    } else {
        $req->setParentCid(absint($parentCid));
    }

    $resp = $client->execute($req);

    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }

    if (isset($resp->item_cats) && isset($resp->item_cats->item_cat)) {
        return $resp->item_cats->item_cat;
    }
    return array();
}

function xt_taobao_item_cats_sync_root() {
    global $wpdb;
    $parents = xt_taobao_item_cats();
    if (is_wp_error($parents)) {
        return $parents;
    }
    foreach ($parents as $parent) {
        $wpdb->replace(XT_TABLE_TAOBAO_ITEMCAT, (array) $parent);
    }
}

function xt_taobao_item_cats_sync_child($parent_cid) {
    global $wpdb;
    $parents = xt_taobao_item_cats($parent_cid);
    if (!is_wp_error($parents) && !empty($parents)) {
        foreach ($parents as $parent) {
            $wpdb->replace(XT_TABLE_TAOBAO_ITEMCAT, (array) $parent);
            if ($parent->is_parent) {
                xt_taobao_item_cats_sync_child($parent->cid);
            }
        }
    }
}

function xt_taobao_topats_result_get($task_id) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    if (intval($task_id) == 0) {
        return new WP_Error('业务错误', '任务标识不正确!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TopatsResultGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TopatsResultGetRequest;
    $req->setTaskId($task_id);
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }

    return $resp->task;
}

function xt_taobao_topats_itemcats($cids = 0, $output_format = 'csv', $type = 1) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }

    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TopatsItemcatsGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TopatsItemcatsGetRequest;
    $req->setCids($cids);
    $req->setOutputFormat($output_format);
    $resp = $client->execute($req);

    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }

    return $resp->task;
}

function xt_taobao_item($num_iid, $fields = 'num_iid,detail_url,title,cid,nick,location,pic_url,price') {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    if (intval($num_iid) == 0) {
        return new WP_Error('业务错误', '淘宝商品标识不正确!');
    }

    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/ItemGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new ItemGetRequest;
    $req->setFields($fields);
    $req->setNumIid($num_iid);
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }
    return $resp->item;
}

function xt_taobao_tql($tql, $session = null) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $resp = $client->executeTql($tql, $session);
    return $resp;
}

function xt_taobaoke_item_covert($num_iids) {
    $num_iidss = array_chunk(explode(',', $num_iids), 10);
    $qls = '';
    foreach ($num_iidss as $num_iids) {
        $qls .= "{select cid,click_url,shop_click_url,seller_credit_score,title,nick,pic_url,delist_time,price,volume,num_iid,location from taobao.taobaoke.items.detail.get where num_iids =" . implode(',', $num_iids) . " }";
    }
    $resps = (xt_taobao_tql($qls));
    if (!is_array($resps)) {
        $resps = array(
            $resps
        );
    }
    $items = array();
    foreach ($resps as $resp) {
        if (isset($resp->taobaoke_item_details)) {
            $items = array_merge($items, _xt_convertDetailToTaobaokeItem($resp->taobaoke_item_details->taobaoke_item_detail));
        }
    }
    return $items;
}

function _xt_convertDetailToTaobaokeItem($details) {
    $items = array();
    if ($details != null && count($details) > 0) {
        foreach ($details as $detail) {
            $detailItem = $detail->item;
            $item = new StdClass;
            $item->click_url = $detail->click_url;
            $location = $detailItem->location;
            if (!empty($location))
                $item->item_location = $location->state . ' ' . $location->city;
            $item->nick = $detailItem->nick;
            $item->num_iid = $detailItem->num_iid;
            $item->pic_url = $detailItem->pic_url;
            $item->price = $detailItem->price;
            $item->seller_credit_score = $detail->seller_credit_score;
            $item->shop_click_url = $detail->shop_click_url;
            $item->title = $detailItem->title;
            $item->volume = $detailItem->volume;
            $items[] = $item;
        }
    }
    return $items;
}

function xt_taobaoke_report($date, $page = 1, $page_size = 40) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }

    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TaobaokeReportGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TaobaokeReportGetRequest;
    $req->setFields("trade_parent_id,trade_id,real_pay_fee,commission_rate,commission,app_key,outer_code,pay_time,pay_price,num_iid,item_title,item_num,category_id,category_name,shop_title,seller_nick");
    $req->setDate($date);
    $req->setPageNo($page);
    $req->setPageSize($page_size);
    $resp = $client->execute($req);

    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }
    if (isset($resp->taobaoke_report)) {
        return $resp->taobaoke_report;
    }
    return array();
}

function xt_taobaoke_listurl($q) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    if (empty($q)) {
        return new WP_Error('业务错误', '未指定搜索关键词!');
    }

    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TaobaokeListurlGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TaobaokeListurlGetRequest;
    $req->setQ($q);
    $req->setOuterCode(xt_outercode());
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }
    $listurl = '';
    if (isset($resp->taobaoke_item) && isset($resp->taobaoke_item->keyword_click_url)) {
        $listurl = $resp->taobaoke_item->keyword_click_url;
        $s8pid = xt_taobao_s8pid();
        if (!empty($s8pid)) {
            $listurl = preg_replace('/mm_[0-9]+_0_0/i', $s8pid, $listurl);
        }
    }

    return $listurl;
}

function xt_taobaoke_item($num_iids = '', $track_iids = '', $fields = 'click_url,shop_click_url,seller_credit_score,detail_url,num_iid,title,nick,auction_point,cid,pic_url,num,price,location,post_fee,express_fee,ems_fee,item_imgs') {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TaobaokeItemsDetailGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TaobaokeItemsDetailGetRequest;
    $req->setFields($fields);
    if (!empty($num_iids))
        $req->setNumIids($num_iids);
    elseif (!empty($track_iids))
        $req->setTrackIids($track_iids);
    else
        return new WP_Error('500', '必须指定num_iids或track_iids');
    $req->setOuterCode(xt_outercode());
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }

    $items = array();
    if (isset($resp->taobaoke_item_details)) {
        $items = $resp->taobaoke_item_details->taobaoke_item_detail;
    }
    return $items;
}

function xt_taobaoke_items_temai($cat = XT_TAOBAO_CAT_TEMAI, $start = 0, $sort = 's') {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TmallTemaiItemsSearchRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TmallTemaiItemsSearchRequest;
    $req->setCat($cat);
    $req->setStart($start);
    $req->setSort($sort);
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }

    return $resp;
}

function xt_taobaoke_items_relate($params) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    $params = _xt_taobaoke_items_relate_params($params);
    if (empty($params['relate_type'])) {
        return new WP_Error('业务错误', '参数不完整,必须指定关联推荐的类型!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TaobaokeItemsRelateGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TaobaokeItemsRelateGetRequest;
    $req->setFields($params['fields']);
    switch ($params['relate_type']) {
        case 1 : //同类商品推荐		
        case 2 : //异类商品推荐
        case 3 : //同店商品推荐
            $num_iid = intval($params['num_iid']);
            if ($num_iid == 0) {
                return new WP_Error('业务错误', '参数不正确,必须指定正确的商品ID!');
            }
            $req->setNumIid($num_iid);
            break;
        case 4 : //店铺热门推荐
            $seller_id = intval($params['seller_id']);
            if ($seller_id == 0) {
                return new WP_Error('业务错误', '参数不正确,必须指定正确的店铺ID!');
            }
            $req->setSellerId($seller_id);
            break;
        case 5 : //类目热门推荐
            $cid = intval($params['cid']);
            if ($cid == 0) {
                return new WP_Error('业务错误', '参数不正确,必须指定正确的分类ID!');
            }
            $req->setCid($cid);
            break;
        default :
            return new WP_Error('业务错误', '参数不正确,必须指定正确的推荐类型!');
    }
    $req->setShopType($params['shop_type']);
    $req->setSort($params['sort']);
    $req->setMaxCount($params['max_count']);
    if (!empty($params['track_iid'])) {
        $req->setTrackIid($params['track_iid']);
    }
    $req->setOuterCode(xt_outercode());
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }
    $items = $resp->taobaoke_items->taobaoke_item;
    return $items;
}

function _xt_taobaoke_items_relate_params($params = array()) {
    return array_merge(array(
                'fields' => 'num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location,volume',
                'relate_type' => '',
                'num_iid' => '',
                'seller_id' => '',
                'cid' => '',
                'shop_type' => 'all',
                'sort' => 'default',
                'max_count' => '',
                'track_iid' => ''
                    ), $params);
}

function xt_taobao_shopcats_list($fields = 'cid,parent_cid,name,is_parent') {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/ShopcatsListGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new ShopcatsListGetRequest;
    $req->setFields($fields);

    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }
    if (isset($resp->shop_cats) && isset($resp->shop_cats->shop_cat)) {
        return $resp->shop_cats->shop_cat;
    }
    return array();
}

function xt_taobaoke_shops_search($params) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    $params = _xt_taobaoke_shops_search_params($params);
    if (empty($params['keyword']) && (empty($params['cid']) || intval($params['cid']) == 0)) {
        return new WP_Error('业务错误', '参数不完整,必须指定搜索关键词或搜索分类!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TaobaokeShopsGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TaobaokeShopsGetRequest;
    $req->setFields($params['fields']);
    if (!empty($params['keyword']))
        $req->setKeyword($params['keyword']);
    if (!empty($params['cid']) && intval($params['cid']) > 0)
        $req->setCid(intval($params['cid']));

    if (!empty($params['start_credit']) && intval($params['start_credit']) > 0)
        $req->setStartCredit(xt_taobao_credit(absint($params['start_credit'])));
    if (!empty($params['end_credit']) && intval($params['end_credit']) > 0)
        $req->setEndCredit(xt_taobao_credit(absint($params['end_credit'])));

    if (!empty($params['start_commissionrate']) && intval($params['start_commissionrate']) > 0)
        $req->setStartCommissionrate($params['start_commissionrate']);
    if (!empty($params['end_commissionrate']) && intval($params['end_commissionrate']) > 0)
        $req->setEndCommissionrate($params['end_commissionrate']);

    if (!empty($params['start_auctioncount']) && intval($params['start_auctioncount']) > 0)
        $req->setStartAuctioncount($params['start_auctioncount']);
    if (!empty($params['end_auctioncount']) && intval($params['end_auctioncount']) > 0)
        $req->setEndAuctioncount($params['end_auctioncount']);

    if (!empty($params['start_totalaction']) && intval($params['start_totalaction']) > 0)
        $req->setStartTotalaction($params['start_totalaction']);
    if (!empty($params['end_totalaction']) && intval($params['end_totalaction']) > 0)
        $req->setEndTotalaction($params['end_totalaction']);

    $req->setOnlyMall($params['only_mall'] ? "true" : "false");

//    if (!empty($params['sort_field']))
//        $req->setSortField($params['sort_field']);
//    if (!empty($params['sort_type'])) {
//        $req->setSortType($params['sort_type']);
//    }
    $req->setSortField('total_auction');
    $req->setSortType('desc');

    if (!empty($params['page_no']))
        $req->setPageNo(intval($params['page_no']));
    if (!empty($params['page_size']))
        $req->setPageSize(intval($params['page_size']));
    $req->setOuterCode(xt_outercode());
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }
    if (isset($resp->taobaoke_shops) && isset($resp->taobaoke_shops->taobaoke_shop)) {
        return array('shops' => $resp->taobaoke_shops->taobaoke_shop, 'total' => $resp->total_results);
    }
    return array('shops' => array(), 'total' => 0);
}

function xt_taobaoke_items_search($params) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    $params = _xt_taobaoke_items_search_params($params);
    if (empty($params['keyword']) && (empty($params['cid']) || intval($params['cid']) == 0)) {
        return new WP_Error('业务错误', '参数不完整,必须指定搜索关键词或搜索分类!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TaobaokeItemsGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TaobaokeItemsGetRequest;
    $req->setFields($params['fields']);
    if (!empty($params['keyword']))
        $req->setKeyword($params['keyword']);
    if (!empty($params['cid']) && intval($params['cid']) > 0)
        $req->setCid(intval($params['cid']));
    if (!empty($params['start_price']) && intval($params['start_price']) > 0)
        $req->setStartPrice($params['start_price']);
    if (!empty($params['end_price']) && intval($params['end_price']) > 0)
        $req->setEndPrice($params['end_price']);
    if (!empty($params['sort']))
        $req->setSort($params['sort']);
    if (!empty($params['start_credit'])) {
        $req->setStartCredit($params['start_credit']);
    }
    if (!empty($params['end_credit'])) {
        $req->setEndCredit($params['end_credit']);
    }
    if (!empty($params['start_commissionRate']) && intval($params['start_commissionRate']) > 0)
        $req->setStartCommissionRate($params['start_commissionRate']);
    if (!empty($params['end_commissionRate']) && intval($params['end_commissionRate']) > 0)
        $req->setEndCommissionRate($params['end_commissionRate']);
    $req->setCashOndelivery($params['cash_ondelivery'] ? "true" : "false");
    $req->setMallItem($params['mall_item'] ? "true" : "false");
    if (!empty($params['page_no']))
        $req->setPageNo(intval($params['page_no']));
    if (!empty($params['page_size']))
        $req->setPageSize(intval($params['page_size']));
    $req->setOuterCode(xt_outercode());
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }
    if (isset($resp->taobaoke_items) && isset($resp->taobaoke_items->taobaoke_item)) {
        return array('items' => $resp->taobaoke_items->taobaoke_item, 'total' => $resp->total_results);
    }
    return array('items' => array(), 'total' => 0);
}

function xt_taobaoke_items_coupon_search($params) {
    $app = xt_taobao_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置淘宝开放平台!');
    }
    $params = _xt_taobaoke_items_coupon_search_params($params);
    if (empty($params['keyword']) && (empty($params['cid']) || intval($params['cid']) == 0)) {
        return new WP_Error('业务错误', '参数不完整,必须指定搜索关键词或搜索分类!');
    }
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/RequestCheckUtil.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/TopClient.php';
    include_once XT_PLUGIN_DIR . '/xt-core/sdks/taobao/request/TaobaokeItemsCouponGetRequest.php';
    $client = new TopClient;
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new TaobaokeItemsCouponGetRequest;
    $req->setFields($params['fields']);
    $req->setCouponType(1);
    if (empty($params['keyword']) && empty($params['cid'])) {
        $params['cid'] = 16;
    }
    if (!empty($params['keyword']))
        $req->setKeyword($params['keyword']);
    if (!empty($params['cid']) && intval($params['cid']) > 0)
        $req->setCid(intval($params['cid']));
    if (!empty($params['shop_type']))
        $req->setShopType($params['shop_type']);
    if (!empty($params['start_coupon_rate']) && intval($params['start_coupon_rate']) > 0)
        $req->setStartCouponRate($params['start_coupon_rate']);
    if (!empty($params['end_coupon_rate']) && intval($params['end_coupon_rate']) > 0)
        $req->setEndCouponRate($params['end_coupon_rate']);
    if (!empty($params['sort']))
        $req->setSort($params['sort']);
    if (!empty($params['start_credit']))
        $req->setStartCredit($params['start_credit']);
    if (!empty($params['end_credit']))
        $req->setEndCredit($params['end_credit']);
    if (!empty($params['start_commissionRate']) && intval($params['start_commissionRate']) > 0)
        $req->setStartCommissionRate($params['start_commissionRate']);
    if (!empty($params['end_commissionRate']) && intval($params['end_commissionRate']) > 0)
        $req->setEndCommissionRate($params['end_commissionRate']);

    if (!empty($params['page_no']))
        $req->setPageNo(intval($params['page_no']));
    if (!empty($params['page_size']))
        $req->setPageSize(intval($params['page_size']));
    $req->setOuterCode(xt_outercode());
    $resp = $client->execute($req);
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }
    return $resp;
}

function xt_taobao_api_error($resp, $client, $req) {
    if (isset($resp->sub_code)) {
        return new WP_Error($resp->sub_code > 0 ? $resp->sub_code : '500', $resp->sub_msg);
    } elseif (isset($resp->code)) {
        return new WP_Error($resp->code > 0 ? $resp->code : '500', $resp->msg);
    }
}

function _xt_taobaoke_items_coupon_search_params($params = array()) {
    return array_merge(array(
                'fields' => 'num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location,volume,coupon_price,coupon_rate,coupon_start_time,coupon_end_time,shop_type',
                'keyword' => '',
                'cid' => '',
                'start_coupon_rate' => '',
                'end_coupon_rate' => '',
                'start_credit' => '',
                'end_credit' => '',
                'sort' => '',
                'start_commissionRate' => '',
                'end_commissionRate' => '',
                'coupon_type' => 1,
                'shop_type' => '',
                'page_no' => 1,
                'page_size' => 40
                    ), $params);
}

function _xt_taobaoke_items_search_params($params = array()) {
    return array_merge(array(
                'fields' => 'num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location,volume',
                'keyword' => '',
                'cid' => '',
                'start_price' => '',
                'end_price' => '',
                'start_credit' => '',
                'end_credit' => '',
                'sort' => '',
                'start_commissionRate' => '',
                'end_commissionRate' => '',
                'cash_ondelivery' => '',
                'mall_item' => '',
                'page_no' => 1,
                'page_size' => 40
                    ), $params);
}

function _xt_taobaoke_shops_search_params($params = array()) {
    return array_merge(array(
                'fields' => 'seller_nick,user_id,click_url,shop_title,commission_rate,seller_credit,shop_type,auction_count,total_auction',
                'keyword' => '',
                'cid' => '',
                'start_credit' => '',
                'end_credit' => '',
                'start_commissionrate' => '',
                'end_commissionrate' => '',
                'start_auctioncount' => '',
                'end_auctioncount' => '',
                'start_totalaction' => '',
                'end_totalaction' => '',
                'only_mall' => '',
                'sort_field' => 'total_auction',
                'sort_type' => 'desc',
                'is_mobile' => '',
                'page_no' => 1,
                'page_size' => 40
                    ), $params);
}

function xt_temai_item_cat($cid, $force = false) {
    global $xt_temai_itemcat;
    if ($force) {
        $cats = xt_taobao_temaicats();
        if (isset($cats[$cid])) {
            return $cats[$cid];
        }
        return array();
    } else {
        if (empty($xt_temai_itemcat)) {
            if (!empty($cid) && $cid != -1) {
                $cats = xt_taobao_temaicats();
                if (isset($cats[$cid])) {
                    $xt_temai_itemcat = $cats[$cid];
                }
            }
        }
    }

    return $xt_temai_itemcat;
}

function xt_taobao_temaicats() {
    global $xt_taobao_temaicats;
    if (empty($xt_taobao_temaicats)) {
        $xt_taobao_temaicats = include('data-temai-category.php');
    }
    return $xt_taobao_temaicats;
}

function xt_taobao_shopcat($cid, $force = false) {
    global $xt_taobao_shopcat;
    if ($force) {
        $cats = xt_taobao_shopcats();
        if (isset($cats[$cid])) {
            return array('cid' => $cid, 'name' => $cats[$cid]);
        }
        return array();
    } else {
        if (empty($xt_taobao_shopcat)) {
            if (!empty($cid) && $cid != -1) {
                $cats = xt_taobao_shopcats();
                if (isset($cats[$cid])) {
                    $xt_taobao_shopcat = array('cid' => $cid, 'name' => $cats[$cid]);
                }
            }
        }
    }

    return $xt_taobao_shopcat;
}

function xt_taobao_shopcats() {
    global $xt_taobao_shopcats;
    if (empty($xt_taobao_shopcats)) {
        $xt_taobao_shopcats = include('data-taobao-shopcats.php');
    }
    return $xt_taobao_shopcats;
}

function xt_taobao_item_cat($cid) {
    global $wpdb, $xt_taobao_itemcat;
    if (empty($xt_taobao_itemcat)) {
        if (!empty($cid)) {
            $xt_taobao_itemcat = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . XT_TABLE_TAOBAO_ITEMCAT . ' WHERE cid=%d', $cid));
            if (!empty($xt_taobao_itemcat)) {
                $xt_taobao_itemcat = (array) $xt_taobao_itemcat;
            } else {
                $result = xt_taobao_item_cats(0, array($cid));
                if (!empty($result) && count($result) == 1 && !is_wp_error($result)) {
                    $xt_taobao_itemcat = (array) ($result[0]);
                    $wpdb->insert(XT_TABLE_TAOBAO_ITEMCAT, $xt_taobao_itemcat);
                }
            }
        }
    }
    return $xt_taobao_itemcat;
}

function xt_taobao_credit($level) {
    global $xt_taobao_credits;
    if (empty($xt_taobao_credits)) {
        $xt_taobao_credits = array(
            0 => '',
            1 => '1heart',
            2 => '2heart',
            3 => '3heart',
            4 => '4heart',
            5 => '5heart',
            6 => '1diamond',
            7 => '2diamond',
            8 => '3diamond',
            9 => '4diamond',
            10 => '5diamond',
            11 => '1crown',
            12 => '2crown',
            13 => '3crown',
            14 => '4crown',
            15 => '5crown',
            16 => '1goldencrown',
            17 => '2goldencrown',
            18 => '3goldencrown',
            19 => '4goldencrown',
            20 => '5goldencrown',
        );
    }
    return isset($xt_taobao_credits[$level]) ? $xt_taobao_credits[$level] : '';
}