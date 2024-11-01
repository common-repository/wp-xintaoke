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
    'type' => 'yiqifa',
    's' => $s,
    'user_id' => $user_id
        ));
$_orders = $_result['orders'];
?>
<div class="clear" style="margin-top:10px;">
    <p class="search-box">
        <label class="screen-reader-text" for="filter-search-input">搜索商城交易:</label>
        <input type="search" id="filter-search-input" name="s" value="<?php echo $s; ?>">
        <input type="button" name="" id="filter-search-submit" class="button" value="搜索商城交易">
    </p> 
</div>
<div class="tablenav top">
    <div class="alignleft actions">
        <input id="X_Fanxian-Order-Get" type="button" class="button-primary" value="手动获取订单">
        &nbsp;&nbsp;&nbsp;&nbsp;
        <input id="X_Fanxian-Order-Import-Open" type="button" class="button-primary" value="导入商城订单" style="display:none;">
    </div>
    <div class="tablenav-pages">
        <span class="displaying-num">
            <?php
            xt_orders_paging_text();
            ?>
        </span>
        <span class="pagination-links">
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
            <th class="manage-column" style="width: 100px"><?php xt_admin_help_link('fanxian_yiqifa')?><span>交易号</span></th>
            <th class="manage-column" style="width: 120px"><span>交易时间</span></th>
            <th class="manage-column"><span>商品</span></th>
            <th class="manage-column" style="width: 80px"><span>单价</span></th>
            <th class="manage-column" style="width: 50px"><span>数量</span></th>
            <th class="manage-column" style="width: 50px"><span>佣金</span></th>
            <th class="manage-column" style="width: 80px"><span>渠道</span></th>
            <th class="manage-column" style="width: 60px"><span>状态</span></th>
            <th class="manage-column" style="width: 80px"><span>网站编号</span></th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th class="manage-column" style="width: 100px"><span>交易号</span></th>
            <th class="manage-column" style="width: 120px"><span>交易时间</span></th>
            <th class="manage-column"><span>商品</span></th>
            <th class="manage-column" style="width: 80px"><span>单价</span></th>
            <th class="manage-column" style="width: 50px"><span>数量</span></th>
            <th class="manage-column" style="width: 50px"><span>佣金</span></th>
            <th class="manage-column" style="width: 80px"><span>渠道</span></th>
            <th class="manage-column" style="width: 60px"><span>状态</span></th>
            <th class="manage-column" style="width: 80px"><span>网站编号</span></th>
        </tr>
    </tfoot>
    <tbody id="the-list" class="list:tag">
        <?php
        $_order_count = 0;
        foreach ($_orders as $order) {
            xt_row_order($order, $_order_count, 'yiqifa');
            $_order_count++;
        }
        ?>
    </tbody>
</table>
<div id="X_Fanxian-Order-Get-Box" style="display:none;">
    <?php
    if (xt_paipai_is_session_ready())
        : $_currentDate = date('Y-m-d', time());
        ?>
        <p>手动获取订单时间段最多只能选择7天。已经获取了的交易记录将自动忽略。</p>
        <p id="X_Fanxian-Order-Get-Date" style="text-align:center;">
            <input type="text" value="<?php echo date('Y-m-d', strtotime("-6 day")); ?>" id="X_Fanxian-Order-StartDate">&nbsp;至&nbsp;
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
<div id="X_Fanxian-Order-Import-Box" style="display:none;">
    <p>1.进入亿起发导出您的CPS明细报表。</p>
    <p>2.wordpress后台---媒体库---添加,上传您下载的报表csv文件。</p>
    <p>3.wordpress后台---新淘客---返现---商城---导入---选择您刚才上传的csv文件导入。</p>
    <table class="wp-list-table widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th class="manage-column"><span>文件名</span></th>
                <th class="manage-column" style="width: 150px"><span>上传日期</span></th>
                <th class="manage-column" style="width: 100px"><span>操作</span></th>
            </tr>
        </thead>
        <tbody>
            <?php
            query_posts(array('post_type' => 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => array('inherit', 'private'), 'posts_per_page' => 5));
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    ?>
                    <tr><td><?php the_title(); ?></td>
                        <td><?php the_time() ?></td>
                        <td><input data-id="<?php the_ID();?>" type="button" class="X_Import button" value="导入"><span><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback"></span></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td colspan="3" style="text-align:center;"><a class="button" href="<?php echo admin_url('media-new.php'); ?>">上传亿起发CPS订单明细</a></td>
            </tr>
        </tbody>
    </table>
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
        $('#X_Fanxian-Order-Import-Open').click(function(){
            tb_show('导入商城订单','#TB_inline?height=400&width=620&inlineId=X_Fanxian-Order-Import-Box');
            return false;
        });
        $('.X_Import').click(function(){
            var self = $(this);
            if (self.attr('data-valid')) {
                return false;
            }
            self.attr('data-valid', 1);
            var id = self.attr('data-id');
            var $panel = self.parent();
            $('.ajax-feedback',$panel).css('visibility', 'visible');
            $.ajax({
                type : "post",
                dataType : "json",
                url : ajaxurl + '?rand=' + Math.random(),
                data : {
                    action : 'xt_admin_ajax_yiqifa_import_cps',
                    id : id
                },
                success : function(response) {
                    if (response.code > 0) {
                        alert(response.msg);
                    } else {
                        //                        var msg = '成功获取['+response.result.total+']条记录,其中新增['+response.result.insert+']';
                        //                        $('#X_Fanxian-Order-Get-Msg').html(msg).show();
                    }
                    self.removeAttr('data-valid');
                    $('.ajax-feedback',$panel).css('visibility', 'hidden');
                },
                error:function(request, error, status){
                    alert(request.responseText);
                    self.removeAttr('data-valid');
                }
            })
        });
        $('#X_Fanxian-Order-Get-Submit').click(function(){
            if ($(this).attr('data-valid')) {
                return false;
            }
            $(this).attr('data-valid', 1);
            $('#X_Fanxian-Order-Get-Msg').hide();
            var $panel = $(this).parent();
            $('.ajax-feedback',$panel).css('visibility', 'visible');
            $.ajax({
                type : "post",
                dataType : "json",
                url : ajaxurl + '?rand=' + Math.random(),
                data : {
                    action : 'xt_admin_ajax_report_yiqifa',
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