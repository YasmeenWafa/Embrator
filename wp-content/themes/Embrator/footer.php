<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "off-canvas-wrap" div and all content after.
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */
?>

<footer class="footer">
    <div class="footer__left">
        <div class="footer__logo logo" >
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                <img src="<?php echo get_stylesheet_directory_uri();?>/src/assets/images/Header/embrator_en_logo.png" alt="Embrator_Logo">
            </a>
        </div>
        <p class="footer__caption">
        Embrator continued to add to its core competencies operating in dire competition in cotton marketplace, leading the way of innovation and creativity in manufacturing and trading operations.
        </p>
        <div class="footer__social">
            <a class="footer__social--link" href="" ><i class="fa fa-facebook-f"></i></a>
            <a class="footer__social--link" href="" ><i class="fa fa-twitter"></i></a>
            <a class="footer__social--link" href="" ><i class="fa fa-pinterest-p"></i></a>
            <a class="footer__social--link" href="" ><i class="fa fa-instagram"></i></a>
        </div>
    </div>
    <div class="footer__right">
        <!-- NewsLetter -->
        <div class="footer-grid">
            <?php dynamic_sidebar( 'footer-widgets' ); ?>
        </div>
        <p class="allRights__rights">© 2018 <span>Embrator</span> All rights reserved. Designed & Developed by Unplugged.</p>
    </div>
</footer>

<?php if ( get_theme_mod( 'wpt_mobile_menu_layout' ) === 'offcanvas' ) : ?>
	</div><!-- Close off-canvas content -->
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>