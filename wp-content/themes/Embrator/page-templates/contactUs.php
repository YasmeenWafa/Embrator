<?php
/*
Template Name: Contact Us
*/
get_header(); ?>

<div class="contact-us-container">
  <h1 class="contact-us-header">Contact Embrator</h1>
  <div class="contact-us-container-inner">
      <div class="contact-us-form">
          <span class="form-heading">Don’t hesitate to contact us if you have questions about anything at all! </span>
          <?php echo do_shortcode("[gravityform id='1' title='false' description='false']")?>
      </div>
      <div class="contact-us-info">
          <div class="blue-box" style="background-image: url(<?php
          echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/bluebkg.png)">

          </div>
          <div class="black-stripes-box" style="background-image: url(<?php
          echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/category_background.png)">

          </div>
          <div class="grey-box">
              <?php if(have_rows('departments_info')): ?>
                <?php while(have_rows('departments_info')): the_row() ?>
                  <div class="department">
                    <h2 class="department_name"><?php the_sub_field('department_name') ?></h2>
                    <div><?php the_sub_field('address') ?></div>

                    <?php if(get_sub_field('telephone')!=null): ?>
                    <div><span>Tel:</span><?php the_sub_field('telephone') ?></div>
                    <?php endif; ?>

                    <?php if(get_sub_field('fax')!=null): ?>
                    <div><span>Fax:</span><?php the_sub_field('fax') ?></div>
                  <?php endif; ?>
                  
                    <div><?php the_sub_field('email_1') ?></div>
                    <div><?php the_sub_field('email_2') ?></div>
                  </div>
                <?php endwhile; ?>
              <?php endif; ?>
          </div>
          <!-- <div class="cat"  data-relative-input="true"  data-hover-only="true">
            <div class="cat__background" style="background-color:red;"></div>
            <div class="cat__pattern" style="background-color:black"></div>
            <figure class="cat__image" style="background-color:blue"></figure>
            <h2 class="cat__title">
              title
            </h2>
          </div> -->
      </div>
  </div>
  <div class="blue-triangle">
      <!-- <h1>hh</h1> -->
  </div>
</div>

<?php get_footer();
