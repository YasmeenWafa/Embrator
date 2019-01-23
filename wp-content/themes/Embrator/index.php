<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

get_header(); ?>

<div class="embrator__template embrator__news">
    <div class="embrator__hero">
        <?php 
        $heroImage1 = get_field('hero_image_news_1',303);
        $heroImage2 = get_field('hero_image_news_2',303);
        $heroImage_size = "large";
        $heroImage_url_1 = $heroImage1["sizes"][$heroImage_size];
        $heroImage_url_2= $heroImage2["sizes"][$heroImage_size];
      ?>
        <div class="embrator__hero__left embrator__hero__half">
            <figure class="embrator__hero__blue embrator__hero__image_3" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/bluebkg.png)"></figure>
            <figure class="embrator__hero__stripes embrator__hero__image_2" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/category_background.png)"></figure>
            <figure class="embrator__hero__image embrator__hero__image_1" style="background-image: url(<?php echo $heroImage_url_1; ?> )">
                <h1 class="embrator__hero__title">
                    <?php the_field('hero_title_news_1',303) ?>
                </h1>
                <p class="embrator__hero__caption">
                    <?php the_field('hero_caption_news_1',303) ?>
                </p>
            </figure>
        </div>
        <div class="embrator__hero__right embrator__hero__half">
            <figure class="embrator__hero__blue embrator__hero__image_3" style="background-image: url(<?php echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/bluebkg.png)"></figure>
            <figure class="embrator__hero__stripes embrator__hero__image_2" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/category_background.png)"></figure>
        <figure class="embrator__hero__image embrator__hero__image_1" style="background-image: url(<?php echo $heroImage_url_2; ?> )">
            <h1 class="embrator__hero__title">
                <?php the_field('hero_title_news_2',303) ?>
            </h1>
            <p class="embrator__hero__caption">
                <?php the_field('hero_caption_news_2',303) ?>
            </p>
        </figure>
            
        </div>
    </div>
    
    <main class="main-content stories">
        <?php if ( have_posts() ) : ?>

        <?php /* Start the Loop */

         ?>
        <?php while ( have_posts() ) : the_post();   
        //     global $num;
        //   $num = $wp_query->current_post;
        ?>
        <!-- <?php //echo $num; ?> -->
        <?php
        
         get_template_part( 'template-parts/content_stories', get_post_format() );
         ?>
        <?php endwhile; ?>
         <?php //echo do_shortcode('[ajax_load_more post_type="post" scroll="false" button_label="Load More Posts"]') ?>

        <?php else : ?>
        <?php get_template_part( 'template-parts/content_stories', 'none' ); ?>

        <?php endif; // End have_posts() check. ?>

        <?php /* Display navigation to next/previous pages when applicable */ ?>
        <!-- <?php
			//if ( function_exists( 'foundationpress_pagination' ) ) :
			//	foundationpress_pagination();
		//	elseif ( is_paged() ) :
			?>
        <nav id="post-nav">
            <div class="post-previous">
                <?php //next_posts_link( __( '&larr; Older posts', 'foundationpress' ) ); ?>
            </div>
            <div class="post-next">
                <?php //previous_posts_link( __( 'Newer posts &rarr;', 'foundationpress' ) ); ?>
            </div>
        </nav>
        <?php //endif; ?> -->
        <?php 
        // Show Load More Button Only if pages is more than 1 page 
            global $wp_query;
            if ($wp_query->max_num_pages > 1) {
                echo '<a href="" class="loadMore eButton">Load More Posts</a>';
            }
        ?>
    </main>
</div>
<?php get_footer();
