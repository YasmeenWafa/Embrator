<?php
/**
 * Job Detail Page Wrapper End
 *
 * @author 	PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/templates/single-jobpost
 * @version     1.0.0
 * @since       2.5.0
 */

ob_start();?>

<div class="clearfix"></div>
</div>
</div>
</section>
<section class="section section__branches" style="background-image: url(<?php
          echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/branches.png)">
    <h3>Embrator Branches</h3>
    <h1>We have <span>100+</span> locations all over Egypt</h1>
    <a class="branches__button" href="<?php echo get_sub_field('branches_button',5) ?>">VIEW ALL BRANCHES</a>
</section>
</div>
<!-- End: Jobs Detail Page Wrapper -->

<?php

$job_endwrapper = ob_get_clean();

/**
 * Modify Job Detail Page Wrapper End Template
 *                                       
 * @since   2.5.0
 * 
 * @param   html    $job_endwrapper   Wrapper Enclosing HTML          .
 */
echo apply_filters( 'sjb_single_job_wrapper_end_template', $job_endwrapper );