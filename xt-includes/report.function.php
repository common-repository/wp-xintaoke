<?php

global $xt_report_total, $xt_report_insert;

function xt_report_taobao($start = 0, $end = 0) {
    $xt_report_total = 0;
    $xt_report_insert = 0;
    $app = xt_get_app_taobao();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret']) || !$app['isValid']) {
        wp_die('暂不支持淘宝订单获取!');
    }
    if (empty($end)) {
        $_date = date('Ymd', current_time('timestamp'));
        $end = date('Ymd', strtotime($_date . '+1 day'));
    }
    if (empty($start)) {
        $start = date('Ymd', current_time('timestamp'));
    }
    while ($start <= $end) {
        _xt_report_taobao_page($start);
        $start = date('Ymd', strtotime($start . '+1 day'));
    }
}

function _xt_report_taobao_page($date, $page = 1) {
    $_PAGE_SIZE = 40;
    $taobaoke_report = xt_taobaoke_report($date, $page, $_PAGE_SIZE);
    if (is_wp_error($taobaoke_report)) {
        wp_die($taobaoke_report->get_error_code(), $taobaoke_report->get_error_message());
    }
    if (isset($taobaoke_report->taobaoke_report_members) && isset($taobaoke_report->taobaoke_report_members->taobaoke_report_member)) {
        $taobaoke_report_members = $taobaoke_report->taobaoke_report_members->taobaoke_report_member;
        $total_results = isset($taobaoke_report->total_results) ? $taobaoke_report->total_results : count($taobaoke_report_members);
        foreach ($taobaoke_report_members as $report) {
            global $xt_report_total;
            $xt_report_total++;
            _xt_report_taobao_report_save($report);
        }
        if ($total_results >= $_PAGE_SIZE) {
//            $totalPageCount = (int) ($total_results / $_PAGE_SIZE);
//            if ($total_results % $_PAGE_SIZE > 0) {
//                $totalPageCount++;
//            }
//            if ($page < $totalPageCount) { // 如果当前页数小于总页数,继续同步
            _xt_report_taobao_page($date, $page + 1);
//            }
        }
    }
}

function _xt_report_taobao_report_save($report) {
    global $wpdb;
    $count = $wpdb->get_var("SELECT COUNT(*) FROM " . XT_TABLE_TAOBAO_REPORT . " WHERE trade_id=$report->trade_id");
    if ($count == 0) {
        $_user_id = 0;
        $_user_name = '';
        $users = xt_report_fanxian_member('taobao', isset($report->outer_code) ? $report->outer_code : '');
        $buyer = $users['buyer'];
        $sharer = $users['sharer'];
        $adser = $users['adser'];
        if ($buyer->exists()) {
            $_user_id = $buyer->ID;
            $_user_name = $buyer->user_login;
        }
        $wpdb->insert(XT_TABLE_TAOBAO_REPORT, array(
            'trade_id' => $report->trade_id,
            'trade_parent_id' => $report->trade_parent_id,
            'real_pay_fee' => isset($report->real_pay_fee) ? $report->real_pay_fee : '',
            'commission_rate' => isset($report->commission_rate) ? $report->commission_rate : '',
            'commission' => isset($report->commission) ? $report->commission : '',
            'app_key' => isset($report->app_key) ? $report->app_key : '',
            'outer_code' => isset($report->outer_code) ? $report->outer_code : '',
            'pay_time' => isset($report->pay_time) ? $report->pay_time : '',
            'pay_price' => isset($report->pay_price) ? $report->pay_price : '',
            'num_iid' => isset($report->num_iid) ? $report->num_iid : '',
            'item_title' => isset($report->item_title) ? $report->item_title : '',
            'item_num' => isset($report->item_num) ? $report->item_num : '',
            'category_id' => isset($report->category_id) ? $report->category_id : '',
            'category_name' => isset($report->category_name) ? $report->category_name : '',
            'shop_title' => isset($report->shop_title) ? $report->shop_title : '',
            'seller_nick' => isset($report->seller_nick) ? $report->seller_nick : '',
            'user_id' => $_user_id,
            'user_name' => $_user_name
        ));
        global $xt_report_insert;
        $xt_report_insert++;
        if (xt_is_fanxian()) {
            xt_report_fanxian_save('taobao', $buyer, $sharer, $adser, $report->trade_id, $report->commission, $report->pay_time);
        }
    }
}

