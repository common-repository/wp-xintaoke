<?php

function xt_row_jifenOrder($jifenOrder, $count) {
    $_status = $jifenOrder->status == 0 ? '等待处理' : ($jifenOrder->status == 1 ? '已完成' : '拒绝');
    ?>
    <tr id="jifenOrder-<?php echo $jifenOrder->id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td><span><?php echo $_status; ?><?php echo ($jifenOrder->status == 0 ? ('(<a href="javascript:;" class="order-status" data-id="' . $jifenOrder->id . '">处理</a>)') : ''); ?></span></td>
        <td><span><?php echo $jifenOrder->user_name; ?></span></td>
        <td><span><?php echo $jifenOrder->title; ?></span></td>
        <td><span><?php echo $jifenOrder->num; ?></span></td>
        <td><span><?php echo $jifenOrder->jifen; ?></span></td>
        <td><span><?php echo $jifenOrder->create_time; ?></span></td>
        <td><?php echo $jifenOrder->content; ?></td>
    </tr>
    <?php
}

function xt_row_jifenItem($jifenItem, $count) {
    ?>
    <tr id="jifenItem-<?php echo $jifenItem->id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td>
            <span><?php echo $jifenItem->title; ?></span>
            <div class="row-actions">
                <span class="inline hide-if-no-js"><a href="#" class="editinline">快速编辑</a></span>
            </div>
            <div class="hidden" id="inline_<?php echo $jifenItem->id; ?>">
                <div class="title"><?php echo $jifenItem->title; ?></div>
                <div class="pic"><?php echo $jifenItem->pic; ?></div>
                <div class="jifen"><?php echo $jifenItem->jifen; ?></div>
                <div class="stock"><?php echo $jifenItem->stock; ?></div>
                <div class="userCount"><?php echo $jifenItem->user_count; ?></div>
                <div class="sort"><?php echo $jifenItem->sort; ?></div>
                <div class="content"><?php echo $jifenItem->content; ?></div>
            </div>
        </td>
        <td><span><a href="<?php echo $jifenItem->pic; ?>" target="_blank"><?php echo $jifenItem->pic; ?></a></span></td>
        <td><span><?php echo $jifenItem->jifen; ?></span></td>
        <td><span><?php echo $jifenItem->stock - $jifenItem->buy_count; ?></span></td>
        <td><span><?php echo $jifenItem->user_count; ?></span></td>
        <td><span><?php echo $jifenItem->buy_count; ?></span></td>
        <td><span><?php echo $jifenItem->sort; ?></span></td>
        <td><?php echo $jifenItem->content; ?></td>
    </tr>
    <?php
}

function xt_row_jifen($jifen, $count) {
    ?>
    <tr id="jifen-<?php echo $jifen->id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td><span><?php echo $jifen->user_name; ?></span></td>
        <td><span><?php echo $jifen->jifen; ?></span></td>
        <td><span><?php echo $jifen->create_time; ?></span></td>
        <td><span><?php echo $jifen->action_name; ?></span></td>
        <td><?php echo $jifen->content; ?></td>
    </tr>
    <?php
}

function xt_user_total_jifen($user_id) {
    global $wpdb;
//    $fanxian = 0;
//    $user = wp_get_current_user();
//    if ($user->exists()) {
//        if ($user_id != $user->ID) {
//            if (!current_user_can('manage_options')) {
//                return $fanxian;
//            }
//        }
    $fanxian = $wpdb->get_var($wpdb->prepare('SELECT SUM(jifen) AS jifen FROM ' . XT_TABLE_FANXIAN . ' WHERE user_id=%d', $user_id));
    if (empty($fanxian)) {
        $fanxian = 0;
    }
//    }
    return $fanxian;
}

function xt_user_total_jifen_order($user_id) {
    global $wpdb;
    $sql = 'SELECT status,ROUND(SUM(jifen)) AS jifen FROM ' . XT_TABLE_USER_JIFEN_ORDER . ' GROUP BY status';
    if ($user_id > 0) {
        $sql = $wpdb->prepare('SELECT status,ROUND(SUM(jifen)) AS jifen FROM ' . XT_TABLE_USER_JIFEN_ORDER . ' WHERE user_id=%d GROUP BY status', $user_id);
    }
    $result = $wpdb->get_results($sql, ARRAY_A);
    $_result = array(
        0,
        0,
        0
    );
    if (!empty($result)) {
        foreach ($result as $r) {
            $_result[absint($r['status'])] = $r['jifen'];
        }
    }
    return $_result;
}

function xt_jifenOrders_pagination_links() {
    echo xt_get_jifenOrders_pagination_links();
}

function xt_get_jifenOrders_pagination_links() {
    global $xt_jifenOrder_query;
    return apply_filters('xt_jifenOrders_pagination_links', $xt_jifenOrder_query->paginate_links);
}

