<?php
$s = isset($_GET['s']) ? urldecode($_GET['s']) : '';
$platform = isset($_GET['platform']) ? ($_GET['platform']) : '';
$user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : '';
$buy_user_id = isset($_GET['buy_user_id']) ? absint($_GET['buy_user_id']) : '';
$share_user_id = isset($_GET['share_user_id']) ? absint($_GET['share_user_id']) : '';
$_result = query_fanxians(array(
    'fanxian_per_page' => 50,
    'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
    'type' => array('ADS', 'SHARE'),
    's' => $s,
    'platform' => $platform,
    'user_id' => $user_id,
    'buy_user_id' => $buy_user_id,
    'share_user_id' => $share_user_id
        ));
$_fanxians = $_result['fanxians'];
?>
<div class="clear" style="margin-top:10px;">
    <ul class="subsubsub">
        <li><a href="http://<?php echo add_query_arg(array('platform' => '', 'paged' => 1, 's' => '', 'buy_user_id' => '', 'share_user_id' => '', 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $platform == '' ? ' class="current"' : '' ?>>全部</a> |</li>
        <li><a href="http://<?php echo add_query_arg(array('platform' => 'taobao', 'paged' => 1, 's' => '', 'buy_user_id' => '', 'share_user_id' => '', 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $platform == 'taobao' ? ' class="current"' : '' ?>>淘宝</a> |</li>
        <li><a href="http://<?php echo add_query_arg(array('platform' => 'paipai', 'paged' => 1, 's' => '', 'buy_user_id' => '', 'share_user_id' => '', 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $platform == 'paipai' ? ' class="current"' : '' ?>>拍拍</a> |</li>
        <li><a href="http://<?php echo add_query_arg(array('platform' => 'yiqifa', 'paged' => 1, 's' => '', 'buy_user_id' => '', 'share_user_id' => '', 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $platform == 'yiqifa' ? ' class="current"' : '' ?>>商城</a></li>
    </ul>
    <p class="search-box">
        <label class="screen-reader-text" for="filter-search-input">搜索推广返现:</label>
        <input type="search" id="filter-search-input" name="s" value="<?php echo $s; ?>">
        <input type="button" name="" id="filter-search-submit" class="button" value="搜索推广返现">
    </p>
</div>
<div class="tablenav top">
    <div class="alignleft actions"></div>
    <div class="tablenav-pages">
        <span class="displaying-num">
            <?php
            xt_fanxians_paging_text();
            ?>
        </span>
        <span class="pagination-links">
            <?php
            xt_fanxians_pagination_links();
            ?>
        </span>
    </div>
    <br class="clear">
</div>
<table class="wp-list-table widefat fixed tags" cellspacing="0">
    <thead>
        <tr>
            <th class="manage-column" style="width: 100px"><?php xt_admin_help_link('fanxian_ads')?><span>平台</span></th>
            <th class="manage-column" style="width: 120px"><span>交易号</span></th>
            <th class="manage-column" style="width: 300px"><span>会员</span></th>
            <th class="manage-column" style="width: 80px"><span>返现(元)</span></th>
            <th class="manage-column" style="width: 80px"><span><?php echo xt_jifenbao_text(); ?></span></th>
            <th class="manage-column" style="width: 120px"><span>生成时间</span></th>
            <th class="manage-column"><span>备注</span></th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th class="manage-column" style="width: 100px"><span>平台</span></th>
            <th class="manage-column" style="width: 120px"><span>交易号</span></th>
            <th class="manage-column" style="width: 300px"><span>会员</span></th>
            <th class="manage-column" style="width: 80px"><span>返现(元)</span></th>
            <th class="manage-column" style="width: 80px"><span><?php echo xt_jifenbao_text(); ?></span></th>
            <th class="manage-column" style="width: 120px"><span>生成时间</span></th>
            <th class="manage-column"><span>备注</span></th>
        </tr>
    </tfoot>
    <tbody id="the-list" class="list:tag">
        <?php
        $_fanxian_count = 0;
        foreach ($_fanxians as $fanxian) {
            xt_row_fanxian($fanxian, $_fanxian_count, 'ADS');
            $_fanxian_count++;
        }
        ?>
    </tbody>
</table>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        $('#filter-search-submit').click(function(){
            var s = $('#filter-search-input').val();
            document.location.href = 'http://<?php echo add_query_arg(array('s' => 'SEARCH', 'paged' => 1, 'platform' => '', 'buy_user_id' => '', 'share_user_id' => '', 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>'.replace('SEARCH',encodeURIComponent(s));
        });
    });	
</script>