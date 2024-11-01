
<?php
wp_enqueue_script('thickbox');
wp_enqueue_style('thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', null, '1.0');
wp_enqueue_script('jquery-ui-datepicker', XT_CORE_JS_URL . '/datepicker/jquery-ui-datepicker.min.js', array(
    'jquery'
        ), '20121008');
wp_enqueue_style('jquery-ui-core', XT_CORE_JS_URL . '/datepicker/jquery.ui.core.css', false, '20121008');
wp_enqueue_style('jquery-ui-theme', XT_CORE_JS_URL . '/datepicker/jquery.ui.theme.css', false, '20121008');
wp_enqueue_style('jquery-ui-datepicker', XT_CORE_JS_URL . '/datepicker/jquery.ui.datepicker.css', false, '20121008');

$s = isset($_GET['s']) ? urldecode($_GET['s']) : '';
$user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : '';
$_result = query_orders(array(
    'order_per_page' => 50,
    'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
    's' => $s,
    'user_id' => $user_id
        ));
$_orders = $_result['orders'];
?>
<div class="clear" style="margin-top:10px;">
    <p class="search-box">
        <label class="screen-reader-text" for="filter-search-input">搜索淘宝交易:</label>
        <input type="search" id="filter-search-input" name="s" value="<?php echo $s; ?>">
        <input type="button" name="" id="filter-search-submit" class="button" value="搜索淘宝交易">
    </p>    
</div>
<div class="tablenav top">
    <div class="alignleft actions">
        <input id="X_Fanxian-Order-Get" type="button" class="button-primary" value="手动获取订单">
    </div>
    <div class="tablenav-pages">
        <span class="displaying-num">
            <?php
            xt_orders_paging_text();
            ?>
        </span> <span class="pagination-links">
            <?php
            xt_orders_pagination_links();
            ?>
        </span>
    </div>
    <br class="clear">
</div>
<table class="wp-list-table widefat fixed tags" cellspacing="0">
    <thead>
        <tr>
            <th class="manage-column" style="width: 120px"><?php xt_admin_help_link('fanxian_taobao')?><span>交易号</span></th>
            <th class="manage-column" style="width: 120px"><span>交易时间</span></th>
            <th class="manage-column"><span>商品</span></th>
            <th class="manage-column" style="width: 60px"><span>单价</span></th>
            <th class="manage-column" style="width: 60px"><span>数量</span></th>
            <th class="manage-column" style="width: 80px"><span>实际付款</span></th>
            <th class="manage-column" style="width: 80px"><span>佣金比例</span></th>
            <th class="manage-column" style="width: 60px"><span>佣金</span></th>
            <th class="manage-column" style="display:none;width: 60px"><span>appKey</span></th>
            <th class="manage-column" style="display:none;width: 80px"><span>渠道</span></th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th class="manage-column" style="width: 120px"><span>交易号</span></th>
            <th class="manage-column" style="width: 120px"><span>交易时间</span></th>
            <th class="manage-column"><span>商品</span></th>
            <th class="manage-column" style="width: 60px"><span>单价</span></th>
            <th class="manage-column" style="width: 60px"><span>数量</span></th>
            <th class="manage-column" style="width: 80px"><span>实际付款</span></th>
            <th class="manage-column" style="width: 80px"><span>佣金比例</span></th>
            <th class="manage-column" style="width: 60px"><span>佣金</span></th>
            <th class="manage-column" style="display:none;width: 60px"><span>appKey</span></th>
            <th class="manage-column" style="display:none;width: 80px"><span>渠道</span></th>
        </tr>
    </tfoot>
    <tbody id="the-list" class="list:tag">
        <?php
        $_order_count = 0;
        foreach ($_orders as $order) {
            xt_row_order($order, $_order_count);
            $_order_count++;
        }
        ?>
    </tbody>
