<div id="X_Account-Jifen">
	<div class="row-fluid">
		<?php
$user = wp_get_current_user();
$jifen = xt_user_total_jifen($user->ID);
$jifenOrder = $jifen > 0 ? xt_user_total_jifen_order($user->ID) : array (
	0,
	0,
	0
);
echo '<p style="padding:0 15px;font-size:14px;">您目前累计'.xt_jifenbao_text().'<em style="color: #F90;font-style:normal;">' . $jifen . '</em>分,可用'.xt_jifenbao_text().'<em style="color: #F90;font-style:normal;">' . ($jifen - $jifenOrder[0] - $jifenOrder[1]) . '</em>分。</p>';
?>
<script type="text/javascript">
var JIFEN = <?php echo ($jifen - $jifenOrder[0] - $jifenOrder[1]);?>;
</script>
		<ul class="span12 nav nav-pills" id="X_Account-Jifen-Type" style="margin-left:0px;">
			<li class="active"><a href="javascript:;" class="xt-current" data-value="jifen"><?php echo xt_jifenbao_text();?>记录<span></span></a></li>
			<li><a href="javascript:;" data-value="order">兑换记录<span></span></a></li>
			<li><a href="javascript:;" data-value="item">可兑换的商品<span></span></a></li>
		</ul>
		<div class="span12 xt-account-list" id="X_Account-Jifen-List" style="margin-left:0px;">
		</div>
	</div>
</div>