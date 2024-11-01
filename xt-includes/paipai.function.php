<?php

function xt_paipai_is_ready() {
    $app = xt_get_app_paipai();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return false;
    }
    return $app;
}

function xt_paipai_is_session_ready() {
    $app = xt_get_app_paipai();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return false;
    }
    if (empty($app['token']) || empty($app['uid'])) {
        return false;
    }
    return $app;
}

function xt_paipai_api_error($result) {
    if (is_wp_error($result)) {
        return $result;
    } else {
        if ($result->errorCode == 1249) {
            //reset token
            $option_platform = get_option(XT_OPTION_PLATFORM);
            $option_platform['paipai']['token'] = array();
            update_option(XT_OPTION_PLATFORM, $option_platform);
            return new WP_Error($result->errorCode, '请获取最新的accessToken并配置在平台中');
        }
        return new WP_Error($result->errorCode, $result->errorMessage);
    }
}

function xt_paipai_item_cats($navigationId = 0) {
    $app = xt_get_app_paipai();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return new WP_Error('系统错误', '尚未配置拍拍开放平台!');
    }
    if (empty($app['token']) || empty($app['uid'])) {
        return new WP_Error('系统错误', '尚未配置拍拍平台QQ号及ACCESS_TOKEN!');
    }
    require_once XT_PLUGIN_DIR . '/xt-core/sdks/paipai/src/PaiPaiOpenApiOauth.php';

    $sdk = new PaiPaiOpenApiOauth($app['appKey'], $app['appSecret'], $app['token'], $app['uid']);
    $sdk->setDebugOn(false);
    $sdk->setMethod("get");
    $sdk->setCharset("utf-8");
    $sdk->setFormat('json');

    $sdk->setApiPath("/attr/getNavigationChildList.xhtml"); //这个是用户需要调用的 接口函数
    $params = & $sdk->getParams(); //注意，这里使用的是引用，故可以直接使用
    $params["pureData"] = 1;
    $params["navigationId"] = $navigationId;
    $resp = $sdk->invoke();
    if ($resp) {
        $resp = json_decode($resp);
    }
    if (isset($resp->errorCode) && $resp->errorCode > 0) {
        return xt_paipai_api_error($resp);
    }
    return $resp;
}

function xt_paipai_item_cats_path($navigationId = 0) {
    $app = xt_get_app_paipai();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return new WP_Error('系统错误', '尚未配置拍拍开放平台!');
    }
    if (empty($app['token']) || empty($app['uid'])) {
        return new WP_Error('系统错误', '尚未配置拍拍平台QQ号及ACCESS_TOKEN!');
    }
    require_once XT_PLUGIN_DIR . '/xt-core/sdks/paipai/src/PaiPaiOpenApiOauth.php';

    $sdk = new PaiPaiOpenApiOauth($app['appKey'], $app['appSecret'], $app['token'], $app['uid']);
    $sdk->setDebugOn(false);
    $sdk->setMethod("get");
    $sdk->setCharset("utf-8");
    $sdk->setFormat('json');

    $sdk->setApiPath("/attr/getNavByNavMapId.xhtml"); //这个是用户需要调用的 接口函数
    $params = & $sdk->getParams(); //注意，这里使用的是引用，故可以直接使用
    $params["pureData"] = 1;
    $params["navId"] = $navigationId;
    $params["mapId"] = 0;
    $resp = $sdk->invoke();
    if ($resp) {
        $resp = json_decode($resp);
    }
    if (isset($resp->errorCode) && $resp->errorCode > 0) {
        return xt_paipai_api_error($resp);
    }
    if (isset($resp->GetPubPathResult)) {
        $resp = $resp->GetPubPathResult;
        if ($resp->errorCode > 0) {
            return xt_paipai_api_error($resp);
        }
    }
    return isset($resp->navBo) ? $resp->navBo : FALSE;
}

function xt_paipai_item_cats_sync($parentCid = 0) {
    global $wpdb;
    $parents = xt_paipai_item_cats($parentCid);
    if (is_wp_error($parents)) {
        return $parents;
    }
    $parents = $parents->childList;
    if (!empty($parents)) {
        foreach ($parents as $parent) {
            $wpdb->replace(XT_TABLE_PAIPAI_ITEMCAT, array(
                'cid' => $parent->navigationId,
                'parent_cid' => $parentCid,
                'name' => $parent->navigationName,
                'is_parent' => 0,
                'is_class' => $parent->isClass,
                'navprop' => $parent->navProp
            ));
            xt_paipai_item_cats_sync($parent->navigationId);
        }
        $wpdb->update(XT_TABLE_PAIPAI_ITEMCAT, array(
            'is_parent' => 1
                ), array(
            'cid' => $parentCid
        ));
    } else {
        $wpdb->update(XT_TABLE_PAIPAI_ITEMCAT, array(
            'is_parent' => 0
                ), array(
            'cid' => $parentCid
        ));
    }
}

