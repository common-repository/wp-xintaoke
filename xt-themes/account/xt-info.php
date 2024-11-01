<?php
global $xt_user;

$xt_user = wp_get_current_user();
xt_update_user_account_counts($xt_user->ID);
$fanxian = xt_user_total_fanxian($xt_user->ID);
$jifen = xt_user_total_jifen($xt_user->ID);
//$jifenOrder = xt_user_total_jifen_order($xt_user->ID);
$tixians = xt_total_tixian_cash($xt_user->ID);
$tixians_jifen = xt_total_tixian_jifen($xt_user->ID);
?>
<div id="X_Account-Info" class="xt-account-info">
    <div class="row-fluid">
        <div class="span12" style="margin:0;">
            <p style="font-size:14px;"><b style="font-size:14px;"><?php xt_the_user_title(); ?>，</b> 欢迎您！ <span style="_position: relative;_top: -1px;font-size: 16px;color: #E5E5E5;margin: 0 10px 0 0;">|</span> 特权等级： <span>未开通</span></p>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6" style="margin:0;">
            <div class="well">
                <ul class="unstyled xt-account-cashback">
                    <li class="xt-account-cashback-ico"><span></span><i>还可提现：</i><b><?php echo $fanxian - ($tixians[0] + $tixians[1]); ?></b>元</li>
                    <li><i>成功提现：</i><b><?php echo $tixians[1] - $tixians[2]; ?></b>元</li>
                    <li><i>等待支付提现：</i><b><?php echo $tixians[0]; ?></b>元</li>
                    <li><i>累计获得返现：</i><b><?php echo $fanxian; ?></b>元</li>
                </ul>
                <div style="text-align: center;"><a id="X_Account-Cash-Exchange-Button" href="javascript:void();" class="btn">兑换现金</a></div>
            </div>
        </div>
        <div class="span6" style="margin:0;margin-left: 2%;">
            <div class="well">
                <ul class="unstyled xt-account-jifen">
                    <li class="xt-account-jifen-ico"><span></span><i>可用<?php echo xt_jifenbao_text(); ?>：</i><b><?php echo $jifen - ($tixians_jifen[0] + $tixians_jifen[1]); ?></b>分</li>
                    <li><i>成功兑换：</i><b><?php echo ($tixians_jifen[1] - $tixians_jifen[2]); ?></b>分</li>
                    <li><i>等待支付兑换：</i><b><?php echo $tixians_jifen[0]; ?></b>分</li>
                    <li><i>累计获得<?php echo xt_jifenbao_text(); ?>：</i><b><?php echo $jifen; ?></b>分</li>
                </ul>
                <div style="text-align: center;"><a id="X_Account-Jifen-Exchange-Button" href="javascript:void();" class="btn">兑换<?php echo xt_jifenbao_text(); ?></a></div>
            </div>	
        </div>
    </div>
</div>			