if (!function_exists("xt_report_fanxian_member")) {

    function xt_report_fanxian_member($platform, $outer_code) {
        if (!empty($outer_code)) {
            if (strpos($outer_code, XT_FANXIAN_PRE, 0) === 0) {
                $outer_code = str_replace(XT_FANXIAN_PRE, '', $outer_code);
                if (preg_match("/^([a-zA-Z]{4})8([a-zA-Z]{4})?/", $outer_code, $guids)) {//share and buy
                    $sharer_id = xt_user_byguid($guids[1]);
                    $buyer_id = 0;
                    $adser_id = 0;
                    if (count($guids) == 3) {
                        $buyer_id = xt_user_byguid($guids[2]);
                        $parents = get_user_meta($buyer_id, XT_USER_PARENT, true);
                        $adser_id = !empty($parents) && isset($parents['id']) ? $parents['id'] : 0;
                    }
                    return array('buyer' => new WP_User($buyer_id), 'sharer' => new WP_User($sharer_id), 'adser' => new WP_User($adser_id));
                } else {
                    $buyer_id = $outer_code;
                    $parents = get_user_meta($buyer_id, XT_USER_PARENT, true);
                    $adser_id = !empty($parents) && isset($parents['id']) ? $parents['id'] : 0;
                    return array('buyer' => new WP_User($buyer_id), 'sharer' => new WP_User(), 'adser' => new WP_User($adser_id));
                }
            }
//		elseif (strpos($report->outer_code, XT_FANXIAN_OLD_PRE, 0) === 0) {
//			$old_user_id = str_replace(XT_FANXIAN_PRE, '', $outer_code);
//			$_oldUsers = get_users(array (
//				'meta_key' => XT_USER_OLD_ID,
//				'meta_value' => $old_user_id
//			));
//			if (!empty ($_oldUsers) && count($_oldUsers) == 1) {
//				return new WP_User($_oldUsers[0]->ID);
//			}
//			return new WP_User();
//		}
        }
        return array('buyer' => new WP_User(), 'sharer' => new WP_User(), 'adser' => new WP_User());
    }

}

function xt_report_fanxian_result($platform, $commission, $buyer, $sharer, $adser) {
    $result = array(
        'isValid' => 0,
        'commission' => $commission,
        'income' => $commission,
        'buy' => array('user_id' => 0, 'user_name' => '', 'rate' => 0, 'cash' => 0, 'jifen' => 0),
        'share' => array('user_id' => 0, 'user_name' => '', 'rate' => 0, 'cash' => 0, 'jifen' => 0),
        'ads' => array('user_id' => 0, 'user_name' => '', 'rate' => 0, 'cash' => 0, 'jifen' => 0)
    );
    $isJifen = xt_fanxian_is_jifenbao($platform);
    if ($isJifen) {
        $result['commission'] = $commission * 100;
    }
    $rate = $share_rate = 0;
    $total = 0;
    if ($sharer->exists()) {
        $result['share']['user_id'] = $sharer->ID;
        $result['share']['user_name'] = $sharer->user_login;
        if (xt_fanxian_is_share()) {
            $share_rate = xt_get_sharerate($sharer->ID);
            if ($share_rate > 0) {
                $fanxian = $jifen = 0;
                if ($isJifen) {
                    $jifen = round(($commission * $share_rate / 100), 2) * 100;
                } else {
                    $fanxian = round(($commission * $share_rate / 100), 2);
                }
                $result['share']['rate'] = $share_rate;
                $result['share']['cash'] = $fanxian;
                $result['share']['jifen'] = $jifen;
                $total+=$share_rate;
            }
        }
    }
    if ($buyer->exists()) {
        $result['buy']['user_id'] = $buyer->ID;
        $result['buy']['user_name'] = $buyer->user_login;
        $rate = xt_get_rate($buyer->ID);
        if ($sharer->exists() && !xt_fanxian_is_sharebuy()) {//share no buy
            $rate = 0;
        }
        if ($rate > 0) {
            $fanxian = $jifen = 0;
            if ($isJifen) {
                $jifen = round(($commission * $rate / 100), 2) * 100;
            } else {
                $fanxian = round(($commission * $rate / 100), 2);
            }
            $result['buy']['rate'] = $rate;
            $result['buy']['cash'] = $fanxian;
            $result['buy']['jifen'] = $jifen;
            $total+=$rate;
        }
        if ($adser->exists()) {
            $result['ads']['user_id'] = $adser->ID;
            $result['ads']['user_name'] = $adser->user_login;
            if (xt_fanxian_is_ad()) {
                $adrate = xt_get_adrate($adser);
                if ($adrate > 0) {
                    $fanxian = $jifen = 0;
                    if ($isJifen) {
                        $jifen = round(($commission * $adrate / 100), 2) * 100;
                    } else {
                        $fanxian = round(($commission * $adrate / 100), 2);
                    }
                    $result['ads']['rate'] = $adrate;
                    $result['ads']['cash'] = $fanxian;
                    $result['ads']['jifen'] = $jifen;
                    $total+=$adrate;
                }
            }
        }
    }
    if ($total <= 90) {
        $result['isValid'] = 1;
        $result['income'] = round(($commission * (90 - $total) / 100), 2);
    }
    return $result;
}

