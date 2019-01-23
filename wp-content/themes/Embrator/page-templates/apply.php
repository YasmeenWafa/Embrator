<?php
/*
Template Name: Apply
*/
get_header(); ?>
  <div class="embrator__template embrator__careers">
    <div class="embrator__hero">
      <?php 
        $heroImage = get_field('hero_image_careers');
        $heroImage_size = "large";
        $heroImage_url = $heroImage["sizes"][$heroImage_size];
      ?>
      <figure class="embrator__hero__blue embrator__hero__image_3" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/bluebkg.png)" ></figure>
      <figure class="embrator__hero__stripes embrator__hero__image_2" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/category_background.png)"></figure>
      <figure class="embrator__hero__image embrator__hero__image_1" style="background-image: url(<?php echo $heroImage_url; ?> )"></figure>
      <h1 class="embrator__hero__title"><?php the_field('hero_title_careers') ?></h1>
    </div>

    <section class="embrator__section">
    <?php //echo do_shortcode("[submit_job_form]")?>
    </section>           

    <section class="section section__branches" style="background-image: url(<?php
          echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/branches.png)" >
          <h3>Embrator Branches</h3>
      <h1>We have <span>100+</span> locations all over Egypt</h1>
      <a class="branches__button" href="<?php echo get_sub_field('branches_button',5) ?>">VIEW ALL BRANCHES</a>
    </section>
  </div>
<?php get_footer();