<?php global $post; ?>
<?php $show_full_post = of_get_option('ttrust_post_show_full'); ?>
<?php $post_show_date = of_get_option('ttrust_post_show_date'); ?>

<div class="project small <?php echo $p; ?>" id="<?php echo $post->ID; ?>">		
	<div class="inside">

<div id="box">
 <?php foreach (get_the_category() as $cat) : ?>
 
 <img src="<?php echo z_taxonomy_image_url($cat->term_id); ?>" />

 <div id="overlay">
   <a class="overlay-text <?php echo $cat->cat_name; ?>" href="<?php echo get_category_link($cat->term_id); ?>"><?php echo $cat->cat_name; ?></a>
  </div>
 

 <?php endforeach; ?>
</div>	
	<div class="meta clearfix">
		<!-- <?php $post_show_author = of_get_option('ttrust_post_show_author'); ?> -->
		<!-- <?php $post_show_date = of_get_option('ttrust_post_show_date'); ?> -->
		<!-- <?php $post_show_category = of_get_option('ttrust_post_show_category'); ?> -->
		<!-- <?php $post_show_comments = of_get_option('ttrust_post_show_comments'); ?> -->
					
		<!-- <?php if($post_show_author || $post_show_date || $post_show_category){ _e('Posted ', 'themetrust'); } ?> -->
		<!-- <?php if($post_show_author) { _e('by ', 'themetrust'); the_author_posts_link(); }?> -->
		<!-- <?php if($post_show_date) { _e('on', 'themetrust'); ?> <?php the_time( 'M j, Y' ); } ?> -->
		<!-- <?php if($post_show_category) { the_category(', '); } ?> -->
		<!-- <?php if(($post_show_author || $post_show_date || $post_show_category) && $post_show_comments){ echo " | "; } ?> -->
		
		<?php if($post_show_comments) : ?>
			<a href="<?php comments_link(); ?>"><?php comments_number(__('No Comments', 'themetrust'), __('One Comment', 'themetrust'), __('% Comments', 'themetrust')); ?></a>
		<?php endif; ?>
	</div>
	
	<!--<?php if($show_full_post && !is_page_template('page-home.php')) : ?>
		<?php the_content(); ?>		
	<?php else: ?>		
		<?php the_excerpt(); ?>
		<?php more_link(); ?>
	<?php endif; ?>	-->											
	</div>
	
</div>