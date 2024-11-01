<?php

function xt_fanxian_type_desc($type) {
    switch ($type) {
        case 'BUY':
            return '购买返现';
            break;
        case 'ADS':
            return '推广返现';
            break;
        case 'SHARE':
            return '分享返现';
            break;
        case 'REGISTE':
            return '注册赠送';
            break;
    }
    return '未知';
}

function xt_fanxian_is_jifenbao($platform, $mall = '') {
    if ($platform == 'taobao') {
        return true;
    } elseif ($platform == 'yiqifa') {
        $malls = array();
        if (isset($malls[$mall])) {
            return true;
        }
    }
    return false;
}

function xt_row_tixian($tixian, $count, $type = "BUY") {
    ?>
    <tr id="tixian-<?php echo $tixian->id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td>
            <span>
                <?php echo xt_row_tixian_status($tixian->status); ?>
                <?php if ($tixian->status == 0): ?>(<a href="javascript:;" class="status" data-id="<?php echo $tixian->id; ?>" data-account="<?php echo esc_html($tixian->alipay) ?>" data-account-name="<?php echo esc_html($tixian->alipay_name) ?>" data-max="<?php echo $tixian->cash ? $tixian->cash : 0; ?>" data-max-jifen="<?php echo $tixian->jifen ? $tixian->jifen : 0; ?>">支付</a>)<?php endif; ?>
            </span>
        </td>
        <td>
            <span><a href="http://<?php echo add_query_arg(array('user_id' => $tixian->user_id, 'paged' => 1, 's' => '', 'status' => -1), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"><?php echo $tixian->user_login; ?></a></span>
        </td>
        <td>
            <p>现金： <span><?php echo $tixian->cash; ?></p>
            <p><?php echo xt_jifenbao_text(); ?>： <span><?php echo intval($tixian->jifen); ?></p>
        </td>
        <td>
            <p>现金： <span><?php echo $tixian->freeze; ?></p>
            <p><?php echo xt_jifenbao_text(); ?>： <span><?php echo intval($tixian->freeze_jifen); ?></p>
        </td>

        <td>
            <p>支付宝：<?php echo $tixian->alipay ?></p>
            <p>实名：<?php echo $tixian->alipay_name ?></p>
        </td>
        <td>
            <p>邮箱：<?php echo $tixian->user_email ?></p>
            <p>Q&nbsp;Q：<?php echo $tixian->qq ?></p>
            <p>手机：<?php echo $tixian->mobile ?></p>
        </td>
        <td>
            <p>创建时间：<?php echo $tixian->create_time ?></p>
            <?php if (!empty($tixian->opertor)): ?>
                <p>操作员：<?php echo $tixian->opertor ?></p>
                <p>操作时间：<?php echo $tixian->update_time ?></p>
            <?php endif; ?>
            <?php if (!empty($tixian->content)): ?><p>备注：<?php echo $tixian->content ?></p><?php endif; ?>
        </td>
    </tr>
    <?php
}

function xt_row_tixian_status($status) {
    switch ($status) {
        case 0 :
            return '待支付';
            break;
        case 1 :
            return '已支付';
            break;
    }
    return '未知';
}

