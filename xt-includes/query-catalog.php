<?php

function xt_get_catalogs_by_cid($cid, $type) {
    global $wpdb;
    $cats = $wpdb->get_results('SELECT * FROM ' . XT_TABLE_CATALOG_ITEMCAT . ' WHERE cid=' . absint($cid) . ' AND type=\'' . $wpdb->escape($type) . '\'');
    if (!empty($cats)) {
        $_parents = $_cats = array();
        foreach ($cats as $cat) {
            $_cats[] = $cat->id;
            $_parents[] = $cat->parent_id;
        }
        if (!empty($_parents) && !empty($_cats)) {
            return array_diff($_cats, $_parents);
        }
    }
    return array();
}

function xt_get_catalogs_by_tags($tags) {
    $tags = array_unique($tags);
    if (!empty($tags)) {
        global $wpdb;
        $_tags = array();
        foreach ($tags as $tag) {
            $tag = strtr($tag, array("\\\\" => "\\\\\\\\", '_' => '\_', '%' => '\%', "\'" => "\\\\\'"));
            $tag = "'" . $wpdb->escape($tag) . "'";
            $_tags[] = $tag;
        }
        $cids = $wpdb->get_col('SELECT DISTINCT cat.cid FROM ' . XT_TABLE_SHARE_TAG_CATALOG . ' AS cat INNER JOIN ' . XT_TABLE_SHARE_TAG . ' AS tag ON tag.id=cat.id WHERE tag.title in (' . implode(',', $_tags) . ')');
        return $cids;
    }
    return array();
}

function xt_update_catalog_terms_cache($id, $cats, $type = 'share_tag') {
    wp_cache_add($id, $cats, 'xt_catalog_' . $type . '_relationships');
}

function xt_get_catalog_terms_cache($id, $type = 'share_tag') {
    $cache = wp_cache_get($id, "xt_catalog_" . "{$type}_relationships");
    return $cache;
}

function xt_row_catalog($cat, $count) {
    $issub = $cat->parent > 0 ? 1 : 0;
    $_url = 'javascript:;';
    if ($cat->type == 'share') {
        $_url = xt_get_shares_search_url(array('cid' => $cat->id));
    } elseif ($cat->type == 'album') {
        $_url = xt_get_albums_search_url(array('cid' => $cat->id));
    }
    ?>
    <tr id="catalog-<?php echo $cat->id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td scope="row"><span><?php echo $cat->id; ?></span></td>
        <td class="name column-name"<?php echo $issub ? ' style="padding-left:20px;"' : '' ?>><strong><a class="row-title" href="<?php echo $_url;?>" target="_blank"><?php echo ($issub ? '— ' : '') . ($cat->title); ?></a></strong><br>
            <div class="row-actions">
                    <!--<span class="edit"><a href="">编辑</a> | </span>-->
                <span class="inline hide-if-no-js"><a href="#" class="editinline">快速编辑</a> | </span>
                <span class="delete"><a class="delete-catalog" href="javascript:;" data-value="<?php echo $cat->id; ?>">删除</a></span>
            </div>
            <div class="hidden" id="inline_<?php echo $cat->id; ?>">
                <div class="title"><?php echo $cat->title; ?></div>
                <div class="pic"><?php echo $cat->pic; ?></div>
                <div class="sort"><?php echo $cat->sort; ?></div>
            </div>
        </td>
        <td><?php echo $cat->is_front ? '前台分类' : '系统分类'; ?></td>
        <td><?php echo $cat->sort ?></td>
        <td><?php echo $cat->count; ?></td>
    </tr>
    <?php
}

function xt_catalogs_album_system($force = false) {
    $result = array();
    $cats = xt_catalogs_album($force);
    foreach ($cats as $cat) {
        if (!$cat->is_front) {
            $result[] = $cat;
        }
    }
    return $result;
}

function xt_catalogs_album_force() {
    xt_catalogs_album(true);
}