function _xt_report_fanxian_save($cash, $jifen, $buyer_id, $buyer_name, $sharer_id, $sharer_name, $adser_id, $adser_name, $type, $platform, $trade_id, $commission, $pay_time) {
    $user_id = 0;
    $user_name = '';
    switch ($type) {
        case 'BUY':
            $user_id = $buyer_id;
            $user_name = $buyer_name;
            break;
        case 'SHARE':
            $user_id = $sharer_id;
            $user_name = $sharer_name;
            break;
        case 'ADS':
            $user_id = $adser_id;
            $user_name = $adser_name;
            break;
    }
    if ($user_id > 0 && ($cash > 0 || $jifen > 0)) {
        if (xt_new_fanxian(array(
                    'platform' => $platform,
                    'trade_id' => $trade_id,
                    'type' => $type,
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'buy_user_id' => $buyer_id,
                    'buy_user_name' => $buyer_name,
                    'share_user_id' => $sharer_id,
                    'share_user_name' => $sharer_name,
                    'ads_user_id' => $adser_id,
                    'ads_user_name' => $adser_name,
                    'commission' => $commission,
                    'fanxian' => $cash,
                    'jifen' => $jifen,
                    'create_time' => current_time('mysql'),
                    'order_time' => $pay_time
                ))) {
            if (!xt_fanxian_is_pendingtixian() && xt_fanxian_is_autocash()) {//处理现金提现,集分宝提现
                $_cash = 0;
                if ($cash > 0) {
                    $_fanxian = xt_user_total_fanxian($user_id);
                    $_tixians = xt_total_tixian($user_id);
                    $_tixian = $_tixians[0] + $_tixians[1]; //未审核,已完成
                    $_cash = $_fanxian - $_tixian; //余额                    
                }
                if ($_cash == 0 && $jifen > 0) {
                    $_fanxian = xt_user_total_jifen($user_id);
                    $_tixians = xt_total_tixian_jifen($user_id);
                    $_tixian = $_tixians[0] + $_tixians[1]; //未审核,已完成
                    $_cash = $_fanxian - $_tixian; //余额                    
                }
                if ($_cash > 0) {
                    xt_new_tixian(array(
                        'user_id' => $user_id,
                        'cash' => $cash,
                        'jifen' => $jifen
                    ));
                }
            }
        }
    }
}

