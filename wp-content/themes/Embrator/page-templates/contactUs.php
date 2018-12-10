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
          <h1>info</h1>
      </div>
  </div>
</div>

<?php get_footer();
