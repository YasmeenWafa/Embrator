<?php
/*
Template Name: Store Locator
*/
get_header(); ?>
  <div class="embrator__template embrator__storeLocator">
    <div class="embrator__hero">
      <?php 
        $heroImage = get_field('hero_image_faq');
        $heroImage_size = "large";
        $heroImage_url = $heroImage["sizes"][$heroImage_size];
      ?>
      <figure class="embrator__hero__blue embrator__hero__image_3" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/bluebkg.png)" ></figure>
      <figure class="embrator__hero__stripes embrator__hero__image_2" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/category_background.png)"></figure>
      <figure class="embrator__hero__image embrator__hero__image_1" style="background-image: url(<?php echo $heroImage_url; ?> )"></figure>
      <h1 class="embrator__hero__title"><?php the_field('hero_title_faq') ?></h1>
    </div>
    <div class="Embrator__map">
      <?php do_shortcode('[google_map_easy id="1"]') ?>
    </div>
  </div>
<?php get_footer();