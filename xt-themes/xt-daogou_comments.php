<?php
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (!comments_open()) :
    return;
endif;
if (post_password_required())
    return;
?>
<div id="comments" class="span12" style="min-height:0px;">
    <h3 style="border-bottom: 1px solid #E5E5E5;">评论(<?php echo get_comments_number(); ?>)</h3>
    <?php if (have_comments()) : ?>
        <ul class="media-list">
            <?php wp_list_comments(array('callback' => 'xt_daogou_comment', 'style' => 'ul', 'reverse_top_level' => true, 'max_depth' => 5)); ?>
        </ul>
        <?php
    endif;
    if (have_comments())
        : if (get_comment_pages_count() > 1 && get_option('page_comments'))
            :
            ?>

            <div id="X_Pagination-Bottom" class="clearfix">
                <div class="pagination xt-pagination-links" style="padding:0;margin:0 auto;">
                    <?php paginate_comments_links(array('type' => 'list')); ?>	
                </div>
            </div>
            <?php
        endif;

        /* If there are no comments and comments are closed, let's leave a note.
         * But we only want the note on posts and pages that had comments in the first place.
         */
        if (!comments_open() && get_comments_number())
            :
            ?>
            <p class="nocomments">评论功能已关闭</p>
            <?php
        endif;
    endif;
    comment_form();
    ?>

</div>