<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

get_header(); ?>
<?php get_template_part( 'template-parts/featured-image' ); ?>
<section class="embrator__singleStory">
    <aside class="embrator__section__social">
        <a class="embrator__section__social--link" href=""><i class="fa fa-facebook-f"></i></a>
        <a class="embrator__section__social--link" href=""><i class="fa fa-twitter"></i></a>
    </aside>
    <main class="embrator__section__main">
        <?php while ( have_posts() ) : the_post(); ?>
        <?php get_template_part( 'template-parts/content', '' ); ?>
        <?php endwhile; ?>
    </main>
</section>
<?php
	$next_post = get_next_post();
	$prev_post = get_previous_post();
	if (!empty( $next_post )): ?>
	<section class="section section__branches next_article_section">
		<?php if (get_the_post_thumbnail_url($next_post->ID)) { ?>	
			<figure class="section__background" data-interchange ="[<?php echo get_the_post_thumbnail_url($next_post->ID, 'featured-small' ); ?>, small], [<?php  echo get_the_post_thumbnail_url($next_post->ID, 'featured-medium' ); ?>, medium], [<?php  echo get_the_post_thumbnail_url($next_post->ID, 'featured-large' ); ?>, large], [<?php  echo get_the_post_thumbnail_url($next_post->ID, 'featured-xlarge' ); ?>, xlarge]" ></figure>
		<?php } else { ?>
			<figure class=" section__background" style="background-image: url(<?php
			echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/branches.png)"></figure>
		<?php } ?>

		<h3>Next Article</h3>
		
		<h1>
			<?php echo esc_attr( $next_post->post_title ); ?>
		</h1>
		<a class="branches__button" href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">Read Post</a>
	</section>
<?php endif; ?>
<?php get_footer();