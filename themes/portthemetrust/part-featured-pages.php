<?php $featured_page_count = intval(of_get_option('ttrust_featured_pages_count')); ?>
<?php $featured_pages_title = of_get_option('ttrust_featured_pages_title'); ?>
<?php $featured_pages_links_enabled = of_get_option('ttrust_featured_pages_links_enabled'); ?>
<?php if($featured_page_count > 0) : ?>
<?php $featured_pages_bkg = of_get_option('ttrust_featured_pages_bkg'); ?>	
<div id="featuredPages" class="full homeSection <?php if($featured_pages_bkg) echo "hasBackground"; ?>">			
	<?php if($featured_pages_title):?>
	<div class="sectionHead">		
	<h3><span><?php echo $featured_pages_title; ?></span></h3>	
	<p><span><?php echo of_get_option('ttrust_featured_pages_description'); ?></span></p>	
	</div>
	<?php endif; ?>		
	<?php
	$args = array(
		'ignore_sticky_posts' => 1,  
		'meta_key' => '_ttrust_page_featured',
		'meta_value' => true,  			
    	'posts_per_page' => $featured_page_count,
    	'post_type' => array(				
		'page'					
		)
	);	
	?>
	<?php $pages = new WP_Query( $args ); ?>
	<div class="wrap">
	<div class="pages thumbs clearfix">
	<?php while ($pages->have_posts()) : $pages->the_post(); ?>			    
		<div <?php post_class('small'); ?>>	
			<div class="inside">
			<?php if($featured_pages_links_enabled): ?>	
			<a class="thumb" href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail("ttrust_one_third_cropped", array('class' => 'thumb', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?></a>			
			<a href="<?php the_permalink() ?>" rel="bookmark" ><span class="title"><span><?php the_title(); ?></span></span></a>
			<?php the_excerpt(); ?>
			<?php else: ?>	
			<?php the_post_thumbnail("ttrust_one_third_cropped", array('class' => '', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?>						
			<span class="title"><span><?php the_title(); ?></span></span>	
			<?php the_excerpt(); ?>
			<?php endif; ?>	
			</div>			
		</div>
	<?php endwhile; ?>	
	</div>
	</div>
</div>
<?php endif; ?>