function xt_jifenOrders_paging_text() {
    echo xt_get_jifenOrders_paging_text();
}

function xt_get_jifenOrders_paging_text() {
    global $xt_jifenOrder_query;
    return apply_filters('xt_jifenOrders_paging_text', $xt_jifenOrder_query->paging_text);
}

function & query_jifenOrders($args = '') {
    unset($GLOBALS['xt_jifenOrder_query']);
    $GLOBALS['xt_jifenOrder_query'] = new XT_JifenOrder_Query();
    $_result = $GLOBALS['xt_jifenOrder_query']->query($args);
    return $_result;
}

class XT_JifenOrder_Query {

    var $jifenOrders;
    var $jifenOrder_count = 0;
    var $found_jifenOrders = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $jifenOrder;
    var $paginate_links = '';
    var $paging_text = '';

    function init() {
        unset($this->jifenOrders);
        $this->jifenOrder_count = 0;
        $this->found_jifenOrders = 0;
        $this->current_jifenOrder = -1;
        $this->in_the_loop = false;

        unset($this->jifenOrder);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb;

        $this->init();

        $defaults = array(
            'page' => 1,
            'jifenOrder_per_page' => 15,
            'user_id' => '',
            'status' => 0,
            'sd' => '',
            'ed' => ''
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);
        do_action_ref_array('xt_pre_get_jifenOrders', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $table = XT_TABLE_USER_JIFEN_ORDER;
        $fields = XT_TABLE_USER_JIFEN_ORDER . ".*,item.title AS title";
        $join = ' LEFT JOIN ' . XT_TABLE_USER_JIFEN_ITEM . ' AS item ON item.id=' . XT_TABLE_USER_JIFEN_ORDER . '.item_id ';

        $page = absint($page);
        $jifenOrder_per_page = absint($jifenOrder_per_page);
        if ($page == 0) {
            $page = 1;
        }
        if ($jifenOrder_per_page == 0) {
            $jifenOrder_per_page = 15;
        }

        $where = '';

        if (!empty($user_id)) {
            if ($user_id != get_current_user_id() && !current_user_can('manage_options')) {
                return array(
                    'jifenOrders' => array(),
                    'total' => 0
                );
            }
            $where .= $wpdb->prepare(' AND  user_id = %d ', $user_id);
        } elseif (!current_user_can('manage_options')) {
            return array(
                'jifenOrders' => array(),
                'total' => 0
            );
        }

        if ($page && $jifenOrder_per_page)
            $limits = $wpdb->prepare("LIMIT %d, %d", ($page - 1) * $jifenOrder_per_page, $jifenOrder_per_page);
        else {
            $limits = '';
        }

        if (!empty($sd))
            $where .= $wpdb->prepare(' AND date(create_time) >= %s ', $sd);
        if (!empty($ed))
            $where .= $wpdb->prepare(' AND date(create_time) <= %s ', $ed);
        $sql = "SELECT $fields FROM $table $join WHERE 1=1 $where ORDER BY status ASC,create_time DESC $limits";
        $paged_jifenOrders = $wpdb->get_results($sql);
        $paged_jifenOrders = apply_filters_ref_array('xt_the_jifenOrders', array(
            $paged_jifenOrders,
            & $this
                ));
        $total_sql = "SELECT COUNT(*) FROM $table WHERE 1=1 $where";
        $total_jifenOrders = $wpdb->get_var($total_sql);
        unset($sql, $total_sql);

        $this->found_jifenOrders = $total_jifenOrders;
        $this->jifenOrders = $paged_jifenOrders;
        $this->jifenOrder_count = count($paged_jifenOrders);

        $total_page = ceil($total_jifenOrders / $jifenOrder_per_page);
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
        $this->paging_text = sprintf('<span class="displaying-num">当前显示 %s&#8211;%s 条，共 %s 条</span>%s', number_format_i18n(($page - 1) * $jifenOrder_per_page + 1), number_format_i18n(min($page * $jifenOrder_per_page, $total_jifenOrders)), number_format_i18n($total_jifenOrders), $this->paging_text);

        return array(
            'jifenOrders' => $paged_jifenOrders,
            'total' => $total_jifenOrders
        );
    }

    function next_jifenOrder() {

        $this->current_jifenOrder++;

        $this->jifenOrder = $this->jifenOrders[$this->current_jifenOrder];
        return $this->jifenOrder;
    }

    function the_jifenOrder() {
        global $xt_jifenOrder;
        $this->in_the_loop = true;

        if ($this->current_jifenOrder == -1) // loop has just started
            do_action_ref_array('xt_jifenOrder_loop_start', array(
                & $this
            ));

        $xt_jifenOrder = $this->next_jifenOrder();
        xt_setup_jifenOrderdata($xt_jifenOrder);
    }

    function have_jifenOrders() {
        if ($this->current_jifenOrder + 1 < $this->jifenOrder_count) {
            return true;
        } elseif ($this->current_jifenOrder + 1 == $this->jifenOrder_count && $this->jifenOrder_count > 0) {
            do_action_ref_array('xt_jifenOrder_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_jifenOrders();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_jifenOrders() {
        $this->current_jifenOrder = -1;
        if ($this->jifenOrder_count > 0) {
            $this->jifenOrder = $this->jifenOrders[0];
        }
    }

}

function xt_jifenItems_pagination_links() {
    echo xt_get_jifenItems_pagination_links();
}

function xt_get_jifenItems_pagination_links() {
    global $xt_jifenItem_query;
    return apply_filters('xt_jifenItems_pagination_links', $xt_jifenItem_query->paginate_links);
}

function xt_jifenItems_paging_text() {
    echo xt_get_jifenItems_paging_text();
}

function xt_get_jifenItems_paging_text() {
    global $xt_jifenItem_query;
    return apply_filters('xt_jifenItems_paging_text', $xt_jifenItem_query->paging_text);
}

function & query_jifenItems($args = '') {
    unset($GLOBALS['xt_jifenItem_query']);
    $GLOBALS['xt_jifenItem_query'] = new XT_JifenItem_Query();
    $_result = $GLOBALS['xt_jifenItem_query']->query($args);
    return $_result;
}

class XT_JifenItem_Query {

    var $jifenItems;
    var $jifenItem_count = 0;
    var $found_jifenItems = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $jifenItem;
    var $paginate_links = '';
    var $paging_text = '';

    function init() {
        unset($this->jifenItems);
        $this->jifenItem_count = 0;
        $this->found_jifenItems = 0;
        $this->current_jifenItem = -1;
        $this->in_the_loop = false;

        unset($this->jifenItem);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb;

        $this->init();

        $defaults = array(
            'page' => 1,
            'jifenItem_per_page' => 15
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);
        do_action_ref_array('xt_pre_get_jifenItems', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $table = XT_TABLE_USER_JIFEN_ITEM;
        $fields = XT_TABLE_USER_JIFEN_ITEM . ".*";
        $join = '';

        $page = absint($page);
        $jifenItem_per_page = absint($jifenItem_per_page);
        if ($page == 0) {
            $page = 1;
        }
        if ($jifenItem_per_page == 0) {
            $jifenItem_per_page = 15;
        }

        $where = '';

        if ($page && $jifenItem_per_page)
            $limits = $wpdb->prepare("LIMIT %d, %d", ($page - 1) * $jifenItem_per_page, $jifenItem_per_page);
        else {
            $limits = '';
        }

        $sql = "SELECT $fields FROM $table $join WHERE 1=1 $where ORDER BY create_time DESC $limits";
        $paged_jifenItems = $wpdb->get_results($sql);
        $paged_jifenItems = apply_filters_ref_array('xt_the_jifenItems', array(
            $paged_jifenItems,
            & $this
                ));
        $total_sql = "SELECT COUNT(*) FROM $table WHERE 1=1 $where";
        $total_jifenItems = $wpdb->get_var($total_sql);
        unset($sql, $total_sql);

        $this->found_jifenItems = $total_jifenItems;
        $this->jifenItems = $paged_jifenItems;
        $this->jifenItem_count = count($paged_jifenItems);

        $total_page = ceil($total_jifenItems / $jifenItem_per_page);
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
        $this->paging_text = sprintf('<span class="displaying-num">当前显示 %s&#8211;%s 条，共 %s 条</span>%s', number_format_i18n(($page - 1) * $jifenItem_per_page + 1), number_format_i18n(min($page * $jifenItem_per_page, $total_jifenItems)), number_format_i18n($total_jifenItems), $this->paging_text);

        return array(
            'jifenItems' => $paged_jifenItems,
            'total' => $total_jifenItems
        );
    }

    function next_jifenItem() {

        $this->current_jifenItem++;

        $this->jifenItem = $this->jifenItems[$this->current_jifenItem];
        return $this->jifenItem;
    }

    function the_jifenItem() {
        global $xt_jifenItem;
        $this->in_the_loop = true;

        if ($this->current_jifenItem == -1) // loop has just started
            do_action_ref_array('xt_jifenItem_loop_start', array(
                & $this
            ));

        $xt_jifenItem = $this->next_jifenItem();
        xt_setup_jifenItemdata($xt_jifenItem);
    }

    function have_jifenItems() {
        if ($this->current_jifenItem + 1 < $this->jifenItem_count) {
            return true;
        } elseif ($this->current_jifenItem + 1 == $this->jifenItem_count && $this->jifenItem_count > 0) {
            do_action_ref_array('xt_jifenItem_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_jifenItems();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_jifenItems() {
        $this->current_jifenItem = -1;
        if ($this->jifenItem_count > 0) {
            $this->jifenItem = $this->jifenItems[0];
        }
    }

}