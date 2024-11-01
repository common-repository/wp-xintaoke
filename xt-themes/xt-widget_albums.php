<?php
global $xt, $wp_query, $xt_albums, $xt_user;

if ($xt->is_album) {
?>
<div class="xt-share xt-share-albums">
	<div class="xt-inner xt-share-inner clearfix">
		<!-- Item Img -->
		<div class="xt-share-image clearfix">
			<?php xt_get_user_info();?>
		</div>
		<!-- //Item Img -->
		<div class="xt-share-main clearfix">
			<!-- Item header -->
			<div class="xt-share-header clearfix">
			</div>
			<!-- //Item header -->
			<!-- Item content -->
			<div class="xt-share-content">
                <h2 class="xt-share-albums-title">TA的更多专辑 <a target="_blank" href="<?php xt_the_user_url()?>#album" class="xt-share-albums-more">查看全部</a></h2>
                	<?php

	$isBig = xt_albumdisplay()=='big'?true:false;
	while (xt_have_albums()) {
		xt_the_album();
		$_picurls = $isBig?get_the_album_picurls_big():get_the_album_picurls_small();
		?>
				<div class="xt-share-albums-list">
					<h3 class="xt-share-album-title">
						<span class="xt-r"><?php echo get_the_album_sharecount()?>个分享</span><a href="<?php echo get_the_album_url();?>"><?php echo wp_trim_words(get_the_album_title(), 10);?></a>
					</h3>
					<ul>
						<li><a href="<?php echo get_the_album_url();?>">
							<?php echo !empty($_picurls[1])?'<img src="'.$_picurls[1].'"/>':'';?>
							<?php echo !empty($_picurls[2])?'<img src="'.$_picurls[2].'"/>':'';?>
							<?php echo !empty($_picurls[3])?'<img src="'.$_picurls[3].'"/>':'';?>
						</a></li>
						<div class="clearfix"></div>
					</ul>
				</div>
		<?php }?>		
			</div>
			<!-- //Item content -->
			<!-- Item footer -->
			<div class="xt-share-footer clearfix">
				
			</div>
			<!-- //Item footer -->
		</div>
		<div class="xt-share-separator"></div>
	</div>
</div>		
<?php


}