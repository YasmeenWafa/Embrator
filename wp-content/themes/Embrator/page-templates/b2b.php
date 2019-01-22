<?php
/*
Template Name: B2B
*/
get_header(); ?>


<div class="b2b-page-container">
  <div class="b2b-page-header">
    <h1 class="b2b-page-header-title">Business To Business</h1>
    <img class="header-image" src="<?php the_field('header_image') ?>" alt="">
    <img class="blue-box" src="<?php echo get_stylesheet_directory_uri(); ?>/src/assets/images/pages/Home/bluebkg.png">
    <img class="black-stripes-box" src="<?php echo get_stylesheet_directory_uri(); ?>/src/assets/images/pages/Home/category_background.png">
  </div>

</div>
<div class="b2b-page-content">
  <ul class=" b2b-tabs tabs" data-tabs id="b2b-tabs">
  <li class="tabs-title "><a href="#form-tab" aria-selected="true">Form</a></li>
  <li class="tabs-title is-active"><a  href="#gallery-tab">Gallery</a></li>
</ul>
  <div class="b2b-tabs-content tabs-content" data-tabs-content="b2b-tabs">
    <div class="tabs-panel" id="form-tab">
      <h1 class="form-header-title">Sign Up To Get <br>
          Our Catalog.
      </h1>
        <div class="form-header-text"><?php the_field('form_header_text'); ?></div>
        <?php echo do_shortcode("[gravityform id='2' title='false' description='false']")?>

    </div>
    <div class="tabs-panel is-active" id="gallery-tab">
      <?php if(have_rows('year_catalogs')): ?>
        <?php while(have_rows('year_catalogs')): the_row(); ?>
      <h1 class="form-header-title">Check Embrator  <br>
        <?php the_sub_field('year'); ?> Catalogs.
      </h1>

      <?php if(have_rows('catalog')): ?>
      <div class="catalogs">
        <?php while(have_rows('catalog')): the_row(); ?>
            <div class="catalog overlay" style="background-image: url(<?php the_sub_field('catalog_background_image');  ?>);">

            <a href="<?php the_sub_field('catalog_file'); ?>">
              <!-- <img class="catalog-background-image overlay"src="<?php the_sub_field('catalog_background_image'); ?>" alt=""> -->
            </a>
            </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
    <?php endwhile; ?>
    <?php endif; ?>
    </div>
</div>


</div>
<section class="section section__branches" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/branches.png)">
    <h3>Embrator Branches</h3>
    <h1>We have <span>100+</span> locations all over Egypt</h1>
    <a class="branches__button" href="<?php echo get_sub_field('branches_button') ?>">VIEW ALL BRANCHES</a>
</section>
<?php get_footer();
