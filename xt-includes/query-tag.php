<?php

function xt_tag_default_sort($tag) {
    if (xt_tag_is_useless($tag)) {
        return 1000;
    }
    return 100;
}

function xt_tag_is_useless($tag) {
    if (is_numeric($tag)) {
        return true;
    }
    global $xt_useless_tags;
    if (empty($xt_useless_tags))
        $xt_useless_tags = include_once 'data-useless-tags.php';;
    if (in_array($tag, $xt_useless_tags)) {
        return true;
    }
    return false;
}

function xt_catalog_checklist($type, $catalogs) {
    if (!empty($catalogs)) {
        foreach ($catalogs as $cat) {
            echo '<li class="popular-category"><label class="selectit"><input value="' . $cat->id . '" type="checkbox" name="' . $type . '_category[]"> ' . $cat->title . '</label>';
            if (isset($cat->child) && !empty($cat->child)) {
                $childrens = $cat->child['catalogs'];
                echo '<ul class="children">';
                foreach ($childrens as $child) {
                    echo '<li class="popular-category"><label class="selectit"><input value="' . $child->id . '" type="checkbox" name="' . $type . '_category[]"> ' . $child->title . '</label>';
                }
                echo '</ul>';
            }
            echo '</li>';
        }
    }
}

function xt_row_tag($tag, $count, $cid = 0) {
    $_tagUrl = xt_get_shares_search_url(array(
        's' => $tag->title,
        'cid' => $cid
            ));
    $terms = xt_get_catalog_terms_cache($tag->id);
    $as = array();
    $cids = array();
    if (!empty($terms)) {
        foreach ($terms as $term) {
            $_url = add_query_arg(array('cid' => $term->id, 'paged' => 1, 's' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $as[] = '<a href="http://' . $_url . '">' . $term->title . '</a>';
            $cids[] = $term->id;
        }
    }
    ?>
    <tr id="tag-<?php echo $tag->id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td scope="row"><span><?php echo $tag->id; ?></span></td>
        <td class="name column-name"><strong><a class="row-title" href="<?php echo $_tagUrl; ?>" target="_blank"><?php echo ($tag->title); ?></a></strong><br>
            <div class="row-actions">
                    <!--<span class="edit"><a href="">编辑</a> | </span>-->
                <span class="inline hide-if-no-js"><a href="#" class="editinline">快速编辑</a> | </span>
                <span class="delete"><a class="delete-tag" href="javascript:;" data-value="<?php echo $tag->id; ?>">删除</a></span>
            </div>
            <div class="hidden" id="inline_<?php echo $tag->id; ?>">
                <div class="title"><?php echo $tag->title; ?></div>
                <div class="sort"><?php echo $tag->sort; ?></div>
                <div class="cids"><?php echo implode(',', $cids); ?></div>
                <div class="cid"><?php echo (isset($tag->cid) ? $tag->cid : 0); ?></div>
            </div>
        </td>
        <td><?php echo implode('&nbsp;,&nbsp;', $as); ?></td>
        <td><?php echo $tag->sort ?></td>
        <td><?php echo $tag->count; ?></td>
    </tr>
    <?php
}

function xt_tags_pagination_links() {
    echo xt_get_tags_pagination_links();
}

function xt_get_tags_pagination_links() {
    global $xt_tag_query;
    return apply_filters('xt_get_tags_pagination_links', $xt_tag_query->paginate_links);
}

function xt_tags_pagination_count() {
    echo xt_get_tags_pagination_count();
}

function xt_get_tags_pagination_count() {
    global $xt_tag_query;
    $pag_page = $xt_tag_query->get('page');
    $pag_num = $xt_tag_query->get('tag_per_page');
    $start_num = intval(($pag_page - 1) * $pag_num) + 1;
    $from_num = $start_num;
    $to_num = ($start_num + ($pag_num - 1) > $xt_tag_query->found_tags) ? $xt_tag_query->found_tags : $start_num + ($pag_num - 1);
    $total = $xt_tag_query->found_tags;

    return apply_filters('xt_get_tags_pagination_count', sprintf('%1$s to %2$s (共 %3$s 标签)', $from_num, $to_num, $total));
}

function xt_get_tag($id, $name = '') {
    global $wpdb;
    $tag = array();
    if ($id > 0) {
        $tag = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . XT_TABLE_SHARE_TAG . ' WHERE id=%d', $id));
    } elseif (!empty($name)) {
        $tag = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . XT_TABLE_SHARE_TAG . ' WHERE title=%s', $wpdb->escape($name)));
    }

    return $tag;
}

function xt_have_tags() {
    global $xt_tag_query;
    return $xt_tag_query->have_tags();
}

function xt_in_the_tag_loop() {
    global $xt_tag_query;
    return $xt_tag_query->in_the_loop;
}

function xt_rewind_tags() {
    global $xt_tag_query;
    return $xt_tag_query->rewind_tags();
}