function xt_paipai_item($id) {
    $app = xt_get_app_paipai();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return new WP_Error('系统错误', '尚未配置拍拍开放平台!');
    }
    if (empty($app['token']) || empty($app['uid'])) {
        return new WP_Error('系统错误', '尚未配置拍拍平台QQ号及ACCESS_TOKEN!');
    }
    require_once XT_PLUGIN_DIR . '/xt-core/sdks/paipai/src/PaiPaiOpenApiOauth.php';

    $sdk = new PaiPaiOpenApiOauth($app['appKey'], $app['appSecret'], $app['token'], $app['uid']);
    $sdk->setDebugOn(false);
    $sdk->setMethod("get");
    $sdk->setCharset("utf-8");
    $sdk->setFormat('json');

    $sdk->setApiPath("/item/getItem.xhtml"); //这个是用户需要调用的 接口函数
    $params = & $sdk->getParams(); //注意，这里使用的是引用，故可以直接使用
    $params["pureData"] = 1;
    $params["itemCode"] = $id;
    $params["needParseAttr"] = 0;
    $params["needDetailInfo"] = 0;
    $params["needExtendInfo"] = 0;
    $resp = $sdk->invoke();
    if ($resp) {
        $resp = json_decode($resp);
    }
    if ($resp->errorCode > 0) {
        return xt_paipai_api_error($resp);
    }
    return $resp;
}

function xt_paipaike_report($start, $end, $page = 1, $page_size = 40) {
    $app = xt_get_app_paipai();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return new WP_Error('系统错误', '尚未配置拍拍开放平台!');
    }
    if (empty($app['token']) || empty($app['uid'])) {
        return new WP_Error('系统错误', '尚未配置拍拍平台QQ号及ACCESS_TOKEN!');
    }
    require_once XT_PLUGIN_DIR . '/xt-core/sdks/paipai/src/PaiPaiOpenApiOauth.php';
    $sdk = new PaiPaiOpenApiOauth($app['appKey'], $app['appSecret'], $app['token'], $app['uid']);
    $sdk->setDebugOn(false);
    $sdk->setMethod("get");
    $sdk->setCharset("utf-8");
    $sdk->setFormat('json');

    $sdk->setApiPath("/cps/etgReportCheck.xhtml");
    $params = & $sdk->getParams();
    $params["pureData"] = 1;
    $params["beginTime"] = $start;
    $params["endTime"] = $end;
    $params["state"] = 0;
    $params["reportType"] = 1;
    $params["userId"] = $app['uid'];
    $params["pageIndex"] = $page;
    $params["pageSize"] = $page_size;
    $resp = $sdk->invoke();
    if ($resp) {
        $resp = json_decode($resp);
    }
    if (isset($resp->errorCode) && $resp->errorCode > 0) {
        return xt_paipai_api_error($resp);
    }
    return $resp;
}

function xt_paipaike_item($id) {
    $app = xt_get_app_paipai();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return new WP_Error('系统错误', '尚未配置拍拍开放平台!');
    }
    if (empty($app['token']) || empty($app['uid'])) {
        return new WP_Error('系统错误', '尚未配置拍拍平台QQ号及ACCESS_TOKEN!');
    }
    if (empty($app['userId'])) {
        return new WP_Error('系统错误', '尚未配置拍拍客的推广ID!');
    }
    require_once XT_PLUGIN_DIR . '/xt-core/sdks/paipai/src/PaiPaiOpenApiOauth.php';

    $sdk = new PaiPaiOpenApiOauth($app['appKey'], $app['appSecret'], $app['token'], $app['uid']);
    $sdk->setDebugOn(false);
    $sdk->setMethod("get");
    $sdk->setCharset("utf-8");
    $sdk->setFormat('json');

    $sdk->setApiPath("/cps/cpsCommQueryAction.xhtml"); //这个是用户需要调用的 接口函数
    $params = & $sdk->getParams(); //注意，这里使用的是引用，故可以直接使用
    $params["pureData"] = 1;
    $params["commId"] = $id;
    $params["userId"] = $app['userId'];
    $resp = $sdk->invoke();
    if ($resp) {
        $resp = json_decode($resp);
    }
    if (isset($resp->errorCode) && $resp->errorCode > 0) {
        return xt_paipai_api_error($resp);
    }
    if (isset($resp->CpsQueryResult)) {
        $resp = $resp->CpsQueryResult;
        if ($resp->errorCode > 0)
            return xt_paipai_api_error($resp);
    } else {
        return new WP_Error('4096', '拍拍服务器错误');
    }
    return $resp;
}

