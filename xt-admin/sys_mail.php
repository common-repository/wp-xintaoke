<?php
$default_mail = array(
    'mail_from' => '',
    'mail_from_name' => '',
    'mailer' => 'smtp',
    'mail_set_return_path' => 'false',
    'smtp_host' => 'localhost',
    'smtp_port' => '25',
    'smtp_ssl' => 'none',
    'smtp_auth' => false,
    'smtp_user' => '',
    'smtp_pass' => ''
);
$mail_options = get_option(XT_OPTION_MAIL);
if (empty($mail_options)) {
    $mail_options = array();
}
$mail_options = array_merge($default_mail, $mail_options);
$baeHidden = IS_BAE ? ' style="display:none" ' : '';
?>
<h3><?php xt_admin_help_link('sys_mail') ?>邮件服务器设置<span><img id="X_Mail-Setting-Ajax-Feedback" src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback"></span></h3>
<table class="form-table">
    <tbody>
        <tr valign="top"<?php echo $baeHidden ?>>
            <th scope="row">模式</th>
            <td>
                <fieldset>
                    <label>
                        <input class="xt_mail_mailer" name="mailer" type="radio" value="smtp" <?php echo $mail_options['mailer'] == 'smtp' ? 'checked' : ''; ?>>&nbsp;SMTP&nbsp;&nbsp;&nbsp;
                        <input class="xt_mail_mailer" name="mailer" type="radio" value="mail" <?php echo $mail_options['mailer'] == 'mail' ? 'checked' : ''; ?>>&nbsp;MAIL
                    </label>
                </fieldset>
                <p class="description">推荐使用SMTP，MAIL方式需要您的服务器本身支持</p>
            </td>
        </tr>
        <tr class="xt_mail_smtp" valign="top"<?php echo $baeHidden ?>>
            <th scope="row">SMTP 地址</th>
            <td>
                <fieldset>
                    <label for="smtp_host">
                        <input class="regular-text" name="smtp_host" id="smtp_host" value="<?php echo $mail_options['smtp_host']; ?>"> 
                    </label>
                    <br>
                </fieldset>
                <p class="description">比如：smtp.qq.com，<a href="http://domain.mail.qq.com/" target="_blank">QQ域名邮箱</a>，<a href="http://ym.163.com/" target="_blank">网易免费企业邮</a></p>
                <p>
                    QQ域名邮箱设置：<br/>
                    SMTP 地址：smtp.qq.com<br/>
                    SMTP 端口：25<br/>
                    用户名：您的域名邮箱<br/>
                    密码：该域名邮箱所属QQ邮箱密码
                </p>
            </td>
        </tr>
        <tr class="xt_mail_smtp" valign="top"<?php echo $baeHidden ?>>
            <th scope="row">SMTP 端口</th>
            <td>
                <fieldset>
                    <label for="smtp_port">
                        <input class="small-text" type="number" name="smtp_port" id="smtp_port" value="<?php echo absint($mail_options['smtp_port']); ?>"> 
                    </label>
                    <br>
                </fieldset>
                <p class="description">通常为：25</p>
            </td>
        </tr>
        <tr class="xt_mail_smtp" valign="top">
            <th scope="row">用户名</th>
            <td>
                <fieldset>
                    <label>
                        <input class="regular-text" name="smtp_user" id="smtp_user" value="<?php echo $mail_options['smtp_user']; ?>"> 
                    </label>
                </fieldset>
                <p class="description">您的邮箱用户名，如：no-reply@xintaonet.com</p>
            </td>
        </tr>
        <tr class="xt_mail_smtp" valign="top"<?php echo $baeHidden ?>>
            <th scope="row">密码</th>
            <td>
                <fieldset>
                    <label>
                        <input class="regular-text" name="smtp_pass" id="smtp_pass" value="<?php echo $mail_options['smtp_pass']; ?>"> 
                    </label>
                </fieldset>
                <p class="description">您的邮箱密码</p>
            </td>
        </tr>
    </tbody>
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="保存更改"></p>
<h3>测试邮件发送<span><img id="X_Mail-Test-Ajax-Feedback" src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback"></span></h3>
<div id="X_Mail-Test-Msg" class="updated fade" style="display:none;">
    <p><strong>邮件发送失败：</strong><span id="X_Mail-Test-Msg-Result"></span></p>
    <pre id="X_Mail-Test-Msg-Detail"></pre>
