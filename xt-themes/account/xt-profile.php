<?php $profileuser = get_user_to_edit(get_current_user_id()); ?>
<div id="X_Account-Profile">
    <div class="row-fluid">
        <form id="X_Account-Profile-Form" class="form-horizontal">
            <?php wp_nonce_field('update-user_' . get_current_user_id()) ?>
            <input type="hidden" name="action" value="xt_ajax_account_profile_update">
            <div class="control-group">
                <label class="control-label" for="user_login">用户名：</label>
                <div class="controls">
                    <input class="required disabled" type="text" name="user_login" id="user_login" disabled value="<?php echo esc_attr($profileuser->user_login); ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="display_name">昵称：</label>
                <div class="controls">
                    <input class="required" type="text" name="display_name" id="display_name" value="<?php echo esc_attr($profileuser->display_name) ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">性别：</label>
                <div class="controls">
                    <?php
                    if ($profileuser->xt_user_gender == '男')
                        :
                        ?>
                        <label class="radio inline">
                            <input type="radio" name="xt_user_gender" value="男" checked> 男
                        </label>
                        <label class="radio inline">
                            <input type="radio" name="xt_user_gender" value="女"> 女
                        </label>
                    <?php else: ?>
                        <label class="radio inline">
                            <input type="radio" name="xt_user_gender" value="男"> 男
                        </label>
                        <label class="radio inline">
                            <input type="radio" name="xt_user_gender" value="女" checked> 女
                        </label>
                    <?php endif; ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">电子邮箱：</label>
                <div class="controls">
                    <input class="required email" type="email" name="email" id="email" value="<?php echo esc_attr($profileuser->user_email) ?>">
                </div>
            </div>
            <?php
            foreach (_wp_get_user_contactmethods($profileuser) as $name => $desc) :
                if ($name == 'xt_user_gender')
                    continue;
                ?>
                <div class="control-group">
                    <label class="control-label"><?php echo $desc; ?>：</label>
                    <div class="controls">
                        <input type="text" name="<?php echo $name; ?>" value="<?php echo esc_attr($profileuser->$name) ?>">
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="control-group">
                <label class="control-label" for="description">个人说明：</label>
                <div class="controls">
                    <textarea name="description" id="description"><?php echo $profileuser->description; ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"></label>
                <div class="controls">
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </div>
        </form>
    </div>
</div>