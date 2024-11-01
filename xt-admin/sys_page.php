<style type="text/css">
.widget-content label.key{width:80px;display:inline-block;text-align:right;}
.widget-content small{padding-left:80px;display:block;}
</style>
<?php
define('XT_PAGE_EDIT', true);
wp_enqueue_script('xt-admin-widgets');
$_sys_pages = xt_design_pages();
$__page = 'home';
$__page_name = '首页';
$__page_preview = home_url();
if (isset ($_GET['xt-page']) && !empty ($_GET['xt-page'])) {
	if (isset ($_sys_pages[$_GET['xt-page']])) {
		$__page = $_GET['xt-page'];
		$__page_name = $_sys_pages[$_GET['xt-page']]['title'];
		$__page_preview = $_sys_pages[$_GET['xt-page']]['preview'];
	} else {
		$pages = get_option(XT_OPTION_PAGES);
		if (isset ($pages[$_GET['xt-page']])) {
			$__page = intval($_GET['xt-page']);
			$_page = get_post($__page);
			if (!empty ($_page)) {
				$__page_name = $_page->post_title;
				$__page_preview = get_permalink($__page);
			} else {
				exit ('<h1>指定的页面不存在</h1>');
			}
		} else {
			exit ('<h1>指定的页面不存在</h1>');
		}
	}
}
//print_r(get_option('xt_option_page_home'));
global $xt_registered_page;
?>
<h3><a href="<?php echo admin_url('admin.php?page=xt_menu_sys&xt-action=pages')?>">返回页面列表</a>&nbsp;>>&nbsp;<?php echo $__page_name.(!empty($__page_preview)?('(<a href="'.$__page_preview.'" target="_blank">预览</a>)'):'')?></h3>
<form action="" method="post">
<?php wp_nonce_field( 'save-home-widgets', '_wpnonce_home_widgets', false ); ?>
</form>
<br class="clear" />
<div id="X_Cat_Selector_Container" style="display:none;position:relative;"></div>

<div id="X_Page-Edit">
	<div id="X_Hd" class="container X_Grid" data-widgets="X_Hd-Widgets-Available-Box" data-value="header" style="margin-bottom:10px;">
		<?php

xt_init_page('header', '页头'); //init 
xt_list_widget_controls('header');
?>
	</div>
	<div id="X_Hd-Widgets-Available-Box" style="display:none;">
		<div class="widget-list">
		<?php xt_list_widgets(); ?>
		</div>
		<br class='clear' />
	</div>
	<div id="X_Bd" class="container X_Grid" data-widgets="X_Bd-Widgets-Available-Box" data-value="<?php echo $__page;?>">
		<?php

xt_init_page($__page, $__page_name); //init 
xt_list_widget_controls($__page);
?>
	</div>
	<div id="X_Bd-Widgets-Available-Box" style="display:none;">
		<div class="widget-list">
		<?php xt_list_widgets(); ?>
		</div>
		<br class='clear' />
	</div>
	<div id="X_Ft" class="container X_Grid" data-value="footer" data-widgets="X_Ft-Widgets-Available-Box" style="margin-top:20px;">
		<?php

xt_init_page('footer', '页尾'); //init 
xt_list_widget_controls('footer');
?>
	</div>
	<div id="X_Ft-Widgets-Available-Box" style="display:none;">
		<div class="widget-list">
		<?php xt_list_widgets(); ?>
		</div>
		<br class='clear' />
	</div>
</div>

<div id="X_Widgets-Layout-Box" style="display:none;">
	<div id="X_Layout-Add-List" class="row-list">
        <span class="extra"></span>
        <div>
            <a class="l-grid-m0" href="javascript:;"  data-value="row-12"></a>
        </div>
        <div>
            <a class="l-grid-s5m0" href="javascript:;" data-value="row-3-9"></a>
            <a class="l-grid-m0s5" href="javascript:;" data-value="row-9-3"></a>
        </div>
        <!--<div data-modules="main,sub,extra" data-type="grid-s5m0e5,grid-s5m0e5,grid-s5e5m0">
            <a data-prototypeid="20" data-componentid="20" class="l-grid-s5m0e5" href="#"></a>
            <a data-prototypeid="229" data-componentid="229" class="l-grid-m0s5e5" href="#"></a>
            <a data-prototypeid="230" data-componentid="230" class="l-grid-s5e5m0" href="#"></a>
        </div>-->
    </div>
</div> 
<div id="X_Widgets-Layout-Change-Box" style="display:none;">
	<div id="X_Layout-Change-List" class="row-list">
        <span class="extra"></span>
        <div>
            <a class="l-grid-s5m0" href="javascript:;" data-value="row-3-9"></a>
            <a class="l-grid-m0s5" href="javascript:;" data-value="row-9-3"></a>
        </div>
    </div>
</div>
<div id="X_Widgets-Text-Editor-Box" style="display:none;">
	<div id="X_Widgets-Text-Editor">
		<textarea id="X_Editor" style=""></textarea>
		<p class="submit">
			<input type="button" class="button-primary" value="保存更改">
		</p>
	</div>
</div>
<script type="text/javascript">
window.UEDITOR_HOME_URL = "<?php echo XT_CORE_JS_URL.'/ueditor/'?>";
var XT_IFRAME = <?php echo json_encode(array('theme'=>XT_THEME_URL.'/xintaoke.min.css','style'=>xt_get_theme()));?>;
</script>