function xt_report_fanxian_save($platform, $buyer, $sharer, $adser, $trade_id, $commission, $pay_time) {
    global $wpdb;
    $result = xt_report_fanxian_result($platform, $commission, $buyer, $sharer, $adser);
    if ($result['isValid']) {
        $buy = $result['buy'];
        $share = $result['share'];
        $ads = $result['ads'];

        $buyer_id = $buy['user_id'];
        $buyer_name = $buy['user_name'];
        $sharer_id = $share['user_id'];
        $sharer_name = $share['user_name'];
        $adser_id = $ads['user_id'];
        $adser_name = $ads['user_name'];

        if (!empty($buy['user_id'])) {
            $type = 'BUY';
            $cash = $buy['cash'];
            $jifen = $buy['jifen'];
            $_count = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . XT_TABLE_FANXIAN . ' WHERE platform=%s AND trade_id=%d AND type=\'BUY\' AND user_id=%d', $platform, $trade_id, $buyer->ID));
            if ($_count == 0) {
                _xt_report_fanxian_save($cash, $jifen, $buyer_id, $buyer_name, $sharer_id, $sharer_name, $adser_id, $adser_name, $type, $platform, $trade_id, $commission, $pay_time);
            }
            if (!empty($ads['user_id'])) {
                $type = 'ADS';
                $cash = $ads['cash'];
                $jifen = $ads['jifen'];
                _xt_report_fanxian_save($cash, $jifen, $buyer_id, $buyer_name, $sharer_id, $sharer_name, $adser_id, $adser_name, $type, $platform, $trade_id, $commission, $pay_time);
            }
        }
        if (!empty($share['user_id'])) {
            $type = 'SHARE';
            $cash = $share['cash'];
            $jifen = $share['jifen'];
            $_count = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . XT_TABLE_FANXIAN . ' WHERE platform=%s AND trade_id=%d AND type=\'SHARE\' AND user_id=%d', $platform, $trade_id, $sharer->ID));
            if ($_count == 0) {
                _xt_report_fanxian_save($cash, $jifen, $buyer_id, $buyer_name, $sharer_id, $sharer_name, $adser_id, $adser_name, $type, $platform, $trade_id, $commission, $pay_time);
            } else {
                global $wpdb;
                $wpdb->update(XT_TABLE_FANXIAN, array('buy_user_id' => $buyer_id, 'buy_user_name' => $buyer_name, 'ads_user_id' => $adser_id, 'ads_user_name' => $adser_name), array('platform' => $platform, 'trade_id' => $trade_id, 'user_id' => $sharer->ID, 'type' => 'SHARE'));
            }
        }
    }
}

function xt_report_paipai($start = 0, $end = 0) {
    $xt_report_total = 0;
    $xt_report_insert = 0;
    $app = xt_get_app_paipai();
    if (empty($app) || !$app['isValid'] || empty($app['appKey']) || empty($app['appSecret'])) {
        wp_die('暂不支持拍拍订单获取!');
    }
    if (empty($end)) {
        $end = current_time('mysql');
    } else {
        $end = date('Y-m-d H:i:s', strtotime($end));
    }
    if (empty($start)) {
        $start = date('Y-m-d H:i:s', strtotime(current_time('mysql') . '-1 day'));
    } else {
        $start = date('Y-m-d H:i:s', strtotime($start));
    }
    _xt_report_paipai_page($start, $end);
}

function _xt_report_paipai_page($start, $end, $page = 1) {
    $_PAGE_SIZE = 40;
    $resp = xt_paipaike_report($start, $end, $page, $_PAGE_SIZE);
    if (is_wp_error($resp)) {
        wp_die($resp->get_error_code(), $resp->get_error_message());
    }
    if (isset($resp->EtgReportResult)) {
        if (isset($resp->EtgReportResult->etgReportDatas)) {
            $reports = $resp->EtgReportResult->etgReportDatas;
            foreach ($reports as $report) {
                global $xt_report_total;
                $xt_report_total++;
                _xt_report_paipai_report_save($report);
            }
            $total_results = $resp->totalNum;
            if ($total_results > $_PAGE_SIZE) {
                $totalPageCount = (int) ($total_results / $_PAGE_SIZE);
                if ($total_results % $_PAGE_SIZE > 0) {
                    $totalPageCount++;
                }
                if ($page < $totalPageCount) { // 如果当前页数小于总页数,继续同步
                    _xt_report_paipai_page($start, $end, $page + 1);
                }
            }
        }
    }
}

