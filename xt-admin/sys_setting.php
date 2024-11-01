<?php ?>
<h3><?php xt_admin_help_link('sys_setting_base') ?>基本设置<span><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback"></span></h3>
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row">登录</th>
            <td>
                <input name="isForceLogin" type="checkbox" id="isForceLogin" value="1" <?php echo xt_is_forcelogin() ? 'checked' : ''; ?>>
                <label for="isForceLogin">未登录状态,进入具体网站购买时弹出登录提示框</label>	
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">淘宝</th>
            <td>
                <fieldset>
                    <label for="isS8">
                        <input type="checkbox" name="isS8" id="isS8" value="1" <?php echo xt_is_s8() ? 'checked' : ''; ?>> 
                        搜索框关键词搜索直接跳转至淘宝(未选中则打开站内搜索页，如果您无完整PID权限，建议取消选中)
                    </label>
                    <br/>
                    <label for="isTaobaoPopup">
                        <input type="checkbox" name="isTaobaoPopup" id="isTaobaoPopup" value="1" <?php echo xt_is_taobaoPopup() ? 'checked' : ''; ?>> 
                        搜索框商品地址搜索直接弹出商品详情(未选中则打开访问详情页)
                    </label>
                </fieldset>	
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">分享</th>
            <td>
                <fieldset>
                    <label for="isDisplayComment">
                        <input type="checkbox" name="isDisplayComment" id="isDisplayComment" value="1" <?php echo xt_is_displaycomment() ? 'checked' : ''; ?>> 
                        分享列表页单个分享是否显示最新评论
                    </label>
                    <br>
                </fieldset>	
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">专辑</th>
            <td>
                <fieldset>
                    <label>
                        显示方案&nbsp;&nbsp;<input name="albumDisplay" type="radio" value="big" <?php echo xt_albumdisplay() == 'big' ? 'checked' : ''; ?>>1大图+4小图&nbsp;&nbsp;&nbsp;
                        <input name="albumDisplay" type="radio" value="small" <?php echo xt_albumdisplay() == 'small' ? 'checked' : ''; ?>>9小图
                    </label>
                </fieldset>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">会员</th>
            <td>
                <fieldset>
                    <label for="followLimit">
                        最多关注<input name="followLimit"
                                   type="number" step="1" min="500" max="5000" id="followLimit" value="<?php echo xt_followlimit(); ?>"
                                   class="small-text">个会员
                    </label>
                </fieldset>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">第三方统计代码</th>
            <td>
                <?php $codeAnalytics = xt_code_analytics(); ?>
                <label for="codeAnalytics">如：
                    <a href="http://tongji.baidu.com/" target="_blank">百度统计</a>，
                    <a href="http://linezing.com/" target="_blank">量子恒道</a>，
                    <a href="http://www.51.la/" target="_blank">51啦</a>，
                    <a href="http://www.google.com/intl/zh-cn/analytics/" target="_blank">Google Analytics</a>...</label>	
                <textarea name="codeAnalytics" rows="4" cols="20" id="codeAnalytics" class="large-text code"><?php echo $codeAnalytics ? $codeAnalytics : ''; ?></textarea>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">第三方分享代码</th>
            <td>
                <?php $codeShare = xt_code_share(); ?>
                <label for="codeShare">如：
                    <a href="http://share.baidu.com/" target="_blank">百度分享</a>，
                    <a href="http://www.jiathis.com/" target="_blank">JiaThis分享</a>...(<span style="color:red;">请选择浮窗式，侧栏式，边栏式其中的一种</span>)</label>	
                <textarea name="codeShare" rows="4" cols="20" id="codeShare" class="large-text code"><?php echo $codeShare ? $codeShare : ''; ?></textarea>
                <label>该代码全站有效，如果仅需要在某个页面分享，此处请留空，然后在具体页面装修时，添加文本模块，然后粘贴分享代码。</label>
            </td>
        </tr>
    </tbody>
