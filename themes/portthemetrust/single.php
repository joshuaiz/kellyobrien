<?php get_header(); ?>
<?php $blog_full_width = of_get_option('ttrust_post_full_width'); ?>
<?php $bw = ($blog_full_width) ? "full" : "twoThirds"; ?>
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
	<div id="middle" class="clearfix">			 
	<div id="content" class="<?php echo $bw; ?>">
		<?php while (have_posts()) : the_post(); ?>
			    
		<div <?php post_class(); ?>>													
			<h1><?php the_title(); ?></h1>
			<div class="meta clearfix">
				<?php $post_show_author = of_get_option('ttrust_post_show_author'); ?>
				<?php $post_show_date = of_get_option('ttrust_post_show_date'); ?>
				<?php $post_show_category = of_get_option('ttrust_post_show_category'); ?>
				<?php $post_show_comments = of_get_option('ttrust_post_show_comments'); ?>
							
				<?php if($post_show_author || $post_show_date || $post_show_category){ _e('Posted ', 'themetrust'); } ?>					
				<?php if($post_show_author) { _e('by ', 'themetrust'); the_author_posts_link(); }?>
				<?php if($post_show_date) { _e('on', 'themetrust'); ?> <?php the_time( 'M j, Y' ); } ?>
				<?php if($post_show_category) { _e('in', 'themetrust'); ?> <?php the_category(', '); } ?>
				<?php if(($post_show_author || $post_show_date || $post_show_category) && $post_show_comments){ echo " | "; } ?>
				
				<?php if($post_show_comments) : ?>
					<a href="<?php comments_link(); ?>"><?php comments_number(__('No Comments', 'themetrust'), __('One Comment', 'themetrust'), __('% Comments', 'themetrust')); ?></a>
				<?php endif; ?>
			</div>
			
			<?php if(of_get_option('ttrust_post_show_featured_image')) : ?>
				<?php get_template_part( 'part-post-thumb'); ?>
			<?php endif; ?>
			
			<?php the_content(); ?>
			
			<?php wp_link_pages( array( 'before' => '<div class="pagination clearfix">Pages: ', 'after' => '</div>' ) ); ?>
																										
		</div>				
		<?php comments_template('', true); ?>
			
		<?php endwhile; ?>					    	
	</div>		
	<?php if($bw == "twoThirds") get_sidebar(); ?>				
	</div>
<?php get_footer(); ?>