function xt_paipaike_items_search($args) {
    $app = xt_get_app_paipai();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return new WP_Error('系统错误', '尚未配置拍拍开放平台!');
    }
    if (empty($app['token']) || empty($app['uid'])) {
        return new WP_Error('系统错误', '尚未配置拍拍平台QQ号及ACCESS_TOKEN!');
    }
    if (empty($app['userId'])) {
        return new WP_Error('系统错误', '尚未配置拍拍客的推广ID!');
    }
    require_once XT_PLUGIN_DIR . '/xt-core/sdks/paipai/src/PaiPaiOpenApiOauth.php';

    $sdk = new PaiPaiOpenApiOauth($app['appKey'], $app['appSecret'], $app['token'], $app['uid']);
    $sdk->setDebugOn(false);
    $sdk->setMethod("get");
    $sdk->setCharset("utf-8");
    $sdk->setFormat('json');

    $sdk->setApiPath("/cps/cpsCommSearch.xhtml"); //这个是用户需要调用的 接口函数

    $params = & $sdk->getParams(); //注意，这里使用的是引用，故可以直接使用
    $params = _xt_paipai_search_params($args);
    $params["pureData"] = 1;
    $params['userId'] = $app['userId'];
    $params['outInfo'] = xt_outercode();
    $params['pageIndex'] = (intval($params['pageIndex']) - 1) * intval($params['pageSize']) + 1;
    if (intval($params['payType']) == 0) {
        unset($params['payType']);
    }
    if (intval($params['begPrice']) == 0) {
        unset($params['begPrice']);
    } else {
        $params['begPrice'] = intval($params['begPrice']) * 100;
    }
    if (intval($params['endPrice']) == 0) {
        unset($params['endPrice']);
    } else {
        $params['endPrice'] = intval($params['endPrice']) * 100;
    }
    if (intval($params['crMin']) == 0) {
        unset($params['crMin']);
    }
    if (intval($params['crMax']) == 0) {
        unset($params['crMax']);
    }
    if (intval($params['classId']) == 0 && empty($params['keyWord'])) {
        return new WP_Error('业务错误', 'keyWord 和 classId 参数至少有一个设值');
    }
    $resp = $sdk->invoke();
    if ($resp) {
        $resp = json_decode($resp);
    }
    if (isset($resp->CpsCommSearchResult)) {
        $resp = $resp->CpsCommSearchResult;
        if ($resp->errorCode > 0) {
            return xt_paipai_api_error($resp);
        } else {
            return array('items' => $resp->vecComm, 'total' => $resp->hitNum);
        }
    } else {
        return new WP_Error('系统错误', '未知');
    }
    return array('items' => array(), 'total' => 0);
}

function _xt_paipai_search_params($params = array()) {
    return array_merge(array(
                'classId' => '',
                'keyWord' => '',
                'begPrice' => '',
                'endPrice' => '',
                'orderStyle' => '',
                'crMin' => '',
                'crMax' => '',
                'payType' => '',
                'property' => '',
                'hotClassId' => '',
                'level' => '',
                'materialId' => '',
                'activeId' => '',
                'address' => '',
                'adPosition' => '',
                'hongbaoTag' => '',
                'productId' => '',
                'pageIndex' => '',
                'pageSize' => ''
                    ), $params);
}

function xt_paipai_item_cat($cid) {
    global $wpdb, $xt_paipai_itemcat;
    if (empty($xt_paipai_itemcat)) {
        if (!empty($cid)) {
            $xt_paipai_itemcat = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . XT_TABLE_PAIPAI_ITEMCAT . ' WHERE cid=%d', $cid));
            if (!empty($xt_paipai_itemcat)) {
                $xt_paipai_itemcat = (array) $xt_paipai_itemcat;
            } else {
                $result = xt_paipai_item_cats_path($cid);
                if (!empty($result) && !is_wp_error($result)) {
                    if (isset($result->NavNode)) {
                        $navNode = $result->NavNode;
                        $pNavNode = (isset($navNode->PNavId) && !empty($navNode->PNavId)) ? $navNode->PNavId : 0;
                        $xt_paipai_itemcat = array(
                            'cid' => $navNode->NavId,
                            'parent_cid' => $pNavNode,
                            'name' => $navNode->Name,
                            'is_parent' => 0
                        );
                        $wpdb->insert(XT_TABLE_PAIPAI_ITEMCAT, $xt_paipai_itemcat);
                        if ($pNavNode) {
                            $parent = $wpdb->get_row('SELECT * FROM ' . XT_TABLE_PAIPAI_ITEMCAT . ' WHERE cid=' . absint($pNavNode));
                            if (!empty($parent)) {
                                if ($parent->is_parent == 0) {
                                    $wpdb->update(XT_TABLE_PAIPAI_ITEMCAT, array('is_parent' => 1), array('cid' => $parent->cid));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $xt_paipai_itemcat;
}