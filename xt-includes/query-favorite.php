<?php


/**
 * Manages Favorite
 *
 */

/*
 * The Loop. Favorite loop control.
 */

/**
 * Whether current Xintaoke query has results to loop over.
 *
 * @uses $xt_favorite_query
 *
 * @return bool
 */
function xt_have_favorites() {
	global $xt_favorite_query;
	return $xt_favorite_query->have_favorites();
}

/**
 * Whether the caller is in the Loop.
 *
 * @uses $xt_favorite_query
 *
 * @return bool True if caller is within loop, false if loop hasn't started or ended.
 */
function xt_in_the_favorite_loop() {
	global $xt_favorite_query;
	return $xt_favorite_query->in_the_loop;
}

/**
 * Rewind the loop favorites.
 *
 * @uses $xt_favorite_query
 *
 * @return null
 */
function xt_rewind_favorites() {
	global $xt_favorite_query;
	return $xt_favorite_query->rewind_favorites();
}

/**
 * Iterate the favorite index in the loop.
 *
 * @uses $xt_favorite_query
 */
function xt_the_favorite() {
	global $xt_favorite_query;
	$xt_favorite_query->the_favorite();
}

/**
 * Retrieve a list of favorites.
 *
 * The favorite list can be for the blog as a whole or for an individual share.
 *
 * The list of favorite arguments are 'status', 'orderby', 'create_date',
 * 'order', 'number', 'offset', and 'share_id'.
 *
 * @param mixed $args Optional. Array or string of options to override defaults.
 * @return array List of favorites.
 */
function & query_favorites($args = '') {
	unset ($GLOBALS['xt_favorite_query']);
	$GLOBALS['xt_favorite_query'] = new XT_Favorite_Query();
	$result = $GLOBALS['xt_favorite_query']->query($args); 
	return $result;
}

/**
 * Xintaoke Favorite Query class.
 *
 */
class XT_Favorite_Query {
	/**
	 * List of favorites.
	 *
	 * @access public
	 * @var array
	 */
	var $favorites;

	/**
	 * The amount of favorites for the current query.
	 *
	 * @access public
	 * @var int
	 */
	var $favorite_count = 0;
	/**
		 * Amount of favorites if limit clause was not used.
		 *
		 * @access public
		 * @var int
		 */
	var $found_favorites = 0;
	/**
	 * Index of the current item in the loop.
	 *
	 * @access public
	 * @var int
	 */
	var $current_share = -1;

	/**
	 * Whether the loop has started and the caller is in the loop.
	 *
	 * @access public
	 * @var bool
	 */
	var $in_the_loop = false;

	/**
	 * The current share ID.
	 *
	 * @access public
	 * @var object
	 */
	var $favorite;

	var $paginate_links = '';

