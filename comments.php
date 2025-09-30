<?php

/* If post is protected by a password and the visitor has not yet
entered the password - will return early without loading the comments.*/
if ( post_password_required() )
    return;
?>

<div class="comments-area">

    <?php if ( have_comments() ) : ?>

      <!-- Display comment counter if there are comments -->
      <div class="comment-title-count">
        <h2 align="center">
          <?php printf( _nx( '1 comment', '%1$s comments', get_comments_number(), 'comments title' ), number_format_i18n( get_comments_number() ) ); ?>
        </h2>
      </div>

      <!-- List blog comments -->
      <div class="comment-list">
          <?php //wp_list_comments('type=comment&callback=customized_comment'); ?>
      </div>
    <?php endif; ?>

      <!-- Check whether comments are open, if so - dispaly comment form -->
      <?php if ( comments_open()  ) :
              comment_form(); // TO BE FORMATTED
            endif;
        ?>

</div>
