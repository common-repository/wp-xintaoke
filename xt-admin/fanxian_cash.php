<?php
wp_enqueue_script('thickbox');
wp_enqueue_style('thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', null, '1.0');
$status = (isset($_GET['status']) ? ($_GET['status']) : 0);

$s = isset($_GET['s']) ? urldecode($_GET['s']) : '';
$user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : '';
$_result = query_tixians(array(
    'tixian_per_page' => 50,
    'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
    'status' => $status,
    's' => $s,
    'user_id' => $user_id
        ));
$_tixians = $_result['tixians'];
?>
<div class="clear" style="margin-top:10px;">
    <ul class="subsubsub">
        <li><a href="http://<?php echo add_query_arg(array('status' => -1, 'paged' => 1, 's' => '', 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $status == -1 ? ' class="current"' : '' ?>>全部</a> |</li>
        <li><a href="http://<?php echo add_query_arg(array('status' => 0, 'paged' => 1, 's' => '', 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $status == 0 ? ' class="current"' : '' ?>>待支付</a> |</li>
        <li><a href="http://<?php echo add_query_arg(array('status' => 1, 'paged' => 1, 's' => '', 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $status == 1 ? ' class="current"' : '' ?>>已支付</a></li>
    </ul>
    <p class="search-box">
        <label class="screen-reader-text" for="filter-search-input">搜索提现记录:</label>
        <input type="search" id="filter-search-input" name="s" value="<?php echo $s; ?>">
        <input type="button" name="" id="filter-search-submit" class="button" value="搜索提现记录">
    </p>
</div>
<div class="tablenav top">
    <div class="tablenav-pages"  style="float:left;">
        <span class="displaying-num">
            <?php
            xt_tixians_paging_text();
            ?>
        </span> 
        <span class="pagination-links">
            <?php
            xt_tixians_pagination_links();
            ?>
        </span>
    </div>
    <br class="clear">
</div>
<table class="wp-list-table widefat fixed tags" cellspacing="0">
    <thead>
        <tr>
            <th class="manage-column" style="width: 100px"><?php xt_admin_help_link('fanxian_cash')?><span>状态</span></th>
            <th class="manage-column" style="width: 100px"><span>会员账号</span></th>
            <th class="manage-column" style="width: 100px"><span>提现(元)</span></th>
            <th class="manage-column" style="width: 100px"><span>冻结(元)</span></th>
            <th class="manage-column" style="width: 200px"><span>支付方式</span></th>
            <th class="manage-column" style="width: 200px"><span>联系方式</span></th>
            <th class="manage-column"><span>备注</span></th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th class="manage-column" style="width: 100px"><span>状态</span></th>
            <th class="manage-column" style="width: 100px"><span>会员账号</span></th>
            <th class="manage-column" style="width: 100px"><span>提现(元)</span></th>
            <th class="manage-column" style="width: 100px"><span>冻结(元)</span></th>
            <th class="manage-column" style="width: 200px"><span>支付方式</span></th>
            <th class="manage-column" style="width: 200px"><span>联系方式</span></th>
            <th class="manage-column"><span>备注</span></th>
        </tr>
    </tfoot>
    <tbody id="the-list" class="list:tag">
        <?php
        $_tixian_count = 0;
        foreach ($_tixians as $tixian) {
            xt_row_tixian($tixian, $_tixian_count);
            $_tixian_count++;
        }
        ?>
    </tbody>
</table>
<div id="X_Tixian_Cash" style="display:none;">
    <p style="text-align:center;">根据实际情况手动转账提现金额给会员!</p>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th style="width:70px;">申请提现</th>
                <td>
                    <p style="margin:0px;padding:0px;">现&nbsp;&nbsp;&nbsp;&nbsp;金：<label id="X_Tixian-Cash-L" style="color:#41830E;font-weight:bold;font-size:16px;"></label>（元）</p>
                    <p style="margin:0px;padding:0px;"><?php echo xt_jifenbao_text(); ?>：<label id="X_Tixian-Jifen-L" style="color:#41830E;font-weight:bold;font-size:16px;"></label>（分）</p>
                    <p style="margin:0px;padding:0px;">共&nbsp;&nbsp;&nbsp;&nbsp;计：<label id="X_Tixian-Total-L" style="color:#41830E;font-weight:bold;font-size:16px;"></label>（元）</p>
                </td>
            </tr>
            <tr valign="top">
                <th style="width:70px;">转账账号</th>
                <td><label id="X_Tixian-Account-L"></label></td>
            </tr>
            <tr valign="top">
                <th style="width:70px;">冻结金额</th>
                <td>
                    <input type="text" id="X_Tixian-Freeze" style="width: 80px;" value="0" data-type="cash">（元）<br>
                    <input type="text" id="X_Tixian-Freeze-Jifen" style="width: 80px;" value="0" data-type="jifen">（<?php echo xt_jifenbao_text(); ?>）
                    <span id="X_Tixian-Freeze-Msg" style="display:none;border-color: #FF8080;background-color: #FFF2F2;border-style: solid;padding: 2px;border-width: 1px;"></span>
                    <br><small>根据实际情况需要,是否冻结部分提现!</small>
                </td>
            </tr>
            <tr valign="top">
                <th style="width:70px;">冻结理由</th>
                <td><textarea rows="3" cols="10" id="X_Tixian-Content" class="large-text code"></textarea></td>
            </tr>
        </tbody>
    </table>
    <p class="submit" style="text-align:center;">
        <input type="button" id="X_Tixian-Submit" name="submit" class="button-primary" value="已手动转账">
        <span><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback"></span>
        <a class="button" href="javascript:;" id="X_Tixian-Cancel">取消</a>
    </p>