	/**
	 * Initiates object properties and sets default values.
	 *
	 * @access public
	 */
	function init() {
		unset ($this->favorites);
		$this->favorite_count = 0;
		$this->found_favorites = 0;
		$this->current_favorite = -1;
		$this->in_the_loop = false;

		unset ($this->favorite);
		$this->paginate_links = '';
	}
	/**
	 * Execute the query
	 *
	 *
	 * @param string|array $query_vars
	 * @return int|array
	 */
	function query($query_vars) {
		global $wpdb;

		$this->init();

		$defaults = array (
			'page' => 1,
			'favorite_per_page' => 10,
			'user_id' => '',
			'type' => 1
		);

		$this->query_vars = wp_parse_args($query_vars, $defaults);
		do_action_ref_array('xt_pre_get_favorites', array (
			& $this
		));
		extract($this->query_vars, EXTR_SKIP);

		$page = absint($page);
		$favorite_per_page = absint($favorite_per_page);
		$_table = _xt_get_favorite_table($type);
		$fields = $_table . '.*,' . XT_TABLE_FAVORITE . '.type as fav_type';
		$join = " INNER JOIN $_table ON $_table.id=" . XT_TABLE_FAVORITE . '.object_id ';
		$where = $wpdb->prepare(' user_id = %d AND type = %d ', $user_id, $type);

		if ($page && $favorite_per_page)
			$limits = $wpdb->prepare("LIMIT %d, %d", ($page -1) * $favorite_per_page, $favorite_per_page);
		else {
			$limits = '';
		}

		$sql = "SELECT $fields FROM " . XT_TABLE_FAVORITE . " $join WHERE $where ORDER BY create_date DESC $limits";
		$paged_favorites = $wpdb->get_results($sql);
		$paged_favorites = apply_filters_ref_array('xt_the_favorites', array (
			$paged_favorites,
			& $this
		));
		$total_sql = "SELECT COUNT(*) FROM " . XT_TABLE_FAVORITE . " WHERE $where";
		$total_favorites = $wpdb->get_var($total_sql);
		unset ($sql, $total_sql);

		$this->found_favorites = $total_favorites;
		$this->favorites = $paged_favorites;
		$this->favorite_count = count($paged_favorites);

		if ($total_favorites > 1) {
			$total_page = ceil($total_favorites / $favorite_per_page);
			$this->paginate_links = paginate_links(array (
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

		return array (
			'favorites' => $paged_favorites,
			'total' => $total_favorites
		);
	}

	/**
	 * Set up the next favorite and iterate current favorite index.
	 *
	 * @access public
	 *
	 * @return object Next favorite.
	 */
	function next_favorite() {

		$this->current_favorite++;

		$this->favorite = $this->favorites[$this->current_favorite];
		return $this->favorite;
	}

	/**
	 * Sets up the current favorite.
	 *
	 * Retrieves the next favorite, sets up the favorite, sets the 'in the loop'
	 * property to true.
	 *
	 * @access public
	 * @uses $favorite
	 * @uses do_action_ref_array() Calls 'xt_favorite_loop_start' if loop has just started
	 */
	function the_favorite() {
		global $xt_favorite;
		$this->in_the_loop = true;

		if ($this->current_favorite == -1) // loop has just started
			do_action_ref_array('xt_favorite_loop_start', array (
				& $this
			));

		$xt_favorite = $this->next_favorite();
		xt_setup_favoritedata($xt_favorite);
	}

	/**
	 * Whether there are more favorites available in the loop.
	 *
	 * Calls action 'loop_end', when the loop is complete.
	 *
	 * @access public
	 * @uses do_action_ref_array() Calls 'xt_favorite_loop_end' if loop is ended
	 *
	 * @return bool True if favorites are available, false if end of loop.
	 */
	function have_favorites() {
		if ($this->current_favorite + 1 < $this->favorite_count) {
			return true;
		}
		elseif ($this->current_favorite + 1 == $this->favorite_count && $this->favorite_count > 0) {
			do_action_ref_array('xt_favorite_loop_end', array (
				& $this
			));
			// Do some cleaning up after the loop
			$this->rewind_favorites();
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Rewind the favorites and reset favorite index.
	 *
	 * @access public
	 */
	function rewind_favorites() {
		$this->current_favorite = -1;
		if ($this->favorite_count > 0) {
			$this->favorite = $this->favorites[0];
		}
	}

}

function _xt_get_favorite_table($type = 1) {
	$_table = XT_TABLE_SHARE;
	switch ($type) {
		case 1 :
			$_table = XT_TABLE_SHARE;
			break;
		case 2 :
			$_table = XT_TABLE_ALBUM;
			break;
		case 3 :
			$_table = XT_TABLE_TOPIC;
			break;
		case 4 :
			$_table = XT_TABLE_GROUP;
			break;
		case 5 :
			$_table = XT_TABLE_BRAND;
			break;
	}
	return $_table;
}

function _xt_get_favorite_metakey($type = 1) {
	$_metakey = XT_USER_COUNT_FAV_SHARE;
	switch ($type) {
		case 1 :
			$_metakey = XT_USER_COUNT_FAV_SHARE;
			break;
		case 2 :
			$_metakey = XT_USER_COUNT_FAV_ALBUM;
			break;
		case 3 :
			$_metakey = XT_USER_COUNT_FAV_TOPIC;
			break;
		case 4 :
			$_metakey = XT_USER_COUNT_FAV_GROUP;
			break;
		case 5 :
			$_metakey = XT_USER_COUNT_FAV_BRAND;
			break;
	}
	return $_metakey;
}

/**
 * Get  a favorite.
 *
 */
function & xt_get_favorite($id = 0, $user_id = 0, $type = 1) {
	if ($id == 0) {
		return $GLOBALS['xt_favorite'];
	}
	global $wpdb;
	$favorite = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . XT_TABLE_FAVORITE . ' WHERE id = %d AND user_id = %d AND type= %d limit 1', $id, $user_id, $type));
	return $favorite;
}

/**
 * deletes a favorite.
 *
 */
function xt_delete_favorite($id, $user_id, $type) {
	global $wpdb;

	do_action('xt_delete_favorite', $id);

	if (!$wpdb->delete(XT_TABLE_FAVORITE, array (
			'id' => $id,
			'user_id' => $user_id,
			'type' => $type
		)))
		return false;
	do_action('xt_deleted_favorite', $id);
	$count = xt_update_object_favorite_count($id, $type);
	$userCount = xt_update_user_favorite_count($user_id, $type);
	return $count;
}

function xt_update_object_favorite_count($id, $type) {
	global $wpdb;
	$_table = _xt_get_favorite_table($type);
	$fav_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_FAVORITE . " WHERE id = %d AND type=%d", $id, $type));

	$wpdb->update($_table, compact('fav_count'), array (
		'id' => $id
	));
	return $fav_count;
}

function xt_update_user_favorite_count($user_id, $type) {
	global $wpdb;
	$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_FAVORITE . " WHERE user_id = %d AND type=%d", $user_id, $type));
	//update user count meta
	$counts = get_user_meta($user_id, XT_USER_COUNT);
	$_metakey = _xt_get_favorite_metakey($type);
	xt_update_user_count($user_id, $_metakey, $count);
	return $count;
}

/**
 * Inserts a favorite to the database.
 *
 * The available favorite data key names are  'create_date','parent', 'status', and 'user_id'.
 *
 *
 * @param array $favoritedata Contains information on the favorite.
 * @return int The new favorite's ID.
 */
function xt_insert_favorite($favoritedata) {
	global $wpdb;
	extract(stripslashes_deep($favoritedata), EXTR_SKIP);

	if (!isset ($create_date))
		$create_date = current_time('mysql');

	$data = compact('id', 'user_id', 'type', 'create_date');
	$wpdb->replace(XT_TABLE_FAVORITE, $data);
	$count = xt_update_object_favorite_count($id, $type);
	xt_update_user_favorite_count($user_id, $type);
	return $count;
}

/**
 * Adds a new favorite to the database.
 *
 *
 * @param array $favoritedata Contains information on the favorite.
 * @return int The ID of the favorite after adding.
 */
function xt_new_favorite($favoritedata) {

	$favoritedata['id'] = (int) $favoritedata['id'];
	$favoritedata['user_id'] = (int) $favoritedata['user_id'];
	$favoritedata['type'] = (int) $favoritedata['type'];
	$favoritedata['create_date'] = current_time('mysql');

	$id = xt_insert_favorite($favoritedata);

	return $id;
}

//Define GLOBAL query
global $xt_the_favorite_query;
$xt_the_favorite_query = new XT_Favorite_Query();
$xt_favorite_query = & $xt_the_favorite_query;