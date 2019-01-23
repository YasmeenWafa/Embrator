<?php
/*
Template Name: Home
*/
get_header(); ?>
<!-- ----------------------------------------Outer Loop ---------------------------------- -->
<header id="cont">
    <div class="home__hero bannerSlider" role="banner" id="ver">
        <?php
            $cat1Count = count(get_field('homepage_bannerslider_cat1'));
            if (have_rows('homepage_bannerslider_cat1')): 
        ?>
        <div class="cat" id="cat1">
            <section class="top">
                <?php // loop through the rows of data
                    $i1 = 1;
                while (have_rows('homepage_bannerslider_cat1')):
                  the_row(); 
                    if($i1 === 1 ){
                        $class= "active";
                    }else if($i1 === $cat1Count){
                        $class= "lastSlide";
                    }else{
                    $class = "";
                    }
                ?>
                <figure class="bannerSlider-figure bannerSlider-figure--top <?php echo $class ?> slide0<?php echo $i1?>">
                    <div class="bannerSlider__title">
                        <!-- <h1>Count =
                            <?php //echo $cat1Count ?>
                        </h1> -->
                        <h2>
                            <?php 
                            //the_sub_field('banner_slide_cat');
                        ?>
                        </h2>
                        <figcaption class="sliderHeadings">
                            <p class="bannerSlider__title__top">
                                <?php the_sub_field('banner_slide_caption_top') ?>
                            </p>
                            <p class="bannerSlider__title__center">
                                <?php the_sub_field('banner_slide_caption') ?>
                            </p>
                        </figcaption>
                    </div>
                </figure>
                <?php 
                    $i1 = $i1+1;
                    $outerLoop = $outerLoop+1;
                    endwhile; 
                ?>
            </section>
            <?php 
                else:
                endif;
                if (have_rows('homepage_bannerslider_cat1')): 
            ?>
            <section class="bottom">
                <?php
                    $j1 = 1;
                    while (have_rows('homepage_bannerslider_cat1')):
                    the_row(); 
                    
                    if($j1 === 1 ){
                        $class= "active";
                    }else{
                        $class = "";
                    }

                    $patternImage = get_sub_field('banner_slide_image');
                    $patternImage_size = "large";
                    $patternImage_url = $patternImage["sizes"][$patternImage_size];

                    $productImage = get_sub_field('banner_product_image');
                    $productImage_size = "large";
                    $productImage_url = $productImage["sizes"][$productImage_size];

                    ?>
                <figure class="bannerSlider-figure bannerSlider-figure--bottom <?php echo $class ?> slide0<?php echo $j1?>"
                    style="background-image: url(<?php echo $patternImage_url; ?> )">
                    <figure class="bannerSlider-product" style="background-image: url(<?php echo $productImage_url; ?> )"></figure>
                </figure>
                <?php 
                    $j1 = $j1+1;
                endwhile;
            else:
            endif;
            ?>
            </section>
        </div>
        <?php
            $cat2Count = count(get_field('homepage_bannerslider_cat2'));
            if (have_rows('homepage_bannerslider_cat2')): 
        ?>
        <div class="cat" id="cat2">
            <section class="top">
                <?php // loop through the rows of data
                    $i2 = 1;
                while (have_rows('homepage_bannerslider_cat2')):
                  the_row(); 
                    // if($i2 === 1 ){
                    //     // $class= "active";
                    // }else 
                    if($i2 === $cat2Count){
                        $class= "lastSlide";
                    }else{
                    $class = "";
                    }
                ?>
                <figure class="bannerSlider-figure bannerSlider-figure--top <?php echo $class ?> slide0<?php echo $i2?>">
                    <div class="bannerSlider__title">
                        <!-- <h1>Count =
                            <?php //echo $cat2Count ?>
                        </h1>
                        <h2>
                            <?php 
                            //the_sub_field('banner_slide_cat');
                        ?>
                        </h2> -->
                        <figcaption class="sliderHeadings">
                            <p class="bannerSlider__title__top">
                                <?php the_sub_field('banner_slide_caption_top') ?>
                            </p>
                            <p class="bannerSlider__title__center">
                                <?php the_sub_field('banner_slide_caption') ?>
                            </p>
                        </figcaption>
                    </div>
                </figure>
                <?php 
                    $i2 = $i2+1;
                    $outerLoop = $outerLoop+1;
                    endwhile; 
                ?>
            </section>
            <?php 
                else:
                endif;
                if (have_rows('homepage_bannerslider_cat2')): 
            ?>
            <section class="bottom">
                <?php
                    $j2 = 1;
                    while (have_rows('homepage_bannerslider_cat2')):
                    the_row(); 
                    
                    // if($j2 === 1 ){
                    // // $class= "active";
                    // }else{
                    // $class = "";
                    // }

                    $patternImage = get_sub_field('banner_slide_image');
                    $patternImage_size = "large";
                    $patternImage_url = $patternImage["sizes"][$patternImage_size];

                    $productImage = get_sub_field('banner_product_image');
                    $productImage_size = "large";
                    $productImage_url = $productImage["sizes"][$productImage_size];

                    ?>
                <figure class="bannerSlider-figure bannerSlider-figure--bottom <?php echo $class ?> slide0<?php echo $j2?>"
                    style="background-image: url(<?php echo $patternImage_url; ?> )">
                    <figure class="bannerSlider-product" style="background-image: url(<?php echo $productImage_url; ?> )"></figure>
                </figure>
                <?php 
                    $j2 = $j2+1;
                endwhile;
            else:
            endif;
            ?>
            </section>
        </div>
        <?php
            $cat3Count = count(get_field('homepage_bannerslider_cat3'));
            if (have_rows('homepage_bannerslider_cat3')): 
        ?>
        <div class="cat" id="cat3">
            <section class="top">
                <?php // loop through the rows of data
                    $i3 = 1;
                while (have_rows('homepage_bannerslider_cat3')):
                  the_row(); 
                    if($i3 === 1 ){
                        // $class= "active";
                    }else if($i3 === $cat3Count){
                        $class= "lastSlide";
                    }else{
                    $class = "";
                    }
                ?>
                <figure class="bannerSlider-figure bannerSlider-figure--top <?php echo $class ?> slide0<?php echo $i3?>">
                    <div class="bannerSlider__title">
                        <!-- <h1>Count =
                            <?php //echo $cat1Count ?>
                        </h1> -->
                        <h2>
                            <?php 
                            //the_sub_field('banner_slide_cat');
                        ?>
                        </h2>
                        <figcaption class="sliderHeadings">
                            <p class="bannerSlider__title__top">
                                <?php the_sub_field('banner_slide_caption_top') ?>
                            </p>
                            <p class="bannerSlider__title__center">
                                <?php the_sub_field('banner_slide_caption') ?>
                            </p>
                        </figcaption>
                    </div>
                </figure>
                <?php 
                    $i3 = $i3+1;
                    $outerLoop = $outerLoop+1;
                    endwhile; 
                ?>
            </section>
            <?php 
                else:
                endif;
                if (have_rows('homepage_bannerslider_cat3')): 
            ?>
            <section class="bottom">
                <?php
                    $j3 = 1;
                    while (have_rows('homepage_bannerslider_cat3')):
                    the_row(); 
                    
                    if($j3 === 1 ){
                        // $class= "active";
                    }else{
                        $class = "";
                    }

                    $patternImage = get_sub_field('banner_slide_image');
                    $patternImage_size = "large";
                    $patternImage_url = $patternImage["sizes"][$patternImage_size];

                    $productImage = get_sub_field('banner_product_image');
                    $productImage_size = "large";
                    $productImage_url = $productImage["sizes"][$productImage_size];

                    ?>
                <figure class="bannerSlider-figure bannerSlider-figure--bottom <?php echo $class ?> slide0<?php echo $j3?>"
                    style="background-image: url(<?php echo $patternImage_url; ?> )">
                    <figure class="bannerSlider-product" style="background-image: url(<?php echo $productImage_url; ?> )"></figure>
                </figure>
                <?php 
                    $j3 = $j3+1;
                endwhile;
            else:
            endif;
            ?>
            </section>
        </div>
        <div class="categoriesVariations">
            <a href="" catNumber='1' class="categoryVariation active ">
                <svg id="Layer_1" style="enable-background:new 0 0 100 100;" version="1.1" viewBox="0 0 100 100"
                    xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path d="M89.9,44.7l-4.4-21c-0.2-1.1-1.3-2-2.3-2c0,0,0,0,0,0c-0.1,0-13.1-1-33.1-1c-20,0-33,1-33.1,1c-1.2,0-2.2,0.8-2.4,2l-4.4,21  c-0.7,3.2,0.5,6.6,3.2,8.8l27.2,23.3c1.8,1.5,4.1,2.4,6.6,2.4h5.8c2.5,0,4.8-0.8,6.6-2.4l27.2-23.3C89.4,51.3,90.5,48,89.9,44.7z   M17,23.8c0.1,0,13-1,33-1s32.9,1,33.1,1c0.2,0,0.4,0.2,0.5,0.4l0.7,3.2c-2.9-0.2-12.6-0.9-34.4-0.9c-20.8,0-30.6,0.6-34,0.9  l0.7-3.1C16.5,23.9,16.7,23.8,17,23.8z M12.1,45.1l3.3-15.8c2.6-0.2,12.4-0.9,34.4-0.9c23.5,0,33.1,0.8,34.8,1l3.3,15.8  c0.2,0.9,0.2,1.9,0,2.8c-4-0.7-15.2-1.8-23.6,4.9c-2.4,1.9-4.4,4.3-6,7.2c-7.7-2.3-13.9-0.9-16.2-0.2c-1.5-2.8-3.5-5.1-5.9-7  c-8.9-7-20.8-5.4-24.2-4.8C11.9,47.1,11.9,46.1,12.1,45.1z M14.6,52c-0.7-0.6-1.3-1.3-1.7-2.1c3.6-0.6,14.4-1.7,22.2,4.5  c5.7,4.5,8.8,12,9,22.3c-0.8-0.3-1.6-0.8-2.3-1.3L14.6,52z M47.1,77.2c-0.3,0-0.7,0-1-0.1C46,71.1,45,65.9,43.1,61.6  c2.2-0.6,7.7-1.8,14.4,0.2c-1.9,4.2-2.9,9.4-3,15.3c-0.5,0.1-1,0.1-1.6,0.1H47.1z M85.4,52L58.2,75.4c-0.5,0.4-1.1,0.8-1.7,1.1  c0.3-10.2,3.3-17.6,9-22.1c7.5-5.9,17.7-5.1,21.7-4.5C86.7,50.6,86.1,51.4,85.4,52z" />
                </svg>
            </a>
            <a href="" catNumber='2' class="categoryVariation ">
                <svg id="Layer_1" style="enable-background:new 0 0 64 64;" version="1.1" viewBox="0 0 64 64" xml:space="preserve"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g>
                        <path d="M12.1,58h39.8L49,34h10.1l-4.2-23H37.8l-0.1,0.9c-0.2,2.7-2.5,4.8-5.2,4.8c-2.7,0-5-2.1-5.2-4.8L27.2,11H9.1L4.9,33.6H15   L12.1,58z M7.3,31.6L10.8,13h14.6c0.7,3.3,3.6,5.8,7.1,5.8c3.4,0,6.4-2.5,7.1-5.8h13.7l3.5,19h-7.9l-0.9-7.8l-2,0.2L49.6,56H14.4   l3.7-31.5l-2-0.2l-0.9,7.4H7.3z" />
                    </g>
                </svg>
            </a>
            <a href="" catNumber='3' class="categoryVariation ">
                <svg id="Layer_1" style="enable-background:new 0 0 64 64;" version="1.1" viewBox="0 0 64 64" xml:space="preserve"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g>
                        <path d="M32,33l6.7,28h14.4L47.9,3H16.1l-5.2,58h14.4L32,33z M45.2,14.8c0.4-0.4,1-0.7,1.6-0.8l0.4,4.9c-1.6-0.2-2.9-1.1-2.9-2.3   C44.4,15.9,44.7,15.3,45.2,14.8z M46.1,5l0.5,6.2H17.4L17.9,5H46.1z M17.1,14c0.8,0.1,1.5,0.4,2.1,0.9c0.6,0.5,0.9,1.1,0.9,1.7   c-0.1,1.3-1.6,2.3-3.4,2.3L17.1,14z M13.1,59l3.4-38.1c0.1,0,0.1,0,0.2,0c2.9,0,5.3-1.9,5.4-4.3c0.1-1.3-0.5-2.4-1.6-3.3   c-0.1-0.1-0.2-0.1-0.2-0.2H31V18h2v-4.8h11.2c-0.1,0.1-0.2,0.1-0.2,0.2c-1.1,0.9-1.6,2.1-1.6,3.3c0.1,2.3,2.3,4.1,5.1,4.2L50.9,59   H40.3L32,24.5L23.7,59H13.1z" />
                    </g>
                </svg>
            </a>
        </div>


        <!-- Prev/Next Navigation -->
        <div id="slideNav">
            <ul>
                <li class="slideNavPrev">
                    <a href="#" title="Go to previous slide">
                        <span class="ico ico-up">↓</span>
                    </a>
                </li>
                <li class="slideNavNext">
                    <a href="#" title="Go to next slide">
                        <span class="ico ico-down">↑</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>


