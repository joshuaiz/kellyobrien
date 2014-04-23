<?php get_header(); ?>

		<div id="pageHead">
			<div class="inside">
			<h1><?php _e('Links', 'themetrust'); ?></h1>
			</div>
		</div>
		<div id="middle" class="clearfix">				 
		<div id="content" class="twoThirds clearfix">			    
			<div class="post">					
				<ul>
					<?php get_links_list(); ?>
				</ul>				
			</div>						    	
		</div>		
		<?php get_sidebar(); ?>
		</div>
<?php get_footer(); ?>
