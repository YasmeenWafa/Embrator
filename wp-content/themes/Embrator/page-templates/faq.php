<?php
/*
Template Name: FAQ
*/
get_header(); ?>
  <div class="embrator__template embrator__faq">
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

    <section class="embrator__section spyScroll">            
      <ul class="spyScroll__sidebar" data-magellan data-offset="100" >
      <?php
      if (have_rows('faq_sidebar')):
        $linkCount=1;
      while (have_rows('faq_sidebar')):the_row(); ?>
        <li class="spyScroll__link">
          <a href="#link<?php echo $linkCount?>">
            <?php echo get_sub_field('faq_sidebar_link') ?>
          </a>
        </li>
        <?php
        $linkCount = $linkCount+1;
				endwhile;
      else:
      endif;
      ?>
      </ul>
      
      <div class="spyScroll__targets">
      <?php
      if (have_rows('faq_q&a')):
        $targetCount=1;
      while (have_rows('faq_q&a')):the_row(); ?>
        <div class="spyScroll__target" id="link<?php echo $targetCount; ?>"  data-magellan-target="link<?php echo $targetCount; ?>">
          <h3 class="embrator__sideTitle"><?php echo get_sub_field('section_title') ?></h3>
          <article class="faq">
          <?php
          if (have_rows('section_qs&as')):
          while (have_rows('section_qs&as')):the_row(); ?>
            <h4 class="faq__q embrator__bold">
              <?php echo get_sub_field('question') ?>
            </h4>
            <p class="faq__ans">
              <?php echo get_sub_field('answer') ?>
            </p>
            <?php
          endwhile;
        else:
        endif;
        ?>
        </article>
        </div>
        <?php
      $targetCount = $targetCount+1;
    endwhile;
  else:
  endif;
  ?>
  </div>
    </section>
    <section class="section section__branches" style="background-image: url(<?php
          echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/branches.png)" >
          <h3>Embrator Branches</h3>
      <h1>We have <span>100+</span> locations all over Egypt</h1>
      <a class="branches__button" href="<?php echo get_sub_field('branches_button',5) ?>">VIEW ALL BRANCHES</a>
    </section>
  </div>
<?php get_footer();