</table>
<div id="X_Fanxian-Order-Get-Box" style="display:none;">
    <?php
    if (xt_taobao_is_session_ready())
        : $_currentDate = date('Y-m-d', time());
        ?>
        <p>手动获取订单时间段最多只能选择30天。并且只能获取最近3个月内的交易记录，已经获取了的交易记录将自动忽略。</p>
        <p id="X_Fanxian-Order-Get-Date" style="text-align:center;">
            <input type="text" value="<?php echo date('Y-m-d', strtotime("-7 day")); ?>" id="X_Fanxian-Order-StartDate">&nbsp;至&nbsp;
            <input type="text" value="<?php echo $_currentDate ?>" id="X_Fanxian-Order-EndDate">
        </p>
        <p style="text-align:center;">
            <input id="X_Fanxian-Order-Get-Submit" type="button" class="button-primary" value="获取交易记录">
            <span><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback"></span>
        </p>
        <p id="X_Fanxian-Order-Get-Msg" style="text-align:center;display:none;color:red;font-size:15px;"></p>
        <?php
    else
        : echo '<p style="text-align:center;padding:25px;"><a style="color:white;" href="' . admin_url('admin.php?page=xt_menu_sys&xt-action=platform') . '" class="button-primary">点击配置淘宝开放平台,并授权</a></p>';
    endif;
    ?>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        
        $('#filter-search-submit').click(function(){
            var s = $('#filter-search-input').val();
            document.location.href = 'http://<?php echo add_query_arg(array('s' => 'SEARCH', 'paged' => 1, 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>'.replace('SEARCH',encodeURIComponent(s));
        });
        
        var start = $('#X_Fanxian-Order-StartDate');
        var end =  $('#X_Fanxian-Order-EndDate');
        $('#X_Fanxian-Order-Get-Date input').datepicker({
            maxDate : new Date(),
            changeMonth : true,
            changeYear : true,
            showOtherMonths : true,
            selectOtherMonths : true,
            dateFormat : "yy-mm-dd",
            monthNamesShort : ["01", "02", "03", "04", "05", "06",
                "07", "08", "09", "10", "11", "12"],
            dayNamesMin : ["日", "一", "二", "三", "四", "五", "六"]
        }).bind("keydown", function() {
            return false
        });
        start.datepicker("option", "maxDate", end.val());
        end.datepicker("option", "minDate", start.val());
        start.datepicker("option", "onSelect", function(f, g) {
            end.datepicker("option", "minDate", f);
            var date = new Date(g.selectedYear, g.selectedMonth, g.selectedDay);
            var max = new Date(date.getTime()+30*86400000);
            end.datepicker('option', 'maxDate', max);
        });
        end.datepicker("option", "onSelect", function(f, g) {
            start.datepicker("option", "maxDate", f);
            var date = new Date(g.selectedYear, g.selectedMonth, g.selectedDay);
            var min = new Date(date.getTime()-30*86400000);
            start.datepicker('option', 'minDate', min);
        });
        $('#X_Fanxian-Order-Get').click(function(){
            tb_show('手动获取订单记录','#TB_inline?height=220&width=420&inlineId=X_Fanxian-Order-Get-Box');
            return false;
        });
        $('#X_Fanxian-Order-Get-Submit').click(function(){
            if ($(this).attr('data-valid')) {
                return false;
            }
            $(this).attr('data-valid', 1);
            $('#X_Fanxian-Order-Get-Msg').hide();
            $panel = $(this).parent();
            $('.ajax-feedback',$panel).css('visibility', 'visible');
            $.ajax({
                type : "post",
                dataType : "json",
                url : ajaxurl + '?rand=' + Math.random(),
                data : {
                    action : 'xt_admin_ajax_report_taobao',
                    start:$('#X_Fanxian-Order-StartDate').val(),
                    end:$('#X_Fanxian-Order-EndDate').val()
                },
                success : function(response) {
                    if (response.code > 0) {
                        alert(response.msg);
                    } else {
                        var msg = '成功获取['+response.result.total+']条记录,其中新增['+response.result.insert+']';
                        $('#X_Fanxian-Order-Get-Msg').html(msg).show();
                    }
                    $('#X_Fanxian-Order-Get-Submit').removeAttr('data-valid');
                    $('.ajax-feedback',$panel).css('visibility', 'hidden');
                },
                error:function(request, error, status){
                    alert(request.responseText);
                    $('#X_Fanxian-Order-Get-Submit').removeAttr('data-valid');
                }
            })
        });
    });	
</script>