</table>
<h3><?php xt_admin_help_link('sys_setting_member') ?>会员中心</h3>
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row">公告</th>
            <td>
                <label for="bulletin">在会员中心显示,建议尽量简要描述</label><br>
                <textarea name="bulletin" rows="4" cols="20" id="bulletin" class="large-text code"><?php
                $bulletin = xt_bulletin();
                echo $bulletin ? $bulletin : '';
                ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<h3><?php xt_admin_help_link('sys_setting_fanxian') ?>返现设置(仅启用返现后以下配置才可正式生效)</h3>
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row">返现</th>
            <td>
                <input name="isFanxian" type="checkbox" id="isFanxian" value="1" <?php echo xt_is_fanxian() ? 'checked' : ''; ?>>
                <label for="isFanxian">启用返现功能,支持会员购买,推广返现</label>	
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">提现</th>
            <td>
                <label for="isPendingTixian">
                    <input name="isPendingTixian" type="checkbox" id="isPendingTixian" value="1" <?php echo xt_fanxian_is_pendingtixian() ? 'checked' : ''; ?>>
                    暂停提现
                </label>
                <br>
                <label for="isAutoCash">
                    <input name="isAutoCash" type="checkbox" id="isAutoCash" value="1" <?php echo xt_fanxian_is_autocash() ? 'checked' : ''; ?>>
                    无需会员手动提现,根据返利记录自动汇总并生成提现记录
                </label>	
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">购买返现</th>
            <td>
                <label for="rate_cashback">
                    购买返现比例<input name="rate_cashback"
                                 type="number" step="1" min="0" max="90" id="rate_cashback" value="<?php echo xt_fanxian_default_rate(); ?>"
                                 class="small-text">% 会员购买可以获得的返现比例(返现=佣金x购买返现比例)
                </label>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">分享返现</th>
            <td>
                <label for="isShare">
                    <input name="isShare" type="checkbox" id="isShare" value="1" <?php echo xt_fanxian_is_share() ? 'checked' : ''; ?>>是否启用分享返现
                </label>
                <br>
                <label for="rate_share">
                    分享返现比例<input name="rate_share"
                                 type="number" step="1" min="0" max="90" id="rate_share" value="<?php echo xt_fanxian_default_sharerate(); ?>"
                                 class="small-text">% 启用分享返现后,会员分享的商品被购买可以获得的返现比例(返现=佣金x分享返现比例)
                </label>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">推广返现</th>
            <td>
                <label for="isAd">
                    <input name="isAd" type="checkbox" id="isAd" value="1" <?php echo xt_fanxian_is_ad() ? 'checked' : ''; ?>>是否启用推广返现
                </label>
                <br>
                <label for="rate_ad">
                    推广返现比例<input name="rate_ad"
                                 type="number" step="1" min="0" max="90" id="rate_ad" value="<?php echo xt_fanxian_default_adrate(); ?>"
                                 class="small-text">% 启用推广返现后,会员推广其他人注册购买后可以获得的返现比例(返现=佣金x推广返现比例)
                </label>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">提现(现金或<?php echo xt_jifenbao_text(); ?>)</th>
            <td>
                <label for="cashback">
                    最低<input name="cashback"
                             type="number" step="1" min="0" max="100" id="cashback" value="<?php echo xt_fanxian_cashback(); ?>"
                             class="small-text"> 元(RMB)或同价值的<?php echo xt_jifenbao_text(); ?>
                </label>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">注册</th>
            <td>
                <fieldset>
                    <label for="registe_cash">
                        赠送<input name="registe_cash"
                                 type="number" step="1" min="0" max="50" id="registe_cash" value="<?php echo xt_fanxian_registe_cash(); ?>"
                                 class="small-text"> 元(RMB)
                    </label>
                    <br>
                    <label for="registe_jifen">
                        赠送<input name="registe_jifen"
                                 type="number" step="100" min="0" max="10000" id="registe_jifen" value="<?php echo xt_fanxian_registe_jifen(); ?>"
                                 class="small-text"> 个<?php echo xt_jifenbao_text(); ?>(100<?php echo xt_jifenbao_text(); ?>=1元RMB)
                    </label>
                    <br>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="保存更改"></p>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#submit').click(function() {
            $('.ajax-feedback').css('visibility', 'visible');
            var setting = {};
            setting.action = 'xt_admin_ajax_setting';

            setting.isForceLogin = $('#isForceLogin').is(':checked') ? 1 : 0;
            setting.isS8 = $('#isS8').is(':checked') ? 1: 0;
            setting.isTaobaoPopup = $('#isTaobaoPopup').is(':checked') ? 1: 0;
            setting.isDisplayComment = $('#isDisplayComment').is(':checked') ? 1: 0;
            setting.albumDisplay = $('input[name="albumDisplay"]:checked').val();
            setting.followLimit = $('#followLimit').val();
            setting.codeAnalytics = $('#codeAnalytics').val();
            setting.codeShare = $('#codeShare').val();            
            setting.isFanxian = $('#isFanxian').is(':checked') ? 1 : 0;
            setting.bulletin = $('#bulletin').val();

            setting.isPendingTixian = $('#isPendingTixian').is(':checked') ? 1 : 0;
            setting.isAutoCash = $('#isAutoCash').is(':checked') ? 1 : 0;
            setting.rate_cashback = $('#rate_cashback').val();
            setting.isShare = $('#isShare').is(':checked') ? 1 : 0;
            setting.rate_share = $('#rate_share').val();
            setting.isAd = $('#isAd').is(':checked') ? 1 : 0;
            setting.rate_ad = $('#rate_ad').val();
            setting.cashback = $('#cashback').val();
            setting.registe_cash = $('#registe_cash').val();
            setting.registe_jifen = $('#registe_jifen').val();

            $.ajax({
                type : "post",
                dataType : "json",
                url : '<?php echo admin_url('admin-ajax.php'); ?>' + '?rand=' + Math.random(),
                data : setting,
                success : function(response) {
                    if (response.code > 0) {
                        alert(response.msg);
                    } else {
                        alert('保存成功');
                    }
                    $('.ajax-feedback').css('visibility', 'hidden');
                }
            })
        });
    });
</script>