<header class="embrator__hero embrator__hero--singleStory">
	<figure class="embrator__hero__blue embrator__hero__image_3" style="background-image: url(<?php
	echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/bluebkg.png)" ></figure>
	<figure class="embrator__hero__stripes embrator__hero__image_2" style="background-image: url(<?php
	echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/category_background.png)"></figure>
	<?php
	// If a featured image is set, insert into layout and use Interchange
	// to select the optimal image size per named media query.
	if ( has_post_thumbnail( $post->ID ) ) { ?>
		<figure class="embrator__hero__image embrator__hero__image_1" role="banner" data-interchange="[<?php the_post_thumbnail_url( 'featured-small' ); ?>, small], [<?php the_post_thumbnail_url( 'featured-medium' ); ?>, medium], [<?php the_post_thumbnail_url( 'featured-large' ); ?>, large], [<?php the_post_thumbnail_url( 'featured-xlarge' ); ?>, xlarge]">
		</figure>
		<h1 class="embrator__hero__title"><?php the_title(); ?></h1>
	<?php }else{;?>
		<figure class=" embrator__hero__image embrator__hero__image_1" style="background-image: url(<?php
			echo get_stylesheet_directory_uri() ?>/src/assets/images/pages/Home/branches.png)">
			<h1 class="embrator__hero__title"><?php the_title(); ?></h1>
		</figure>		
	<?php } ?>
</header>


