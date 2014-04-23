<?php /*
Template Name: Home
*/ ?>
<?php get_header(); ?>

<div id="middle" class="clearfix">	
<div id="content" class="full">	
<?php get_template_part( 'part-projects-home'); //projects section ?>
<?php get_template_part( 'part-featured-pages'); //featured pages section ?>
<?php get_template_part( 'part-posts-home'); //posts section?>

<?php while (have_posts()) : the_post(); //home content section ?>	
<?php if($post->post_content):?>
<?php $home_content_bkg = of_get_option('ttrust_home_content_bkg'); ?>
<div id="homeContent" class="full homeSection clearfix <?php if($home_content_bkg) echo "hasBackground"; ?>">	
	<div class="inside">
	<?php the_content(); ?>	
	</div>		
</div>
<?php endif; ?>
<?php endwhile; ?>

<?php get_template_part( 'part-testimonials-home'); //testimonials section ?>

</div>
</div>

<?php get_footer(); ?>	