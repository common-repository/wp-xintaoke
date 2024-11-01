<?php
global $wpdb;

if (!current_user_can('publish_pages')) {
	exit ('您无权操作此功能');
}
$_share_id = isset ($_GET['id']) ? intval($_GET['id']) : 0;
if ($_share_id == 0) {
	exit ('未指定要分类的分享');
}
$cids = $wpdb->get_col('SELECT cid FROM ' . XT_TABLE_SHARE_CATALOG . ' WHERE id=' . $wpdb->escape($_share_id));
$cats = xt_catalogs_share();
if (!empty ($cats)) {
	echo '<div id="X_Share_Tool_Catalog" data-id="' . $_share_id . '">';
	foreach ($cats as $cat) {
		$childs = $cat->child;
		if (in_array($cat->id, $cids)) {
			echo '<span data-value="' . $cat->id . '" class="label label-default">' . esc_html($cat->title) . '</span>';
		} else {
			echo '<span data-value="' . $cat->id . '" class="label">' . esc_html($cat->title) . '</span>';
		}
		if (!empty ($childs)) {
			$childCats = $childs['catalogs'];
			foreach ($childCats as $sub) {
				if (in_array($sub->id, $cids)) {
					echo '<span data-value="' . $sub->id . '" class="label label-default">' . esc_html($sub->title) . '</span>';
				} else {
					echo '<span data-value="' . $sub->id . '" class="label">' . esc_html($sub->title) . '</span>';
				}
			}
		}
	}
	echo '</div>';
}
?>