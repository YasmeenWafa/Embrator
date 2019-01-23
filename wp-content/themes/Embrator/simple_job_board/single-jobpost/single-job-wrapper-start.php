<?php
/**
 * Job Detail Page Wrapper Start
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost
 * @version     1.0.0
 * @since       2.5.0
 */

ob_start();
?>
<!-- Start: Jobs Listing Wrapper -->
<div class="embrator__template embrator__careers">
    <div class="embrator__hero">
        <?php 
        $heroImage = get_field('hero_image_careers',283);
        $heroImage_size = "large";
        $heroImage_url = $heroImage["sizes"][$heroImage_size];
      ?>
        <figure class="embrator__hero__blue embrator__hero__image_3" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/bluebkg.png)"></figure>
        <figure class="embrator__hero__stripes embrator__hero__image_2" style="background-image: url(<?php
      echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/category_background.png)"></figure>
        <figure class="embrator__hero__image embrator__hero__image_1" style="background-image: url(<?php echo $heroImage_url; ?> )"></figure>
        <!-- <h1 class="embrator__hero__title"><?php //echo str_replace('Archives: Jobs','Careers',get_the_archive_title()); ?></h1> -->
        <h1 class="embrator__hero__title">
            <?php the_field('hero_title_careers',283) ?>
        </h1>


    </div>

    <section class="embrator__section ">
        <div class="embrator__section__social">
            <a class="embrator__section__social--link" href=""><i class="fa fa-facebook-f"></i></a>
            <a class="embrator__section__social--link" href=""><i class="fa fa-twitter"></i></a>
        </div>
        <div class="sjb-page">
            <div class="sjb-detail">
                <div class="list-data">
                    <?php

            $job_startwrapper = ob_get_clean();

            /**
             * Modify Job Detail Page Wrapper Start Template
             *                                       
             * @since   2.5.0
             * 
             * @param   html    $job_startwrapper   Starting HTML          .
             */
            echo apply_filters( 'sjb_single_job_wrapper_start_template', $job_startwrapper );