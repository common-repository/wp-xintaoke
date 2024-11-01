<?php
$_url = isset($_POST['url']) ? $_POST['url'] : '';
$item = array();
$msg = '';
if (!empty($_url)) {
    $result = xt_share_fetch($_url, -1);
    if ($result['code'] == 0) {
        $item = $result['result'];
    } else {
        $msg = $result['msg'];
    }
}
if (!empty($item)) {
    $clickUrl = xt_jump_url(array('id' => get_the_share_key($item['share_key']), 'type' => $item['from_type']));
    if ($item['from_type'] == 'taobao') {
        ?>
        <div class="row-fluid">
            <div class="span9">
                <div class="pull-left" style="width:300px;overflow:hidden">
                    <a href="<?php echo $clickUrl; ?>" target="_blank">
                        <img src="<?php echo xt_pic_url($item['pic_url'], 300, $item['from_type']) ?>"/>
                    </a>
                </div>
                <div class="pull-left" style="margin-left: 20px;width:380px;overflow: hidden;">
                    <h4 style="margin-top:0px;"><a id="X_Item-Get-Title" href="<?php echo $clickUrl; ?>" target="_blank"><?php echo $item['title'] ?></a></h4>
                    <table class="table">
                        <tbody>
                            <?php
                            if (isset($item['nick']) && !empty($item['nick'])) {
                                ?>
                                <tr><th>店&nbsp;掌&nbsp;柜：</th><td><a id="X_Item-Get-Seller" href="javascript:;" target="_blank"><?php echo $item['nick']; ?></a></td></tr>
                                <?php
                            } else {
                                ?>
                                <tr class="hide"><th>店&nbsp;掌&nbsp;柜：</th><td></td></tr>
                                <?php
                            }
                            ?>
                            <?php
                            if (isset($item['location']) && !empty($item['location'])) {
                                ?>
                                <tr><th>所&nbsp;在&nbsp;地：</th><td><?php echo $item['location']; ?></td></tr>
                                <?php
                            } else {
                                ?>
                                <tr class="hide"><th>所&nbsp;在&nbsp;地：</th><td></td></tr>
                                <?php
                            }
                            ?>
                            <?php
                            if (isset($item['volume']) && !empty($item['volume'])) {
                                ?>
                                <tr><th id="X_Item-Get-Volume-Title">销&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：</th><td id="X_Item-Get-Volume"><?php echo $item['volume']; ?></td></tr>
                                <?php
                            } else {
                                ?>
                                <tr id="X_Item-Get-Volume-Title" class="hide"><th>销&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：</th><td id="X_Item-Get-Volume"></td></tr>
                                <?php
                            }
                            ?>
                            <tr class="xt-price"><th>价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格：</th><td><b>￥</b><strong id="X_Item-Get-Price"><?php echo $item['price']; ?></strong></td></tr>
                            <tr class="xt-fanxian"><th>返&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;利：</th><td id="X_Item-Get-Fanxian" data-type="<?php echo xt_fanxian_is_jifenbao($item['from_type']) ? 'jifenbao' : 'cash' ?>"></td></tr>
                            <tr class="xt-btns"><td colspan="2"><a href="<?php echo $clickUrl ?>" target="_blank" class="btn btn-primary btn-large">立刻购买</a>&nbsp;&nbsp;&nbsp;&nbsp;<button id="X_Item-Get-Publish-Btn" data-url="http://item.taobao.com/item.htm?id=<?php echo get_the_share_key($item['share_key']); ?>" type="button" class="btn btn-success btn-large X_Publish">我要分享</button></td></tr>
                        </tbody>
                    </table>
                </div>            
            </div>
            <div class="span3">
                <div id="X_Item-Get-Recommend" class="xt-widget-recommend-side clearfix" data-platform="<?php echo $item['from_type']; ?>" data-cid="<?php echo $item['cat'] ?>" data-id="<?php echo get_the_share_key($item['share_key']); ?>" style="height:288px;padding-top:7px;">
                    <div class="hd"><h4><span>猜你喜欢</span></h4></div>
                    <div class="bd">
                        <ul class="media-list">
                        </ul>               
                    </div>
                </div>
            </div>
        </div>
        <?php
    } elseif ($item['from_type'] == 'paipai') {
        ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="pull-left" style="width:300px;overflow:hidden">
                    <a href="<?php echo $clickUrl; ?>" target="_blank">
                        <img src="<?php echo xt_pic_url($item['pic_url'], 300, $item['from_type']) ?>"/>
                    </a>
                </div>
                <div class="pull-left" style="margin-left: 20px;width:380px;overflow: hidden;">
                    <h4 style="margin-top:0px;"><a id="X_Item-Get-Title" href="<?php echo $clickUrl; ?>" target="_blank"><?php echo $item['title'] ?></a></h4>
                    <table class="table">
                        <tbody>
                            <?php
                            if (isset($item['nick']) && !empty($item['nick'])) {
                                ?>
                                <tr><th>店&nbsp;掌&nbsp;柜：</th><td><?php echo $item['nick']; ?></td></tr>
                                <?php
                            } else {
                                ?>
                                <tr class="hide"><th>店&nbsp;掌&nbsp;柜：</th><td></td></tr>
                                <?php
                            }
                            ?>
                            <?php
                            if (isset($item['location']) && !empty($item['location'])) {
                                ?>
                                <tr><th>所&nbsp;在&nbsp;地：</th><td><?php echo $item['location']; ?></td></tr>
                                <?php
                            } else {
                                ?>
                                <tr class="hide"><th>所&nbsp;在&nbsp;地：</th><td></td></tr>
                                <?php
                            }
                            ?>
                            <?php
                            if (isset($item['volume']) && !empty($item['volume'])) {
                                ?>
                                <tr><th id="X_Item-Get-Volume-Title">销&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：</th><td id="X_Item-Get-Volume"><?php echo $item['volume']; ?></td></tr>
                                <?php
                            } else {
                                ?>
                                <tr id="X_Item-Get-Volume-Title" class="hide"><th>销&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：</th><td id="X_Item-Get-Volume"></td></tr>
                                <?php
                            }
                            ?>
                            <tr class="xt-price"><th>价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格：</th><td><b>￥</b><strong id="X_Item-Get-Price"><?php echo $item['price']; ?></strong></td></tr>
                            <tr class="xt-fanxian"><th>返&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;利：</th><td id="X_Item-Get-Fanxian" data-type="<?php echo xt_fanxian_is_jifenbao($item['from_type']) ? 'jifenbao' : 'cash' ?>"></td></tr>
                            <tr class="xt-btns"><td colspan="2"><a href="<?php echo $clickUrl ?>" target="_blank" class="btn btn-primary btn-large">立刻购买</a>&nbsp;&nbsp;&nbsp;&nbsp;<button id="X_Item-Get-Publish-Btn" data-url="http://auction1.paipai.com/<?php echo get_the_share_key($item['share_key']); ?>" type="button" class="btn btn-success btn-large X_Publish">我要分享</button></td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="X_Item-Get-Recommend" class="xt-widget-recommend-side clearfix hide" data-platform="<?php echo $item['from_type']; ?>" data-cid="<?php echo $item['cat'] ?>" data-id="<?php echo get_the_share_key($item['share_key']); ?>" style="height:288px;padding-top:7px;">
                    <div class="hd"><h4><span>猜你喜欢</span></h4></div>
                    <div class="bd">
                        <ul class="media-list">
                        </ul>               
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    xt_not_found('“请用淘宝、天猫、聚划算、拍拍的宝贝网址查返利”');
}
?>