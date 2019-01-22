<?php
/*
Template Name: About
*/
get_header(); ?>


<div class="about-page-container">
  <div class="about-page-header">
    <h1 class="about-page-header-title">About Embrator</h1>
    <img class="header-image" src="<?php the_field('header_image') ?>" alt="">
    <img class="blue-box" src="<?php echo get_stylesheet_directory_uri(); ?>/src/assets/images/pages/Home/bluebkg.png">
    <img class="black-stripes-box" src="<?php echo get_stylesheet_directory_uri(); ?>/src/assets/images/pages/Home/category_background.png">
  </div>

</div>
<div class="about-page-paragraph">
    <h1 class="header-paragraph-title">Philosophy, <br>
Mission, <br>
and Vision.
 </h1>
    <div class="header-paragraph-text"><?php the_field('header_text'); ?></div>
</div>
<div class="year-buttons" style="display:none;">
  <?php if(have_rows('years_slider')): ?>
    <div class="years">
      <?php while(have_rows('years_slider')): the_row(); ?>
      <div class="year">
          <a class="year-button" ><?php the_sub_field('year'); ?></a>
      </div>

    <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>
<div class="about-page-years-slider">
  <?php if(have_rows('years_slider')): ?>
    <div class="years-slider">
      <?php while(have_rows('years_slider')): the_row(); ?>
      <div class="year-slide">

        <div class="year-header">
          In <?php the_sub_field('year') ?>,
        </div>
          <div class="year-slide-text">
            <?php the_sub_field('year_text'); ?>
          </div>

      </div>

    <?php endwhile; ?>
    <!-- <div class="l"><a class='slick-prev'><i class='icon-up-open' aria-hidden='true'></i></a></div> -->
    </div>
    <?php endif; ?>
</div>
<section class="section section__branches" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/branches.png)">
    <h3>Embrator Branches</h3>
    <h1>We have <span>100+</span> locations all over Egypt</h1>
    <a class="branches__button" href="<?php echo get_sub_field('branches_button') ?>">VIEW ALL BRANCHES</a>
</section>


<?php get_footer();