function xt_row_fanxian($fanxian, $count, $type = "BUY") {

    if ($type == 'BUY'):
        $_url = 'http://' . add_query_arg(array('user_id' => $fanxian->user_id, 'buy_user_id' => '', 'paged' => 1, 's' => '', 'platform' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        ?>
        <tr id="fanxian-<?php echo $fanxian->trade_id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
            <td><span><?php echo $fanxian->platform; ?></span></td>
            <td><span><?php echo $fanxian->trade_id; ?></span></td>
            <td><a href="<?php echo $_url ?>"><?php echo $fanxian->user_name; ?></a></td>
            <td><?php echo $fanxian->fanxian ?></td>
            <td><?php echo $fanxian->jifen ?></td>
            <td><?php echo $fanxian->create_time ?></td>
            <td><?php echo $fanxian->content ?></td>
        </tr>
        <?php
    elseif ($type == 'ADS' || $type == 'SHARE') :
        $_url = 'http://' . add_query_arg(array('user_id' => $fanxian->user_id, 'share_user_id' => '', 'buy_user_id' => '', 'paged' => 1, 's' => '', 'platform' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $_ads_buy_url = 'http://' . add_query_arg(array('user_id' => '', 'share_user_id' => '', 'buy_user_id' => $fanxian->buy_user_id, 'paged' => 1, 's' => '', 'platform' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $_ads_share_url = 'http://' . add_query_arg(array('user_id' => '', 'share_user_id' => $fanxian->share_user_id, 'buy_user_id' => '', 'paged' => 1, 's' => '', 'platform' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        ?>
        <tr id="fanxian-<?php echo $fanxian->yiqifaId; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
            <td><span><?php echo $fanxian->platform; ?></span></td>
            <td><span><?php echo $fanxian->trade_id; ?></span></td>
            <td>
                <p>购买会员：<a href="<?php echo $_ads_buy_url ?>"><?php echo $fanxian->buy_user_name; ?></a></p>
                <p>推广会员：<a href="<?php echo $_url ?>"><?php echo $fanxian->user_name; ?></a></p>
                <p>分享会员：<a href="<?php echo $_ads_share_url ?>"><?php echo $fanxian->share_user_name; ?></a></p>
            </td>
            <td><?php echo $fanxian->fanxian ?></td>
            <td><?php echo $fanxian->jifen ?></td>
            <td><?php echo $fanxian->create_time ?></td>
            <td><?php echo $fanxian->content ?></td>
        </tr>
        <?php
    endif;
}

function xt_order_trade_id($type, $order, $echo = true) {
    switch ($type) {
        case 'taobao' :
            $trade_id = $order->trade_id;
            break;
        case 'paipai' :
            $trade_id = $order->dealId;
            break;
        case 'yiqifa' :
            $trade_id = $order->yiqifaId;
            break;
    }
    $trade_id = apply_filters('xt_order_trade_id', $trade_id, $echo);
    if ($echo) {
        echo $trade_id;
    } else {
        return $trade_id;
    }
}

function xt_row_order($order, $count, $type = "taobao") {
    $_url = 'http://' . add_query_arg(array('s' => '', 'paged' => 1, 'user_id' => $order->user_id), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    if ($type == 'taobao')
        :
        ?>
        <tr id="order-<?php echo $order->trade_id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
            <td><span><?php xt_order_trade_id($type, $order); ?></span></td>
            <td><span><?php echo $order->pay_time; ?></span></td>
            <td class="name column-name">
                <p>商品：<a target="_blank" href="http://item.taobao.com/item.htm?id=<?php echo $order->num_iid ?>"><?php echo $order->item_title; ?></a></p>
                <?php if (!empty($order->category_name)): ?><p>分类：<?php echo $order->category_name ?></p><?php endif; ?>
                <?php if (!empty($order->shop_title)): ?><p>店铺：<?php echo $order->shop_title; ?></p><?php endif; ?>
                <?php if (!empty($order->user_name)): ?><p>会员：<a href="<?php echo $_url ?>"><?php echo $order->user_name; ?></a></p><?php endif; ?>
            </td>
            <td><?php echo $order->pay_price ?></td>
            <td><?php echo $order->item_num ?></td>
            <td><?php echo $order->real_pay_fee ?></td>
            <td><?php echo $order->commission_rate * 100 ?>%</td>
            <td><?php echo round($order->commission, 2) ?></td>
            <td style="display:none;"><?php echo $order->app_key ?></td>
            <td style="display:none;"><?php echo $order->outer_code ?></td>
        </tr>
        <?php
    elseif ($type == 'paipai') :
        ?>
        <tr id="order-<?php echo $order->dealId; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
            <td><span><?php xt_order_trade_id($type, $order); ?></span></td>
            <td><span><?php echo $order->chargeTime; ?></span></td>
            <td class="name column-name">
                <p>商品：<a target="_blank" href="http://auction1.paipai.com/<?php echo $order->commId ?>"><?php echo $order->commName; ?></a></p>
                <?php if (!empty($order->className)): ?><p>分类：<?php echo $order->className ?></p><?php endif; ?>
                <?php if (!empty($order->shopId)): ?><p>店铺：<a target="_blank" href="http://shop.paipai.com/<?php echo $order->shopId ?>"><?php echo $order->shopName ?></a></p><?php endif; ?>
                <?php if (!empty($order->user_name)): ?><p>会员：<a href="<?php echo $_url ?>"><?php echo $order->user_name; ?></a></p><?php endif; ?>
            </td>
            <td>¥<?php echo round($order->careAmount / 100, 2) ?></td>
            <td><?php echo $order->commNum ?></td>
            <td><?php echo round($order->discount / 100, 2) ?>%</td>
            <td>¥<?php echo round($order->brokeragePrice / 100, 2) ?></td>
            <td>¥<?php echo round($order->realCost / 100, 2) ?></td>
            <td><?php echo $order->outInfo ?></td>
            <td><?php echo $order->bargainState ? '佣金冻结' : '正常' ?></td>
        </tr>
        <?php
    elseif ($type == 'yiqifa') :
        ?>
        <tr id="order-<?php echo $order->yiqifaId; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
            <td><span><?php xt_order_trade_id($type, $order); ?></span></td>
            <td><span><?php echo $order->orderTime; ?></span></td>
            <td class="name column-name">
                <?php if (!empty($order->itemTitle)): ?><p>商品：<?php echo $order->itemTitle; ?></p><?php endif; ?>
                <?php if (!empty($order->actionName)): ?><p>商城：<?php echo $order->actionName ?></p><?php endif; ?>
                <?php if (!empty($order->user_name)): ?><p>会员：<a href="<?php echo $_url ?>"><?php echo $order->user_name; ?></a></p><?php endif; ?>
            </td>
            <td><?php echo $order->itemPrice ?></td>
            <td><?php echo $order->itemNums ?></td>
            <td><?php echo round($order->commission, 2) ?></td>
            <td><?php echo $order->outerCode ?></td>
            <td><?php echo xt_report_yiqifa_status($order->orderStatus) ?></td>
            <td><?php echo $order->wid ?></td>
        </tr>
        <?php
    endif;
}

function xt_insert_tixian($tixiandata) {
    global $wpdb;
    extract(stripslashes_deep($tixiandata), EXTR_SKIP);

    $data = compact('user_id', 'cash', 'freeze', 'jifen', 'freeze_jifen', 'status', 'account', 'account_name', 'create_time', 'update_time', 'content');
    if ($wpdb->insert(XT_TABLE_TIXIAN, $data)) {
        $id = $wpdb->insert_id;
        return $id;
    }
    return 0;
}

function xt_new_tixian($tixiandata) {
    $tixiandata['user_id'] = (int) $tixiandata['user_id'];
    $user = new WP_User($tixiandata['user_id']);
    $account_field = XT_USER_ALIPAY;
    $account = $user->$account_field;
    $account_name_field = XT_USER_ALIPAY_NAME;
    $account_name = $user->$account_name_field;
    if ($user->exists() && !empty($account) && !empty($account_name)) {
        global $wpdb;
        $old = $wpdb->get_row('SELECT * FROM ' . XT_TABLE_TIXIAN . ' WHERE user_id=' . $user->ID . ' AND status=0'); //审核中
        if (!empty($old)) { //累加到未审核的
            return $wpdb->update(XT_TABLE_TIXIAN, array(
                        'cash' => (float) $old->cash + (float) $tixiandata['cash'],
                        'jifen' => intval($old->jifen) + intval($tixiandata['jifen'])
                            ), array(
                        'id' => $old->id
                    ));
        } else {
            $cashback = (int) xt_fanxian_cashback();
            if (((float) $tixiandata['cash'] + intval($tixiandata['jifen']) * 100) < $cashback) {
                return 0;
            }
            $tixiandata['status'] = 0;
            $tixiandata['cash'] = (float) $tixiandata['cash'];
            $tixiandata['jifen'] = (intval($tixiandata['jifen']));
            $tixiandata['freeze'] = 0;
            $tixiandata['freeze_jifen'] = 0;
            $tixiandata['account'] = $account;
            $tixiandata['account_name'] = $account_name;
            $tixiandata['content'] = isset($tixiandata['content']) ? $tixiandata['content'] : '';
            $tixiandata['create_time'] = current_time('mysql');
            $tixiandata['update_time'] = current_time('mysql');
            return xt_insert_tixian($tixiandata);
        }
    }
}

function xt_insert_fanxian($fanxiandata) {
    global $wpdb;
    extract(stripslashes_deep($fanxiandata), EXTR_SKIP);

    $data = compact('platform', 'trade_id', 'type', 'user_id', 'user_name', 'buy_user_id', 'buy_user_name', 'share_user_id', 'share_user_name', 'ads_user_id', 'ads_user_name', 'commission', 'fanxian', 'jifen', 'create_time', 'order_time');
    if ($wpdb->insert(XT_TABLE_FANXIAN, $data)) {
        //update cash
        xt_update_user_account_counts($user_id, true, true);
        return 1;
    }
    return 0;
}

function xt_new_fanxian($fanxiandata) {
    $fanxiandata['platform'] = $fanxiandata['platform'];
    $fanxiandata['trade_id'] = $fanxiandata['trade_id'];
    $fanxiandata['type'] = $fanxiandata['type'];
    $fanxiandata['user_id'] = $fanxiandata['user_id'];
    $fanxiandata['user_name'] = $fanxiandata['user_name'];
    $fanxiandata['buy_user_id'] = isset($fanxiandata['buy_user_id']) ? $fanxiandata['buy_user_id'] : '';
    $fanxiandata['buy_user_name'] = isset($fanxiandata['buy_user_name']) ? $fanxiandata['buy_user_name'] : '';
    $fanxiandata['share_user_id'] = isset($fanxiandata['share_user_id']) ? $fanxiandata['share_user_id'] : '';
    $fanxiandata['share_user_name'] = isset($fanxiandata['share_user_name']) ? $fanxiandata['share_user_name'] : '';
    $fanxiandata['ads_user_id'] = isset($fanxiandata['ads_user_id']) ? $fanxiandata['ads_user_id'] : '';
    $fanxiandata['ads_user_name'] = isset($fanxiandata['ads_user_name']) ? $fanxiandata['ads_user_name'] : '';
    $fanxiandata['commission'] = isset($fanxiandata['commission']) ? $fanxiandata['commission'] : '';
    $fanxiandata['fanxian'] = $fanxiandata['fanxian'];
    $fanxiandata['jifen'] = $fanxiandata['jifen'];
    $fanxiandata['create_time'] = current_time('mysql');
    $fanxiandata['order_time'] = $fanxiandata['order_time'];

    return xt_insert_fanxian($fanxiandata);
}

function xt_user_total_fanxian($user_id) {
    global $wpdb;
//    $fanxian = 0.00;
//    $user = wp_get_current_user();
//    if ($user->exists()) {
//        if ($user_id != $user->ID) {
//            if (!current_user_can('manage_options')) {
//                return $fanxian;
//            }
//        }
    $fanxian = $wpdb->get_var($wpdb->prepare('SELECT ROUND(SUM(fanxian),2) AS fanxian FROM ' . XT_TABLE_FANXIAN . ' WHERE user_id=%d', $user_id));
    if (empty($fanxian)) {
        $fanxian = 0.00;
    }
//    }
    return $fanxian;
}

function xt_total_trade($platform = 'taobao') {
    global $wpdb;
    switch ($platform) {
        case 'taobao' :
            $result = $wpdb->get_row('SELECT ROUND(SUM(real_pay_fee),2) AS trade,ROUND(SUM(commission),2) AS commission FROM ' . XT_TABLE_TAOBAO_REPORT, ARRAY_A);
            break;
        case 'paipai' :
            $result = $wpdb->get_row('SELECT ROUND(SUM(careAmount),2) AS trade,ROUND(SUM(brokeragePrice),2) AS commission FROM ' . XT_TABLE_PAIPAI_REPORT . ' WHERE bargainState=0', ARRAY_A);
            break;
        case 'yiqifa' :
            $result = $wpdb->get_row('SELECT ROUND(SUM(itemPrice*itemNums),2) AS trade,ROUND(SUM(commission),2) AS commission FROM ' . XT_TABLE_YIQIFA_REPORT, ARRAY_A);
            break;
    }
    $_result = array(
        'trade' => 0.00,
        'commission' => 0.00
    );
    if (!empty($result)) {
        $_result['trade'] = $result['trade'] > 0 ? $result['trade'] : 0.00;
        $_result['commission'] = $result['commission'] > 0 ? $result['commission'] : 0.00;
    }
    return $_result;
}

function xt_total_fanxian($group = 'platform') {
    global $wpdb;
    $result = $wpdb->get_results("SELECT $group,ROUND(SUM(fanxian),2) AS total,SUM(jifen) AS total_jifen FROM " . XT_TABLE_FANXIAN . " GROUP BY $group", ARRAY_A);
    $_result = array(
        'total' => 0.00,
        'taobao' => 0.00,
        'paipai' => 0.00,
        'yiqifa' => 0.00,
        'xt' => 0.00
    );
    if (!empty($result)) {
        foreach ($result as $r) {
            $_result[$r['platform']] = $r['total'] + round((absint($r['total_jifen'])) / 100, 2);
            if ($r['platform'] != 'xt') {
                $_result['total'] = $_result['total'] + $r['total'] + round((absint($r['total_jifen'])) / 100, 2);
            }
        }
    }
    $_result['total'] = round($_result['total'], 2);
    return $_result;
}

/**
 * 0:等待支付提现,1:成功提现现金,2:冻结
 * @global type $wpdb
 * @param type $user_id
 * @return type
 */
function xt_total_tixian($user_id = 0) {
    global $wpdb;
    $sql = 'SELECT status,ROUND(SUM(cash),2) AS total,ROUND(SUM(freeze),2) AS freeze,SUM(jifen) AS total_jifen,SUM(freeze_jifen) AS freeze_jifen FROM ' . XT_TABLE_TIXIAN . ' GROUP BY status';
    if ($user_id > 0) {
        $sql = $wpdb->prepare('SELECT status,ROUND(SUM(cash),2) AS total,ROUND(SUM(freeze),2) AS freeze,SUM(jifen) AS total_jifen,SUM(freeze_jifen) AS freeze_jifen FROM ' . XT_TABLE_TIXIAN . ' WHERE user_id=%d GROUP BY status', $user_id);
    }
    $result = $wpdb->get_results($sql, ARRAY_A);
    $_result = array(
        0.00,
        0.00,
        0.00
    );
    if (!empty($result)) {
        foreach ($result as $r) {
            $_result[absint($r['status'])] = $r['total'] + round((absint($r['total_jifen'])) / 100, 2);
            if (absint($r['status']) == 1) {
                $_result[2] = $r['freeze'] + round((absint($r['total_freeze'])) / 100, 2); //freeze
            }
        }
    }
    return $_result;
}

/**
 * 0:等待支付提现,1:成功提现现金,2:冻结
 * @global type $wpdb
 * @param type $user_id
 * @return type
 */
function xt_total_tixian_cash($user_id = 0) {
    global $wpdb;
    $sql = 'SELECT status,ROUND(SUM(cash),2) AS total,ROUND(SUM(freeze),2) AS freeze FROM ' . XT_TABLE_TIXIAN . ' GROUP BY status';
    if ($user_id > 0) {
        $sql = $wpdb->prepare('SELECT status,ROUND(SUM(cash),2) AS total,ROUND(SUM(freeze),2) AS freeze FROM ' . XT_TABLE_TIXIAN . ' WHERE user_id=%d GROUP BY status', $user_id);
    }
    $result = $wpdb->get_results($sql, ARRAY_A);
    $_result = array(
        0.00,
        0.00,
        0.00
    );
    if (!empty($result)) {
        foreach ($result as $r) {
            $_result[absint($r['status'])] = $r['total'];
            if (absint($r['status']) == 1) {
                $_result[2] = $r['freeze']; //freeze
            }
        }
    }
    return $_result;
}

/**
 * 0:等待支付提现,1:成功提现现金,2:冻结
 * @global type $wpdb
 * @param type $user_id
 * @return type
 */
function xt_total_tixian_jifen($user_id = 0) {
    global $wpdb;
    $sql = 'SELECT status,SUM(jifen) AS total,SUM(freeze_jifen) AS freeze FROM ' . XT_TABLE_TIXIAN . ' GROUP BY status';
    if ($user_id > 0) {
        $sql = $wpdb->prepare('SELECT status,SUM(jifen) AS total,SUM(freeze_jifen) AS freeze FROM ' . XT_TABLE_TIXIAN . ' WHERE user_id=%d GROUP BY status', $user_id);
    }
    $result = $wpdb->get_results($sql, ARRAY_A);
    $_result = array(
        0,
        0,
        0
    );
    if (!empty($result)) {
        foreach ($result as $r) {
            $_result[absint($r['status'])] = $r['total'];
            if (absint($r['status']) == 1) {
                $_result[2] = $r['freeze']; //freeze
            }
        }
    }
    return $_result;
}

function xt_total_tixian_count() {
    global $wpdb;
    $result = $wpdb->get_results('SELECT status,count(id) AS total FROM ' . XT_TABLE_TIXIAN . ' GROUP BY status', ARRAY_A);
    $_result = array(
        0,
        0,
    );
    if (!empty($result)) {
        foreach ($result as $r) {
            $_result[absint($r['status'])] = $r['total'];
        }
    }
    return $_result;
}

function xt_orders_pagination_links() {
    echo xt_get_orders_pagination_links();
}

function xt_get_orders_pagination_links() {
    global $xt_order_query;
    return apply_filters('xt_orders_pagination_links', $xt_order_query->paginate_links);
}

function xt_orders_paging_text() {
    echo xt_get_orders_paging_text();
}

function xt_get_orders_paging_text() {
    global $xt_order_query;
    return apply_filters('xt_orders_paging_text', $xt_order_query->paging_text);
}

function & query_orders($args = '') {
    unset($GLOBALS['xt_order_query']);
    $GLOBALS['xt_order_query'] = new XT_Order_Query();
    $_result = $GLOBALS['xt_order_query']->query($args);
    return $_result;
}

class XT_Order_Query {

    var $orders;
    var $order_count = 0;
    var $found_orders = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $order;
    var $paginate_links = '';
    var $paging_text = '';

    function init() {
        unset($this->orders);
        $this->order_count = 0;
        $this->found_orders = 0;
        $this->current_order = -1;
        $this->in_the_loop = false;

        unset($this->order);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb;

        $this->init();

        $defaults = array(
            'page' => 1,
            'order_per_page' => 15,
            'user_id' => '',
            'isUnorder' => '',
            'sd' => '',
            'ed' => '',
            'type' => 'taobao',
            's' => ''
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);

        do_action_ref_array('xt_pre_get_orders', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $table = '';
        $date_field = '';
        $code_field = '';
        $trade_field = '';
        $title_field = '';
        $shop_field = '';
        $seller_field = '';

        $page = absint($page);
        $order_per_page = absint($order_per_page);
        if ($page == 0) {
            $page = 1;
        }
        if ($order_per_page == 0) {
            $order_per_page = 15;
        }
        switch ($type) {
            case 'paipai' :
                $table = XT_TABLE_PAIPAI_REPORT;
                $date_field = $table . '.chargeTime';
                $code_field = $table . '.outInfo';
                $trade_field = $table . '.dealId';
                $title_field = $table . '.commName';
                $shop_field = $table . '.shopName';
                break;
            case 'yiqifa' :
                $table = XT_TABLE_YIQIFA_REPORT;
                $date_field = $table . '.orderTime';
                $code_field = $table . '.outerCode';
                $trade_field = $table . '.yiqifaId';
                $title_field = $table . '.itemTitle';
                $shop_field = $table . '.actionName';
                break;
            default :
                $table = XT_TABLE_TAOBAO_REPORT;
                $date_field = $table . '.pay_time';
                $code_field = $table . '.outer_code';
                $trade_field = $table . '.trade_id';
                $title_field = $table . '.item_title';
                $shop_field = $table . '.shop_title';
                $seller_field = $table . '.seller_nick';
                break;
        }

        $fields = $table . '.*,fanxian.fanxian AS cash ,fanxian.jifen AS jifen ';
        $join = ' LEFT JOIN ' . XT_TABLE_FANXIAN . ' AS fanxian ON fanxian.type=\'BUY\' AND platform=\'' . $type . '\' AND fanxian.trade_id=' . $trade_field . ' ';
        $where = '';

        if (!empty($user_id)) {
            if ($user_id != get_current_user_id() && !current_user_can('manage_options')) {
                return array(
                    'orders' => array(),
                    'total' => 0
                );
            }
            $where .= $wpdb->prepare(' AND  ' . $table . '.user_id = %d ', $user_id);
        } elseif ($isUnorder) {
            $fields = $table . '.*';
            $join = '';
            $where .= ' AND  ' . $table . '.user_id = 0 ';
        } elseif (empty($user_id) && !current_user_can('manage_options')) {
            return array(
                'orders' => array(),
                'total' => 0
            );
        }
        if ($page && $order_per_page)
            $limits = $wpdb->prepare("LIMIT %d, %d", ($page - 1) * $order_per_page, $order_per_page);
        else {
            $limits = '';
        }

        if (!empty($sd))
            $where .= $wpdb->prepare(' AND date(' . $date_field . ') >= %s ', $sd);
        if (!empty($ed))
            $where .= $wpdb->prepare(' AND date(' . $date_field . ') <= %s ', $ed);

        $search = trim(trim($s), '*');
        if (!empty($search)) {
            $search_columns = array();
            if (!empty($search)) {
                if (is_numeric($search))
                    $search_columns = array($trade_field);
                else
                    $search_columns = array($title_field, $shop_field, $seller_field, $table . '.user_name');
                $where.=$this->get_search_sql($search, $search_columns);
            }
        }

        $sql = "SELECT $fields FROM $table $join WHERE 1=1 $where ORDER BY $date_field DESC $limits";
        $paged_orders = $wpdb->get_results($sql);
        $paged_orders = apply_filters_ref_array('xt_the_orders', array(
            $paged_orders,
            & $this
                ));
        $total_sql = "SELECT COUNT(*) FROM $table WHERE 1=1 $where";
        $total_orders = $wpdb->get_var($total_sql);
        unset($sql, $total_sql);

        $this->found_orders = $total_orders;
        $this->orders = $paged_orders;
        $this->order_count = count($paged_orders);

        if ($total_orders > 1 || $page > 1) {
            $total_page = ceil($total_orders / $order_per_page);
            $this->paginate_links = paginate_links(array(
                'base' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_fanxian' ? add_query_arg('paged', '%#%', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) : '#%#%',
                'format' => '',
                'end_size' => 3,
                'total' => $total_page,
                'current' => $page,
                'prev_text' => '上一页',
                'next_text' => '下一页',
                'mid_size' => 1,
                'type' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_fanxian' ? 'plain' : 'list'
                    ));
            $this->paging_text = sprintf('<span class="displaying-num">当前显示 %s&#8211;%s 条，共 %s 条</span>%s', number_format_i18n(($page - 1) * $order_per_page + 1), number_format_i18n(min($page * $order_per_page, $total_orders)), number_format_i18n($total_orders), $this->paging_text);
        }

        return array(
            'orders' => $paged_orders,
            'total' => $total_orders
        );
    }

    function get_search_sql($string, $cols, $equals = array()) {
        $string = esc_sql($string);

        $searches = array();
        $leading_wild = '%';
        $trailing_wild = '%';
        foreach ($cols as $col) {
            if (!empty($col)) {
                if (in_array($col, $equals))
                    $searches[] = "$col = '$string'";
                else
                    $searches[] = "$col LIKE '$leading_wild" . like_escape($string) . "$trailing_wild'";
            }
        }

        return ' AND (' . implode(' OR ', $searches) . ')';
    }

    function next_order() {

        $this->current_order++;

        $this->order = $this->orders[$this->current_order];
        return $this->order;
    }

    function the_order() {
        global $xt_order;
        $this->in_the_loop = true;

        if ($this->current_order == -1) // loop has just started
            do_action_ref_array('xt_order_loop_start', array(
                & $this
            ));

        $xt_order = $this->next_order();
        xt_setup_orderdata($xt_order);
    }

    function have_orders() {
        if ($this->current_order + 1 < $this->order_count) {
            return true;
        } elseif ($this->current_order + 1 == $this->order_count && $this->order_count > 0) {
            do_action_ref_array('xt_order_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_orders();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_orders() {
        $this->current_order = -1;
        if ($this->order_count > 0) {
            $this->order = $this->orders[0];
        }
    }

}

function xt_fanxians_pagination_links() {
    echo xt_get_fanxians_pagination_links();
}

function xt_get_fanxians_pagination_links() {
    global $xt_fanxian_query;
    return apply_filters('xt_fanxians_pagination_links', $xt_fanxian_query->paginate_links);
}

function xt_fanxians_paging_text() {
    echo xt_get_fanxians_paging_text();
}

function xt_get_fanxians_paging_text() {
    global $xt_fanxian_query;
    return apply_filters('xt_fanxians_paging_text', $xt_fanxian_query->paging_text);
}

function & query_fanxians($args = '') {
    unset($GLOBALS['xt_fanxian_query']);
    $GLOBALS['xt_fanxian_query'] = new XT_Fanxian_Query();
    $_result = $GLOBALS['xt_fanxian_query']->query($args);
    return $_result;
}

class XT_Fanxian_Query {

    var $fanxians;
    var $fanxian_count = 0;
    var $found_fanxians = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $fanxian;
    var $paginate_links = '';
    var $paging_text = '';

    function init() {
        unset($this->fanxians);
        $this->fanxian_count = 0;
        $this->found_fanxians = 0;
        $this->current_fanxian = -1;
        $this->in_the_loop = false;

        unset($this->fanxian);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb;

        $this->init();

        $defaults = array(
            'page' => 1,
            'fanxian_per_page' => 15,
            'user_id' => '',
            'buy_user_id' => '',
            'share_user_id' => '',
            'type' => array('BUY'),
            'platform' => '',
            'sd' => '',
            'ed' => '',
            's' => ''
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);
        do_action_ref_array('xt_pre_get_fanxians', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $table = XT_TABLE_FANXIAN;
        $date_field = '';
        $code_field = '';

        $page = absint($page);
        $fanxian_per_page = absint($fanxian_per_page);
        if ($page == 0) {
            $page = 1;
        }
        if ($fanxian_per_page == 0) {
            $fanxian_per_page = 15;
        }

        $fields = '*';
        $where = '';

        if (!empty($user_id)) {
            $where .= $wpdb->prepare(' AND  user_id = %d ', $user_id);
        } if (!empty($buy_user_id)) {
            $where .= $wpdb->prepare(' AND  buy_user_id = %d ', $buy_user_id);
        } if (!empty($share_user_id)) {
            $where .= $wpdb->prepare(' AND  share_user_id = %d ', $share_user_id);
        } elseif (!current_user_can('manage_options')) {
            return array(
                'fanxians' => array(),
                'total' => 0
            );
        }
        if (!empty($type)) {
            if (!is_array($type)) {
                $type = array($type);
            }
            $type_in = array();
            foreach ($type as $_t) {
                if (in_array($_t, array('BUY', 'ADS', 'SHARE')))
                    $type_in[] = " type = '" . $_t . "'";
            }
            $where .= ' AND (' . implode(' OR ', $type_in) . ') ';
        }
        if (!empty($platform) && in_array($platform, array('taobao', 'paipai', 'yiqifa'))) {
            $where .= ' AND platform=\'' . $platform . '\' ';
        }
        if (!empty($s)) {
            $s = trim($s);
            if (is_numeric($s)) {
                $where .= ' AND trade_id = ' . $s . ' ';
            } else {
                $where .= ' AND (user_name LIKE \'%' . $wpdb->escape($s) . '%\' OR buy_user_name LIKE \'%' . $wpdb->escape($s) . '%\' OR share_user_name LIKE \'%' . $wpdb->escape($s) . '%\') ';
            }
        }
        if ($page && $fanxian_per_page)
            $limits = $wpdb->prepare("LIMIT %d, %d", ($page - 1) * $fanxian_per_page, $fanxian_per_page);
        else {
            $limits = '';
        }

        if (!empty($sd))
            $where .= $wpdb->prepare(' AND date(create_time) >= %s ', $sd);
        if (!empty($ed))
            $where .= $wpdb->prepare(' AND date(create_time) <= %s ', $ed);
        $sql = "SELECT $fields FROM $table WHERE 1=1 $where ORDER BY create_time DESC $limits";
        $paged_fanxians = $wpdb->get_results($sql);
        $paged_fanxians = apply_filters_ref_array('xt_the_fanxians', array(
            $paged_fanxians,
            & $this
                ));
        $total_sql = "SELECT COUNT(*) FROM $table WHERE 1=1 $where";
        $total_fanxians = $wpdb->get_var($total_sql);
        unset($sql, $total_sql);

        $this->found_fanxians = $total_fanxians;
        $this->fanxians = $paged_fanxians;
        $this->fanxian_count = count($paged_fanxians);

        $total_page = ceil($total_fanxians / $fanxian_per_page);
        $this->paginate_links = paginate_links(array(
            'base' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_fanxian' ? add_query_arg('paged', '%#%', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) : '#%#%',
            'format' => '',
            'end_size' => 3,
            'total' => $total_page,
            'current' => $page,
            'prev_text' => '上一页',
            'next_text' => '下一页',
            'mid_size' => 1
                ));
        $this->paging_text = sprintf('<span class="displaying-num">当前显示 %s&#8211;%s 条，共 %s 条</span>%s', number_format_i18n(($page - 1) * $fanxian_per_page + 1), number_format_i18n(min($page * $fanxian_per_page, $total_fanxians)), number_format_i18n($total_fanxians), $this->paging_text);

        return array(
            'fanxians' => $paged_fanxians,
            'total' => $total_fanxians
        );
    }

    function next_fanxian() {

        $this->current_fanxian++;

        $this->fanxian = $this->fanxians[$this->current_fanxian];
        return $this->fanxian;
    }

    function the_fanxian() {
        global $xt_fanxian;
        $this->in_the_loop = true;

        if ($this->current_fanxian == -1) // loop has just started
            do_action_ref_array('xt_fanxian_loop_start', array(
                & $this
            ));

        $xt_fanxian = $this->next_fanxian();
        xt_setup_fanxiandata($xt_fanxian);
    }

    function have_fanxians() {
        if ($this->current_fanxian + 1 < $this->fanxian_count) {
            return true;
        } elseif ($this->current_fanxian + 1 == $this->fanxian_count && $this->fanxian_count > 0) {
            do_action_ref_array('xt_fanxian_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_fanxians();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_fanxians() {
        $this->current_fanxian = -1;
        if ($this->fanxian_count > 0) {
            $this->fanxian = $this->fanxians[0];
        }
    }

}

function xt_tixians_pagination_links() {
    echo xt_get_tixians_pagination_links();
}

function xt_get_tixians_pagination_links() {
    global $xt_tixian_query;
    return apply_filters('xt_tixians_pagination_links', $xt_tixian_query->paginate_links);
}

function xt_tixians_paging_text() {
    echo xt_get_tixians_paging_text();
}

function xt_get_tixians_paging_text() {
    global $xt_tixian_query;
    return apply_filters('xt_tixians_paging_text', $xt_tixian_query->paging_text);
}

function & query_tixians($args = '') {
    unset($GLOBALS['xt_tixian_query']);
    $GLOBALS['xt_tixian_query'] = new XT_Tixian_Query();
    $_result = $GLOBALS['xt_tixian_query']->query($args);
    return $_result;
}

class XT_Tixian_Query {

    var $tixians;
    var $tixian_count = 0;
    var $found_tixians = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $tixian;
    var $paginate_links = '';
    var $paging_text = '';

    function init() {
        unset($this->tixians);
        $this->tixian_count = 0;
        $this->found_tixians = 0;
        $this->current_tixian = -1;
        $this->in_the_loop = false;

        unset($this->tixian);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb;

        $this->init();

        $defaults = array(
            'page' => 1,
            'tixian_per_page' => 15,
            'user_id' => '',
            'status' => 0,
            'sd' => '',
            'ed' => '',
            's' => ''
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);
        do_action_ref_array('xt_pre_get_tixians', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $table = XT_TABLE_TIXIAN;
        $fields = XT_TABLE_TIXIAN . ".*,ali.meta_value AS alipay,aliname.meta_value AS alipay_name,q.meta_value AS qq,m.meta_value AS mobile,$wpdb->users.user_login as user_login,$wpdb->users.user_email AS user_email ";
        $join = " INNER JOIN $wpdb->users ON $wpdb->users.ID=" . XT_TABLE_TIXIAN . ".user_id ";
        $join .= " LEFT JOIN $wpdb->usermeta AS ali ON ali.user_id = " . XT_TABLE_TIXIAN . ".user_id AND ali.meta_key='" . XT_USER_ALIPAY . "' ";
        $join .= " LEFT JOIN $wpdb->usermeta AS aliname ON aliname.user_id = " . XT_TABLE_TIXIAN . ".user_id AND aliname.meta_key='" . XT_USER_ALIPAY_NAME . "' ";
        $join .= " LEFT JOIN $wpdb->usermeta AS q ON q.user_id = " . XT_TABLE_TIXIAN . ".user_id AND q.meta_key='" . XT_USER_QQ . "' ";
        $join .= " LEFT JOIN $wpdb->usermeta AS m ON m.user_id = " . XT_TABLE_TIXIAN . ".user_id AND m.meta_key='" . XT_USER_MOBILE . "' ";

        $page = absint($page);
        $tixian_per_page = absint($tixian_per_page);
        if ($page == 0) {
            $page = 1;
        }
        if ($tixian_per_page == 0) {
            $tixian_per_page = 15;
        }

        $where = '';

        if (!empty($user_id)) {
            $where .= $wpdb->prepare(' AND  ' . XT_TABLE_TIXIAN . '.user_id = %d ', $user_id);
        }
        $search = trim(trim($s), '*');
        if (!empty($search)) {
            $search_columns = array();
            if (!empty($search)) {
                $search_columns = array($wpdb->users . '.user_login', $wpdb->users . '.user_nicename', $wpdb->users . '.display_name');
                $where.=$this->get_search_sql($search, $search_columns);
            }
        }
        if ($status != -1) {
            $where .= $wpdb->prepare(' AND  status = %d ', (int) $status);
        }


        if ($page && $tixian_per_page)
            $limits = $wpdb->prepare("LIMIT %d, %d", ($page - 1) * $tixian_per_page, $tixian_per_page);
        else {
            $limits = '';
        }

        if (!empty($sd))
            $where .= $wpdb->prepare(' AND date(create_time) >= %s ', $sd);
        if (!empty($ed))
            $where .= $wpdb->prepare(' AND date(create_time) <= %s ', $ed);
        $sql = "SELECT $fields FROM $table $join WHERE 1=1 $where ORDER BY status ASC,create_time DESC $limits";
        $paged_tixians = $wpdb->get_results($sql);
        $paged_tixians = apply_filters_ref_array('xt_the_tixians', array(
            $paged_tixians,
            & $this
                ));
        $total_sql = "SELECT COUNT(*) FROM $table INNER JOIN $wpdb->users ON $wpdb->users.ID=" . XT_TABLE_TIXIAN . ".user_id WHERE 1=1 $where";
        $total_tixians = $wpdb->get_var($total_sql);
        unset($sql, $total_sql);

        $this->found_tixians = $total_tixians;
        $this->tixians = $paged_tixians;
        $this->tixian_count = count($paged_tixians);

        $total_page = ceil($total_tixians / $tixian_per_page);
        $this->paginate_links = paginate_links(array(
            'base' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_fanxian' ? add_query_arg('paged', '%#%', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) : '#%#%',
            'format' => '',
            'end_size' => 3,
            'total' => $total_page,
            'current' => $page,
            'prev_text' => '上一页',
            'next_text' => '下一页',
            'mid_size' => 1
                ));
        $this->paging_text = sprintf('<span class="displaying-num">当前显示 %s&#8211;%s 条，共 %s 条</span>%s', number_format_i18n(($page - 1) * $tixian_per_page + 1), number_format_i18n(min($page * $tixian_per_page, $total_tixians)), number_format_i18n($total_tixians), $this->paging_text);

        return array(
            'tixians' => $paged_tixians,
            'total' => $total_tixians
        );
    }

    function get_search_sql($string, $cols) {
        $string = esc_sql($string);

        $searches = array();
        $leading_wild = '%';
        $trailing_wild = '%';
        foreach ($cols as $col) {
            $searches[] = "$col LIKE '$leading_wild" . like_escape($string) . "$trailing_wild'";
        }

        return ' AND (' . implode(' OR ', $searches) . ')';
    }

    function next_tixian() {

        $this->current_tixian++;

        $this->tixian = $this->tixians[$this->current_tixian];
        return $this->tixian;
    }

    function the_tixian() {
        global $xt_tixian;
        $this->in_the_loop = true;

        if ($this->current_tixian == -1) // loop has just started
            do_action_ref_array('xt_tixian_loop_start', array(
                & $this
            ));

        $xt_tixian = $this->next_tixian();
        xt_setup_tixiandata($xt_tixian);
    }

    function have_tixians() {
        if ($this->current_tixian + 1 < $this->tixian_count) {
            return true;
        } elseif ($this->current_tixian + 1 == $this->tixian_count && $this->tixian_count > 0) {
            do_action_ref_array('xt_tixian_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_tixians();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_tixians() {
        $this->current_tixian = -1;
        if ($this->tixian_count > 0) {
            $this->tixian = $this->tixians[0];
        }
    }

}