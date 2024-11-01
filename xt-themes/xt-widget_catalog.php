<?php
global $xt, $wp_query, $xt_catalog;

//TODO

if ($xt->is_shares) {
	$_catalogs = xt_catalogs_share();
	$xt_share_param = $wp_query->query_vars['xt_param'];
	$terms = array ();
	$terms_sub = array ();
	$terms_tag = array ();
	$terms_select = 0;
	$terms_parent = 0;
	$terms_tag_parent = 0;
	$tag_select = isset ($xt_share_param['s']) ? $xt_share_param['s'] : '';
	if (!empty ($xt_catalog)) { //指定了分类
		$terms_tag_parent = $xt_catalog->id;
		$terms_select = $xt_catalog->id;
		$terms_parent = $xt_catalog->parent;
		$terms_sub = $terms = xt_catalogs_share_sub($terms_select);
	}
	//catalog
	$terms = xt_catalogs_share_sub($terms_parent);
	//tag
	$query_tags = query_tags(array (
		'cid' => $terms_select
	));
	$terms_tag = $query_tags['tags'];
	if (!empty ($terms)) {
?>
<div class="span3 xt-share xt-share-catalog">
	<div class="thumbnail">
		<div class="caption">
		<?php

		echo '<a href="' . xt_get_shares_search_url(array_merge($xt_share_param, array (
			'cid' => $terms_parent,
			's' => '',
			'sortOrder' => '',
			'price' => '',
			'page' => 1
		))) .
		'" ' . ($terms_select == 0 ? ' class="label label-tag active"' : 'class="label label-tag"') . '>全部</a>';
		foreach ($terms as $term) {
			if ($term->is_front) {
				echo '<a href="' . xt_get_shares_search_url(array_merge($xt_share_param, array (
					'cid' => $term->id,
					's' => '',
					'sortOrder' => '',
					'price' => '',
					'page' => 1
				))) .
				'"' . ($terms_select == $term->id ? ' class="label label-tag active"' : 'class="label label-tag"') . '>' . $term->title . '</a>';
			}
		}

		if (!empty ($terms_sub)) {
			echo '<div class="clearfix"><h4>分类</h4>';
			foreach ($terms_sub as $sub) {
				echo '<a href="' . xt_get_shares_search_url(array_merge($xt_share_param, array (
					'cid' => $sub->id,
					'sortOrder' => '',
					'price' => '',
					'page' => 1
				))) .
				'" class="label label-tag">' . $sub->title . '</a>';
			}
			echo '</div>';
		}
		if (!empty ($terms_tag)) {
			echo '<div class="clearfix"><h4>热门标签</h4>';
			foreach ($terms_tag as $tag) {
				echo '<a href="' . xt_get_shares_search_url(array_merge($xt_share_param, array (
					's' => $tag->title,
					'sortOrder' => '',
					'price' => '',
					'page' => 1
				))) .
				'"' . ($tag_select == $tag->title ? ' class="label label-tag active"' : 'class="label label-tag"') . '>' . $tag->title . '</a>';

			}
			echo '</div>';
		}
?>
		<?php


		if (!empty ($terms_sub) || !empty ($terms_tag)) {
			echo '<div class="xt-share-catalog-arrow"><span></span></div>';
		}
?>
</div>
</div>		
</div>		
<?php


	}
}