function _xt_report_paipai_report_save($report) {
    global $wpdb;
    $count = $wpdb->get_var("SELECT COUNT(*) FROM " . XT_TABLE_PAIPAI_REPORT . " WHERE dealId=$report->dealId");
    if ($count == 0) {
        $_user_id = '';
        $_user_name = '';
        $users = xt_report_fanxian_member('paipai', isset($report->outer_code) ? $report->outer_code : '');
        $buyer = $users['buyer'];
        $sharer = $users['sharer'];
        $adser = $users['adser'];
        if ($buyer->exists()) {
            $_user_id = $buyer->ID;
            $_user_name = $buyer->user_login;
        }
        $wpdb->insert(XT_TABLE_PAIPAI_REPORT, array(
            'dealId' => $report->dealId,
            'discount' => $report->discount,
            'careAmount' => isset($report->careAmount) ? $report->careAmount : '',
            'brokeragePrice' => isset($report->brokeragePrice) ? $report->brokeragePrice : '',
            'realCost' => isset($report->realCost) ? $report->realCost : '',
            'bargainState' => isset($report->bargainState) ? $report->bargainState : '',
            'chargeTime' => isset($report->chargeTime) ? gmdate('Y-m-d H:i:s', ($report->chargeTime + (get_option('gmt_offset') * 3600))) : '',
            'commNum' => isset($report->commNum) ? $report->commNum : '',
            'commId' => isset($report->commId) ? $report->commId : '',
            'commName' => isset($report->commName) ? $report->commName : '',
            'classId' => isset($report->classId) ? $report->classId : '',
            'className' => isset($report->className) ? $report->className : '',
            'shopId' => isset($report->shopId) ? $report->shopId : '',
            'shopName' => isset($report->shopName) ? $report->shopName : '',
            'outInfo' => isset($report->outInfo) ? $report->outInfo : '',
            'user_id' => $_user_id,
            'user_name' => $_user_name
        ));
        global $xt_report_insert;
        $xt_report_insert++;
        if ($report->bargainState === 0 && xt_is_fanxian()) { //佣金正常
            xt_report_fanxian_save('paipai', $buyer, $sharer, $adser, $report->dealId, round($report->brokeragePrice / 100, 2), $report->chargeTime);
        }
    }
}

function xt_report_yiqifa($start = '', $end = '') {
    $xt_report_total = 0;
    $xt_report_insert = 0;
    $app = xt_get_app_yiqifa();
    if (empty($app) || empty($app['account']) || empty($app['sid']) || empty($app['syncSecret'])) {
        wp_die('尚未配置亿起发账号,网站主ID,密钥!');
    }
    _xt_report_yiqifa_page($start, $end);
}

