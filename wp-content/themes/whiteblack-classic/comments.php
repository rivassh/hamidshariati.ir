<br/>
<hr/>

<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @package WhiteBlack Classic
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}


// Verifica se ci sono commenti
if ( have_comments() ) :
?>

	<h3 class="comments-title">
        <?php
            $comment_count = get_comments_number();
            if ( '1' === $comment_count ) {
                printf( esc_html__( 'One comment on &ldquo;%s&rdquo;', 'whiteblack-classic' ), get_the_title() );
            } else {
                printf(
                    esc_html(
                        _n( '%1$s comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', $comment_count, 'whiteblack-classic' )
                    ),
                    number_format_i18n( $comment_count ),
                    get_the_title()
                );
            }
        ?>
    </h3><!-- .comments-title -->

    <ol class="comment-list">
        <?php
            // Mostra i commenti
            wp_list_comments(
                array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 50, // Imposta la dimensione dell'avatar
                    'callback'    => 'custom_comment_callback', // Callback personalizzato per la visualizzazione
                )
            );
        ?>
    </ol><!-- .comment-list -->

<?php endif; ?>

<?php
// Modifica la visualizzazione dei commenti con un callback personalizzato
function custom_comment_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <article class="comment-body">
            <header class="comment-meta">
                <div class="comment-author vcard">
                    <?php echo get_avatar( $comment, 50, '', '', array( 'class' => 'comment-avatar') ); ?>
                    <cite class="fn"><?php comment_author_link(); ?></cite>
                </div><!-- .comment-author -->

                <div class="comment-metadata">
                    <a href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
                        <time datetime="<?php comment_time( 'c' ); ?>">
                            <?php printf( esc_html__( '%1$s at %2$s', 'whiteblack-classic' ), get_comment_date(), get_comment_time() ); ?>
                        </time>
                    </a>
                </div><!-- .comment-metadata -->
            </header><!-- .comment-meta -->

            <div class="comment-content">
                <?php comment_text(); ?>
            </div><!-- .comment-content -->
			
            <div class="reply">
                <?php comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'whiteblack-classic' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            </div><!-- .reply -->
        </article><!-- .comment-body -->
    </li><!-- #comment-## -->
    <?php
}
?>
<table class="spazio15 stg-table" style="width:100%">
	<tr>
		<td style="auto"></td>
		<td style="width:350px">
			<?php
			// Modulo per aggiungere un nuovo commento
			if ( comments_open() ) :
		    comment_form();
			endif;
			?>
		</td>
		<td style="auto"></td>
	</tr>
</table>



<div class="blocco">
				<div class="a-sinistra"><?php previous_comments_link( __( '&larr; Older Comments', 'whiteblack-classic' ) ); ?></div>
				<div class="a-destra"><?php next_comments_link( __( 'Newer Comments &rarr;', 'whiteblack-classic' ) ); ?></div>
			</div>