function xt_catalogs_album($force = false) {
    $result = array();
    if (!$force) {
        $result = get_option(XT_OPTION_CATALOG_ALBUM);
    }

    if (empty($result)) {
        $result = xt_root_catalogs_album();
        if (!empty($result)) {
            foreach ($result as &$root) {
                $childs = query_catalogs(array(
                    'nopage' => true,
                    'sortOrder' => 'count',
                    'parent' => $root->id,
                    'type' => 'album'
                        ));
                if (!empty($childs['catalogs'])) {
                    $_childIds = array();
                    foreach ($childs['catalogs'] as $child) {
                        $_childIds[] = $child->id;
                    }
                    if (empty($root->children)) {
                        $root->children = implode(',', $_childIds);
                        global $wpdb;
                        $wpdb->update(XT_TABLE_CATALOG, array('children' => implode(',', $_childIds)), array('id' => $root->id));
                    }
                }
                $root->child = $childs;
            }
        }
        update_option(XT_OPTION_CATALOG_ALBUM, $result);
    }
    return $result;
}

function xt_root_catalogs_album() {
    $result = query_catalogs(array(
        'nopage' => true,
        'sortOrder' => 'count',
        'type' => 'album'
            ));
    return $result['catalogs'];
}

function xt_catalogs_album_sub($cid) {
    $_catalogs = xt_catalogs_album();
    if ($cid == 0) {
        $result = array();
        foreach ($_catalogs as $_cat) {
            $result[] = $_cat;
        }
        return $result;
    } else {
        foreach ($_catalogs as $_cat) {
            if ($_cat->id == $cid) {
                if (isset($_cat->child)) {
                    $_child = $_cat->child;
                    return isset($_child['catalogs']) ? $_child['catalogs'] : array();
                }
            }
        }
    }

    return array();
}

function xt_catalogs_share_system($force = false) {
    $result = array();
    $cats = xt_catalogs_share($force);
    foreach ($cats as $cat) {
        if (!$cat->is_front) {
            $result[] = $cat;
        }
    }
    return $result;
}

function xt_catalogs_share_force() {
    xt_catalogs_share(true);
}

function xt_catalogs_share($force = false) {
    $result = array();
    if (!$force) {
        $result = get_option(XT_OPTION_CATALOG_SHARE);
    }

    if (empty($result)) {
        $result = xt_root_catalogs_share();
        if (!empty($result)) {
            foreach ($result as &$root) {
                $childs = query_catalogs(array(
                    'nopage' => true,
                    'sortOrder' => 'count',
                    'parent' => $root->id
                        ));
                if (!empty($childs['catalogs'])) {
                    $_childIds = array();
                    foreach ($childs['catalogs'] as $child) {
                        $tags = query_tags(array('page' => 1, 'tag_per_page' => 12, 'cid' => $child->id));
                        if (!empty($tags['tags'])) {
                            $child->tags = $tags['tags'];
                        } else {
                            $child->tags = array();
                        }
                        $_childIds[] = $child->id;
                    }
                    if (empty($root->children)) {
                        $root->children = implode(',', $_childIds);
                        global $wpdb;
                        $wpdb->update(XT_TABLE_CATALOG, array('children' => implode(',', $_childIds)), array('id' => $root->id));
                    }
                }
                $root->child = $childs;
            }
        }
        update_option(XT_OPTION_CATALOG_SHARE, $result);
    }
    return $result;
}

function xt_root_catalogs_share() {
    $result = query_catalogs(array(
        'nopage' => true,
        'sortOrder' => 'count'
            ));
    return $result['catalogs'];
}

