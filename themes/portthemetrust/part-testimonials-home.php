<?php $home_testimonial_count = intval(of_get_option('ttrust_home_testimonial_count')); ?>
<?php if($home_testimonial_count > 0) : ?>
<?php $testimonails_bkg = of_get_option('ttrust_testimonials_bkg'); ?>
<div id="testimonials" class="full homeSection clearfix <?php if($testimonails_bkg) echo "hasBackground"; ?>">
				
	<?php if(of_get_option('ttrust_testimonials_title') || of_get_option('ttrust_testimonials_description')) : ?>
	<div class="sectionHead">		
	<h3><span><?php echo of_get_option('ttrust_testimonials_title'); ?></span></h3>	
	<p><span><?php echo of_get_option('ttrust_testimonials_description'); ?></span></p>		
	</div>
	<?php endif; ?>		
	<?php
	$args = array(
		'ignore_sticky_posts' => 1,    	
    	'post_type' => array(				
		'testimonial'					
		),
		'posts_per_page' => $home_testimonial_count,
	);
	$testimonials = new WP_Query( $args );		
	?>
	<div class="wrap">
	<div class="testimonials thumbs clearfix">
		<?php while ($testimonials->have_posts()) : $testimonials->the_post(); ?>			    
		<div <?php post_class('small'); ?>>		
			<div class="inside">		
				<?php the_post_thumbnail("ttrust_square_medium", array('class' => '', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?>						
					
				<?php the_content(); ?>	
				<span class="title"><span>- <?php the_title(); ?></span></span>
			</div>		
		</div>
		<?php endwhile; ?>		
	</div>
	</div>
</div>
<?php endif; ?>