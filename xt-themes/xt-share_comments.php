<div class="row-fluid">
	<div id="X_Share-Comments-List" class="span12" style="min-height:0px;">
		<ul class="media-list">
		<?php	
			$_isAjax=defined('DOING_AJAX')?true:false;
			$_page = 1;
			if($_isAjax){
				$_share_id = $_POST['share_id'];
				$_page = isset($_POST['page'])?$_POST['page']:1;
			}else{
				$_share_id = get_the_share_id();
			}
			if (!empty($_share_id)&&((!$_isAjax&&get_the_share_commentcount()>0)||$_isAjax)) {
					query_comments(array('share_id'=>$_share_id,'page'=>$_page));
					while(xt_have_comments()) :
						xt_comment_template();
					endwhile;
		}
		?>
		</ul>
		<div id="X_Share-Comments-Page" class="xt-share-comments-page">
			<div class="pagination" style="text-align:right">
				<?php xt_comments_pagination_links();?>
			</div>
		</div>	
	</div>
</div>
