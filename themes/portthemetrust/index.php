<?php get_header(); ?>
<?php $blog_full_width = of_get_option('ttrust_post_full_width'); ?>
<?php $bw = ($blog_full_width) ? "full" : "twoThirds"; ?>

<?php if(!is_front_page()) : ?>
<div id="pageHead">
	<div class="inside">
	<?php $blog_page_id = of_get_option('ttrust_blog_page'); ?>
	<?php $blog_page = get_page($blog_page_id); ?>
	<h1><?php echo $blog_page->post_title; ?></h1>
	<?php $page_description = get_post_meta($blog_page_id, "_ttrust_page_description_value", true); ?>
	<?php if ($page_description) : ?>
		<p><?php echo $page_description; ?></p>
	<?php endif; ?>
	<div class="grace-intro"><p>Grace is my favorite word. I also happen to like Zamboni, but turns out like Kleenex it’s a brand name somebody owns. Grace is something we all have and we should unleash more. This is my way of sharing it. The <a href="http://dictionary.reference.com/browse/grace?s=t" target="_blank">definition of grace</a>. I don’t proclaim to be a proficient writer,  please see #5.</p></div>
	</div>
</div>
<?php endif; ?>


<div id="middle" class="clearfix">
	<div id="inner">
		<div id="content" class="<?php echo $bw; ?>">
			<div class="posts clearfix">			
				<?php while (have_posts()) : the_post(); ?>			    
					<?php get_template_part( 'part-post'); ?>	
				<?php endwhile; ?>
	
				<?php get_template_part( 'part-pagination'); ?>
			</div>		    	
		</div>		
			<?php if($bw == "twoThirds") get_sidebar(); ?>			
	</div>	
</div>
<?php get_footer(); ?>
