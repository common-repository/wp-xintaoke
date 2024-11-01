<?php
$_currentDate = date('Y-m-d', time());
?>
<div id="X_Account-Order">
    <div class="row-fluid">
        <div class="span7">
            <div class="form-inline xt-account-datepicker">
                <input type="text" name="sd" class="input-medium" value="<?php echo date('Y-m-d', strtotime("-3 month")); ?>" id="X_Account-Order-StartDate"> 至 
                <input type="text" name="ed" class="input-medium" value="<?php echo $_currentDate ?>" id="X_Account-Order-EndDate">
                <button class="btn" id="X_Account-Order-Date-Submit">搜索</button>
            </div>
        </div>
        <div class="span5" id="X_Account-Order-Date-Shortcut">
            <ul class="nav nav-pills">
                <li><a href="javascript:;" data-from="<?php echo date('Y-m-d', strtotime("-7 day")); ?>" data-to="<?php echo $_currentDate ?>">最近一周</a></li>
                <li><a href="javascript:;" data-from="<?php echo date('Y-m-d', strtotime("-1 month")); ?>" data-to="<?php echo $_currentDate ?>">1个月</a></li>
                <li class="active"><a href="javascript:;" data-from="<?php echo date('Y-m-d', strtotime("-3 month")); ?>" data-to="<?php echo $_currentDate ?>">3个月</a></li>
                <li><a href="javascript:;" data-from="<?php echo date('Y-m-d', strtotime("-6 month")); ?>" data-to="<?php echo $_currentDate ?>">6个月</a></li>
                <li><a href="javascript:;"  data-from="<?php echo date('Y-m-d', strtotime("-1 year")); ?>" data-to="<?php echo $_currentDate ?>">1年</a></li>	
            </ul>
        </div>
        <ul class="span12 nav nav-pills" id="X_Account-Order-Type" style="margin-left:0px;">
            <li class="active"><a href="javascript:;" data-value="taobao">淘宝订单<span></span></a></li>
            <li><a href="javascript:;" data-value="paipai">拍拍订单<span></span></a></li>
            <li><a href="javascript:;" data-value="yiqifa">其他商城<span></span></a></li>
        </ul>
        <div class="span12 xt-account-list" id="X_Account-Order-List" style="margin-left:0px;">

        </div>	
    </div>
</div>