</div>	
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        
        $('#filter-search-submit').click(function(){
            var s = $('#filter-search-input').val();
            document.location.href = 'http://<?php echo add_query_arg(array('s' => 'SEARCH', 'paged' => 1, 'status' => -1, 'user_id' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>'.replace('SEARCH',encodeURIComponent(s));
        });
        
        $('.status').click(function() {
            $('#X_Tixian-Cash-L').text($(this).attr('data-max'));
            $('#X_Tixian-Jifen-L').text($(this).attr('data-max-jifen'));
            $('#X_Tixian-Account-L').text($(this).attr('data-account') + '('
                + $(this).attr('data-account-name') + ')');
            var total = (Math.round(parseFloat($(this).attr('data-max'))*100)/100)+parseInt($(this).attr('data-max-jifen'))/100;
            $('#X_Tixian-Total-L').text(total);
            $('#X_Tixian-Submit').attr('data-id', $(this).attr('data-id'))
            .val('已手动转账[' + total + '元]');
            $('#X_Tixian-Freeze,#X_Tixian-Freeze-Jifen').val(0);
            $('#X_Tixian-Content').val('');
            tb_show('确认支付提现',
            '#TB_inline?height=400&width=500&inlineId=X_Tixian_Cash');
        });
        $('#X_Tixian-Freeze,#X_Tixian-Freeze-Jifen').focus(function() {
            xt_tixian_validate($(this));
        }).blur(function() {
            xt_tixian_validate($(this));
        })
        $('#X_Tixian-Cancel').click(function() {
            tb_remove();
            return false;
        })
        $('#X_Tixian-Submit').click(function() {
            if (xt_tixian_validate($('#X_Tixian-Freeze'))&&xt_tixian_validate($('#X_Tixian-Freeze-Jifen'))) {
                xt_tixian_status($(this).attr('data-id'), ($('#X_Tixian-Freeze')
                .val().trim()),($('#X_Tixian-Freeze-Jifen')
                .val().trim()), $('#X_Tixian-Content').val().trim());
            }
        });
        function xt_tixian_validate() {
            $('#X_Tixian-Freeze-Msg').hide();
            var cash = $('#X_Tixian-Freeze').val();
            var jifen = $('#X_Tixian-Freeze-Jifen').val();
            var regex = /^\d*\.?\d*$/;
            if (!regex.test(cash)||!regex.test(jifen)) {
                $('#X_Tixian-Freeze-Msg').show().text('输入金额类型不正确');
                return false;
            }
            var max = $('#X_Tixian-Total-L').text();
            if ((parseFloat(cash)+parseInt(jifen)/100) > parseFloat(max)) {
                $('#X_Tixian-Freeze-Msg').show().text('输入金额超过申请提现金额');
                return false;
            }
            if (cash > 0 || jifen>0) {
                $('#X_Tixian-Submit').val('已手动转账['
                    + (Math.round((parseFloat(max) - parseFloat(cash)-parseInt(jifen)/100)*100)/100) + '元]')
                var content = $('#X_Tixian-Content').val();
                if (!content) {
                    $('#X_Tixian-Freeze-Msg').show().text('冻结大于0时,必须填写冻结理由');
                    return false;
                }
            } else {
                $('#X_Tixian-Submit').val('已手动转账[' + (Math.round(parseFloat(max)*100)/100) + '元]');
            }
            return true;
        }
        function xt_tixian_status(id, freeze,freeze_jifen, content) {
            $.ajax({
                type : "post",
                dataType : "json",
                url : ajaxurl + '?rand=' + Math.random(),
                data : {
                    action : 'xt_admin_ajax_tixian_update',
                    id : id,
                    freeze : freeze,
                    freeze_jifen : freeze_jifen,
                    content : content
                },
                success : function(response) {
                    if (response.code > 0) {
                        alert(response.msg);
                    } else {
                        alert('操作成功');
                        tb_remove();
                        // document.location.href = document.location.href;
                    }
                }
            })
        }
    });
</script>