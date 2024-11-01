<?php
$_result = query_fanxians(array('type' => array('ADS', 'SHARE'), 'user_id' => get_current_user_id(), 'page' => absint($_POST['page']), 'fanxian_per_page' => 15, 'sd' => $_POST['sd'], 'ed' => $_POST['ed']));

$_fanxians = $_result['fanxians'];
$_total = $_result['total'];
?>
<table class="table table-striped table-hover table-bordered">
    <colgroup>
        <col style="width:80px;"/>
        <col style="width:80px;"/>
        <col style="width:150px;"/>
        <col style="width:150px;"/>
        <col style="width:150px;"/>
        <col/>
    </colgroup>
    <thead>
        <tr>
            <th>类型</th>
            <th>来源</th>            
            <th>购买会员</th>
            <th>可返金额</th>
            <th>可返<?php echo xt_jifenbao_text();?></th>
            <th>时间</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($_total > 0) {
            foreach ($_fanxians as $_f) {
                $_type = xt_fanxian_type_desc($_f->type);
                $_platform = xt_platforms_desc($_f->platform);
                echo "<tr><td class=\"xt-td-center\">$_type</td><td class=\"xt-td-center\">$_platform</td><td class=\"xt-td-center\">$_f->buy_user_name</td><td class=\"xt-td-center\">$_f->fanxian</td><td class=\"xt-td-center\">$_f->jifen</td><td class=\"xt-td-center\">$_f->create_time</td></tr>";
            }
        }
        ?>

    </tbody>
</table>
<?php if ($_total == 0) { ?>
    <div class="well xt-noresult">
        <div>暂无推广记录 | 暂无符合查询条件的推广信息</div>
    </div>
    <?php
} else {
    echo '<div id="X_Pagination-Bottom" class="clearfix">';
    xt_fanxians_paging_text();
    echo '<div class="pagination xt-pagination-links xt-account-pagination-links">';
    xt_fanxians_pagination_links();
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>				