</div>
<table class="form-table">
    <tbody>
        <tr class="xt_mail_smtp" valign="top">
            <th scope="row">收件人邮箱</th>
            <td>
                <fieldset>
                    <label>
                        <input class="regular-text" name="to" id="to" value=""> 
                    </label>
                </fieldset>
                <p class="description">请输入一个您自己的邮箱测试是否可以接收到邮件</p>
            </td>
        </tr>
    </tbody>
</table>
<p class="submit"><input type="button" name="submit" id="submitTest" class="button-primary" value="点击发送"></p>
<?php
//$retrievepassword_title = xt_mail_retrieve_password_title();
//$retrievepassword_message = xt_mail_retrieve_password_message();
//
//global $wpdb;
//$user = wp_get_current_user();
//$user_login = $user->user_login;
//$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
//if (empty($key)) {
//    // Generate something random for a key...
//    $key = wp_generate_password(20, false);
//    do_action('retrieve_password_key', $user_login, $key);
//    // Now insert the new md5 key into the db
//    $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
//}
//$retrievepassword_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
?>
<h3 style="display: none">找回密码邮件模板(不建议修改)<span><img id="X_Mail-RetrievePassword-Ajax-Feedback" src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback"></span></h3>
<table class="form-table" style="display: none;">
    <tbody>
        <tr valign="top">
            <th scope="row">邮件标题</th>
            <td>
                <input class="regular-text" name="retrievepassword_title" id="retrievepassword_title" value="<?php echo $retrievepassword_title; ?>"> 
                <p class="description">{blogname}:站点名称</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">邮件内容</th>
            <td>
                <textarea name="retrievepassword_message" rows="6" cols="20" id="retrievepassword_message" class="large-text code"><?php echo $retrievepassword_message; ?></textarea>
                <p class="description">{blogname}:站点名称，{home}:站点地址，{userlogin}:用户名，{url}:重置密码的地址</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">效果演示</th>
            <td>
                <p class="description">
                    <?php echo str_replace('{blogname}', get_bloginfo('name'), $retrievepassword_title) ?><br/>
                    <?php
                    echo str_replace(array("\r\n", "\n", "\r", '{blogname}', '{home}', '{userlogin}', '{url}'), array('<br/>', '<br/>', '<br/>',
                        get_bloginfo('name'),
                        '<a href="' . home_url('/') . '" target="_blank">' . home_url('/') . '</a>', $user->user_login,
                        '<a href="' . $retrievepassword_url . '" target="_blank">' . $retrievepassword_url . '</a>'), $retrievepassword_message)
                    ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>
<p class="submit" style="display:none;"><input type="button" name="submit" id="submitRetrievePassword" class="button-primary" value="保存设置"></p>
<script type="text/javascript">
    jQuery(document).ready(function($) {
<?php if (!IS_BAE) { ?>
            $('.xt_mail_mailer').change(function(){
                if($(this).is(':checked')){
                    if($(this).val()=='smtp'){
                        $('.xt_mail_smtp').show();
                    }else{
                        $('.xt_mail_smtp').hide();
                    }
                }
                return false;
            });
            $('.xt_mail_mailer:checked').change();
<?php } ?>
        $('#submit').click(function() {
            $('#X_Mail-Setting-Ajax-Feedback').css('visibility', 'visible');
            var setting = {};
            setting.action = 'xt_admin_ajax_mail';

            setting.mailer = $('.xt_mail_mailer:checked').val();
            setting.smtp_host = $('#smtp_host').val();
            setting.smtp_port = $('#smtp_port').val();
            setting.smtp_user = $('#smtp_user').val();
            setting.smtp_pass = $('#smtp_pass').val();

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
                    $('#X_Mail-Setting-Ajax-Feedback').css('visibility', 'hidden');
                }
            })
        });
        $('#submitTest').click(function() {
            $('#X_Mail-Test-Msg').hide();
            $('#X_Mail-Test-Ajax-Feedback').css('visibility', 'visible');
            var setting = {};
            setting.action = 'xt_admin_ajax_mail_test';

            setting.to = $('#to').val();

            $.ajax({
                type : "post",
                dataType : "json",
                url : '<?php echo admin_url('admin-ajax.php'); ?>' + '?rand=' + Math.random(),
                data : setting,
                success : function(response) {
                    if (response.code > 0) {
                        $('#X_Mail-Test-Msg-Result').html(response.result.phpmailer);
                        $('#X_Mail-Test-Msg-Detail').html(response.result.smtpdebug);                        
                        $('#X_Mail-Test-Msg').show();
                    } else {
                        alert('邮件发送成功');
                    }
                    $('#X_Mail-Test-Ajax-Feedback').css('visibility', 'hidden');
                }
            })
        });
    });
</script>