function xt_catalogs_share_sub($cid) {
    $_catalogs = xt_catalogs_share();
    if ($cid == 0) {
        $result = array();
        foreach ($_catalogs as $_cat) {
            $result[] = $_cat;
        }
        return $result;
    } else {
        foreach ($_catalogs as $_cat) {
            if ($_cat->id == $cid) {
                if (isset($_cat->child)) {
                    $_child = $_cat->child;
                    return isset($_child['catalogs']) ? $_child['catalogs'] : array();
                }
            }
        }
    }

    return array();
}

function xt_get_catalog($id) {
    global $wpdb;
    $cat = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . XT_TABLE_CATALOG . ' WHERE id=%d', $id));
    return $cat;
}

function xt_have_catalogs() {
    global $xt_catalog_query;
    return $xt_catalog_query->have_catalogs();
}

function xt_in_the_catalog_loop() {
    global $xt_catalog_query;
    return $xt_catalog_query->in_the_loop;
}

function xt_rewind_catalogs() {
    global $xt_catalog_query;
    return $xt_catalog_query->rewind_catalogs();
}

function xt_the_catalog() {
    global $xt_catalog_query;
    $xt_catalog_query->the_catalog();
}

function & query_catalogs($args = '') {
    unset($GLOBALS['xt_catalog_query']);
    $GLOBALS['xt_catalog_query'] = new XT_Catalog_Query();
    $result = $GLOBALS['xt_catalog_query']->query($args);
    return $result;
}

class XT_Catalog_Query {

    var $catalogs;
    var $catalog_count = 0;
    var $found_catalogs = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $catalog;
    var $paginate_links = '';

    function init() {
        unset($this->catalogs);
        $this->catalog_count = 0;
        $this->found_catalogs = 0;
        $this->current_catalog = -1;
        $this->in_the_loop = false;

        unset($this->catalog);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb;

        $this->init();

        $defaults = array(
            'page' => 1,
            'catalog_per_page' => 10,
            'type' => 'share',
            'parent' => 0,
            'sortOrder' => 'id',
            'nopage' => false,
            'hasChild' => false,
            'sample' => true,
            'isFront' => ''
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);
        do_action_ref_array('xt_pre_get_catalogs', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $page = absint($page);
        $catalog_per_page = absint($catalog_per_page);
        $table = XT_TABLE_CATALOG;
        $fields = $sample ? ' id,title,pic,sort,parent,is_front,count,type,children ' : '*';
        $join = "";
        $where = $wpdb->prepare(' type = %s AND parent= %d ', $type, $parent);

        if ($isFront != '') {
            $where = $where . $wpdb->prepare(' is_front= %d ', $isFront ? 1 : 0);
        }
        if (!$nopage && $page && $catalog_per_page)
            $limits = $wpdb->prepare("LIMIT %d, %d", ($page - 1) * $catalog_per_page, $catalog_per_page);
        else {
            $limits = '';
        }
        if ($sortOrder == 'count') {
            $sortOrder = ' count DESC,id ';
        }
        $sql = "SELECT $fields FROM $table WHERE $where ORDER BY sort ASC,$sortOrder DESC $limits";
        $paged_catalogs = $wpdb->get_results($sql);

        $total_sql = "SELECT COUNT(*) FROM $table WHERE $where";
        $total_catalogs = $wpdb->get_var($total_sql);
        unset($sql, $total_sql);

        $this->found_catalogs = $total_catalogs;
        $this->catalogs = $paged_catalogs;
        $this->catalog_count = count($paged_catalogs);

        if ($total_catalogs > 1) {
            $total_page = ceil($total_catalogs / $catalog_per_page);
            $this->paginate_links = paginate_links(array(
                'base' => '#%#%',
                'format' => '',
                'end_size' => 3,
                'total' => $total_page,
                'current' => $page,
                'prev_text' => '上一页',
                'next_text' => '下一页',
                'mid_size' => 1
                    ));
        }

        return array(
            'catalogs' => $paged_catalogs,
            'total' => $total_catalogs
        );
    }

    function next_catalog() {

        $this->current_catalog++;

        $this->catalog = $this->catalogs[$this->current_catalog];
        return $this->catalog;
    }

    function the_catalog() {
        global $xt_catalog;
        $this->in_the_loop = true;

        if ($this->current_catalog == -1) // loop has just started
            do_action_ref_array('xt_catalog_loop_start', array(
                & $this
            ));

        $xt_catalog = $this->next_catalog();
        xt_setup_catalogdata($xt_catalog);
    }

    function have_catalogs() {
        if ($this->current_catalog + 1 < $this->catalog_count) {
            return true;
        } elseif ($this->current_catalog + 1 == $this->catalog_count && $this->catalog_count > 0) {
            do_action_ref_array('xt_catalog_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_catalogs();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_catalogs() {
        $this->current_catalog = -1;
        if ($this->catalog_count > 0) {
            $this->catalog = $this->catalogs[0];
        }
    }

}

function xt_catalog_exit($id, $name = '', $parent = 0, $type = 'share') {
    global $wpdb;
    $count = 0;
    if ($id > 0) {
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_CATALOG . " WHERE id = %d", $id));
    } elseif (!empty($name)) {
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_CATALOG . " WHERE title = %s AND parent=%d AND type=%s", $wpdb->escape($name), $parent, $wpdb->escape($type)));
    }

    if ($count > 0) {
        return true;
    }
    return false;
}

