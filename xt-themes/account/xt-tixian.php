<?php
if (xt_fanxian_is_pendingtixian()) {
    exit('<div class="well">暂停提现,请稍后操作</div>');
}
$user = wp_get_current_user();
if ($user->exists()) {
    $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'cash';
    if (!in_array($type, array('cash', 'jifenbao'))) {
        $type = 'cash';
    }
    $_fanxian = 0;
    $_tixian = 0;
    $_cash = 0;
    $_cash_text = '元';
    if ($type == 'cash') {
        $_fanxian = xt_user_total_fanxian($user->ID);
        $_tixians = xt_total_tixian($user->ID);
        $_tixian = $_tixians[0] + $_tixians[1]; //未审核,已完成
        $_cash = $_fanxian - $_tixian; //余额        
    } else {
        $_cash_text = xt_jifenbao_text();
        $_fanxian = xt_user_total_jifen($user->ID);
        $_tixians = xt_total_tixian_jifen($user->ID);
        $_tixian = $_tixians[0] + $_tixians[1]; //未审核,已完成
        $_cash = $_fanxian - $_tixian; //余额   
    }
    ?>
    <form class="xt-form-tixian">

        <?php
        $account_field = XT_USER_ALIPAY;
        $account = $user->$account_field;
        $account_name_field = XT_USER_ALIPAY_NAME;
        $account_name = $user->$account_name_field;
        if (!empty($account) && !empty($account_name))
            :
            $cashback = (int) xt_fanxian_cashback();
            if ($type == 'jifenbao') {
                $cashback = $cashback * 100;
            }
            ?>
            <div class="form-horizontal">
                <div class="control-group">
                    <label class="control-label">可提现：</label>
                    <div class="controls">
                        <div class="input-append">
                            <span class="input-small uneditable-input"><?php echo round($_cash, 2); ?></span><span class="add-on"><?php echo $_cash_text; ?></span>
                        </div> 
                        <small class="help-inline" style="padding-top: 5px;margin-bottom:5px;">最低提现<b style="color:red;"><?php echo $cashback; ?></b><?php echo $_cash_text; ?></small>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">支付宝：</label>
                    <div class="controls">
                        <span class="input uneditable-input"><?php echo $account . '(' . $account_name . ')' ?></span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">提现数额：</label>
                    <div class="controls">
                        <div class="input-append">
                            <input type="text" id="X_Tixian-Amount" style="width: 80px;" data-min="<?php echo $cashback; ?>" data-max="<?php echo round($_cash, 2); ?>">（<?php echo $_cash_text; ?>）
                            <span class="add-on"><?php echo $_cash_text; ?></span>
                        </div>
                        <span class="help-inline" id="X_AmountMsg">请输入提现数额</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="controls">
                        <button class="btn" data-dismiss="modal">取消</button>
                        <button id="X_Tixian-Submit" class="btn btn-primary" data-type="<?php echo $type; ?>">保存</button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="well well-small" style="text-align:center;">
                <p><label>您当前可提现数额： <span class="badge badge-success"><?php echo round($_cash, 2); ?></span>（<?php echo $_cash_text; ?>）</label></p>
                <p><label>尚未填写支付宝账号,支付宝实名：</label><a href="javascript:;" id="X_Account-Profile-Link" class="btn btn-primary">点击配置</a></p>
            </div>	
        <?php endif; ?>


    </form>
    <?php
} else {
    exit('未登录');
}
?>
