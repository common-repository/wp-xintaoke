<?php

define('XT_YIQIFA_URL', 'http://openapi.yiqifa.com/api2');
define('YIQIFA_OPEN_URL', "http://open.yiqifa.com");

function xt_yiqifa_is_ready() {
    $app = xt_get_app_yiqifa();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return false;
    }
    return $app;
}

function xt_yiqifa_is_session_ready() {
    $app = xt_get_app_yiqifa();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return false;
    }
    if (empty($app['account']) || !isset($app['sid']) || empty($app['syncSecret'])) {
        return false;
    }
    return $app;
}

//商家-商家网站分类
if (!function_exists('xt_yiqifa_website_category')) {

    function xt_yiqifa_website_category($isSync = false) {
        global $xt_yiqifa_website_categories;
        if (!$isSync) {
            if (empty($xt_yiqifa_website_categories)) {
                $xt_yiqifa_website_categories = xt_get_option(XT_OPTION_YIQIFA_WEBSITE_CATEGORY);
            }
        }
        if ($isSync || empty($xt_yiqifa_website_categories)) {
            $result = xt_yiqifa_api_website_category();
            if (is_wp_error($result)) {
                wp_die("发生错误:" . $result->get_error_code() . ',' . $result->get_error_message());
            }
            $cats = $result['web_cats']['web_cat'];
            foreach ($cats as $cat) {
                $_cat = array(
                    'id' => $cat['web_catid'],
                    'name' => $cat['web_cname'],
                    'amount' => $cat['amount']
                );
                $website_list = xt_yiqifa_api_website_list(array('catid' => $cat['web_catid']));
                if (!is_wp_error($website_list)) {
                    $webs = array();
                    foreach ($website_list['web_list']['web'] as $web) {
                        parse_str($web['web_o_url'], $params);
                        $url = isset($params['t']) ? $params['t'] : $web['web_o_url'];
                        $webs[$web['web_id']] = array(
                            'id' => $web['web_id'],
                            'name' => $web['web_name'],
                            'cid' => $web['web_catid'],
                            'logo' => $web['logo_url'],
                            'url' => xt_yiqifa_website_url_convert($url),
                            'commission' => $web['commission']
                        );
                    }
                    $_cat['sites'] = $webs;
                } else {
                    $_cat['sites'] = array();
                }
                $xt_yiqifa_website_categories[$_cat['id']] = $_cat;
                uasort($xt_yiqifa_website_categories, "xt_yiqifa_website_category_sort");
            }
            if (!empty($xt_yiqifa_website_categories)) {
                if (!xt_add_option(XT_OPTION_YIQIFA_WEBSITE_CATEGORY, $xt_yiqifa_website_categories, '', 'no'))
                    xt_update_option(XT_OPTION_YIQIFA_WEBSITE_CATEGORY, $xt_yiqifa_website_categories);
            }
        }
        return $xt_yiqifa_website_categories;
    }

}

function xt_yiqifa_website_url_convert($url) {
    if (stripos($url, 'www.360buy.com') !== false) {
        $url = 'http://www.jd.com/';
    } elseif (stripos($url, 'www.suning.cn') !== false) {
        $url = 'http://www.suning.com/';
    }
    return $url;
}

function xt_yiqifa_website_category_sort($a, $b) {
    return $a['id'] - $b['id'];
}

//商家-商家网站
function xt_get_website($id) {
    $cats = xt_yiqifa_website_category();
    if (absint($id) > 0 && !empty($cats)) {
        foreach ($cats as $cat) {
            if (isset($cat['sites']) && !empty($cat['sites'])) {
                if (isset($cat['sites'][$id])) {
                    return $cat['sites'][$id];
                }
            }
        }
    }
    return array();
}

//团购-分类
function xt_yiqifa_tuan_category() {
    global $xt_yiqifa_tuan_categories;
    if (empty($xt_yiqifa_tuan_categories)) {
        $xt_yiqifa_tuan_categories = include('data-tuan-category.php');
    }
    return $xt_yiqifa_tuan_categories;
}

//团购-城市
function xt_yiqifa_tuan_city() {
    global $xt_yiqifa_tuan_cities;
    if (empty($xt_yiqifa_tuan_cities)) {
        $xt_yiqifa_tuan_cities = include ('data-tuan-city.php');
    }
    return $xt_yiqifa_tuan_cities;
}