function xt_catalog_delete($ids) {
    global $wpdb;
    $ids = explode(',', $ids);
    if (!empty($ids)) {
        foreach ($ids as $id) {
            $id = intval($id);
            $_catalog = xt_get_catalog($id);
            if (!empty($_catalog)) {
                if ($_catalog->type == 'share') { //删除分享与分类的关系
                    xt_delete_share_catalog($id);
                    xt_delete_tag_catalog($id); //删除标签与分类的关系
                    $wpdb->query('DELETE FROM ' . XT_TABLE_CATALOG_ITEMCAT . ' WHERE id=' . $id); //删除自动分类配置
                } elseif ($_catalog->type == 'album') {
                    xt_delete_album_catalog($id); //删除专辑与分类的关系
                }

                $childs = $wpdb->get_col('SELECT id FROM ' . XT_TABLE_CATALOG . ' WHERE parent=' . $id);
                if (!empty($childs)) { //删除子分类
                    xt_catalog_delete(implode(',', $childs));
                }
                $wpdb->delete(XT_TABLE_CATALOG, array(
                    'id' => $id
                ));
            }
        }
    }
}

function xt_update_catalog_count($cid, $type = 'share') {
    global $wpdb;
    $table = XT_TABLE_SHARE_CATALOG;
    if ($type == 'album') {
        $table = XT_TABLE_ALBUM_CATALOG;
    }
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE cid = %d", $cid));
    $wpdb->update(XT_TABLE_CATALOG, compact('count'), array(
        'id' => $cid
    ));
    return $count;
}

function xt_delete_album_catalog($cid, $id = 0) {
    global $wpdb;
    $data = array();
    if ($cid > 0) {
        $data['cid'] = $cid;
    }
    if ($id > 0) {
        $data['id'] = $id;
    }
    if (!empty($data))
        $wpdb->delete(XT_TABLE_ALBUM_CATALOG, $data);
}

function xt_insert_album_catalog($catalogdata) {
    global $wpdb;
    extract(stripslashes_deep($catalogdata), EXTR_SKIP);
    $_catalog = xt_get_catalog($cid);
    $_album = xt_get_album($id);
    if (!empty($_catalog) && !empty($_album)) {
        $data = compact('id', 'cid', 'create_date_gmt');
        if ($wpdb->insert(XT_TABLE_ALBUM_CATALOG, $data)) {
            $count = xt_update_catalog_count($cid, 'album');
            return $id;
        }
    }
    return 0;
}

