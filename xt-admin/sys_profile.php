<?php
$total_trade_taobao = xt_total_trade();
$total_trade_paipai = xt_total_trade('paipai');
$total_trade_yiqifa = xt_total_trade('yiqifa');
$total_tixian = xt_total_tixian();
$total_fanxian = xt_total_fanxian();
$total_share = xt_total_share();
$total_user = xt_total_user();
$total_album = xt_total_album();
$count_tixian = xt_total_tixian_count();
?>
<style type="text/css">
    .xt-nums{font-family: Arial;font-size:14px;font-weight:bold;color:#F1470B;margin-right:5px;}
    .welcome-panel .welcome-panel-column{width: 20%;min-width: 150px;}
    .welcome-panel h4{margin:5px 0px;}
    .welcome-panel .welcome-panel-column ul{margin:10px 0px;}
    .wp-list-table.widefat th,.wp-list-table.widefat td{text-align:center;border-width:1px;border-left-color: #DFDFDF;border-right-width: 0px;}
    .wp-list-table.widefat td{padding:4px;}
</style>
<div class="widget-liquid-left">
    <div id="widgets-left">
        <div class="welcome-panel" style="margin-top:10px;">
            <div class="welcome-panel-column-container">
                <div class="welcome-panel-column">
                    <h4>
                        <span class="icon16 icon-page"></span> 待办
                    </h4>
                    <ul>
                        <li>未处理的提现：<a href="<?php echo admin_url('admin.php?page=xt_menu_fanxian&xt-action=cash'); ?>" style="text-decoration: none;"><strong class="xt-nums"><?php echo $count_tixian[0] ?></strong>条</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <h2>资产(<small><?php echo xt_jifenbao_text() ?>已经折算成现金</small>)</h2>
        <table class="wp-list-table widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" class="manage-column" style="width: 10%"></th>
                    <th scope="col" class="manage-column" style="width: 18%">交易总额</th>
                    <th scope="col" class="manage-column" style="width: 18%">佣金收入</th>
                    <th scope="col" class="manage-column" style="width: 18%">返现总额</th>
                    <th scope="col" class="manage-column" style="width: 18%">提现总额</th>
                    <th scope="col" class="manage-column" style="width: 18%">站长收入</th>
                </tr>
            </thead>
            <tbody>
                <tr class="" valign="top">
                    <td>淘宝</td>
                    <td><?php echo $total_trade_taobao['trade'] ?></td>
                    <td><?php echo $total_trade_taobao['commission'] ?></td>
                    <td><?php echo $total_fanxian['taobao'] ?></td>
                    <td rowspan="4"></td>
                    <td><?php echo $total_trade_taobao['commission'] - $total_fanxian['taobao'] ?></td>
                </tr>
                <tr class="" valign="top">
                    <td>拍拍</td>
                    <td><?php echo $total_trade_paipai['trade'] ?></td>
                    <td><?php echo $total_trade_paipai['commission'] ?></td>
                    <td><?php echo $total_fanxian['paipai'] ?></td>
                    <td><?php echo $total_trade_paipai['commission'] - $total_fanxian['paipai'] ?></td>
                </tr>
                <tr class="" valign="top">
                    <td>亿起发</td>
                    <td><?php echo $total_trade_yiqifa['trade'] ?></td>
                    <td><?php echo $total_trade_yiqifa['commission'] ?></td>
                    <td><?php echo $total_fanxian['yiqifa'] ?></td>
                    <td><?php echo $total_trade_yiqifa['commission'] - $total_fanxian['yiqifa'] ?></td>
                </tr>
                <tr class="" valign="top">
                    <td>系统赠送</td>
                    <td></td>
                    <td></td>
                    <td><?php echo $total_fanxian['xt'] ?></td>
                    <td></td>
                </tr>
                <tr class="" valign="top">
                    <td>合计</td>
                    <td><strong class="xt-nums"><?php echo $total_trade_taobao['trade'] + $total_trade_paipai['trade'] + $total_trade_yiqifa['trade']; ?></strong></td>
                    <td><strong class="xt-nums"><?php echo $total_trade_taobao['commission'] + $total_trade_paipai['commission'] + $total_trade_yiqifa['commission']; ?></strong></td>
                    <td><strong class="xt-nums"><?php echo $total_fanxian['total'] ?></strong><?php echo $total_fanxian['xt'] > 0 ? ('+' . $total_fanxian['xt']) : ''; ?></td>
                    <td style="border-top-width:0px;"><strong class="xt-nums"><?php echo $total_tixian[1] - $total_tixian[2]; ?></strong></td>
                    <td><strong class="xt-nums"><?php echo $total_trade_taobao['commission'] + $total_trade_paipai['commission'] + $total_trade_yiqifa['commission'] - ($total_tixian[1] - $total_tixian[2]); ?></strong></td>
                </tr>
            </tbody>
        </table>
        <h2>运营</h2>
        <table class="wp-list-table widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" style="width: 10%"></th>
                    <th scope="col" colspan="2" style="width: 20%">会员</th>
                    <th scope="col" colspan="3" style="width: 60%">分享</th>
                    <th scope="col" style="width: 10%">专辑</th>
                </tr>
            </thead>
            <tbody>
                <tr class="" valign="top">
                    <td rowspan="2"></td>
                    <td>注册会员</td>
                    <td>系统会员</td>
                    <td>淘宝</td>
                    <td>拍拍</td>
                    <td>其他</td>
                    <td rowspan="2"></td>
                </tr>
                <tr class="" valign="top">
                    <td><?php echo $total_user['total'] - $total_user['system']; ?></td>
                    <td><?php echo $total_user['system']; ?></td>
                    <td><?php echo $total_share['taobao']; ?></td>
                    <td><?php echo $total_share['paipai']; ?></td>
                    <td><?php echo $total_share['yiqifa']; ?></td>
                </tr>
                <tr class="" valign="top">
                    <td style="border-top-width:0px;">合计</td>
                    <td colspan="2"><strong class="xt-nums"><?php echo $total_user['total']; ?></strong></td>
                    <td colspan="3"><strong class="xt-nums"><?php echo $total_share['total']; ?></strong></td>
                    <td style="border-top-width:0px;"><strong class="xt-nums"><?php echo $total_album; ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>	
</div>
<div class="widget-liquid-right">
    <div id="widgets-right">
        <div id="X_Plugin" class="widgets-holder-wrap">
            <div class="sidebar-name">
                <h3>新淘客平台</h3>
            </div>
            <div class="widgets-sortables ui-sortable" style="min-height: 0px;padding-top:0px;">
                <div class="misc-pub-section">访问<a href="http://plugin.xintaonet.com" target="_blank">新淘客平台</a>(SEO,皮肤,装修)</div>
            </div>
        </div>
        <div id="X_Version" class="widgets-holder-wrap">
            <div class="sidebar-name">
                <h3>版本信息<span id="X_Time" style="font-family: sans-serif;font-size: 12px;"></span>
                    <span><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback" title="" alt=""></span>
                </h3>
            </div>
            <div class="widgets-sortables ui-sortable" style="min-height: 0px;padding-top:0px;">

            </div>
        </div>
        <div id="X_Bulletin" class="widgets-holder-wrap">
            <div class="sidebar-name">
                <h3>官方公告
                    <span><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback" title="" alt=""></span>
                </h3>
            </div>
            <div class="widgets-sortables ui-sortable" style="min-height: 0px;padding-top:0px;">
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<script>
    jQuery(function($){
<?php
$updateUrl = '<a style="font-size:16px;font-weight:bold;color:red;" href="' . admin_url('plugins.php') . '">更新</a>';
if (IS_BAE) {
    $updateUrl = '<a style="font-size:16px;font-weight:bold;color:red;" href="http://plugin.xintaonet.com/help/?id=170" target="_blank">更新</a>';
} elseif (IS_SAE) {
    $updateUrl = '<a style="font-size:16px;font-weight:bold;color:red;" href="http://plugin.xintaonet.com/help/?id=168" target="_blank">更新</a>';
}
?>
        $('#X_Version .ajax-feedback,#X_Bulletin .ajax-feedback').css("visibility", "visible");
        $.getScript("<?php echo XT_VERSION_URL ?>", function(){
            if(typeof(XT_VERSION)!="undefined"){
                if((XT_VERSION.version!="<?php echo XT_VERSION ?>")||(XT_VERSION.dbversion!=<?php echo XT_DB_VERSION ?>)){
                    $('#X_Version .widgets-sortables').append('<div class="misc-pub-section">有新版本【'+XT_VERSION.version+'】可以<?php echo $updateUrl; ?></div>');
                    if(XT_VERSION.readme&&XT_VERSION.readme.length>0){
                        for(var i=0;i<XT_VERSION.readme.length;i++){
                            $('#X_Version .widgets-sortables').append('<div class="misc-pub-section"><label>'+(i+1)+'.</label>'+XT_VERSION.readme[i]+'</div>');
                        }
                    }
                }else{
                    $('#X_Version .widgets-sortables').append('<div class="misc-pub-section">您使用的是最新版本【'+XT_VERSION.version+'】</div>');
                }
                if(XT_VERSION.bulletin&&XT_VERSION.bulletin.length>0){
                    for(var i=0;i<XT_VERSION.bulletin.length;i++){
                        var bulletin = XT_VERSION.bulletin[i];
                        $('#X_Bulletin .widgets-sortables').append('<div class="misc-pub-section"><label>'+bulletin.date+'：</label><a href="'+(bulletin.url?bulletin.url:'javascript:;')+'" target="_blank">'+bulletin.title+'</a></div>');
                    }
                }
            }
            $('#X_Time').html("（检查时间：<?php echo current_time('mysql'); ?>）");
            $('#X_Version .ajax-feedback,#X_Bulletin .ajax-feedback').css("visibility", "hidden");            
        });

    });
</script>