function xt_the_tag() {
    global $xt_tag_query;
    $xt_tag_query->the_tag();
}

function & query_tags($args = '') {
    unset($GLOBALS['xt_tag_query']);
    $GLOBALS['xt_tag_query'] = new XT_Tag_Query();
    $result = $GLOBALS['xt_tag_query']->query($args);
    return $result;
}

class XT_Tag_Query {

    var $tags;
    var $tag_count = 0;
    var $found_tags = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $tag;
    var $paginate_links = '';

    function init() {
        unset($this->tags);
        $this->tag_count = 0;
        $this->found_tags = 0;
        $this->current_tag = -1;
        $this->in_the_loop = false;

        unset($this->tag);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb;

        $this->init();

        $defaults = array(
            'page' => 1,
            'tag_per_page' => 10,
            'cid' => 0,
            's' => ''
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);
        do_action_ref_array('xt_pre_get_tags', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $page = absint($page);
        $tag_per_page = absint($tag_per_page);
        $table = XT_TABLE_SHARE_TAG;
        $fields = XT_TABLE_SHARE_TAG . '.*';
        $orderBy = XT_TABLE_SHARE_TAG . ".sort ASC," . XT_TABLE_SHARE_TAG . ".count DESC";
        $join = "";
        $totalJoin = "";
        $where = "";
        $groupby = "";
        $totalgroupby = "";
        if ($cid > 0) {
            global $xt_catalog;
            if (empty($xt_catalog) || $xt_catalog->id != $cid) {
                $xt_catalog = xt_get_catalog($cid);
            }
            if (!empty($xt_catalog)) {
                $table = XT_TABLE_SHARE_TAG_CATALOG;
                $orderBy = "sort ASC," . XT_TABLE_SHARE_TAG_CATALOG . ".count DESC";
                $fields = XT_TABLE_SHARE_TAG_CATALOG . ".cid," . XT_TABLE_SHARE_TAG_CATALOG . ".sort," . XT_TABLE_SHARE_TAG . ".id," . XT_TABLE_SHARE_TAG . ".title," . XT_TABLE_SHARE_TAG . ".is_hot," . XT_TABLE_SHARE_TAG_CATALOG . ".count," . XT_TABLE_SHARE_TAG . ".nums ";
                $join = " INNER JOIN " . XT_TABLE_SHARE_TAG . " ON " . XT_TABLE_SHARE_TAG . ".id=" . XT_TABLE_SHARE_TAG_CATALOG . ".id ";
                if (isset($xt_catalog->children) && !empty($xt_catalog->children)) {
                    $fields = XT_TABLE_SHARE_TAG_CATALOG . ".cid," . XT_TABLE_SHARE_TAG_CATALOG . ".sort AS sort,min(" . XT_TABLE_SHARE_TAG_CATALOG . ".sort) AS childSort," . XT_TABLE_SHARE_TAG . ".id," . XT_TABLE_SHARE_TAG . ".title," . XT_TABLE_SHARE_TAG . ".is_hot,max(" . XT_TABLE_SHARE_TAG_CATALOG . ".count) AS count," . XT_TABLE_SHARE_TAG . ".nums ";
                    $orderBy = "sort ASC,childSort ASC," . XT_TABLE_SHARE_TAG_CATALOG . ".count DESC";
                    $where .= " AND " . XT_TABLE_SHARE_TAG_CATALOG . ".cid in(" . $wpdb->escape($xt_catalog->children) . "," . $cid . ") ";
                    $groupby .="GROUP BY " . XT_TABLE_SHARE_TAG . ".id";
                    $totalgroupby.="GROUP BY " . XT_TABLE_SHARE_TAG_CATALOG . ".id";
                } else {
                    $where = $wpdb->prepare(" AND " . XT_TABLE_SHARE_TAG_CATALOG . ".cid=%d", $cid);
                    $groupby .="GROUP BY " . XT_TABLE_SHARE_TAG . ".id";
                }
            }
        } elseif ($cid == -1) {
            $join = '';
            $where = ' AND ' . XT_TABLE_SHARE_TAG . '.id NOT IN (SELECT ID FROM ' . XT_TABLE_SHARE_TAG_CATALOG . ')';
        }

        if (!empty($s)) {
            $where.=' AND ' . XT_TABLE_SHARE_TAG . '.title like \'%' . $wpdb->escape($s) . '%\'';
            if ($table != XT_TABLE_SHARE_TAG) {
                $totalJoin = " INNER JOIN " . XT_TABLE_SHARE_TAG . " ON " . XT_TABLE_SHARE_TAG . ".id=" . XT_TABLE_SHARE_TAG_CATALOG . ".id ";
            }
        }

        if ($page && $tag_per_page)
            $limits = $wpdb->prepare("LIMIT %d, %d", ($page - 1) * $tag_per_page, $tag_per_page);
        else {
            $limits = '';
        }
        $sql = "SELECT $fields FROM $table $join WHERE 1=1 $where {$groupby} ORDER BY $orderBy $limits";
        $paged_tags = $wpdb->get_results($sql);
        $total_sql = "SELECT COUNT(DISTINCT($table.id)) FROM $table $totalJoin WHERE 1=1 $where";
        $total_tags = $wpdb->get_var($total_sql);
        unset($sql, $total_sql);

        $this->found_tags = $total_tags;
        $this->tags = $paged_tags;
        $this->tag_count = count($paged_tags);
        if ($total_tags > 1) {
            $total_page = ceil($total_tags / $tag_per_page);
            $this->paginate_links = paginate_links(array(
                'base' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_share' ? add_query_arg('paged', '%#%', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) : '#%#%',
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
            'tags' => $paged_tags,
            'total' => $total_tags
        );
    }

    function get($query_var) {
        if (isset($this->query_vars[$query_var]))
            return $this->query_vars[$query_var];

        return '';
    }

    function next_tag() {

        $this->current_tag++;

        $this->tag = $this->tags[$this->current_tag];
        return $this->tag;
    }

    function the_tag() {
        global $xt_tag;
        $this->in_the_loop = true;

        if ($this->current_tag == -1) // loop has just started
            do_action_ref_array('xt_tag_loop_start', array(
                & $this
            ));

        $xt_tag = $this->next_tag();
        xt_setup_tagdata($xt_tag);
    }

    function have_tags() {
        if ($this->current_tag + 1 < $this->tag_count) {
            return true;
        } elseif ($this->current_tag + 1 == $this->tag_count && $this->tag_count > 0) {
            do_action_ref_array('xt_tag_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_tags();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_tags() {
        $this->current_tag = -1;
        if ($this->tag_count > 0) {
            $this->tag = $this->tags[0];
        }
    }

}

function xt_tag_catalog_exit($id, $cid) {
    global $wpdb;
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_SHARE_TAG_CATALOG . " WHERE id = %d AND cid = %d", $wpdb->escape($id), $wpdb->escape($cid)));
    if ($count > 0) {
        return true;
    }
    return false;
}

function xt_tag_exit($name) {
    global $wpdb;
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_SHARE_TAG . " WHERE title = %s", $wpdb->escape($name)));
    if ($count > 0) {
        return true;
    }
    return false;
}

function xt_tag_delete($ids) {
    global $wpdb;
    $ids = explode(',', $ids);
    if (!empty($ids)) {
        foreach ($ids as $id) {
            $id = intval($id);
            $wpdb->delete(XT_TABLE_SHARE_TAG_CATALOG, array(
                'id' => $id
            ));
            //删除标签与分类的关系
            $wpdb->delete(XT_TABLE_SHARE_TAG, array(
                'id' => $id
            ));
            //删除标签
        }
    }
}

function xt_delete_tag_catalog($cid, $id = 0) {
    global $wpdb;
    $data = array();
    if ($cid > 0) {
        $data['cid'] = $cid;
    }
    if ($id > 0) {
        $data['id'] = $id;
    }
    if (!empty($data))
        $wpdb->delete(XT_TABLE_SHARE_TAG_CATALOG, $data);
}

function xt_insert_tag_catalog($tagdata) {
    global $wpdb;
    extract(stripslashes_deep($tagdata), EXTR_SKIP);
    $data = compact('id', 'cid', 'sort');
    if ($wpdb->insert(XT_TABLE_SHARE_TAG_CATALOG, $data)) {
        return $id;
    }
    return 0;
}

function xt_new_tag_catalog($tagdata) {

    $tagdata['id'] = (int) $tagdata['id'];
    $tagdata['cid'] = $tagdata['cid'];
    $tagdata['sort'] = isset($tagdata['sort']) ? $tagdata['sort'] : 100;

    $id = xt_insert_tag_catalog($tagdata);

    return $id;
}

function xt_insert_tag($tagdata) {
    global $wpdb;
    extract(stripslashes_deep($tagdata), EXTR_SKIP);

    if (xt_tag_exit($title)) {
        return 0;
    }
    $data = compact('title', 'sort', 'is_hot');
    if ($wpdb->insert(XT_TABLE_SHARE_TAG, $data)) {
        return $wpdb->insert_id;
    }
    return 0;
}

function xt_new_tag($tagdata) {

    $tagdata['title'] = strip_tags($tagdata['title']);
    if (!isset($tagdata['sort'])) {
        $tagdata['sort'] = xt_tag_default_sort($tagdata['title']);
    } else {
        $tagdata['sort'] = (int) $tagdata['sort'];
    }

    $tagdata['is_hot'] = isset($tagdata['is_hot']) ? ($tagdata['is_hot'] ? 1 : 0) : 0;

    $id = xt_insert_tag($tagdata);

    return $id;
}

//Define GLOBAL query
global $xt_the_tag_query;
$xt_the_tag_query = new XT_Tag_Query();
$xt_tag_query = & $xt_the_tag_query;