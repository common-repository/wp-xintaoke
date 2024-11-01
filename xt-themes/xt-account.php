<div class="row-fluid X_Layout row-2-10 xt-first-child">
    <div class="span2 X_Region X_Region_Span2">
        <div id="X_Account-Nav" class="xt-account-nav well well-small">
            <ul class="nav nav-list">
                <?php if (xt_is_fanxian()): ?>
                    <li class="nav-header">返现管理</li>
                    <li class="active"><a href="#info" id="X_Account-Info-A"><i class="icon-home"></i>我的账户</a></li>
                    <li><a href="#orders" id="X_Account-Orders-A"><i class="icon-list-alt"></i>我的订单</a></li>
                    <li><a href="#unorders" id="X_Account-Unorders-A"><i class="icon-search"></i>找回订单</a></li>
                    <?php if (xt_fanxian_is_ad() || xt_fanxian_is_share()): ?><li><a href="#tuiguang" id="X_Account-Tuiguang-A"><i class="icon-share"></i>我的推广</a></li><?php endif; ?>
                <!--<li><a href="#jifen" id="X_Account-Jifen-A"><i class="icon-gift"></i>我的<?php echo xt_jifenbao_text(); ?></a></li>-->
                <?php endif; ?>
                <li class="nav-header">账户设置</li>
                <li><a href="#profile" id="X_Account-Profile-A"><i class="icon-user"></i>个人信息</a></li>
                <li><a href="#bind" id="X_Account-Bind-A"><i class="icon-cog"></i>账号绑定</a></li>
            </ul>
        </div>
    </div>
    <div class="span10 X_Region X_Region_Span10">
        <div id="X_Account-Content" class="xt-account-content clearfix">
            <div id="X_Account-Container" class="xt-account-container">
                <div class="xt-account-main" style="background:white;">
                    <?php
                    $bulletin = xt_bulletin();
                    if (!empty($bulletin)):
                        ?>
                        <div class="alert" style="margin-bottom:5px;">
                            <a class="close" data-dismiss="alert">&times;</a>
                            <?php echo $bulletin; ?>
                        </div>
                    <?php endif; ?>
                    <div id="X_Account-Right" style="padding:10px;">

                    </div>
                </div>	
            </div>
        </div>
    </div>
</div>