<!--<?php if(has_post_thumbnail()) : ?>	
	<?php if(of_get_option('ttrust_post_featured_img_size')=="large") : ?>											
		<a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail('ttrust_post_thumb_big', array('class' => 'postThumb', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?></a>		    	
	<?php else: ?>
		<a href="<?php the_permalink() ?>" rel="bookmark" ><?php the_post_thumbnail('ttrust_post_thumb_small', array('class' => 'postThumb alignleft', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?></a>
	<?php endif; ?>	
<?php endif; ?> -->

<a href="<?php the_permalink(); ?>" rel="bookmark" >	
		<?php the_post_thumbnail("ttrust_post_thumb_med", array('class' => '', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?>
		<span class="title"><span><?php the_title(); ?></span></span>
</a>	