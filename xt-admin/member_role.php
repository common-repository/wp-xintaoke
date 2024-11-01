
<style type="text/css">
    .widefat tbody th.check-column{padding-bottom:2px;}.inline-edit-row .error{color:red;}
    .form-wrap .form-field{margin:0px;padding-top: 0px;}
</style>
<?php
global $wp_roles;
$_roles = $wp_roles->role_objects;
?>
<div id="col-container">
    <div id="col-right" style="padding-top:10px;">
        <table class="wp-list-table widefat fixed roles" cellspacing="0">
            <thead>
                <tr>
                    <th class="manage-column" style="width: 100px"><span>类型</span></th>
                    <th class="manage-column" style="width: 100px"><span>英文</span></th>
                    <th class="manage-column"><span>中文</span></th>
                    <th class="manage-column" style="width: 100px"><span>返现比例</span></th>
                    <th class="manage-column" style="width: 100px"><span>分享比例</span></th>                    
                    <th class="manage-column" style="width: 100px"><span>推广比例</span></th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th class="manage-column" style="width: 100px"><span>类型</span></th>
                    <th class="manage-column" style="width: 100px"><span>英文</span></th>
                    <th class="manage-column"><span>中文</span></th>
                    <th class="manage-column" style="width: 100px"><span>返现比例</span></th>
                    <th class="manage-column" style="width: 100px"><span>分享比例</span></th>                    
                    <th class="manage-column" style="width: 100px"><span>推广比例</span></th>
                </tr>
            </tfoot>
            <tbody id="the-list" class="list:role">
                <?php
                $_role_count = 0;
                foreach ($_roles as $_k => $role) {
                    xt_row_role($_k, $role, $_role_count);
                    $_role_count++;
                }
                ?>
            </tbody>
        </table>
        <table style="display: none">
            <tbody>
                <tr id="inline-edit" class="inline-edit-row">
                    <td colspan="4" class="colspanchange">
                        <fieldset>
                            <div class="inline-edit-col">
                                <h4>快速编辑</h4>
                                <label> <span class="title">中文</span> <span class="input-text-wrap"><input type="text" name="title" class="ptitle" value="" /></span></label>
                                <label> <span class="title">返现比例</span> <span class="input-text-wrap"><input name="rate" type="number" step="1" min="0" max="90" value="" class="small-text"/></span><span>留空:表示启用站点默认返现比例(<?php echo xt_fanxian_default_rate(); ?>%)</span></label>
                                <label> <span class="title">分享比例</span> <span class="input-text-wrap"><input name="sharerate" type="number" step="1" min="0" max="90" value="" class="small-text"/></span><span>留空:表示启用站点默认分享比例(<?php echo xt_fanxian_default_sharerate(); ?>%)</span></label>                                                                
                                <label> <span class="title">推广比例</span> <span class="input-text-wrap"><input name="adrate" type="number" step="1" min="0" max="90" value="" class="small-text"/></span><span>留空:表示启用站点默认推广比例(<?php echo xt_fanxian_default_adrate(); ?>%)</span></label>
                            </div>
                        </fieldset>
                        <p class="inline-edit-save submit">
                            <a accesskey="c" href="#inline-edit" title="取消" class="cancel button-secondary alignleft">取消</a> <a accesskey="s" href="#inline-edit" title="更新角色" class="save button-primary alignright">更新角色</a>
                            <img class="waiting" style="display: none;" src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" alt="" /> <span class="error" style="display: none;"></span> <br class="clear" />
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="col-left">
        <div class="col-wrap">
            <div class="form-wrap">
                <h3><?php xt_admin_help_link('member_role')?>添加新角色</h3>
                <form id="addrole" method="post" action="" class="validate">
                    <input type="hidden" name="action" value="add-role">
                    <div class="form-field form-required">
                        <label for="role-name">英文</label> <input name="role-name" id="role-name" type="text" value="" size="40" aria-required="true">
                        <p>角色的英文描述,如:vip(可用拼音),一旦设置,不能修改</p>
                    </div>
                    <div class="form-field form-required">
                        <label for="role-title">中文</label> <input name="role-title" id="role-title" type="text" value="" size="40" aria-required="true">
                        <p>角色的中文描述,如:VIP会员。</p>
                    </div>
                    <div class="form-field">
                        <label for="role-rate">返现比例</label> <input name="role-rate" type="number" step="1" min="0" max="90" id="role-rate" value="" class="small-text">
                        <p>范围0-90。该角色的会员返现比例。留空:表示使用当前站点的默认返现比例(<?php echo xt_fanxian_default_rate(); ?>%)</p>
                    </div>
                    <div class="form-field">
                        <label for="role-sharerate">分享比例</label><input name="role-sharerate" type="number" step="1" min="0" max="90" id="role-sharerate" value="" class="small-text">
                        <p>
                            范围0-90。该角色的会员分享比例。留空:表示使用当前站点的默认分享比例(<?php echo xt_fanxian_default_sharerate(); ?>%)
                            <br>
                        </p>
                    </div>
                    <div class="form-field">
                        <label for="role-adrate">推广比例</label><input name="role-adrate" type="number" step="1" min="0" max="90" id="role-adrate" value="" class="small-text">
                        <p>
                            范围0-90。该角色的会员推广比例。留空:表示使用当前站点的默认推广比例(<?php echo xt_fanxian_default_adrate(); ?>%)
                        </p>
                    </div>
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button" value="添加新角色">
                        <img class="waiting" style="display: none;" src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" alt="" />
                    </p>
                </form>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        var default_rate = <?php echo xt_fanxian_default_rate() ?>;
        var default_sharerate = <?php echo xt_fanxian_default_sharerate() ?>;                
        var default_adrate = <?php echo xt_fanxian_default_adrate() ?>;

        var what = '#role-';
	
        $('#submit').click(function() {
            addRole();
            return false;
        });
        $('a.delete-role').live('click',function() {
            if(confirm('您确认要删除该角色!一旦删除,则该角色下的用户将归属于(角色)订阅者')){
                deleteRole($(this).attr('data-value'));	
            }
            return false;
        });
        $('.editinline').live('click', function() {
            revertRow();
            id = $(this).parents('tr:first').attr('id');
            id = id.substr(id.lastIndexOf('-') + 1);
            var editRow = $('#inline-edit').clone(true);
            var rowData = $('#inline_' + id);
            $('td', editRow).attr('colspan',
            $('.widefat:first thead th:visible').length);
            if ($(what + id).hasClass('alternate'))
                $(editRow).addClass('alternate');
            $(what + id).hide().after(editRow);

            $(':input[name="title"]', editRow).val($('.title', rowData).text());
            var rate = $('.rate', rowData).text();
            $(':input[name="rate"]', editRow).val(default_rate==rate?'':rate);
            
            var sharerate = $('.sharerate', rowData).text();
            $(':input[name="sharerate"]', editRow).val(default_sharerate==sharerate?'':sharerate);		
            
            var adrate = $('.adrate', rowData).text();
            $(':input[name="adrate"]', editRow).val(default_adrate==adrate?'':adrate);
		
            $(editRow).attr('id', 'edit-' + id).addClass('inline-editor').show();
            $('a.cancel', $(editRow)).click(function() {
                revertRow();
                return false;
            });
            $('a.save', $(editRow)).click(function() {
                inline_edit($(editRow));
                return false;
            });

            $('.ptitle', editRow).eq(0).focus();
            return false;
        });
        function revertRow() {
            var id = $('#the-list tr.inline-editor').attr('id');
            if (id) {
                $('#the-list .inline-edit-save .waiting').hide();
                $('#' + id).remove();
                id = id.substr(id.lastIndexOf('-') + 1);
                $(what + id).show();
            }
        }
        function deleteRole(ids) {
            if (ids) {
                var setting = {};
                setting.action = 'xt_admin_ajax_role_delete';
                setting.roles = ids;
                $.ajax({
                    type : "post",
                    dataType : "json",
                    url : ajaxurl + '?rand=' + Math.random(),
                    data : setting,
                    success : function(r) {
                        if (r.code > 0) {
                            alert(r.msg);
                        } else {
                            top.location.reload();// 刷新
                        }
                    }
                })
            }
        }
        function addRole() {
            var form = $('#addrole');
            if (!validateForm(form))
                return false;
            var setting = {};
            setting.title = $('#role-title').val();
            setting.name = $('#role-name').val();
            setting.ismulti = 0;
            setting.rate = $('#role-rate').val();
            setting.sharerate = $('#role-sharerate').val();            
            setting.adrate = $('#role-adrate').val();
            setting.action = 'xt_admin_ajax_role_add';
            $('.waiting', form).show();
            $.ajax({
                type : "post",
                dataType : "html",
                url : ajaxurl + '?rand=' + Math.random(),
                data : setting,
                success : function(r) {
                    if (r == '') {
                        top.location.reload();// 刷新
                    } else {
                        alert(r);
                        $('.waiting', form).hide();
                    }
                }
            })
        }
        function inline_edit(editRow) {
            var id = editRow.attr('id');
            id = id.substr(id.lastIndexOf('-') + 1);
            var title = editRow.find(':input[name="title"]').val();
            var rate = editRow.find(':input[name="rate"]').val();
            var sharerate = editRow.find(':input[name="sharerate"]').val();            
            var adrate = editRow.find(':input[name="adrate"]').val();
            var ismulti = 0;
            var error = editRow.find('.error');
            var waiting = editRow.find('.waiting');
		
            var setting = {};
            setting.action = 'xt_admin_ajax_role_update';

            setting.name = id;
            setting.title = title;
            setting.rate = rate;
            setting.sharerate = sharerate;            
            setting.adrate = adrate;
            setting.ismulti = ismulti;
            setting.alternate = editRow.hasClass('alternate') ? 1 : 0;
            waiting.show();
            error.hide();
            $.ajax({
                type : "post",
                dataType : "html",
                url : ajaxurl + '?rand=' + Math.random(),
                data : setting,
                success : function(r) {
                    var row, new_id;
                    waiting.hide();
                    if (r) {
                        if (-1 != r.indexOf('<tr')) {
                            $(what + id).remove();
                            new_id = $(r).attr('id');

                            $('#edit-' + id).before(r).remove();
                            row = new_id ? $('#' + new_id) : $(what + id);
                            row.hide().fadeIn();
                        } else
                            error.text(r).show();
                    } else
                        error.text('未知错误').show();
                }
            })
        }
    });
</script>