//团购-区域
function xt_yiqifa_tuan_city_region() {
    global $xt_yiqifa_tuan_city_regions;
    if (empty($xt_yiqifa_tuan_city_regions)) {
        $xt_yiqifa_tuan_city_regions = include ('data-tuan-city-region.php');
    }
    return $xt_yiqifa_tuan_city_regions;
}

//团购-网站
function xt_yiqifa_tuan_website($isSync = false) {
    global $xt_yiqifa_tuan_websites;
    if (!$isSync) {
        if (empty($xt_yiqifa_tuan_websites)) {
            $xt_yiqifa_tuan_websites = xt_get_option(XT_OPTION_YIQIFA_TUAN_WEBSITE);
        }
    }
    if ($isSync || empty($xt_yiqifa_tuan_websites)) {
        $result = xt_yiqifa_api_tuan_website();
        if (is_wp_error($result)) {
            wp_die("发生错误:" . $result->get_error_code() . ',' . $result->get_error_message());
        }
        foreach ($result['web_list']['web'] as $web) {
            parse_str($web['web_o_url'], $params);
            $url = isset($params['t']) ? $params['t'] : $web['web_o_url'];
            $xt_yiqifa_tuan_websites[$web['web_id']] = array(
                'id' => $web['web_id'],
                'name' => $web['web_name'],
                'url' => $url
            );
        }
        if (!empty($xt_yiqifa_tuan_websites)) {
            if (!xt_add_option(XT_OPTION_YIQIFA_TUAN_WEBSITE, $xt_yiqifa_tuan_websites, '', 'no'))
                xt_update_option(XT_OPTION_YIQIFA_TUAN_WEBSITE, $xt_yiqifa_tuan_websites);
        }
    }
    return $xt_yiqifa_tuan_websites;
}

//比价-商品分类
function xt_yiqifa_category() {
    global $xt_yiqifa_categories;
    if (empty($xt_yiqifa_categories)) {
        $xt_yiqifa_categories = include('data-bijia-category.php');
    }
    return $xt_yiqifa_categories;
}

//特卖活动-定时

function xt_cron_yiqifa_hotactivity() {
    xt_yiqifa_hotactivity_website(true);
    xt_yiqifa_hotactivity(true);
}

//特卖活动-网站
function xt_yiqifa_hotactivity_website($isSync = false) {
    global $xt_yiqifa_hotactivity_websites;
    if (!$isSync) {
        if (empty($xt_yiqifa_hotactivity_websites)) {
            $xt_yiqifa_hotactivity_websites = xt_get_option(XT_OPTION_YIQIFA_HOTACTIVITY_WEBSITE);
        }
    }
    if ($isSync || empty($xt_yiqifa_hotactivity_websites)) {
        $result = xt_yiqifa_api_hotactivity_website();
        if (is_wp_error($result)) {
            wp_die("发生错误:" . $result->get_error_code() . ',' . $result->get_error_message());
        }
        $xt_yiqifa_hotactivity_websites = $result['hot_webs']['hot_web'];
        if (!empty($xt_yiqifa_hotactivity_websites)) {
            if (!xt_add_option(XT_OPTION_YIQIFA_HOTACTIVITY_WEBSITE, $xt_yiqifa_hotactivity_websites, '', 'no'))
                xt_update_option(XT_OPTION_YIQIFA_HOTACTIVITY_WEBSITE, $xt_yiqifa_hotactivity_websites);
        }
    }
    return $xt_yiqifa_hotactivity_websites;
}

