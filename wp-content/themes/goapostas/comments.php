<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package GoApostas
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

$goapostas_comment_count = get_comments_number();

$title = '<i class="icon-comments"></i>' . $goapostas_comment_count . ' ' . __('comentários', 'goapostas');
			
?>

<div id="comments" class="comments-area">

	<?php
	$comment_args = [
		'title_reply' => $title,
		'comment_field' => '<p class="comment-form-comment">'
							.'<picture></picture>'
							.'<label for="comment">' . __( 'Comment', 'goapostas' ) . '</label>'
							.'<textarea id="comment" name="comment" cols="45" rows="2" aria-required="true" placeholder="' .__('Comentar...', 'goapostas'). '" required="required"></textarea>'
							.'</p>',
		'label_submit' => __('Comentar', 'goapostas'),
	];
	comment_form($comment_args);

	// You can start editing here -- including this comment!

	if ( have_comments() ) :
		?>
		<!-- <h2 class="comments-title">
			<?php
			if ( '1' === $goapostas_comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'goapostas' ),
					'<span>' . get_the_title() . '</span>'
				);
			} else {
				printf( // WPCS: XSS OK.
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $goapostas_comment_count, 'comments title', 'goapostas' ) ),
					number_format_i18n( $goapostas_comment_count ),
					'<span>' . get_the_title() . '</span>'
				);
			}
			?>
		</h2> -->
		<!-- .comments-title -->

		<?php //the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
			$per_page = 3;
			$pages = intval( $goapostas_comment_count / $per_page );
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'per_page' => $per_page
			) );
			?>
		</ol><!-- .comment-list -->
		<div class="comment-navigation">
			<a href="#" id="more_comments_link" data-page="<?php echo $pages;  ?>" data-post="<?php echo get_the_ID(); ?>"><?php echo __('Mostrar mais comentários', 'goapostas'); ?></a>
		</div>

		<?php

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'goapostas' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().

	?>

</div><!-- #comments -->
