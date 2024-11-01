/**
 * Created with JetBrains PhpStorm. User: xuheng Date: 12-8-8 Time: 下午2:00 To
 * change this template use File | Settings | File Templates.
 */
var templates = [{
	"name" : "list",
	"pre" : "list.png",
	'title' : '(带序号)文字列表',
	'preHtml' : '<p><label>显示行数：</label><input type="text" size="5" value="10" name="t_list_count"></p><p><label>样式：</label><input type="radio" name="t_list" value="ordered" checked>数字序号&nbsp;&nbsp;&nbsp;<input type="radio" name="t_list" value="unordered">圆心序号&nbsp;&nbsp;&nbsp;<input type="radio" name="t_list" value="unstyled">无序号</p>',
	"html" : function() {
		var count = parseInt(jQuery('#preview input[name="t_list_count"]')
				.val());
		if (isNaN(count)) {
			alert('列表行数必须为数字');
			return false;
		}
		var style = jQuery('#preview input[name="t_list"]:checked').val();
		var list = jQuery('<ul></ul>');
		if (style == 'ordered') {
			list = jQuery('<ol></ol>');
		}
		if (style == 'unstyled') {
			list.addClass('unstyled');
		}
		for (var i = 0; i < count; i++) {
			list.append('<li><a href="http://" target="_blank">链接' + (i + 1)
					+ '</a></li>');
		}
		return jQuery('<div></div>').append(list).html();
	}

}, {
	"name" : "table",
	"pre" : "table.png",
	'title' : '表格',
	'preHtml' : '<p><label>显示行数：</label><input type="text" size="5" value="5" name="t_table_row">&nbsp;&nbsp;&nbsp;<label>显示列数：</label><input type="text" size="5" value="5" name="t_table_col"></p><p><label>样式：</label><input type="checkbox" name="t_table" value="table-striped" checked>间隔色&nbsp;&nbsp;&nbsp;<input type="checkbox" name="t_table" value="table-bordered" checked>边框&nbsp;&nbsp;&nbsp;<input type="checkbox" name="t_table" value="table-condensed">紧凑</p>',
	"html" : function() {
		var row = parseInt(jQuery('#preview input[name="t_table_row"]').val());
		var col = parseInt(jQuery('#preview input[name="t_table_col"]').val());
		if (isNaN(row) || isNaN(col)) {
			alert('表格行数,列数必须为数字');
			return false;
		}
		var classes = [];
		jQuery('#preview input[name="t_table"]:checked').each(function() {
					classes.push(jQuery(this).val());
				});
		var table = jQuery('<table class="table"></table>');
		table.addClass(classes.join(' '));
		var tds = [];
		var ths = [];
		for (var i = 0; i < col; i++) {
			tds.push('<td></td>');
			ths.push('<th>标题</th>');
		}
		var tbody = jQuery('<tbody></tbody>');
		for (var i = 0; i < row; i++) {
			tbody.append('<tr>' + tds.join() + '</tr>');
		}
		table.append('<thead><tr>' + ths.join('') + '</tr></thead>');
		table.append(tbody);
		return jQuery('<div></div>').append(table).html();
	}

}];