//特卖活动-活动
function xt_yiqifa_hotactivity($isSync = false) {
    global $xt_yiqifa_hotactivities;
    if (!$isSync) {
        if (empty($xt_yiqifa_hotactivities)) {
            $xt_yiqifa_hotactivities = xt_get_option(XT_OPTION_YIQIFA_HOTACTIVITY);
        }
    }
    if ($isSync || empty($xt_yiqifa_hotactivities)) {
        $xt_yiqifa_hotactivities = array();
        $websites = xt_yiqifa_hotactivity_website();
        if (!empty($websites)) {
            foreach ($websites as $site) {
                $activities = array();
                for ($i = 1; $i < 10; $i++) {
                    $result = xt_yiqifa_api_hotactivity_list(array('webid' => $site['web_id'], 'page_no' => $i));
                    if (is_wp_error($result)) {
                        continue;
                    } else {
                        $hots = array();
                        foreach ($result['hot_list']['hot'] as $hot) {
                            parse_str($hot['hot_o_url'], $params);
                            $hot['hot_o_url'] = isset($params['t']) ? $params['t'] : $hot['hot_o_url'];
                            $hots[] = $hot;
                        }
                        $activities = array_merge($activities, $hots);
                    }
                }
                $xt_yiqifa_hotactivities[$site['web_id']] = $activities;
            }
        }
        if (!empty($xt_yiqifa_hotactivities)) {
            if (!xt_add_option(XT_OPTION_YIQIFA_HOTACTIVITY, $xt_yiqifa_hotactivities, '', 'no'))
                xt_update_option(XT_OPTION_YIQIFA_HOTACTIVITY, $xt_yiqifa_hotactivities);
        }
    }
    return $xt_yiqifa_hotactivities;
}

//API
function xt_yiqifa_api_result($response) {
    if (empty($response) || $response == 'null') {
        return xt_yiqifa_api_error();
    }
    if (strncmp($response, '{errors:{error:', strlen('{errors:{error:')) === 0) {
        $response = str_replace('{errors:{error:', '{"errors":{"error":', $response);
    }
    $response = str_replace(array("\r\n"), array(""), $response); //fixed 
    $result = json_decode($response, true);
    if (!empty($result) && isset($result['errors'])) {
        if (count($result['errors']['error']) == 1) {
            if ($result['errors']['error'][0]['error_code'] == 'C0003') {
                return array();
            }
        }
        return xt_yiqifa_api_error($result['errors']['error']);
    } else {
        $result = $result['response'];
    }
    if (empty($result)) {
        if (strlen('yiqifaopen_problem=yiqifaopen_frequence_limit') === 0 || substr($response, -strlen('yiqifaopen_problem=yiqifaopen_frequence_limit')) === 'yiqifaopen_problem=yiqifaopen_frequence_limit') {
            return xt_yiqifa_api_error(new WP_Error(500, '亿起发开放平台流量超出限制'));
        }
    }
    return $result;
}

function xt_yiqifa_api_website_category($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'web_catid,web_cname,amount,web_type,modified_time,total';
    }

    $type = isset($params['type']) ? absint($params['type']) : 1;
    if (!in_array($type, array(1, 2))) {
        $type = 1;
    }
    $url = XT_YIQIFA_URL . "/open.website.category.get.json";
    $query_args = array('fields' => ($fields), 'type' => $type);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_website_list($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'web_id,web_name,web_catid,logo_url,web_o_url,commission,total';
    }

    $type = isset($params['type']) ? absint($params['type']) : 1;
    if (!in_array($type, array(1, 2))) {
        $type = 1;
    }
    $catid = isset($params['catid']) ? ($params['catid']) : '';

    $url = XT_YIQIFA_URL . "/open.website.list.get.json";
    $query_args = array('fields' => ($fields), 'type' => $type, 'catid' => $catid);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_website($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'web_id,web_name,web_catid,logo_url,web_url,information,begin_date,end_date,commission';
    }

    $type = isset($params['type']) ? absint($params['type']) : 1;
    if (!in_array($type, array(1, 2))) {
        $type = 1;
    }
    $webid = isset($params['webid']) ? absint($params['webid']) : '';

    $url = XT_YIQIFA_URL . "/open.website.get.json";
    $query_args = array('fields' => ($fields), 'type' => $type, 'webid' => $webid);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));

    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_category($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'catid,cname,parent_id,alias,is_parent,modified_time';
    }

    $parent_id = isset($params['parent_id']) ? absint($params['parent_id']) : '';

    $url = XT_YIQIFA_URL . "/open.category.get.json";
    $query_args = array('fields' => ($fields), 'parent_id' => $parent_id);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_product_search($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $keyword = isset($params['keyword']) ? $params['keyword'] : '';
    if (empty($keyword)) {
        $keyword = '女';
        //return xt_yiqifa_api_product_list_get($params);//TODO
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total';
    }
    $page_no = isset($params['page_no']) ? absint($params['page_no']) : 1;
    if ($page_no == 0) {
        $page_no = 1;
    }
    $page_size = isset($params['page_size']) ? absint($params['page_size']) : 40;
    if ($page_size == 0) {
        $page_size = 40;
    }
    $catid = isset($params['catid']) ? absint($params['catid']) : '';
    if ($catid == 0) {
        $catid = '';
    }
    $webid = isset($params['webid']) ? $params['webid'] : '';
    $price_range = '';
    $minprice = isset($params['minprice']) ? absint($params['minprice']) : "";
    if ($minprice == 0) {
        $minprice = '';
    }

    $maxprice = isset($params['maxprice']) ? absint($params['maxprice']) : "";
    if ($maxprice == 0) {
        $maxprice = '';
    }
    if ($minprice > 0 && $maxprice > 0) {
        $price_range.=$minprice . ',' . $maxprice;
    }
    $orderby = isset($params['orderby']) ? absint($params['orderby']) : "";
    if ($orderby == 0) {
        $orderby = 3;
    }
    $url = XT_YIQIFA_URL . "/open.product.search.json";

    $query_args = array('fields' => ($fields), 'keyword' => urlencode(iconv('UTF-8', 'GBK', $keyword)), 'page_no' => $page_no, 'page_size' => $page_size, 'catid' => $catid, 'webid' => $webid, 'price_range' => $price_range, 'orderby' => $orderby);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    $result = xt_yiqifa_api_result($response);
    if (empty($result)) {
        return array('total' => 0, 'pdt_list' => array('pdt' => array()));
    }
    return $result;
}