function xt_new_album_catalog($catalogdata) {

    $catalogdata['id'] = (int) $catalogdata['id'];
    $catalogdata['cid'] = $catalogdata['cid'];
    $catalogdata['create_date_gmt'] = current_time('mysql', 1);

    $id = xt_insert_album_catalog($catalogdata);

    return $id;
}

function xt_delete_share_catalog($cid, $id = 0) {
    global $wpdb;
    $data = array();
    if ($cid > 0) {
        $data['cid'] = $cid;
    }
    if ($id > 0) {
        $data['id'] = $id;
    }
    if (!empty($data))
        $wpdb->delete(XT_TABLE_SHARE_CATALOG, $data);
}

function xt_insert_share_catalog($catalogdata) {
    global $wpdb;
    extract(stripslashes_deep($catalogdata), EXTR_SKIP);
    $_catalog = xt_get_catalog($cid);
    $_share = get_share($id);
    if (!empty($_catalog) && !empty($_share)) {
        $data = compact('id', 'cid', 'create_date_gmt');
        if ($wpdb->insert(XT_TABLE_SHARE_CATALOG, $data)) {
            $count = xt_update_catalog_count($cid);
            return $id;
        }
    }
    return 0;
}

function xt_new_share_catalog($catalogdata) {

    $catalogdata['id'] = (int) $catalogdata['id'];
    $catalogdata['cid'] = $catalogdata['cid'];
    $catalogdata['create_date_gmt'] = current_time('mysql', 1);

    $id = xt_insert_share_catalog($catalogdata);

    return $id;
}

function xt_insert_catalog($catalogdata) {
    global $wpdb;
    extract(stripslashes_deep($catalogdata), EXTR_SKIP);
    if ($parent > 0) {
        $_catalog = xt_get_catalog($parent);
        if (empty($_catalog)) {
            return 0;
        }
    }
    if (xt_catalog_exit(0, $title, $parent, $type)) {
        return 0;
    }
    $data = compact('title', 'pic', 'sort', 'parent', 'is_front', 'keywords', 'description', 'type');
    if (isset($catalogdata['id'])) {
        $data['id'] = $catalogdata['id'];
    }
    if ($wpdb->insert(XT_TABLE_CATALOG, $data)) {
        if ($parent > 0) {
            $children = $wpdb->get_col('SELECT id FROM ' . XT_TABLE_CATALOG . ' WHERE parent=' . intval($parent));
            if (!empty($children)) {
                $wpdb->update(XT_TABLE_CATALOG, array('children' => implode(',', $children)), array('id' => intval($parent)));
            }
        }
        return $wpdb->insert_id;
    }
    return 0;
}

function xt_new_catalog($catalogdata) {
    if (isset($catalogdata['id'])) {
        $catalogdata['id'] = (int) $catalogdata['id'];
    }
    $catalogdata['title'] = strip_tags($catalogdata['title']);
    $catalogdata['pic'] = isset($catalogdata['pic']) ? $catalogdata['pic'] : '';
    $catalogdata['sort'] = isset($catalogdata['sort']) ? (int) $catalogdata['sort'] : 100;
    $catalogdata['parent'] = isset($catalogdata['parent']) ? (int) $catalogdata['parent'] : 0;
    $catalogdata['is_front'] = isset($catalogdata['is_front']) ? ($catalogdata['is_front'] ? 1 : 0) : 1;
    $catalogdata['keywords'] = isset($catalogdata['keywords']) ? $catalogdata['keywords'] : '';
    $catalogdata['description'] = isset($catalogdata['description']) ? $catalogdata['description'] : '';
    $catalogdata['type'] = isset($catalogdata['type']) ? $catalogdata['type'] : 'share';

    $id = xt_insert_catalog($catalogdata);

    return $id;
}

//Define GLOBAL query
global $xt_the_catalog_query;
$xt_the_catalog_query = new XT_Catalog_Query();
$xt_catalog_query = & $xt_the_catalog_query;