function _xt_report_yiqifa_page($start, $end, $page = 1, $action_id = '', $order_no = '', $status = '') {
    $app = xt_get_app_yiqifa();
    if (empty($app) || empty($app['account']) || empty($app['sid']) || empty($app['syncSecret'])) {
        wp_die('尚未配置亿起发账号,网站主ID,密钥!');
    }
    //$url = 'http://o.yiqifa.com/servlet/queryCpsMultiRow?sid=' . urlencode($app['sid']) . '&username=' . urlencode($app['account']) . '&privatekey=' . urlencode($app['syncSecret']) . '&st=' . urlencode($start) . '&ed=' . urlencode($end);
    $url = 'http://o.yiqifa.com/servlet/queryCpsMultiRow?sid=' . $app['sid'] . '&username=' . urlencode($app['account']) . '&privatekey=' . urlencode($app['syncSecret']) . '&ed=' . urlencode($end) . '&st=' . urlencode($start) . '&action_id=' . urlencode($action_id) . '&order_no=' . urlencode($order_no) . '&status=' . urlencode($status);
    $body = (file_get_contents($url));
    if (!empty($body)) {
        $body = xt_iconv($body, 'GBK', 'UTF-8');
        $lines = (explode("\n", $body));
        if (!empty($lines)) {
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $params = explode("||", $line);
                    if (!empty($params) && count($params) > 17) {
                        $yiqifaId = $params[0]; // 亿起发唯一编号
                        $actionId = $params[1]; // 联盟活动编号
                        $sid = $params[2]; // 网营商ID（商城）
                        $wid = $params[3]; // 网站编号
                        $orderTime = $params[4]; // 下单时间
                        $orderNo = $params[5]; // 订单编号
                        $commissionType = $params[6]; //佣金分类
                        $itemId = $params[7]; //商品编号
                        $itemNums = $params[8]; //订单商品件数
                        $itemPrice = $params[9]; //订单商品价格
                        $outerCode = $params[10]; //反馈标签（返利标识）
                        $orderStatus = $params[11]; //订单状态
                        $commission = $params[12]; //网站主佣金
                        $cid = $params[13]; //商品分类
                        //14 未知
                        $itemTitle = $params[15]; //商品标题
                        $actionName = $params[16]; //商城活动标题
                        //17 ?时间
                        //18无
                        //19无
                        //20 ?价格
                        //21无
                        //22无
                        global $xt_report_total;
                        $xt_report_total++;
                        global $wpdb;
                        $count = $wpdb->get_var("SELECT COUNT(*) FROM " . XT_TABLE_YIQIFA_REPORT . " WHERE yiqifaId=$yiqifaId");
                        $_user_id = '';
                        $_user_name = '';
                        $users = xt_report_fanxian_member('yiqifa', $outerCode);
                        $buyer = $users['buyer'];
                        $sharer = $users['sharer'];
                        $adser = $users['adser'];
                        if ($buyer->exists()) {
                            $_user_id = $buyer->ID;
                            $_user_name = $buyer->user_login;
                        }
                        if ($count == 0) {
                            $wpdb->insert(XT_TABLE_YIQIFA_REPORT, array(
                                'yiqifaId' => $yiqifaId,
                                'actionId' => $actionId,
                                'actionName' => $actionName,
                                'cid' => $cid,
                                'cname' => $cid,
                                'commission' => $commission,
                                'commissionType' => $commissionType,
                                'itemId' => $itemId,
                                'itemNums' => $itemNums,
                                'itemPrice' => $itemPrice,
                                'itemTitle' => $itemTitle,
                                'orderNo' => $orderNo,
                                'orderStatus' => $orderStatus,
                                'orderTime' => $orderTime,
                                'outerCode' => $outerCode,
                                'sid' => $sid,
                                'wid' => $wid,
                                'user_id' => $_user_id,
                                'user_name' => $_user_name
                            ));
                            global $xt_report_insert;
                            $xt_report_insert++;
                        } else {
                            $wpdb->update(XT_TABLE_YIQIFA_REPORT, array(
                                'actionId' => $actionId,
                                'actionName' => $actionName,
                                'cid' => $cid,
                                'cname' => $cid,
                                'commission' => $commission,
                                'commissionType' => $commissionType,
                                'itemId' => $itemId,
                                'itemNums' => $itemNums,
                                'itemPrice' => $itemPrice,
                                'itemTitle' => $itemTitle,
                                'orderNo' => $orderNo,
                                'orderStatus' => $orderStatus,
                                'orderTime' => $orderTime,
                                'outerCode' => $outerCode,
                                'sid' => $sid,
                                'wid' => $wid
                                    ), array(
                                'yiqifaId' => $yiqifaId
                            ));
                        }

                        if ($orderStatus == 'A' && xt_is_fanxian()) { //订单状态已确认
                            xt_report_fanxian_save('yiqifa', $buyer, $sharer, $adser, $yiqifaId, $commission, $orderTime);
                        }
                    }
                }
            }
        }
    }
}

function xt_report_yiqifa_status($status) {
    switch ($status) {
        case 'R' :
            return '未确认';
            break;
        case 'A' :
            return '成功订单';
            break;
        case 'F' :
            return '无效订单';
            break;
    }
    return '';
}