function xt_yiqifa_api_product_list_get($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total';
    }
    $page_no = isset($params['page_no']) ? absint($params['page_no']) : 1;
    if ($page_no == 0) {
        $page_no = 1;
    }
    $page_size = isset($params['page_size']) ? absint($params['page_size']) : 40;
    if ($page_size == 0) {
        $page_size = 40;
    }
    $catid = isset($params['catid']) ? absint($params['catid']) : '';

    $webid = isset($params['webid']) ? $params['webid'] : '';
    if (empty($catid) && empty($webid)) {
        $catid = XT_BIJIA_CATEGORY_DEFAULT;
    }
    $price_range = '';
    $minprice = isset($params['minprice']) ? absint($params['minprice']) : "";
    if ($minprice == 0) {
        $minprice = '';
    }

    $maxprice = isset($params['maxprice']) ? absint($params['maxprice']) : "";
    if ($maxprice == 0) {
        $maxprice = '';
    }
    if ($minprice > 0 && $maxprice > 0) {
        $price_range.=$minprice . ',' . $maxprice;
    }

    $url = XT_YIQIFA_URL . "/open.product.list.get.json";
    $query_args = array('fields' => ($fields), 'page_no' => $page_no, 'page_size' => $page_size, 'catid' => $catid, 'webid' => $webid, 'price_range' => $price_range);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    $result = xt_yiqifa_api_result($response);
    if (empty($result)) {
        return array('total' => 0, 'pdt_list' => array('pdt' => array()));
    }
    return $result;
}

