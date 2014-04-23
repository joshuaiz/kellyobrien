<?php $recent_posts_count = intval(of_get_option('ttrust_recent_posts_count')); ?>
<?php $recent_posts_title = of_get_option('ttrust_recent_posts_title'); ?>
<?php if($recent_posts_count > 0) : ?>
<div id="homePosts" class="full homeSection clearfix">			

	<?php if($recent_posts_title):?>
	<div class="sectionHead">
		<div class="inside">		
		<h3><span><?php echo $recent_posts_title; ?></span></h3>	
		<p><span><?php echo of_get_option('ttrust_recent_posts_description'); ?></span></p>	
		<?php $blog_page_id = of_get_option('ttrust_blog_page'); ?>
		<?php if($blog_page_id) : ?>
		<div class="viewAll">
		<a href="<?php echo get_permalink($blog_page_id); ?>" class="button"><?php echo of_get_option('ttrust_posts_button_label'); ?></a>
		</div>
		<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>		
	
	<?php
	$args = array(
		'ignore_sticky_posts' => 1,					
    	'posts_per_page' => $recent_posts_count,
    	'post_type' => array(				
		'post'					
		)
	);	
	?>
	<?php $recentPosts = new WP_Query( $args ); ?>
	<div class="wrap">	
	<div class="posts clearfix thumbs">
	<?php while ($recentPosts->have_posts()) : $recentPosts->the_post(); ?>			    
		<?php get_template_part( 'part-post-small'); ?>
	<?php endwhile; ?>	
	</div>
	</div>
</div>
<?php endif; ?>