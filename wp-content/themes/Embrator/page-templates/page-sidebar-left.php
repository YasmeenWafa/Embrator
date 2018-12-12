<?php
/*
Template Name: Left Sidebar
*/
get_header(); ?>

<?php get_template_part( 'template-parts/featured-image' ); ?>
<div class="main-container">
	<div class="main-grid sidebar-left">
		<main class="main-content">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'page' ); ?>
				<?php comments_template(); ?>
			<?php endwhile; ?>
		 </main>
	<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer();
// Home Deprecated 
<?php
/*
Template Name: Home
*/
get_header(); ?>
<?php
// check if the repeater field has rows of data
if (have_rows('homepage_bannerslider')): ?>
<header class="home__hero bannerSlider" role="banner" id="ver">
  <?php // loop through the rows of data
while (have_rows('homepage_bannerslider')):
the_row(); ?>
  <?php
$patternImage = get_sub_field('banner_slide_image');
$patternImage_size = "large";
$patternImage_url = $patternImage["sizes"][$patternImage_size];

$productImage = get_sub_field('banner_product_image');
$productImage_size = "large";
$productImage_url = $productImage["sizes"][$productImage_size];
?>
<section class="top">
  <figure class="bannerSlider-figure" style="background-image: url(<?php echo $patternImage_url; ?> )">
    <figure class="bannerSlider-product" style="background-image: url(<?php echo $productImage_url; ?> )"></figure>
    <div class="bannerSlider__title">
      <figcaption class="sliderHeadings">
          <p class="bannerSlider__title__top"><?php the_sub_field('banner_slide_caption_top') ?></p>
          <p class="bannerSlider__title__center"><?php the_sub_field('banner_slide_caption') ?></p>
      </figcaption>
    </div>
  </figure>
  </section>
  <section class="bottom">
  <figure class="bannerSlider-figure" style="background-image: url(<?php echo $patternImage_url; ?> )">
  </figure>
  </section>
  <?php // display a sub field value
endwhile; ?>
</header>
<?php
else:
// no rows found
endif;
?>
<!-- Section 2  -->
<section class="section section__intro ">
    <h2><?php the_field('intro_section_pre-title') ?></h2>
    <h1><?php the_field('intro_section_title') ?></h1>
    <div class="division">
        <figure class="division__image" style="background-image: url(<?php the_field('division_image') ?> )"></figure>
        <div class="division__video"><?php the_field('division_video') ?></div>
        <p class="division__caption" >
          <?php the_field('division_caption') ?>
        </p>
    </div>
</section>

<!-- Section 3  -->

<?php
// check if the repeater field has rows of data
if (have_rows('collection-slider')): ?>
<section class="section section__collectionsSlider">
  <?php // loop through the rows of data
while (have_rows('collection-slider')):
the_row(); ?>
  <?php
$collectionImage = get_sub_field('collectionsslider__image');
$collectionImage_size = "large";
$collectionImage_url = $collectionImage["sizes"][$collectionImage_size];
?>
                                        
  <figure class="collectionsSlider__image" style="background-image: url(<?php echo $collectionImage_url; ?> )">
    <div class="collectionsSlider__group">
      <h2><?php echo get_sub_field('collections_slider_background_pre') ?></h2>
      <h1><?php echo get_sub_field('collections_slider_background_title') ?></h1>
      <a href="<?php echo get_sub_field('collections_slider_button') ?>">View Collection</a>
    </div>
  </figure>
  <?php // display a sub field value
endwhile; ?>
</section>
<?php
else:
// no rows found
endif;
?>
<section class="section section__lastCategories">
  <h2><?php the_field('lastCats_pre') ?></h2>
  <h1><?php the_field('lastCats_title') ?></h1>
  <?php
// check if the repeater field has rows of data
if (have_rows('lastCats_cats')): ?>
  <div class="lastCats" id="scene" data-relative-input="true"  data-hover-only="false">
  <?php // loop through the rows of data
  $i = 0;
while (have_rows('lastCats_cats')):the_row(); 
?>
  <?php
$categoryImage = get_sub_field('cat_image');
$categoryImage_size = "large";
$categoryImage_url = $categoryImage["sizes"][$categoryImage_size];
$i++;
?>
    <a href="<?php echo get_sub_field('cat_link') ?>" class="cat cat-<?php echo $i?>"  data-relative-input="true"  data-hover-only="true">
      <div class="cat__background" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/bluebkg.png)" data-depth="0.2"></div>
      <div class="cat__pattern" data-depth="0.2" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/category_background.png)"></div>
      <figure data-depth="0.8" class="cat__image" style="background-image: url(<?php echo $categoryImage_url; ?> )"></figure>
      <h2 class="cat__title" data-depth="0.6">
        <?php echo get_sub_field('cat_title') ?>
      </h2>
    </a>
    <?php // display a sub field value
endwhile; ?>
</div>
<?php
else:
// no rows found
endif;
?>
      <a class="lastCats__button" href="<?php echo get_sub_field('last_categories_button') ?>">View Categories</a>
</section>
<section class="section section__branches" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/branches.png)" >
      <h3>Embrator Branches</h3>
  <h1>We have <span>100+</span> locations all over Egypt</h1>
  <a class="branches__button" href="<?php echo get_sub_field('branches_button') ?>">VIEW ALL BRANCHES</a>
</section>

<?php get_footer();