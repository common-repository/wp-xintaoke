<?php
$user_id = get_current_user_id();
$taobaos = get_user_meta($user_id, XT_TAOBAO);
$weibos = get_user_meta($user_id, XT_WEIBO);
$qqs = get_user_meta($user_id, XT_QQ);
$taobao = !empty($taobaos) ? $taobaos[0] : array();
$weibo = !empty($weibos) ? $weibos[0] : array();
$qq = !empty($qqs) ? $qqs[0] : array();
$_bind_url = xt_platform_authorize_url('[PLATFORM]', urlencode(xt_site_url('account#bind')), '');
?>
<div id="X_Account-Bind">
    <div class="row-fluid">
        <div class="span10">
            <ul class="media-list">
                <li class="media xt-bind-icon xt-bind-icon-taobao">
                    <a class="pull-left"></a>
                    <div class="media-body">
                        <?php if (!empty($taobao)): ?>
                            <h5 class="media-heading">你已绑定淘宝帐号：<b class="text-success"><?php echo $taobao['nick'] ?></b></h5>
                            <div class="media">
                                <?php if ($taobao['buyer_credit']->level > 2): ?>
                                    已通过买家认证<a target="_blank" class="xt-v" href="javascript:;"><img title="买家认证" src="<?php echo XT_CORE_IMAGES_URL; ?>/buyer_s.png" class="xt-v"></a>
                                <?php else: ?>
                                    <span>未通过买家认证</span>&nbsp;&nbsp;&nbsp;<a href="<?php echo str_replace('[PLATFORM]', 'taobao', $_bind_url) ?>" title="去淘宝认证">再次认证</a>&nbsp;&nbsp;&nbsp;<b style="color:red;">淘宝买家信用必须大于2颗心</b>	
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <h5 class="media-heading">你的淘宝帐号(未绑定)</h5>
                            <div class="media">
                                <a href="<?php echo str_replace('[PLATFORM]', 'taobao', $_bind_url) ?>">去淘宝绑定</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
                <li class="media xt-bind-icon xt-bind-icon-sina">
                    <a class="pull-left"></a>
                    <div class="media-body">
                        <?php if (!empty($weibo)): ?>
                            <h5 class="media-heading">你已绑定新浪微博帐号：<b class="text-success"><?php echo $weibo['screen_name'] ? $weibo['screen_name'] : $weibo['name'] ?></b><a class="xt-unbind-link pull-right" href="javascript:;" data-type="sina">取消绑定</a></h5>
                            <?php
                            $settings = get_user_meta($user_id, XT_WEIBO_SETTING);
                            $setting = !empty($settings) ? $settings[0] : array(
                                'share' => 1,
                                'album' => 1,
                                'topic' => 1
                                    );
                            if (!empty($setting))
                                :
                                ?>
                                <div class="media">
                                    <label class="checkbox inline"><input type="checkbox" <?php echo $setting['share'] ? 'checked' : '' ?> data-value="share">分享</label>
                                    <label class="checkbox inline"><input type="checkbox" <?php echo $setting['album'] ? 'checked' : '' ?> data-value="album">专辑</label> 
                                    <label class="checkbox inline"><input type="checkbox" <?php echo $setting['topic'] ? 'checked' : '' ?> data-value="topic">主题</label> 
                                    &nbsp;&nbsp;&nbsp;<label class="checkbox inline"><a href="javascript:;" data-type="sina" class="xt-account-bind-setting"><b>保存同步设置</b><i></i></a></label>
                                </div>
                            <?php endif;
                        else:
                            ?>
                            <h5 class="media-heading">你的新浪微博帐号(未绑定)</h5>
                            <div class="media">
                                <a href="<?php echo str_replace('[PLATFORM]', 'weibo', $_bind_url) ?>">绑定并关注我们</a>
                            </div>
<?php endif; ?>
                    </div>
                </li>
                <li class="media xt-bind-icon xt-bind-icon-qq">
                    <a class="pull-left"></a>
                    <div class="media-body">
                        <?php if (!empty($qq)): ?>
                            <h5 class="media-heading">你已绑定QQ空间帐号：<b class="text-success"><?php echo $qq['nickname'] ?></b><a class="xt-unbind-link pull-right" href="javascript:;" data-type="qq">取消绑定</a></h5>
                            <?php
                            $settings = get_user_meta($user_id, XT_QQ_SETTING);
                            $setting = !empty($settings) ? $settings[0] : array(
                                'share' => 1,
                                'album' => 1,
                                'topic' => 1
                                    );
                            if (!empty($setting))
                                :
                                ?>
                                <div class="media">
                                    <label class="checkbox inline"><input type="checkbox" <?php echo $setting['share'] ? 'checked' : '' ?> data-value="share">分享</label>
                                    <label class="checkbox inline"><input type="checkbox" <?php echo $setting['album'] ? 'checked' : '' ?> data-value="album">专辑</label>
                                    <label class="checkbox inline"><input type="checkbox" <?php echo $setting['topic'] ? 'checked' : '' ?> data-value="topic">主题</label>
                                    &nbsp;&nbsp;&nbsp;<label class="checkbox inline"><a href="javascript:;" data-type="qq" class="xt-account-bind-setting"><b>保存同步设置</b><i></i></a></label>
                                </div>
                            <?php endif;
                        else:
                            ?>
                            <h5 class="media-heading">你的QQ空间帐号(未绑定)</h5>
                            <div class="media">
                                <a href="<?php echo str_replace('[PLATFORM]', 'qq', $_bind_url) ?>">绑定并关注我们</a>
                            </div>
<?php endif; ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>