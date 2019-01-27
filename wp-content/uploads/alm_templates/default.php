<?php
global $num;
$num = $wp_query->current_post;
 ?>

<article id="post-<?php the_ID(); ?>" class="story story_<?php echo  $num; ?>">
	<a href="<?php echo get_permalink() ?>"></a>
	<figure class="embrator__hero__stripes story__stripes" style="background-image: url(<?php
	  echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/category_background.png)"></figure>
	  <figure class="story__thumbnail" style="background-image: url(<?php the_post_thumbnail_url();?>)"></figure>
    <header class="story__header">
        <?php foundationpress_entry_meta(); ?>
        <?php the_title( '<h1 class="story__title entry-title">', '</h1>' );?>
    </header>
    <div class="story__body">
        <p>
            <?php echo wp_trim_words( get_the_content(), 20," ... <a  class='read-more' href='".get_permalink()."'>Read More</a>" );?>
        </p>
    </div>
    <footer>
        <?php
			wp_link_pages(
				array(
					'before' => '<nav id="page-nav"><p>' . __( 'Pages:', 'foundationpress' ),
					'after'  => '</p></nav>',
					)
				);
				?>
    </footer>
</article>