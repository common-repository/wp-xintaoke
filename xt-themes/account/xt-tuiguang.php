<?php
$_currentDate = date('Y-m-d', time());
?>
<div id="X_Account-Tuiguang">
    <div class="row-fluid">
        <div class="span7">
            <div class="form-inline xt-account-datepicker">
                <input type="text" name="sd" class="input-medium" value="<?php echo date('Y-m-d', strtotime("-1 year")); ?>" id="X_Account-Tuiguang-StartDate"> 至 
                <input type="text" name="ed" class="input-medium" value="<?php echo $_currentDate ?>" id="X_Account-Tuiguang-EndDate">
                <button class="btn" id="X_Account-Tuiguang-Date-Submit" >搜索</button>
            </div>
        </div>
        <div class="span5" id="X_Account-Tuiguang-Date-Shortcut">
            <ul class="nav nav-pills">
                <li><a href="javascript:;" data-from="<?php echo date('Y-m-d', strtotime("-7 day")); ?>" data-to="<?php echo $_currentDate ?>">最近一周</a></li>
                <li><a href="javascript:;" data-from="<?php echo date('Y-m-d', strtotime("-1 month")); ?>" data-to="<?php echo $_currentDate ?>">1个月</a></li>
                <li><a href="javascript:;" data-from="<?php echo date('Y-m-d', strtotime("-3 month")); ?>" data-to="<?php echo $_currentDate ?>">3个月</a></li>
                <li><a href="javascript:;" data-from="<?php echo date('Y-m-d', strtotime("-6 month")); ?>" data-to="<?php echo $_currentDate ?>">6个月</a></li>
                <li class="active"><a href="javascript:;"  data-from="<?php echo date('Y-m-d', strtotime("-1 year")); ?>" data-to="<?php echo $_currentDate ?>">1年</a></li>	
            </ul>
        </div>
        <ul class="span12 nav nav-pills" id="X_Account-Tuiguang-Type" style="margin-left:0px;">
            <li><a href="javascript:;" data-value="invite">推广方式<span></span></a></li>
            <li class="active"><a href="javascript:;" data-value="order">推广记录<span></span></a></li>
            <li><a href="javascript:;" data-value="member">推广会员<span></span></a></li>
        </ul>
        <div class="span12 xt-account-list" id="X_Account-Tuiguang-List" style="margin-left:0px;">
        </div>	
    </div>
</div>