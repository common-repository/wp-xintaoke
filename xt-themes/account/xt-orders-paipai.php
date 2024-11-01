<?php
$isUnorder = (isset($_POST['type']) && $_POST['type'] == 'unorder' ? true : false);
$_params = array('type' => 'paipai', 'page' => absint($_POST['page']), 'order_per_page' => 15, 'sd' => $_POST['sd'], 'ed' => $_POST['ed']);
if ($isUnorder) {
    $_params['isUnorder'] = true;
} else {
    $_params['user_id'] = get_current_user_id();
}

$_result = query_orders($_params);

$_orders = $_result['orders'];
$_total = $_result['total'];
?>
<table class="table table-striped table-hover table-bordered">
    <colgroup>
        <col style="width:120px;"/>
        <col/>
        <col style="width:80px;"/>
        <col style="width:50px;"/>
        <col style="width:80px;"/>
        <col style="width:90px;"/>
        <col style="width:140px;"/>
    </colgroup>
    <thead>
        <tr>
            <th>拍拍交易号</th>
            <th>商品名称</th>
            <th>成交价格</th>
            <th>数量</th>
            <th>可返现金</th>
            <th>可返<?php echo xt_jifenbao_text();?></th>
            <th>交易时间</th>
        </tr>
    </thead>	
    <tbody>	
        <?php
        if ($_total > 0) {
            $rate = 0;
            if ($isUnorder) {
                $rate = xt_get_rate(get_current_user_id());
            }
            foreach ($_orders as $_o) {
                $_item_title = '';
                if ($_o->commId) {
                    $_item_title = '<a target="_blank" href="' . xt_jump_url(array('id' => $_o->commId, 'type' => 'paipai')) . '">' . $_o->commName . '</a>';
                } else {
                    $_item_title = $_o->commName;
                }
                $dealId = $isUnorder ? (substr_replace($_o->dealId, str_repeat('*', strlen($_o->dealId) - 2), 1, -1) . '<br><a data-id="' . $_o->id . '" data-platform="paipai" href="javascript:;" class="xt-unorder-open">找回</a>') : $_o->dealId;
                $cash = $jifen = 0;
                $isJifenbao = xt_fanxian_is_jifenbao('paipai');
                if ($isUnorder) {
                    if ($isJifenbao) {
                        $jifen = ($isUnorder ? round(($_o->brokeragePrice / 100 * $rate / 100), 2) : $_o->cash) * 100;
                    } else {
                        $cash = $isUnorder ? round(($_o->brokeragePrice / 100 * $rate / 100), 2) : $_o->cash;
                    }
                } else {
                    $jifen = $_o->jifen;
                    $cash = $_o->cash;
                }
                $price = round($_o->careAmount / 100, 2);
                echo "<tr><td class=\"xt-td-center\">" . $dealId . "</td><td>$_item_title</td><td class=\"xt-td-center\">$price</td><td class=\"xt-td-center\">$_o->commNum</td><td class=\"xt-td-center\">$cash</td><td class=\"xt-td-center\">$jifen</td><td class=\"xt-td-center\">$_o->chargeTime</td></tr>";
            }
        }
        ?>

    </tbody>
</table>
<?php if ($_total == 0) { ?>
    <div class="well xt-noresult">
        <div>暂无交易记录 | 暂无符合查询条件的订单信息</div>
    </div>
    <?php
} else {
    echo '<div id="X_Pagination-Bottom" class="clearfix">';
    xt_orders_paging_text();
    echo '<div class="pagination xt-pagination-links xt-account-pagination-links">';
    xt_orders_pagination_links();
    echo '</div>';
    echo '</div>';
    echo '</div>';
}?>