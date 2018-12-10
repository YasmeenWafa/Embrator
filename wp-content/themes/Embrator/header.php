<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "container" div.
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

?>
<!doctype html>
<html class="no-js" <?php language_attributes(); ?> >
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>

	<?php if ( get_theme_mod( 'wpt_mobile_menu_layout' ) === 'offcanvas' ) : ?>
		<?php get_template_part( 'template-parts/mobile-off-canvas' ); ?>
	<?php endif; ?>

  <header class="embrator__header embrator__header--responsive header hide-for-large <?php //echo $class?>" role="banner" id="header">
	<div class="nav-bar">
		<div id="nav-icon">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
		</div>
		<div class="main__menu" style="background-image: url(<?php
	echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/home/background.png)">
			<?php foundationpress_top_bar_center(); ?>
			<!-- <?php// foundationpress_mobile_nav(); ?> -->
			
			<!-- <div id="social-channels">
			<a href=""><i class="fa fa-facebook-f"></i></a>
			<a href=""><i class="fa fa-instagram"></i></a>
			<a href=""><i class="fa fa-twitter"></i></a>
			<a href=""><i class="fa fa-pinterest-p"></i></a>
			</div> -->
		</div>
			<div class="nav-bar__logo logo <?php //echo $class ?>" >
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<img src="<?php echo get_stylesheet_directory_uri();?>/src/assets/images/Header/embrator_en_logo.png" alt="Embrator_Logo">
				</a>
			</div>
			<div class="nav-bar__right">
				<a href ="" class="navigation__button"><i class="fa fa-search"></i></a>
				<a href="" class="navigation__button"><i class="fa fa-shopping-cart"></i></a>
			</div>
		</div>
    </header>

    <header class="embrator__header embrator__header--desktop header show-for-large <?php //echo $class?>" role="banner">
		<div class="embrator__navigation navigation">
			<div class="nav-bar__logo logo" >
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<img src="<?php echo get_stylesheet_directory_uri();?>/src/assets/images/Header/embrator_en_logo.png" alt="Embrator_Logo">
				</a>
			</div>
            <div class="navigation__center">
				<?php foundationpress_top_bar_center(); ?>
			</div>
			<div class="navigation__right">
				<div class="navigation__right__top">
					<a href="" class="navigation__language"><span class="navigation__language__selection navigation__language__selection--active">En</span>/ <span class="navigation__language__selection">Ar</span></a>
				</div>
				<div class="navigation__right__bottom">
					<a href="" class="button">
						<span class="button__logo"><i class="fa fa-user"></i></span>
						<span class="button__text">Sign Up</span>
					</a>
					<a href ="" class="navigation__button"><i class="fa fa-search"></i></a>
					<a href="" class="navigation__button"><i class="fa fa-shopping-cart"></i></a>
				</div>
            </div>
		</div>
	</header>
	
<div class="menu-popUp show-for-large" id="topMenu">
	<div class="menu-popUp__table">
		<ul class="menu-popUp__list">
			<span class="menu-popUp__listTitle"><?php the_field('first_column_title');?></span>
			<?php
			// check if the repeater field has rows of data
			if (have_rows('product_navigation_first_column',5)): 
			while (have_rows('product_navigation_first_column',5)):the_row(); ?>
			<li class="menu-popUp__listItem">
				<a href="<?php echo get_sub_field('product_link') ?>">
					<span class="menu-popUp__listItem__logo">
						<img src="<?php echo get_sub_field('product_logo') ?>">
					</span>
					<span class="menu-popUp__listItem__text">
						<?php echo get_sub_field('product_type') ?>
					</span>
				</a>
			</li>
			<?php // display a sub field value
				endwhile;
				else:
				// no rows found
				endif;
			?>
		</ul>
		<ul class="menu-popUp__list">
			<span class="menu-popUp__listTitle">
				<?php the_field('product_name_1');?>
			</span>
			<?php // check if the repeater field has rows of data
			 if (have_rows('product_navigation_column_1',5)): 
			while (have_rows('product_navigation_column_1',5)):the_row(); ?>
			<li class="menu-popUp__listItem">
				<a href="<?php echo get_sub_field('product_link_1') ?>">
					<span class="menu-popUp__listItem__text">
						<?php echo get_sub_field('product_category_1') ?>
					</span>
				</a>
			</li>
			<?php // display a sub field value
				endwhile;
				else: // no rows found 
				endif;
			?>
		</ul>
		<ul class="menu-popUp__list">
			<span class="menu-popUp__listTitle">
				<?php the_field('product_name_2');?>
			</span>
			<?php // check if the repeater field has rows of data
			 if (have_rows('product_navigation_column_2',5)): 
			while (have_rows('product_navigation_column_2',5)):the_row(); ?>
			<li class="menu-popUp__listItem">
				<a href="<?php echo get_sub_field('product_link_2') ?>">
					<span class="menu-popUp__listItem__text">
						<?php echo get_sub_field('product_category_2') ?>
					</span>
				</a>
			</li>
			<?php // display a sub field value
				endwhile;
				else: // no rows found 
				endif;
			?>
		</ul>
		<ul class="menu-popUp__list">
			<span class="menu-popUp__listTitle">
				<?php the_field('product_name_3');?>
			</span>
			<?php // check if the repeater field has rows of data
			 if (have_rows('product_navigation_column_3',5)): 
			while (have_rows('product_navigation_column_3',5)):the_row(); ?>
			<li class="menu-popUp__listItem">
				<a href="<?php echo get_sub_field('product_link_3') ?>">
					<span class="menu-popUp__listItem__text">
						<?php echo get_sub_field('product_category_3') ?>
					</span>
				</a>
			</li>
			<?php // display a sub field value
				endwhile;
				else: // no rows found 
				endif;
			?>
		</ul>
		<ul class="menu-popUp__list">
			<span class="menu-popUp__listTitle">
				<?php the_field('product_name_4');?>
			</span>
			<?php // check if the repeater field has rows of data
			 if (have_rows('product_navigation_column_4',5)): 
			while (have_rows('product_navigation_column_4',5)):the_row(); ?>
			<li class="menu-popUp__listItem">
				<a href="<?php echo get_sub_field('product_link_4') ?>">
					<span class="menu-popUp__listItem__text">
						<?php echo get_sub_field('product_category_4') ?>
					</span>
				</a>
			</li>
			<?php // display a sub field value
				endwhile;
				else: // no rows found 
				endif;
			?>
		</ul>
		<?php
			$featuredImage = get_field('featured_product_image',5);
			$featuredImage_size = "thumbnail";
			$featuredImage_url = $featuredImage["sizes"][$featuredImage_size];
		?>
		<figure
			class="featuredImageMenu"
			style="background-image: url(<?php echo $featuredImage_url; ?>)"
		>
		<p href="<?php the_field('featured_product_text',5)?>">Summer Collection</p>
			<a href="<?php the_field('featured_product_link',5)?>">Shop Now</a>
		</figure>
	</div>
</div>
                                      