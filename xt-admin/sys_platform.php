<style type="text/css">
    #normal-sortables .postbox .submit{float:none;}.form-table th {width:100px;}.postbox .hndle{cursor:auto;}
</style>
<script type="text/javascript">
    var authWin;
    function authoritySuccess(){
        alert('授权成功!');
        if (authWin && !authWin.closed) {
            authWin.close();
            top.location.reload();//刷新
        }
    }

    function expires_in_time(maxtime, fn) {
        var timer = setInterval(function() {
            if (maxtime >= 0) {
                d = parseInt(maxtime / 3600 / 24);
                h = parseInt((maxtime / 3600) % 24);
                m = parseInt((maxtime / 60) % 60);
                s = parseInt(maxtime % 60);
                msg = d + "天" + h + "小时" + m + "分" + s + "秒";
                fn(msg);
                --maxtime;
            } else {
                clearInterval(timer);
                fn("已失效!");
            }
        }, 1000);
    }
</script>
<?php
$app_xt = xt_get_app_xt();
$app_taobao = xt_get_app_taobao();
$app_paipai = xt_get_app_paipai();
$app_yiqifa = xt_get_app_yiqifa();
$app_weibo = xt_get_app_weibo();
$app_qq = xt_get_app_qq();
$_loginurl = $_loginurl = xt_platform_authorize_url('[PLATFORM]', 'authoritySuccess', 'admin');
?>
<div id="dashboard-widgets-wrap">
    <?php
    if (IS_CLOUD) {
        if (strpos(home_url(), 'sinaapp.com') !== false) {
            ?>
            <div class="updated" style="color:red;font-weight: bold;font-size:15px;padding:10px 5px;">
                建议绑定自己的独立域名后，再到以下平台用独立域名申请APP，<a href="http://plugin.xintaonet.com/help/?id=126#X_Help-4" target="_blank">新浪SAE域名绑定</a>

            </div>
            <?php
        } elseif (strpos(home_url(), 'duapp.com') !== false) {
            ?>
            <div class="updated" style="color:red;font-weight: bold;font-size:15px;padding:10px 5px;">
                建议绑定自己的独立域名后，再到以下平台用独立域名申请APP，<a href="http://plugin.xintaonet.com/help/?id=163#X_Help-6" target="_blank">百度BAE域名绑定</a><br/>
            </div>
            <?php
        }
    }
    ?>

    <div id="dashboard-widgets" class="metabox-holder columns-2">
        <div id="postbox-container-1" class="postbox-container">
            <div id="normal-sortables" class="meta-box-sortables ">
                <div class="postbox">
                    <h3 class="hndle">
                        <?php xt_admin_help_link('sys_platform_xintao') ?>
                        <span>新淘客(实现装修,URL,皮肤定制,自动分享等平台功能)</span>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">AppKey</th>
                                    <td><input name="appkey" type="text"
                                               value="<?php echo $app_xt['appKey'] ?>" class="regular-text">
                                        <br/><small><a href="http://plugin.xintaonet.com" target="_blank">访问新淘客平台</a></small>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">AppSecret</th>
                                    <td><input name="appsecret" type="text"
                                               value="<?php echo $app_xt['appSecret'] ?>" class="regular-text">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary"
                                   value="保存更改" data-platform="xt"><span><img
                                    src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>"
                                    class="ajax-feedback"></span>
                        </p>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle">
                        <?php xt_admin_help_link('sys_platform_paipai') ?>
                        <span>拍拍(实现拍拍商品,拍拍客推广功能)</span>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">开发者(QQ号)</th>
                                    <td><input name="uid" type="text"
                                               value="<?php echo $app_paipai['uid'] ?>"
                                               class="regular-text">
                                        <br/><small><a href="http://fuwu.paipai.com/my/index.xhtml" target="_blank">访问拍拍开放平台</a></small>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">appOAuthID</th>
                                    <td><input name="appkey" type="text"
                                               value="<?php echo $app_paipai['appKey'] ?>"
                                               class="regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">secretOAuthKey</th>
                                    <td><input name="appsecret" type="text"
                                               value="<?php echo $app_paipai['appSecret'] ?>"
                                               class="regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">accessToken</th>
                                    <td><input name="token" type="text"
                                               value="<?php echo!empty($app_paipai['token']) ? $app_paipai['token'] : '' ?>"
                                               class="regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">推广ID</th>
                                    <td><input name="userId" type="text"
                                               value="<?php echo!empty($app_paipai['userId']) ? $app_paipai['userId'] : '' ?>"
                                               class="regular-text">
                                        <br/><small><a href="http://etg.qq.com/" target="_blank">访问易推广(拍拍客)</a></small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary"
                                   value="保存更改" data-platform="paipai"><span><img
                                    src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>"
                                    class="ajax-feedback"></span>
                        </p>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle">
                        <?php xt_admin_help_link('sys_platform_weibo') ?>
                        <span>新浪微博(实现新浪微博登录功能)</span>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">AppKey</th>
                                    <td><input name="appkey" type="text"
                                               value="<?php echo $app_weibo['appKey'] ?>" class="regular-text">
                                        <br/><small><a href="http://open.weibo.com/" target="_blank">访问新浪微博开放平台</a></small>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">AppSecret</th>
                                    <td><input name="appsecret" type="text"
                                               value="<?php echo $app_weibo['appSecret'] ?>"
                                               class="regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">新浪授权</th>
                                    <td>
                                        <?php if (isset($app_weibo['name']) && !empty($app_weibo['name']) && isset($app_weibo['token']['expires_in_date'])): ?>
                                            <strong> <?php echo $app_weibo['name'] ?></strong> 
                                            <?php if (isset($app_weibo['token']['expires_in_date'])): ?>
                                                &nbsp;&nbsp;&nbsp;(有效期:<b id="X_Sys_Platform_Weibo_Expires" style="color: #090;"> <?php echo $app_weibo['token']['expires_in_date'] ?></b>) 
                                                <script type="text/javascript">
                                                    expires_in_time(
        <?php echo strtotime($app_weibo['token']['expires_in_date']) - current_time('timestamp') ?>,
            function(msg) {
                document.getElementById('X_Sys_Platform_Weibo_Expires').innerHTML = msg;
            });
                                                </script> 
                                            <?php endif; ?> &nbsp;&nbsp;&nbsp;
                                            <a href="<?php echo str_replace('[PLATFORM]', 'weibo', $_loginurl); ?>" class="xt_auth_login">重新授权</a> 
                                        <?php else: ?> 
                                            <a href="<?php echo str_replace('[PLATFORM]', 'weibo', $_loginurl); ?>" class="xt_auth_login">点击授权</a>(请授权您的新浪微博,否则会员无法自动关注您)
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary"
                                   value="保存更改" data-platform="weibo"><span><img
                                    src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>"
                                    class="ajax-feedback"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div id="postbox-container-2" class="postbox-container">
            <div id="normal-sortables" class="meta-box-sortables ">
                <div class="postbox">
                    <h3 class="hndle">
                        <?php xt_admin_help_link('sys_platform_taobao') ?>
                        <span>淘宝(淘宝登录,淘宝商品,店铺,特价,折扣推广功能)</span>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">AppKey</th>
                                    <td><input name="appkey" type="text"
                                               value="<?php echo $app_taobao['appKey'] ?>"
                                               class="regular-text">
                                        <br/><small><a href="http://my.open.taobao.com/xtao/website_list.htm" target="_blank">访问淘宝开放平台</a></small>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">AppSecret</th>
                                    <td><input name="appsecret" type="text"
                                               value="<?php echo $app_taobao['appSecret'] ?>"
                                               class="regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">淘点金完整PID</th>
                                    <td><input name="tkpid" type="text"
                                               value="<?php echo isset($app_taobao['tkpid']) ? $app_taobao['tkpid'] : '' ?>"
                                               class="regular-text">
                                        <br/><small><a href="http://www.alimama.com/index.htm" target="_blank">访问淘宝联盟(阿里妈妈)</a></small>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">搜索框完整PID</th>
                                    <td><input name="s8pid" type="text"
                                               value="<?php echo isset($app_taobao['s8pid']) ? $app_taobao['s8pid'] : '' ?>"
                                               class="regular-text"><br><small>格式:mm_账户id编号_网站id编号_广告位id编号</small></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">淘宝授权</th>
                                    <td>
                                        <?php if (isset($app_taobao['name']) && !empty($app_taobao['name']) && isset($app_taobao['token']['expires_in_date'])): ?>
                                            <strong> <?php echo $app_taobao['name'] ?></strong> 
                                            <?php if (isset($app_taobao['token']['expires_in_date'])): ?>
                                                &nbsp;&nbsp;&nbsp;(有效期:<b id="X_Sys_Platform_Taobao_Expires" style="color: #090;"> <?php echo $app_taobao['token']['expires_in_date'] ?></b>) 
                                                <script type="text/javascript">
                                                    expires_in_time(
        <?php echo strtotime($app_taobao['token']['expires_in_date']) - current_time('timestamp') ?>,
            function(msg) {
                document.getElementById('X_Sys_Platform_Taobao_Expires').innerHTML = msg;
            });
                                                </script> 
                                            <?php endif; ?> &nbsp;&nbsp;&nbsp;
                                            <a href="<?php echo str_replace('[PLATFORM]', 'taobao', $_loginurl); ?>" class="xt_auth_login">重新授权</a> 
                                        <?php else: ?> 
                                            <a href="<?php echo str_replace('[PLATFORM]', 'taobao', $_loginurl); ?>" class="xt_auth_login">点击授权</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary"
                                   value="保存更改" data-platform="taobao"><span><img
                                    src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>"
                                    class="ajax-feedback"></span>
                        </p>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle">
                        <?php xt_admin_help_link('sys_platform_yiqifa') ?>
                        <span>亿起发(实现商城,比价,团购,优惠活动,推广功能)</span>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">用户名</th>
                                    <td><input name="account" type="text"
                                               value="<?php echo $app_yiqifa['account'] ?>"
                                               class="regular-text">
                                        <br/><small><a href="http://www.yiqifa.com/" target="_blank">访问亿起发</a></small>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">网站主ID</th>
                                    <td><input name="sid" type="text"
                                               value="<?php echo $app_yiqifa['sid'] ?>"
                                               class="regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">网站ID</th>
                                    <td><input name="wid" type="text"
                                               value="<?php echo isset($app_yiqifa['wid']) ? $app_yiqifa['wid'] : '' ?>"
                                               class="regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">同步密钥</th>
                                    <td><input name="syncsecret" type="text"
                                               value="<?php echo $app_yiqifa['syncSecret'] ?>"
                                               class="regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">AppKey</th>
                                    <td><input name="appkey" type="text"
                                               value="<?php echo $app_yiqifa['appKey'] ?>"
                                               class="regular-text">
                                        <br/><small><a href="http://open.yiqifa.com/" target="_blank">访问亿起发开放平台</a></small>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">AppSecret</th>
                                    <td><input name="appsecret" type="text"
                                               value="<?php echo $app_yiqifa['appSecret'] ?>"
                                               class="regular-text"></td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary"
                                   value="保存更改" data-platform="yiqifa"><span><img
                                    src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>"
                                    class="ajax-feedback"></span>
                        </p>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle">
                        <?php xt_admin_help_link('sys_platform_qq') ?>
                        <span>QQ(实现QQ登录功能)</span>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">APP ID</th>
                                    <td><input name="appkey" type="text"
                                               value="<?php echo $app_qq['appKey'] ?>" class="regular-text">
                                        <br/><small><a href="http://connect.qq.com/" target="_blank">访问QQ互联开放平台</a></small>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">APP KEY</th>
                                    <td><input name="appsecret" type="text"
                                               value="<?php echo $app_qq['appSecret'] ?>" class="regular-text">
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">QQ授权</th>
                                    <td>
                                        <?php if (isset($app_qq['name']) && !empty($app_qq['name']) && isset($app_qq['token']['expires_in_date'])): ?>
                                            <strong> <?php echo $app_qq['name'] ?></strong> 
                                            <?php if (isset($app_qq['token']['expires_in_date'])): ?>
                                                &nbsp;&nbsp;&nbsp;(有效期:<b id="X_Sys_Platform_QQ_Expires" style="color: #090;"> <?php echo $app_qq['token']['expires_in_date'] ?></b>) 
                                                <script type="text/javascript">
                                                    expires_in_time(
        <?php echo strtotime($app_qq['token']['expires_in_date']) - current_time('timestamp') ?>,
            function(msg) {
                document.getElementById('X_Sys_Platform_QQ_Expires').innerHTML = msg;
            });
                                                </script> 
                                            <?php endif; ?> &nbsp;&nbsp;&nbsp;
                                            <a href="<?php echo str_replace('[PLATFORM]', 'qq', $_loginurl); ?>" class="xt_auth_login">重新授权</a> 
                                        <?php else: ?> 
                                            <a href="<?php echo str_replace('[PLATFORM]', 'qq', $_loginurl); ?>" class="xt_auth_login">点击授权</a>(请授权您的QQ,否则会员无法自动关注您)
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit">
                            <input type="submit" name="submit" class="button-primary"
                                   value="保存更改" data-platform="qq"><span><img
                                    src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>"
                                    class="ajax-feedback"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready( function($) {
        $('input[name="submit"]').click(function() {
            $panel = $(this).parents('.postbox:first');
            $('.ajax-feedback',$panel).css('visibility', 'visible');
            var setting = {};
            setting.action = 'xt_admin_ajax_platform';
		
            setting.platform = $(this).attr('data-platform');
            setting.appKey = $('input[name="appkey"]',$panel).val();
            setting.appSecret = $('input[name="appsecret"]',$panel).val();
            if(setting.platform=='paipai'){
                setting.uid=$('input[name="uid"]',$panel).val();
                setting.token=$('input[name="token"]',$panel).val();
                setting.userId=$('input[name="userId"]',$panel).val();
            }else if(setting.platform=='yiqifa'){
                setting.account=$('input[name="account"]',$panel).val();
                setting.sid=$('input[name="sid"]',$panel).val();
                setting.wid=$('input[name="wid"]',$panel).val();
                setting.syncSecret=$('input[name="syncsecret"]',$panel).val();
            }else if(setting.platform=='taobao'){
                setting.tkpid=$('input[name="tkpid"]',$panel).val();
                setting.s8pid=$('input[name="s8pid"]',$panel).val();
            }
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
                        top.location.reload();//刷新
                    }
                    $('.ajax-feedback',$panel).css('visibility', 'hidden');
                }
            })
        });
        $('.xt_auth_login').click(function(){
            authWin = window.open($(this).attr('href'), 'authWin', "resizable=1,location=0,status=0,scrollbars=0,width=800,height=600");
            return false;
        });
    });
</script>