function xt_yiqifa_api_tuan_search($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $keyword = isset($params['keyword']) ? $params['keyword'] : '';
    if (empty($keyword)) {//no keyword
        return xt_yiqifa_api_tuan_product_list($params);
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'tuan_pid,title,web_id,pdt_o_url,pic_url,ori_price,cur_price,begin_time,end_time,bought,tuan_catid,city_id,city_name,discount,modified_time,total';
    }
    $page_no = isset($params['page_no']) ? absint($params['page_no']) : 1;
    if ($page_no == 0) {
        $page_no = 1;
    }
    $page_size = isset($params['page_size']) ? absint($params['page_size']) : 40;
    if ($page_size == 0) {
        $page_size = 40;
    }
    $catid = isset($params['catid']) ? absint($params['catid']) : '';
    if ($catid == 0) {
        $catid = '';
    }
    //$webid = isset($params['webid']) ? $params['webid'] : '';
    $minprice = 1;
    $maxprice = 1000;
    $price = isset($params['price']) ? $params['price'] : '';
    switch ($price) {
        case 'low':
            $minprice = 0;
            $maxprice = 50;
            break;
        case 'medium':
            $minprice = 50;
            $maxprice = 100;
            break;
        case 'high':
            $minprice = 100;
            $maxprice = 1000000;
            break;
    }
    $price_range = $minprice . ',' . $maxprice;
    $city_id = isset($params['city_id']) ? absint($params['city_id']) : '';
    if (empty($city_id)) {
        $city_id = 110000;
    }
    $cities = xt_yiqifa_tuan_city();
    if (!empty($city_id) && $city_id != XT_CITY_DEFAULT) {
        if (!isset($cities[$city_id])) {
            $city_id = XT_CITY_DEFAULT;
        }
    }
    $orderby = isset($params['orderby']) ? ($params['orderby']) : "";
    if (is_numeric($orderby)) {
        $orderby = '';
    }
    $url = XT_YIQIFA_URL . "/open.tuan.search.json";
    $query_args = array('fields' => ($fields), 'keyword' => urlencode(iconv('UTF-8', 'GBK', $keyword)), 'page_no' => $page_no, 'page_size' => $page_size, 'catid' => $catid, 'price_range' => $price_range, 'orderby' => $orderby, 'city_id' => $city_id);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    $result = xt_yiqifa_api_result($response);
    if (empty($result)) {
        return array('total' => 0, 'tuan_list' => array('tuan' => array()));
    }
    return $result;
}

function xt_yiqifa_api_tuan_product_list($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'tuan_pid,title,web_id,pdt_o_url,pic_url,ori_price,cur_price,begin_time,end_time,bought,tuan_catid,city_id,city_name,discount,modified_time,total';
    }
    $page_no = isset($params['page_no']) ? absint($params['page_no']) : 1;
    if ($page_no == 0) {
        $page_no = 1;
    }
    $page_size = isset($params['page_size']) ? absint($params['page_size']) : 40;
    if ($page_size == 0) {
        $page_size = 40;
    }
    $catid = isset($params['catid']) ? absint($params['catid']) : '';
    if ($catid == 0) {
        $catid = '';
    }
    //$webid = isset($params['webid']) ? $params['webid'] : '';
    $minprice = 1;
    $maxprice = 1000;
    $price = isset($params['price']) ? $params['price'] : '';
    switch ($price) {
        case 'low':
            $minprice = 0;
            $maxprice = 50;
            break;
        case 'medium':
            $minprice = 50;
            $maxprice = 100;
            break;
        case 'high':
            $minprice = 100;
            $maxprice = 1000000;
            break;
    }
    $price_range = $minprice . ',' . $maxprice;
    $city_id = isset($params['city_id']) ? absint($params['city_id']) : '';
    if (empty($city_id)) {
        $city_id = XT_CITY_DEFAULT;
    }
    $url = XT_YIQIFA_URL . "/open.tuan.product.list.get.json";
    $query_args = array('fields' => ($fields), 'page_no' => $page_no, 'page_size' => $page_size, 'catid' => $catid, 'price_range' => $price_range, 'city_id' => $city_id);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    $result = xt_yiqifa_api_result($response);

    if (empty($result)) {
        return array('total' => 0, 'tuan_list' => array('tuan' => array()));
    }
    return $result;
}

