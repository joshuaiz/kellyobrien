<?php get_header(); ?>			
		
		<div id="pageHead">				
		<div class="inside">				
			<h1><?php the_title(); ?></h1>
			<div class="projectNav clearfix">					
								
				<div class="previous <?php if(!get_previous_post()){ echo 'inactive'; }?>">
					<?php previous_post_link('%link', ''); ?>
				</div>
				<?php $portfolio_page_id = of_get_option('ttrust_portfolio_page'); ?>
				<?php if($portfolio_page_id) : ?>
				<div class="portfolio <?php if(!get_next_post()){ echo 'inactive'; }?>">						
					<a href="<?php echo get_permalink($portfolio_page_id); ?>"></a>				
				</div>
				<?php endif; ?>
				<div class="next <?php if(!get_next_post()){ echo 'inactive'; }?>">						
					<?php next_post_link('%link', ''); ?>				
				</div>
			</div>								
		</div>					
		</div>
		<div id="middle" class="clearfix">		 
		<div id="content" class="full">			
			<?php while (have_posts()) : the_post(); ?>			    
			<div class="project clearfix">   						
				<?php the_content(); ?>									
			</div>
			<?php comments_template('', true); ?>	
			<?php endwhile; ?>										    	
		</div>
		</div>
<?php get_footer(); ?>