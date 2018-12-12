<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();


// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

?>
<div class="checkout-page-container">
	<?php
	// include('menu.php');
	 // get_template_part('menu');
	  ?>

	 <div class="checkout-page-container-inner">


	<div class="history-info">

	<p> <a href="<?php echo get_permalink( get_page_by_title('home')); ?>" >Home</a>  /  <a href="<?php echo get_permalink( get_page_by_title('Cart')); ?>" >Cart</a>  /  <a href="#"><?php wp_title(''); ?></a></p>
	</div>
	<h1 class="page-heading">Checkout</h1>


<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<!-- <div class="col-2"> -->
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			<!-- </div> -->
			<p class="form-row checkbox-row">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox inline">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="" type="checkbox" id="" value="forever" /> <span class="checkmark"></span>
					<span class="save-info-checkout">Save this information for next time.</span>
				</label>


			</p>
			<div id="place-order-section" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

<div class="order-review-all">
<!--  desktop-->

<div class="order-review-desktop">
<!-- <h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3> -->

<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

<div id="order_review" class="woocommerce-checkout-review-order">
	<?php do_action( 'woocommerce_checkout_order_review' ); ?>
</div>
<?php do_action( 'woocommerce_before_checkout_form', $checkout );
?>
	</div>
<!-- <desktop -->
	<ul class="order-review-accordion accordion mobile-view" data-accordion data-multi-expand="true" data-allow-all-closed="true">
		<li class="accordion-item is-active" data-accordion-item>
			<a href="#" class="accordion-title">Show order summary
				 <i class="icon-down-open"></i>

				  <span><?php wc_cart_totals_order_total_html(); ?></span> </a>
	<div class="order-review accordion-content"  data-tab-content>


	<!-- <h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3> -->

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>
<?php do_action( 'woocommerce_before_checkout_form', $checkout );
 ?>

		</div>
		</li>
</ul>


</div>

<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

		</form>
	 </div>
</div>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