function xt_yiqifa_api_tuan_city($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'city_id,name_cn,name_py,is_parent,parent_id,level,tag,head,type,total';
    }

    $type = isset($params['type']) ? absint($params['type']) : 1;
    if (!in_array($type, array(1, 2))) {
        $type = 2; //all
    }
    $url = XT_YIQIFA_URL . "/open.tuan.city.get.json";
    $query_args = array('fields' => ($fields), 'type' => $type);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_tuan_city_region($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'region_id,name_cn,name_py,parent_id,level,type';
    }

    $type = isset($params['type']) ? absint($params['type']) : 1;
    if (!in_array($type, array(1, 2))) {
        $type = 2; //all
    }
    $city_id = isset($params['city_id']) ? absint($params['city_id']) : '';
    if (empty($city_id)) {
        $city_id = 110000;
    }
    $url = XT_YIQIFA_URL . "/open.tuan.city.region.get.json";
    $query_args = array('fields' => ($fields), 'type' => $type, 'city_id' => $city_id);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_tuan_category($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'catid,cat_name,alias,is_parent,parent_id,create_time';
    }
    $url = XT_YIQIFA_URL . "/open.tuan.category.get.json";
    $query_args = array('fields' => ($fields));
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_tuan_website($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'web_id,web_name,web_o_url,modified_time,total';
    }
    $url = XT_YIQIFA_URL . "/open.tuan.website.get.json";
    $query_args = array('fields' => ($fields));
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_hotactivity_category($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'hot_catid,hot_cname,modified_time';
    }
    $url = XT_YIQIFA_URL . "/open.hotactivity.category.get.json";
    $query_args = array('fields' => ($fields));
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_hotactivity_website($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'web_id,web_name,web_o_url,modified_time';
    }
    $url = XT_YIQIFA_URL . "/open.hotactivity.website.get.json";
    $query_args = array('fields' => ($fields));
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_hotactivity_list($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'hot_id,web_id,web_name,hot_name,pic_url,hot_o_url,discount,brand_name,hot_catid,begin_date,end_date,modified_time,total';
    }
    $catid = isset($params['catid']) ? ($params['catid']) : '';
    $webid = isset($params['webid']) ? absint($params['webid']) : '';
    if ($webid == 0) {
        $webid = '';
    }
    $page_no = isset($params['page_no']) ? absint($params['page_no']) : 1;
    if ($page_no == 0) {
        $page_no = 1;
    }
    $page_size = isset($params['page_size']) ? absint($params['page_size']) : 100;
    if ($page_size == 0) {
        $page_size = 100;
    }

    $url = XT_YIQIFA_URL . "/open.hotactivity.list.get.json";
    $query_args = array('fields' => ($fields), 'catid' => $catid, 'webid' => $webid, 'page_no' => $page_no, 'page_size' => $page_size);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_ad_category($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'ad_catid,ad_cname,ad_amount';
    }
    $url = XT_YIQIFA_URL . "/open.yiqifa.ad.category.get.json";
    $query_args = array('fields' => ($fields));
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_ad_list($params = array()) {
    $app = xt_yiqifa_is_ready();
    if (!$app) {
        return new WP_Error('系统错误', '尚未配置亿起发开放平台!');
    }
    $fields = isset($params['fields']) ? $params['fields'] : '';
    if (empty($fields)) {
        $fields = 'ad_id,ad_name,ad_catid,ad_cname,logo_url,ad_o_url,adver_name,adver_id,charge_type,audit_mode,ad_type,begin_date,end_date,create_time,modified_time,commission,introduction,confirm_time,total';
    }
    $charge_type = 'cps';
    $ad_catid = isset($params['ad_catid']) ? ($params['ad_catid']) : '';
    $audit_mode = urlencode(iconv('UTF-8', 'GBK', isset($params['audit_mode']) ? $params['audit_mode'] : '无需审核,自动审核,人工审核'));
    $ad_type = isset($params['ad_type']) ? $params['ad_type'] : 'web';
    $url = XT_YIQIFA_URL . "/open.yiqifa.ad.list.get.json";
    $query_args = array('fields' => ($fields), 'charge_type' => $charge_type, 'ad_catid' => $ad_catid, 'audit_mode' => $audit_mode, 'ad_type' => $ad_type);
    $response = trim(YiqifaUtils :: sendRequest(add_query_arg($query_args, $url), $app['appKey'], $app['appSecret']));
    return xt_yiqifa_api_result($response);
}

function xt_yiqifa_api_error($result = false) {
    if (is_wp_error($result)) {
        return $result;
    } elseif (is_array($result)) {
        $error = new WP_Error();
        foreach ($result as $err) {
            $error->add($err['error_code'], $err['msg'], $err['request']);
        }
        return $error;
    } else {
        return new WP_Error('500', '请求亿起发发生错误:' . $result);
    }
}

class YiqifaUtils {

    static function sendRequest($url, $key, $secret) {
        $au = YiqifaUtils :: generateOauth($url, $key, $secret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: " . $au
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, YIQIFA_OPEN_URL);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        curl_close($ch);
        return iconv("GBK", "UTF-8//IGNORE", $result);
    }

