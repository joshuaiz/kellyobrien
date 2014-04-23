<?php global $p; ?>
<div class="project small <?php echo $p; ?>" id="project-<?php echo $post->ID; ?>">
	<div class="inside">
	<a href="<?php the_permalink(); ?>" rel="bookmark" >	
		<?php the_post_thumbnail("ttrust_project_thumb", array('class' => '', 'alt' => ''.get_the_title().'', 'title' => ''.get_the_title().'')); ?>
		<span class="title"><span><?php the_title(); ?></span></span>
	</a>	
	</div>																																
</div>