<?php get_header(); ?>
<?php $blog_full_width = of_get_option('ttrust_post_full_width'); ?>
<?php $bw = ($blog_full_width) ? "full" : "twoThirds"; ?>
		<div id="pageHead">
			<div class="inside">
			<?php global $post; if(is_archive() && have_posts()) :

				if (is_category()) : ?>
					<h1><?php single_cat_title(); ?></h1>				
					<?php if(strlen(category_description()) > 0) echo category_description(); ?>
				<?php elseif( is_tag() ) : ?>
					<h1><?php single_tag_title(); ?></h1>
				<?php elseif (is_day()) : ?>
					<h1>Archive <?php the_time('M j, Y'); ?></h1>
				<?php elseif (is_month()) : ?>
					<h1>Archive <?php the_time('F Y'); ?></h1>
				<?php elseif (is_year()) : ?>
					<h1>Archive <?php the_time('Y'); ?></h1>
				<?php elseif (isset($_GET['paged']) && !empty($_GET['paged'])) : ?>
					<h1>Archive</h1>
				<?php endif; ?>

			<?php endif; ?>
			</div>
		</div>		
		<div id="middle" class="clearfix">				 
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
		
<?php get_footer(); ?>