    static function hmacsha1($key, $data) {
        $blocksize = 64;
        $hashfunc = 'sha1';
        if (strlen($key) > $blocksize)
            $key = pack('H*', $hashfunc($key));
        $key = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack('H*', $hashfunc(($key ^ $opad) .
                        pack('H*', $hashfunc(($key ^ $ipad) .
                                        $data))));

        return base64_encode($hmac);
    }

    static function generateOauth($url, $key, $secret) {

        $authparam = self :: generateAuthParams($key, $secret);

        $params = array_merge($authparam, self :: parseGetParams($url));

        $basestr = self :: generateBaseStr($url, $params);

        $tk = $secret . "&openyiqifa";

        $sign = self :: hmacsha1($tk, $basestr);
        $str = "";
        foreach ($authparam as $k => $v) {
            if ($str == "")
                $str .= $k . "=\"" . urlencode($v) . "\"";
            else
                $str .= ("," . $k . "=\"" . urlencode($v) . "\"");
        }

        $str = "OAuth " . $str . ",oauth_signature=\"" . urlencode($sign) . "\"";
        return $str;
    }

    static function generateAuthParams($key, $secret) {
        $ts = strtotime("now");
        $nonce = $ts + rand();
        $authparam = array(
            "oauth_consumer_key" => $key,
            "oauth_signature_method" => "HMAC-SHA1",
            "oauth_timestamp" => $ts,
            "oauth_nonce" => $nonce,
            "oauth_version" => "1.0",
            "oauth_token" => "openyiqifa"
        );
        return $authparam;
    }

    static function generateRequestStr($url) {
        $authparam = self :: generateAuthParams();

        $params = array_merge($authparam, self :: parseGetParams($url));

        $basestr = self :: generateBaseStr($url, $params);

        $sign = self :: hmacsha1(self :: TOKEN_KEYS, $basestr);

//$params['oauth_signature'] = urlencode($sign);

        return self :: constructRequestURL($url) . '?oauth_signature=' . urlencode($sign) . "&" . self :: normalizeRequestParameters($params);
    }

    static function generateBaseStr($url, $params) {
        $params = self :: sortParams($params);

        $basestr = "GET&" . urlencode(self :: constructRequestURL($url)) . '&' . urlencode(self :: normalizeRequestParameters($params));
        return $basestr;
    }

    static function normalizeRequestParameters($params) {
        $s = "";
        foreach ($params as $k => $v) {
            if ($s == "") {
                $s = $k . "=" . urlencode($v);
            } else {
                $s = $s . "&" . $k . "=" . urlencode($v);
            }
        }
        return $s;
    }

    static function sortParams($params) {
        $keys = array_keys($params);
        sort($keys);
        $newparams = array();
        foreach ($keys as $k) {
            $newparams[$k] = $params[$k];
        }
        return $newparams;
    }

    static function constructRequestURL($url) {
        $i = strpos($url, "?");
        if (!$i) {
            return $url;
        } else {
            return substr($url, 0, $i);
        }
    }

    static function parseGetParams($url) {
        $params = array();
        $i = strpos($url, "?");

        if (!$i) {
            return $params;
        }

        $sp = explode("&", substr($url, $i + 1, strlen($url)));

        foreach ($sp as $p) {
            $spi = explode("=", $p);
            if (count($spi) > 1)
                $params[urldecode($spi[0])] = urldecode($spi[1]);
        }
        return $params;
    }

}

function xt_bijia_item_cat($cid) {
    global $xt_bijia_itemcat;
    if (empty($xt_bijia_itemcat)) {
        if (!empty($cid) && $cid != -1) {
            $cats = xt_yiqifa_category();
            if (isset($cats[$cid])) {
                $xt_bijia_itemcat = $cats[$cid];
            }
        }
    }
    return $xt_bijia_itemcat;
}

function xt_tuan_item_cat($cid) {
    global $xt_tuan_itemcat;
    if (empty($xt_tuan_itemcat)) {
        if (!empty($cid) && $cid != -1) {
            $cats = xt_yiqifa_tuan_category();
            if (isset($cats[$cid])) {
                $xt_tuan_itemcat = $cats[$cid];
            }
        }
    }
    return $xt_tuan_itemcat;
}