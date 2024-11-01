<style type="text/css">
    #normal-sortables .postbox .submit{float:none;}.form-table th {width:100px;}.postbox .hndle{cursor:auto;}
</style>

<?php
?>
<div id="dashboard-widgets-wrap">
    <div id="dashboard-widgets" class="metabox-holder columns-2">

        <div id="postbox-container-1" class="postbox-container">
            <div class="meta-box-sortables ">
                <div class="postbox">
                    <h3 class="hndle">
                        <span>网站主验证工具</span>
                    </h3>
                    <div class="inside">
                        <table class="form-table" id="X_Link_Convert_Table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">验证代码</th>
                                    <td><input id="X_Verify-Input" name="link" type="text" value="" class="regular-text"></td>
                                </tr>
                            </tbody>
                        </table>
                        <p>
                            支持以下格式验证：<br>
                        <ul>
                            <li>html标签验证(举例)：<code><?php echo esc_html('<meta name="baidu-site-verification" content="LxR1ldS1AdTF5qS6" />') ?></code></li>
                            <li>html代码验证(举例)：<code>d4df7b6239c425d8cc897411ef11abe7</code></li>
                        </ul>
                        验证步骤：<br>
                        <ol>
                            <li>在需要验证网站主的第三方网站(如百度联盟)上边复制验证码</li>
                            <li>将复制的验证码粘贴至上边验证代码文本框中，点击保存设置</li>
                            <li>回到第三方网站(如百度联盟)，点击完成验证即可</li>
                        </ol>
                        提示：<br>
                        <ul>
                            <li>如第三方仅提供文件验证，请站长自行下载并上传至自己的网站内完成验证</li>
                            <li>清空并保存可以删除已有的验证代码</li>
                        </ul>
                        </p>
                        <p class="submit">
                            <input id="X_Verify" type="submit" name="submit" class="button-primary" value="保存设置">
                            <span><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div id="postbox-container-2" class="postbox-container">
            <div class="meta-box-sortables ">
                <div class="postbox">
                    <h3 class="hndle">
                        <span>链接转换工具(转换后的链接可自动跟踪会员返利)</span>
                    </h3>
                    <div class="inside">
                        <table class="form-table" id="X_Link_Convert_Table">
                            <tbody>
                                <tr valign="top">
                                    <th scope="row">链接1</th>
                                    <td><input name="link" type="text" value="" class="xt-link regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">链接2</th>
                                    <td><input name="link" type="text" value="" class="xt-link regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">链接3</th>
                                    <td><input name="link" type="text" value="" class="xt-link regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">链接4</th>
                                    <td><input name="link" type="text" value="" class="xt-link regular-text"></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">链接5</th>
                                    <td><input name="link" type="text" value="" class="xt-link regular-text"></td>
                                </tr>
                            </tbody>
                        </table>
                        <p>
                            支持以下类型转换:<br>
                        <ul>
                            <li>淘宝联盟<strong style="color:#21759B;">http://s.click.taobao.com</strong>开头的推广链接转换,访问<a href="http://www.alimama.com/" target="_blank">淘宝联盟</a></li>
                            <li>易推广(拍拍)<strong style="color:#21759B;">http://te.paipai.com</strong>开头的推广链接转换,访问<a href="http://etg.qq.com/" target="_blank">易推广</a></li>
                        </ul>
                        </p>
                        <p class="submit">
                            <input id="X_Link_Convert" type="submit" name="submit" class="button-primary"
                                   value="开始转换"><span><img
                                    src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>"
                                    class="ajax-feedback"></span>
                            <a id="X_Link_Convert_Clear" class="button" href="javascript:;">清空所有</a>	
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready( function($) {
        $('#X_Verify').click(function(){
            var $panel = $(this).parents('.postbox:first');
            var verification = $('#X_Verify-Input',$panel).val();
            var setting = {};
            setting.action = 'xt_admin_ajax_site_verify';
            setting.verification = verification;
            $('.ajax-feedback',$panel).css('visibility', 'visible');
            $.ajax({
                type : "post",
                dataType : "json",
                url : '<?php echo admin_url('admin-ajax.php'); ?>' + '?rand=' + Math.random(),
                data : setting,
                success : function(response) {
                    if (response.code > 0) {
                        alert(response.msg);
                    } else {
                        if(!verification){
                            alert('已清除验证代码');
                        }else{
                            alert('添加验证代码成功，请回到第三方网站完成后续验证步骤');
                        }
                    }
                    $('.ajax-feedback',$panel).css('visibility', 'hidden');
                }
            })
            return false;
        });
        $('#X_Link_Convert_Clear').click(function(){
            var $panel = $(this).parents('.postbox:first');
            $('input.xt-link',$panel).val('');
            $('input.xt-link-result',$panel).parents('tr').remove();
        });
        $('#X_Link_Convert').click(function(){
            var links = [];
            var $panel = $(this).parents('.postbox:first');
            $('input.xt-link',$panel).each(function(){
                $('.ajax-feedback',$panel).css('visibility', 'visible');
                var link = $(this).val();
                //todo validate;
                links.push(link);
            });
            if(links.length==0){
                alert('请填写正确的链接地址');
                $('.ajax-feedback',$panel).css('visibility', 'hidden');
                return false;
            }
            var setting = {};
            setting.action = 'xt_admin_ajax_link_convert';
            setting.links = links;
            $.ajax({
                type : "post",
                dataType : "json",
                url : '<?php echo admin_url('admin-ajax.php'); ?>' + '?rand=' + Math.random(),
                data : setting,
                success : function(response) {
                    if (response.code > 0) {
                        alert(response.msg);
                    } else {
                        var $result = response.result;
                        for(var i=0;i<$result.length;i++){
                            var _link = $result[i];
                            if(_link){
                                var input = $('input.xt-link',$panel).eq(i).parents('tr:first').next().find('input.xt-link-result');
                                if(input.length==0){
                                    $('input.xt-link',$panel).eq(i).parents('tr:first').after('<tr><th>&nbsp;&nbsp;&nbsp;'+(i+1)+'转换后</th><td><input name="link" type="text" value="'+_link+'" class="xt-link-result regular-text"></td></tr>');
                                }else{
                                    input.val(_link);
                                }
                            }
                        }
                    }
                    $('.ajax-feedback',$panel).css('visibility', 'hidden');
                }
            })
            return false;
        });
    });
</script>