</header>

<!-- Section 2  -->
<section id="sec2" class="section section__intro ">
    <h2>
        <?php the_field('intro_section_pre-title') ?>
    </h2>
    <h1>
        <?php the_field('intro_section_title') ?>
    </h1>
    <div class="division">
        <figure class="division__image" style="background-image: url(<?php the_field('division_image') ?> )"></figure>
        <div class="division__video">
            <?php the_field('division_video') ?>
        </div>
        <p class="division__caption">
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

$nextButtonImage = get_sub_field('collections_slider_nextButtonBackground');
$nextButtonImage_size = "medium";
$nextButtonImage_url = $nextButtonImage["sizes"][$nextButtonImage_size];

$previousButtonImage = get_sub_field('collections_slider_previousButtonBackground');
$previousButtonImage_size = "medium";
$previousButtonImage_url = $previousButtonImage["sizes"][$previousButtonImage_size];
?>

    <figure class="collectionsSlider__image" style="background-image: url(<?php echo $collectionImage_url; ?> )">
        <div class="collectionsSlider__group">
            <h2>
                <?php echo get_sub_field('collections_slider_background_pre') ?>
            </h2>
            <h1>
                <?php echo get_sub_field('collections_slider_background_title') ?>
            </h1>
            <a href="<?php echo get_sub_field('collections_slider_button') ?>">View Collection</a>
            <figure class="collectionsSlider__nextButtonBg" style="background-image: url(<?php echo $nextButtonImage_url; ?> )">
            </figure>
            <figure class="collectionsSlider__prevButtonBg" style="background-image: url(<?php echo $previousButtonImage_url; ?>)">
            </figure>
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
    <h2>
        <?php the_field('lastCats_pre') ?>
    </h2>
    <h1>
        <?php the_field('lastCats_title') ?>
    </h1>
    <?php
// check if the repeater field has rows of data
if (have_rows('lastCats_cats')): ?>
    <div class="lastCats" id="scene" data-relative-input="true" data-hover-only="false">
        <?php // loop through the rows of data
  $i = 0;
while (have_rows('lastCats_cats')):the_row(); 
?>
        <?php
$categoryImage = get_sub_field('cat_image');
$categoryImage_size = "medium";
$categoryImage_url = $categoryImage["sizes"][$categoryImage_size];
$i++;
?>
        <a href="<?php echo get_sub_field('cat_link') ?>" class="cat cat-<?php echo $i?>" data-relative-input="true"
            data-hover-only="true">
            <div class="cat__background" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/bluebkg.png)"
                data-depth="0.2"></div>
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
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/branches.png)">
    <h3>Embrator Branches</h3>
    <h1>We have <span>100+</span> locations all over Egypt</h1>
    <a class="branches__button" href="<?php echo get_sub_field('branches_button') ?>">VIEW ALL BRANCHES</a>
</section>

<?php get_footer();